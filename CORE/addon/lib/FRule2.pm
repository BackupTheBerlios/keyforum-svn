package FRule;
use strict;
$GLOBAL::TIPI={};
use Itami::ConvData;
use Math::Pari;
require "antiflood.pm";
require "msgpila.pm";


#Carico le librerie dei vari tipi memorizzati
require "tipo1.pm"; # Comandi admin
require "tipo2.pm"; # Registrazione utenti
require "tipo3.pm"; # Thread
require "tipo4.pm"; # Reply
require "tipo5.pm"; # ExtDati e varie per modificare i propri dati nel db

sub new {
    my ($packname,$fname,$id,$pkey)=@_;
    my $this=bless({},$packname);
    @{$this}{'fname','id','pkey'}=($fname,$id,$pkey);
    $this->{msgpila}=msgpila->new();
    $this->{presente}=$GLOBAL::SQL->prepare("SELECT count(*) FROM ".$fname."_congi WHERE HASH=?");
    print "creo i tipi\n";
    $this->MakeType;   # Creo gli oggetti per i vari tipi di dato
    return $this;
    
}
# Questa funzione è incaricata di aggiungere i messaggi al database.

sub AddRows {
    my ($this,$list)=@_;
    return undef if ref($list) ne "HASH";
    my $type=$this->{type};
    my @ritorna=([],{});
    # Viene creata una lista di hash secondo l'ordine di importanza.
    # I Thread verrano inseriti prima dei reply, per ovvi motivi.
    my $ordine=$this->sortmsg($list);
    my ($dipen,$msg,$tmptype);
    foreach my $alfa (@$ordine) {
        next if ref($alfa) ne "ARRAY"; # Andiamo alla prossima cella se non è un riferimento ad un hash
        foreach my $beta (@$alfa) { # Adesso beta contiene l'hash da inserire
            next unless exists $list->{$beta}; # Meglio continuare con il successivo se non troviamo più beta
            $msg=$list->{$beta};
            $tmptype=$type->{$msg->{TYPE}};
            $tmptype->MakeMd5($msg);  # Crea l'md5 del messaggio e lo inseriscein $Msg->{TRUEMD5}
            $msg->{ERRORE}=6, next if $this->Presente($msg->{TRUEMD5}); # Se il messaggio c'è già nelle tabelle lo saltiamo
            next unless $tmptype->CheckFormat($msg); # Controlla il formato del messaggio. Inserisce l'errore in $msg->{ERRORE}
            next unless $tmptype->Rule($msg); # Controlla se il messaggio rispetta le regole impostate dell'admin
            # Controlla le dipendeze del messaggio. Un reply dipende dal suo thread e dal suo autore. Devono esserci entrambi.
            # Anche in questo caso se manca qualcosa viene inserito l'errore in $msg->{ERRORE}
            $this->{msgpila}->addcoda($dipen,$msg->{'TRUEMD5'},$msg),next if $dipen=$tmptype->CheckDipen($msg,$ritorna[1]);
            # Prova ad inserirlo. L'errore viene inserito in $msg->{ERRORE}. Ad esempio se la firma non è valida
            next unless $tmptype->Inserisci($msg);
            push @{$ritorna[0]},$msg->{'TRUEMD5'};  # Se è stato inserito si mette l'hash un un vettore per offrirlo a tutti
            # Se avverte l'oggetto msgpila di controllare se alcuni messaggi nel buffer dipendevano da questo messaggio appena inserito
            $this->{msgpila}->aggiunto($msg->{'TRUEMD5'});
        }
    }
    while (my ($key,$value)=each %$list) {
        print unpack("H*",$value->{TRUEMD5})." ERRORE ".$value->{ERRORE}."\n";
    }
    $ritorna[1] = [ keys %{$ritorna[1]} ];  # Prendo le chiave dell'hash e le metto in un vettore :)
    return @ritorna;
}
# Aggiunge una singola riga.
# Serve solo se si inserisce la locale.
sub AddRow {
    my ($this,$msg)=@_;
    return undef if ref($msg) ne "HASH";
    $msg->{ERRORE}=102,return undef unless $msg->{TYPE}; # 102 il tipo non è specificato
    $msg->{ERRORE}=101,return undef unless exists $this->{type}->{$msg->{TYPE}};  # 101 Il tipo del messaggio non è installato
    my $tmptype=$this->{type}->{$msg->{TYPE}}; 
    
    $tmptype->MakeMd5($msg); # Calcolo l'md5
    return undef if $tmptype->CheckDipen($msg,{});   # si cotrolla se manca una dipendenda, l'errore lo inserisce la funzione CheckDipen
    my $item=$GLOBAL::ForUtility->{$this->{id}};
    my $password;
    my $CPSIGN=$msg->{CPSIGN} || 'SIGN';
    $msg->{ERRORE}=105,return undef unless $msg->{'_PRIVATE'};  # 105 non ho la chiave privata per firmare il msg
    $msg->{ERRORE}=104,return undef unless $password=$item->GetPrivateKey($msg->{'_PRIVATE'},$msg->{'_PWD'}); # 104 non riesco a convertire la chiave in oggetto
    $msg->{ERRORE}=103,return undef unless $msg->{$CPSIGN}=$item->Firma($msg->{TRUEMD5},$password);  # 103 non riesco a firmare il messaggio
    return undef unless $tmptype->CheckFormat($msg); 
    return $tmptype->Inserisci($msg);
}
# Crea un oggetto di ogni tipo per ogni board creata.
# L'oggetto è attivo o meno a seconda della configurazione dell'admin.
sub MakeType {
    my ($this)=@_;
    $this->{type}={};
    while (my ($key, $value)=each(%$GLOBAL::TIPI)) {
         $this->{type}->{$key}=$value->($this->{'fname'},$this->{'id'});
    }
}
# Ad ogni messaggio viene data una cerca priorità per l'inserimento.
# I thread devono essere inseriti prima dei reply.
sub sortmsg {
    my ($this, $lista)=@_;
    my ($conta,$priorita,$sublist,$type)=(0,'',[],$this->{type});
    while (my ($md5,$msg)=each(%$lista)) {
        $msg->{ERRORE}=101,next unless exists $type->{$msg->{TYPE}};
        push (@ { $sublist->[ $type->{$msg->{TYPE}}->priorita($msg) ] } , $md5 );
        $conta++;
    }
    return undef unless $conta;
    return $sublist;
}

sub MakeQuery {
    my ($this)=@_;
    my $tosend={};
    while (my ($key, $value)=each(%{$this->{type}})) {
         $tosend->{$key}=$value->SelectQuery;
    }
    return $tosend;
}
sub Presente {
    my ($this,$hash)=@_;
    $this->{presente}->execute($hash);
    return $this->{presente}->fetchrow_arrayref->[0]
}





1;