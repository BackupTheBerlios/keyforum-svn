<?PHP
/****VARIABILI DA NON TOCCARE******

	$ris 		= vettore di tutte le categorie e figli
	$num_figli 	= vettore del numero di figli
	$totmsg		= numero totale di messaggi presenti nel db
*/ 

include_once ("testa.php");
echo "<tr><td>";
// carico la lingua per la index
$lang += $std->load_lang('lang_index', $blanguage );
$whereiam="index";

// lo stato delle categorie salvato nel cookie
$hidesez=explode(",",$std->GetKFcookie("collapseprefs",$SNAME));


$query_last= "
select * from (
	SELECT 
		  {$SNAME}_msghe.hash
		, ({$SNAME}_msghe.last_reply_time + ".GMT_TIME.") as last_reply_time
		, {$SNAME}_msghe.last_reply_author
		, {$SNAME}_newmsg.sez
		, {$SNAME}_newmsg.title
		,{$SNAME}_membri.autore 
	FROM `{$SNAME}_msghe` 
		join {$SNAME}_membri on {$SNAME}_msghe.last_reply_author = {$SNAME}_membri.hash
		join {$SNAME}_newmsg on {$SNAME}_msghe.hash = {$SNAME}_newmsg.edit_of
	WHERE {$SNAME}_newmsg.visibile='1'
	ORDER BY {$SNAME}_msghe.last_reply_time desc
	)as asd
GROUP BY asd.sez";
$result_last = $db->get_results($query_last);
if($result_last)foreach($result_last as $riga)
{
	$last_action[$riga->sez] = Array(
		 'sez_id' => $riga->sez
		,'title' => $riga->title
		,'thr_hash' => $riga->hash
		,'autore' => $riga->autore
		,'autore_hash' => $riga->last_reply_author
		,'date' => $riga->last_reply_time
		);
}

require_once("lib/TreeClass.php");
$tree=new Tree;
$tree->AddNode(" 0","root");

$query = "SELECT id, sez_name, SEZ_DESC, FIGLIO, `MOD`, SEZ_DESC, REPLY_NUM, PKEY,PRKEY,THR_NUM
 from {$_ENV['sesname']}_sez order by FIGLIO,ORDINE ";
$result = $db->get_results($query);

foreach ( $result as $row )
{
$tree->AddNode(" ".$row->id," ".$row->FIGLIO);
$forum[$row->id+0]= Array(
	 'SEZ_ID'	=> $row->id
	,'SEZ_NAME' => $row->sez_name
	,'SEZ_DESC' => $row->SEZ_DESC
	,'MOD' 		=> $row->MOD
	,'PKEY' 	=> $row->PKEY
	,'PRKEY'	=> $row->THR_NUM
	,'REPLY_NUM'=> $row->REPLY_NUM
	,'THR_NUM'	=> $row->THR_NUM
	,'ONLY_AUTH'=> $row->ONLY_AUTH
	,'AUTOFLUSH'=> $row->AUTOFLUSH
	,'ORDINE'	=> $row->ORDINE
	,'FIGLIO' 	=> $row->FIGLIO
	,'last_admin_edit' => $row->last_admin_edit
	,'last_action' => $last_action[$row->id+0]
	,'num_figli' => 0
	);
}
unset($last_action);


$ris=$tree->drawTree();
$num_figli = array_fill(0,count($ris),0);
numfigli($ris,2,3); //NON MI CHIEDETE PERCHE' 2 e 3
$i=0;
foreach($num_figli as $id => $numero)
{
	$i++;
	$id = (int)$ris[$id]['id'];
	if($forum[$id])	$forum[$id]['num_figli'] = $numero;
	$lev = $ris[$i]['lev'];
	$id  = (int)$ris[$i]['id'];
	$level=$lev-4;
	$forum[$id]['level'] = $level;
}



for($i=0;$i<=count($ris);$i++) 
{
	$lev = $ris[$i]['lev'];
	$id  = (int)$ris[$i]['id'];
	$level=$lev-4;
	if ($level == 0)
	{
		draw_forum($forum[$id],$i);
	}
	$totmsg += $forum[$id]['REPLY_NUM'] + $forum[$id]['THR_NUM'];
}

include('end.php');





//FUNZIONE DI VISUALIZZAZIONE
function draw_forum($sez,$indice)
{
	global $ris,$forum,$lang,$db,$SNAME,$hidesez,$sezcollector,$std;
	switch($sez[level])
	{
		case 0:
		$divshow = ( in_array($sez['SEZ_ID'],$hidesez) ? 'none' : 'show');
		$divhide = ( in_array($sez['SEZ_ID'],$hidesez) ? 'show' : 'none');
		$sezcollector .= $sez['SEZ_ID'].",";
		echo "
		<div class='borderwrap' style='display:$divhide' id='divhide_{$sez['SEZ_ID']}'>
		 <div class='maintitlecollapse'>
		  <p class='expand'><a href=\"javascript:ShowHideSection({$sez['SEZ_ID']}, 0,'$SNAME');\">
		  <img src='img/exp_plus.gif' border='0'  alt='Expand' /></a></p>
		  <p><a href='sezioni.php?SEZID={$sez['SEZ_ID']}'>{$sez['SEZ_NAME']}</a></p>
 		 </div>
		</div>
		
		<div class='borderwrap' style='display:$divshow' id='divshow_{$sez['SEZ_ID']}'>
		 <div class='maintitle' >
		  <p class='expand'><a href=\"javascript:ShowHideSection({$sez['SEZ_ID']}, 1,'$SNAME');\">
		  <img src='img/exp_minus.gif' border='0' alt='Collapse' /></a></p>
		  <p><a href='sezioni.php?SEZID={$sez['SEZ_ID']}'>{$sez['SEZ_NAME']}</a></p>
  		</div>
  <table cellspacing=\"1\">
    <tr>
      <th align=\"left\" width=\"40%\" colspan=\"2\" class='titlemedium'>".$lang['col_forum']."</th>
      <th align=\"center\" width=\"1%\" class='titlemedium'>".$lang['col_topic']."</th>
      <th align=\"center\" width=\"1%\" class='titlemedium'>".$lang['col_replies']."</th>
      <th align=\"left\" width=\"39%\" class='titlemedium'>".$lang['col_lastpost']."</th>
    </tr>";
		for($i=0;$i<$sez['num_figli']; $i++)
		{
			$next_id = $ris[$indice+$i+1];
			$next_id = (int) $next_id['id'];
					
			draw_forum($forum[$next_id],$indice+1+$i);
			$num_sottofigli = sottofigli($forum[$next_id],$indice+1);
			$indice = $indice+$num_sottofigli;
		}
		echo "<tr><td class='darkrow2' colspan='5'>&nbsp;</td></tr></table></div><br>";
		break;
		
		case 1:
			//Default value
			$notfirst=0;
			$subsections="";
			for($i=0;$i<$sez['num_figli'];$i++)
			{
				$next_id = $ris[$indice+$i+1];
				$next_id = (int) $next_id['id'];
			
				if($notfirst)
    		     $subsections .= ", <b><a href='sezioni.php?SEZID={$forum[$next_id]['SEZ_ID']}'>".secure_v($forum[$next_id]['SEZ_NAME'])."</a></b>";
		        else
		          $subsections="<br><i>".$lang['subforums']."</i><b><a href='sezioni.php?SEZID={$forum[$next_id]['SEZ_ID']}'>".secure_v($forum[$next_id][SEZ_NAME])."</a></b>";
		       $notfirst=1;
			}
			//Ultimo messaggio
			$sez['last_action'] = last_action($sez,$indice);
			//Numero messaggi
			list($num_sotto_reply,$num_sotto_thr) = get_reply_thr($sez,$indice);
			$sez['REPLY_NUM'] = $num_sotto_reply ;
			$sez['THR_NUM'] = $num_sotto_thr;

			
			$write_date=$std->PostDate($sez['last_action']['date']);
			$hash = @unpack("H32alfa",$sez['last_action']['thr_hash']);
			$nickhash= @unpack("H32alfa",$sez['last_action']['autore_hash']);
			if(strlen($sez['last_action']['title'])>50)
			{
				$msg=substr($sez['last_action']['title'], 0, 50)."...";
			}
			else
			{
				$msg=$sez['last_action']['title'];
			}
			$moderators=$std->ListMod($sez[MOD]);

			echo "
			<tr>
			<td class='row4' width='5%' align='center'><img src='img/bf_new.gif' alt=''></td>
			<td class='row4'><b><a href='sezioni.php?SEZID={$sez['SEZ_ID']}'>{$sez['SEZ_NAME']}</a></b><br /><span class='desc'>{$sez['SEZ_DESC']} $subsections <br /><font color='#808080'><i>{$lang['col_moderators']}: $moderators</i></font><br /></span></td>".'
			<td class="row2" align="center">'.$sez['THR_NUM'].'</td>
			<td class="row2" align="center">'.$sez['REPLY_NUM'].'</td>
			<td class="row2" nowrap="nowrap">'.$lang['last_in'].'<a href="showmsg.php?SEZID='.$sez['last_action']['sez_id'].'&amp;THR_ID='.$hash['alfa'].'&amp;pag=last#end_page">'.secure_v($msg).'</a><br>'.$lang['last_data'].$write_date.'<br>'.$lang['last_from'].'<a href="showmember.php?MEM_ID='.$nickhash['alfa'].'">'.secure_v($sez['last_action']['autore']).'</a></td>';
		break;
		default:
		break;
	}
}
//FUNZIONE NUMERO DI FIGLI

function numfigli($a,$i,$j)
{
	global $num_figli;
	
	$tot = count($a);
	while($j<=$tot)
	{
		if($a[$j]['lev'] == $a[$i]['lev']+1)
		{
			$num_figli[$i]++;
			$j++;
		}
		else if($a[$j]['lev'] < $a[$i]['lev']+1)
		{
			return $j;
		}
		else if($a[$j]['lev'] > $a[$i]['lev']+1)
		{
			$j = numfigli($a,$j-1,$j);
		}
	}
}
function last_action($current,$indice)
{
	global $forum,$ris;
	$max = $current['last_action']['date'];
	$return = $current['last_action'];
	
	for($i=0;$i<$current['num_figli'];$i++)
	{
		$next_id = $ris[$indice+$i+1];
		$next_id = (int) $next_id['id'];
		$tmp = last_action($forum[$next_id],$indice+$i+1);
		if($tmp['date'] > $max)
		{
			$max = $tmp['date'];
			$return = $tmp;
		}
	}
	return $return;
}


function get_reply_thr($current,$indice)
{
	global $forum,$ris;
	
	$rep = $current['REPLY_NUM'];
	$thr = $current['THR_NUM'];
	
	for($i=0;$i<$current['num_figli'];$i++)
	{
		$next_id = $ris[$indice+$i+1];
		$next_id = (int) $next_id['id'];
		list($sotto_rep,$sotto_thr) = get_reply_thr($forum[$next_id],$indice+$i+1);
		$rep += $sotto_rep;
		$thr += $sotto_thr;
	}
	$return = Array($rep,$thr);
	return $return;
}
function sottofigli($sez,$indice)
{
	global $ris,$forum;
	$result=0;
	//echo "<br>asdasd su {$sez[SEZ_NAME]}:  ";
	for($i=0;$i<$sez['num_figli'];$i++)
	{
		$next_id = $ris[$indice+$i+1];
		$next_id = (int) $next_id['id'];
		//echo "il prossimo id è $next_id";
		$result += sottofigli($forum[$next_id],$indice+1);
	}
	//echo "result vale $result a cui aggiungo {$sez['num_figli']}";
	$result += $sez['num_figli'];
	return $result;
}
?>

