<?
require_once('ez_sql.php');
include ("core.php");

include("testa.php");



// lingua
$lang += load_lang('lang_mngws', $blanguage ); 

//inizializzo il db
//classe PEAR per file config (XML)
require_once "Config.php";
$xmldata = new Config;

$root =& $xmldata->parseConfig('http://'.$_SERVER['HTTP_HOST'].'/config/config.xml', 'XML');
if (PEAR::isError($root)) {
    die($lang['mngws_error1'] . $root->getMessage());
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

if(!$_REQUEST['ws']){
	$data = $db->get_results("SELECT DISTINCT SUBKEY FROM config WHERE MAIN_GROUP='WEBSERVER'");
	layoutws($data);
	die();
}else{
	$ws = $db->get_results("SELECT FKEY, VALUE FROM config WHERE MAIN_GROUP='WEBSERVER' AND SUBKEY='".$_REQUEST['ws']."'");
	foreach($ws as $riga){
		if($riga->FKEY=="BIND") $data['BIND']=$riga->VALUE;
		if($riga->FKEY=="DIRECTORY") $data['DIRECTORY']=$riga->VALUE;
		if($riga->FKEY=="GROUP") $data['GROUP']=$riga->VALUE;
		if($riga->FKEY=="PORTA") $data['PORTA']=$riga->VALUE;
		if($riga->FKEY=="SesName") $data['SesName']=$riga->VALUE;
	}
	if(!$_REQUEST['edit']){
		layout($data);
		die();
	}else{
		$postdata['bsession']=$_REQUEST['bsession'];
		$postdata['bind']=$_REQUEST['bind'];
		$postdata['bport']=$_REQUEST['bport'];
		$postdata['directory']=$_REQUEST['directory'];
		
		UpdateDb($postdata, $data);
		
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
		install_keyforum.bat
		:fine
		ECHO OK
		ECHO;
		";
		$handle = fopen($filename, 'w');
		fwrite($handle, $chkdir);
		fclose($handle);
		
		
		echo "<CENTER><b><H3>Board {$_REQUEST['ws']} ".$lang['mngws_updated']."</H3></b>";
		echo "<font color=red><b><H3>".$lang['mngws_reboot']."</H3></b></font><br><br></center></body></html>";
	}
}

function layoutws($data=array()){
	if(!$_REQUEST['lang']){
		$blanguage=GetUserLanguage();
	} else {
		$blanguage=$_REQUEST['lang'];
	}
	// lingua
	$lang = load_lang('lang_mngws', $blanguage ); 
	echo "<p align='center'><b><font face='Verdana' size='6'>".$lang['mngws_title']."</font></b></p><br>";
	foreach($data as $riga){
		echo "<p align='center'><b><a href='mngws.php?ws=".$riga->SUBKEY."'>".$riga->SUBKEY."</a></b></p>";
	}
	echo "</body></html>";
}

function layout($data=array()){
	global $db;
	if(!$_REQUEST['lang']){
		$blanguage=GetUserLanguage();
	} else {
		$blanguage=$_REQUEST['lang'];
	}
	// lingua
	$lang = load_lang('lang_mngws', $blanguage ); 
	
	echo  "
	<br>
	<div align=\"center\">
		<table border=\"0\" width=\"500\" id=\"table1\">
			<tr>
				<th class='row1'>
				<font face=\"Verdana\"><b>".$lang['mngws_title1']." ".$_REQUEST['ws']."</b></font></th>
			</tr>
			<tr>
				<td class='row1' >
	            <p align=\"center\"><br>
	            <form method=\"POST\" action=\"mngws.php?edit=1&ws=".$_REQUEST['ws']."\">
					
					<table border=\"0\" width=\"100%\" id=\"table2\">
						<tr>
							<td class='row1' width=\"27%\"><b>
							<font size=\"2\">".$lang['mngws_sessname']."</font></b></td>
							<td class='row1' width=\"71%\"><input type=\"text\" name=\"bsession\" value=\"".$data['SesName']."\" size=\"20\"></td>
						</tr>
						<tr>
							<td class='row3' colspan=\"2\">".$lang['mngws_info1']."</td>
						</tr>
						<tr>
							<td class='row1' width=\"27%\"><b><font face=\"Verdana\" size=\"2\">porta</font></b></td>
							<td class='row1' width=\"71%\"><input type=\"text\" name=\"bport\" value=\"".$data['PORTA']."\" size=\"20\"></td>
						</tr>
						<tr>
							<td class='row3' width=\"98%\" colspan=\"2\">
							".$lang['mngws_insertvalue']." &gt; 
							".$lang['mngws_valueinfo']."</td>
						</tr>
						<tr>
							<td class='row1' width=\"27%\"><b><font face=\"Verdana\" size=\"2\">bind</font></b></td>
							<td class='row1' width=\"71%\"><select size=\"1\" name=\"bind\">
							<option ";
							if($data['BIND']=="127.0.0.1") echo "selected";
							echo " value=\"127.0.0.1\">127.0.0.1</option>
							<option ";
							if($data['BIND']=="*") echo "selected";
							echo " value=\"*\">*</option>
							</select></td>
						</tr>
						<tr>
							<td class='row3' width=\"98%\" colspan=\"2\">
							".$lang['mngws_info3']."</td>
						</tr>
						<tr>
							<td class='row1' width=\"27%\"><b><font face=\"Verdana\" size=\"2\">".$lang['mngws_directory']."</font></b></td>
							<td class='row1' width=\"71%\"><input type=\"text\" name=\"directory\" value=\"".$data['DIRECTORY']."\" size=\"20\"></td>
						</tr>
						<tr>
							<td class='row3' width=\"98%\" colspan=\"2\">
							".$lang['mngws_info4']."</td>
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

function UpdateDb($postdata, $data){
	global $db;

	if($postdata['directory']!=$data['DIRECTORY']){
		$db->query("UPDATE config
		SET VALUE='".$postdata['directory']."'
		WHERE MAIN_GROUP='WEBSERVER' AND SUBKEY='".$_REQUEST['ws']."' AND FKEY='DIRECTORY'");
	}
	
	if($postdata['bport']!=$data['PORTA']){
		$db->query("UPDATE config
		SET VALUE='".$postdata['bport']."'
		WHERE MAIN_GROUP='WEBSERVER' AND SUBKEY='".$_REQUEST['ws']."' AND FKEY='PORTA'");
	}
	
	if($postdata['bind']!=$data['BIND']){
		$db->query("UPDATE config
		SET VALUE='".$postdata['bind']."'
		WHERE MAIN_GROUP='WEBSERVER' AND SUBKEY='".$_REQUEST['ws']."' AND FKEY='BIND'");
	}
	
	if($postdata['bsession']!=$data['SesName']){
		$db->query("UPDATE config
		SET VALUE='".$postdata['bsession']."'
		WHERE MAIN_GROUP='WEBSERVER' AND SUBKEY='".$_REQUEST['ws']."' AND FKEY='SesName'");
		$db->query("UPDATE config
		SET SUBKEY='".$postdata['bsession']."'
		WHERE SUBKEY='".$_REQUEST['ws']."'");
	}
}

?>