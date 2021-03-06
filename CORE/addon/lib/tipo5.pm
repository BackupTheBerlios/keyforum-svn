package tipo5;
# reply
use strict;
use Digest::MD5;
use Itami::BinDump;
my $thistype=5;  # Dichiaro il tipo 
$GLOBAL::TIPI->{$thistype}=\&new; # Si inserisce l'indirizzo della funzione in una variabile globale.
my @campi=('DATE','TYPE','AUTORE','PKEY'); # Questi sono i campi che verrano usati per il calcolo dell'md5
my @items;

sub SelectQuery {
    my $this=shift;
    my $tosend=[];
    push(@$tosend,
        "SELECT `HASH`,`AUTORE`,`PKEY`,'$thistype' AS `TYPE`,`DATE`,`TICKET` FROM ".$this->{fname}."_tovalidate WHERE `HASH`=?;");
    return $tosend;
}

# Questa funzione viene richiamata dall'esterno e ci deve essere.
sub new {
    my ($fname,$id)=@_;
    my $this=bless({},"tipo".$thistype);
    print "Creato un tipo $thistype\n";
    @{$this}{'fname','id'}=($fname,$id);
    $this->{autoflush}=int(eval{$GLOBAL::Fconf->{$id}->{'TYPE'}->{'5'}->{'AUTOFLUSH'}});
    {$this->{autoflush}=72000000,last if $this->{autoflush}<=0 || $this->{autoflush}>20000;
    $this->{autoflush}=5*3600,last if $this->{autoflush}<5;
    $this->{autoflush}=$this->{autoflush}*3600;}
    $this->{congi}=$GLOBAL::SQL->prepare("INSERT INTO ".$fname."_congi (`HASH`,`TYPE`,`WRITE_DATE`,`INSTIME`,`AUTORE`) VALUES (?,'".$thistype."',?,?,?)");
    $this->{Inserisci}=$GLOBAL::SQL->prepare("INSERT INTO ".$fname."_tovalidate (`HASH`,`AUTORE`,`PKEY`,`DATE`,`TICKET_HASH`,`TICKET`) VALUES(?,?,?,?,?,?)");
    $this->{Update}=$GLOBAL::SQL->prepare("UPDATE ".$fname."_congi SET CAN_SEND='0' WHERE `WRITE_DATE`<? AND `TYPE`='5'");
    $this->{DeleteCongi}=$GLOBAL::SQL->prepare("DELETE FROM ".$fname."_congi WHERE `WRITE_DATE`<? AND `TYPE`='5'");
    $this->{DeleteToValidate}=$GLOBAL::SQL->prepare("DELETE FROM ".$fname."_tovalidate WHERE `DATE`<?");
    $this->{CountTicket}=$GLOBAL::SQL->prepare("SELECT count(*) FROM ".$fname."_tovalidate WHERE `TICKET_HASH`=?");
    push @items,$this;
    return $this;
}
# Questa funzione viene sempre richiamata.
sub Rule {
    my ($this,$msg)=@_;
    # Se il messaggio � scritto prima di una certa data impostata dall'utente si esce
    $msg->{ERRORE}=198,return undef if $msg->{DATE} < GM_TIME()-$this->{autoflush}-3700;
    $msg->{ERRORE}=28,return undef if $msg->{DATE}>GM_TIME()+3700; #Niente messaggi nel futuro
    return 1;
}
# Questa funzione viene richiamata dall'esterno e ci deve essere.
sub Inserisci {
    my ($this, $msg)=@_;
    my $forumid=$this->{id};
    # Controllo la firma digitale. Con la public key dell'amministratore. -100=firma non valida
    my $futils=$GLOBAL::ForUtility->{$forumid};
    my $ticket=$GLOBAL::TICKET{$forumid};
    my $reftick=BinDump::MainDeDump($msg->{TICKET});
    $msg->{ERRORE}=195,return undef unless $ticket->ValidateTicket($reftick);
    $msg->{ERRORE}=194,return undef if $msg->{'TRUEMD5'} ne $reftick->{AUTORE};
    $ticket->DeleteTicket($reftick->{HASH});
    $msg->{TICKET_HASH}=$reftick->{HASH};
    $this->{CountTicket}->execute($reftick->{HASH});
    my ($num)=$this->{CountTicket}->fetchrow_array;
    $msg->{ERRORE}=193,return undef if $num;
    $this->{congi}->execute($msg->{TRUEMD5},$msg->{DATE},time(),'');
    $this->{Inserisci}->execute($msg->{TRUEMD5},$msg->{AUTORE},$msg->{PKEY},$msg->{'DATE'},$reftick->{HASH},$msg->{TICKET});
    return 1;
}

# Questa funzione viene richiamata dall'esterno e ci deve essere.
# Controlla se il formato del messaggio � valido. Non ci pu� essere una data che contenga dei caratteri :\
sub CheckFormat {
    my ($this, $msg)=@_;
    $msg->{ERRORE}=11,return undef if length($msg->{AUTORE}) > 30 || length($msg->{AUTORE}) < 4; # 11 dim dell'autore non valida
    $msg->{ERRORE}=13,return undef if $msg->{PKEY} =~ /\D/; # 13 la chaive pubblica deve contenere solo numeri.
    $msg->{ERRORE}=197,return undef if length($msg->{PKEY}) > 500; # 197 chiave pubblica troppo grande
    $msg->{ERRORE}=196,return undef if length($msg->{TICKET}) > 1000; # 196 TICKET troppo grande
    $msg->{ERRORE}=4,return undef if $msg->{DATE} =~ /\D/; # -4 la data contiene dei caratteri non numeri
    $msg->{ERRORE}=3,return undef if length($msg->{DATE})<10; # -3 Data non valida (quando la lunghezza � minore di 10
    $msg->{ERRORE}=0; # Imposto la variabile ERRORE per questo messaggio a zero (nessun errore).
    return 1;
}
# Questa funzione viene richiamata dall'esterno e ci deve essere.
# Controlla se le dipenze di sto messaggio sono presenti nel database.
sub CheckDipen {
    my ($this,$msg,$dipen)=@_;
    return undef;
}
# Questa funzione viene richiamata dall'esterno e ci deve essere.
# Da una priorit� al messaggio.
sub priorita { # Ritorna la priorit� del messaggio che si prova ad inserire.
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