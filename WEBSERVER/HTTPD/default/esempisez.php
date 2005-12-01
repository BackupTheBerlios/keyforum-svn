<?php
require ("testa.php");

if (!session_id()) print "Non hai una sessesione\n";
$sez[SEZID]=10;
$sez[SEZ_NAME]='alfa test';
$sez[SEZ_DESC]='descrizione della sezione alfa test';
$sez[ORDINE]=2; //è la seconda sezione
$core=new CoreSock;
// Per momotizzare una variabile dobbiamo usare l'insieme delle funzione chiamate TMPVAR.
// La funzione che momorizza si chiama ADDVAR
// Visto che ci possono essere anche più sessioni memorizzate noi inseriamo la nostra variabile in una sottochiave di session_id();
// EditSez è il nome della funzione che nel core crea e modifica le sezioni.
// il vettore numero ci suggerisce che possiamo inserire un insieme di puù sezioni nello stesso comando.
$req[TMPVAR][ADDVAR][session_id()][EditSez][0]=$sez;
$core->Send($req);
$core->Read(); //buttiamo via la risposta, che ce ne frega?

/* Una volta che abbiamo inserito la variabile nel core ci possiamo anche disconnettere.
  Possiamo chiudere il browser. Possiamo andare a casa fare la doccia...etc.
  La variabile che abbiamo messo dentro resta memorizzata fino
  a quando nn viene riavviato il core o non usiam l'apposita funzione.
*/

// Deciamo di aggiungere una nuova sezione:
$sez[SEZID]=20;
$sez[SEZ_NAME]='beta test';
$sez[SEZ_DESC]='descrizione della sezione abeta test';
$sez[ORDINE]=1; //è la prima sezione
$secreq[TMPVAR][ADDVAR][session_id()][EditSez][1]=$sez;
$core->Send($secreq);  //facciamo un altro store
$core->Read();  //butto via la risposta


/*
 Con la richiesta che segue chiediamo di fornirci sia il BINDUMP delle variabili che la lista ramificata delle variabili aggiunte.
 il BINDUMP è la funzione che trasforma la lista delle variabili che abbiamo aggiunto nel formato leggibile da keyforum per l'amministrazione.
*/
$terreq[TMPVAR][DUMP]=session_id(); //Faccio la richiesta delle variabili solo per dimostrazione. Nel nostro caso è inutile.
$terreq[TMPVAR][BINDUMP]=session_id();  //Ritorna la stringa binaria nel formato che ci serve a noi.
$core->Send($terreq); //
$risp=$core->Read(); //Adesso la risposta ci serve.
print "Il messaggio amministrativo che verrà spedito è:<bR>\n".$risp[TMPVAR][BINDUMP]."<br><br>\n\n";
/*Da questo punto in poi iniziamo a formare il messaggio che verrà spedito nel sistema.*/

$ADMIN[COMMAND]=base64_encode($risp[TMPVAR][BINDUMP]); //COMMAND deve contenere le variabili in formato bindump base64
$ADMIN[DATE]=$risp['CORE']['INFO']['GMT_TIME']; //Anche senza richiederlo al core. Il core risponde inserendo in reply l'ora sincronizzata di GMT
$ADMIN[TITLE]='Aggiunta di due sezione :)'; //Assegno un titolo al messaggio che invio. Non è obbligatorio.
/*
Prima di continuare salvo in alcune variabili la chiave pubblica e privata del forum. Ovviamente sono da cambiare.
Nel mio caso ho creato un nuovo forum vuoto con le nuove tabelle per vedere se funzionava.
*/
$PKEY='11525763062122400783746582957003630548852011083518671259710447421489327816772980934117585292056354176956912683309'.
        '591018730123251261769607820831881173179903013302130896812428218207405486800150129053615850063680267926992390965'.
        '7156735995112881607128572166722612013140750139553953724350717445673570897137818305919';
$PRIVKEY='QVFRQUFBQUJCMVpsY25OcGIyNEdCQUFBQURFdU9URUJDRWxrWlc1MGFYUjVCZ0lBQUFCcGJ3RUhRMmhsWTJ0bFpBRUFBUWR3Y21sMllYUmxCQWt'.
        'BQUFBQkJGOXdhR2tHTlFFQUFERXhOVEkxTnpZek1EWXlNVEl5TkRBd056Z3pOelEyTlRneU9UVTNNREF6TmpNd05UUTRPRFV5TURFeE1EZ3pOVEU'.
        '0TmpjeE1qVTVOekV3TkRRM05ESXhORGc1TXpJM09ERTJOemN5T1Rnd09UTTBNVEUzTlRnMU1qa3lNRFUyTXpVME1UYzJPVFUyT1RFeU5qZ3pNekE'.
        '1TlRreE1ERTROek13TVRJek1qVXhNall4TnpZNU5qQTNPREl3T0RNeE9EZ3hNVGN6TVRjNU9UQXdPRFkxTnpBek5qTTJNVFUxTmpRd09UUTROVFE'.
        'yTXpJeE1EZzJPRFU1TmprNU1qRTJORFEzT0RVek5EVXdPVFV3TlRjeU1qZzVOVE0xTkRjeE5UWTBOelUzTXpVME1qY3pNek15TlRNM01qWTVNakl'.
        '4TkRFNU5UYzRNREF4TXpZNE1qQTFOVGM1TkRZMk5EVXhNRFk0TnpFME9EZzNPVEkyT1RFNU1EYzVOREV5TnpNNU56WTFNREV3TmpBME5qVTVPRGs'.
        '1TVRBNU5UTTBOQUVDWDI0R05RRUFBREV4TlRJMU56WXpNRFl5TVRJeU5EQXdOemd6TnpRMk5UZ3lPVFUzTURBek5qTXdOVFE0T0RVeU1ERXhNRGd'.
        '6TlRFNE5qY3hNalU1TnpFd05EUTNOREl4TkRnNU16STNPREUyTnpjeU9UZ3dPVE0wTVRFM05UZzFNamt5TURVMk16VTBNVGMyT1RVMk9URXlOamd'.
        '6TXpBNU5Ua3hNREU0TnpNd01USXpNalV4TWpZeE56WTVOakEzT0RJd09ETXhPRGd4TVRjek1UYzVPVEF6TURFek16QXlNVE13T0RrMk9ERXlOREk'.
        '0TWpFNE1qQTNOREExTkRnMk9EQXdNVFV3TVRJNU1EVXpOakUxT0RVd01EWXpOamd3TWpZM09USTJPVGt5TXprd09UWTFOekUxTmpjek5UazVOVEV'.
        '4TWpnNE1UWXdOekV5T0RVM01qRTJOamN5TWpZeE1qQXhNekUwTURjMU1ERXpPVFUxTXprMU16Y3lORE0xTURjeE56UTBOVFkzTXpVM01EZzVOekV'.
        '6TnpneE9ETXdOVGt4T1FFQ1gzRUdtd0FBQURFd09UVTBORFExTVRFd09EYzVPVGMyTWpnME5qZ3hPREV6TnpVek5UZzNOVEV5TXprMk1EY3pOVFE'.
        'xTkRVMk5UYzBOVGc1T1RnNU5qTTRORFUzTWpNd09ERTFNRGsxT1RJNU5UazBOakF4TmpFMk1USTVNRE16TXpNek16a3pOamN6TkRNMU5UWTFNVEk'.
        '0TWpJNE5qWTBOams1TXpBeE1ESTFNelF4TVRnek5UVTRNRFEwTWpNeU5USXhNVGN3TlRjME1qRTVNalF4T1RrekFRSmZjQWFiQUFBQU1UQTFNakU'.
        'xTXprNE16WTFNekUzTXpnMU1USXdNemN3TkRrME16STJPRE0wT1RZNU5EQTNNemcwTlRZeE9USTBNakF6TWpNNU1UYzJPRFl3T1Rjd05EVTFNakV'.
        '3TVRnME9UTTRNVFl3TWpBNE1EUTFNemd6Tnpnd01UVXhOalE0TVRFeU16RXlOVEl5TWpBME1ERXpPVGd5T1RFek56TTJOVGt4T1RJek56TXdNekl'.
        '3TURNM09UQTVORE0yTnprNU5qUTJNRGM1TmpnMU9ETUJBMTlrY0FhYUFBQUFNVGt3T1RBeU1qZzBOVEExTWpNNU5qTTVOalkwTURVMk1qWTFOakk'.
        'zTVRFd05UZ3hPVGsxTXprek5ERTFNams0TXpNMU5EQXdPVEkyTlRBM056UXpOalk0T1RBMU1qUTFOelUwTWpVMU16RXhNek13TmpRMk5UZzFPRFk'.
        'wT1RZNU1EVTJNVGd6TWpVMU5Ea3pOVEkzTXpZeE56WTVNalkyTkRreU9UQTNNRGN4T1RFNU1EQXpNVEkxTVRJeU1UUXlNemt4TlRVME9USTVPUUV'.
        'DWDNVR21nQUFBRE13TmpNM05ETTNNVEV3T1RrMU9EQTFNakV6TkRZMk1UYzBNRGt5TnpZMk1EazFNRGN6T1RRM016WXhNVFV3TmpNd016WTBNemc'.
'1TnpJeE9EQTVNVGs1TURJek5qSTFOVEkyTXpBeE1UYzBNak01TXpjd016WTNNREEyT1Rrd056azVOVGM1TXpVME1EVXlNakl5TVRrME9UVTVPVGsyTWpNMU16WTFNVFV5T'.
'kRRM01qVTVOREU1TmpnME9EQTFNREV3TVRVM05UWXpNakVCQTE5a2NRYWFBQUFBTmpFNU5qVTBPRE0wT1RBek1qUTVNamd3TWprek1UVXpOemt3TVRnME1UWTFNRFkyTXpjd05'.
'UQTFPVE00T0Rnek5UZ3lNRGs0T0RBNE1URTRPVEEwTlRJNE16azVOVGcxTkRJM09UY3pOakU1TmpVME56VXpNRGMyTkRnME16Z3pNalkyT0RZeE9EZ3dNakV3TnpFd01E'.
'WTROekV6TURjME5EWXpPVFkwTkRFeU1qWXhORGswTnpBNU9EazJORFE0TmpFeU9USTVOekF5TlFFQ1gyUUdOQUVBQURjM09ERTFOalV4T1RVek9ERTJPVEEzTmpJeE5'.
'ERXdNRFV4TnpReE5UUXhNelkwTlRVMk5qY3hNRE13TnpjNE5EQTNOekUxT0RJNE9UZ3pNelEzT0RnNE56Y3dPVFkzTWpJMU9UVTJPVFV6TmpnNU1EUTJNREEwTURBd05ESTV'.
'PVEV3TXpJd05UVXpOalU1TURjM09ETXpPREUxTURZM01UYzJNRFl5T1RFd09ERXdOakV6T0RJMk9EZ3pOems0TVRrM016azFNekUzTXpFeU9UQTNPVFF6TkRFNU5Ua3hOemM'.
'1TkRZd05UQTNOall5TnpFME1qTTBPRGMyTmpZNU5qa3pNVGMyTmpJME5UTTBNakk0TXpFek1ESTFOakl3TmpreE5Ea3dNRGszT0RnNE5UUXpNRGM0T1RVMU5UZzRO'.
'RFk1TkRJME5UY3pNemMyTnpFM016RTJNVEU1TkRVek1qQXdNRGc1TmpZMk16RXdPVGs0TVRNNU5EZzVPVGd5TWpBMU9EUXhOakU1T1RBM016RXlPREF3TXpNM0FRSmZaUU1CQUFFQQ==';
// Scusate il casino per ste variabili di merda >.<

// Ora dobbiamo crea l'MD5 del messaggio
$ADMIN[MD5]=md5($PKEY.$ADMIN[DATE].$ADMIN[TITLE].$ADMIN[COMMAND],true);
//IMPORTANTE: In futuro, sicuramente, cambierà il metodo di calcolo dell'hash. Penso che sarà qualcosa del tipo md5(dato."\x00".dato."\x00".dato)
// Il cambiamento è fondamentale per eliminare un buco nella sicurezza.
// I forum basati sul vecchio MD5 saranno incompatibili con il nuovo formato.

// Prepariamo una variabile per dire al core, a quale forum appartiene il messaggio:
$ADMIN[FDEST]=sha1($PKEY,true); //Lo sha1 della chiave pubblica identifica il forum

/*Adesso ci manca solo la firma RSA.
 Per ottenere la firma ci servono due dati. MD5 del messaggio & chiave privata.
*/
print "MD5 $ADMIN[MD5]\n<br>";
$quintareq[RSA][FIRMA][0][md5]=$ADMIN[MD5];
$quintareq[RSA][FIRMA][0][priv_pwd]=base64_decode(base64_decode($PRIVKEY)); //la pwd era in base64 ben due volte, per errore. Lo devo decodificare.
$core->Send($quintareq);
$var=$core->Read();
$ADMIN[SIGN]=$var[RSA][FIRMA][$ADMIN[MD5]];  //Inserisco la firma nel futuro messaggio
$ADMIN[TYPE]=3; // Specifico il tipo di messaggio (3=messaggio admin)

$sestareq[FORUM][ADDMSG]=$ADMIN;
$core->Send($sestareq);
$risp=$core->Read();
print "La risposta è ".$risp[FORUM][ADDMSG]."<br>\n";

$quartareq[TMPVAR][DELVAR]=session_id(); // Cancello la variabile che avevo messo in store. Una volta spedito il messaggio amministrativo non mi serve più
$core->Send($quartareq);
$core->Read();

$whereiam=1;
include ("end.php");

?>