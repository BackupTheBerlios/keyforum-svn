package cTcp;
use strict;
use Digest::CRC;
use IO::Socket;
use IO::Select;
use Compress::Zlib;
use Carp;
use Time::HiRes;
use Itami::BinDump;
use Itami::Cycle;
use Itami::ItemGroup;
use Itami::TryConn;
use Itami::Proto::DataProto;
use Itami::Proto::CompProto;
use Itami::Proto::compbase;
sub new {
	my ($class, %argv)=@_;
	my $this={};
	#$this->{config}={};
	#$this->{config}->{BandLimit}=$argv{BandLimit} || 1500;
	$this->{IOS}={};
	$this->{IOS}->{'read'}=IO::Select->new();
	$this->{TimeOut}={};
	$this->{TimeOut}->{Correct}=Cycle->new(0.1);
	$this->{TimeOut}->{TryConn}=Cycle->new(0.3);
	$this->{TimeOut}->{MaxSleep}=Cycle->new(3);
	$this->{Socket}={};
	$this->{LastSend}=0;
	$this->{Return}={};
	$this->{tosendnum}=0;
	$this->{TryConn}=TryConn->new;
	$this->{ShowError}=$argv{ShowError} if exists $argv{ShowError};
	$this->{Group}=ItemGroup->new();
	$this->{Group}->AddGroup("_Dedicata",-1);
	return bless($this,$class);
}
sub _Protocolli {
	my $tipo=shift;
	return Proto::DataProto->new() if $tipo eq "data";
	return Proto::CompProto->new() if $tipo eq "compdata";
	return Proto::compbase->new() if $tipo eq "compbase";
	return undef;
}
sub AddSock($$%) {
	#my ($this, $sock, $type, $bandlimit,$name)=@_;
	my ($this,$sock,%conf)=@_;
	return undef unless fileno $sock;
	$this->Remove(fileno $sock,1) if exists $this->{Socket}->{fileno $sock};
	if (defined $conf{'group'} && !$this->{Group}->ExistsGroup($conf{'group'})) {
		($conf{'force_group'}) ? (delete $conf{'group'}) : (return undef);
	}
	$conf{'type'} ||= 'data';
	$conf{'bandlimit'}=0 unless defined $conf{'bandlimit'};
	#return undef if $conf{'type'} !~ /^server$|^compdata$|^data$|^compbase$/;
	$this->{IOS}->{'read'}->add($sock);
	my $socknum=fileno $sock;
	$this->{Socket}->{"$socknum"}={};
	my $questo=$this->{Socket}->{"$socknum"};
	if ($conf{'type'} eq 'server') {
		$questo->{Server}=1;
	} elsif (! ($questo->{Type} = _Protocolli($conf{'type'})) ) {
		delete $this->{Socket}->{"$socknum"};
		return undef;
	}
	if (exists $conf{'MaxSleep'}) {
		$questo->{MaxSleep}=$conf{'MaxSleep'};
		$questo->{LastRecv}=Time::HiRes::time();
	}
	$questo->{BandLimit}=$conf{'bandlimit'};
	$questo->{Sock}=$sock;
	$questo->{Group}=$conf{'group'} || '_Dedicata';
	return 1;
}
sub AddGroup {
	my ($this, $groupname,$bandalimite)=@_;
	unless ($this->{Group}->AddGroup($groupname,$bandalimite)) {
		print STDERR "Impossibile creare il gruppo $groupname\n" if $this->{ShowError};
		return undef;
	}
	return 1;
}
sub EditBWGroup {
	my ($this, $groupname,$bandalimite)=@_;
	return $this->{Group}->EditGroup($groupname,$bandalimite);
}
sub GetSockGroup {
	my ($this, $sock)=@_;
	my $socknum=fileno $sock;
	return undef if !$socknum || !exists $this->{Socket}->{"$socknum"};
	return $this->{Socket}->{"$socknum"}->{Group};
}
sub Select {
	my ($this, $timeout)=@_;
	my $start=Time::HiRes::time();
	my ($alfa, $beta,$gamma);
	my ($timeout2,$timeout3)=($timeout,0);
	LTIME: {
		do {
			(!defined($timeout)) ? ($timeout3=0.20) : (($timeout2>0.20 || $timeout2<0) ? ($timeout3=0.20) : ($timeout3=$timeout2));
			$this->_RefilBuff();
			if (($alfa, $beta,$gamma)=IO::Select::select($this->{IOS}->{'read'},$this->{TryConn}->GetIOSEL,$this->{IOS}->{'read'},$timeout3)) {
				$this->_reader($alfa) if @$alfa>0;
				$this->_writer($beta) if @$beta>0;
				$this->{Return}->{'Exception'}=$gamma if scalar(@$gamma)>0;
			}
			$this->TryConnTimeout if $this->{TimeOut}->{TryConn}->check;
			$this->CheckLastSend if $this->{TimeOut}->{MaxSleep}->check;
			last LTIME if keys(%{$this->{Return}});
			$timeout2=$timeout-(Time::HiRes::time()-$start) if defined($timeout);
			last LTIME if defined($timeout2) && $timeout2<0.001;
		} while (!defined($timeout) || $timeout2>0);
	}
	$this->_RefilBuff();
	my $return=$this->{Return};
	$this->{Return}={};
	(keys(%$return)) ? (return $return) : (return undef);
}
sub CheckLastSend {
	my $this=shift;
	my $now=Time::HiRes::time();
	foreach my $buf (values(%{$this->{Socket}})) {
		next unless exists $buf->{MaxSleep};
		next if $now-$buf->{LastRecv}<$buf->{MaxSleep};
		print "CTCP: ".$buf->{Sock}->peerhost." TIMEOUT. ".($now-$buf->{LastRecv})." secondi.\n";
		$this->Remove($buf->{Sock});
		_addItem($this->{Return}, 'Disconnessi', $buf->{Sock});
	}
}
sub TryConnTimeout {
	my $this=shift;
	my @sock=$this->{TryConn}->TimeOutSock;
	return undef if $#sock <0;
	$this->_addItems('ConnessioneFallita', @sock);
	my $buf;
	foreach $buf (@sock) {
		$this->{TryConn}->DeCheck($buf);
	}
	
}
sub _writer {
	my ($this, $writer) =@_;
	my $buf;
	foreach $buf (@$writer) {
		_addItem($this->{Return}, 'ConnessioneRiuscita', $buf);
		$this->{TryConn}->DeCheck($buf);
	}
}
sub GetIpTry {
	my ($this, $sock,$notdelete)=@_;
	my $ip=inet_ntoa($this->{TryConn}->GetIp($sock));
	#my $ip=$sock->peerhost or inet_ntoa($this->{TryConn}->GetIp($sock));
	$this->{TryConn}->Remove($sock) unless $notdelete;
	return $ip;
}
sub TryConnect {
	my $this=shift;
	return $this->{TryConn}->Tryer(@_);
}
sub _reader {
	my ($this, $reader)=@_;
	my $buf;
	foreach $buf (@$reader) {
		($this->{Socket}->{fileno $buf}->{Server}) ?
			_addItem($this->{Return}, 'ConnectionRequest', $buf) :
			$this->_recv($this->{Socket}->{fileno $buf},$buf);
	}
}
sub _addItem {
	my ($ref, $name, $ogg)=@_;	
	$ref->{"$name"}=[] unless exists $ref->{"$name"};
	push(@{$ref->{"$name"}}, $ogg);
}
sub _addItems {
	my ($this, $name, @items)=@_;
	return undef if scalar(@items)==0;
	$this->{Return}->{"$name"}=[] unless exists $this->{Return}->{"$name"};
	push(@{$this->{Return}->{"$name"}}, @items);
}
sub _recv {
	my ($this,$objsock, $sock)=@_;
	my $temp='';
	recv($sock,$temp,15000,0);
	#print "CTCP: recv\n";
	if ($temp ne '') {
		_addItem($this->{Return}, 'CanRead', $sock) if $this->_bufferizza($objsock,$temp);
	} else {
		_addItem($this->{Return}, 'Disconnessi', $sock);
		$this->Remove($sock);
	}
}
sub _bufferizza {
	my ($this,$objsock, $temp)=@_;
	$objsock->{LastRecv}=Time::HiRes::time();
	my $ret=$objsock->{Type}->_bufferizza($temp);
	return 1 if $ret>0;
	return 0 unless $ret;
	print "$objsock rimosso perchè ha inviato un pacchetto errato.\n";
	$this->Remove($objsock->{Sock});
	_addItem($this->{Return}, 'Disconnessi', $objsock->{Sock});
	return undef; 
}

sub send {
	my @dati=@_;
	my $this=shift @dati;
	my $sock=shift @dati;
	my $socknum;
	if (-S $sock) {
		$socknum=fileno($sock);
		return undef unless exists $this->{Socket}->{"$socknum"};
	} else {
		$socknum=$sock;
		return undef unless exists $this->{Socket}->{"$socknum"};
		$sock=$this->{Socket}->{"$socknum"}->{Sock};
	}
	#print STDERR "non esiste $sock\n" unless exists $this->{Socket}->{"$socknum"};
	my $sockit=$this->{Socket}->{"$socknum"};
	$sockit->{ToSendBuff}.=$sockit->{Type}->sender(@dati);
	return undef unless $sockit->{ToSendBuff};
	$this->{tosendnum}++ if $this->{Group}->AddItem(Group=>$sockit->{Group} || "_Dedicata", Item=>$socknum);
	$this->_RefilBuff();
	return 1;
}

sub recv {
	my ($this, $sock)=@_;

	$sock=fileno $sock;
	return undef unless exists $this->{Socket}->{$sock};
	return $this->{Socket}->{$sock}->{Type}->reader;
#	if ($this->{Socket}->{$sock}->{Type} eq 'data') {
#		return substr($this->{Socket}->{$sock}->{TmpRecvBuff},0,$lung || length($this->{Socket}->{$sock}->{TmpRecvBuff}),"");
#	} elsif ($this->{Socket}->{$sock}->{Type} eq 'compdata') {
#		return shift(@{$this->{Socket}->{$sock}->{RecvBuff}});
	#}

}

sub Remove {
	my ($this,$sock, $forceclose)=@_;
	my $socknum;
	return undef unless $sock;
	if (exists $this->{Socket}->{"$sock"}) {
		
		$socknum=$sock;
		$sock=$this->{Socket}->{"$socknum"}->{Sock};
		$this->{IOS}->{'read'}->remove($sock);
	} else {
		#(exists $this->{Socket}->{"$sock"}) ? ($socknum=$sock)  : (return undef);
		$this->{IOS}->{'read'}->remove($sock);
		$socknum=fileno($sock) or return undef;
		return undef unless exists $this->{Socket}->{"$socknum"};
	}
	#print "CTCP: rimosso $sock con $socknum\n";
	if (exists $this->{Socket}->{"$socknum"}->{Group}) {
		--$this->{tosendnum} if $this->{Group}->RemoveItem($this->{Socket}->{"$socknum"}->{Group} || "_Dedicata",$socknum);
	}
	delete ($this->{Socket}->{"$socknum"});
	close $sock if $forceclose && $sock;
}
sub DESTROY {
	my $sock=shift;
}
# $this->{Socket}->{fileno $sock}->{BandLimit}  Limite di banda per singolo socket
# $this->{tosend}->{$sock}			GLi sock con dati nel buffer=>Banda dedicata
# $this->{config}->{BandLimit}		Limite di banda generale per tutti gli host
# $this->{Socket}->{$sock}->{ToSendBuff} Dati dello socket da spedire
sub _RefilBuff {
	my $this=shift;
	
	return undef unless $this->{tosendnum};
	return undef unless $this->{TimeOut}->{Correct}->check();
	my $TempoPassato=Time::HiRes::time()-$this->{LastSend};
	$this->{LastSend}=Time::HiRes::time();
	$TempoPassato=2 if $TempoPassato>2 || $TempoPassato<=0;
	my ($group,$item,$division,$banda,$resto,$endbuff);
	$resto=0;
	foreach $group ($this->{Group}->GroupList()) {
		
		$division=$this->{Group}->Division($group);
		$banda=int($division*$TempoPassato)+1 if $division>0;
		foreach $item ($this->{Group}->ItemList($group)) {
			unless ($this->{Socket}->{$item}->{Sock}) {
				print "Socket vuoto di $item $group\n";
				$this->{Group}->RemoveItem($group,$item);
				next;
			}
			if ($division==0) {
				$banda=$this->{Socket}->{$item}->{BandLimit};
				($banda>0) ? ($banda=int($banda*$TempoPassato)+1) : ($banda=-1);
			} elsif($division<0) {
				$banda=-1;
			} else {
				$banda+=$resto;
			}
			($resto,$endbuff)=_addtobuff($this->{Socket}->{$item}->{Sock},$banda,\$this->{Socket}->{$item}->{ToSendBuff});
			if ($endbuff) {
				#$this->{tosendnum}-- if $this->{Group}->RemoveItem($this->{Socket}->{$item}->{Group},$item);
				$this->{tosendnum}-- if $this->{Group}->RemoveItem($group,$item);
				_addItem($this->{Return},'EmptyOutBuff',$this->{Socket}->{$item}->{Sock});
			}
		}
		$resto=0;
	}
	
}

# rimpie il buffer di uno socket e ritorna il numero di byte che non ha spedito.
# Il terzo valore dei parametri (i dati) deve essere passato come reference ad uno scalare.
sub _addtobuff {
	my ($sock, $bandlimit, $dati)=@_;
	my ($pacchetto,$spediti,$dimpacchetto,$resto)=(0,0,0,0);
	$dimpacchetto=1400 if $bandlimit<0;
	while (_canwrite($sock)) {
		if ($bandlimit>=0) {
			($bandlimit>1400) ? ($dimpacchetto=1400) : ($dimpacchetto=$bandlimit);
			last if $dimpacchetto<=0;
			$bandlimit-=$dimpacchetto;
		}
		last if length($$dati)==0;
		#$pacchetto=substr ($$dati,0,$dimpacchetto,"");
		CORE::send $sock,substr($$dati,0,$dimpacchetto,""),0;
	}
	$resto=$bandlimit if $bandlimit>0;
	return ($resto, !length($$dati));
}
sub _canwrite {
	my $sock=shift;
	my $rin='';
	return undef unless defined $sock;
	vec($rin,fileno($sock),1) = 1;
	return select(undef,$rin,undef,0);
}


1;
__END__


=head1 NAME

IO::Socket::cTcp - Utilità per la gestione di connessioni multiple

=head1 SYNOPSIS

    use IO::Socket::cTcp;
	
=head1 REQUIRE
use IO::Socket;
use IO::Select;
use Compress::Zlib;
use Carp;
use Time::HiRes;
use Digest::CRC;


=head1 DESCRIPTION

C<IO::Socket::cTcp> si trova ad un livello superiore rispetto a L<IO::Select>.
Può trovarsi utile nel caso si volessero usare connessioni multiple
con un controllo dei buffer e sulla banda in uscita.


=head1 CONSTRUCTOR

=over 4

=item new ( [ARGS] )

Crea un nuovo oggetto C<IO::Socket::cTcp> .
Gli argomenti sono opzionali.
Es:
	new (BandLimit=>1500);  #Banda limite in upload impostato a 1500 byte al secondo (connessione generica)

=back

=head2 METHODS

=over 4

=item AddSock(socket,[Type],[BandLimit],[Name])

Socket è l'oggetto socket che si intende gestire con la libreria cTcp.
"Type" specifica il tipo dello socket.
I tipi di dati possono essere "server,compdata,data";
|->data: Oggetto socket standard con un altro client
|->server: L'oggetto socket specificato viene gestito come server
	e avviserà quando qualcuno tenta di connettersi su di esso.
|->compdata: Se specificato questo tipo anche l'altro client deve fare uso della medesima libreria cTcp.
 Specificando questo tipo il pacchetto che si tenta di inviare viene compresso con zlib.
 Oltre che l'invio di stringhe standard si possono inviare anche hash.
 La sicurezza del protocollo non è garantita, è ancora alle prime ver.
bandlimite è la banda che si intende dedicare a questo socket.
Se non è specificato bandlimit, la banda generica usata nel costruttore viene
suddivisa tra gli socket che non hanno specificato bandlimit.
Se bandlimit è negativo è come assegnare a bandlimit un valore infinito.
"Name" è il nome da assegnare allo socket nel caso si voglia identificarlo ad un gruppo.
 Ad esmepio ci possono essere 5 socket con il nome HTTP_SERVER e altri 2 socket con 
 il nome SMTP_SERVER.
 
 
 =item SockName(socket, [name])
 Se name è definito cambio il nome allo socket.
 Il valore di ritorno è il nome del gruppo dello socket.
 Il nome può essere definito quando aggiungiamo lo socket con il metodo AddSock o usando SockName().
 

=item Select([timeout])

Il metodo Select deve essere chiamato costantemente.
Resta in ascolto di tutti gli socket impostati sull'oggetto cTcp per un tempo massimo impostato su "timeout".
Se timeout è undef il tempo di attesa è infinito fino a quando non accade
qualcosa in uno degli socket da tenere sotto controllo.
Il metodo Select torna un riferimento ad un hash. Ogni elemento dell'hash ha un riferimento ad un array do oggetti socket.
Le chiavi dell'hash che è ritornato dal metodo possono essere:
exception-La funzione Select di Io::Select ha tornato un exception, vedere Io::Select

ConnessioneFallita-Se avete tentato di creare una nuova connessione con il metodo TryConnect()
questo è uno dei valori che vi ritorna per le connessioni che dopo il timeout non sono ancora connesse.

ConnessioneRiuscita-Torna se avete tentato una connessione con TryConnect().
Contiene la lista degli socket correttamente connessi.

ConnectionRequest-Se state monitorando oggetti socket di tipo "server" questo valore vi può ritornare.
Contiene la lista di socket server sui quali c'è una richiesta di connessione.
Per ottenere lo socket in attesa di una connessione su quella porta basta prendere
lo socket server ritornato ed eseguire un $serverobj->accept();

CanRead-Sono disponibili dei dati in lettura su quello socket.
 possiamo eseguire un $cTcpObj->recv($socket);
 Nel caso la connessione sia un comdata la funzione recv può ritornare un riferimento ad un hash.
	
Disconnessi-Gli socket di tipo "compdata" e "data" disconnessi.

EmptyOutBuff-Quando i buffer in uscita sono stati correttamente inviati vengono inseriti in questa chiave.
 Può essere utile nel caso si spediscano pacchetti molti grandi
 e appena l'avete finito di inviare volete riempire nuovamente il buffer.
 E' sempre una lista di socket.

=item TryConnect(Ip or Host, porta,[timeout])

Tenta di effettuara una connessione con l'ip e porta specificata.
I risultati sono ritornati dalla funzione Select.
Il metodo torna l'oggetto socket Non-Bloccante di quella connessione.

=item send(socket,Datas,[Compressed])

- socket è lo socket al quale si intendono inviare i dati.

- Datas è una stringa o un riferimento ad un HASH (hashref).
Attenzione! Può essere solo un hashref solo nel caso che il tipo dello socket specificato sia 'compdata'.

- Compressed è un valore boleano, ha significato solo se il tipo è 'compdata'.
Se è TRUE forza la NON compressione. Di standard viene spedito compresso un pacchetto compdata.

=item recv(socket)

socket sul quale leggere dati.
Torna qualcosa solo se lo socket contiene veramente dei dati, altrimenti torna undef.
Se l'oggetto socket è compdata il valore tornato può essere un riferimento ad un HASH!
Se l'oggetto socket è compdata il pacchetto è disponibile in lettura solo se è stato ricevuto interamente.

=item Remove(socket)

L'oggetto socket specificato non viene più tenuto sotto osservazione.
Eventuali buffer di dati in uscita ed in entrata vengono immediatamente cancellati.

=back

=head1 EXAMPLE

Portscanner tramite la funzione TryConnect:

	use IO::Socket::cTcp;
	$ctcp=cTcp->new();
	my $port=1;
	while (1) {
		if ($ref=$ctcp->Select(0.9)) { 
			foreach $buf (keys(%$ref)) {
				print "$buf this socket:\n";
				foreach $socket (@{$ref->{$buf}}) {
					print "\t$socket:",$socket->peerhost,"-",$socket->peerport,"\n";
					# se lo socket non è connesso peerhost e peerport non tornato alcun valore.
				}
			}
		} else {
			print "nothing now\n";
		}
		$ctcp->TryConnect("127.0.0.1",$port,3);
		$port++;
		$ctcp->TryConnect("127.0.0.1",$port,3);
		$port++;
	}

=head1 AUTHOR

DanieleG detto anche LordSaga640 °_°

=head1 SEE ALSO

L<IO::Socket>, L <IO::Select>

=cut