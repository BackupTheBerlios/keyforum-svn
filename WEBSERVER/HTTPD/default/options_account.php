<?php
$whereiam='options_account.php';

include_once("lib/lib.php");
//if(is_array($lang)) { $lang += $std->load_lang('lang_optionsaccount', $blanguage );} else { $lang = $std->load_lang('lang_optionsccount', $blanguage );}

$title = $lang['optprf_title'];

require('lib/user_panel.php');
include_once('lib/bbcode_parser.php');

if (!$_SESSION[$SNAME]['sess_auth']) {
    $url = "login.php";
    echo "<tr><td><center>".$lang['sign_login']."<br>";
    echo "".$lang['reply_loginred']."</center></td></tr><script language=\"javascript\">setTimeout(\"window.location='$url'\", 1500);</script>";
    include ("end.php");
    exit(0);
  }

//AQUISIZIONE DATI
$mem_id = $_GET['MEM_ID']; 			//dell'utente da modificare
$mem_hash = pack('H*',$mem_id); 
list($user_hash,$user_id) = get_my_info($SNAME);
$verify = ($user_id === $mem_id ? 1 : 0);

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
		$IDENTIFICATORE=md5($_SESSION[$SNAME]['sess_password'].$_SESSION[$SNAME]['sess_nick']); // identificatore dell'utente nella tabella localmember. easadecimale
		switch($_POST['action'])
		{
			case 'remove_account':
				//Consistenza dati
				if($_POST['password'] != $_POST['password2']) $std->Error('Le password non coincidono');
				if(pack('H*',md5($_POST['password'])) != $_SESSION[$SNAME]['sess_password']) $std->Error('La password inserita non è corretta');
				//Apporto le modifiche al database
				$query = "DELETE FROM {$SNAME}_localmember WHERE CONVERT(HASH USING utf8) = '$IDENTIFICATORE' LIMIT 1;";
				$db->query($query);
				if($db->rows_affected != 1) $std->Error("Errore imprevistissimo durante l\'esecuzione della query $query");
				DestroySession(); //Se l'utente non esiste non posso essere loggato
				//Success!
				Success_Page("Successo!","Modifiche apportate con successo<br>Logout in corso...",'index.php');
			break;
			
			case 'export_account':
				//Controllo dati
				if(!$userdata) $std->Error("Impossibile Esportare, Utente non loggatoCome diavolo ci sei finito qui?");
				if($_POST['exp_password']) //Se devo esportare la password
				{
					if(pack('H*',md5($_POST['exp_password'])) != $_SESSION[$SNAME]['sess_password']) //Controllo che sia giusta
					{
						$std->Error('La password inserita non è corretta');
					}
				}
				//Preparazione dati da esportare
				$export['NICK'] = $_SESSION[$SNAME]['sess_nick'];
				$export['PWD']  =  ($_POST['exp_password'] ? $_POST['exp_password'] : '');
				if($_POST['exp_settings'] == 1)
				{
					$export += get_object_vars($userdata);
					unset($export['IS_AUTH']); //Importante... in fase di import non importare anche is_auth
				}
				$export['PASSWORD'] = $userdata->PASSWORD; //Obbligatorio
				UserExport($export);
				exit();
			break;
			
			case 'convert_account':
				//Controllo dati
				if(!$userdata) $std->Error("Impossibile Esportare, Utente non loggatoCome diavolo ci sei finito qui?");
				
				$privkey=base64_decode($userdata->PASSWORD);
				$KEY_DECRYPT=pack('H*',md5($_SESSION[$SNAME]['sess_nick'].$_SESSION[$SNAME]['sess_password'])); // = password per decriptare la chiave privata in localmember (16byte)

				$req[FUNC][BlowDump2var][Key]=$KEY_DECRYPT;
				$req[FUNC][BlowDump2var][Data]=$privkey;
				if(!$core) $core=new CoreSock;
				if (!$core->Send($req)) $std->Error("Error sending data to the core");
				if (!$risp=$core->Read()) $std->Error ("Error receiving data from the core");

				$oldprivkey=$risp[FUNC][BlowDump2var];
				if ( !$oldprivkey ) $std->Error("Error receiving data from the core, aborting.");

				$query="SELECT hash FROM {$SNAME}_membri WHERE PKEYDEC='" . $oldprivkey['private']['_n'] ."';";
				$userhash = $db->get_var($query);

				if ( empty($userhash) ) $std->Error("User not found!");
				
				$oldprivkey[hash]=$userhash;
				$req2[FUNC][var2BlowDump64][Key]=$KEY_DECRYPT;
				$req2[FUNC][var2BlowDump64][Data]=$oldprivkey;
				
				if ( !$core->Send($req2) ) $std->Error("Timeout sending data to the core, aborting.");
				$resp2=$core->Read();
				if ( !$resp2[FUNC][var2BlowDump64] ) $std->Error("Error receiving data from the core, aborting.");
				$finalpkey64=$resp2[FUNC][var2BlowDump64];
				
				$sqladd = "UPDATE {$SNAME}_localmember SET password='" . mysql_real_escape_string($finalpkey64) .
						"' WHERE hash='$IDENTIFICATORE';";
    			if ( !$db->query($sqladd) ) $std->Error("Error inserting updated private key");
			    else $std->Error("","","User correctly converted !");
			break;
			default:
				$std->Error("La funzione richiesta non esiste");
			break;
		}
	}
}
include("testa.php");

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
	<div class="maintitle"><?=$lang['optprf_welcome']?></div>
	<div class="formsubtitle">Elimina Account</div>
	<table cellspacing="0" align="center" width="100%">
	<tr>
		<td class="pformleft" align="justify" style="width:60%">
			E' possibile che per qualche oscuro motivo non comprensibile alla mente umana tu possa decidere
			di voler cancellare l'utente locale. Ricorda che se non hai la chiave privata, non sarai <b>mai e poi mai</b>
			in grado di recuperare quest'account. Se non sei sicuro esporta prima l'utente.
		</td>
		<td class="pformright">
		<form action="" method="post" name="REPLIER">
			<input type="hidden" name="MEM_ID" value="<?=$mem_id?>" />
			<input type="hidden" name="action" value="remove_account" />
			<table width="100%">
				<tr>
					<td align="right">Password:</td>
					<td><input type="password" name="password"  /></td>
				</tr>
				<tr>
					<td align="right">Conferma:</td>
					<td><input type="password" name="password2" /></td>
				</tr>
				<tr><td colspan="2" align="center"><input type="submit" value="Elimina" /></td></tr>
			</table>
		</form>
	    </td>
	</tr>
</table>
<div class="formsubtitle">Esporta Account</div>
	<table cellspacing="0" align="center" width="100%">
	<tr>
		<td class="pformleft" align="justify" style="width:60%">
			<b>PER ADESSO USARE ANCORA <a href="userexport.php">userexport.php</a></b>  <br /><br />
			Hai provato a trascrivere la tua chiave provata su carta ma ti sei fermato al primo rigo?<br />
			Puoi esportare tutte le informazioni relative al tuo account in un unnico file xml, comodo no?<br />
			<span style="color:#FF0000">*</span>Non è consigliato esportare anche la password.

		</td>
		<td class="pformright">
		<form action="" method="post" name="REPLIER">
			<input type="hidden" name="MEM_ID" value="<?=$mem_id?>" />
			<input type="hidden" name="action" value="export_account" />
			<table width="100%">
				<tr>
					<td align="right"><span style="color:#FF0000">*</span> Esporta password:</td>
					<td>
					<script type="text/javascript" language="javascript">
					function toggle(obj)
					{
						if(obj.disabled == true) obj.disabled = false;
						else obj.disabled = true;
					}
					</script>
						<input type="checkbox" value="1" onchange="javascript:toggle(exp_password);" />
						<input type="password" name="exp_password"  disabled="disabled"/>
					</td>
				</tr>
				<tr>
					<td align="right">Esporta preferenze:</td>
					<td><input type="checkbox" name="exp_settings" value="1" checked /></td>
				</tr>
				<tr><td colspan="2" align="center"><input type="submit" value="Esporta" /></td></tr>
			</table>
		</form>
	    </td>
	</tr>
</table>
<div class="formsubtitle">Converti Account</div>
<table cellspacing="0" align="center" width="100%">
	<tr>
		<td class="pformleft" align="justify" style="width:60%">
			Se hai importato l' utente da una board precedente, ma non riesci ad usare Keyforum, probabilmente 
			l'account ha bisogno di essere convertito. Nota che l'utente importato deve già essere presente nella
			<a target=_blank href='userlist.php?validati=1&amp;nonvalidati=1'>Lista utenti</a>.
		</td>
		<td class="pformright" align="center">
			<form action="" method="post" name="REPLIER">
			<input type="hidden" name="MEM_ID" value="<?=$mem_id?>" />
			<input type="hidden" name="action" value="convert_account" />
			<input type="submit" value="Converti" />
			</form>
		</td>
	</tr>
</table>
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

function UserExport($array)
{
	global $_REQUEST;
	// modificando gli header l'output viene salvato invece che visualizzato
	$filename = "userdata.xml";
	
	$xmlcont="<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n<USERDATA>\n";
	foreach($array as $key=>$value)
	{
		//$xmlcont.="\t<".ucwords($key).">".$value."</".ucwords($key).">\n";
		$xmlcont.="\t<$key>$value</$key>\n";
	}
	$xmlcont.="</USERDATA>";
	
	header("Content-type: text/xml");
	header("Content-Disposition: attachment; " . $filename);
	print($xmlcont);
}

?>
