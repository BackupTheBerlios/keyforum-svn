<?PHP
ob_start('ob_gzhandler'); 
include ("lib.php");
include ("testa.php");
include ("core.php");
// le righe commentate con "funziona" significa che sono state provate

$sesname=$_POST[name]; // Copio le variabili di POST in scalari normali. funza
$pkey_dec=$_POST[pkey];

$forum_id=sha1($pkey_dec); // Calcolo l'ID del forum in esadecimale, funziona

if (strlen($sesname) != 5) die("Il nome del forum deve essere di 5 caratteri");

$core=NEW CoreSock;
$req['FUNC']['Dec2Base64']=$pkey_dec;
if (!$core->Send($req)) die ("Errore mentre si tentava di comunicare con il core: ".$core->errmsg);
if (!($risp=$core->Read(6))) die ("Errore mentre si tentava di comunicare con il core: ".$core->errmsg);

$pkey_base64=$risp['FUNC']['Dec2Base64'];
print $pkey_base64;
mysql_query("");


?>
