package phpdump;

sub binary2var($) {
	my $stringa=shift;
	return _strparse(\$stringa) if substr($stringa,0,2,'') eq "\x52\x03";
	return undef;
}
sub _strparse {
	my $stringa=shift;
	my $tipo=unpack("C",substr($$stringa,0,1,''));
	return substr($$stringa,0,unpack("I",substr($$stringa,0,4,'')),'') if $tipo==3;
	return unpack("I",substr($$stringa,0,4,'')) if $tipo==2;
	return (_arrayparse($stringa)) if $tipo==1;
	return undef;
}

sub _arrayparse {
	my $stringa=shift;
	my $vettore={};
	my $key;
	for(my $varnum=unpack("I",substr($$stringa,0,4,'')); $varnum>0; $varnum--) {
		$key=substr($$stringa,0,unpack("C",substr($$stringa,0,1,'')),'');
		$vettore->{$key}=_strparse($stringa);
		last if length($$stringa)<6;
	}
	return $vettore;
}
sub var2binary {
	my $var=shift;
	return "\x52\x03"._vardump($var);

}
sub _vardump {
	my $var=shift;
	return "\x01"._arraydump($var) if ref($var) eq "ARRAY";
	return "\x01"._hashdump($var) if ref($var) eq "HASH";
	return "\x03".pack("I", length("$var"))."$var";
}
sub _arraydump() {
	my $var=shift;
	my ($indice,$stringa)=(0,'');
	foreach my $buf (@$var) {
		$indice++;
		$stringa.=chr(length("$indice"))."$indice"._vardump($buf);
	}
	return pack("I",$indice).$stringa;
}
sub _hashdump {
	my $var=shift;
	my ($indice,$stringa)=(0,'');
	while (my($key,$value)=each(%$var)) {
		$indice++;
		$stringa.=chr(length("$key"))."$key"._vardump($value);
	}
	return pack("I",$indice).$stringa;
}
1;