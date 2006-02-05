<?
require_once('ez_sql.php');
require "functions.php";
include ("core.php");

include("testa.php");



// lingua
$lang += load_lang('lang_mngbd', $blanguage ); 

//inizializzo il db
//classe PEAR per file config (XML)
require_once "Config.php";
$xmldata = new Config;

$root =& $xmldata->parseConfig('http://'.$_SERVER['HTTP_HOST'].'/config/config.xml', 'XML');
if (PEAR::isError($root)) {
    die($lang['mngbd_error1'] . $root->getMessage());
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

$group = $db->get_var("SELECT VALUE FROM config WHERE MAIN_GROUP='SHARESERVER' AND SUBKEY='TCP' AND FKEY='GROUP'");

if(!$_REQUEST['edit']){
	$data['BAND'] = $db->get_var("SELECT VALUE FROM config WHERE MAIN_GROUP='TCP' AND SUBKEY='BANDA_LIMITE' AND FKEY='".$group."'");
	$data['PORT'] = $db->get_var("SELECT VALUE FROM config WHERE MAIN_GROUP='SHARESERVER' AND SUBKEY='TCP' AND FKEY='PORTA'");
	layout($data);
	die();
}else{
	UpdateDb($group);
		
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
			
			
	echo "<CENTER><b><H3>".$lang['mngbd_updated']."</H3></b>";
	echo "<font color=red><b><H3>".$lang['mngbd_reboot']."</H3></b></font><br><br></center></body></html>";
}


function layout($data){
	if(!$_REQUEST['lang']){
		$blanguage=GetUserLanguage();
	} else {
		$blanguage=$_REQUEST['lang'];
	}
	// lingua
	$lang = load_lang('lang_mngbd', $blanguage ); 
	
	echo  "
	<br>
	<div align=\"center\">
	  <table border=\"0\" width=\"500\" id=\"table1\">
		<tr>
		  <th class='row1'>
		  <font face=\"Verdana\"><b>".$lang['mngbd_title']."</b></font></th>
		</tr>
		<tr>
		  <td class='row1' >
		    <p align=\"center\"><br>
	            	<form method=\"POST\" action=\"mngbd.php?edit=1\">
					
				<table border=\"0\" width=\"100%\" id=\"table2\">
					<tr>
						<td class='row1' width=\"27%\"><b>
						<font size=\"2\">".$lang['mngbd_band']."</font></b></td>
						<td class='row1' width=\"71%\"><input type=\"text\" name=\"band\" value=\"".$data['BAND']."\" size=\"20\">".$lang['mngbd_kb']."</td>
					</tr>
					<tr>
						<td class='row3' colspan=\"2\">".$lang['mngbd_info']."</td>
					</tr>
					<tr>
						<td class='row1' width=\"27%\"><b>
						<font size=\"2\">".$lang['mngbd_port']."</font></b></td>
						<td class='row1' width=\"71%\"><input type=\"text\" name=\"port\" value=\"".$data['PORT']."\" size=\"20\"></td>
					</tr>
					<tr>
						<td class='row3' colspan=\"2\">".$lang['mngbd_info2']."</td>
					</tr>
				</table>
				<p align=\"center\"><input type=\"submit\" value=\"Conferma\" name=\"B1\"></p>
			</form>
		    </p>
		  </td>
		</tr>
	  </table>
	</div>
	
	</body>
	
	</html>
	";
}

function UpdateDb($group){
	global $db;

	if(is_numeric($_REQUEST['band'])){
		$db->query("UPDATE config
		SET VALUE='".$_REQUEST['band']."'
		WHERE MAIN_GROUP='TCP' AND SUBKEY='BANDA_LIMITE' AND FKEY='".$group."'");
	}
	if(is_numeric($_REQUEST['port'])){
		$db->query("UPDATE config
		SET VALUE='".$_REQUEST['port']."'
		WHERE MAIN_GROUP='SHARESERVER' AND SUBKEY='TCP' AND FKEY='PORTA'");
	}

}

?>