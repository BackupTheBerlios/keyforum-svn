<?php
require_once("lib/lib.php");

$id=$_GET['id'];
$SNAME=$_ENV['sesname'];
$query = "Select binimage,binimagetype FROM `{$SNAME}_emoticons` where id=$id";
$result = @mysql_query($query);
list($image,$extn) = @mysql_fetch_row($result);

show_db_image($image,$extn);


function show_db_image($image, $extn)
{
header('Content-Control: cache');
header('Content-Type: image/'. date('r', time()+86400)); // expire in 24H
header('Content-Type: image/'.$extn);
echo base64_decode($image);
}
?>