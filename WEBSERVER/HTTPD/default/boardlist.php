<?PHP
// v. 0.4
include ("testa.php");
$SNAME=$_ENV['sesname'];
$whereiam="boardlist";

// carico la lingua per la boardlist
$lang = $std->load_lang('lang_boardlist', $blanguage );

function PageSelect() {
?>
<table border="0" cellpadding="5px" cellspacing="0" width="100%">
  <tbody>
  <tr>
    <td align="left" nowrap="nowrap" width="20%">
<?
  global $NumPag;
  global $CurrPag;
  global $Section;
  $link = "boardlist.php?pag=";
  if ($NumPag > 0) {
    echo "<span class='pagelink'>".($NumPag+1)."&nbsp;Pagine</span>&nbsp;";
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
<?
$risultato=mysql_query("SELECT count(SUBKEY) AS c FROM config WHERE FKEY='PKEY';");
$ris=mysql_query($risultato);
$riga = mysql_fetch_assoc($risultato);
$Num3d = $riga['c'];
$NumPag = intval(($Num3d-1) / $BoardXPage);
$CurrPag = $_REQUEST['pag'];
if (! is_numeric($CurrPag))
  $CurrPag = 0;
if ($CurrPag < 0) $CurrPag = 0;

PageSelect();
?>
<?
  echo "
<div class=\"borderwrap\">
  <div class=\"maintitle\">
    <p class=\"expand\"></p>
    <p>".$lang['board_list']."</p>
  </div>
  <table cellspacing=\"1\">
    <tr>
      <th align=\"right\" width=\"1%\" class='titlemedium'>".$lang['board_num']."</th>
      <th align=\"left\" width=\"10%\" class='titlemedium'>&nbsp;".$lang['board_name']."</th>
      <th align=\"center\" width=\"10%\" class='titlemedium'>".$lang['board_bind']."</th>
      <th align=\"center\" width=\"7%\" class='titlemedium'>".$lang['board_port']."</th>
      <th align=\"center\" width=\"72%\" class='titlemedium'>".$lang['board_pkey']."</th>
    </tr>";
?>
<?PHP
$i=$CurrPag*$BoardXPage;
$querywse="SELECT DISTINCT SUBKEY, VALUE FROM config WHERE MAIN_GROUP='SHARE' AND FKEY='PKEY' LIMIT ".($CurrPag*$BoardXPage).",$BoardXPage;";
$responsewse=mysql_query($querywse) or Muori ($lang['inv_query'] . mysql_error());
while($valuewse=mysql_fetch_assoc($responsewse)){

$queryws="SELECT DISTINCT SUBKEY FROM config WHERE FKEY='SesName' AND VALUE='".$valuewse['SUBKEY']."';";
$responsews=mysql_query($queryws) or Muori ($lang['inv_query'] . mysql_error());
while($valuews=mysql_fetch_assoc($responsews)){
   $querywsl="SELECT SUBKEY, FKEY, VALUE FROM config WHERE SUBKEY='".$valuews['SUBKEY']."' OR SUBKEY='".$BNAME."';";
   $responsewsl=mysql_query($querywsl) or Muori ("Query non valida: " . mysql_error());
   while($valuewsl=mysql_fetch_assoc($responsewsl)){
      if(($valuewsl['FKEY']=="BIND")AND($valuewsl['SUBKEY']==$valuews['SUBKEY'])){
         $bindwsl=$valuewsl['VALUE'];
      }elseif(($valuewsl['FKEY']=="PORTA")AND($valuewsl['SUBKEY']==$valuews['SUBKEY'])){
         $portwsl=$valuewsl['VALUE'];
      }elseif($valuewsl['FKEY']=="BIND"){
         $bindboard=$valuewsl['VALUE'];
      }
   }
   if($portwsl){
      if((!$bindwsl)OR($bindwsl==$_SERVER['REMOTE_ADDR'])OR($bindwsl==$bindboard)){
         $req_dec[FUNC][Base642Dec]=$valuewse['VALUE'];
         $core   = new CoreSock;
         $core->Send($req_dec);
         if (!($rep_dec=$core->Read())) die ($lang['timeout']);
         echo "<tr>
	    <td class='row1' align='right'>".++$i."</td>
	    <td class='row2' align='left'>&nbsp;<a href=\"http://$bindwsl:$portwsl\">".$valuews['SUBKEY']."</a></td>
            <td class='row2' align='center'>".$bindwsl."</td>
            <td class='row2' align='center'>".$portwsl."</td>
	    <td class='row2' align='center'><textarea rows='5' name='chiave' cols='70' readonly class='row2' style='border: none; overflow: auto'>".$rep_dec[FUNC][Base642Dec]."</textarea></td>\n</tr>";
      }
   }
}

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
