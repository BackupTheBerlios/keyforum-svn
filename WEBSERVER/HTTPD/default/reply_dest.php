<?php

include("testa.php");

$MSG_HASH=pack("H32",$_REQUEST['repof']);
$SNAME=$_ENV['sesname'];

if ( strlen($_REQUEST['edit_of'])==32 ) {
    $EDIT_OF=pack("H32",$_REQUEST['edit_of']);
    if ( strlen($EDIT_OF) == 16 )
        $edit_val=1;
}

if (!$GLOBALS['sess_auth']) die ("Non hai effettuato il login \n");

$IDENTIFICATORE=md5($GLOBALS['sess_password'].$GLOBALS['sess_nick']); // = identificatore dell'utente nella tabella localmember. easadecimale
$KEY_DECRYPT=md5($GLOBALS['sess_nick'].$GLOBALS['sess_password'],TRUE);// = password per decriptare la chiave privata in localmember (16byte)
  $query="SELECT PASSWORD FROM ".$SNAME."_localmember WHERE HASH='".$IDENTIFICATORE."';";
  $risultato = mysql_query($query) or die ("Query non valida: " . mysql_error());
$riga = mysql_fetch_assoc($risultato) or die("utente non trovato\n");
$privkey=base64_decode($riga[PASSWORD]);
/* Vedi se l'utente è bannato, CanWrite in forumlib.pm
$querysql="SELECT ban FROM " . $_ENV['sesname'] . "_membri WHERE HASH='" .
*/
$PKEY=$std->getpkey($SNAME);
$req[FUNC][Base642Dec]=$PKEY;
$req[FUNC][BlowDump2var][Key]=$KEY_DECRYPT;
$req[FUNC][BlowDump2var][Data]=$privkey;
$core=new CoreSock;
if (!$core->Send($req)) die("Impossibile comunicare con il core.\n<br>");
if (!$risp=$core->Read()) die ("Il core non ha risposto nel tempo indicato.\n");
$PKEY=$risp[FUNC][Base642Dec];
if ( strlen($PKEY) < 120 ) die("La chiave pubblica dell'admin non è valida, non posso validare il messaggio\n");
if (strlen($risp[FUNC][BlowDump2var][hash])!=16) die ("Errore nel trattamento dei tuoi dati privati\n");

$querysql="SELECT count(*) FROM " . $SNAME . "_newmsg WHERE HASH='".$MSG_HASH."'";
$sqlresult=mysql_query($querysql);
if (!mysql_num_rows($sqlresult)) die("<br>Errore: Messaggio Non trovato!\n");
$mreq['FORUM']['ADDMSG'];
$mreq['FORUM']['ADDMSG']['FDEST']=sha1($PKEY,TRUE);
$mreq['FORUM']['ADDMSG']['REP_OF']=$MSG_HASH;
$mreq['FORUM']['ADDMSG']['AUTORE']=$risp['FUNC']['BlowDump2var']['hash'];
$mreq['FORUM']['ADDMSG']['AVATAR']=$_REQUEST['avatar'];
$mreq['FORUM']['ADDMSG']['FIRMA']=$_REQUEST['firma'];
$mreq['FORUM']['ADDMSG']['TYPE']='2';
$mreq['FORUM']['ADDMSG']['DATE']=$risp['CORE']['INFO']['GMT_TIME'];
$mreq['FORUM']['ADDMSG']['TITLE']=$_REQUEST['title'];


$mreq['FORUM']['ADDMSG']['BODY']=$_REQUEST['body'];
$MD5_MSG=md5($PKEY.$MSG_HASH.$mreq[FORUM][ADDMSG]['AUTORE']."2".$EDIT_OF
        .$_REQUEST['avatar'].$_REQUEST['firma'].$mreq[FORUM][ADDMSG]['DATE'].$_REQUEST['title'].$_REQUEST['body'],TRUE);
        if ( $edit_val )
    $mreq['FORUM']['ADDMSG']['EDIT_OF']=$EDIT_OF; else $mreq['FORUM']['ADDMSG']['EDIT_OF']=$MD5_MSG;
$mreq['FORUM']['ADDMSG']['MD5']=$MD5_MSG;
$nreq['RSA']['FIRMA'][0]['md5']=$MD5_MSG;
$nreq['RSA']['FIRMA'][0]['priv_key']=$KEY_DECRYPT;
$nreq['RSA']['FIRMA'][0]['priv_pwd']=$privkey;
if (!$core->Send($nreq)) die("Impossibile comunicare con il core.\n<br>");
if (!$risp=$core->Read()) die ("Il core non ha risposto nel tempo indicato.\n");
$mreq['FORUM']['ADDMSG']['SIGN']=$risp[RSA][FIRMA][$MD5_MSG];
#$mreq[FORUM][ADDMSG]=$REP_DATA;

$core->Send($mreq);
$risp=$core->Read();
print $risp['FORUM']['ADDMSG'];
print "alfa\n";
?>