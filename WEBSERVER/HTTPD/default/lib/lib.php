<?php

  $Timer1 = microtime();
  $Timer1 = explode(" ",$Timer1);
  $Timer1 = $Timer1[0] + $Timer1[1];

$corecalls=0;

session_start();

//classe PEAR per file config (XML)
require_once "Config.php";
$xmldata = new Config;

$root =& $xmldata->parseConfig('http://'.$_SERVER['HTTP_HOST'].'/config/config.xml', 'XML');
if (PEAR::isError($root)) {
    die('Error reading XML config file: ' . $root->getMessage());
}

$settings = $root->toArray();

// dati del db
require_once('lib/ez_sql.php');
$_ENV['sql_host']=$settings['root']['conf']['DB']['host'];
$_ENV['sql_user']=$settings['root']['conf']['DB']['dbuser'];
$_ENV['sql_passwd']=$settings['root']['conf']['DB']['dbpassword'];
$_ENV['sql_dbname']=$settings['root']['conf']['DB']['dbname'];
$_ENV['sql_dbport']=$settings['root']['conf']['DB']['dbport'];

if(!$_ENV['sql_dbport']){$_ENV['sql_dbport']="3306";}
$db = new db($_ENV['sql_user'], $_ENV['sql_passwd'], $_ENV['sql_dbname'],$_ENV['sql_host'].":".$_ENV['sql_dbport']);

/*$SQL = mysql_pconnect($_ENV['sql_host'].":".$_ENV['sql_dbport'],$_ENV['sql_user'],$_ENV['sql_passwd']);
if (!$SQL) die ("Non riesco a connettermi al server MySQL");
if ( !mysql_select_db($_ENV['sql_dbname']) ) die("Impossibile aprire il DataBase MySQL:".mysql_error()."</br>");*/

$query = "SELECT * FROM config WHERE 1";
$result = $db->get_results($query);
foreach($result as $riga)
{
	$config[$riga->MAIN_GROUP][$riga->SUBKEY][$riga->FKEY] = $riga->VALUE;
}
//var_dump($config);


define ("SID", session_id());
define ("USID", "PHPSESSID=".session_id());
define ("GMT_TIME", 3600*1);

    $porta=$_SERVER['SERVER_PORT'] ;
	foreach($config['WEBSERVER'] as $nome=>$array)
	{
		if($array['PORTA'] == $porta)
		{
			$keyforum['porta'] = $porta;
			$keyforum['sesname'] = $array['SesName'];
			$keyforum['nome'] = $nome;
			$_ENV['sesname']= $array['SesName'];
			break;
		}
	}

    /*$subkey = $db->get_var("SELECT subkey FROM config WHERE fkey='PORTA' AND VALUE='$porta' LIMIT 1");
    $riga = $db->get_var("SELECT value FROM config WHERE fkey='SesName' AND SUBKEY='$subkey'");
    if($riga){
         $_ENV['sesname']=$riga;
    } else {
          $_ENV['sesname']=$subkey;
      }*/

if (!$_ENV['sesname']) {
	print "Nessuna board assegnata a questo webserver.\n";
	exit();
}
# Controllo se la sessione è stata registrata dallo stesso che ne ha fatto richiesta
# tramite l'IP di origine del browser.
#if (!isset($_SESSION['IP'])) {
#	session_unset();
#	$_SESSION['IP']=$_ENV["REMOTE_ADDR"];
#} else {
#	if ($_SESSION['IP'] != $_ENV["REMOTE_ADDR"]) session_unset();
#}

$GLOBALS['sess_nick'] = &$sess_nick;
$GLOBALS['sess_password'] = &$sess_password;
$GLOBALS['sess_auth'] = &$sess_auth;
$GLOBALS['SEZ_DATA'] = &$SEZ_DATA;

function CheckSession() {
	global $db;
	$result = $db->get_row("SELECT NICK,PASSWORD FROM session WHERE SESSID='".session_id()."' AND IP=md5('".$_SERVER['REMOTE_ADDR']."') AND FORUM='".$_ENV['sesname']."';");
   if ($result ) {
      $GLOBALS['sess_nick'] = $result->NICK;
      $GLOBALS['sess_password'] = $result->PASSWORD;
      $GLOBALS['sess_auth'] = 1;
  } else {
      
      $GLOBALS['sess_nick'] = "";
      $GLOBALS['sess_password'] = "";
      $GLOBALS['sess_auth'] = 0;
  }

}

function DestroySession() 
{
global $db;
	$sess_id = session_id();
	$my_ip = $_SERVER['REMOTE_ADDR'];
	$SNAME = $_ENV['sesname'];
	$query="DELETE FROM session WHERE SESSID='$sess_id' AND IP=md5('$my_ip') AND FORUM='$SNAME' ";
	$db->query($query);
}

function Muori($errore) {
  echo $errore;
  exit(0);
}
function GetLastMsg($sezid) {
	global $db;
	$query="SELECT (msghe.last_reply_time+".GMT_TIME.") AS 'time_action', newmsg.TITLE AS 'TITLE', membri.AUTORE AS 'nick', membri.HASH AS 'nickhash', newmsg.EDIT_OF AS 'hash',newmsg.SEZ AS 'SEZID'"
	." FROM ".$_ENV['sesname']."_msghe AS msghe, ".$_ENV['sesname']."_membri AS membri,".$_ENV['sesname']."_newmsg AS newmsg"
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

$db->query("update `session` set `DATE`='".time()."' where `SESSID`='".session_id()."' AND IP=md5('".$_SERVER['REMOTE_ADDR']."') AND FORUM='".$_ENV['sesname']."';");
if (rand(0,10)<1) $db->query("DELETE FROM `session` WHERE `DATE`<'".(time()-3600)."';");
if ($_REQUEST['SEZID']) 
{
	$tmp = mysql_escape_string($_GET['SEZID']);
	$ris = $db->get_row("SELECT * FROM {$_ENV['sesname']}_sez WHERE ID='$tmp)';");
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
	if(!$GLOBALS['sess_nick']) return "";
	if(!$userdata) return "";
	$KEY_DECRYPT=pack('H*',md5($GLOBALS['sess_nick'].$GLOBALS['sess_password'])); // = password per decriptare la chiave privata in localmember (16byte)
	$privkey=base64_decode($userdata->PASSWORD);
	$PKEY=$std->getpkey($SNAME);
	$req[FUNC][Base642Dec]=$PKEY;
	$req[FUNC][BlowDump2var][Key]=$KEY_DECRYPT;
	$req[FUNC][BlowDump2var][Data]=$privkey;
	$core=new CoreSock;
	if (!$core->Send($req)) return NULL; //die($lang['reply_core']);
	if (!$risp=$core->Read()) return NULL; // die ($lang['reply_timeout']);
	$return[0]=$risp[FUNC]["BlowDump2var"]["hash"]; 	//dell'utente loggato in questo momento
	list($asd,$return[1]) = unpack('H*',$return[0]);
	return ($return);
}


//--------------------------------
// Classe per funzioni globali
//--------------------------------
require "lib/functions.php";
require "lib/core.php";
$std   = new FUNC;

CheckSession();

// *** DATI UTENTE ****
$userdata=$std->GetUserData($_ENV["sesname"],$sess_nick,$sess_password);
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