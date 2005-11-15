chdir ($ENV{"DOCUMENT_ROOT"});
use Itami::forumlib;
use CGI qw/:standard/;
use Digest::MD5;
use Itami::Adder;
use Itami::BinDump;
use strict;
my ($SECTION_DATA,%NEWMSG,$EDIT_OF, $edit_val,$privForum,$privfor);
$EDIT_OF=param("edit_of");
if (length($EDIT_OF)==32) {
	$EDIT_OF=pack("H32",$EDIT_OF);
	$edit_val=1;
}
print "La chiave pubblica dell'admin non è valida, non posso validare il messaggio\n" if length($ENV{pkey})<120;
ForumLib::Error("Nessuna sessione attiva.Impossibile continuare.<br>\n") unless ForumLib::LoadSession();
ForumLib::Error("Non hai effettuato il login.<br>\n") unless ForumLib::LoadSessionData();
ForumLib::Error("Impossibile caricare la chiave privata.<br>\n") unless ForumLib::GetPrivateKey();
ForumLib::Error("Errore: Sezione non valida\n") unless $SECTION_DATA=ForumLib::GetSezName(param("sezid"));
ForumLib::Error("Non hai i permessi per scrivere messaggi\n") unless ForumLib::CanWrite();
if (length($SECTION_DATA->{PKEY})>100) {
	$privForum=param("PrivKey");
	ForumLib::Error("Per inserire messaggi in questa sezione occore possedere la chiave privata.<br>\n") unless $privForum;
	ForumLib::Error("Chiave privata immessa non valida.<br>\n") unless eval {$privForum=ForumLib::ConvPrivateKey($privForum)};
	$privfor=1;
}
$NEWMSG{TITLE}=param("subject");
$NEWMSG{SUBTITLE}=param("desc");
$NEWMSG{BODY}=param("body");
$NEWMSG{AUTORE}=ForumLib::GetAuthorHash();
$NEWMSG{EDIT_OF}=$EDIT_OF;
$NEWMSG{AVATAR}=param("avatar");
$NEWMSG{FIRMA}=param("firma");
$NEWMSG{DATE}=ForumLib::_gmtime();
$NEWMSG{SEZ}=$SECTION_DATA->{ID};

ForumLib::Error("Formato subject non valido") if length($NEWMSG{TITLE}) > 200 || length($NEWMSG{TITLE})<3;
ForumLib::Error("Formato sotto subject non valido") if length($NEWMSG{SUBTITLE}) > 250;
ForumLib::Error("Formato sotto corpo non valido") if length($NEWMSG{BODY}) > 50000 || length($NEWMSG{BODY}) <2;
ForumLib::Error("Formato sotto avatar non valido") if length($NEWMSG{AVATAR}) > 255;
ForumLib::Error("Formato sotto firma non valido") if length($NEWMSG{FIRMA}) > 255;
my $md5oo=Digest::MD5->new();


###############
# redirect
###############

	my $returnth = param("sezid");
	my $returnport = "20585";
	my $returnurl = "sezioni.php?SEZID=$returnth";
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



$md5oo->add($ENV{pkey});
$md5oo->add($NEWMSG{SEZ});
$md5oo->add($NEWMSG{AUTORE});
$md5oo->add("1");
$md5oo->add($EDIT_OF) if $edit_val;
$md5oo->add($NEWMSG{DATE});
$md5oo->add($NEWMSG{TITLE});
$md5oo->add($NEWMSG{SUBTITLE});
$md5oo->add($NEWMSG{BODY});
$md5oo->add($NEWMSG{FIRMA});
$md5oo->add($NEWMSG{AVATAR});
my $hash=$md5oo->digest;
my $adder=Adder->new(ForumLib::SQL(), $ENV{sesname});
$NEWMSG{SIGN}=ForumLib::RsaSign($hash);
if ($privfor) {
	$NEWMSG{FOR_SIGN}=ForumLib::RsaSign($hash,$privForum);
	my $formpub=ForumLib::GenPublicKey(ConvData::Bin2Dec($SECTION_DATA->{PKEY}));
	ForumLib::Error("La chiave privata del forum immessa non corrisponde con quella pubblica del forum.") unless ForumLib::RsaCheck($hash,$NEWMSG{FOR_SIGN},$formpub);
}

if ($edit_val) {
	if ($adder->_AddType1_edit($hash,\%NEWMSG)) {
		$adder->Priority($hash,3);
		print $returnhtml;
		exit( 0 ); 	
	} else {
		print "Errore imprevisto nell'aggiunta della modifica\n";	
	}
} else {
	if ($adder->_AddType1($hash,\%NEWMSG)) {
		$adder->Priority($hash,2);
		print $returnhtml;
		exit( 0 ); 	
	} else {
		print "Errore imprevisto nell'aggiunta del messaggio\n";	
	}
}

#ForumLib::Do("INSERT INTO ".$ENV{sesname}."_congi (`HASH`,`TYPE`,`WRITE_DATE`,`LAST_SEND`) VALUES(?,?,?,?);",undef,$hash,1,$NEWMSG{date},-1);
#ForumLib::Do("INSERT INTO ".$ENV{sesname}."_newmsg (`HASH`,`SEZ`,`AUTORE`,`EDIT_OF`,`DATE`,`TITLE`,`SUBTITLE`,`BODY`,`FIRMA`,`AVATAR`,`SIGN`)".
#			 " VALUES(?,?,?,?,?,?,?,?,?,?,?);",undef,$hash,$NEWMSG{sezid},$NEWMSG{author},$NEWMSG{edit_of},$NEWMSG{date},
#			 $NEWMSG{subject},$NEWMSG{desc},$NEWMSG{body},$NEWMSG{firma},$NEWMSG{avatar},$sign);
#ForumLib::Do("INSERT INTO ".$ENV{sesname}."_msghe (`HASH`,`DATE`,`AUTORE`,`last_reply_author`,`last_reply_time`) VALUES(?,?,?,?,?);",undef,
#			 $hash,$NEWMSG{date},$NEWMSG{author},$NEWMSG{author},$NEWMSG{date});
#ForumLib::Do("UPDATE ".$ENV{sesname}."_sez SET THR_NUM=THR_NUM+1 WHERE ID=?;",undef,$NEWMSG{sezid});
#print "sezione id:".$NEWMSG{sezid}."<br>\n";
#print "ciao ".$SECTION_DATA->{ID}."<br>\n";