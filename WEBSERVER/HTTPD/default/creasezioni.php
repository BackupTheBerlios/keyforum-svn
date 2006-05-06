<?php
require ("testa.php");
require("admin.php");

include("pkeytemp.php");

$admin=new Admin(base64_decode($PRIVKEY));
$core=new CoreSock;

include("listasezioni.php");

$risp=$admin->Send2Core("modifico la sezione");

if($risp[ERRORE])
{
$std->Error($risp[ERRORE]);
} else {
$std->Error("","","comando eseguito...");
}

include ("end.php");

?>