<?php
require "functions.php";
include ("core.php");
include("testa.php");

echo"<center>";

$lang += load_lang('lang_restart', $blanguage ); 

// creo il file restart.txt
$curdir = getcwd();
list ($phpdir, $installdir) = spliti ('\\\WEBSERVER\\\HTTPD', $curdir);
$filename = "$phpdir/restart.txt";
$handle = fopen($filename, 'w');
fwrite($handle, "keyforum restarts if this file exist");
fclose($handle);

$corereq = new CoreSock;


$req[CHIUDI]=1;

if ( !($corereq->Send($req)) )
    {
    echo ($lang['core_problem']);
    } else 
    {
    echo ("<b>".$lang['restart']."</b>");
    }


echo "<br><br>".$lang['info1']."";

echo "</center></body>";
?>