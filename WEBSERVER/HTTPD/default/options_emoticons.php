<?php
$whereiam='options_emoticons.php';
include_once("lib/lib.php");
if(is_array($lang)){$lang += $std->load_lang('lang_optionsforum', $blanguage );} else {$lang = $std->load_lang('lang_optionsforum', $blanguage );}

$title = $lang['optfrm_title'];

include("testa.php");
require('lib/user_panel.php');
include_once('lib/bbcode_parser.php');

if (!$_SESSION['sess_auth']) {
    $url = "login.php";
    echo "<tr><td><center>".$lang['sign_login']."<br>";
    echo "".$lang['reply_loginred']."</center></td></tr><script language=\"javascript\">setTimeout('delayer()', 1500);\nfunction delayer(){ window.location='$url';}</script>";
    include ("end.php");
    exit(0);
  }

//AQUISIZIONE DATI
$SNAME=$_ENV['sesname'];
$mem_id = $_GET['MEM_ID'];
$mem_hash = pack('H*',$mem_id); 
list($user_hash,$user_id) = get_my_info($SNAME);
if(!$mem_id)
{
	$mem_id = $user_id;
}

$verify = ($user_id == $mem_id ? 1 : 0);

//POSTBACK PAGE
if($_POST['MEM_ID'])
{
	$is_post_back = 1;
	if(!$verify)
	{
		/*echo '<tr><td>
		<table cellspacing="0" width="100%">
		<tr>
		<td valign="top" class="nopad" width="24%">'.show_menu().'</td>
		<td class="nopad" width="1%"><!-- --></td>
		<!-- Start main CP area -->
		<td valign="top" class="nopad" width="75%">
			<div id="ucpcontent">
			<div class="maintitle">'.$lang['optusr_welcome'].'</div>'.
			show_private_form($_POST).
		'</div></td></tr></table>';*/
		$std->Error("Non sei aurorizzato");
	}
	else
	{
		//var_dump($_POST);
		//Aquisizione dati
		$new_num_row = $_POST['num_row'];
		$new_clic = $_POST['check'];
		$old_clic = $_POST['old_check'];

		//Processing new_data
		foreach($old_clic as $id=>$old_value)
		{
			if($new_clic[$id] != $old_value)
			{
				if($new_clic[$id] === NULL)
				{
					$torem[] = $id;
				}
				else
				{
					$toadd[] = $id;
				}
			}
		}
		if($toadd)foreach($toadd as $key=>$id)
		{
			$where_add .= " id = '$id' OR";
		}
		if($torem)foreach($torem as $key=>$id)
		{
			$where_rem .= " id = '$id' OR";
		}
		$where_add = substr($where_add,0,-3);
		$where_rem = substr($where_rem,0,-3);
		
		
		$query_add = "Update {$SNAME}_emoticons
			set clickable='1' WHERE $where_add";
		$query_rem = "Update {$SNAME}_emoticons
			set clickable='0' WHERE $where_rem";
		if($toadd)$result = $db->query($query_add);
		if($torem)$result = $db->query($query_rem);
		//-------------------------------------------//
		$result = $db->query("
			UPDATE {$SNAME}_localmember
			SET EMOCOL = '$new_num_row'
			WHERE hash = '{$userdata->HASH}'
			LIMIT 1");
			
		$is_post_back= 0;
		/*echo "</table>";
		$is_post_back= 1;
		$std->Redirect('Modifica impostazioni forum',$_SERVER['HTTP_REFERER'],$lang['optusr_msg'],$lang['optusr_msg']);
		exit();*/
	}
}


$query = "
	Select EMOCOL
	FROM {$SNAME}_localmember
	WHERE hash = '{$userdata->HASH}'
	LIMIT 1;
	";
$current = $db->get_row($query);
$query ="SELECT * from {$SNAME}_emoticons where 1 order by clickable desc";
$emoticons = $db->get_results($query);
if($emoticons) foreach($emoticons as $emo)
{
	$emo->address = ($emo->internal ? "showemo.php?id={$emo->id}" : "img/emoticons/{$emo->image}");
}


//OUTPUT
if(!$is_post_back && $verify)
{
?><tr><td>
<table cellspacing="0" width="100%">
	<tr>
		<td valign="top" class="nopad" width="24%"><?=show_menu();?></td>
		<td class="nopad" width="1%"><!-- Riga vuota --></td>
<!-- Start main CP area -->
		<td valign="top" class="nopad" width="75%">
			<div id="ucpcontent">
	<div class="maintitle"><?=$lang['optusr_welcome']?></div>
	<form action="" method="post" name="REPLIER">
	<input type="hidden" name="MEM_ID" value="<?=$mem_id?>" />
	<div class="formsubtitle">Preferenze Emoticons</div>
	<table cellspacing="0" align="center" width="100%">
	<tr>
		<td colspan="2">
		<div align='center' style="color:#FF0000; font-size:large; text-align:center">Attenzione!!</div>
		<p align="justify">
		Modificando queste impostazioni potrebbero verificarsi problemi di visualizzazione: seleziona con cura le emoticons veloci che vuoi visualizzare nella pagina delle riposte e nel riquadro 'Fast Reply' e regola di conseguenza il numero di colonne su cui si devono disporre. I valori di default sono 20 emoticons su 4 colonne.
		</p>
		</td>
	</tr>
	<tr><td colspan="2"><hr /></td></tr>
	<tr>
		<td width="70%">Numero di emoticons da mostrare sulla stessa riga</td>
		<td >   
			<?=select_num_step('num_row',$current->EMOCOL,4,2,10,1)?>
    	</td>
	</tr>

	</table>
<div class="formsubtitle">Emoticons veloci</div>
<table cellspacing="0" align="center" width="100%">
	<tr><td>
	<table width="100%"  border="0" >
	<?=show_emoticons($emoticons,3)?>
	</table>		
	<tr>
		<td class="formbuttonrow" colspan="2">
		<input type="submit" value="<?=$lang['optusr_update']?>" class="button"/>
		</td>
	</tr>
</table>
</form>
</div>
	<!-- end main CP area -->
</td></tr></table>



</td></tr>

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
	$return .="<select name='$name'>";
	$lang = get_language_list();
	foreach($lang as $short=>$string)
	{
		$selected = ($default == $short ? 'selected' : "");
		$return.= "<option value='$short' $selected>$string</option>\n";
	}
	$return .="</select>";
	return $return;
}

function is_valid($pkey) //TO DISCOVER
{
	if($pkey) return true;
	else return false;
}
function show_emoticons($emoticons,$num_row=2)
{
	$i = 0;
	$td_w = round(100/$num_row,0);
	foreach($emoticons as  $emo)
	{
		$return .= ($i%$num_row == 0 ? "<tr>" : '');
		$checked = ($emo->clickable == 1? 'checked' : '');
		$short = $emo->typed;
		/*$return.="
		<td>
		<table align='left' width='100%' cellspacing='0' cellpadding='0'>
		<tr>
			<td width='50%' align='center'>
				<img src='$emo->address' alt='{$emo->typed}' title='{emo->typed}'>
			</td>
			<td >	
				<input type='text' name='short[$emo->id]' value='$short' disabled>
			</td>
			<td >
				<input type='checkbox' name='check[$emo->id]' $checked >Mostra
			</td>
		</tr>
		</table>
		</td>";*/
		$return .="<td width='$td_w%' align='center'>
				<img src='$emo->address' alt='{$emo->typed}' title='{$emo->typed}'>
				<br>
				<input type='hidden' name='old_check[$emo->id]' value='$checked'>
   				<input type='checkbox' name='check[$emo->id]' $checked value='checked'>Mostra
				<br><br><br> <!-- LOOOL -->
		</td>";
		$i++;
		$return .= ($i%$num_row == 0 ? "</tr>" : '');
		
	}
	return $return;
}

?>