<?PHP
include ("testa.php");

// carico la lingua per la gestip
$lang += $std->load_lang('lang_gestip', $blanguage );

$whereiam="gestip";
$idquery="SELECT value FROM config WHERE MAIN_GROUP='SHARE' AND SUBKEY='$SNAME' AND FKEY='ID';";
$idriga =$db->get_var($idquery);
?>
<tr>
	<td>


<?PHP
if ($_POST[action]=="update") {

// *** cambio stato agli STATICI ***
if ($_POST['static'])
{
while (list ($chiave, $valore) = each ($_POST['static'])) {
    $statlist[$valore] .= $_POST[ip][$chiave].",";
   }
$statlist[0]=substr($statlist[0],0,-1);
$statlist[1]=substr($statlist[1],0,-1);

// da dinamico a statico
if ($statlist[1])
{
$db->query("UPDATE iplist SET STATIC=1 WHERE IP IN ({$statlist[1]}) AND BOARD='$idriga';");
}

// da statico a dinamico
if ($statlist[0])
{
$db->query("UPDATE iplist SET STATIC=0 WHERE IP IN ({$statlist[0]}) AND BOARD='$idriga';");
}

}

// *** cancellati ***
if ($_POST[delete])
{
while (list ($chiave, $valore) = each ($_POST[delete])) {
    $dellist .= $_POST[ip][$chiave].",";
   }
$dellist=substr($dellist,0,-1);
$db->query("DELETE FROM iplist WHERE BOARD='$idriga' AND IP IN ($dellist);");
}

}

// *** nuovo IP ***
if ($_POST[action]=="new") {

if ($_POST['STATIC']) $stat=1; else $stat=0; 
	$db->query("INSERT INTO iplist (BOARD,IP,TCP_PORT,TROVATO,STATIC) VALUES "
	."('".$idriga['value']."','".Ip2Num($_POST[ip])."','$_POST[TCP_PORT]','3','$stat');");
}


?>
<?PHP
//DEFAULT VALUE:
$_GET['order'] = (isset($_GET['order']) ? $_GET['order'] : 6);
$_GET['mode'] = (isset($_GET['mode']) ? $_GET['mode'] : 1);
//PREPARAZIONE DATI 

/*!!!!!!NON CAMBIARE L'ORDINE!!!!!*/
$titoli_array = Array(
	0 => array('IP',$lang['gestip_id'],15)
	,array('TCP_PORT',$lang['gestip_porta'],10)
	,array('CLIENT_NAME',$lang['gestip_nomeclient'],15)
	,array('CLIENT_VER',$lang['gestip_clientvers'],7)
	,array('DESC',$lang['gestip_nick'],15)
	,array('FALLIMENTI',$lang['gestip_failed'],1)
	,array('TROVATO',$lang['gestip_source'],15)
	,array('STATIC',$lang['gestip_static'],5)
	,array(NULL,$lang['gestip_delete'],5)
	);
//PREPARAZIONE QUERY

$mode_a =  Array('ASC','DESC');
$order = $titoli_array[$_REQUEST['order']][0];
$mode = $mode_a[$_REQUEST['mode']];
$new_mode = !$_REQUEST['mode'];
$ordinamento = "ORDER BY '$order' $mode, 'IP'";

//QUERY
$risultato = $db->get_results("SELECT * FROM iplist WHERE BOARD='$idriga' $ordinamento;");

//OUTPUT
?>	
	<?=$lang['gestip_nodelist']?> <?=$idriga?> <br>
	<?=$lang['gestip_info']?><br><br>
	<a href='gestip.php'><?=$lang['gestip_refresh']?></a><br>
	<br><br>
	<form method=post action=gestip.php>
	<input type='hidden' name='mode' value='<?=$_REQUEST['mode']?>'>
	<input type='hidden' name='order' value='<?=$_REQUEST['order']?>'>
	 <div align=right><input type=submit value=update></div>
	<div class="borderwrap">
	  <div class="maintitle">
	    <p class="expand"></p>
		<p><?=$lang['gestip_manageip']?></p>
	  </div>
	  <table cellspacing="1">
		<tR><?=titoli($titoli_array)?></tR>

<?PHP
# 2 Scambio nodi
# 1 passivo
# 3 manuale
$str_trovato = Array(
	'1' =>$lang['gestip_passive']
	,$lang['gestip_nodeexc']
	,$lang['gestip_usrsource']
	,$lang['gestip_httpsource']);

if ($risultato)
{
foreach($risultato as $ris) {
	$count++;
	echo "\t<tr>
	<td class=row2>".Num2Ip($ris->IP)."</td>
	<td class=row1>$ris->TCP_PORT</td>
	<td class=row2>$ris->CLIENT_NAME</td>
	<td class=row1>$ris->CLIENT_VER</td>
	<td class=row2>".secure_v($ris->DESC)."</td>
	<tD class=row1>$ris->FALLIMENTI</tD>\n";
	unset($chec);

	$how = $str_trovato[$ris->TROVATO];
	echo "\t<tD class=row2>$how</td>\n\t<td class=row1>";	
	$chec[$ris->STATIC]['start']="<b>";
	$chec[$ris->STATIC]['end']="</b>";

	

	//echo "<INPUT type=CHECKBOX name=STATIC[$count] value='1' $chec></td>\n";
	

	echo "{$chec[1]['start']}y{$chec[1]['end']}<input type=\"radio\" value=1  name=static[$count] >{$chec[0]['start']}n{$chec[0]['end']}<input type=\"radio\" name=static[$count] value=0>";

	
	echo "\t<tD class=row2><INPUT type=CHECKBOX name=delete[$count] value='1'><input type=hidden name=ip[$count] value='$ris->IP'></td>\n"
	."\t</tR>\n";
}
}
echo "</table></div>
<input type=hidden name=action value=update>
<div align=right><input type=submit value=update></div>
<br><br><br><br>";
echo "</form>";
?>
	
	<form method='post' action=''>
	<input type='hidden' name='action' value='new'>
	<input type='hidden' name='mode' value='<?=$_GET['mode']?>'>
	<input type='hidden' name='order' value='<?=$_GET['order']?>'>
	<table border=0 cellspacing=1 cellpadding=1 align=center>
	<tR>
		<?PHP echo "<td colspan=2 class=row4 align=center><b>".$lang['gestip_addip']."</b></td>";?>
	</tR>
	<tr>
		<td class=row1>IP</td>	
		<td class=row2><input type='text' name='ip'></td>
	</tr>
	<tr>
		<?PHP echo "<td class=row2>".$lang['gestip_addporttcp']."</td>";?>
		<td class=row1><input type='text' name='TCP_PORT' value='40569'></td>
	</tr>
	<tr>
		<?PHP echo "<td class=row1>".$lang['gestip_addstatic']."</td>	";?>	
		<td class=row2><INPUT type='CHECKBOX' name='STATIC' value='1'></td>
	</tr>
	<tR>
		<?PHP echo "<td colspan=2 class=row4 align=center><input type=submit value=".$lang['gestip_add']."></form></td>";?>
	</tR>
	</table>
  </td>
</tr>
<?PHP
include ("end.php");




function titoli($array)
{
	//preparazione link
	$link = '?';
	foreach($_GET as $key=>$value)
	{
		if($key != 'mode' && $key != 'order') 
		$link .= "$key=$value&";
	}
	//CAMPO STRINGA LARGHEZZA
	$order = $_REQUEST['order'];
	for($i=0,$tot=count($array);$i<$tot;$i++)
	{
		list($asd,$stringa,$lar) = $array[$i];
		$new_mode = ($i == $order ?  (int)!$_REQUEST['mode'] : $_REQUEST['mode']);
		$return .="<th align='left' width='$lar%' class='titlemedium'>
			<a href='$link&amp;mode=$new_mode&order=$i'>$stringa</th>";
	}
	return $return;
}
?>
