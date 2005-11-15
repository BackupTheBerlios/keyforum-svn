<?PHP
ob_start('ob_gzhandler'); 
include ("lib.php");
include ("testa.php");
?>

<tr><td valign=top>
<h1><center>Impostazioni WebServer</center></h1>
Questa pagina server per gestire i webserver.<br>
Puoi limitare la banda ed impedire o abilitare gli accessi esterni.<bR>


<?PHP 
if ($_GET[delete]) {
mysql_query("DELETE FROM config WHERE MAIN_GROUP='WEBSERVER' AND SUBKEY='$_GET[bname]';");
$_GET[bname]="";
}
if ($_POST[webname]) {
	if ($_POST[nuovo]) {
	    mysql_query("INSERT INTO config (MAIN_GROUP,SUBKEY,FKEY,VALUE) VALUES('WEBSERVER','$_POST[webname]',"
		."'BIND','".$_POST[BIND]."');");
		mysql_query("INSERT INTO config (MAIN_GROUP,SUBKEY,FKEY,VALUE) VALUES('WEBSERVER','$_POST[webname]',"
		."'SesName','".$_POST[SesName]."');");
		mysql_query("INSERT INTO config (MAIN_GROUP,SUBKEY,FKEY,VALUE) VALUES('WEBSERVER','$_POST[webname]',"
		."'GROUP','".$_POST[GROUP]."');");
		mysql_query("INSERT INTO config (MAIN_GROUP,SUBKEY,FKEY,VALUE) VALUES('WEBSERVER','$_POST[webname]',"
		."'DIRECTORY','".$_POST[DIRECTORY]."');");
		mysql_query("INSERT INTO config (MAIN_GROUP,SUBKEY,FKEY,VALUE) VALUES('WEBSERVER','$_POST[webname]',"
		."'PORTA','".$_POST[PORTA]."');");
	} else {
		mysql_query("UPDATE config SET VALUE='".$_POST[BIND]."' WHERE MAIN_GROUP='WEBSERVER' AND FKEY='BIND' AND SUBKEY='$_POST[webname]';");
		mysql_query("UPDATE config SET VALUE='".$_POST[SesName]."' WHERE MAIN_GROUP='WEBSERVER' AND FKEY='SesName' AND SUBKEY='$_POST[webname]';");
		mysql_query("UPDATE config SET VALUE='".$_POST[DIRECTORY]."' WHERE MAIN_GROUP='WEBSERVER' AND FKEY='DIRECTORY' AND SUBKEY='$_POST[webname]';");
		mysql_query("UPDATE config SET VALUE='".$_POST[GROUP]."' WHERE MAIN_GROUP='WEBSERVER' AND FKEY='GROUP' AND SUBKEY='$_POST[webname]';");
		mysql_query("UPDATE config SET VALUE='".$_POST[PORTA]."' WHERE MAIN_GROUP='WEBSERVER' AND FKEY='PORTA' AND SUBKEY='$_POST[webname]';");
	}
    
}
if ($_GET[bname]) {
	
	print "<h3><center>Configurazione per la board $_GET[bname]</center></h3>\n";
	print "<form method=get action=webserver.php><input type=hidden name=delete value=1>";
	print "<input type=hidden name=bname value='$_GET[bname]'><input type=submit value='Cancella WebServer'></form><br>\n";
	print "<form method=post action=webserver.php><input type=hidden name=webname value='$_GET[bname]'>";
	print "<input type=hidden name=nuovo value='$_GET[nuovo]'>";
    print "<table border=0 align=center cellspacing=0 cellpadding=0>\n";
	print "<tR>\n\t<td class=row2>Variabile</td>\n\t<tD class=row2>Valore</tD>\n</tr>\n";
	$result2=mysql_query("SELECT FKEY, VALUE FROM config WHERE MAIN_GROUP='WEBSERVER' AND SUBKEY='$_GET[bname]';");
	while ($info = mysql_fetch_row($result2)) {
		$valori[$info[0]]=$info[1];
	}
	print "
	<tr><td>BIND</td><td><input type=text name=BIND value='$valori[BIND]'></tD></tR>
	<tr><td>DIRECTORY</td><td><input type=text name=DIRECTORY value='$valori[DIRECTORY]'></tD></tR>
	<tr><td>Banda Limite</td><td>";
	print "<SELECT size=1 cols=4 NAME='GROUP'>\n";
	$alf=mysql_query("SELECT FKEY,VALUE FROM config WHERE MAIN_GROUP='TCP' AND SUBKEY='BANDA_LIMITE' ORDER BY FKEY;");
	while ($grp = mysql_fetch_row($alf)) {
		if ($grp[1]<1) $strin=" infinita"; else $strin=(( integer ) ($grp[1]/1024))." KBs";
		if ($grp[0]==$valori[GROUP]) $selez="selected"; else $selez="";
		
		print "<OPTION value=\"$grp[0]\" $selez> $grp[0] - banda $strin\n";
	}
	print "</select></td></tr>\n";
	print "<tr><td>PORTA</td><td><input type=text name=PORTA value='$valori[PORTA]'></tD></tR>
	<tr><td>SesName</td><td>";
	print "<SELECT size=1 cols=4 NAME='SesName'>\n";

	$alf=mysql_query("SELECT SUBKEY FROM config WHERE MAIN_GROUP='SHARE' GROUP BY SUBKEY;");
	print "<OPTION value=''>Nessun Gruppo\n";
	while ($grp = mysql_fetch_row($alf)) {
		if ($grp[0]==$valori[SesName]) $selez="selected"; else $selez="";
		print "<OPTION value=\"$grp[0]\" $selez> $grp[0]\n";
	}
	print "</select></td></tr>\n";
	print "</table><center><input type=submit value='Aggiungi/Aggiorna'></center></form>\n";
	
	
} else {
	print "Segue la lista dei webserver creati:<bR><br><br>";
	$result=mysql_query("SELECT SUBKEY FROM config WHERE MAIN_GROUP='WEBSERVER' GROUP BY SUBKEY;");
	while ($dati = mysql_fetch_row($result)) {
		print "<li> <b><a href='webserver.php?bname=$dati[0]'>$dati[0]</a></b>\n<bR>";
	}
	print "<br><bR>
<form method=get action='webserver.php'>
<input type=hidden name=nuovo value=1>
<input type=text name=bname><bR>
<input type=submit value='Crea Nuovo WebServer'></form><br><bR>";
 
}
?>
<bR>
</tr></td>
<?PHP
include ("end.php");
?>
