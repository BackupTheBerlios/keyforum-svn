package CheckFormat;
use strict;
use Itami::ConvData;



sub MsgList {
	my $hashref=shift;
	return undef if ref($hashref) ne "HASH";
	my $msgvalidi=0;
	my ($hash, $msg);
	while (($hash, $msg)=each(%$hashref)) {
		delete ($hashref->{$hash}), next if length($hash) != 16;
		delete ($hashref->{$hash}), next unless MsgFormat($msg);
		delete($msg->{EDIT_OF}) if $msg->{EDIT_OF} eq $hash;
		$msgvalidi++;
	}
	($msgvalidi) ? (return $hashref) : (return undef);
}
sub Sorter {
	my $hashref=shift;
	my ($md5, $msg, @msg,$priorita);
	@$msg[0,1,2,3,4]=({},{},{},{},{});
	while (($md5, $msg)=each(%$hashref)) {
		$priorita=priorita($md5, $msg);
		$msg[$priorita]->{$md5}=$msg;
	}
	return \@msg;
}
sub priorita {
	my ($md5,$msg)=@_;
	return 0 if $msg->{TYPE} == 3;
	return 1 if $msg->{TYPE} == 4;
	return 2 if $msg->{TYPE} == 1 && !exists $msg->{EDIT_OF};
	return 3 if $msg->{TYPE} == 1 && exists $msg->{EDIT_OF};
	return 3 if $msg->{TYPE} == 2 && !exists $msg->{EDIT_OF};
	return 4 if $msg->{TYPE} == 2 && exists $msg->{EDIT_OF};
	return 4;
}


sub MsgFormat {
	my $msg=shift;
	return undef if ref($msg) ne "HASH";
	#print "messaggi di tipo ".$msg->{TYPE}."\n";
	return FormatNewMsg($msg) if $msg->{TYPE} eq "1";	
	return FormatReplyMsg($msg) if $msg->{TYPE} eq "2";	
	return FormatAdmin($msg) if $msg->{TYPE} eq "3";
	return FormatMembri($msg) if $msg->{TYPE} eq "4";
	return undef;
}
sub FormatAdmin {
	my $comm=shift;
	return 1;	
}
sub FormatNewMsg {
	my $msg=shift;	
	return undef if $msg->{SEZ}=~ /\D/;
	return undef if $msg->{SEZ}<=0;
	return undef if length($msg->{AUTORE}) != 16;
	return undef if length($msg->{EDIT_OF}) != 16;
	return undef if $msg->{DATE} =~ /\D/;
	return undef if length($msg->{TITLE}) >200;
	return undef if length($msg->{SUBTITLE}) >250;
	return undef if length($msg->{BODY}) >50000;
	return undef if length($msg->{FIRMA}) >255;
	return undef if length($msg->{AVATAR}) >255;
	return undef if length($msg->{SIGN}) >130;
	return undef if $msg->{DATE}<1;
	return 1;
}
sub FormatReplyMsg {
	my $reply=shift;
	return undef if length($reply->{AUTORE}) != 16;
	return undef if length($reply->{EDIT_OF}) != 16;
	return undef if length($reply->{REP_OF}) != 16;
	return undef if $reply->{DATE} =~ /\D/;
	return undef if length($reply->{FIRMA}) >255;
	return undef if length($reply->{AVATAR}) >255;
	return undef if length($reply->{TITLE}) >200;
	return undef if length($reply->{BODY}) >50000;
	return undef if length($reply->{SIGN}) >130;
	return undef if $reply->{DATE}<1;
	return 1;
}
sub FormatMembri {
	my $membro=shift;
	return undef if length($membro->{AUTORE})<4;
	return undef if length($membro->{AUTORE})>30;
	return undef if length($membro->{PKEY})>130;
	return undef if $membro->{DATE} =~ /\D/;
	return undef if $membro->{DATE}<10;
	$membro->{PKEY}=ConvData::Bin2Dec($membro->{PKEY});
	return 1;
}


1;