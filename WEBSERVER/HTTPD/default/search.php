<?PHP
// v 0.1

include ("testa.php");
include ("mostra_messaggio.php");

// carico la lingua per la ricerca
$lang = $std->load_lang('lang_search', $blanguage );
$lang_sez = $std->load_lang('lang_sezioni', $blanguage );

?>

<tr>
 <td align="center">
   La ricerca non è ancora stata implementata del tutto...<br><br>
<?

if (! $_REQUEST["find"])
  include "search_form.php";
else {
  $Keywords = $_REQUEST["keywords"];
  $Nick = mysql_real_escape_string($_REQUEST["namesearch"]);
  if ($Nick) {
    $NickTable = "left outer join {$SNAME}_membri as membri on msg.autore=membri.hash";
    if ($_REQUEST["exactname"] == 1)
      $Nick = " and membri.autore like '$Nick' ";
    else
      $Nick = " and membri.autore like '%$Nick%' ";
  }
  else
    $NickTable = "";
//  $Forum = $_REQUEST["forminput"]; da fare + avanti...
  $Forum = $_REQUEST["forums"];
  $Giorni = $_REQUEST["prune"];
  if ($Giorni>0)
    $FasciaTemporale = " and `msg.DATE` ".$_REQUEST["prune_type"].(time()-(86400*$Giorni));
  if ($_REQUEST["search_in"] == 1)
    $where = "((msg.BODY like '%".mysql_real_escape_string($Keywords)."%') or (msg.TITLE like '%".mysql_real_escape_string($Keywords)."%'))";
  else
    $where = "(msg.TITLE like '%".mysql_real_escape_string($Keywords)."%')";
  $where = $where.$FasciaTemporale.$Nick;
  $SQLQuery1 = "SELECT count(msg.hash) as num FROM {$SNAME}_newmsg as msg $NickTable where visibile = '1' and $where";
  $SQLQuery2 = "SELECT count(msg.hash) as num FROM {$SNAME}_reply as msg $NickTable where visibile = '1' and $where";
  $SQLQuery = "select sum(num) as num from (($SQLQuery1) UNION ($SQLQuery2)) as result_num";

  $risultato=mysql_query($SQLQuery) or Muori ("Query non valida: " . mysql_error());
  if ($riga = mysql_fetch_assoc($risultato))
    $num = $riga["num"];
  else
    $num = 0;

  echo "Sono stati trovati <b>$num</b> post che soddisfano la ricerca.<br><br>";

  $SQLQuery1 = "SELECT msg.hash, msg.edit_of as rep_of, msg.sez, msg.edit_of, msg.type, msg.date, msg.title, msg.subtitle, msg.body FROM {$SNAME}_newmsg as msg $NickTable where visibile = '1' and $where";
  $SQLQuery2 = "SELECT msg.hash, msg.rep_of, 0 as sez, msg.edit_of, msg.type, msg.date, msg.title, '' as subtitle, msg.body FROM {$SNAME}_reply as msg $NickTable where visibile = '1' and $where";
  $SQLQuery = "select * from (($SQLQuery1) UNION ($SQLQuery2)) as result_msg order by `".$_REQUEST["sort_key"]."` ".$_REQUEST["sort_order"];
  $risultato=mysql_query($SQLQuery) or Muori ("Query non valida: " . mysql_error());

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
    <th align="left" width="57%" class='titlemedium'><?PHP echo $lang_sez['topic_title'] ?></th>
    <th align="center" width="6%" class='titlemedium'><?PHP echo $lang_sez['topic_replies'] ?></th>
    <th align="center" width="10%" class='titlemedium'><?PHP echo $lang_sez['topic_starter'] ?></th>
    <th align="center" width="7%" class='titlemedium'><?PHP echo $lang_sez['toppic_views'] ?></th>
    <th align="center" width="18%" class='titlemedium'><?PHP echo $lang_sez['topic_laction'] ?></th>
   </tr>
<?
  while ($riga1 = mysql_fetch_assoc($risultato)) {
    $SQLQuery = "select * from {$SNAME}_newmsg as t1,{$SNAME}_msghe as t2 where t1.hash='".$riga1["rep_of"]."' and t1.hash=t2.hash";
    $ris=mysql_query($SQLQuery) or Muori ("Query non valida: " . mysql_error());
    $riga = mysql_fetch_assoc($ris);

    $iden=unpack("H32hex",$riga['HASH']);
    $reply_date=strftime("%d/%m/%y  - %H:%M:%S",$riga['last_reply_time']);
    $write_date=strftime("%d/%m/%y  - %H:%M:%S",$riga['WRITE_DATE']);
    $ris2 = mysql_query("select valore from temp where chiave='".$iden['hex']."';");
    if ($tmp = mysql_fetch_assoc($ris2)) {
      $num = $tmp["valore"];
      if ($num<$riga["reply_num"])
        $PostStatImage = "f_norm";
      else
        $PostStatImage = "f_norm_no";
      if ($riga['nickhash'])
        $nickhash=unpack("H32alfa",$riga['NICKHASH']);
      else 
        $nickhash['alfa']=''; 
      if ($riga['dnickhash'])
        $dnickhash=unpack("H32alfa",$riga['DNICKHASH']);
      else 
        $dnickhash['alfa']=''; 
    }
    else
      $PostStatImage = "f_norm";
    if(strlen($riga["TITLE"])>100) $title=substr($riga["TITLE"], 0, 100)."...";
    else                           $title=$riga["TITLE"];

    echo "
<tr height='35'>
  <td align='center' class='row2'><img src='img/$PostStatImage.gif'></td>
  <td align='center' class='row2'>&nbsp;</td>
  <td align='left' class='row2'><table border='0' cellpadding='2px' cellspacing='0'><tbody><tr><td align='left' nowrap='nowrap'><a href='showmsg.php?SEZID=".$riga["SEZ"]."&THR_ID=".$iden['hex']."' title='".$lang_sez['topic_start']." {$write_date}'>".secure_v($title)."</a></td>".$Pages."</tr></tbody></table>&nbsp;".secure_v($riga["subtitle"])."</td>
  <td align=center class='row4'>".$riga["reply_num"]."</td>
  <td align=center class='row4'><u><small><a href='showmember.php?MEM_ID=".$nickhash['alfa']."'>".secure_v($riga["NICK"])."</a></small></u></td>
  <td align=center class='row4'>".$riga['read_num']."</td>
  <tD align=left class='row4'><small>{$reply_date}<br><a href=\"showmsg.php?SEZID={$SEZID}&THR_ID=".$iden['hex']."&pag=last#end_page\">".$lang_sez['topic_last']."</a>: <b><a href='showmember.php?MEM_ID=".$dnickhash['alfa']."'>".secure_v($riga["DNICK"])."</b></small></tD>
</tr>\n";
  }
}

?>
</table>
</div>

 </td>
</tr>

<?PHP
include ("end.php");
?>
