package Cycle;   # Fornisce alcune funzione per l'esecuzione di sub a tempi determinati.
use strict;
use Time::HiRes;
# Ad esempio se vogliamo eseguire una funzione solo 3 secondo dopo l'ultima volta che è stata eseguita
# allora si userà questa funzione.

# Le funzioni che fanno uso di questa libreria non contano molto
# sul tempo ma la sfruttano solo per limitare il carico della CPU.
sub new {
	my ($package, $cycle)=@_;
	my $timer=bless({},$package);
	#$timer->{last}=Time::HiRes::time();
	$timer->{last}=0;
	$timer->{every}=$cycle;
	return $timer;
}
sub check {
	my $timer=shift;
	return undef if Time::HiRes::time()-$timer->{'last'}<$timer->{every};
	$timer->{'last'}=Time::HiRes::time();
	return 1;	
}

1;