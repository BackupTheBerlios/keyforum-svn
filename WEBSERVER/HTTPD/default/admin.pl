chdir ($ENV{"DOCUMENT_ROOT"});
push(@INC,"..");
use strict;
use Itami::forumlib;
use Itami::BinDump;
use CGI qw/:standard/;
use MIME::Base64;
use Itami::Adder;
use admin;

# Dichiaro le variabili principali
my ($params,$idtemp,$command,$code,$warning,$private);

# Carico l'ID della sessione
ForumLib::Error("Sessione non registrata o scaduta. Attiva i COOKIE.\n") unless ForumLib::LoadSession();
$idtemp="ADMINCOMM".ForumLib::session();

# Carico in memoria tutte le varibili registrate
$command={} unless $command=BinDump::MainDeDump(MIME::Base64::decode_base64(ForumLib::LoadTempData($idtemp)));

$params = CGI::Vars();	# Metto le variabili ricevute tramite GET o POST in un HASH

SWITCH:
{
	clear(),last SWITCH if $params->{action} eq "clear";
	EditSez(),last SWITCH if $params->{action} eq "EditSez";
	drop(),last SWITCH if $params->{action} eq "drop";
	AuthMem(),last SWITCH if $params->{action} eq "AuthMem";
	ConfTable(),last SWITCH if $params->{action} eq "ConfTable";
	Send(),last SWITCH if $params->{action} eq "Send";
}


# Converto la nuova variabile con i comandi in una struttura binaria
$code=MIME::Base64::encode_base64(BinDump::MainDump($command,0,1),''); 
print ForumLib::Head();
ShowList();

print "ATTENZIONE:<bR><font color=red><b>$warning</b><bR></font>" if $warning;


print qq~<br><br>
<form method=post action=admin.pl>
<input type=hidden name=action value=clear>
<input type=hidden name=PHPSESSID value="~.ForumLib::session().qq~">
<center><input type=submit value="cancella tutti i valori"></center></form>
<br><br>
<table border=0 cellspacing=1 cellpadding=1 bordercolor=white width=85% align=center>
<tr>
	<td colspan=2 class=row2 height=25 align=center><b>Aggiungi/Modifica sezione</b></td>
</tR>
<tr>
	<td colspan=2 class=row1 height=25 align=left><b>Ricorda che:</b><bR>
	1) I moderatori possono essere solo aggiunti agli altri e non eliminati.<br>
	2) La chiave pubblica di un forum è consigliabile metterla solo al momento della creazione del forum<br>
	3) Gli altri valori come nome e descrizione è bene ripeterli e reimpostarli ogni volta anche se avete intenzione di cambiare solo un valore.<bR>
	4) I moderatori aggiunti devono essere separati dal '%', devi inserire l'HASH esadecimale del membro.<br>
	5) L'autoflush specifica un tempo in ore. I messaggi più vecchi del tempo specificato vengono cancellati. Se il tempo specificato è ZERO i messaggi permangono.<br><br>
	<li> Cosa è la chiave pubblica di un forum?<br>
	Proteggendo un forum con una chiave pubblica è possibile scrivere nuovi messaggi solo colore che posseggono la chiave privata.
	Può essere utile in molte situazioni, come ad esempio se si intende creare un home con le ultime notizie importanti.
	Anche se solo chi possiede la chiave privata può scrivere tutti i messaggi, tutti i membri iscritti possono rispondere.<bR>
	Puoi generare nuovi chiavi pubblica da questa pagina <a href="KeyGen.pl" target=_new>KeyGen.pl</a>
	<form method=post action=admin.pl>
	<input type=hidden name=PHPSESSID value="~.ForumLib::session().qq~">
	<input type=hidden name=action value=EditSez>
	</td>
</tr>
<tr>
	<td class=row3><b>ID Numero sezione:</b></tD>
	<td class=row4><input type=text name=SEZID size=10></tD>
</tR>
<tr>
	<td class=row3><b>Nome Sezione:</b></tD>
	<td class=row4><input type=text name=SEZ_NAME size=50></tD>
</tR>
<tr>
	<td class=row3><b>Descrizione Sezione:</b></tD>
	<td class=row4><input type=text name=SEZ_DESC size=50></tD>
</tR>
<tr>
	<td class=row3><b>Ordine (numerico):</b></tD>
	<td class=row4><input type=text name=ORDINE size=50></tD>
</tR>
<tr>
	<td class=row3><b>Figlio di... (numero sezione):</b></tD>
	<td class=row4><input type=text name=FIGLIO size=50></tD>
</tR>
<tr>
	<td class=row3><b>Chiave Pubblica:</b></tD>
	<td class=row4><input type=text name=PKEY size=60></tD>
</tR>
<tr>
	<td class=row3><b>Lista Moderatori:</b></tD>
	<td class=row4><input type=text name=MOD size=40></tD>
</tR>
<tR>
	<tD class=row3><b>Solo autorizzati dall'admin:</b></td>
	<td class=row4><INPUT type="CHECKBOX" name="ONLY_AUTH" value="1" checked></td>
</tr>
<tR>
	<tD class=row3><b>Autoflush (in ore):</b></td>
	<td class=row4><input type=text name=AUTOFLUSH value=0></td>
</tr>
<tr>
	<td colspan=2 class=row2 height=25 align=center><input type=submit value="Aggiungi Comando"></form></td>
</tR>
<tr>
	<td colspan=2 class=row2 height=25 align=center><b>Convalida Membro</b></td>
</tR>
<tr>
	<td colspan=2 class=row1 height=25 align=left><b>Ricorda che:</b><bR>
	1) I membri devono essere HASH esadecimale a 32byte.<bR>
	2) La loro firma RSA sarà assegnata quando invierai l'intero pacco di comandi.
	<form method=post action=admin.pl>
	<input type=hidden name=PHPSESSID value="~.ForumLib::session().qq~">
	<input type=hidden name=action value=AuthMem>
	</td>
</tr>
<tr>
	<td class=row3><b>Hash membro:</b></tD>
	<td class=row4><input type=text name=HASH size=50></tD>
</tR>
<tr>
	<td colspan=2 class=row2 height=25 align=center><input type=submit value="Aggiungi Comando"></form></td>
</tR>
<tr>
	<td colspan=2 class=row2 height=25 align=center><b>Modifica la tabella di configurazione</b></td>
</tR>
<tr>
	<td class=row3><b>GRUPPO:</b></tD>
	<td class=row4>	<form method=post action=admin.pl>
	<input type=hidden name=PHPSESSID value="~.ForumLib::session().qq~">
	<input type=hidden name=action value=ConfTable><input type=text name=ALFA size=50></tD>
</tR>
<tr>
	<td class=row3><b>CHIAVE:</b></tD>
	<td class=row4><input type=text name=BETA size=50></tD>
</tR>
<tr>
	<td class=row3><b>SOTTO CHIAVE:</b></tD>
	<td class=row4><input type=text name=GAMMA size=50></tD>
</tR>
<tr>
	<td class=row3><b>VALORE:</b></tD>
	<td class=row4><input type=text name=DELTA size=50></tD>
</tR>
<tr>
	<td colspan=2 class=row2 height=25 align=center><input type=submit value="Aggiungi Comando"></form></td>
</tR>
<tr>
	<td colspan=2 class=row2 height=25 align=center><b>Invia tutti i comandi</b></td>
</tR>
<tr>
	<td colspan=2 class=row1 height=25 align=left><b>Ricorda che:</b><bR>
	1) Inserisci nel textbox la chiave privata del forum e invia tutti i comandi.<br>
	2) Se la chiave è errata i comandi non verranno inviati e ne eseguiti.<bR>
	3) Il titolo comandi deve essere una breve descrizione delle azioni compiute (non obbligatorio).
	<form method=post action=admin.pl>
	<input type=hidden name=PHPSESSID value="~.ForumLib::session().qq~">
	<input type=hidden name=action value=Send>
	</td>
</tr>
<tr>
	<td class=row3 valign=center><b>Titolo Comandi:</b></tD>
	<td class=row4><input type=text name=title size=40></tD>
</tR>
<tr>
	<td class=row3 valign=center><b>Chiave Privata RSA:</b></tD>
	<td class=row4><textarea cols=35 rows=5 name=Private></textarea></tD>
</tR>
<tr>
	<td colspan=2 class=row2 height=25 align=center><input type=submit value="Spedisci i comandi"></form></td>
</tR>
</table>
</body>
</html>
~;
ForumLib::UpdateTempVal($idtemp,$code,3600);
ForumLib::DelTempData();



sub clear {
	$command={};
	
}
sub ShowList {
	return undef unless scalar(keys(%$command));
	print "<table border=0 cellspacing=1 cellpadding=1 bordercolor=white width=85% align=center>\n";
	my $index=0;
	while (my ($key,$value)=each %$command) {
		print "<tr>
	<td colspan=2 class=row2 align=center><b>$key</b>
	<form method=post action=admin.pl>
	<input type=hidden name=action value=drop>
	<input type=hidden name=tipo value='$key'>
	<input type=hidden name=PHPSESSID value='".ForumLib::session()."'></td>
</tR>\n";
		foreach my $buf (@$value) {
			print "<tr>\n\t<td class=row3 valign=top><input name='REM$index' type='checkbox' value='1'></td>\n";
			print "\t<td><table width=100% cellspacing=1 cellpadding=1 bordercolor=white border=0>\n";
			print "\t<tR>\n\t\t<td class=row1 align=center><b>Chiave</b></td>\n\t\t<td class=row2 align=center><b>Valore</b></tD>\n\t</tR>\n";
			while (my ($subkey,$subvalue)=each %$buf) {
				print "\t<tR>\n\t\t<td class=row1>$subkey</td>\n\t\t<td class=row2>$subvalue</tD>\n\t</tR>\n";
			}
			$index++;
			print "\t</table>\n\t</td>\n</tr>\n";
		}
		print "<tr>
	<td colspan=2 class=row2 align=center><input type=submit value=Cancella></form></td>
</tR>\n";
	}
	print "</table>";

}
sub ConfTable {
	my ($alfa,$beta,$gamma,$delta)=@{$params}{'ALFA','BETA','GAMMA','DELTA'};
	$command->{ConfTable}=[] unless exists $command->{ConfTable};
	my $nhash={};
	@{$nhash}{'a','b','c','d'}=($alfa,$beta,$gamma,$delta);
	push(@{$command->{ConfTable}},$nhash);
}
sub EditSez {
	my ($id,$name,$desc,$mod,$pkey,$only_auth,$autoflush,$ordine, $figlio)=@{$params}{'SEZID','SEZ_NAME','SEZ_DESC','MOD','PKEY','ONLY_AUTH','AUTOFLUSH','ORDINE','FIGLIO'};
	return Warning("ID sezione non specificato") if $id eq "";
	return Warning("ID sezione contiene caratteri non numerici.") if $id=~ m/\D/;
	return Warning("L'ID della sezione non è valido. Solo numeri da zero a 1000000") if $id<0 || $id>1000000;
	return Warning("Hai superato la lunghezza massima di 250 per il nome sezione.") if length($name)>250;
	return Warning("Non hai specificato il nome della sezione.") if length($name)<1;
	return Warning("La chiave pubblica del forum può contenere solo numeri.") if $pkey=~ /\D/;
	return Warning("L'autoflush deve essere solo un numero!") if $autoflush=~ /\D/;
	return Warning("L'ordine delle sezioni deve essere un numero!") if $ordine=~ /\D/;
	return Warning("Il figlio deve essere un numero!") if $figlio=~ /\D/;
	my @mod=split(/%/,$mod);
	my @newmod;
	foreach my $buf (@mod) {
		next if length($buf)==0;
		return Warning("Un moderatore ha una lunghezza dell'HASH differente da 32 byte") if length($buf) !=32;
		return Warning("Un moderatore ha un HASH con caratteri errati") if $buf=~ /[^A-F0-9]/i;
		push(@newmod,$buf);
	}
	$mod=join("%",@newmod);
	$command->{EditSez}=[] if ref($command->{EditSez}) ne "ARRAY";
	my $sez={};
	$mod.='%' if $mod;
	($only_auth) ? ($only_auth=2) : ($only_auth=1);
	$autoflush=0 unless $autoflush;
	@{$sez}{'SEZID','SEZ_NAME','SEZ_DESC','MOD','PKEY','ONLY_AUTH','AUTOFLUSH','ORDINE','FIGLIO'}=($id,$name,$desc,$mod,$pkey,$only_auth,$autoflush,$ordine,$figlio);
	push(@{$command->{EditSez}},$sez);
	
	
}
sub drop {
	my $ind=$params->{tipo};
	return Warning("Si tenta di cancellare valori in una sezione che non è presenta nella lista dei comandi. =>\"$ind\"") unless exists $command->{$ind};	
	my $ind=$command->{$ind};
	return Warning("Si tenta di cancellare in un tipo con nessun elemento") unless scalar(@$ind);
	my $num;
	while (my ($key, $value)=each %$params) {
		if ($key=~ m/^REM(\d+)/) {
			$num=$1;
			if (scalar(@$ind)==1) {
				shift @$ind;
			} elsif ($num<scalar(@$ind)) {
				$ind->[$num]=pop @$ind;
			} else {
				pop @$ind;	
			}
		}
	}
}
sub Warning {
	$warning.="<li>".$_[0]."<br>\n";
	return $_[1];
}
sub AuthMem {
	my $memhash=$params->{HASH};
	return Warning("L'hash dei membri deve essere di  32 byte.") if length($memhash) !=32;
	return Warning("Deve essere in formato esadecimale l'HASH membro.") if $memhash=~ /[^a-fA-F0-9]/i;
	my $mem={};
	$mem->{HASH}=$memhash;
	$command->{AuthMem}=[] if ref($command->{AuthMem}) ne "ARRAY";
	push(@{$command->{AuthMem}},$mem);
}
sub Send {
	return Warning("Lunghezza del titolo deve essere massimo di 150 caratteri") if length($params->{title})>150;
	return Warning("Provi ad inviare un messaggio senza comandi all'interno, impossibile continuare") unless scalar(keys(%$command));
	$private=ForumLib::ConvPrivateKey($params->{Private}) or ForumLib::Error("Errore nell'apertura della chaive privata.\n");	
	LoadModules("Crypt::RSA");
	LoadModules("Digest::MD5");
	# Autentifico i membri aggiunti che necessitavano della chiave privata dell'admin
	ConvAuthSign($command->{AuthMem}) if exists $command->{AuthMem};
	# Converto i comandi in una stringa binaria e poi la riconverto in base64
	$code=MIME::Base64::encode_base64(BinDump::MainDump($command,0,1),''); 
	my %MSG;
	# Inserisco tutti i dati in un HASH
	$MSG{'DATE'}=ForumLib::_gmtime() || return(Warning("Errore nel settaggio della data"));
	$MSG{'TITLE'}=$params->{title};
	$MSG{'COMMAND'}=$code;
	# Genere l'HASH md5 del messaggio
	my $md5=Digest::MD5::md5($ENV{PKEY}.$MSG{'DATE'}.$MSG{'TITLE'}.$MSG{'COMMAND'});
	# genero la firma elettronica del realtivo messaggio
	$MSG{'SIGN'}=ForumLib::RsaSign($md5,$private);
	# Carico la chiave pubblica del forum e testo se è compatibile con quella privata
	my $public_admin=ForumLib::GenPublicKey($ENV{PKEY}) || return Warning ("Impossibile caricare la chiave pubblica del forum.");
	return Warning("La chiave privata immessa non corrispondente con la chiave pubblica del forum.") unless ForumLib::RsaCheck($md5,$MSG{'SIGN'},$public_admin);
	# Eseguo i comandi immessi dall'admin
	my $esec=admin->new(ForumLib::SQL(),$ENV{sesname});
	$esec->execute($code,$MSG{'DATE'});
	# Aggiungo i comani al database
	my $adder=Adder->new(ForumLib::SQL(), $ENV{sesname});
	$adder->_AddType3($md5,\%MSG,"-3");
	$adder->Priority($md5,0);
	Warning("Messaggio Admin aggiunto correttamente. I comandi sono stati immediatamente eseguiti su questo database.");
}
sub LoadModules {
	eval "use ".$_[0].";";
	Error("Errore nel caricamento di un modulo:".$@) if $@;
}
sub ConvAuthSign {
	my $ref=shift;
	return undef if ref($ref) ne "ARRAY";
	foreach my $buf (@$ref) {
		next if ref($buf) ne "HASH";
		next if length($buf->{HASH}) != 32;
		$buf->{HASH}=pack("H32",$buf->{HASH});
		$buf->{AUTH}=ForumLib::RsaSign($buf->{HASH},$private);
	}
}