<?php

include ("lib/lib.php"); # Librerie per creare la connessione MySQL

CheckSession();
?>
<?
$blanguage='ita'; // Lingua di visualizzazione
$lang = $std->load_lang('lang_reply_dest', $blanguage );
$SNAME=$_ENV["sesname"];
$MSG_HASH=pack("H32",$_REQUEST['repof']);
$SNAME=$_ENV['sesname'];

if ( strlen($_REQUEST['edit_of'])==32 ) {
    $EDIT_OF=pack("H32",$_REQUEST['edit_of']);
    if ( strlen($EDIT_OF) == 16 )
        $edit_val=1;
}

if (!$GLOBALS['sess_auth']) die ($lang['reply_login']);

$IDENTIFICATORE=md5($GLOBALS['sess_password'].$GLOBALS['sess_nick']); // = identificatore dell'utente nella tabella localmember. easadecimale
$KEY_DECRYPT=md5($GLOBALS['sess_nick'].$GLOBALS['sess_password'],TRUE);// = password per decriptare la chiave privata in localmember (16byte)
  $query="SELECT PASSWORD FROM ".$SNAME."_localmember WHERE HASH='".$IDENTIFICATORE."';";
  $risultato = mysql_query($query) or die ($lang['inv_query'] . mysql_error());
$riga = mysql_fetch_assoc($risultato) or die($lang['reply_user']);
$privkey=base64_decode($riga[PASSWORD]);
/* Vedi se l'utente � bannato, CanWrite in forumlib.pm
$querysql="SELECT ban FROM " . $_ENV['sesname'] . "_membri WHERE HASH='" .
*/
$PKEY=$std->getpkey($SNAME);
$req[FUNC][Base642Dec]=$PKEY;
$req[FUNC][BlowDump2var][Key]=$KEY_DECRYPT;
$req[FUNC][BlowDump2var][Data]=$privkey;
$core=new CoreSock;
if (!$core->Send($req)) die($lang['reply_core']);
if (!$risp=$core->Read()) die ($lang['reply_timeout']);
$PKEY=$risp[FUNC][Base642Dec];
if ( strlen($PKEY) < 120 ) die($lang['reply_admin']);
if (strlen($risp[FUNC][BlowDump2var][hash])!=16) die ($lang['reply_pdata']);

$querysql="SELECT count(*) FROM " . $SNAME . "_newmsg WHERE HASH='".mysql_escape_string($MSG_HASH)."'";
$sqlresult=mysql_query($querysql);
if (!mysql_num_rows($sqlresult)) die($lang['reply_mnf']);
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
if (!$core->Send($nreq)) die($lang['reply_core']);
if (!$risp=$core->Read()) die ($lang['reply_timeout']);
$mreq['FORUM']['ADDMSG']['SIGN']=$risp[RSA][FIRMA][$MD5_MSG];
#$mreq[FORUM][ADDMSG]=$REP_DATA;

$core->Send($mreq);
$risp=$core->Read();
?>

<html>
 <head>
  <link rel="shortcut icon" href="favicon.ico">
  <title><? echo $lang['reply_wait']; ?></title>
  <meta http-equiv='refresh' content='2; url=showmsg.php?SEZID=<? echo $_REQUEST['sezid']; ?>&THR_ID=<? echo $_REQUEST['repof'] ?>&pag=last#end_page'>
  <script type="text/javascript"> </script>
  <link type="text/css" rel="stylesheet" href="style_page_redirect.css">
 </head>
 <body>
  <div id="redirectwrap">
   <h4><? echo $lang['reply_thanks']; ?></h4>
   <p>
<?
// echo $risp['FORUM']['ADDMSG']."<br>";
// echo "alfa<br>";
?>
         <? echo $lang['reply_wait2']; ?><br><br>
   </p>
   <p class="redirectfoot">(<a href="showmsg.php?SEZID=<? echo $_REQUEST['sezid']; ?>&THR_ID=<? echo $_REQUEST['repof'] ?>&pag=last#end_page"><? echo $lang['reply_nowait']; ?></a>)</p>
  </div>
 </body>
</html>