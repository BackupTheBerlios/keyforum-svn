package ConvData;
use strict;
#use Math::BigInt;
use MIME::Base64;
use Math::BigIntFast;
# Pacchetto di utilità per convertire i vari tipi di dati.



# Converte un numero decimale in una stringa binaria
sub Dec2Bin {
	return pack("H*",Bit::Vector->new_Dec(1024,shift)->to_Hex);
}


# Converte da Binario a cifra decimale (in stringa)
sub Bin2Dec {
	return Bit::Vector->new_Hex(1032,unpack("H*",$_[0]))->to_Dec;
}

# Converte da Base64 a decimale
sub Dec2Base64 {
	return MIME::Base64::encode_base64(Dec2Bin(shift),"");
}
sub Base642Dec {
	return Bin2Dec(MIME::Base64::decode_base64(shift),"");	
}
1;