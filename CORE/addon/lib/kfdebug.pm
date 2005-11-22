package kfdebug;
use strict;
use IO::Socket::INET;
my $file=0;
my ($debugfile,$livello,$tipo,$mysql,$sth);

sub mysqllog {
	($livello,$tipo,$mysql)=@_;
	$sth=$mysql->prepare("INSERT INTO log (DATA,LIVELLO,TIPO,ACT_ID,ACT_VAL,IP,STRINGA) VALUES(?,?,?,?,?,?,?)");
}
sub setup {
	my ($dir,$level,$type)=@_;
	return undef unless $dir;
	my @now=localtime(time);
	my $filename=$dir . "/KF_log " . $now[3] . "-". ($now[4]+1) . "-" . ($now[5]+1900) . ".txt";
	open($debugfile,">>$filename") or return errore($filename);
	print "DebugFile: $filename con livello $level\n",
	$file=1;$livello=$level;
	$tipo=$type;
	my $now=localtime;
	
	print $debugfile "#####################################
#	KeyForum DebugFile	    #
#	Livello Debug $level  	    #
#    $now	    #
#####################################\n
";
	my $old=select($debugfile);
	$|=1;
	select($old);
	
}
sub scrivi {
	my ($liv,$type,$act_id,$act_val,$ip,$str)=@_;
	return undef if $livello < $liv;
	return undef unless $type & $tipo;
	return undef unless $sth;
	#print $debugfile "[". localtime() . "] " . shift() ."\n";
	$ip=Numerizza($ip) if $ip;
	$sth->execute(time(),$liv,$type,$act_id,$act_val || '0',$ip || '0',$str || '');
}
sub scrivi_force {
	my ($liv,$type,$act_id,$act_val,$ip,$str)=@_;
	return undef unless $sth;
	$ip=Numerizza($ip) if $ip;
	$sth->execute(time(),$liv,$type,$act_id,$act_val || '0',$ip || '0',$str || '');
}
sub Numerizza {
	return unpack("I", inet_aton(shift())) || undef;
}
sub errore {
	print STDERR "\nImpossibile aprire in scrittura il file $_[0]\n";
	return undef;	
}

END {
	if ($file) {
		my $now=localtime;
		print $debugfile "
#####################################
#  Exit $now   #
#####################################

";
	close($debugfile);
	}
}
1;