package GloIni;
use strict;
my $ini;

sub load {
	my $file=shift;
	open (INI, "<$file") or return undef;
	my $line;
	$ini={};
	while (<INI>) {
		$line=$_;
		next if $line=~ m/^#/;
		$line =~ s/\015?\012/\n/g;
		eval {$ini->{$1}=$2} if $line =~ /(.+?)=(.+?)\n/;
	}
	close INI;
	return 1;
}
sub GetVal {
	return $ini->{$_[0]} if exists $ini->{$_[0]};
	return undef;
}





1;