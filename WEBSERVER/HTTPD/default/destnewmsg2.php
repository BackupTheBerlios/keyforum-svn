<?php

include ("lib/lib.php"); # Librerie per creare la connessione MySQL

CheckSession();
?>
<?

$userdata=$std->GetUserData($_ENV["sesname"],$sess_nick,$sess_password);

if($userdata['LANG']) {
$blanguage=$userdata['LANG']; // Lingua di visualizzazione
} else {$blanguage="eng";}

$lang = $std->load_lang('lang_reply_dest', $blanguage );
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

$PKEY=$std->getpkey($SNAME);
$req[FUNC][Base642Dec]=$PKEY;
$req[FUNC][BlowDump2var][Key]=$KEY_DECRYPT;
$req[FUNC][BlowDump2var][Data]=$privkey;
$core=new CoreSock;
if (!$core->Send($req)) die($lang['reply_core']);
if (!$risp=$core->Read()) die ($lang['reply_timeout']);
$PKEY=$risp[FUNC][Base642Dec];
if ( strlen($PKEY) < 120 ) die($lang['reply_admin']);
if ( strlen($risp[FUNC][BlowDump2var][hash]) != 16 ) die ($lang['reply_pdata']);

$userhash=$risp[FUNC][BlowDump2var][hash];
if ( get_magic_quotes_gpc() ) $userhash=stripslashes($userhash);
$userhash=mysql_real_escape_string($userhash);
$banquery="SELECT ban FROM $SNAME" . "_membri WHERE HASH='$userhash';";
$banresult=mysql_query($banquery);
$banned=mysql_fetch_row($banresult);
if ( $banned[0] ) die($lang['reply_ban']);

$mreq['FORUM']['ADDMSG'];
$mreq['FORUM']['ADDMSG']['SEZ']=$_REQUEST['sezid'];
$mreq['FORUM']['ADDMSG']['FDEST']=sha1($PKEY,TRUE);
$mreq['FORUM']['ADDMSG']['AUTORE']=$risp['FUNC']['BlowDump2var']['hash'];
$mreq['FORUM']['ADDMSG']['AVATAR']=$_REQUEST['avatar'];
$mreq['FORUM']['ADDMSG']['FIRMA']=$_REQUEST['firma'];
$mreq['FORUM']['ADDMSG']['TYPE']='1';
$mreq['FORUM']['ADDMSG']['DATE']=$risp['CORE']['INFO']['GMT_TIME'];
$mreq['FORUM']['ADDMSG']['TITLE']=$_REQUEST['subject'];
$mreq['FORUM']['ADDMSG']['BODY']=$_REQUEST['body'];
$mreq['FORUM']['ADDMSG']['SUBTITLE']=$_REQUEST['desc'];

$MD5_MSG=md5($PKEY.$_REQUEST['sezid'].$mreq[FORUM][ADDMSG]['AUTORE']."1".$EDIT_OF
        .$mreq[FORUM][ADDMSG]['DATE'].$_REQUEST['subject'].$_REQUEST['desc'].$_REQUEST['body'].$_REQUEST['firma'].$_REQUEST['avatar'],TRUE);
if ( $edit_val )
    $mreq['FORUM']['ADDMSG']['EDIT_OF']=$EDIT_OF;
else $mreq['FORUM']['ADDMSG']['EDIT_OF']=$MD5_MSG;
$mreq['FORUM']['ADDMSG']['MD5']=$MD5_MSG;

$nreq['RSA']['FIRMA'][0]['md5']=$MD5_MSG;
$nreq['RSA']['FIRMA'][0]['priv_key']=$KEY_DECRYPT;
$nreq['RSA']['FIRMA'][0]['priv_pwd']=$privkey;
if (!$core->Send($nreq)) die($lang['reply_core']);
if (!$risp=$core->Read()) die ($lang['reply_timeout']);
$mreq['FORUM']['ADDMSG']['SIGN']=$risp[RSA][FIRMA][$MD5_MSG];

$core->Send($mreq);
$risp=$core->Read();
$THR_ID=bin2hex($MD5_MSG);
?>

<html>
 <head>
  <link rel="shortcut icon" href="favicon.ico">
  <title><?
         if($risp['FORUM']['ADDMSG']==1){
           echo $lang['reply_wait'];
         }else{
           echo $lang['reply_error'];
         }
       ?></title>
  <? if($risp['FORUM']['ADDMSG']==1){ ?>
   <meta http-equiv='refresh' content='2; url=showmsg.php?SEZID=<? echo $_REQUEST['sezid']; ?>&THR_ID=<? echo $THR_ID; ?>&pag=last#end_page'>
  <? } ?>
  <link type="text/css" rel="stylesheet" href="style_page_redirect.css">
 </head>
 <body>
  <div id="redirectwrap">
   <h4><?
         if($risp['FORUM']['ADDMSG']==1){
           echo $lang['reply_thanks'];
         }else{
           echo "<font color='red'>".$lang['reply_error']."</font>";
         }
       ?></h4>
   <p>
    <?
      if($risp['FORUM']['ADDMSG']==1){
        echo $lang['reply_ok']."<br>".$lang['reply_wait2'];
      }elseif($risp['FORUM']['ADDMSG']==-2){
        echo "<b>".$lang['reply_error2']."</b><br>";
      }elseif($risp['FORUM']['ADDMSG']==-1){
        echo "<b>".$lang['reply_error3']."</b><br>";
      }
    ?><br><br>
   </p>
   <p class="redirectfoot">(<a href="showmsg.php?SEZID=<? echo $_REQUEST['sezid']; ?>&THR_ID=<? echo $THR_ID; ?>&pag=last#end_page"><?
      if($risp['FORUM']['ADDMSG']==1){
         echo $lang['reply_nowait'];
      }else{
         echo $lang['reply_nowait2'];
      }
    ?></a>)
   </p>
  </div>
 </body>
</html>