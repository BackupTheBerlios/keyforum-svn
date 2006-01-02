<?PHP
// v. 0.14
$SNAME=$_ENV['sesname'];
$whereiam="userlist";
include ("testa.php");
// carico la lingua per le sezioni
$lang = $std->load_lang('lang_userlist', $blanguage );


?>
<tr><td>
<?
function PageSelect() {
	global $lang;
	global $NumPag;
	global $CurrPag;
	global $Section;

	$link  = "?validati={$_REQUEST['validati']}&amp;nonvalidati={$_REQUEST['nonvalidati']}&amp;";
	$link .= "?order_by={$_REQUEST['order_by']}&amp;order={$_REQUEST['order']}&amp;";
	$link .= "pag=";

	echo '
	<table border="0" cellpadding="5px" cellspacing="0" width="100%">
		<tbody>
		<tr>
			<td align="left" nowrap="nowrap" width="20%">';
	
	if($NumPag > 0) 
	{
		echo "<span class='pagelink'>".($NumPag+1)."&nbsp;".$lang['usrlist_pages']."</span>&nbsp;";
		if ($CurrPag>0) 
		{
			# Pagina precedente
			echo "<span class='pagelinklast'><a href=\"{$link}0\">&laquo;</a></span>&nbsp;";
			echo "<span class='pagelink'><a href=\"{$link}".($CurrPag-1)."\">&lt;</a></span>&nbsp;";
		}
		
		# Visualizzo i link solamente per un certo numero di pagine
		if ($CurrPag > $Section) {echo "<span class='pagelink'>..</span>&nbsp;";}
		$StartPag = $CurrPag-$Section;
		if ($StartPag < 0) {$StartPag = 0;}
		$EndPag = $CurrPag+$Section;
		if ($EndPag > $NumPag) {$EndPag = $NumPag;}
		
		for ($i = $StartPag+1; $i <= $EndPag+1; $i++) 
		{
			if ($i-1 == $CurrPag)
				echo "<span class='pagecurrent'>$i</span>&nbsp;";
			else
				echo "<span class='pagelink'><a href=\"{$link}".($i-1)."\">$i</a></span>&nbsp;";
		}
		if ($CurrPag < $NumPag - $Section) {print "<span class='pagelink'>..</span>&nbsp;";}
		
		if ($CurrPag<$NumPag) 
		{
			# Pagina successiva
			echo "<span class='pagelink'><a href=\"{$link}".($CurrPag+1)."\">&gt;</a></span>";
			echo "&nbsp;<span class='pagelinklast'><a href=\"{$link}{$NumPag}\">&raquo; ".($NumPag+1)."</a></span>";
		}
	}
	echo'</tr></tbody></table>';
} //End Pageselect
?>
<script type="text/javascript"><!--

var formblock;
var forminputs;

function prepare() {
formblock= document.getElementById('user_form');
forminputs = formblock.getElementsByTagName('input');
value = '1';
}

function select_all(name) 
{
	for (i = 0; i < forminputs.length; i++) 
	{
	// regex here to check name attribute
		var regex = new RegExp(name, "i");
		if (regex.test(forminputs[i].getAttribute('name'))) 
		{
			if (value == '1') 
			{
				forminputs[i].checked = true;
			} 
			else 
			{
				forminputs[i].checked = false;
			}
		}
	}
	if (value == '1') 
	{
		value = '0';
		return '<?=$lang['usrlist_selnone']?>';
	}
	else
	{
		value = '1';
		return '<?=$lang['usrlist_selall']?>';
	}
}

function invert_all(name) 
{
	for (i = 0; i < forminputs.length; i++) 
	{
		// regex here to check name attribute
		var regex = new RegExp(name, "i");
		if (regex.test(forminputs[i].getAttribute('name'))) 
		{
			if (forminputs[i].checked == true) 
			{
				forminputs[i].checked = false;
			} 
			else 
			{
				forminputs[i].checked = true;
			}
		}
	}
}

if (window.addEventListener) {
window.addEventListener("load", prepare, false);
} else if (window.attachEvent) {
window.attachEvent("onload", prepare)
} else if (document.getElementById) {
window.onload = prepare;
}

//--></script>


  <form method='get' action='userlist.php'>
    <fieldset class='row3'>
     <? echo "<legend>".$lang['usrlist_show']."</legend>"; ?>
     <table border="0" cellpadding="2px" cellspacing="0" width="100%">
      <tr>
       <td>
        <input type='checkbox' name='validati' value='1'<? if($_REQUEST['validati']) echo ' checked'; echo ">".$lang['usrlist_valid']; ?>
        <input type='checkbox' name='nonvalidati' value='1'<? if($_REQUEST['nonvalidati']) echo ' checked'; echo ">".$lang['usrlist_notvalid']."&nbsp;"; ?>
        <input type='hidden' name='pag' value='0'>
        &nbsp;
        <? echo $lang['usrlist_orderby']; ?>
        <select name="order_by" size="1">
          <option <? if(!$_REQUEST['order_by']) echo "selected"; ?><? if($_REQUEST['order_by']=="DATE") echo "selected"; ?> value='DATE'><? echo $lang['usrlist_orderby_date']; ?></option>
          <option <? if($_REQUEST['order_by']=="AUTORE") echo "selected"; ?> value='AUTORE'><? echo $lang['usrlist_orderby_nick']; ?></option>
          <option <? if($_REQUEST['order_by']=="msg_num") echo "selected"; ?> value='msg_num'><? echo $lang['usrlist_orderby_msg']; ?></option>
        </select>
        <select name="order" size="1">
          <option <? if(!$_REQUEST['order']) echo "selected"; ?><? if($_REQUEST['order']=="ASC") echo "selected"; ?> value='ASC'><? echo $lang['usrlist_order_asc']; ?></option>
          <option <? if($_REQUEST['order']=="DESC") echo "selected"; ?> value='DESC'><? echo $lang['usrlist_order_des']; ?></option>
        </select>
        <? echo"<input type='submit' value='".$lang['usrlist_apply']."' class='button'>"; ?>
       </td>
      </tr>
     </table>
    </fieldset>
  </form>
<?
if($_REQUEST['validati']){
   if($_REQUEST['nonvalidati']){
      $query="SELECT count(HASH) AS c FROM {$SNAME}_membri;";
   }else{
      $query="SELECT count(HASH) AS c FROM {$SNAME}_membri WHERE is_auth='1';";
   }
}else{
   if($_REQUEST['nonvalidati']){
      $query="SELECT count(HASH) AS c FROM {$SNAME}_membri WHERE is_auth='0';";
   }else{
      $query="SELECT count(HASH) AS c FROM {$SNAME}_membri WHERE is_auth='2';";
   }
}
$Num3d = $db->get_var($query);
$NumPag = intval(($Num3d-1) / $UserXPage);
$CurrPag = $_REQUEST['pag'];
if (! is_numeric($CurrPag))
  $CurrPag = 0;
if ($CurrPag < 0) $CurrPag = 0;

PageSelect();

if(!$_REQUEST['order_by']){
   $order_by="DATE";
}else{
   $order_by=$_REQUEST['order_by'];
}
if(!$_REQUEST['order']){
   $order="";
}else{
   $order=" ".$_REQUEST['order'];
}
?>
<form method="POST" action="authusers.php" id="user_form">
<div class="borderwrap">
  <div class="maintitle">
    <p class="expand"></p>
    <?PHP echo "<p>".$lang['usrlist_userlist']."</p>";?>
  </div>
  
  <table cellspacing="1">
  <?PHP echo"
    <tr>
      <th align=\"right\" width=\"1%\" class='titlemedium'>".$lang['usrlist_num']."</th>
      <th align=\"left\" width=\"21%\" class='titlemedium'>&nbsp;".$lang['usrlist_nick']."</th>
      <th align=\"center\" width=\"26%\" class='titlemedium'>".$lang['usrlist_hash']."</th>
      <th align=\"center\" width=\"16%\" class='titlemedium'>".$lang['usrlist_date']."</th>
      <th align=\"right\" width=\"11%\" class='titlemedium'>".$lang['usrlist_messages']."</th>
      <th align=\"center\" width=\"21%\" class='titlemedium'>".$lang['usrlist_gruop']."</th>
	  <th align=\"center\" width=\"4%\" class='titlemedium'>".$lang['usrlist_action']."</th>
    </tr>";?>
<?PHP
if($_REQUEST['validati']){
   if($_REQUEST['nonvalidati']){
      $risultato=$db->get_results("SELECT HASH,AUTORE, DATE, TYPE, is_auth, msg_num FROM {$SNAME}_membri ORDER BY ".$order_by.$order." LIMIT ".($CurrPag*$UserXPage).",$UserXPage;");
   }else{
      $risultato=$db->get_results("SELECT HASH,AUTORE, DATE, TYPE, is_auth, msg_num FROM {$SNAME}_membri WHERE is_auth='1' ORDER BY ".$order_by.$order." LIMIT ".($CurrPag*$UserXPage).",$UserXPage;");
   }
}else{
   if($_REQUEST['nonvalidati']){
      $risultato=$db->get_results("SELECT HASH,AUTORE, DATE, TYPE, is_auth, msg_num FROM {$SNAME}_membri WHERE is_auth='0' ORDER BY ".$order_by.$order." LIMIT ".($CurrPag*$UserXPage).",$UserXPage;");
   }else{
      $risultato=$db->get_results("SELECT HASH,AUTORE, DATE, TYPE, is_auth, msg_num FROM {$SNAME}_membri WHERE is_auth='2' ORDER BY ".$order_by.$order." LIMIT ".($CurrPag*$UserXPage).",$UserXPage;");
   }
}
# 2 Scambio nodi
# 1 passivo
# 3 manuale
$i=$CurrPag*$UserXPage;
if($risultato)foreach($risultato as $ris) 
{
	$userhash=unpack("H32hex",$ris->HASH);
	echo "
    <tr>
	<td class='row1' align='right'>".++$i."</td>
	<td class='row2' align='left'>&nbsp;<a href='showmember.php?MEM_ID={$userhash['hex']}'>".secure_v($ris->AUTORE)."</a></td>
	<td class='row2' align='center'>".$userhash['hex']."</td>
	<td class='row1' align='center'>".strftime("%d/%m/%y  - %H:%M:%S",$ris->DATE)."</td>
	<td class='row1' align='right'>".$ris->msg_num."</td>
	<td class='row2' align='center'>";
	
	if($ris->is_auth){
	  echo $lang['usrlist_member'] . "</td>\n<td class='row2'>";
	} else {
		echo $lang['usrlist_validated'];
	
		// Validator or Admin only
		if ($userdata->LEVEL >8)
		{
	        echo "</td>\n\t<td class='row2'><input type=\"checkbox\" name=\"toauth[$i]\" value=\"$userhash[hex]\" />Auth";
		$displaysubmit=1;
		} else { echo "</td>\n<td class='row2'>";}
	
	

	};
	
	echo "</td>\n</tr>";
};

?>
 
  </table>
</div>

<table width='100%'>
	<tr><td> <? PageSelect(); ?> </td>
		<td align='right'>
<?PHP if ($displaysubmit)
{
	echo "
	<input type='button' value='{$lang['usrlist_selall']}' onclick=\"this.value=select_all('toauth');\" class='button' />
	<input type='button' value='{$lang['usrlist_invsel']}' onclick=\"invert_all('toauth');\"class='button' />
	<input type='submit' value='{$lang['usrlist_doit']}' name='doauth' class='button'>
	</td>";
}
?>
	
	</tr>
</table>
</form>
</td></tr>

<?PHP
include ("end.php");
?>
