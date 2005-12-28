<?php
$whereiam='options_sign.php';
$title = "Il tuo pannello di controllo";

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
			<div class="maintitle">Benvenuto nel Pannelo di Controllo</div>'.
			show_private_form($_POST).
		'</div></td></tr></table>';
	}
	else
	{
		echo "INSERIRE QUI IL CODICE PER MODIFICARE LA FIRMA:<br>Messaggi tipo 5";
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
			<div class="maintitle">Benvenuto nel Pannelo di Controllo</div>
			<script type="text/javascript">
			<!--
			var MessageMax  = "10000";
			var Override    = "";
			function CheckLength(){
				MessageLength  = document.REPLIER.body.value.length;
				message  = "";
					if (MessageMax > 0){
						message = "Lunghezza massima " + MessageMax + " caratteri.";
					}
					else{
					message = "";
					}
				alert(message + " Hai usato " + MessageLength + " caratteri.");
			}
	
			function ValidateForm(){
				MessageLength  = document.REPLIER.body.value.length;
				errors = "";
					if (MessageMax !=0){
						if (MessageLength > MessageMax){
							errors = "Lunghezza massima " + MessageMax + " caratteri. Caratteri utilizzati: " + MessageLength;
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
<div class="formsubtitle">Firma Attuale</div>
<div class="signature" style="padding: 5px;"><?=$current_sign_nobb?></div>
<div class="formsubtitle">Modifica Firma</div>
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
			(<a href="javascript:CheckLength()">Controlla Lunghezza</a>)
		</td>
	</tr>
	<tr>
		<td class="formbuttonrow"><input type="button" value="Aggiorna Firma"  onclick="ValidateForm()" class="button"/></td>
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