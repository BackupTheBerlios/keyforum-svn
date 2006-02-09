<?php
require_once("lib/lib.php");

$id=$_GET['id'];
$query = "Select binimage,binimagetype FROM `{$SNAME}_emoticons` where id=$id";
$emo = $db->get_row($query);

show_db_image($emo->binimage,$emo->binimagetype);


function show_db_image($image, $extn)
{
header('Content-Control: cache');
header('Content-Type: image/'. date('r', time()+86400)); // expire in 24H
header('Content-Type: image/'.$extn);
echo $image;
}
?>