<?php

$server_sql = mysql_pconnect($_ENV[sql_host],$_ENV[sql_user],$_ENV[sql_passwd]);
if (!$server_sql) die ("Non riesco a connettermi al server MySQL");
if ( !mysql_select_db($_ENV[sql_dbname]) ) die("Impossibile aprire il DataBase MySQL:".mysql_error().".</br>");

function Muori($errore) {
  print $errore;
  exit(0);
}
?>