<? require_once("lib/lib.php");  ?>

<html> 
 <head> 
  <meta http-equiv="content-type" content="text/html; charset=iso-8859-1" /> 
  <title>Faccine</title>
  <link type="text/css" rel="stylesheet" href="style_page.css" />
     </head>
     
      <body topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0">

         
       <script language='javascript'>
      <!--      	
      		function emot(st) {
			obj_body = opener.document.forms[2].elements['body'];
			lastchar = obj_body.value.charAt(obj_body.value.length-1);
			if (lastchar == ' ' || lastchar == '\n' || obj_body.value.length==0)
				obj_body.value = obj_body.value + st + ' ';
			else
				obj_body.value = obj_body.value + ' ' + st + ' ';
			obj_body.focus();
	}      	
      //-->
 </script>

<?

  $query="SELECT id,typed,image,internal from {$SNAME}_emoticons WHERE enabled";
  $res = $db->get_results($query);
  


    $emocol=3; // numero colonne emoticon

    $totemo = $db->num_rows;
    $scarto=($totemo * 2) % $emocol;
    

    $colpercentile=50/$emocol;

    echo "<table cellspacing='1' cellpadding='4'>";	
    for ($i = 1; $i <= $emocol; $i++) 
    	{
        echo "<td width='$colpercentile%' align='center' class='pformstrip' valign='middle'>Before</td>";
        echo "<td width='$colpercentile%' align='center' class='pformstrip' valign='middle'>After</td>";
	}
    
    $colcont=0;	
    $coltot=0;
    foreach($res  as $row)
    {
    if (!$colcont) { echo "<tr>"; }
    $emoadr = ($row->internal ? "showemo.php?id={$row->id}" : "img/emoticons/{$row->image}");
    
    echo "<td align='center' class='row1' valign='middle'>
		<a href='javascript:emot(\"{$row->typed}\")'>{$row->typed}</a></td>";
    echo "<td align='center' class='row2' valign='middle'>
		<img src='$emoadr' border='0' valign='absmiddle' style='cursor:pointer;' onclick='javascript:emot(\"{$row->typed}\")' alt='{$row->typed}' title='{$row->typed}'></td>";
    $colcont++;
    if ($colcont == $emocol) {echo "</tr>";$colcont=0;}
    }	

   if($scarto)
   {
       for ($i = 1; $i <= $scarto; $i++){echo "<td class='row1' >&nbsp;</td><td class='row2' >&nbsp;</td>";}
       echo "<tr>";
   }

    
    echo "</table>";

?>
   </body>
</html>