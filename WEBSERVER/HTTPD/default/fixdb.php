<?
include ("lib.php"); # Librerie per creare la connessione MySQL
# Le query vanno scritte dalla modifica più recente alla più vecchia salvo motivi diversi
mysql_query("ALTER TABLE `keyfo_membri` ADD INDEX ( `is_auth` ) ;") or print ("Errore query : " . mysql_error() . "<br>");
mysql_query("ALTER TABLE `keyfo_sez` ADD `AUTOFLUSH` INT( 10 ) UNSIGNED DEFAULT '0' NOT NULL ;") or print ("Errore query : " . mysql_error() . "<br>");
mysql_query("ALTER TABLE `keyfo_sez` ADD `ORDINE` INT( 10 ) UNSIGNED DEFAULT '0' NOT NULL ;") or print ("Errore query : " . mysql_error() . "<br>");
mysql_query("ALTER TABLE `keyfo_sez` ADD `FIGLIO` INT( 10 ) UNSIGNED DEFAULT '0' NOT NULL ;") or print ("Errore query : " . mysql_error() . "<br>");
mysql_query("ALTER TABLE `keyfo_congi` ADD INDEX ( `INSTIME` ) ;") or print ("Errore query : " . mysql_error() . "<br>");
mysql_query("ALTER TABLE `keyfo_sez` ADD `ONLY_AUTH` INT( 8 ) UNSIGNED DEFAULT '1' NOT NULL ;") or print ("Errore query : " . mysql_error() . "<br>");
echo "DB Aggiornato";
?>