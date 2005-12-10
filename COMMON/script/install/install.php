<?php

// *****************************
// preparazione di keyforum.conf
// richiede che sia gi� stato creato un php.ini valido
// sotto apache/bin (tramite install-phpini.php)
// *****************************

$curdir = getcwd();
list ($phpdir, $installdir) = spliti ('\\\COMMON\\\script\\\install', $curdir);
$apachedir = ereg_replace ("\\\\","/",$phpdir);


// *********
// leggo configurazione da file XML (richiede il modulo CONFIG di PEAR)
require_once("Config.php");
$conf = new Config;
$root =& $conf->parseConfig('../../config/config.xml', 'XML');
if (PEAR::isError($root)) {
    die('Error reading XML config file: ' . $root->getMessage());
}
$settings = $root->toArray();
// *********

// *********
// connessione al db
 $connection = @mysql_connect($settings['root']['conf']['DB']['host'].":".$settings['root']['conf']['DB']['dbport'], $settings['root']['conf']['DB']['dbuser'], $settings['root']['conf']['DB']['dbpassword'])
  	or die("Non riesco a connettermi al database");


 $db = mysql_select_db($settings['root']['conf']['DB']['dbname'], $connection)
 	or die("Non riesco a selezionare il database");
// *********


// preparazione keyforum.conf (apache)
echo "PREPARAZIONE KEYFORUM.CONF \n";
$apacheconf  = "ServerRoot \"$apachedir/WEBSERVER/apache\"\n";
$apacheconf .= "LoadModule php5_module \"$apachedir/WEBSERVER/apache/bin/php5apache2.dll\"\n";
$apacheconf .= "Alias /config \"$apachedir/COMMON/config/\"\n\n";	

// CGI-BIN
$apacheconf .= "ScriptAlias /cgi-bin/ \"$apachedir/WEBSERVER/HTTPD/cgi-bin/\"\n\n";

$apacheconf .= "<Directory \"$apachedir/WEBSERVER/HTTPD/cgi-bin/\">\n";
$apacheconf .= "   AllowOverride None\n";
$apacheconf .= "   Options None\n";
$apacheconf .= "   Order allow,deny\n";
$apacheconf .= "   Allow from all\n";
$apacheconf .= "</Directory>\n\n";

	
$sql="SELECT subkey,fkey,value FROM config WHERE (fkey='DIRECTORY' OR fkey='PORTA' OR fkey='BIND') AND main_group='WEBSERVER'  GROUP BY subkey,fkey ORDER BY subkey";
$result = mysql_query($sql);

$cnt=0;
while ($row=mysql_fetch_array($result)) {
$cnt++;
if($row['fkey']=="BIND"){$bind=$row['value'];}
if($row['fkey']=="DIRECTORY") {$dir=$row['value'];}
if($row['fkey']=="PORTA") {$port=$row['value'];}
 
if ($cnt==3){
 $cnt=0;
 $apacheconf .= "# {$row['subkey']}\n";
 $apacheconf .= "Listen $port\n";
 $apacheconf .= "<VirtualHost $bind:$port>\n";
 $apacheconf .= "DocumentRoot \"$apachedir/WEBSERVER/HTTPD/$dir\"\n";
 $apacheconf .= "</VirtualHost>\n\n";
            }

}

// startup page
$apacheconf .= "# startup page\n";
$apacheconf .= "Listen 81\n";
$apacheconf .= "<VirtualHost 127.0.0.1:81>\n";
$apacheconf .= "DocumentRoot \"$apachedir/WEBSERVER/HTTPD/startup\"\n";
$apacheconf .= "</VirtualHost>\n";

// sovrascrivo keyforum.conf
$filename = "$apachedir/WEBSERVER/apache/conf/keyforum.conf";
echo "-> $filename\n";
$handle = fopen($filename, 'w');
fwrite($handle, $apacheconf);
fclose($handle);

// sovrascrivo chkdir.bat
$filename = "$apachedir/COMMON/script/chkdir.bat";
echo "-> $filename\n";
$chkdir= "@echo off
ECHO DIRECTORY CHECK
IF EXIST \"$phpdir\WEBSERVER\Apache\conf\keyforum.conf\" GOTO fine
ECHO KEYFORUM NEEDS CONFIGURATION
ECHO;
pause
install_keyforum.bat
:fine
ECHO OK
ECHO;
";
$handle = fopen($filename, 'w');
fwrite($handle, $chkdir);
fclose($handle);

exit();
?>
