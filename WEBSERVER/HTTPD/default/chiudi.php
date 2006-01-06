<?php

include("testa.php");
$lang += $std->load_lang('lang_close', $blanguage );

$corereq = new CoreSock;

$req[CHIUDI]=1;

if ( !($corereq->Send($req)) )
    echo "".$lang['close_info1']."\n";
else echo "".$lang['close_info2']."\n";


echo " <br><br><a href=\"javascript:window.close();\" >".$lang['close_closew']."</a> "
?>