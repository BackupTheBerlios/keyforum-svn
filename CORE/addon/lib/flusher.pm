package flusher;
use strict;
use Itami::Cycle;

sub new {
	my ($pname, $DB, $fname)=@_;
	my $this=bless({},$pname);
	$this->{DB}=$DB;
	
	# Aumento la vita delle righe da eliminare appena aviato keyforum
	#$DB->do("UPDATE ".$fname."_purgatorio SET `DELETE_DATE`=`DELETE_DATE`+3600 WHERE `DELETE_DATE`>=?",undef,time);
	#$DB->do("UPDATE ".$fname."_purgatorio SET `DELETE_DATE`=? WHERE `DELETE_DATE`<?",undef,time+3600,time);
	
	# Carico la configurazione della board
	my $sth=$DB->prepare("SELECT `GROUP`, `FKEY`, `SUBKEY`,`VALUE` FROM ".$fname."_conf ORDER BY `GROUP`, `FKEY`,`SUBKEY`;");
	$sth->execute;
	my @tmp;
	my $config={};
	addhashref($config,@tmp) while @tmp=$sth->fetchrow_array;
	$sth->finish;
	$this->{bconf}=$config;
	
	# Carico la configurazione delle sezione per l'autoflush
	$sth=$DB->prepare("SELECT `ID`,`AUTOFLUSH` FROM ".$fname."_sez WHERE `AUTOFLUSH`>0;");
	$sth->execute;
	my ($sezioni,$indice)=([],0);
	@{$sezioni->[$indice++]}=@tmp while @tmp=$sth->fetchrow_array;
	$sth->finish;
	$this->{sez}=$sezioni;
	$this->{ciclo}=Cycle->new(600);
	# Creo le query da eseguire
	$this->{qu}={};
	$this->{qu}->{DeleteMember}=$DB->prepare("DELETE FROM ".$fname."_membri WHERE `DATE`<? AND `is_auth`='0'");
	$this->{qu}->{CanSendMember}=$DB->prepare("UPDATE ".$fname."_membri as membri,".$fname."_congi as congi"
								." SET `congi`.`CAN_SEND`='0' "
								."WHERE `congi`.`TYPE`='4' AND congi.HASH=membri.HASH AND `WRITE_DATE`<? AND `membri`.`is_auth`='0'");
	return $this;
}
sub check {
	my ($this)=@_;
	return undef unless $this->{ciclo}->check;
	
	return undef unless $this->{bconf}->{'CORE'}->{FLUSH_MEMBRI}->{TIME};
	return undef if $this->{bconf}->{'CORE'}->{FLUSH_MEMBRI}->{TIME}<=0;
	my $ora=time()-($this->{bconf}->{'CORE'}->{FLUSH_MEMBRI}->{TIME}*3600);
	$this->{qu}->{CanSendMember}->execute($ora);
	kfdebug::scrivi(11,1,17,
		$this->{qu}->{DeleteMember}->execute($ora));
	
}
# Crea HASH da alcune tabelle di configurazione dell'admin
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


1;