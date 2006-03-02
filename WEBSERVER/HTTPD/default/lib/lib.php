<?php
$sep = PATH_SEPARATOR; 
ini_set("include_path", ini_get ("include_path")."$sep.$sep../../../WEBSERVER/pear");
  $Timer1 = microtime();
  $Timer1 = explode(" ",$Timer1);
  $Timer1 = $Timer1[0] + $Timer1[1];

$corecalls=0;

if($string=ob_get_contents())
{
	session_start();
	ob_end_flush();
}
else	session_start();

//--------------------------------
// Classe per funzioni globali
//--------------------------------
require "lib/functions.php";
require "core.php";
$std   = new FUNC;


//classe PEAR per file config (XML)
require_once "Config.php";
$xmldata = new Config;

$root =& $xmldata->parseConfig('http://'.$_SERVER['HTTP_HOST'].'/config/config.xml', 'XML');
if (PEAR::isError($root)) {
    $std->Error('Error reading XML config file: ' . $root->getMessage());
}

$settings = $root->toArray();

// dati del db
require_once('ez_sql.php');
$_ENV['sql_host']=$settings['root']['conf']['DB']['host'];
$_ENV['sql_user']=$settings['root']['conf']['DB']['dbuser'];
$_ENV['sql_passwd']=$settings['root']['conf']['DB']['dbpassword'];
$_ENV['sql_dbname']=$settings['root']['conf']['DB']['dbname'];
$_ENV['sql_dbport']=$settings['root']['conf']['DB']['dbport'];

if(!$_ENV['sql_dbport']){$_ENV['sql_dbport']="3306";}
$db = new db($_ENV['sql_user'], $_ENV['sql_passwd'], $_ENV['sql_dbname'],$_ENV['sql_host'].":".$_ENV['sql_dbport']);

$query = "SELECT * FROM config WHERE 1";
$result = $db->get_results($query);
if($result)foreach($result as $riga)
{
	$config[$riga->MAIN_GROUP][$riga->SUBKEY][$riga->FKEY] = $riga->VALUE;
}
 
define ("SID", session_id());
define ("USID", "PHPSESSID=".session_id());
define ("GMT_TIME", date("Z")); // offset GMT

// sessione
$keyforum['porta'] = $_SERVER['SERVER_PORT'];
$keyforum['sesname'] = $_SERVER['sesname'];
$keyforum['nome'] = $_SERVER['sesname'];
$SNAME = $_SERVER['sesname'];


//Configurazione della board
$query = "SELECT * FROM {$_SERVER['sesname']}_conf WHERE 1";
$result = $db->get_results($query);
if ($result) { 
foreach($result as $riga)
{
	$forum_conf[$riga->GROUP][$riga->FKEY][$riga->SUBKEY] = array('VALUE' =>$riga->VALUE, 'PRESENT' =>$riga->present, 'DATE'=>$riga->date);
}
}

$GLOBALS['SEZ_DATA'] = &$SEZ_DATA;

function CheckSession() {
global $db,$SNAME;
if(!$_SESSION[$SNAME]) //Se non sono già autenticato
{
 if($_COOKIE["sess_auth_{$SNAME}"]) //Se c'è il cookie
 {
  //Prendo i dati dal cookie
  $the_cookie = unserialize(stripslashes($_COOKIE["sess_auth_{$SNAME}"]));
  list($nick,$pass,$logged_since) = $the_cookie;
  //E mi autentico
  $_SESSION[$SNAME]['sess_nick'] = $nick;
  $_SESSION[$SNAME]['sess_password'] = $pass;
  $_SESSION[$SNAME]['logged_since'] = $logged_since;
  $_SESSION[$SNAME]['sess_auth'] = 1;
  //Reimposto la scadenza del cookie
  $the_cookie = array(mysql_real_escape_string($nick),$pass,$logged_since);
  setcookie("sess_auth_{$SNAME}",serialize($the_cookie),time()+60*60*24*7);
 }
}
}

function DestroySession() 
{
global $db,$SNAME;
	$sess_id = session_id();
	$my_ip = $_SERVER['REMOTE_ADDR'];
	$SNAME = $_SERVER['sesname'];
	$query="DELETE FROM session WHERE SESSID='$sess_id' AND IP=md5('$my_ip') AND FORUM='$SNAME' ";
	$db->query($query);
	
	unset($_SESSION[$SNAME]);
	if(isset($_COOKIE["sess_auth_{$SNAME}"])) @setcookie("sess_auth_{$SNAME}",'',time()-60*60*24*1); //Scade ieri ed è vouto
}

function Muori($errore) {
  echo $errore;
  exit(0);
}
function GetLastMsg($sezid) {
	global $db;
	$query="SELECT (msghe.last_reply_time+".GMT_TIME.") AS 'time_action', newmsg.TITLE AS 'TITLE', membri.AUTORE AS 'nick', membri.HASH AS 'nickhash', newmsg.EDIT_OF AS 'hash',newmsg.SEZ AS 'SEZID'"
	." FROM ".$_SERVER['sesname']."_msghe AS msghe, ".$_SERVER['sesname']."_membri AS membri,".$_SERVER['sesname']."_newmsg AS newmsg"
	." WHERE msghe.HASH=newmsg.EDIT_OF"
	." AND msghe.last_reply_author=membri.HASH"
	." AND newmsg.visibile='1'"
	." AND newmsg.SEZ='".$sezid."'"
	." ORDER BY msghe.last_reply_time DESC"
	." LIMIT 1;";
	$risultato=$db->get_row($query);
	return($risultato);
}
function Ip2Num ($ip) {
	$ip=explode('.',$ip);
	$ip=unpack("Iip",pack("CCCC",$ip[0],$ip[1],$ip[2],$ip[3]));
	# Sembra che in unpack, la I interger senza segno e con segno diano lo stesso risultato.
	# Per questo motivo in basso c'è l'if che controlla
	if ($ip[ip]<0) return($ip[ip]+pow(2,32)); else return($ip[ip]);
}
function Num2Ip ($ip) {
	return implode('.', unpack("C4",pack("I",$ip+0)));
}

$db->query("update `session` set `DATE`='".time()."' where `SESSID`='".session_id()."' AND IP=md5('".$_SERVER['REMOTE_ADDR']."') AND FORUM='".$_SERVER['sesname']."';");
if (rand(0,10)<1) $db->query("DELETE FROM `session` WHERE `DATE`<'".(time()-3600)."';");
if ($_REQUEST['SEZID']) 
{
	$tmp = mysql_escape_string($_GET['SEZID']);
	$ris = $db->get_row("SELECT * FROM {$_SERVER['sesname']}_sez WHERE ID='$tmp)';");
  if($ris) {
    $GLOBALS['SEZ_DATA']=$ris;
  }
}

function secure_v($val) {
  if ($val == "") { return ""; }
//  $val = str_replace( " ", " ", $val );
  $val = str_replace( chr(0xCA), "", $val );
  $val = str_replace( "&"            , "&amp;"         , $val );
/*  $val = str_replace( "<!--"         , "<!--"  , $val );
  $val = str_replace( "-->"          , "-->"       , $val );*/
  $val = preg_replace( "/<script/i"  , "<script"   , $val );
  $val = str_replace( ">"            , "&gt;"          , $val );
  $val = str_replace( "<"            , "&lt;"          , $val );
  $val = str_replace( "\""           , "&quot;"        , $val );
  $val = preg_replace( "/\n/"        , "<br />"          , $val );
  $val = preg_replace( "/\\\$/"      , "$"        , $val );
  $val = preg_replace( "/\r/"        , ""              , $val );
/*  $val = str_replace( "!"            , "!"         , $val );
  $val = str_replace( "'"            , "'"         , $val );*/
  $val = str_replace( "'"            , "&#039;"         , $val );
  $val = preg_replace("/&amp;#([0-9]+);/s", "&#\\1;", $val );
  $val = stripslashes($val);
//  $val = preg_replace( "/\\\(?!&amp;#|?#)/", "\", $val );
  return $val;
}

function get_my_info()
{
	/* RETURN: Array( hash - id ) */
	global $std,$SNAME,$userdata;
	if(!$_SESSION[$SNAME]['sess_nick']) return "";
	if(!$userdata) return "";
	$KEY_DECRYPT=pack('H*',md5($_SESSION[$SNAME]['sess_nick'].$_SESSION[$SNAME]['sess_password'])); // = password per decriptare la chiave privata in localmember (16byte)
	$privkey=base64_decode($userdata->PASSWORD);
	
	$PKEY=$std->getpkey($SNAME);
	$req[FUNC][Base642Dec]=$PKEY;
	$req[FUNC][BlowDump2var][Key]=$KEY_DECRYPT;
	$req[FUNC][BlowDump2var][Data]=$privkey;
	$core=new CoreSock;
	if (!$core->Send($req)) return NULL; 
	if (!$risp=$core->Read()) return NULL; 
	$return[0]=$risp[FUNC]["BlowDump2var"]["hash"]; 	//dell'utente loggato in questo momento
	list($asd,$return[1]) = unpack('H*',$return[0]);
	return ($return);
}


function WhoIsMe()
 {
	global $SNAME,$db;

	list($user_hash,$user_id) = get_my_info($SNAME);

	$user_hash = mysql_real_escape_string($user_hash);
	$query = "SELECT *
			FROM {$SNAME}_membri where hash = '$user_hash'
			LIMIT 1";
	$Iam = $db->get_row($query);
	return $Iam;
}




// *** variabili usate ovunque ***
$forum_id = pack('H*',$config[SHARE][$SNAME][ID]);


CheckSession();

// *** DATI UTENTE ****
$userdata=$std->GetUserData($SNAME,$_SESSION[$SNAME]['sess_nick'],$_SESSION[$SNAME]['sess_password']);

// *** LINGUA UTENTE **
if($userdata->LANG)
{
	$blanguage=$userdata->LANG; // Lingua di visualizzazione
}
else 
{
	switch (substr(trim($HTTP_ACCEPT_LANGUAGE),0,2)) 
	{
		case "it":
			$blanguage="ita";
		break;
		case "fr":
			$blanguage="eng";
		break;
		case "de":
			$blanguage="eng";
		break;
		case "es":
			$blanguage="eng";
		break;
		case "en-us":
		case "en":
		default:
			$blanguage="eng";
		break;
	} 
}


?>
