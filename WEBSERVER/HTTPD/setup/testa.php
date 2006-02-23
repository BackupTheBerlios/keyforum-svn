<?
require_once "Config.php";
require_once "ez_sql.php";

$xmldata = new Config;
$root =& $xmldata->parseConfig('http://'.$_SERVER['HTTP_HOST'].'/config/config.xml', 'XML');
if (PEAR::isError($root)) {  die('Error reading XML config file: ' . $root->getMessage()); }
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
//configurazione
$query = "SELECT * FROM config WHERE 1";
$result = $db->get_results($query);
foreach($result as $riga)
{
	$config[$riga->MAIN_GROUP][$riga->SUBKEY][$riga->FKEY] = $riga->VALUE;
}

include("langsupport.php");

// determino la lingua
if(!$_REQUEST['lang'])
{
$blanguage=GetUserLanguage();
} else {
$blanguage=$_REQUEST['lang'];
}

// lingua
$lang = load_lang('lang_index', $blanguage ); 


?>

<html>
<head>
<link rel="shortcut icon" href="favicon.ico">
<link type="text/css" rel="stylesheet" href="style_page.css" />

<script> 
function goTo(screen)
{
document.toolbar.action = screen;
document.toolbar.submit();
}
</script>


</head>
<body>

<div class="borderwrap">
  <div id='logostrip'>
    <a href=index.php><div id='logographic'></div></a>
  </div>
  </div>
<form method="POST" name="toolbar" action="">
  <div align=center>
  <?
  echo "
  <input onClick=\"javascript:goTo('addboard.php')\" class=\"button\" type=\"button\" value=\"{$lang['index_importbrd']}\" name=\"B1\">
  <input onClick=\"javascript:goTo('newboard.php')\" class=\"button\" type=\"button\" value=\"{$lang['index_createnewbrd']}\" name=\"B2\">
  <input onClick=\"javascript:goTo('mngws.php')\" class=\"button\" type=\"button\" value=\"{$lang['index_managews']}\" name=\"B3\">
  <input onClick=\"javascript:goTo('addon.php')\" class=\"button\" type=\"button\" value=\"{$lang['index_addon']}\" name=\"B3\">
  <input onClick=\"javascript:goTo('mngbd.php')\" class=\"button\" type=\"button\" value=\"{$lang['index_managebd']}\" name=\"B3\">
  <input onClick=\"javascript:goTo('restart.php')\" class=\"button\" type=\"button\" value=\"{$lang['index_restart']}\" name=\"B4\">
  ";
  ?>
  </div>
</form>
<br><br>