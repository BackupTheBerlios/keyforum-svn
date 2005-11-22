package kfshell;
use strict;
use Crypt::RSA;
use MIME::Base64;
use Crypt::Blowfish;
use Itami::ConvData;
use Itami::BinDump;
use Time::Local;

my $uptime=time();


sub new {
	my ($ogg,$sock,$server)=@_;
	return undef unless $GLOBAL::ctcp->AddSock($sock,(MaxSleep=>120,type=>'compbase'));
	my $this=bless({},'kfshell');
	$this->{num}=$ogg;
	$GLOBAL::CLIENT{$ogg}=$this;
	$this->ResetSendVar;
	return $this;
}

sub RecData {
	my ($this,$ogg,$data,$sock)=@_;
	return undef if ref($data) ne "HASH";
	$this->act_RSA($data->{RSA}) if exists $data->{RSA};
	$this->act_INFO($data->{INFO}) if exists $data->{INFO};
	$this->act_HASHREQ($data->{HASHREQ}) if exists $data->{HASHREQ};
	$this->act_FUNC($data->{FUNC}) if exists $data->{FUNC};
	die("\n\nKeyForum chiuso per una richiesta dalla Shell.\n\n") if exists($data->{CHIUDI}) && $data->{CHIUDI};
	$GLOBAL::ctcp->send($this->{num},$this->{tosend});
	$this->ResetSendVar();
}
sub act_FUNC {
	my ($this,$data)=@_;
	$this->{tosend}->{'FUNC'}={} unless exists $this->{tosend}->{'FUNC'};
	$this->{tosend}->{'FUNC'}->{Dec2Bin}=ConvData::Dec2Bin($data->{Dec2Bin}) if exists $data->{Dec2Bin};
	$this->{tosend}->{'FUNC'}->{Bin2Dec}=ConvData::Bin2Dec($data->{Bin2Dec}) if exists $data->{Bin2Dec};
	$this->{tosend}->{'FUNC'}->{Dec2Base64}=ConvData::Dec2Base64($data->{Dec2Base64}) if exists $data->{Dec2Base64};
	$this->{tosend}->{'FUNC'}->{Base642Dec}=ConvData::Base642Dec($data->{Base642Dec}) if exists $data->{Base642Dec};
	$this->{tosend}->{'FUNC'}->{BlowDump2var}=BinDump::MainDeDump(DeCryptBlowFish($data->{BlowDump2var}->{Key},$data->{BlowDump2var}->{Data}))
		if exists $data->{BlowDump2var};
	$this->{tosend}->{'FUNC'}->{BinDump2var}=BinDump::MainDeDump($data->{BinDump2var}) if exists $data->{BinDump2var};
}
sub ResetSendVar {
	my $this=shift;
	$this->{tosend}={};
	$this->{tosend}->{'CORE'}->{'INFO'}->{'UPTIME'}=time()-$uptime;
	$this->{tosend}->{'CORE'}->{'INFO'}->{'GMT_TIME'}=Time::Local::timelocal(gmtime(time()+$GLOBAL::ntpoffset));
	$this->{tosend}->{'CORE'}->{'INFO'}->{'NTP_SEC'}=$GLOBAL::ntpoffset;
}
sub act_HASHREQ {
	my ($this,$data)=@_;
	return undef if ref($data) ne "HASH";
	my $tmp;
	while (my($key,$value)=each(%$data)) {
		next unless $tmp= keyforum::CheckGate($key);
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
	$this->{tosend}->{INFO}->{CONN}=\%GLOBAL::ItemSubscribe;
}
sub FreeBuff {

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
		unless($tmp= keyforum::CheckGate($buf)) {
			
			next;	
		}
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


# Creazione del server per di keyforum shell
{
	return 1 unless $GLOBAL::CONFIG->{SHELL}->{TCP}->{PORTA};
	my $kfshell = IO::Socket::INET->new(Listen => 5,
			LocalPort => $GLOBAL::CONFIG->{SHELL}->{TCP}->{PORTA},
			LocalAddr => $GLOBAL::CONFIG->{SHELL}->{TCP}->{BIND},
			Proto => 'tcp'
		) or errore("Impossibile creare il server SHELL sulla porta ".$GLOBAL::CONFIG->{SHELL}->{TCP}->{PORTA}."\nErrore:$!\n");
	$GLOBAL::SERVER{fileno($kfshell)}=\&kfshell::new;
	$GLOBAL::ctcp->AddSock($kfshell,(type=>'server')) or errore("Errore non previsto nell'aggiunta del'oggetto server SHELL\n");
}
sub errore {
	my $errore=shift;
	die("Errore nel modulo kfshell.pm : $errore\n");
}

1;

