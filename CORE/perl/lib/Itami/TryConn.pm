package TryConn;
use IO::Select;
use IO::Socket::INET;
use strict;
use Time::HiRes;

sub new {
	my ($packname)=@_;
	my $this=bless({},$packname);
	$this->{IOSELECT}=IO::Select->new();
	$this->{TIMEOUT}={};
	$this->{IP}={};
	return $this;
}
#=Time::HiRes::time()
sub Tryer {
	my ($this, $ip, $porta, $timeout)=@_;	
	$timeout=10 unless defined $timeout;
	return if !defined($porta) || $porta<0 || $porta>65535;
	my $SockTest = IO::Socket::INET->new(Proto => 'tcp');
	my $ict_set="1";
	ioctl ($SockTest,2147772030, $ict_set);
	return undef unless $ip=inet_aton $ip;
	$SockTest->connect($porta, $ip);
	$this->{IOSELECT}->add($SockTest);
	my $socknum=fileno $SockTest;
	$this->{IP}->{$socknum}=$ip;
	$this->{TIMEOUT}->{$socknum}=$timeout+Time::HiRes::time();
	return $SockTest;
}
sub TimeOutSock {
	my $this=shift;
	my ($buf,@ipout);
	foreach $buf ($this->{IOSELECT}->handles) {
		push(@ipout,$buf) if $this->{TIMEOUT}->{fileno $buf}<Time::HiRes::time();
	}
	return @ipout;
}
sub GetIOSEL {
	return (shift)->{IOSELECT};
}
sub GetIp {
	my ($this, $sock)=@_;
	return undef unless exists $this->{IP}->{fileno $sock};
	return $this->{IP}->{fileno $sock};
}
sub Remove {
	my ($this,$sock)=@_;
	delete $this->{TIMEOUT}->{fileno $sock};
	delete $this->{IP}->{fileno $sock};
	$this->{IOSELECT}->remove($sock);
}
sub DeCheck {
	my ($this,$sock)=@_;
	$this->{IOSELECT}->remove($sock);
}
1;