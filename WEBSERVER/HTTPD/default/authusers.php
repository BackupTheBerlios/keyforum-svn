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
	echo '
		<form method="POST" name="main" action="">
			<table cellspacing="1" align="center">
				<tr>
					<th class="darkrow2" align="center">ID</th>
					<th class="darkrow2" align="center">Hash</th>
				</tr>';
	while (list ($key, $userhash) = each ($_REQUEST['toauth'])) 
	{
		if (strlen($userhash) != 32) $std->Error("Selected user hash (id $key) has wrong lenght!");
			echo "
				<tr>
					<td class='row1' align='center'>$key</td>
					<td class='row1' align='center'>
					<input type='hidden' name='toauth[$key]' value='$userhash'>
						$userhash
					</td>
				</tr>\n";
	}
		echo "
				<tr>
					<td class='row2'>
						Private Key
						<p align=\"center\">
							<a href=\"javascript:KeyRingPopUp('main','privkey')\">
								<img border=\"0\" src=\"img/keyring.gif\" width=\"32\" height=\"32\">
							</a>
						</p>
					</td>
					<td><textarea cols=35 rows=5 name=\"privkey\"></textarea></td>
				</tr>
				<tr>
					<td class='row1' align='center' colspan='2'>
						<input type='submit' class='button' name='submit'>
					</td>
				</tr>
			</table>
		</form>";
}
else {
	
	require_once("admin.php");
	require_once("core.php");
	
	$PRIVKEY = $_REQUEST['privkey'];

	$admin=new Admin(base64_decode($PRIVKEY));
	$core=new CoreSock;

	$forum_id = pack('H*',$config[SHARE][$SNAME][ID]);
	
	// got privkey, auth'em!
	while (list ($key, $userhash) = each ($_REQUEST['toauth'])) 
	{
		if (strlen($userhash) != 32) $std->Error("Selected user hash (id $key) has wrong lenght!");
		$member=pack("H*",$userhash);
		$admin->AuthMem($member);
	}

	// TODO: return Warning("Deve essere in formato esadecimale l'HASH membro.") if $memhash=~ /[^a-fA-F0-9]/i;
	
	$messaggio['BODY']=$core->Var2BinDump($admin->ReturnVar());
	$messaggio['TITLE']='Valido utenti';
	$messaggio['TYPE']=1;
	$messaggio['_PRIVATE']=base64_decode($PRIVKEY);
	$risp = $core->AddMsg($messaggio);
	switch($risp['ERRORE'])
	{
		case '104':
			$std->Error("Private key not valid!");		
		break;
		case -1: 
			$std->Error("The Core didn't accept the message, aborting.");
		break;
		case -2;
			$std->Error("Forum unknown, cannot auth user(s).");
		break;
		case NULL:
			$std->Error("","","User(s) authed, let the spam begin.");
		break;
		default:
			$std->Error("Error receiving response form the core!");
		break;
	// yes, the end!
	}
}	

include("end.php");

?>