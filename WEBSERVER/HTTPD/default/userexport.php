<?PHP

if (isset($_REQUEST['submit'])) {

// dati inviati, cerco l'utente
$whereiam="userexport";
require_once("lib/lib.php"); 
$rawpasswd=pack("H*",md5($_REQUEST['passwd']));
$identificatore=md5($rawpasswd.$_REQUEST['nick']);

$userdata= $db->get_row("SELECT * from {$SNAME}_LOCALMEMBER WHERE HASH='{$identificatore}'");

if($userdata)
{
   if($_REQUEST['B_Export']) { UserExport($userdata);}

} else {
// dati inviati, ma utente non trovato
$whereiam="userexport";
include ("testa.php");
$lang += $std->load_lang('lang_userexport', $blanguage );
ShowForm();
echo "<center><b>user not found !!!</b></center>";
include ("end.php");
}
} else {
// dati non inviati, prima visualizzazione del form
$whereiam="userexport";
include ("testa.php");
$lang += $std->load_lang('lang_userexport', $blanguage );
ShowForm();
include ("end.php");
}


// il form di input
function ShowForm()
{
global $lang,$_SERVER,$_REQUEST;
echo"
<div align=\"center\">
  <table border=\"0\" style=\"border-collapse: collapse\" width=\"100%\" id=\"table2\">
    <tr>
      <td align=\"center\">
      <form method=\"POST\" action=\"".$_SERVER['PHP_SELF']."?submit=1\">
<table cellSpacing=\"0\" cellPadding=\"0\" border=\"0\" id=\"table3\">
  <tr>
    <td align=\"right\">
     <p >".$lang['usrexp_usrname']."&nbsp;</td>
    <td>
    <p ><input value=\"".$_REQUEST['nick']."\" name=\"nick\"></td>
  </tr>
  <tr>
    <td align=\"right\">
     <p >".$lang['usrexp_password']."&nbsp;</td>
    <td>
    <p ><input type=\"password\" value=\"".$_REQUEST['passwd']."\" name=\"passwd\"></td>
  </tr>
        <td align=\"right\" colspan=\"2\">
       <p align=\"left\" ><input type=\"checkbox\" name=\"pwdexport\" value=\"1\">esporta 
       anche la password (opzione sconsigliata)<p ></td>
  </tr>  
</table>
   <p><input type=\"submit\" value=\"".$lang['usrexp_export']."\" name=\"B_Export\"></p>
      </form>
      <p>&nbsp;</td>
    </tr>
  </table>
</div>";
}



function UserExport($userdata)
{
global $_REQUEST;
// modificando gli header l'output viene salvato invece che visualizzato
$filename = "userdata.xml";

if($_REQUEST['pwdexport']) {$userpwd=$_REQUEST['passwd'];}

$xmlcont="<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>
  <USERDATA>
  <NICK>{$_REQUEST['nick']}</NICK>
  <PWD>$userpwd</PWD>
  <KEY>{$userdata->PASSWORD}</KEY>
  <LANG>{$userdata->LANG}</LANG>
  <TPP>{$userdata->TPP}</TPP>
  <PPP>{$userdata->PPP}</PPP>
  </USERDATA>";

header("Content-type: text/xml");
header("Content-Disposition: attachment; " . $filename);
     
print($xmlcont);
}


?>
