<?
function printmsg($MSG) {
  global $GLOBALS;
  global $blanguage;
  global $lang;
  global $std;
  global $userdata;
  $usercolor = $std->GetUserColor($MSG['memhash']);
  $mio_nick = $GLOBALS['sess_nick'];
  if ($MSG['date'])
    $write_date=strftime("%d/%m/%y  - %H:%M:%S",$MSG['date']);
  $hash=unpack("H32hex",$MSG['hash']);
  if (eregi("http:\/\/", secure_v($MSG['avatar'])))
    $avatar="<img src='".$MSG['avatar']."'alt=''><br />";
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
      $auth="<a href='admin.pl?action=AuthMem&amp;HASH=".$tmp['hash']."'>NO</a>";
  }
  else {
    $autore=secure_v($MSG['autore']);
    if ($MSG['is_auth'])
      $auth="<b>YES</b>";
    else
      $auth="NO";
  }

if(($userdata->LEVEL)  OR ($MSG['autore']==$mio_nick))
{
  if ($MSG['repof']) {
    $tmp=unpack("H32repof/H32mshash", $MSG['repof'].$MSG['hash']);
    $EDITER="<a href='edreply.php?REP_OF=".$tmp['repof']."&amp;EDIT_OF=".$tmp['mshash']."&amp;SEZID=".$_REQUEST["SEZID"]."'><img src=\"img/buttons/".$blanguage."/p_edit.gif\" border=\"0\" alt=\"Edit\" ></a>";
  }
  elseif ($MSG[SEZ]) {
    $tmp=unpack("H32mshash", $MSG['edit_of']);
    $EDITER="<a href='ednewmsg.php?EDIT_OF=".$tmp['mshash']."&amp;SEZID=$MSG[SEZ]'><img src=\"img/buttons/".$blanguage."/p_edit.gif\" border=\"0\" alt=\"Edit\" ></a>";
  }
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
   
  // visualizzo le firme ?
  if($userdata->HIDESIG) { $MSG['firma']=""; } else { $MSG['firma'] = secure_v($MSG['firma']);}
  
  echo<<<EOF
<table width='100%' border='0' cellspacing='1' cellpadding='3'>
<tr>
 <td valign='middle' class='row4' width='1%'><span class='normalname'><u>{$autore}</u></span>
   <a name={$postid}></a>
 </td>
 <td class='row4' valign='top' width='99%'>
  <div align='left' class='row4' style='float:left;padding-top:4px;padding-bottom:4px'>
   <span class='postdetails'><b>{$lang['shmsg_sendon']}</b>{$write_date}</span></b>
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
EOF;
 
 $title=$MSG['title'];
 if($MSG['subtitle']){
    $title=$title.", ".$MSG['subtitle'];
 }
 
 if($title) {
 echo "<table border='1' bordercolor='#DEDEFF' cellspacing='0' cellpadding='0' width='100%'><tr><td bordercolor='#F0F0FF' class='postdetails'><b>{$lang['shmsg_title']}</b> ";
 echo secure_v($title)."</td></tr></table><br />";
 }
 
 $tmp=unpack("H32mshash", $MSG['edit_of']);
 echo "<div class='postcolor'> ".convert($MSG['body'])."</div>
  <br /><br />--------------------<br />
  <div class='signature'>".convert($MSG['firma'])."</div>
 </td>
</tr>
<tr>
 <td class='darkrow3' align='left'><b></b></td>
 <td class='darkrow3' nowrap='nowrap' align='left'>
   <!-- PM / EMAIL / WWW / MSGR / UP -->
   <div align='left' class='darkrow3' style='float:left;width:auto'>
     <a href='javascript:scroll(0,0);'><img src=\"img/buttons/".$blanguage."/p_up.gif\" border=\"0\" alt=''></a>
   </div>

   <!-- REPORT / UP -->
   <div align='right'>
   $EDITER
   <a href=\"reply.php?SEZID=".$_REQUEST["SEZID"]."&amp;THR_ID=".$_REQUEST["THR_ID"]."&amp;quote=".$tmp['mshash']."\"><img src='img/buttons/".$blanguage."/p_quote.gif' alt='Quote' border='0'></a>
   </div>
 </td>
</tr>
</table>
<div class='darkrow1' style='height:5px'><!-- --></div>
";

}
?>