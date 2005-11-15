<?php
  include ("testa.php");

// carico la lingua per le sezioni
$lang = $std->load_lang('lang_reply', $blanguage );

  $SNAME=$_ENV["sesname"];
  $MSGID=mysql_real_escape_string(pack("H*",$_REQUEST["THR_ID"]));
  $query="SELECT {$SNAME}_newmsg.title as title, {$SNAME}_membri.AUTORE as autore"
    ." FROM {$SNAME}_msghe,{$SNAME}_newmsg,{$SNAME}_membri"
    ." WHERE {$SNAME}_newmsg.EDIT_OF={$SNAME}_msghe.HASH"
    ." AND {$SNAME}_newmsg.EDIT_OF='".$MSGID."'"
    ." AND {$SNAME}_newmsg.visibile='1'"
    ." AND {$SNAME}_membri.HASH={$SNAME}_msghe.AUTORE";
  $risultato=mysql_query($query) or Muori ($lang['inv_query'] . mysql_error());

  if (!$sess_auth) {
    $url = "login.php?SEZID=".$_REQUEST["SEZID"]."&THR_ID=".$_REQUEST["THR_ID"]."&pag=".$_REQUEST["pag"];
    echo "<tr><td><center>".$lang['reply_login']."<br>";
    echo "".$lang['reply_loginred']."</center></td></tr><script language=\"javascript\">setTimeout('delayer()', 1500);\nfunction delayer(){ window.location='$url';}</script>";
    include ("end.php");
    exit(0);
  }
  if ($riga = mysql_fetch_assoc($risultato)) {
    print "<tr><td>".$lang['reply_info1']."<b>$riga[autore]</b> ".$lang['reply_info2']." \"".secure_v($riga['title'])."\":<br>";
  }
  else {
    print "<tr><td>".$lang['reply_notfound']."</td></tr>\n";
    include ("end.php");
    exit();
  }

  $Quote = mysql_real_escape_string(pack("H*",$_REQUEST["quote"]));
  if ($Quote == $MSGID)
    $query="SELECT body,(reply.`date`+".GMT_TIME.") as data,membri.autore from {$SNAME}_newmsg as reply,{$SNAME}_membri as membri where reply.autore=membri.hash and reply.EDIT_OF='$Quote' and visibile='1';";
  else
    $query="SELECT body,(reply.`date`+".GMT_TIME.") as data,membri.autore from {$SNAME}_reply as reply,{$SNAME}_membri as membri where reply.autore=membri.hash and reply.EDIT_OF='$Quote' and visibile='1';";
  $risultato=mysql_query($query);
  if ($riga = mysql_fetch_assoc($risultato)) {
    $Data = strftime("%d/%m/%y  - %H:%M:%S",$riga["data"]);
    $Testo = "[quote=".secure_v($riga["autore"])." @ $Data]\n".htmlspecialchars($riga["body"])."\n[/quote]";
  }
  else
    $Testo = "";
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
<form action="reply_dest.pl" method="post">
  <input type="hidden" name="sezid" value='<?php echo $_REQUEST["SEZID"];?>'>
  <input type="hidden" name="repof" value='<?php print $_REQUEST["THR_ID"];?>'>

  <div id="colo-sx" class='post2' align=center>
    <table width="100" border="0" cellspacing="0" cellpadding="3">
      <tr>
        <td height='31' colspan='4'>
          <div align='center'><p><strong>Smiles:</strong></p></div>
        </td>
      </tr>
      <?php include "emoticons.php"; ?>
    </table>
  </div>
  <div id="colo-dx" ></div>
  <div id="centrale" class='post2' align='center'>
    <br />
    <?php include "buttons.php"; ?>
    <div>
      <table class=pformstrip width="60%">
        <tr>
          <?php echo "<td>".$lang['reply_title']."</td>"; ?>
          <td><input type="text" name="title" size="50" class="forminput"></td>
        </tr>
        <tr>
          <?php echo "<td>".$lang['reply_body']."</td>"; ?>
          <td><textarea name="body" rows="15" cols="70" wrap="virtual"><?php echo $Testo; ?></textarea></td>
        </tr>
        <tr>
					<?php echo "<td>".$lang['reply_avatar']."</td>"; ?>
          <td><input type="text" name="avatar" size="50" class="forminput"></td>
        </tr>
        <tr>
          <?php echo "<td>".$lang['reply_signature']."</td>"; ?>
          <td><textarea cols="70" rows="2" name="firma"></textarea></td>
        </tr>
      </table>
    </div>
    <br />
    <center>
      <?php echo "<input type='submit' name='submit' value='".$lang['reply_submit']."' accesskey='s' /><br />"; ?>
<?php echo "".$lang['reply_info3']."<bR>"; ?>
<?php echo "".$lang['reply_info4']."<br>"; ?>
    </center>
    <br />
  </div>
</form>
</div>
<script language='JavaScript'>
<?php include "bbcode.php"; ?>
</script>



<?php
include ("end.php");
?>
