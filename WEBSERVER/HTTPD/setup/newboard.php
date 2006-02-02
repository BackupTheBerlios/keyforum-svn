<?
include("testa.php");


// lingua
$lang += load_lang('lang_newboard', $blanguage ); 

echo "

<div align=center>
<form method=\"POST\" action=\"newboard.php\">
  <p>".$lang['newbrd_sesname']."<br>
  <input type=\"text\" name=\"bsession\" size=\"20\"></p>
  <p>".$lang['newbrd_description']."<br>
  <input type=\"text\" name=\"bdesc\" size=\"73\"></p>
  <p>&nbsp;</p>
  <p>
  <input type=hidden name=\"submit\" value=1>
  <input type=\"submit\" value=\"".$lang['newbrd_create']."\" name=\"B1\"></p>
</form>
</div>
";?>


<?php

if($submit)
{
require "functions.php";
include ("core.php");
$corereq['RSA']['GENKEY']['CONSOLE_OUTPUT']=0;

$coresk = new CoreSock;
echo "<div align=center>";
echo $lang['newbrd_keygen'];
flush();

if ( !$coresk->Send($corereq) ) $std->Error($lang['newbrd_senderr']);

$coreresp = $coresk->Read(180);

if ( !$coreresp ) die($lang['newbrd_coretimeout']);

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


echo "<b>".$lang['newbrd_pk']."</b> <br>";
echo "<textarea rows='5' name='chiave' cols='70' readonly class='row2' style='border: none; overflow: auto'>$pubkey</textarea><br><br>";
echo "<b>".$lang['newbrd_brdid']."</b><br>";
echo "<textarea rows='1' name='chiave' cols='70' readonly class='row2' style='border: none; overflow: auto'>$bid</textarea><br><br>";

echo "<b>".$lang['newbrd_xmlfile']." (es $bname.xml) ".$lang['newbrd_infoxml'].": </b><br>";
echo "<textarea rows='15' name='chiave' cols='70' readonly class='row2' style='border: none; overflow: auto'>$xmlcont</textarea><br><br>";


echo "<b>".$lang['newbrd_infoprvkey']."</b><br>";
echo "<textarea rows='40' name='chiave' cols='70' readonly class='row2' style='border: none; overflow: auto'>$privkey</textarea><br>";
echo "</div>";



  
  
 
}


?>




</body>

</html>
