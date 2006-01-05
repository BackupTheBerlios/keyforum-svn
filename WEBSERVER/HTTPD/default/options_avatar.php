<?php
$whereiam='options_avatar.php';
$title = "Il tuo pannello di controllo";

include("testa.php");
$lang += $std->load_lang('lang_optionsavatar', $blanguage );
require('lib/user_panel.php');
include_once('lib/bbcode_parser.php');

//AQUISIZIONE DATI
$SNAME=$_ENV['sesname'];
$mem_id = $_GET['MEM_ID']; 			//dell'utente da modificare
$mem_hash = pack('H*',$mem_id); 
list($user_hash,$user_id) = get_my_info($SNAME);

if (!$sess_auth) {
    $url = "login.php";
    echo "<tr><td><center>".$lang['sign_login']."<br>";
    echo "".$lang['reply_loginred']."</center></td></tr><script language=\"javascript\">setTimeout('delayer()', 1500);\nfunction delayer(){ window.location='$url';}</script>";
    include ("end.php");
    exit(0);
  }

//POSTBACK PAGE
if($_POST['MEM_ID'])
{
	$is_post_back = 1;
	if(($user_id != $_POST['MEM_ID']) && !is_valid($_POST['privkey']))
	{
		echo '<tr><td>
		<table cellspacing="0" width="100%">
		<tr>
		<td valign="top" class="nopad" width="24%">'.show_menu().'</td>
		<td class="nopad" width="1%"><!-- --></td>
		<!-- Start main CP area -->
		<td valign="top" class="nopad" width="75%">
			<div id="ucpcontent">
			<div class="maintitle">'.$lang['optavt_welcome'].'</div>'.
			show_private_form($_POST).
		'</div></td></tr></table>';
	}
	else
	{
		echo "".$lang['optavt_modsucc']."";
		$is_post_back= 0;
	}
}

$current_avatar = stripslashes(get_avatar($mem_id));

//Processing data
$current_avatar_nobb = convert(secure_v($current_avatar));


//OUTPUT
if(!$is_post_back)
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
			<? echo " <div class=\"maintitle\">".$lang['optavt_welcome']."</div>"; ?>
			<script type="text/javascript">
<!--
var url_input      = "<?=$current_avatar?>";
var remove_pressed = 0;
	function select_url(){
		restore_it();
	}
	
	function select_upload(){
		try{
			if ( document.creator.url_avatar.value != "" ) {
				url_input = document.creator.url_avatar.value;
			}
			document.creator.url_avatar.value = "";
		}
		catch(nourl){
			return true;
		}
	}
	
	function restore_it(){
		if (url_input != ""){
			document.creator.url_avatar.value = url_input;
			}
		}
	function checkform(){
		if ( remove_pressed != 1 ){
			return true;
		}
	
		else{
		<? echo"	fcheck = confirm(".$lang['optavt_removeavatar'].");"; ?>
			if ( fcheck == true ){
				return true;
			}
			else{
				return false;
				}
			}
	}
-->
</script>
<? echo " <div class=\"formsubtitle\">".$lang['optavt_avataropt']."</div>"; ?>
	<? echo" ".$lang['optavt_info1'].""; ?>
<? echo" <div class=\"formsubtitle\">".$lang['optavt_current']."</div>"; ?>
<div class=\"tablepad\" align=\"center\"><img src='<?=$current_avatar?>' border='0'  alt='' /><br /><? echo"".$lang['optavt_info2']."</div>"; ?>
<? echo" <div class=\"formsubtitle\">".$lang['optavt_preext']."</div>"; ?>
<form action="http://www.keyforum.net/forum/index.php?act=UserCP&amp;CODE=getgallery" method="post">
	<table cellspacing="0" width="100%">
		<tr>
			<? echo "<td class=\"pformleft\">".$lang['optavt_selectavt']."</td>"; ?>
			<td class="pformright">
				<select name='av_cat' class='forminput'>
					<? echo"<option value='root'>".$lang['optavt_base']."</option>"; ?>
					<option value='IPB_Community_Pack'>IPB Community Pack</option>
					<option value='Smiley_Avatars'>Smiley Avatars</option>
				</select>&nbsp;&nbsp;
				<? echo "<input type=\"submit\" value=\"".$lang['optavt_submit']."\" name=\"submit\" />"; ?>
			</td>
		</tr>
	</table>
</form>
<form action="" method="post"  enctype='multipart/form-data' name="creator" onsubmit="return checkform();">
<input type="hidden" name="MEM_ID" value="<?=$mem_id?>" />
<input type='hidden' name='MAX_FILE_SIZE' value='9000000' />
<? echo "<div class=\"formsubtitle\">".$lang['optavt_avtprsnl']."</div>"; ?>
<table cellspacing="0"  width="100%">
	<tr>
		<? echo "<td class=\"pformleft\" >".$lang['optavt_url']."</td>"; ?>
		<td class="pformright">
			<input type="text" size="50" maxlength="90" name="url_avatar" value="<?=$current_avatar?>" onfocus="select_url()" />
			&nbsp;&nbsp;(<a href="javascript:restore_it()"> <? echo"".$lang['optavt_rep'].""; ?></a>)
		</td>
	</tr>
</table>
<table cellspacing="0" width="100%">
	<tr>
		<? echo"<td class=\"pformleft\">".$lang['optavt_loadimg']."</td>"; ?>
		<td class="pformright"><input type="file" size="30" name="upload_avatar" onfocus="select_upload()" onclick="select_upload()" /></td>
	</tr>
</table>
<table cellspacing="0" width="100%">
	<tr>
		<td class="pformleft">&nbsp;</td>
		<? echo "<td class=\"pformright\">".$lang['optavt_rid']."</td>"; ?>
	</tr>
</table>
	<div align="center" class="formsubtitle">
		<? echo "<input type=\"submit\" name=\"submit\" value=\"".$lang['optavt_update']."\" />"; ?>
		&nbsp;&nbsp;&nbsp;<input type="submit" name="remove" onclick="remove_pressed=1;" <? echo"value=\"".$lang['optavt_remove']."\" />"; ?>
	</div>
</form></div>
		</td>
	</tr>
</table>
	<!-- end main CP area -->

	</tr></td>

<?php 
} //end postback

include('end.php');?>



<?
function is_valid($pkey) //Fake Function
{
	if($pkey) return true;
	else return false;
}?>
