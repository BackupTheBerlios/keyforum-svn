<?PHP

// devo caricare la lingua per ricavare il titolo della pagina...
require_once("lib/lib.php");
if (is_array($lang)) {$lang += $std->load_lang('lang_shownewmsg', $blanguage );} else {$lang = $std->load_lang('lang_shownewmsg', $blanguage );}
$title=$lang['page_title'] ;



//POSTBACK!
if($_REQUEST['allread'])
 {
 $userdata->LASTREAD=$_REQUEST['allread'];
 $std->UpdateUserData($_ENV["sesname"],$userdata);
 $std->Redirect($lang['shnwmsg_allread'],"index.php",$lang['shnwmsg_markedread'],$lang['shnwmsg_at'].date("d/m/Y H:i:s",$_REQUEST['allread'])); 
 }

//END POSTBACK!
$tst=$_REQUEST['tst'];
include ("testa.php");

function PageSelect() {
global $tst,$sess_auth;
?>
<tr><td>
<table border="0" cellpadding="5px" cellspacing="0" width="100%">
  <tbody>
  <tr>
    <td align="left" nowrap="nowrap" width="20%">
<?
  global $blanguage;
  global $NumPag;
  global $CurrPag;
  global $Section;
  global $lang;
  $mytime=time();
  $link = "shownewmsg.php?tst=$tst&pag=";
  if ($NumPag > 0) {
    echo "<span class='pagelink'>".($NumPag+1)."&nbsp;".$lang['pages']."</span>&nbsp;";
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

    
    
     if ($sess_auth) 
	 {
	 	echo "<td align=right><a href='?allread=$mytime'>{$lang['mark_all_read']}</a></td>";
	 }
      ?>
      
    </tr>
  </tbody>
</table>
<?
}
?>

<tr>
 <td>


<?PHP

$timelimit=$userdata->LASTREAD - GMT_TIME;
if($timelimit < 1) {$timelimit=time()-2592000;}

$last24=$timelimit-86400;
$last7g=$timelimit-604800;
$last30g=$timelimit-2592000;



if($tst){
$timelimit=$_REQUEST['tst'];
$sel[$tst]="selected";
} else {$sel['from']="selected";}

$query="SELECT count(msghe.HASH) as 'HASH'
FROM {$SNAME}_msghe AS msghe,
{$SNAME}_newmsg AS newmsg,{$SNAME}_membri AS membri,{$SNAME}_membri AS repau 
WHERE newmsg.EDIT_OF=msghe.HASH AND newmsg.visibile='1' AND membri.HASH=msghe.AUTORE 
AND repau.HASH=msghe.last_reply_author  AND last_reply_time > $timelimit";

$Num3d = $db->get_var($query);
$NumPag = intval(($Num3d-1) / $ThreadXPage);
$CurrPag = $_REQUEST["pag"];
if (! is_numeric($CurrPag))
  $CurrPag = 0;
if ($CurrPag < 0) $CurrPag = 0;

PageSelect();

?>
<div class="borderwrap">
  <div class="maintitle">
    <p class="expand"></p>
    <?PHP
    
    $dform="<form method=\"POST\" name=\"dform\">
       <select class=content name=\"quicklink\" size=\"1\" 
       onchange=\"dform.target='_BLANK';location.href=document.dform.quicklink.options[document.dform.quicklink.selectedIndex].value\" 
       style=\"font-family: Verdana; font-size: 8pt\" >";
       
    $dform .="<option {$sel['from']} value=\"shownewmsg.php\">".date("d/m/Y H:i:s",$timelimit)."</option>";
    $dform .="<option {$sel[$last24]} value=\"shownewmsg.php?tst=$last24\">ultime 24 ore</option>";
    $dform .="<option {$sel[$last7g]} value=\"shownewmsg.php?tst=$last7g\">ultimi 7 giorni</option>";
    $dform .="<option {$sel[$last30g]} value=\"shownewmsg.php?tst=$last30g\">ultimi 30 giorni</option>";

    
    $dform .="</select></form>";
    
    
	echo "<p>{$lang['msg_last'] }$dform</p>";
    ?>
  </div>
  <table cellspacing="1">
   <tr>
    <th align="center" width="1%">&nbsp;</th>
    <th align="center" width="1%">&nbsp;</th>
    <th align="left" width="47%" class='titlemedium'><?PHP echo $lang['topic_title'] ?></th>
    <th align="center" width="17%" class='titlemedium'><?PHP echo $lang['forum'] ?></th>    
    <th align="center" width="6%" class='titlemedium'><?PHP echo $lang['topic_replies'] ?></th>
    <th align="center" width="10%" class='titlemedium'><?PHP echo $lang['topic_starter'] ?></th>
    <th align="center" width="18%" class='titlemedium'><?PHP echo $lang['topic_laction'] ?></th>
   </tr>
<?PHP

// ricavo la lista delle sezioni e la metto in un array
$query="SELECT ID,SEZ_NAME from {$SNAME}_sez";
$sez = $db->get_results($query);
if ($sez) foreach ($sez as $riga) {
 $sezname[$riga->ID]=$riga->SEZ_NAME;
}

$query="SELECT msghe.HASH as 'HASH',newmsg.title AS 'title', (last_reply_time+".GMT_TIME.") as last_reply_time,membri.AUTORE as nick,membri.HASH AS 'nickhash',"
  ." repau.AUTORE as dnick, repau.HASH as dnickhash, (msghe.DATE+".GMT_TIME.") AS 'write_date', reply_num, newmsg.SUBTITLE as 'subtitle',newmsg.SEZ AS 'sez',"
  ." newmsg.body like '%[TOPIC-PINNED]%' as pinned, newmsg.body like '%[TOPIC-CLOSED]%' as closed, newmsg.body like '%[TOPIC-FIXED]%' as fixed "
  ." FROM {$SNAME}_msghe AS msghe,{$SNAME}_newmsg AS newmsg,{$SNAME}_membri AS membri,{$SNAME}_membri AS repau "
  ." WHERE newmsg.EDIT_OF=msghe.HASH"
  ." AND newmsg.visibile='1'"
  ." AND membri.HASH=msghe.AUTORE "
  ." AND repau.HASH=msghe.last_reply_author"
  ." AND last_reply_time > $timelimit"
  ." ORDER BY msghe.last_reply_time DESC"
  ." LIMIT ".($CurrPag*$ThreadXPage).",$ThreadXPage;";
  

  
$risultato=$db->get_results($query);
if($risultato) foreach($risultato as $riga ) {
  $iden=unpack("H32hex",$riga->HASH);
  

  $reply_date=$std->PostDate($riga->last_reply_time);
  $write_date=$std->PostDate($riga->write_date);
    
  
  $num = $db->get_var("select valore from temp where chiave='".$iden['hex']."';");
  //Default data
  if ($num) 
  {
	$PostStatImage = ($num < $riga->reply_num ? "f_norm" : "f_norm_no");
    $nickhash =($riga->nickhash ? unpack("H32alfa",$riga->nickhash) : '');
    $dnickhash=($riga->dnickhash ? unpack("H32alfa",$riga->dnickhash) : '');
  }
  else
  $PostStatImage = "f_norm";
  
  if ($riga->pinned) {
      $post_icon="<img src='img/pinned.gif' alt='Pinned!'>";
      $pinned_str="<b>Pinned: ";
      $pinned_close="</b>";
  } else {
      $post_icon="";
      $pinned_str="";
      $pinned_close="";
  }
  
  // closed
  if ($riga->closed)  {$PostStatImage = "f_closed";}
  
  // fixed
  if ($riga->fixed)  {$PostStatImage = "f_fixed";}

  $rep=$riga->reply_num;
  $i=0;
  $Pages="";
  if($rep>$PostXPage){
     while($rep>0){
        if($i<=$Section){
           $Pages=$Pages."<td align='left' nowrap='nowrap'><span class='pagelink'><a href='showmsg.php?SEZID=".$riga->sez."&amp;THR_ID=".$iden['hex']."&amp;pag={$i}'>".++$i."</a></span></td>";
           $rep=$rep-$PostXPage;
        }else{
           $Pages=$Pages."<td align='left' nowrap='nowrap'><span class='pagelink'>..</span>&nbsp;<span class='pagelink'><a href='showmsg.php?SEZID=".$riga->sez."&amp;THR_ID=".$iden['hex']."&amp;pag=last#end_page'>&raquo;</a></span></td>";
           $rep=0;
        }
     }
  }
  
  if(!trim($riga->title)){$riga->title="(untitled)";}
  
  if(strlen($riga->title)>100){
     $title=substr($riga->title, 0, 100)."...";
  }else{
     $title=$riga->title;
  }
  echo "
<tr>
  <td align='center' class='row2'><img src='img/$PostStatImage.gif' alt=''></td>
  <td align='center' class='row2'>$post_icon</td>
  <td align='left' class='row2'><table border='0' cellpadding='2px' cellspacing='0'><tbody><tr><td align='left' nowrap='nowrap'>$pinned_str<a href='showmsg.php?SEZID=".$riga->sez."&amp;THR_ID=".$iden['hex']."' title='".$lang['topic_start']." {$write_date}'>".secure_v($title)."</a>$pinned_close</td>".$Pages."</tr></tbody></table>&nbsp;".secure_v($riga->subtitle)."</td>
  <td align=center class='row4'><a href='sezioni.php?SEZID=".$riga->sez."'>".$sezname[$riga->sez]."</td>
  <td align=center class='row4'>".$riga->reply_num."</td>
  <td align=center class='row4'><small><u><a href='showmember.php?MEM_ID=".$nickhash['alfa']."'>".secure_v($riga->nick)."</a></u></small></td>
  <tD align=left class='row4'><small>{$reply_date}<br><a href=\"showmsg.php?SEZID=".$riga->sez."&amp;THR_ID=".$iden['hex']."&amp;pag=last#end_page\">".$lang['topic_last']."</a>: <b><a href='showmember.php?MEM_ID=".$dnickhash['alfa']."'>".secure_v($riga->dnick)."</a></b></small></tD>
</tr>\n";
}


echo "</table></div>";
 echo "</td></tr>";

PageSelect(); 


include ("end.php");
?>
