package CycleFunc;   # Fornisce alcune funzione per l'esecuzione di sub a tempi determinati.
use strict;
use Time::HiRes;
# Ad esempio se vogliamo eseguire una funzione solo 3 secondo dopo l'ultima volta che è stata eseguita
# allora si userà questa funzione.

# Le funzioni che fanno uso di questa libreria non contano molto
# sul tempo ma la sfruttano solo per limitare il carico della CPU.
sub new {
	my ($package, $cycle,$func)=@_;
	my $timer=bless({},$package);
	#$timer->{last}=Time::HiRes::time();
	$timer->{'next'}=0;
	$timer->{'func'}=$func if ref($func) eq "CODE";
	$timer->{'every'}=$cycle;
	return $timer;
}
sub check {
	my $timer=shift;
	#return undef if Time::HiRes::time()-$timer->{'last'}<$timer->{every};
	return undef if $timer->{'next'}>Time::HiRes::time();
	$timer->{'next'}=Time::HiRes::time()+$timer->{'every'};
	$timer->{'func'}->() if exists $timer->{'func'};
	return 1;	
}

1;