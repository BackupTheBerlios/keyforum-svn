print "Caricamento Librerie...\n";
#require Devel::Symdump;
sleep 1;
use strict;
my ($SQL, $CONFIG, %SERVER,%CLIENT);
#GloIni::GetVal("start_browser") and fork and sleep(4) and exit system "explorer ".GloIni::GetVal("start_browser");
LoadLibrary("Itami::GloIni");
LoadLibrary("DBI");
LoadLibrary("Itami::cTcp");
LoadLibrary("Itami::LogFile");
LoadLibrary("Itami::ConvData");
LoadLibrary("Itami::stati");
#LoadLibrary("Itami::HTTPServerMs");
LoadLibrary("Itami::Cycle");
LoadLibrary("Math::Pari");
LoadLibrary("Crypt::RSA");
LoadLibrary("Digest::MD5");
LoadLibrary("Digest::SHA1");
LoadLibrary("File::Glob ':glob'");
capturl(@ARGV) if $#ARGV>-1;
#use Devel::Size;
require "ShareDB.pm";
require "FRule.pm";
require "ShSession.pm";
require "SignTime.pm";
require "PerlScript.pm";
require "GestIp.pm";
require "kfdebug.pm";
require "kfshell.pm";
print "Connessione all'SQL server\n";

# Carico la configurazione base e avvio la connessione con il DataBase
&Init;

# Carica la configurazione all'interno della tabella "config"
&LoadConf;

# Creo l'oggetto che gestirà tutte le connessioni
my $ctcp=cTcp->new();

# Crea gli oggetti secondo la configurazione caricata.
&MakeItem;
ShSession::Declare(\&sender);
my $loop=Cycle->new(25);
#my $test=Cycle->new(20);
#print Devel::Symdump->inh_tree;
my ($ref, $num);
while (1) {
	#if ($test->check) {
	#	print "CTCP:".Devel::Size::total_size($ctcp)." ";
	#	print "WEBSERVER:".Devel::Size::total_size(\%SERVER)."\n";
	#}
	if($ref=$ctcp->Select(5)) {
		# Richiesta di connessione su un server
		if (exists $ref->{ConnectionRequest}) {
			foreach my $value (@{$ref->{ConnectionRequest}}) {
				my $socket=$value->accept;
				# La riga seguente deve essere decommentata per evitare i localhost
				next unless exists $SERVER{fileno $value};
				$num=fileno $socket;
				my $srv=$SERVER{fileno $value};
				if ($srv == -2) {
					if ($socket->peerhost() eq $socket->sockhost()) {
						GestIP::Banna($socket->peerhost());
						close($socket);
						next;
					}
					next unless $ctcp->AddSock($socket,(group=>$ctcp->GetSockGroup($value),force_group=>1, type=>'compdata',MaxSleep=>130));
					(ShSession::AddItem($num,$socket)) ?
						($CLIENT{$num}=-2) :
							($ctcp->Remove($socket,1));
				} elsif (ref($srv) eq "CODE") {
					next unless $ctcp->AddSock($socket,(MaxSleep=>200,type=>'compbase'));
					$CLIENT{$num}=$srv->($num);
				}
			}
		}
		# Possiamo leggere dati da un canale registrato
		if (exists $ref->{CanRead}) {
			foreach my $value (@{$ref->{CanRead}}) {
				next unless exists $CLIENT{fileno $value};
				# Se l'oggetto è uguale a -2 significa che appartiene alla sessione di ShareDB
				# Altrimenti ad un webserver.
				my $cli=$CLIENT{fileno $value};
				if ($cli == -2) {
					ShSession::RecvData(fileno $value, $_, $value) while $_=$ctcp->recv($value);
				} elsif (ref($cli) eq "kfshell") {
					$cli->RecData($_) while $_=$ctcp->recv($value);
				}
			}
		}
		# Si disconnette uno socket
		if (exists $ref->{'Disconnessi'}) {
			foreach my $value (@{$ref->{'Disconnessi'}}) {
				
				my $ind=fileno $value;

				#print $CLIENT{$fddf}." -\n";
				next unless exists $CLIENT{$ind};
				if ($CLIENT{$ind} == -2) {
					ShSession::DeleteItem($ind,$value);
					kfdebug::scrivi(10,2,6,$ind,$value->peerhost);  # Si è disconnesso X
				} elsif (ref($CLIENT{$ind}) eq "kfshell") {
					delete $CLIENT{$ind};
				}
				delete $CLIENT{fileno $value};
				close $value;
			}
		}
		# Il buffer di uno socket che conteneva dei dati è vuoto.
		if (exists $ref->{'EmptyOutBuff'}) {
			foreach my $value (@{$ref->{'EmptyOutBuff'}}) {
				next unless exists $CLIENT{fileno $value};
				if ($CLIENT{fileno $value} == -2) {
					#ShSession::DeleteItem(fileno $value);
				} elsif (ref($CLIENT{fileno $value}) eq "kfshell") {
				}
			}
		}
		if (exists $ref->{'ConnessioneRiuscita'}) {
			foreach my $value (@{$ref->{'ConnessioneRiuscita'}}) {
				$ctcp->GetIpTry($value);
				kfdebug::scrivi(9,2,1,fileno($value),$value->peerhost);#"Connessione riuscita con ".$value->peerhost.". Assegnato ID ".fileno($value));
				GestIP::remove2try($value->peerhost);
				$ctcp->AddSock($value,(group=>$CONFIG->{SHARESERVER}->{TCP}->{GROUP},force_group=>1, type=>'compdata',MaxSleep=>130));
				(ShSession::AddItem(fileno $value,$value)) ?
					($CLIENT{fileno $value}=-2) :
						($ctcp->Remove($value,1));
			}
		}
		if (exists $ref->{'ConnessioneFallita'}) {
			foreach my $value (@{$ref->{ConnessioneFallita}}) {
				my $ip=$ctcp->GetIpTry($value);
				kfdebug::scrivi(15,2,2,undef,$ip); #"Connessione fallita con $ip");
				GestIP::remove2try($ip);
				close($value);
			}
		}
	}
	ShSession::Check($ctcp) if $loop->check;
}
sub sender {return $ctcp->send($_[0],$_[1]);}

sub destroy {
	my ($item,$forceclose)=@_;
	$forceclose=1 unless defined $forceclose;
	#print "distruggo $item\n";
	delete $CLIENT{$item};
	$ctcp->Remove($item,$forceclose);
}

sub Init {
	my $logga=shift;
	# Carico il file di configurazione di KeyForum.
	# La classe terrrà le informazioni all'interno della propria classe.
	GloIni::load("config.ini") or die("Impossibile aprire il file config.ini\n");

	# Redirigo l'STDERR Handle al file specificato nel file config.ini
	if (!$logga && ($_=GloIni::GetVal("LogFile"))) {
		tie (*STDERR, "LogFile",$_) or die("Impossibile collegare STDERR al file $_\n");	
	}
	
	# Mi connetto al server SQL
	unless ($SQL = DBI->connect("DBI:mysql:database=".GloIni::GetVal("DB_name")
							.";host=".GloIni::GetVal("DB_host")
							.";port=".GloIni::GetVal("DB_port"),
							GloIni::GetVal("DB_user"),GloIni::GetVal("DB_password"),
							{
								PrintError => 1,
								PrintWarn=>1
							 })) {
		print "Impossibile connettersi al database MySQL per i suguenti motivi:\n";
		print $DBI::errstr;
		print "\nIl demone di MySQL non è stato avviato o la porta tcp è errata\n" if $DBI::errstr=~ /connect/;
		print "\nL'utente o la password per connettersi a MySQL server sono errati\n" if $DBI::errstr=~ /denied/;
		print "Premi Invio per uscire...\n";
		<STDIN>;
		exit;
	}
	kfdebug::mysqllog(GloIni::GetVal("DebugLevel"),GloIni::GetVal("DebugType"),$SQL) if GloIni::GetVal("DebugMysql");
	kfdebug::scrivi_force(0,0,3);
	stati::declare($SQL,'stat');
}
sub LoadConf {
	$CONFIG={};
	my $sth=$SQL->prepare("SELECT MAIN_GROUP, SUBKEY, FKEY, VALUE FROM config ORDER BY MAIN_GROUP,SUBKEY,FKEY");
	$sth->execute()  or die($SQL->errstr."\n");
	my (@tmp,$sub,$key,$value,$fkey);
	while (@tmp=$sth->fetchrow_array) {
		$sub=$CONFIG;
		$value=pop @tmp;
		$fkey=pop @tmp or next;
		foreach $key (@tmp) {
			if ($key) {
				$sub->{$key}={} unless exists $sub->{$key};
				$sub=$sub->{$key};
			}
		}
		$sub->{$fkey}=$value;
	}
	$sth->finish;
	ForumVer::SetVar("TCP_PORT",$CONFIG->{SHARESERVER}->{TCP}->{PORTA});
	ForumVer::SetVar("UDP_PORT",$CONFIG->{SHARESERVER}->{UDP}->{PORTA});
	ForumVer::SetVar("DESC", $CONFIG->{SHARESERVER}->{TCP}->{NICK});
}

sub MakeItem {
	my ($buf, $key, $value);
	if (exists $CONFIG->{NTP}) {
		$CONFIG->{HTTP}={} unless ref($CONFIG->{HTTP}) eq "HASH";
		$CONFIG->{HTTP}->{Env}->{timeoffset}=SignTime::Connect($CONFIG->{NTP});
	}
	# Creo i gruppi di limite banda
	if (exists $CONFIG->{TCP}->{BANDA_LIMITE}) {
		$ctcp->AddGroup($key,$value) while ($key,$value)=each(%{$CONFIG->{TCP}->{BANDA_LIMITE}});
	}
	
	$CONFIG->{TEMP_DIRECTORY}="temp_bf" if length($CONFIG->{TEMP_DIRECTORY})==0;
	mkdir $CONFIG->{TEMP_DIRECTORY} unless -d $CONFIG->{TEMP_DIRECTORY};
	unlink glob($CONFIG->{TEMP_DIRECTORY}."/*.dbm.dir");
	unlink glob($CONFIG->{TEMP_DIRECTORY}."/*.dbm.pag");
	
	MakeShareSession($key, $value) while ($key, $value)=each(%{$CONFIG->{SHARE}});
	my $server = IO::Socket::INET->new(Listen => 5,
			LocalPort => $CONFIG->{SHARESERVER}->{TCP}->{PORTA},
			LocalAddr => $CONFIG->{SHARESERVER}->{TCP}->{BIND},
			Proto => 'tcp'
		) or die("Impossibile creare il server SHAREDATA sulla porta ".$CONFIG->{SHARESERVER}->{TCP}->{PORTA}."\nErrore:$!\n");
	$SERVER{fileno $server}=-2;
	$ctcp->AddSock($server,(type=>'server', group=>$CONFIG->{SHARESERVER}->{TCP}->{GROUP})) or die ("Errore non previsto nell'aggiunta del'oggetto server sharedata\n");
	return 1 unless $CONFIG->{SHELL}->{TCP}->{PORTA};
	my $phpserver = IO::Socket::INET->new(Listen => 5,
			LocalPort => $CONFIG->{SHELL}->{TCP}->{PORTA},
			LocalAddr => $CONFIG->{SHELL}->{TCP}->{BIND},
			Proto => 'tcp'
		) or die("Impossibile creare il server SHELL sulla porta ".$CONFIG->{SHELL}->{TCP}->{PORTA}."\nErrore:$!\n");
	$SERVER{fileno $phpserver}=\&kfshell::new;
	kfshell::sender(\&sender);
	$ctcp->AddSock($phpserver,(type=>'server')) or die ("Errore non previsto nell'aggiunta del'oggetto server SHELL\n");
}
sub MakeShareSession {
	my ($ForumName, $value)=@_;
	my $public_key=$value->{PKEY} || return Error("Chiave pubblca del forum $ForumName non valida\n");
	# Carico la chiave pubblica del forum che carico.
	# La chiave pubblica è di vitale importanza e serve non solo per identificare un forum
	# ma anche per autentificare le operazioni dell'admin.
	$public_key=ConvData::Base642Dec($public_key);
	
	# Creo la chiave che identificherà quello specifico forum (in base alla chiave pubblica)
	my $Identificatore=Digest::SHA1::sha1("$public_key");
	print "creato sharesessione obj ".unpack("H*",$Identificatore)."\n";
	# Creo l'oggetto Rule.
	# La classe FRule è l'abbreviazione di ForumRule, regole del forum.
	# I metodi dell'oggetto mi dicono se certe azioni da parte di certi utenti sono permesse
	# in base anche alla configurazione caricata da $ForumName."_conf" .
	my $rule=FRule->new($SQL, $ForumName, $Identificatore,$public_key,$CONFIG->{BUFFER}) || return Error("Errore nel caricamento della configurazione di $ForumName\n");
	
	# Creo l'oggetto che mi permetterà di scambiare le righe delle varie tabelle.
	# Questo oggetto particolare si chiama Gate (non lavora con socket).
	# Il compito dell'iterazione tra gli socket è di una classe a livello superiore.
	my $dbmpath=$CONFIG->{TEMP_DIRECTORY}."/".unpack("H*",$Identificatore)."_hash.dbm";
	my $Gate=ShareDB->new($SQL, $dbmpath,$ForumName) or return Error("Impossibile creare l'oggetto di condivisione database\n");

	my %ftable = (newmsg => $ForumName."_newmsg",
				  membri=> $ForumName."_membri",
				  admin=> $ForumName."_admin",
				  reply=> $ForumName."_reply",
				 );
	#$ftable{joinnewaut}=$ftable{newmsg}.".AUTORE=".$ftable{membri}.".AUTORE";
	#$ftable{joinreplyaut}=$ftable{reply}.".AUTORE=".$ftable{membri}.".AUTORE";
	$Gate->tab_conf(
		ShareName=>$Identificatore,
		Identificatore=>"HASH",
		Table=>$ForumName."_congi",
		Type=>"TYPE",
		LastSend=>"LAST_SEND",
		Query=>{
		"1"=>[
			"SELECT `HASH`, `SEZ`, `AUTORE`, `EDIT_OF`, '1' AS `TYPE`,`DATE`, `TITLE`, `SUBTITLE`, `BODY`, `FIRMA`, `AVATAR`, `SIGN`,`FOR_SIGN` "
			."FROM ".$ftable{newmsg}." WHERE `HASH`=?",
			"SELECT `HASH`, `SEZ`, `AUTORE`, `EDIT_OF`,'1' AS `TYPE`, `DATE`, `TITLE`, `SUBTITLE`, `BODY`, `FIRMA`, `AVATAR`, `SIGN`,`FOR_SIGN` "
			."FROM ".$ftable{newmsg}." WHERE `EDIT_OF`=?",
			"SELECT `HASH`, `REP_OF`, `AUTORE`, `EDIT_OF`,'2' AS `TYPE`, `DATE`, `FIRMA`, `AVATAR`, `TITLE`, `BODY`, `SIGN` "
			."FROM ".$ftable{reply}." WHERE `REP_OF`=?"
			],
		"2"=>[
			"SELECT `HASH`, `REP_OF`, `AUTORE`, `EDIT_OF`, '2' AS `TYPE`, `DATE`, `FIRMA`, `AVATAR`, `TITLE`, `BODY`, `SIGN` "
			."FROM ".$ftable{reply}." WHERE HASH=?",
			"SELECT `HASH`, `REP_OF`, `AUTORE`, `EDIT_OF`, '2' AS `TYPE`, `DATE`, `FIRMA`, `AVATAR`, `TITLE`, `BODY`, `SIGN` "
			."FROM ".$ftable{reply}." WHERE EDIT_OF=?"
			],
		"3"=> ["SELECT `HASH`, `DATE`, '3' AS `TYPE`,`SIGN`, `TITLE`, `COMMAND` FROM ".$ftable{admin}." WHERE `HASH`=? LIMIT 120;"],
		"4"=>["SELECT `HASH`, `AUTORE`, '4' AS `TYPE`, `DATE`, `PKEY`, `SIGN`, `AUTH` FROM ".$ftable{membri}." WHERE `HASH`=? LIMIT 120;"]
		}
	) or return Error("Impossibile completare alcune operazione per il forum $ForumName\n");
	ShSession::AddGate($Identificatore,$Gate,$rule,$SQL,$CONFIG->{SHARESERVER});
	print "INDEX: Aggiunta board con PKEY:$public_key\n";
}
sub Error {
	print STDERR shift;
	return shift || undef;	
}
sub capturl {
	&Init(1);
	my $url=$ARGV[0];
	if ($url=~/shaf:\/\/\|AddBoard\|\/(.+?)\//i) {
		my $id=ConvData::Base642Dec(urldecode($1));
	}
}
sub urldecode {
	my $url=shift;
	$url=~s/%([A-Fa-f\d]{2})/chr hex $1/eg;
	return $url;
}
sub LoadLibrary {
	my $lib=shift;
	eval "use $lib;";
	if ($@) {
		print "La libreria $lib non è stata caricata.\nProva a riinstallare il software.\n";
		<STDIN>;
		exit(0);
	}
}