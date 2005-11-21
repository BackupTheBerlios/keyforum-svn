<?php

include("testa.php");

$MSG_HASH=pack("H32",$_REQUEST['repof'];
$SNAME=$_ENV['sesname'];

if ( strlen($_REQUEST['edit_of']==32 ) {
    $EDIT_OF=pack("H32",$_REQUEST['edit_of'];
    if ( strlen($EDIT_OF) == 16 )
        $edit_val=1;
}

$PKEY=$std->getpkey($SNAME);

if ( strlen($PKEY) < 120 ) die("La chiave pubblica dell'admin non è valida, non posso valida il messaggio\n");

if ( !$_REQUEST['PHPSESSID'] ) die("Nessuna sessione attiva.Impossibile continuare.<br>\n");

$query_ip=unpack("H*",md5($_SERVER['REMOTE_ADDR'],TRUE));
$querysql="SELECT NICK, PASSWORD FROM session WHERE SESSID='" . $_REQUEST['PHPSESSID']
          . "' AND IP='$query_ip' AND FORUM='" . $SNAME . "';";
$sqlresult=mysql_query($querysql);
if (mysql_num_rows($sqlresult) == 0) die("<br>Non hai effettuato il login.\n");
if (mysql_num_rows($sqlresult) > 1) die("<br>Errore! Più di una sessione trovata!\n");
else {
      $resdata=mysql_fetch_array($sqlresult,MYSQL_NUM);
      $NICK=$resdata[0];
      $PASSWD=$resdata[1];
      $IDENTIFICATORE=md5($PASSWD . $NICK); 		// = md5_hex in perl
      $KEY_DECRYPT=md5($NICK . $PASSWD,TRUE);		// = md5 in perl
      $AUTH=1;
}

// GetPrivateKey
$querysql="SELECT PASSWORD FROM " . $SNAME . "_localmember WHERE HASH='" . $IDENTIFICATORE . "';";
$sqlresult=mysql_query($querysql);
if (mysql_num_rows($sqlresult) == 0) die("<br>Utente non trovato per questa board!\n");
$resdata=mysql_fetch_array($sqlresult,MYSQL_NUM);
// -------------

/* Vedi se l'utente è bannato, CanWrite in forumlib.pm
$querysql="SELECT ban FROM " . $_ENV['sesname'] . "_membri WHERE HASH='" .
*/

$querysql="SELECT ". $SNAME . "_newmsg.title as title, " . $SNAME . "_membri.AUTORE as autore"
          . " FROM ". $SNAME . "_msghe," . $SNAME . "_newmsg," . $SNAME . "_membri"
          . " WHERE " . $SNAME . "_newmsg.EDIT_OF=" . $SNAME . "_msghe.HASH"
          . " AND " . $SNAME . "_newmsg.EDIT_OF=" . $MSG_HASH . " AND " . $SNAME . "_newmsg.visibile='1'"
          . " AND " . $SNAME . "_membri.HASH=" . $SNAME . "_msghe.AUTORE";
$sqlresult=mysql_query($querysql);
if (mysql_num_rows($sqlresult) == 0) die("<br>Errore: Messaggio Non trovato!\n");

$REP_DATA['REP_OF']=$MSG_HASH;
$REP_DATA['AUTORE']=ForumLib::GetAuthorHash();
$REP_DATA['AVATAR']=$_REQUEST['avatar'];
$REP_DATA['FIRMA']=$_REQUEST['firma'];
$REP_DATA['DATE']=ForumLib::_gmtime();
$REP_DATA['TITLE']=$_REQUEST['title'];
if ( $edit_val )
    $REP_DATA['EDIT_OF']=$EDIT_OF;
$REP_DATA['BODY']=$_REQUEST['body'];
