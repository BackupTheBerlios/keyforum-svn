<?php

include("testa.php");

$MODO=$_REQUEST['MODO'];
$SEZ=$_REQUEST['SEZ'];
$ORDER=$_REQUEST['ORDER'];
$PKEY=$_ENV['pkey'];

if ( !$MODO || !$SEZ || !$ORDER || !$PKEY )
    die("Errore nella richiesta!\n");
else {
    $coreconn=new CoreSock;
    
    $convreq['FUNC']['Base642Dec']=$PKEY;
    if ( !$coreconn->Send($convreq) ) die("Errore nell'invio della richiesta al core!\n");
    $pkeydec=$coreconn->Read();
    if ( !$pkeydec ) die("Errore nella ricezione dati dal core!\n");
    $pkeysha1=sha1($pkeydec['FUNC']['Base642Dec']);
    $forumid=pack("H*",$pkeysha1);
    
    $hashreq['HASHREQ'][$forumid]['MODO']=$MODO;
    $hashreq['HASHREQ'][$forumid]['LIMIT']=100;
    $hashreq['HASHREQ'][$forumid]['SEZ']=$SEZ;
    $hashreq['HASHREQ'][$forumid]['ORDER']=$ORDER;
    
    if ( !$coreconn->Send($hashreq) ) die("Errore nell'invio della richiesta al core!\n");
    else {
        $resp=$coreconn->Read();
        if ( !$resp ) die("Errore nella ricezione dati dal core!\n");
        else print "<br>Richiesta inviata a " . $resp['HASHREQ'][$forumid] . " nodi.\n";
    }
}
?>    