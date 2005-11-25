<?php
include ("testa.php");
$lang = $std->load_lang('lang_showmsg', $blanguage );

// Parser
include_once("lib/bbcode_parser.php");

function GetUserColor($MSG) {
  $userhash=unpack("H32hex",$MSG['memhash']);
  // colori normali
  $usercolor['sx_color']=substr($userhash['hex'],0,6);
  $usercolor['dx_color']=substr($userhash['hex'],-6);
  // colori invertiti
  $usercolor['sx_color_i']=dechex(16777215- hexdec($usercolor['sx_color']));
  $usercolor['dx_color_i']=dechex(16777215- hexdec($usercolor['dx_color']));
  return $usercolor;
}

function printmsg($MSG,$usercolor) {
  global $GLOBALS;
  global $blanguage;
  global $lang;
  $mio_nick = $GLOBALS['sess_nick'];
  if ($MSG['date'])
    $write_date=strftime("%d/%m/%y  - %H:%M:%S",$MSG['date']);
  $hash=unpack("H32hex",$MSG['hash']);
  if (eregi("http:\/\/", secure_v($MSG['avatar'])))
    $avatar="<img src='".$MSG['avatar']."'><br />";
  if ($MSG['regdate'])
    $register_date=strftime("%d/%m/%y",$MSG['regdate']);
  if ($MSG['gruppo'])
    $gruppo=$MSG['title']; else $gruppo="membri";

  if ($MSG['memhash']) {
    $tmp=unpack("H32hash",$MSG['memhash']);
    $autore="<a href='showmember.php?MEM_ID=".$tmp['hash']."'>".secure_v($MSG['autore'])."</a>";
    if ($MSG['is_auth'])
      $auth="<b>YES</b>";
    else
      $auth="<a href='admin.pl?action=AuthMem&HASH=".$tmp['hash']."'>NO</a>";
  }
  else {
    $autore=secure_v($MSG['autore']);
    if ($MSG['is_auth'])
      $auth="<b>YES</b>";
    else
      $auth="NO";
  }
  if ($MSG['autore']==$mio_nick)
    $edit_width="";
  else
    $edit_width=' width="35" ';
  if ($MSG['repof']) {
    $tmp=unpack("H32repof/H32mshash", $MSG['repof'].$MSG['hash']);
    $EDITER="<a href='edreply.php?REP_OF=".$tmp['repof']."&EDIT_OF=".$tmp['mshash']."&SEZID=".$_REQUEST["SEZID"]."'><img src=\"img/p_edit.gif\" border=\"0\" alt=\"Edit\" $edit_width></a>";
  }
  elseif ($MSG[SEZ]) {
    $tmp=unpack("H32mshash", $MSG['edit_of']);
    $EDITER="<a href='ednewmsg.php?EDIT_OF=$tmp[mshash]&SEZID=$MSG[SEZ]'><img src=\"img/p_edit.gif\" border=\"0\" alt=\"Edit\" $edit_width></a>";
  }
  if($MSG['edit_of']!=$MSG['hash']){
     $queryaut="SELECT AUTORE FROM `".$_ENV["sesname"]."_membri` WHERE HASH='".mysql_real_escape_string($MSG['real_autore'])."' LIMIT 1;";
     $risultatoaut=mysql_query($queryaut) or Muori ($lang['inv_query'] . mysql_error());
     while ($rigaaut = mysql_fetch_assoc($risultatoaut)) $realautore=$rigaaut['AUTORE'];
     $MSG['body'] = $MSG['body']."\n\n\n\n [SIZE=1][COLOR=blue]".$lang['shmsg_modby']." ".secure_v($realautore)." ".$lang['shmsg_on']." ".strftime("%d/%m/%y  - %H:%M:%S",$MSG['real_date'])."[/COLOR][/SIZE]";
  }
  if(($MSG['real_hash'])AND($MSG['edit_of']!=$MSG['real_hash'])){
     $queryaut="SELECT AUTORE FROM `".$_ENV["sesname"]."_membri` WHERE HASH='".mysql_real_escape_string($MSG['real_autore'])."' LIMIT 1;";
     $risultatoaut=mysql_query($queryaut) or Muori ($lang['inv_query'] . mysql_error());
     while ($rigaaut = mysql_fetch_assoc($risultatoaut)) $realautore=$rigaaut['AUTORE'];
     $MSG['body'] = $MSG['body']."\n\n\n\n [SIZE=1][COLOR=blue]".$lang['shmsg_modby']." ".secure_v($realautore)." ".$lang['shmsg_on']." ".strftime("%d/%m/%y  - %H:%M:%S",$MSG['real_date'])."[/COLOR][/SIZE]";
  }
  
  $MSG['body'] = secure_v($MSG['body']);
  $MSG['firma'] = secure_v($MSG['firma']);
  
  echo<<<EOF
<table width=100% border='0' cellspacing='1' cellpadding='3'>
<tr>
 <td valign='middle' class='row4' width='1%'><span class='normalname'><u>{$autore}</u></a></span>
   <a name={$postid}></a>
 </td>
 <td class='row4' valign='top' width='99%'>
  <div align='left' class='row4' style='float:left;padding-top:4px;padding-bottom:4px'>
   <span class='postdetails'><b>{$lang['shmsg_sendon']}</b>{$write_date}</span>
  </div>
  <div align='right'>
  </div>
 </td>
</tr>
<tr>
 <td valign='top' class='post2'>
  <span class='postdetails'><br />
  {$avatar}
  {$lang['shmsg_mbrtype']}<br />
  {$lang['shmsg_adminauth']}{$auth}<br />
  {$lang['shmsg_group']}{$gruppo}<br />
  {$lang['shmsg_messages']}{$MSG['msg_num']}<br />
  {$lang['shmsg_joined']}{$register_date}<br /><br />
  </span><br />
  <div align="center"><span
   style="padding:2px;background:#{$usercolor['sx_color']};color:#{$usercolor['sx_color_i']};">{$usercolor['sx_color']}</span><span
   style="padding:2px;background:#{$usercolor['dx_color']};color:#{$usercolor['dx_color_i']};">{$usercolor['dx_color']}</span>
  </div>
  <img src='img/spacer.gif' alt='' width='160' height='1' /><br />
 </td>
 <td width='100%' valign='top' class='post2'>
  <table border=1 bordercolor='#DEDEFF' cellspacing=0 cellpadding=0 width=100%>
   <tr>
    <td bordercolor='#F0F0FF' class='postdetails'><b>{$lang['shmsg_title']}

</b>
EOF;
 $title=$MSG['title'];
 if($MSG['subtitle']){
    $title=$title.", ".$MSG['subtitle'];
 }

 echo secure_v($title)."</td>
   </tr>
  </table><br />
  <div class='postcolor'> ".convert($MSG['body'])."</div>
  <br /><br />--------------------<br />
  <div class='signature'>".convert($MSG['firma'])."</div>
 </td>
</tr>
<tr>
 <td class='darkrow3' align='left'><b></b></td>
 <td class='darkrow3' nowrap='nowrap' align='left'>
   <!-- PM / EMAIL / WWW / MSGR / UP -->
   <div align='left' class='darkrow3' style='float:left;width:auto'>
     <a href='javascript:scroll(0,0);'><img src=\"img/12.gif\" border=\"0\"></a>
   </div>

   <!-- REPORT / UP -->
   <div align='right'>
   $EDITER
   <a href=\"reply.php?SEZID=".$_REQUEST["SEZID"]."&THR_ID=".$_REQUEST["THR_ID"]."&quote=$tmp[mshash]\"><img src='img/buttons/".$blanguage."/p_quote.gif' alt='Quote' border='0'></a>
   </div>
 </td>
</tr>
</table>
<div class='darkrow1' style='height:5px'><!-- --></div>
";

}

function PageSelect($pos) {
?>
<table border="0" cellpadding="5px" cellspacing="0" width="100%">
  <tbody>
  <tr>
    <td align="left" nowrap="nowrap" width="20%">
<?php
  global $NumPag;
  global $CurrPag;
  global $Section;
  global $blanguage;
  global $lang;
  $link = "showmsg.php?SEZID=".$_REQUEST["SEZID"]."&THR_ID=".$_REQUEST["THR_ID"]."&pag=";
  if ($NumPag > 0) {
    echo "<span class='pagelink'>".($NumPag+1)."&nbsp;".$lang['shmsg_pages']."</span>&nbsp;";
    if ($CurrPag>0) { # Pagina precedente
      echo "<span class='pagelinklast'><a href=\"{$link}0\">&laquo;</a></span>&nbsp;";
      echo "<span class='pagelink'><a href=\"{$link}".($CurrPag-1)."\">&lt;</a></span>&nbsp;";
    }

    # Visualizzo i link solamente per un certo numero di pagine
    if ($CurrPag > $Section) {print "<span class='pagelink'>..</span>&nbsp;";}
      $StartPag = $CurrPag-$Section;
    if ($StartPag < 0) {$StartPag = 0;}
      $EndPag = $CurrPag+$Section;
    if ($EndPag > $NumPag) {$EndPag = $NumPag;}

    for ($i = $StartPag+1; $i <= $EndPag+1; $i++) {
      if ($i-1 == $CurrPag)
        echo "<span class='pagecurrent'>$i</span>&nbsp;";
      else
        echo "<span class='pagelink'><a href=\"{$link}".($i-1)."\">$i</a></span>&nbsp;";
    }
    if ($CurrPag < $NumPag - $Section) {print "<span class='pagelink'>..</span>&nbsp;";}

    if ($CurrPag<$NumPag) { # Pagina successiva
      echo "<span class='pagelink'><a href=\"{$link}".($CurrPag+1)."\">&gt;</a></span>";
      echo "&nbsp;<span class='pagelinklast'><a href=\"{$link}{$NumPag}\">&raquo; ".($NumPag+1)."</a></span>";
    }
  }

  if ($pos==1) { # Opzioni di inizio pagina
    ?>
    </td>
    <td align="right" width="80%">
      <a href="reply.php?SEZID=<?php echo $_REQUEST["SEZID"]; ?>&THR_ID=<?php echo $_REQUEST["THR_ID"];?>"> <?php echo " <img src='img/buttons/".$blanguage."/t_reply.gif' border='0' alt='Rispondi' /></a>"; ?>
    </td>
    <td align="right" width="80%">
      <a href="writenewmsg.php?SEZID=<?php echo $_REQUEST[SEZID]; ?>" class="normalname"> <?php echo " <img src='img/buttons/".$blanguage."/t_new.gif' border='0' alt='Apri Nuovo Topic' /></a>"; ?>
    </td>
    </tr>
    <?php
  } else {
    echo "
	<script type=\"text/javascript\">
	<!--
		var dom = (document.getElementById && !document.all)? 1: 0;
		function show_hide(the_id)
		{
			var obj = (dom)? document.getElementById(the_id): document.all[the_id];
			if(obj.style.visibility == \"hidden\"){
				obj.style.visibility = \"visible\";
				obj.style.display = \"block\";
			}
			else {
				obj.style.visibility = \"hidden\";
				obj.style.display = \"none\";
			}
		}
	-->
	</script>
	";
	global $sess_auth;
	if($sess_auth){
	  $logged="javascript:show_hide('FastReply')";
	}else{
	  $logged="login.php?SEZID=".$_REQUEST["SEZID"]."&THR_ID=".$_REQUEST["THR_ID"]."&pag=".$_REQUEST["pag"];
	}
  	echo "</td><td align='right' width='80%' class='normalname'><a href=\"$logged\"><img src='img/buttons/".$blanguage."/t_qr.gif' border='0' alt='FastReply' /></a></td><td align='right' width='80%'><a href=\"reply.php?SEZID={$_REQUEST['SEZID']}&THR_ID={$_REQUEST['THR_ID']}\"><img src=\"img/buttons/".$blanguage."/t_reply.gif\" border=\"0\" alt=\"Rispondi\" /></a></td><td align='right' width='80%'><a href=\"writenewmsg.php?SEZID={$_REQUEST['SEZID']}\" class=\"normalname\"><img src=\"img/buttons/".$blanguage."/t_new.gif\" border=\"0\" alt=\"Apri Nuovo Topic\" /></a></td></tr>\n";

  }
  echo "</tbody>\n</table>\n";
}

function FastReply() {
	global $lang;
	echo "<script language='JavaScript'>";
        include "bbcode.php";
	echo"
		</script>
		<script type=\"text/javascript\">
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

		<div id=\"FastReply\" style=\"visibility:hidden;display:none;\" class='post2'>
		<form method=post action='reply_dest.php'>
			<input type=hidden name=\"sezid\" value='{$_REQUEST[SEZID]}' />
			<input type=hidden name=\"repof\" value='{$_REQUEST[THR_ID]}' />
		<div id=\"head01\" align=left class='maintitle'>
			FastReply:
		</div>
		<div id=\"colo-sx\" class='post2' align='center'>
					<br />
   		<table border=\"0\" cellspacing=\"0\" cellpadding=\"3\">
    		<tr>
     			<td height='31' colspan='4'>
      			<div align='center'><p><strong>Smiles:</strong></p></div>
     			</td>
    		</tr>";
        include "emoticons.php";
    	echo "</table>
   		</div>
   		<div id=\"colo-dx\"></div>
  		<div id=\"centrale\" class='post2' align=center>
		<br />
		<table border='0' cellspacing='0' cellpadding='0'>
		 <tr>
		  <td align='center'>";
	include "buttons.php";
	echo "	  </td>
		  <td width='100%'>
		 </tr>
		 <tr>
		  <td>
			<textarea name=\"body\" rows=\"8\" cols=\"70\" wrap=\"virtual\"></textarea>
			<br /><br />
			<center>
			<input type='submit' name='submit' value='".$lang['shmsg_submit']."' accesskey='s' />
			<input type='button' name='nfr' onclick=\"show_hide('FastReply');\" value='".$lang['shmsg_hidefp']."' accesskey='h' />
			</center>
			<br />
		  </td>
		  <td>
		  </td>
		 </tr>
		</table>
		</div>
		</form>
		</div>
	";
}

?>
<tr>
<td>
<?php

$SNAME=$_ENV['sesname'];
$MSGID=pack("H*",$_REQUEST['THR_ID']);
$query="SELECT newmsg.HASH as hash,newmsg.title as title, membri.AUTORE as autore, newmsg.SUBTITLE as subtitle,"
." newmsg.BODY as body, (msghe.DATE+".GMT_TIME.") as 'date', membri.avatar AS 'avatar', membri.firma AS 'firma',"
." (membri.DATE+".GMT_TIME.") AS 'regdate', membri.msg_num AS 'msg_num',membri.title as 'gruppo',"
." membri.is_auth AS 'is_auth', membri.HASH AS 'memhash', newmsg.AUTORE AS 'real_autore', (newmsg.DATE+".GMT_TIME.") AS 'real_date',"
." newmsg.SEZ AS 'SEZ', newmsg.EDIT_OF as edit_of, msghe.reply_num as reply_num"
." FROM {$SNAME}_msghe AS msghe,{$SNAME}_newmsg AS newmsg,{$SNAME}_membri AS membri"
." WHERE newmsg.EDIT_OF=msghe.HASH"
." AND newmsg.EDIT_OF='".mysql_escape_string($MSGID)."'"
." AND newmsg.visibile='1'"
." AND membri.HASH=msghe.AUTORE"
.";"
;
$risultato=mysql_query($query) or Muori ($lang['inv_query'] . mysql_error());

if (!($riga = mysql_fetch_assoc($risultato))) {
  echo "".$lang['shmsg_msgnotfound']."\n\t</td>\n</tR>\n";
  include ("end.php");
  exit(0);
}


/*
  Preparo le variabili per la selezione delle pagine...
*/
$Num3d = $riga["reply_num"];
$NumPag = intval(($Num3d-1) / $PostXPage);
$CurrPag = $_REQUEST["pag"];
if ($CurrPag=="last")
  $CurrPag = $NumPag;
if (! is_numeric($CurrPag))
  $CurrPag = 0;
if ($CurrPag < 0) $CurrPag = 0;

mysql_query("replace temp(chiave,valore,TTL) values ('".$_REQUEST['THR_ID']."',$Num3d,".(time()+2592000).");");

mysql_query("update {$SNAME}_msghe set read_num=read_num+1 WHERE HASH='".mysql_escape_string($MSGID)."';");

?>
<a href="searcher.pm?MODO=2&REP_OF=<?php print urlencode($MSGID);?>">
<?php echo "".$lang['shmsg_findnewmsg']."</a><br />"; ?>

<?php
  PageSelect(1);

  if ($CurrPag<1)
    printmsg($riga,GetUserColor($riga));

$query="SELECT edit.TITLE AS title, edit.BODY AS body, membri.AUTORE as autore,"
." (origi.DATE+".GMT_TIME.") as 'date', (membri.DATE+".GMT_TIME.") AS regdate, membri.avatar AS avatar, membri.firma AS firma,"
." membri.is_auth AS 'is_auth', membri.msg_num AS 'msg_num',"
." membri.title as 'gruppo', membri.HASH AS 'memhash',origi.HASH AS 'hash', edit.REP_OF AS 'repof',"
." edit.EDIT_OF AS 'edit_of', edit.HASH AS 'real_hash', edit.AUTORE AS 'real_autore', (edit.DATE+".GMT_TIME.") AS 'real_date'"
." FROM `{$SNAME}_reply` AS origi, `{$SNAME}_reply` AS edit, `{$SNAME}_membri` AS membri"
." WHERE edit.EDIT_OF=origi.HASH"
." AND membri.HASH=origi.AUTORE"
." AND edit.REP_OF='".mysql_escape_string($MSGID)."'"
." AND edit.visibile='1'"
." ORDER BY origi.DATE"
." LIMIT ".($CurrPag*$PostXPage).",$PostXPage;";
$risultato=mysql_query($query) or Muori ($lang['inv_query'] . mysql_error());

  while ($riga = mysql_fetch_assoc($risultato)) printmsg($riga,GetUserColor($riga));

  PageSelect(2);
  FastReply();
?>
</td>
</tr>
<?php
include ("end.php");
?>
