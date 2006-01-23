<?php

echo"
<head>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=windows-1252\">
<title></title>
</head>

<body bgcolor=\"#FFFFFF\">

<center>
";

include ("core.php");
include("langsupport.php");

// determino la lingua
if(!$_REQUEST['lang'])
{
$blanguage=GetUserLanguage();
} else {
$blanguage=$_REQUEST['lang'];
}

$lang = load_lang('lang_restart', $blanguage ); 

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

echo "<br><br><a href=\"javascript:window.close();\" >{$lang['close_page']}</a>";

echo "</center></body>";
?>