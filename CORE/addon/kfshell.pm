package kfshell;
use strict;
use Crypt::RSA;
use Digest::MD5;
use MIME::Base64;
use Crypt::Blowfish;
use Itami::ConvData;
use Itami::BinDump;
use Time::Local;
use Math::Pari;
my $uptime=time();
my $win32api=0;
my $hWnd;
my $TMPVAR={};

sub new {
	my ($ogg,$sock,$server)=@_;
	return undef unless $GLOBAL::ctcp->AddSock($sock,(MaxSleep=>120,type=>'compbase'));
	my $this=bless({},'kfshell');
	$this->{num}=$ogg;
	$GLOBAL::CLIENT{$ogg}=$this;
	return $this;
}

sub RecData {
	my ($this,$ogg,$data,$sock)=@_;
	return undef if ref($data) ne "HASH";
	$this->ResetSendVar();
	$this->act_RSA($data->{RSA}) if exists $data->{RSA};
	$this->act_INFO($data->{INFO}) if exists $data->{INFO};
	$this->act_HASHREQ($data->{HASHREQ}) if exists $data->{HASHREQ};
	$this->act_FUNC($data->{FUNC}) if exists $data->{FUNC};
	$this->act_FORUM($data->{FORUM}) if exists $data->{FORUM};
	$this->act_CORE($data->{'CORE'}) if exists $data->{'CORE'};
	$this->act_TMPVAR($data->{'TMPVAR'}) if exists $data->{'TMPVAR'};
	die("\n\nKeyForum chiuso per una richiesta dalla Shell.\n\n") if exists($data->{CHIUDI}) && $data->{CHIUDI};
	$GLOBAL::ctcp->send($this->{num},$this->{tosend});
	
}
sub act_TMPVAR {
	my ($this,$data)=@_;
	return undef if ref($data) ne "HASH";
	$this->{tosend}->{TMPVAR}={} unless exists $this->{tosend}->{TMPVAR};
	delete $TMPVAR->{$data->{DELVAR}} if exists $data->{DELVAR};
	aggiungi($TMPVAR,$data->{ADDVAR}) if exists $data->{ADDVAR};
	$this->{tosend}->{TMPVAR}->{DUMP}=$TMPVAR->{$data->{DUMP}} if exists $data->{DUMP};
	$this->{tosend}->{TMPVAR}->{BINDUMP}=BinDump::MainDump($TMPVAR->{$data->{BINDUMP}},0,1) if exists $data->{BINDUMP};
}
sub aggiungi {
    my ($var,$adder)=@_;
    return undef if ref($adder) ne "HASH";
    while (my ($key,$value)=each(%$adder)) {
        $var->{$key}=$value,next if ref($value) ne "HASH";
        $var->{$key}={} if ref($var->{$key}) ne "HASH";
        aggiungi($var->{$key},$value);
    }
}

sub act_FORUM {
	my ($this,$data)=@_;
	return undef if ref($data) ne "HASH";
	$this->{tosend}->{'FORUM'}={} unless exists $this->{tosend}->{'FORUM'};
	$this->act_FORUM_ADDMSG($data->{ADDMSG}) if exists $data->{ADDMSG};
}
sub act_FORUM_ADDMSG {
	my ($this,$data)=@_;
	return undef if ref($data) ne "HASH";
	my ($md5,$fdest)=($data->{'MD5'},$data->{'FDEST'});
	unless (exists $GLOBAL::Rule{$fdest}) {
		$this->{tosend}->{'FORUM'}->{'ADDMSG'}=-2;
		return undef;
	}
	my $msg={};
	$msg->{$md5}=$data;
	my ($AddedRows, $ReqRows)=$GLOBAL::Rule{$fdest}->AddRows($msg);
	if (scalar(@$AddedRows)) {
		$GLOBAL::Gate{$fdest}->OffertHashBrCa($AddedRows);
		$this->{tosend}->{'FORUM'}->{'ADDMSG'}=1;
	} else {
		$this->{tosend}->{'FORUM'}->{'ADDMSG'}=-1;
	}
}
sub act_CORE {
	my ($this,$data)=@_;
	return undef if ref($data) ne "HASH";
	$this->{tosend}->{'CORE'}={} unless exists $this->{tosend}->{'CORE'};
	$this->{tosend}->{'CORE'}->{'Win32HideWin'}=$this->act_CORE_HideWin($data->{'Win32HideWin'}) if exists $data->{'Win32HideWin'};
}
sub act_CORE_HideWin {
	my ($this,$data)=@_;
	return "Errore: Avevo tentato questa azione prima ma ho avuto dei problemi :(" if $win32api<0;
	if ($win32api==0) {
		eval "use Win32::API;";
		if ($@) {$win32api=-1;my $errore="$@";$@='';return "Errore: ".$errore;}
		eval {
			Win32::API->Import("kernel32.dll", 'HWND GetConsoleWindow()' ) || die("Errore nel caricamento di kernel32.dll");
			Win32::API->Import("user32.dll", 'BOOL ShowWindow( HWND hWnd, int iCommand )' ) || die("Errore nel caricamento di user32.dll");
			$hWnd = GetConsoleWindow();
		};
		if ($@) {$win32api=-1;my $errore="$@";$@='';return "Errore: ".$errore;}
		$win32api=1;
	}
	eval "ShowWindow(\$hWnd,0x00);" if $data eq 'hide';#Nasconde la finestra
	eval "ShowWindow(\$hWnd,0x04 );" if $data eq 'show';#Mostra la finestra
	if ($@) {my $errore="$@";$@='';return "Errore: ".$errore;}
	return "ok";
}
sub act_FUNC {
	my ($this,$data)=@_;
	return undef if ref($data) ne "HASH";
	$this->{tosend}->{'FUNC'}={} unless exists $this->{tosend}->{'FUNC'};
	$this->{tosend}->{'FUNC'}->{Dec2Bin}=ConvData::Dec2Bin($data->{Dec2Bin}) if exists $data->{Dec2Bin};
	$this->{tosend}->{'FUNC'}->{Bin2Dec}=ConvData::Bin2Dec($data->{Bin2Dec}) if exists $data->{Bin2Dec};
	$this->{tosend}->{'FUNC'}->{Dec2Base64}=ConvData::Dec2Base64($data->{Dec2Base64}) if exists $data->{Dec2Base64};
	$this->{tosend}->{'FUNC'}->{Base642Dec}=ConvData::Base642Dec($data->{Base642Dec}) if exists $data->{Base642Dec};
	$this->{tosend}->{'FUNC'}->{BlowDump2var}=BinDump::MainDeDump(DeCryptBlowFish($data->{BlowDump2var}->{Key},$data->{BlowDump2var}->{Data}))
		if exists $data->{BlowDump2var};
	$this->{tosend}->{'FUNC'}->{BinDump2var}=BinDump::MainDeDump($data->{BinDump2var}) if exists $data->{BinDump2var};
	$this->{tosend}->{'FUNC'}->{var2BinDump}=BinDump::MainDump($data->{var2BinDump},0,1) if exists $data->{var2BinDump};
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
	$this->act_RSA_GENKEY($data->{GENKEY}) if exists $data->{GENKEY};
}
sub act_RSA_GENKEY {
	my ($this,$data)=@_;
	my $verbosity=$data->{CONSOLE_OUTPUT} || '0';
	$this->{tosend}->{RSA}->{GENKEY}={} unless exists $this->{tosend}->{RSA}->{GENKEY};
	my $rsa=new Crypt::RSA;
	my ($public, $private) = $rsa->keygen (Identity  => 'io', Size => 1024,Verbosity => $verbosity) or return(
		$this->{tosend}->{RSA}->{GENKEY}->{ERR}=$rsa->errstr());
	$this->{tosend}->{RSA}->{GENKEY}->{pub}="".$public->{n};
	if (exists $data->{NICK}) { # richiesta di chiave privata per un utente, aggiungo hash e data
		my $pkey_dec = ConvData::Base642Dec($data->{PKEY64});
		my $store_date = Time::Local::timelocal(gmtime(time()+$GLOBAL::ntpoffset));
		my $store_hash = Digest::MD5::md5($pkey_dec.$data->{NICK}.$store_date.$public->{n});
		$this->{tosend}->{RSA}->{GENKEY}->{date} = $store_date;
		$this->{tosend}->{RSA}->{GENKEY}->{hash} = $store_hash;
		$this->{tosend}->{RSA}->{GENKEY}->{pkeydec} = $pkey_dec;
		
		$private = PrivateKey2Base64($private,0,$store_hash,$store_date);
	}
	else { 
		$private=PrivateKey2Base64($private,0,0,0);
	}
	$private=CryptBlowFish($data->{PWD},$private) if $data->{PWD};
	$this->{tosend}->{RSA}->{GENKEY}->{priv}=MIME::Base64::encode_base64($private,'');
	
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
		next unless $priv_key=GetPrivateKey($buf->{priv_pwd},$buf->{priv_key});
		$this->{tosend}->{RSA}->{FIRMA}->{$buf->{md5}}= $rsa->sign ( 
								Message    => $buf->{md5}, 
								Key        => $priv_key
						) || ($this->{tosend}->{RSA}->{FIRMA}->{"ERR".$buf->{md5}}=$rsa->errstr());
		$priv_key='';
	}
}


# FUNZIONI VARIE

# Decripta e scompatta la chiave privata
sub CryptBlowFish {
	my ($key, $testo_normale)=@_;
	my ($pezzo,$cipher,$testo_criptato);
	return undef unless $cipher = new Crypt::Blowfish $key;
	$testo_normale=pack("I/a*", $testo_normale);
	while (length($testo_normale)>0) {
		$pezzo=substr($testo_normale, 0, 8, "");
		$pezzo.="\x00"x(8-length($pezzo)) if length($pezzo)<8;
		$testo_criptato.=$cipher->encrypt($pezzo);
	}
	return $testo_criptato;
}
sub GetPrivateKey {
	my ($codice,$pwd)=@_;
	my ($PRIVATE_DATA,$RSA_PRIVATE);
	return undef unless $codice;
	#return undef unless $codice=MIME::Base64::decode_base64($codice);
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
	$RSA_PRIVATE->{'Identity'} = 'Board';
	$RSA_PRIVATE->{'Cipher'} = 'Blowfish';
	bless($RSA_PRIVATE,'Crypt::RSA::Key::Private');
	return $RSA_PRIVATE;
}
sub PrivateKey2Base64 {
	my ($private,$encode,$hash,$date)=@_;
	my $subpr={};
	$subpr->{Version} = "1.91";
	$subpr->{Checked} = "0";
	$subpr->{Identity} = "io";
	$subpr->{private}={};
	if ($hash && $date) {
		$subpr->{hash} = $hash;
		$subpr->{date} = $date;
	}
	my ($key, $value);
	$subpr->{private}->{$key}="$value" while ($key, $value)=each %{$private->{private}};
	return MIME::Base64::encode_base64(BinDump::MainDump($subpr,0,1),'') if $encode;
	return BinDump::MainDump($subpr,0,1);
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
print "KFSHELL: KeyForum Shell avviato ed in ascolto sulla porta ".$GLOBAL::CONFIG->{SHELL}->{TCP}->{PORTA}.".\n";
sub errore {
	my $errore=shift;
	die("Errore nel modulo kfshell.pm : $errore\n");
}

1;

