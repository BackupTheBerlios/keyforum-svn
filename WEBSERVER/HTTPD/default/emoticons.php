<?
 $SNAME=$_ENV['sesname'];
  $query="SELECT id,typed,image,internal from {$SNAME}_emoticons WHERE enabled AND clickable";
  $res=mysql_query($query) or die(mysql_error());
  
  $emocol=4; // numero colonne emoticon

  $colcont=0;	
   while ($row = mysql_fetch_assoc($res))
     {
      if (!$colcont) { echo "<tr>"; }
      if ($row['internal']) {$emoadr="showemo.php?id={$row['id']}";} else {$emoadr="img/emoticons/{$row['image']}";}
    echo "<td><img src='$emoadr' border='0' valign='absmiddle' style='cursor:hand;cursor:pointer;' onclick='javascript:emot(\"{$row['typed']}\")' alt='{$row['typed']}'></td>";
    $colcont++;
    if ($colcont == $emocol) {echo "</tr>";$colcont=0;}
    }	
    
?>
    <tr><td colspan="4" align="center"><a href='javascript:' onclick='emopopup()'>Mostra tutte</a></td></tr>
