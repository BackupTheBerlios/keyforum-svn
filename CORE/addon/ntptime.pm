package SignTime;
use strict;
require BNTP;
use Time::Local;



sub Connect {
	my $host=$GLOBAL::CONFIG->{NTP};
	return undef unless length $host;
	
	if (my $pkt = Net::NTP::get_ntp_response($host)) {
		return undef if $pkt->{"Receive Timestamp"}<1109508769;
		$GLOBAL::ntpoffset=int($pkt->{"Receive Timestamp"}-time());
		print "Sincronizzazione orologio con $host riuscita, Offset:".$GLOBAL::ntpoffset."\n";
	}
	
}

sub TimeStampGM {
	return Time::Local::timelocal(gmtime(time()+$GLOBAL::ntpoffset));
}
sub TimeStampLocal {
	return time()+$GLOBAL::ntpoffset;
}
&Connect;
1;