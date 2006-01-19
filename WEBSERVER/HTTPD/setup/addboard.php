<?
require_once('ez_sql.php');
include ("core.php");
$core=NEW CoreSock;


//inizializzo il db
//classe PEAR per file config (XML)
require_once "Config.php";
$xmldata = new Config;

// classe PEAR per gli UPLOAD
require 'HTTP/Upload.php';

$root =& $xmldata->parseConfig('http://'.$_SERVER['HTTP_HOST'].'/config/config.xml', 'XML');
if (PEAR::isError($root)) {
    die('Error reading XML config file: ' . $root->getMessage());
}

$settings = $root->toArray();

// dati del db
$_ENV['sql_host']=$settings['root']['conf']['DB']['host'];
$_ENV['sql_user']=$settings['root']['conf']['DB']['dbuser'];
$_ENV['sql_passwd']=$settings['root']['conf']['DB']['dbpassword'];
$_ENV['sql_dbname']=$settings['root']['conf']['DB']['dbname'];
$_ENV['sql_dbport']=$settings['root']['conf']['DB']['dbport'];

if(!$_ENV['sql_dbport']){$_ENV['sql_dbport']="3306";}
$db = new db($_ENV['sql_user'], $_ENV['sql_passwd'], $_ENV['sql_dbname'],$_ENV['sql_host'].":".$_ENV['sql_dbport']);


// nascondo gli errori mysql
$db->hide_errors();

// occhio, la chiave va trasformata da decimale...

//layout iniziale
$data=array();

if (!$submit AND !$import)
{
layout($data);
die();
}


// lettura file

$submit=$_REQUEST['submit'];

if (isset($submit)) {

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

// leggo il file

$root =& $xmldata->parseConfig('uploads/'.$file->getProp('name'), 'XML');
if (PEAR::isError($root)) {
    die('Error reading XML config file: ' . $root->getMessage());
}

$data = $root->toArray();

layout($data);

die();

}


if (isset($import)) {


// ******* DATI FORUM **********

$postdata['bsession']=$_REQUEST['bsession'];
$postdata['pkey']=$_REQUEST['pkey'];
$postdata['bid']=$_REQUEST['bid'];
$postdata['bind']=$_REQUEST['bind'];
$postdata['bport']=$_REQUEST['bport'];

// pkey da decimale a base64
$req['FUNC']['Dec2Base64']=$postdata['pkey'];
if (!$core->Send($req)) die ("Errore mentre si tentava di comunicare con il core: ".$core->errmsg);
if (!($risp=$core->Read(6))) die ("Errore mentre si tentava di comunicare con il core: ".$core->errmsg);
$postdata['pkey']=$risp['FUNC']['Dec2Base64'];

CreateDb($postdata);

// sovrascrivo chkdir.bat in modo da forzare una autoconfigurazione al prossimo avvio

$curdir = getcwd();
list ($phpdir, $installdir) = spliti ('\\\WEBSERVER\\\HTTPD\\\setup', $curdir);
$apachedir = ereg_replace ("\\\\","/",$phpdir);

$filename = "$apachedir/COMMON/script/chkdir.bat";
$chkdir= "@echo off
ECHO DIRECTORY CHECK
IF EXIST \"$phpdir\WEBSERVER\Apache\conf\fakefile.null\" GOTO fine
ECHO KEYFORUM NEEDS CONFIGURATION
ECHO;
pause
install_keyforum.bat
:fine
ECHO OK
ECHO;
";
$handle = fopen($filename, 'w');
fwrite($handle, $chkdir);
fclose($handle);


echo "<CENTER><b><H3>Board {$postdata['bsession']} importata ! </H3></b>";
echo "<br><H3>l'indirizzo per raggiungerla è http://127.0.0.1:{$postdata['bport']}</H3><br>";
echo "<font color=red><b><H3>Ricorda che per attivarla occorre riavviare KeyForum...</H3></b></font><br><br></center>";

layout();

die();

}


function FindFreePort()
{

$host="127.0.0.1";
$timeout= 0.5;
echo "<center>finding free port... wait ...";
for ($port = 20585; $port <= 65000; $port++) {
    
    settype($ports[$i], "integer");
    	if (($handle = @fsockopen($host, $port, $errno, $errstr, $timeout)) == false)
    	{
 	return $port;
    	} else { 
    	echo "$port  ";
    	flush();
    	}
   
}
 echo "</center>";	

}


// layout
function layout($data=array())
{

$portalibera=FindFreePort();

echo  "

<html>

<head>
<meta http-equiv=\"Content-Language\" content=\"it\">
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=windows-1252\">
<title>IMPORTA NUOVA BOARD</title>
<link type=\"text/css\" rel=\"stylesheet\" href=\"style_page.css\">
</head>

<style>

</style>

<body>
<br>
<div align=\"center\">
	<table border=\"0\" width=\"500\" id=\"table1\">
		<tr>
			<th class='row1'>
			<font face=\"Verdana\"><b>IMPORTA NUOVA BOARD</b></font></th>
		</tr>
		<tr>
			<td class='row1' >
            <table cellSpacing=\"0\" cellPadding=\"0\" width=\"100%\" align=\"center\" border=\"0\" id=\"table3\">
              <tr>
                <td><center>
                <form method=\"post\" encType=\"multipart/form-data\" action=\"addboard.php?submit=1\">
                  <table width=\"550\" align=\"center\" id=\"table4\">
                    <tr>
                      <td align=\"middle\">&nbsp; <b>carica file XML:</b>
                      <input type=\"file\" name=\"userfile\"><br>
                      <input type=\"hidden\" name=\"file\" value=1>
                      <input type=\"submit\" value=\"Importa\"> </td>
                    </tr>
                  </table>
                </form>
                </center></td>
              </tr>
            </table>
            <p align=\"center\"><br>
            <b>Dati modificabili dall'utente</b></p>
            <form method=\"POST\" action=\"addboard.php?import=1\">
				
				<table border=\"0\" width=\"100%\" id=\"table2\">
					<tr>
						<td class='row1' width=\"27%\"><b>
						<font size=\"2\">nome/sessione</font></b></td>
						<td class='row1' width=\"71%\"><input type=\"text\" name=\"bsession\" value=\"{$data['root']['BOARD']['SESSION']}\" size=\"20\"></td>
					</tr>
					<tr>
						<td class='row3' colspan=\"2\">nome della 
						board e identificativo per le tabelle del database, 
                        inserire un nome alfanumerico senza spazi o lasciare 
                        quello proposto di default (scelta consigliata)</td>
					</tr>
					<tr>
						<td class='row1' width=\"27%\"><b><font face=\"Verdana\" size=\"2\">porta</font></b></td>
						<td class='row1' width=\"71%\"><input type=\"text\" name=\"bport\" value=\"$portalibera\" size=\"20\"></td>
					</tr>
					<tr>
						<td class='row3' width=\"98%\" colspan=\"2\">
						inserisci un valore &gt; 
						10.000, o mantieni quella proposta di default (scelta 
						consigliata)</td>
					</tr>
					<tr>
						<td class='row1' width=\"27%\"><b><font face=\"Verdana\" size=\"2\">bind</font></b></td>
						<td class='row1' width=\"71%\"><select size=\"1\" name=\"bind\">
						<option selected value=\"127.0.0.1\">127.0.0.1</option>
						<option value=\"*\">*</option>
						</select></td>
					</tr>
					<tr>
						<td class='row3' width=\"98%\" colspan=\"2\">
						lascia 127.0.0.1 se non 
						vuoi che il forum sia accessibile dall'esterno, oppure * se 
						invece vuoi che altre persone possano navigare il forum 
						usando il tuo server web.<br>
						Questo valore non influenza lo scambio messaggi tra i 
						client. </td>
					</tr>
					<tr>
						<td class='row1' width=\"98%\" colspan=\"2\">
                        <p align=\"center\"><br>
                        <b>Dati della board <br>
                        (forniti dall'amministratore e non modificabili)<br>
&nbsp;</b></td>
					</tr>
					<tr>
						<td class='row1' width=\"27%\"><b><font face=\"Verdana\" size=\"2\">id</font></b></td>
						<td class='row1' width=\"71%\"><input type=\"text\" name=\"bid\" value=\"{$data['root']['BOARD']['ID']}\" size=\"49\"></td>
					</tr>
					<tr>
						<td class='row3' width=\"98%\" colspan=\"2\">
						identificatore univoco 
						della board (esadecimale)</td>
					</tr>
					<tr>
						<td class='row1' width=\"27%\"><b><font size=\"2\">chiave pubblica</font></b></td>
						<td class='row1' width=\"71%\">
						<textarea rows=\"6\" name=\"pkey\" cols=\"41\">{$data['root']['BOARD']['PKEY']}</textarea></td>
					</tr>
					<tr>
						<td class='row3' width=\"98%\" colspan=\"2\">la chiave 
						pubblica della board (decimale)</td>
					</tr>
					<tr>
						<td class='row1' width=\"27%\" height=\"16\"></td>
						<td class='row1' width=\"71%\" height=\"16\"></td>
					</tr>
				</table>
				<p align=\"center\"><input type=\"submit\" value=\"Conferma\" name=\"B1\"></p>
			</form>
			</td>
		</tr>
	</table>
</div>

</body>

</html>
";

}


function CreateDb($data)
{
global $db;

$bsess=$data['bsession'];
$pkey=$data['pkey'];
$bid=$data['bid'];
$bbind=$data['bind'];
$bport=$data['bport'];

$db->query("insert  into config values 
('SHARE', '$bsess', 'PKEY', '$pkey'), 
('SHARE', '$bsess', 'ID', '$bid'), 
('WEBSERVER', '$bsess', 'BIND', '$bbind'), 
('WEBSERVER', '$bsess', 'SesName', '$bsess'), 
('WEBSERVER', '$bsess', 'GROUP', 'generic'), 
('WEBSERVER', '$bsess', 'DIRECTORY', 'default'), 
('WEBSERVER', '$bsess', 'PORTA', '$bport');");
$db->query("CREATE TABLE `{$bsess}_admin` (
  `HASH` binary(16) NOT NULL,
  `TITLE` tinytext NOT NULL,
  `BODY` mediumtext NOT NULL,
  `DATE` int(10) unsigned NOT NULL default '0',
  `SIGN` tinyblob NOT NULL,
  PRIMARY KEY  (`HASH`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;");
$db->query("CREATE TABLE `{$bsess}_conf` (
  `GROUP` varchar(100) NOT NULL default '',
  `FKEY` varchar(100) NOT NULL default '',
  `SUBKEY` varchar(100) NOT NULL default '',
  `VALUE` mediumtext NOT NULL,
  `present` tinyint(3) unsigned NOT NULL default '1',
  `date` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`GROUP`,`FKEY`,`SUBKEY`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;");
$db->query("CREATE TABLE `{$bsess}_congi` (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `HASH` binary(16) NOT NULL,
  `TYPE` tinyint(3) unsigned NOT NULL,
  `WRITE_DATE` int(10) unsigned NOT NULL,
  `CAN_SEND` enum('0','1') NOT NULL default '1',
  `INSTIME` int(10) unsigned NOT NULL default '0',
  `AUTORE` binary(16) NOT NULL,
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `HASH` (`HASH`),
  KEY `AUTORE` (`AUTORE`),
  KEY `WRITE_DATE` (`WRITE_DATE`),
  KEY `INSTIME` (`INSTIME`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED COLLATE=latin1_general_ci AUTO_INCREMENT=1;");
$db->query("CREATE TABLE `{$bsess}_emoticons` (
  `id` smallint(3) NOT NULL auto_increment,
  `typed` varchar(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `image` varchar(128) default NULL,
  `binimage` blob,
  `binimagetype` varchar(4) default NULL,
  `internal` tinyint(1) NOT NULL default '0',
  `clickable` tinyint(1) NOT NULL default '0',
  `enabled` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=248;");
$db->query("insert  into {$bsess}_emoticons values 
(1, ':mellow:', 'mellow.gif', null, 'gif', 0, 1, 1), 
(2, ':huh:', 'huh.gif', null, 'gif', 0, 1, 1), 
(3, '^_^', 'happy.gif', null, 'gif', 0, 1, 1), 
(4, ':o', 'ohmy.gif', null, 'gif', 0, 1, 1), 
(5, ';)', 'wink.gif', null, 'gif', 0, 1, 1), 
(6, ':P', 'tongue.gif', null, 'gif', 0, 1, 1), 
(7, ':D', 'biggrin.gif', null, 'gif', 0, 1, 1), 
(8, ':lol2:', 'laugh.gif', null, 'gif', 0, 1, 1), 
(9, 'B-)', 'cool.gif', null, 'gif', 0, 1, 1), 
(10, ':rolleyes:', 'rolleyes.gif', null, 'gif', 0, 1, 1), 
(11, '-_-', 'sleep.gif', null, 'gif', 0, 1, 1), 
(12, '&lt;_&lt;', 'dry.gif', null, 'gif', 0, 1, 1), 
(13, ':)', 'smile.gif', null, 'gif', 0, 1, 1), 
(14, ':wub:', 'wub.gif', null, 'gif', 0, 1, 1), 
(15, ':mad:', 'mad.gif', null, 'gif', 0, 1, 1), 
(16, ':(', 'sad.gif', null, 'gif', 0, 1, 1), 
(17, ':unsure:', 'unsure.gif', null, 'gif', 0, 1, 1), 
(18, ':wacko:', 'wacko.gif', null, 'gif', 0, 1, 1), 
(19, ':blink:', 'blink.gif', null, 'gif', 0, 1, 1), 
(20, ':ph34r:', 'ph34r.gif', null, 'gif', 0, 1, 1), 
(21, ':ambulance:', 'ambulance.gif', null, 'gif', 0, 0, 1), 
(22, ':angel:', 'angel.gif', null, 'gif', 0, 0, 1), 
(23, ':applause:', 'applause.gif', null, 'gif', 0, 0, 1), 
(24, ':artist:', 'artist.gif', null, 'gif', 0, 0, 1), 
(25, ':baby:', 'baby.gif', null, 'gif', 0, 0, 1), 
(26, ':bag:', 'bag.gif', null, 'gif', 0, 0, 1), 
(27, ':band:', 'band.gif', null, 'gif', 0, 0, 1), 
(28, ':banned:', 'banned.gif', null, 'gif', 0, 0, 1), 
(29, ':beer:', 'beer.gif', null, 'gif', 0, 0, 1), 
(30, ':beer2:', 'beer2.gif', null, 'gif', 0, 0, 1), 
(31, ':blowup:', 'blowup.gif', null, 'gif', 0, 0, 1), 
(32, ':boat:', 'boat.gif', null, 'gif', 0, 0, 1), 
(33, ':book:', 'book.gif', null, 'gif', 0, 0, 1), 
(34, ':bow:', 'bow.gif', null, 'gif', 0, 0, 1), 
(35, ':boxe:', 'boxe.gif', null, 'gif', 0, 0, 1), 
(36, ':boxing:', 'boxing.gif', null, 'gif', 0, 0, 1), 
(37, ':canadian:', 'canadian.gif', null, 'gif', 0, 0, 1), 
(38, ':censored:', 'censored.gif', null, 'gif', 0, 0, 1), 
(39, ':chair:', 'chair.gif', null, 'gif', 0, 0, 1), 
(40, ':chef:', 'chef.gif', null, 'gif', 0, 0, 1), 
(41, ':cool2:', 'cool2.gif', null, 'gif', 0, 0, 1), 
(42, ':cowboy:', 'cowboy.gif', null, 'gif', 0, 0, 1), 
(43, ':crutch:', 'crutch.gif', null, 'gif', 0, 0, 1), 
(44, ':cry:', 'cry.gif', null, 'gif', 0, 0, 1), 
(45, ':death:', 'death.gif', null, 'gif', 0, 0, 1), 
(46, ':devil:', 'devil.gif', null, 'gif', 0, 0, 1), 
(47, ':dj:', 'dj.gif', null, 'gif', 0, 0, 1), 
(48, ':drunk:', 'drunk.gif', null, 'gif', 0, 0, 1), 
(49, ':eat:', 'eat.gif', null, 'gif', 0, 0, 1), 
(50, ':farewell:', 'farewell.gif', null, 'gif', 0, 0, 1), 
(51, ':gathering:', 'gathering.gif', null, 'gif', 0, 0, 1), 
(52, ':ghost:', 'ghost.gif', null, 'gif', 0, 0, 1), 
(53, ':gossip:', 'gossip.gif', null, 'gif', 0, 0, 1), 
(54, ':graduate:', 'graduate.gif', null, 'gif', 0, 1, 1), 
(55, ':guillotine:', 'guillotine.gif', null, 'gif', 0, 0, 1), 
(56, ':guitar:', 'guitar.gif', null, 'gif', 0, 0, 1), 
(57, ':gunbandana:', 'gunbandana.gif', null, 'gif', 0, 0, 1), 
(58, ':hammerer:', 'hammerer.gif', null, 'gif', 0, 0, 1), 
(59, ':happybday:', 'happybday.gif', null, 'gif', 0, 0, 1), 
(60, ':help:', 'help.gif', null, 'gif', 0, 0, 1), 
(61, ':hmm:', 'hmm.gif', null, 'gif', 0, 0, 1), 
(62, ':hoover:', 'hoover.gif', null, 'gif', 0, 0, 1), 
(63, ':horse:', 'horse.gif', null, 'gif', 0, 0, 1), 
(64, ':king:', 'king.gif', null, 'gif', 0, 0, 1), 
(65, ':kiss:', 'kiss.gif', null, 'gif', 0, 0, 1), 
(66, ':kiss2:', 'kiss2.gif', null, 'gif', 0, 0, 1), 
(67, ':laughing:', 'laughing.gif', null, 'gif', 0, 0, 1), 
(68, ':love:', 'love.gif', null, 'gif', 0, 0, 1), 
(69, ':mad2:', 'mad2.gif', null, 'gif', 0, 0, 1), 
(70, ':mobile:', 'mobile.gif', null, 'gif', 0, 0, 1), 
(71, ':nono:', 'nono.gif', null, 'gif', 0, 0, 1), 
(72, ':nugget:', 'nugget.gif', null, 'gif', 0, 0, 1), 
(73, ':phone:', 'phone.gif', null, 'gif', 0, 0, 1), 
(74, ':photo:', 'photo.gif', null, 'gif', 0, 0, 1), 
(75, ':pizza:', 'pizza.gif', null, 'gif', 0, 0, 1), 
(76, ':punk:', 'punk.gif', null, 'gif', 0, 0, 1), 
(77, ':ranting:', 'ranting.gif', null, 'gif', 0, 0, 1), 
(78, ':rotfl:', 'rotfl.gif', null, 'gif', 0, 1, 1), 
(79, ':runaway:', 'runaway.gif', null, 'gif', 0, 0, 1), 
(80, ':sbav:', 'sbav.gif', null, 'gif', 0, 0, 1), 
(81, ':sbav2:', 'sbav2.gif', null, 'gif', 0, 0, 1), 
(82, ':scared:', 'scared.gif', null, 'gif', 0, 0, 1), 
(83, ':scooter:', 'scooter.gif', null, 'gif', 0, 0, 1), 
(84, ':secret:', 'secret.gif', null, 'gif', 0, 0, 1), 
(85, ':serenade:', 'serenade.gif', null, 'gif', 0, 0, 1), 
(86, ':shifty:', 'shifty.gif', null, 'gif', 0, 0, 1), 
(87, ':shock:', 'shock.gif', null, 'gif', 0, 0, 1), 
(88, ':sign-ban:', 'sign-ban.gif', null, 'gif', 0, 0, 1), 
(89, ':sign-dots:', 'sign-dots.gif', null, 'gif', 0, 0, 1), 
(90, ':sign-offtopic:', 'sign-offtopic.gif', null, 'gif', 0, 0, 1), 
(91, ':sign-spam:', 'sign-spam.gif', null, 'gif', 0, 0, 1), 
(92, ':sign-stupid:', 'sign-stupid.gif', null, 'gif', 0, 0, 1), 
(93, ':sleeping:', 'sleeping.gif', null, 'gif', 0, 0, 1), 
(94, ':starwars:', 'starwars.gif', null, 'gif', 0, 0, 1), 
(95, ':surrender:', 'surrender.gif', null, 'gif', 0, 0, 1), 
(96, ':terafin-grin:', 'terafin-grin.gif', null, 'gif', 0, 0, 1), 
(97, ':thumbdown:', 'thumbdown.gif', null, 'gif', 0, 0, 1), 
(98, ':thumbup:', 'thumbup.gif', null, 'gif', 0, 0, 1), 
(99, ':tomato:', 'tomato.gif', null, 'gif', 0, 0, 1), 
(100, ':tongue2:', 'tongue2.gif', null, 'gif', 0, 1, 1), 
(101, ':tooth:', 'tooth.gif', null, 'gif', 0, 0, 1), 
(102, ':tv:', 'tv.gif', null, 'gif', 0, 0, 1), 
(103, ':uh:', 'uh.gif', null, 'gif', 0, 0, 1), 
(104, ':wallbash:', 'wallbash.gif', null, 'gif', 0, 0, 1), 
(105, ':whistling:', 'whistling.gif', null, 'gif', 0, 0, 1), 
(106, ':wine:', 'wine.gif', null, 'gif', 0, 0, 1), 
(107, ':worthy:', 'worthy.gif', null, 'gif', 0, 0, 1), 
(108, ':wub2:', 'wub2.gif', null, 'gif', 0, 0, 1), 
(109, ':xmas:', 'xmas.gif', null, 'gif', 0, 0, 1), 
(110, ':yeahright:', 'yeahright.gif', null, 'gif', 0, 0, 1), 
(111, ':yes:', 'yes.gif', null, 'gif', 0, 0, 1), 
(112, ':adminpower:', 'adminpower.gif', null, 'gif', 0, 0, 1), 
(113, ':afro:', 'afro.gif', null, 'gif', 0, 0, 1), 
(114, ':angry:', 'angry.gif', null, 'gif', 0, 0, 1), 
(115, ':apple:', 'apple.gif', null, 'gif', 0, 0, 1), 
(116, ':argue:', 'argue.gif', null, 'gif', 0, 0, 1), 
(117, ':arrow:', 'arrow.gif', null, 'gif', 0, 0, 1), 
(118, ':asd:', 'asd.gif', null, 'gif', 0, 0, 1), 
(119, ':baboso:', 'baboso.gif', null, 'gif', 0, 0, 1), 
(120, ':badmood:', 'badmood.gif', null, 'gif', 0, 0, 1), 
(121, ':ban:', 'ban.gif', null, 'gif', 0, 0, 1), 
(122, ':banana:', 'banana.gif', null, 'gif', 0, 0, 1), 
(123, ':bastardinside:', 'bastardinside.gif', null, 'gif', 0, 0, 1), 
(124, ':beg:', 'beg.gif', null, 'gif', 0, 0, 1), 
(125, ':biggrin-santa:', 'biggrin-santa.gif', null, 'gif', 0, 0, 1), 
(126, ':biggrin2:', 'biggrin2.gif', null, 'gif', 0, 0, 1), 
(127, ':bleh:', 'bleh.gif', null, 'gif', 0, 0, 1), 
(128, ':blow:', 'blow.gif', null, 'gif', 0, 0, 1), 
(129, ':blush:', 'blush.gif', null, 'gif', 0, 0, 1), 
(130, ':blush2:', 'blush2.gif', null, 'gif', 0, 0, 1), 
(131, ':bond:', 'bond.gif', null, 'gif', 0, 0, 1), 
(132, ':bounce:', 'bounce.gif', null, 'gif', 0, 0, 1), 
(133, ':bustedcop:', 'bustedcop.gif', null, 'gif', 0, 0, 1), 
(134, ':bye:', 'bye.gif', null, 'gif', 0, 0, 1), 
(135, ':cheers:', 'cheers.gif', null, 'gif', 0, 0, 1), 
(136, ':cheese:', 'cheese.gif', null, 'gif', 0, 0, 1), 
(137, ':clap:', 'clap.gif', null, 'gif', 0, 0, 1), 
(138, ':closedeyes:', 'closedeyes.gif', null, 'gif', 0, 0, 1), 
(139, ':cold:', 'cold.gif', null, 'gif', 0, 0, 1), 
(140, ':console:', 'console.gif', null, 'gif', 0, 0, 1), 
(141, ':crackegg:', 'crackegg.gif', null, 'gif', 0, 0, 1), 
(142, ':crazy-santa:', 'crazy-santa.gif', null, 'gif', 0, 0, 1), 
(143, ':crybaby:', 'crybaby.gif', null, 'gif', 0, 0, 1), 
(144, ':cupid:', 'cupid.gif', null, 'gif', 0, 0, 1), 
(145, ':dance:', 'dance.gif', null, 'gif', 0, 0, 1), 
(146, ':dead:', 'dead.gif', null, 'gif', 0, 0, 1), 
(147, ':director:', 'director.gif', null, 'gif', 0, 0, 1), 
(148, ':doctor:', 'doctor.gif', null, 'gif', 0, 0, 1), 
(149, ':dribble:', 'dribble.gif', null, 'gif', 0, 0, 1), 
(150, ':drive:', 'drive.gif', null, 'gif', 0, 0, 1), 
(151, ':edonkey:', 'edonkey.gif', null, 'gif', 0, 0, 1), 
(152, ':evil:', 'evil.gif', null, 'gif', 0, 0, 1), 
(153, ':excl:', 'excl.gif', null, 'gif', 0, 0, 1), 
(154, ':fear:', 'fear.gif', null, 'gif', 0, 0, 1), 
(155, ':fight:', 'fight.gif', null, 'gif', 0, 0, 1), 
(156, ':flirt:', 'flirt.gif', null, 'gif', 0, 0, 1), 
(157, ':flower:', 'flower.gif', null, 'gif', 0, 0, 1), 
(158, ':flush:', 'flush.gif', null, 'gif', 0, 0, 1), 
(159, ':folle:', 'folle.gif', null, 'gif', 0, 0, 1), 
(160, ':fuckyou:', 'fuckyou.gif', null, 'gif', 0, 0, 1), 
(161, ':giggle:', 'giggle.gif', null, 'gif', 0, 0, 1), 
(162, ':glare:', 'glare.gif', null, 'gif', 0, 0, 1), 
(163, ':gogo:', 'gogo.gif', null, 'gif', 0, 0, 1), 
(164, ':group:', 'group.gif', null, 'gif', 0, 0, 1), 
(165, ':gun:', 'gun.gif', null, 'gif', 0, 0, 1), 
(166, ':haha:', 'haha.gif', null, 'gif', 0, 0, 1), 
(167, ':clap2:', 'clap2.gif', null, 'gif', 0, 0, 1), 
(168, ':harp:', 'harp.gif', null, 'gif', 0, 0, 1), 
(169, ':hello:', 'hello.gif', null, 'gif', 0, 0, 1), 
(170, ':hysterical:', 'hysterical.gif', null, 'gif', 0, 0, 1), 
(171, ':idea:', 'idea.gif', null, 'gif', 0, 0, 1), 
(172, ':injured:', 'injured.gif', null, 'gif', 0, 0, 1), 
(173, ':italy:', 'italy.gif', null, 'gif', 0, 0, 1), 
(174, ':jason:', 'jason.gif', null, 'gif', 0, 0, 1), 
(175, ':jawdrop:', 'jawdrop.gif', null, 'gif', 0, 0, 1), 
(176, ':jumpon:', 'jumpon.gif', null, 'gif', 0, 0, 1), 
(177, ':kicking:', 'kicking.gif', null, 'gif', 0, 0, 1), 
(178, ':kisskiss:', 'kisskiss.gif', null, 'gif', 0, 0, 1), 
(179, ':kissme-santa:', 'kissme-santa.gif', null, 'gif', 0, 0, 1), 
(180, ':laser:', 'laser.gif', null, 'gif', 0, 0, 1), 
(181, ':letto:', 'letto.gif', null, 'gif', 0, 0, 1), 
(182, ':linguaccia:', 'linguaccia.gif', null, 'gif', 0, 0, 1), 
(183, ':linux:', 'linux.gif', null, 'gif', 0, 0, 1), 
(184, ':lock:', 'lock.gif', null, 'gif', 0, 0, 1), 
(185, ':lol:', 'lol.gif', null, 'gif', 0, 0, 1), 
(186, ':lollone:', 'lollone.gif', null, 'gif', 0, 0, 1), 
(187, ':loveh:', 'loveh.gif', null, 'gif', 0, 0, 1), 
(188, ':macosx:', 'macosx.gif', null, 'gif', 0, 0, 1), 
(189, ':megalol:', 'megalol.gif', null, 'gif', 0, 0, 1), 
(190, ':mitico:', 'mitico.gif', null, 'gif', 0, 0, 1), 
(191, ':muletto:', 'muletto.gif', null, 'gif', 0, 0, 1), 
(192, ':napoleon:', 'napoleon.gif', null, 'gif', 0, 0, 1), 
(193, ':ninja:', 'ninja.gif', null, 'gif', 0, 0, 1), 
(194, ':nono2:', 'nono2.gif', null, 'gif', 0, 0, 1), 
(195, ':nyanya:', 'nyanya.gif', null, 'gif', 0, 0, 1), 
(196, ':ola:', 'ola.gif', null, 'gif', 0, 0, 1), 
(197, ':oops:', 'oops.gif', null, 'gif', 0, 0, 1), 
(198, ':pcthrow:', 'pcthrow.gif', null, 'gif', 0, 0, 1), 
(199, ':pcwhack:', 'pcwhack.gif', null, 'gif', 0, 0, 1), 
(200, ':pirate:', 'pirate.gif', null, 'gif', 0, 0, 1), 
(201, ':plane:', 'plane.gif', null, 'gif', 0, 0, 1), 
(202, ':please:', 'please.gif', null, 'gif', 0, 0, 1), 
(203, ':popcorn:', 'popcorn.gif', null, 'gif', 0, 0, 1), 
(204, ':pope:', 'pope.gif', null, 'gif', 0, 0, 1), 
(205, ':poppe:', 'poppe.gif', null, 'gif', 0, 0, 1), 
(206, ':protest:', 'protest.gif', null, 'gif', 0, 0, 1), 
(207, ':ranting2:', 'ranting2.gif', null, 'gif', 0, 0, 1), 
(208, ':rocket:', 'rocket.gif', null, 'gif', 0, 0, 1), 
(209, ':rofl:', 'rofl.gif', null, 'gif', 0, 0, 1), 
(210, ':saacrede:', 'saacrede.gif', null, 'gif', 0, 0, 1), 
(211, ':sadbye:', 'sadbye.gif', null, 'gif', 0, 0, 1), 
(212, ':scratch:', 'scratch.gif', null, 'gif', 0, 0, 1), 
(213, ':scream:', 'scream.gif', null, 'gif', 0, 0, 1), 
(214, ':senzaundente:', 'senzaundente.gif', null, 'gif', 0, 0, 1), 
(215, ':shark:', 'shark.gif', null, 'gif', 0, 0, 1), 
(216, ':shit:', 'shit.gif', null, 'gif', 0, 0, 1), 
(217, ':shrug:', 'shrug.gif', null, 'gif', 0, 0, 1), 
(218, ':smoke:', 'smoke.gif', null, 'gif', 0, 0, 1), 
(219, ':snack:', 'snack.gif', null, 'gif', 0, 0, 1), 
(220, ':sofa:', 'sofa.gif', null, 'gif', 0, 0, 1), 
(221, ':sorry:', 'sorry.gif', null, 'gif', 0, 0, 1), 
(222, ':spacecraft:', 'spacecraft.gif', null, 'gif', 0, 0, 1), 
(223, ':spam:', 'spam.gif', null, 'gif', 0, 0, 1), 
(224, ':spank:', 'spank.gif', null, 'gif', 0, 0, 1), 
(225, ':startrek:', 'startrek.gif', null, 'gif', 0, 0, 1), 
(226, ':stopspam:', 'stopspam.gif', null, 'gif', 0, 0, 1), 
(227, ':stretcher:', 'stretcher.gif', null, 'gif', 0, 0, 1), 
(228, ':sweatdrop:', 'sweatdrop.gif', null, 'gif', 0, 0, 1), 
(229, ':sweatdrop2:', 'sweatdrop2.gif', null, 'gif', 0, 0, 1), 
(230, ':swordfight:', 'swordfight.gif', null, 'gif', 0, 0, 1), 
(231, ':tease:', 'tease.gif', null, 'gif', 0, 0, 1), 
(232, ':think:', 'think.gif', null, 'gif', 0, 1, 1), 
(233, ':triste:', 'triste.gif', null, 'gif', 0, 0, 1), 
(234, ':tvhappy:', 'tvhappy.gif', null, 'gif', 0, 0, 1), 
(235, ':type:', 'type.gif', null, 'gif', 0, 0, 1), 
(236, ':urinal:', 'urinal.gif', null, 'gif', 0, 0, 1), 
(237, ':village:', 'village.gif', null, 'gif', 0, 0, 1), 
(238, ':vomit:', 'vomit.gif', null, 'gif', 0, 0, 1), 
(239, ':war:', 'war.gif', null, 'gif', 0, 0, 1), 
(240, ':welcome:', 'welcome.gif', null, 'gif', 0, 0, 1), 
(241, ':wheelchair:', 'wheelchair.gif', null, 'gif', 0, 0, 1), 
(242, ':whip:', 'whip.gif', null, 'gif', 0, 0, 1), 
(243, ':windows:', 'windows.gif', null, 'gif', 0, 0, 1), 
(244, ':worthy2:', 'worthy2.gif', null, 'gif', 0, 0, 1), 
(245, ':yeah:', 'yeah.gif', null, 'gif', 0, 0, 1), 
(246, ':zao:', 'zao.gif', null, 'gif', 0, 0, 1), 
(247, ':zzz:', 'zzz.gif', null, 'gif', 0, 0, 1);");
$db->query("CREATE TABLE `{$bsess}_extdati` (
  `HASH` binary(16) NOT NULL,
  `AUTORE` binary(16) NOT NULL,
  `DATE` int(10) unsigned NOT NULL,
  `TITLE` tinytext NOT NULL,
  `BODY` blob NOT NULL,
  `SIGN` tinyblob NOT NULL,
  PRIMARY KEY  (`HASH`),
  KEY `DATE` (`DATE`),
  KEY `AUTORE` (`AUTORE`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;");
$db->query("CREATE TABLE `{$bsess}_localkey` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `kname` varchar(30) collate latin1_general_ci NOT NULL,
  `kvalue` text collate latin1_general_ci NOT NULL,
  `ktype` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1;");
$db->query("CREATE TABLE `{$bsess}_localmember` (
  `HASH` char(32) character set latin1 collate latin1_general_ci NOT NULL,
  `PASSWORD` mediumtext character set latin1 collate latin1_general_ci NOT NULL,
  `LANG` char(3) NOT NULL default 'eng',
  `TPP` smallint(6) NOT NULL default '20',
  `PPP` smallint(6) NOT NULL default '10',
  `HIDESIG` tinyint(1) NOT NULL default '0',
  `LASTREAD` int(10) NOT NULL default '0',
  `LEVEL` tinyint(2) unsigned NOT NULL default '0',
  `IS_AUTH` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`HASH`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;");
$db->query("CREATE TABLE `{$bsess}_membri` (
  `HASH` binary(16) NOT NULL,
  `AUTORE` varchar(30) NOT NULL default '',
  `DATE` int(10) unsigned NOT NULL,
  `AUTH` tinyblob NOT NULL,
  `SIGN` tinyblob NOT NULL,
  `is_auth` enum('0','1') NOT NULL default '0',
  `firma` text NOT NULL,
  `avatar` blob NOT NULL,
  `title` varchar(100) NOT NULL default '',
  `email` varchar(100) NOT NULL default '',
  `nascita` varchar(20) NOT NULL default '',
  `provenienza` varchar(70) NOT NULL default '',
  `ban` int(10) unsigned NOT NULL default '0',
  `present` enum('0','1') NOT NULL default '1',
  `msg_num` int(10) unsigned NOT NULL default '0',
  `tot_msg_num` int(10) unsigned NOT NULL default '0',
  `edit_avatar` int(10) unsigned NOT NULL,
  `edit_dati` int(10) unsigned NOT NULL default '0',
  `edit_adminset` int(10) unsigned NOT NULL default '0',
  `EXTRA` blob NOT NULL,
  `PKEYDEC` text NOT NULL,
  `PKEYMD5` binary(16) NOT NULL,
  PRIMARY KEY  (`HASH`),
  KEY `PKEYMD5` (`PKEYMD5`),
  KEY `is_auth` (`is_auth`),
  KEY `DATE` (`DATE`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;");
$db->query("CREATE TABLE `{$bsess}_msghe` (
  `HASH` binary(16) NOT NULL,
  `last_reply_time` int(10) unsigned NOT NULL,
  `last_reply_author` binary(16) NOT NULL,
  `reply_num` int(10) unsigned NOT NULL default '0',
  `DATE` int(10) unsigned NOT NULL,
  `AUTORE` binary(16) NOT NULL,
  `read_num` int(10) unsigned NOT NULL default '0',
  `block_date` int(10) unsigned NOT NULL default '0',
  `pinned` enum('0','1') NOT NULL default '0',
  `last_admin_update` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`HASH`),
  KEY `last_reply_time` (`last_reply_time`),
  KEY `AUTORE` (`AUTORE`),
  KEY `last_reply_author` (`last_reply_author`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED;");
$db->query("CREATE TABLE `{$bsess}_newmsg` (
  `HASH` binary(16) NOT NULL,
  `SEZ` int(8) unsigned NOT NULL default '0',
  `visibile` enum('0','1') NOT NULL default '1',
  `IS_EDIT` int(10) unsigned NOT NULL default '0',
  `AUTORE` binary(16) NOT NULL,
  `EDIT_OF` binary(16) NOT NULL,
  `DATE` int(10) unsigned NOT NULL default '0',
  `TITLE` tinytext NOT NULL,
  `SUBTITLE` tinytext NOT NULL,
  `BODY` mediumtext NOT NULL,
  `SIGN` tinyblob NOT NULL,
  `FOR_SIGN` tinyblob NOT NULL,
  `ADMIN_SIGN` tinyblob NOT NULL,
  `EXTVAR` blob NOT NULL,
  PRIMARY KEY  (`HASH`),
  KEY `EDIT_OF` (`EDIT_OF`),
  KEY `DATE` (`DATE`),
  KEY `AUTORE` (`AUTORE`),
  KEY `SEZ` (`SEZ`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;");
$db->query("CREATE TABLE `{$bsess}_priority` (
  `HASH` binary(16) NOT NULL,
  `PRIOR` int(10) NOT NULL default '0',
  PRIMARY KEY  (`HASH`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;");
$db->query("CREATE TABLE `{$bsess}_purgatorio` (
  `HASH` binary(16) NOT NULL,
  `TYPE` enum('1','2','3','4') collate latin1_general_ci NOT NULL,
  `DELETE_DATE` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`HASH`),
  KEY `DELETE_DATE` (`DELETE_DATE`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;");
$db->query("CREATE TABLE `{$bsess}_reply` (
  `HASH` binary(16) NOT NULL,
  `REP_OF` binary(16) NOT NULL,
  `AUTORE` binary(16) NOT NULL,
  `IS_EDIT` enum('0','1') NOT NULL default '0',
  `EDIT_OF` binary(16) NOT NULL,
  `DATE` int(10) unsigned NOT NULL,
  `TITLE` tinytext NOT NULL,
  `BODY` mediumtext NOT NULL,
  `visibile` enum('0','1') NOT NULL default '1',
  `SIGN` tinyblob NOT NULL,
  `ADMIN_SIGN` tinyblob NOT NULL,
  `EXTVAR` blob NOT NULL,
  PRIMARY KEY  (`HASH`),
  KEY `REP_OF` (`REP_OF`),
  KEY `EDIT_OF` (`EDIT_OF`),
  KEY `DATE` (`DATE`),
  KEY `AUTORE` (`AUTORE`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;");
$db->query("CREATE TABLE `{$bsess}_sez` (
  `ID` int(10) unsigned NOT NULL,
  `SEZ_NAME` varchar(250) default '',
  `SEZ_DESC` text,
  `MOD` text NOT NULL,
  `PKEY` text NOT NULL,
  `PRKEY` tinyblob NOT NULL,
  `THR_NUM` int(10) unsigned NOT NULL default '0',
  `REPLY_NUM` int(10) unsigned NOT NULL default '0',
  `ONLY_AUTH` int(10) unsigned NOT NULL default '1',
  `AUTOFLUSH` int(10) unsigned NOT NULL default '0',
  `ORDINE` int(10) unsigned NOT NULL default '0',
  `FIGLIO` int(10) unsigned NOT NULL default '0',
  `last_admin_edit` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;");
$db->query("CREATE TABLE `{$bsess}_titles` (
  `id` smallint(5) NOT NULL auto_increment,
  `posts` int(10) default NULL,
  `title` varchar(128) character set latin1 collate latin1_general_ci default NULL,
  `pips` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;");
$db->query("insert  into {$bsess}_titles values 
(1, 0, 'Timido allievo', 1), 
(2, 50, 'Pronto a postare', 2), 
(3, 100, 'Frequentatore assiduo', 3), 
(4, 200, 'Membro a pieno titolo', 3), 
(5, 400, 'Spammatore di frodo', 4), 
(6, 600, 'Scrivano assuefatto', 4), 
(7, 900, 'Capitello del forum', 5), 
(8, 1200, 'Colonna portante', 5), 
(9, 1500, 'Veterano del forum', 6), 
(10, 2000, 'Silver member', 6), 
(11, 2500, 'Gold member', 7);");

}

?>