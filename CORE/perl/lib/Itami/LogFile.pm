package LogFile;
#use IO::Handle;
#use IO::File;
#require Tie::Handle;
#@ISA = qw(Exporter Tie::Handle);
use strict;
my $LogHand;

sub Open {
	#untie *STDERR;
	#$LogHand = new IO::File(">".$_[0]) or return undef;
	#untie *STDERR;
	#tie *STDERR, __PACKAGE__;
	return 1;
}
sub Save {

	1;
	
}
sub TIEHANDLE {
	my ($package, $file)=@_;
	open (STDERR, ">$file") or return undef;
	return 1;
}
sub PRINT {
	print $LogHand localtime(),@_,"<--\n";
	$LogHand->autoflush(1);
	
}
sub DESTROY {
	close $LogHand;	
}
1;