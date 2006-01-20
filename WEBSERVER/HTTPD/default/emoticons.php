<script type="text/javascript">
<!--
 function emopopup()
 {
   window.open('emopopup.php','Emoticons','width=770,height=500,resizable=yes,scrollbars=yes'); 
 }
-->
</script>
<?
 $SNAME=$_ENV['sesname'];
 global $db;
  $query="SELECT id,typed,image,internal from {$SNAME}_emoticons WHERE enabled AND clickable";
  $res=$db->get_results($query);
  
  $emocol=$userdata->EMOCOL;
  if(!$emocol) $emocol=4; //Supporto per chi non ha aggiornato il database Da rimuovere
  
  $colcont=0;	
	if($res) foreach($res as $row)
	{
		if ($colcont%$emocol== 0) { echo "<tr>"; }
		$emoadr = ($row->internal ? "showemo.php?id={$row->id}" : "img/emoticons/{$row->image}");
		echo "
		<td>
			<img src='$emoadr' border='0' valign='absmiddle' style='cursor:pointer;' onclick='javascript:emot(\"{$row->typed}\")' alt='{$row->typed}' title='{$row->typed}'>
		</td>";
		$colcont++;
		if ($colcont%$emocol == 0) {echo "</tr>";}
	}	
    
?>
    <tr><td colspan="<?=$emocol?>" align="center"><a href='javascript:emopopup()'><? echo $lang['emo_showallemo']; ?></a></td></tr>
