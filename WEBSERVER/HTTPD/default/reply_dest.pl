chdir ($ENV{"DOCUMENT_ROOT"});
use Itami::forumlib;
use Itami::Adder;
use CGI qw/:standard/;
use Digest::MD5;
use strict;
use Itami::BinDump;
my ($MSG_DATA,$MSG_HASH,%REP_DATA,$EDIT_OF,$edit_val);
$MSG_HASH=pack("H32",param("repof"));
$EDIT_OF=param("edit_of");
if (length($EDIT_OF)==32) {
	$EDIT_OF=pack("H32",$EDIT_OF);
	$edit_val=1 if length($EDIT_OF)==16;
}
print "La chiave pubblica dell'admin non è valida, non posso valida il messaggio\n" if length($ENV{pkey})<120;
ForumLib::Error("Nessuna sessione attiva.Impossibile continuare.<br>\n") unless ForumLib::LoadSession();
ForumLib::Error("Non hai effettuato il login.<br>\n") unless ForumLib::LoadSessionData();
ForumLib::Error("Impossibile caricare la chiave privata.<br>\n") unless ForumLib::GetPrivateKey();
ForumLib::Error("Non hai i permessi per scrivere messaggi\n") unless ForumLib::CanWrite();
ForumLib::Error("Errore: Messaggio Non trovato\n") unless $MSG_DATA=ForumLib::LoadMsg($MSG_HASH);

$REP_DATA{'REP_OF'}=$MSG_HASH;
$REP_DATA{'AUTORE'}=ForumLib::GetAuthorHash();
$REP_DATA{AVATAR}=param("avatar");
$REP_DATA{FIRMA}=param("firma");
$REP_DATA{DATE}=ForumLib::_gmtime();
$REP_DATA{TITLE}=param("title");
$REP_DATA{EDIT_OF}=$EDIT_OF if $edit_val;
$REP_DATA{BODY}=param("body");

ForumLib::Error("Formato subject non valido") if length($REP_DATA{TITLE}) > 200;
ForumLib::Error("Formato corpo non valido") if length($REP_DATA{BODY}) > 50000 || length($REP_DATA{BODY}) <2;
ForumLib::Error("Formato avatar non valido") if length($REP_DATA{AVATAR}) > 255;
ForumLib::Error("Formato firma non valido") if length($REP_DATA{FIRMA}) > 255;

my $md5oo=Digest::MD5->new();
$md5oo->add($ENV{pkey});
$md5oo->add($REP_DATA{'REP_OF'});
$md5oo->add($REP_DATA{'AUTORE'});
$md5oo->add("2");
$md5oo->add($REP_DATA{EDIT_OF}) if $edit_val;
$md5oo->add($REP_DATA{AVATAR});
$md5oo->add($REP_DATA{FIRMA});
$md5oo->add($REP_DATA{DATE});
$md5oo->add($REP_DATA{TITLE});
$md5oo->add($REP_DATA{BODY});

my $hash=$md5oo->digest;

###############
# redirect
###############

	my $returnth = param("repof");
	my $returnsezid = param("sezid");
	my $returnport = "20585";
	my $returnurl = "showmsg.php?SEZID=$returnsezid&THR_ID=$returnth&pag=last#end_page";
	my $returnhtml = "
	<html>
	<head>
	<title>Attendi...</title>
	<meta http-equiv='refresh' content='2; url=$returnurl' />
	<script type=\"text/javascript\"> </script>
	<style type='text/css'>
	html { overflow-x: auto; }
	BODY { font-family: Verdana, Tahoma, Arial, sans-serif;font-size: 11px;margin: 0px;padding: 0px;text-align: center;color: #000;background-color: #FFFFFF; }
	.tablefill { padding: 6px;background-color: #F5F9FD;border: 1px solid #345487; }
	</style>
	</head>
	<body>
	<table width='100%' height='85%' align='center'>
	<tr>
	  <td valign='middle'>
		  <table align='center' cellpadding=\"4\" class=\"tablefill\">
		  <tr>
			<td width=\"100%\" align=\"center\">
			  Messaggio inserito con successo<br /><br />
			  Attendi mentre viene caricata la pagina...<br /><br />
		    </td>
		  </tr>
		</table>
	  </td>
	</tr>
	</table>
	</body>
</html>
	
	";
	
######################	


$REP_DATA{SIGN}=ForumLib::RsaSign($hash);
my $adder=Adder->new(ForumLib::SQL(), $ENV{sesname});
if ($edit_val) {
	if($adder->_AddType2_edit($hash,\%REP_DATA)) {
		#print "modifica inviata con successo\n";
		$adder->Priority($hash,1);
		print $returnhtml;
		exit( 0 ); 	
	} else {
		print "Errore imprevisto in aggiunta alla modifica\n";	
	}
} else {
	$adder->_AddType2($hash,\%REP_DATA);
	$adder->Priority($hash,6);
        print $returnhtml;
	exit( 0 ); 

}



#ForumLib::Do("INSERT INTO ".$ENV{sesname}."_congi (`HASH`,`TYPE`,`WRITE_DATE`,`LAST_SEND`) VALUES(?,?,?,?);",undef,$hash,2,$REP_DATA{date},-1);
#ForumLib::Do("INSERT INTO ".$ENV{sesname}."_reply (`HASH`,`REP_OF`,`AUTORE`,`EDIT_OF`,`DATE`,`FIRMA`,`AVATAR`,`TITLE`,`BODY`,`SIGN`)"
#			 ." VALUES(?,?,?,?,?,?,?,?,?,?);",undef,$hash,$REP_DATA{'REP_OF'},$REP_DATA{'AUTORE'},$hash,$REP_DATA{date},
#			$REP_DATA{firma} || "",$REP_DATA{avatar} || "",$REP_DATA{title},$REP_DATA{body},$sign);
#my $hexxa=unpack("H*",$hash);
#print "$ENV{pkey}-$REP_DATA{'REP_OF'}-$REP_DATA{'AUTORE'}-2-$REP_DATA{avatar}-$REP_DATA{firma}-$REP_DATA{date}-$REP_DATA{title}-$REP_DATA{body}\n$hash\n$hexxa\n";

