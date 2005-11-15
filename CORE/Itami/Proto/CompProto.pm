package Proto::CompProto;
use strict;
use Itami::BinDump;
use Compress::Zlib;
use Digest::CRC;
eval "use XML::Dumper;";
my $canxml=1;
if ($@) {
	$canxml=0;
	print STDERR "La libreria XML::Dumper non è stata caricata.\nInstallala per attivarla nel protocollo.\n";
}
$@='';

sub new {
	my $this=bless({},'Proto::CompProto');
	$this->{ricev}=[];
	return $this;
}

sub _bufferizza {
	my ($this,$pacc)=@_;
	$this->{pacc}.=$pacc;
	unless ($this->{Header}) {
		return 0 if (length($this->{pacc}) <11);
		my %header;
		@header{'proto','dim','compression','type','crc'}=unpack("CICCI",substr($this->{pacc},0,11,""));
		$this->{DataHeader}=\%header;
		$this->{Header}=1;
		return -1 if $header{proto} != 154;
	}
	return 0 if length($this->{pacc}) < $this->{DataHeader}->{dim};
	$this->{Header}=0;
	my $data=substr($this->{pacc},0,$this->{DataHeader}->{dim},"");
	return 0 if Digest::CRC::crc32($data) != $this->{DataHeader}->{crc};
	$data=uncompress($data) if $this->{DataHeader}->{'compression'};
	return 0 unless $data;
	my ($perlvar,$other);
	casi: {
		$perlvar=$data, last casi if $this->{DataHeader}->{type}==0;
		$perlvar=BinDump::MainDeDump($data), last casi if $this->{DataHeader}->{type}==1;
		$perlvar=xml2pl($data), last casi if $this->{DataHeader}->{type}==2 && $canxml;
		print "Formato pacchetto ignoto ".($this->{DataHeader}->{type})."\n";
		return -1;
	}
	undef $data;
	return 0 unless $perlvar;
	push(@{$this->{ricev}}, $perlvar);
	if ($this->{pacc}) {
		$other=$this->_bufferizza('');
		return -1 if $other<0;
	}
	return 1+$other;
}

sub sender {
	my ($this,$data,$tipo)=@_;
	if (ref($data) eq "HASH" || ref($data) eq "ARRAY") {
		switch:
		{
			if ($tipo==2 && $canxml) {
				return undef unless $data=pl2xml($data);
				last switch;
			}
			return undef unless $data=BinDump::MainDump($data,0,1);
			$tipo=1;
		}
	} else {
		$tipo=0;	
	}
	my ($compr)=0;
	if (length($data)>2000) {
		$compr=1;
		$data=compress($data);
	}
	#my $pacchetto=;
	#print "CompProto spedisce $pacchetto\n";
	return "\x9a".pack("ICCI", length($data),$compr,$tipo,Digest::CRC::crc32($data)).$data;
}

sub reader {
	my $this=shift;
	return shift(@{$this->{ricev}});
}







1;
