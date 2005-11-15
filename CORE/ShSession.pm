package ShSession;

use strict;
require ShareDB;
require "versione.pm";
use Itami::Cycle;
require "GestIp.pm";
require "PerlScript.pm";
my (%ItemSubscribe, %Gate, %ItemSend, %Rule, %IP,$Sender);

my $brcast=Cycle->new(20);
my $ipwebsite=Cycle->new(7200);
PerlScript::RegisterVar("ShSession::Gate",\%Gate);
PerlScript::RegisterVar("ShSession::ItemSubscribe",\%ItemSubscribe);
sub Check {
	my $ctcp=shift;
	if ($brcast->check) {
		my $gate;
		#print "invio hash random\n";
		foreach $gate (values(%Gate)) {
			$gate->SendRandomHash(50); 	# Invia 50 hash random alle persone iscritte
		}
		BigAutoFlush();
	}
	my $numitem=scalar(keys(%ItemSubscribe));
	my $numgate=scalar(keys(%Gate));
	if ($numitem<$numgate*5 && $numitem<18) {
		foreach my $forums (keys(%IP)) {
			$IP{$forums}->Connect($ctcp) if $Gate{$forums}->Iscritti<4;
		}
	}
	return undef unless $ipwebsite->check;
	foreach my $ipogg (values(%IP)) {
		$ipogg->iamlive();
	}
}
sub retItemSubscribe {
	return \%ItemSubscribe;	
}
sub Declare {
	$Sender=shift;	
}
sub AddItem {
	my ($ogg,$sock)=@_;
	return undef if GestIP::JustConn($sock->peerhost);
	return undef if exists $ItemSubscribe{$ogg};
	return undef if scalar(keys(%ItemSubscribe))>25;
	GestIP::Connesso($sock->peerhost);
	kfdebug::scrivi(9,2,7,fileno($sock),$sock->peerhost); # Connesso alla board con successo.
	$ItemSubscribe{$ogg}={};
	$ItemSubscribe{$ogg}->{SuoJoin}={};
	$ItemSubscribe{$ogg}->{DatiClient}={};
	$ItemSend{$ogg}={};
	Hello($ogg); # Aggiunge ai dati da spedire le info del mio client
	JoinInto($ogg); # Aggiunge ai dati da spedire la lista dei Forum ai quali voglio entrare
	IPrequest($ogg); # Richiedo la lista di IP solo delle board con meno di 150IP.
	$ItemSubscribe{$ogg}->{DatiClient}->{IP}=$sock->peerhost();
	AutoFlush($ogg); # Spedisco i dati
	return 1;
}
sub DeleteItem {
	my ($ogg,$sock)=@_;
	my $buf;
	foreach $buf (keys(%{$ItemSubscribe{$ogg}->{SuoJoin}})) {
		#print "SHARE SESSION: rimuovo $ogg da $buf\n";
		$Gate{$buf}->RemoveItem($ogg) if exists $Gate{$buf};
	}
	delete $ItemSend{$ogg};
	delete $ItemSubscribe{$ogg};
	GestIP::Disconnesso($sock->peerhost);
	#return undef unless exists $this->{Item}->{$ogg};
	#return delete ${$this->{Item}}->{$ogg};
}
sub CheckGate {
	return $Gate{$_[0]} if exists $Gate{$_[0]};
}
sub AddGate {
	my ($name, $gate,$rule,$sql,$config)=@_;	
	return undef if exists $Gate{$name};
	$Gate{$name}=$gate;
	$Rule{$name}=$rule;
	$gate->Declare(\&Sender);
	$IP{$name}=GestIP->new(unpack("H*",$name),$sql,$config->{SOURCER}->{DEFAULT},$config->{TCP}->{PORTA});
	return 1;
}
sub RecvData {
	my ($ogg, $hashref, $sock)=@_;
	#print "ricevo da $sock\n";
	return undef if ref($hashref) ne "HASH";
	my ($key, $value,$AddedRows, $ReqRows);
	while (my ($gate, $data)=each(%$hashref)) {
		#print $gate."-".$data."\n";
	}
	my ($BroCast)=0;
	ReadHello($ogg,$hashref->{Hello},$sock) if exists $hashref->{Hello};
	ReadJoinInto($ogg, $hashref->{JoinInto},$sock->peerhost) if exists $hashref->{JoinInto};
	ReadJoined($ogg, $hashref->{Joined},$sock) if exists $hashref->{Joined};
	ReadIPrequest($ogg, $hashref->{IPrequest}) if exists $hashref->{IPrequest};
	ReadIpList($ogg, $hashref->{IpList}) if exists $hashref->{IpList};
	foreach $key (keys(%$hashref)) {
		$value=$hashref->{$key};
		SWITCH: {
			last SWITCH unless exists $Gate{$key};
			last SWITCH unless $Gate{$key}->RecvData($ogg,$value);
			last SWITCH if ref($value->{ROWS}) ne "HASH";
			kfdebug::scrivi(15,1,10,scalar(keys(%{$value->{ROWS}})),$sock->peerhost);  # X mi invia Y messaggi
			($AddedRows, $ReqRows)=$Rule{$key}->AddRows($value->{ROWS});
			kfdebug::scrivi(16,1,11,undef,$sock->peerhost);  # X mi invia Y messaggi
			delete $value->{ROWS};	# Cancello e libero spazio
			$Gate{$key}->RowReqDest($ogg, $ReqRows) if ref($ReqRows) eq "ARRAY" && scalar(@$ReqRows);
			$BroCast||=$Gate{$key}->OffertHashBrCa($AddedRows);
			
		}
		delete $hashref->{$key};
	}
	$value='';

	($BroCast) ? (&BigAutoFlush) : (AutoFlush($ogg)); # Inivio i dati se presenti nel buffer

}


#########################
# Spedisce i dati
#########################

sub AutoFlush {
	my $dest=shift;
	return undef unless exists $ItemSubscribe{$dest};
	return undef if scalar(keys(%{$ItemSend{$dest}}))==0; # Se non ci sono dati da spedire esco
	#print "provo a spedire\n";
	#while (my ($gate, $data)=each(%{$ItemSend{$dest}})) {
	#	eval {print $gate."<->".$data." con ".scalar(keys(%$data))." elementi\n";}
	#}
	$Sender->($dest,$ItemSend{$dest});
	$ItemSend{$dest}={};
}
sub BigAutoFlush {
	my ($key, $value);
	#print "provo a spedire a tutti\n";
	while (($key, $value)=each(%ItemSend)) {
		next if scalar(keys(%$value))==0; # Se non ci sono dati da spedire passo al prossimo
		$Sender->($key,$value);
		$ItemSend{$key}={};
	}
}
#############################
# Da leggere
#############################
sub ReadHello {
	my ($ogg, $helloref, $sock)=@_;
	return undef if ref($helloref) ne "HASH";
	$ItemSubscribe{$ogg}->{DatiClient}=$helloref;
	$ItemSubscribe{$ogg}->{DatiClient}->{IP}=$sock->peerhost();
}
sub ReadJoinInto {
	my ($ogg, $JoinIntoRef,$ip)=@_;
	return undef if ref($JoinIntoRef) ne "ARRAY";
	my $buf;
	foreach $buf (@$JoinIntoRef) {
		next unless exists $Gate{$buf};
		$Gate{$buf}->NewSession($ogg);
		$ItemSubscribe{$ogg}->{SuoJoin}->{$buf}=time() unless exists $ItemSubscribe{$ogg}->{SuoJoin}->{$buf};
		$IP{$buf}->Presente($ip,$ItemSubscribe{$ogg}->{DatiClient});
	}
	Joined($ogg);
	return 1;
}
sub ReadJoined {
	my ($ogg, $JoinedRef)=@_;
	return undef if ref($JoinedRef) ne "HASH";
	#print "ricevo Joined\n";
	my $buf;
	$ItemSubscribe{$ogg}->{MioJoin}=$JoinedRef;
	return 1;
}
sub ReadIPrequest {
	my ($ogg, $ipreqRef)=@_;
	return undef if ref($ipreqRef) ne "ARRAY";
	kfdebug::scrivi(16,2,12,$ogg); # Richiede una lista di IP validi
	my ($buf,$listaip, $num)=('',{},0);
	foreach $buf (@$ipreqRef) {
		next if length($buf) != 20;
		next unless exists $IP{$buf};
		$listaip->{$buf}=$IP{$buf}->IpRandList || next;
		$num++;
	}
	AutoFlush($ogg) if exists $ItemSend{$ogg}->{IpList};
	$ItemSend{$ogg}->{IpList}=$listaip if $num>0;
	return 1;
}
sub ReadIpList {
	my ($ogg, $IpListRef)=@_;
	return undef if ref($IpListRef) ne "HASH";
	kfdebug::scrivi(16,2,13,$ogg); # Mi invia una lista di IP validi
	my ($board, $lista,$singolo);
	while (($board, $lista)=each %$IpListRef) {
		next if length($board) != 20;
		next unless exists $IP{$board};
		next if ref($lista) ne "ARRAY";
		next if scalar(@$lista)>50;
		foreach $singolo (@$lista) {
			next if ref($singolo) ne "HASH";
			$IP{$board}->Aggiungi($singolo);
		}
		$IP{$board}->Conteggio;
	}
	
}
#############################
# Quello che deve spedire
#############################
sub Sender {
	my ($name, $subname,$dest,$ref)=@_;
	return undef unless exists $ItemSubscribe{$dest};
	$ItemSend{$dest}->{$name}={} unless exists $ItemSend{$dest}->{$name};
	AutoFlush($dest) if exists $ItemSend{$dest}->{$name}->{$subname};
	$ItemSend{$dest}->{$name}->{$subname}=$ref;
	return 1;
}
sub Joined {
	my $dest=shift;
	return undef unless exists $ItemSubscribe{$dest};
	AutoFlush($dest) if exists $ItemSend{$dest}->{Joined};
	$ItemSend{$dest}->{Joined}={%{$ItemSubscribe{$dest}->{SuoJoin}}};
	return 1;
}
sub IPrequest {
	my $dest=shift;
	return undef unless exists $ItemSubscribe{$dest};
	AutoFlush($dest) if exists $ItemSend{$dest}->{IPrequest};
	my ($board,@board_ip);
	foreach $board (keys(%IP)) {
		next if $IP{$board}->GetNumIp > 150;
		push(@board_ip,$board);
	}
	$ItemSend{$dest}->{IPrequest}=\@board_ip if $#board_ip >-1;
	return 1;
}
sub Hello {
	my $dest=shift;
	return undef unless exists $ItemSubscribe{$dest};
	AutoFlush($dest) if exists $ItemSend{$dest}->{Hello};
	$ItemSend{$dest}->{Hello}={ForumVer::Dumper()};
	return 1;
}
sub JoinInto {
	my $dest=shift;
	return undef unless exists $ItemSubscribe{$dest};
	AutoFlush($dest) if exists $ItemSend{$dest}->{JoinInto};
	$ItemSend{$dest}->{JoinInto}=[];
	my $gate;
	foreach $gate (keys(%Gate)) {
			push(@{$ItemSend{$dest}->{JoinInto}}, $gate);
	}
	return 1;
}
	
1;