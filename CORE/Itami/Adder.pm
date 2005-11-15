package Adder;
use strict;
use Itami::ConvData;
sub new {
	my ($packname, $db, $fname)=@_;
	my $this=bless({}, $packname);
	$this->{Congi}=$db->prepare("INSERT INTO ".$fname."_congi (`HASH`,`TYPE`,`WRITE_DATE`,`INSTIME`,`AUTORE`) VALUES(?,?,?,?,?);");
	$this->{InviUpdate}=$db->prepare("UPDATE ".$fname."_newmsg SET visibile='0' WHERE EDIT_OF=? AND `DATE`<?");
	$this->{InsertNewMsg}=$db->prepare("INSERT INTO ".$fname."_newmsg "
		."(`HASH`,`SEZ`,`AUTORE`,`EDIT_OF`,`DATE`,`TITLE`,`SUBTITLE`,`BODY`,`FIRMA`,`AVATAR`,`SIGN`,`FOR_SIGN`,`visibile`) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)");
	$this->{UpDateAvatar}=$db->prepare("UPDATE ".$fname."_membri SET firma=?, avatar=?,edit_firma=? WHERE HASH=? AND ?>edit_firma");
	
	$this->{UpdateMember}=$db->prepare("UPDATE ".$fname."_membri SET `AUTORE`=?, `DATE`=?, `PKEY`=?, `SIGN`=?, `present`='1' WHERE `HASH`=?");
	$this->{UpAuthMember}=$db->prepare("UPDATE ".$fname."_membri SET `AUTH`=?,`is_auth`='1' WHERE `HASH`=?;");
	$this->{InsertMember}=$db->prepare("INSERT INTO ".$fname."_membri (`HASH`,`AUTORE`,`DATE`,`PKEY`,`SIGN`) VALUES(?,?,?,?,?)");
	$this->{UpDateCongi}=$db->prepare("UPDATE ".$fname."_congi SET CAN_SEND='1' WHERE HASH=?");
	$this->{InsertMsghe}=$db->prepare("INSERT INTO ".$fname."_msghe (`HASH`,`last_reply_time`,`last_reply_author`,`DATE`,`AUTORE`) VALUES(?,?,?,?,?)");
	
	$this->{SelMember}=$db->prepare("SELECT count(*) FROM ".$fname."_membri WHERE HASH=?");
	
	$this->{IncrementMsgNum}=$db->prepare("UPDATE ".$fname."_membri SET msg_num=msg_num+1 WHERE HASH=?");
	
	$this->{InviUpdateReply}=$db->prepare("UPDATE ".$fname."_reply SET visibile='0' WHERE EDIT_OF=? AND `DATE`<?");
	
	$this->{InsertReply}=$db->prepare("INSERT INTO ".$fname."_reply (`HASH`,`REP_OF`,`AUTORE`,`EDIT_OF`,`DATE`,`FIRMA`,`AVATAR`,`TITLE`,`BODY`,`SIGN`,`visibile`) VALUES(?,?,?,?,?,?,?,?,?,?,?)");
	
	$this->{InsertCommand}=$db->prepare("INSERT INTO ".$fname."_admin (`HASH`,`TITLE`,`COMMAND`,`DATE`,`SIGN`) VALUES(?,?,?,?,?)");
	
	$this->{IncMsghe}=$db->prepare("UPDATE ".$fname."_msghe SET reply_num=reply_num+1 WHERE HASH=?");
	
	$this->{IncThrSez}=$db->prepare("UPDATE ".$fname."_sez SET THR_NUM=THR_NUM+1 WHERE ID=?");
	$this->{IncRepSez}=$db->prepare("UPDATE `".$fname."_sez`,`".$fname."_newmsg` SET REPLY_NUM=REPLY_NUM+1"
									." WHERE ".$fname."_sez.ID=".$fname."_newmsg.SEZ AND ".$fname."_newmsg.visibile='1'"
									." AND ".$fname."_newmsg.EDIT_OF=?");
	$this->{UpDateLastTime}=$db->prepare("UPDATE ".$fname."_msghe SET last_reply_time=?,last_reply_author=? WHERE HASH=? AND last_reply_time<?");
	
	$this->{priority}=$db->prepare("INSERT INTO ".$fname."_priority (`HASH`,`PRIOR`) VALUES(?,?)");
	$this->{DB}=$db;
	return $this;
}
sub _AddType3 {
	my ($this, $md5, $msg)=@_;
	$this->{Congi}->execute($md5,"3",$msg->{DATE},time(),'');
	$this->{InsertCommand}->execute($md5, $msg->{TITLE} || '', $msg->{COMMAND} || '', $msg->{DATE}, $msg->{SIGN});
	return 1;
}
sub _AddType2_edit {
	my ($this, $md5, $msg)=@_;	
	$this->{InviUpdateReply}->execute($msg->{EDIT_OF},$msg->{DATE});
	return undef unless $this->{InviUpdateReply}->rows;
	my $cambiati='0';
	$cambiati='1' if $this->{DB}->{mysql_info}=~/Changed: (\d+?)/ && $1;
	$this->{Congi}->execute($md5,"2",$msg->{DATE},time(),$msg->{AUTORE});
	$this->{InsertReply}->execute($md5, $msg->{REP_OF}, $msg->{AUTORE}, $msg->{EDIT_OF}, $msg->{DATE},
		$msg->{FIRMA} || '', $msg->{AVATAR} || '',$msg->{TITLE} || '', $msg->{BODY} , $msg->{SIGN},$cambiati);	
	$this->_UpDateAvat($msg);
	return 1;
}
sub _AddType2 {
	my ($this, $md5, $msg)=@_;	
	$this->{Congi}->execute($md5,"2",$msg->{DATE},time(),$msg->{AUTORE});
	$this->{InsertReply}->execute($md5, $msg->{REP_OF}, $msg->{AUTORE}, $md5, $msg->{DATE},
		$msg->{FIRMA} || '', $msg->{AVATAR} || '',$msg->{TITLE} || '', $msg->{BODY} , $msg->{SIGN},'1');
    $this->{IncrementMsgNum}->execute($msg->{AUTORE});	
	$this->_UpDateAvat($msg);
	$this->{IncMsghe}->execute($msg->{REP_OF});
	$this->{IncRepSez}->execute($msg->{REP_OF});
	$this->{UpDateLastTime}->execute($msg->{DATE},$msg->{AUTORE},$msg->{REP_OF}, $msg->{DATE});
	return 1;
}
sub _AddType1_edit {
	my ($this, $md5, $msg)=@_;	
	$this->{InviUpdate}->execute($msg->{EDIT_OF},$msg->{DATE});
	return undef unless $this->{InviUpdate}->rows;
	my $cambiati='0';
	$cambiati='1' if $this->{DB}->{mysql_info}=~/Changed: (\d+?)/ && $1;
	$this->{Congi}->execute($md5,"1",$msg->{DATE},time(),$msg->{AUTORE});
	$this->{InsertNewMsg}->execute($md5, $msg->{SEZ}, $msg->{AUTORE}, $msg->{EDIT_OF}, $msg->{DATE},
		$msg->{TITLE} || '', $msg->{SUBTITLE} || '', $msg->{BODY}, $msg->{FIRMA} || '', $msg->{AVATAR} || '', $msg->{SIGN},$msg->{FOR_SIGN} || '',$cambiati);	
	$this->_UpDateAvat($msg);
	return 1;
}
sub _AddType1 {
	my ($this, $md5, $msg)=@_;
	$this->{Congi}->execute($md5,"1",$msg->{DATE},time(),$msg->{AUTORE});
	$this->{InsertNewMsg}->execute($md5, $msg->{SEZ}, $msg->{AUTORE}, $md5, $msg->{DATE},
		$msg->{TITLE} || '', $msg->{SUBTITLE} || '', $msg->{BODY}, $msg->{FIRMA} || '', $msg->{AVATAR} || '', $msg->{SIGN},$msg->{FOR_SIGN} || '','1');
	$this->{InsertMsghe}->execute($md5,$msg->{DATE}, $msg->{AUTORE},$msg->{DATE}, $msg->{AUTORE});
	$this->{IncrementMsgNum}->execute($msg->{AUTORE});
	$this->{IncThrSez}->execute($msg->{SEZ});
	$this->_UpDateAvat($msg);
	return 1;
}
sub _AddType4 {
	my ($this, $md5, $msg)=@_;
	$this->{UpDateCongi}->execute($md5);
	$this->{Congi}->execute($md5,"4",$msg->{DATE},time(),$md5) unless $this->{UpDateCongi}->rows;
	$msg->{PKEY}=ConvData::Dec2Bin($msg->{PKEY});
	if ($this->ExistsMember($md5)) {#$msg->{AUTORE}
		$this->{UpdateMember}->execute($msg->{AUTORE},$msg->{DATE},$msg->{PKEY},$msg->{SIGN} || '',$md5);
	} else {
		$this->{InsertMember}->execute($md5,$msg->{AUTORE},$msg->{DATE},$msg->{PKEY},$msg->{SIGN} || '');
	}
	return 1;
}
sub _UpDateType4 {
	my ($this, $md5, $msg)=@_;
	return undef unless $this->ExistsMember($msg->{AUTORE});
	$msg->{PKEY}=ConvData::Dec2Bin($msg->{PKEY});
	$this->{Congi}->execute($md5,"4",$msg->{DATE},time(),$md5);
	$this->{UpdateMember}->execute($msg->{AUTORE},$msg->{DATE},$msg->{PKEY},$msg->{SIGN},$md5);
	return 1;
}
sub _UpDateType4Auth {
	my ($this, $md5, $auth)=@_;
	$this->{UpAuthMember}->execute($auth, $md5);
	return 1;
}
sub _UpDateAvat {
	my ($this, $msg)=@_;
	return undef if length($msg->{FIRMA})==0 && length($msg->{AVATAR})==0;
	$this->{UpDateAvatar}->execute($msg->{FIRMA} || '',$msg->{AVATAR} || '',$msg->{DATE},$msg->{AUTORE},$msg->{DATE});
	return 1;
}
sub ExistsMember {
	my $this=shift;
	$this->{SelMember}->execute(shift);
	my $num=$this->{SelMember}->fetchrow_arrayref->[0];
	$this->{SelMember}->finish;
	return $num;
}
sub Priority {
	my ($this,$hash,$priority)=@_;
	$this->{priority}->execute($hash,$priority || 0);
}
1;