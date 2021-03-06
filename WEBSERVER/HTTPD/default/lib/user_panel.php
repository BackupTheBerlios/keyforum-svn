<?php
function get_language_list()
{
	global $std,$blanguage;
	$lang = $std->load_lang('lang_language', $blanguage );
	
	$dir_open = @ opendir('lang');
	if (! $dir_open)
		return 'Error to opern lang dir';
	while (($file = readdir($dir_open)) !== false) 
	{
		if(strpos($file,".") === FALSE)
		{
			$return[$file] = $lang[$file];
		}
	}
	closedir($dir_open);
	return $return;
}

function get_level_list()
{
	global $std,$blanguage;
	
	$level[0] = 'Utente';
	$level[1] = 'Moderatore';
	/*...*/
	$level[9] = 'Validatore';
	$level[10] = 'Admin';
	$level[11] = 'Mantainer';
	return $level;
}

function get_sign($mem_id)
{
	global $SNAME,$db;
	$user_hash = @pack("H*",$mem_id);
	$user_hash = substr($user_hash,0,16); //BUG: PLS FIX ME (17 caratteri, aggiunge uno 0 :s )
	$user_hash = mysql_real_escape_string($user_hash);
	
	$query = "SELECT firma
		FROM {$SNAME}_membri where hash = '$user_hash'
		LIMIT 1";
	$firma = $db->get_var($query);
	return $firma;
}

function get_avatar($mem_id)
{
	global $SNAME,$db;
	$user_hash = @pack("H*",$mem_id);
	$user_hash = substr($user_hash,0,16); //BUG: PLS FIX ME (17 caratteri, aggiunge uno 0 :s )
	$user_hash = mysql_real_escape_string($user_hash);
	
	$query = "SELECT avatar
		FROM {$SNAME}_membri where hash = '$user_hash'
		LIMIT 1";
	$avatar = $db->get_var($query);
	return $avatar;
}


function show_menu()
{
	global $mem_id,$whereiam,$std,$blanguage;
	
	//PER TRADUZIONI
	$lang = $std->load_lang('lang_userpanel', $blanguage );
	
	//Modifica qui il menu
	
	/*$menu['Messenger'] = Array(
		 'pm_new.php' => 'Invia nuovo PVT'
		,'pm_inbox.php' => 'Messaggi Ricevuti'
		,'pm_sent.php' => 'Messaggi Inviati'
		);*/
		
	$menu[$lang['usrpnl_pnlprofile']] = Array(
		'options_profile.php'	=> $lang['usrpnl_profilemod']
		,'options_sign.php' 	=> $lang['usrpnl_signaturemod']
		,'options_avatar.php' 	=> $lang['usrpnl_avatarmod'] 
		);
			
	$menu[$lang['usrpnl_options']] = Array(
		 'options_forum.php' 	=> $lang['usrpnl_forumopt']
		,'options_emoticons.php' => $lang['usrpnl_emoopt']
		,'options_account.php' => $lang['usrpnl_accountopt']
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

function Success_Page($title,$msg,$url=NULL,$exit=NULL)
{
	global $std,$blanguage,$PHP_SELF;
	$url = ($url === NULL ? $PHP_SELF.'?'.$QUERY_STRING: $url);

	$lang = $std->load_lang('lang_functions', $blanguage );

	$return ="
		<link type='text/css' rel='stylesheet' href='style_page_redirect.css'>
		<script type='text/javascript' language='javascript'>
			 setTimeout(\"location.href='$url'\",1000);
		</script>
		<div id='redirectwrap'>
			<h4>$title</h4>
			<p>$msg</p>
			<p class='redirectfoot'>{$lang['redirect_wait']}<br>(<a href='$url'>{$lang['redirect_invite']}</a>)</p>
		</div>
		";
	echo $return;
	if($exit) exit();
}
function is_valid($pkey) //TO DISCOVER
{
	if($pkey) return true;
	else return false;
}
?>