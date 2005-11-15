package BinDump;

# Questo package è una mia piccola invenzione.
# Permette di fare il dump di variabili preregistrate o non.
# BinDump 0.02a by Daniele Guiducci
use strict;

# E' la lista delle variabili che si intende registrare.
# Deve essere uguale per tutti i CLIENT che usano questo package.
# Il nome della variabile viene trasformata in numero secondo l'ordine di inserimento.
# Cambiando questo ordine le comunicazione possono essere un po' (TANTO) confuse.
my @RegisterVar= qw();
my %TagName;
{
	my ($buf, $cont)=('',0);
	foreach $buf (@RegisterVar) {$TagName{$buf}="\x00".pack("C",$cont++);}
}

# $refvar=Riferimento da una variabile (solo HASH)
# $onlyregister= Se Vero fa il dump dei soli valori registrati per gli HASH.
# $subref = Se vero fa il dump dei riferimenti dell'hash (immaginate le sottodirectory).
# Ritorna il pacchetto binario dumpato.
sub MainDump {
	my ($refvar,$onlyregister, $subref)=@_;
	return undef unless UNIVERSAL::isa($refvar,'HASH');
	return "\x01".HashDump($refvar,$onlyregister, $subref);
}
# Fa L'operazione inmversa di MainDump.
# Come parametro accetta un pacchetto binario.
# Restituisce un puntatore a qualcosa.
sub MainDeDump {
	my $pacchetto=shift;
	return HashDeDump(\$pacchetto) if substr($pacchetto,0,1,"") eq "\x01";
	return undef;
}
# Fa l'operazione inversa di HashDump. Prende la struttura binaria a la scompatta in un HASH.
# Ritorna l'indirizzo dell'hash depaccato.
sub HashDeDump {
	my $pacchetto=shift;
	my $hash={};
	my ($key, $value);
	# Controllo il numero delle variabili dell'HASH.
	my $num=unpack("I", substr($$pacchetto,0,4,""));
	my $index=0;
	while ($index++ < $num && length($$pacchetto) > 2) {
		($key,$value)=(undef,undef);
		$key=GetKeyName($pacchetto);
		$value=GetValue($pacchetto);
		$hash->{$key}=$value if $key && $value;
	}
	return $hash;
}
# data la struttura binaria analizza i primi byte e ne prende il valore
sub GetValue {
	my $ref=shift;
	my $type=unpack("C",substr($$ref,0,1,""));
	if ($type==6) {
		my $lung=unpack("I", substr($$ref,0,4,""));
		return substr($$ref,0,$lung,"") if $lung;
		return "";
	}
	return unpack("C",substr($$ref,0,1,"")) if $type==1;
	return unpack("S",substr($$ref,0,2,"")) if $type==2;
	return unpack("I",substr($$ref,0,4,"")) if $type==3;
	return substr($$ref,0,16,"") if $type==5;
	return HashDeDump($ref) if $type==4;
	return ArrayDeDump($ref) if $type==7;
	return undef;	
}
# Dato l'indirizzo di uno scalare, tiglie i primi byte e prende il nome della variabile che segue.
# Se il tipo è \x00 allora è una variabile registrata.
# Se è 0x01 allora segue il nome della variabile in formato <Length 1byte><Name>
sub GetKeyName {
	my $ref=shift;
	my $type= substr($$ref,0,1,"");
	my $name;
	if ($type eq "\x01") {
		my $lung=unpack("C", substr($$ref,0,1,""));
		return substr($$ref,0,$lung,"") if $lung;
	}
	if ($type eq "\x00") {
		my $var=unpack("C", substr($$ref,0,1,""));
		return $RegisterVar[$var] if (defined($RegisterVar[$var]));
		return undef;
	}
	return undef;	
}
# L'HASH viene encodato un un pacchetto binario molto piccolo.
sub HashDump {
	my ($refvar,$onlyregister, $subref)=@_;
	my ($KeyName,$KeyValue)=('','');
	my $Ncoppie=0;
	my $struttura="";
	WDUMP: while ( my ($key, $value) = each %$refvar ) {
		KNAME:
		{
			$KeyName=$TagName{$key},last KNAME if exists $TagName{$key};
			$KeyName="\x01".pack("C/a*",$key),last KNAME unless $onlyregister;
			next WDUMP;
		}
		# 1=Unsigned Char | 2=Unsigned Short | 3=Unsigned Integer | 4=HASH
		# 5=HASH 16 BYTE | 6=Stringa | 7=ARRAY
		KVALUE:
		{	next WDUMP if !ref($value) && length("$value")==0;
			if (ref($value)) { # Se è un riferimento a qualcosa prova a fare il dumping
				next WDUMP unless $subref;
				$KeyValue="\x04".HashDump($value,$onlyregister,1),last KVALUE if ref($value) eq "HASH";
				$KeyValue="\x07".ArrayDump($value,$onlyregister,1),last KVALUE if ref($value) eq "ARRAY";
				next WDUMP;
			}
			if ($value!~ m/\D/ && $value<4294967296) { # Se contiene solo cifre decimali prova a fare il dumping
				$KeyValue="\x01".pack("C", $value),last KVALUE if $value<255;
				$KeyValue="\x02".pack("S", $value),last KVALUE if $value<65536;
				$KeyValue="\x03".pack("I", $value),last KVALUE;
			}
			$KeyValue="\x05".$value,last KVALUE if length($value)==16;
			# Se non è stato individuato un tipo approriato viene encodato come stringa.
			# Va bene per qualsiasi tipo di valore.
			$KeyValue="\x06".pack ("I/a*", "$value"); 
		}
		$Ncoppie++;
		$struttura.=$KeyName.$KeyValue;
	}
	return pack("I", $Ncoppie).$struttura;
}
sub PackValue {
	my ($value,$onlyregister,$subref)=@_;
	#print "pacco $value\n";
	if (ref($value)) { # Se è un riferimento a qualcosa prova a fare il dumping
		return undef unless $subref;
		return "\x04".HashDump($value,$onlyregister,1) if ref($value) eq "HASH";
		return "\x07".ArrayDump($value,$onlyregister,1) if ref($value) eq "ARRAY";
		return undef;
	}
	if ($value!~ m/\D/ && $value<4294967296) { # Se contiene solo cifre decimali prova a fare il dumping
		return "\x01".pack("C", $value) if $value<255;
		return "\x02".pack("S", $value) if $value<65536;
		return "\x03".pack("I", $value);
	}
	return "\x05".$value if length($value)==16;
	# Se non è stato individuato un tipo approriato viene encodato come stringa.
	# Va bene per qualsiasi tipo di valore.
	return "\x06".pack ("I/a*", $value); 
}
sub ArrayDeDump {
	my $pacchetto=shift;
	my ($index,$value,$estratti)=(0,"",0);
	my $num=unpack("I",substr($$pacchetto,0,4,""));
	my $arrayref=[];
	while ($estratti++<$num && length($$pacchetto)>2) {
		$value="";
		$index=unpack("S",substr($$pacchetto,0,2,""));
		$value=GetValue($pacchetto);
		$arrayref->[$index]=$value if $value;
	}
	return $arrayref;
}
sub ArrayDump {
	my ($arrayref,$onlyregister,$subref)=@_;
	return undef if ref($arrayref) ne "ARRAY";
	my $buf;
	my ($index,$struttura,$KeyValue,$totind)=(0,"","",0);
	foreach $buf (@$arrayref) {
		$KeyValue="";
		$KeyValue=PackValue($buf,$onlyregister,$subref) if $buf;
		if ($KeyValue) {
			$struttura.=pack("S",$index).$KeyValue;
			$totind++;
		}
		$index++;
	}
	return pack("I",$totind).$struttura;
}
1;