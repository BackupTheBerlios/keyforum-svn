package CheckRsa;
use Digest::MD5;
use Crypt::RSA;
use Math::Pari;
use strict;
use Itami::ConvData;
my @tipi;
$tipi[0]=[qw(SEZ AUTORE TYPE EDIT_OF DATE TITLE SUBTITLE BODY FIRMA AVATAR)];
$tipi[1]=[qw(REP_OF AUTORE TYPE EDIT_OF AVATAR FIRMA DATE TITLE BODY)];
$tipi[2]=[qw(DATE TITLE COMMAND)];
$tipi[3]=[qw(AUTORE DATE PKEY)];
my $RSA= new Crypt::RSA;
sub new {
	my ($packname, $db, $fname, $pkeyadmin)=@_;
	my $this=bless({}, $packname);
	#$this->{DB}=$db;
	$this->{PkeyAdmin}=$pkeyadmin;
	$this->{AdminPublic}=bless({},'Crypt::RSA::Key::Public');
	$this->{AdminPublic}->{n}=PARI($pkeyadmin);
	$this->{AdminPublic}->{e}=PARI("65537");
	$this->{AdminPublic}->{Version} = "1.91";
	$this->{AdminPublic}->{Identity} = "i";
	$this->{"GetPkeyAutore"}=$db->prepare("SELECT PKEYDEC FROM ".$fname."_membri WHERE HASH=? AND present='1';");
	$this->{RSA} = new Crypt::RSA;
	return $this;
}

sub GetPkey {
	my $this=shift;
	$this->{"GetPkeyAutore"}->execute(shift);
	if (my $ref=$this->{"GetPkeyAutore"}->fetchrow_arrayref) {
		return undef if length($ref->[0])<125;
		return $ref->[0];
	}
	return undef;
}
sub Validifica {
	my ($this, $md5, $sign, $pkey)=@_;
	return undef if length($sign)<100;
	return undef if length($pkey)<100;
	my $public=bless({},'Crypt::RSA::Key::Public');
	$public->{n}=PARI("$pkey");
	$public->{e}=PARI("65537");
	$public->{Version} = "1.91";
	$public->{Identity} = "i";
	return $RSA->verify (
            Message    => $md5, 
            Signature  => $sign, 
            Key        => $public) || Warning($RSA->errstr());
}
sub Warning {
	print STDERR shift;
	return undef;
}
sub MD5Make {
	my ($this,$msg)=@_;
	return undef if ref($msg) ne "HASH";
	my $ctx = Digest::MD5->new;
	$ctx->add($this->{PkeyAdmin});
	foreach my $key (@{$tipi[int($msg->{TYPE})-1]}) {
		$ctx->add($msg->{$key}) if exists $msg->{$key};
	}
	return $ctx->digest;
}
1;