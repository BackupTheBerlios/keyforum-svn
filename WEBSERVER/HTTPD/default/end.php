<?
// carico la lingua per la end
$lang = $std->load_lang('lang_end', $blanguage );

$today=$lang['day'.date("w")]." ".date("j")." ".$lang['month'.date("n")]." ".date("Y")." - ".date("g:i a");

?>
<tr>
  <td>
  <?
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
  ?>
   <?PHP
       if ((!$SEZ_DATA->ID)AND($whereiam=="index")){
   ?>
   <br>
   <div class="borderwrap" id="fo_stat">
     <div class="maintitle">
       <p class="expand"></p>
       <p><? echo $lang['mtitle_stat']; ?></p>
     </div>
     <table cellspacing="1">
       <tr>
	<th align="right" colspan="2">&nbsp;
	</th>
       </tr>
       <tr>
	<td class="formsubtitle" colspan="2"><img src='img/connect.gif' alt=''>&nbsp;
        <?PHP
           $idquery="SELECT value FROM config WHERE MAIN_GROUP='SHARE' AND SUBKEY='".$SNAME."' AND FKEY='ID';";
           $idriga = $db->get_var($idquery);
           $req_nod[INFO][FORUM][0]=pack("H*", $idriga);
           $core   = new CoreSock;
           if ( !(@$core->Connect()) ) echo "Core offline!";
           else {
                 $core->Send($req_nod);
                 if (!($risposta=$core->Read())) die ($lang['timeout']);
                 if (!$risposta[INFO][FORUM][$req_nod[INFO][FORUM][0]][NUM_NODI])
                     echo $lang['perl_noderror3'];
                 else echo $lang['perl_node1'].$risposta[INFO][FORUM][$req_nod[INFO][FORUM][0]][NUM_NODI].$lang['perl_node2'];
           }
        ?>
        </td>
       </tr>
       <tr>
	<td class="row1" width="1%"><img src='img/stats.gif' border='0' alt='Stats'></td>
	<td class="row2">
	 <? if ($totmsg) {print $lang['stat_dbmess1']."<b>$totmsg</b>".$lang['stat_dbmess2']."<br>";} ?>
	 <div class="thin">
         <?
           $num_msg_inserted=$db->get_var("SELECT count(1) FROM {$SNAME}_congi WHERE INSTIME>'".(time()-3600)."' AND (TYPE='1' OR TYPE='2');");
	   if($num_msg_inserted)
	     echo "{$lang['stat_dbmesslh1']}<b>$num_msg_inserted</b>{$lang['stat_dbmesslh2']}";
	 ?>
	 </div>
	 <?
	    $reg_users=$db->get_var("SELECT COUNT(AUTORE) FROM {$SNAME}_membri;");
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
	    echo '<td width=\"33%\" align="right"><img src="img/stat_sql.gif" alt="">&nbsp;'.$lang['stat_numquery'].'<b>'.$db->num_queries.'</b></td>';
	 ?>
	 </tr></table>
	</td>
       </tr>
     </table>
   </div>
   <?
     }else{
   ?>
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
	    echo "<td width=\"33%\" align='right'><img src='img/stat_sql.gif' alt=''>&nbsp;<b>$db->num_queries</b> queries</td>";
		
	 ?> </tr></table>
	</td>
       </tr>
     </table>
   </div>
   <?
     };
   ?>
<? 
$newurl = str_replace('&','&amp;',$_SERVER['QUERY_STRING']);
$newurl="chlang.php?".$newurl."&amp;script=".$_SERVER['PHP_SELF']."&amp;lang="; 
   
   $seleng["$blanguage"]="selected";


 ?> 
  </td>
</tr>
<tr>
<? $revision = file("revision.txt"); ?>
  <td align=center><br>KeyForum 0.43 <b>Alfa</b> rev. <? echo $revision[0]; ?></td>
</tr>
</table>
<a name="end_page"></a>

</body>
</html>