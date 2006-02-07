<?PHP
// v. 0.4
include ("testa.php");
include ("lib/bbcode_parser.php");
$SNAME=$_ENV['sesname'];
$whereiam="boardlist";

// carico la lingua per la boardlist
$lang += $std->load_lang('lang_boardlist', $blanguage );

?>
<tr>
 <td>

<?
$Num3d = count($config["SHARE"]);
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
	if($config['WEBSERVER'][$nome_board]['SesName']){
		$board[$i] = Array('nome' =>$nome_board, 'pkey' => $config['SHARE'][$array['SesName']]['PKEY']);
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
  <table cellspacing='1' width="100%">
    <tr>
      <th align='right'  width='0%' class='titlemedium'><?=$lang['board_num']?></th>
      <th align='left'    class='titlemedium'>ID</th>
<!--  Ci interessano davvero?
	  <th align='center' class='titlemedium'><?=$lang['board_bind']?></th>
      <th align='center' class='titlemedium'><?=$lang['board_port']?></th>
-->
      <th align='left' class='titlemedium'><?=$lang['board_name']?></th>
	  <th align='left' class='titlemedium'>Admin</th>
      <th align='left' class='titlemedium'>Descrizione</th>
      <th align='left' class='titlemedium'>Note</th>
<!-- problemi di layout
	  <th align='center' class='titlemedium' width="20%"><?=$lang['board_pkey']?></th>
-->
    </tr>
<?
$tot = min($Num3d - $CurrPag*$BoardXPage,$BoardXPage);
for($i=$CurrPag*$BoardXPage;$i<$CurrPag*$BoardXPage+$tot;$i++)
{
	if($config['WEBSERVER'][$board[$i]['nome']]['PORTA'])
	{
	if((!$config['WEBSERVER'][$board[$i]['nome']]['BIND'])OR($config['WEBSERVER'][$board[$i]['nome']]['BIND']==$_SERVER['REMOTE_ADDR'])OR($config['WEBSERVER'][$board[$i]['nome']]['BIND']==$config['WEBSERVER'][$SNAME]['BIND'])OR($config['WEBSERVER'][$board[$i]['nome']]['BIND']=='*'))
		{
	 		if((!$config['WEBSERVER'][$board[$i]['nome']]['BIND'])OR($config['WEBSERVER'][$board[$i]['nome']]['BIND']=='*'))
			{
	 			$bind=$_SERVER['SERVER_NAME'];
		 	}
			else
			{
		 		$bind=$config['WEBSERVER'][$board[$i]['nome']]['BIND'];
	 		}
			$query = "SELECT SUBKEY, value FROM {$board[$i]['nome']}_conf WHERE `GROUP` = 'FORUM' AND FKEY ='DATA' AND PRESENT ='1' ";
			$result = $db->get_results($query);
			foreach($result as $row)
			{
				$board_conf[$row->SUBKEY] = $row->value;
			}
			$to_show[] = array(
				 'num'		=>$i+1
				,'nome'		=>$board[$i]['nome']
				,'bind'		=>$bind
				,'porta'	=>$config['WEBSERVER'][$board[$i]['nome']]['PORTA']
				,'pkey'		=>$board[$i]['pkey']
				,'conf'		=>$board_conf
				);
		}
	}
}
if($to_show)foreach($to_show as $key=>$array)
{
	$nota_estesa = secure_v($array['conf']['NOTE']);
	if(strlen($nota_estesa) > 100) $nota_taglio = html_substr($nota_estesa,70);
	echo "
	<tr>
	<td class='row1' align='center'>{$array['num']}</td>
	<td class='row2' align='left'><a target='_blank' href='http://{$array['bind']}:{$array['porta']}'>{$array['nome']}</a></td>
	<!-- <td class='row2' align='center'>$bind</td>
	<td class='row2' align='center'>{$array['porta']}</td>-->
	<td class='row2' align='center'>{$array['conf']['NAME']}</td>
	<td class='row2' align='center'>{$array['conf']['ADMIN_NAME']}</td>
	<td class='row2' >".convert(secure_v($array['conf']['DESCRIZIONE']))."</td>	
	<td class='row2' ><p title='$nota_estesa'>$nota_taglio...</td>		
	<!-- Problemi di layout: <td class='row2' >
		<textarea name='chiave' cols='1' rows='3' readonly class='row2' style='width:100%;border: none;overflow:auto;'>{$array['pkey']}</textarea></td>\n--> 
	</tr>";
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
<table border="0" cellpadding="5" cellspacing="0" width="100%">
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
function html_substr($posttext, $minimum_length = 200, $length_offset = 20, $cut_words = FALSE, $dots = TRUE) 
{
   // $minimum_length:   
   // The approximate length you want the concatenated text to be   
   // $length_offset:   
   // The variation in how long the text can be in this example text    
   // length will be between 200 and 200-20=180 characters and the   // character where the last tag ends
   // Reset tag counter & quote checker   
   $tag_counter = 0;   
   $quotes_on = FALSE;   
   // Check if the text is too long   
   if (strlen($posttext) > $minimum_length) 
   {
		// Reset the tag_counter and pass through (part of) the entire text       
		$c = 0;       
		for ($i = 0; $i < strlen($posttext); $i++) 
		{           
			// Load the current character and the next one           
			// if the string has not arrived at the last character           
			$current_char = substr($posttext,$i,1);           
			if ($i < strlen($posttext) - 1) 
			{
				$next_char = substr($posttext,$i + 1,1);
			}           
			else 
			{               $next_char = "";           }           
			// First check if quotes are on           
			if (!$quotes_on) 
			{
				// Check if it's a tag               
				// On a "<" add 3 if it's an opening tag (like <a href...)               
				// or add only 1 if it's an ending tag (like </a>)               
				if ($current_char == '<') 
				{                   
					if ($next_char == '/') 
					{                       $tag_counter += 1;                   }                   
					else 
					{                       $tag_counter += 3;                   }               
				}               
				// Slash signifies an ending (like </a> or ... />)               
				// substract 2               
				if ($current_char == '/' && $tag_counter <> 0) $tag_counter -= 2;               
				// On a ">" substract 1               
				if ($current_char == '>') $tag_counter -= 1;               
				// If quotes are encountered, start ignoring the tags               
				// (for directory slashes)               
				if ($current_char == '"') $quotes_on = TRUE;           }           
				else 
				{               
					// IF quotes are encountered again, turn it back off               
					if ($current_char == '"') $quotes_on = FALSE;           
				}                      
				// Count only the chars outside html tags           
				if($tag_counter == 2 || $tag_counter == 0){               $c++;           }                // Check if the counter has reached the minimum length yet,           
				// then wait for the tag_counter to become 0, and chop the string there           
				if ($c > $minimum_length - $length_offset && $tag_counter == 0 && ($next_char == ' ' || $cut_words == TRUE)) 
				{               
					$posttext = substr($posttext,0,$i + 1);                             
					if($dots){                   $posttext .= '...';               }               
					return $posttext;           
				}       
			}   
		}     
	return $posttext;
}

//END PAGE SELECT FUNCTION

?>
