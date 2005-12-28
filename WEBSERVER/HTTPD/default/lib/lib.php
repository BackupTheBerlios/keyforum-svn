<?php

  $Timer1 = microtime();
  $Timer1 = explode(" ",$Timer1);
  $Timer1 = $Timer1[0] + $Timer1[1];

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
$_ENV['sql_host']=$settings['root']['conf']['DB']['host'];
$_ENV['sql_user']=$settings['root']['conf']['DB']['dbuser'];
$_ENV['sql_passwd']=$settings['root']['conf']['DB']['dbpassword'];
$_ENV['sql_dbname']=$settings['root']['conf']['DB']['dbname'];
$_ENV['sql_dbport']=$settings['root']['conf']['DB']['dbport'];



if(!$_ENV['sql_dbport']){$_ENV['sql_dbport']="3306";}

$SQL = mysql_pconnect($_ENV['sql_host'].":".$_ENV['sql_dbport'],$_ENV['sql_user'],$_ENV['sql_passwd']);
if (!$SQL) die ("Non riesco a connettermi al server MySQL");
if ( !mysql_select_db($_ENV['sql_dbname']) ) die("Impossibile aprire il DataBase MySQL:".mysql_error()."</br>");
define ("SID", session_id());
define ("USID", "PHPSESSID=".session_id());
define ("GMT_TIME", 3600*1);

    $porta=$_SERVER['SERVER_PORT'] ;
    $query2="SELECT subkey FROM config WHERE fkey='PORTA' AND VALUE='$porta' LIMIT 1";
    $risultato2 = mysql_query($query2) or Muori ("Query non valida: " . mysql_error());
    $riga2 = mysql_fetch_assoc($risultato2);
    $query="SELECT value FROM config WHERE fkey='SesName' AND SUBKEY='".$riga2['subkey']."'";
    $risultato = mysql_query($query) or Muori ("Query non valida: " . mysql_error());
    $riga = mysql_fetch_assoc($risultato);
    if($riga['value']){
         $_ENV['sesname']=$riga['value'];
    } else {
          $_ENV['sesname']=$riga2['subkey'];
      }

if (!$_ENV['sesname']) {
	print "Nessuna board assegnata a questo webserver.\n";
	exit();
}
# Controllo se la sessione � stata registrata dallo stesso che ne ha fatto richiesta
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
  $query="SELECT NICK,PASSWORD FROM session WHERE SESSID='".session_id()."' AND IP=md5('".$_SERVER['REMOTE_ADDR']."') AND FORUM='".$_ENV['sesname']."';";
  $risultato = mysql_query($query) or Muori ("Query non valida: " . mysql_error());
  if ($riga = mysql_fetch_assoc($risultato)) {
      $GLOBALS['sess_nick'] = $riga["NICK"];
      $GLOBALS['sess_password'] = $riga["PASSWORD"];
      $GLOBALS['sess_auth'] = 1;
  } else {
      
      $GLOBALS['sess_nick'] = "";
      $GLOBALS['sess_password'] = "";
      $GLOBALS['sess_auth'] = 0;
  }

}

function DestroySession() {

$query="DELETE FROM `session` WHERE `SESSID`='".session_id()."' AND IP=md5('".$_SERVER['REMOTE_ADDR']."') AND FORUM='".$_ENV['sesname']."';";
mysql_query($query) or Muori ("Query non valida: " . mysql_error());
}

function Muori($errore) {
  echo $errore;
  exit(0);
}
function GetLastMsg($sezid) {
	$query="SELECT (msghe.last_reply_time+".GMT_TIME.") AS 'time_action', newmsg.TITLE AS 'TITLE', membri.AUTORE AS 'nick', membri.HASH AS 'nickhash', newmsg.EDIT_OF AS 'hash',newmsg.SEZ AS 'SEZID'"
	." FROM ".$_ENV['sesname']."_msghe AS msghe, ".$_ENV['sesname']."_membri AS membri,".$_ENV['sesname']."_newmsg AS newmsg"
	." WHERE msghe.HASH=newmsg.EDIT_OF"
	." AND msghe.last_reply_author=membri.HASH"
	." AND newmsg.visibile='1'"
	." AND newmsg.SEZ='".$sezid."'"
	." ORDER BY msghe.last_reply_time DESC"
	." LIMIT 1;";
	$risultato=mysql_query($query) or Muori ("Query non valida: " . mysql_error());
	return(mysql_fetch_assoc($risultato));
}
function Ip2Num ($ip) {
	$ip=explode('.',$ip);
	$ip=unpack("Iip",pack("CCCC",$ip[0],$ip[1],$ip[2],$ip[3]));
	# Sembra che in unpack, la I interger senza segno e con segno diano lo stesso risultato.
	# Per questo motivo in basso c'� l'if che controlla
	if ($ip[ip]<0) return($ip[ip]+pow(2,32)); else return($ip[ip]);
}
function Num2Ip ($ip) {
	return implode('.', unpack("C4",pack("I",$ip+0)));
}

mysql_query("update `session` set `DATE`='".time()."' where `SESSID`='".session_id()."' AND IP=md5('".$_SERVER['REMOTE_ADDR']."') AND FORUM='".$_ENV['sesname']."';");
if (rand(0,10)<1) mysql_query("DELETE FROM `session` WHERE `DATE`<'".(time()-3600)."';");
if ($_REQUEST['SEZID']) {
  $risultato=mysql_query("SELECT * FROM ".$_ENV['sesname']."_sez WHERE ID='".mysql_escape_string($_GET['SEZID'])."';");
  if($ris=mysql_fetch_assoc($risultato)) {
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
	$privkey=base64_decode($userdata['PASSWORD']);
	$PKEY=$std->getpkey($SNAME);
	$req[FUNC][Base642Dec]=$PKEY;
	$req[FUNC][BlowDump2var][Key]=$KEY_DECRYPT;
	$req[FUNC][BlowDump2var][Data]=$privkey;
	$core=new CoreSock;
	if (!$core->Send($req)) die($lang['reply_core']);
	if (!$risp=$core->Read()) die ($lang['reply_timeout']);
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

?>