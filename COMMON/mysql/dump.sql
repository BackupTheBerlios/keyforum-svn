-- phpMyAdmin SQL Dump
-- version 2.6.0-pl3
-- http://www.phpmyadmin.net
-- 
-- Host: localhost:33006
-- Generato il: 20 Dic, 2005 at 12:13 PM
-- 
-- Database: `keyforum`
-- 

-- --------------------------------------------------------

-- 
-- Struttura della tabella `config`
-- 

CREATE TABLE `config` (
  `MAIN_GROUP` varchar(100) collate latin1_general_ci NOT NULL default '',
  `SUBKEY` varchar(100) collate latin1_general_ci NOT NULL default '',
  `FKEY` varchar(100) collate latin1_general_ci NOT NULL default '',
  `VALUE` varchar(255) collate latin1_general_ci NOT NULL default '',
  PRIMARY KEY  (`SUBKEY`,`MAIN_GROUP`,`FKEY`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- 
-- Dump dei dati per la tabella `config`
-- 

INSERT INTO `config` VALUES ('', '', 'NTP', 'pool.ntp.org');
INSERT INTO `config` VALUES ('', '', 'TEMP_DIRECTORY', './temp');
INSERT INTO `config` VALUES ('TCP', 'BANDA_LIMITE', 'generic', '-1');
INSERT INTO `config` VALUES ('HTTP', 'CGI', 'pm', 'cpt');
INSERT INTO `config` VALUES ('HTTP', 'CGI', 'pl', 'perl\\bin\\perl.exe');
INSERT INTO `config` VALUES ('HTTP', 'CGI', 'php', 'php\\php.exe');
INSERT INTO `config` VALUES ('HTTP', 'Env', 'sql_dbname', 'KeyForum');
INSERT INTO `config` VALUES ('HTTP', 'Env', 'sql_host', '127.0.0.1');
INSERT INTO `config` VALUES ('HTTP', 'Env', 'sql_user', 'root');
INSERT INTO `config` VALUES ('HTTP', 'INDEX', '1', 'index.html');
INSERT INTO `config` VALUES ('HTTP', 'INDEX', '2', 'index.php');
INSERT INTO `config` VALUES ('HTTP', 'INDEX', '3', 'index.pl');
INSERT INTO `config` VALUES ('WEBSERVER', 'SETUP', 'DIRECTORY', 'setup');
INSERT INTO `config` VALUES ('WEBSERVER', 'SETUP', 'BIND', '127.0.0.1');
INSERT INTO `config` VALUES ('WEBSERVER', 'SETUP', 'GROUP', 'generic');
INSERT INTO `config` VALUES ('WEBSERVER', 'SETUP', 'PORTA', '20584');
INSERT INTO `config` VALUES ('SHARESERVER', 'TCP', 'GROUP', 'generic');
INSERT INTO `config` VALUES ('SHARESERVER', 'TCP', 'NICK', 'anonimo');
INSERT INTO `config` VALUES ('SHARESERVER', 'TCP', 'PORTA', '40569');
INSERT INTO `config` VALUES ('SHARESERVER', 'SOURCER', 'DEFAULT', 'http://www.keyforum.net/startup/index.php');
INSERT INTO `config` VALUES ('SHARE', 'keyfo', 'PKEY', '5bcQ8YzrwxnukP52C8zYh3nj+38B91qejmTH3ctOwpB+hGuGw2Co3X5P5CKn4Jqg1nI5DaYTZYOPQVVLthOugd0QfEXDeuXf1KHR4q3zItDL2aVpTBIx7p3puN53ve8sOPLehjmuZ0ji3cUrUe8DdNLkFrTrMILkKZhDCIxE1Ek=');
INSERT INTO `config` VALUES ('WEBSERVER', 'keyfo', 'BIND', '127.0.0.1');
INSERT INTO `config` VALUES ('WEBSERVER', 'keyfo', 'SesName', 'keyfo');
INSERT INTO `config` VALUES ('WEBSERVER', 'keyfo', 'GROUP', 'generic');
INSERT INTO `config` VALUES ('WEBSERVER', 'keyfo', 'DIRECTORY', 'default');
INSERT INTO `config` VALUES ('WEBSERVER', 'keyfo', 'PORTA', '20585');
INSERT INTO `config` VALUES ('SHARE', 'intkf', 'PKEY', 'uQYbjeIDBY+dlfyt2gI35pH/eucaTE0QWJSlE6ibtw2mxKzQEQRPbaBUj3CsWFcKUYIxG4DRiv8WIrBb63Ej2QKV2In6YeC/lQsYVIsnYouJqKqU7znxqrclFoEPLKCMnbT9JegOyrDP9U9kKdRpywg5kYPYKY60V31W/HO7zZ0=');
INSERT INTO `config` VALUES ('SHARE', 'tstkf', 'PKEY', 'uPSy1B30jgEH/fZoe3LZUzVrWYmhlbBfh0bOMJgRgcTIIJg2OGL7TGGYooZiTQTboSRuoQ4yPXiIaue5UZvCykVN7f/siIrBodMxBExnQmLdZo8iHAhCkbbtuTWiqusk8zs6sHx95jUxxwyoNnFw2vF4eL3g6Ne4WDJvR52llRs=');
INSERT INTO `config` VALUES ('WEBSERVER', 'intkf', 'BIND', '127.0.0.1');
INSERT INTO `config` VALUES ('WEBSERVER', 'intkf', 'SesName', 'intkf');
INSERT INTO `config` VALUES ('WEBSERVER', 'intkf', 'GROUP', 'generic');
INSERT INTO `config` VALUES ('WEBSERVER', 'intkf', 'DIRECTORY', 'default');
INSERT INTO `config` VALUES ('WEBSERVER', 'intkf', 'PORTA', '20586');
INSERT INTO `config` VALUES ('WEBSERVER', 'tstkf', 'BIND', '127.0.0.1');
INSERT INTO `config` VALUES ('WEBSERVER', 'tstkf', 'SesName', 'tstkf');
INSERT INTO `config` VALUES ('WEBSERVER', 'tstkf', 'GROUP', 'generic');
INSERT INTO `config` VALUES ('WEBSERVER', 'tstkf', 'DIRECTORY', 'default');
INSERT INTO `config` VALUES ('WEBSERVER', 'tstkf', 'PORTA', '20587');
INSERT INTO `config` VALUES ('SHELL', 'TCP', 'PORTA', '40565');
INSERT INTO `config` VALUES ('SHELL', 'TCP', 'BIND', '127.0.0.1');
INSERT INTO `config` VALUES ('SHELL', 'ACCESS', 'PWD', '123');
INSERT INTO `config` VALUES ('SHARE', 'tstkf', 'ID', '4ae7ed127692bdc2ec2a743419bda766e5e7bcf0');
INSERT INTO `config` VALUES ('SHARE', 'intkf', 'ID', '6f7c7ac92b9c17f69544bee96458e45c5733b9e9');
INSERT INTO `config` VALUES ('SHARE', 'keyfo', 'ID', 'b99a568cda554c315c1948db7fbfc3320d61af81');
INSERT INTO `config` VALUES ('CORE', 'ADDON', 'keyforum', 'load');
INSERT INTO `config` VALUES ('CORE', 'ADDON', 'kfshell', 'load');
INSERT INTO `config` VALUES ('CORE', 'ADDON', 'ntptime', 'load');
INSERT INTO `config` VALUES ('KEYFORUM', 'DEBUG', 'TYPE', 'mysql');
INSERT INTO `config` VALUES ('KEYFORUM', 'DEBUG', 'LEVEL', '0');
INSERT INTO `config` VALUES ('KEYFORUM', 'DEBUG', 'FILTRO', '0');

-- --------------------------------------------------------

-- 
-- Struttura della tabella `intkf_admin`
-- 

CREATE TABLE `intkf_admin` (
  `HASH` binary(16) NOT NULL,
  `TITLE` tinytext collate latin1_general_ci NOT NULL,
  `COMMAND` mediumtext collate latin1_general_ci NOT NULL,
  `TYPE` enum('3') collate latin1_general_ci NOT NULL default '3',
  `DATE` int(10) unsigned NOT NULL default '0',
  `SIGN` tinyblob NOT NULL,
  PRIMARY KEY  (`HASH`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- 
-- Dump dei dati per la tabella `intkf_admin`
-- 


-- --------------------------------------------------------

-- 
-- Struttura della tabella `intkf_conf`
-- 

CREATE TABLE `intkf_conf` (
  `GROUP` varchar(100) collate latin1_general_ci NOT NULL default '',
  `FKEY` varchar(100) collate latin1_general_ci NOT NULL default '',
  `SUBKEY` varchar(100) collate latin1_general_ci NOT NULL default '',
  `VALUE` varchar(255) collate latin1_general_ci NOT NULL default '',
  PRIMARY KEY  (`GROUP`,`FKEY`,`SUBKEY`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- 
-- Dump dei dati per la tabella `intkf_conf`
-- 


-- --------------------------------------------------------

-- 
-- Struttura della tabella `intkf_congi`
-- 

CREATE TABLE `intkf_congi` (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `HASH` binary(16) NOT NULL,
  `TYPE` enum('1','2','3','4') collate latin1_general_ci NOT NULL,
  `WRITE_DATE` int(10) unsigned NOT NULL,
  `CAN_SEND` enum('0','1') collate latin1_general_ci NOT NULL default '1',
  `SNDTIME` int(10) unsigned NOT NULL default '0',
  `INSTIME` int(10) unsigned NOT NULL default '0',
  `AUTORE` binary(16) NOT NULL,
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `HASH` (`HASH`),
  KEY `AUTORE` (`AUTORE`),
  KEY `WRITE_DATE` (`WRITE_DATE`),
  KEY `INSTIME` (`INSTIME`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;

-- 
-- Dump dei dati per la tabella `intkf_congi`
-- 


-- --------------------------------------------------------

-- 
-- Struttura della tabella `intkf_emoticons`
-- 

CREATE TABLE `intkf_emoticons` (
  `id` smallint(3) NOT NULL auto_increment,
  `typed` varchar(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `image` varchar(128) default NULL,
  `binimage` blob,
  `binimagetype` varchar(4) default NULL,
  `internal` tinyint(1) NOT NULL default '0',
  `clickable` tinyint(1) NOT NULL default '0',
  `enabled` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=248 ;

-- 
-- Dump dei dati per la tabella `intkf_emoticons`
-- 

INSERT INTO `intkf_emoticons` VALUES (1, ':mellow:', 'mellow.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (2, ':huh:', 'huh.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (3, '^_^', 'happy.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (4, ':o', 'ohmy.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (5, ';)', 'wink.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (6, ':P', 'tongue.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (7, ':D', 'biggrin.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (8, ':lol2:', 'laugh.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (9, 'B-)', 'cool.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (10, ':rolleyes:', 'rolleyes.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (11, '-_-', 'sleep.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (12, '&lt;_&lt;', 'dry.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (13, ':)', 'smile.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (14, ':wub:', 'wub.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (15, ':mad:', 'mad.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (16, ':(', 'sad.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (17, ':unsure:', 'unsure.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (18, ':wacko:', 'wacko.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (19, ':blink:', 'blink.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (20, ':ph34r:', 'ph34r.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (21, ':ambulance:', 'ambulance.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (22, ':angel:', 'angel.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (23, ':applause:', 'applause.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (24, ':artist:', 'artist.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (25, ':baby:', 'baby.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (26, ':bag:', 'bag.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (27, ':band:', 'band.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (28, ':banned:', 'banned.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (29, ':beer:', 'beer.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (30, ':beer2:', 'beer2.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (31, ':blowup:', 'blowup.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (32, ':boat:', 'boat.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (33, ':book:', 'book.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (34, ':bow:', 'bow.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (35, ':boxe:', 'boxe.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (36, ':boxing:', 'boxing.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (37, ':canadian:', 'canadian.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (38, ':censored:', 'censored.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (39, ':chair:', 'chair.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (40, ':chef:', 'chef.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (41, ':cool2:', 'cool2.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (42, ':cowboy:', 'cowboy.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (43, ':crutch:', 'crutch.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (44, ':cry:', 'cry.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (45, ':death:', 'death.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (46, ':devil:', 'devil.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (47, ':dj:', 'dj.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (48, ':drunk:', 'drunk.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (49, ':eat:', 'eat.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (50, ':farewell:', 'farewell.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (51, ':gathering:', 'gathering.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (52, ':ghost:', 'ghost.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (53, ':gossip:', 'gossip.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (54, ':graduate:', 'graduate.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (55, ':guillotine:', 'guillotine.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (56, ':guitar:', 'guitar.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (57, ':gunbandana:', 'gunbandana.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (58, ':hammerer:', 'hammerer.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (59, ':happybday:', 'happybday.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (60, ':help:', 'help.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (61, ':hmm:', 'hmm.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (62, ':hoover:', 'hoover.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (63, ':horse:', 'horse.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (64, ':king:', 'king.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (65, ':kiss:', 'kiss.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (66, ':kiss2:', 'kiss2.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (67, ':laughing:', 'laughing.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (68, ':love:', 'love.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (69, ':mad2:', 'mad2.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (70, ':mobile:', 'mobile.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (71, ':nono:', 'nono.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (72, ':nugget:', 'nugget.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (73, ':phone:', 'phone.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (74, ':photo:', 'photo.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (75, ':pizza:', 'pizza.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (76, ':punk:', 'punk.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (77, ':ranting:', 'ranting.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (78, ':rotfl:', 'rotfl.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (79, ':runaway:', 'runaway.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (80, ':sbav:', 'sbav.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (81, ':sbav2:', 'sbav2.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (82, ':scared:', 'scared.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (83, ':scooter:', 'scooter.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (84, ':secret:', 'secret.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (85, ':serenade:', 'serenade.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (86, ':shifty:', 'shifty.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (87, ':shock:', 'shock.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (88, ':sign-ban:', 'sign-ban.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (89, ':sign-dots:', 'sign-dots.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (90, ':sign-offtopic:', 'sign-offtopic.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (91, ':sign-spam:', 'sign-spam.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (92, ':sign-stupid:', 'sign-stupid.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (93, ':sleeping:', 'sleeping.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (94, ':starwars:', 'starwars.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (95, ':surrender:', 'surrender.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (96, ':terafin-grin:', 'terafin-grin.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (97, ':thumbdown:', 'thumbdown.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (98, ':thumbup:', 'thumbup.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (99, ':tomato:', 'tomato.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (100, ':tongue2:', 'tongue2.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (101, ':tooth:', 'tooth.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (102, ':tv:', 'tv.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (103, ':uh:', 'uh.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (104, ':wallbash:', 'wallbash.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (105, ':whistling:', 'whistling.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (106, ':wine:', 'wine.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (107, ':worthy:', 'worthy.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (108, ':wub2:', 'wub2.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (109, ':xmas:', 'xmas.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (110, ':yeahright:', 'yeahright.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (111, ':yes:', 'yes.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (112, ':adminpower:', 'adminpower.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (113, ':afro:', 'afro.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (114, ':angry:', 'angry.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (115, ':apple:', 'apple.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (116, ':argue:', 'argue.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (117, ':arrow:', 'arrow.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (118, ':asd:', 'asd.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (119, ':baboso:', 'baboso.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (120, ':badmood:', 'badmood.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (121, ':ban:', 'ban.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (122, ':banana:', 'banana.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (123, ':bastardinside:', 'bastardinside.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (124, ':beg:', 'beg.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (125, ':biggrin-santa:', 'biggrin-santa.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (126, ':biggrin2:', 'biggrin2.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (127, ':bleh:', 'bleh.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (128, ':blow:', 'blow.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (129, ':blush:', 'blush.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (130, ':blush2:', 'blush2.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (131, ':bond:', 'bond.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (132, ':bounce:', 'bounce.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (133, ':bustedcop:', 'bustedcop.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (134, ':bye:', 'bye.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (135, ':cheers:', 'cheers.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (136, ':cheese:', 'cheese.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (137, ':clap:', 'clap.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (138, ':closedeyes:', 'closedeyes.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (139, ':cold:', 'cold.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (140, ':console:', 'console.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (141, ':crackegg:', 'crackegg.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (142, ':crazy-santa:', 'crazy-santa.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (143, ':crybaby:', 'crybaby.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (144, ':cupid:', 'cupid.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (145, ':dance:', 'dance.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (146, ':dead:', 'dead.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (147, ':director:', 'director.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (148, ':doctor:', 'doctor.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (149, ':dribble:', 'dribble.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (150, ':drive:', 'drive.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (151, ':edonkey:', 'edonkey.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (152, ':evil:', 'evil.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (153, ':excl:', 'excl.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (154, ':fear:', 'fear.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (155, ':fight:', 'fight.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (156, ':flirt:', 'flirt.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (157, ':flower:', 'flower.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (158, ':flush:', 'flush.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (159, ':folle:', 'folle.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (160, ':fuckyou:', 'fuckyou.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (161, ':giggle:', 'giggle.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (162, ':glare:', 'glare.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (163, ':gogo:', 'gogo.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (164, ':group:', 'group.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (165, ':gun:', 'gun.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (166, ':haha:', 'haha.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (167, ':clap2:', 'clap2.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (168, ':harp:', 'harp.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (169, ':hello:', 'hello.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (170, ':hysterical:', 'hysterical.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (171, ':idea:', 'idea.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (172, ':injured:', 'injured.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (173, ':italy:', 'italy.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (174, ':jason:', 'jason.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (175, ':jawdrop:', 'jawdrop.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (176, ':jumpon:', 'jumpon.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (177, ':kicking:', 'kicking.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (178, ':kisskiss:', 'kisskiss.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (179, ':kissme-santa:', 'kissme-santa.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (180, ':laser:', 'laser.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (181, ':letto:', 'letto.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (182, ':linguaccia:', 'linguaccia.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (183, ':linux:', 'linux.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (184, ':lock:', 'lock.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (185, ':lol:', 'lol.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (186, ':lollone:', 'lollone.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (187, ':loveh:', 'loveh.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (188, ':macosx:', 'macosx.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (189, ':megalol:', 'megalol.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (190, ':mitico:', 'mitico.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (191, ':muletto:', 'muletto.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (192, ':napoleon:', 'napoleon.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (193, ':ninja:', 'ninja.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (194, ':nono2:', 'nono2.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (195, ':nyanya:', 'nyanya.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (196, ':ola:', 'ola.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (197, ':oops:', 'oops.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (198, ':pcthrow:', 'pcthrow.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (199, ':pcwhack:', 'pcwhack.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (200, ':pirate:', 'pirate.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (201, ':plane:', 'plane.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (202, ':please:', 'please.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (203, ':popcorn:', 'popcorn.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (204, ':pope:', 'pope.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (205, ':poppe:', 'poppe.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (206, ':protest:', 'protest.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (207, ':ranting2:', 'ranting2.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (208, ':rocket:', 'rocket.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (209, ':rofl:', 'rofl.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (210, ':saacrede:', 'saacrede.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (211, ':sadbye:', 'sadbye.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (212, ':scratch:', 'scratch.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (213, ':scream:', 'scream.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (214, ':senzaundente:', 'senzaundente.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (215, ':shark:', 'shark.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (216, ':shit:', 'shit.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (217, ':shrug:', 'shrug.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (218, ':smoke:', 'smoke.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (219, ':snack:', 'snack.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (220, ':sofa:', 'sofa.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (221, ':sorry:', 'sorry.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (222, ':spacecraft:', 'spacecraft.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (223, ':spam:', 'spam.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (224, ':spank:', 'spank.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (225, ':startrek:', 'startrek.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (226, ':stopspam:', 'stopspam.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (227, ':stretcher:', 'stretcher.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (228, ':sweatdrop:', 'sweatdrop.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (229, ':sweatdrop2:', 'sweatdrop2.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (230, ':swordfight:', 'swordfight.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (231, ':tease:', 'tease.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (232, ':think:', 'think.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (233, ':triste:', 'triste.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (234, ':tvhappy:', 'tvhappy.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (235, ':type:', 'type.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (236, ':urinal:', 'urinal.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (237, ':village:', 'village.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (238, ':vomit:', 'vomit.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (239, ':war:', 'war.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (240, ':welcome:', 'welcome.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (241, ':wheelchair:', 'wheelchair.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (242, ':whip:', 'whip.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (243, ':windows:', 'windows.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (244, ':worthy2:', 'worthy2.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (245, ':yeah:', 'yeah.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (246, ':zao:', 'zao.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `intkf_emoticons` VALUES (247, ':zzz:', 'zzz.gif', NULL, NULL, 0, 0, 1);

-- --------------------------------------------------------

-- 
-- Struttura della tabella `intkf_localmember`
-- 

CREATE TABLE `intkf_localmember` (
  `HASH` char(32) character set latin1 collate latin1_general_ci NOT NULL,
  `PASSWORD` mediumtext character set latin1 collate latin1_general_ci NOT NULL,
  `LANG` char(3) NOT NULL default 'eng',
  `TPP` smallint(6) NOT NULL default '20',
  `PPP` smallint(6) NOT NULL default '10',
  `HIDESIG` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`HASH`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dump dei dati per la tabella `intkf_localmember`
-- 


-- --------------------------------------------------------

-- 
-- Struttura della tabella `intkf_membri`
-- 

CREATE TABLE `intkf_membri` (
  `HASH` binary(16) NOT NULL,
  `AUTORE` varchar(30) collate latin1_general_ci default '',
  `DATE` int(10) unsigned NOT NULL default '0',
  `PKEY` tinyblob NOT NULL,
  `PKEYDEC` text collate latin1_general_ci NOT NULL,
  `AUTH` tinyblob NOT NULL,
  `TYPE` enum('4') collate latin1_general_ci NOT NULL default '4',
  `SIGN` tinyblob NOT NULL,
  `is_auth` enum('0','1') collate latin1_general_ci NOT NULL default '0',
  `firma` tinytext collate latin1_general_ci NOT NULL,
  `avatar` tinytext collate latin1_general_ci NOT NULL,
  `title` tinytext collate latin1_general_ci NOT NULL,
  `ban` enum('0','1') collate latin1_general_ci NOT NULL default '0',
  `present` enum('0','1') collate latin1_general_ci NOT NULL default '1',
  `msg_num` int(10) unsigned NOT NULL default '0',
  `edit_firma` int(10) unsigned NOT NULL default '0',
  `edit_adminset` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`HASH`),
  KEY `is_auth` (`is_auth`),
  KEY `PKEY` (`PKEY`(20))
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- 
-- Dump dei dati per la tabella `intkf_membri`
-- 


-- --------------------------------------------------------

-- 
-- Struttura della tabella `intkf_msghe`
-- 

CREATE TABLE `intkf_msghe` (
  `HASH` binary(16) NOT NULL,
  `last_reply_time` int(10) unsigned NOT NULL default '0',
  `last_reply_author` binary(16) NOT NULL default '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0',
  `reply_num` int(10) unsigned NOT NULL default '0',
  `DATE` int(10) unsigned NOT NULL default '0',
  `AUTORE` binary(16) NOT NULL default '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0',
  `read_num` int(10) unsigned NOT NULL default '0',
  `block_date` int(10) unsigned NOT NULL default '0',
  `pinned` enum('0','1') collate latin1_general_ci NOT NULL default '0',
  `last_admin_update` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`HASH`),
  KEY `last_reply_time` (`last_reply_time`),
  KEY `AUTORE` (`AUTORE`),
  KEY `last_reply_author` (`last_reply_author`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=FIXED;

-- 
-- Dump dei dati per la tabella `intkf_msghe`
-- 


-- --------------------------------------------------------

-- 
-- Struttura della tabella `intkf_newmsg`
-- 

CREATE TABLE `intkf_newmsg` (
  `HASH` binary(16) NOT NULL,
  `SEZ` int(8) unsigned NOT NULL default '0',
  `visibile` enum('0','1') collate latin1_general_ci NOT NULL default '1',
  `AUTORE` binary(16) NOT NULL default '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0',
  `EDIT_OF` binary(16) NOT NULL default '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0',
  `TYPE` enum('1') collate latin1_general_ci NOT NULL default '1',
  `DATE` int(10) unsigned NOT NULL default '0',
  `TITLE` tinytext collate latin1_general_ci NOT NULL,
  `SUBTITLE` tinytext collate latin1_general_ci NOT NULL,
  `BODY` mediumtext collate latin1_general_ci NOT NULL,
  `FIRMA` tinytext collate latin1_general_ci NOT NULL,
  `AVATAR` tinytext collate latin1_general_ci NOT NULL,
  `SIGN` tinyblob NOT NULL,
  `FOR_SIGN` tinyblob NOT NULL,
  PRIMARY KEY  (`HASH`),
  KEY `EDIT_OF` (`EDIT_OF`),
  KEY `DATE` (`DATE`),
  KEY `AUTORE` (`AUTORE`),
  KEY `SEZ` (`SEZ`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- 
-- Dump dei dati per la tabella `intkf_newmsg`
-- 


-- --------------------------------------------------------

-- 
-- Struttura della tabella `intkf_priority`
-- 

CREATE TABLE `intkf_priority` (
  `HASH` binary(16) NOT NULL,
  `PRIOR` int(10) NOT NULL default '0',
  PRIMARY KEY  (`HASH`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- 
-- Dump dei dati per la tabella `intkf_priority`
-- 


-- --------------------------------------------------------

-- 
-- Struttura della tabella `intkf_purgatorio`
-- 

CREATE TABLE `intkf_purgatorio` (
  `HASH` binary(16) NOT NULL,
  `TYPE` enum('1','2','3','4') collate latin1_general_ci NOT NULL,
  `DELETE_DATE` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`HASH`),
  KEY `DELETE_DATE` (`DELETE_DATE`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- 
-- Dump dei dati per la tabella `intkf_purgatorio`
-- 


-- --------------------------------------------------------

-- 
-- Struttura della tabella `intkf_reply`
-- 

CREATE TABLE `intkf_reply` (
  `HASH` binary(16) NOT NULL,
  `REP_OF` binary(16) NOT NULL,
  `AUTORE` binary(16) NOT NULL,
  `EDIT_OF` binary(16) NOT NULL,
  `DATE` int(10) unsigned NOT NULL default '0',
  `FIRMA` tinytext collate latin1_general_ci NOT NULL,
  `TYPE` enum('2') collate latin1_general_ci NOT NULL default '2',
  `AVATAR` tinytext collate latin1_general_ci NOT NULL,
  `TITLE` tinytext collate latin1_general_ci NOT NULL,
  `BODY` mediumtext collate latin1_general_ci NOT NULL,
  `visibile` enum('0','1') collate latin1_general_ci NOT NULL default '1',
  `SIGN` tinyblob NOT NULL,
  PRIMARY KEY  (`HASH`),
  KEY `REP_OF` (`REP_OF`),
  KEY `EDIT_OF` (`EDIT_OF`),
  KEY `DATE` (`DATE`),
  KEY `AUTORE` (`AUTORE`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- 
-- Dump dei dati per la tabella `intkf_reply`
-- 


-- --------------------------------------------------------

-- 
-- Struttura della tabella `intkf_sez`
-- 

CREATE TABLE `intkf_sez` (
  `ID` int(8) unsigned NOT NULL,
  `SEZ_NAME` varchar(250) collate latin1_general_ci default '',
  `SEZ_DESC` text collate latin1_general_ci,
  `MOD` varchar(250) collate latin1_general_ci NOT NULL default '',
  `PKEY` tinyblob NOT NULL,
  `PRKEY` tinyblob NOT NULL,
  `THR_NUM` int(8) unsigned NOT NULL default '0',
  `REPLY_NUM` int(8) unsigned NOT NULL default '0',
  `ONLY_AUTH` int(8) unsigned NOT NULL default '1',
  `AUTOFLUSH` int(10) unsigned NOT NULL default '0',
  `ORDINE` int(10) unsigned NOT NULL default '0',
  `FIGLIO` int(10) unsigned NOT NULL default '0',
  `last_admin_edit` int(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- 
-- Dump dei dati per la tabella `intkf_sez`
-- 


-- --------------------------------------------------------

-- 
-- Struttura della tabella `iplist`
-- 

CREATE TABLE `iplist` (
  `IP` int(10) unsigned NOT NULL default '0',
  `BOARD` char(40) collate latin1_general_ci NOT NULL default '',
  `TCP_PORT` mediumint(8) unsigned NOT NULL default '0',
  `UDP_PORT` mediumint(8) unsigned NOT NULL default '0',
  `CLIENT_NAME` char(20) collate latin1_general_ci NOT NULL default '',
  `CLIENT_VER` int(10) unsigned NOT NULL default '0',
  `DESC` char(100) collate latin1_general_ci NOT NULL default '',
  `TROVATO` smallint(5) unsigned NOT NULL default '0',
  `STATIC` enum('1','0') collate latin1_general_ci NOT NULL default '0',
  `FALLIMENTI` int(10) unsigned NOT NULL default '1',
  PRIMARY KEY  (`IP`,`BOARD`),
  KEY `BOARD` (`BOARD`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- 
-- Dump dei dati per la tabella `iplist`
-- 


-- --------------------------------------------------------

-- 
-- Struttura della tabella `keyfo_admin`
-- 

CREATE TABLE `keyfo_admin` (
  `HASH` binary(16) NOT NULL,
  `TITLE` tinytext collate latin1_general_ci NOT NULL,
  `COMMAND` mediumtext collate latin1_general_ci NOT NULL,
  `TYPE` enum('3') collate latin1_general_ci NOT NULL default '3',
  `DATE` int(10) unsigned NOT NULL default '0',
  `SIGN` tinyblob NOT NULL,
  PRIMARY KEY  (`HASH`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- 
-- Dump dei dati per la tabella `keyfo_admin`
-- 


-- --------------------------------------------------------

-- 
-- Struttura della tabella `keyfo_conf`
-- 

CREATE TABLE `keyfo_conf` (
  `GROUP` varchar(100) collate latin1_general_ci NOT NULL default '',
  `FKEY` varchar(100) collate latin1_general_ci NOT NULL default '',
  `SUBKEY` varchar(100) collate latin1_general_ci NOT NULL default '',
  `VALUE` varchar(255) collate latin1_general_ci NOT NULL default '',
  PRIMARY KEY  (`GROUP`,`FKEY`,`SUBKEY`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- 
-- Dump dei dati per la tabella `keyfo_conf`
-- 


-- --------------------------------------------------------

-- 
-- Struttura della tabella `keyfo_congi`
-- 

CREATE TABLE `keyfo_congi` (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `HASH` binary(16) NOT NULL,
  `TYPE` enum('1','2','3','4') collate latin1_general_ci NOT NULL,
  `WRITE_DATE` int(10) unsigned NOT NULL,
  `CAN_SEND` enum('0','1') collate latin1_general_ci NOT NULL default '1',
  `SNDTIME` int(10) unsigned NOT NULL default '0',
  `INSTIME` int(10) unsigned NOT NULL default '0',
  `AUTORE` binary(16) NOT NULL,
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `HASH` (`HASH`),
  KEY `AUTORE` (`AUTORE`),
  KEY `WRITE_DATE` (`WRITE_DATE`),
  KEY `INSTIME` (`INSTIME`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;

-- 
-- Dump dei dati per la tabella `keyfo_congi`
-- 


-- --------------------------------------------------------

-- 
-- Struttura della tabella `keyfo_emoticons`
-- 

CREATE TABLE `keyfo_emoticons` (
  `id` smallint(3) NOT NULL auto_increment,
  `typed` varchar(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `image` varchar(128) default NULL,
  `binimage` blob,
  `binimagetype` varchar(4) default NULL,
  `internal` tinyint(1) NOT NULL default '0',
  `clickable` tinyint(1) NOT NULL default '0',
  `enabled` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=248 ;

-- 
-- Dump dei dati per la tabella `keyfo_emoticons`
-- 

INSERT INTO `keyfo_emoticons` VALUES (1, ':mellow:', 'mellow.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (2, ':huh:', 'huh.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (3, '^_^', 'happy.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (4, ':o', 'ohmy.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (5, ';)', 'wink.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (6, ':P', 'tongue.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (7, ':D', 'biggrin.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (8, ':lol2:', 'laugh.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (9, 'B-)', 'cool.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (10, ':rolleyes:', 'rolleyes.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (11, '-_-', 'sleep.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (12, '&lt;_&lt;', 'dry.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (13, ':)', 'smile.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (14, ':wub:', 'wub.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (15, ':mad:', 'mad.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (16, ':(', 'sad.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (17, ':unsure:', 'unsure.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (18, ':wacko:', 'wacko.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (19, ':blink:', 'blink.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (20, ':ph34r:', 'ph34r.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (21, ':ambulance:', 'ambulance.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (22, ':angel:', 'angel.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (23, ':applause:', 'applause.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (24, ':artist:', 'artist.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (25, ':baby:', 'baby.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (26, ':bag:', 'bag.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (27, ':band:', 'band.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (28, ':banned:', 'banned.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (29, ':beer:', 'beer.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (30, ':beer2:', 'beer2.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (31, ':blowup:', 'blowup.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (32, ':boat:', 'boat.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (33, ':book:', 'book.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (34, ':bow:', 'bow.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (35, ':boxe:', 'boxe.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (36, ':boxing:', 'boxing.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (37, ':canadian:', 'canadian.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (38, ':censored:', 'censored.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (39, ':chair:', 'chair.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (40, ':chef:', 'chef.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (41, ':cool2:', 'cool2.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (42, ':cowboy:', 'cowboy.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (43, ':crutch:', 'crutch.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (44, ':cry:', 'cry.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (45, ':death:', 'death.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (46, ':devil:', 'devil.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (47, ':dj:', 'dj.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (48, ':drunk:', 'drunk.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (49, ':eat:', 'eat.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (50, ':farewell:', 'farewell.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (51, ':gathering:', 'gathering.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (52, ':ghost:', 'ghost.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (53, ':gossip:', 'gossip.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (54, ':graduate:', 'graduate.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (55, ':guillotine:', 'guillotine.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (56, ':guitar:', 'guitar.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (57, ':gunbandana:', 'gunbandana.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (58, ':hammerer:', 'hammerer.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (59, ':happybday:', 'happybday.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (60, ':help:', 'help.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (61, ':hmm:', 'hmm.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (62, ':hoover:', 'hoover.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (63, ':horse:', 'horse.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (64, ':king:', 'king.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (65, ':kiss:', 'kiss.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (66, ':kiss2:', 'kiss2.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (67, ':laughing:', 'laughing.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (68, ':love:', 'love.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (69, ':mad2:', 'mad2.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (70, ':mobile:', 'mobile.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (71, ':nono:', 'nono.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (72, ':nugget:', 'nugget.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (73, ':phone:', 'phone.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (74, ':photo:', 'photo.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (75, ':pizza:', 'pizza.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (76, ':punk:', 'punk.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (77, ':ranting:', 'ranting.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (78, ':rotfl:', 'rotfl.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (79, ':runaway:', 'runaway.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (80, ':sbav:', 'sbav.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (81, ':sbav2:', 'sbav2.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (82, ':scared:', 'scared.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (83, ':scooter:', 'scooter.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (84, ':secret:', 'secret.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (85, ':serenade:', 'serenade.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (86, ':shifty:', 'shifty.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (87, ':shock:', 'shock.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (88, ':sign-ban:', 'sign-ban.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (89, ':sign-dots:', 'sign-dots.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (90, ':sign-offtopic:', 'sign-offtopic.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (91, ':sign-spam:', 'sign-spam.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (92, ':sign-stupid:', 'sign-stupid.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (93, ':sleeping:', 'sleeping.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (94, ':starwars:', 'starwars.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (95, ':surrender:', 'surrender.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (96, ':terafin-grin:', 'terafin-grin.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (97, ':thumbdown:', 'thumbdown.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (98, ':thumbup:', 'thumbup.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (99, ':tomato:', 'tomato.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (100, ':tongue2:', 'tongue2.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (101, ':tooth:', 'tooth.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (102, ':tv:', 'tv.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (103, ':uh:', 'uh.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (104, ':wallbash:', 'wallbash.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (105, ':whistling:', 'whistling.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (106, ':wine:', 'wine.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (107, ':worthy:', 'worthy.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (108, ':wub2:', 'wub2.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (109, ':xmas:', 'xmas.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (110, ':yeahright:', 'yeahright.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (111, ':yes:', 'yes.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (112, ':adminpower:', 'adminpower.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (113, ':afro:', 'afro.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (114, ':angry:', 'angry.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (115, ':apple:', 'apple.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (116, ':argue:', 'argue.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (117, ':arrow:', 'arrow.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (118, ':asd:', 'asd.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (119, ':baboso:', 'baboso.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (120, ':badmood:', 'badmood.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (121, ':ban:', 'ban.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (122, ':banana:', 'banana.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (123, ':bastardinside:', 'bastardinside.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (124, ':beg:', 'beg.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (125, ':biggrin-santa:', 'biggrin-santa.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (126, ':biggrin2:', 'biggrin2.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (127, ':bleh:', 'bleh.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (128, ':blow:', 'blow.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (129, ':blush:', 'blush.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (130, ':blush2:', 'blush2.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (131, ':bond:', 'bond.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (132, ':bounce:', 'bounce.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (133, ':bustedcop:', 'bustedcop.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (134, ':bye:', 'bye.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (135, ':cheers:', 'cheers.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (136, ':cheese:', 'cheese.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (137, ':clap:', 'clap.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (138, ':closedeyes:', 'closedeyes.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (139, ':cold:', 'cold.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (140, ':console:', 'console.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (141, ':crackegg:', 'crackegg.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (142, ':crazy-santa:', 'crazy-santa.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (143, ':crybaby:', 'crybaby.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (144, ':cupid:', 'cupid.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (145, ':dance:', 'dance.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (146, ':dead:', 'dead.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (147, ':director:', 'director.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (148, ':doctor:', 'doctor.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (149, ':dribble:', 'dribble.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (150, ':drive:', 'drive.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (151, ':edonkey:', 'edonkey.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (152, ':evil:', 'evil.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (153, ':excl:', 'excl.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (154, ':fear:', 'fear.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (155, ':fight:', 'fight.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (156, ':flirt:', 'flirt.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (157, ':flower:', 'flower.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (158, ':flush:', 'flush.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (159, ':folle:', 'folle.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (160, ':fuckyou:', 'fuckyou.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (161, ':giggle:', 'giggle.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (162, ':glare:', 'glare.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (163, ':gogo:', 'gogo.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (164, ':group:', 'group.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (165, ':gun:', 'gun.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (166, ':haha:', 'haha.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (167, ':clap2:', 'clap2.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (168, ':harp:', 'harp.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (169, ':hello:', 'hello.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (170, ':hysterical:', 'hysterical.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (171, ':idea:', 'idea.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (172, ':injured:', 'injured.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (173, ':italy:', 'italy.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (174, ':jason:', 'jason.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (175, ':jawdrop:', 'jawdrop.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (176, ':jumpon:', 'jumpon.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (177, ':kicking:', 'kicking.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (178, ':kisskiss:', 'kisskiss.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (179, ':kissme-santa:', 'kissme-santa.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (180, ':laser:', 'laser.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (181, ':letto:', 'letto.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (182, ':linguaccia:', 'linguaccia.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (183, ':linux:', 'linux.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (184, ':lock:', 'lock.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (185, ':lol:', 'lol.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (186, ':lollone:', 'lollone.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (187, ':loveh:', 'loveh.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (188, ':macosx:', 'macosx.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (189, ':megalol:', 'megalol.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (190, ':mitico:', 'mitico.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (191, ':muletto:', 'muletto.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (192, ':napoleon:', 'napoleon.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (193, ':ninja:', 'ninja.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (194, ':nono2:', 'nono2.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (195, ':nyanya:', 'nyanya.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (196, ':ola:', 'ola.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (197, ':oops:', 'oops.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (198, ':pcthrow:', 'pcthrow.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (199, ':pcwhack:', 'pcwhack.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (200, ':pirate:', 'pirate.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (201, ':plane:', 'plane.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (202, ':please:', 'please.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (203, ':popcorn:', 'popcorn.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (204, ':pope:', 'pope.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (205, ':poppe:', 'poppe.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (206, ':protest:', 'protest.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (207, ':ranting2:', 'ranting2.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (208, ':rocket:', 'rocket.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (209, ':rofl:', 'rofl.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (210, ':saacrede:', 'saacrede.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (211, ':sadbye:', 'sadbye.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (212, ':scratch:', 'scratch.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (213, ':scream:', 'scream.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (214, ':senzaundente:', 'senzaundente.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (215, ':shark:', 'shark.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (216, ':shit:', 'shit.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (217, ':shrug:', 'shrug.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (218, ':smoke:', 'smoke.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (219, ':snack:', 'snack.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (220, ':sofa:', 'sofa.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (221, ':sorry:', 'sorry.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (222, ':spacecraft:', 'spacecraft.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (223, ':spam:', 'spam.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (224, ':spank:', 'spank.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (225, ':startrek:', 'startrek.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (226, ':stopspam:', 'stopspam.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (227, ':stretcher:', 'stretcher.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (228, ':sweatdrop:', 'sweatdrop.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (229, ':sweatdrop2:', 'sweatdrop2.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (230, ':swordfight:', 'swordfight.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (231, ':tease:', 'tease.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (232, ':think:', 'think.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (233, ':triste:', 'triste.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (234, ':tvhappy:', 'tvhappy.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (235, ':type:', 'type.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (236, ':urinal:', 'urinal.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (237, ':village:', 'village.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (238, ':vomit:', 'vomit.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (239, ':war:', 'war.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (240, ':welcome:', 'welcome.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (241, ':wheelchair:', 'wheelchair.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (242, ':whip:', 'whip.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (243, ':windows:', 'windows.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (244, ':worthy2:', 'worthy2.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (245, ':yeah:', 'yeah.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (246, ':zao:', 'zao.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `keyfo_emoticons` VALUES (247, ':zzz:', 'zzz.gif', NULL, NULL, 0, 0, 1);

-- --------------------------------------------------------

-- 
-- Struttura della tabella `keyfo_localmember`
-- 

CREATE TABLE `keyfo_localmember` (
  `HASH` char(32) character set latin1 collate latin1_general_ci NOT NULL,
  `PASSWORD` mediumtext character set latin1 collate latin1_general_ci NOT NULL,
  `LANG` char(3) NOT NULL default 'eng',
  `TPP` smallint(6) NOT NULL default '20',
  `PPP` smallint(6) NOT NULL default '10',
  `HIDESIG` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`HASH`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dump dei dati per la tabella `keyfo_localmember`
-- 


-- --------------------------------------------------------

-- 
-- Struttura della tabella `keyfo_membri`
-- 

CREATE TABLE `keyfo_membri` (
  `HASH` binary(16) NOT NULL,
  `AUTORE` varchar(30) collate latin1_general_ci default '',
  `DATE` int(10) unsigned NOT NULL default '0',
  `PKEY` tinyblob NOT NULL,
  `PKEYDEC` text collate latin1_general_ci NOT NULL,
  `AUTH` tinyblob NOT NULL,
  `TYPE` enum('4') collate latin1_general_ci NOT NULL default '4',
  `SIGN` tinyblob NOT NULL,
  `is_auth` enum('0','1') collate latin1_general_ci NOT NULL default '0',
  `firma` tinytext collate latin1_general_ci NOT NULL,
  `avatar` tinytext collate latin1_general_ci NOT NULL,
  `title` tinytext collate latin1_general_ci NOT NULL,
  `ban` enum('0','1') collate latin1_general_ci NOT NULL default '0',
  `present` enum('0','1') collate latin1_general_ci NOT NULL default '1',
  `msg_num` int(10) unsigned NOT NULL default '0',
  `edit_firma` int(10) unsigned NOT NULL default '0',
  `edit_adminset` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`HASH`),
  KEY `is_auth` (`is_auth`),
  KEY `PKEY` (`PKEY`(20))
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- 
-- Dump dei dati per la tabella `keyfo_membri`
-- 


-- --------------------------------------------------------

-- 
-- Struttura della tabella `keyfo_msghe`
-- 

CREATE TABLE `keyfo_msghe` (
  `HASH` binary(16) NOT NULL,
  `last_reply_time` int(10) unsigned NOT NULL default '0',
  `last_reply_author` binary(16) NOT NULL default '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0',
  `reply_num` int(10) unsigned NOT NULL default '0',
  `DATE` int(10) unsigned NOT NULL default '0',
  `AUTORE` binary(16) NOT NULL default '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0',
  `read_num` int(10) unsigned NOT NULL default '0',
  `block_date` int(10) unsigned NOT NULL default '0',
  `pinned` enum('0','1') collate latin1_general_ci NOT NULL default '0',
  `last_admin_update` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`HASH`),
  KEY `last_reply_time` (`last_reply_time`),
  KEY `AUTORE` (`AUTORE`),
  KEY `last_reply_author` (`last_reply_author`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=FIXED;

-- 
-- Dump dei dati per la tabella `keyfo_msghe`
-- 


-- --------------------------------------------------------

-- 
-- Struttura della tabella `keyfo_newmsg`
-- 

CREATE TABLE `keyfo_newmsg` (
  `HASH` binary(16) NOT NULL,
  `SEZ` int(8) unsigned NOT NULL default '0',
  `visibile` enum('0','1') collate latin1_general_ci NOT NULL default '1',
  `AUTORE` binary(16) NOT NULL default '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0',
  `EDIT_OF` binary(16) NOT NULL default '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0',
  `TYPE` enum('1') collate latin1_general_ci NOT NULL default '1',
  `DATE` int(10) unsigned NOT NULL default '0',
  `TITLE` tinytext collate latin1_general_ci NOT NULL,
  `SUBTITLE` tinytext collate latin1_general_ci NOT NULL,
  `BODY` mediumtext collate latin1_general_ci NOT NULL,
  `FIRMA` tinytext collate latin1_general_ci NOT NULL,
  `AVATAR` tinytext collate latin1_general_ci NOT NULL,
  `SIGN` tinyblob NOT NULL,
  `FOR_SIGN` tinyblob NOT NULL,
  PRIMARY KEY  (`HASH`),
  KEY `EDIT_OF` (`EDIT_OF`),
  KEY `DATE` (`DATE`),
  KEY `AUTORE` (`AUTORE`),
  KEY `SEZ` (`SEZ`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- 
-- Dump dei dati per la tabella `keyfo_newmsg`
-- 


-- --------------------------------------------------------

-- 
-- Struttura della tabella `keyfo_priority`
-- 

CREATE TABLE `keyfo_priority` (
  `HASH` binary(16) NOT NULL,
  `PRIOR` int(10) NOT NULL default '0',
  PRIMARY KEY  (`HASH`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- 
-- Dump dei dati per la tabella `keyfo_priority`
-- 


-- --------------------------------------------------------

-- 
-- Struttura della tabella `keyfo_purgatorio`
-- 

CREATE TABLE `keyfo_purgatorio` (
  `HASH` binary(16) NOT NULL,
  `TYPE` enum('1','2','3','4') collate latin1_general_ci NOT NULL,
  `DELETE_DATE` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`HASH`),
  KEY `DELETE_DATE` (`DELETE_DATE`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- 
-- Dump dei dati per la tabella `keyfo_purgatorio`
-- 


-- --------------------------------------------------------

-- 
-- Struttura della tabella `keyfo_reply`
-- 

CREATE TABLE `keyfo_reply` (
  `HASH` binary(16) NOT NULL,
  `REP_OF` binary(16) NOT NULL,
  `AUTORE` binary(16) NOT NULL,
  `EDIT_OF` binary(16) NOT NULL,
  `DATE` int(10) unsigned NOT NULL default '0',
  `FIRMA` tinytext collate latin1_general_ci NOT NULL,
  `TYPE` enum('2') collate latin1_general_ci NOT NULL default '2',
  `AVATAR` tinytext collate latin1_general_ci NOT NULL,
  `TITLE` tinytext collate latin1_general_ci NOT NULL,
  `BODY` mediumtext collate latin1_general_ci NOT NULL,
  `visibile` enum('0','1') collate latin1_general_ci NOT NULL default '1',
  `SIGN` tinyblob NOT NULL,
  PRIMARY KEY  (`HASH`),
  KEY `REP_OF` (`REP_OF`),
  KEY `EDIT_OF` (`EDIT_OF`),
  KEY `DATE` (`DATE`),
  KEY `AUTORE` (`AUTORE`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- 
-- Dump dei dati per la tabella `keyfo_reply`
-- 


-- --------------------------------------------------------

-- 
-- Struttura della tabella `keyfo_sez`
-- 

CREATE TABLE `keyfo_sez` (
  `ID` int(8) unsigned NOT NULL,
  `SEZ_NAME` varchar(250) collate latin1_general_ci default '',
  `SEZ_DESC` text collate latin1_general_ci,
  `MOD` varchar(250) collate latin1_general_ci NOT NULL default '',
  `PKEY` tinyblob NOT NULL,
  `PRKEY` tinyblob NOT NULL,
  `THR_NUM` int(8) unsigned NOT NULL default '0',
  `REPLY_NUM` int(8) unsigned NOT NULL default '0',
  `ONLY_AUTH` int(8) unsigned NOT NULL default '1',
  `AUTOFLUSH` int(10) unsigned NOT NULL default '0',
  `ORDINE` int(10) unsigned NOT NULL default '0',
  `FIGLIO` int(10) unsigned NOT NULL default '0',
  `last_admin_edit` int(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- 
-- Dump dei dati per la tabella `keyfo_sez`
-- 


-- --------------------------------------------------------

-- 
-- Struttura della tabella `log`
-- 

CREATE TABLE `log` (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `DATA` int(10) unsigned NOT NULL default '0',
  `LIVELLO` smallint(5) unsigned NOT NULL default '0',
  `TIPO` smallint(5) unsigned NOT NULL default '0',
  `IP` int(10) unsigned NOT NULL default '0',
  `ACT_ID` mediumint(8) unsigned NOT NULL default '0',
  `ACT_VAL` int(11) NOT NULL default '0',
  `STRINGA` char(32) collate latin1_general_ci NOT NULL default '0',
  PRIMARY KEY  (`ID`),
  KEY `DATA` (`DATA`),
  KEY `TIPO` (`TIPO`),
  KEY `LIVELLO` (`LIVELLO`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=4 ;

-- 
-- Dump dei dati per la tabella `log`
-- 

INSERT INTO `log` VALUES (1, 1131460133, 0, 0, 0, 3, 0, '');
INSERT INTO `log` VALUES (2, 1131460144, 9, 2, 178468434, 7, 5, '');
INSERT INTO `log` VALUES (3, 1131460144, 16, 2, 0, 12, 5, '');

-- --------------------------------------------------------

-- 
-- Struttura della tabella `session`
-- 

CREATE TABLE `session` (
  `SESSID` varchar(32) collate latin1_general_ci NOT NULL default '',
  `IP` varchar(32) collate latin1_general_ci NOT NULL default '',
  `FORUM` varchar(10) collate latin1_general_ci NOT NULL default '',
  `NICK` varchar(32) collate latin1_general_ci NOT NULL default '',
  `DATE` int(10) unsigned NOT NULL default '0',
  `PASSWORD` tinyblob NOT NULL,
  PRIMARY KEY  (`SESSID`,`IP`,`FORUM`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- 
-- Dump dei dati per la tabella `session`
-- 


-- --------------------------------------------------------

-- 
-- Struttura della tabella `stat`
-- 

CREATE TABLE `stat` (
  `CHIAVE1` char(50) NOT NULL default '',
  `CHIAVE2` char(50) NOT NULL default '',
  `CHIAVE3` char(50) NOT NULL default '',
  `VALORE1` double NOT NULL default '0',
  `VALORE2` double NOT NULL default '0',
  `VALORE3` double NOT NULL default '0',
  `VALORE4` double NOT NULL default '0',
  PRIMARY KEY  (`CHIAVE3`,`CHIAVE2`,`CHIAVE1`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED;

-- 
-- Dump dei dati per la tabella `stat`
-- 


-- --------------------------------------------------------

-- 
-- Struttura della tabella `temp`
-- 

CREATE TABLE `temp` (
  `CHIAVE` varchar(150) collate latin1_general_ci NOT NULL default '',
  `VALORE` text collate latin1_general_ci NOT NULL,
  `TTL` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`CHIAVE`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- 
-- Dump dei dati per la tabella `temp`
-- 


-- --------------------------------------------------------

-- 
-- Struttura della tabella `tstkf_admin`
-- 

CREATE TABLE `tstkf_admin` (
  `HASH` binary(16) NOT NULL,
  `TITLE` tinytext collate latin1_general_ci NOT NULL,
  `COMMAND` mediumtext collate latin1_general_ci NOT NULL,
  `TYPE` enum('3') collate latin1_general_ci NOT NULL default '3',
  `DATE` int(10) unsigned NOT NULL default '0',
  `SIGN` tinyblob NOT NULL,
  PRIMARY KEY  (`HASH`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- 
-- Dump dei dati per la tabella `tstkf_admin`
-- 


-- --------------------------------------------------------

-- 
-- Struttura della tabella `tstkf_conf`
-- 

CREATE TABLE `tstkf_conf` (
  `GROUP` varchar(100) collate latin1_general_ci NOT NULL default '',
  `FKEY` varchar(100) collate latin1_general_ci NOT NULL default '',
  `SUBKEY` varchar(100) collate latin1_general_ci NOT NULL default '',
  `VALUE` varchar(255) collate latin1_general_ci NOT NULL default '',
  PRIMARY KEY  (`GROUP`,`FKEY`,`SUBKEY`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- 
-- Dump dei dati per la tabella `tstkf_conf`
-- 


-- --------------------------------------------------------

-- 
-- Struttura della tabella `tstkf_congi`
-- 

CREATE TABLE `tstkf_congi` (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `HASH` binary(16) NOT NULL,
  `TYPE` enum('1','2','3','4') collate latin1_general_ci NOT NULL,
  `WRITE_DATE` int(10) unsigned NOT NULL,
  `CAN_SEND` enum('0','1') collate latin1_general_ci NOT NULL default '1',
  `SNDTIME` int(10) unsigned NOT NULL default '0',
  `INSTIME` int(10) unsigned NOT NULL default '0',
  `AUTORE` binary(16) NOT NULL,
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `HASH` (`HASH`),
  KEY `AUTORE` (`AUTORE`),
  KEY `WRITE_DATE` (`WRITE_DATE`),
  KEY `INSTIME` (`INSTIME`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;

-- 
-- Dump dei dati per la tabella `tstkf_congi`
-- 


-- --------------------------------------------------------

-- 
-- Struttura della tabella `tstkf_emoticons`
-- 

CREATE TABLE `tstkf_emoticons` (
  `id` smallint(3) NOT NULL auto_increment,
  `typed` varchar(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `image` varchar(128) default NULL,
  `binimage` blob,
  `binimagetype` varchar(4) default NULL,
  `internal` tinyint(1) NOT NULL default '0',
  `clickable` tinyint(1) NOT NULL default '0',
  `enabled` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=248 ;

-- 
-- Dump dei dati per la tabella `tstkf_emoticons`
-- 

INSERT INTO `tstkf_emoticons` VALUES (1, ':mellow:', 'mellow.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (2, ':huh:', 'huh.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (3, '^_^', 'happy.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (4, ':o', 'ohmy.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (5, ';)', 'wink.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (6, ':P', 'tongue.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (7, ':D', 'biggrin.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (8, ':lol2:', 'laugh.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (9, 'B-)', 'cool.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (10, ':rolleyes:', 'rolleyes.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (11, '-_-', 'sleep.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (12, '&lt;_&lt;', 'dry.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (13, ':)', 'smile.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (14, ':wub:', 'wub.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (15, ':mad:', 'mad.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (16, ':(', 'sad.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (17, ':unsure:', 'unsure.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (18, ':wacko:', 'wacko.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (19, ':blink:', 'blink.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (20, ':ph34r:', 'ph34r.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (21, ':ambulance:', 'ambulance.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (22, ':angel:', 'angel.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (23, ':applause:', 'applause.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (24, ':artist:', 'artist.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (25, ':baby:', 'baby.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (26, ':bag:', 'bag.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (27, ':band:', 'band.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (28, ':banned:', 'banned.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (29, ':beer:', 'beer.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (30, ':beer2:', 'beer2.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (31, ':blowup:', 'blowup.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (32, ':boat:', 'boat.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (33, ':book:', 'book.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (34, ':bow:', 'bow.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (35, ':boxe:', 'boxe.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (36, ':boxing:', 'boxing.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (37, ':canadian:', 'canadian.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (38, ':censored:', 'censored.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (39, ':chair:', 'chair.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (40, ':chef:', 'chef.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (41, ':cool2:', 'cool2.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (42, ':cowboy:', 'cowboy.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (43, ':crutch:', 'crutch.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (44, ':cry:', 'cry.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (45, ':death:', 'death.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (46, ':devil:', 'devil.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (47, ':dj:', 'dj.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (48, ':drunk:', 'drunk.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (49, ':eat:', 'eat.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (50, ':farewell:', 'farewell.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (51, ':gathering:', 'gathering.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (52, ':ghost:', 'ghost.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (53, ':gossip:', 'gossip.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (54, ':graduate:', 'graduate.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (55, ':guillotine:', 'guillotine.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (56, ':guitar:', 'guitar.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (57, ':gunbandana:', 'gunbandana.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (58, ':hammerer:', 'hammerer.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (59, ':happybday:', 'happybday.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (60, ':help:', 'help.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (61, ':hmm:', 'hmm.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (62, ':hoover:', 'hoover.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (63, ':horse:', 'horse.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (64, ':king:', 'king.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (65, ':kiss:', 'kiss.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (66, ':kiss2:', 'kiss2.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (67, ':laughing:', 'laughing.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (68, ':love:', 'love.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (69, ':mad2:', 'mad2.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (70, ':mobile:', 'mobile.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (71, ':nono:', 'nono.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (72, ':nugget:', 'nugget.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (73, ':phone:', 'phone.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (74, ':photo:', 'photo.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (75, ':pizza:', 'pizza.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (76, ':punk:', 'punk.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (77, ':ranting:', 'ranting.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (78, ':rotfl:', 'rotfl.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (79, ':runaway:', 'runaway.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (80, ':sbav:', 'sbav.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (81, ':sbav2:', 'sbav2.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (82, ':scared:', 'scared.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (83, ':scooter:', 'scooter.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (84, ':secret:', 'secret.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (85, ':serenade:', 'serenade.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (86, ':shifty:', 'shifty.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (87, ':shock:', 'shock.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (88, ':sign-ban:', 'sign-ban.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (89, ':sign-dots:', 'sign-dots.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (90, ':sign-offtopic:', 'sign-offtopic.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (91, ':sign-spam:', 'sign-spam.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (92, ':sign-stupid:', 'sign-stupid.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (93, ':sleeping:', 'sleeping.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (94, ':starwars:', 'starwars.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (95, ':surrender:', 'surrender.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (96, ':terafin-grin:', 'terafin-grin.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (97, ':thumbdown:', 'thumbdown.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (98, ':thumbup:', 'thumbup.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (99, ':tomato:', 'tomato.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (100, ':tongue2:', 'tongue2.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (101, ':tooth:', 'tooth.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (102, ':tv:', 'tv.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (103, ':uh:', 'uh.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (104, ':wallbash:', 'wallbash.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (105, ':whistling:', 'whistling.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (106, ':wine:', 'wine.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (107, ':worthy:', 'worthy.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (108, ':wub2:', 'wub2.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (109, ':xmas:', 'xmas.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (110, ':yeahright:', 'yeahright.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (111, ':yes:', 'yes.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (112, ':adminpower:', 'adminpower.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (113, ':afro:', 'afro.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (114, ':angry:', 'angry.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (115, ':apple:', 'apple.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (116, ':argue:', 'argue.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (117, ':arrow:', 'arrow.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (118, ':asd:', 'asd.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (119, ':baboso:', 'baboso.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (120, ':badmood:', 'badmood.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (121, ':ban:', 'ban.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (122, ':banana:', 'banana.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (123, ':bastardinside:', 'bastardinside.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (124, ':beg:', 'beg.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (125, ':biggrin-santa:', 'biggrin-santa.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (126, ':biggrin2:', 'biggrin2.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (127, ':bleh:', 'bleh.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (128, ':blow:', 'blow.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (129, ':blush:', 'blush.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (130, ':blush2:', 'blush2.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (131, ':bond:', 'bond.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (132, ':bounce:', 'bounce.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (133, ':bustedcop:', 'bustedcop.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (134, ':bye:', 'bye.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (135, ':cheers:', 'cheers.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (136, ':cheese:', 'cheese.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (137, ':clap:', 'clap.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (138, ':closedeyes:', 'closedeyes.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (139, ':cold:', 'cold.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (140, ':console:', 'console.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (141, ':crackegg:', 'crackegg.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (142, ':crazy-santa:', 'crazy-santa.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (143, ':crybaby:', 'crybaby.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (144, ':cupid:', 'cupid.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (145, ':dance:', 'dance.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (146, ':dead:', 'dead.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (147, ':director:', 'director.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (148, ':doctor:', 'doctor.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (149, ':dribble:', 'dribble.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (150, ':drive:', 'drive.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (151, ':edonkey:', 'edonkey.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (152, ':evil:', 'evil.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (153, ':excl:', 'excl.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (154, ':fear:', 'fear.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (155, ':fight:', 'fight.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (156, ':flirt:', 'flirt.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (157, ':flower:', 'flower.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (158, ':flush:', 'flush.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (159, ':folle:', 'folle.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (160, ':fuckyou:', 'fuckyou.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (161, ':giggle:', 'giggle.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (162, ':glare:', 'glare.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (163, ':gogo:', 'gogo.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (164, ':group:', 'group.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (165, ':gun:', 'gun.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (166, ':haha:', 'haha.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (167, ':clap2:', 'clap2.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (168, ':harp:', 'harp.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (169, ':hello:', 'hello.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (170, ':hysterical:', 'hysterical.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (171, ':idea:', 'idea.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (172, ':injured:', 'injured.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (173, ':italy:', 'italy.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (174, ':jason:', 'jason.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (175, ':jawdrop:', 'jawdrop.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (176, ':jumpon:', 'jumpon.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (177, ':kicking:', 'kicking.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (178, ':kisskiss:', 'kisskiss.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (179, ':kissme-santa:', 'kissme-santa.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (180, ':laser:', 'laser.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (181, ':letto:', 'letto.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (182, ':linguaccia:', 'linguaccia.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (183, ':linux:', 'linux.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (184, ':lock:', 'lock.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (185, ':lol:', 'lol.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (186, ':lollone:', 'lollone.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (187, ':loveh:', 'loveh.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (188, ':macosx:', 'macosx.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (189, ':megalol:', 'megalol.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (190, ':mitico:', 'mitico.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (191, ':muletto:', 'muletto.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (192, ':napoleon:', 'napoleon.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (193, ':ninja:', 'ninja.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (194, ':nono2:', 'nono2.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (195, ':nyanya:', 'nyanya.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (196, ':ola:', 'ola.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (197, ':oops:', 'oops.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (198, ':pcthrow:', 'pcthrow.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (199, ':pcwhack:', 'pcwhack.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (200, ':pirate:', 'pirate.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (201, ':plane:', 'plane.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (202, ':please:', 'please.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (203, ':popcorn:', 'popcorn.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (204, ':pope:', 'pope.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (205, ':poppe:', 'poppe.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (206, ':protest:', 'protest.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (207, ':ranting2:', 'ranting2.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (208, ':rocket:', 'rocket.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (209, ':rofl:', 'rofl.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (210, ':saacrede:', 'saacrede.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (211, ':sadbye:', 'sadbye.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (212, ':scratch:', 'scratch.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (213, ':scream:', 'scream.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (214, ':senzaundente:', 'senzaundente.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (215, ':shark:', 'shark.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (216, ':shit:', 'shit.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (217, ':shrug:', 'shrug.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (218, ':smoke:', 'smoke.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (219, ':snack:', 'snack.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (220, ':sofa:', 'sofa.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (221, ':sorry:', 'sorry.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (222, ':spacecraft:', 'spacecraft.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (223, ':spam:', 'spam.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (224, ':spank:', 'spank.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (225, ':startrek:', 'startrek.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (226, ':stopspam:', 'stopspam.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (227, ':stretcher:', 'stretcher.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (228, ':sweatdrop:', 'sweatdrop.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (229, ':sweatdrop2:', 'sweatdrop2.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (230, ':swordfight:', 'swordfight.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (231, ':tease:', 'tease.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (232, ':think:', 'think.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (233, ':triste:', 'triste.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (234, ':tvhappy:', 'tvhappy.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (235, ':type:', 'type.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (236, ':urinal:', 'urinal.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (237, ':village:', 'village.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (238, ':vomit:', 'vomit.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (239, ':war:', 'war.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (240, ':welcome:', 'welcome.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (241, ':wheelchair:', 'wheelchair.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (242, ':whip:', 'whip.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (243, ':windows:', 'windows.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (244, ':worthy2:', 'worthy2.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (245, ':yeah:', 'yeah.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (246, ':zao:', 'zao.gif', NULL, NULL, 0, 0, 1);
INSERT INTO `tstkf_emoticons` VALUES (247, ':zzz:', 'zzz.gif', NULL, NULL, 0, 0, 1);

-- --------------------------------------------------------

-- 
-- Struttura della tabella `tstkf_localmember`
-- 

CREATE TABLE `tstkf_localmember` (
  `HASH` char(32) character set latin1 collate latin1_general_ci NOT NULL,
  `PASSWORD` mediumtext character set latin1 collate latin1_general_ci NOT NULL,
  `LANG` char(3) NOT NULL default 'eng',
  `TPP` smallint(6) NOT NULL default '20',
  `PPP` smallint(6) NOT NULL default '10',
  `HIDESIG` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`HASH`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dump dei dati per la tabella `tstkf_localmember`
-- 


-- --------------------------------------------------------

-- 
-- Struttura della tabella `tstkf_membri`
-- 

CREATE TABLE `tstkf_membri` (
  `HASH` binary(16) NOT NULL,
  `AUTORE` varchar(30) collate latin1_general_ci default '',
  `DATE` int(10) unsigned NOT NULL default '0',
  `PKEY` tinyblob NOT NULL,
  `PKEYDEC` text collate latin1_general_ci NOT NULL,
  `AUTH` tinyblob NOT NULL,
  `TYPE` enum('4') collate latin1_general_ci NOT NULL default '4',
  `SIGN` tinyblob NOT NULL,
  `is_auth` enum('0','1') collate latin1_general_ci NOT NULL default '0',
  `firma` tinytext collate latin1_general_ci NOT NULL,
  `avatar` tinytext collate latin1_general_ci NOT NULL,
  `title` tinytext collate latin1_general_ci NOT NULL,
  `ban` enum('0','1') collate latin1_general_ci NOT NULL default '0',
  `present` enum('0','1') collate latin1_general_ci NOT NULL default '1',
  `msg_num` int(10) unsigned NOT NULL default '0',
  `edit_firma` int(10) unsigned NOT NULL default '0',
  `edit_adminset` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`HASH`),
  KEY `is_auth` (`is_auth`),
  KEY `PKEY` (`PKEY`(20))
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- 
-- Dump dei dati per la tabella `tstkf_membri`
-- 


-- --------------------------------------------------------

-- 
-- Struttura della tabella `tstkf_msghe`
-- 

CREATE TABLE `tstkf_msghe` (
  `HASH` binary(16) NOT NULL,
  `last_reply_time` int(10) unsigned NOT NULL default '0',
  `last_reply_author` binary(16) NOT NULL default '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0',
  `reply_num` int(10) unsigned NOT NULL default '0',
  `DATE` int(10) unsigned NOT NULL default '0',
  `AUTORE` binary(16) NOT NULL default '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0',
  `read_num` int(10) unsigned NOT NULL default '0',
  `block_date` int(10) unsigned NOT NULL default '0',
  `pinned` enum('0','1') collate latin1_general_ci NOT NULL default '0',
  `last_admin_update` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`HASH`),
  KEY `last_reply_time` (`last_reply_time`),
  KEY `AUTORE` (`AUTORE`),
  KEY `last_reply_author` (`last_reply_author`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=FIXED;

-- 
-- Dump dei dati per la tabella `tstkf_msghe`
-- 


-- --------------------------------------------------------

-- 
-- Struttura della tabella `tstkf_newmsg`
-- 

CREATE TABLE `tstkf_newmsg` (
  `HASH` binary(16) NOT NULL,
  `SEZ` int(8) unsigned NOT NULL default '0',
  `visibile` enum('0','1') collate latin1_general_ci NOT NULL default '1',
  `AUTORE` binary(16) NOT NULL default '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0',
  `EDIT_OF` binary(16) NOT NULL default '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0',
  `TYPE` enum('1') collate latin1_general_ci NOT NULL default '1',
  `DATE` int(10) unsigned NOT NULL default '0',
  `TITLE` tinytext collate latin1_general_ci NOT NULL,
  `SUBTITLE` tinytext collate latin1_general_ci NOT NULL,
  `BODY` mediumtext collate latin1_general_ci NOT NULL,
  `FIRMA` tinytext collate latin1_general_ci NOT NULL,
  `AVATAR` tinytext collate latin1_general_ci NOT NULL,
  `SIGN` tinyblob NOT NULL,
  `FOR_SIGN` tinyblob NOT NULL,
  PRIMARY KEY  (`HASH`),
  KEY `EDIT_OF` (`EDIT_OF`),
  KEY `DATE` (`DATE`),
  KEY `AUTORE` (`AUTORE`),
  KEY `SEZ` (`SEZ`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- 
-- Dump dei dati per la tabella `tstkf_newmsg`
-- 


-- --------------------------------------------------------

-- 
-- Struttura della tabella `tstkf_priority`
-- 

CREATE TABLE `tstkf_priority` (
  `HASH` binary(16) NOT NULL,
  `PRIOR` int(10) NOT NULL default '0',
  PRIMARY KEY  (`HASH`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- 
-- Dump dei dati per la tabella `tstkf_priority`
-- 


-- --------------------------------------------------------

-- 
-- Struttura della tabella `tstkf_purgatorio`
-- 

CREATE TABLE `tstkf_purgatorio` (
  `HASH` binary(16) NOT NULL,
  `TYPE` enum('1','2','3','4') collate latin1_general_ci NOT NULL,
  `DELETE_DATE` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`HASH`),
  KEY `DELETE_DATE` (`DELETE_DATE`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- 
-- Dump dei dati per la tabella `tstkf_purgatorio`
-- 


-- --------------------------------------------------------

-- 
-- Struttura della tabella `tstkf_reply`
-- 

CREATE TABLE `tstkf_reply` (
  `HASH` binary(16) NOT NULL,
  `REP_OF` binary(16) NOT NULL,
  `AUTORE` binary(16) NOT NULL,
  `EDIT_OF` binary(16) NOT NULL,
  `DATE` int(10) unsigned NOT NULL default '0',
  `FIRMA` tinytext collate latin1_general_ci NOT NULL,
  `TYPE` enum('2') collate latin1_general_ci NOT NULL default '2',
  `AVATAR` tinytext collate latin1_general_ci NOT NULL,
  `TITLE` tinytext collate latin1_general_ci NOT NULL,
  `BODY` mediumtext collate latin1_general_ci NOT NULL,
  `visibile` enum('0','1') collate latin1_general_ci NOT NULL default '1',
  `SIGN` tinyblob NOT NULL,
  PRIMARY KEY  (`HASH`),
  KEY `REP_OF` (`REP_OF`),
  KEY `EDIT_OF` (`EDIT_OF`),
  KEY `DATE` (`DATE`),
  KEY `AUTORE` (`AUTORE`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- 
-- Dump dei dati per la tabella `tstkf_reply`
-- 


-- --------------------------------------------------------

-- 
-- Struttura della tabella `tstkf_sez`
-- 

CREATE TABLE `tstkf_sez` (
  `ID` int(8) unsigned NOT NULL,
  `SEZ_NAME` varchar(250) collate latin1_general_ci default '',
  `SEZ_DESC` text collate latin1_general_ci,
  `MOD` varchar(250) collate latin1_general_ci NOT NULL default '',
  `PKEY` tinyblob NOT NULL,
  `PRKEY` tinyblob NOT NULL,
  `THR_NUM` int(8) unsigned NOT NULL default '0',
  `REPLY_NUM` int(8) unsigned NOT NULL default '0',
  `ONLY_AUTH` int(8) unsigned NOT NULL default '1',
  `AUTOFLUSH` int(10) unsigned NOT NULL default '0',
  `ORDINE` int(10) unsigned NOT NULL default '0',
  `FIGLIO` int(10) unsigned NOT NULL default '0',
  `last_admin_edit` int(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- 
-- Dump dei dati per la tabella `tstkf_sez`
-- 

        