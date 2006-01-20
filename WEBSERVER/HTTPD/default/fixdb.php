<?
include ("testa.php"); # Librerie per creare la connessione MySQL

$lastver="0.43";

// nascondo gli errori mysql
$db->hide_errors();

echo "<br><br><center><b>";

// ******* V 310 **********
$rev="310";
echo "<font color=blue>AGGIORNAMENTO PER VERSIONE $rev</font><br>";
foreach($config['WEBSERVER'] as $prefix)
{
$db->query("alter table {$prefix['SesName']}_localmember add column  `LEVEL` tinyint(2) unsigned NOT NULL default '0';");
OutSess($prefix['SesName']);
}
echo "</font>";
TestDB($lastver,$rev);



// ******* V 375 **********
$rev="375";
echo "<font color=blue>AGGIORNAMENTO PER VERSIONE $rev</font><br>";

foreach($config['WEBSERVER'] as $prefix)
{
$db->query("alter table {$prefix['SesName']}_localmember add column  `IS_AUTH` tinyint(1) unsigned NOT NULL default '0';");
OutSess($prefix['SesName']);
}
TestDB($lastver,$rev);



// ******* V 466 **********
$rev="466";
echo "<font color=blue>AGGIORNAMENTO PER VERSIONE $rev</font><br>";

foreach($config['WEBSERVER'] as $prefix)
{
$db->query("alter table {$prefix['SesName']}_localmember add column  `HIDEAVATAR` tinyint(1) unsigned NOT NULL default '0';");
OutSess($prefix['SesName']);
}
TestDB($lastver,$rev);




//*********V 470x colonne emoticons *******
$rev="470";
echo "<font color=blue>AGGIORNAMENTO PER VERSIONE $rev</font><br>";

foreach($config['WEBSERVER'] as $prefix)
{
$db->query("alter table {$prefix['SesName']}_localmember add column  `EMOCOL` tinyint(1) unsigned NOT NULL default '4';");
OutSess($prefix['SesName']);
}
TestDB($lastver,$rev);


echo "</b></center>";

include ("end.php");

function TestDB($lastver,$rev)
{
echo "<br>DB Aggiornato alla versione $lastver rev $rev<br><br><br>";
}

function OutSess($sess)
{
echo "<font color=violet>$sess</font> ";
}

?>