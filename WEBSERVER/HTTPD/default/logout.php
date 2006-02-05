<?PHP
$whereiam="logout";
ob_start();
include ("testa.php");

// carico la lingua per le sezioni
$lang += $std->load_lang('lang_logout', $blanguage );

?>
<tR>
	<td>
	<?PHP
if ($_SESSION['sess_auth']) {
DestroySession();

echo "".$lang['logout_success']."<br>\n";
$SEZ_ID=$_REQUEST["SEZID"];
if ($SEZ_ID) {
   $THR_ID=$_REQUEST["THR_ID"];
   if ($THR_ID)
      $url="showmsg.php?SEZID=$SEZ_ID&THR_ID=$THR_ID";
   else
      $url="sezioni.php?SEZID=$SEZ_ID";
}
else $url="index.php";
echo "<br><center>".$lang['logout_loginred']."</center><script language=\"javascript\">setTimeout('delayer()', 1500);\nfunction delayer(){ window.location='$url';}</script>";
} else {

echo "".$lang['logout_notlogged']."<br>\n";

}

?>
	</td>
</tR>

<?PHP
include ("end.php");
?>