package ShareDB;
use SDBM_File;
require HashReq;
use strict;
use Fcntl; # For O_RDWR, O_CREAT, etc. all'inizio
use Itami::stati;
use Itami::Cycle;
use Itami::ConvData;
# $this contenuto:
# DataBase	=> Oggetto per accedere al database.
# DBM		=> Oggetto DBM con tutti gli MD5 delle righe.
# TabConf	=> {} Configurazione della tabella da condividere
#			|-> Identificatore	=> Nome della colonna che identifica una riga (Md5 etc...)
#			|-> TableName		=> Nome tabella da ocndividere
#			|-> Colonne			=> Colonne da scambiare! (lista array)
#			|-> LastOffer		=> Nome della colonna con la data dell'ultima offerta di essa ad altri nodi
#			|-> InsertDate		=> La data di creazione della riga.

# Non ho più voglia di programmare.
# E'8 ore che non faccio altro che scorrere migliaia di righe di codice cercando di ricordare un punto che forse
# può essere un punto debole per i lamer!!!
# FERMATE IL MONDO!!! VOGLIO SCENDERE!!!
# W l'erba B)
sub Declare {
	my $this=shift;
	$this->{Sender}=shift;	
}

sub new {
	my ($libname, $db, $dbm_path, $fname)=@_;
	my $this=bless({},$libname);
	$this->{DataBase}=$db;
	my %dbm;
	tie(%dbm, 'SDBM_File', $dbm_path, O_RDWR | O_CREAT, 0666) or return Error ("Impossibile aprire in scrittura il file $dbm_path DBM\n");
	$this->{DBM}=\%dbm;
	#$this->tab_conf(%tab_conf) if scalar(keys(%tab_conf))>0;
	$this->{Sessioni}={};
	$this->{HashReq}=HashReq->new($db, $fname);
	$this->{NumeroOggetti}=0;
	$this->{fname}=$fname;
	return $this;
}
sub Iscritti {
	my $this=shift;
	return $this->{NumeroOggetti};
}
sub tab_conf {
	my ($this, %tab_conf)=@_;
	my ($sth, $md5_list);
	$this->{TabConf}=\%tab_conf;
	$sth=$this->{DataBase}->prepare("SELECT ".($this->{TabConf}->{Identificatore}).",".($this->{TabConf}->{Type})." FROM ".($this->{TabConf}->{Table}));
	$sth->execute() or return Error($this->{DataBase}->errstr."\n");
	print "KEYFORUM: Creazione indice degli HASH.\n";
	$this->{DBM}->{$md5_list->[0]}=$md5_list->[1] while $md5_list=$sth->fetchrow_arrayref;
	$sth->finish;
	my ($query, $type, $list);
	$this->{Type}={};
	while (($type, $list)=each(%{$this->{TabConf}->{Query}})) {
		$this->{Type}->{$type}=[];
		foreach $query (@$list) {
			push(@{$this->{Type}->{$type}},$this->{DataBase}->prepare($query));
		}
	}
	$this->{Query}={};
	$this->{Query}->{RandomSelect}=$this->{DataBase}->prepare("SELECT HASH, TYPE, `ID` FROM ".($this->{TabConf}->{Table})." WHERE `CAN_SEND`='1' AND `ID`>? ORDER BY `ID`");
	$this->{Query}->{SendTime}=$this->{DataBase}->prepare("UPDATE ".($this->{TabConf}->{Table})." SET SNDTIME=SNDTIME+1 WHERE HASH=?");
	$this->{Query}->{GetType}=$this->{DataBase}->prepare("SELECT TYPE FROM ".$this->{TabConf}->{Table}." WHERE HASH=?");
	$this->{Query}->{Contali}=$this->{DataBase}->prepare("SELECT ID FROM ".$this->{TabConf}->{Table}." ORDER BY ID DESC LIMIT 1");
	$this->{Query}->{LastIns}=$this->{DataBase}->prepare("SELECT HASH,TYPE FROM ".($this->{TabConf}->{Table})." WHERE `CAN_SEND`='1' ORDER BY WRITE_DATE DESC LIMIT 150");
	#$this->{Query}->{SelPrio}=$this->{DataBase}->prepare("SELECT HASH FROM ".$this->{fname}."_priority ORDER BY `PRIOR` LIMIT 50");
	#$this->{Query}->{DelePrio}=$this->{DataBase}->prepare("DELETE FROM ".$this->{fname}."_priority WHERE HASH=?");
	
	# Converto le PKEY binarie in decimale dove non è stato fatto
	
	$this->contali();
	$this->{Index}=int(rand()*$this->{NumRows});
	delete $this->{TabConf}->{Query};
}
# $this->{"Sessioni"}={}
# $this->{"Sessioni"}->{$ItemA}={}
# $this->{"Sessioni"}->{$ItemA}->{"Item"}=$ItemA;
sub NewSession {
	my ($this,$oggname)=@_;
	return undef if exists $this->{Sessioni}->{"$oggname"};
	$this->{Sessioni}->{"$oggname"}={};
	$this->{NumeroOggetti}++;
	my $hash_req={};
	$hash_req->{MODO}=4;
	$hash_req->{TYPE}=1;	
	$hash_req->{LIMIT}=600;
	$hash_req->{ORDER}='DESC' if rand()<0.5;
	$this->{Sender}->($this->{TabConf}->{ShareName},'HASH_REQ', $oggname, $hash_req);
	my $msgref=$this->PrendiUltimiMsg();
	$this->{Sender}->($this->{TabConf}->{ShareName},'OFF_HASH', $oggname, $msgref) if $#{$msgref} > -1;
	return $oggname;
}
sub RemoveItem {
	my ($this, $ogg)=@_;
	$this->{NumeroOggetti}--;
	delete $this->{Sessioni}->{"$ogg"};
}
sub PrendiUltimiMsg {
	my $this=shift;
	$this->{Query}->{LastIns}->execute;
	my ($ref,@vet);
	while ($ref=$this->{Query}->{LastIns}->fetchrow_arrayref) {
		push(@vet,$ref->[0]);
		next if exists $this->{DBM}->{$ref->[0]};
		$this->{DBM}->{$ref->[0]}=$ref->[1];
	}
	$this->{Query}->{LastIns}->finish;
	return \@vet;
}
# Possibili messaggi che ricevo
# $hashref{OFF_HASH}=[] lista di messaggi che mi offre l'altro client
# $hashref{ROWS}={} mi ha inviato delle righe che probabilmente ho richiesto io
# $hashref{HASH_REQ}={} mi chiede una lista di hash (sta a lui decidere in che modo da devo prelevare dal database
#						può ad esempio richiedere gli ultimi 50hash inseriti nel mio DB
#						oppure gli hash inseriti nel giorno 25 dicembre del tipo 2
# $hashref{ROW_REQ}=[] Lista di hash di varie righe richieste dall'altro. IO devo rispondere con ROWS
sub RecvData {
	my ($this, $ogg, $hashref)=@_;
	#print "ricevo da share tab\n";
	return undef unless exists $this->{Sessioni}->{"$ogg"};
	return undef if ref($hashref) ne "HASH";
	$this->OFF_HASH($ogg, $hashref->{OFF_HASH}) if exists $hashref->{OFF_HASH};
	$this->ROW_REQ($ogg, $hashref->{ROW_REQ}) if exists $hashref->{ROW_REQ};
	$this->ROWS($ogg, $hashref->{ROWS}) if exists $hashref->{ROWS};
	$this->HASH_REQ($ogg, $hashref->{HASH_REQ}) if exists $hashref->{HASH_REQ};
	return 1;
}

sub ROWS {
	my ($this, $ogg, $ref)=@_;
}
################################
#
# Leggo i dati ricevuti e rispondo
################################
sub HASH_REQ {
	my ($this, $ogg, $ref)=@_;
	return undef if ref($ref) ne "HASH";
	my (@val,$ris,@hash);
	my $query=$this->{HashReq}->check_req($ref,\@val) || return undef;
	my $sth=$this->{DataBase}->prepare($query);
	$sth->execute(@val) or return undef;
	push(@hash,$ris->[0]) while $ris  = $sth->fetchrow_arrayref;
	#print "SHARE DB:Il nodo $ogg esegue: $query\n";
	kfdebug::scrivi(13,1,15,$ogg); # Fa un HASH REQ X
	kfdebug::scrivi(14,1,16,$#hash+1);  # e gli sono stati spediti Y HASH
	$this->{Sender}->($this->{TabConf}->{ShareName},"OFF_HASH", $ogg, \@hash) if $#hash>=0;
	return 1;
}
sub ROW_REQ {
	my ($this, $ogg, $ref)=@_;
	return undef if ref($ref) ne "ARRAY";
	return undef if length($ref->[0])!=16;
	my $riga=$ref->[0];
	kfdebug::scrivi(16,1,23,$#{$ref}+1);  # Mi richiede X messaggi
	kfdebug::scrivi(12,8,14,$ogg), return(undef) if $this->{Sessioni}->{$ogg}->{'LastHashReq'} =~ /\Q$riga\E/;
	$this->{Sessioni}->{$ogg}->{'LastHashReq'}=$this->{Sessioni}->{$ogg}->{'LastHashReq'}.$ref->[0];
	substr($this->{Sessioni}->{$ogg}->{'LastHashReq'},0,16,'') if length($this->{Sessioni}->{$ogg}->{'LastHashReq'})>=80;
	
	my $refe={};
	#my $hashcolum=$this->{TabConf}->{Identificatore};
	my ($hash, $query,$buff,$thisrow);
	my $numrow=0;
	MAINFOR: foreach $hash (@$ref) {
		next unless exists $this->{DBM}->{$hash};
		next unless exists $this->{Type}->{$this->{DBM}->{$hash}};
		foreach $query (@{$this->{Type}->{$this->{DBM}->{$hash}}}) {
			$query->execute($hash);
			while ($buff=$query->fetchrow_hashref) {
				$thisrow=$buff->{HASH};
				next if exists $refe->{$thisrow};
				delete $buff->{HASH};
				$refe->{$thisrow}=$buff;$numrow++;
				#$this->{Query}->{SendTime}->execute($thisrow);
				last MAINFOR if $numrow>300;
			}
			$query->finish;
		}
	}
	stati::updateadd($numrow,0,0,0,'BOARD','SENDMSG','TOTAL');
	#print "ShareDb: $ogg :Richiede ".($#{$ref}+1)." MSG. Inviati $numrow ".unpack("H*",$this->{Sessioni}->{$ogg}->{'LastHashReq'}).".\n";
	kfdebug::scrivi(17,1,24,$numrow);
	return $this->{Sender}->($this->{TabConf}->{ShareName},"ROWS", $ogg, $refe) if $numrow>0;
}
sub OFF_HASH {
	my ($this, $ogg, $ref)=@_;
	return undef if ref($ref) ne "ARRAY";
	my @ROW_REQ;
	kfdebug::scrivi(18,1,21,$#{$ref}+1); # Mi offrono X HASH random
	foreach my $hash (@$ref) {
		push(@ROW_REQ, $hash) unless exists $this->{DBM}->{$hash};
	}
	if ($#ROW_REQ>-1) {
		kfdebug::scrivi(17,1,22,$#ROW_REQ+1);  # Richiedo X HASH che mi mancano
		return $this->{Sender}->($this->{TabConf}->{ShareName},"ROW_REQ", $ogg, \@ROW_REQ);
	}
}
################################
# BroadCast Hash
#
################################
sub SendRandomHash {  # Invia hash random periodicamente.
	my ($this, $num)=@_;
	return undef if $this->{NumeroOggetti}<1;
	my ($tmp,@hash,$type);
	$this->{Query}->{RandomSelect}->execute($this->{Index}) or return Error("DB ERROR:".$this->{DataBase}->errstr."\n");
	my $numero=0;
	my $start_index=$this->{Index};
	while ($tmp=$this->{Query}->{RandomSelect}->fetchrow_arrayref) {
		$this->{DBM}->{$tmp->[0]}=$tmp->[1] unless exists $this->{DBM}->{$tmp->[0]};
		push(@hash, $tmp->[0]);
		if ($numero++>48) {
			$this->{Index}=$tmp->[2];
			last;
		}
	}
	$this->{Query}->{RandomSelect}->finish;
	$this->{Index}=0 if $start_index==$this->{Index};
	kfdebug::scrivi(21,1,20,$this->{Index});  # Siamo all'indice
	kfdebug::scrivi(17,1,19,$#hash+1); # Spedisci X HASH random ai vicini
	foreach $tmp (keys(%{$this->{Sessioni}})) {
		$this->{Sender}->($this->{TabConf}->{ShareName},"OFF_HASH", $tmp, \@hash) if $#hash>-1;
	}
	return 1;
}
sub OffertHashBrCa { # offre una lista di hash a tutti quegli iscritti
	my ($this, $arrayref)=@_;
	return undef if ref($arrayref) ne "ARRAY";
	return undef if scalar(@$arrayref)==0;
	my $sendto=0;my $type;
	foreach my $tmp (@$arrayref) {
		next if exists $this->{DBM}->{$tmp};
		$this->{Query}->{GetType}->execute($tmp);
		next unless $type=$this->{Query}->{GetType}->fetchrow_arrayref;
		$this->{DBM}->{$tmp}=$type->[0];
	}
	foreach my $tmp (keys(%{$this->{Sessioni}})) {
		$this->{Sender}->($this->{TabConf}->{ShareName},"OFF_HASH", $tmp, $arrayref);
		$sendto++;
	}
	print "SHARE DB: Offerti ".($#{$arrayref}+1)." HASH nuovi a $sendto persone\n";
	return $sendto;
}
sub GenericRequest { # richiede/offre una lista di hash a tutti quegli iscritti
	my ($this, $name,$ref)=@_;
	return undef if ref($ref) ne "ARRAY" && ref($ref) ne "HASH";
	my $sendto=0;
	foreach my $tmp (keys(%{$this->{Sessioni}})) {
		$this->{Sender}->($this->{TabConf}->{ShareName},$name, $tmp, $ref);
		$sendto++;
	}
	return $sendto;
}
sub RowReqDest {  # Richiede alcuni hash ad un client in particolare
	my ($this, $dest, $arrayref)=@_;
	return undef if ref($arrayref) ne "ARRAY";
	return undef if scalar(@$arrayref)==0;
	return undef unless exists $this->{Sessioni}->{"$dest"};
	return $this->{Sender}->($this->{TabConf}->{ShareName},"ROW_REQ", $dest, $arrayref);	
}

################################
# Ricerca Binaria
################################
sub contali {
	my $this=shift;
	$this->{Query}->{Contali}->execute();
	my $tmp;
	$this->{NumRows}=$tmp->[0] if $tmp=$this->{Query}->{Contali}->fetchrow_arrayref;
	$this->{NumRows}=0 unless $this->{NumRows};
	$this->{Query}->{Contali}->finish;
}
sub DESTROY {
	my $this=shift;
	delete $this->{DBM};
	print "chiuso tutto\n";
}
sub Error {
	print STDERR shift;
	return shift || undef;	
}

1;