#! /opt/lampp/bin/perl

use strict;
push(@INC,"/usr/lib/perl5/site_perl/5.8.7");
push(@INC,"/usr/lib/perl5/site_perl/5.8.7/i486-linux");
use CGI qw/:standard/;
use lib "../../../CORE/Itami";
use BinDump;
use MIME::Base64;
my $params = CGI::Vars();

print "Content-type: text/html\n\n";
print "<html><body>";
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
	my $codpriv=PrivateKey2Base64($private);
	print "La Password Privata è:<bR><textarea cols=40 rows=10>$codpriv</textarea><br>\n";
	print "Quella pubblica è:<br><textarea cols=40 rows=10>$pkey</textarea><br><bR>\n";

print "<br><br><br><br><form method=post action=mykeygen.pl><input type=submit name=GenNew value='Genera Nuova Coppia di Chiavi'></form>";
print "</body></html>";

sub PrivateKey2Base64 {
        my $private=shift;
	my $subpr={};
	$subpr->{Version} = "1.91";
	$subpr->{Checked} = "0";
	$subpr->{Identity} = "io";
	$subpr->{private}={};
	my ($key, $value);
	$subpr->{private}->{$key}="$value" while ($key, $value)=each %{$private->{private}};
	return MIME::Base64::encode_base64(BinDump::MainDump($subpr,0,1),'');
}
