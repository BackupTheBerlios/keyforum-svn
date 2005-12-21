<?php

include "testa.php";

?>

<?php
$whereiam = "authusers";

if ( empty($_REQUEST['privkey']) ) {	// show hash list to auth
	echo "<form method=\"POST\" action=\"authusers.php\">";
	echo "<table cellspacing=\"1\" align='center'>\n";
	while (list ($key, $userhash) = each ($_REQUEST['toauth'])) {
		if (strlen($userhash) != 32) die ("Selected user hash (id $key) has wrong lenght!");
		echo "<tr><td class='row1' align='center'>$key</td><td class='row2' align='center'>$userhash</td></tr>\n";
		echo "<input type='hidden' name='toauth[$key]' value='$userhash'>";
	}
	echo "<tr><td><textarea cols=35 rows=5 name=\"privkey\"></textarea></td></tr>\n";
	echo "<tr><td class='row1' align='center'><input type=submit name='submit'></td></tr>\n";
	echo "</table>";
	echo "</form>";
}
else {	// got privkey, auth'em!
	while (list ($key, $userhash) = each ($_REQUEST['toauth'])) {
		if (strlen($userhash) != 32) die ("Selected user hash (id $key) has wrong lenght!");
		$tosign['HASH'][] = pack("H32",$userhash);
	}

	// TODO: return Warning("Deve essere in formato esadecimale l'HASH membro.") if $memhash=~ /[^a-fA-F0-9]/i;
	reset($tosign);
	
	// hash array to be signed by the core with forum private key
	foreach ( $tosign['HASH'] as $key1 => $hash ) {
		$corereq['RSA']['FIRMA'][$key1]['md5'] = $hash;
		$corereq['RSA']['FIRMA'][$key1]['priv_pwd'] = base64_decode($_REQUEST['privkey']);
	}

	$PKEY64 = $std->getpkey($SNAME);
	$corereq['FUNC']['Base642Dec'] = $PKEY64;
	$coresk = new CoreSock;
	if ( !($coresk->Send($corereq)) ) die("Error sending data to the core!\n");
	$coreresp = $coresk->Read();
	
	// signed hash goes with his sign to build admin command array
	foreach ($tosign['HASH'] as $key2 => $hash ) {
		if ( empty($coreresp['RSA']['FIRMA'][$hash]) ) die("Core didn't sign hash $hash, aborting!\n");
		$mem['HASH']=$hash;
		$mem['AUTH']=$coreresp['RSA']['FIRMA'][$hash];
		$command[AuthMem][$key2] = $mem;
	}

	if ( strlen($coreresp['FUNC']['Base642Dec']) < 120 ) die("Error, forum public key invalid!\n");
	$PKEY = $coreresp['FUNC']['Base642Dec'];
	$date = $coreresp['CORE']['INFO']['GMT_TIME'];
	
	unset($corereq,$coreresp);
	
	// we need the bindump of the command, ask the core to do it
	$corereq[TMPVAR][ADDVAR][session_id()] = $command;
	
	if ( !($coresk->Send($corereq)) ) die("Error sending data to the core!\n");
	$coresk->Read();
	unset($corereq);
	
	$corereq[TMPVAR][BINDUMP] = session_id();
	if ( !($coresk->Send($corereq)) ) die("Error sending data to the core!\n");
	$coreresp = $coresk->Read();
	if ( empty($coreresp[TMPVAR][BINDUMP]) ) die("Error asking the BinDump of the command to the core.\n");
	
	// bindump of the admin command in base64 = $code is the COMMAND to send to the core
	$code = base64_encode($coreresp[TMPVAR][BINDUMP]);
	unset($corereq,$coreresp);
	
	// admin message md5....
	$msg_md5 = pack("H32",md5($PKEY . $date . "User(s) auth" . $code));
	
	// ...and sign it
	$corereq['RSA']['FIRMA'][0]['md5'] = $msg_md5;
	$corereq['RSA']['FIRMA'][0]['priv_pwd'] = base64_decode($_REQUEST['privkey']);
	$corereq[TMPVAR][DELVAR] = session_id();
	
	if ( !($coresk->Send($corereq)) ) die("Error sending data to the core!\n");
	$coreresp = $coresk->Read();
	
	if ( empty($coreresp['RSA']['FIRMA'][$msg_md5]) ) die("Error signing admin command.\n");
	unset($corereq);	
	
	$corereq['FORUM']['ADDMSG']['SIGN'] = $coreresp['RSA']['FIRMA'][$msg_md5];
	unset($coreresp);
	$corereq['FORUM']['ADDMSG']['MD5'] = $msg_md5;
	$corereq['FORUM']['ADDMSG']['DATE'] = $date;
	$corereq['FORUM']['ADDMSG']['TITLE'] = "User(s) auth";
	$corereq['FORUM']['ADDMSG']['COMMAND'] = $code;
	$corereq['FORUM']['ADDMSG']['TYPE'] = '3';
	$corereq['FORUM']['ADDMSG']['FDEST'] = sha1($PKEY,TRUE);
	
	if ( !($coresk->Send($corereq)) ) die("Error sending data to the core!\n");
	$coreresp = $coresk->Read();
	if ( !$coreresp ) die("Error receiving response form the core!");
	if ( $coreresp['FORUM']['ADDMSG'] == -2 ) die("Forum unknown, cannot auth user(s).");
	if ( $coreresp['FORUM']['ADDMSG'] == -1 ) die("The Core didn't accept the message, aborting.");
	if ( $coreresp['FORUM']['ADDMSG'] == 1 ) echo "User(s) authed, let the spam begin.<br><br>";
	// yes, the end!
}	

include("end.php");

?>