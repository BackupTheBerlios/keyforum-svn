<?PHP
ob_start('ob_gzhandler'); 
include ("lib.php");
include ("testa.php");
?>
<tr><td valign=top>
<h1><center>Aggiungi forum</center></h1>
<form method=post action=group.pl>
<table border=1 cellspacing=1 cellpadding=1 bordercolor=black>
<tr><td>Chiave pubblica:</td><td><input type=text name=pkey size=60></td></tr>
<tr><td>Nome (*Identificatore):</td><td><input type=text name=name></td></tr>
<!--<<tr><td>**Porta del server HTTP:</td><td><input type=text name=porta></td></tr>-->
<!--<<tr><td>Directory:</td><td><input type=text name=directory value="./anime"></td><tr>-->
<!--<<tr><td>Bind:</td><td><input type=text name=bind value="127.0.0.1"></td><tr>-->
<!--<tr><td>Gruppo Limite Banda:</td><td><input type=text name=gruppo></td><tr>-->
<!--<<tr><tD>Grupo Limite Banda</tD><tD><SELECT size=1 cols=4 NAME="gruppo">-->
<?PHP 
#$result=mysql_query("SELECT FKEY,VALUE FROM config WHERE MAIN_GROUP='TCP' AND SUBKEY='BANDA_LIMITE' ORDER BY FKEY;");
#while ($dati = mysql_fetch_row($result)) {
#if ($dati[1]<1) {
#    $strin=" infinita";
#} else {
#$strin=(( integer ) ($dati[1]/1024))." KBs";
#}
#print "<OPTION value=\"$dati[0]\"> $dati[0] - banda $strin\n";
#}

 

?><!--</select></tD></tr>-->
<tr><td>Continua</td><td><input type=submit value="Registra"></form></td></tr>
</table>
<bR>
<h1><center>Crea Nuovo Forum</center></h1>
<form method=post action=group.pl>
<input type=hidden name=MakeNew value=1>
<table border=1 cellspacing=1 cellpadding=1 bordercolor=black>
<tr><td>Nome (*Identificatore):</td><td><input type=text name=name></td></tr>
<tr><td>Continua</td><td><input type=submit value="Crea Nuovo"></form></td></tr>
</table>

* Il nome o identificatore deve essere una formula di 5 caratteri che identifica quel forum e tutte le tabelle nel database.<br>

 <br><bR>
</tr></tD>
<?PHP
include ("end.php");
?>