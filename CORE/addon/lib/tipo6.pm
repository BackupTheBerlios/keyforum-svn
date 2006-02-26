package tipo6;  # Messaggi Privati
# reply
use strict;
use Digest::MD5;
use Itami::BinDump;
my $thistype=6;  # Dichiaro il tipo 
$GLOBAL::TIPI->{$thistype}=\&new; # Si inserisce l'indirizzo della funzione in una variabile globale.
my @campi=('DATE','TYPE','AUTORE','TITLE','BODY','DEST'); # Questi sono i campi che verrano usati per il calcolo dell'md5
my @items;

sub SelectQuery {
    my $this=shift;
    my $tosend=[];
    push(@$tosend,
        "SELECT `HASH`,`AUTORE`,`TITLE`,'$thistype' AS `TYPE`,`DEST`,`DATE`,`BODY`,`SIGN` FROM ".$this->{fname}."_mp WHERE `HASH`=?;");
    return $tosend;
}

# Questa funzione viene richiamata dall'esterno e ci deve essere.
sub new {
    my ($fname,$id)=@_;
    my $this=bless({},"tipo".$thistype);
    print "Creato un tipo $thistype\n";
    @{$this}{'fname','id'}=($fname,$id);
    # Antiflood
    my $name=$GLOBAL::Fconf->{$this->{id}}->{'TYPE'}->{$thistype}->{'ANTIFLOOD'};
    $this->{AntiFlood}=AntiFlood->new($fname,$id,$GLOBAL::Fconf->{$id}->{"ANTIFLOOD_".$name},$name,$thistype);
    $this->{congi}=$GLOBAL::SQL->prepare("INSERT INTO ".$fname."_congi(`HASH`,`TYPE`,`WRITE_DATE`,`INSTIME`,`AUTORE`) VALUES (?,'".$thistype."',?,?,?)");
    # Autoflush
    $this->{autoflush}=int(eval{$GLOBAL::Fconf->{$id}->{'TYPE'}->{$thistype}->{'AUTOFLUSH'}});
    {$this->{autoflush}=72000000,last if $this->{autoflush}<=0 || $this->{autoflush}>20000;
    $this->{autoflush}=5*3600,last if $this->{autoflush}<5;
    $this->{autoflush}=$this->{autoflush}*3600;}
    $this->{congi}=$GLOBAL::SQL->prepare("INSERT INTO ".$fname."_congi (`HASH`,`TYPE`,`WRITE_DATE`,`INSTIME`,`AUTORE`) VALUES (?,'".$thistype."',?,?,?)");
    $this->{Inserisci}=$GLOBAL::SQL->prepare("INSERT INTO ".$fname."_mp (`HASH`,`AUTORE`,`DEST`,`DATE`,`BODY`,`TITLE`,`SIGN`) VALUES(?,?,?,?,?,?,?)");
    $this->{Update}=$GLOBAL::SQL->prepare("UPDATE ".$fname."_congi SET CAN_SEND='0' WHERE `TYPE`='6' AND `WRITE_DATE`<?");
    $this->{DeleteCongi}=$GLOBAL::SQL->prepare("DELETE FROM ".$fname."_congi WHERE `TYPE`='6' AND `WRITE_DATE`<?");
    $this->{DeleteToValidate}=$GLOBAL::SQL->prepare("DELETE FROM ".$fname."_mp WHERE `DATE`<?");
    push @items,$this;
    return $this;
}
# Questa funzione viene sempre richiamata.
sub Rule {
    my ($this,$msg)=@_;
    # Se il messaggio è scritto prima di una certa data impostata dall'utente si esce
    $msg->{ERRORE}=198,return undef if $msg->{DATE} < GM_TIME()-$this->{autoflush}-3700;
    $msg->{ERRORE}=28,return undef if $msg->{DATE} > GM_TIME()+3700; #Niente messaggi nel futuro
    return 1 if $GLOBAL::Permessi->{$this->{id}}->CanDo($msg->{AUTORE},$msg->{DATE},'ANTIFLOOD','NO_LIMIT');
    $msg->{ERRORE}=24,return undef unless $this->{AntiFlood}->Check($msg->{AUTORE},$msg->{DATE});  # 23 Antiflood...troppi msg
    return 1;
}
# Questa funzione viene richiamata dall'esterno e ci deve essere.
sub Inserisci {
    my ($this, $msg)=@_;
    my $forumid=$this->{id};
    # Controllo la firma digitale. Con la public key dell'amministratore. -100=firma non valida
    my $futils=$GLOBAL::ForUtility->{$forumid};
    my $permessi=$GLOBAL::Permessi->{$forumid};
    $msg->{ERRORE}=27,return undef if $permessi->CanDo($msg->{AUTORE},$msg->{DATE},'IS_BAN'); # 27 L'utente è bannato
    unless ($permessi->TypePerm($thistype,$msg->{DATE},'ENABLE')) { # Se nessuno può scrivere messaggi
        SWITCH: { # Almeno il mittente o il destinatario devono essere autorizzati a ricevere o spedire messaggi.
            last SWITCH if $permessi->CanDo($msg->{DEST},$msg->{DATE},'CAN','RECV_MP');
            $msg->{ERRORE}=301;
            last SWITCH if $permessi->CanDo($msg->{AUTORE},$msg->{DATE},'CAN','WRITE_MP'); # Devi avere l'autorizzazione dall'admin
            $msg->{ERRORE}=300;
            return undef;
        }
        $msg->{ERRORE}=0;
    } else {
        $msg->{ERRORE}=302, return undef unless $permessi->CanDo($msg->{AUTORE},$msg->{DATE},'CANT','WRITE_MP');
    }
    $this->{congi}->execute($msg->{TRUEMD5},$msg->{DATE},time(),$msg->{AUTORE});
    $this->{Inserisci}->execute($msg->{TRUEMD5},$msg->{AUTORE},$msg->{DEST},$msg->{'DATE'},$msg->{BODY},$msg->{TITLE},$msg->{SIGN});
    return 1;
}

# Questa funzione viene richiamata dall'esterno e ci deve essere.
# Controlla se il formato del messaggio è valido. Non ci può essere una data che contenga dei caratteri :\
sub CheckFormat {
    my ($this, $msg)=@_;
    $msg->{ERRORE}=15,return undef if length($msg->{AUTORE}) != 16; # 15 dim dell'autore deve essere 16byte
    $msg->{ERRORE}=194,return undef if length($msg->{DEST}) != 16; # 194 la dim del campo DEST deve essere 16 caratteri
    $msg->{ERRORE}=4,return undef if $msg->{DATE} =~ /\D/; # -4 la data contiene dei caratteri non numeri
    $msg->{ERRORE}=3,return undef if length($msg->{DATE})<10; # -3 Data non valida (quando la lunghezza è minore di 10
    $msg->{ERRORE}=19,return undef if length($msg->{TITLE}) > 200;   # 19 Il titolo può essere massimo di 200 caratteri
    $msg->{ERRORE}=21,return undef if length($msg->{BODY}) > 15000 || length($msg->{BODY})==0; # 21 il BODY può essere di massimo 15k carratteri
    $msg->{ERRORE}=0; # Imposto la variabile ERRORE per questo messaggio a zero (nessun errore).
    return 1;
}
# Questa funzione viene richiamata dall'esterno e ci deve essere.
# Controlla se le dipenze di sto messaggio sono presenti nel database.
sub CheckDipen {
    my ($this,$msg,$dipen)=@_;
    my $return ='';
    unless ($GLOBAL::ForUtility->{$this->{id}}->ExecuteQuery('ExistsUser',$msg->{AUTORE})) { # Controllo se è presente l'utente che ha scritto il msg
        $dipen->{$msg->{AUTORE}}++;
        $msg->{ERRORE}=22; # 22 Manca l'autore nel database.
        $return= $msg->{AUTORE};  # Questo Thread resterà in attesa nel buffer fino a quando non arriva l'autore del messaggio
    }
    # Si aggiunge l'mp anche se manca il destinatario.
    # Se non è presente nella tabella si richiede al vicino.
    unless ($GLOBAL::ForUtility->{$this->{id}}->ExecuteQuery('ExistsUser',$msg->{DEST})) { # Controllo se è presente l'utente che ha scritto il msg
        $dipen->{$msg->{DEST}}++;
        #$msg->{ERRORE}=187; # 187 manca il destinatario
        #$return= $msg->{DEST};  # Questo Thread resterà in attesa nel buffer fino a quando non arriva l'autore del messaggio
    }
    return $return;
}
# Questa funzione viene richiamata dall'esterno e ci deve essere.
# Da una priorità al messaggio.
sub priorita { # Ritorna la priorità del messaggio che si prova ad inserire.
    my ($this,$msg)=@_;
    return 10; # Le NON modifiche vengono inserite prima
}
# Questa funzione viene richiamata dall'esterno e ci deve essere.
sub MakeMd5 { # La funzione crea l'md5 del messaggio e lo inserisce nel campo TRUEMD5
    my ($this,$msg)=@_;
    my $ctx=Digest::MD5->new;
    $ctx->add($GLOBAL::PubKey->{$this->{id}}."c");
    foreach my $buf (@campi) {
        $ctx->add($msg->{$buf}."c");
    }
    $msg->{'TRUEMD5'}=$ctx->digest;
}

# Questa funzione viene eseguita ogni 5 minuti e gli viene passato l'oggetto da analizzare
sub _AutoFlush {
    my $this=shift;
    $this->{DeleteCongi}->execute(GM_TIME()-$this->{$this->{autoflush}}-7200);
    $this->{DeleteToValidate}->execute(GM_TIME()-$this->{$this->{autoflush}}-7200);
    $this->{Update}->execute(GM_TIME()-$this->{$this->{autoflush}});
}


sub AutoFlush { # Questa funzione viene eseguita ogni 5 minuti
    foreach my $buf (@items) {
        $buf->_AutoFlush;
    }
}

push (@{$GLOBAL::CycleFunc},CycleFunc->new(300,\&AutoFlush));
sub GM_TIME {return Time::Local::timelocal(gmtime(time()+$GLOBAL::ntpoffset));}
1;