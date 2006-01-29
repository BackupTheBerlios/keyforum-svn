<?
require_once('ez_sql.php');
include ("core.php");
include("testa.php");


// lingua
$lang += load_lang('lang_addon', $blanguage ); 

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

if($_REQUEST['action'])
{
	$new_addon_array = $_POST['new_addon'];
	$old_addon_array = $_POST['old_addon'];
	foreach($old_addon_array as $old_addon=>$value)
	{
		if($new_addon_array[$old_addon] != $value)
		{
			if($value === '1')
			{	
				$key = substr($old_addon,0,-3);
				$to_rem[$key] = $new_addon_array[$old_addon];
			}
			else if($value === '0')
			{
				$key = substr($old_addon,0,-3);
				$to_add[$key] = $new_addon_array[$old_addon];
			}
			else
			{
				$key = substr($old_addon,0,-3);
				$db->query("INSERT INTO config ( MAIN_GROUP , SUBKEY , FKEY , VALUE ) VALUES ('CORE', 'ADDON', '$key', 'load')");
			}
		}
	}
	
	if($to_add)foreach($to_add as $key=>$value)
	{
		$query_add .= "OR (SUBKEY = 'ADDON' AND FKEY = '$key') ";
	}
	if($to_rem)foreach($to_rem as $key=>$value)
	{
		$query_rem .= "OR (SUBKEY = 'ADDON' AND FKEY = '$key') ";
	}
	$query_add = "Update config SET `VALUE` = 'load' WHERE 0 $query_add";
	$query_rem = "Update config SET VALUE = 'not load' WHERE 0 $query_rem";

	if(count($to_add)) $db->query($query_add);
	if(count($to_rem)) $db->query($query_rem);
	echo "<center><b>".$lang['addon_successmod']."</b><br>  <a target=_blank href=\"restart.php\">".$lang['addon_restartkf']."</a></center>";
	exit();
}

//ACQUISIZIONE DATI

$query = "SELECT * FROM config WHERE 1";
$result = $db->get_results($query);
foreach($result as $riga)
{
	$config[$riga->MAIN_GROUP][$riga->SUBKEY][$riga->FKEY] = $riga->VALUE;
}

$dir = "../../../CORE/addon/";
// Open a known directory, and proceed to read its contents
if (is_dir($dir)) 
{
	if ($dh = opendir($dir)) 
	{
		while (($file = readdir($dh)) !== false) 
		{
			if(strpos($file,'.') != 0  && !is_dir($file))
			{
				//Prendo Il nome e la descrizione dalla prima riga (anche se poco elegante e lento)
				$riga = file($dir.$file);
				list($nome, $descrizione) = explode('|',$riga[0]);
				$nome = substr($nome,1);
				$nome_file = substr($file,0,-3);
				if(isset($config[CORE][ADDON][$nome_file]))
				{
					if($config[CORE][ADDON][$nome_file] == 'load')	$stato = 1;
					else $stato = 0; 
				}
				else $stato = '';
				
				$addon[$file] = array($nome, $descrizione,$stato);
			}
       }
       closedir($dh);
   }
}
?>
<html>
<head>
<meta http-equiv="Content-Language" content="it">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Gestione Addon</title>
<link type="text/css" rel="stylesheet" href="style_page.css">
</head>
<body>
<br>
<? echo " <h3>".$lang['addon_info1']."</h3>";?>
<div align="center">
<form method="post" action="">
	<table border="0" width="100%">
		<tr>
			<th class='row1'>
<? echo "			<font face=\"Verdana\"><b>".$lang['addon_addongest']."</b></font></th>";?>
		</tr>
		<tr>
			<td class='row1' >
            <table cellSpacing="0" cellPadding="0" width="100%" align="center" border="0">
<?	if($addon)foreach($addon as $file=>$arr)
	{
		list($nome,$descrizione,$stato)=$arr;
		$checked = ($stato ? 'checked' : '');
		echo "
			<tr>
				<td>$nome</td>
				<td>$descrizione</td>
				<td>
					".$lang['addon_enable']."
					<input type='hidden'   name='old_addon[$file]' value='$stato'>
					<input type='checkbox' name='new_addon[$file]' value='1' $checked >
				</td>
			</tr>";
			} 
?>
            </table>
			</td>
		</tr>
		<tr>
			<td class='row1' align="center">
				<? echo " <input type=\"submit\" name=\"action\" value=\"".$lang['addon_submit']."\" />"; ?>
			</td>
		</tr>
	</table>
</form>
</div>

</body>

</html>
