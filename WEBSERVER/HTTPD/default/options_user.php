<?php
$whereiam='options_user.php';
$title = "Il tuo pannello di controllo";

include("testa.php");
require('lib/user_panel.php');
include_once('lib/bbcode_parser.php');

if (!$sess_auth) {
    $url = "login.php";
    echo "<tr><td><center>".$lang['sign_login']."<br>";
    echo "".$lang['reply_loginred']."</center></td></tr><script language=\"javascript\">setTimeout('delayer()', 1500);\nfunction delayer(){ window.location='$url';}</script>";
    include ("end.php");
    exit(0);
  }

//AQUISIZIONE DATI
$SNAME=$_ENV['sesname'];
$mem_id = $_GET['MEM_ID']; 			//dell'utente da modificare
$mem_hash = pack('H*',$mem_id); 
list($user_hash,$user_id) = get_my_info($SNAME);
$verify = ($user_id == $mem_id ? 1 : 0);

//POSTBACK PAGE
if($_POST['MEM_ID'])
{
	$is_post_back = 1;
	if(!$verify)
	{
		echo '<tr><td>
		<table cellspacing="0" width="100%">
		<tr>
		<td valign="top" class="nopad" width="24%">'.show_menu().'</td>
		<td class="nopad" width="1%"><!-- --></td>
		<!-- Start main CP area -->
		<td valign="top" class="nopad" width="75%">
			<div id="ucpcontent">
			<div class="maintitle">Benvenuto nel Pannelo di Controllo</div>'.
			show_private_form($_POST).
		'</div></td></tr></table>';
	}
	else
	{
		//Aquisizione dati
		$new_lang = $_POST['lang'];
		$new_tpp = $_POST['tpp'];
		$new_ppp = $_POST['ppp'];
		$new_hidesig =$_POST['hidesig'];
		//Controllo dati
		//todo
		
		$query = "UPDATE {$SNAME}_localmember 
		SET LANG = '$new_lang'
			,TPP = '$new_tpp' 
			,PPP = '$new_ppp'
			,HIDESIG ='$new_hidesig'
		WHERE HASH = '{$userdata['HASH']}'  LIMIT 1 ";
		$result = mysql_query($query) or die(mysql_error());
		if($result)
		{
			echo "modifiche avvenute con successo (magari)";
			$is_post_back= 0;
		}
		else
		{
			die('Errore imprevisto');
		}
	}
}


$query = "
	Select LANG, TPP, PPP, HIDESIG 
	FROM {$SNAME}_localmember
	WHERE hash = '{$userdata['HASH']}'
	LIMIT 1;
	";
$result = mysql_query($query);
$current = mysql_fetch_array($result);




//OUTPUT
if(!$is_post_back && $verify)
{
?><tr><td>
<table cellspacing="0" width="100%">
	<tr>
		<td valign="top" class="nopad" width="24%"><?=show_menu();?>
		</td>
		<td class="nopad" width="1%"><!-- --></td>
<!-- Start main CP area -->
		<td valign="top" class="nopad" width="75%">
			<div id="ucpcontent">
				<div class="maintitle">Benvenuto nel Pannelo di Controllo</div>
	<form action="" method="post" name="REPLIER">
	<input type="hidden" name="MEM_ID" value="<?=$mem_id?>" />
	<div class="formsubtitle">Impostazioni Internazionali</div>
	<table cellspacing="0" align="center" width="100%">
	<tr>
		<td class="pformleft" >Lingua</td>
		<td class="pformright">   
			<?=select_language('lang',$current['LANG'])?>
    </td>
	<tr>
		<td class="pformleft" >Ora del server</td>
		<td class="pformright">
			asd
	    </td>
	</tr>
</table>
<div class="formsubtitle">Impostazioni Forum</div>
<table cellspacing="0" align="center" width="100%">
	<tr>
		<td class="pformleft" >Numero di reply per pagina</td>
		<td class="pformright">
			<input type="text" size="10" name="ppp" value="<?=$current['PPP']?>" /> 
		</td>
	</tr>
	<tr>
		<td class="pformleft" >Numero di discussioni per pagina</td>
		<td class="pformright">
			<input type="text" size="10" name="tpp" value="<?=$current['TPP']?>"/> 
		</td>
	</tr>
	<tr>
		<td class="pformleft" >Mostra la firma del client</td>
		<td class="pformright">
			<? $checked = ($current['HIDESIG'] ? 'checked' : '');?>
			<input type="checkbox" name="hidesig" value='1' <?=$checked?> /> 
		</td>
	</tr>
	<tr>
		<td class="formbuttonrow" colspan="2">
			<input type="submit" value="Aggiorna" class="button"/>
		</td>
	</tr>

</form>
</div>
		</td>
	</tr>
</table>
			</div>
		</td>
	</tr>
</table>
	<!-- end main CP area -->

	</tr></td>

<?php 
} //end postback

include('end.php');?>



<?
function select_language($name,$default)
{
	global $std,$blanguage;
	$lang = $std->load_lang('lang_register', $blanguage );
	
	$dir_open = @ opendir('lang');
	if (! $dir_open)
		return 'Error to opern lang dir';
	
	$return .="<select name='$name'>";
	
	while (($file = readdir($dir_open)) !== false) 
	{
		if(strpos($file,".") === FALSE)
		{
			$selected = ($default == $file ? 'selected' : "");
			$return.= "<option value='$file' $selected >$file</option>\n";
			//$return.= "<option value='$file' $selected>{$lang[$file]}</option>\n";
		}
	}
	$return .="</select>";
	closedir($dir_open);
	return $return;
}

function is_valid($pkey) //TO DISCOVER
{
	if($pkey) return true;
	else return false;
}?>
