<?php
$whereiam='options_forum.php';
$title = "Il tuo pannello di controllo";

include("testa.php");
$lang = $std->load_lang('lang_optionsforum', $blanguage );
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
			<div class="maintitle">'.$lang['optusr_welcome'].'</div>'.
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
		$new_hideavatar =$_POST['hideavatar'];
		$new_hideimg =$_POST['hideimg'];
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
			echo "".$lang['optusr_msg']."";
			$is_post_back= 0;
			/*
			$is_post_back= 1;
			$std->Redirect('Modifica impostazioni forum',$_SERVER['HTTP_REFERER'],$lang['optusr_msg'],$lang['optusr_msg']);
			exit();*/
		}
		else
		{
			die(''.$lang['optusr_errmsg'].'');
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
<? echo "				<div class=\"maintitle\">".$lang['optusr_welcome']."</div>"; ?>
	<form action="" method="post" name="REPLIER">
	<input type="hidden" name="MEM_ID" value="<?=$mem_id?>" />
<? echo "	<div class=\"formsubtitle\">".$lang['optusr_intopt']."</div>"; ?>
	<table cellspacing="0" align="center" width="100%">
	<tr>
<? echo "			<td class=\"pformleft\" >".$lang['optusr_lang']."</td>"; ?>
		<td class="pformright">   
			<?=select_language('lang',$current['LANG'])?>
    </td>
<!--	<tr>
<? echo "			<td class=\"pformleft\" >".$lang['optusr_time']."</td>"; ?>
		<td class="pformright">
			asd
	    </td>
	</tr>-->
</table>
<? echo "<div class=\"formsubtitle\">".$lang['optusr_bopt']."</div>"; ?>
<table cellspacing="0" align="center" width="100%">
	<tr>
		<td width="70%"><?=$lang['optusr_showsign']?></td>
		<td>
			<?=select_yn('hidesig',$current['HIDESIG'])?>
		</td>
	</tr>
	<tr>
		<td width="70%">Visualizzare le immagini che gli altri utenti inseriscono nei loro messaggi?</td>
		<td>
			<?=select_yn('hideimg',$current['HIDEIMG'])?>
		</td>
	</tr>
	<tr>
		<td width="70%">Visualizzare gli avatar degli altri utenti quando leggi i messaggi del forum?</td>
		<td>
			<?=select_yn('hideavatar',$current['HIDEAVATAR'])?>
		</td>
	</tr>

	<tr>
	<td width="70%"><?=$lang['optusr_ppp']?></td>
		<td>
			<?=select_num_step('ppp',$current['PPP'],10,5,40,5)?>
		</td>
	</tr>
	<tr>
		<td width="70%"><?=$lang['optusr_tpp']?></td>
		<td>
			<?=select_num_step('tpp',$current['TPP'],20,5,40,5)?>
		</td>
	</tr>
	<tr>
		<td class="formbuttonrow" colspan="2">
<? echo "			<input type=\"submit\" value=\"".$lang['optusr_update']."\" class=\"button\"/>"; ?>
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
function select_num_step($name,$current,$default,$min=5,$max=50,$step=5)
{
	$return .="<select name='$name'>";
	for($i=$min;$i<=$max;$i=$i+$step)
	{
		$selected = ($current == $i ? 'selected' : '');
		$return.= "<option value='$i' $selected >$i</option>\n";
	}
	$selected = ($current == $default ? 'selected' : '');
	$return .="<option value='$default' $selected >Usa Default Forum</option>";
	$return .="</select>";
	return $return;
}
function select_yn($name,$current)
{
	//Dal momento che stiamo nascondendo :D
	//0 YES
	//1	NO
	$string[0] = 'Si';
	$string[1] = 'No';
	$return .="<select name='$name'>";
	for($i=0;$i<=1;$i++)
	{
		$selected = ($current == $i ? 'selected' : '');
		$return.= "<option value='$i' $selected >{$string[$i]}</option>\n";
	}
	$return .="</select>";
	return $return;
	
}
function select_language($name,$default)
{
	global $std,$blanguage;
	$lang = $std->load_lang('lang_language', $blanguage );
	
	$dir_open = @ opendir('lang');
	if (! $dir_open)
		return 'Error to opern lang dir';
	
	$return .="<select name='$name'>";
	
	while (($file = readdir($dir_open)) !== false) 
	{
		if(strpos($file,".") === FALSE)
		{
			$selected = ($default == $file ? 'selected' : "");
			//$return.= "<option value='$file' $selected >$file</option>\n";
			$return.= "<option value='$file' $selected>{$lang[$file]}</option>\n";
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
