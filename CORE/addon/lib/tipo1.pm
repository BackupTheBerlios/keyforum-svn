package tipo1;
# Messaggi admin
use strict;
use Digest::MD5;
require "admin.pm";

my $thistype=1;  # Dichiaro il tipo 
$GLOBAL::TIPI->{$thistype}=\&new; # Si inserisce l'indirizzo della funzione in una variabile globale.
my @campi=('DATE','TYPE','TITLE','BODY'); # Questi sono i campi che verrano usati per il calcolo dell'md5

# Ritorna un array con la lista di query da eseguire quando viene richiesto un hash da mettere al posto del ? del tipo di questa libreria
sub SelectQuery {
    my $this=shift;
    my $tosend=[];
    push(@$tosend,"SELECT `HASH`, `DATE`, '$thistype' AS `TYPE`,`SIGN`, `TITLE`, `BODY` FROM ".$this->{fname}."_admin WHERE `HASH`=?;");
    return $tosend;
}


# Questa funzione viene richiamata dall'esterno e ci deve essere.
sub new {
    my ($fname,$id)=@_;
    my $this=bless({},"tipo".$thistype);
    print "Creato un tipo $thistype\n";
    @{$this}{'fname','id'}=($fname,$id);
    $this->{AdminComm}=admin->new($fname,$id);
    $this->{congi}=$GLOBAL::SQL->prepare("INSERT INTO ".$fname."_congi(`HASH`,`TYPE`,`WRITE_DATE`,`INSTIME`) VALUES (?,'".$thistype."',?,?)");
    $this->{addcom}=$GLOBAL::SQL->prepare("INSERT INTO ".$fname."_admin (`HASH`,`TITLE`,`BODY`,`DATE`,`SIGN`) VALUES(?,?,?,?,?)");
    return $this;
}
# Questa funzione viene richiamata dall'esterno e ci deve essere.
sub Inserisci {
    my ($this, $msg)=@_;
    my $forumid=$this->{id};
     my $futils=$GLOBAL::ForUtility->{$forumid};
    # Controllo la firma digitale. Con la public key dell'amministratore. -100=firma non valida
    $msg->{ERRORE}=100,return undef unless $GLOBAL::ForUtility->{$forumid}->CheckSignPkey($msg->{TRUEMD5},$msg->{SIGN},$GLOBAL::PubKey->{$this->{id}});
    # Esegue i comandi admin
    $this->{AdminComm}->execute($msg->{BODY},$msg->{DATE});
    return $this->_inserisci($msg);
}
# questa funzione è richiamata solo dalle funzioni interne e aggiunge la riga nella tabella solo se tutte le condizioni sono rispettate
sub _inserisci {
    my ($this,$msg)=@_;
    $this->{congi}->execute($msg->{TRUEMD5},$msg->{DATE},time());
    $this->{addcom}->execute($msg->{TRUEMD5},$msg->{TITLE} || '',$msg->{BODY},$msg->{DATE},$msg->{SIGN});
    return 1;
}
# Questa funzione viene richiamata dall'esterno e ci deve essere.
# Controlla se il formato del messaggio è valido. Non ci può essere una data che contenga dei caratteri :\
sub CheckFormat {
    my ($this, $msg)=@_;
    $msg->{ERRORE}=1,return undef unless length($msg->{BODY}); # -1 Manca il corpo (in questo caso i commandi admin
    $msg->{ERRORE}=2,return undef if length($msg->{TITLE})>255; # -2 titolo troppo grande
    $msg->{ERRORE}=4,return undef if $msg->{DATE} =~ /\D/; # -4 la data contiene dei caratteri non numeri
    $msg->{ERRORE}=3,return undef if length($msg->{DATE})<10; # -3 Data non valida (quando la lunghezza è minore di 10
    $msg->{ERRORE}=5,return undef if length($msg->{SIGN})<100; # -5 Firma non valida
    $msg->{ERRORE}=0; # Imposto la variabile ERRORE per questo messaggio a zero (nessun errore).
    return 1;
}
# Controllo se il messaggio rispetta le regole dell'admin
sub Rule {
    return 1; # i messaggi admin sono ritenuti sempre validi per via della loro natura superiore divina
}
# Questa funzione viene richiamata dall'esterno e ci deve essere.
# Controlla se le dipenze di sto messaggio sono presenti nel database.
sub CheckDipen {
    return undef;
}
# Questa funzione viene richiamata dall'esterno e ci deve essere.
# Da una priorità al messaggio.
sub priorita { # Ritorna la priorità del messaggio che si prova ad inserire.
    return 0; # I messaggi dell'admin vengono inseriti prima di ogni altro
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

1;