<?PHP
// v. 0.14
include ("testa.php");
// carico la lingua per le sezioni
$lang = $std->load_lang('lang_userlist', $blanguage );

$SNAME=$_ENV['sesname'];
$whereiam="userlist";

function PageSelect() {
?>
<table border="0" cellpadding="5px" cellspacing="0" width="100%">
  <tbody>
  <tr>
    <td align="left" nowrap="nowrap" width="20%">
<?
	global $lang;
  global $NumPag;
  global $CurrPag;
  global $Section;
  if($_REQUEST['validati']){
     if($_REQUEST['nonvalidati']){
        $link="userlist.php?validati='1'&nonvalidati='1'&";
     }else{
        $link="userlist.php?validati='1'&";
     }
  }else{
     if($_REQUEST['nonvalidati']){
        $link="userlist.php?nonvalidati='1'&";
     }else{
        $link="userlist.php?";
     }
  }
  if($_REQUEST['order_by']){
     $link=$link."order_by=".$_REQUEST['order_by']."&";
  }
  if($_REQUEST['order']){
     $link=$link."order=".$_REQUEST['order']."&";
  }
  $link = $link."pag=";
  if ($NumPag > 0) {
    echo "<span class='pagelink'>".($NumPag+1)."&nbsp;".$lang['usrlist_pages']."</span>&nbsp;";
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
      </tr>
  </tbody>
</table>
<?
}
?>
<tr>
 <td>
  <form method='get' action='userlist.php'>
    <fieldset class='row3'>
     <? echo "<legend>".$lang['usrlist_show']."</legend>"; ?>
     <table border="0" cellpadding="2px" cellspacing="0" width="100%">
      <tr>
       <td>
        <input type='checkbox' name='validati' value='1'<? if($_REQUEST['validati']) echo ' checked'; echo ">".$lang['usrlist_valid']; ?>
        <input type='checkbox' name='nonvalidati' value='1'<? if($_REQUEST['nonvalidati']) echo ' checked'; echo ">".$lang['usrlist_notvalid']."&nbsp;"; ?>
        <input type='hidden' name='pag' value='0'>
        &nbsp;
        <? echo $lang['usrlist_orderby']; ?>
        <select name="order_by" size="1">
          <option <? if(!$_REQUEST['order_by']) echo "selected"; ?><? if($_REQUEST['order_by']=="DATE") echo "selected"; ?> value='DATE'><? echo $lang['usrlist_orderby_date']; ?></option>
          <option <? if($_REQUEST['order_by']=="AUTORE") echo "selected"; ?> value='AUTORE'><? echo $lang['usrlist_orderby_nick']; ?></option>
          <option <? if($_REQUEST['order_by']=="msg_num") echo "selected"; ?> value='msg_num'><? echo $lang['usrlist_orderby_msg']; ?></option>
        </select>
        <select name="order" size="1">
          <option <? if(!$_REQUEST['order']) echo "selected"; ?><? if($_REQUEST['order']=="ASC") echo "selected"; ?> value='ASC'><? echo $lang['usrlist_order_asc']; ?></option>
          <option <? if($_REQUEST['order']=="DESC") echo "selected"; ?> value='DESC'><? echo $lang['usrlist_order_des']; ?></option>
        </select>
        <? echo"<input type='submit' value='".$lang['usrlist_apply']."' class='button'>"; ?>
       </td>
      </tr>
     </table>
    </fieldset>
  </form>
<?
if($_REQUEST['validati']){
   if($_REQUEST['nonvalidati']){
      $risultato=mysql_query("SELECT count(HASH) AS c FROM {$SNAME}_membri;");
   }else{
      $risultato=mysql_query("SELECT count(HASH) AS c FROM {$SNAME}_membri WHERE is_auth='1';");
   }
}else{
   if($_REQUEST['nonvalidati']){
      $risultato=mysql_query("SELECT count(HASH) AS c FROM {$SNAME}_membri WHERE is_auth='0';");
   }else{
      $risultato=mysql_query("SELECT count(HASH) AS c FROM {$SNAME}_membri WHERE is_auth='2';");
   }
}
$ris=mysql_query($risultato);
$riga = mysql_fetch_assoc($risultato);
$Num3d = $riga['c'];
$NumPag = intval(($Num3d-1) / $UserXPage);
$CurrPag = $_REQUEST['pag'];
if (! is_numeric($CurrPag))
  $CurrPag = 0;
if ($CurrPag < 0) $CurrPag = 0;

PageSelect();

if(!$_REQUEST['order_by']){
   $order_by="DATE";
}else{
   $order_by=$_REQUEST['order_by'];
}
if(!$_REQUEST['order']){
   $order="";
}else{
   $order=" ".$_REQUEST['order'];
}
?>
<div class="borderwrap">
  <div class="maintitle">
    <p class="expand"></p>
    <?PHP echo "<p>".$lang['usrlist_userlist']."</p>";?>
  </div>
  <form method="POST" action="authusers.php">
  <table cellspacing="1">
  <?PHP echo"
    <tr>
      <th align=\"right\" width=\"1%\" class='titlemedium'>".$lang['usrlist_num']."</th>
      <th align=\"left\" width=\"21%\" class='titlemedium'>&nbsp;".$lang['usrlist_nick']."</th>
      <th align=\"center\" width=\"26%\" class='titlemedium'>".$lang['usrlist_hash']."</th>
      <th align=\"center\" width=\"16%\" class='titlemedium'>".$lang['usrlist_date']."</th>
      <th align=\"right\" width=\"11%\" class='titlemedium'>".$lang['usrlist_messages']."</th>
      <th align=\"center\" width=\"21%\" class='titlemedium'>".$lang['usrlist_gruop']."</th>
	  <th align=\"center\" width=\"4%\" class='titlemedium'>".$lang['usrlist_action']."</th>
    </tr>";?>
<?PHP
if($_REQUEST['validati']){
   if($_REQUEST['nonvalidati']){
      $risultato=mysql_query("SELECT HASH,AUTORE, DATE, TYPE, is_auth, msg_num FROM {$SNAME}_membri ORDER BY ".$order_by.$order." LIMIT ".($CurrPag*$UserXPage).",$UserXPage;");
   }else{
      $risultato=mysql_query("SELECT HASH,AUTORE, DATE, TYPE, is_auth, msg_num FROM {$SNAME}_membri WHERE is_auth='1' ORDER BY ".$order_by.$order." LIMIT ".($CurrPag*$UserXPage).",$UserXPage;");
   }
}else{
   if($_REQUEST['nonvalidati']){
      $risultato=mysql_query("SELECT HASH,AUTORE, DATE, TYPE, is_auth, msg_num FROM {$SNAME}_membri WHERE is_auth='0' ORDER BY ".$order_by.$order." LIMIT ".($CurrPag*$UserXPage).",$UserXPage;");
   }else{
      $risultato=mysql_query("SELECT HASH,AUTORE, DATE, TYPE, is_auth, msg_num FROM {$SNAME}_membri WHERE is_auth='2' ORDER BY ".$order_by.$order." LIMIT ".($CurrPag*$UserXPage).",$UserXPage;");
   }
}
# 2 Scambio nodi
# 1 passivo
# 3 manuale
$i=$CurrPag*$UserXPage;
while($ris=mysql_fetch_assoc($risultato)) {
	$userhash=unpack("H32hex",$ris['HASH']);
	echo "
    <tr>
	<td class='row1' align='right'>".++$i."</td>
	<td class='row2' align='left'>&nbsp;".secure_v($ris['AUTORE'])."</td>
	<td class='row2' align='center'>".$userhash['hex']."</td>
	<td class='row1' align='center'>".strftime("%d/%m/%y  - %H:%M:%S",$ris['DATE'])."</td>
	<td class='row1' align='right'>".$ris['msg_num']."</td>
	<td class='row2' align='center'>";
	if($ris['is_auth']){
	  echo $lang['usrlist_member'] . "</td>\n<td class='row2'>";
	} else {
		echo $lang['usrlist_validated'];
		echo "</td>\n\t<td class='row2'><input type=\"checkbox\" name=\"toauth[$i]\" value=\"$userhash[hex]\" />Auth";
		$displaysubmit=1;
	};
	echo "</td>\n</tr>";
};

?>
 
  </table>
</div>

<table width=100%>
	<tr><td> <? PageSelect(); ?> </td>
		<td align='right'><?PHP if ($displaysubmit)
							echo "<input type='submit' value='Do it!' name='doauth' class='button'></td>";?>
	</tr>
</table>

<?PHP
include ("end.php");
?>
