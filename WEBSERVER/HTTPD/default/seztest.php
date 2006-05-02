<?php
require ("testa.php");
require("admin.php");

// inserire la chiave privata del forum nel file pkeytemp.php
include("pkeytemp.php");

$admin=new Admin(base64_decode($PRIVKEY));
$core=new CoreSock;


// PARAMETRI:
// $admin->EditSez($SEZID,$SEZ_NAME,$SEZ_DESC,$ORDINE,$FIGLIO,$ONLY_AUTH=1,$NEED_PERM=0,$HIDE=0,$ALIAS='',$REDIRECT='')
//
// $SEZID = id sezione, se omesso ne viene creato uno nuovo progressivo. Se indicato, lo crea se non esiste (sconsigliato) o lo edita se esiste
// $SEZ_NAME = nome sezione
// $SEZ_DESC = descrizione
// $ORDINE = ordine (posizione rispetto alle altre nella stessa sottosezione) - se >9000 il forum è una categoria, può contenere altri forum, ma non messaggi.
// $FIGLIO = figlio di (forum a cui questo sottoforum appartiene, se 0 è un categoria principale)
// $ONLY_AUTH = se 1 (default) solo gli utenti autorizzati possono scrivere, se 0 può scrivere chiunque
// $NEED_PERM = se 1 occorrono permessi specifici per scrivere in quella sezione
// $HIDE = se 1 la sezione è invisibile, per cancellare una sezione va impostato HIDE=1
// $ALIAS = se contiene altri numeri di sezioni, separati da virgole, i messaggi di quelle sezioni verranno mostrati anche in questa
// $REDIRECT = se contiene un indirizzo http quella sezione fungerà da redirect all'indirizzo


// le righe seguenti sono commentate per evitare danni accidentali
// togliete il commento solo se siete sicuri di averne compreso bene la funzione

// $admin->EditSez('','Testing Zone','',1,0,1,0,1,20,'http://www.keyforum.net');
// $admin->EditSez(100,'Testing Zone','',9004,0,1,0,0,'','');
// $admin->EditSez(101,'#profiles#','area per cambi avatar, firme, ecc..',1,100,1,0,1,'','');
// $admin->EditSez(102,'Redirector','redirector verso una URL esterna',2,100,1,0,0,'','http://www.keyforum.net');
// $admin->EditSez('','Alias','raggruppa i messaggi di sezioni diverse',3,100,1,0,0,'81,82,83','');


$risp=$admin->Send2Core("modifico la sezione");

if($risp[ERRORE])
{
$std->Error($risp[ERRORE]);
} else {
$std->Error("","","comando eseguito...");
}

include ("end.php");

?>