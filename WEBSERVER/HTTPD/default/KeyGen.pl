push(@INC,"..");
use strict;
use Itami::forumlib;
use CGI qw/:standard/;
my $params = CGI::Vars();
print ForumLib::Head();
if ($params->{GenNew}) {
	eval "use Crypt::RSA;";
	if ($@) {
		print "Non riesco a caricare la libreria Crypt::RSA $@\n";
		exit(0);	
	}
	eval "use MIME::Base64;";
	if ($@) {
		print "Non riesco a caricare la libreria MIME::Base64 $@\n";
		exit(0);	
	}
	my $rsa = new Crypt::RSA;
	my ($public, $private) = $rsa->keygen (Identity  => 'io', Size => 1024) or exit print "Errore chiave rsa:".$rsa->errstr();
	my $pkey=$public->{n};
	my $codpriv=ForumLib::PrivateKey2Base64($private);
	print "La Password Privata è:<bR><textarea cols=40 rows=10>$codpriv</textarea><br>\n";
	print "Quella pubblica è:<br><textarea cols=40 rows=10>$pkey</textarea><br><bR>\n";
}
print "<br><br><br><br><form method=post action=KeyGen.pl><input type=submit name=GenNew value='Genera Nuova Coppia di Chiavi'></form>";
print "</body></html>";