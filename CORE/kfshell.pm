package kfshell;
use Crypt::RSA;
use MIME::Base64;
use Crypt::Blowfish;
use strict;
my $uptime=time();
my $sender;
sub sender {$sender=shift;}

sub new {
	my $ogg=shift;
	my $this=bless({},'kfshell');
	$this->{num}=$ogg;
	$this->ResetSendVar;
	return $this;
}

sub RecData {
	my ($this,$data)=@_;
	return undef if ref($data) ne "HASH";
	$this->act_RSA($data->{RSA}) if exists $data->{RSA};
	$this->act_INFO($data->{INFO}) if exists $data->{INFO};
	$this->act_HASHREQ($data->{HASHREQ}) if exists $data->{HASHREQ};
	die("\n\nKeyForum chiuso per una richiesta dalla Shell.\n\n") if exists($data->{CHIUDI}) && $data->{CHIUDI};
	$sender->($this->{num},$this->{tosend});
	$this->ResetSendVar();
}
sub ResetSendVar {
	my $this=shift;
	$this->{tosend}={};
	$this->{tosend}->{'CORE'}->{'INFO'}->{'UPTIME'}=time()-$uptime;
}
sub act_HASHREQ {
	my ($this,$data)=@_;
	return undef if ref($data) ne "HASH";
	my $tmp;
	while (my($key,$value)=each(%$data)) {
		next unless $tmp= ShSession::CheckGate($key);
		$this->{tosend}->{HASHREQ}->{$key}=$tmp->GenericRequest('HASH_REQ',$value);
	}
}
sub act_INFO {
	my ($this,$data)=@_;
	return undef if ref($data) ne "HASH";
	$this->{tosend}->{INFO}={} unless exists $this->{tosend}->{INFO};
	$this->act_INFO_FORUM($data->{FORUM}) if exists $data->{FORUM};
	$this->act_INFO_CONN if exists($data->{CONN}) && $data->{CONN};
}
sub act_INFO_CONN {
	my $this=shift;
	$this->{tosend}->{INFO}->{CONN}=ShSession::retItemSubscribe();
}
sub act_RSA {
	my ($this,$data)=@_;
	return undef if ref($data) ne "HASH";
	$this->{tosend}->{RSA}={} unless exists $this->{tosend}->{RSA};
	$this->act_RSA_FIRMA($data->{FIRMA}) if exists $data->{FIRMA};
}
sub act_INFO_FORUM {
	my ($this,$data)=@_;
	return undef if ref($data) ne "HASH";
	$this->{tosend}->{INFO}->{FORUM}={} unless exists $this->{tosend}->{INFO}->{FORUM};
	my $tosend=$this->{tosend}->{INFO}->{FORUM};
	my $tmp;
	foreach my $buf (values(%$data)) {
		$tosend->{$buf}={} unless exists $tosend->{$buf};
		next unless $tmp= ShSession::CheckGate($buf);
		$tosend->{$buf}->{NUM_NODI}=$tmp->Iscritti;
	}
}
sub act_RSA_FIRMA {
	my ($this,$data)=@_;
	return undef if ref($data) ne "HASH";
	$this->{tosend}->{RSA}->{FIRMA}={} unless exists $this->{tosend}->{RSA}->{FIRMA};
	my $priv_key;
	my $rsa=new Crypt::RSA;
	foreach my $buf (values(%$data)) {
		next unless $priv_key=GetPrivateKey($buf->{priv_key},$buf->{priv_pwd});
		$this->{tosend}->{RSA}->{FIRMA}->{$buf->{md5}}= $rsa->sign ( 
								Message    => $buf->{md5}, 
								Key        => $priv_key
						) || ($this->{tosend}->{RSA}->{FIRMA}->{"ERR".$buf->{md5}}=$rsa->errstr());
		$priv_key='';
	}
}


# FUNZIONI VARIE

# Decripta e scompatta la chiave privata
sub GetPrivateKey {
	my ($codice,$pwd)=@_;
	my ($PRIVATE_DATA,$RSA_PRIVATE);
	return undef unless $codice;
	return undef unless $codice=MIME::Base64::decode_base64($codice);
	$codice=DeCryptBlowFish($pwd,$codice) if $pwd;
	return undef unless $PRIVATE_DATA=BinDump::MainDeDump($codice);
	return undef unless ref($PRIVATE_DATA->{private}) eq "HASH";
	$RSA_PRIVATE={};
	$RSA_PRIVATE->{private}={};
	foreach my $chiave (keys %{$PRIVATE_DATA->{private}}) {
		$RSA_PRIVATE->{private}->{$chiave}=PARI($PRIVATE_DATA->{private}->{$chiave});
		delete $PRIVATE_DATA->{private}->{$chiave};
	}
	$RSA_PRIVATE->{Version} = "1.91";
	$RSA_PRIVATE->{Checked} = 0;
	$RSA_PRIVATE->{Identity} => 'Board';
	$RSA_PRIVATE->{'Cipher'} => 'Blowfish';
	bless($RSA_PRIVATE,'Crypt::RSA::Key::Private');
	return $RSA_PRIVATE;
}

# Decripta dati criptati in blowfish
sub DeCryptBlowFish {
	my ($key, $testo_criptato)=@_;
	return undef if length($testo_criptato)%8!=0;  # La lunghezza del dato deve essere multiplo di 8
	my ($pezzo,$cipher,$testo_normale);
	return undef unless $cipher = new Crypt::Blowfish $key;
	while (length($testo_criptato)>0) {
		$pezzo=substr($testo_criptato, 0, 8, "");
		$testo_normale.=$cipher->decrypt($pezzo);
	}
	return unpack("I/a",$testo_normale);	
}
1;