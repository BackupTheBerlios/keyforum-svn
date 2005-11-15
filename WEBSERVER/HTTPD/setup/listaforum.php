<?PHP
ob_start('ob_gzhandler'); 
include ("lib.php");
include ("testa.php");
?>
<tr><td valign=top>
<h2><center>Lista forum</center></h2>
<?PHP
	if ($_GET[act]=='del') {
	    $forum=$_GET[forum];
		mysql_query("DELETE FROM config WHERE MAIN_GROUP='SHARE' AND SUBKEY='{$forum}';");
		mysql_query("DELETE FROM config WHERE MAIN_GROUP='WEBSERVER' AND SUBKEY='{$forum}';");
		mysql_query("DROP TABLE {$forum}_admin,{$forum}_conf,{$forum}_congi,"
				."{$forum}_localmember,{$forum}_membri,{$forum}_msghe,{$forum}_newmsg,"
				."{$forum}_priority,{$forum}_reply,{$forum}_sez");
	}

	$result=mysql_query("SELECT SUBKEY FROM config WHERE MAIN_GROUP='SHARE';");
	while ($info = mysql_fetch_row($result)) {
		print "<li> $info[0] - <a href='listaforum.php?act=del&forum=$info[0]'>Cancella</a><br>";
	}
	
?>







</td></tr>
<?PHP
include ("end.php");
?>