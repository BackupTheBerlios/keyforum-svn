<?
// carico la lingua per la end
$lang += $std->load_lang('lang_end', $blanguage );

$today=$std->k_date($lang['bottom_date'])." - ".date($lang['bottom_time']);
?>
<tr>
  <td>
  <?
  
  $newurl = str_replace('&','&amp;',$_SERVER['QUERY_STRING']);
  $newurl="chlang.php?".$newurl."&amp;script=".$_SERVER['PHP_SELF']."&amp;lang="; 
     
  $seleng["$blanguage"]="selected";
  
   echo "
   <br>
   <table cellpadding='2' width='100%' bgcolor='#8394B2'>
    <tr>
     <td>
      <form method='POST' name='langform' action=''>
       <select name='langjump' class='content' size='1' onchange='location.href=document.langform.langjump.options[document.langform.langjump.selectedIndex].value' style='font-family: Verdana; font-size: 8pt'>
        <optgroup label='".$lang['language_selection']."'> 			
         <option ".$seleng["eng"]." value='".$newurl."eng'>English</option>
         <option ".$seleng["ita"]." value='".$newurl."ita' >Italiano</option>
        </optgroup>
       </select>
      </form>
     </td>
     <td align=right>";
     
    // forum jumper
    echo $std->ForumJumper($_REQUEST['SEZID']);
     
     echo "</td>
    </tr>
 </table>";


       if ((!$SEZ_DATA->ID)AND($whereiam=="index")){
      
      // lo stato delle categorie salvato nel cookie
      $hidesez=explode(",",$std->GetKFcookie("collapseprefs",$_ENV["sesname"]));
      
      if (in_array("stat",$hidesez))
       {
          $divshow = 'none';
          $divhide = 'show';
      } else
      {
          $divshow = 'show';
          $divhide = 'none';
      }
   
      
      
      ?>
<br>
   
   <div class="borderwrap" style="display:<? echo $divhide; ?>" id="divhide_stat">
   	<div class="maintitle">
   		<p class="expand"><a href="javascript:ShowHideSection('stat', 0);"><img src='img/exp_plus.gif' border='0'  alt='Expand' /></a></p>
   		<p><? echo $lang['mtitle_stat']; ?></p>
   	</div>
   </div>
   

   
   <div class="borderwrap" style="display:<? echo $divshow; ?>" id="divshow_stat">
     <div class="maintitle">
       <p class="expand"><a href="javascript:ShowHideSection('stat', 1);"><img src='img/exp_minus.gif' border='0'  alt='Expand' /></a></p>
       <p><? echo $lang['mtitle_stat']; ?></p>
     </div>
     <table cellspacing="1">
       <tr>
	<th align="right" colspan="2">&nbsp;
	</th>
       </tr>
       <tr>
	   	   <!-- BEGIN Nodi connessi -->
	<td class="formsubtitle" colspan="2"><img src='img/connect.gif' alt=''>&nbsp;
        <?PHP
           $idquery="SELECT value FROM config WHERE MAIN_GROUP='SHARE' AND SUBKEY='".$SNAME."' AND FKEY='ID';";
           $idriga = $db->get_var($idquery);
           $req_nod[INFO][FORUM][0]=pack("H*", $idriga);
           $core   = new CoreSock;
           if ( !(@$core->Connect()) ) echo "<font color=red>Core offline!</font>";
           else {
                 $core->Send($req_nod);
                 if (!($risposta=$core->Read())) {
                   echo "<font color=red>".($lang['timeout']."</font><br>");
                   } else {
                 if (!$risposta[INFO][FORUM][$req_nod[INFO][FORUM][0]][NUM_NODI])
                     echo $lang['perl_noderror3'];
                 else echo $lang['perl_node1'].$risposta[INFO][FORUM][$req_nod[INFO][FORUM][0]][NUM_NODI].$lang['perl_node2'];
                   }
           }
        ?>
        </td>
       </tr>
	   <!-- End Nodi connessi -->
       <tr>
   	   <!-- Begin  totale messaggi -->
	<td class="row1" width="1%"><img src='img/stats.gif' border='0' alt='Stats'></td>
	<td class="row2">
	 <? if ($totmsg) {print $lang['stat_dbmess1']."<b>$totmsg</b>".$lang['stat_dbmess2']."<br>";} ?>
	 <div class="thin">
	 <!-- Messaggi scritti nell'ultima ora -->
	<?
	$timelimit = time()-GMT_TIME-3600;
	$query ="SELECT count(1) as num from {$SNAME}_reply where date > $timelimit AND visibile='1' 
			UNION
			SELECT count(1) from {$SNAME}_newmsg where date > $timelimit AND visibile='1'";
	$results = $db->get_results($query);
	$num_rep_inserted = $results[0]->num;
	$num_thr_inserted = $results[1]->num;
	if($num_rep_inserted || $num_thr_inserted)
	{
		echo "{$lang['stat_dbmesslh1']} <b>$num_rep_inserted</b> {$lang['stat_dbmesslh2rep']}<br>";
		echo "{$lang['stat_dbmesslh1']} <b>$num_thr_inserted</b> {$lang['stat_dbmesslh2thr']}";
	}
	else 
	{
		echo $lang['stat_dbmesslh3'];
	}
	?>
	 </div>
	 <!-- Utenti iscritti -->
	 <?
	    $reg_users=$db->get_var("SELECT COUNT(AUTORE) FROM {$SNAME}_membri WHERE is_auth='1';");
	    if($reg_users)
              echo "{$lang['stat_reguser1']}<b>$reg_users</b>{$lang['stat_reguser2']}";
	 ?>
	</td>
       </tr>
       <tr>
	<td class="row1" colspan="2">
	
	
	<table width="100%"><tr>
	 <?PHP
	 
	 	//TIME
	    $Timer2 = microtime();
	    $Timer2 = explode(" ",$Timer2);
	    $Timer2 = $Timer2[0] + $Timer2[1];
	    echo '<td width="33%"><img src="img/stat_time.gif" alt="">&nbsp;'.$lang['stat_extime'].'<b>'.round(($Timer2 - $Timer1), 4).'</b> sec</td>';
	    // CURRENT DATE/TIME
	    echo "<td width=\"33%\" align=center>$today</td>";	
	     //Query
	    echo "<td width=\"33%\" align=\"right\"><img src=\"img/stat_sql.gif\" alt=\"\">&nbsp;".$lang['stat_numquery']."<b>$db->num_queries</b> / core calls: <b>$corecalls</b></td>";
	 ?>
	 </tr></table>
	</td>
       </tr>
     </table>
   </div>
   <?
     }else{
   ?>
   <br>
   <div class="borderwrap" id="fo_stat">
     <table cellspacing="1"width="100%">
       <tr>
	<td align="left" class="row5"><table width="100%"><tr>
	 <?PHP
	 	//TIME
	    $Timer2 = microtime();
	    $Timer2 = explode(" ",$Timer2);
	    $Timer2 = $Timer2[0] + $Timer2[1];
	    echo '<td width="33%"><img src="img/stat_time.gif" alt="">&nbsp;'.$lang['stat_extime'].'<b>'.round(($Timer2 - $Timer1), 4).'</b> sec</td>';
	    
	    // CURRENT DATE/TIME
	    echo "<td width=\"33%\" align=center>$today</td>";
		//QUERY
	    echo "<td width=\"33%\" align=\"right\"><img src=\"img/stat_sql.gif\" alt=\"\">&nbsp;".$lang['stat_numquery']."<b>$db->num_queries</b> / core calls: <b>$corecalls</b></td>";
		
	 ?> </tr></table>
	</td>
       </tr>
     </table>
   </div>
   <?
     };
   ?>

  </td>
</tr>
<? 
if($whereiam=="index")
{
echo "<tr><td align=right>";
$sezcollector .= "stat";
echo "<a href=\"javascript:ShowHideAll('$sezcollector', 0,'{$_ENV['sesname']}');\"><img src='img/exp_minus.gif' border='0'  alt='Collapse All' /></a> | <a href=\"javascript:ShowHideAll('$sezcollector', 1,'{$_ENV['sesname']}');\"><img src='img/exp_plus.gif' border='0'  alt='Expand All' /></a>";
echo "</td></tr>";
}
?>
<tr>
<? $revision = file("revision.txt"); ?>
  <td align=center><br>KeyForum 0.43 <b>Alfa</b> rev. <? echo $revision[0]; ?></td>
</tr>
</table>
<a name="end_page"></a>

<? if ($_REQUEST['debug']) $db->querylog(); ?>

</body>
</html>