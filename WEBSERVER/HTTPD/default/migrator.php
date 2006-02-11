<?PHP

// *******************************
// script di conversione utenti e messaggi da una board vecchia
// ad una nuova
//
// NOTA IMPORTANTE: � necessario inserire le sezioni prima di fare le operazioni di migrazione!
//

//******** CONFIGURAZIONE *************

// massima durata in secondi dello script
ini_set("max_execution_time",3600);

// nome sessione di origine (bastano le tabelle _memberi , _newmsg , _reply )
$sesorg="keyfo2";
// chiave privata Admin forum di destinazione
// va inserita in un file chiamato pkeytemp.php
// contenente
//$PRIVKEY=".....";
include("pkeytemp.php");

// il forum di destinazione � quello corrente...

//************************************************

$whereiam="migrator";
include ("testa.php");

// chiudo la tabella di testa.php per permettere il flush()
echo "</table>";

$core=new CoreSock;

// decodifico la chiave privata dell'admin
$PRIVKEY=base64_decode($PRIVKEY);


echo "<b>migrazione del forum $sesorg, attendere prego....</b><br>";
flush();

// se non esiste creo la tabella temporanea degli hash
$db->query(" 

CREATE TABLE IF NOT EXISTS `hash_tmp` (
 `OLD_HASH` binary(16) NOT NULL,
 `NEW_HASH` binary(16) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci

");


// svuoto la tabella temporanea, se piena
$db->query("delete from hash_tmp where 1");


// ************************************
// CONVERSIONE UTENTI
// ************************************
echo "<br>Conversione utenti<br>";
$feedback=0;
$accepted=0;
$cerror="";
if (! $res = $db->get_results("SELECT HASH,AUTORE,PKEYDEC FROM {$sesorg}_membri WHERE IS_AUTH='1'",ARRAY_N) ) die ("Non riesco a fare la select in {$sesorg}_membri :(");
$togo=$db->num_rows;
echo "$togo records selected<br>";
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
    $accepted++;
    $togo--;
    if($feedback==100) {echo " (-{$togo})<br>";$feedback=0;}
    echo "|";
    flush();
    
    } else {
        list(,$hex)=unpack("H*",$utente[0]);
        $cerror += "Utente con hash $hex non aggiunto perch� $res[ERRORE]<br>";
        
    }
}

echo "<br>$accepted record accettati dal core<br>";
$test = $db->get_var("SELECT count(*) FROM {$SNAME}_membri");
echo "$test messaggi nel nuovo db<br>";

// se ci sono stati errori li stampo alla fine
echo "<br>$cerror<br>";



// ************************************
// CONVERSIONE THREAD
// ************************************
echo "<br>Conversione thread<br>";
$feedback=0;
$accepted=0;
$cerror="";
$ebody=0;
if (! $res = $db->get_results("SELECT HASH,EDIT_OF,(HASH <> EDIT_OF) AS IS_EDIT,AUTORE,`DATE`,TITLE,SUBTITLE,BODY,SEZ FROM {$sesorg}_newmsg ORDER BY HASH=EDIT_OF DESC, `DATE`",ARRAY_A) ) die ("Non riesco a fare la select in {$sesorg}_newmsg :(");
$togo=$db->num_rows;
echo "$togo records selected<br>";
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
        
/*
        // verifica
        $rhash=mysql_real_escape_string($risp[MD5]);
        $db->get_results("SELECT HASH FROM {$SNAME}_newmsg WHERE HASH='$rhash'");
        if (!$db->num_rows) { echo "<br>warning - messaggio non realmente inserito<br>";}

*/        
    // feedback
   $feedback++;
   $accepted++;
    $togo--;
    if($feedback==100) {echo " (-{$togo})<br>";$feedback=0;}
    echo "|";
    flush();
        
    } else {
        list(,$hex)=unpack("H*",$msg[HASH]);
        $cerror += "MSG con hash $hex non aggiunto perch� $risp[ERRORE]\n<br>";
        
    }
}

echo "<br>$accepted messaggi accettati dal core<br>";

$test = $db->get_var("SELECT count(*) FROM {$SNAME}_newmsg");
echo "$test messaggi nel nuovo db<br>";

// se ci sono stati errori li stampo alla fine
echo "<br>$cerror<br>";
if ($ebody) { echo "trovati $ebody messaggi con il body vuoto";}


  

// ************************************
// CONVERSIONE REPLY
// ************************************
echo "<br>Conversione reply<br>";
$feedback=0;
$accepted=0;
$cerror="";
$ebody=0;
if (! $res = $db->get_results("SELECT HASH,EDIT_OF,(HASH <> EDIT_OF) AS IS_EDIT,AUTORE,`DATE`,TITLE,BODY,REP_OF FROM {$sesorg}_reply ORDER BY HASH=EDIT_OF DESC, `DATE`",ARRAY_A) ) die ("Non riesco a fare la select in {$sesorg}_reply :(");
$togo=$db->num_rows;
echo "$togo records selected<br>";
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
        
/*
                // verifica
	        $rhash=mysql_real_escape_string($risp[MD5]);
	        $db->get_results("SELECT HASH FROM {$SNAME}_reply WHERE HASH='$rhash'");
        if (!$db->num_rows) { echo "<br>warning - messaggio non realmente inserito<br>";}
*/        

        
    // feedback
   $feedback++;
   $accepted++;

    $togo--;
    if($feedback==100) {echo " (-{$togo})<br>";$feedback=0;}
    echo "|";
    flush();        
        
    } else {
        list(,$hex)=unpack("H*",$msg[HASH]);
        $cerror +=  "reply con hash $hex non aggiunto perch� $risp[ERRORE]\n<br>";
        
    }
}

echo "<br>$accepted messaggi accettati dal core<br>";

$test = $db->get_var("SELECT count(*) FROM {$SNAME}_reply");
echo "$test messaggi nel nuovo db<br>";

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