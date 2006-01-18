package HashReq;
use strict;


sub new {
	my ($packname, $db, $fname)=@_;
	my $this=bless({},$packname);
	$this->{Fname}=$fname;
	return $this;	
}

sub check_req {
	my ($this, $req,$val)=@_;
	return undef if ref($val) ne "ARRAY";
	return undef unless exists $req->{MODO};
	my $query;
	SWITCH: {
		$query=$this->MODO1($req,$val),last SWITCH if $req->{MODO} eq "1"; # Cerca nei nuovi messaggi
		$query=$this->MODO2($req,$val),last SWITCH if $req->{MODO} eq "2"; # Cerca nei reply
		$query=$this->MODO3($req,$val),last SWITCH if $req->{MODO} eq "3"; # Cerca nei messaggi amministrativi
		$query=$this->MODO4($req,$val),last SWITCH if $req->{MODO} eq "4"; # Cerca in tutti i messaggi
		return undef;
	}
	return $query;
}
# Ricerca in congi
sub MODO4 {
	my ($this, $req,$val)=@_;
	my $where = "congi.CAN_SEND='1'";
	$where.=" AND ".$_ if $_=$this->where_date($req,$val);
	$where.=" AND ".$_ if $_=$this->where_type($req,$val);
	$where.=" AND ".$_ if $_=$this->where_autore($req,$val);
	$where.=$this->limit($req);
	return "SELECT congi.HASH"
	." FROM ".$this->{Fname}."_congi AS congi"
	." WHERE $where;";
}
# Ricerca in admin
sub MODO3 {
	my ($this, $req,$val)=@_;
	my $where = "tmp.HASH=congi.HASH AND congi.CAN_SEND='1'";
	$where.=" AND ".$_ if $_=$this->where_date($req,$val);
	$where.=" AND ".$_ if $_=$this->where_title($req,$val);
	$where.=$this->limit($req);
	return "SELECT congi.HASH"
	." FROM ".$this->{Fname}."_admin AS tmp, ".$this->{Fname}."_congi AS congi"
	." WHERE $where;";
}
# Ricerca in reply
sub MODO2 {
	my ($this, $req,$val)=@_;
	my $where = "tmp.HASH=congi.HASH AND congi.CAN_SEND='1'";
	$where.=" AND ".$_ if $_=$this->where_date($req,$val);
	$where.=" AND ".$_ if $_=$this->where_autore($req,$val);
	$where.=" AND ".$_ if $_=$this->where_title($req,$val);
	$where.=" AND ".$_ if $_=$this->where_editof($req,$val);
	$where.=" AND ".$_ if $_=$this->where_repof($req,$val);
	$where.=$this->limit($req);
	return "SELECT congi.HASH"
	." FROM ".$this->{Fname}."_reply AS tmp, ".$this->{Fname}."_congi AS congi"
	." WHERE $where;";
}
# Ricerca in thread
sub MODO1 {
	my ($this, $req,$val)=@_;
	my $where = "tmp.HASH=congi.HASH AND congi.CAN_SEND='1'";
	$where.=" AND ".$_ if $_=$this->where_date($req,$val);
	$where.=" AND ".$_ if $_=$this->where_autore($req,$val);
	$where.=" AND ".$_ if $_=$this->where_title($req,$val);
	$where.=" AND ".$_ if $_=$this->where_editof($req,$val);
	$where.=" AND ".$_ if $_=$this->where_sez($req,$val);
	$where.=$this->limit($req);
	return "SELECT congi.HASH"
	." FROM ".$this->{Fname}."_newmsg AS tmp, ".$this->{Fname}."_congi AS congi"
	." WHERE $where;";
}


####################################
# creazione delle clausule where della ricerca
####################################
sub limit {
	my ($this, $req)=@_;
	my ($desc,$num)=('',0);
	$desc="DESC " if $req->{ORDER} eq "DESC";
	$num=int($req->{LIMIT});
	$num=600 if $num>600;
	$num=100 if $num<10;
	return " ORDER BY WRITE_DATE ".$desc."LIMIT $num";
}
sub where_type {
	my ($this, $req,$val)=@_;
	return undef unless exists $req->{TYPE};
	return undef if $req->{TYPE}=~ /\D/;
	#return undef if $req->{TYPE}<0 || $req->{TYPE}>4;
	push(@$val, $req->{TYPE});
	return "TYPE=?";
}
sub where_sez {
	my ($this, $req,$val)=@_;
	return undef unless exists $req->{SEZ};
	return undef if $req->{SEZ}=~ /\D/;
	return undef unless $req->{SEZ};
	push(@$val, $req->{SEZ});
	return "SEZ=?";
}
sub where_repof {
	my ($this, $req,$val)=@_;
	return undef if length($req->{REP_OF})!=16;
	push(@$val, $req->{REP_OF});
	return "REP_OF=?";
}
sub where_editof {
	my ($this, $req,$val)=@_;
	return undef if length($req->{EDIT_OF})!=16;
	push(@$val, $req->{EDIT_OF});
	return "EDIT_OF=?";
}
sub where_title {
	my ($this, $req,$val)=@_;
	my $lung=length($req->{TITLE});
	if ($lung>1 && $lung<21) {
		push(@$val, '%'.$req->{TITLE}.'%');
		return "TITLE LIKE ?"
	}
	return undef;
}
sub where_autore {
	my ($this, $req,$val)=@_;
	if (length($req->{AUTORE})==16) {
		push(@$val, $req->{AUTORE});
		return "AUTORE=?";
	}
	return undef;
}
sub where_date {
	my ($this, $req,$val)=@_;
	my $where='';
	if($req->{MAX_DATE}>1000000000) {
		$where="congi.WRITE_DATE<?";
		push(@$val,$req->{MAX_DATE});
	}
	if($req->{MIN_DATE}>1000000000) {
		($where) ? ($where.=" AND congi.WRITE_DATE>?") : ($where.="congi.WRITE_DATE>?");
		push(@$val,$req->{MIN_DATE});
	}
	return $where;
}


1;