<?php

include "testa.php";

?>

<script language="JavaScript" type="text/javascript">
<!--
function  KeyRingPopUp(retform,retfield) {
      
      msgWindow=open('pkeyring.php?rm='+retform+'&rf='+retfield,'KeyRing','toolbar=no,location=no,scrollbars=yes,directories=no,status=yes,menubar=no,resizeable=yes,width=300,height=200');
      if (msgWindow.opener == null) msgWindow.opener = self;
}

//-->
</script>


<?php
$whereiam = "authusers";

if ( empty($_REQUEST['privkey']) ) {	// show hash list to auth
	echo "<form method=\"POST\" name=\"main\" action=\"authusers.php\">";
	echo "<table cellspacing=\"1\" align='center'>\n";
	echo "<tr><th class='darkrow2' align='center'>ID</th><th class='darkrow2' align='center'>Hash</th></tr>";
	while (list ($key, $userhash) = each ($_REQUEST['toauth'])) {
		if (strlen($userhash) != 32) $std->Error("Selected user hash (id $key) has wrong lenght!");
		echo "<tr><td class='row1' align='center'>$key</td><td class='row1' align='center'>$userhash</td></tr>\n";
		echo "<input type='hidden' name='toauth[$key]' value='$userhash'>";
	}
	echo "<tr><td class='row2'>Private Key<p align=\"center\"><a href=\"javascript:KeyRingPopUp('main','privkey')\">
  <img border=\"0\" src=\"img/keyring.gif\" width=\"32\" height=\"32\"></a></p></td><td><textarea cols=35 rows=5 name=\"privkey\"></textarea></td></tr>\n";
	echo "<tr><td class='row1'></td><td class='row1' align='center'><input type=submit class='button' name='submit'></td></tr>\n";
	echo "</table>";
	echo "</form>";
}
else {	// got privkey, auth'em!
	while (list ($key, $userhash) = each ($_REQUEST['toauth'])) {
		if (strlen($userhash) != 32) $std->Error("Selected user hash (id $key) has wrong lenght!");
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
	if ( !($coresk->Send($corereq)) ) $std->Error("Error sending data to the core!");
	$coreresp = $coresk->Read();
	
	// signed hash goes with his sign to build admin command array
	foreach ($tosign['HASH'] as $key2 => $hash ) {
		if ( empty($coreresp['RSA']['FIRMA'][$hash]) ) $std->Error("Core didn't sign hash $hash, aborting!");
		$mem['HASH']=$hash;
		$mem['AUTH']=$coreresp['RSA']['FIRMA'][$hash];
		$command[AuthMem][$key2] = $mem;
	}

	if ( strlen($coreresp['FUNC']['Base642Dec']) < 120 ) $std->Error("Error, forum public key invalid!");
	$PKEY = $coreresp['FUNC']['Base642Dec'];
	$date = $coreresp['CORE']['INFO']['GMT_TIME'];
	
	unset($corereq,$coreresp);
	
	// we need the bindump of the command, ask the core to do it
	$corereq[TMPVAR][ADDVAR][session_id()] = $command;
	
	if ( !($coresk->Send($corereq)) ) $std->Error("Error sending data to the core!");
	$coresk->Read();
	unset($corereq);
	
	$corereq[TMPVAR][BINDUMP] = session_id();
	if ( !($coresk->Send($corereq)) ) $std->Error("Error sending data to the core!");
	$coreresp = $coresk->Read();
	if ( empty($coreresp[TMPVAR][BINDUMP]) ) $std->Error("Error asking the BinDump of the command to the core.");
	
	// bindump of the admin command in base64 = $code is the COMMAND to send to the core
	$code = base64_encode($coreresp[TMPVAR][BINDUMP]);
	unset($corereq,$coreresp);
	
	// admin message md5....
	$msg_md5 = pack("H32",md5($PKEY . $date . "User(s) auth" . $code));
	
	// ...and sign it
	$corereq['RSA']['FIRMA'][0]['md5'] = $msg_md5;
	$corereq['RSA']['FIRMA'][0]['priv_pwd'] = base64_decode($_REQUEST['privkey']);
	$corereq[TMPVAR][DELVAR] = session_id();
	
	if ( !($coresk->Send($corereq)) ) $std->Error("Error sending data to the core!");
	$coreresp = $coresk->Read();
	
	if ( empty($coreresp['RSA']['FIRMA'][$msg_md5]) ) $std->Error("Error signing admin command.");
	unset($corereq);	
	
	$corereq['FORUM']['ADDMSG']['SIGN'] = $coreresp['RSA']['FIRMA'][$msg_md5];
	unset($coreresp);
	$corereq['FORUM']['ADDMSG']['MD5'] = $msg_md5;
	$corereq['FORUM']['ADDMSG']['DATE'] = $date;
	$corereq['FORUM']['ADDMSG']['TITLE'] = "User(s) auth";
	$corereq['FORUM']['ADDMSG']['COMMAND'] = $code;
	$corereq['FORUM']['ADDMSG']['TYPE'] = '3';
	$corereq['FORUM']['ADDMSG']['FDEST'] = sha1($PKEY,TRUE);
	
	if ( !($coresk->Send($corereq)) ) $std->Error("Error sending data to the core!\n");
	$coreresp = $coresk->Read();
	if ( !$coreresp ) $std->Error("Error receiving response form the core!");
	if ( $coreresp['FORUM']['ADDMSG'] == -2 ) $std->Error("Forum unknown, cannot auth user(s).");
	if ( $coreresp['FORUM']['ADDMSG'] == -1 ) $std->Error("The Core didn't accept the message, aborting.");
	if ( $coreresp['FORUM']['ADDMSG'] == 1 ) $std->Error("","","User(s) authed, let the spam begin.");
	// yes, the end!
}	

include("end.php");

?>