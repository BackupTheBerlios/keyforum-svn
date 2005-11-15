package ItemGroup;
use strict;

# Modulo per la gestione di gruppi di oggetti che condividono qualcosa.

sub new($%) {
	my ($packname)=@_;
	my $this=bless({},$packname);
	return $this;
}
sub EditGroup {
	my ($this, $groupname, $division)=@_;
	return undef unless exists $this->{$groupname};
	return $this->{$groupname}->EditDiv($division);
}
sub AddGroup {
	my ($this, $groupname, $division)=@_;
	return undef unless $groupname;
	return undef if exists $this->{$groupname};
	my $ref=SingGroup->new($division);
	if (ref $ref) {
		$this->{$groupname}=$ref ;
		return 1 if ref $ref;
	}
	return undef;
}
sub ExistsGroup {
	return exists $_[0]->{$_[1]};	
}
sub RemoveGroup {
	my ($this, $groupname)=@_;
	delete $this->{$groupname};
}
sub RemoveItem {
	my ($this, $groupname, $itemname)=@_;
	return undef unless exists $this->{$groupname};
	return $this->{$groupname}->Remove($itemname);

}
sub ItemList {
	return undef unless exists $_[0]->{$_[1]};
	return $_[0]->{$_[1]}->ItemList();
}
sub GroupList {
	return keys(%{$_[0]});	
}
sub AddItem($%) {
	my ($this,%iteminfo)=@_;
	return undef if !exists $iteminfo{Item} || !exists $iteminfo{Group};
	unless (exists $this->{$iteminfo{Group}}) {
		(exists $iteminfo{Division}) ? 
		($this->AddGroup($iteminfo{Group},$iteminfo{Division})) :
		(return undef);
	}
	return $this->{$iteminfo{Group}}->AddItem($iteminfo{Item},$iteminfo{Name});
}
sub Division {
	return 	$_[0]->{$_[1]}->Division;
}
package SingGroup;
sub new {
	my ($packname,$division)=@_;
	my $this=bless({},$packname);
	$this->{Division}=$division;
	$this->{ItemNum}=0;
	$this->{Item}={};
	return $this;
}
sub Remove {
	my ($this, $itemname)=@_;
	return undef unless delete $this->{Item}->{"$itemname"};
	--$this->{ItemNum};
	return 1;
}
sub EditDiv {
	$_[0]->{Division}=$_[1];	
}
sub ItemList {
	my ($this)=@_;
	return values(%{$this->{Item}});	
}
sub ItemNumber {
	my $this=shift;
	return $this->{ItemNum};
}
sub AddItem {
	my ($this, $item, $itemname)=@_;
	$itemname||=$item;
	return undef if exists $this->{Item}->{$itemname};
	$this->{Item}->{"$itemname"}=$item;
	return ++$this->{ItemNum};
}
sub Division {
	my $this=shift;
	return $this->{Division}/$this->{ItemNum} if $this->{ItemNum}>0 && $this->{Division}>0;
	return $this->{Division} if $this->{Division}<0;
	return 0;
}

1;