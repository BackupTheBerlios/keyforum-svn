<?PHP
/****VARIABILI DA NON TOCCARE******

	$ris 		= vettore di tutte le categorie e figli
	$num_figli 	= vettore del numero di figli
	$totmsg		= numero totale di messaggi presenti nel db
*/ 

include ("testa.php");
echo "<tr><td>";
// carico la lingua per la index
$lang += $std->load_lang('lang_index', $blanguage );
$whereiam="index";

// lo stato delle categorie salvato nel cookie
$hidesez=explode(",",$std->GetKFcookie("collapseprefs",$SNAME));


$query_last= "
select * from (
	SELECT 
		  keyfo_msghe.hash
		, (keyfo_msghe.last_reply_time + ".GMT_TIME.") as last_reply_time
		, keyfo_msghe.last_reply_author
		, keyfo_newmsg.sez
		, keyfo_newmsg.title
		,keyfo_membri.autore 
	FROM `keyfo_msghe` 
		join keyfo_membri on keyfo_msghe.last_reply_author = keyfo_membri.hash
		join keyfo_newmsg on keyfo_msghe.hash = keyfo_newmsg.edit_of
	WHERE keyfo_newmsg.visibile='1'
	ORDER BY keyfo_msghe.last_reply_time desc
	)as asd
GROUP BY asd.sez";
$result_last = $db->get_results($query_last);
foreach($result_last as $riga)
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

$query = "SELECT id, sez_name, SEZ_DESC, figlio, `MOD`, SEZ_DESC, REPLY_NUM, PKEY,PRKEY,THR_NUM
 from {$_ENV['sesname']}_sez order by figlio,ordine ";
$result = $db->get_results($query);

foreach ( $result as $row )
{
$tree->AddNode(" ".$row->id," ".$row->figlio);
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

foreach($num_figli as $id => $numero)
{
	$id = (int)$ris[$id]['id'];
	$forum[$id]['num_figli'] = $numero;
}




for($i=0;$i<=count($ris);$i++) 
{
	$lev = $ris[$i]['lev'];
	$id  = (int)$ris[$i]['id'];
	$level=$lev-4;  
	if ($level == 0)
	{
		$sez_id = $id;
		draw_forum($forum[$sez_id],0,$i);
	}
	$totmsg += $forum[$id]['REPLY_NUM'] + $forum[$id]['THR_NUM'];
}


include('end.php');





//FUNZIONE DI VISUALIZZAZIONE
function draw_forum($sez, $level,$indice)
{
	global $ris,$forum,$lang,$db,$SNAME,$hidesez,$sezcollector,$std;
	switch($level)
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
		for($i=0;$i <$sez['num_figli'] ; $i++)
		{
			$next_id = $ris[$indice+$i+1];
			$next_id = (int) $next_id['id'];
			draw_forum($forum[$next_id],$level+1,$indice+1+$i);
		}
		echo "<tr><td class='darkrow2' colspan='5'>&nbsp;</td></tr></table></div><br>";
		break;
		
		case 1:
			//Default value
			$notfirst=0;
			$subsections="";
			
			$last_action_id = $sez['SEZ_ID'];
			$last_action_max = $sez['last_action']['date'];
			for($i=0;$i<$sez['num_figli'];$i++)
			{
				$next_id = $ris[$indice+$i+1];
				$next_id = (int) $next_id['id'];

				//ultima azione
				if($forum[$next_id]['last_action']['date'] > $last_action_max)
				{
					$last_action_max = $forum[$next_id]['last_action']['date'];
					$last_action_id  = $next_id;
				}
				//numero discussioni e risposte
				$num_sotto_reply += $forum[$next_id]['REPLY_NUM'];
				$num_sotto_thr += $forum[$next_id]['THR_NUM'];
				
				if($notfirst)
    		     $subsections .= ", <b><a href='sezioni.php?SEZID={$forum[$next_id]['SEZ_ID']}'>".secure_v($forum[$next_id]['SEZ_NAME'])."</a></b>";
		        else
		          $subsections="<br><i>".$lang['subforums']."</i><b><a href='sezioni.php?SEZID={$forum[$next_id]['SEZ_ID']}'>".secure_v($forum[$next_id][SEZ_NAME])."</a></b>";
		       $notfirst=1;
			}
			$sez['REPLY_NUM'] += $num_sotto_reply ;
			$sez['THR_NUM'] += $num_sotto_thr;
			if($last_action_id != $sez['SEZ_ID'])
			{
				$sez['last_action'] = $forum[$last_action_id]['last_action'];
			}
			
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

?>

