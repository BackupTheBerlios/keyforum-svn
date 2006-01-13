<?php

include("testa.php");
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
    echo "".$lang['close_info1']."\n";
else echo "".$lang['close_info2']."\n";


echo " <br><br><a href=\"javascript:window.close();\" >".$lang['close_closew']."</a> "
?>