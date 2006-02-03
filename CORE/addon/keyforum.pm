package keyforum;
#use Data::Dump qw(dump);
$GLOBAL::Fconf={};
$GLOBAL::ForUtility={};
$GLOBAL::SelectQuery='';
$GLOBAL::PubKey={};
$GLOBAL::Permessi={};
$GLOBAL::ExtVar={};
use Itami::Cycle;
require ShareDB;
require "versione.pm";
require "GestIp.pm";
require "kfdebug.pm";
require "FRule2.pm";
require "LoadForumConfig.pm";
require "ForumUtility.pm";
require "permessi.pm";
require "extvar.pm";
use strict;
#%GLOBAL::ItemSubscribe
#%GLOBAL::Gate
#%GLOBAL::ItemSend
#%GLOBAL::Rule
#%GLOBAL::IP;


my $brcast=Cycle->new(20);
my $ipwebsite=Cycle->new(7200);
sub Check {
	my $ctcp=$GLOBAL::ctcp;
	if ($brcast->check) {
		my $gate;
		#print "invio hash random\n";
		foreach $gate (values(%GLOBAL::Gate)) {
			$gate->SendRandomHash(50); 	# Invia 50 hash random alle persone iscritte
		}
		BigAutoFlush();
	}
	my $numitem=scalar(keys(%GLOBAL::ItemSubscribe));
	my $numgate=scalar(keys(%GLOBAL::Gate));
	if ($numitem<$numgate*5 && $numitem<18) {
		foreach my $forums (keys(%GLOBAL::IP)) {
			$GLOBAL::IP{$forums}->Connect($ctcp) if $GLOBAL::Gate{$forums}->Iscritti<4;
		}
	}
	return undef unless $ipwebsite->check;
	foreach my $ipogg (values(%GLOBAL::IP)) {
		$ipogg->iamlive();
	}
}

sub new {
	my ($ogg,$sock,$serv)=@_;
	return undef if GestIP::JustConn($sock->peerhost);
	return undef if exists $GLOBAL::ItemSubscribe{$ogg};
	return undef if scalar(keys(%GLOBAL::ItemSubscribe))>25;
	return undef unless $GLOBAL::ctcp->AddSock($sock,(group=>$GLOBAL::CONFIG->{SHARESERVER}->{TCP}->{GROUP},force_group=>1, type=>'compdata',MaxSleep=>130));
	my $this=bless({},'keyforum');
	$this->{num}=$ogg;$this->{sock}=$sock;
	$this->{'peerhost'}=$sock->peerhost;
	$GLOBAL::CLIENT{$ogg}=$this;
	GestIP::Connesso($sock->peerhost);
	kfdebug::scrivi(9,2,7,fileno($sock),$sock->peerhost); # Connesso alla board con successo.
	$GLOBAL::ItemSubscribe{$ogg}={};
	$GLOBAL::ItemSubscribe{$ogg}->{SuoJoin}={};
	$GLOBAL::ItemSubscribe{$ogg}->{DatiClient}={};
	$GLOBAL::ItemSend{$ogg}={};
	Hello($ogg); # Aggiunge ai dati da spedire le info del mio client
	JoinInto($ogg); # Aggiunge ai dati da spedire la lista dei Forum ai quali voglio entrare
	IPrequest($ogg); # Richiedo la lista di IP solo delle board con meno di 150IP.
	$GLOBAL::ItemSubscribe{$ogg}->{DatiClient}->{IP}=$sock->peerhost();
	AutoFlush($ogg); # Spedisco i dati
	return 1;
}
sub DeleteItem {
	my $this=shift;
	my ($ogg,$sock)=($this->{num},$this->{sock});
	foreach my $buf (keys(%{$GLOBAL::ItemSubscribe{$ogg}->{SuoJoin}})) {
		#print "SHARE SESSION: rimuovo $ogg da $buf\n";
		$GLOBAL::Gate{$buf}->RemoveItem($ogg) if exists $GLOBAL::Gate{$buf};
	}
	delete $GLOBAL::ItemSend{$ogg};
	delete $GLOBAL::ItemSubscribe{$ogg};
	GestIP::Disconnesso($this->{peerhost});
	#return undef unless exists $this->{Item}->{$ogg};
	#return delete ${$this->{Item}}->{$ogg};
}
sub CheckGate {
	return $GLOBAL::Gate{$_[0]} if exists $GLOBAL::Gate{$_[0]};
}
sub tryconn {
	my ($success,$ogg, $sock,$ip)=@_;
	GestIP::remove2try($ip);
	return undef unless $success;
	if ($sock->peerhost eq $sock->sockhost) { # Se è localhost
		GestIP::Banna($sock->peerhost);
		close($sock);
	}
	new($ogg, $sock);
}
sub DESTROY {
	my $this=shift;
	$this->DeleteItem();	
}
sub AddGate {
	my ($name, $gate,$rule,$config)=@_;	
	return undef if exists $GLOBAL::Gate{$name};
	$GLOBAL::Gate{$name}=$gate;
	$GLOBAL::Rule{$name}=$rule;
	$gate->Declare(\&Sender);
	$GLOBAL::IP{$name}=GestIP->new(unpack("H*",$name),$GLOBAL::SQL,$config->{SOURCER}->{DEFAULT},$config->{TCP}->{PORTA});
	return 1;
}
sub FreeBuff {
}
sub RecData {
	my ($this,$ogg, $hashref, $sock)=@_;
	#print "ricevo da $sock\n";
	return undef if ref($hashref) ne "HASH";
	my ($key, $value,$AddedRows, $ReqRows);
	#while (my ($gate, $data)=each(%$hashref)) {
		#print $gate."-".$data."\n";
	#}
	
	ReadHello($ogg,$hashref->{Hello},$sock) if exists $hashref->{Hello};
	ReadJoinInto($ogg, $hashref->{JoinInto},$sock->peerhost) if exists $hashref->{JoinInto};
	ReadJoined($ogg, $hashref->{Joined},$sock) if exists $hashref->{Joined};
	ReadIPrequest($ogg, $hashref->{IPrequest}) if exists $hashref->{IPrequest};
	ReadIpList($ogg, $hashref->{IpList}) if exists $hashref->{IpList};
	foreach $key (keys(%$hashref)) {
		next unless exists $GLOBAL::Gate{$key};
		$GLOBAL::Gate{$key}->RecvData($ogg,$hashref->{$key});
		delete $hashref->{$key};
	}
	$value='';
	&BigAutoFlush;
	#($BroCast) ? (&BigAutoFlush) : (AutoFlush($ogg)); # Inivio i dati se presenti nel buffer
}


#########################
# Spedisce i dati
#########################

sub AutoFlush {
	my $dest=shift;
	return undef unless exists $GLOBAL::ItemSubscribe{$dest};
	return undef if scalar(keys(%{$GLOBAL::ItemSend{$dest}}))==0; # Se non ci sono dati da spedire esco
	#print "provo a spedire\n";
	#while (my ($gate, $data)=each(%{$ItemSend{$dest}})) {
	#	eval {print $gate."<->".$data." con ".scalar(keys(%$data))." elementi\n";}
	#}
	$GLOBAL::ctcp->send($dest,$GLOBAL::ItemSend{$dest});
	$GLOBAL::ItemSend{$dest}={};
}
sub BigAutoFlush {
	my ($key, $value);
	#print "provo a spedire a tutti\n";
	while (($key, $value)=each(%GLOBAL::ItemSend)) {
		next if scalar(keys(%$value))==0; # Se non ci sono dati da spedire passo al prossimo
		$GLOBAL::ctcp->send($key,$value);
		$GLOBAL::ItemSend{$key}={};
	}
}
#############################
# Da leggere
#############################
sub ReadHello {
	my ($ogg, $helloref, $sock)=@_;
	return undef if ref($helloref) ne "HASH";
	$GLOBAL::ItemSubscribe{$ogg}->{DatiClient}=$helloref;
	$GLOBAL::ItemSubscribe{$ogg}->{DatiClient}->{IP}=$sock->peerhost();
}
sub ReadJoinInto {
	my ($ogg, $JoinIntoRef,$ip)=@_;
	return undef if ref($JoinIntoRef) ne "ARRAY";
	my $buf;
	foreach $buf (@$JoinIntoRef) {
		next unless exists $GLOBAL::Gate{$buf};
		$GLOBAL::Gate{$buf}->NewSession($ogg);
		$GLOBAL::ItemSubscribe{$ogg}->{SuoJoin}->{$buf}=time() unless exists $GLOBAL::ItemSubscribe{$ogg}->{SuoJoin}->{$buf};
		$GLOBAL::IP{$buf}->Presente($ip,$GLOBAL::ItemSubscribe{$ogg}->{DatiClient});
	}
	Joined($ogg);
	return 1;
}
sub ReadJoined {
	my ($ogg, $JoinedRef)=@_;
	return undef if ref($JoinedRef) ne "HASH";
	#print "ricevo Joined\n";
	my $buf;
	$GLOBAL::ItemSubscribe{$ogg}->{MioJoin}=$JoinedRef;
	return 1;
}
sub ReadIPrequest {
	my ($ogg, $ipreqRef)=@_;
	return undef if ref($ipreqRef) ne "ARRAY";
	kfdebug::scrivi(16,2,12,$ogg); # Richiede una lista di IP validi
	my ($buf,$listaip, $num)=('',{},0);
	foreach $buf (@$ipreqRef) {
		next if length($buf) != 20;
		next unless exists $GLOBAL::IP{$buf};
		$listaip->{$buf}=$GLOBAL::IP{$buf}->IpRandList || next;
		$num++;
	}
	AutoFlush($ogg) if exists $GLOBAL::ItemSend{$ogg}->{IpList};
	$GLOBAL::ItemSend{$ogg}->{IpList}=$listaip if $num>0;
	return 1;
}
sub ReadIpList {
	my ($ogg, $IpListRef)=@_;
	return undef if ref($IpListRef) ne "HASH";
	kfdebug::scrivi(16,2,13,$ogg); # Mi invia una lista di IP validi
	my ($board, $lista,$singolo);
	while (($board, $lista)=each %$IpListRef) {
		next if length($board) != 20;
		next unless exists $GLOBAL::IP{$board};
		next if ref($lista) ne "ARRAY";
		next if scalar(@$lista)>50;
		foreach $singolo (@$lista) {
			next if ref($singolo) ne "HASH";
			$GLOBAL::IP{$board}->Aggiungi($singolo);
		}
		$GLOBAL::IP{$board}->Conteggio;
	}
	
}
#############################
# Quello che deve spedire
#############################
sub Sender {
	my ($name, $subname,$dest,$ref)=@_;
	return undef unless exists $GLOBAL::ItemSubscribe{$dest};
	$GLOBAL::ItemSend{$dest}->{$name}={} unless exists $GLOBAL::ItemSend{$dest}->{$name};
	AutoFlush($dest) if exists $GLOBAL::ItemSend{$dest}->{$name}->{$subname};
	$GLOBAL::ItemSend{$dest}->{$name}->{$subname}=$ref;
	return 1;
}
sub Joined {
	my $dest=shift;
	return undef unless exists $GLOBAL::ItemSubscribe{$dest};
	AutoFlush($dest) if exists $GLOBAL::ItemSend{$dest}->{Joined};
	$GLOBAL::ItemSend{$dest}->{Joined}={%{$GLOBAL::ItemSubscribe{$dest}->{SuoJoin}}};
	return 1;
}
sub IPrequest {
	my $dest=shift;
	return undef unless exists $GLOBAL::ItemSubscribe{$dest};
	AutoFlush($dest) if exists $GLOBAL::ItemSend{$dest}->{IPrequest};
	my ($board,@board_ip);
	foreach $board (keys(%GLOBAL::IP)) {
		next if $GLOBAL::IP{$board}->GetNumIp > 150;
		push(@board_ip,$board);
	}
	$GLOBAL::ItemSend{$dest}->{IPrequest}=\@board_ip if $#board_ip >-1;
	return 1;
}
sub Hello {
	my $dest=shift;
	return undef unless exists $GLOBAL::ItemSubscribe{$dest};
	AutoFlush($dest) if exists $GLOBAL::ItemSend{$dest}->{Hello};
	$GLOBAL::ItemSend{$dest}->{Hello}={ForumVer::Dumper()};
	return 1;
}
sub JoinInto {
	my $dest=shift;
	return undef unless exists $GLOBAL::ItemSubscribe{$dest};
	AutoFlush($dest) if exists $GLOBAL::ItemSend{$dest}->{JoinInto};
	$GLOBAL::ItemSend{$dest}->{JoinInto}=[];
	my $gate;
	foreach $gate (keys(%GLOBAL::Gate)) {
			push(@{$GLOBAL::ItemSend{$dest}->{JoinInto}}, $gate);
	}
	return 1;
}

sub MakeShareSession {
	my ($ForumName, $value)=@_;
	my $public_key=$value->{PKEY} || return errore("Chiave pubblca del forum $ForumName non valida\n");
	# Carico la chiave pubblica del forum che carico.
	# La chiave pubblica è di vitale importanza e serve non solo per identificare un forum
	# ma anche per autentificare le operazioni dell'admin.
	# $public_key=ConvData::Base642Dec($public_key);
	return errore("La chiave pubblica del forum $ForumName non è valida.\n") if $public_key =~ /\D/ || length($public_key)<200 || length($public_key)>500;
	
	# Creo la chiave che identificherà quello specifico forum (in base alla chiave pubblica)
	my $Identificatore=Digest::SHA1::sha1("$public_key");
	$GLOBAL::PubKey->{$Identificatore}=$public_key;
	#Carico la configurazione impostata dall'admin per questo forum che si crea
	LoadForumConfig::Load($ForumName,$Identificatore);
	$GLOBAL::ForUtility->{$Identificatore}=ForumUtility->new($ForumName,$Identificatore);
	$GLOBAL::ExtVar->{$Identificatore}=ExtVar->new($ForumName,$Identificatore);
	# Creo l'oggetto Rule.
	# La classe FRule è l'abbreviazione di ForumRule, regole del forum.
	# I metodi dell'oggetto mi dicono se certe azioni da parte di certi utenti sono permesse
	# in base anche alla configurazione caricata da $ForumName."_conf" .
	$GLOBAL::Permessi->{$Identificatore}=Permessi->new($ForumName,$Identificatore);
	my $rule=FRule->new($ForumName, $Identificatore,$public_key) || return errore("Errore nel caricamento della configurazione di $ForumName\n");
	
	# Creo l'oggetto che mi permetterà di scambiare le righe delle varie tabelle.
	# Questo oggetto particolare si chiama Gate (non lavora con socket).
	# Il compito dell'iterazione tra gli socket è di una classe a livello superiore.
	my $dbmpath=$GLOBAL::CONFIG->{TEMP_DIRECTORY}."/".unpack("H*",$Identificatore)."_hash.dbm";
	my $Gate=ShareDB->new($GLOBAL::SQL, $dbmpath,$ForumName) or return errore("Impossibile creare l'oggetto di condivisione database\n");

	my %ftable = (newmsg => $ForumName."_newmsg",
				  membri=> $ForumName."_membri",
				  admin=> $ForumName."_admin",
				  reply=> $ForumName."_reply",
				  extdati=> $ForumName."_extdati",
				 );
	#print  dump($rule->MakeQuery)."<-\n";
	$Gate->tab_conf(
		ShareName=>$Identificatore,
		Identificatore=>"HASH",
		Table=>$ForumName."_congi",
		Type=>"TYPE",
		LastSend=>"LAST_SEND",
		Query=>$rule->MakeQuery()
	) or return errore("Impossibile completare alcune operazione per il forum $ForumName\n");
	AddGate($Identificatore,$Gate,$rule,$GLOBAL::CONFIG->{SHARESERVER});
	print "KEYFORUM: Aggiunta la board con ID ".unpack("H*",$Identificatore)."\n";
}
sub StartUp {
	errore("Non è specificata una porta TCP per KeyForum!\n") unless $GLOBAL::CONFIG->{SHELL}->{TCP}->{PORTA};
	my $keyforum = IO::Socket::INET->new(Listen => 5,
			LocalPort => $GLOBAL::CONFIG->{SHARESERVER}->{TCP}->{PORTA},
			LocalAddr => $GLOBAL::CONFIG->{SHARESERVER}->{TCP}->{BIND},
			Proto => 'tcp'
		) or errore("Impossibile creare il server SHARESERVER sulla porta ".$GLOBAL::CONFIG->{SHARESERVER}->{TCP}->{PORTA}."\nErrore:$!\n");
	print "KEYFORUM: Avviato ed in ascolto sulla porta ".$GLOBAL::CONFIG->{SHARESERVER}->{TCP}->{PORTA}."\n";
	$GLOBAL::SERVER{fileno($keyforum)}=\&keyforum::new;
	$GLOBAL::ctcp->AddSock($keyforum,(type=>'server',group=>$GLOBAL::CONFIG->{SHARESERVER}->{TCP}->{GROUP})) or errore("Errore non previsto nell'aggiunta del'oggetto server KeyForum\n");
	# Configuro KeyForumDebug
	kfdebug::mysqllog($GLOBAL::CONFIG->{KEYFORUM}->{DEBUG}->{LEVEL},
			$GLOBAL::CONFIG->{KEYFORUM}->{DEBUG}->{FILTRO},$GLOBAL::SQL) if $GLOBAL::CONFIG->{KEYFORUM}->{DEBUG}->{TYPE} eq 'mysql';
	push (@{$GLOBAL::CycleFunc},CycleFunc->new(20,\&keyforum::Check)); #Aggiungo la funzione check che si esegua ogni 20sec
	ForumVer::SetVar("TCP_PORT",$GLOBAL::CONFIG->{SHARESERVER}->{TCP}->{PORTA});
	ForumVer::SetVar("UDP_PORT",$GLOBAL::CONFIG->{SHARESERVER}->{UDP}->{PORTA});
	ForumVer::SetVar("DESC", $GLOBAL::CONFIG->{SHARESERVER}->{TCP}->{NICK});
	$GLOBAL::CONFIG->{TEMP_DIRECTORY}="temp_bf" if length($GLOBAL::CONFIG->{TEMP_DIRECTORY})==0;
	mkdir $GLOBAL::CONFIG->{TEMP_DIRECTORY} unless -d $GLOBAL::CONFIG->{TEMP_DIRECTORY};
	unlink glob($GLOBAL::CONFIG->{TEMP_DIRECTORY}."/*.dbm.dir");
	unlink glob($GLOBAL::CONFIG->{TEMP_DIRECTORY}."/*.dbm.pag");
	my ($key,$value);
	MakeShareSession($key, $value) while ($key, $value)=each %{$GLOBAL::CONFIG->{SHARE}};
}

sub errore {
	my $errore=shift;
	die("Errore nel modulo keyforum.pm : $errore\n");
}
&StartUp;

1;