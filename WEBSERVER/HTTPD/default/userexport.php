<?PHP

include ("testa.php");
$lang = $std->load_lang('lang_userexport', $blanguage );
$SNAME=$_ENV['sesname'];
$whereiam="userexport";

?>



<div align="center">
  <table border="0" style="border-collapse: collapse" width="100%" id="table2">
    <tr>
      <td align="center">
      <form method="POST" action="<?php echo $_SERVER['PHP_SELF'];?>?submit=1">

<table cellSpacing="0" cellPadding="0" border="0" id="table3">
  <tr>
    <td align="right">
    <? echo" <p >".$lang['usrexp_usrname']."&nbsp;</td>";?>
    <td>
    <p ><input value="<? echo $_REQUEST['nick']; ?>" name="nick"></td>
  </tr>
  <tr>

    <td align="right">
    <? echo" <p >".$lang['usrexp_password']."&nbsp;</td>";?>
    <td>
    <p ><input type="password" value="<? echo $_REQUEST['passwd']; ?>" name="passwd"></td>
  </tr>
</table>

      <? echo"   <p><input type=\"submit\" value=\"".$lang['usrexp_export']."\" name=\"B_Export\"><input type=\"submit\" value=\"".$lang['usrexp_show']."\" name=\"B_Show\"></p>";?>
      </form>
      <p>&nbsp;</td>
    </tr>
  </table>
</div>





<?

function UserExport($nick,$key)
{

$filename = "userdata.xml";

$handle = fopen($filename, 'w');

$xmlcont="<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>
  <USERDATA>
  <NICK>$nick</NICK>
  <KEY>$key</KEY>
  </USERDATA>";

fwrite($handle, $xmlcont);

fclose($handle);

echo "<SCRIPT language=JScript src=\"/scripts.js\"></SCRIPT>
<META HTTP-EQUIV=\"Refresh\" CONTENT=\"1;URL=http://".$_SERVER['HTTP_HOST']."/forcedownload.php?file=userdata.xml\">";

echo "<center><a href=\"http://".$_SERVER['HTTP_HOST']."/userdata.xml\">If download doesn't start in 5 seconds you might try right-clicking here and selecting \"Save As\"</a></center>";

}


if (isset($_REQUEST['submit'])) {


$rawpasswd=pack("H*",md5($_REQUEST['passwd']));
$identificatore=md5($rawpasswd.$_REQUEST['nick']);

// echo "val -->".$_REQUEST["B_Show"];

$ris=mysql_query("SELECT PASSWORD from {$SNAME}_LOCALMEMBER WHERE HASH='{$identificatore}'");

if($pwd=mysql_fetch_assoc($ris))
{

if($_REQUEST['B_Show'])
{
echo "
<center>
<b>{$_REQUEST['nick']}</b><br>
<textarea rows='10' name='chiave' cols='70' readonly class='row2' style='border: none; overflow: auto'>
{$pwd['PASSWORD']}
</textarea>
</center>";
}

if($_REQUEST['B_Export'])
{
UserExport($_REQUEST['nick'],$pwd['PASSWORD']);
}


} else {
echo "<center><b>user not found !!!</b></center>";
}

}

include ("end.php");
?>
