<?PHP
// v. 0.4
include ("testa.php");
$SNAME=$_ENV['sesname'];
$whereiam="boardlist";

// carico la lingua per la boardlist
$lang += $std->load_lang('lang_boardlist', $blanguage );

?>
<tr>
 <td>

<?
$Num3d = $db->get_var("SELECT count(SUBKEY) FROM config WHERE FKEY='PKEY';");
$NumPag = intval(($Num3d-1) / $BoardXPage);
$CurrPag = $_REQUEST['pag'];
if (! is_numeric($CurrPag))
  $CurrPag = 0;
if ($CurrPag < 0) $CurrPag = 0;

PageSelect();

//acquisizione dati:

$i = 0;

foreach($config['WEBSERVER'] as $nome_board=>$array)
{
	if($config['SHARE'][$array['SesName']]['PKEY']){
		$board[$i] = Array('nome' =>$nome_board, 'nome_ses' => $array['SesName'], 'pkey' => $config['SHARE'][$array['SesName']]['PKEY']);
		$i++;
	}
} //prendo tutte le board;

//output
?>
<div class='borderwrap'>
  <div class='maintitle'>
    <p class='expand'></p>
    <p><?=$lang['board_list']?></p>
  </div>
  <table cellspacing='1'>
    <tr>
      <th align='right'  width='1%' class='titlemedium'><?=$lang['board_num']?></th>
      <th align='left'   width='10' class='titlemedium'>&nbsp;<?=$lang['board_name']?></th>
      <th align='center' width='10%' class='titlemedium'><?=$lang['board_bind']?></th>
      <th align='center' width='7%' class='titlemedium'><?=$lang['board_port']?></th>
      <th align='center' width='72%' class='titlemedium'><?=$lang['board_pkey']?></th>
    </tr>
<?
$i=$CurrPag*$BoardXPage;
$tot = $i + min(count($config['SHARE']) -$i ,$BoardXPage);
for($i=$CurrPag*$BoardXPage;$i<$tot;$i++)
{
	if($config['WEBSERVER'][$board[$i]['nome']]['PORTA'])
	{
	 if((!$config['WEBSERVER'][$board[$i]['nome']]['BIND'])OR($config['WEBSERVER'][$board[$i]['nome']]['BIND']==$_SERVER['REMOTE_ADDR'])OR($config['WEBSERVER'][$board[$i]['nome']]['BIND']==$bindboard)){
		 echo "<tr>
			<td class='row1' align='right'>".($i+1)."</td>
			<td class='row2' align='left'>
			&nbsp;<a target='_blank' href='http://{$config['WEBSERVER'][$board[$i]['nome']]['BIND']}:{$config['WEBSERVER'][$board[$i]['nome']]['PORTA']}'>{$board[$i]['nome']}</a>
			</td>
				<td class='row2' align='center'>".$config['WEBSERVER'][$board[$i]['nome']]['BIND']."</td>
				<td class='row2' align='center'>".$config['WEBSERVER'][$board[$i]['nome']]['PORTA']."</td>
			<td class='row2' align='center'>
				<textarea rows='5' name='chiave' cols='70' readonly class='row2' style='border: none; overflow: auto'>".$board[$i]['pkey']."</textarea></td>\n</tr>";
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




//FUNZIONI
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
}//END PAGE SELECT FUNCTION
?>
