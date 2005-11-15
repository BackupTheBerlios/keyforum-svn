package ForumLib;
use strict;
use DBI;
use CGI qw/:standard/;
use Time::Local;
print header;
use CGI::Carp qw(fatalsToBrowser);
use Itami::BinDump;

# Apro il database e mi connetto ad esso
my $SQL = DBI->connect("DBI:mysql:database=".$ENV{sql_dbname}
		.";host=".$ENV{sql_host}
		.";port=".($ENV{sql_port} or 3306),
		$ENV{sql_user},
		$ENV{sql_passwd},
		{
			PrintError => 1,
			PrintWarn=>1
		}) or Error("Can't connect to database\n",0);
my ($CONFIG,$SESSID,$NICK, $PASSWD, $IDENTIFICATORE, $PRIVATE_DATA, $AUTH,$KEY_DECRYPT, $RSA_PRIVATE);
sub GetAuthorHash {
	Error("Hash autore non valido") if length($PRIVATE_DATA->{hash}) != 16;
	return $PRIVATE_DATA->{hash};
}
sub Do {
	$SQL->do(@_) or Error ("Impossibile eseguire una query:\n".$SQL->errstr."\n");	
}
sub session {
	return $SESSID;
}
sub Execute {
	my @query=@_;
	foreach my $buf (@query) {
		$SQL->do($buf) or return Warning ("<br>\nImpossibile eseguire la query:\n<br><b>".$SQL->errstr."</b>\n<br>$buf<br>");	
	}
	return 1;
}
sub SQL {return $SQL}

sub Warning {
	print (shift)."\n";
	return undef;
}
sub SqlWarn {
	print "Impossibile eseguire una query:\n".$SQL->errstr."\n";
}
sub Error {
	print shift;
	exit shift;
}

sub CanRegisterFlood {
	my ($board_name, $date_reg)=@_;
	#my ($RefFlood, $sth, $ref);
	#(ref($CONFIG->{REG_FLOOD}) eq "HASH") ? ($RefFlood = $CONFIG->{REG_FLOOD}) : (return undef);
	#$sth=$SQL->prepare("SELECT count(*) FROM ".$board_name."_membri WHERE `DATE` BETWEEN ? AND ?;");
	#while (my ($durata, $maxnum)=each %$RefFlood) {
	#	$sth->execute($date_reg-$durata, $date_reg);
	#	next unless $ref=$sth->fetchrow_arrayref;
	#	return undef if $ref->[0] > $maxref;
	#}
	return 1;
}
sub PermessiRegistrazione {
	my ($board_name)=shift;
	(LoadConf($board_name) or return undef) unless defined $CONFIG;
	return undef if $CONFIG->{VAR}->{REG_USR_MODE};
	return 1;
}
sub LoadConf {
	my $board_name=shift;
	my $sth=$SQL->prepare("SELECT `GROUP`, `FKEY`, `VALUE` FROM ".$board_name."_conf;") or return undef;
	$sth->execute() or Warning ("Impossibile eseguire una query:\n".$SQL->errstr."\n");
	my @conf;
	while (@conf=$sth->fetchrow_array) {
		$CONFIG->{$conf[0]}={} unless exists $CONFIG->{$conf[0]};
		$CONFIG->{$conf[0]}->{$conf[1]}=$conf[2];
	}
	$sth->finish;
	return 1;
}
sub LoadMsg {
	my $msghash=shift;
	my $SNAME=$ENV{sesname};
	my $sth=$SQL->prepare("SELECT ".$SNAME."_newmsg.title as title, ".$SNAME."_membri.AUTORE as autore"
	." FROM ".$SNAME."_msghe,".$SNAME."_newmsg,".$SNAME."_membri"
	." WHERE ".$SNAME."_newmsg.EDIT_OF=".$SNAME."_msghe.HASH"
	." AND ".$SNAME."_newmsg.EDIT_OF=?"
	." AND ".$SNAME."_newmsg.visibile='1'"
	." AND ".$SNAME."_membri.HASH=".$SNAME."_msghe.AUTORE");	
	$sth->execute($msghash);
	if (my $ref=$sth->fetchrow_hashref) {
		$sth->finish;
		return $ref;	
	}
	$sth->finish;
	return undef;
}
sub DelTempData {
	$SQL->do("DELETE FROM `temp` WHERE TTL<'".time()."';");	
}
sub LoadSessionData {
	return undef unless defined $SESSID;
	LoadModules ("Digest::MD5");
	my $sth=$SQL->prepare("SELECT `NICK`, `PASSWORD` FROM session WHERE SESSID=? AND IP=? AND FORUM=?;") or return undef;
	my $ip=unpack("H*",Digest::MD5::md5($ENV{REMOTE_ADDR}));
	$sth->execute($SESSID,$ip,$ENV{sesname}) or Warning ("Impossibile eseguire una query:\n ".$SQL->errstr."\n");;
	if (my @dati=$sth->fetchrow_array) {
		$sth->finish;
		($NICK,$PASSWD)=($dati[0],$dati[1]);
		$IDENTIFICATORE=Digest::MD5::md5_hex($PASSWD.$NICK);
		$KEY_DECRYPT=Digest::MD5::md5($NICK.$PASSWD);
		$AUTH=1;
		return 1;
	}
	$sth->finish;
	$AUTH=0;
	return 0;
}
sub LoadModules {
	eval "use ".$_[0].";";
	Error("Errore nel caricamento di un modulo:".$@) if $@;
}
sub CryptBlowFish {
	my ($key, $testo_normale)=@_;
	LoadModules("Crypt::Blowfish");
	my ($pezzo,$cipher,$testo_criptato);
	return undef unless $cipher = new Crypt::Blowfish $key;
	my $testo_normale=pack("I/a*", $testo_normale);
	while (length($testo_normale)>0) {
		$pezzo=substr($testo_normale, 0, 8, "");
		$pezzo.="\x00"x(8-length($pezzo)) if length($pezzo)<8;
		$testo_criptato.=$cipher->encrypt($pezzo);
	}
	return $testo_criptato;	
}
sub DeCryptBlowFish {
	my ($key, $testo_criptato)=@_;
	LoadModules("Crypt::Blowfish");
	return undef if length($testo_criptato)%8!=0;
	my ($pezzo,$cipher,$testo_normale);
	return undef unless $cipher = new Crypt::Blowfish $key;
	while (length($testo_criptato)>0) {
		$pezzo=substr($testo_criptato, 0, 8, "");
		$testo_normale.=$cipher->decrypt($pezzo);
	}
	return unpack("I/a",$testo_normale);	
}
sub GetPrivateKey {
	return undef unless $AUTH;
	my $sth=$SQL->prepare("SELECT `PASSWORD` FROM ".$ENV{sesname}."_localmember WHERE `HASH`=?;") or return undef;
	$sth->execute($IDENTIFICATORE) or Warning ("Impossibile eseguire una query:\n".$SQL->errstr."\n");
	if (my @dati=$sth->fetchrow_array) {
		LoadModules("MIME::Base64");
		LoadModules("Math::Pari");
		$sth->finish;
		eval {$PRIVATE_DATA=BinDump::MainDeDump(DeCryptBlowFish($KEY_DECRYPT,MIME::Base64::decode_base64($dati[0])))};
		Error("Errore nel caricamento della chiave privata:".$@) if $@;
		return undef unless ref($PRIVATE_DATA->{private}) eq "HASH";
		$RSA_PRIVATE={};
		$RSA_PRIVATE->{private}={};
		foreach my $chiave (keys %{$PRIVATE_DATA->{private}}) {
			$RSA_PRIVATE->{private}->{$chiave}=PARI($PRIVATE_DATA->{private}->{$chiave});
			delete $PRIVATE_DATA->{private}->{$chiave};
		}
		$RSA_PRIVATE->{Version} = "1.91";
		$RSA_PRIVATE->{Checked} = 0;
		$RSA_PRIVATE->{Identity} => 'Board';
		$RSA_PRIVATE->{'Cipher'} => 'Blowfish';
		bless($RSA_PRIVATE,'Crypt::RSA::Key::Private');
		return 1;
	}
	$sth->finish;
	return 0;	
}
sub ConvPrivateKey {
	my $code=shift;
	my $private;
	LoadModules("Math::Pari");
	LoadModules("MIME::Base64");
	eval {$private=BinDump::MainDeDump(MIME::Base64::decode_base64($code))};
	Error("Errore nel caricamento della chiave RSA:".$@) if $@;
	return undef if ref($private->{private}) ne "HASH";
	foreach my $chiave (keys %{$private->{private}}) {
		$private->{private}->{$chiave}=PARI($private->{private}->{$chiave});
	}
	$private->{Version} = "1.91";
	$private->{Checked} = 0;
	$private->{Identity} => 'Board';
	$private->{'Cipher'} => 'Blowfish';
	bless($private,'Crypt::RSA::Key::Private');
	return $private;
}
sub GetSezName {
	my $id=shift;
	return undef unless defined $id;
	return undef if $id=~ /\D/;
	my $sth=$SQL->prepare("SELECT * FROM ".$ENV{sesname}."_sez WHERE `ID`=?;");
	$sth->execute($id) or Warning ("Impossibile eseguire una query:\n".$SQL->errstr."\n");
	if (my $ref=$sth->fetchrow_hashref) {
		$sth->finish;
		return $ref;	
	}
	$sth->finish;
	return undef;
}
sub CanWrite {
	my $sth=$SQL->prepare("SELECT `ban` FROM ".$ENV{sesname}."_membri WHERE `HASH`=?;");
    $sth->execute($PRIVATE_DATA->{hash});	
	if (my $ref=$sth->fetchrow_hashref) {
		$sth->finish;
		($ref->{ban}) ? (return 0) : (return 1);
	}
	$sth->finish;
	return 0;
}
sub _gmtime {
	return Time::Local::timelocal(gmtime(time+$ENV{timeoffset}))
}
sub _localtime {
	return time()+$ENV{timeoffset};
}
sub RsaSign {
	my $hash=shift;
	my $private=shift || $RSA_PRIVATE;
	return undef unless length $hash;
	LoadModules("Crypt::RSA");
	my $rsa = new Crypt::RSA;
	return $rsa->sign (Message => $hash, Key => $private) || Error("RSA ERROR:".$rsa->errstr());
}
sub RsaCheck {
	my ($md5,$sign, $pub)=@_;
	return undef unless defined $pub;
	return undef unless defined $sign;
	return undef unless defined $md5;
	LoadModules("Crypt::RSA");
	my $rsa = new Crypt::RSA;
	return $rsa->verify (Message => $md5, Signature  => $sign, Key => $pub);	
}
sub LoadSession {
	my $sessid;
	my $tmp=$ENV{HTTP_COOKIE};
	if ($tmp=~ /PHPSESSID=([0-9A-Fa-f]{32})/i) {
			$SESSID=$1;
			return 1;
	}
	my $tmp=param("PHPSESSID");
	if ($tmp=~ /^([0-9A-Fa-f]{32})$/i) {
			$SESSID=$1;
			return 1;
	}
	return 0;
}
sub LoadTempData {
	my $sth=$SQL->prepare("SELECT `VALORE` FROM `temp` WHERE `CHIAVE`=?;");
	$sth->execute(shift);
	my $var;
	return $var->{VALORE} if $var=$sth->fetchrow_hashref;
	return undef;
}
sub UpdateTempTTL {
	$SQL->do("UPDATE `temp` SET `TTL`=? WHERE `CHIAVE`=?;",undef,time+$_[1],$_[0]);
}
sub NewTempData {
	$SQL->do("INSERT INTO `temp` (`CHIAVE`,`VALORE`,`TTL`) VALUES(?,?,?);",undef,$_[0],$_[1],time()+$_[2]);
	return 1;
}
sub UpdateTempVal {
	my ($chiave, $valore,$time)=@_;
	my $sth=$SQL->prepare("UPDATE `temp` SET `VALORE`=?,`TTL`=? WHERE `CHIAVE`=?;");
	$sth->execute($valore || '',$time+time(),$chiave);
	return NewTempData($chiave,$valore,$time) unless $sth->rows();
	return 1;
}
sub Head {
	return qq~<html>
<head>
	<title>PP generator</title>
</head>
<LINK href="style_page.css" rel=stylesheet type=text/css>
<body bgcolor=white text=black>
~;
	
}
sub GenPublicKey {
	my $num=shift;
	return undef if length($num)<100;
	return undef if $num=~ /\D/;
	my $public=bless({},'Crypt::RSA::Key::Public');
	$public->{n}=PARI($num);
	$public->{e}=PARI("65537");
	$public->{Version} = "1.91";
	$public->{Identity} = "i";
	return $public;
}
sub PrivateKey2Base64 {
	my $private=shift;
	LoadModules("MIME::Base64");
	my $subpr={};
	$subpr->{Version} = "1.91";
	$subpr->{Checked} = "0";
	$subpr->{Identity} = "io";
	$subpr->{private}={};
	my ($key, $value);
	$subpr->{private}->{$key}="$value" while ($key, $value)=each %{$private->{private}};
	return MIME::Base64::encode_base64(BinDump::MainDump($subpr,0,1),'');
}
1;