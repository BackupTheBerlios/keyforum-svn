package admin;
use Itami::BinDump;
use Itami::ConvData;
use strict;
sub new {
	my ($packname, $db, $fname)=@_;
	my $this=bless({}, $packname);
	$this->{DB}=$db;
	$this->{Fname}=$fname;
	return $this;	
}
sub EditSez {
	my ($this, $com,$date)=@_;
	return undef unless defined $com->{SEZID};
	return undef if $com->{SEZID}=~ /\D/;
	# Inserisco il nuovo record. Se non esiste viene creato altrimenti mi darà errore.
	# Così dopo basterà eseguire singoli update per ogni dato modificato.
	$this->{DB}->do("INSERT INTO ".$this->{Fname}."_sez (`ID`) values (?);",undef, $com->{SEZID});
	if (length($com->{PKEY})>100) {
		$this->{DB}->do("UPDATE ".$this->{Fname}."_sez SET `PKEY`=? WHERE ID=?;",undef, ConvData::Dec2Bin($com->{PKEY}),$com->{SEZID});
	}
	if (length($com->{MOD})>=32) {
		$this->{DB}->do("UPDATE ".$this->{Fname}."_sez SET `MOD`=CONCAT(`MOD`,?) WHERE ID=?;",undef, $com->{MOD},$com->{SEZID});
	}
	if (length($com->{SEZ_NAME}) || length($com->{SEZ_DESC})) {
		$this->{DB}->do("UPDATE ".$this->{Fname}."_sez SET `SEZ_NAME`=?,`SEZ_DESC`=?,`AUTOFLUSH`=?,`ORDINE`=?,`FIGLIO`=?,`last_admin_edit`=? WHERE `last_admin_edit`<=? AND ID=?;",
						undef, $com->{SEZ_NAME} || '', $com->{SEZ_DESC} || '',$com->{AUTOFLUSH} || '0',$com->{ORDINE} || '0',$com->{FIGLIO} || '0',$date,$date,$com->{SEZID});
	}
	if ($com->{ONLY_AUTH} eq "2" || $com->{ONLY_AUTH} eq "1") {
		$this->{DB}->do("UPDATE ".$this->{Fname}."_sez SET `ONLY_AUTH`=?,`last_admin_edit`=? WHERE `last_admin_edit`<=? AND ID=?;",
			undef, $com->{ONLY_AUTH}-1,$date,$date,$com->{SEZID});
	}
	return 1;
}
sub execute {
	my ($this, $command,$date)=@_;
	$command=BinDump::MainDeDump(MIME::Base64::decode_base64($command));
	return undef if ref($command) ne "HASH";
	my ($key, $value, $sing);
	while (($key, $value)=each %$command) {
		$this->ConfTable($value,$date),next if $key eq "ConfTable";
		foreach $sing (wantlist($value)) {
			next if ref($sing) ne "HASH";
			$this->EditSez($sing,$date), next if $key eq "EditSez";
			$this->AuthMem($sing,$date), next if $key eq "AuthMem";
		} 
	}
	
}
sub ConfTable {
	my ($this,$com,$date)=@_;
	my $fname=$this->{Fname};
	return undef unless $com=wantlistref($com);
	my $inserisci = $this->{DB}->prepare("INSERT INTO ".$fname."_conf (`GROUP`,`FKEY`,`SUBKEY`,`VALUE`,`date`,`present`) VALUES(?,?,?,?,?,?)");
	my $update = $this->{DB}->prepare("UPDATE ".$fname."_conf SET `VALUE`=?,`date`=?,`present`=? WHERE `GROUP`=? AND `FKEY`=? AND `SUBKEY`=? AND `date`<?");
	while(my $riga=pop(@$com)) {
		next if ref($riga) ne "HASH";
		if ($riga->{present}) { #Se deve essere presente la riga
			$update->execute($riga->{d} || '',$date,'1',$riga->{a} || '',$riga->{b} || '',$riga->{c} || '',$date);
			next if $update->rows;
			$inserisci->execute($riga->{a} || '',$riga->{b} || '',$riga->{c} || '',$riga->{d} || '',$date,'1');
			next;
		}
		#se deve essere cancellata.
		$update->execute('',$date,'0',$riga->{a} || '',$riga->{b} || '',$riga->{c} || '',$date);
		next if $update->rows;
		$inserisci->execute($riga->{a} || '',$riga->{b} || '',$riga->{c} || '','',$date,'0');
	}
	return 1;
}
sub AuthMem {
	my ($this,$com,$date)=@_;
	return undef if length($com->{AUTH})<100;
	return undef if length($com->{HASH})!=16;
	$this->{DB}->do("DELETE FROM ".$this->{Fname}."_purgatorio WHERE `HASH`=?;",undef,$com->{HASH});	
	my $sth=$this->{DB}->prepare("UPDATE ".$this->{Fname}."_membri SET `AUTH`=?,`is_auth`='1' WHERE HASH=?;");
	$sth->execute($com->{AUTH},$com->{HASH});
	unless ($sth->rows) {
		$this->{DB}->do("DELETE FROM ".$this->{Fname}."_congi WHERE `HASH`=?;",undef,$com->{HASH});	
		$this->{DB}->do("INSERT INTO ".$this->{Fname}."_membri (`HASH`,`is_auth`,`AUTH`,`present`) VALUES(?,?,?,'0');",undef,$com->{HASH},'1',$com->{AUTH});
	}
	
}
sub wantlist {
    my $var=shift;
    return values(%$var) if ref($var) eq "HASH";
    return @$var if ref($var) eq "ARRAY";
    return();
}
sub wantlistref {
    my $var=shift;
    return $var if ref($var) eq "ARRAY";
    return undef if ref($var) ne "HASH";
    my @vet=values(%$var);
    return \@vet;
}
# EditSez Aggiunge/Modifica nuove sezioni.
# AuthMem Assegna una firma dell'admin ad un membro
# CloseMsg Chiude o apre una discussione
# ConfTab Modifica le impostazioni generali del forum.
# EditMem Modifica alcune variabili di un utente.

1;