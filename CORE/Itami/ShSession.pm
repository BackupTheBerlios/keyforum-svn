package ShSession;
use strict;
use Itami::ShareDB;

sub new {
	my ($libname,$registersub, $recvdatasub,$removesub,$sender)=@_;
	my $this=bless({},$libname);
	$this->{Item}={};
	$this->{Gate}={};
	$this->{regsub}=$registersub;
	$this->{recdatasub}=$recvdatasub;
	$this->{remsub}=$removesub;
	$this->{Sender}=$sender;
	return $this;
}
sub AddItem {
	my ($this, $ogg)=@_;
	return undef if exists $this->{Item}->{$ogg};
	$this->{Item}->{$ogg}={};
	$this->{Sender}->($ogg, $this->Join({}));
	return 1;
}
sub DeleteItem {
	my ($this, $ogg)=@_;
	return undef unless exists $this->{Item}->{$ogg};
	return delete ${$this->{Item}}->{$ogg};
}
sub AddGate {
	my ($this, $name, $gate)=@_;	
	return undef if exists $this->{Gate}->{$name};
	$this->{Gate}->{$name}=$gate;
	return 1;
}
sub RecvData {
	my ($this, $ogg, $hashref)=@_;
	return undef if ref($hashref) ne "HASH";
	my ($Gate, $data);
	if ( $hashref) {}
	while (($Gate, $data)=each(%$hashref)) {
		next unless exists $this->{Gate}->{$Gate};
		$this->{recdatasub}->($this->{Gate}->{$Gate}, $ogg, $data);
	}
}

sub Join {
	my ($this, $daspedire)=@_;
	$daspedire->{JoinInto}={};
	my $Gate;
	foreach $Gate (keys(%{$this->{Gate}})) {
			1
	}
	return $daspedire;	
}
	
1;