<?php

$curdir = getcwd();
list ($phpdir, $installdir) = spliti ('\\\COMMON\\\script\\\install', $curdir);
$apachedir = ereg_replace ("\\\\","/",$phpdir);

// die("$curdir     -> $phpdir\n");

// *********
// preparazione di php.ini
// leggo il sample
echo "PREPARAZIONE PHP.INI ....\n";
$filename = "php.ini.sam";
$handle = fopen($filename, "r");
$phpinistart = fread($handle, filesize($filename));
fclose($handle);

$phpiniend=str_replace("{keypath}",$phpdir,$phpinistart);

// sovrascrivo php.ini
$filename = "$apachedir/WEBSERVER/apache/bin/php.ini";
echo "-> $filename\n";
$handle = fopen($filename, 'w');
fwrite($handle, $phpiniend);

exit();
?>
