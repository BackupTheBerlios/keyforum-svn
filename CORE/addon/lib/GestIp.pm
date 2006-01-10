package GestIP;
use strict;
use HTTP::Request;
use LWP::UserAgent;
use IO::Socket::INET;
use Itami::Cycle;
my %ip_connessi;
my %bannati;
my %tring;

my $pager = LWP::UserAgent->new;
my $num_connessi=0;
my $MYSQL;
sub JustConn {
	return exists $ip_connessi{(shift)};
}
sub add2try {
	$tring{(shift)}=time();
}
sub remove2try {
	my $ip=shift;
	return delete $tring{$ip} if $ip;
	foreach my $buf (keys(%tring)) {
		delete $tring{$buf};	
	}
}
sub Banna {
	my $ip=shift;
	kfdebug::scrivi(8,2,4,undef,$ip);  # utente X bannato
	$bannati{$ip}=time();
	return undef unless defined $MYSQL;
	return undef unless $ip=Numerizza($ip);
	$MYSQL->do("DELETE FROM iplist WHERE `IP`=? AND STATIC='0'",undef,$ip);
}
sub Connesso {
	$ip_connessi{(shift)}=time();
	$num_connessi++;
}
sub Disconnesso {
	delete $ip_connessi{(shift)};
	$num_connessi--;
}
sub new {
	my ($packname, $sha1_hex, $DB,$site,$porta)=@_;
	$MYSQL=$DB;
	return undef if length($sha1_hex)!=40;
	my $this=bless({}, $packname);
	$this->{SQL}=$DB;
	$this->{Board}=$sha1_hex;
	$this->{ConteggioIP}=0;
	$this->{TimeToUpdate}=Cycle->new(150);
	my ($buf, $select);
	$this->{Insert}=$DB->prepare("INSERT INTO iplist (`BOARD`,`IP`,`FALLIMENTI`,`TCP_PORT`,`UDP_PORT`,`CLIENT_NAME`,`CLIENT_VER`,`DESC`,`TROVATO`) VALUES('".$sha1_hex."',?,?,?,?,?,?,?,?)");
	$this->{FallimentoGen}=$DB->prepare("UPDATE iplist SET FALLIMENTI=FALLIMENTI+1 WHERE IP=?");
	$this->{Connesso}=$DB->prepare("UPDATE iplist SET FALLIMENTI=0 WHERE IP=? AND BOARD='".$sha1_hex."'");
	$this->{SelectRand}=$DB->prepare("SELECT `IP`,`TCP_PORT`,`UDP_PORT`,`CLIENT_NAME`,`CLIENT_VER`,`DESC` FROM iplist WHERE `FALLIMENTI`=0 AND `BOARD`='".$sha1_hex."' LIMIT 25");
	$this->{Presente}=$DB->prepare("SELECT count(*) FROM iplist WHERE IP=? AND BOARD='".$sha1_hex."'");
	$this->{ContaIP}=$DB->prepare("SELECT count(*) FROM iplist WHERE BOARD='".$sha1_hex."'");
	$this->{UpDate}=$DB->prepare("UPDATE iplist SET `FALLIMENTI`=0,`TCP_PORT`=?,`UDP_PORT`=?,`CLIENT_NAME`=?,`CLIENT_VER`=?,`DESC`=? WHERE `IP`=? AND `BOARD`='".$sha1_hex."'");
	$this->{Delete}=$DB->prepare("DELETE FROM iplist WHERE `FALLIMENTI`>3 AND STATIC='0'");
	$this->Conteggio();
	return $this unless defined $site;
	$this->{LoadIpHttp} = HTTP::Request->new(GET => $site."?id=".$sha1_hex."&port=".$porta."&v=2");
	$this->{iamlive} = HTTP::Request->new(GET => $site."?id=".$sha1_hex."&port=".$porta."&notmsg=1");
	return $this;
}
sub Connect {
	my ($this, $ctcp)=@_;
	$this->Conteggio if $this->{TimeToUpdate}->check;
	$this->{Delete}->execute();
	my $num=int(rand($this->{ConteggioIP}));
	my ($ref,$ipformat);
	my $sth=$this->{SQL}->prepare("SELECT IP, TCP_PORT FROM iplist WHERE `BOARD`=? LIMIT $num,2");
	$sth->execute($this->{Board});
	my $socket;
	while ($ref=$sth->fetchrow_hashref) {
		$ipformat=Num2ip($ref->{IP});
		next if exists $ip_connessi{$ipformat};
		next if exists $bannati{$ipformat};
		next if exists $tring{$ipformat};
		kfdebug::scrivi(13,2,5,undef,$ipformat); #Provo a connettermi con 
		add2try($ipformat);
		$socket=$ctcp->TryConnect($ipformat, $ref->{TCP_PORT}, 6);
		$GLOBAL::tryconn{fileno $socket}=\&keyforum::tryconn;
		$this->{FallimentoGen}->execute($ref->{IP}) if $num_connessi>0;
	}
	$sth->finish;
	
}
sub IpRandList {
	my ($this)=@_;
	$this->{SelectRand}->execute();
	my ($ref,@ip_list);
	push(@ip_list,$ref) while $ref=$this->{SelectRand}->fetchrow_hashref;
	$this->{SelectRand}->finish;
	($#ip_list>-1) ? (return \@ip_list) :(return undef);
}
sub iamlive {
	my $this=shift;
	return undef unless exists $this->{iamlive};
	if ($this->{ConteggioIP}>6) {
		my $response = $pager->request($this->{iamlive});
		print scalar localtime(time())." Update on server:".$response->content."\n";
	} else {
		print scalar localtime(time())." Update on server: IP request\n";
		my ($ip,$porta);
		my $response = $pager->request($this->{LoadIpHttp});
		my $page=$response->content;
		while ($page=~ m/(\d+?\.\d+?\.\d+?\.\d+?):(\d+)/g) {
			($ip,$porta)=($1,$2);
			next if $porta<1 || $porta>65535;
			next if exists $bannati{$ip};
			next unless $ip=Numerizza($ip);
			
			$this->Aggiungi({IP=>$ip,TCP_PORT=>$porta},4);
		}
	}
	$this->Conteggio;
}
sub Conteggio {
	my ($this)=@_;
	$this->{ContaIP}->execute();
	my ($ref);
	$this->{ConteggioIP}=$ref->[0] if $ref=$this->{ContaIP}->fetchrow_arrayref;
	$this->{ContaIP}->finish;
	return $this->{ConteggioIP};
}
sub GetNumIp {
	return (shift)->{ConteggioIP};
}
sub Presente {
	my ($this, $ip, $info)=@_;
	return undef unless $ip=Numerizza($ip);
	$this->{UpDate}->execute($info->{TCP_PORT} || 0,$info->{UDP_PORT} || 0,$info->{CLIENT_NAME} || "",$info->{CLIENT_VER} || "",$info->{DESC}  || "",$ip);
	#if ($this->NelDatabase($ip)) {
	if ($this->{UpDate}->rows) {
		$this->{Connesso}->execute($ip);
	} else {
		return undef if $this->{ConteggioIP}>250;
		$this->{Insert}->execute($ip,0,$info->{TCP_PORT} || 0,$info->{UDP_PORT} || 0,$info->{CLIENT_NAME} || "",$info->{CLIENT_VER} || "",$info->{DESC}  || "",1);
	}
	return 1;
}
sub Aggiungi {
	my ($this, $info,$from)=@_;
	$from||=2;
	return undef if ref($info) ne "HASH";
	return undef if $info->{'IP'}=~ /\D/ or $info->{'IP'} < 1 or $info->{'IP'} > 4294967296;
	return undef if exists $bannati{Num2ip($info->{'IP'})};
	return undef if $this->NelDatabase($info->{IP});
	return undef if $this->{ConteggioIP}>200;
	$this->{Insert}->execute($info->{IP},2,$info->{TCP_PORT} || 0,$info->{UDP_PORT} || 0,$info->{CLIENT_NAME} || "",int($info->{CLIENT_VER}),$info->{DESC} || "",$from);
	return 1;
}
sub NelDatabase {
	my ($this, $ip)=@_;
	return undef unless $ip=Numerizza($ip);
	my ($num,$ref)=(0,0);
	$this->{Presente}->execute($ip);
	$num=$ref->[0] if $ref=$this->{Presente}->fetchrow_arrayref;
	$this->{Presente}->finish;
	return $num;
}
sub Numerizza {
	my $ip=shift;
	return $ip if $ip !~ /\D/;
	return unpack("I", inet_aton($ip)) || undef;
}
sub Num2Pacc {
	return pack("I", shift);	
}
sub Num2ip{
	return inet_ntoa(pack("I", shift));	
}


1;