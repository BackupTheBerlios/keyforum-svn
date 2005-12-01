<?php

require_once "Config.php";
$xmldata = new Config;

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

$server_sql = mysql_pconnect($_ENV['sql_host'].":".$_ENV['sql_dbport'],$_ENV['sql_user'],$_ENV['sql_passwd']);
//$server_sql = mysql_pconnect($_ENV[sql_host],$_ENV[sql_user],$_ENV[sql_passwd]);
if (!$server_sql) die ("Non riesco a connettermi al server MySQL");
if ( !mysql_select_db($_ENV[sql_dbname]) ) die("Impossibile aprire il DataBase MySQL:".mysql_error().".</br>");

function Muori($errore) {
  print $errore;
  exit(0);
}
?>