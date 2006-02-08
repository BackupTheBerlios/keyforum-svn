<?PHP

// *******************************
// script di conversione utenti e messaggi da una board
// ad una nuova
//
// NOTA IMPORTANTE: � necessario inserire le sezioni prima di fare le operazioni di migrazione!
//
// richiede questa tabella temporanea, vuota nel db sorgente
/*
CREATE TABLE `hash_tmp` (
  `OLD_HASH` binary(16) NOT NULL,
  `NEW_HASH` binary(16) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci
*/

//******** CONFIGURAZIONE *************

// massima durata in secondi dello script
ini_set("max_execution_time",3600);

//nome db con i dati di origine
$dborigname="keyforum-org";
// nome sessione di origine
$sesorg="keyfo";
// chiave privata Admin forum di destinazione
// va inserita in un file chiamato pkeytemp.php
// contenente
//$PRIVKEY=".....";
include("pkeytemp.php");

// sessione da convertire
$consess="imptest";

// il forum di destinazione � quello corrente...

//************************************************

$whereiam="migrator";
include ("testa.php");

// chiudo la tabella di testa.php per permettere il flush()
echo "</table>";

$core=new CoreSock;

// database di origine
$dborig = new db($_ENV['sql_user'], $_ENV['sql_passwd'], $dborigname,$_ENV['sql_host'].":".$_ENV['sql_dbport']);

// decodifico la chiave privata dell'admin
$PRIVKEY=base64_decode($PRIVKEY);


echo "<b>migrazione del forum $sesorg dal database $dborigname, attendere prego....</b><br>";
flush();


// ************************************
// CONVERSIONE UTENTI
// ************************************
echo "<br>Conversione utenti<br>";
$feedback=0;
$cerror="";
if (! $res = $dborig->get_results("SELECT HASH,AUTORE,PKEYDEC FROM {$sesorg}_membri WHERE IS_AUTH='1'",ARRAY_N) ) die ("Non riesco a fare la select in {$sesorg}_membri :(");
$togo=$dborig->num_rows;
foreach ($res as $utente) {
    $riga=array();
    $riga[AUTORE]=$utente[1];
    $riga[PKEYDEC]=$utente[2];
    $riga[TYPE]=2;  // Utente
    $riga[_PRIVATE]=$PRIVKEY;
    $riga[CPSIGN]='AUTH';

    $res=$core->AddMsg($riga);
    if ($res[MD5]) {
    $db->doQuery("INSERT INTO hash_tmp (OLD_HASH,NEW_HASH) VALUES(?,?)",array($utente[0],$res[MD5]));
    
    // feedback
    $feedback++;
    $togo--;
    if($feedback==100) {echo " (-{$togo})<br>";$feedback=0;}
    echo "|";
    flush();
    
    } else {
        list(,$hex)=unpack("H*",$utente[0]);
        $cerror += "Utente con hash $hex non aggiunto perch� $res[ERRORE]<br>";
        
    }
}

// se ci sono stati errori li stampo alla fine
echo "<br>$cerror<br>";



// ************************************
// CONVERSIONE THREAD
// ************************************
echo "<br>Conversione thread<br>";
$feedback=0;
$cerror="";
$ebody=0;
if (! $res = $dborig->get_results("SELECT HASH,EDIT_OF,(HASH <> EDIT_OF) AS IS_EDIT,AUTORE,`DATE`,TITLE,SUBTITLE,BODY,SEZ FROM {$sesorg}_newmsg ORDER BY HASH=EDIT_OF DESC, `DATE`",ARRAY_A) ) die ("Non riesco a fare la select in {$sesorg}_newmsg :(");
$togo=$dborig->num_rows;
foreach ($res as $msg) {
    $riga=array();
    if($msg[IS_EDIT]) {
        $riga[EDIT_OF]=cambia($msg[EDIT_OF]);
        $riga[IS_EDIT]=1;
    } else
        $riga[EDIT_OF]='';
    $riga[AUTORE]=cambia($msg[AUTORE]);
    $riga['DATE']=$msg['DATE'];
    $riga['TYPE']=3;
    $riga['TITLE']=$msg['TITLE'];
    $riga['SEZ']=$msg['SEZ'];
    $riga['SUBTITLE']=$msg['SUBTITLE'];
    
        // body vuoti ?
    if(!$msg['BODY']){$ebody++;$msg['BODY']="{empty}";}
    
    $riga['BODY']=$msg['BODY'];
    $riga[_PRIVATE]=$PRIVKEY;
    $riga[CPSIGN]='ADMIN_SIGN';
    $risp=$core->AddMsg($riga);
    if ($risp[MD5]) {
        $db->doQuery("INSERT INTO hash_tmp (OLD_HASH,NEW_HASH) VALUES(?,?)",array($msg['HASH'],$risp[MD5]));
        
    // feedback
   $feedback++;
    $togo--;
    if($feedback==100) {echo " (-{$togo})<br>";$feedback=0;}
    echo "|";
    flush();
        
    } else {
        list(,$hex)=unpack("H*",$msg[HASH]);
        $cerror += "MSG con hash $hex non aggiunto perch� $risp[ERRORE]\n<br>";
        
    }
}

// se ci sono stati errori li stampo alla fine
echo "<br>$cerror<br>";
if ($ebody) { echo "trovati $ebody messaggi con il body vuoto";}


  

// ************************************
// CONVERSIONE REPLY
// ************************************
echo "<br>Conversione reply<br>";
$feedback=0;
$cerror="";
$ebody=0;
if (! $res = $dborig->get_results("SELECT HASH,EDIT_OF,(HASH <> EDIT_OF) AS IS_EDIT,AUTORE,`DATE`,TITLE,BODY,REP_OF FROM {$sesorg}_reply ORDER BY HASH=EDIT_OF DESC, `DATE`",ARRAY_A) ) die ("Non riesco a fare la select in {$sesorg}_reply :(");
$togo=$dborig->num_rows;
foreach ($res as $msg) {
    $riga=array();
    if($msg[IS_EDIT]) {
        $riga[EDIT_OF]=cambia($msg[EDIT_OF]);
        $riga[IS_EDIT]=1;
    } else
        $riga[EDIT_OF]='';
    $riga[AUTORE]=cambia($msg[AUTORE]);
    $riga[REP_OF]=cambia($msg[REP_OF]);
    $riga['DATE']=$msg['DATE'];
    $riga['TYPE']=4;
    $riga['TITLE']=$msg['TITLE'];
    
    // body vuoti ?
    if(!$msg['BODY']){$ebody++;$msg['BODY']="{empty}";}
    
    $riga['BODY']=$msg['BODY'];
    $riga[_PRIVATE]=$PRIVKEY;
    $riga[CPSIGN]='ADMIN_SIGN';
    $risp=$core->AddMsg($riga);
    if ($risp[MD5]) {
        $db->doQuery("INSERT INTO hash_tmp (OLD_HASH,NEW_HASH) VALUES(?,?)",array($msg['HASH'],$risp[MD5]));
        
    // feedback
   $feedback++;
    $togo--;
    if($feedback==100) {echo " (-{$togo})<br>";$feedback=0;}
    echo "|";
    flush();        
        
    } else {
        list(,$hex)=unpack("H*",$msg[HASH]);
        $cerror +=  "reply con hash $hex non aggiunto perch� $risp[ERRORE]\n<br>";
        
    }
}

// se ci sono stati errori li stampo alla fine
echo "<br>$cerror<br>";
if ($ebody) { echo "trovati $ebody messaggi con il body vuoto";}



function cambia ($old_hash) {
    global $db;
    $riga=$db->get_row($db->MakeQuery("SELECT NEW_HASH FROM hash_tmp WHERE OLD_HASH=?;",array($old_hash)),ARRAY_A);
    if ($riga) return $riga[NEW_HASH];
    else return NULL;
}

$db->MakeQuery("SELECT HASH,FIRMA FROM ME WHERE HASH=? AND NICK=?;",array("5465465456","ffs'@ me ?"));



// riapro la tabella chiusa per il flush()
echo '<table border="0" cellspacing="0" cellpadding="0" align="center" width="100%">';
include("end.php");
exit(0);


?>