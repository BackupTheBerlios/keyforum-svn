package Proto::udpbase;
use strict;
use Itami::phpdump;


sub new {
	my $this=bless({},'Proto::udpbase');
	$this->{ricev}=[];
	return $this;
}

sub _bufferizza {
	my ($this,$pacc,$ip)=@_;
	return 0 if (length($pacc) <2);
	my ($proto)=unpack("C",substr($pacc,0,1,""));
	return 0 if $proto != 21;
	my $perlvar=phpdump::binary2var($pacc);
	return undef unless $perlvar;
	push(@{$this->{ricev}}, {'var'=>$perlvar,'ip'=>$ip});
	return 1;
}

sub sender {
	my ($this,$data)=@_;
	if (ref($data) eq "HASH" || ref($data) eq "ARRAY") {
		return undef unless $data=phpdump::var2binary($data);
	} else {
		return undef;
	}
	return "\x15".$data;
}

sub reader {
	my ($this,$varie)=@_;
	my $dati;
	return undef unless $dati=shift(@{$this->{ricev}});
	$$varie=$dati->{ip};
	return $dati->{var};
}





1;