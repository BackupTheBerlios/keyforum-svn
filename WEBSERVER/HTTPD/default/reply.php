<?php
include "testa.php";

// carico la lingua per le sezioni
$lang += $std->load_lang('lang_reply', $blanguage );

$SNAME=$_ENV["sesname"];
$MSGID=mysql_real_escape_string(pack("H*",$_REQUEST["THR_ID"]));
$query="SELECT {$SNAME}_newmsg.title as title, {$SNAME}_membri.AUTORE as autore"
  ." FROM {$SNAME}_msghe,{$SNAME}_newmsg,{$SNAME}_membri"
  ." WHERE {$SNAME}_newmsg.EDIT_OF={$SNAME}_msghe.HASH"
  ." AND {$SNAME}_newmsg.EDIT_OF='".$MSGID."'"
  ." AND {$SNAME}_newmsg.visibile='1'"
  ." AND {$SNAME}_membri.HASH={$SNAME}_msghe.AUTORE";
$riga=$db->get_row($query);


if (!$sess_auth) {
  $url = "login.php?SEZID=".$_REQUEST["SEZID"]."&THR_ID=".$_REQUEST["THR_ID"]."&pag=".$_REQUEST["pag"];
  echo "<tr><td><center>".$lang['reply_login']."<br />";
  echo "".$lang['reply_loginred']."</center></td></tr><script language=\"javascript\">setTimeout('delayer()', 1500);\nfunction delayer(){ window.location='$url';}</script>";
  include "end.php";
  exit;
}
if ($riga) {
  echo "<tr><td>".$lang['reply_info1']."<b>$riga->autore</b> ".$lang['reply_info2']." \"".secure_v($riga->title)."\":<br />";
}
else {
	$std->Error($lang['reply_notfound']);
 /* echo "<tr><td>".$lang['reply_notfound']."</td></tr>\n";
  include "end.php";*/
  exit;
}

$Quote = mysql_real_escape_string(pack("H*",$_REQUEST["quote"]));
if ($Quote == $MSGID) {
  $query="SELECT body,(reply.`date`+".GMT_TIME.") as data,membri.autore from {$SNAME}_newmsg as reply,{$SNAME}_membri as membri where reply.autore=membri.hash and reply.EDIT_OF='$Quote' and visibile='1';";
}
else {
  $query="SELECT body,(reply.`date`+".GMT_TIME.") as data,membri.autore from {$SNAME}_reply as reply,{$SNAME}_membri as membri where reply.autore=membri.hash and reply.EDIT_OF='$Quote' and visibile='1';";
}
$riga=$db->get_row($query);

if ($riga) {
  $quote_date = strftime("%d/%m/%y  - %H:%M:%S",$riga->data);
  $nquote = "[quote=".secure_v($riga->autore)." @ $quote_date]";
  $box_text = $nquote.htmlspecialchars(stripslashes($riga->body))."[/quote]";
}
else {
  $box_text = "";
}

// Preview

if($_REQUEST["preview"]) {
  // Parser
  include_once("lib/bbcode_parser.php");
  
  $box_text = htmlspecialchars(stripslashes($_REQUEST["body"]));
  $preview_text = "<div style=\"padding: 10px;width:95%\">{$lang['reply_preview']}:<br /><br />".convert(secure_v($_REQUEST["body"]))."</div><hr />";
}

echo<<<EOF
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
  
function Validator(theForm)
{

// allow only 255 characters maximum in the signature field
if (theForm.firma.value.length > 255)
   {
alert("{$lang['signature_to_long']} "+theForm.firma.value.length);
theForm.firma.focus();
return (false);
   }
}  
  
  
  
//-->
</script>

<div id="FastReply" class='post2'>
{$preview_text}
<form name="reply" action="reply_dest.php" method="post" onsubmit="return Validator(this)" >
  <input type="hidden" name="sezid" value='{$_REQUEST["SEZID"]}' />
  <input type="hidden" name="repof" value='{$_REQUEST["THR_ID"]}' />
  <input type="hidden" name="THR_ID" value='{$_REQUEST["THR_ID"]}' />
  <input type="hidden" name="quote" value='{$_REQUEST["quote"]}' />

  <div id="colo-sx" class='post2' align=center>
    <table width="100" border="0" cellspacing="0" cellpadding="3">
      <tr>
        <td height='31' colspan='4'>
          <div align='center'><p><strong>Smiles:</strong></p></div>
        </td>
      </tr>
EOF;

include "emoticons.php";

echo<<<EOF
    </table>
  </div>
  <div id="colo-dx" ></div>
  <div id="centrale" class='post2' align='center'>
    <br />
EOF;

include "buttons.php";

echo<<<EOF
    <div>
      <table class=pformstrip width="60%">
        <tr>
          <td>{$lang['reply_title']}</td>
          <td><input type="text" name="title" size="50" class="forminput" /></td>
        </tr>
        <tr>
          <td>{$lang['reply_body']}</td>
          <td><textarea name="body" rows="15" cols="70" wrap="virtual" onselect="storeCaret(this);" onclick="storeCaret(this);" onkeyup="storeCaret(this);"  >{$box_text}</textarea></td>
        </tr>
        <tr>
	  <td>{$lang['reply_avatar']}</td>
          <td><input type="text" name="avatar" size="50" class="forminput" /></td>
        </tr>
        <tr>
          <td>{$lang['reply_signature']}</td>
          <td><textarea cols="70" rows="2" name="firma"></textarea></td>
        </tr>
      </table>
    </div>
    <br />
    <center>
      <input type='submit' name='submit' value='{$lang['reply_submit']}' accesskey='s' />
      <input type='submit' name='preview' value='{$lang['reply_preview']}' accesskey='p' onclick="document.reply.action='reply.php';document.reply.submit()" /><br />
      {$lang['reply_info3']}<br />
      {$lang['reply_info4']}<br />
    </center>
    <br />
  </div>
</form>
</div>
<script language='JavaScript'>
EOF;

include "bbcode.php";

echo<<<EOF
</script>
EOF;

include "end.php";
?>
