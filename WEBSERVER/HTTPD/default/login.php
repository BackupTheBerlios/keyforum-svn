<?PHP
$whereiam="login";
ob_start();
include ("testa.php"); echo "<tr><td>";

// carico la lingua per il login
$lang += $std->load_lang('lang_login', $blanguage );
if ($_SESSION[$SNAME]['sess_auth']==1) 
{
	echo $lang['login_info1'];
}
else 
{
	if ($_REQUEST["nick"]) 
	{
		$rawpasswd=pack("H*",md5($_REQUEST["passwd"]));
		$identificatore=md5($rawpasswd.$_REQUEST["nick"]);

		$query="SELECT count(1) as 'num' FROM {$SNAME}_localmember WHERE HASH='".mysql_escape_string($identificatore)."';";
		$risultato=$db->get_var($query);
		if ($risultato==0) Muori("".$lang['login_err']."\n<br>");
		
		$query="INSERT INTO `session` (`SESSID`,`IP`,`FORUM`,`NICK`,`DATE`,`PASSWORD`) 
		VALUES('".session_id()."',md5('".$_SERVER['REMOTE_ADDR']."'),'".mysql_real_escape_string($_ENV[sesname])."','".mysql_real_escape_string($_POST["nick"])."','".time()."','".mysql_real_escape_string($rawpasswd)."');";
		
		if(isset($_POST['remember']))
		{
			$the_cookie = array(mysql_real_escape_string($_POST["nick"]),mysql_real_escape_string($rawpasswd));
			setcookie("sess_auth_{$SNAME}",serialize($the_cookie),time()+60*60*24*7);
		}
		$_SESSION[$SNAME]['sess_nick'] = mysql_real_escape_string($_POST["nick"]);
		$_SESSION[$SNAME]['sess_password'] = $rawpasswd;
		$_SESSION[$SNAME]['sess_auth'] = 1;
		
		echo $lang['login_succ'];
		$SEZ_ID=$_REQUEST["SEZID"];
		if ($SEZ_ID) 
		{
			$THR_ID=$_REQUEST["THR_ID"];
			if ($THR_ID)	$url="showmsg.php?SEZID=$SEZ_ID&THR_ID=$THR_ID";
			else			$url="sezioni.php?SEZID=$SEZ_ID";
		}
		else $url="index.php";
		$db->query($query) or Muori ($lang['inv_query'] . $db-debug());
		echo "<br><center>".$lang['login_back']."</center><script language=\"javascript\">setTimeout('delayer()', 2000);\nfunction delayer(){ window.location='$url';}</script>";
	}
	else 
	{	//SHOW FORM   ?>
    <br><br><center>
    <form method="post" action="login.php" align="center">
    <input type="hidden" name="SEZID" value="<?=$_REQUEST["SEZID"]?>">
    <input type="hidden" name="THR_ID" value="<?=$_REQUEST["THR_ID"]?>">
    <table border="0" cellspacing="0" cellpadding="0">
<!--    <tr>
      <td background="img/login_4.gif" height="22" width="8"><font size="1">&nbsp;</font></td>
      <td background="img/login_1.gif" colspan="2" align="center" height="22">
        <b>Login</b>
      </td>
      <td background="img/login_5.gif" height="22" width="4"><font size="1">&nbsp;</font></td>
      <td background="img/login_B.gif" height="22" width="8"><font size="1">&nbsp;</font></td>
    </tr>-->
    <tr>
<!--      <td background="img/login_8.gif" height="24"><font size="1">&nbsp;</font></td>
      <td background="img/login_8.gif" align="right">Nick:&nbsp;</td>
      <td background="img/login_8.gif"><input type=text name=nick></td>
      <td background="img/login_8.gif" height="24"><font size="1">&nbsp;</font></td>
      <td background="img/login_A.gif" rowspan="3" width="8"><font size="1">&nbsp;</font></td>-->
      <td align="right"><?=$lang['login_username']?>&nbsp;</td>
      <td><input type=text name=nick></td>
    </tr>
    <tr>
<!--      <td background="img/login_8.gif" height="24"><font size="1">&nbsp;</font></td>
      <td background="img/login_8.gif" align="right">Password:&nbsp;</td>
      <td background="img/login_8.gif"><input type=password name=passwd></td>
      <td background="img/login_8.gif" height="24"><font size="1">&nbsp;</font></td>-->
      <td align="right"><?=$lang['login_password']?>&nbsp;</td>
      <td ><input type=password name=passwd></td>
    </tr>
    <tr>
<!--      <td background="img/login_8.gif" height="24"><font size="1">&nbsp;</font></td>
      <td background="img/login_8.gif" align="right">Password:&nbsp;</td>
      <td background="img/login_8.gif"><input type=password name=passwd></td>
      <td background="img/login_8.gif" height="24"><font size="1">&nbsp;</font></td>-->
      <td align="right">Ricordami<?=$lang['login_remember']?>&nbsp;</td>
      <td align="left"><input type="checkbox" name="remember"></td>
    </tr>
    <tr>
<!--      <td background="img/login_3.gif" height="25"><font size="1">&nbsp;</font></td>
      <td background="img/login_3.gif" colspan="2" align="center" height="25">-->
      <td colspan="2" align="center" height="25">
        <input type="image" src="img/login_2.gif">
      </td>
<!--      <td background="img/login_3.gif" height="25"><font size="1">&nbsp;</font></td>-->
    </tr>
<!--    <tr>
      <td background="img/login_6.gif" width="8" height="11"><font size="1">&nbsp;</font></td>
      <td background="img/login_7.gif" colspan="3"><font size="1">&nbsp;</font></td>
      <td background="img/login_9.gif" width="8"><font size="1">&nbsp;</font></td>
    </tr>-->
    </table><br>
    </form> <?=$lang['login_info2']?><br></center>
	<?php
  }

}
?>
  </td>
</tr>
<?PHP
include ("end.php");
?>