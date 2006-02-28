<?php
$whereiam='options_profile.php';

include_once("lib/lib.php");
if(is_array($lang)) { $lang += $std->load_lang('lang_optionsprofile', $blanguage );} else { $lang = $std->load_lang('lang_optionsprofile', $blanguage );}

$title = $lang['optprf_title'];

include("testa.php");
require('lib/user_panel.php');
include_once('lib/bbcode_parser.php');

if (!$_SESSION[$SNAME]['sess_auth']) {
    $url = "login.php";
    echo "<tr><td><center>".$lang['sign_login']."<br>";
    echo "".$lang['reply_loginred']."</center></td></tr><script language=\"javascript\">setTimeout('delayer()', 1500);\nfunction delayer(){ window.location='$url';}</script>";
    include ("end.php");
    exit(0);
  }

//AQUISIZIONE DATI
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
			<div class="maintitle">'.$lang['optprf_welcome'].'</div>'.
			show_private_form($_POST).
		'</div></td></tr></table>';
	}
	else
	{
		//Aquisizione dati
		//Controllo dati
		//todo
		echo "".$lang['optprf_reply']."";
	}
}


$query = "
	Select * 
	FROM {$SNAME}_localmember
	WHERE hash = '{$userdata->HASH}'
	LIMIT 1;
	";
$current = $db->get_row($query);

//TO DO




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
<? echo "				<div class=\"maintitle\">".$lang['optprf_welcome']."</div>"; ?>
	<form action="" method="post" name="REPLIER">
	<input type="hidden" name="MEM_ID" value="<?=$mem_id?>" />
<? echo "<div class=\"formsubtitle\">".$lang['optprf_prsnlprf']."</div>";?>
	<table cellspacing="0" align="center" width="100%">
	<tr>
		<? echo " <td class=\"pformleft\" >".$lang['optprf_birthdate']."</td>"; ?>
		<td class="pformright">
			<?=select_number('giorno',$current->giorno,31);?>
			<?=select_number('mese',$current->mese,12);?>
			<?=select_number('anno',$current->anno,date('Y')*-1);?>
	    </td>
	<tr>
		<? echo "<td class=\"pformleft\" >".$lang['optprf_homepage']."</td>"; ?>
		<td class="pformright">   
			<input type="text" name="homepage" value="<?=$current->homepage?>" size="40"/>
    	</td>
	</tr>
	<tr>
		<? echo "<td class=\"pformleft\" >".$lang['optprf_icq']."</td>"; ?>
		<td class="pformright">   
			<input type="text" name="icq" value="<?=$current->icq?>"  size="11"/>
    	</td>
	</tr>
	<tr>
		<? echo "<td class=\"pformleft\" >".$lang['optprf_msn']."</td>"; ?>
		<td class="pformright">   
			<input type="text" name="msn" value="<?=$current->msn?>"  size="11"/>
    	</td>
	</tr>
	<tr>
		<? echo "<td class=\"pformleft\" >".$lang['optprf_yahoo']."</td>"; ?>
		<td class="pformright">   
			<input type="text" name="yahoo" value="<?=$current->yahoo?>"  size="11"/>
    	</td>
	</tr>
	<tr>
		<? echo "<td class=\"pformleft\" >".$lang['optprf_aol']."</td>"; ?>
		<td class="pformright">   
			<input type="text" name="aol" value="<?=$current->aol?>"  size="11"/>
    	</td>
	</tr>
	<tr>
		<? echo "<td class=\"pformleft\" >".$lang['optprf_location']."</td>"; ?>
		<td class="pformright">   
			<input type="text" name="location" value="<?=$current->location?>" />
    	</td>
	</tr>
	<tr>
		<td class="formbuttonrow" colspan="2">
<? echo "			<input type=\"submit\" value=\"".$lang['optprf_update']."\" class=\"button\"/>"; ?>
		</td>
	</tr>
</table>
</form>
</div>
	<!-- end main CP area -->
		</td>
	</tr>
</table>


	</td></tr>

<?php 
} //end postback

include('end.php');?>



<?
function select_number($name,$default,$number)
{
	global $std,$blanguage;
	$lang = $std->load_lang('lang_register', $blanguage );

	$return .="
		<select name='$name'>
		<option value=''>--</option>
		";
	
	if($number > 0)
	{
		for($i=1;$i<=$number;$i++)
		{
			$selected = ($default == $i ? 'selected' : "");
			$return.= "<option value='$i' $selected >$i</option>\n";
		}
	}
	else
	{
		$number *= -1;
		for($i=$number,$number-=100;$i>=$number;$i--)
		{
			$selected = ($default == $i ? 'selected' : "");
			$return.= "<option value='$i' $selected >$i</option>\n";
		}
	}
	$return .="</select>";
	return $return;
}
?>
