<?php
$whereiam='options_sign.php';

include_once("lib/lib.php");
if(is_array($lang)){$lang += $std->load_lang('lang_optionssign', $blanguage );} else {$lang = $std->load_lang('lang_optionssign', $blanguage );}


$title = $lang['optsign_title'];

include("testa.php");
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
			<div class="maintitle">'.$lang['optsign_welcome'].'</div>'.
			show_private_form($_POST).
		'</div></td></tr></table>';
	}
	else
	{
		echo "".$lang['optsign_info1']."";
		$is_post_back= 0;
	}
}

$current_sign = stripslashes(get_sign($mem_id));

//Processing data
$current_sign_nobb = convert(secure_v($current_sign));


//OUTPUT
if(!$is_post_back)
{
?><tr><td>
<table cellspacing="0" width="100%">
	<tr>
		<td valign="top" class="nopad" width="24%"><?=show_menu();?></td>
		<td class="nopad" width="1%"><!-- --></td>
		<!-- Start main CP area -->
		<td valign="top" class="nopad" width="75%">
			<div id="ucpcontent">
			<? echo "<div class=\"maintitle\">".$lang['optsign_welcome']."</div>"; ?>
			<script type="text/javascript">
			<!--
			var MessageMax  = "10000";
			var Override    = "";
			function CheckLength(){
				MessageLength  = document.REPLIER.body.value.length;
				message  = "";
					if (MessageMax > 0){
						message =<? echo"\"".$lang['optsign_maxl']."\" + MessageMax + \"".$lang['optsign_char']."\"";?>;
					}
					else{
					message = "";
					}
				alert(message +<? echo"\"".$lang['optsign_used']."\" + MessageLength + \"".$lang['optsign_char']."\")"; ?>;
			}
	
			function ValidateForm(){
				MessageLength  = document.REPLIER.body.value.length;
				errors = "";
					if (MessageMax !=0){
						if (MessageLength > MessageMax){
							errors =<? echo"\"".$lang['optsign_maxl']."\" + MessageMax + \"".$lang['optsign_rem']."\" + MessageLength"; ?>;
						}
					}
					if (errors != "" && Override == ""){
						alert(errors);
						return false;
					}
					else{
						/*document.REPLIER.submit.disabled = true;
						return true;*/
						document.REPLIER.submit();
					}
			}
-->
</script>
<form action="" method="post" name="REPLIER">
<input type="hidden" name="MEM_ID" value="<?=$mem_id?>" />
<? echo"<div class=\"formsubtitle\">".$lang['optsign_currsign']."</div>"; ?>
<div class="signature" style="padding: 5px;"><?=$current_sign_nobb?></div>
<? echo"<div class=\"formsubtitle\">".$lang['optsign_modsign']."</div>"; ?>
<table cellspacing="0" align="center">
	<tr>
		<td align="center">
			<script language='JavaScript'>
<? include "bbcode.php"; ?>
</script>
			<? include('buttons.php')?>
		</td>
	</tr>
	<tr>
		<td class="pformleft" align="center">
			<textarea cols="60" rows="12" name="body" class="textinput"><?=$current_sign?></textarea><br />
			(<a href="javascript:CheckLength()"><? echo"".$lang['optsign_ctrllength']."";?></a>)
		</td>
	</tr>
	<tr>
		<? echo" <td class=\"formbuttonrow\"><input type=\"button\" value=\"".$lang['optsign_update']."\"  onclick=\"ValidateForm()\" class=\"button\"/></td>"; ?>
	</tr>
</table>
</form>
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
function is_valid($pkey) //Fake Function
{
	if($pkey) return true;
	else return false;
}