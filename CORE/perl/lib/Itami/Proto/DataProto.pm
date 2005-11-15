package Proto::DataProto;
use strict;

sub new {
	my $this=bless({},'Proto::DataProto');
	return $this;
}

sub _bufferizza {
	my ($this,$pacc)=@_;
	$this->{pacc}.=$pacc;
	#stati::updateadd(length($pacc),0,0,0,'CTCPDATA','INBAND','TOTAL');
	return 1;
}

sub sender {
	my $this=shift;
	return shift;
}

sub reader {
	my $this=shift;
	my $pacc=$this->{pacc};
	delete $this->{pacc};
	return $pacc;
}


1;