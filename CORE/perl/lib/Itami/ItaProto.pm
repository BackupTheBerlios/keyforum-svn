package ItaProto;
my %protocolli;

#
#
#	_recv deve essere il nome del metodo per quando l'utente richiede i dati
#	_buff il nome del metodo quando si ricevono i dati dallo socket
#	_send deve formare il pacchetto per spedirlo (deve ritornare un pacchetto da spedire)
sub new {
	my ($packname, $proto)=@_;
	return $protocolli{$proto}->new() if exists $protocolli{$proto};
	return undef;
}


package DataProto;

$ItaProto::protocolli{data}=__PACKAGE__;

sub new {
	my ($packname)=@_;
	print "creo $packname\n";
	my $this=bless({},$packname);
	$this->{RecData}="";
}

sub _recv {
	my ($this, $lenght)=@_;
	$lenght=length($this->{RecData}) unless defined $lenght;
	return substr($this->{RecData},0,$lenght,"");
}
sub _buff {
	my ($this, $data)=@_;
	$this->{RecData}.=$data;
}
sub send {return $_[1];}