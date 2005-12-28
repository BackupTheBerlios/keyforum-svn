<?php

function get_sign($mem_id)
{
	global $SNAME;
	$user_hash = @pack("H*",$mem_id);
	$user_hash = mysql_real_escape_string($user_hash);
	
	$query = "SELECT firma
		FROM {$SNAME}_membri where hash = '$user_hash'
		LIMIT 1";
	$result = mysql_query($query) or die(mysql_error() . "<br>$query");
	list($firma) = mysql_fetch_array($result);
	return $firma;
}

function get_avatar($mem_id)
{
	global $SNAME;
	$user_hash = @pack("H*",$mem_id);
	$user_hash = mysql_real_escape_string($user_hash);
	
	$query = "SELECT avatar
		FROM {$SNAME}_membri where hash = '$user_hash'
		LIMIT 1";
	$result = mysql_query($query) or die(mysql_error() . "<br>$query");
	list($avatar) = mysql_fetch_array($result);
	return $avatar;
}


function show_menu()
{
	global $mem_id;
	return '
	<div class="borderwrap">
			<div class="maintitle">Menu</div>
			<!-- Messenger Links -->
			<table cellspacing="1" width="100%">
			 <tr>
			  <td><div class="formsubtitle">Messenger</div>
			<!--<p>
				<img src="img/nav_m_dark.gif" border="0"  alt="." /> <a href="index.php?act=Msg&amp;CODE=04"><b>Invia nuovo PVT</b></a><br />
				&nbsp;&nbsp; <img src="img/msg_folder.gif" border="0"  alt="-" />&nbsp;<a href="index.php?act=Msg&amp;CODE=01&amp;VID=in">Inbox (13)</a><br />&nbsp;&nbsp; <img src="img/msg_folder.gif" border="0"  alt="-" />&nbsp;<a href="index.php?act=Msg&amp;CODE=01&amp;VID=sent">Sent Items</a><br />
				&nbsp;&nbsp; <img src="img/msg_folder.gif" border="0"  alt="-" />&nbsp;<a href="index.php?act=Msg&amp;CODE=20">PVT Salvati (Non-Spediti)</a><br />
				<img src="img/nav_m_dark.gif" border="0"  alt="." /> <a href="index.php?act=Msg&amp;CODE=delete">Svuota Cartelle PVT</a><br />
				<img src="img/nav_m_dark.gif" border="0"  alt="." /> <a href="index.php?act=Msg&amp;CODE=07">Modifica Cartelle Archivio</a><br />
				<img src="img/nav_m_dark.gif" border="0"  alt="." /> <a href="index.php?act=Msg&amp;CODE=02">Rubrica Personale</a><br />
				<img src="img/nav_m_dark.gif" border="0"  alt="." /> <a href="index.php?act=Msg&amp;CODE=14">Archivio Messaggi</a><br />
				<img src="img/nav_m_dark.gif" border="0"  alt="." /> <a href="index.php?act=Msg&amp;CODE=30">Traccia Messaggi</a><br />
			</p> -->
	 	<!-- End Messenger -->
                          </td>
                         </tr>
		<!-- Topic Tracker -->
                         <tr>
			  <td><div class="formsubtitle">Sottoscrizioni</div>
		<!--<p>
				<img src="img/nav_m_dark.gif" border="0"  alt="." /> <a href="index.php?act=UserCP&amp;CODE=26">Iscrizioni Discussioni</a><br />
				<img src="img/nav_m_dark.gif" border="0"  alt="." /> <a href="index.php?act=UserCP&amp;CODE=50">Iscrizioni Forum</a><br />
			</p>-->
                          </td>
                         </tr>
		<!-- Profile -->
                         <tr>
			  <td><div class="formsubtitle">Profilo Personale</div>
			<p>
				<img src="img/nav_m_dark.gif" border="0"  alt="." /> 
				<a href="options_user.php?MEM_ID='.$mem_id.'">Modifica Profilo</a><br />
				<img src="img/nav_m_dark.gif" border="0"  alt="." /> 
				<a href="options_sign.php?MEM_ID='.$mem_id.'">Modifica Firma</a><br />
				<img src="img/nav_m_dark.gif" border="0"  alt="." />
				<a href="options_avatar.php?MEM_ID='.$mem_id.'">Modifica Avatar</a><br />
				<img src="img/nav_m_dark.gif" border="0"  alt="." /> 
				<a href="">Cambia Foto Personale</a><br />
			</p>
                          </td>
                         </tr>
		<!-- Options -->
                         <tr>
			  <td><div class="formsubtitle">Opzioni</div>
			<p>
				<!--IBF.OPTION_LINKS
				<img src="img/nav_m_dark.gif" border="0"  alt="." /> 
				<a href="">Gestione Allegati</a><br />
				<img src="img/nav_m_dark.gif" border="0"  alt="." /> 
				<a href="">Gestisci Utenti Bloccati</a><br />
				<img src="img/nav_m_dark.gif" border="0"  alt="." /> 
				<a href="">Impostazioni Email</a><br />
				<img src="img/nav_m_dark.gif" border="0"  alt="." /> 
				<a href="">Impostazioni Forum</a><br />
				<img src="img/nav_m_dark.gif" border="0"  alt="." /> 
				<a href="">Cambia Indirizzo Email</a><br />
				<img src="img/nav_m_dark.gif" border="0"  alt="." /> 
				<a href="">Cambia Password</a> -->
			</p>
                          </td>
                         </tr>
                        </table>
		</div>';
}

function show_private_form($data)
{
	$return .= '<form method="post" action="">
	<table cellspacing="0" align="center" border="1">
	<tr>
		<td class="row2">Private key</td>
		<td align="center">
			<textarea cols="35" rows="5" name="privkey"></textarea>';			
	
	foreach($data as $key=>$value)
	{
		$value = stripslashes($value);
		if($key != 'privkey')
		$return .= "<input type='hidden' name='$key' value='$value'>";
	}
	$return .= '</td></tr>
	<tr><td class="row1" align="center" colspan="2"><input type="submit" class="button" name="submit"></td></table></form>';
	return $return;
}