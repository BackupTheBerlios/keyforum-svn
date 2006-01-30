<?PHP


//classe PEAR per file config (XML)
require_once "Config.php";
$xmldata = new Config;


$whereiam="migrator";
ob_start('ob_gzhandler'); 
include ("testa.php");

// I dati di origine, nel mio computer, erano su un altro database.
// Avevo due MySQL avviati nello stesso istante.
$dborig=new db('root','','keyforum','127.0.0.1:33007');

// Necessito della chiave privata dell'admin!
$PRIVKEY=base64_decode("");

// Vi ricordo che è necessario inserire le sezioni prima di fare le operazioni di migrazione!

if (! $res = $dborig->get_results("SELECT HASH,AUTORE,PKEYDEC FROM keyfo_membri WHERE IS_AUTH='1'",ARRAY_N) ) die ("Non riesco a fare la select in keyfo_membri :(");
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
    } else {
        list(,$hex)=unpack("H*",$utente[0]);
        print "Utente con hash $hex non aggiunto perchè $res[ERRORE]\n<br>";
        
    }
}

if (! $res = $dborig->get_results("SELECT HASH,EDIT_OF,(HASH <> EDIT_OF) AS IS_EDIT,AUTORE,`DATE`,TITLE,SUBTITLE,BODY,SEZ FROM keyfo_newmsg ORDER BY HASH=EDIT_OF DESC, `DATE`",ARRAY_A) ) die ("Non riesco a fare la select in keyfo_membri :(");
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
    $riga['BODY']=$msg['BODY'];
    $riga[_PRIVATE]=$PRIVKEY;
    $riga[CPSIGN]='ADMIN_SIGN';
    $risp=$core->AddMsg($riga);
    if ($risp[MD5]) {
        $db->doQuery("INSERT INTO hash_tmp (OLD_HASH,NEW_HASH) VALUES(?,?)",array($msg['HASH'],$risp[MD5]));
    } else {
        list(,$hex)=unpack("H*",$msg[HASH]);
        print "MSG con hash $hex non aggiunto perchè $risp[ERRORE]\n<br>";
        
    }
}
  


if (! $res = $dborig->get_results("SELECT HASH,EDIT_OF,(HASH <> EDIT_OF) AS IS_EDIT,AUTORE,`DATE`,TITLE,BODY,REP_OF FROM keyfo_reply ORDER BY HASH=EDIT_OF DESC, `DATE`",ARRAY_A) ) die ("Non riesco a fare la select in keyfo_membri :(");
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
    $riga['BODY']=$msg['BODY'];
    $riga[_PRIVATE]=$PRIVKEY;
    $riga[CPSIGN]='ADMIN_SIGN';
    $risp=$core->AddMsg($riga);
    if ($risp[MD5]) {
        $db->doQuery("INSERT INTO hash_tmp (OLD_HASH,NEW_HASH) VALUES(?,?)",array($msg['HASH'],$risp[MD5]));
    } else {
        list(,$hex)=unpack("H*",$msg[HASH]);
        print "reply con hash $hex non aggiunto perchè $risp[ERRORE]\n<br>";
        
    }
}

function cambia ($old_hash) {
    global $db;
    $riga=$db->get_row($db->MakeQuery("SELECT NEW_HASH FROM hash_tmp WHERE OLD_HASH=?;",array($old_hash)),ARRAY_A);
    if ($riga) return $riga[NEW_HASH];
    else return NULL;
}

#print preg_replace("/c/","kkk","ciao cioccio bello",1);
$db->MakeQuery("SELECT HASH,FIRMA FROM ME WHERE HASH=? AND NICK=?;",array("5465465456","ffs'@ me ?"));



include("end.php");
exit(0);


?>