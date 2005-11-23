<?php

include("testa.php");
require("lib/adder.php");

$MSG_HASH = pack("H32",$_REQUEST['repof']);
$SNAME = $_ENV['sesname'];

if ( strlen($_REQUEST['edit_of']) == 32 ) {
    $EDIT_OF = pack("H32",$_REQUEST['edit_of']);
    if ( strlen($EDIT_OF) == 16 )
        $edit_val = 1;
}

$PKEY=$std->getpkey($SNAME);

if ( strlen($PKEY) < 120 ) die("La chiave pubblica dell'admin non è valida, non posso valida il messaggio\n");

if ( !$_REQUEST['PHPSESSID'] ) die("Nessuna sessione attiva.Impossibile continuare.<br>\n");

$query_ip = md5($_SERVER[REMOTE_ADDR]);
$querysql = "SELECT NICK, PASSWORD FROM session WHERE SESSID='" . $_REQUEST['PHPSESSID']
            . "' AND IP='$query_ip' AND FORUM='" . $SNAME . "';";
$sqlresult = mysql_query($querysql);

if (mysql_num_rows($sqlresult) == 0) die("<br>Non hai effettuato il login.\n");
if (mysql_num_rows($sqlresult) > 1) die("<br>Errore! Più di una sessione trovata!\n");
else {
      $resdata = mysql_fetch_array($sqlresult,MYSQL_NUM);
      $NICK = $resdata[0];
      $PASSWD = $resdata[1];
      $IDENTIFICATORE = md5($PASSWD . $NICK); 		// = md5_hex in perl
      $KEY_DECRYPT = md5($NICK . $PASSWD,TRUE);		// = md5 in perl
      $AUTH = 1;
}

// GetPrivateKey
$querysql = "SELECT PASSWORD FROM " . $SNAME . "_localmember WHERE HASH='" . $IDENTIFICATORE . "';";
$sqlresult = mysql_query($querysql);
if (mysql_num_rows($sqlresult) == 0) die("<br>Utente non trovato per questa board!\n");
$resdata = mysql_fetch_array($sqlresult,MYSQL_NUM);
$priv_key = $resdata[0];

$coresk = new CoreSock;
$corereq[FUNC][BlowDump2var][Key] = $priv_key;
$corereq[FUNC][BlowDump2var][Data] = $KEY_DECRYPT;

if ( !($coresk->Send($corereq)) ) die("<br>Errore inviando la richiesta al core!\n");
$coreresp = $coresk->Read();
if ( !($coreresp) ) die("<br>Errore durante la ricezione della risposta dal core!\n");

$PRIVATE_DATA = $coreresp['FUNC']['BlowDump2var'];
// ricevo anche gmtime, lo salvo adesso
// si potrebbe aggiungere un controllo per vedere se ntp_sec=0
// avvisando l'utente che non si è sincronizzati con ntp
$coretime = $coreresp['CORE']['INFO']['GMT_TIME'];

if ( strlen($PRIVATE_DATA['hash']) != 16 ) die("<br>Hash dell'autore non valido!\n");
// -------------

// Vedi se l'utente è bannato, CanWrite in forumlib.pm
$querysql = "SELECT ban FROM " . $_ENV['sesname'] . "_membri WHERE HASH='" . $PRIVATE_DATA['hash'] . "';";
$sqlresult = mysql_query($querysql);
if (mysql_num_rows($sqlresult) != 0) die("<br>Utente bannato da questo forum!\n");

// Vedi se il messaggio esiste
$querysql="SELECT ". $SNAME . "_newmsg.title as title, " . $SNAME . "_membri.AUTORE as autore"
          . " FROM ". $SNAME . "_msghe," . $SNAME . "_newmsg," . $SNAME . "_membri"
          . " WHERE " . $SNAME . "_newmsg.EDIT_OF=" . $SNAME . "_msghe.HASH"
          . " AND " . $SNAME . "_newmsg.EDIT_OF=" . $MSG_HASH . " AND " . $SNAME . "_newmsg.visibile='1'"
          . " AND " . $SNAME . "_membri.HASH=" . $SNAME . "_msghe.AUTORE";
$sqlresult=mysql_query($querysql);
if (mysql_num_rows($sqlresult) == 0) die("<br>Errore: Messaggio Non trovato!\n");

if ( strlen($_REQUEST['title']) > 200 ) die("<br>Formato subject non valido\n");
if ( strlen($_REQUEST['body']) > 50000 ) die("<br>Formato body non valido\n");
if ( strlen($_REQUEST['avatar']) > 255 ) die("<br>Formato avatar non valido\n");
if ( strlen($_REQUEST['firma']) > 255 ) die("<br>Formato firma non valido\n");

// dati del messaggio, md5 calcolato su questi.
$REP_DATA['REP_OF']=$MSG_HASH;
$REP_DATA['AUTORE']=$PRIVATE_DATA['hash'];
$REP_DATA['AVATAR']=$_REQUEST['avatar'];
$REP_DATA['FIRMA']=$_REQUEST['firma'];
$REP_DATA['DATE']=$coretime;
$REP_DATA['TITLE']=$_REQUEST['title'];
if ( $edit_val )
    $REP_DATA['EDIT_OF']=$EDIT_OF;
else $REP_DATA['EDIT_OF']='';
$REP_DATA['BODY']=$_REQUEST['body'];

$domd5 = $PKEY . $REP_DATA['REP_OF'] . $REP_DATA['AUTORE'] . "2" . $REP_DATA['EDIT_OF']
               . $REP_DATA['AVATAR'] . $REP_DATA['FIRMA'] . $REP_DATA['DATE']
               . $REP_DATA['TITLE'] . $REP_DATA['BODY'];

$hash = md5($domd5,TRUE);

// richiesta firma rsa del msg al core
$corereq2['RSA']['FIRMA'][0]['md5'] = $hash;
$corereq2['RSA']['FIRMA'][0]['priv_key'] = $priv_key;
$corereq2['RSA']['FIRMA'][0]['priv_pwd'] = $KEY_DECRYPT;

if ( !($coresk->Send($corereq2)) ) die("<br>Errore inviando la richiesta al core!\n");
$coreresp2 = $coresk->Read();
if ( !$coreresp2 ) die("<br>Errore nella ricezione dal core!\m");
if ( $coreresp2['RSA']['FIRMA'][$hash] == '' ) die("<br>Errore: il core non ha firmato il messaggio!\n");

$REP_DATA['SIGN']=$coreresp2['RSA']['FIRMA'][$hash];

$adder = new ADDER($SNAME);
if ( $edit_val )
      echo "in Edit_val\n";
else {
        $adder->Congi($hash,"2",$REP_DATA['DATE'],time(),$REP_DATA['AUTORE']);
        $adder->InsertReply($hash,$REP_DATA['REP_OF'],$REP_DATA['AUTORE'],$hash,$REP_DATA['DATE'],
                      $REP_DATA['FIRMA'],$REP_DATA['AVATAR'],$REP_DATA['TITLE'],$REP_DATA['BODY'],$REP_DATA['SIGN'],'1');
        $adder->IncrementMsgNum($REP_DATA['AUTORE']);
        $adder->UpdateAvatar($REP_DATA['FIRMA'],$REP_DATA['AVATAR'],$REP_DATA['DATE'],$REP_DATA['AUTORE'],$REP_DATA['DATE']);
        $adder->IncMsghe($REP_DATA['REP_OF']);
        $adder->IncRepSez($REP_DATA['REP_OF']);
        $this->{UpDateLastTime}->execute($msg->{DATE},$msg->{AUTORE},$msg->{REP_OF}, $msg->{DATE});
        $adder->UpDateLastTime($REP_DATA['DATE'],$REP_DATA['AUTORE'],$REP_DATA['REP_OF'],$REP_DATA['DATE']);
}

// tutto ok, visualizzo pagina di redirect
$returnth=$_REQUEST['repof'];
$returnsezid=$_REQUEST['sezid'];
$returnurl="showmsg.php?SEZID=$returnsezid&THR_ID=$returnth&pag=last#end_page";

?>

<html>
  <head>
    <title>Attendi...</title>
    <meta http-equiv='refresh' content='2; url=<?php echo $returnurl; ?>' />
    <script type=\"text/javascript\"> </script>
    <style type='text/css'>
      html { overflow-x: auto; }
      BODY { font-family: Verdana, Tahoma, Arial, sans-serif;font-size: 11px;margin: 0px;padding: 0px;text-align: center;color: #000;background-color: #FFFFFF; }
      .tablefill { padding: 6px;background-color: #F5F9FD;border: 1px solid #345487; }
    </style>
  </head>
  <body>
  <table width='100%' height='85%' align='center'>
    <tr>
      <td valign='middle'>
      <table align='center' cellpadding=\"4\" class=\"tablefill\">
        <tr>
          <td width=\"100%\" align=\"center\">
            Messaggio inserito con successo<br><br>
            Attendi mentre viene caricata la pagina...<br><br>
          </td>
        </tr>
      </table>
      </td>
    </tr>
  </table>
 </body>
</html>
