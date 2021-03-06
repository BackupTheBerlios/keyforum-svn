<?php
$whereiam="register";
//classe PEAR per file config (XML)
require_once "Config.php";
$xmldata = new Config;
// classe PEAR per gli UPLOAD
require 'HTTP/Upload.php';
include("testa.php");



// carico la lingua per la registrazione
$lang += $std->load_lang('lang_register', $blanguage );

//POSTBACK

//ACQUISIZIONE DATI
$forum_id = pack('H*',$config[SHARE][$SNAME][ID]);

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

//CONTROLLO I DATI
if(!$forum_id) $std->Error('Non riesco a trovare il forum');
if(!empty($nick) and !empty($password) and !empty($privkey) ) 	$import_user=1;
if(!empty($nick) and !empty($password) and empty($privkey) )	$create_user=1; 
if ($import_user) 
{	
	// BEGIN IMPORT
    if ( strlen($nick) < 3 or strlen($nick) > 30 ) $std->Error("".$lang['reg_nicknotvalid']."");
    if ( strlen($password) < 3 or strlen($password) > 30 ) $std->Error("".$lang['reg_passnotvalid']."");
        
        $identif = md5( pack('H*',md5($password)) . $nick );
        $sql_insert = "INSERT INTO $SNAME" . "_localmember (hash, password $optfield) VALUES ('"
                        . $identif . "','" . mysql_real_escape_string($privkey) . "' $optvalue)";
        if ( !$db->query($sql_insert) ) $std->Error("".$lang['reg_usererr']."");
        else $std->Error("","",$lang['reg_importok'].$lang['reg_import_import']);
}
//END IMPORT
if ($create_user) 
{   
	// BEGIN CREATE

	$identif = md5(pack('H*',md5($password)) . $nick);
	$password= pack('H*',md5($nick . pack('H*',md5($password))));
	
	$core = new CoreSock;
	$chiavi=$core->GenRsaKey($password,1);
	//if($privkey) $chiavi[priv] = $privkey; //Prendo la chiave privata dal form se presente altrimenti prendo quella appena generata

	echo "Adding user into the system... <br>";
	$risp = $core->NewUser($nick,$chiavi[pub],base64_decode($chiavi[priv]),$password);
	if ( !empty($risp['ERRORE']) ) 
	{
		//NON TRADURRE: Si prevede una gestione migliore degli errori del core :)
		switch($risp['ERRORE'])
		{
			case 11: 
				$std->Error("L'username deve avere una lunghezza compresa tra 4 e 30 caratteri");
			break;
			case 28: 
				$std->Error("Niente messaggi nel futuro");
			break;
			default:
				$std->Error("Errore generico in fase di registrazione");
			breaK;
		}
	} else "Ok <br><br>\n";
	
	//add user hash returned from the core (md5) into the private key
	if ( empty($risp['MD5']) ) $std->Error("Core didn't return TRUEMD5, aborting!");
	$truemd5=$risp['MD5'];
	//var_dump($risp);
	// echo "TRUEMD5: " . $truemd5 . "<br><br>";
		
	// dedump private key....
	$req[FUNC][BlowDump2var][Data]=base64_decode($chiavi[priv]);
	$req[FUNC][BlowDump2var][Key]=$password;
	if ( !$core->Send($req) ) $std->Error("Timeout sending data  to the core, aborting.");
	$resp=$core->Read();
	$newprivkey=$resp[FUNC][BlowDump2var];
	if ( !$newprivkey ) $std->Error("Error receiving data from the core, aborting.");
	
	//....add user hash....
	$newprivkey[hash]=$truemd5;
	// var_dump($newprivkey);
	// ...dump the new private key....we'll get it in base64...
	//unset($req);
	$req2[FUNC][var2BlowDump64][Key]=$password;
	$req2[FUNC][var2BlowDump64][Data]=$newprivkey;
	//var_dump($req2);
	//echo "<br>FATTO REQ2<br><br>";
	if ( !$core->Send($req2) ) $std->Error("Timeout sending data to the core, aborting.");
	$resp2=$core->Read();
	//var_dump($resp2);
	//echo "FATTO RESP2<br><br>";
	if ( !$resp2 ) $std->Error("Error receiving data from the core, aborting.");
	$finalpkey64=$resp2[FUNC][var2BlowDump64];
	
	//var_dump($finalpkey64);
	
	echo "Adding user into the local members table... ";
	$sqladd = "
		INSERT INTO {$SNAME}_localmember 
			(hash, password $optfield) VALUES 
			('$identif','" . mysql_real_escape_string($finalpkey64) . "' $optvalue)";
        if ( !$db->query($sqladd) ) $std->Error("".$lang['reg_usererr']."");
        else echo "Done...<br><br>";
	
	echo "<br><br>";
	$std->Error("","","<b>IMPORTANTISSIMO:</b> devi salvare il file con l'utente generato e metterlo 
	in un posto sicuro, ti servir� in futuro se vorrai reinstallare keyforum e 
	loggarti con lo stesso utente. Per salvare il file <a href=\"userexport.php\">fai 
	click qui</a></p><br>");
	echo "<br><br>";
	include("end.php");
	exit(0);	
	//END CREATE		
}


if (isset($submit)) {

// echo '<pre>';
$upload = new http_upload('it');
$file = $upload->getFiles('userfile');
if (PEAR::isError($file)) {
	$std->Error ($file->getMessage());
}
if ($file->isValid()) {
	$file->setName('uniq');
	$dest_dir = './uploads/';
	$dest_name = $file->moveTo($dest_dir);
	if (PEAR::isError($dest_name)) {
		$std->Error ($dest_name->getMessage());
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
    $std->Error('Error reading XML config file: ' . $root->getMessage());
}

$userdata = $root->toArray();

$userkey=$userdata['root']['USERDATA']['KEY'];
$usernick=$userdata['root']['USERDATA']['NICK'];
$userpwd=$userdata['root']['USERDATA']['PWD'];
$usertpp=$userdata['root']['USERDATA']['TPP'];
$userppp=$userdata['root']['USERDATA']['PPP'];
$userlang=$userdata['root']['USERDATA']['LANG'];

}

// *** javascript per controlli formali *** DA TRADURRE !!!
echo "
<script type=\"text/javascript\">
<!--
  
function Validator(theForm)
{

// nickname must be between 3 and 30 characters long
if (theForm.nick.value.length < 3 || theForm.nick.value.length >30 )
   {
alert(\"nickname must be between 3 and 30 characters long\");
theForm.nick.focus();
return (false);
   }

// password must be between 3 and 30 characters long
if (theForm.password.value.length < 3 || theForm.password.value.length >30 )
   {
alert(\"password must be between 3 and 30 characters long\");
theForm.password.focus();
return (false);
   }

// password must match exactly
if (theForm.password.value != theForm.password2.value )
   {
alert(\"passwords must match exactly\");
theForm.password.focus();
return (false);
   }
}  
  
//-->
</script>
";

?>

<tr><td>

<form method=post action="" onsubmit="return Validator(this)">
<table align=center width=550>
<tr>
<td colspan=2>
<div class='borderwrap'>
 <div class='maintitle' align=center>
   INSERT NEW USER
 </div>
</div>
</td>
</tr>
<tr>
<? echo" <td class=row3 colspan=\"2\" align=center><font color=blue><b>".$lang['reg_info3']."</b></font></td>";?>
</tr>
<tr>
 <td class=row1><? echo $lang['reg_nick']; ?></td>
	<td class=row2><input type=text size=35 value="<? echo $usernick; ?>" name=nick></td>
</tr>
<tr>
	<td class=row1><? echo $lang['reg_password']; ?></td>
	<td class=row2><input type=password size=35 value="<? echo $userpwd; ?>"  name=password></td>
</tr>
<tr>
	<td width=250 class=row1><? echo $lang['reg_password2']; ?></td>
	<td  class=row2><input type=password size=35 value="<? echo $userpwd; ?>"  name=password2></td>
</tr>
<tr>
  <td class="row1" colspan="2" align="center">
  <input class=button type='submit' name='action' value="<? echo $lang['reg_submit']; ?>" ><br>
  </td>
</tr>
<tr>
  <td class="row3" colspan="2" align="center">
<? echo"<font color=blue>".$lang['reg_info4']."</font></td>";?>
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
	</optgroup>
    </select>
    
    <br>
<? echo"   ".$lang['reg_tpp']."";?><input type="text" name="TPP" value="<? echo $usertpp; ?>"  size="4"><? echo"".$lang['reg_ppp']."";?> 
    <input type="text" name="PPP" value="<? echo $userppp; ?>"  size="4"></td>
</tr>
<tr>
  <td class="row1" colspan="2" align="center">
  <input class=button type=submit value="<? echo $lang['reg_submit']; ?>" ><br>
  </td>
</tr>
</table>


<center>
</center>
</form>

<center>
<form action="" method="post" enctype="multipart/form-data">
<input type="hidden" name="submit" value="1" />
<input type="hidden" name="MAX_FILE_SIZE" value="100000">
<table align=center width=550>
<tr><td align="center"> 
<div class='borderwrap'>
<font color=blue><b>IMPORT USER FROM XML FILE</b></font>
<? echo"<b>".$lang['reg_importinfo']."</b>";?>
<p>&nbsp;<? echo "   ".$lang['reg_import']; ?>
<input class=button name="userfile" type="file">
<input class=button type="submit" value="<? echo $lang['reg_submit'] ?>">
</p>
</div>
  </td></tr>
</table> 
</form>
</center>




<?

echo "<br>
<table align=center width=80%>
<tr>
<td>
{$lang['reg_info1']}<br>{$lang['reg_info2']} 
</td>
</tr>
</table></br>";

?>

</td></tr>
<?PHP
include ("end.php");
?>
