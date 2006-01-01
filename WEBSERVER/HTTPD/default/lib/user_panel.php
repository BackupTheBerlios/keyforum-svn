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
	global $mem_id,$whereiam;
	//Modifica qui il menu
	
	/*$menu['Messenger'] = Array(
		 'pm_new.php' => 'Invia nuovo PVT'
		,'pm_inbox.php' => 'Messaggi Ricevuti'
		,'pm_sent.php' => 'Messaggi Inviati'
		);*/
		
	$menu['Profilo Personale'] = Array(
		'options_profile.php'	=> 'Modifica Profilo(Non funzia)'
		,'options_sign.php' => 'Modifica Firma(Non funzia)'
		,'options_avatar.php' => 'Modifica Avatar(Non funzia)'
		);
			
	$menu['Opzioni'] = Array(
		'options_forum.php' => 'Impostazioni Forum'
			);
	
	$return ='
		<div class="borderwrap">
			<div class="maintitle">Menu</div>
			<!-- Messenger Links -->
			<table cellspacing="1" width="100%">';
	foreach($menu as $title=>$submenu)
	{
		$return .="
		<!-- Start $title -->
		<tr>
			<td>
				<div class='formsubtitle'>$title</div>
				<p>";
		foreach($submenu as $url=>$label)
		{
			if($whereiam==$url)
			{
				$return .="
				<img src='img/nav_m_dark.gif' border='0'  alt='.' />
				<b>$label</b><br />\n";
			}
			else
			{
				$return .="
				<img src='img/nav_m_dark.gif' border='0'  alt='.' />
				<a href='$url?MEM_ID=$mem_id'>$label</a><br />\n";
			}
		}
		$return .="
			<!-- End $title -->
				</p></td></tr>\n";
	}
	$return .="</table></div>";
	return $return;
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