<?PHP
// v. 0.10
include ("testa.php");
$lang += $std->load_lang('lang_sezioni', $blanguage );


?>
<?
function PageSelect() {
?>
<tr><td>
<table border="0" cellpadding="5" cellspacing="0" width="100%">
  <tbody>
  <tr>
    <td align="left" nowrap="nowrap" width="20%">
<?
  global $blanguage;
  global $NumPag;
  global $CurrPag;
  global $Section;
  global $lang;
  global $userdata;
  $link = '?';
  foreach($_GET as $nome=>$valore)
  {
  	if($nome != 'pag') 	$link .= "$nome=$valore&amp;";
  }
  $link .= "pag=";
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
        <a href='writenewmsg.php?SEZID=<? echo $_REQUEST["SEZID"]; ?>'> <? echo "  <img src='img/buttons/".$blanguage."/t_new.gif' border='0' alt=''></a>"; ?>
      </td>
    </tr>
  </tbody>
</table>
<?
}
?>

<tr>
 <td>

<?

// sottoforum
if((!$db->get_var("SELECT HIDE from {$SNAME}_sez WHERE ID=".$_REQUEST["SEZID"].";")) OR ($userdata->LEVEL >=11)) //se è visibile
{

  $mainzedid=$_REQUEST["SEZID"];
if($userdata->LEVEL < 11) {$showhiddensez="AND HIDE='0'";}
  $query = "SELECT * FROM ".$_SERVER["sesname"]."_sez WHERE figlio=$mainzedid {$showhiddensez} ORDER BY ORDINE;";
  $sez = $db->get_results($query);
  $num_sottoforum=$db->num_rows;
  
  if($userdata->LEVEL >=11){$sezeditor="<a href='adminsez.php?SEZID={$SEZ_DATA->ID}'><img src='img/s_edit.gif' border='0'  alt='Edit section' /></a>";}
  
  // esistono sottoforum ?
  if($num_sottoforum)
  {
   
   if($SEZ_DATA->REDIRECT){
   	$link="target='_blank' href='".$SEZ_DATA->REDIRECT;
   }else{
   	$link="href='sezioni.php?SEZID=".$SEZ_DATA->ID;
   }
   
   
   echo "
   <div class='borderwrap'>
     <div class='maintitle'>
       <p class='expand'>$sezeditor</p>
       <p><a ".$link."'>{$SEZ_DATA->SEZ_NAME}</a></p>
     </div>";

     echo "
     <table cellspacing=\"1\">
       <tr>
         <th align=\"left\" width=\"40%\" colspan=\"2\" class='titlemedium'>".$lang['col_forum']."</th>
         <th align=\"center\" width=\"1%\" class='titlemedium'>".$lang['col_topic']."</th>
         <th align=\"center\" width=\"1%\" class='titlemedium'>".$lang['col_replies']."</th>
         <th align=\"left\" width=\"39%\" class='titlemedium'>".$lang['col_lastpost']."</th>
       </tr>
    ";
   }
  if($sez) foreach($sez as $sezval) 
  {
  
      $MSG=GetLastMsg($sezval->ID);
	  //Default data
      $write_date= ($MSG->time_action ? strftime("%d/%m/%y  - %H:%M:%S",$MSG->time_action) : '');
      $hash= ($MSG->hash ? unpack("H32alfa",$MSG->hash) : '');
      $nickhash=($MSG->nickhash ? unpack("H32alfa",$MSG->nickhash) : '');
	  $msg=(strlen($MSG->TITLE)>50 ? substr($MSG->TITLE, 0, 50)."..." : $MSG->TITLE);
	  
	  
      $notfirst=0;
      $subsections="";
      $querysubs = "SELECT ID, SEZ_NAME, REDIRECT FROM ".$_SERVER["sesname"]."_sez WHERE FIGLIO=".$sezval->ID." AND HIDE='0' ORDER BY ORDINE;";
      $subsez = $db->get_results($querysubs);
      if($subsez)foreach($subsez as $subsezval) {
      	if($subsezval->REDIRECT){
		$link="target='_blank' href='".$subsezval->REDIRECT;
	}else{
		$link="href='sezioni.php?SEZID=".$subsezval->ID;
   	}
        if($notfirst)
          $subsections=$subsections.", <b><a ".$link."'>".secure_v($subsezval->SEZ_NAME)."</a></b>";
        else
          $subsections="<br><i>".$lang['subforums']."</i><b><a ".$link."'>".secure_v($subsezval->SEZ_NAME)."</a></b>";
        $notfirst=1;
      }
      $querymods="SELECT {$SNAME}_membri.AUTORE as 'MOD', {$SNAME}_membri.HASH as 'MOD_HASH', VALORE
      		FROM {$SNAME}_permessi
      		LEFT OUTER JOIN {$SNAME}_membri on {$SNAME}_permessi.autore = {$SNAME}_membri.hash
      		WHERE CHIAVE_B='IS_MOD' AND CHIAVE_A='".$sezval->ID."'
      		ORDER BY {$SNAME}_permessi.autore ASC,{$SNAME}_permessi.DATE DESC;";
      $mods = $db->get_results($querymods);
      $moderators="";
      $notfirst="";
      $buffer="";
      if($mods)foreach($mods as $modsval) {
      	if(($modsval->MOD_HASH != $buffer) and ($modsval->VALORE))
      	{
		$modhash= @unpack("H32alfa",$modsval->MOD_HASH);
		if($notfirst)
		  $moderators.=", <a href='showmember.php?MEM_ID=".$modhash["alfa"]."'>".$modsval->MOD."</a>";
		else
		  $moderators=" <a href='showmember.php?MEM_ID=".$modhash["alfa"]."'>".$modsval->MOD."</a>";
		$notfirst=1;
	}
	$buffer=$modsval->MOD_HASH;
      }
      if($sezval->REDIRECT){
      	$link="target='_blank' href='".$sezval->REDIRECT;
      }else{
      	$link="href='sezioni.php?SEZID=".$sezval->ID;
      }
		?>
      <tr>
        <td class="row4" width="5%" align="center">
			<img src="img/bf_new.gif" alt="">
		</td>
        <td class="row4">
		<b><a "<?=$link?>'"><?=secure_v($sezval->SEZ_NAME)?></a></b><br />
		<span class="desc">
			<?=secure_v($sezval->SEZ_DESC).$subsections?><br />
			<font color="#808080">
				<i><?=$lang['col_moderators']?>:<?=$moderators?></i>
			</font><br />
		</span>
	</td>
        <td class="row2" align="center"><?=$sezval->THR_NUM?></td>
        <td class="row2" align="center"><?=$sezval->REPLY_NUM?></td>
        <td class="row2" nowrap="nowrap">
			<?=$lang['last_in']?>
			<a href="showmsg.php?SEZID=<?=$MSG->SEZID?>&amp;THR_ID=<?=$hash['alfa']?>&amp;pag=last#end_page">
			<?=secure_v($msg)?></a><br>
			<?=$lang['last_data'].$write_date?><br>
			<?=$lang['last_from']?><a href="showmember.php?MEM_ID=<?=$nickhash['alfa']?>">
			<?=secure_v($MSG->nick)?></a></td>
<?

      $totmsg = $totmsg + $sezval->THR_NUM + $sezval->REPLY_NUM;
    }

  // esistono sottoforum ?
  if($num_sottoforum)
  {    
    echo "<tr> 
            <td class='darkrow2' colspan='5'>&nbsp;</td>
          </tr></table></div>";
  }

  // end sottoforum


?>
<!-- who posted -->
<script  language="javascript" type="text/javascript">
function who_posted(tid,sid)
{
	window.open("who_posted.php?TID=" + tid + "&amp;SID=" + sid, "WhoPosted","toolbar=no,scrollbars=yes,resizable=yes,width=350,height=300");
}
</script>



<?PHP
$SEZID=$_REQUEST['SEZID'];


// se >= 9000 è un forum di categoria e non può contenere messaggi
if(($SEZ_DATA->ORDINE < 9000) and (!$SEZ_DATA->REDIRECT))
{
echo "<a href=\"searcher.php?MODO=1&amp;SEZ=".$SEZID."&amp;ORDER=DESC\">".$lang['req_last']."</a><br><br>";

$Num3d = $Num3d = $db->get_var("SELECT THR_NUM from {$SNAME}_sez WHERE ID=$SEZID AND HIDE='0';");
$NumPag = intval(($Num3d-1) / $ThreadXPage);
$CurrPag = $_REQUEST["pag"];
if (! is_numeric($CurrPag))
  $CurrPag = 0;
if ($CurrPag < 0) $CurrPag = 0;

if($SEZ_DATA->REDIRECT){
	$link="target='_blank' href='".$SEZ_DATA->REDIRECT;
}else{
	$link="href='sezioni.php?SEZID=".$SEZ_DATA->ID;
}

PageSelect();
?>
<div class="borderwrap">
  <div class="maintitle">
    <p class="expand"><? echo $sezeditor; ?></p>
    <?PHP
	echo "<p><a ".$link."'>".secure_v($SEZ_DATA->SEZ_NAME)."</a></p>";
    ?>
  </div>
  <table cellspacing="1">
   <tr>
    <th align="center" width="1%">&nbsp;</th>
    <th align="center" width="1%">&nbsp;</th>
    <th align="left" width="57%" class='titlemedium'><?PHP echo $lang['topic_title'] ?></th>
    <th align="center" width="6%" class='titlemedium'><?PHP echo $lang['topic_replies'] ?></th>
    <th align="center" width="10%" class='titlemedium'><?PHP echo $lang['topic_starter'] ?></th>
    <th align="center" width="7%" class='titlemedium'><?PHP echo $lang['toppic_views'] ?></th>
    <th align="center" width="18%" class='titlemedium'><?PHP echo $lang['topic_laction'] ?></th>
   </tr>

 




<?PHP
/*$query="SELECT msghe.HASH as 'HASH',newmsg.title AS 'title', (last_reply_time+".GMT_TIME.") as last_reply_time,membri.AUTORE as nick,membri.HASH AS 'nickhash',"
  ." repau.AUTORE as dnick, repau.HASH as dnickhash, (msghe.DATE+".GMT_TIME.") AS 'write_date', reply_num, read_num,newmsg.SUBTITLE as 'subtitle' "
  ." FROM {$SNAME}_msghe AS msghe,{$SNAME}_newmsg AS newmsg,{$SNAME}_membri AS membri,{$SNAME}_membri AS repau "
  ." WHERE newmsg.EDIT_OF=msghe.HASH"
  ." AND newmsg.SEZ='".$SEZID."'"
  ." AND newmsg.visibile='1'"
  ." AND membri.HASH=msghe.AUTORE "
  ." AND repau.HASH=msghe.last_reply_author"
  ." ORDER BY msghe.last_reply_time DESC"
  ." LIMIT ".($CurrPag*$ThreadXPage).",$ThreadXPage;";*/
  $query="
	SELECT {$SNAME}_msghe.hash 	as HASH
		, {$SNAME}_newmsg.title 	as title
		, {$SNAME}_newmsg.subtitle
		
		, {$SNAME}_msghe.PINNED as pinned
		, {$SNAME}_msghe.block_date as closed
		, {$SNAME}_msghe.FIXED as fixed
		, {$SNAME}_msghe.SPECIAL as special
		, {$SNAME}_msghe.HOME as home
	
		, {$SNAME}_msghe.autore	as nickhash 
		, autore.AUTORE as nick
		, {$SNAME}_msghe.last_reply_author as dnickhash
		, lastreply.AUTORE 		as dnick
		,({$SNAME}_msghe.DATE+".GMT_TIME.") 				AS open_date
		,({$SNAME}_msghe.last_reply_time+".GMT_TIME.") 	AS last_reply_time
		, {$SNAME}_msghe.reply_num
		, {$SNAME}_msghe.read_num
   FROM {$SNAME}_msghe
   JOIN {$SNAME}_membri as autore on autore.hash = {$SNAME}_msghe.autore
   JOIN {$SNAME}_membri as lastreply on lastreply.hash = {$SNAME}_msghe.last_reply_author
   JOIN {$SNAME}_newmsg on {$SNAME}_newmsg.edit_of = {$SNAME}_msghe.hash
   WHERE {$SNAME}_newmsg.SEZ='$SEZID' AND {$SNAME}_newmsg.visibile='1'
   ORDER BY pinned desc, {$SNAME}_msghe.last_reply_time DESC
   LIMIT ".($CurrPag*$ThreadXPage).",$ThreadXPage;";


$risultato=$db->get_results($query);



if($risultato) foreach($risultato as $riga)
{
  $iden=unpack("H32hex",$riga->HASH);
  
  $reply_date=$std->PostDate($riga->last_reply_time);
  $write_date=$std->PostDate($riga->write_date);
  
  $tmp = $db->get_var("select valore from temp where chiave='".$iden['hex']."';");
  if (($tmp)OR($tmp==='0'))
    $PostStatImage = ($tmp < $riga->reply_num ? "f_norm" : "f_norm_no");
  else
    $PostStatImage = "f_norm";
	
    $nickhash=@unpack("H32alfa",$riga->nickhash);
    $dnickhash=@unpack("H32alfa",$riga->dnickhash);
	

    // pinned
    if ($riga->pinned)
    {
    $post_icon="<img src='img/pinned.gif' alt='Pinned!'>";
    $pinned_str=$lang['topic_pinned'];
    $pinned_close="</b>";
    $ispin=true;
    } else
    {
    $post_icon="";
    $pinned_str="";
    $pinned_close="";
    $ispin=false;
    }
    

    // closed
	if ($riga->closed)  {$PostStatImage = "f_closed";}

    // fixed
	if ($riga->fixed)  {$PostStatImage = "f_fixed";}

// *** separatore: inizio post in evidenza ***
if($ispin AND !$stop_pin)
{
echo "    <tr>
      <td align='center' class='darkrow1'>&nbsp;</td>
      <td align='center' class='darkrow1'>&nbsp;</td>
	  <td align='left' class='darkrow1' colspan='5' style='padding:6px'><b>Discussioni in rilievo</b></td>
    </tr>";
$stop_pin=true;
}


		 
  $rep=$riga->reply_num;
  $i=0;
  $Pages="";
  if($rep>$PostXPage){
     while($rep>0){
        if($i<=$Section){
           $Pages=$Pages."<td align='left' nowrap='nowrap'><span class='pagelink'><a href='showmsg.php?SEZID={$SEZID}&amp;THR_ID=".$iden['hex']."&amp;pag={$i}'>".++$i."</a></span></td>";
           $rep=$rep-$PostXPage;
        }else{
           $Pages=$Pages."<td align='left' nowrap='nowrap'><span class='pagelink'>..</span>&nbsp;<span class='pagelink'><a href='showmsg.php?SEZID={$SEZID}&amp;THR_ID=".$iden['hex']."&amp;pag=last#end_page'>&raquo;</a></span></td>";
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

// *** separatore: fine post in evidenza ***
if (!$ispin AND $stop_pin AND !$stop_not_pin)
{
echo "<td align='center' class='darkrow1'>&nbsp;</td>";
echo "<td align='center' class='darkrow1'>&nbsp;</td>";
echo "<td align='left' class='darkrow1' colspan='5' style='padding:6px'><b>Altre discussioni</b></td>";
$stop_not_pin=true;
}

  ?>
<tr>
	<td align='center' class='row2'>
	<img src='img/<? echo $PostStatImage; ?>.gif' alt=''>
	</td>
	<td align='center' class='row2'><?=$post_icon?></td>
	<td align='left' class='row2'>
	<table border='0' cellpadding='2' cellspacing='0'>
		<tbody>
		<tr>
			<td align='left' nowrap='nowrap'>
			<?=$pinned_str?><a href='showmsg.php?SEZID=<?=$SEZID?>&amp;THR_ID=<?=$iden['hex']?>' title='<?=$lang['topic_start'] . $write_date?>'>
			<?=secure_v($title)?></a>
			<?=$pinned_close?>
			</td>
			<?=$Pages?>
		</tr>
		</tbody>
	</table>&nbsp;<?=secure_v($riga->subtitle)?>
	</td>
	<td align=center class='row4'>
		<a href="javascript:who_posted('<?=$iden['hex']?>','<?=$SEZID?>');"><?=$riga->reply_num?></a>
	</td>
	<td align=center class='row4'>
		<small><u>
			<a href='showmember.php?MEM_ID=<?=$nickhash['alfa']?>'><?=secure_v($riga->nick)?></a>
		</u></small>
	</td>
	<td align=center class='row4'><?=$riga->read_num?></td>
	<tD align=left class='row4'>
		<small><?=$reply_date?><br>
		<a href="showmsg.php?SEZID=<?=$SEZID?>&amp;THR_ID=<?=$iden['hex']?>&amp;pag=last#end_page">
		<?=$lang['topic_last']?></a>: 
		<b>
			<a href='showmember.php?MEM_ID=<?=$dnickhash['alfa']?>'>
				<?=secure_v($riga->dnick)?></a>
		</b>
		</small>
	</tD>
</tr>
<?



}



echo "</table></div>";
 echo "</td></tr>";

PageSelect(); 

} // FI - Forum di categoria

}else{ // Fine controllo di visibilità
	$std->Error($lang['sezioni_hide']);
}


include ("end.php");
?>
