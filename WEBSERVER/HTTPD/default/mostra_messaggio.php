<?
function printmsg($MSG,$postlink) {
  global $GLOBALS;
  global $blanguage;
  global $lang;
  global $std;
  global $userdata;
  global $db;
  global $member_titles;
  global $closed;
  global $SNAME;

  $query="SELECT VALORE as 'is_mod'
  	FROM {$SNAME}_permessi
  	WHERE AUTORE='".mysql_real_escape_string($MSG->memhash)."'
  	AND {$SNAME}_permessi.chiave_a = '{$_GET['SEZID']}'
  	AND {$SNAME}_permessi.chiave_b ='IS_MOD'
  	ORDER BY DATE DESC;";
  $riga=$db->get_row($query);
  $MSG->is_mod=$riga->is_mod;
  
  $usercolor = $std->GetUserColor($MSG->memhash);
  $mio_nick = $_SESSION[$SNAME]['sess_nick'];
  if ($MSG->date)
    $write_date=strftime("%d/%m/%y  - %H:%M:%S",$MSG->date);
  $hash=unpack("H32hex",$MSG->hash);
  if (eregi("http:\/\/", secure_v($MSG->avatar))){
    if(!$userdata->HIDEAVATAR)
      $avatar="<img src='".$MSG->avatar."'alt=''><br />";
  }
  if ($MSG->regdate)
    $register_date=strftime("%d/%m/%y",$MSG->regdate);
  if ($MSG->gruppo)
    $gruppo=$MSG->gruppo; else $gruppo="membri";
  if ($MSG->is_mod)
    $Is_mod="<span style='color:#FF0000'>Moderatore</span><br />"; else $Is_mod = '';
  if ($MSG->memhash) {
    $tmp=unpack("H32hash",$MSG->memhash);
    $autore="<a href='showmember.php?MEM_ID=".$tmp['hash']."'>".secure_v($MSG->autore)."</a>";
    if ($MSG->is_auth)
      $auth="<b>YES</b>";
    else
      $auth="<a href='admin.pl?action=AuthMem&amp;HASH=".$tmp['hash']."'>NO</a>";
  }
  else {
    $autore=secure_v($MSG->autore);
    if ($MSG->is_auth)
      $auth="<b>YES</b>";
    else
      $auth="NO";
  }

if(($userdata->LEVEL)  OR ($MSG->autore==$mio_nick))
{
  if ($MSG->repof) {
    $tmp=unpack("H32repof/H32mshash", $MSG->repof.$MSG->hash);
    $EDITER="<a href='edreply.php?REP_OF=".$tmp['repof']."&amp;EDIT_OF=".$tmp['mshash']."&amp;SEZID=".$_REQUEST["SEZID"]."'><img src=\"img/buttons/".$blanguage."/p_edit.gif\" border=\"0\" alt=\"Edit\" ></a>";
  }
  elseif ($MSG->SEZ) {
    $tmp=unpack("H32mshash", $MSG->edit_of);
    $EDITER="<a href='ednewmsg.php?EDIT_OF=".$tmp['mshash']."&amp;SEZID=$MSG->SEZ'><img src=\"img/buttons/".$blanguage."/p_edit.gif\" border=\"0\" alt=\"Edit\" ></a>";
  }
}  
 
  if($MSG->edit_of!=$MSG->hash){
     $queryaut="SELECT AUTORE FROM `".$_SERVER["sesname"]."_membri` WHERE HASH='".mysql_real_escape_string($MSG->real_autore)."' LIMIT 1;";
     $realautore=$db->get_var($queryaut);
     $MSG->body = $MSG->body."\n\n\n\n [SIZE=1][COLOR=blue]".$lang['shmsg_modby']." ".secure_v($realautore)." ".$lang['shmsg_on']." ".strftime("%d/%m/%y  - %H:%M:%S",$MSG->real_date)."[/COLOR][/SIZE]";
  }

  if(($MSG->real_hash)AND($MSG->edit_of!=$MSG->real_hash)){
     $queryaut="SELECT AUTORE FROM `".$_SERVER["sesname"]."_membri` WHERE HASH='".mysql_real_escape_string($MSG->real_autore)."' LIMIT 1;";
     $realautore=$db->get_var($queryaut);
     $MSG->body = $MSG->body."\n\n\n\n [SIZE=1][COLOR=blue]".$lang['shmsg_modby']." ".secure_v($realautore)." ".$lang['shmsg_on']." ".strftime("%d/%m/%y  - %H:%M:%S",$MSG->real_date)."[/COLOR][/SIZE]";
  }
  list($asd,$postid) = unpack('H*',$MSG->hash);
  $MSG->body = secure_v($MSG->body);
   
  // visualizzo le firme ?
  if($userdata->HIDESIG) { $MSG->firma=""; } else { $MSG->firma = secure_v($MSG->firma);}
  // visualizzo lgli avatar
  if($userdata->HIDEAVATAR) { $MSG->avatar="" ;}
  
// titolo utente e pips
$membertitle=$std->MemberTitle($member_titles,$MSG->msg_num);

$msg_link="";
if($postlink)
  $msg_link="<span class='postdetails'>{$lang['shmsg_message']} <a title='{$lang['shmsg_msglnk']}' href='#' onclick='link_to_post(\"{$postid}\"); return false;'>#{$postlink}</a></span>";
  
  echo<<<EOF
<a name='{$postlink}'></a>
<table width='100%' border='0' cellspacing='1' cellpadding='3'>
<tr>
 <td valign='middle' class='row4' width='1%'><span class='normalname'><u>{$autore}</u></span>
   <a id='post_{$postid}'></a>
 </td>
 <td class='row4' valign='top' width='99%'>
  <div align='left' class='row4' style='float:left;padding-top:4px;padding-bottom:4px'>
   <span class='postdetails'><b>{$lang['shmsg_sendon']}</b>{$write_date}</span>
  </div>
  <div align="right">
   {$msg_link}
  </div>
 </td>
</tr>
<tr>
 <td valign='top' class='post2'>
  <span class='postdetails'><br />
  {$avatar}
  {$membertitle['title']}<br />
  {$membertitle['pips']}<br />  
  {$Is_mod}
  {$lang['shmsg_adminauth']}{$auth}<br />
  {$lang['shmsg_group']}{$gruppo}<br />
  {$lang['shmsg_messages']}{$MSG->msg_num}<br />
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
 
 $title=$MSG->title;
 if($MSG->subtitle){
    $title=$title.", ".$MSG->subtitle;
 }
 
 if($title) {
 echo "<table border='1' bordercolor='#DEDEFF' cellspacing='0' cellpadding='0' width='100%' class='post_title'>
 		<tr>
			<td bordercolor='#F0F0FF' class='postdetails'>
				<b>{$lang['shmsg_title']}</b>
				".secure_v($title)."
			</td>
		</tr>
	   </table><br />";
 }
 
 $tmp=unpack("H32mshash", $MSG->edit_of);
 echo "<div class='postcolor'> ".convert($MSG->body)."</div>
  <br /><br />--------------------<br />
  <div class='signature'>".convert($MSG->firma)."</div>
 </td>
</tr>
<tr>
 <td class='darkrow3' align='left'><b>&nbsp;</b></td>
 <td class='darkrow3' nowrap='nowrap' align='left'>
   <!-- PM / EMAIL / WWW / MSGR / UP -->
   <div align='left' class='darkrow3' style='float:left;width:auto'>
     <a href='javascript:scroll(0,0);'><img src=\"img/buttons/".$blanguage."/p_up.gif\" border=\"0\" alt=''></a>
   </div>

   <!-- REPORT / UP -->
   <div align='right'>
   $EDITER";
   
   if (!$closed) {echo "<a href=\"reply.php?SEZID=".$_REQUEST["SEZID"]."&amp;THR_ID=".$_REQUEST["THR_ID"]."&amp;quote=".$tmp['mshash']."\"><img src='img/buttons/".$blanguage."/p_quote.gif' alt='Quote' border='0'></a>";}
   echo"
   </div>
 </td>
</tr>
</table>
<div class='darkrow1' style='height:5px'><!-- --></div>
";

}
?>
