<?
ini_set("include_path", ini_get ("include_path").";.;../../../WEBSERVER/pear");

$host="127.0.0.1";
$timeout= 0.5;

// *********
// leggo configurazione da file XML (richiede il modulo CONFIG di PEAR)
require_once("Config.php");
$conf = new Config;
$root =& $conf->parseConfig('../config/config.xml', 'XML');
if (PEAR::isError($root)) {
    die('Error reading XML config file: ' . $root->getMessage());
}
$settings = $root->toArray();
// *********

// porta per mysql
$mysqlport=$settings['root']['conf']['DB']['dbport'];

// leggo keyforum.conf per rilevare le porte richieste
$filename = "../../WEBSERVER/Apache/conf/keyforum.conf";
$handle = fopen($filename, "r");

while (!feof($handle)) {
    $buffer = fgets($handle);

if (preg_match("/\blisten\b/i", $buffer)) { 
        $portlist .= trim(str_replace("\n","",(preg_replace("/\blisten\b/i","",$buffer)))).","; 
        }

}

fclose($handle);

// altre porte (keyforum com = 40569 , keyforum shell = 40565")
$otherport=",40569,40565";

$portlist = $portlist.$mysqlport.$otherport;

$ports= explode(',', $portlist);

$blocked=0;

foreach ($ports as $i => $value) {

settype($ports[$i], "integer");
	if (($handle = @fsockopen($host, $ports[$i], $errno, $errstr, $timeout)) == false)
	{
	echo "Port".$ports[$i]."=FREE\n";
	flush();
	}	else
	{ 
	echo "Port".$ports[$i]."=BLOCKED\n";
	flush();
	$blocked=1;
	}

}	

     @fclose($handle);			

     // se ci sono porte occupate lo scrivo in un file		
     if ($blocked) { 
       $filename = "../../kstop.txt";
       $handle = fopen($filename, 'w');
       fwrite($handle, "(some ports blocked)");
       fclose($handle);
       }


?>