<?php

include("lib/lib.php");


$MODO = $_REQUEST['MODO'];
$ORDER = $_REQUEST['ORDER'];
$LIMIT = $_REQUEST['LIMIT'];

if ( empty($MODO) or ($MODO > 4 or $MODO < 1 ) ) $std->Error("Invalid HashReq MODE!");
if ( empty($ORDER) ) $ORDER = "DESC";
if ( empty($LIMIT) ) $LIMIT = 200;

$PKEY=$std->getpkey($_ENV['sesname']);

if ( empty($MODO) or empty($PKEY) )
    $std->Error("Error on request!\n");
else {
    $coreconn = new CoreSock;
    
    $convreq['FUNC']['Base642Dec'] = $PKEY;
    if ( !$coreconn->Send($convreq) ) $std->Error("Error sending request to core!\n");
    $pkeydec=$coreconn->Read();
    if ( !$pkeydec ) $std->Error("Error receiving core data!\n");
    $pkeysha1=sha1($pkeydec['FUNC']['Base642Dec']);
    $forumid=pack("H*",$pkeysha1);
    
    $hashreq['HASHREQ'][$forumid]['MODO'] = $MODO;
    $hashreq['HASHREQ'][$forumid]['LIMIT'] = $LIMIT;
    $hashreq['HASHREQ'][$forumid]['ORDER'] = $ORDER;
    if (!empty($_REQUEST['MAX_DATE'])) $hashreq['HASHREQ'][$forumid]['MAX_DATE'] = $_REQUEST['MAX_DATE'];
    if (!empty($_REQUEST['MIN_DATE'])) $hashreq['HASHREQ'][$forumid]['MIN_DATE'] = $_REQUEST['MIN_DATE'];

    switch ($MODO) {
    		case 1:	// MODO=1 search in threads
    				$SEZ = $_REQUEST['SEZ'];
    				if ( !empty($SEZ) and ($SEZ > 0) ) $hashreq['HASHREQ'][$forumid]['SEZ']=$SEZ;
    				if (!empty($_REQUEST['AUTORE'])) $hashreq['HASHREQ'][$forumid]['AUTORE'] = pack("H32",$_REQUEST['AUTORE']);
					if (!empty($_REQUEST['EDITOF'])) $hashreq['HASHREQ'][$forumid]['EDITOF'] = pack("H32",$_REQUEST['EDITOF']);
    				break;
    		case 2:	// MODO=2 reply search
    				if (!empty($_REQUEST['REPOF'])) $hashreq['HASHREQ'][$forumid]['REPOF'] = pack("H32",$_REQUEST['REPOF']);
    				if (!empty($_REQUEST['EDITOF'])) $hashreq['HASHREQ'][$forumid]['EDITOF'] = pack("H32",$_REQUEST['EDITOF']);
    				if (!empty($_REQUEST['AUTORE'])) $hashreq['HASHREQ'][$forumid]['AUTORE'] = pack("H32",$_REQUEST['AUTORE']);
					break;
    		case 3:
    				if (!empty($_REQUEST['TITLE'])) $hashreq['HASHREQ'][$forumid]['TITLE'] = $_REQUEST['TITLE'];
    				break;
    		case 4:	// MODO=4 ricerca in tutti i messaggi (amministrativi, utenti, thread, reply)
    				break;
    		default:
    				$std->Error("Unkown MODE, check your request.");
    }
    if ( !$coreconn->Send($hashreq) ) $std->Error("Error sending request to core!\n");
    else {
        $resp=$coreconn->Read();
        if ( !$resp ) $std->Error("Error reciving core data!\n");
        else $std->Redirect("Hash request",$_SERVER['HTTP_REFERER'],"Hash request","Request sent to " . $resp['HASHREQ'][$forumid] . " nodes");

    }
}
?>    