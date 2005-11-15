package SignTime;
require BNTP;
use Time::Local;
my $offset=0;



sub Connect {
	my $host=shift;
	return undef unless length $host;
	
	if ($pkt = Net::NTP::get_ntp_response($host)) {
		return undef if $pkt->{"Receive Timestamp"}<1109508769;
		$offset=int($pkt->{"Receive Timestamp"}-time());
		print "Sincronizzazione orologio con $host riuscita, Offset:$offset\n";
		return $offset;
	}
	
}

sub TimeStampGM {
	return Time::Local::timelocal(gmtime(time()+$offset));
}
sub TimeStampLocal {
	return time()+$offset;
}
1;