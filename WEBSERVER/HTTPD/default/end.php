<?
// carico la lingua per la end
$lang = $std->load_lang('lang_end', $blanguage );
?>
<tr>
  <td>
   <br><br>
   <? $newurl="chlang.php?".$_SERVER['QUERY_STRING']."&script=".$_SERVER['PHP_SELF']."&lang="; 
   
   $seleng["$blanguage"]="selected";

 echo"
 <form method=\"POST\" name=\"langform\">
   <select class=content name=\"langjump\" size=\"1\" onchange=\"location.href=document.langform.langjump.options[document.langform.langjump.selectedIndex].value\" style=\"font-family: Verdana; font-size: 8pt\" >
   		<optgroup label=\"Scegli la Lingua\"> 			
   <option ".$seleng["eng"]." value='".$newurl."eng'>English</option>
   <option ".$seleng["ita"]." value='".$newurl."ita' >Italiano</option>
   		</optgroup>
   	</select>
</form>
 "; 
 ?> 
  
   <?PHP
       if ((!$SEZ_DATA['ID'])AND(!$whereiam)){
   ?>
   <div class="borderwrap" style="display:show" id="fo_stat">
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
	<td class="formsubtitle" colspan="2"><img src='img/connect.gif'>&nbsp;
        <?PHP
           $idquery="SELECT value FROM config WHERE MAIN_GROUP='SHARE' AND SUBKEY='".$SNAME."' AND FKEY='ID';";
           $idrisultato = mysql_query($idquery) or Muori ($lang['inv_query'] . mysql_error());
           $idriga = mysql_fetch_assoc($idrisultato);
           $req_nod[INFO][FORUM][0]=pack("H*", $idriga['value']);
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
           $risp=mysql_query("SELECT count(*) AS NUM FROM ".$_ENV['sesname']."_congi WHERE INSTIME>'".(time()-3600)."' AND (TYPE='1' OR TYPE='2');");
	   if($ris=mysql_fetch_assoc($risp))
	     echo $lang['stat_dbmesslh1']."<b>".$ris['NUM']."</b>".$lang['stat_dbmesslh2'];
	 ?>
	 </div>
	 <?
	    $risultato=mysql_query("SELECT COUNT(AUTORE) AS c FROM ".$_ENV['sesname']."_membri;");
	    if($ris2=mysql_fetch_assoc($risultato))
              echo $lang['stat_reguser1']."<b>".$ris2['c']."</b>".$lang['stat_reguser2'];
	 ?>
	</td>
       </tr>
       <tr>
	<td class="row1" colspan="2">
	 <?PHP
	    $Timer2 = microtime();
	    $Timer2 = explode(" ",$Timer2);
	    $Timer2 = $Timer2[0] + $Timer2[1];
	    echo '<img src="img/stat_time.gif">&nbsp;'.$lang['stat_extime'].'<b>'.round(($Timer2 - $Timer1), 4).'</b> sec';
	 ?>
	</td>
       </tr>
     </table>
   </div>
   <?
     }else{
   ?>
   <div class="borderwrap" style="display:show" id="fo_stat">
     <table cellspacing="1">
       <tr>
	<td align="left" class="row5">
	 <?PHP
	    $Timer2 = microtime();
	    $Timer2 = explode(" ",$Timer2);
	    $Timer2 = $Timer2[0] + $Timer2[1];
	    echo '<img src="img/stat_time.gif">&nbsp;'.$lang['stat_extime'].'<b>'.round(($Timer2 - $Timer1), 4).'</b> sec';
	 ?>
	</td>
       </tr>
     </table>
   </div>
   <?
     };
   ?>
  </td>
</tr>
<tr>
  <td align=center><br>KeyForum 0.43 <b>Alfa</b></td>
</tr>
</table>
<a name="end_page"></a>
</body>
</html>