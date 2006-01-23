<? require_once("lib/lib.php"); ?>

<html>
<HEAD>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=ISO-8859-1">
<title>KeyRing</title>
<link type="text/css" rel="stylesheet" href="style_page.css">
<link rel="shortcut icon" href="favicon.ico">
</HEAD>
<body>

<?
$SNAME=$_ENV['sesname'];

$query="select * from {$SNAME}_localkey";
$result = $db->get_results($query);

if($result)
{
foreach($result as $row)
{
$keycount++;
$key['id'][$keycount]=$row->id;
$key['name'][$keycount]=$row->kname;
$key['value'][$keycount]=$row->kvalue;
}
}
?>

<script language="JavaScript"><!-- Hide

function update(keyid) {

switch (keyid){

<?
// questo accrocco è dovuto ad un bug di IE che non mi permette di passare stringhe troppo lunghe ad una funzione JS
for ($i = 1; $i <= $keycount; $i++) {
echo "case \"{$key['id'][$i]}\":"; 
echo "opener.document.forms[\"{$_REQUEST['rm']}\"].{$_REQUEST['rf']}.value = '{$key['value'][$i]}';";   
echo "break;";
}
?>

}
  
    window.close();
}


// --></script>


<?

echo "<table width='100%' cellspacing='1' align='center'>
<tr>
<th class='darkrow2' align='center'>ID</th>
<th class='darkrow2' align='center'>KEY</th>
</tr>";

for ($i = 1; $i <= $keycount; $i++) {
echo"
<tr>
<td class='row1' align='center'>{$key['id'][$i]}</td>
<td class='row1' align='center'><a href=\"javascript:update('{$key['id'][$i]}');\">{$key['name'][$i]}</a></td>
</tr>
";
}


?>

</table>
</body>
</html>