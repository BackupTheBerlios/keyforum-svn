<?PHP

ob_start('ob_gzhandler'); 
include ("testa.php");

// carico la lingua per l'edit
$lang += $std->load_lang('lang_ednewmsg', $blanguage );

  if (!$_SESSION[$SNAME]['sess_auth']) {
    $url = "login.php?SEZID=".$_REQUEST["SEZID"]."&THR_ID=".$_REQUEST["EDIT_OF"]."&pag=".$_REQUEST["pag"];
    echo "<tr><td><center>".$lang['edmsg_login']."<br>";
    echo "".$lang['edmsg_loginred']."</center></td></tr><script language=\"javascript\">setTimeout('delayer()', 1500);\nfunction delayer(){ window.location='$url';}</script>";
    include ("end.php");
    exit(0);
  }

  $EDITID = mysql_real_escape_string(pack("H*",$_REQUEST["EDIT_OF"]));
  $query="SELECT title,subtitle,body from {$SNAME}_newmsg as msg where EDIT_OF='$EDITID' and visibile='1';";
  $riga = $db->get_row($query);
  if ($riga)
   {
    $Testo = htmlspecialchars(stripslashes($riga->body));
    $Titolo = secure_v($riga->title);
    $SubTitolo = secure_v($riga->subtitle);
  }
  else {
    $Testo = "";
    $Titolo = "";
  }
  if(($userdata->LEVEL)>0)
  {
	  $Pinned="";
	  $Fixed="";
	  $Home="";
	  $query="SELECT PINNED, FIXED, HOME from {$SNAME}_msghe WHERE HASH='$EDITID';";
	  $riga = $db->get_row($query);
	  if ($riga)
	  {
	    $Pinned=$riga->PINNED;
	    $Fixed=$riga->FIXED;
	    $Home=$riga->HOME;
	  }
   }

?>
<tr><td>

<script type="text/javascript">
<!--
  function altezze(){
    co1 = document.getElementById('colo-sx').offsetHeight;
    co2 = document.getElementById('colo-dx').offsetHeight;
    co3 = document.getElementById('centrale').offsetHeight;
    altok = co1;
    if (co2 > altok) altok = co2;
    if (co3 > altok) altok = co3;
    altokok = co1;
    if (co2 < altokok) altokok = co2;
    if (co3 < altokok) altokok = co3;
    document.getElementById('colo-sx').style.height = altok + 'px';
    document.getElementById('colo-dx').style.height = altok + 'px';
    document.getElementById('centrale').style.height = altok + 'px';
    document.getElementById('FastReply').style.height = altokok + 'px';
  }
//-->
</script>
<div id="FastReply" class='post2'>
<form method=post action="destnewmsg2.php">
<input type="hidden" name="edit_of" value="<?PHP echo $_REQUEST["EDIT_OF"];?>">
  <div id="colo-sx" class='post2' align=center>
    <table width="100" border="0" cellspacing="0" cellpadding="3">
      <tr>
        <td height='31' colspan='4'>
          <div align='center'><p><strong>Smiles:</strong></p></div>
        </td>
      </tr>
      <? include "emoticons.php"; ?>
    </table>
  </div>
  <div id="colo-dx" ></div>
  <div id="centrale" class='post2' align='center'>
    <br />
    <? include "buttons.php"; ?>
    <div>
      <table class=pformstrip width=60%>
        <tr>
	  <? echo "<td>".$lang['edmsg_Idsect']."</td>"; ?>
	  <td><input type=text name=sezid size=50 class=forminput value="<?PHP echo $_REQUEST["SEZID"];?>"></td>
        </tr>
        <tr>
          <? echo "<td>".$lang['edmsg_prvkey']."</tD>"; ?>
          <td><textarea cols="70" rows="3" name="PrivKey"></textarea></td>
        </tr>
        <tr>
	  <? echo "<td>".$lang['edmsg_title']."</td>"; ?>
	  <td><input type=text name=subject size=50 class=forminput value="<? echo $Titolo; ?>"></td>
        </tr>
        <tr>
	  <? echo "<td>".$lang['edmsg_descr']."</td>"; ?>
	  <td><input type=text name=desc size="50" class="forminput" value="<? echo $SubTitolo; ?>"></td>
        </tr>
        <tr>
	  <? echo "<td>".$lang['edmsg_body']."</td>"; ?>
	  <td><textarea name="body" rows="15" cols="70" wrap="virtual"><? echo $Testo; ?></textarea></tD>
        </tr>
        <tr>
	  <? echo "<td>".$lang['edmsg_avatar']."</td>"; ?>
	  <td><input type=text name=avatar size=50 class=forminput></td>
        </tr>
        <tr>
	  <? echo "<td>".$lang['edmsg_signature']."</td>"; ?>
	  <td><textarea cols=70 rows=2 name=firma></textarea></td>
        </tr>
        <? if(($userdata->LEVEL)>0) { ?>
        <tr>
          <td></td>
          <? if($Pinned) $Pinned=" checked='checked'";
             if($Fixed) $Fixed=" checked='checked'";
             if($Home) $Home=" checked='checked'"; ?>
          <td><input name='extvar' type='hidden' value='1' />
              <input name='pinned' type='checkbox' value='<? echo $lang['edmsg_pinned']."'".$Pinned." /> ".$lang['edmsg_pinned']; ?>
              <input name='fixed' type='checkbox' value='<? echo $lang['edmsg_fixed']."'".$Fixed." /> ".$lang['edmsg_fixed']; ?>
              <input name='home' type='checkbox' value='<? echo $lang['edmsg_home']."'".$Home." /> ".$lang['edmsg_home']; ?>
          </td>
        </tr>
        <? } ?>
      </table>
    </div>
    <br />
    <center>
      <? echo "<input type='submit' name='submit' value='".$lang['edmsg_submit']."' accesskey='s' /><br />"; ?>
<? echo "".$lang['edmsg_info1']."<bR>"; ?>
<? echo "".$lang['edmsg_info2']."<br>"; ?>
    </center>
    <br />
  </div>
</form>
</div>
<script language='JavaScript'>
<? include "bbcode.php"; ?>
</script>
</td></tr>
<?PHP
include ("end.php");
?>
