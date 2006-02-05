<?
include ("lib/lib.php"); # Librerie per creare la connessione MySQL

  $EDIT_OF=$_REQUEST["edit_of"];
  if (strlen($EDIT_OF)==32) {
    $EDIT_OF=pack("H32",$EDIT_OF);
    $edit_val=1;
  }
  if (strlen($ENV[pkey])<120) {
    echo "La chiave pubblica dell'admin non è valida, non posso validare il messaggio\n";
    include ("end.php");
    exit(0);
  }

  CheckSession();

  if (!$_SESSION['sess_auth']) {
    $url = "login.php?SEZID=".$_REQUEST["SEZID"]."&THR_ID=".$_REQUEST["THR_ID"]."&pag=".$_REQUEST["pag"];
    echo "<tr><td><center>".$lang['reply_login']."<br>";
    echo "".$lang['reply_loginred']."</center></td></tr><script language=\"javascript\">setTimeout('delayer()', 1500);\nfunction delayer(){ window.location='$url';}</script>";
    include ("end.php");
    exit(0);
  }

  $query="SELECT PKEY,ORDINE FROM `".mysql_real_escape_string($_ENV["sesname"])."_sez` WHERE `ID`=".mysql_escape_string($_REQUEST["SEZID"]).";";
  $SECTION_DATA = $db->get_row($query);
  if (!$SECTION_DATA) 
  {
  	$std->Error('Sezione non trovata');
    include ("end.php");
    exit(0);
  }
  if ($SECTION_DATA->ORDINE>9000)
    echo "Errore: Sezione non valida\n";
    include ("end.php");
    exit(0);
  }

  // ****** Devo prima reperire l'hash dell'utente
  $query="SELECT `PASSWORD` FROM `".mysql_real_escape_string($_ENV["sesname"])."_localmember` WHERE `HASH`=".???????.";";
  if !($PASSWORD = $db->get_var($query)) {
    echo "Impossibile caricare la chiave privata.<br>\n";
    include ("end.php");
    exit(0);
  }

  if (strlen($PASSWORD)>100) {
    $privForum=$_REQUEST["PrivKey"];
    $privForum or Muori("Per inserire messaggi in questa sezione occore possedere la chiave privata.<br>\n");
    ForumLib::Error("Chiave privata immessa non valida.<br>\n") unless eval {$privForum=ForumLib::ConvPrivateKey($privForum)};
    $privfor=1;
  }

  $NEWMSG["TITLE"]=$_REQUEST["subject"];
  $NEWMSG["SUBTITLE"]=$_REQUEST["desc"];
  $NEWMSG["BODY"]=$_REQUEST["body"];
  $NEWMSG["AUTORE"]=ForumLib::GetAuthorHash();
  $NEWMSG["EDIT_OF"]=$EDIT_OF;
  $NEWMSG["AVATAR"]=$_REQUEST["avatar"]);
  $NEWMSG["FIRMA"]=$_REQUEST["firma"];
  $NEWMSG["DATE"]=ForumLib::_gmtime();
  $NEWMSG["SEZ"]=$_REQUEST["SEZID"];

  if (strlen($NEWMSG["TITLE"])>200 || strlen($NEWMSG["TITLE"])<3) {
    echo "Formato subject non valido";
    include ("end.php");
    exit(0);
  }
  if (strlen($NEWMSG["SUBTITLE"])>250) {
    echo "Formato sotto subject non valido";
    include ("end.php");
    exit(0);
  }
  if (strlen($NEWMSG["BODY"])>50000 || strlen($NEWMSG["BODY"])<2) {
    echo "Formato corpo non valido";
    include ("end.php");
    exit(0);
  }
  if (strlen($NEWMSG["AVATAR"])>255) {
    echo "Formato avatar non valido";
    include ("end.php");
    exit(0);
  }
  if (strlen($NEWMSG["FIRMA"])>255) {
    echo "Formato firma non valido";
    include ("end.php");
    exit(0);
  }

###############
# redirect
###############

  $returnurl = "sezioni.php?SEZID=".$_REQUEST["sezid");
  $returnhtml = "
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
</html>";

######################

$md5 = $ENV["pkey"].$NEWMSG["SEZ"].$NEWMSG["AUTORE"]."1";
if ($edit_val)
  $md5 = $md5.$EDIT_OF;
$md5 = $md5.$NEWMSG["DATE"].$NEWMSG["TITLE"].$NEWMSG["SUBTITLE"].$NEWMSG["BODY"].$NEWMSG["FIRMA"].$NEWMSG["AVATAR"];
$md5 = md5($md5);
/*
my $md5oo=Digest::MD5->new();
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
my $hash=$md5oo->digest;*/
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
    echo $returnhtml;
    include ("end.php");
    exit( 0 );
  }
  else {
    echo "Errore imprevisto nell'aggiunta della modifica\n";
  }
}
else {
  if ($adder->_AddType1($hash,\%NEWMSG)) {
    $adder->Priority($hash,2);
    echo $returnhtml;
    include ("end.php");
    exit( 0 );
  }
  else {
    echo "Errore imprevisto nell'aggiunta del messaggio\n";
  }
}
  include ("end.php");
?>