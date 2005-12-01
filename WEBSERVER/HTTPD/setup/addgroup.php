<?PHP
ob_start('ob_gzhandler'); 
include ("lib.php");
include ("testa.php");
include ("core.php");
// le righe commentate con "funziona" significa che sono state provate
?>
<tr><td><center><h2>Aggiunta board</h2></center>
<?PHP
$core=NEW CoreSock;
$sesname=$_POST[name]; // Copio le variabili di POST in scalari normali. funza
    if (strlen($sesname) != 5) die("Il nome del forum deve essere di 5 caratteri");
if (strlen($_POST[pkey])>150) {
    $pkey_dec=$_POST[pkey];
    $forum_id=sha1($pkey_dec); // Calcolo l'ID del forum in esadecimale, funziona
    $req['FUNC']['Dec2Base64']=$pkey_dec;
    if (!$core->Send($req)) die ("Errore mentre si tentava di comunicare con il core: ".$core->errmsg);
    if (!($risp=$core->Read(6))) die ("Errore mentre si tentava di comunicare con il core: ".$core->errmsg);
    $pkey_base64=$risp['FUNC']['Dec2Base64'];
} else {
    $req[RSA][GENKEY][GEN]=1;
    $req[RSA][GENKEY][CONSOLE_OUTPUT]=1;
    if (!$core->Send($req)) die ("Errore mentre si tentava di comunicare con il core: ".$core->errmsg);
    if (!($risp=$core->Read(120))) die ("Errore mentre si tentava di comunicare con il core: ".$core->errmsg);
    $forum_id=sha1($risp[RSA][GENKEY][pub]);
    $pkey_dec=$risp[RSA][GENKEY][pub];
    print "Chiave pubblica:<textarea cols=40 rows=10>".$risp[RSA][GENKEY][pub]."</textarea><br><br>";
    print "Chiave privata:<textarea cols=40 rows=10>".$risp[RSA][GENKEY][priv]."</textarea><br><br>";
    print "Forum ID: $forum_id<br><br>\n";
    $req=NULL;
    $req['FUNC']['Dec2Base64']=$pkey_dec;
    if (!$core->Send($req)) die ("Errore mentre si tentava di comunicare con il core: ".$core->errmsg);
    if (!($risp=$core->Read(6))) die ("Errore mentre si tentava di comunicare con il core: ".$core->errmsg);
    $pkey_base64=$risp['FUNC']['Dec2Base64'];
    print "Chiave pubblica in base64<textarea>$pkey_base64</textarea><br>\n";
}
mysql_query("INSERT INTO config (MAIN_GROUP,SUBKEY,FKEY,VALUE) VALUES('SHARE','".$sesname."','PKEY','".$pkey_base64."'),('SHARE','".$sesname."','ID','".$forum_id."')") or die(mysql_error());
print "Creazione tabelle Mysql...<br>\n";
mysql_query("CREATE TABLE `".$sesname."_sez` (
  `ID` INT UNSIGNED NOT NULL,
  `SEZ_NAME` varchar(250) default '',
  `SEZ_DESC` text,
  `MOD` varchar(250) NOT NULL default '',
  `PKEY` tinyblob NOT NULL default '',
  `PRKEY` tinyblob NOT NULL default '',
  `THR_NUM` INT UNSIGNED NOT NULL default '0',
  `REPLY_NUM` INT UNSIGNED NOT NULL default '0',
  `ONLY_AUTH` INT UNSIGNED NOT NULL default '1',
  `AUTOFLUSH` INT UNSIGNED NOT NULL default '0',
  `ORDINE` INT UNSIGNED NOT NULL default '0',
  `FIGLIO` INT UNSIGNED NOT NULL default '0',
  `last_admin_edit` INT UNSIGNED NOT NULL default '0',
  PRIMARY KEY (`ID`)
) TYPE=MyISAM;") or die(mysql_error());

mysql_query("CREATE TABLE  `".$sesname."_conf` (
  `GROUP` varchar(100) NOT NULL default '',
  `FKEY` varchar(100) NOT NULL default '',
  `SUBKEY` varchar(100) NOT NULL default '',
  `VALUE` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`GROUP`,`FKEY`,`SUBKEY`)
) TYPE=MyISAM;") or die(mysql_error());
mysql_query("CREATE TABLE  `".$sesname."_congi` (
  `ID` INT(10) UNSIGNED AUTO_INCREMENT NOT NULL,
  `HASH` BINARY(16) NOT NULL UNIQUE KEY,
  `TYPE` TINYINT UNSIGNED NOT NULL,
  `WRITE_DATE` INT UNSIGNED NOT NULL,
  `CAN_SEND` enum('0','1') NOT NULL default '1',
  `INSTIME` INT UNSIGNED NOT NULL default '0',
  `AUTORE` BINARY(16) NOT NULL,
  PRIMARY KEY(`ID`),
  KEY (AUTORE),
  KEY (`WRITE_DATE`),
  KEY (`INSTIME`)
) TYPE=MyISAM ROW_FORMAT=FIXED;") or die(mysql_error());
mysql_query("CREATE TABLE  `".$sesname."_membri` (
  `HASH` BINARY(16) NOT NULL PRIMARY KEY,
  `AUTORE` varchar(30) NOT NULL default '',
  `DATE` INT UNSIGNED NOT NULL,
  `PKEY` tinyblob NOT NULL default '',
  `AUTH` tinyblob NOT NULL default '',
  `SIGN` tinyblob NOT NULL default '',
  `is_auth` enum('0','1') NOT NULL default '0',
  `firma` TEXT NOT NULL default '',
  `avatar` TINYTEXT NOT NULL default '',
  `title` varchar(100) NOT NULL default '',
  `email` varchar(100) NOT NULL default '',
  `nascita` varchar(20) NOT NULL default '',
  `provenienza` varchar(70) NOT NULL default '',
  `ban` INT UNSIGNED NOT NULL default '0',
  `present` enum('0','1') NOT NULL default '1',
  `msg_num` INT UNSIGNED NOT NULL default '0',
  `tot_msg_num` INT UNSIGNED NOT NULL default '0',
  `edit_firma` INT UNSIGNED NOT NULL default '0',
  `edit_adminset` INT UNSIGNED NOT NULL default '0',
  `EXTRA` BLOB NOT NULL default '',
  `PKEYDEC` TEXT NOT NULL,
  KEY (`is_auth`),
  KEY (PKEY(20)),
  KEY(`DATE`)
) TYPE=MyISAM;") or die(mysql_error());

mysql_query("CREATE TABLE  `".$sesname."_newmsg` (
  `HASH` BINARY(16) NOT NULL PRIMARY KEY,
  `SEZ` INT(8) UNSIGNED NOT NULL default '0',
  `visibile` enum('0','1') NOT NULL default '1',
  `AUTORE` BINARY(16) NOT NULL default '',
  `EDIT_OF` BINARY(16) NOT NULL default '',
  `DATE` INT UNSIGNED NOT NULL default '0',
  `TITLE` tinytext NOT NULL default '',
  `SUBTITLE` tinytext NOT NULL default '',
  `BODY` mediumtext NOT NULL,
  `SIGN` tinyblob NOT NULL default '',
  `FOR_SIGN` tinyblob NOT NULL default '',
  `ADMIN_SIGN` tinyblob NOT NULL default '',
  `EXTVAR` BLOB NOT NULL default '',
  KEY (`EDIT_OF`),
  KEY (`DATE`),
  KEY (`AUTORE`),
  KEY (`SEZ`)
) TYPE=MyISAM;") or die(mysql_error());
mysql_query("CREATE TABLE  `".$sesname."_msghe` (
  `HASH` BINARY(16) NOT NULL PRIMARY KEY,
  `last_reply_time` INT UNSIGNED NOT NULL,
  `last_reply_author` BINARY(16) NOT NULL,
  `reply_num` INT UNSIGNED NOT NULL default '0',
  `DATE` INT UNSIGNED NOT NULL,
  `AUTORE` BINARY(16) NOT NULL,
  `read_num` INT UNSIGNED NOT NULL default '0',
  `block_date` INT UNSIGNED NOT NULL default '0',
  `pinned` enum('0','1') NOT NULL default '0',
  `last_admin_update` INT UNSIGNED NOT NULL default '0',
  KEY (`last_reply_time`),
  KEY (`AUTORE`),
  KEY (`last_reply_author`)
) TYPE=MyISAM ROW_FORMAT=FIXED;") or die(mysql_error());


mysql_query("CREATE TABLE  `".$sesname."_reply` (
  `HASH` BINARY(16) NOT NULL PRIMARY KEY,
  `REP_OF` BINARY(16) NOT NULL,
  `AUTORE` BINARY(16) NOT NULL,
  `EDIT_OF` BINARY(16) NOT NULL,
  `DATE` INT UNSIGNED NOT NULL,
  `TITLE` tinytext NOT NULL default '',
  `BODY` mediumtext NOT NULL,
  `visibile` enum('0','1') NOT NULL default '1',
  `SIGN` tinyblob NOT NULL default '',
  `ADMIN_SIGN` TINYBLOB NOT NULL default '',
  `EXTVAR` BLOB NOT NULL default '',
  KEY (`REP_OF`),
  KEY (`EDIT_OF`),
  KEY (`DATE`),
  KEY (`AUTORE`)
) TYPE=MyISAM;") or die(mysql_error());

mysql_query("CREATE TABLE  `".$sesname."_extdati` (
  `HASH` BINARY(16) NOT NULL PRIMARY KEY,
  `AUTORE` BINARY(16) NOT NULL,
  `DATE` INT UNSIGNED NOT NULL,
  `TITLE` tinytext NOT NULL default '',
  `BODY` BLOB NOT NULL,
  `SIGN` tinyblob NOT NULL default '',
  KEY (`DATE`),
  KEY (`AUTORE`)
) TYPE=MyISAM;") or die(mysql_error());

mysql_query("CREATE TABLE  `".$sesname."_admin` (
  `HASH` BINARY(16) NOT NULL PRIMARY KEY,
  `TITLE` tinytext NOT NULL default '',
  `COMMAND` mediumtext NOT NULL,
  `DATE` INT UNSIGNED NOT NULL default '0',
  `SIGN` tinyblob NOT NULL
) TYPE=MyISAM;") or die(mysql_error());
mysql_query("CREATE TABLE  `".$sesname."_localmember` (
  `HASH` char(32) NOT NULL PRIMARY KEY,
  `PASSWORD` mediumtext NOT NULL
) TYPE=MyISAM;") or die(mysql_error());
?>
</td></tr>
</table>