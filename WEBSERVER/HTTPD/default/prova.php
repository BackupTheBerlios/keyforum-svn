<?PHP
 include ("testa.php");
?>
<?PHP
$req_nod[INFO][FORUM][0]="b99a568cda554c315c1948db7fbfc3320d61af81";
$core   = new CoreSock;
$core->Send($req_nod);
if (!($risposta=$core->Read())) die ("Non ha risposto entro il timeout");
echo $risposta[INFO][FORUM]["b99a568cda554c315c1948db7fbfc3320d61af81"][NUM_NODI];
?>