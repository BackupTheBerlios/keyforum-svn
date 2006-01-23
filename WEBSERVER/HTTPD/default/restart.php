<?php
include("testa.php");

// creo il file restart.txt
$curdir = getcwd();
list ($phpdir, $installdir) = spliti ('\\\WEBSERVER\\\HTTPD', $curdir);
$filename = "$phpdir/restart.txt";
$handle = fopen($filename, 'w');
fwrite($handle, "keyforum restarts if this file exist");
fclose($handle);


$lang += $std->load_lang('lang_close', $blanguage );
if($std->Multiutenza($SNAME)) 
{
	$std->Notice($lang[close_disabled_body],$lang[close_disabled_title]);
	include('end.php');
	die();
}

$corereq = new CoreSock;



$req[CHIUDI]=1;

if ( !($corereq->Send($req)) )
    {
    $std->Notice($lang['close_info1']);
    } else 
    {
    $std->Notice($lang['restart']);
    }

?>