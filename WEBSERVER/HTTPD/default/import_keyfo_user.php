<?php

include("testa.php");

if (!$_SESSION[$SNAME]['sess_auth']) $std->Error ("Fare il login prima di convertire l'utente");

$IDENTIFICATORE=md5($_SESSION[$SNAME]['sess_password'].$_SESSION[$SNAME]['sess_nick']); // = identificatore dell'utente nella tabella localmember. easadecimale
$KEY_DECRYPT=pack('H*',md5($_SESSION[$SNAME]['sess_nick'].$_SESSION[$SNAME]['sess_password']));// = password per decriptare la chiave privata in localmember (16byte)
$query="SELECT PASSWORD FROM ".$SNAME."_localmember WHERE HASH='$IDENTIFICATORE';";
$password = $db->get_var($query);
if(!$password)
{
	$std->Error("niente password");
	
}
else
{
	$privkey=base64_decode($password);
}

$req[FUNC][BlowDump2var][Key]=$KEY_DECRYPT;
$req[FUNC][BlowDump2var][Data]=$privkey;
$core=new CoreSock;
if (!$core->Send($req)) $std->Error("Error sending data to the core");
if (!$risp=$core->Read()) $std->Error ("Error receiving data from the core");

$oldprivkey=$risp[FUNC][BlowDump2var];
if ( !$oldprivkey ) $std->Error("Error receiving data from the core, aborting.");

$query="SELECT hash FROM {$SNAME}_membri WHERE PKEYDEC='" . $oldprivkey['private']['_n'] ."';";
$userhash = $db->get_var($query);

if ( empty($userhash) ) $std->Error("User not found!");

$oldprivkey[hash]=$userhash;
$req2[FUNC][var2BlowDump64][Key]=$KEY_DECRYPT;
$req2[FUNC][var2BlowDump64][Data]=$oldprivkey;

//var_dump($req2);
//echo "<br><br><br>\n";

if ( !$core->Send($req2) ) $std->Error("Timeout sending data to the core, aborting.");
$resp2=$core->Read();

if ( !$resp2[FUNC][var2BlowDump64] ) $std->Error("Error receiving data from the core, aborting.");
$finalpkey64=$resp2[FUNC][var2BlowDump64];

echo "Updating user into the local members table... ";
	$sqladd = "UPDATE {$SNAME}_localmember SET password='" . mysql_real_escape_string($finalpkey64) .
		"' WHERE hash='$IDENTIFICATORE';";
    if ( !$db->query($sqladd) ) $std->Error("Error inserting updated private key");
    else $std->Error("","","User correctly converted !");


?>