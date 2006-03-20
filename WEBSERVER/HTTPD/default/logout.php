<?PHP
$whereiam="logout";
ob_start();
include ("testa.php");
// carico la lingua per le sezioni
$lang += $std->load_lang('lang_logout', $blanguage );

?>
<tr>
	<td>
		<?PHP
		if (!$_SESSION[$SNAME]['sess_auth']) 
		{
			$std->Error($lang['logout_notlogged']);
		}
		
		$SEZ_ID=$_REQUEST["SEZID"];
		$THR_ID=$_REQUEST["THR_ID"];
				
		DestroySession();
		do
		{
			$url="index.php";
			if ($THR_ID)
			{
				$url="showmsg.php?SEZID=$SEZ_ID&THR_ID=$THR_ID";
				break;
			}
			if ($SEZ_ID) 
			{
				$url="sezioni.php?SEZID=$SEZ_ID";
				break;
			}
		}while(false);
		?>
		
		<?=$lang['logout_success']?><br>
		<center><?=$lang['logout_loginred']?></center>
		<script  type="text/javascript" language='javascript'>
			setTimeout('delayer()', 1500);
			function delayer(){ window.location='<?=$url?>';}
		</script>
	</td>
</tr>

<?PHP
include ("end.php");
?>