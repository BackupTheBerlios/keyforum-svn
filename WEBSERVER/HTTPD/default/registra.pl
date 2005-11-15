chdir ($ENV{"DOCUMENT_ROOT"});
use Crypt::RSA;
use Digest::MD5;
use Itami::BinDump;
use Itami::forumlib;
use Itami::ConvData;
use Itami::Adder;
use MIME::Base64;
use CGI qw/:standard/;
use strict;
print "<html><head><title> ".$ENV{"sesname"}." Forum</title>
<LINK href=\"style_page.css\" rel=stylesheet type=text/css>
</head><body bgcolor=\"#EEEEEE\" text=black link=blue>\n\n";
my $rsa = new Crypt::RSA;
my $nick=param('nick');
my $password=param('password');
my $old_key=param('pkey');
print "La chiave pubblica dell'admin non è valida, non posso valida il messaggio\n" if length($ENV{pkey})<120;
Error("Il nick non rispetta la lunghezza corretta (min 3, max 30)<br>\n") if length($nick) > 30 || length($nick) < 3;
Error("La password non rispetta la lunghezza corretta (min 3, max 30)<bR>\n") if length($password) > 30 || length($password) < 3;
Error("Non hai i permessi per registrare un utente su questa board\n<br>") unless ForumLib::PermessiRegistrazione($ENV{sesname});
Error("L'Antiflood che controlla le registrazioni effettuate nel sistema ti impedisce di registrare al momento, riprova più tardi\n<br>") unless ForumLib::CanRegisterFlood($ENV{sesname}, time());
if ($old_key) {
  my $identificatore=unpack("H*",Digest::MD5::md5(Digest::MD5::md5($password).$nick));
  print "Ripristino utente in corso...<br>\n";
  #my $adder=Adder->new(ForumLib::SQL(), $ENV{sesname});
  print "Inserisco la chiave privata nel database<br>\n";
  ForumLib::Do("INSERT INTO ".$ENV{sesname}."_localmember (`HASH`,`PASSWORD`) VALUES(?,?);",undef,$identificatore,$old_key);
  print "Utente ripristinato, fai il login dalla relativa pagina.<br>";
  print "<p><a href=\"login.php\">login</a></p>";
}
else {

print "Generazione chiavi RSA a 1024 bits<br>\n";
my ($public, $private) = $rsa->keygen (
					Identity => 'bforum',
					Size => 1024,
					Verbosity => 0) or Error("Errore durante la generazione delle chiavi RSA:<b>".$rsa->errstr()."</b><br>\n");
print "Generazione identificatore<br>\n";
my $identificatore=unpack("H*",Digest::MD5::md5(Digest::MD5::md5($password).$nick));
my $date=ForumLib::_gmtime();
my ($key, $value);
my $hash=Digest::MD5::md5($ENV{pkey}.$nick.$date.$public->{n});
my $STORING={};
$STORING->{private}={};
$STORING->{private}->{$key}="$value" while ($key, $value)=each %{$private->{private}};
$STORING->{hash}=$hash;
$STORING->{date}=$date;
print "Converto la chiave privata in binario<br>\n";
my $BinData=BinDump::MainDump($STORING,0,1);
return undef unless length $BinData;
print "Cripto la chiave privata<br>\n";
$BinData=MIME::Base64::encode_base64(ForumLib::CryptBlowFish(Digest::MD5::md5($nick.Digest::MD5::md5($password)), $BinData),"");
print "Genero la firma digitale<br>\n";
my $firma_digitale= $rsa->sign (Message => $hash, Key => $private) || Error $rsa->errstr();


print "Questa è la tua password criptata: <b>NON PERDERLA!</b><br>\n".
	  "<textarea cols=50 rows=8 WRAP='physical'>$BinData</textarea><br><br>\n";
#my $BinPKEY=ConvData::Dec2Bin($public->{n});

my $adder=Adder->new(ForumLib::SQL(), $ENV{sesname});
print "Inserisco la chiave privata nel database<br>\n";
ForumLib::Do("INSERT INTO ".$ENV{sesname}."_localmember (`HASH`,`PASSWORD`) VALUES(?,?);",undef,$identificatore,$BinData);
print "Inserisco nel sistema la tua identità.<br>\n";
my %MSG;
@MSG{'AUTORE','DATE','PKEY','SIGN'}=($nick,$date,$public->{n},$firma_digitale);
$adder->_AddType4($hash,\%MSG);
$adder->Priority($hash,10);
#ForumLib::Do("INSERT INTO ".$ENV{sesname}."_congi (`HASH`,`TYPE`,`WRITE_DATE`,`LAST_SEND`) VALUES(?,?,?,?);",undef,$hash,4,$date,-1);
#ForumLib::Do("INSERT INTO ".$ENV{sesname}."_membri (`HASH`,`AUTORE`,`DATE`,`PKEY`,`SIGN`) VALUES(?,?,?,?,?);",undef,$hash,$nick,$date,$BinPKEY,$firma_digitale);
print "Fine esecuzione, fai il login dalla relativa pagina.<br>\n";
print "<p><a href=\"login.php\">login</a></p>";
}

sub Error {
	print shift;
	exit shift;	
}