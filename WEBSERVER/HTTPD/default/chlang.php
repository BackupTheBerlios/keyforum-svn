<?PHP
 include ("lib/lib.php");

$newurl="http://".$_SERVER['HTTP_HOST'].$_GET['script']."?".$_SERVER['QUERY_STRING'];


$userdata->LANG=$_GET['lang'];
$std->UpdateUserData($_ENV["sesname"],$userdata);

$std->Redirect("change language","$newurl","change language","language changed to {$_GET['lang']}");

?>