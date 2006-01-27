package ForumUtility;
use strict;
use Crypt::RSA;
use Math::Pari;

my $rsa=Crypt::RSA->new();

sub new {
    my ($packname,$fname,$id)=@_;
    my $this=bless({},$packname);
    @{$this}{'fname','id','query'}=($fname,$id,{});
    $this->{LoadSezInfo}=$GLOBAL::SQL->prepare("SELECT `ONLY_AUTH`,`AUTOFLUSH` FROM ".$fname."_sez WHERE ID=?");
    $this->{LoadUserData}=$GLOBAL::SQL->prepare("SELECT `PKEYDEC`,`is_auth`,`DATE`,`tot_msg_num`,`AUTORE` FROM ".$fname."_membri WHERE HASH=? AND present='1'");
    $this->{GetOrigAutNewMsg}=$GLOBAL::SQL->prepare("SELECT AUTORE FROM ".$fname."_newmsg WHERE HASH=? AND IS_EDIT='0'");
    $this->{GetOrigAutReply}=$GLOBAL::SQL->prepare("SELECT AUTORE FROM ".$fname."_reply WHERE HASH=? AND IS_EDIT='0'");
    $this->{ExistsThread}=$GLOBAL::SQL->prepare("SELECT count(*) as num FROM ".$fname."_newmsg WHERE EDIT_OF=?");
    $this->{ExistsReply}=$GLOBAL::SQL->prepare("SELECT count(*) as num FROM ".$fname."_reply WHERE EDIT_OF=?");
    $this->{LoadOriginalSez}=$GLOBAL::SQL->prepare("SELECT `SEZ` FROM ".$fname."_newmsg WHERE EDIT_OF=? AND `DATE`<? ORDER BY `DATE` DESC LIMIT 1");
    $this->{IsThreadClose}=$GLOBAL::SQL->prepare("SELECT block_date<? FROM ".$fname."_msghe WHERE HASH=? AND block_date>1000000");
    return $this;
}
sub InsertQuery {
    my ($this,$name,$query)=@_;
    $this->{query}->{$name}=$GLOBAL::SQL->prepare($query);
}
sub ExecuteQuery {
    my $this=shift;
    my $name=shift;
    die("Impossibile eseguire la query $name, non trovata.") unless exists $this->{query}->{$name};
    $this->{query}->{$name}->execute(@_);
    return $this->{query}->{$name}->fetchrow_arrayref->[0]
}
sub CheckSignPkey {
    my ($this,$md5,$sign,$pkey)=@_;
    return undef if length($sign)<100;
    return undef if length($pkey)<200;
    my $public=bless({},'Crypt::RSA::Key::Public');
    @{$public}{'n','e','Version','Identity'}=(PARI("$pkey"),PARI("65537"),"1.91","i");
    return $rsa->verify (
        Message    => $md5, 
        Signature  => $sign, 
        Key        => $public) || Warning("Errore nel modulo CheckRsa: ".$rsa->errstr());
    
}
sub Firma {
    my ($this,$md5,$private)=@_;
    return undef unless $md5;
    return undef unless $private;
    return $rsa->sign ( 
            Message    => $md5, 
            Key        => $private
        ) || Warning("Errore nel modulo CheckRsa: ".$rsa->errstr());
    
}

sub GetPrivateKey {
	my ($this,$codice,$pwd)=@_;
	my ($PRIVATE_DATA,$RSA_PRIVATE);
	return undef unless $codice;
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
sub LoadSezInfo {
    my ($this,$sezid)=@_;
    $this->{LoadSezInfo}->execute($sezid);
    return $_ if $_=$this->{LoadSezInfo}->fetchrow_hashref;
    return undef;
}
sub LoadOriginalSez {
    my ($this,$hash, $date)=@_;
    $this->{LoadOriginalSez}->execute($hash,$date);
    return $_->{SEZ} if $_=$this->{LoadOriginalSez}->fetchrow_hashref;
    return undef;
}
sub IsThreadClose {
     my ($this,$hash, $date)=@_;
    $this->{IsThreadClose}->execute($date,$hash);
    return $_ if $_=$this->{IsThreadClose}->fetchrow_hashref;
    return undef;
}
sub LoadUserData {
    my ($this,$autore)=@_;
    $this->{LoadUserData}->execute($autore);
    return $_ if $_=$this->{LoadUserData}->fetchrow_hashref;
    return undef;
}
sub ExistsThread {
    my ($this,$hash) = @_;
    $this->{ExistsThread}->execute($hash);
    return $_->{num} if $_=$this->{ExistsThread}->fetchrow_hashref;
    return undef;
}
sub ExistsReply {
    my ($this,$hash) = @_;
    $this->{ExistsReply}->execute($hash);
    return $_->{num} if $_=$this->{ExistsReply}->fetchrow_hashref;
    return undef;
}
sub GetOriginalAutoreNewMsg {
    my ($this,$hash)=@_;
    $this->{GetOrigAutNewMsg}->execute($hash);
    return $_->[0] if $_=$this->{GetOrigAutNewMsg}->fetchrow_arrayref;
    return undef;
}
sub GetOriginalAutoreReply {
    my ($this,$hash)=@_;
    $this->{GetOrigAutReply}->execute($hash);
    return $_->[0] if $_=$this->{GetOrigAutReply}->fetchrow_arrayref;
    return undef;
}

sub Warning {
    print STDERR shift;
    return undef;   
}
1;