<?php

include ("lib/lib.php"); # Librerie per creare la connessione MySQL

if(is_array($lang)) { $lang += $std->load_lang('lang_reply_dest', $blanguage );} else { $lang = $std->load_lang('lang_reply_dest', $blanguage );}

$MSG_HASH=pack("H32",$_REQUEST['repof']);

if ( strlen($_REQUEST['edit_of'])==32 ) {
    $EDIT_OF=pack("H32",$_REQUEST['edit_of']);
    if ( strlen($EDIT_OF) == 16 )
        $edit_val=1;
}

// salvo le dimensioni della textbox, se richiesto
if ($_REQUEST['tboxsave'])
{
$newboxsize=explode(":",$_REQUEST['boxsize']);
$userdata->TBOX_ROWS=$newboxsize[0];
$userdata->TBOX_COLS=$newboxsize[1];
$std->UpdateUserData($SNAME,$userdata);
}


if (!$_SESSION[$SNAME]['sess_auth']) $std->Error ($lang['reply_login'],$_REQUEST['body']);

$IDENTIFICATORE=md5($_SESSION[$SNAME]['sess_password'].$_SESSION[$SNAME]['sess_nick']); // = identificatore dell'utente nella tabella localmember. easadecimale
$KEY_DECRYPT=pack('H*',md5($_SESSION[$SNAME]['sess_nick'].$_SESSION[$SNAME]['sess_password']));// = password per decriptare la chiave privata in localmember (16byte)
$query="SELECT PASSWORD FROM ".$SNAME."_localmember WHERE HASH='".$IDENTIFICATORE."';";
$privkey=base64_decode($db->get_var($query));

$PKEY=$std->getpkey($SNAME);;
//$req[FUNC][Base642Dec]=$PKEY;
$req[FUNC][BlowDump2var][Key]=$KEY_DECRYPT;
$req[FUNC][BlowDump2var][Data]=$privkey;
$core=new CoreSock;
if (!$core->Send($req)) $std->Error($lang['reply_core'],$_REQUEST['body']);

// timeout ?
if (!$risp=$core->Read()) 
{

$std->Error($lang['reply_timeout'],$_REQUEST['body']);
}

//$PKEY=$risp[FUNC][Base642Dec];

if ( strlen($PKEY) < 120 ) 
{
$std->Error($lang['reply_admin'],$_REQUEST['body']);
}

if (strlen($risp[FUNC][BlowDump2var][hash])!=16) $std->Error ($lang['reply_pdata'],$_REQUEST['body']);

$userhash=$risp[FUNC][BlowDump2var][hash];
if ( get_magic_quotes_gpc() ) $userhash=stripslashes($userhash);
$userhash=mysql_real_escape_string($userhash);

$querysql="SELECT count(1) FROM {$SNAME}_newmsg WHERE HASH='".mysql_escape_string($MSG_HASH)."'";
$sqlresult=$db->get_var($querysql);
if (!$sqlresult) $std->Error($lang['reply_mnf'],$_REQUEST['body']);

$forum_id = pack('H*',$config[SHARE][$SNAME][ID]);
//$mreq['FDEST']=pack('H*',sha1($PKEY));
$mreq['REP_OF']=$MSG_HASH;
$mreq['AUTORE']=$risp['FUNC']['BlowDump2var']['hash'];
$mreq['AVATAR']=$_REQUEST['avatar'];
$mreq['FIRMA']=$_REQUEST['firma'];
$mreq['TYPE']='4';
//$mreq['DATE']=$risp['CORE']['INFO']['GMT_TIME'];
$mreq['TITLE']=$_REQUEST['title'];
$mreq['BODY']=$_REQUEST['body'];
$mreq['_PRIVATE']=$privkey;
$mreq['_PWD']=$KEY_DECRYPT;

if ( $edit_val ) {
	$mreq['EDIT_OF']=$EDIT_OF;
	$mreq['IS_EDIT']='1';
}

/*$MD5_MSG=pack('H*',md5($PKEY.$MSG_HASH.$mreq[FORUM][ADDMSG]['AUTORE']."2".$EDIT_OF
        .$_REQUEST['avatar'].$_REQUEST['firma'].$mreq[FORUM][ADDMSG]['DATE'].$_REQUEST['title'].$_REQUEST['body']));
if ( $edit_val ) {
	$mreq['FORUM']['ADDMSG']['EDIT_OF']=$EDIT_OF;
	$mreq['FORUM']['ADDMSG']['IS_EDIT']='1';
} else $mreq['FORUM']['ADDMSG']['EDIT_OF']=$MD5_MSG;

$mreq['FORUM']['ADDMSG']['MD5']=$MD5_MSG;
$nreq['RSA']['FIRMA'][0]['md5']=$MD5_MSG;
$nreq['RSA']['FIRMA'][0]['priv_key']=$KEY_DECRYPT;
$nreq['RSA']['FIRMA'][0]['priv_pwd']=$privkey;
if (!$core->Send($nreq)) $std->Error($lang['reply_core'],$_REQUEST['body']);
if (!$risp=$core->Read()) $std->Error ($lang['reply_timeout'],$_REQUEST['body']);
$mreq['FORUM']['ADDMSG']['SIGN']=$risp[RSA][FIRMA][$MD5_MSG];
#$mreq[FORUM][ADDMSG]=$REP_DATA;

$core->Send($mreq);
$risp=$core->Read();
*/

$risp = $core->AddMsg($mreq);

if ( empty($risp[ERRORE]) ) {		// ok, redirect
    $rurl="showmsg.php?SEZID=".$_REQUEST['sezid']."&THR_ID=".$_REQUEST['repof']."&pag=last#end_page";
    $std->Redirect($lang['reply_thanks'],$rurl,$lang['reply_ok'],$lang['reply_ok']);
}
else $std->Error("Error adding reply, error code: " . $risp[ERRORE],$_REQUEST['body']);

/*switch ($risp['FORUM']['ADDMSG']) {
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
		echo "ERRORE: " . $risp['FORUM']['ADDMSG']['ERRORE'] . "<br>";
        $std->Error($lang['reply_error3'],$_REQUEST['body']);
        break;
    default:
        // error: Unknown error
		echo "ERRORE: " . $risp['FORUM']['ADDMSG']['ERRORE'] . "<br>";
        $std->Error($lang['reply_error3'],$_REQUEST['body']);
} 
*/


?>