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
		$new_level =$_POST['level'];
		//Controllo dati
		//todo
		
		$query = "UPDATE {$SNAME}_localmember 
		SET LANG = '$new_lang'
			,TPP = '$new_tpp' 
			,PPP = '$new_ppp'
			,HIDESIG ='$new_hidesig'
			,LEVEL ='$new_level'
		WHERE HASH = '{$userdata->HASH}'  LIMIT 1 ";
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
			$std->Error($lang['optusr_errmsg']);
			die(''.$lang['optusr_errmsg'].'');
		}
	}
}


$query = "
	Select LANG, TPP, PPP, HIDESIG , LEVEL
	FROM {$SNAME}_localmember
	WHERE hash = '{$userdata->HASH}'
	LIMIT 1;
	";
$current = $db->get_row($query);




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
	<div class="formsubtitle"><?=$lang['optusr_intopt']?></div>
	<table cellspacing="0" align="center" width="100%">
	<tr>
		<td class="pformleft" ><?=$lang['optusr_lang']?></td>
		<td class="pformright">   
			<?=select_language('lang',$current->LANG)?>
    	</td>
<!--<tr>
		<td class=\"pformleft\" >".$lang['optusr_time']."</td>"; ?>
		<td class="pformright">
			asd
	    </td>
	</tr> -->
	</table>
<div class="formsubtitle"><?=$lang['optusr_bopt']?></div>
<table cellspacing="0" align="center" width="100%">
	<tr>
		<td width="70%"><?=$lang['optusr_showsign']?></td>
		<td>
			<?=select_yn('hidesig',$current->HIDESIG)?>
		</td>
	</tr>
	<tr>
		<? echo"<td width=\"70%\">".$lang['optusr_showimg']."</td>"; ?>
		<td>
			<?=select_yn('hideimg',$current->HIDEIMG)?>
		</td>
	</tr>
	<tr>
		<? echo "<td width=\"70%\">".$lang['optusr_showavatar']."</td>"; ?>
		<td>
			<?=select_yn('hideavatar',$current->HIDEAVATAR)?>
		</td>
	</tr>

	<tr>
	<td width="70%"><?=$lang['optusr_ppp']?></td>
		<td>
			<?=select_num_step('ppp',$current->PPP,10,5,40,5)?>
		</td>
	</tr>
	<tr>
		<td width="70%"><?=$lang['optusr_tpp']?></td>
		<td>
			<?=select_num_step('tpp',$current->TPP,20,5,40,5)?>
		</td>
	</tr>
	<tr>
		<td width="70%"><?=$lang['optusr_admrights']?></td>
		<td>
			<?=select_admin_controls('level',$current->LEVEL,0)?>
		</td>
	</tr>
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
function select_admin_controls($name,$current,$default)
{
	$level = get_level_list();
	$return .="<select name='$name'>";
	foreach($level as $value=>$label)
	{
		$selected = ($current == $value ? 'selected' : '');
		$return.= "<option value='$value' $selected >$label</option>\n";
	}
	$selected = ($current == $default ? 'selected' : '');
	$return .="<option value='$default' $selected >Usa Default Forum</option>";
	$return .="</select>";
	return $return;
}


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
}?>
