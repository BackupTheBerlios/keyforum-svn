#chdir ($ENV{"DOCUMENT_ROOT"});
use strict;
use Itami::ConvData;
use Itami::forumlib;
use Itami::BinDump;
use CGI qw/:standard/;
my $SQL=ForumLib::SQL();
my $BoardName=param("name");
$BoardName=lc $BoardName;
if (length($BoardName)!=5 || $BoardName=~ /[\W\d]/) {
	print "Il nome della board non è valido. Il nome deve essere di 5 caratteri alfabetici anche scelti a caso.\n";
	exit;
}

my $sth=$SQL->prepare("SELECT count(*) WHERE `MAIN_GROUP`='SHARE' AND `SUBKEY`=?;");
$sth->execute($BoardName);
if (my $ref=$sth->fetchrow_arrayref) {
	if ($ref->[0]>0) {
		print "Esiste già una board con quel nome. Torna indietro a scegline un altro\n";
	}
}
$sth->finish;
my $pkey=param("pkey");
my ($codpriv,$key, $value);
if (length($pkey)<150) {
	unless (param("MakeNew")) {
		print "La chiave pubblica è errata\n";
		exit();
	}
	eval "use Crypt::RSA;";
	if ($@) {
		print "Non riesco a caricare la libreria RSA $@\n";
		exit(0);	
	}
	eval "use MIME::Base64;";
	if ($@) {
		print "Non riesco a caricare la libreria MIME::Base64 $@\n";
		exit(0);	
	}
	my $rsa = new Crypt::RSA;
	my ($public, $private) = $rsa->keygen (Identity  => 'io', Size => 1024) or exit print "Errore chiave rsa:".$rsa->errstr();
	$pkey=$public->{n};
	$codpriv=ForumLib::PrivateKey2Base64($private);
} else {
	if ($pkey=~ /\D/) {
		print "La chiave pubblica immessa contiene caratteri non validi. Deve essere di soli numeri.\n";	
	}
}
ForumLib::Execute(DropTable($BoardName));
unless (ForumLib::Execute(MakeQuery($BoardName))) {
	print "Alcune tabelle del forum non sono state possibili crearle. Impossibile registrarsi a questa board :(\n";
	exit;
}

if ($SQL->do("INSERT INTO `config` (`MAIN_GROUP`, `SUBKEY`, `FKEY`,`VALUE`) VALUES (?,?,?,?);",undef,"SHARE",$BoardName,"PKEY",ConvData::Dec2Base64($pkey))) {
	print "Sei registrato correttamente al forum.<br>Le modifiche saranno attive solo al riavvio di KeyForum<bR>";
	print "Per poter leggere e scrivere nel forum è necessario creare un webserver connesso a questa board.<bR>";
	print "La password privata del forum è:<bR><textarea cols=40 rows=10>$codpriv</textarea><br>Quella pubblica è:<br><textarea cols=40 rows=10>$pkey</textarea>\n" if $codpriv;
	print "<a href='webserver.php'>Vai alla creazione guidata</a>";
} else {
	print "Errore non previsto nella registrazione del forum nella lista delle board iscritte. Impossibile registrarsi\n";
	ForumLib::Execute(DropTable());
}
#ForumLib::Do("INSERT INTO `config` (`MAIN_GROUP`, `SUBKEY`, `FKEY`,`VALUE`) VALUES (?,?,?,?);",undef,"WEBSERVER",$BoardName,"BIND",param("bind") || "");
#ForumLib::Do("INSERT INTO `config` (`MAIN_GROUP`, `SUBKEY`, `FKEY`,`VALUE`) VALUES (?,?,?,?);",undef,"WEBSERVER",$BoardName,"DIRECTORY",param("directory"));
#ForumLib::Do("INSERT INTO `config` (`MAIN_GROUP`, `SUBKEY`, `FKEY`,`VALUE`) VALUES (?,?,?,?);",undef,"WEBSERVER",$BoardName,"PORTA",param("porta") || "");
#ForumLib::Do("INSERT INTO `config` (`MAIN_GROUP`, `SUBKEY`, `FKEY`,`VALUE`) VALUES (?,?,?,?);",undef,"WEBSERVER",$BoardName,"GROUP",param("gruppo") || "");
#ForumLib::Do("INSERT INTO `config` (`MAIN_GROUP`, `SUBKEY`, `FKEY`,`VALUE`) VALUES (?,?,?,?);",undef,"WEBSERVER",$BoardName,"SesName",$BoardName);


sub DropTable {
	my $BoardName=shift;
	return
	"DROP TABLE IF EXISTS `".$BoardName."_reply`;",
	"DROP TABLE IF EXISTS `".$BoardName."_msghe`;",
	"DROP TABLE IF EXISTS `".$BoardName."_newmsg`;",
	"DROP TABLE IF EXISTS `".$BoardName."_admin`;",
	"DROP TABLE IF EXISTS `".$BoardName."_membri`;",
	"DROP TABLE IF EXISTS `".$BoardName."_congi`;",
	"DROP TABLE IF EXISTS `".$BoardName."_sez`;",
	"DROP TABLE IF EXISTS `".$BoardName."_conf`;",
	"DROP TABLE IF EXISTS `".$BoardName."_localmember`;";
}
sub MakeQuery {
	my $BoardName=shift;
	
return "CREATE TABLE `".$BoardName."_sez` (
  `ID` int(8) unsigned NOT NULL,
  `SEZ_NAME` varchar(250) NOT NULL default '',
  `SEZ_DESC` text NOT NULL,
  `MOD` varchar(250) NOT NULL default '',
  `PKEY` tinyblob NOT NULL default '',
  `PRKEY` tinyblob NOT NULL default '',
  `THR_NUM` int(8) unsigned NOT NULL default '0',
  `REPLY_NUM` int(8) unsigned NOT NULL default '0',
  `ONLY_AUTH` int(8) unsigned NOT NULL default '1',
  `AUTOFLUSH` int(10) unsigned NOT NULL default '0',
  `ORDINE` int(10) unsigned NOT NULL default '0',
  `FIGLIO` int(10) unsigned NOT NULL default '0',
  `last_admin_edit` int(8) unsigned NOT NULL default '0',
  PRIMARY KEY (`ID`)
) TYPE=MyISAM;"," 

CREATE TABLE  `".$BoardName."_conf` (
  `GROUP` varchar(100) NOT NULL default '',
  `FKEY` varchar(100) NOT NULL default '',
  `SUBKEY` varchar(100) NOT NULL default '',
  `VALUE` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`GROUP`,`FKEY`,`SUBKEY`)
) TYPE=MyISAM;"," 


CREATE TABLE  `".$BoardName."_purgatorio` (
  `HASH` TINYBLOB NOT NULL,
  `TYPE` enum('1','2','3','4') NOT NULL,
  `DELETE_DATE` int(10) unsigned NOT NULL,
  PRIMARY KEY  (HASH(16)),
  KEY (`DELETE_DATE`)
) TYPE=MyISAM;",
"
CREATE TABLE  `".$BoardName."_congi` (
  `ID` INT(10) UNSIGNED AUTO_INCREMENT NOT NULL,
  `HASH` TINYBLOB NOT NULL,
  `TYPE` enum('1','2','3','4') NOT NULL,
  `WRITE_DATE` int(10) unsigned NOT NULL,
  `CAN_SEND` enum('0','1') NOT NULL default '1',
  `SNDTIME` INT UNSIGNED default '0' NOT NULL,
  `INSTIME` INT UNSIGNED default '0' NOT NULL,
  `AUTORE` TINYBLOB NOT NULL,
  PRIMARY KEY(`ID`),
  UNIQUE KEY (HASH(16)),
  KEY (AUTORE(16)),
  KEY (`WRITE_DATE`),
  KEY (`INSTIME`)
) TYPE=MyISAM;",
#  KEY `LAST_SEND` (`LAST_SEND`)
#  `LAST_SEND` int(10) NOT NULL default '-1',
" 


CREATE TABLE  `".$BoardName."_membri` (
  `HASH` TINYBLOB NOT NULL,
  `AUTORE` tinytext NOT NULL default '',
  `DATE` int(10) unsigned NOT NULL default '0',
  `PKEY` tinyblob NOT NULL default '',
  `AUTH` tinyblob NOT NULL default '',
  `TYPE` enum('4') NOT NULL default '4',
  `SIGN` tinyblob NOT NULL default '',
  `is_auth` enum('0','1') NOT NULL default '0',
  `firma` tinytext NOT NULL default '',
  `avatar` tinytext NOT NULL default '',
  `title` tinytext NOT NULL default '',
  `ban` enum('0','1') NOT NULL default '0',
  `present` enum('0','1') NOT NULL default '1',
  `msg_num` int(10) unsigned NOT NULL default '0',
  `edit_firma` int(10) unsigned NOT NULL default '0',
  `edit_adminset` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (HASH(16)),
  KEY `is_auth` (`is_auth`),
  KEY (PKEY(20))
) TYPE=MyISAM;",
#FOREIGN KEY (HASH(16)) REFERENCES `".$BoardName."_congi` (HASH) ON DELETE CASCADE
" 


CREATE TABLE  `".$BoardName."_newmsg` (
  `HASH` TINYBLOB NOT NULL,
  `SEZ` int(8) unsigned NOT NULL default '0',
  `visibile` enum('0','1') NOT NULL default '1',
  `AUTORE` TINYBLOB NOT NULL default '',
  `EDIT_OF` TINYBLOB NOT NULL default '',
  `TYPE` enum('1') NOT NULL default '1',
  `DATE` int(10) unsigned NOT NULL default '0',
  `TITLE` tinytext NOT NULL default '',
  `SUBTITLE` tinytext NOT NULL default '',
  `BODY` mediumtext NOT NULL,
  `FIRMA` tinytext NOT NULL default '',
  `AVATAR` tinytext NOT NULL default '',
  `SIGN` tinyblob NOT NULL default '',
  `FOR_SIGN` tinyblob NOT NULL default '',
  PRIMARY KEY  (HASH(16)),
  KEY `EDIT_OF` (EDIT_OF(16)),
  KEY `DATE` (`DATE`),
  KEY `AUTORE` (AUTORE(16)),
  KEY `SEZ` (`SEZ`)
) TYPE=MyISAM;",
#  FOREIGN KEY (`AUTORE`) REFERENCES `".$BoardName."_membri` (`HASH`) ON DELETE CASCADE,
#  FOREIGN KEY (`HASH`) REFERENCES `".$BoardName."_congi` (`HASH`) ON DELETE CASCADE,
#  FOREIGN KEY (`EDIT_OF`) REFERENCES `".$BoardName."_newmsg` (`HASH`) ON DELETE CASCADE,
#  FOREIGN KEY (`SEZ`) REFERENCES `".$BoardName."_sez` (`ID`)
" 

CREATE TABLE  `".$BoardName."_msghe` (
  `HASH` TINYBLOB NOT NULL,
  `last_reply_time` int(10) unsigned NOT NULL default '0',
  `last_reply_author` TINYBLOB NOT NULL default '',
  `reply_num` int(10) unsigned NOT NULL default '0',
  `DATE` int(10) unsigned NOT NULL default '0',
  `AUTORE` TINYBLOB NOT NULL default '',
  `read_num` int(10) unsigned NOT NULL default '0',
  `block_date` int(10) unsigned NOT NULL default '0',
  `pinned` enum('0','1') NOT NULL default '0',
  `last_admin_update` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (HASH(16)),
  KEY `last_reply_time` (`last_reply_time`),
  KEY `AUTORE` (AUTORE(16)),
  KEY `last_reply_author` (last_reply_author(16))
) TYPE=MyISAM ROW_FORMAT=FIXED;",
#  FOREIGN KEY (`AUTORE`) REFERENCES `".$BoardName."_membri` (`HASH`),
#  FOREIGN KEY (`last_reply_author`) REFERENCES `".$BoardName."_membri` (`HASH`),
#  FOREIGN KEY (`HASH`) REFERENCES `".$BoardName."_newmsg` (`HASH`)
"

CREATE TABLE  `".$BoardName."_reply` (
  `HASH` TINYBLOB NOT NULL,
  `REP_OF` TINYBLOB NOT NULL,
  `AUTORE` TINYBLOB NOT NULL,
  `EDIT_OF` TINYBLOB NOT NULL,
  `DATE` int(10) unsigned NOT NULL default '0',
  `FIRMA` tinytext NOT NULL default '',
  `TYPE` enum('2') NOT NULL default '2',
  `AVATAR` tinytext NOT NULL default '',
  `TITLE` tinytext NOT NULL default '',
  `BODY` mediumtext NOT NULL,
  `visibile` enum('0','1') NOT NULL default '1',
  `SIGN` tinyblob NOT NULL default '',
  PRIMARY KEY  (HASH(16)),
  KEY `REP_OF` (REP_OF(16)),
  KEY `EDIT_OF` (EDIT_OF(16)),
  KEY `DATE` (`DATE`),
  KEY `AUTORE` (AUTORE(16))
) TYPE=MyISAM;
",
#  FOREIGN KEY (`HASH`) REFERENCES `".$BoardName."_congi` (`HASH`) ON DELETE CASCADE,
#  FOREIGN KEY (`EDIT_OF`) REFERENCES `".$BoardName."_reply` (`HASH`) ON DELETE CASCADE,
#  FOREIGN KEY (`AUTORE`) REFERENCES `".$BoardName."_membri` (`HASH`) ON DELETE CASCADE,
#  FOREIGN KEY (`REP_OF`) REFERENCES `".$BoardName."_newmsg` (`HASH`) ON DELETE CASCADE

" 

CREATE TABLE  `".$BoardName."_admin` (
  `HASH` TINYBLOB NOT NULL,
  `TITLE` tinytext NOT NULL default '',
  `COMMAND` mediumtext NOT NULL,
  `TYPE` enum('3') NOT NULL default '3',
  `DATE` int(10) unsigned NOT NULL default '0',
  `SIGN` tinyblob NOT NULL default '',
  PRIMARY KEY (HASH(16))
) TYPE=MyISAM;
",
#  FOREIGN KEY (`HASH`) REFERENCES `".$BoardName."_congi` (`HASH`) ON DELETE CASCADE
"
CREATE TABLE  `".$BoardName."_localmember` (
  `HASH` char(32) NOT NULL,
  `PASSWORD` mediumtext NOT NULL,
  PRIMARY KEY (`HASH`)
) TYPE=InnoDB;","
CREATE TABLE `".$BoardName."_priority` (
  `HASH` tinyblob NOT NULL,
  `PRIOR` int(10) NOT NULL default '0',
  PRIMARY KEY  (`HASH`(16))
) TYPE=MyISAM; 



";	
}

sub Warning {
	print (shift)."\n";
}
