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

$nick = $_REQUEST['nick'];
$password = $_REQUEST['password'];
$privkey = $_REQUEST['privkey'];

// valori opzionali
$rlang = $_REQUEST['lang'];
$rtpp = $_REQUEST['TPP'];
$rppp = $_REQUEST['PPP'];

if($rlang) {$optfield .= ",lang"; $optvalue .= ",'$rlang'";}
if($rtpp) {$optfield .= ",tpp"; $optvalue .= ",'$rtpp'";}
if($rppp) {$optfield .= ",ppp"; $optvalue .= ",'$rppp'";}


if ( isset($nick) and isset($password) and isset($privkey) ) {
        $PKEY=$std->getpkey($SNAME);
        if ( strlen($PKEY) < 120 ) die("".$lang['reg_keynotvalid']."");
        if ( strlen($nick) < 3 or strlen($nick) > 30 ) die("".$lang['reg_nicknotvalid']."");
        if ( strlen($password) < 3 or strlen($password) > 30 ) die("".$lang['reg_passnotvalid']."");
        // ?? Error("Non hai i permessi per registrare un utente su questa board\n<br>") unless ForumLib::PermessiRegistrazione($ENV{sesname});
        // ?? Error("L'Antiflood che controlla le registrazioni effettuate nel sistema ti impedisce di registrare al momento, riprova più tardi\n<br>") unless ForumLib::CanRegisterFlood($ENV{sesname}, time());
        
        $identif = md5( md5($password,TRUE) . $nick );
        $sql_insert = "INSERT INTO $SNAME" . "_localmember (hash, password $optfield) VALUES ('"
                        . $identif . "','" . mysql_real_escape_string($privkey) . "' $optvalue)";
        if ( !mysql_query($sql_insert) ) die("".$lang['reg_usererr']."");
        else echo "".$lang['reg_importok']."";
        include ("end.php");
        exit;
}


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
	echo "".$lang['reg_nofile']."";
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
$userpwd=$userdata['root']['USERDATA']['PWD'];
$usertpp=$userdata['root']['USERDATA']['TPP'];
$userppp=$userdata['root']['USERDATA']['PPP'];
$userlang=$userdata['root']['USERDATA']['LANG'];



}

?>


<tr><td>

<form method=post action="register.php">
<table align=center width=550>
<tr>
<? echo" <td class=row3 colspan=\"2\" align=center><b>".$lang['reg_info3']."</td>";?>
</tr>
<tr>
 <td class=row1><? echo $lang['reg_nick']; ?></td>
	<td class=row2><input type=text value="<? echo $usernick; ?>" name=nick></td>
</tr>
<tr>
	<td class=row1><? echo $lang['reg_password']; ?></td>
	<td class=row2><input type=password value="<? echo $userpwd; ?>"  name=password></td>
</tr>
<tr>
  <td class="row1" colspan="2" align="center">
  <input type=submit value="<? echo $lang['reg_submit']; ?>" ><br>
  </td>
</tr>
<tr>
  <td class="row3" colspan="2" align="center">
<? echo"    ".$lang['reg_info4']."</td>";?>
</tr>
<tr>
  <td class="row1" colspan="2" align="center">
    <?php echo $lang['reg_privkey'];?>
  </td>
</tr>
<tr>
  <td class="row2" colspan="2" align="center">
    <textarea cols="60" rows="5" name="privkey" wrap="virtual"><? echo $userkey; ?></textarea>
  </td>
</tr>
<tr>
  <td class="row3" colspan="2" align="center">
<? echo"    ".$lang['reg_info5']."</td>";?>
</tr>
<tr>
  <td class="row2" colspan="2" align="center">
<? echo"    ".$lang['reg_language']."";?>
    
    <? $langselect[$userlang]="selected"; ?>
    
    <select size="1" name="lang">
    <optgroup label='Language selection'>
    <option <? echo $langselect['eng']; ?> value="eng">English</option>
    <option <? echo $langselect['ita']; ?> value="ita">Italiano</option>
    </select>
    
    <br>
<? echo"   ".$lang['reg_tpp']."";?><input type="text" name="TPP" value="<? echo $usertpp; ?>"  size="4"><? echo"".$lang['reg_ppp']."";?> 
    <input type="text" name="PPP" value="<? echo $userppp; ?>"  size="4"></td>
</tr>
<tr>
  <td class="row1" colspan="2" align="center">
  <input type=submit value="<? echo $lang['reg_submit']; ?>" ><br>
  </td>
</tr>
</table>
<center>
</center>
</form>

<center>
<form action="<?php echo $_SERVER['PHP_SELF'];?>?submit=1" method="post" enctype="multipart/form-data">
<input type="hidden" name="MAX_FILE_SIZE" value="100000">
<table align=center width=550>
<tr><td align="center"> 
<br>
<? echo"<p><b>".$lang['reg_importinfo']."</b></p>";?>
<p>&nbsp;<? echo "   ".$lang['reg_import']; ?>
<input name="userfile" type="file"><br>
<input type="submit" value="<? echo $lang['reg_submit'] ?>">
</p>
  </td></tr>
</table> 
</form>
</center>




<?php echo $lang['reg_info1']."<br>".$lang['reg_info2']; ?>

</td></tr>
<?PHP
include ("end.php");
?>
