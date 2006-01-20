package tipo3;
# Thread
use strict;
use Digest::MD5;
use Itami::BinDump;
require "antiflood.pm";
my $thistype=3;  # Dichiaro il tipo 
$GLOBAL::TIPI->{$thistype}=\&new; # Si inserisce l'indirizzo della funzione in una variabile globale.
my @campi=('DATE','TYPE','SEZ','AUTORE','EDIT_OF','IS_EDIT','TITLE','SUBTITLE','BODY','EXTVAR'); # Questi sono i campi che verrano usati per il calcolo dell'md5


sub SelectQuery {
    my $this=shift;
    my $tosend=[];
    push(@$tosend,
        "SELECT `HASH`, `SEZ`, `AUTORE`, `EDIT_OF`, '$thistype' AS `TYPE`,`DATE`,`IS_EDIT`, `TITLE`, `SUBTITLE`, `BODY`, `EXTVAR`, `SIGN`,`ADMIN_SIGN` FROM ".$this->{fname}."_newmsg WHERE `HASH`=?;",
        "SELECT `HASH`, `SEZ`, `AUTORE`, `EDIT_OF`, '$thistype' AS `TYPE`,`DATE`,`IS_EDIT`, `TITLE`, `SUBTITLE`, `BODY`, `EXTVAR`, `SIGN`,`ADMIN_SIGN` FROM ".$this->{fname}."_newmsg WHERE `EDIT_OF`=? AND `IS_EDIT`='1' LIMIT 100;",
        "SELECT `HASH`, `REP_OF`, `AUTORE`, `EDIT_OF`, '4' AS `TYPE`,`DATE`,`IS_EDIT`, `TITLE`, `BODY`, `EXTVAR`, `SIGN`,`ADMIN_SIGN` FROM ".$this->{fname}."_reply WHERE `EDIT_OF`=? LIMIT 150;");
    return $tosend;
}

# Questa funzione viene richiamata dall'esterno e ci deve essere.
sub new {
    my ($fname,$id)=@_;
    my $this=bless({},"tipo".$thistype);
    print "Creato un tipo $thistype\n";
    @{$this}{'fname','id'}=($fname,$id);
    my $name=$GLOBAL::Fconf->{$this->{id}}->{'TYPE'}->{$thistype}->{'ANTIFLOOD'};
    $this->{AntiFlood}=AntiFlood->new($fname,$id,$GLOBAL::Fconf->{$id}->{"ANTIFLOOD_".$name},$name,$thistype);
    $this->{congi}=$GLOBAL::SQL->prepare("INSERT INTO ".$fname."_congi(`HASH`,`TYPE`,`WRITE_DATE`,`INSTIME`,`AUTORE`) VALUES (?,'".$thistype."',?,?,?)");
    $this->{InviUpdate}=$GLOBAL::SQL->prepare("UPDATE ".$fname."_newmsg SET visibile='0' WHERE EDIT_OF=? AND `DATE`<?");
    $this->{Inserisci}=$GLOBAL::SQL->prepare("INSERT INTO ".$fname."_newmsg (`HASH`,`SEZ`,`AUTORE`, `EDIT_OF`,`DATE`,`IS_EDIT`, `TITLE`, `SUBTITLE`, `BODY`, `EXTVAR`, `SIGN`,`ADMIN_SIGN`,`visibile`) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)");
    $this->{InsertMsghe}=$GLOBAL::SQL->prepare("INSERT INTO ".$fname."_msghe (`HASH`,`last_reply_time`,`last_reply_author`,`DATE`,`AUTORE`) VALUES(?,?,?,?,?)");
    $this->{IncThrnum}=$GLOBAL::SQL->prepare("UPDATE ".$fname."_sez SET THR_NUM=THR_NUM+1 WHERE ID=?");
    $this->{IncMsgNum}=$GLOBAL::SQL->prepare("UPDATE ".$fname."_membri SET msg_num=msg_num+1 WHERE HASH=?");
    $this->{IncTotMsgNum}=$GLOBAL::SQL->prepare("UPDATE ".$fname."_membri SET tot_msg_num=tot_msg_num+1 WHERE HASH=?");
    return $this;
}
# Questa funzione viene sempre richiamata.
sub Rule {
    my ($this,$msg)=@_;
    return 1 if length($msg->{'ADMIN_SIGN'})>50;
    # Se il messaggio è scritto prima di una certa data impostata dall'utente si esce
    $msg->{ERRORE}=23,return undef if $msg->{DATE}< $GLOBAL::Fconf->{$this->{id}}->{'CORE'}->{'MSG'}->{'MAX_OLD'};
    $msg->{ERRORE}=24,return undef unless $this->{AntiFlood}->Check($msg->{AUTORE},$msg->{DATE});  # 23 Antiflood...troppi msg
    $msg->{ERRORE}=28,return undef if $msg->{DATE}>Time::Local::timelocal(gmtime(time()+$GLOBAL::ntpoffset))+3700; #Niente messaggi nel futuro
    return 1;
}
# Questa funzione viene richiamata dall'esterno e ci deve essere.
sub Inserisci {
    my ($this, $msg)=@_;
    my $forumid=$this->{id};
    # Controllo la firma digitale. Con la public key dell'amministratore. -100=firma non valida
    my $futils=$GLOBAL::ForUtility->{$forumid};
    my $permessi=$GLOBAL::Permessi->{$this->{id}};
    return $this->Admin_ins($msg,$futils) if length($msg->{'ADMIN_SIGN'})>50;
    
    my ($valid_sign,$sez_data,$user_data);
    $msg->{ERRORE}=25,return undef unless $sez_data=$futils->LoadSezInfo($msg->{SEZ});  #25 Sezione non trovata
    $msg->{ERRORE}=26,return undef unless $user_data=$futils->LoadUserData($msg->{AUTORE}); #26 dati utente non caricati
    #$msg->{ERRORE}=27,return undef if $user_data->{ban}<$msg->{DATE} && $user_data->{ban}>1000000000; # 27 L'utente è bannato
    $msg->{ERRORE}=27,return undef if $permessi->CanDo($msg->{AUTORE},$msg->{DATE},'IS_BAN'); # 27 L'utente è bannato
    $msg->{ERRORE}=179,return undef if $msg->{DATE}<$user_data->{DATE}; # 179 Non si può scriver msg prima della data di registrazione
    
    if ($sez_data->{NEED_PERM}) { # se la sezione è protetta e richiede una autorizzazione esplicita
        $msg->{ERRORE}=29, return undef unless $permessi->CanDo($msg->{AUTORE},$msg->{DATE},$msg->{SEZ},'CAN_WRITE_THREAD');  # 29 il forum è protetto (solo autorizzati)
    }
    $valid_sign=$futils->CheckSignPkey($msg->{TRUEMD5},$msg->{'SIGN'},$user_data->{'PKEYDEC'}) if length($user_data->{'PKEYDEC'})>270 && length($msg->{SIGN})>100;
    $msg->{ERRORE}=100,return undef unless $valid_sign; # 100 Nessun SIGN valido, si esce
    
    return $this->non_edit_ins($msg,$futils,$user_data,$sez_data) unless $msg->{IS_EDIT}; # Inserisce le non modifiche
    
    
    return $this->edit_ins($msg,$futils,$user_data,$sez_data,$permessi); # Inserisce le modifiche
}

sub non_edit_ins {
    my ($this,$msg,$futils,$user_data,$sez_data)=@_;
    if($sez_data->{ONLY_AUTH}) { # Se richiede l'autorizzazione la sezione
        $msg->{ERRORE}=31,return undef unless $user_data->{is_auth}; #31 I non autorizza non possono scrivere in questa sezione
    } else { # Se non richiede l'autorizzazione si può scrivere un solo messaggio
        $msg->{ERRORE}=32,return undef if !$user_data->{is_auth} && $user_data->{tot_msg_num}>1; #32 se non si è autorizzati  solo 1 msg
    }
    return $this->_inserisci($msg);
}
sub edit_ins {
    my ($this,$msg,$futils,$user_data,$sez_data,$permessi)=@_;
    $msg->{ERRORE}=163,return undef unless $user_data->{is_auth}; # i non autorizzati non possono modificare i messaggi
    my $original_autore=$futils->GetOriginalAutoreNewMsg($msg->{EDIT_OF});  # Prendo l'autore originale del messaggio.
    #$msg->{ERRORE}=164,return undef if $original_autore ne $msg->{AUTORE}; # Solo gli autori possono modificare i propri messaggi
    if ($original_autore ne $msg->{AUTORE}) { # Se l'autore è differente
        # 164 Solo gli autori e moderatori possono modificare i propri messaggi
        $msg->{ERRORE}=164,return undef unless $permessi->CanDo($msg->{AUTORE},$msg->{DATE},$msg->{SEZ},'IS_MOD');
    }
    return $this->_inserisci($msg);
}
sub Admin_ins { #
    my ($this,$msg,$futils)=@_;
    my $admin_auth=$futils->CheckSignPkey($msg->{TRUEMD5},$msg->{'ADMIN_SIGN'},$GLOBAL::PubKey->{$this->{id}});
    $msg->{ERRORE}=151,return undef unless $admin_auth; # 151 Firma admin_sign non valida
    $msg->{FOR_SIGN}='';
    $msg->{SIGN}='';
    $this->_inserisci($msg);
    return 1;
}
# questa funzione è richiamata solo dalle funzioni interne e aggiunge la riga nella tabella solo se tutte le condizioni sono rispettate
sub _inserisci {
    my ($this,$msg)=@_;
    my $cambiati='0';
    if ($msg->{IS_EDIT}) { # Se è una modifica...
        $this->{IncTotMsgNum}->execute($msg->{AUTORE});
        $this->{InviUpdate}->execute($msg->{EDIT_OF});
        $cambiati='1' if $GLOBAL::SQL->{mysql_info}=~/Changed: (\d+?)/ && $1;
    } else { # se è un nuovo thread
        $msg->{EDIT_OF}=$msg->{TRUEMD5}; # EDIT_OF è uguale a se stesso se non è una modifica
        $this->{InsertMsghe}->execute($msg->{TRUEMD5},$msg->{DATE},$msg->{AUTORE},$msg->{DATE},$msg->{AUTORE});
        $this->{IncThrnum}->execute($msg->{SEZ});
        $this->{IncMsgNum}->execute($msg->{AUTORE});
        $cambiati='1';
    }
    $this->{congi}->execute($msg->{TRUEMD5},$msg->{DATE},time(),$msg->{AUTORE});
    $this->{Inserisci}->execute($msg->{TRUEMD5},$msg->{SEZ},$msg->{AUTORE},$msg->{EDIT_OF},$msg->{DATE},$msg->{IS_EDIT},
        $msg->{TITLE} || '',$msg->{SUBTITLE} || '',$msg->{BODY},$msg->{EXTVAR} || '',$msg->{SIGN},$msg->{ADMIN_SIGN} || '',$cambiati);
    $this->{AntiFlood}->dbminsert($msg->{AUTORE},$msg->{DATE});
    $GLOBAL::ExtVar->{$this->{id}}->ExecuteThread($msg->{AUTORE},$msg->{EDIT_OF},$msg->{SEZ},BinDump::MainDeDump($msg->{EXTVAR}),$msg->{DATE}) if length $msg->{EXTVAR};
    return 1;
}
# Questa funzione viene richiamata dall'esterno e ci deve essere.
# Controlla se il formato del messaggio è valido. Non ci può essere una data che contenga dei caratteri :\
sub CheckFormat {
    my ($this, $msg)=@_;
    $msg->{ERRORE}=15,return undef if length($msg->{AUTORE}) != 16; # 15 dim dell'autore deve essere 16byte
    $msg->{ERRORE}=16,return undef if $msg->{SEZ} =~ /\D/; # 16 La sezione deve contenere solo numeri
    $msg->{ERRORE}=17,return undef if !$msg->{SEZ} || $msg->{SEZ}>=9000; # 17 La sezione deve avere un valore valido tra 1 e 9000
    $msg->{ERRORE}=18,return undef if length($msg->{EXTVAR}) > 5000; # 18 EXTVAR non può superare i 15000 caratteri
    $msg->{ERRORE}=19,return undef if length($msg->{TITLE}) > 200; # 19 Il titolo può essere massimo di 200 caratteri
    $msg->{ERRORE}=20,return undef if length($msg->{SUBTITLE}) > 250; # 20 il SUBTITLE può essere massimo di 250 caratteri
    $msg->{ERRORE}=33,return undef if length($msg->{EDIT_OF}) != 16 && $msg->{IS_EDIT}; # 33 lunghezza di edit_of non valida per essere una modifica
    $msg->{ERRORE}=21,return undef if length($msg->{BODY}) > 50000 || length($msg->{BODY})==0; # 21 il BODY può essere di massimo 50k carratteri
    $msg->{ERRORE}=4,return undef if $msg->{DATE} =~ /\D/; # -4 la data contiene dei caratteri non numeri
    $msg->{ERRORE}=3,return undef if length($msg->{DATE})<10; # -3 Data non valida (quando la lunghezza è minore di 10
    $msg->{ERRORE}=0; # Imposto la variabile ERRORE per questo messaggio a zero (nessun errore).
    return 1;
}
# Questa funzione viene richiamata dall'esterno e ci deve essere.
# Controlla se le dipenze di sto messaggio sono presenti nel database.
sub CheckDipen {
    my ($this,$msg,$dipen)=@_;
    my $return='';
    unless ($GLOBAL::ForUtility->{$this->{id}}->ExecuteQuery('ExistsUser',$msg->{AUTORE})) { # Controllo se è presente l'utente che ha scritto il msg
        $dipen->{$msg->{AUTORE}}++;
        $msg->{ERRORE}=22; # 22 Manca l'autore nel database.
        $return= $msg->{AUTORE};  # Questo Thread resterà in attesa nel buffer fino a quando non arriva l'autore del messaggio
    }
    if ($msg->{IS_EDIT}) {
        unless($GLOBAL::ForUtility->{$this->{id}}->ExistsThread($msg->{EDIT_OF})) {
            $return=$msg->{EDIT_OF};
            $msg->{ERRORE}=169; # 169 manca il thread originale nel database
            $dipen->{$msg->{EDIT_OF}}++;
        }
    }
    return $return;
}
# Questa funzione viene richiamata dall'esterno e ci deve essere.
# Da una priorità al messaggio.
sub priorita { # Ritorna la priorità del messaggio che si prova ad inserire.
    my ($this,$msg)=@_;
    return 4 if $msg->{'IS_EDIT'};  # Se è una modifica ha priorità più bassa (più basso è il numero prima viene inserito)
    return 3; # Le NON modifiche vengono inserite prima
}
# Questa funzione viene richiamata dall'esterno e ci deve essere.
sub MakeMd5 { # La funzione crea l'md5 del messaggio e lo inserisce nel campo TRUEMD5
    my ($this,$msg)=@_;
    ($msg->{'IS_EDIT'}) ? ($msg->{'IS_EDIT'}='1') : ($msg->{'IS_EDIT'}='0');  # IS_EDIT è un campo con poche possibilità di scelta :\
    delete $msg->{EDIT_OF} unless $msg->{'IS_EDIT'};# Se non è una modifica cancello il campo EDIT_OF
    my $ctx=Digest::MD5->new;
    $ctx->add($GLOBAL::PubKey->{$this->{id}}."c");
    foreach my $buf (@campi) {
        $ctx->add($msg->{$buf}."c");
    }
    $msg->{'TRUEMD5'}=$ctx->digest;
}

1;
