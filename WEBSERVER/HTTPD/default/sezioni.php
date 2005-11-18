<?PHP
// v. 0.9
include ("testa.php");
$lang = $std->load_lang('lang_sezioni', $blanguage );

function PageSelect() {
?>
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
  $link = "sezioni.php?SEZID=".$_REQUEST["SEZID"]."&pag=";
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

    ?>
      <td align=right>
        <a href='writenewmsg.php?SEZID=<? echo $_REQUEST["SEZID"]; ?>'> <? echo "  <img src='img/buttons/".$blanguage."/t_new.gif' border=0></a>"; ?>
      </td>
    </tr>
  </tbody>
</table>
<?
}
?>

<tr>
 <td>
<?PHP
$SEZID=$_REQUEST['SEZID'];
$SNAME=$_ENV['sesname'];

echo "<a href=\"searcher.php?MODO=1&SEZ=".$SEZID."&ORDER=DESC\">".$lang['req_last']."</a><br><br>";

$query="SELECT THR_NUM from {$SNAME}_sez WHERE ID=$SEZID;";
$risultato=mysql_query($query);
$riga = mysql_fetch_assoc($risultato);

$Num3d = $riga["THR_NUM"];
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
	echo "<p>".$SEZ_DATA['SEZ_NAME']."</p>";
    ?>
  </div>
  <table cellspacing="1">
   <tr>
    <th align="center" width="1%">&nbsp;</th>
    <th align="center" width="1%">&nbsp;</th>
    <th align="left" width="50%" class='titlemedium'><?PHP echo $lang['topic_title'] ?></th>
    <th align="center" width="6%" class='titlemedium'><?PHP echo $lang['topic_replies'] ?></th>
    <th align="center" width="14%" class='titlemedium'><?PHP echo $lang['topic_starter'] ?></th>
    <th align="center" width="7%" class='titlemedium'><?PHP echo $lang['toppic_views'] ?></th>
    <th align="center" width="21%" class='titlemedium'><?PHP echo $lang['topic_laction'] ?></th>
   </tr>
<?PHP
$query="SELECT msghe.HASH as 'HASH',newmsg.title AS 'title', (last_reply_time+".GMT_TIME.") as last_reply_time,membri.AUTORE as nick,membri.HASH AS 'nickhash',"
  ." repau.AUTORE as dnick, repau.HASH as dnickhash, (msghe.DATE+".GMT_TIME.") AS 'write_date', reply_num, read_num,newmsg.SUBTITLE as 'subtitle' "
  ." FROM {$SNAME}_msghe AS msghe,{$SNAME}_newmsg AS newmsg,{$SNAME}_membri AS membri,{$SNAME}_membri AS repau "
  ." WHERE newmsg.EDIT_OF=msghe.HASH"
  ." AND newmsg.SEZ='".$SEZID."'"
  ." AND newmsg.visibile='1'"
  ." AND membri.HASH=msghe.AUTORE "
  ." AND repau.HASH=msghe.last_reply_author"
  ." ORDER BY msghe.last_reply_time DESC"
  ." LIMIT ".($CurrPag*$ThreadXPage).",$ThreadXPage;";
$risultato=mysql_query($query) or Muori ($lang['inv_query'] . mysql_error());

while ($riga = mysql_fetch_assoc($risultato)) {
  $iden=unpack("H32hex",$riga['HASH']);
  $reply_date=strftime("%d/%m/%y  - %H:%M:%S",$riga['last_reply_time']);
  $write_date=strftime("%d/%m/%y  - %H:%M:%S",$riga['write_date']);
  $ris2 = mysql_query("select valore from temp where chiave='".$iden['hex']."';");
  if ($tmp = mysql_fetch_assoc($ris2)) {
    $num = $tmp["valore"];
    if ($num<$riga["reply_num"])
      $PostStatImage = "f_norm";
    else
      $PostStatImage = "f_norm_no";
    if ($riga['nickhash'])
      $nickhash=unpack("H32alfa",$riga['nickhash']);
    else 
      $nickhash['alfa']=''; 
    if ($riga['dnickhash'])
      $dnickhash=unpack("H32alfa",$riga['dnickhash']);
    else 
      $dnickhash['alfa']=''; 
  }
  else
    $PostStatImage = "f_norm";
  $rep=$riga["reply_num"];
  $i=0;
  $Pages="";
  if($rep>$PostXPage){
     while($rep>0){
        if($i<=$Section){
           $Pages=$Pages."<td align='left' nowrap='nowrap'><span class='pagelink'><a href='showmsg.php?SEZID={$SEZID}&THR_ID=".$iden['hex']."&pag={$i}'>".++$i."</a></span></td>";
           $rep=$rep-$PostXPage;
        }else{
           $Pages=$Pages."<td align='left' nowrap='nowrap'><span class='pagelink'>..</span>&nbsp;<span class='pagelink'><a href='showmsg.php?SEZID={$SEZID}&THR_ID=".$iden['hex']."&pag=last#end_page'>&raquo;</a></span></td>";
           $rep=0;
        }
     }
  }
  echo "
<tr height='35'>
  <td align='center' class='row2'><img src='img/$PostStatImage.gif'></td>
  <td align='center' class='row2'>&nbsp;</td>
  <td align='left' class='row2'><table border='0' cellpadding='2px' cellspacing='0'><tbody><tr><td align='left' nowrap='nowrap'><a href='showmsg.php?SEZID={$SEZID}&THR_ID=".$iden['hex']."' title='".$lang['topic_start']." {$write_date}'>".secure_v($riga["title"])."</a></td>".$Pages."</tr></tbody></table>".secure_v($riga["subtitle"])."</td>
  <td align=center class='row4'>".$riga["reply_num"]."</td>
  <td align=center class='row4'><u><small><a href='showmember.php?MEM_ID=".$nickhash['alfa']."'>".secure_v($riga["nick"])."</a></small></u></td>
  <td align=center class='row4'>".$riga['read_num']."</td>
  <tD align=left class='row4'><small>{$reply_date}<br><a href=\"showmsg.php?SEZID={$SEZID}&THR_ID=".$iden['hex']."&pag=last#end_page\">".$lang['topic_last']."</a>: <b><a href='showmember.php?MEM_ID=".$dnickhash['alfa']."'>".secure_v($riga["dnick"])."</b></small></tD>
</tr>\n";
}
?>
</table>
</div>

<? PageSelect(); ?>

 </td>
</tr>
<?PHP
include ("end.php");
?>
