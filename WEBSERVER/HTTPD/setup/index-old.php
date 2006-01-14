<?PHP
ob_start('ob_gzhandler'); 
include ("lib.php");
include ("testa.php");
?>
<tr><td valign=top>
<h2><center>KeyForum Config</center></h2>
Questa pagina ti aiuterà nella configurazione e setup di nuovi forum con semplici passi.<br>

<lI> <a href="tcp.php">Limita la banda in UPLOAD e crea nuovi gruppi per le connessioni</a><br>
<li> <a href="group.php">Aggiungi o elimina i forum ai quali sei segnato</a><bR>
<li> <a href="webserver.php">Modifica e crea i webserver di accesso</a><bR>
</td></tr>


<?PHP
include ("end.php");
?>