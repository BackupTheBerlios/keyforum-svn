<html>

<head>
<meta http-equiv=\"Content-Language\" content=\"it\">
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=windows-1252\">
<title>CREA NUOVA BOARD</title>
<link type=\"text/css\" rel=\"stylesheet\" href=\"style_page.css\">
</head>

<style>

</style>

<body bgcolor="#E4EAF2">

<form method="POST" action="newboard.php">
  <p>Nome sessione (5 caratteri, niente spazi)<br>
  <input type="text" name="bsession" size="20"></p>
  <p>Descrizione Board<br>
  <input type="text" name="bdesc" size="73"></p>
  <p>&nbsp;</p>
  <p>
  <input type=hidden name="submit" value=1>
  <input type="submit" value="Crea !" name="B1"></p>
</form>



<?php

if($submit)
{
include ("core.php");
$corereq['RSA']['GENKEY']['CONSOLE_OUTPUT']=0;

$coresk = new CoreSock;

echo "Generazione chiavi in corso, attendere .......<br><br>";
flush();

if ( !$coresk->Send($corereq) ) $std->Error("Errore in send!");

$coreresp = $coresk->Read(180);

if ( !$coreresp ) die("il core non ha risposto nel tempo indicato");

$bname=$_REQUEST['bsession'];
$bdesc=$_REQUEST['bdesc'];
$pubkey=$coreresp['RSA']['GENKEY']['pub'];
$privkey=$coreresp['RSA']['GENKEY']['priv'];
$bid=sha1($pubkey);

$xmlcont="<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>
    <BOARD>
    	<NAME>$bname</NAME>
    	<SESSION>$bname</SESSION>
    	<DESC>$bdesc</DESC>
    	<LANG>IT</LANG>
    	<PKEY>$pubkey</PKEY>
    	<ID>$bid</ID>
    	<STARTUP>*</STARTUP>
  </BOARD>";

echo "<b>chiave pubblica (da distribuire):</b> <br>";
echo "<textarea rows='5' name='chiave' cols='70' readonly class='row2' style='border: none; overflow: auto'>$pubkey</textarea><br><br>";
echo "<b>id board</b><br>";
echo "<textarea rows='1' name='chiave' cols='70' readonly class='row2' style='border: none; overflow: auto'>$bid</textarea><br><br>";

echo "<b>file XML (es $bname.xml) da distribuire/importare in addboard.php: </b><br>";
echo "<textarea rows='15' name='chiave' cols='70' readonly class='row2' style='border: none; overflow: auto'>$xmlcont</textarea><br><br>";


echo "<b>chiave privata ADMIN (conservare gelosamente e non distribuire): </b><br>";
echo "<textarea rows='40' name='chiave' cols='70' readonly class='row2' style='border: none; overflow: auto'>$privkey</textarea><br>";



  
  
 
}


?>




</body>

</html>
