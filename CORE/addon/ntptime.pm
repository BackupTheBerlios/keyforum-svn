#SignTime|Permette di sincronizzare l'ora con il server ntp per una maggiore precisione
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
		print "NTPTIME: Sincronizzazione orologio con $host riuscita, Offset:".$GLOBAL::ntpoffset."\n";
	} else {
		print "NTPTIME: Sincronizzazione orologio con $host fallita.\n";
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