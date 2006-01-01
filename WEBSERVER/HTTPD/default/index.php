<?PHP
// v 0.14

include ("testa.php");


// carico la lingua per la index
$lang = $std->load_lang('lang_index', $blanguage );

$whereiam="index";

echo "<tr><td>";

$query = "SELECT * FROM ".$_ENV["sesname"]."_sez WHERE figlio=0 ORDER BY ID;";
$mainsez = $db->get_results($query);
foreach($mainsez as $mainsezval) {
echo "
<div class='borderwrap'>
  <div class='maintitle'>
    <p class='expand'></p>
    <p><a href='sezioni.php?SEZID={$mainsezval->ID}'>{$mainsezval->SEZ_NAME}</a></p>
  </div>";

  echo "
  <table cellspacing=\"1\">
    <tr>
      <th align=\"left\" width=\"50%\" colspan=\"2\" class='titlemedium'>".$lang['col_forum']."</th>
      <th align=\"center\" width=\"1%\" class='titlemedium'>".$lang['col_topic']."</th>
      <th align=\"center\" width=\"1%\" class='titlemedium'>".$lang['col_replies']."</th>
      <th align=\"left\" width=\"29%\" class='titlemedium'>".$lang['col_lastpost']."</th>
      <th align=\"center\" width=\"15%\" class='titlemedium'>".$lang['col_moderators']."</th>
    </tr>
    ";

  $mainzedid=$mainsezval->ID;
  $query = "SELECT * FROM ".$_ENV["sesname"]."_sez WHERE figlio=$mainzedid ORDER BY ID;";
  $sez = $db->get_results($query);
 foreach($sez as $sezval) {
  
      $MSG=GetLastMsg($sezval->ID);
      if ($MSG['time_action']) 
        $write_date=strftime("%d/%m/%y  - %H:%M:%S",$MSG['time_action']);
      else 
        $write_date='';
      if ($MSG['hash']) 
        $hash=unpack("H32alfa",$MSG['hash']);
      else 
        $hash['alfa']='';
      if ($MSG['nickhash'])
        $nickhash=unpack("H32alfa",$MSG['nickhash']);
      else 
        $nickhash['alfa']='';
      if(strlen($MSG['TITLE'])>50){
         $msg=substr($MSG['TITLE'], 0, 50)."...";
      }else{
         $msg=$MSG['TITLE'];
      }
      $notfirst=0;
      $subsections="";
      $querysubs = "SELECT ID, SEZ_NAME FROM ".$_ENV["sesname"]."_sez WHERE FIGLIO=$sezval->ID ORDER BY ID;";
      $subsez = mysql_query($querysubs) or die($lang['inv_query'] . mysql_error());
      while ($subsezval = mysql_fetch_assoc($subsez)) {
        if($notfirst)
          $subsections=$subsections.", <b><a href='sezioni.php?SEZID=".$subsezval['ID']."'>".secure_v($subsezval['SEZ_NAME'])."</a></b>";
        else
          $subsections="<br><i>".$lang['subforums']."</i><b><a href='sezioni.php?SEZID=".$subsezval['ID']."'>".secure_v($subsezval['SEZ_NAME'])."</a></b>";
        $notfirst=1;
      }
      echo '
      <tr>
        <td class="row4" width="5%" align="center"><img src="img/bf_new.gif" alt=""></td>
        <td class="row4"><b><a href="sezioni.php?SEZID='.$sezval->ID.'">'.secure_v($sezval->SEZ_NAME).'</a></b><br /><span class="desc">'.secure_v($sezval->SEZ_DESC).$subsections.'<br /><br /></span></td>
        <td class="row2" align="center">'.$sezval->THR_NUM.'</td>
        <td class="row2" align="center">'.$sezval->REPLY_NUM.'</td>
        <td class="row2" nowrap="nowrap">'.$lang['last_in'].'<a href="showmsg.php?SEZID='.$MSG['SEZID'].'&amp;THR_ID='.$hash['alfa'].'&amp;pag=last#end_page">'.secure_v($msg).'</a><br>'.$lang['last_data'].$write_date.'<br>'.$lang['last_from'].'<a href="showmember.php?MEM_ID='.$nickhash['alfa'].'">'.secure_v($MSG['nick']).'</a></td>
        <td class="row2" align="center">';
        $matr=explode("%",$sezval->MOD);
        for($counter=0; $counter<(strlen($sezval->MOD)/33); $counter++){
          if (!$nick[$matr[$counter]]) {
            $modhash=mysql_escape_string(pack("H*",$matr[$counter]));
            $modnick = $db->get_var("SELECT AUTORE FROM {$SNAME}_membri WHERE HASH='$modhash';");
            $nick[$matr[$counter]] = $modnick;
          }
          if($counter>0){
             echo ", ";
          }
          echo '<a href="showmember.php?MEM_ID='.$matr[$counter].'">'.secure_v($nick[$matr[$counter]])."</a>";
        }
        echo '</td></tr>';
      $totmsg = $totmsg + $sezval->THR_NUM + $sezval->REPLY_NUM;
    }
echo "<tr> 
          <td class='darkrow2' colspan=6>&nbsp;</td>
        </tr></table></div><br>";
  

  
  }
  


?>

 </td>
</tr>

<?PHP

include ("end.php");
?>
