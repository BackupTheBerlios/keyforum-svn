package FileDonkey;
use strict;
require "WriteDonkeyProtocol.pm";
require "ReadDonkeyProtocol.pm";
use IO::Socket::INET;

# Variabili usate dalla libreria
my %server;
my %serverid;
my $searchtime=Cycle->new(30);
my %query;
my $reader=ReadDonkeyProtocol->new(); # Intepreta i pacchetti del server donkey
$GLOBAL::SQL->do("UPDATE filedonkip SET IS_CONNECT='0' WHERE 1");
$query{CercaServer}=$GLOBAL::SQL->prepare("SELECT IP,PORTA,NOME,ID FROM filedonkip WHERE IS_CONNECT='0' AND NEXT_TRY<?");
$query{UpDateTry}=$GLOBAL::SQL->prepare("UPDATE filedonkip SET NEXT_TRY=?,IS_CONNECT='0' WHERE IP=?");
$query{UpDateConnect}=$GLOBAL::SQL->prepare("UPDATE filedonkip SET IS_CONNECT='1',SRVMSG='',LAST_CONNECT=? WHERE IP=?");
$query{UpDateSrvMsg}=$GLOBAL::SQL->prepare("UPDATE filedonkip SET SRVMSG=CONCAT(SRVMSG,?) WHERE IP=?");
$query{UpDateFilesUsers}=$GLOBAL::SQL->prepare("UPDATE filedonkip SET NFILES=?,NUSERS=? WHERE IP=?");
$query{UpDateNameDesc}=$GLOBAL::SQL->prepare("UPDATE filedonkip SET NOME=?,DESCRIZIONE=? WHERE IP=?");
$query{CercaWord}=$GLOBAL::SQL->prepare("SELECT PAROLA,DIMMIN,DIMMAX,TIPO,ID FROM filedonkword WHERE IS_SEARCH='0' ORDER BY ID LIMIT 1");
$query{Cercato}=$GLOBAL::SQL->prepare("UPDATE filedonkword SET IS_SEARCH='1',SEARCH_DATE=? WHERE ID=?");
$query{UpdateFile}=$GLOBAL::SQL->prepare("UPDATE filedonkfile SET NOME=?,`SIZE`=?,TIPO=?,FONTI=?,COMPLETE=?,CODEC=?,DURATA=?,BITRATE=?,LAST_UPDATE=? WHERE HASH=? AND SERVID=?");
$query{InsertFile}=$GLOBAL::SQL->prepare("INSERT INTO filedonkfile (NOME,SIZE,TIPO,FONTI,COMPLETE,CODEC,DURATA,BITRATE,LAST_UPDATE,HASH,SERVID) VALUES(?,?,?,?,?,?,?,?,?,?,?)");
$query{UpdateHashSrv}=$GLOBAL::SQL->prepare("UPDATE filedonkhash SET FONTI=? WHERE HASH=?");
$query{UpdateHash}=$GLOBAL::SQL->prepare("UPDATE filedonkhash SET FONTI=?,SERVID=? WHERE HASH=?");
$query{InserisciHash}=$GLOBAL::SQL->prepare("INSERT INTO filedonkhash (HASH,SERVID,FONTI) VALUES(?,?,?)");
$query{SelectHash}=$GLOBAL::SQL->prepare("SELECT * FROM filedonkhash WHERE HASH=?");
my $lastrequest=0;
my $isconnect=0;
# Funzioni appartenenti all'oggetto del server
# Viene chiamata quando si riceve un messaggio
sub new {
    my ($packname,$ogg,$sock,$ip)=@_;
    my $this=bless({},$packname);
    return undef unless $GLOBAL::ctcp->AddSock($sock,(type=>'donkeyproto'));
    $GLOBAL::CLIENT{$ogg}=$this;
    $this->{Write}=WriteDonkeyProtocol->new();
    $this->{Write}->edit_var('client_port',8794);
    $this->{Write}->edit_var('id',$this->{Write}->ip2id($sock->sockhost));
    $this->{Write}->edit_var('name','KeyForumFK');
    $this->{Write}->edit_var('DisableHeader',1);
    $this->{ip}=$ip;
    $this->{id}=$serverid{$ip};
    $this->{ogg}=$ogg;
    $this->{sock}=$sock;
    $server{$ogg}=time;
    $GLOBAL::ctcp->send($ogg,$this->{Write}->hello_server);
    #print "Inviato un hello\n";
}
sub RecData {
    my ($this,$ogg,$data,$sock)=@_;
    #print "Ricevo qualcosa dal server ".$this->{ip}."\n";
    {
        $this->OffriFiles($ogg),last unless $this->{OffriFiles};
        #$this->ListaServer($ogg),last unless $this->{ListaServer};
    }
    my $valore=ReadDonkeyProtocol->Estrai($data);
    return undef unless $valore;
    $query{UpDateSrvMsg}->execute($valore->{'Server_message'}."\n",$this->{ip}) if $valore->{'Server_message'};
    $query{UpDateFilesUsers}->execute($valore->{'Server_status'}->{'Nfiles'},$valore->{'Server_status'}->{'Nusers'},$this->{ip}) if exists $valore->{'Server_status'};
    $this->update_servinfo($valore->{'Server_info_data'}->{Meta_tag_list}) if exists $valore->{'Server_info_data'};
    $this->FileTrovati($valore->{'Search_file_results'}) if exists $valore->{'Search_file_results'};
    
    #print "ECCO:".dump($valore)."\n";
}
sub FileTrovati {
    my ($this,$valore)=@_;
    print "Ricevo file\n";
    return undef if ref($valore) ne "HASH";
    my $file=$valore->{File_info_list};
    return undef if ref($file) ne "ARRAY";
    my $tmp;
    my ($numtot,$hashinsert,$filenovi)=(0,0,0);
    foreach my $buf (@$file) {
        next unless $tmp=$buf->{Meta_tag_list};
        my @dati=($tmp->{'name'} || '',$tmp->{'size'} || '0', $tmp->{'type'} || '',$tmp->{'availab'} || '0',$tmp->{'availab_compl'} || '0',
                  $tmp->{'codec'} || '',$tmp->{'durata'} || '0',$tmp->{'bitrate'} || '0',time(),$buf->{File_hash},$this->{id});
        $query{UpdateFile}->execute(@dati);
        $query{InsertFile}->execute(@dati),$filenovi++ unless $query{UpdateFile}->rows;
        $query{SelectHash}->execute($buf->{File_hash});
        if (my $riga=$query{SelectHash}->fetchrow_hashref) {
            if($riga->{SERVID}==$this->{id}) {
                $query{UpdateHashSrv}->execute($tmp->{'availab'} || '0',$buf->{File_hash});
            } elsif ($riga->{FONTI}<$tmp->{'availab'}) {
                $query{UpdateHash}->execute($tmp->{'availab'} || '0',$this->{id},$buf->{File_hash});
            }
        } else {
            $hashinsert++;
            $query{InserisciHash}->execute($buf->{File_hash},$this->{id},$tmp->{'availab'} || '0');
        }
        $tmp='';$numtot++;
    }
    print "FILESERVER: $numtot ricevuti,$filenovi nuovi,$hashinsert HASH INSERITI\n";
}
sub Cerca {
    my ($this,@dati)=@_;
    print "Cerco nei server la parola ".$dati[0]."\n";
    $GLOBAL::ctcp->send($this->{ogg},$_) if $_=$this->{Write}->Search_file(@dati);
}
sub update_servinfo {
    my ($this,$info)=@_;
    return undef if ref($info) ne "HASH";
    $query{UpDateNameDesc}->execute($info->{name},$info->{desc},$this->{ip});
}
sub ListaServer {
    my ($this,$ogg)=@_;
    $GLOBAL::ctcp->send("\x14");
    $this->{ListaServer}=1;
}
sub OffriFiles {
    my ($this,$ogg)=@_;
    $GLOBAL::ctcp->send($ogg,$this->{Write}->offer_files);
    $this->{OffriFiles}=1;
}
# Quando il buffer è vuoto, va inserito anche se non si usa
sub FreeBuff {
    
}
# Viene chiamato quando l'oggetto viene cancellato (l'altro nodo si sconnette)
sub DESTROY {
    my $this=shift;
    print "Disconnesso\n";
    delete $server{$this->{ogg}};
    $query{UpDateTry}->execute(time()+120,$this->{ip});
}

# Funzione non appartenenti all'oggetto dei server.
# Il risultato della
sub tryconn {
    my $risultato=shift;
    print "Risultato della trycon\n";
    return ConnessioneRiuscita(@_) if $risultato;
    return ConnessioneFallita(@_);
}
sub ConnessioneRiuscita {
    my ($fileno,$sock,$ip)=@_;
    print "Connesisone riuscita con $ip\n";
    $query{UpDateConnect}->execute($ip,time());
    FileDonkey->new($fileno,$sock,$ip);
    $isconnect=1;
}
# Se la connessione fallisce si imposta una nuova chiamata tra 1 minuto
sub ConnessioneFallita {
    my ($fileno,$sock,$ip)=@_;
    print "COnnessione fallita\n";
    $query{UpDateTry}->execute(time()+200,$ip);
}

# Prova a stabilire una connessione ad un server
sub Connect2Server {
    my ($ip,$port)=@_;
    my $socket=$GLOBAL::ctcp->TryConnect($ip, $port, 4);
    $GLOBAL::tryconn{fileno $socket}=\&FileDonkey::tryconn;
}
# Cerca i server da contattare nella tabella mysql (questa funzione viene chiamata automaticamente ogni 30secondi circa)
sub CercaServer {
    if (time()-$lastrequest>300) {
        return &DisconnettiTutti if $isconnect==1; # Chiude tutte le connessione se l'ultima parola cercata è di 5 minuti fa
        return;
    }
    $query{CercaServer}->execute(time);
    while (my $riga=$query{CercaServer}->fetchrow_hashref) {
        print "FileDonkey: provo a connettermi a ".$riga->{NOME}."\n";
        Connect2Server($riga->{IP},$riga->{PORTA});
        $serverid{$riga->{IP}}=$riga->{ID};
    }
    $query{CercaServer}->finish;
}
sub CercaWord {
    $query{CercaWord}->execute();
    if (my $riga=$query{CercaWord}->fetchrow_hashref) {
        $lastrequest=time();
        print "Dovrei cercare la parola ".$riga->{PAROLA}."\n";
        return undef unless $isconnect;
        return undef unless CercaTutti($riga->{PAROLA},$riga->{TIPO},int($riga->{DIMMIN})*1048576,int($riga->{DIMMAX})*1048576);
        $query{Cercato}->execute(time,$riga->{ID});
    }
    $query{CercaWord}->finish;
}
sub CercaTutti {
    my @dati=@_;
    my $num=0;
    foreach my $buf (keys(%server)) {
        $GLOBAL::CLIENT{$buf}->Cerca(@dati);
        $num++;
    }
    return $num;
}
sub DisconnettiTutti {
    print "Keyword timeout: Disconnessione da tutti i server donkey\n";
    foreach my $buf (keys(%server)) {
        $GLOBAL::ctcp->Remove($buf,1);
        delete $GLOBAL::CLIENT{$buf};
    }
    $isconnect=0;
}
push (@{$GLOBAL::CycleFunc},CycleFunc->new(30,\&FileDonkey::CercaServer));
push (@{$GLOBAL::CycleFunc},CycleFunc->new(30,\&FileDonkey::CercaWord));


1;