<?PHP
// v 0.1

include ("testa.php");

// carico la lingua per la ricerca
$lang = $std->load_lang('lang_search', $blanguage );

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

  $SQLQuery1 = "SELECT msg.hash, '' as rep_of, msg.sez, msg.edit_of, msg.type, msg.date, msg.title, msg.subtitle, msg.body FROM {$SNAME}_newmsg as msg $NickTable where visibile = '1' and $where";
  $SQLQuery2 = "SELECT msg.hash, msg.rep_of, 0 as sez, msg.edit_of, msg.type, msg.date, msg.title, '' as subtitle, msg.body FROM {$SNAME}_reply as msg $NickTable where visibile = '1' and $where";
  $SQLQuery = "select * from (($SQLQuery1) UNION ($SQLQuery2)) as result_msg order by `".$_REQUEST["sort_key"]."` ".$_REQUEST["sort_order"];
  $risultato=mysql_query($SQLQuery) or Muori ("Query non valida: " . mysql_error());
  while ($riga = mysql_fetch_assoc($risultato)) {
    echo '<a href="">link</a><br>';
  }
}

?>

 </td>
</tr>

<?PHP
include ("end.php");
?>
