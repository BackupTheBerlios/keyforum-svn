<?PHP
ob_start('ob_gzhandler'); 
include ("lib.php");
include ("testa.php");
if ($_GET[group]) {
    mysql_query("UPDATE config SET `VALUE`='".$_GET[limit]."' WHERE `MAIN_GROUP`='TCP'"
	." AND `SUBKEY`='BANDA_LIMITE' AND `FKEY`='".$_GET[group]."'") or print mysql_error();
	if (mysql_affected_rows()==0) {
		mysql_query("INSERT INTO config (`MAIN_GROUP`,`SUBKEY`,`FKEY`,`VALUE`) VALUES('TCP','BANDA_LIMITE','".$_GET[group]."','".$_GET[limit]."');") or print mysql_error();
	}
}
?>
<tr><td valign=top>
<center><h3>Limitazione della banda in upload</h3></center>
TU puoi tranquillamente iscriverti a più gruppi di discussioni. Puoi anche aprire il tuo Webserver per l'accesso multiplo di più persone. Può così diventare necessario limitare la banda per alcuni tipi di connessioni.<br>
Di seguito puoi creare gruppi da associare a vari forum di tipo webserver o dati.<br>
Esempio pratico:<br>
<table border=0 bordercolor=black cellspacing=1 cellpadding=1>
<tr><td class=row2><b>Gruppo</b></tD><td class=row2><b>Banda</b></td></tR>
<tr><td class=row1>webserver_generico</tD><tD class=row1>-1 (infinito)</td></tr>
<tr><td class=row1>data</td><td class=row1>8000 (8KB al secondo)</td></tr>
</table><bR>
Un limite di banda con valore negativo è come impostarla con un valore molto grande (infinito).<bR>
<br><br>
<table border=1 bordercolor=black cellspacing=1 cellpadding=1 align=center width=400>
<tr><td class=row2><b>Gruppo</b></tD><td class=row2><b>Banda</b></td><td class=row2><b>Azione</b></td></tR>
<?php
$result=mysql_query("SELECT FKEY,VALUE FROM config WHERE MAIN_GROUP='TCP' AND SUBKEY='BANDA_LIMITE' ORDER BY FKEY;");
while ($dati = mysql_fetch_row($result)) {

print "<tr>
	<td class=row1><form method=get action=tcp.php>$dati[0]<input type=hidden name=group value=\"$dati[0]\"></td>
	<td class=row1><input type=text name=limit value=\"$dati[1]\"></td>
	<td class=row1><input type=submit name=action value=update></form></td>
</tr>\n";
}
?>
<tr>
	<td class=row1><form method=get action=tcp.php><input type=text name=group></td>
	<td class=row1><input type=text name=limit></td>
	<td class=row1><input type=submit name=action value=Nuovo></td></tr>
</table>
</td></tr>
<?PHP
include ("end.php");
?>