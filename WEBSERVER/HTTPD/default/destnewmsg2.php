<?php

include ("lib/lib.php"); # Librerie per creare la connessione MySQL

if(is_array($lang)) { $lang += $std->load_lang('lang_reply_dest', $blanguage );} else { $lang = $std->load_lang('lang_reply_dest', $blanguage );}

$SNAME=$_ENV['sesname'];


if ( strlen($_REQUEST['edit_of'])==32 ) {
    $EDIT_OF=pack("H32",$_REQUEST['edit_of']);
    if ( strlen($EDIT_OF) == 16 )
        $edit_val=1;
}

if (!$GLOBALS['sess_auth']) $std->Error ($lang['reply_login']);

$IDENTIFICATORE=md5($GLOBALS['sess_password'].$GLOBALS['sess_nick']); // = identificatore dell'utente nella tabella localmember. easadecimale
$KEY_DECRYPT=pack('H*',md5($GLOBALS['sess_nick'].$GLOBALS['sess_password']));// = password per decriptare la chiave privata in localmember (16byte)
$query="SELECT PASSWORD FROM ".$SNAME."_localmember WHERE HASH='$IDENTIFICATORE';";
$password = $db->get_var($query);
if(!$password)
{
	$std->Error($lang['reply_user'],$_REQUEST['body']);
	
}
else
{
	$privkey=base64_decode($password);
}


$PKEY=$std->getpkey($SNAME);
//$req[FUNC][Base642Dec]=$PKEY;
$req[FUNC][BlowDump2var][Key]=$KEY_DECRYPT;
$req[FUNC][BlowDump2var][Data]=$privkey;
$core=new CoreSock;
if (!$core->Send($req)) $std->Error($lang['reply_core']);
if (!$risp=$core->Read()) $std->Error ($lang['reply_timeout']);
//$PKEY=$risp[FUNC][Base642Dec];
if ( strlen($PKEY) < 120 ) $std->Error($lang['reply_admin']);
if ( strlen($risp[FUNC][BlowDump2var][hash]) != 16 ) $std->Error ($lang['reply_pdata']);

$userhash=$risp[FUNC][BlowDump2var][hash];
if ( get_magic_quotes_gpc() ) $userhash=stripslashes($userhash);
$userhash=mysql_real_escape_string($userhash);

$banquery="SELECT ban FROM {$SNAME}_membri WHERE HASH='$userhash';";
$banned=$db->get_var($banquery);
if ($banned) $std->Error($lang['reply_ban'],$_REQUEST['body']);

$mreq['FORUM']['ADDMSG'];
$mreq['FORUM']['ADDMSG']['SEZ']=$_REQUEST['sezid'];
$mreq['FORUM']['ADDMSG']['FDEST']=pack('H*',sha1($PKEY));
$mreq['FORUM']['ADDMSG']['AUTORE']=$risp['FUNC']['BlowDump2var']['hash'];
$mreq['FORUM']['ADDMSG']['AVATAR']=$_REQUEST['avatar'];
$mreq['FORUM']['ADDMSG']['FIRMA']=$_REQUEST['firma'];
$mreq['FORUM']['ADDMSG']['TYPE']='3';
$mreq['FORUM']['ADDMSG']['DATE']=$risp['CORE']['INFO']['GMT_TIME']; //'1138225060';
$mreq['FORUM']['ADDMSG']['TITLE']=$_REQUEST['subject'];
$mreq['FORUM']['ADDMSG']['BODY']=$_REQUEST['body'];
$mreq['FORUM']['ADDMSG']['SUBTITLE']=$_REQUEST['desc'];
$mreq['FORUM']['ADDMSG']['_PRIVATE']=$privkey;
$mreq['FORUM']['ADDMSG']['_PWD']=$KEY_DECRYPT;

$MD5_MSG=pack('H*',md5($PKEY.$_REQUEST['sezid'].$mreq[FORUM][ADDMSG]['AUTORE']."1".$EDIT_OF
        .$mreq[FORUM][ADDMSG]['DATE'].$_REQUEST['subject'].$_REQUEST['desc'].$_REQUEST['body'].$_REQUEST['firma'].$_REQUEST['avatar']));
if ( $edit_val )
    $mreq['FORUM']['ADDMSG']['EDIT_OF']=$EDIT_OF;
else $mreq['FORUM']['ADDMSG']['EDIT_OF']=$MD5_MSG;
$mreq['FORUM']['ADDMSG']['MD5']=$MD5_MSG;

// message sign request with user private key
$nreq['RSA']['FIRMA'][0]['md5']=$MD5_MSG;
$nreq['RSA']['FIRMA'][0]['priv_key']=$KEY_DECRYPT;
$nreq['RSA']['FIRMA'][0]['priv_pwd']=$privkey;
if (!$core->Send($nreq)) $std->Error($lang['reply_core']);
if (!$risp=$core->Read()) $std->Error ($lang['reply_timeout']);

$mreq['FORUM']['ADDMSG']['SIGN']=$risp[RSA][FIRMA][$MD5_MSG];

// if a private key was supplied (passworded forum) use it to sign the message and add it in FOR_SIGN
if ( !empty($_REQUEST['PrivKey']) ) {
	$corereq['RSA']['FIRMA'][0]['md5']=$MD5_MSG;
	$corereq['RSA']['FIRMA'][0]['priv_pwd']=base64_decode($_REQUEST['PrivKey']);
	if (!$core->Send($corereq)) $std->Error($lang['reply_core']);
	if (!$risp=$core->Read()) $std->Error ($lang['reply_timeout']);
	$mreq['FORUM']['ADDMSG']['FOR_SIGN']=$risp[RSA][FIRMA][$MD5_MSG];
}

if (!$core->Send($mreq)) $std->Error($lang['reply_core']);
if (!$risp=$core->Read()) $std->Error ($lang['reply_timeout']);

if($_REQUEST['edit_of']) 
{
$THR_ID=$_REQUEST['edit_of'];
} else {
$THR_ID=bin2hex($MD5_MSG);
}



switch ($risp['FORUM']['ADDMSG']) {
    case 1:
        // ok, redirect
        $rurl="showmsg.php?SEZID=".$_REQUEST['sezid']."&THR_ID=".$THR_ID."&pag=last#end_page";
        $std->Redirect($lang['reply_thanks'],$rurl,$lang['reply_ok'],$lang['reply_ok']);
        break;
    case -2:
        // error: board not forun
        $std->Error($lang['reply_error2'],$_REQUEST['body']);
        break;
    case -1:
        // error: Unknown error
		echo "ERRORE: " . $risp['FORUM']['ADDMSG']['ERRORE'] . "<br><br>";
        $std->Error($lang['reply_error3'],$_REQUEST['body']);
        break;
    default:
        // error: Unknown error
		echo "ERRORE: " . $risp['FORUM']['ADDMSG']['ERRORE'] . "<br><br>";
        $std->Error($lang['reply_error3'],$_REQUEST['body']);
} 

?>