package tipo2;
# registrazione utenti
use strict;
use Digest::MD5;
require "antiflood.pm";
my $thistype=2;  # Dichiaro il tipo 
$GLOBAL::TIPI->{$thistype}=\&new; # Si inserisce l'indirizzo della funzione in una variabile globale.

# Ritorna un array con la lista di query da eseguire quando viene richiesto un hash da mettere al posto del ? del tipo di questa libreria
sub SelectQuery {
    my $this=shift;
    my $tosend=[];
    push(@$tosend,
        "SELECT `HASH`, `AUTORE`, '$thistype' AS `TYPE`, `DATE`, `PKEYDEC`, `SIGN`, `AUTH` FROM ".$this->{fname}."_membri WHERE `HASH`=?;",
        "SELECT `HASH`, `REP_OF`, `AUTORE`, `EDIT_OF`, '4' AS `TYPE`,`DATE`,`IS_EDIT`, `TITLE`, `BODY`, `EXTVAR`, `SIGN`,`ADMIN_SIGN` FROM ".$this->{fname}."_reply WHERE `AUTORE`=? LIMIT 150;"
        );
    return $tosend;
}
my @campi=('DATE','TYPE','AUTORE','PKEYDEC'); # Questi sono i campi che verrano usati per il calcolo dell'md5
# Questa funzione viene richiamata dall'esterno e ci deve essere.
sub new {
    my ($fname,$id)=@_;
    my $this=bless({},"tipo".$thistype);
    print "Creato un tipo $thistype\n";
    @{$this}{'fname','id'}=($fname,$id);
    $this->{congi}=$GLOBAL::SQL->prepare("INSERT INTO ".$fname."_congi(`HASH`,`TYPE`,`WRITE_DATE`,`INSTIME`,`AUTORE`) VALUES (?,'".$thistype."',?,?,?)");
    $this->{insertm}=$GLOBAL::SQL->prepare("INSERT INTO ".$fname."_membri (`HASH`,`AUTORE`,`DATE`,`PKEYDEC`,`SIGN`,`PKEYMD5`) VALUES(?,?,?,?,?,?)");
    $this->{presente}=$GLOBAL::SQL->prepare("SELECT count(*) FROM ".$fname."_membri WHERE HASH=?");
    $this->{update}=$GLOBAL::SQL->prepare("UPDATE ".$fname."_membri SET AUTORE=?,`DATE`=?,`PKEYDEC`=?,`SIGN`=?,`PKEYMD5`=?,present='1' WHERE HASH=?");
    $this->{updateauth}=$GLOBAL::SQL->prepare("UPDATE ".$fname."_membri SET is_auth='1',`AUTH`=? WHERE HASH=?");
    $this->{pkeymd5}=$GLOBAL::SQL->prepare("SELECT count(*) FROM ".$fname."_membri WHERE `PKEYMD5`=?");
    $GLOBAL::ForUtility->{$id}->InsertQuery('ExistsUser',"SELECT count(*) FROM ".$fname."_membri WHERE HASH=? AND present='1'");
    return $this;
}
# Questa funzione viene sempre richiamata.
sub Rule {
    my ($this,$msg)=@_;
    # Se il messaggio è scritto prima di una certa data impostata dall'utente si esce
    return undef if $msg->{DATE}< $GLOBAL::Fconf->{$this->{id}}->{'CORE'}->{'MSG'}->{'MAX_OLD'};
    $msg->{ERRORE}=28,return undef if $msg->{DATE}>Time::Local::timelocal(gmtime(time()+$GLOBAL::ntpoffset))+3700; #Niente messaggi nel futuro
    return 1;
}
# Questa funzione viene richiamata dall'esterno e ci deve essere.
sub Inserisci {
    my ($this, $msg)=@_;
    my $forumid=$this->{id};
    # Controllo la firma digitale. Con la public key dell'amministratore. -100=firma non valida
    my ($valid_sign,$valid_auth);
    $valid_sign=$GLOBAL::ForUtility->{$forumid}->CheckSignPkey($msg->{TRUEMD5},$msg->{SIGN},$msg->{PKEYDEC}) if length($msg->{PKEYDEC})>270 && $msg->{SIGN};
    $valid_auth=$GLOBAL::ForUtility->{$forumid}->CheckSignPkey($msg->{TRUEMD5},$msg->{AUTH},$GLOBAL::PubKey->{$forumid}) if length($msg->{AUTH})>100;
    $msg->{ERRORE}=100,return undef unless $valid_sign || $valid_auth; # Nessun SIGN o AUTH valido, si esce
    $msg->{AUTH}='' unless $valid_auth;
    $msg->{SIGN}='' unless $valid_sign;

    # 112-Un utente fittizio non può avere una public key (utente creato dall'admin in caso di importazione
    # di vecchie board. Si creano utenti senza PKEY e senza firma digitale propria ma validati dall'admin
    $msg->{ERRORE}=112,return undef if !$valid_sign && length($msg->{PKEYDEC});  # Se non ha firma valida ma ha una PKEY.
    $msg->{PKEYMD5}=Digest::MD5::md5($msg->{PKEYDEC}) if $msg->{PKEYDEC}; # calcolo l'md5 della pkey
    unless ($valid_auth) { # Se non ha autorizzazione si controlla l'unicità della public key nel database.
        $msg->{ERRORE}=113,return undef if $this->univocita($msg->{PKEYMD5});  # Se c'è già un pkey uguale si esce
        # 113 - La public key che vuoi usare è già presente nel database.
    }    
    $this->_inserisci($msg); # Inserisco l'utente senza auth
    $this->{updateauth}->execute($msg->{AUTH},$msg->{TRUEMD5}) if $valid_auth; # Aggiorno l'autorizzazione se è valida
    return 1;
}
# questa funzione è richiamata solo dalle funzioni interne e aggiunge la riga nella tabella solo se tutte le condizioni sono rispettate
sub _inserisci {
    my ($this,$msg)=@_;
    $this->{congi}->execute($msg->{TRUEMD5},$msg->{DATE},time(),$msg->{TRUEMD5});
    unless ($this->Presente($msg->{TRUEMD5})) {
        $this->{insertm}->execute($msg->{TRUEMD5},$msg->{AUTORE},$msg->{DATE},$msg->{PKEYDEC},$msg->{SIGN},$msg->{PKEYMD5});
    } else {
        $this->{update}->execute($msg->{AUTORE},$msg->{DATE},$msg->{PKEYDEC},$msg->{SIGN},$msg->{PKEYMD5},$msg->{TRUEMD5});
    }
    return 1;
}
# Questa funzione viene richiamata dall'esterno e ci deve essere.
# Controlla se il formato del messaggio è valido. Non ci può essere una data che contenga dei caratteri :\
sub CheckFormat {
    my ($this, $msg)=@_;
    $msg->{ERRORE}=11,return undef if length($msg->{AUTORE}) > 30 || length($msg->{AUTORE}) < 4; # 11 dim dell'autore non valida
    $msg->{ERRORE}=13,return undef if $msg->{PKEYDEC} =~ /\D/; # 13 la chaive pubblica deve contenere solo numeri.
    $msg->{ERRORE}=4,return undef if $msg->{DATE} =~ /\D/; # -4 la data contiene dei caratteri non numeri
    $msg->{ERRORE}=3,return undef if length($msg->{DATE})<10; # -3 Data non valida (quando la lunghezza è minore di 10
    $msg->{ERRORE}=0; # Imposto la variabile ERRORE per questo messaggio a zero (nessun errore).
    return 1;
}
# Questa funzione viene richiamata dall'esterno e ci deve essere.
# Controlla se le dipenze di sto messaggio sono presenti nel database.
sub CheckDipen {
    return undef; # I comandi admin non dipendono da nessun altro messaggio
}
# Questa funzione viene richiamata dall'esterno e ci deve essere.
# Da una priorità al messaggio.
sub priorita { # Ritorna la priorità del messaggio che si prova ad inserire.
    return 2; #più è bassa e prima viene inserito.
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
sub univocita {
    my ($this,$pkeymd5)=@_;
    $this->{pkeymd5}->execute($pkeymd5);
    return $this->{pkeymd5}->fetchrow_arrayref->[0];
}
sub Presente {
    my ($this,$hash)=@_;
    $this->{presente}->execute($hash);
    return $this->{presente}->fetchrow_arrayref->[0];
}
1;