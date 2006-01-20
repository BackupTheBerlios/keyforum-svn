package admin;
use Itami::BinDump;
use Itami::ConvData;
use strict;
sub new {
	my ($packname, $fname,$id)=@_;
	my $this=bless({}, $packname);
	$this->{Fname}=$fname;
	$this->{id}=$id;
	return $this;	
}
sub EditSez {
	my ($this, $com,$date)=@_;
	return undef unless defined $com->{SEZID};
	return undef if $com->{SEZID}=~ /\D/;
	# Inserisco il nuovo record. Se non esiste viene creato altrimenti mi darà errore.
	# Così dopo basterà eseguire singoli update per ogni dato modificato.
	$GLOBAL::SQL->do("INSERT INTO ".$this->{Fname}."_sez (`ID`) values (?);",undef, $com->{SEZID});

	if (length($com->{SEZ_NAME}) || length($com->{SEZ_DESC})) {
		$GLOBAL::SQL->do("UPDATE ".$this->{Fname}."_sez SET `SEZ_NAME`=?,`SEZ_DESC`=?,`AUTOFLUSH`=?,`ORDINE`=?,`FIGLIO`=?,NEED_PERM=?,`ONLY_AUTH`=?,`last_admin_edit`=? WHERE `last_admin_edit`<=? AND ID=?;",
						undef, $com->{SEZ_NAME} || '', $com->{SEZ_DESC} || '',$com->{AUTOFLUSH} || '0',$com->{ORDINE} || '0',$com->{FIGLIO} || '0',$com->{NEED_PERM} || '0',$com->{ONLY_AUTH} || '0',$date,$date,$com->{SEZID});
	}
	return 1;
}
sub execute {
	my ($this, $command,$date)=@_;
	$command=BinDump::MainDeDump($command);
	return undef if ref($command) ne "HASH";
	my ($key, $value, $sing);
	while (($key, $value)=each %$command) {
		$this->ConfTable($value,$date),next if $key eq "ConfTable";
		foreach $sing (wantlist($value)) {
			next if ref($sing) ne "HASH";
			$this->EditSez($sing,$date), next if $key eq "EditSez";
			$this->AuthMem($sing,$date), next if $key eq "AuthMem";
			$this->EditPerm($sing,$date), next if $key eq "EditPerm";
		} 
	}
	
}
sub EditPerm {
	my ($this, $com,$date)=@_;
	return undef if ref($com) ne "HASH";
	return $GLOBAL::Permessi->{$this->{id}}->EditPermessi($com->{autore},$com->{data} || $date,$com->{chiave1},$com->{chiave2},$com->{valore});
}
sub ConfTable {
	my ($this,$com,$date)=@_;
	my $fname=$this->{Fname};
	return undef unless $com=wantlistref($com);
	my $inserisci = $GLOBAL::SQL->prepare("INSERT INTO ".$fname."_conf (`GROUP`,`FKEY`,`SUBKEY`,`VALUE`,`date`,`present`) VALUES(?,?,?,?,?,?)");
	my $update = $GLOBAL::SQL->prepare("UPDATE ".$fname."_conf SET `VALUE`=?,`date`=?,`present`=? WHERE `GROUP`=? AND `FKEY`=? AND `SUBKEY`=? AND `date`<?");
	while(my $riga=pop(@$com)) {
		next if ref($riga) ne "HASH";
		unless ($riga->{delete}) { #Se deve essere presente la riga
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
	LoadForumConfig::Load($fname,$this->{id});  # Ricarico la configurazione
	return 1;
}
sub AuthMem {
	my ($this,$com,$date)=@_;
	return undef if length($com->{AUTH})<100;
	return undef if length($com->{HASH})!=16;	
	my $sth=$GLOBAL::SQL->prepare("UPDATE ".$this->{Fname}."_membri SET `AUTH`=?,`is_auth`='1' WHERE HASH=?;");
	$sth->execute($com->{AUTH},$com->{HASH});
	unless ($sth->rows) {
		$GLOBAL::SQL->do("DELETE FROM ".$this->{Fname}."_congi WHERE `HASH`=?;",undef,$com->{HASH});	
		$GLOBAL::SQL->do("INSERT INTO ".$this->{Fname}."_membri (`HASH`,`is_auth`,`AUTH`,`present`) VALUES(?,?,?,'0');",undef,$com->{HASH},'1',$com->{AUTH});
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