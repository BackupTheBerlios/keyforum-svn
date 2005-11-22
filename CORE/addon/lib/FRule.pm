package FRule;
use strict;
use Itami::ConvData;
use Math::Pari;
#require "Depend.pm";
require "CheckFormat.pm";
require "CheckRsa.pm";
use Itami::Adder;
#require "SignTime.pm";
require "admin.pm";
sub new {
	my ($packname, $DB, $fname, $Identificatore, $public_key, $buffer_rule)=@_;
	my $sth=$DB->prepare("SELECT `GROUP`, `FKEY`, `SUBKEY`,`VALUE` FROM ".$fname."_conf ORDER BY `GROUP`, `FKEY`,`SUBKEY`");
	$sth->execute or return Error($DB->errstr."\n");
	my (@tmp);
	my $config={};
	addhashref($config,@tmp) while @tmp=$sth->fetchrow_array;
	$sth->finish;
	my $this=bless({},$packname);
	$this->{DB}=$DB;
	$this->{Config}=$config;
	$this->{Name}=$fname;
	$this->{Adder}=Adder->new($DB, $fname);
	$this->{PKEYADMIN}=$public_key;
	$this->{Rsa}=CheckRsa->new($DB, $fname, $public_key) or return Error("Errore imprevisto nella creazione oggetto per $fname RSA\n");
	$this->{Identificatore}=$Identificatore;
	#$this->{BufferMsg}=Depend->new($buffer_rule->{EMPTY_TIME}, $buffer_rule->{EMPTY_NUM});
	$this->{ExistsMember}=$DB->prepare("SELECT count(*) FROM ".$fname."_membri WHERE HASH=? AND present='1';");
	$this->{ExistsNewMsg}=$DB->prepare("SELECT count(*) FROM ".$fname."_newmsg WHERE HASH=?;");
	$this->{ExistsReply}=$DB->prepare("SELECT count(*) FROM ".$fname."_reply WHERE HASH=?;");
	$this->{ExistsSez}=$DB->prepare("SELECT `PKEY`,`MOD`,`ONLY_AUTH` FROM ".$fname."_sez WHERE ID=? AND ORDINE<9000;");
	$this->{ExistsHash}=$DB->prepare("SELECT count(*) FROM ".$fname."_congi WHERE HASH=?;");
	$this->{GetModByReply}=$DB->prepare("SELECT count(*) FROM ".$fname."_sez WHERE `MOD` like ?;");
	$this->{DatiMembro}=$DB->prepare("SELECT is_auth,msg_num,AUTORE,`DATE` FROM ".$fname."_membri WHERE HASH=?;");
	$this->{UnivocitaPkey}=$DB->prepare("SELECT count(*) FROM ".$fname."_membri WHERE PKEY=?;");
	$this->{AntiFloodCheck}=$DB->prepare("SELECT count(*) FROM ".$fname."_congi WHERE AUTORE=? AND WRITE_DATE>? AND WRITE_DATE<?;");
	$this->{AdminComm}=admin->new($DB,$fname);
	$this->AntiFloodFormat();
	return $this;
}
sub AntiFloodFormat {
	my $this=shift;
	return undef if ref($this->{Config}->{ANTIFLOOD_AUTH}) ne "HASH";
	my $hashref=$this->{Config}->{ANTIFLOOD_AUTH};
	foreach my $buf (sort(keys(%$hashref))) {
		$hashref->{$buf}->{RANGE_TIME}=int($hashref->{$buf}->{RANGE_TIME}/2);
		$hashref->{$buf}->{MAX_MSG}=int($hashref->{$buf}->{MAX_MSG})+2;
		delete($hashref->{$buf}), next if $hashref->{$buf}->{RANGE_TIME}<1;
		delete($hashref->{$buf}), next if $hashref->{$buf}->{MAX_MSG}<1;
	}	
}
sub AddRows {
	my ($this, $hashref)=@_;
	return undef unless $hashref=CheckFormat::MsgList($hashref); # Vengono cancellati i messaggi mal formattati
	#print "ci sono messaggi validi, ordino la lista\n";
	$hashref=CheckFormat::Sorter($hashref);  # Inserisco i msg in un vettore secondo un ordine di importanza.
	#print "controllo la firma digitale\n";
	my (%richiesti, %aggiunti);
	my ($md5, $msg, $odref,$truemd5,$dipen);
	my @risultato;
	$risultato[0]=[];
	$risultato[1]=[];
	foreach $odref (@$hashref) {
		while (($md5,$msg)=each %$odref) {
			$truemd5=$this->{Rsa}->MD5Make($msg);
			next if $this->ExistsHash($truemd5);
			if ($dipen=$this->Dipend($msg)) {
				push(@{$risultato[1]},$dipen);
				next;
			}
			next unless $this->RULE($truemd5,$msg);
			push(@{$risultato[0]},$truemd5);
		}
	}
	return @risultato;
}
sub AntiFlood {
	my ($this, $autore,$data)=@_;
	return undef if ref($this->{Config}->{ANTIFLOOD_AUTH}) ne "HASH";
	my $hashref=$this->{Config}->{ANTIFLOOD_AUTH};my $num;
	foreach my $buf (sort(keys(%$hashref))) {
		$this->{AntiFloodCheck}->execute($autore,
										 $data-$hashref->{$buf}->{RANGE_TIME},
										 $data+$hashref->{$buf}->{RANGE_TIME});
		$num=$this->{AntiFloodCheck}->fetchrow_arrayref->[0];
		$this->{AntiFloodCheck}->finish();
		return 1 if $num > $hashref->{$buf}->{MAX_MSG};
	}
	return undef;
}
sub RULE {
	my($this, $md5, $msg)=@_;
	return undef if $msg->{DATE}>Time::Local::timelocal(gmtime(time()+$GLOBAL::ntpoffset))+3700;
	return $this->RULE_TYPE3($md5, $msg) if $msg->{TYPE}==3;
	return undef if $msg->{DATE}<$this->{Config}->{'CORE'}->{'MSG'}->{'MAX_OLD'};
	return $this->RULE_TYPE4($md5, $msg) if $msg->{TYPE}==4;
	return undef if $this->AntiFlood($msg->{AUTORE},$msg->{DATE});
	return $this->RULE_TYPE1($md5, $msg) if $msg->{TYPE}==1;
	return $this->RULE_TYPE2($md5, $msg) if $msg->{TYPE}==2;
	return undef;
}
sub RULE_TYPE3 {
	my ($this, $md5, $msg)=@_;
	if ($this->{Rsa}->Validifica($md5, $msg->{SIGN},$this->{PKEYADMIN})) {
		kfdebug::scrivi(8,1,8); #Eseguo un msg amministrativo
	} else {
		kfdebug::scrivi(6,8,9); #Anomalia. msg admin non valido
		return undef;
	}
	$this->{AdminComm}->execute($msg->{COMMAND},$msg->{DATE});
	$this->{Adder}->_AddType3($md5,$msg);
	return 1;
}
sub RULE_TYPE2 {
	my($this, $md5, $msg)=@_;
	# Solo gli autorizzati possono scrivere.
	my $datimembro=$this->dati_member($msg->{AUTORE});
	return 0 unless $datimembro->[0];
	return undef if $datimembro->[3]>$msg->{DATE}; # Il messaggio non può essere più vecchi dell'autore
	return undef unless $this->{Rsa}->Validifica($md5,$msg->{SIGN},$this->{Rsa}->GetPkey($msg->{AUTORE}));
	if (length($msg->{EDIT_OF})==16) { # Se è una modifica di un messaggi riceve un trattamento speciale
		my $rauthor;
		return undef unless $rauthor=$this->GetOriginalAuthorReply($msg->{EDIT_OF});
		if ($rauthor ne $msg->{AUTORE}) { # Se i due autori sono differenti
			my $hex=unpack("H32",$msg->{AUTORE});
			my $mod;
			return undef unless $mod=$this->GetModByReply("%".$hex."%");
		}
		kfdebug::scrivi(26,1,25,undef,undef,$datimembro->[2]); #àAgiunta edit di 
		$this->{Adder}->_AddType2_edit($md5,$msg);
		return 1;
	}
	kfdebug::scrivi(26,1,26,undef,undef,$datimembro->[2]);  # AGgiunta risposta di X
	$this->{Adder}->_AddType2($md5,$msg);
	return 1;
}
sub RULE_TYPE1 {
	my($this, $md5, $msg)=@_;
	return undef unless defined $msg->{SEZ};
	$this->{ExistsSez}->execute($msg->{SEZ});
	my ($datisez,$rauthor);
	return undef unless $datisez=$this->{ExistsSez}->fetchrow_arrayref;  # Si caricano i dati della sezione e si esce se non esiste
	my $datimembro=$this->dati_member($msg->{AUTORE});  # Si prendono i dati dell'autore
	if ($datisez->[2]) {# se la sezione richiede di essere autorizzati
		return undef unless $datimembro->[0];  # e non lo si è....non viene aggiunto il msg
	}
	return 0 if $datimembro->[1]>0 && !$datimembro->[0];   # I non autorizzati possono scrivere un solo messaggio
	return undef if $datimembro->[3]>$msg->{DATE};  # Il messaggio non può essere più vecchi dell'autore
	# Si identifica l'autore.
	return undef unless $this->{Rsa}->Validifica($md5,$msg->{SIGN},$this->{Rsa}->GetPkey($msg->{AUTORE}));
	# Se il forum necessita di password si verifica se è corretta.
	if (length($datisez->[0])>10) {
		return undef if length($msg->{FOR_SIGN})<100;
		return undef unless $this->{Rsa}->Validifica($md5,$msg->{FOR_SIGN},ConvData::Bin2Dec($datisez->[0]));
	} else {
		delete $msg->{FOR_SIGN};
	}
	if (length($msg->{EDIT_OF})==16) { # Se è una modifica di un messaggi riceve un trattamento speciale
		return undef unless $rauthor=$this->GetOriginalAuthorNewMsg($msg->{EDIT_OF});
		if ($rauthor ne $msg->{AUTORE}) { # Se i due autori sono differenti
			my $hex=unpack("H*",$msg->{AUTORE});
			my $mod=$datisez->[1];
			return undef unless $mod=~ m/$hex/i;
		}
		kfdebug::scrivi(25,1,27,undef,undef,$datimembro->[2]);
		return $this->{Adder}->_AddType1_edit($md5,$msg);
	}
	kfdebug::scrivi(25,1,28,undef,undef,$datimembro->[2]);
	return $this->{Adder}->_AddType1($md5,$msg);
}
sub RULE_TYPE4 {
	my ($this, $md5, $msg)=@_;

	unless ($this->{Rsa}->Validifica($md5,$msg->{AUTH},$this->{PKEYADMIN})) {
		return undef unless $this->{Rsa}->Validifica($md5,$msg->{SIGN},$msg->{PKEY});
		# Ogni utente deve avere la PKEY differente (parte del controllo antiflood)
		if ($this->UnivocitaPkey(ConvData::Dec2Bin($msg->{PKEY}))) {
			kfdebug::scrivi(10,8,29,undef,undef,$msg->{AUTORE});
			return 0;
		}
		# Insert o update senza modificare l'AUTH
		kfdebug::scrivi(27,1,30,undef,undef,$msg->{AUTORE});
		$this->{Adder}->_AddType4($md5,$msg);
		return 1;
	}
	# Insert o update con AUTH
	return undef unless $msg->{SIGN};
	return undef unless $this->{Rsa}->Validifica($md5,$msg->{SIGN},$msg->{PKEY});
	#if ($msg->{SIGN}) {
	#	delete $msg->{SIGN} unless $this->{Rsa}->Validifica($md5,$msg->{SIGN},$msg->{PKEY});
	kfdebug::scrivi(25,1,31,undef,undef,$msg->{AUTORE});
	#}
	$this->{Adder}->_AddType4($md5,$msg);
	$this->{Adder}->_UpDateType4Auth($md5,$msg->{AUTH});
	return 1;
}
sub GetOriginalAuthorNewMsg {
	my ($this, $hash)=@_;
	my $sth=$this->{DB}->prepare("SELECT AUTORE FROM ".$this->{Name}."_newmsg WHERE HASH=? AND EDIT_OF=?;");
	$sth->execute($hash,$hash);
	my $ide;
	return $ide->[0] if $ide=$sth->fetchrow_arrayref;
	return undef;
}
sub GetOriginalAuthorReply {
	my ($this, $hash)=@_;
	my $sth=$this->{DB}->prepare("SELECT AUTORE FROM ".$this->{Name}."_reply WHERE HASH=?;");
	$sth->execute($hash);
	my $ide;
	return $ide->[0] if $ide=$sth->fetchrow_arrayref;
	return undef;
}
# COntrolla se al messaggio che si vuole aggiungere mancano altri messaggi.
# Ad esempi se si tenta di aggiungere un messaggio di PIPPO ma non abbiamo l'utente PIPPO
# la funzione torna l'HASH identificativo dell'utente PIPPO.
sub Dipend {
	my ($this,$msg)=@_;
	return undef if $msg->{TYPE}==3;
	return undef if $msg->{TYPE}==4;
	if ($msg->{TYPE}==1) {
		return $msg->{AUTORE} unless $this->ExistsMember($msg->{AUTORE});
		return $msg->{EDIT_OF} if exists $msg->{EDIT_OF} && !$this->ExistsNewMsg($msg->{EDIT_OF});
		return undef;
	}
	if ($msg->{TYPE}==2) {
		return $msg->{AUTORE} unless $this->ExistsMember($msg->{AUTORE});
		return $msg->{EDIT_OF} if exists $msg->{EDIT_OF} && !$this->ExistsReply($msg->{EDIT_OF});
		return $msg->{REP_OF} unless $this->ExistsNewMsg($msg->{REP_OF});
		return undef;
	}
}
sub GetModByReply {
	my $this=shift;
	$this->{GetModByReply}->execute(shift);
	my $num;
	$num=$num->[0] if $num=$this->{GetModByReply}->fetchrow_arrayref;
	$this->{GetModByReply}->finish;
	return $num;
}
sub ExistsMember {
	my $this=shift;
	$this->{ExistsMember}->execute(shift);
	my $num=$this->{ExistsMember}->fetchrow_arrayref->[0];
	$this->{ExistsMember}->finish;
	return $num;
}
sub dati_member {
	my $this=shift;
	$this->{DatiMembro}->execute(shift);
	my $num=$this->{DatiMembro}->fetchrow_arrayref;
	$this->{DatiMembro}->finish;
	return $num;
}
sub ExistsHash {
	my $this=shift;
	$this->{ExistsHash}->execute(shift);
	my $num=$this->{ExistsHash}->fetchrow_arrayref->[0];
	$this->{ExistsHash}->finish;
	return $num;
}
#UnivocitaPkey
sub ExistsNewMsg {
	my $this=shift;
	$this->{ExistsNewMsg}->execute(shift);
	my $num=$this->{ExistsNewMsg}->fetchrow_arrayref->[0];
	$this->{ExistsNewMsg}->finish;
	return $num;
}
sub UnivocitaPkey {
	my $this=shift;
	$this->{UnivocitaPkey}->execute(shift);
	my $num=$this->{UnivocitaPkey}->fetchrow_arrayref->[0];
	$this->{UnivocitaPkey}->finish;
	return $num;
}
sub ExistsReply {
	my $this=shift;
	$this->{ExistsReply}->execute(shift);
	my $num=$this->{ExistsReply}->fetchrow_arrayref->[0];
	$this->{ExistsReply}->finish;
	return $num;
}
######################################################
######################################################
sub can_write {
	
	
}
sub addhashref {
	my ($hash,@vet)=@_;
	return undef if $#vet<1;
	if ($#vet==1) {
		return if $vet[0] eq "";
		$hash->{$vet[0]}=$vet[1];
		return 1;
	}
	my $key=shift @vet;
	if ($key ne "") {
		$hash->{$key}={} if ref($hash->{$key}) ne "HASH";
		return addhashref($hash->{$key},@vet);
	}
	return addhashref($hash,@vet);
}

sub Error {
	print STDERR shift;
	return shift || undef;	
}

1;