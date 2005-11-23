<?PHP
$whereiam="login";
include ("testa.php");

// carico la lingua per il login
$lang = $std->load_lang('lang_login', $blanguage );

?>
<tr>
  <td>
<?PHP
if ($sess_auth==1) {
  echo $lang['login_info1'];
} else {
  if ($_REQUEST["nick"]) {
    $rawpasswd=pack("H*",md5($_REQUEST["passwd"]));
    $identificatore=md5($rawpasswd.$_REQUEST["nick"]);
    $query="SELECT count(*) as 'num' FROM `".mysql_real_escape_string($_ENV[sesname])."_localmember` WHERE `HASH`='".mysql_escape_string($identificatore)."';";
    $risultato=mysql_query($query) or Muori ($lang['inv_query'] . mysql_error());
    if ($riga = mysql_fetch_assoc($risultato)) {
      if ($riga[num] == 0) Muori("".$lang['login_err']."\n<br>");
      $query="INSERT INTO `session` (`SESSID`,`IP`,`FORUM`,`NICK`,`DATE`,`PASSWORD`) ".
              "VALUES('".session_id()."',md5('".$_SERVER['REMOTE_ADDR']."'),'".mysql_real_escape_string($_ENV[sesname])."','".mysql_real_escape_string($_POST["nick"])."','".time()."','".mysql_real_escape_string($rawpasswd)."');";
      echo $lang['login_succ'];
      $SEZ_ID=$_REQUEST["SEZID"];
      if ($SEZ_ID) {
        $THR_ID=$_REQUEST["THR_ID"];
        if ($THR_ID)
          $url="showmsg.php?SEZID=$SEZ_ID&THR_ID=$THR_ID";
        else
          $url="sezioni.php?SEZID=$SEZ_ID";
      }
      else $url="index.php";
      echo "<br><center>".$lang['login_back']."</center><script language=\"javascript\">setTimeout('delayer()', 2000);\nfunction delayer(){ window.location='$url';}</script>";
      mysql_query($query) or Muori ($lang['inv_query'] . mysql_error());
    }
  }
  else {
    echo '
    <br><br><center>
    <form method="post" action="login.php" align="center">
    <input type="hidden" name="SEZID" value="'.$_REQUEST["SEZID"].'">
    <input type="hidden" name="THR_ID" value="'.$_REQUEST["THR_ID"].'">
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
      <td align="right">Nick:&nbsp;</td>
      <td><input type=text name=nick></td>
    </tr>
    <tr>
<!--      <td background="img/login_8.gif" height="24"><font size="1">&nbsp;</font></td>
      <td background="img/login_8.gif" align="right">Password:&nbsp;</td>
      <td background="img/login_8.gif"><input type=password name=passwd></td>
      <td background="img/login_8.gif" height="24"><font size="1">&nbsp;</font></td>-->
      <td align="right">Password:&nbsp;</td>
      <td ><input type=password name=passwd></td>
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
    </form> '.$lang['login_info2'].'<br></center>';
  }

}
?>
  </td>
</tr>
<?PHP
include ("end.php");
?>