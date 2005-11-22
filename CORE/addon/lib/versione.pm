package ForumVer;
use strict;
my %dati;
$dati{protocollo}=2;
$dati{CLIENT_NAME}="KeyForum";
$dati{CLIENT_VER}=1;
sub FK {
	return $dati{$_[0]} if exists $dati{$_[0]};
	return undef;
}
sub Dumper {
	return %dati;	
}
sub SetVar {
	$dati{$_[0]}=$_[1];
}
	  
	  
	  
	  
	  
1;