<?PHP

ob_start('ob_gzhandler');
include ("testa.php");

// carico la lingua per l'edit
$lang += $std->load_lang('lang_edreply', $blanguage );

$SNAME=$_ENV[sesname];
$MSGID=mysql_escape_string(pack("H*",$_REQUEST["REP_OF"]));
$query="SELECT {$SNAME}_newmsg.title as title, {$SNAME}_membri.AUTORE as autore"
  ." FROM {$SNAME}_msghe,{$SNAME}_newmsg,{$SNAME}_membri"
  ." WHERE {$SNAME}_newmsg.EDIT_OF={$SNAME}_msghe.HASH"
  ." AND {$SNAME}_newmsg.EDIT_OF='".$MSGID."'"
  ." AND {$SNAME}_newmsg.visibile='1'"
  ." AND {$SNAME}_membri.HASH={$SNAME}_msghe.AUTORE";

  echo "<tr><td>";
  if (!$sess_auth) {
    $url = "login.php?SEZID=".$_REQUEST["SEZID"]."&THR_ID=".$_REQUEST["REP_OF"]."&pag=".$_REQUEST["pag"];
    echo "<tr><td><center>".$lang['edrep_login']."<br>";
    echo "".$lang['edrep_loginred']."</center></td></tr><script language=\"javascript\">setTimeout('delayer()', 1500);\nfunction delayer(){ window.location='$url';}</script>";
    include ("end.php");
    exit(0);
  }
if ($riga = $db->get_row($query)) 
{
	echo "{$lang['edrep_info1']}<b>$riga->autore</b>{$lang['edrep_info2']}\"$riga->title\":<br>";
} 
else
{
	$std-Error($lang['edrep_notfound']);
	exit();
}
  $EDITID = mysql_real_escape_string(pack("H*",$_REQUEST["EDIT_OF"]));
  $query="SELECT title,body from {$SNAME}_reply as reply where EDIT_OF='$EDITID' and visibile='1';";

  if ($riga = $db->get_row($query)) {
    $Testo = htmlspecialchars(stripslashes($riga->body));
    $Titolo = secure_v($riga->title);
  }
  else {
    $Testo = "";
    $Titolo = "";
  }
?>

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
<form action="reply_dest.php" method="post">
  <input type="hidden" name="sezid" value='<?PHP echo $_REQUEST["SEZID"];?>'>
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
      <table class=pformstrip width="60%">
        <tr>
          <? echo"<td>".$lang['edrep_repof']."</td>"; ?>
          <td><input type=text name=repof size=50 class=forminput value="<?PHP print $_REQUEST["REP_OF"];?>"></td>
        </tr>
        <tr>
          <? echo"<td>".$lang['edrep_edof']."</td>"; ?>
          <td><input type=text name=edit_of size=50 class=forminput value="<?PHP print $_REQUEST["EDIT_OF"];?>"></td>
        </tr>
        <tr>
          <? echo"<td>".$lang['edrep_title']."</td>"; ?>
          <td><input type=text name=title size=50 class=forminput><? echo $Titolo; ?></td>
        </tr>
        <tr>
          <? echo"<td>".$lang['edrep_body']."</td>"; ?>
          <td><textarea name="body" rows="15" cols="70" wrap="virtual"><? echo $Testo; ?></textarea></td>
        </tr>
        <tr>
          <? echo" <td>".$lang['edrep_avatar']."</td>"; ?>
          <td><input type=text name=avatar size=50 class=forminput></td>
        </tr>
        <tr>
          <? echo"<td>".$lang['edrep_signature']."</td>"; ?>
          <td><textarea cols=70 rows=2 name=firma></textarea></td>
        </tr>
      </table>
    </div>
    <br />
    <center>
      <? echo"<input type='submit' name='submit' value='".$lang['edrep_submit']."' accesskey='s' /><br />"; ?>
<? echo"".$lang['edrep_info3']."<bR>"; ?>
<? echo"".$lang['edrep_info4']."<br>"; ?>
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
