<?PHP
// v1.1

ob_start('ob_gzhandler');
include ("testa.php");

// carico la lingua per writenewmsg
$lang += $std->load_lang('lang_writenewmsg', $blanguage );
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

<form method="post" action="destnewmsg2.php">
<input type='hidden' name='sezid' value="<?=$_REQUEST["SEZID"]?>">

  <div id="colo-sx" class='post2' align=center>
    <table width="100" border="0" cellspacing="0" cellpadding="3">
      <tr>
        <td height='31' colspan='4'>
          <div align='center'><p><strong><?=$lang['wreply_smiles']?></strong></p></div>
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
      <table class=pformstrip width="60%">
        <tr>
          <? echo "<td>".$lang['wreply_title']."</td>"; ?>
          <td><input type=text name=subject size=50 class=forminput></td>
        </tr>
        <tr>
          <? echo "<td>".$lang['wreply_subtitle']."</td>"; ?>
          <td><input type=text name=desc size=50 class=forminput></td>
        </tr>
        <tr>
          <? echo "<td>".$lang['wreply_body']."</td>"; ?>
          <td><textarea name="body" rows="15" cols="70" wrap="virtual"></textarea></tD>
        </tr>
<?PHP
if ($SEZ_DATA->PKEY) {
    print "
        <tr>
          <td>".$lang['wreply_privkey']."</tD>
          <td><textarea cols=\"70\" rows=\"3\" name=\"PrivKey\"></textarea></tD>
        </tr>\n";
}
?>
        <tr>
          <? echo "<td>".$lang['wreply_avatar']."</td>"; ?>
          <td><input type=text name=avatar size=50 class=forminput></td>
        </tr>
        <tr>
          <? echo "<td>".$lang['wreply_signature']."</td>"; ?>
          <td><textarea cols="70" rows="2" name=firma></textarea></td>
        </tr>
      </table>
    </div>
    <br />
    <center>
      <? echo "<input type='submit' name='submit' value='".$lang['wreply_submit']."' accesskey='s' /><br />"; ?>
<? echo "".$lang['wreply_info1']."<bR>"; ?>
<? echo "".$lang['wreply_info2']."<br>"; ?>
    </center>
    <br />
  </div>
</form>
</div>

<script language='JavaScript' type="text/javascript">
<? include "bbcode.php"; ?>
</script>

</td></tr>
<?PHP
include ("end.php");
?>
