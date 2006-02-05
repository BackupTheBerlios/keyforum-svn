<?php
require ("testa.php");
require("admin.php");
$PRIVKEY='{private key here}';

$admin=new Admin(base64_decode($PRIVKEY));
$core=new CoreSock;

# ordine figlio auth=1 richiesto_permesso=0
$admin->EditCat(1,'Generale','',1,0);

$risp=$admin->Send2Core("modifico la sezione");

if($risp[ERRORE])
{
$std->Error($risp[ERRORE]);
} else {
$std->Error("","","comando eseguito...");
}

include ("end.php");

?>