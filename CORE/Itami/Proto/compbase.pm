package Proto::compbase;
use strict;
use Itami::phpdump;


sub new {
	my $this=bless({},'Proto::compbase');
	$this->{ricev}=[];
	return $this;
}

sub _bufferizza {
	my ($this,$pacc)=@_;
	$this->{pacc}.=$pacc;
	unless ($this->{Header}) {
		return 0 if (length($this->{pacc}) <6);
		my %header;
		@header{'proto','dim'}=unpack("CI",substr($this->{pacc},0,5,""));
		$this->{DataHeader}=\%header;
		$this->{Header}=1;
		return -1 if $header{proto} != 21;
	}
	return 0 if length($this->{pacc}) < $this->{DataHeader}->{dim};
	$this->{Header}=0;
	my $data=substr($this->{pacc},0,$this->{DataHeader}->{dim},"");
	my $perlvar=phpdump::binary2var($data);
	return undef unless $perlvar;
	my $other=0;
	push(@{$this->{ricev}}, $perlvar);
	if ($this->{pacc}) {
		$other=$this->_bufferizza('');
		return -1 if $other<0;
	}
	return 1+$other;
}

sub sender {
	my ($this,$data)=@_;
	if (ref($data) eq "HASH" || ref($data) eq "ARRAY") {
		return undef unless $data=phpdump::var2binary($data);
	} else {
		return undef;
	}
	return "\x15".pack("I", length($data)).$data;
}

sub reader {
	my $this=shift;
	return shift(@{$this->{ricev}});
}





1;