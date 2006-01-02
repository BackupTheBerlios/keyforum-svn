<?php

include ("lib/lib.php"); # Librerie per creare la connessione MySQL

//Gi� appena fatto da lib.php
/*CheckSession();
?>
<?
$userdata=$std->GetUserData($_ENV["sesname"],$sess_nick,$sess_password);

if($userdata->LANG) {
$blanguage=$userdata->LANG; // Lingua di visualizzazione
} else {$blanguage="eng";}*/

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
$KEY_DECRYPT=pack('H*',md5($GLOBALS['sess_nick'].$GLOBALS['sess_password']));// = password per decriptare la chiave privata in localmember (16byte)
$query="SELECT PASSWORD FROM ".$SNAME."_localmember WHERE HASH='".$IDENTIFICATORE."';";
$privkey=base64_decode($db->get_var($query));

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

$userhash=$risp[FUNC][BlowDump2var][hash];
if ( get_magic_quotes_gpc() ) $userhash=stripslashes($userhash);
$userhash=mysql_real_escape_string($userhash);

$banquery="SELECT ban FROM $SNAME" . "_membri WHERE HASH='$userhash';";
$banned=$db->get_var($banquery);
if ( $banned ) die($lang['reply_ban']);

$querysql="SELECT count(1) FROM {$SNAME}_newmsg WHERE HASH='".mysql_escape_string($MSG_HASH)."'";
$sqlresult=$db->get_var($querysql);
if (!$sqlresult) die($lang['reply_mnf']);
$mreq['FORUM']['ADDMSG'];
$mreq['FORUM']['ADDMSG']['FDEST']=pack('H*',sha1($PKEY));
$mreq['FORUM']['ADDMSG']['REP_OF']=$MSG_HASH;
$mreq['FORUM']['ADDMSG']['AUTORE']=$risp['FUNC']['BlowDump2var']['hash'];
$mreq['FORUM']['ADDMSG']['AVATAR']=$_REQUEST['avatar'];
$mreq['FORUM']['ADDMSG']['FIRMA']=$_REQUEST['firma'];
$mreq['FORUM']['ADDMSG']['TYPE']='2';
$mreq['FORUM']['ADDMSG']['DATE']=$risp['CORE']['INFO']['GMT_TIME'];
$mreq['FORUM']['ADDMSG']['TITLE']=$_REQUEST['title'];


$mreq['FORUM']['ADDMSG']['BODY']=$_REQUEST['body'];
$MD5_MSG=pack('H*',md5($PKEY.$MSG_HASH.$mreq[FORUM][ADDMSG]['AUTORE']."2".$EDIT_OF
        .$_REQUEST['avatar'].$_REQUEST['firma'].$mreq[FORUM][ADDMSG]['DATE'].$_REQUEST['title'].$_REQUEST['body']));
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


switch ($risp['FORUM']['ADDMSG']) {
    case 1:
        // ok, redirect
        $rurl="showmsg.php?SEZID=".$_REQUEST['sezid']."&THR_ID=".$_REQUEST['repof']."&pag=last#end_page";
        $std->Redirect($lang['reply_thanks'],$rurl,$lang['reply_ok'],$lang['reply_ok']);
        break;
    case -2:
        // error: board not forun
        $std->Error($lang['reply_error2'],$_REQUEST['body']);
        break;
    case -1:
        // error: Unknown error
        $std->Error($lang['reply_error3'],$_REQUEST['body']);
        break;
    default:
        // error: Unknown error
        $std->Error($lang['reply_error3'],$_REQUEST['body']);
} 



?>