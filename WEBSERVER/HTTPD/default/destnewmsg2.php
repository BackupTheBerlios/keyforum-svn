<?php

include ("lib/lib.php"); # Librerie per creare la connessione MySQL

if(is_array($lang)) { $lang += $std->load_lang('lang_reply_dest', $blanguage );} else { $lang = $std->load_lang('lang_reply_dest', $blanguage );}

$forum_id = pack('H*',$config[SHARE][$SNAME][ID]);

$EDITID = mysql_real_escape_string(pack("H*",$_REQUEST["edit_of"]));

if ( strlen($_REQUEST['edit_of'])==32 ) {
    $EDIT_OF=pack("H32",$_REQUEST['edit_of']);
    if ( strlen($EDIT_OF) == 16 )
        $edit_val=1;
}

if (!$_SESSION[$SNAME]['sess_auth']) $std->Error ($lang['reply_login']);

$IDENTIFICATORE=md5($_SESSION[$SNAME]['sess_password'].$_SESSION[$SNAME]['sess_nick']); // = identificatore dell'utente nella tabella localmember. easadecimale
$KEY_DECRYPT=pack('H*',md5($_SESSION[$SNAME]['sess_nick'].$_SESSION[$SNAME]['sess_password']));// = password per decriptare la chiave privata in localmember (16byte)
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


$PKEY=$config[SHARE][$SNAME][PKEY];
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

$forum_id = pack('H*',$config[SHARE][$SNAME][ID]);
$mreq['SEZ']=$_REQUEST['sezid'];
//$mreq['FDEST']=pack('H*',sha1($PKEY));
$mreq['AUTORE']=$risp['FUNC']['BlowDump2var']['hash'];
$mreq['AVATAR']=$_REQUEST['avatar'];
$mreq['FIRMA']=$_REQUEST['firma'];
$mreq['TYPE']='3';
$mreq['DATE']=$risp['CORE']['INFO']['GMT_TIME']; //'1138225060';
$mreq['TITLE']=$_REQUEST['subject'];
$mreq['BODY']=$_REQUEST['body'];
$mreq['SUBTITLE']=$_REQUEST['desc'];
$mreq['_PRIVATE']=$privkey;
$mreq['_PWD']=$KEY_DECRYPT;

if ( $edit_val ) {
    $mreq['EDIT_OF']=$EDIT_OF;
	$mreq['IS_EDIT']='1';
}


// ****************** test: pinned


if($_REQUEST['extvar'])
{
	$query="SELECT PINNED, FIXED, HOME, block_date from {$SNAME}_msghe WHERE HASH='$EDITID';";
	$riga = $db->get_row($query);
	if ($riga)
	{
		$Pinned=$riga->PINNED;
		$Fixed=$riga->FIXED;
		$Home=$riga->HOME;
		$Lock=$riga->block_date;
	}else{
		$Pinned=0;
		$Fixed=0;
		$Home=0;
		$Lock=0;
	}
	$change=0;
	$extvar=array();
	if($_REQUEST['pinned'] xor $Pinned)
	{
		$change=1;
		if($_REQUEST['pinned']) $Pinned=1;
		else $Pinned=0;
	}
	if($_REQUEST['fixed'] xor $Fixed)
	{
		$change=1;
		if($_REQUEST['fixed']) $Fixed=1;
		else $Fixed=0;
	}
	if($_REQUEST['home'] xor $Home)
	{
		$change=1;
		if($_REQUEST['home']) $Home=1;
		else $Home=0;
	}
	if($_REQUEST['lock'] xor $Lock)
	{
		if($_REQUEST['lock'])
		{
			$core=new CoreSock;
			if (@$core->Connect())
			{
				// Questa parte mi serve solo per ottenere una risposta dal core, cos� da leggerne il timestamp GMT
				$idquery="SELECT value FROM config WHERE MAIN_GROUP='SHARE' AND SUBKEY='".$SNAME."' AND FKEY='ID';";
				$idriga = $db->get_var($idquery);
				$req_nod[INFO][FORUM][0]=@pack("H*", $idriga);
                 		$core->Send($req_nod);
				$risp=$core->Read();
				// Fine parte fittizia
				
				$Lock=$risp['CORE']['INFO']['GMT_TIME'];
				$change=1;
			}
		}else{
			$Lock=0;
			$change=1;
		}
	}
	if($change)
	{
		$extvar[update_thread]=1; // Eseguo l'update sul thread
		$extvar[pinned]=$Pinned;
		$extvar[fixed]=$Fixed;
		$extvar[home]=$Home;
		$extvar[block_date]=$Lock;
		$mreq['EXTVAR']=$core->Var2BinDump($extvar); // Inserisco la extvar come allegato del messaggio:
	}
}


// ****************** test: pinned - end



/*$MD5_MSG=pack('H*',md5($PKEY.$_REQUEST['sezid'].$mreq[FORUM][ADDMSG]['AUTORE']."1".$EDIT_OF
        .$mreq[FORUM][ADDMSG]['DATE'].$_REQUEST['subject'].$_REQUEST['desc'].$_REQUEST['body'].$_REQUEST['firma'].$_REQUEST['avatar']));
if ( $edit_val ) {
    $mreq['FORUM']['ADDMSG']['EDIT_OF']=$EDIT_OF;
	$mreq['FORUM']['ADDMSG']['IS_EDIT']='1';
}
else $mreq['FORUM']['ADDMSG']['EDIT_OF']=$MD5_MSG;
$mreq['FORUM']['ADDMSG']['MD5']=$MD5_MSG;

// message sign request with user private key
$nreq['RSA']['FIRMA'][0]['md5']=$MD5_MSG;
$nreq['RSA']['FIRMA'][0]['priv_key']=$KEY_DECRYPT;
$nreq['RSA']['FIRMA'][0]['priv_pwd']=$privkey;
if (!$core->Send($nreq)) $std->Error($lang['reply_core']);
if (!$risp=$core->Read()) $std->Error ($lang['reply_timeout']);

$mreq['FORUM']['ADDMSG']['SIGN']=$risp[RSA][FIRMA][$MD5_MSG];
*/

//if (!$core->Send($mreq)) $std->Error($lang['reply_core']);
//if (!$risp=$core->Read()) $std->Error ($lang['reply_timeout']);

$risp = $core->AddMsg($mreq);

if ( empty($risp['ERRORE']) ) {		// ok, redirect
	if ($_REQUEST['edit_of']) $THR_ID=$_REQUEST['edit_of'];
	else $THR_ID=bin2hex($risp['MD5']);
    $rurl="showmsg.php?SEZID=".$_REQUEST['sezid']."&THR_ID=".$THR_ID;
    $std->Redirect($lang['reply_thanks'],$rurl,$lang['reply_ok'],$lang['reply_ok']);
}
else $std->Error("Error adding reply, error code: " . $risp[ERRORE],$_REQUEST['body']);

/*if($_REQUEST['edit_of']) 
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
} */

?>