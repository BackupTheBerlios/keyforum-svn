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
	// input box per priv key, submit
	echo "<tr><td><textarea cols=35 rows=5 name=\"privkey\"></textarea></td></tr>\n";
	echo "<tr><td class='row1' align='center'><input type=submit name='submit'></td></tr>\n";
	echo "</table>";
	echo "</form>";
}
else {	// got privkey, auth'em!
	//var_dump($_REQUEST['toauth']);
	while (list ($key, $userhash) = each ($_REQUEST['toauth'])) {
		if (strlen($userhash) != 32) die ("Selected user hash (id $key) has wrong lenght!");
		//$command['AuthMem']['HASH'][] = $userhash;
		$tosign['HASH'][] = pack("H32",$userhash);
	}

	// TODO: return Warning("Deve essere in formato esadecimale l'HASH membro.") if $memhash=~ /[^a-fA-F0-9]/i;
	//var_dump($command);
	//$title = "User(s) auth";		//admin message title, optional
	
	//$i=0;
	//reset($command);
	reset($tosign);
	
	foreach ( $tosign['HASH'] as $key1 => $hash ) {
		$corereq['RSA']['FIRMA'][$key1]['md5'] = $hash;
		$corereq['RSA']['FIRMA'][$key1]['priv_pwd'] = base64_decode($_REQUEST['privkey']);
	}
	//var_dump($corereq);
	$PKEY64 = $std->getpkey($SNAME);
	$corereq['FUNC']['Base642Dec'] = $PKEY64;
	$coresk = new CoreSock;
	if ( !($coresk->Send($corereq)) ) die("Error sending data to the core!\n");
	$coreresp = $coresk->Read();
	//var_dump($coreresp);
	//if ( empty($coreresp['RSA']['FIRMA'][$hash]) ) die($coreresp['RSA']['FIRMA']["ERR" . $hash]);
	foreach ($tosign['HASH'] as $key2 => $hash ) {
		if ( empty($coreresp['RSA']['FIRMA'][$hash]) ) die("Core didn't sign hash $hash, aborting!\n");
		//$tosign['AUTH'][$key2] = $coreresp['RSA']['FIRMA'][$hash];
		//$command['AuthMem']['AUTH'][] = $coreresp['RSA']['FIRMA'][$hash];
		//$hex_hash = unpack("H32",$hash);
		//if (strlen($hex_hash[1]) != 32) die("Error unpacking user hash!\n");
		$mem['HASH']=$hash;
		$mem['AUTH']=$coreresp['RSA']['FIRMA'][$hash];
		$command['AuthMem'][$key2] = $mem;
		//$command['AuthMem'][$key2]['AUTH'] = $coreresp['RSA']['FIRMA'][$hash];
	}
	echo "vardump :" . var_dump($command);
	if ( strlen($coreresp['FUNC']['Base642Dec']) < 120 ) die("Error, forum public key invalid!\n");
	$PKEY = $coreresp['FUNC']['Base642Dec'];
	$date = $coreresp['CORE']['INFO']['GMT_TIME'];
	
	unset($corereq,$coreresp);
	
	$code = base64_encode($std->var2binary($command));
	//my $md5=Digest::MD5::md5($ENV{PKEY}.$MSG{'DATE'}.$MSG{'TITLE'}.$MSG{'COMMAND'});
	//echo var_dump($code) . "<br><br>" . var_dump($command);
		
	$msg_md5 = pack("H32",md5($PKEY . $date . "User(s) auth" . $code));
	
	$corereq['RSA']['FIRMA'][0]['md5'] = $msg_md5;
	$corereq['RSA']['FIRMA'][0]['priv_pwd'] = base64_decode($_REQUEST['privkey']);
	
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
	//echo "Errore: " . $coreresp['FORUM']['ADDMSG']['ERRORE'] . "<br>";
	if ( !$coreresp ) die("Error receiving response form the core!");
	if ( $coreresp['FORUM']['ADDMSG'] == -2 ) die("Forum unknown, cannot auth user(s).");
	if ( $coreresp['FORUM']['ADDMSG'] == -1 ) die("The Core didn't accept the message, aborting.");
	if ( $coreresp['FORUM']['ADDMSG'] == 1 ) echo "Admin commands executed.<br><br>";
}	

include("end.php");

?>