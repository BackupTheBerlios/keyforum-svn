package ConvData;
use strict;
use Math::BigInt;
use MIME::Base64;
# Pacchetto di utilità per convertire i vari tipi di dati.



# Converte un numero decimale in una stringa binaria
sub Dec2Bin {
	my $stringa=Math::BigInt->new(shift)->as_hex();
	substr($stringa,0,2,"");
	my $bin='';
	$bin=(chr hex $_).$bin while $_=substr($stringa,-2,2,"");
	return $bin;
}


# Converte da Binario a cifra decimale (in stringa)
sub Bin2Dec {
	return undef if length($_[0])==0;
	return Math::BigInt->new("0x".unpack("H*",$_[0]))->bstr();
}

# Converte da Base64 a decimale
sub Dec2Base64 {
	return MIME::Base64::encode_base64(Dec2Bin(shift),"");
}
sub Base642Dec {
	return Bin2Dec(MIME::Base64::decode_base64(shift),"");	
}
1;