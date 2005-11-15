<?PHP
// v. 0.6
include ("testa.php");

// carico la lingua per la gestip
$lang = $std->load_lang('lang_gestip', $blanguage );

$whereiam="gestip";
?>
<tr>
	<td>
<?PHP
if ($_POST[action]=="update") {
    if($_POST[delete]) {
		mysql_query("DELETE FROM iplist WHERE BOARD='".$_ENV[idboard]."' AND IP='$_POST[ip]';") or print ($lang['inv_query'] . mysql_error());
	} else {
		if ($_POST['STATIC']) $stat=1; else $stat=0; 
		mysql_query("UPDATE iplist SET STATIC='$stat' WHERE IP='$_POST[ip]' AND BOARD='".$_ENV[idboard]."';") or print ($lang['inv_query'] . mysql_error());
	}
	
} elseif ($_POST[action]=="nuovo") {
	if ($_POST['STATIC']) $stat=1; else $stat=0; 
	mysql_query("INSERT INTO iplist (BOARD,IP,TCP_PORT,TROVATO,STATIC) VALUES "
	."('".$_ENV[idboard]."','".Ip2Num($_POST[ip])."','$_POST[TCP_PORT]','3','$stat');") or print ($lang['inv_query'] . mysql_error());
}
?>
<?PHP
	echo $lang['gestip_nodelist'];
	print $_ENV[idboard];?>.<bR>
	<?PHP echo $lang['gestip_info'];?>.<br><br>
	<?PHP echo "<a href=\"gestip.php\">".$lang['gestip_refresh']."</a><br>";?>
	<br><br>
	<div class="borderwrap">
	  <div class="maintitle">
	    <p class="expand"></p>
	    <?PHP
		echo "
		<p>".$lang['gestip_manageip']."</p>
	  </div>
	  <table cellspacing=\"1\">
		<tR>
		 <th align=\"left\" width=\"15%\" class='titlemedium'>".$lang['gestip_id']."</th>
		 <th align=\"left\" width=\"10%\" class='titlemedium'>".$lang['gestip_porta']."</th>
		 <th align=\"left\" width=\"15%\" class='titlemedium'>".$lang['gestip_nomeclient']."</th>
		 <th align=\"left\" width=\"7%\" class='titlemedium'>".$lang['gestip_clientvers']."</th>
		 <th align=\"left\" width=\"15%\" class='titlemedium'>".$lang['gestip_nick']."</th>
		 <th align=\"left\" width=\"1%\" class='titlemedium'>".$lang['gestip_failed']."</th>
		 <th align=\"left\" width=\"15%\" class='titlemedium'>".$lang['gestip_source']."</th>
		 <th align=\"left\" width=\"5%\" class='titlemedium'>".$lang['gestip_static']."</th>
		 <th align=\"left\" width=\"5%\" class='titlemedium'>".$lang['gestip_delete']."</th>
		 <th align=\"left\" width=\"7%\" class='titlemedium'>".$lang['gestip_update']."</th>
		</tR>";
		  ?>
<?PHP
$risultato=mysql_query("SELECT * FROM iplist WHERE BOARD='".$_ENV[idboard]."';");
# 2 Scambio nodi
# 1 passivo
# 3 manuale
while($ris=mysql_fetch_assoc($risultato)) {
	echo "\t<tr>
	<td class=row2>".Num2Ip($ris[IP])."</td>
	<td class=row1>$ris[TCP_PORT]</td>
	<td class=row2>$ris[CLIENT_NAME]</td>
	<td class=row1>$ris[CLIENT_VER]</td>
	<td class=row2>".secure_v($ris["DESC"])."</td>
	<tD class=row1>$ris[FALLIMENTI]</tD>\n";
	if ($ris['STATIC']) $chec="checked"; else $chec="";
	if ($ris[TROVATO]==1) $how=$lang['gestip_passive'];
		elseif ($ris[TROVATO]==2) $how=$lang['gestip_nodeexc'];
		elseif ($ris[TROVATO]==3) $how=$lang['gestip_usrsource'];
		elseif ($ris[TROVATO]==4) $how=$lang['gestip_httpsource'];
		else $how=$ris[TROVATO];
	echo "\t<tD class=row2>$how</td>\n\t<td class=row1>";
	echo "<form method=post action=gestip.php><input type=hidden name=action value=update>"
			."<INPUT type=CHECKBOX name=STATIC value='1' $chec></td>\n";
	echo "\t<tD class=row2><INPUT type=CHECKBOX name=delete value='1'></td>\n"
	."\t<td class=row1><input type=hidden name=ip value='$ris[IP]'><input type=submit value=update></form></td></tR>\n";
}
?>
	</table></div><br><br>
	<form method=post action=gestip.php>
	<input type=hidden name=action value=nuovo>
	<table border=0 cellspacing=1 cellpadding=1 align=center>
	<tR>
		<?PHP echo "<td colspan=2 class=row4 align=center><b>".$lang['gestip_addip']."</b></td>";?>
	</tR>
	<tr>
		<td class=row1>IP</td>	
		<td class=row2><input type=text name=ip></td>
	</tr>
	<tr>
		<?PHP echo "<td class=row2>".$lang['gestip_addporttcp']."</td>";?>
		<td class=row1><input type=text name=TCP_PORT value=40569></td>
	</tr>
	<tr>
		<?PHP echo "<td class=row1>".$lang['gestip_addstatic']."</td>	";?>	
		<td class=row2><INPUT type=CHECKBOX name=STATIC value='1'></td>
	</tr>
	<tR>
		<?PHP echo "<td colspan=2 class=row4 align=center><input type=submit value=".$lang['gestip_add']."></form></td>";?>
	</tR>
	</table>
  </td>
</tr>
<?PHP
include ("end.php");
?>
