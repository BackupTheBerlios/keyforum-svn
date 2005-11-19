<?php


//classe PEAR per file config (XML)
require_once "Config.php";
$xmldata = new Config;


$whereiam="register";
ob_start('ob_gzhandler'); 
include ("testa.php");

// classe PEAR per gli UPLOAD
require 'HTTP/Upload.php';

// carico la lingua per la registrazione
$lang = $std->load_lang('lang_register', $blanguage );

?>

<?

if (isset($submit)) {

// echo '<pre>';
$upload = new http_upload('it');
$file = $upload->getFiles('userfile');
if (PEAR::isError($file)) {
	die ($file->getMessage());
}
if ($file->isValid()) {
	$file->setName('uniq');
	$dest_dir = './uploads/';
	$dest_name = $file->moveTo($dest_dir);
	if (PEAR::isError($dest_name)) {
		die ($dest_name->getMessage());
	}
	$real = $file->getProp('real');
	// echo "Uploaded $real as $dest_name in $dest_dir\n";
} elseif ($file->isMissing()) {
	echo "No file selected\n";
} elseif ($file->isError()) {
	echo $file->errorMsg() . "\n";
}
//print_r($file->getProp());
//echo '</pre>';

// leggo il file


$root =& $xmldata->parseConfig('uploads/'.$file->getProp('name'), 'XML');
if (PEAR::isError($root)) {
    die('Error reading XML config file: ' . $root->getMessage());
}

$userdata = $root->toArray();

$userkey=$userdata['root']['USERDATA']['KEY'];
$usernick=$userdata['root']['USERDATA']['NICK'];


}

?>


<tr><td>
<form method=post action="registra.pl">
<table align=center width=350>
<tr>
	<?php echo "<td class=row1>".$lang['reg_nick']."</td>";?>
	<td class=row2><input type=text value="<? echo $usernick; ?>" name=nick></td>
</tr>
<tr>
	<?php echo "<td class=row1>".$lang['reg_password']."</td>";?>
	<td class=row2><input type=text name=password></td>
</tr>
<tr>
  <td class="row1" colspan="2" align="center">
    <?php echo "<input type=submit value=\" ".$lang['reg_submit']." \"><br>";?>
  </td>
</tr>
<tr>
  <td class="row1" colspan="2" align="center">
    <?php echo $lang['reg_privkey'];?>
  </td>
</tr>
<tr>
  <td class="row2" colspan="2" align="center">
    <textarea cols="50" rows="5" name="pkey" wrap="virtual"><? echo $userkey; ?></textarea>
  </td>
</tr>
</table>
<center>

</center>
</form>

<center>
<br>
<form action="<?php echo $_SERVER['PHP_SELF'];?>?submit=1" method="post" enctype="multipart/form-data">
<?php echo "   ".$lang['reg_import']." <input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"100000\"> "; ?>
   
   <input name="userfile" type="file"><br>
   <input type="submit" value="Invia">
</form>
</center>




<?php echo $lang['reg_info1']."<br>".$lang['reg_info2']; ?>

</td></tr>
<?PHP
include ("end.php");
?>
