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
$lang += $std->load_lang('lang_register', $blanguage );

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


if ( !empty($nick) and !empty($password) and !empty($privkey) ) {	// import the user
        if ( strlen($nick) < 3 or strlen($nick) > 30 ) die("".$lang['reg_nicknotvalid']."");
        if ( strlen($password) < 3 or strlen($password) > 30 ) die("".$lang['reg_passnotvalid']."");
        // ?? Error("Non hai i permessi per registrare un utente su questa board\n<br>") unless ForumLib::PermessiRegistrazione($ENV{sesname});
        // ?? Error("L'Antiflood che controlla le registrazioni effettuate nel sistema ti impedisce di registrare al momento, riprova più tardi\n<br>") unless ForumLib::CanRegisterFlood($ENV{sesname}, time());
        
        $identif = md5( md5($password,TRUE) . $nick );
        $sql_insert = "INSERT INTO $SNAME" . "_localmember (hash, password $optfield) VALUES ('"
                        . $identif . "','" . mysql_real_escape_string($privkey) . "' $optvalue)";
        if ( !$db->query($sql_insert) ) die("".$lang['reg_usererr']."");
        else echo "".$lang['reg_importok']."";
        include ("end.php");
        exit;
}

if ( !empty($nick) and !empty($password) and empty($privkey) ) { // create a new user
	$identif = md5(md5($password,TRUE) . $nick);
	$PKEY64 = $std->getpkey($SNAME);
	$corereq['RSA']['GENKEY']['CONSOLE_OUTPUT'] = 0;
	$corereq['RSA']['GENKEY']['PWD'] = md5($nick . md5($password,TRUE),TRUE);
	$corereq['RSA']['GENKEY']['NICK'] = $nick;
	$corereq['RSA']['GENKEY']['PKEY64'] = $PKEY64;
	// quando invio una richiesta GENKEY dove è presente NICK sto generando una chiave per un utente, il core mi ritorna anche la PKEY in decimale in ['pkeydec']
	// e l'hash del messaggio in ['hash'] così evito di fare richieste/conti dopo
	
	$coresk = new CoreSock;
	if ( !$coresk->Send($corereq) ) die("Errore in send1!");
	$coreresp = $coresk->Read(120);
	if ( !$coreresp ) die("Errore in read1!");
	$rsapub = $coreresp['RSA']['GENKEY']['pub'];		// in decimale
	$rsapriv = $coreresp['RSA']['GENKEY']['priv'];		// in base64
	$PKEY = $coreresp['RSA']['GENKEY']['pkeydec'];		//pkey del forum in decimale
	$date = $coreresp['RSA']['GENKEY']['date'];			// la prendo così perchè deve essere quella usata per creare l'hash
	$hash = $coreresp['RSA']['GENKEY']['hash'];
	
	unset($coreresp,$corereq);
	if ( strlen($PKEY) < 120 ) die("".$lang['reg_keynotvalid']."");
	$corereq['FUNC']['Dec2Bin'] = $rsapub;			// converto la chiave pubblica in binario
	if ( !$coresk->Send($corereq) ) die("Error sending a request to the core!");
	$coreresp = $coresk->Read();
	if ( !$coreresp ) die("Error receiving a response from the core!");
	$rsapub_bin = $coreresp['FUNC']['Dec2Bin'];
	unset($coreresp, $corereq);
	
	//$hash = md5($PKEY . $nick . $date . $rsapub,TRUE);
	$corereq['RSA']['FIRMA'][0]['md5'] = $hash;
	$corereq['RSA']['FIRMA'][0]['priv_key'] = md5($nick . md5($password,TRUE),TRUE);
	$corereq['RSA']['FIRMA'][0]['priv_pwd'] = base64_decode($rsapriv);
	
	if ( !$coresk->Send($corereq) ) die("Errore in send2!");
	$coreresp = $coresk->Read();
	if ( !$coreresp ) die("Errore in read2!");
	
	if ( empty($coreresp['RSA']['FIRMA'][$hash]) ) die($coreresp['RSA']['FIRMA']["ERR" . $hash]);
	$firma_rsa = $coreresp['RSA']['FIRMA'][$hash];
	unset($coreresp,$corereq);
	
	echo "Adding user into the local members table... ";
	$sqladd = "INSERT INTO {$SNAME}_localmember (hash, password $optfield) VALUES ('"
                    . $identif . "','" . mysql_real_escape_string($rsapriv) . "' $optvalue)";
        if ( !$db->query($sqladd) ) die("".$lang['reg_usererr']."");
        else echo "Ok<br><br>";
	
	echo "<br>";
	
	echo "Adding user into the system... ";
	$addreq['FORUM']['ADDMSG']['MD5'] = $hash;
	$addreq['FORUM']['ADDMSG']['AUTORE'] = $nick;
	$addreq['FORUM']['ADDMSG']['DATE'] = $date;
	$addreq['FORUM']['ADDMSG']['PKEY'] = $rsapub_bin;
	$addreq['FORUM']['ADDMSG']['SIGN'] = $firma_rsa;
	$addreq['FORUM']['ADDMSG']['TYPE'] = '4';
	$addreq['FORUM']['ADDMSG']['FDEST'] = pack('H*',sha1($PKEY));
	
	if ( !$coresk->Send($addreq) ) die("Error sending the request to the core!");
	$coreresp = $coresk->Read();
	if ( !$coreresp ) die("Error receiving response form the core!");
	if ( $coreresp['FORUM']['ADDMSG'] == -2 ) die("Forum unknown, cannot register the user.");
	if ( $coreresp['FORUM']['ADDMSG'] == -1 ) die("The Core didn't accept the message, aborting.");
	if ( $coreresp['FORUM']['ADDMSG'] == 1 ) echo "Ok<br><br>";
	include("end.php");
	exit(0);			
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
