package Proto::DonkeyProto;
use strict;
use Compress::Zlib;

sub new {
	my $this=bless({},'Proto::DonkeyProto');
	$this->{ricev}=[];
        print "creato novo donkeyproto\n";
	return $this;
}

sub _bufferizza {
	my ($this,$pacc)=@_;
	$this->{pacc}.=$pacc;
	unless ($this->{Header}) {
		return 0 if (length($this->{pacc}) <6);
		my %header;
		@header{'proto','dim','tipo'}=unpack("CIC",substr($this->{pacc},0,6,''));
		$this->{DataHeader}=\%header;
		$this->{Header}=1;
		return -1 if $header{proto} != 227 && $header{proto} != 212;
	}
	return 0 if length($this->{pacc}) < $this->{DataHeader}->{dim}-1;
	$this->{Header}=0;
	my $data=substr($this->{pacc},0,$this->{DataHeader}->{dim}-1,'');
	$data=uncompress($data) if $this->{DataHeader}->{'proto'} == 212;
	push(@{$this->{ricev}}, chr($this->{DataHeader}->{'tipo'}).$data);
	if ($this->{pacc}) {
		return -1 if $this->_bufferizza('')<0;
	}
	return 1;
}

sub sender {
    my ($this,$msg)=@_;
    return "\xe3".pack("I",length($msg)).$msg;
}

sub reader {
	my $this=shift;
	return shift(@{$this->{ricev}});
}







1;
