<?
include ("testa.php"); # Librerie per creare la connessione MySQL

// nascondo gli errori mysql
$db->hide_errors();

echo "<br><br><center><b>";

// ******* V 310 **********
$rev="310";
echo "<font color=blue>AGGIORNAMENTO PER VERSIONE $rev</font><br>";

$db->query("alter table intkf_localmember add column  `LEVEL` tinyint(2) unsigned NOT NULL default '0';");
$db->query("alter table keyfo_localmember add column  `LEVEL` tinyint(2) unsigned NOT NULL default '0';");
$db->query("alter table tstkf_localmember add column  `LEVEL` tinyint(2) unsigned NOT NULL default '0';");

// test
$EZSQL_ERROR="";
$db->get_var("select LEVEL from keyfo_localmember");
if (!$EZSQL_ERROR)
{
echo "<br>DB Aggiornato alla versione 0.43 rev $rev<br><br><br>";
} else {
echo "<br><font color=red><b>errore aggiornando a 0.43 rev $rev</b></font><br>";
}


// ******* V 375 **********
$rev="375";
echo "<font color=blue>AGGIORNAMENTO PER VERSIONE $rev</font><br>";

$db->query("alter table intkf_localmember add column  `IS_AUTH` tinyint(1) unsigned NOT NULL default '0';");
$db->query("alter table keyfo_localmember add column  `IS_AUTH` tinyint(1) unsigned NOT NULL default '0';");
$db->query("alter table tstkf_localmember add column  `IS_AUTH` tinyint(1) unsigned NOT NULL default '0';");

// test
$EZSQL_ERROR="";
$db->get_var("select IS_AUTH from keyfo_localmember");
if (!$EZSQL_ERROR)
{
echo "<br>DB Aggiornato alla versione 0.43 rev $rev<br><br><br>";
} else {
echo "<br><font color=red><b>errore aggiornando a 0.43 rev $rev</b></font><br>";
}


// ******* V 466 **********
$rev="466";
echo "<font color=blue>AGGIORNAMENTO PER VERSIONE $rev</font><br>";

$db->query("alter table intkf_localmember add column  `HIDEAVATAR` tinyint(1) unsigned NOT NULL default '0';");
$db->query("alter table keyfo_localmember add column  `HIDEAVATAR` tinyint(1) unsigned NOT NULL default '0';");
$db->query("alter table tstkf_localmember add column  `HIDEAVATAR` tinyint(1) unsigned NOT NULL default '0';");
echo "<br>DB Aggiornato alla versione 0.43 rev $rev<br><br><br>";

//*********V 470x colonne emoticons *******
$rev="470";
echo "<font color=blue>AGGIORNAMENTO PER VERSIONE $rev</font><br>";

$db->query("alter table intkf_localmember add column  `EMOCOL` tinyint(1) unsigned NOT NULL default '4';");
$db->query("alter table keyfo_localmember add column  `EMOCOL` tinyint(1) unsigned NOT NULL default '4';");
$db->query("alter table tstkf_localmember add column  `EMOCOL` tinyint(1) unsigned NOT NULL default '4';");
echo "<br>DB Aggiornato alla versione 0.43 rev $rev<br><br><br>";



echo "</b></center>";

include ("end.php");
?>