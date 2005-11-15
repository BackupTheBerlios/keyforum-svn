package PerlScript;
use strict;
use Digest::MD5;

my %moduli;
my %var;

sub Execute {
	my ($dir, $file,$sock,$get, $post, $env)=@_;
	my $md5=Digest::MD5::md5(lc($dir.$file));
	#my $old_stdout;#open $old_stdout, "<&", \*STDOUT or return undef;#open STDOUT, ">&", $sock or return undef;
	my $page='';
	my $startsub;
	open(TMP, '>',\$page) || return undef;  # In questo modo catturo l'output su una variabile
	my $old=select TMP;
	my $header='';
	if (ref($moduli{$md5}) eq "CODE") {
		$moduli{$md5}->(\%var,$get, $post, $env,\$header);
	} else {
		$startsub=require $dir.$file;
		if (ref($startsub) eq "CODE") {
			$moduli{$md5}=$startsub;
			$startsub->(\%var,$get, $post, $env,\$header);
		}
	}
	select $old;
	close TMP;
	#$sock
	#open STDOUT, ">&",$old_stdout or die "impossibile aprire STOUT $!\n";
	return ($page,$header);
}
sub RegisterVar {
	$var{$_[0]}=$_[1];
	return 1;
}
1;