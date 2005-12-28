<?php
$whereiam='options_user.php';
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
		echo "modifiche avvenute con successo (magari)";
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
			<div class="maintitle">Benvenuto nel Pannelo di Controllo</div>
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
			fcheck = confirm("Rimuovere il tuo Avatar?");
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
<div class="formsubtitle">Impostazioni Avatar</div>
	<p>L'Avatar non deve essere pi&#249; grosso di 90 pixel per 90 pixel. Gli avatar caricati dal tuo computer non possono essere pi&#249; grossi di 50 KB.<br />Tipi di file permessi: <strong>gif,jpg,jpeg,png</strong></p>
<div class="formsubtitle">Avatar Corrente</div>
<div class="tablepad" align="center"><img src='<?=$current_avatar?>' border='0' width='90' height='90' alt='' /><br />Inserisci l'URL di un Avatar online 90x90</div>
<div class="formsubtitle">Avatar preinstallati</div>
<form action="http://www.keyforum.net/forum/index.php?act=UserCP&amp;CODE=getgallery" method="post">
	<table cellspacing="0" width="100%">
		<tr>
			<td class="pformleft">Scegli un avatar da una delle nostre Gallerie</td>
			<td class="pformright">
				<select name='av_cat' class='forminput'>
					<option value='root'>Galleria Base</option>
					<option value='IPB_Community_Pack'>IPB Community Pack</option>
					<option value='Smiley_Avatars'>Smiley Avatars</option>
				</select>&nbsp;&nbsp;
				<input type="submit" value="Vai!" name="submit" />
			</td>
		</tr>
	</table>
</form>
<form action="" method="post"  enctype='multipart/form-data' name="creator" onsubmit="return checkform();">
<input type="hidden" name="MEM_ID" value="<?=$mem_id?>" />
<input type='hidden' name='MAX_FILE_SIZE' value='9000000' />
<div class="formsubtitle">Avatar Personalizzato</div>
<table cellspacing="0"  width="100%">
	<tr>
		<td class="pformleft" >URL</td>
		<td class="pformright">
			<input type="text" size="50" maxlength="90" name="url_avatar" value="<?=$current_avatar?>" onfocus="select_url()" />
			&nbsp;&nbsp;(<a href="javascript:restore_it()">Ripristina</a>)
		</td>
	</tr>
</table>
<table cellspacing="0" width="100%">
	<tr>
		<td class="pformleft"><strong>Oppure</strong> carica una nuova immagine dal tuo computer</td>
		<td class="pformright"><input type="file" size="30" name="upload_avatar" onfocus="select_upload()" onclick="select_upload()" /></td>
	</tr>
</table>
<table cellspacing="0" width="100%">
	<tr>
		<td class="pformleft">&nbsp;</td>
		<td class="pformright"><b>Ridimensionamento Automatico Attivo</b><br />(Questo ridimensioner&agrave; l'immagine se &#232; troppo grossa rispetto ai parametri prefediniti)</td>
	</tr>
</table>
	<div align="center" class="formsubtitle">
		<input type="submit" name="submit" value="Aggiorna Avatar" />
		&nbsp;&nbsp;&nbsp;<input type="submit" name="remove" onclick="remove_pressed=1;" value="Rimuovi Avatar" />
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
