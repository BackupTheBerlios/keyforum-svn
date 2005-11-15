package DBIpp;
use DBI;
use strict;
our $AUTOLOAD;

#@ISA = qw(DBI);
# DBI	=> Oggetto DBI
sub new {
	my $libname=shift;
	my $sqlobj= DBI->connect(@_);
	my $this=bless ({},$libname);
	$this->{DBI}=$sqlobj;
	return $this;
}
sub InsertBuffer {
	my $db=shift;
	$db->{DBIpp}->{Conf}->{Buffer}=shift;
}
sub insert {
	my $db=shift;
	return $db->do(@_) unless $db->{DBIpp}->{Conf}->{Buffer};
	
	1;
}

sub AUTOLOAD {
	my $this=shift;
	my $name=$AUTOLOAD;
	$name =~ s/.*://; 
	return $this->{DBI}->$name(@_);
}



1;