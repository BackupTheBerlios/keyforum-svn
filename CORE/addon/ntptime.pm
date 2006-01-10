package SignTime;
use strict;
require BNTP;
use Time::Local;

$GLOBAL::ntpoffset=0;

sub Connect {
	my $host=$GLOBAL::CONFIG->{NTP};
	return undef unless length $host;
	
	if (my $pkt = Net::NTP::get_ntp_response($host)) {
		return undef if $pkt->{"Receive Timestamp"}<1109508769;
		$GLOBAL::ntpoffset=int($pkt->{"Receive Timestamp"}-time());
		print scalar localtime(time())." NTPTIME: Sync on $host done, Offset:".$GLOBAL::ntpoffset."\n";
	} else {
		print scalar localtime(time())." NTPTIME: Sync on $host failed.\n";
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