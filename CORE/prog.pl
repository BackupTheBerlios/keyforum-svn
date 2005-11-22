BEGIN {push(@INC, './addon','./addon/lib');}
print "Avvio KeyForum....\n";
sleep 1;
use strict;
LoadLibrary("Itami::GloIni");
LoadLibrary("DBI");
LoadLibrary("Itami::cTcp");
LoadLibrary("Itami::LogFile");
LoadLibrary("Itami::ConvData");
LoadLibrary("Itami::stati");
LoadLibrary("Itami::Cycle");
LoadLibrary("Itami::CycleFunc");
LoadLibrary("Math::Pari");
LoadLibrary("Crypt::RSA");
LoadLibrary("Digest::MD5");
LoadLibrary("Digest::SHA1");
LoadLibrary("File::Glob ':glob'");
#require "ShareDB.pm";
#require "FRule.pm";
#require "ShSession.pm";
#require "SignTime.pm";
#require "PerlScript.pm";
#require "GestIp.pm";
#require "kfdebug.pm";
#require "kfshell.pm";
#$GLOBAL::SERVER={};
$GLOBAL::CycleFunc=[];
#$GLOBAL::CLIENT={};
print "Connessione a MySQL server...\n";
$GLOBAL::ctcp=cTcp->new();
&Init; # Connessione al server MySQL e caricamento della tabella conf.

&LoadAddOn;  # Carico gli addon specificati con CONFIG
my $ref;
while (1) {
	foreach my $buf (@{$GLOBAL::CycleFunc}) {
		$buf->check;	
	}
	redo unless $ref=$GLOBAL::ctcp->Select(5);
	if (exists $ref->{ConnectionRequest}) {
		foreach my $value (@{$ref->{ConnectionRequest}}) {
			my $socket=$value->accept;
			my ($socknum,$servernum)=(fileno($socket), fileno($value));
			next unless exists $GLOBAL::SERVER{$servernum};
			$GLOBAL::SERVER{$servernum}->($socknum,$socket,$value);
		}
	}
	if (exists $ref->{CanRead}) {
		foreach my $value (@{$ref->{CanRead}}) {
			next unless exists $GLOBAL::CLIENT{fileno $value};
			$GLOBAL::CLIENT{fileno $value}->RecData(fileno $value,$_,$value) while $_=$GLOBAL::ctcp->recv($value);
		}
	}
	if (exists $ref->{'Disconnessi'}) {
		foreach my $value (@{$ref->{'Disconnessi'}}) {
			next unless delete $GLOBAL::CLIENT{fileno $value};
			close $value;
		}
	}
	if (exists $ref->{'EmptyOutBuff'}) {
		foreach my $value (@{$ref->{'EmptyOutBuff'}}) {
			next unless exists $GLOBAL::CLIENT{fileno $value};
			$GLOBAL::CLIENT{fileno $value}->FreeBuff();
		}
	}
	if (exists $ref->{'ConnessioneRiuscita'}) {
		foreach my $value (@{$ref->{'ConnessioneRiuscita'}}) {
			$GLOBAL::ctcp->GetIpTry($value);
			$GLOBAL::tryconn{fileno $value}->(1,fileno $value,$value,$value->peerhost);
		}
	}
	if (exists $ref->{'ConnessioneFallita'}) {
		foreach my $value (@{$ref->{ConnessioneFallita}}) {
			my $ip=$GLOBAL::ctcp->GetIpTry($value);
			$GLOBAL::tryconn{fileno $value}->(0,fileno $value,$value,$ip);
			close($value);
		}
	}
	if (exists $ref->{'Exception'}) {
		foreach my $value (@{$ref->{ConnessioneFallita}}) {
			print "Eccezione: $value. Non gestita\n";
		}
	}


}





#################################################
# Funzioni varie di avvio e connessione a MySQL #
#################################################
# Carico gli addon richiesti da CONFIG
sub LoadAddOn {
	my $lista=$GLOBAL::CONFIG->{CORE}->{ADDON};
	return undef if ref($lista) ne "HASH";
	while (my ($key, $value)=each(%$lista)) {
		next if lc($value) ne 'load';
		eval "require \"".$key.".pm\";";
		next unless $@;
		print "Errore nel caricamento di un addon:\n$@\n";
		die(Error($@));
	}
}
# Connessione al server mysql
sub Init {
	# Carico il file di configurazione di KeyForum.
	# La classe terrrà le informazioni all'interno della propria classe.
	GloIni::load("config.ini") or die("Impossibile aprire il file config.ini\n");

	# Redirigo l'STDERR Handle al file specificato nel file config.ini
	if ($_=GloIni::GetVal("LogFile")) {
		tie (*STDERR, "LogFile",$_) or die("Impossibile collegare STDERR al file $_\n");	
	}
	
	# Mi connetto al server SQL
	unless ($GLOBAL::SQL = DBI->connect("DBI:mysql:database=".GloIni::GetVal("DB_name")
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
	stati::declare($GLOBAL::SQL,'stat');
	&LoadConf;
	
	return 1 unless exists $GLOBAL::CONFIG->{TCP}->{BANDA_LIMITE};
	while (my ($key,$value)=each(%{$GLOBAL::CONFIG->{TCP}->{BANDA_LIMITE}})) {
		$GLOBAL::ctcp->AddGroup($key,$value);
	}
}
# Carica la tabella config in una variabile globale.
sub LoadConf {
	$GLOBAL::CONFIG={};
	my $sth=$GLOBAL::SQL->prepare("SELECT MAIN_GROUP, SUBKEY, FKEY, VALUE FROM config ORDER BY MAIN_GROUP,SUBKEY,FKEY");
	$sth->execute() or die($GLOBAL::SQL->errstr."\n");
	my (@tmp,$sub,$key,$value,$fkey);
	while (@tmp=$sth->fetchrow_array) {
		$sub=$GLOBAL::CONFIG;
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
sub Error {
	print STDERR shift;
	return shift || undef;	
}
# RIcordarsi di andare a modificare ForumVar::SetVar TCP_PORT UDP_PORT DESC
#ForumVer::SetVar("TCP_PORT",$GLOBAL::CONFIG->{SHARESERVER}->{TCP}->{PORTA});
#ForumVer::SetVar("UDP_PORT",$GLOBAL::CONFIG->{SHARESERVER}->{UDP}->{PORTA});
#ForumVer::SetVar("DESC", $GLOBAL::CONFIG->{SHARESERVER}->{TCP}->{NICK});
	
# configurare kfdebug