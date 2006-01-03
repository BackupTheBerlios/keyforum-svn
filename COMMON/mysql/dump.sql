/*
MySQL Backup
Source Host:           localhost
Source Server Version: 5.0.15
Source Database:       keyforum_base
Date:                  2006/01/03 20:18:33
*/

SET FOREIGN_KEY_CHECKS=0;
use keyforum_base;
#----------------------------
# Table structure for config
#----------------------------
CREATE TABLE `config` (
  `MAIN_GROUP` varchar(100) collate latin1_general_ci NOT NULL default '',
  `SUBKEY` varchar(100) collate latin1_general_ci NOT NULL default '',
  `FKEY` varchar(100) collate latin1_general_ci NOT NULL default '',
  `VALUE` varchar(255) collate latin1_general_ci NOT NULL default '',
  PRIMARY KEY  (`SUBKEY`,`MAIN_GROUP`,`FKEY`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
#----------------------------
# Records for table config
#----------------------------


insert  into config values 
('', '', 'NTP', 'pool.ntp.org'), 
('', '', 'TEMP_DIRECTORY', './temp'), 
('TCP', 'BANDA_LIMITE', 'generic', '-1'), 
('HTTP', 'CGI', 'pm', 'cpt'), 
('HTTP', 'CGI', 'pl', 'perl\\bin\\perl.exe'), 
('HTTP', 'CGI', 'php', 'php\\php.exe'), 
('HTTP', 'Env', 'sql_dbname', 'KeyForum'), 
('HTTP', 'Env', 'sql_host', '127.0.0.1'), 
('HTTP', 'Env', 'sql_user', 'root'), 
('HTTP', 'INDEX', '1', 'index.html'), 
('HTTP', 'INDEX', '2', 'index.php'), 
('HTTP', 'INDEX', '3', 'index.pl'), 
('WEBSERVER', 'SETUP', 'DIRECTORY', 'setup'), 
('WEBSERVER', 'SETUP', 'BIND', '127.0.0.1'), 
('WEBSERVER', 'SETUP', 'GROUP', 'generic'), 
('WEBSERVER', 'SETUP', 'PORTA', '20584'), 
('SHARESERVER', 'TCP', 'GROUP', 'generic'), 
('SHARESERVER', 'TCP', 'NICK', 'anonimo'), 
('SHARESERVER', 'TCP', 'PORTA', '40569'), 
('SHARESERVER', 'SOURCER', 'DEFAULT', 'http://www.keyforum.net/startup/index.php'), 
('SHARE', 'keyfo', 'PKEY', '5bcQ8YzrwxnukP52C8zYh3nj+38B91qejmTH3ctOwpB+hGuGw2Co3X5P5CKn4Jqg1nI5DaYTZYOPQVVLthOugd0QfEXDeuXf1KHR4q3zItDL2aVpTBIx7p3puN53ve8sOPLehjmuZ0ji3cUrUe8DdNLkFrTrMILkKZhDCIxE1Ek='), 
('WEBSERVER', 'keyfo', 'BIND', '127.0.0.1'), 
('WEBSERVER', 'keyfo', 'SesName', 'keyfo'), 
('WEBSERVER', 'keyfo', 'GROUP', 'generic'), 
('WEBSERVER', 'keyfo', 'DIRECTORY', 'default'), 
('WEBSERVER', 'keyfo', 'PORTA', '20585'), 
('SHARE', 'intkf', 'PKEY', 'uQYbjeIDBY+dlfyt2gI35pH/eucaTE0QWJSlE6ibtw2mxKzQEQRPbaBUj3CsWFcKUYIxG4DRiv8WIrBb63Ej2QKV2In6YeC/lQsYVIsnYouJqKqU7znxqrclFoEPLKCMnbT9JegOyrDP9U9kKdRpywg5kYPYKY60V31W/HO7zZ0='), 
('SHARE', 'tstkf', 'PKEY', 'uPSy1B30jgEH/fZoe3LZUzVrWYmhlbBfh0bOMJgRgcTIIJg2OGL7TGGYooZiTQTboSRuoQ4yPXiIaue5UZvCykVN7f/siIrBodMxBExnQmLdZo8iHAhCkbbtuTWiqusk8zs6sHx95jUxxwyoNnFw2vF4eL3g6Ne4WDJvR52llRs='), 
('WEBSERVER', 'intkf', 'BIND', '127.0.0.1'), 
('WEBSERVER', 'intkf', 'SesName', 'intkf'), 
('WEBSERVER', 'intkf', 'GROUP', 'generic'), 
('WEBSERVER', 'intkf', 'DIRECTORY', 'default'), 
('WEBSERVER', 'intkf', 'PORTA', '20586'), 
('WEBSERVER', 'tstkf', 'BIND', '127.0.0.1'), 
('WEBSERVER', 'tstkf', 'SesName', 'tstkf'), 
('WEBSERVER', 'tstkf', 'GROUP', 'generic'), 
('WEBSERVER', 'tstkf', 'DIRECTORY', 'default'), 
('WEBSERVER', 'tstkf', 'PORTA', '20587'), 
('SHELL', 'TCP', 'PORTA', '40565'), 
('SHELL', 'TCP', 'BIND', '127.0.0.1'), 
('SHELL', 'ACCESS', 'PWD', '123'), 
('SHARE', 'tstkf', 'ID', '4ae7ed127692bdc2ec2a743419bda766e5e7bcf0'), 
('SHARE', 'intkf', 'ID', '6f7c7ac92b9c17f69544bee96458e45c5733b9e9'), 
('SHARE', 'keyfo', 'ID', 'b99a568cda554c315c1948db7fbfc3320d61af81'), 
('CORE', 'ADDON', 'keyforum', 'load'), 
('CORE', 'ADDON', 'kfshell', 'load'), 
('CORE', 'ADDON', 'ntptime', 'load'), 
('KEYFORUM', 'DEBUG', 'TYPE', 'mysql'), 
('KEYFORUM', 'DEBUG', 'LEVEL', '0'), 
('KEYFORUM', 'DEBUG', 'FILTRO', '0');
#----------------------------
# Table structure for intkf_admin
#----------------------------
CREATE TABLE `intkf_admin` (
  `HASH` binary(16) NOT NULL,
  `TITLE` tinytext collate latin1_general_ci NOT NULL,
  `COMMAND` mediumtext collate latin1_general_ci NOT NULL,
  `TYPE` enum('3') collate latin1_general_ci NOT NULL default '3',
  `DATE` int(10) unsigned NOT NULL default '0',
  `SIGN` tinyblob NOT NULL,
  PRIMARY KEY  (`HASH`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
#----------------------------
# No records for table intkf_admin
#----------------------------

#----------------------------
# Table structure for intkf_conf
#----------------------------
CREATE TABLE `intkf_conf` (
  `GROUP` varchar(100) collate latin1_general_ci NOT NULL default '',
  `FKEY` varchar(100) collate latin1_general_ci NOT NULL default '',
  `SUBKEY` varchar(100) collate latin1_general_ci NOT NULL default '',
  `VALUE` varchar(255) collate latin1_general_ci NOT NULL default '',
  PRIMARY KEY  (`GROUP`,`FKEY`,`SUBKEY`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
#----------------------------
# No records for table intkf_conf
#----------------------------

#----------------------------
# Table structure for intkf_congi
#----------------------------
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
#----------------------------
# No records for table intkf_congi
#----------------------------

#----------------------------
# Table structure for intkf_emoticons
#----------------------------
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
#----------------------------
# Records for table intkf_emoticons
#----------------------------


insert  into intkf_emoticons values 
(1, ':mellow:', 'mellow.gif', null, 'gif', 0, 1, 1), 
(2, ':huh:', 'huh.gif', null, 'gif', 0, 1, 1), 
(3, '^_^', 'happy.gif', null, 'gif', 0, 1, 1), 
(4, ':o', 'ohmy.gif', null, 'gif', 0, 1, 1), 
(5, ';)', 'wink.gif', null, 'gif', 0, 1, 1), 
(6, ':P', 'tongue.gif', null, 'gif', 0, 1, 1), 
(7, ':D', 'biggrin.gif', null, 'gif', 0, 1, 1), 
(8, ':lol2:', 'laugh.gif', null, 'gif', 0, 1, 1), 
(9, 'B-)', 'cool.gif', null, 'gif', 0, 1, 1), 
(10, ':rolleyes:', 'rolleyes.gif', null, 'gif', 0, 1, 1), 
(11, '-_-', 'sleep.gif', null, 'gif', 0, 1, 1), 
(12, '&lt;_&lt;', 'dry.gif', null, 'gif', 0, 1, 1), 
(13, ':)', 'smile.gif', null, 'gif', 0, 1, 1), 
(14, ':wub:', 'wub.gif', null, 'gif', 0, 1, 1), 
(15, ':mad:', 'mad.gif', null, 'gif', 0, 1, 1), 
(16, ':(', 'sad.gif', null, 'gif', 0, 1, 1), 
(17, ':unsure:', 'unsure.gif', null, 'gif', 0, 1, 1), 
(18, ':wacko:', 'wacko.gif', null, 'gif', 0, 1, 1), 
(19, ':blink:', 'blink.gif', null, 'gif', 0, 1, 1), 
(20, ':ph34r:', 'ph34r.gif', null, 'gif', 0, 1, 1), 
(21, ':ambulance:', 'ambulance.gif', null, 'gif', 0, 0, 1), 
(22, ':angel:', 'angel.gif', null, 'gif', 0, 0, 1), 
(23, ':applause:', 'applause.gif', null, 'gif', 0, 0, 1), 
(24, ':artist:', 'artist.gif', null, 'gif', 0, 0, 1), 
(25, ':baby:', 'baby.gif', null, 'gif', 0, 0, 1), 
(26, ':bag:', 'bag.gif', null, 'gif', 0, 0, 1), 
(27, ':band:', 'band.gif', null, 'gif', 0, 0, 1), 
(28, ':banned:', 'banned.gif', null, 'gif', 0, 0, 1), 
(29, ':beer:', 'beer.gif', null, 'gif', 0, 0, 1), 
(30, ':beer2:', 'beer2.gif', null, 'gif', 0, 0, 1), 
(31, ':blowup:', 'blowup.gif', null, 'gif', 0, 0, 1), 
(32, ':boat:', 'boat.gif', null, 'gif', 0, 0, 1), 
(33, ':book:', 'book.gif', null, 'gif', 0, 0, 1), 
(34, ':bow:', 'bow.gif', null, 'gif', 0, 0, 1), 
(35, ':boxe:', 'boxe.gif', null, 'gif', 0, 0, 1), 
(36, ':boxing:', 'boxing.gif', null, 'gif', 0, 0, 1), 
(37, ':canadian:', 'canadian.gif', null, 'gif', 0, 0, 1), 
(38, ':censored:', 'censored.gif', null, 'gif', 0, 0, 1), 
(39, ':chair:', 'chair.gif', null, 'gif', 0, 0, 1), 
(40, ':chef:', 'chef.gif', null, 'gif', 0, 0, 1), 
(41, ':cool2:', 'cool2.gif', null, 'gif', 0, 0, 1), 
(42, ':cowboy:', 'cowboy.gif', null, 'gif', 0, 0, 1), 
(43, ':crutch:', 'crutch.gif', null, 'gif', 0, 0, 1), 
(44, ':cry:', 'cry.gif', null, 'gif', 0, 0, 1), 
(45, ':death:', 'death.gif', null, 'gif', 0, 0, 1), 
(46, ':devil:', 'devil.gif', null, 'gif', 0, 0, 1), 
(47, ':dj:', 'dj.gif', null, 'gif', 0, 0, 1), 
(48, ':drunk:', 'drunk.gif', null, 'gif', 0, 0, 1), 
(49, ':eat:', 'eat.gif', null, 'gif', 0, 0, 1), 
(50, ':farewell:', 'farewell.gif', null, 'gif', 0, 0, 1), 
(51, ':gathering:', 'gathering.gif', null, 'gif', 0, 0, 1), 
(52, ':ghost:', 'ghost.gif', null, 'gif', 0, 0, 1), 
(53, ':gossip:', 'gossip.gif', null, 'gif', 0, 0, 1), 
(54, ':graduate:', 'graduate.gif', null, 'gif', 0, 1, 1), 
(55, ':guillotine:', 'guillotine.gif', null, 'gif', 0, 0, 1), 
(56, ':guitar:', 'guitar.gif', null, 'gif', 0, 0, 1), 
(57, ':gunbandana:', 'gunbandana.gif', null, 'gif', 0, 0, 1), 
(58, ':hammerer:', 'hammerer.gif', null, 'gif', 0, 0, 1), 
(59, ':happybday:', 'happybday.gif', null, 'gif', 0, 0, 1), 
(60, ':help:', 'help.gif', null, 'gif', 0, 0, 1), 
(61, ':hmm:', 'hmm.gif', null, 'gif', 0, 0, 1), 
(62, ':hoover:', 'hoover.gif', null, 'gif', 0, 0, 1), 
(63, ':horse:', 'horse.gif', null, 'gif', 0, 0, 1), 
(64, ':king:', 'king.gif', null, 'gif', 0, 0, 1), 
(65, ':kiss:', 'kiss.gif', null, 'gif', 0, 0, 1), 
(66, ':kiss2:', 'kiss2.gif', null, 'gif', 0, 0, 1), 
(67, ':laughing:', 'laughing.gif', null, 'gif', 0, 0, 1), 
(68, ':love:', 'love.gif', null, 'gif', 0, 0, 1), 
(69, ':mad2:', 'mad2.gif', null, 'gif', 0, 0, 1), 
(70, ':mobile:', 'mobile.gif', null, 'gif', 0, 0, 1), 
(71, ':nono:', 'nono.gif', null, 'gif', 0, 0, 1), 
(72, ':nugget:', 'nugget.gif', null, 'gif', 0, 0, 1), 
(73, ':phone:', 'phone.gif', null, 'gif', 0, 0, 1), 
(74, ':photo:', 'photo.gif', null, 'gif', 0, 0, 1), 
(75, ':pizza:', 'pizza.gif', null, 'gif', 0, 0, 1), 
(76, ':punk:', 'punk.gif', null, 'gif', 0, 0, 1), 
(77, ':ranting:', 'ranting.gif', null, 'gif', 0, 0, 1), 
(78, ':rotfl:', 'rotfl.gif', null, 'gif', 0, 1, 1), 
(79, ':runaway:', 'runaway.gif', null, 'gif', 0, 0, 1), 
(80, ':sbav:', 'sbav.gif', null, 'gif', 0, 0, 1), 
(81, ':sbav2:', 'sbav2.gif', null, 'gif', 0, 0, 1), 
(82, ':scared:', 'scared.gif', null, 'gif', 0, 0, 1), 
(83, ':scooter:', 'scooter.gif', null, 'gif', 0, 0, 1), 
(84, ':secret:', 'secret.gif', null, 'gif', 0, 0, 1), 
(85, ':serenade:', 'serenade.gif', null, 'gif', 0, 0, 1), 
(86, ':shifty:', 'shifty.gif', null, 'gif', 0, 0, 1), 
(87, ':shock:', 'shock.gif', null, 'gif', 0, 0, 1), 
(88, ':sign-ban:', 'sign-ban.gif', null, 'gif', 0, 0, 1), 
(89, ':sign-dots:', 'sign-dots.gif', null, 'gif', 0, 0, 1), 
(90, ':sign-offtopic:', 'sign-offtopic.gif', null, 'gif', 0, 0, 1), 
(91, ':sign-spam:', 'sign-spam.gif', null, 'gif', 0, 0, 1), 
(92, ':sign-stupid:', 'sign-stupid.gif', null, 'gif', 0, 0, 1), 
(93, ':sleeping:', 'sleeping.gif', null, 'gif', 0, 0, 1), 
(94, ':starwars:', 'starwars.gif', null, 'gif', 0, 0, 1), 
(95, ':surrender:', 'surrender.gif', null, 'gif', 0, 0, 1), 
(96, ':terafin-grin:', 'terafin-grin.gif', null, 'gif', 0, 0, 1), 
(97, ':thumbdown:', 'thumbdown.gif', null, 'gif', 0, 0, 1), 
(98, ':thumbup:', 'thumbup.gif', null, 'gif', 0, 0, 1), 
(99, ':tomato:', 'tomato.gif', null, 'gif', 0, 0, 1), 
(100, ':tongue2:', 'tongue2.gif', null, 'gif', 0, 1, 1), 
(101, ':tooth:', 'tooth.gif', null, 'gif', 0, 0, 1), 
(102, ':tv:', 'tv.gif', null, 'gif', 0, 0, 1), 
(103, ':uh:', 'uh.gif', null, 'gif', 0, 0, 1), 
(104, ':wallbash:', 'wallbash.gif', null, 'gif', 0, 0, 1), 
(105, ':whistling:', 'whistling.gif', null, 'gif', 0, 0, 1), 
(106, ':wine:', 'wine.gif', null, 'gif', 0, 0, 1), 
(107, ':worthy:', 'worthy.gif', null, 'gif', 0, 0, 1), 
(108, ':wub2:', 'wub2.gif', null, 'gif', 0, 0, 1), 
(109, ':xmas:', 'xmas.gif', null, 'gif', 0, 0, 1), 
(110, ':yeahright:', 'yeahright.gif', null, 'gif', 0, 0, 1), 
(111, ':yes:', 'yes.gif', null, 'gif', 0, 0, 1), 
(112, ':adminpower:', 'adminpower.gif', null, 'gif', 0, 0, 1), 
(113, ':afro:', 'afro.gif', null, 'gif', 0, 0, 1), 
(114, ':angry:', 'angry.gif', null, 'gif', 0, 0, 1), 
(115, ':apple:', 'apple.gif', null, 'gif', 0, 0, 1), 
(116, ':argue:', 'argue.gif', null, 'gif', 0, 0, 1), 
(117, ':arrow:', 'arrow.gif', null, 'gif', 0, 0, 1), 
(118, ':asd:', 'asd.gif', null, 'gif', 0, 0, 1), 
(119, ':baboso:', 'baboso.gif', null, 'gif', 0, 0, 1), 
(120, ':badmood:', 'badmood.gif', null, 'gif', 0, 0, 1), 
(121, ':ban:', 'ban.gif', null, 'gif', 0, 0, 1), 
(122, ':banana:', 'banana.gif', null, 'gif', 0, 0, 1), 
(123, ':bastardinside:', 'bastardinside.gif', null, 'gif', 0, 0, 1), 
(124, ':beg:', 'beg.gif', null, 'gif', 0, 0, 1), 
(125, ':biggrin-santa:', 'biggrin-santa.gif', null, 'gif', 0, 0, 1), 
(126, ':biggrin2:', 'biggrin2.gif', null, 'gif', 0, 0, 1), 
(127, ':bleh:', 'bleh.gif', null, 'gif', 0, 0, 1), 
(128, ':blow:', 'blow.gif', null, 'gif', 0, 0, 1), 
(129, ':blush:', 'blush.gif', null, 'gif', 0, 0, 1), 
(130, ':blush2:', 'blush2.gif', null, 'gif', 0, 0, 1), 
(131, ':bond:', 'bond.gif', null, 'gif', 0, 0, 1), 
(132, ':bounce:', 'bounce.gif', null, 'gif', 0, 0, 1), 
(133, ':bustedcop:', 'bustedcop.gif', null, 'gif', 0, 0, 1), 
(134, ':bye:', 'bye.gif', null, 'gif', 0, 0, 1), 
(135, ':cheers:', 'cheers.gif', null, 'gif', 0, 0, 1), 
(136, ':cheese:', 'cheese.gif', null, 'gif', 0, 0, 1), 
(137, ':clap:', 'clap.gif', null, 'gif', 0, 0, 1), 
(138, ':closedeyes:', 'closedeyes.gif', null, 'gif', 0, 0, 1), 
(139, ':cold:', 'cold.gif', null, 'gif', 0, 0, 1), 
(140, ':console:', 'console.gif', null, 'gif', 0, 0, 1), 
(141, ':crackegg:', 'crackegg.gif', null, 'gif', 0, 0, 1), 
(142, ':crazy-santa:', 'crazy-santa.gif', null, 'gif', 0, 0, 1), 
(143, ':crybaby:', 'crybaby.gif', null, 'gif', 0, 0, 1), 
(144, ':cupid:', 'cupid.gif', null, 'gif', 0, 0, 1), 
(145, ':dance:', 'dance.gif', null, 'gif', 0, 0, 1), 
(146, ':dead:', 'dead.gif', null, 'gif', 0, 0, 1), 
(147, ':director:', 'director.gif', null, 'gif', 0, 0, 1), 
(148, ':doctor:', 'doctor.gif', null, 'gif', 0, 0, 1), 
(149, ':dribble:', 'dribble.gif', null, 'gif', 0, 0, 1), 
(150, ':drive:', 'drive.gif', null, 'gif', 0, 0, 1), 
(151, ':edonkey:', 'edonkey.gif', null, 'gif', 0, 0, 1), 
(152, ':evil:', 'evil.gif', null, 'gif', 0, 0, 1), 
(153, ':excl:', 'excl.gif', null, 'gif', 0, 0, 1), 
(154, ':fear:', 'fear.gif', null, 'gif', 0, 0, 1), 
(155, ':fight:', 'fight.gif', null, 'gif', 0, 0, 1), 
(156, ':flirt:', 'flirt.gif', null, 'gif', 0, 0, 1), 
(157, ':flower:', 'flower.gif', null, 'gif', 0, 0, 1), 
(158, ':flush:', 'flush.gif', null, 'gif', 0, 0, 1), 
(159, ':folle:', 'folle.gif', null, 'gif', 0, 0, 1), 
(160, ':fuckyou:', 'fuckyou.gif', null, 'gif', 0, 0, 1), 
(161, ':giggle:', 'giggle.gif', null, 'gif', 0, 0, 1), 
(162, ':glare:', 'glare.gif', null, 'gif', 0, 0, 1), 
(163, ':gogo:', 'gogo.gif', null, 'gif', 0, 0, 1), 
(164, ':group:', 'group.gif', null, 'gif', 0, 0, 1), 
(165, ':gun:', 'gun.gif', null, 'gif', 0, 0, 1), 
(166, ':haha:', 'haha.gif', null, 'gif', 0, 0, 1), 
(167, ':clap2:', 'clap2.gif', null, 'gif', 0, 0, 1), 
(168, ':harp:', 'harp.gif', null, 'gif', 0, 0, 1), 
(169, ':hello:', 'hello.gif', null, 'gif', 0, 0, 1), 
(170, ':hysterical:', 'hysterical.gif', null, 'gif', 0, 0, 1), 
(171, ':idea:', 'idea.gif', null, 'gif', 0, 0, 1), 
(172, ':injured:', 'injured.gif', null, 'gif', 0, 0, 1), 
(173, ':italy:', 'italy.gif', null, 'gif', 0, 0, 1), 
(174, ':jason:', 'jason.gif', null, 'gif', 0, 0, 1), 
(175, ':jawdrop:', 'jawdrop.gif', null, 'gif', 0, 0, 1), 
(176, ':jumpon:', 'jumpon.gif', null, 'gif', 0, 0, 1), 
(177, ':kicking:', 'kicking.gif', null, 'gif', 0, 0, 1), 
(178, ':kisskiss:', 'kisskiss.gif', null, 'gif', 0, 0, 1), 
(179, ':kissme-santa:', 'kissme-santa.gif', null, 'gif', 0, 0, 1), 
(180, ':laser:', 'laser.gif', null, 'gif', 0, 0, 1), 
(181, ':letto:', 'letto.gif', null, 'gif', 0, 0, 1), 
(182, ':linguaccia:', 'linguaccia.gif', null, 'gif', 0, 0, 1), 
(183, ':linux:', 'linux.gif', null, 'gif', 0, 0, 1), 
(184, ':lock:', 'lock.gif', null, 'gif', 0, 0, 1), 
(185, ':lol:', 'lol.gif', null, 'gif', 0, 0, 1), 
(186, ':lollone:', 'lollone.gif', null, 'gif', 0, 0, 1), 
(187, ':loveh:', 'loveh.gif', null, 'gif', 0, 0, 1), 
(188, ':macosx:', 'macosx.gif', null, 'gif', 0, 0, 1), 
(189, ':megalol:', 'megalol.gif', null, 'gif', 0, 0, 1), 
(190, ':mitico:', 'mitico.gif', null, 'gif', 0, 0, 1), 
(191, ':muletto:', 'muletto.gif', null, 'gif', 0, 0, 1), 
(192, ':napoleon:', 'napoleon.gif', null, 'gif', 0, 0, 1), 
(193, ':ninja:', 'ninja.gif', null, 'gif', 0, 0, 1), 
(194, ':nono2:', 'nono2.gif', null, 'gif', 0, 0, 1), 
(195, ':nyanya:', 'nyanya.gif', null, 'gif', 0, 0, 1), 
(196, ':ola:', 'ola.gif', null, 'gif', 0, 0, 1), 
(197, ':oops:', 'oops.gif', null, 'gif', 0, 0, 1), 
(198, ':pcthrow:', 'pcthrow.gif', null, 'gif', 0, 0, 1), 
(199, ':pcwhack:', 'pcwhack.gif', null, 'gif', 0, 0, 1), 
(200, ':pirate:', 'pirate.gif', null, 'gif', 0, 0, 1), 
(201, ':plane:', 'plane.gif', null, 'gif', 0, 0, 1), 
(202, ':please:', 'please.gif', null, 'gif', 0, 0, 1), 
(203, ':popcorn:', 'popcorn.gif', null, 'gif', 0, 0, 1), 
(204, ':pope:', 'pope.gif', null, 'gif', 0, 0, 1), 
(205, ':poppe:', 'poppe.gif', null, 'gif', 0, 0, 1), 
(206, ':protest:', 'protest.gif', null, 'gif', 0, 0, 1), 
(207, ':ranting2:', 'ranting2.gif', null, 'gif', 0, 0, 1), 
(208, ':rocket:', 'rocket.gif', null, 'gif', 0, 0, 1), 
(209, ':rofl:', 'rofl.gif', null, 'gif', 0, 0, 1), 
(210, ':saacrede:', 'saacrede.gif', null, 'gif', 0, 0, 1), 
(211, ':sadbye:', 'sadbye.gif', null, 'gif', 0, 0, 1), 
(212, ':scratch:', 'scratch.gif', null, 'gif', 0, 0, 1), 
(213, ':scream:', 'scream.gif', null, 'gif', 0, 0, 1), 
(214, ':senzaundente:', 'senzaundente.gif', null, 'gif', 0, 0, 1), 
(215, ':shark:', 'shark.gif', null, 'gif', 0, 0, 1), 
(216, ':shit:', 'shit.gif', null, 'gif', 0, 0, 1), 
(217, ':shrug:', 'shrug.gif', null, 'gif', 0, 0, 1), 
(218, ':smoke:', 'smoke.gif', null, 'gif', 0, 0, 1), 
(219, ':snack:', 'snack.gif', null, 'gif', 0, 0, 1), 
(220, ':sofa:', 'sofa.gif', null, 'gif', 0, 0, 1), 
(221, ':sorry:', 'sorry.gif', null, 'gif', 0, 0, 1), 
(222, ':spacecraft:', 'spacecraft.gif', null, 'gif', 0, 0, 1), 
(223, ':spam:', 'spam.gif', null, 'gif', 0, 0, 1), 
(224, ':spank:', 'spank.gif', null, 'gif', 0, 0, 1), 
(225, ':startrek:', 'startrek.gif', null, 'gif', 0, 0, 1), 
(226, ':stopspam:', 'stopspam.gif', null, 'gif', 0, 0, 1), 
(227, ':stretcher:', 'stretcher.gif', null, 'gif', 0, 0, 1), 
(228, ':sweatdrop:', 'sweatdrop.gif', null, 'gif', 0, 0, 1), 
(229, ':sweatdrop2:', 'sweatdrop2.gif', null, 'gif', 0, 0, 1), 
(230, ':swordfight:', 'swordfight.gif', null, 'gif', 0, 0, 1), 
(231, ':tease:', 'tease.gif', null, 'gif', 0, 0, 1), 
(232, ':think:', 'think.gif', null, 'gif', 0, 1, 1), 
(233, ':triste:', 'triste.gif', null, 'gif', 0, 0, 1), 
(234, ':tvhappy:', 'tvhappy.gif', null, 'gif', 0, 0, 1), 
(235, ':type:', 'type.gif', null, 'gif', 0, 0, 1), 
(236, ':urinal:', 'urinal.gif', null, 'gif', 0, 0, 1), 
(237, ':village:', 'village.gif', null, 'gif', 0, 0, 1), 
(238, ':vomit:', 'vomit.gif', null, 'gif', 0, 0, 1), 
(239, ':war:', 'war.gif', null, 'gif', 0, 0, 1), 
(240, ':welcome:', 'welcome.gif', null, 'gif', 0, 0, 1), 
(241, ':wheelchair:', 'wheelchair.gif', null, 'gif', 0, 0, 1), 
(242, ':whip:', 'whip.gif', null, 'gif', 0, 0, 1), 
(243, ':windows:', 'windows.gif', null, 'gif', 0, 0, 1), 
(244, ':worthy2:', 'worthy2.gif', null, 'gif', 0, 0, 1), 
(245, ':yeah:', 'yeah.gif', null, 'gif', 0, 0, 1), 
(246, ':zao:', 'zao.gif', null, 'gif', 0, 0, 1), 
(247, ':zzz:', 'zzz.gif', null, 'gif', 0, 0, 1);
#----------------------------
# Table structure for intkf_localkey
#----------------------------
CREATE TABLE `intkf_localkey` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `kname` varchar(30) collate latin1_general_ci NOT NULL,
  `kvalue` text collate latin1_general_ci NOT NULL,
  `ktype` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
#----------------------------
# No records for table intkf_localkey
#----------------------------

#----------------------------
# Table structure for intkf_localmember
#----------------------------
CREATE TABLE `intkf_localmember` (
  `HASH` char(32) character set latin1 collate latin1_general_ci NOT NULL,
  `PASSWORD` mediumtext character set latin1 collate latin1_general_ci NOT NULL,
  `LANG` char(3) NOT NULL default 'eng',
  `TPP` smallint(6) NOT NULL default '20',
  `PPP` smallint(6) NOT NULL default '10',
  `HIDESIG` tinyint(1) NOT NULL default '0',
  `LASTREAD` int(10) NOT NULL default '0',
  `LEVEL` tinyint(2) unsigned NOT NULL default '0',
  PRIMARY KEY  (`HASH`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
#----------------------------
# No records for table intkf_localmember
#----------------------------

#----------------------------
# Table structure for intkf_membri
#----------------------------
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
#----------------------------
# No records for table intkf_membri
#----------------------------

#----------------------------
# Table structure for intkf_msghe
#----------------------------
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
#----------------------------
# No records for table intkf_msghe
#----------------------------

#----------------------------
# Table structure for intkf_newmsg
#----------------------------
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
#----------------------------
# No records for table intkf_newmsg
#----------------------------

#----------------------------
# Table structure for intkf_priority
#----------------------------
CREATE TABLE `intkf_priority` (
  `HASH` binary(16) NOT NULL,
  `PRIOR` int(10) NOT NULL default '0',
  PRIMARY KEY  (`HASH`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
#----------------------------
# No records for table intkf_priority
#----------------------------

#----------------------------
# Table structure for intkf_purgatorio
#----------------------------
CREATE TABLE `intkf_purgatorio` (
  `HASH` binary(16) NOT NULL,
  `TYPE` enum('1','2','3','4') collate latin1_general_ci NOT NULL,
  `DELETE_DATE` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`HASH`),
  KEY `DELETE_DATE` (`DELETE_DATE`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
#----------------------------
# No records for table intkf_purgatorio
#----------------------------

#----------------------------
# Table structure for intkf_reply
#----------------------------
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
#----------------------------
# No records for table intkf_reply
#----------------------------

#----------------------------
# Table structure for intkf_sez
#----------------------------
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
  `LAST_POST` int(10) unsigned NOT NULL default '0',
  `LAST_TITLE` tinytext collate latin1_general_ci,
  `LAST_HASH` binary(16) NOT NULL default '0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0',
  `LAST_POSTER_NAME` varchar(30) collate latin1_general_ci NOT NULL default '',
  `LAST_POSTER_HASH` binary(16) NOT NULL default '0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0',
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
#----------------------------
# No records for table intkf_sez
#----------------------------

#----------------------------
# Table structure for iplist
#----------------------------
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
#----------------------------
# No records for table iplist
#----------------------------

#----------------------------
# Table structure for keyfo_admin
#----------------------------
CREATE TABLE `keyfo_admin` (
  `HASH` binary(16) NOT NULL,
  `TITLE` tinytext collate latin1_general_ci NOT NULL,
  `COMMAND` mediumtext collate latin1_general_ci NOT NULL,
  `TYPE` enum('3') collate latin1_general_ci NOT NULL default '3',
  `DATE` int(10) unsigned NOT NULL default '0',
  `SIGN` tinyblob NOT NULL,
  PRIMARY KEY  (`HASH`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
#----------------------------
# No records for table keyfo_admin
#----------------------------

#----------------------------
# Table structure for keyfo_conf
#----------------------------
CREATE TABLE `keyfo_conf` (
  `GROUP` varchar(100) collate latin1_general_ci NOT NULL default '',
  `FKEY` varchar(100) collate latin1_general_ci NOT NULL default '',
  `SUBKEY` varchar(100) collate latin1_general_ci NOT NULL default '',
  `VALUE` varchar(255) collate latin1_general_ci NOT NULL default '',
  PRIMARY KEY  (`GROUP`,`FKEY`,`SUBKEY`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
#----------------------------
# No records for table keyfo_conf
#----------------------------

#----------------------------
# Table structure for keyfo_congi
#----------------------------
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
#----------------------------
# No records for table keyfo_congi
#----------------------------

#----------------------------
# Table structure for keyfo_emoticons
#----------------------------
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
#----------------------------
# Records for table keyfo_emoticons
#----------------------------


insert  into keyfo_emoticons values 
(1, ':mellow:', 'mellow.gif', null, 'gif', 0, 1, 1), 
(2, ':huh:', 'huh.gif', null, 'gif', 0, 1, 1), 
(3, '^_^', 'happy.gif', null, 'gif', 0, 1, 1), 
(4, ':o', 'ohmy.gif', null, 'gif', 0, 1, 1), 
(5, ';)', 'wink.gif', null, 'gif', 0, 1, 1), 
(6, ':P', 'tongue.gif', null, 'gif', 0, 1, 1), 
(7, ':D', 'biggrin.gif', null, 'gif', 0, 1, 1), 
(8, ':lol2:', 'laugh.gif', null, 'gif', 0, 1, 1), 
(9, 'B-)', 'cool.gif', null, 'gif', 0, 1, 1), 
(10, ':rolleyes:', 'rolleyes.gif', null, 'gif', 0, 1, 1), 
(11, '-_-', 'sleep.gif', null, 'gif', 0, 1, 1), 
(12, '&lt;_&lt;', 'dry.gif', null, 'gif', 0, 1, 1), 
(13, ':)', 'smile.gif', null, 'gif', 0, 1, 1), 
(14, ':wub:', 'wub.gif', null, 'gif', 0, 1, 1), 
(15, ':mad:', 'mad.gif', null, 'gif', 0, 1, 1), 
(16, ':(', 'sad.gif', null, 'gif', 0, 1, 1), 
(17, ':unsure:', 'unsure.gif', null, 'gif', 0, 1, 1), 
(18, ':wacko:', 'wacko.gif', null, 'gif', 0, 1, 1), 
(19, ':blink:', 'blink.gif', null, 'gif', 0, 1, 1), 
(20, ':ph34r:', 'ph34r.gif', null, 'gif', 0, 1, 1), 
(21, ':ambulance:', 'ambulance.gif', null, 'gif', 0, 0, 1), 
(22, ':angel:', 'angel.gif', null, 'gif', 0, 0, 1), 
(23, ':applause:', 'applause.gif', null, 'gif', 0, 0, 1), 
(24, ':artist:', 'artist.gif', null, 'gif', 0, 0, 1), 
(25, ':baby:', 'baby.gif', null, 'gif', 0, 0, 1), 
(26, ':bag:', 'bag.gif', null, 'gif', 0, 0, 1), 
(27, ':band:', 'band.gif', null, 'gif', 0, 0, 1), 
(28, ':banned:', 'banned.gif', null, 'gif', 0, 0, 1), 
(29, ':beer:', 'beer.gif', null, 'gif', 0, 0, 1), 
(30, ':beer2:', 'beer2.gif', null, 'gif', 0, 0, 1), 
(31, ':blowup:', 'blowup.gif', null, 'gif', 0, 0, 1), 
(32, ':boat:', 'boat.gif', null, 'gif', 0, 0, 1), 
(33, ':book:', 'book.gif', null, 'gif', 0, 0, 1), 
(34, ':bow:', 'bow.gif', null, 'gif', 0, 0, 1), 
(35, ':boxe:', 'boxe.gif', null, 'gif', 0, 0, 1), 
(36, ':boxing:', 'boxing.gif', null, 'gif', 0, 0, 1), 
(37, ':canadian:', 'canadian.gif', null, 'gif', 0, 0, 1), 
(38, ':censored:', 'censored.gif', null, 'gif', 0, 0, 1), 
(39, ':chair:', 'chair.gif', null, 'gif', 0, 0, 1), 
(40, ':chef:', 'chef.gif', null, 'gif', 0, 0, 1), 
(41, ':cool2:', 'cool2.gif', null, 'gif', 0, 0, 1), 
(42, ':cowboy:', 'cowboy.gif', null, 'gif', 0, 0, 1), 
(43, ':crutch:', 'crutch.gif', null, 'gif', 0, 0, 1), 
(44, ':cry:', 'cry.gif', null, 'gif', 0, 0, 1), 
(45, ':death:', 'death.gif', null, 'gif', 0, 0, 1), 
(46, ':devil:', 'devil.gif', null, 'gif', 0, 0, 1), 
(47, ':dj:', 'dj.gif', null, 'gif', 0, 0, 1), 
(48, ':drunk:', 'drunk.gif', null, 'gif', 0, 0, 1), 
(49, ':eat:', 'eat.gif', null, 'gif', 0, 0, 1), 
(50, ':farewell:', 'farewell.gif', null, 'gif', 0, 0, 1), 
(51, ':gathering:', 'gathering.gif', null, 'gif', 0, 0, 1), 
(52, ':ghost:', 'ghost.gif', null, 'gif', 0, 0, 1), 
(53, ':gossip:', 'gossip.gif', null, 'gif', 0, 0, 1), 
(54, ':graduate:', 'graduate.gif', null, 'gif', 0, 1, 1), 
(55, ':guillotine:', 'guillotine.gif', null, 'gif', 0, 0, 1), 
(56, ':guitar:', 'guitar.gif', null, 'gif', 0, 0, 1), 
(57, ':gunbandana:', 'gunbandana.gif', null, 'gif', 0, 0, 1), 
(58, ':hammerer:', 'hammerer.gif', null, 'gif', 0, 0, 1), 
(59, ':happybday:', 'happybday.gif', null, 'gif', 0, 0, 1), 
(60, ':help:', 'help.gif', null, 'gif', 0, 0, 1), 
(61, ':hmm:', 'hmm.gif', null, 'gif', 0, 0, 1), 
(62, ':hoover:', 'hoover.gif', null, 'gif', 0, 0, 1), 
(63, ':horse:', 'horse.gif', null, 'gif', 0, 0, 1), 
(64, ':king:', 'king.gif', null, 'gif', 0, 0, 1), 
(65, ':kiss:', 'kiss.gif', null, 'gif', 0, 0, 1), 
(66, ':kiss2:', 'kiss2.gif', null, 'gif', 0, 0, 1), 
(67, ':laughing:', 'laughing.gif', null, 'gif', 0, 0, 1), 
(68, ':love:', 'love.gif', null, 'gif', 0, 0, 1), 
(69, ':mad2:', 'mad2.gif', null, 'gif', 0, 0, 1), 
(70, ':mobile:', 'mobile.gif', null, 'gif', 0, 0, 1), 
(71, ':nono:', 'nono.gif', null, 'gif', 0, 0, 1), 
(72, ':nugget:', 'nugget.gif', null, 'gif', 0, 0, 1), 
(73, ':phone:', 'phone.gif', null, 'gif', 0, 0, 1), 
(74, ':photo:', 'photo.gif', null, 'gif', 0, 0, 1), 
(75, ':pizza:', 'pizza.gif', null, 'gif', 0, 0, 1), 
(76, ':punk:', 'punk.gif', null, 'gif', 0, 0, 1), 
(77, ':ranting:', 'ranting.gif', null, 'gif', 0, 0, 1), 
(78, ':rotfl:', 'rotfl.gif', null, 'gif', 0, 1, 1), 
(79, ':runaway:', 'runaway.gif', null, 'gif', 0, 0, 1), 
(80, ':sbav:', 'sbav.gif', null, 'gif', 0, 0, 1), 
(81, ':sbav2:', 'sbav2.gif', null, 'gif', 0, 0, 1), 
(82, ':scared:', 'scared.gif', null, 'gif', 0, 0, 1), 
(83, ':scooter:', 'scooter.gif', null, 'gif', 0, 0, 1), 
(84, ':secret:', 'secret.gif', null, 'gif', 0, 0, 1), 
(85, ':serenade:', 'serenade.gif', null, 'gif', 0, 0, 1), 
(86, ':shifty:', 'shifty.gif', null, 'gif', 0, 0, 1), 
(87, ':shock:', 'shock.gif', null, 'gif', 0, 0, 1), 
(88, ':sign-ban:', 'sign-ban.gif', null, 'gif', 0, 0, 1), 
(89, ':sign-dots:', 'sign-dots.gif', null, 'gif', 0, 0, 1), 
(90, ':sign-offtopic:', 'sign-offtopic.gif', null, 'gif', 0, 0, 1), 
(91, ':sign-spam:', 'sign-spam.gif', null, 'gif', 0, 0, 1), 
(92, ':sign-stupid:', 'sign-stupid.gif', null, 'gif', 0, 0, 1), 
(93, ':sleeping:', 'sleeping.gif', null, 'gif', 0, 0, 1), 
(94, ':starwars:', 'starwars.gif', null, 'gif', 0, 0, 1), 
(95, ':surrender:', 'surrender.gif', null, 'gif', 0, 0, 1), 
(96, ':terafin-grin:', 'terafin-grin.gif', null, 'gif', 0, 0, 1), 
(97, ':thumbdown:', 'thumbdown.gif', null, 'gif', 0, 0, 1), 
(98, ':thumbup:', 'thumbup.gif', null, 'gif', 0, 0, 1), 
(99, ':tomato:', 'tomato.gif', null, 'gif', 0, 0, 1), 
(100, ':tongue2:', 'tongue2.gif', null, 'gif', 0, 1, 1), 
(101, ':tooth:', 'tooth.gif', null, 'gif', 0, 0, 1), 
(102, ':tv:', 'tv.gif', null, 'gif', 0, 0, 1), 
(103, ':uh:', 'uh.gif', null, 'gif', 0, 0, 1), 
(104, ':wallbash:', 'wallbash.gif', null, 'gif', 0, 0, 1), 
(105, ':whistling:', 'whistling.gif', null, 'gif', 0, 0, 1), 
(106, ':wine:', 'wine.gif', null, 'gif', 0, 0, 1), 
(107, ':worthy:', 'worthy.gif', null, 'gif', 0, 0, 1), 
(108, ':wub2:', 'wub2.gif', null, 'gif', 0, 0, 1), 
(109, ':xmas:', 'xmas.gif', null, 'gif', 0, 0, 1), 
(110, ':yeahright:', 'yeahright.gif', null, 'gif', 0, 0, 1), 
(111, ':yes:', 'yes.gif', null, 'gif', 0, 0, 1), 
(112, ':adminpower:', 'adminpower.gif', null, 'gif', 0, 0, 1), 
(113, ':afro:', 'afro.gif', null, 'gif', 0, 0, 1), 
(114, ':angry:', 'angry.gif', null, 'gif', 0, 0, 1), 
(115, ':apple:', 'apple.gif', null, 'gif', 0, 0, 1), 
(116, ':argue:', 'argue.gif', null, 'gif', 0, 0, 1), 
(117, ':arrow:', 'arrow.gif', null, 'gif', 0, 0, 1), 
(118, ':asd:', 'asd.gif', null, 'gif', 0, 0, 1), 
(119, ':baboso:', 'baboso.gif', null, 'gif', 0, 0, 1), 
(120, ':badmood:', 'badmood.gif', null, 'gif', 0, 0, 1), 
(121, ':ban:', 'ban.gif', null, 'gif', 0, 0, 1), 
(122, ':banana:', 'banana.gif', null, 'gif', 0, 0, 1), 
(123, ':bastardinside:', 'bastardinside.gif', null, 'gif', 0, 0, 1), 
(124, ':beg:', 'beg.gif', null, 'gif', 0, 0, 1), 
(125, ':biggrin-santa:', 'biggrin-santa.gif', null, 'gif', 0, 0, 1), 
(126, ':biggrin2:', 'biggrin2.gif', null, 'gif', 0, 0, 1), 
(127, ':bleh:', 'bleh.gif', null, 'gif', 0, 0, 1), 
(128, ':blow:', 'blow.gif', null, 'gif', 0, 0, 1), 
(129, ':blush:', 'blush.gif', null, 'gif', 0, 0, 1), 
(130, ':blush2:', 'blush2.gif', null, 'gif', 0, 0, 1), 
(131, ':bond:', 'bond.gif', null, 'gif', 0, 0, 1), 
(132, ':bounce:', 'bounce.gif', null, 'gif', 0, 0, 1), 
(133, ':bustedcop:', 'bustedcop.gif', null, 'gif', 0, 0, 1), 
(134, ':bye:', 'bye.gif', null, 'gif', 0, 0, 1), 
(135, ':cheers:', 'cheers.gif', null, 'gif', 0, 0, 1), 
(136, ':cheese:', 'cheese.gif', null, 'gif', 0, 0, 1), 
(137, ':clap:', 'clap.gif', null, 'gif', 0, 0, 1), 
(138, ':closedeyes:', 'closedeyes.gif', null, 'gif', 0, 0, 1), 
(139, ':cold:', 'cold.gif', null, 'gif', 0, 0, 1), 
(140, ':console:', 'console.gif', null, 'gif', 0, 0, 1), 
(141, ':crackegg:', 'crackegg.gif', null, 'gif', 0, 0, 1), 
(142, ':crazy-santa:', 'crazy-santa.gif', null, 'gif', 0, 0, 1), 
(143, ':crybaby:', 'crybaby.gif', null, 'gif', 0, 0, 1), 
(144, ':cupid:', 'cupid.gif', null, 'gif', 0, 0, 1), 
(145, ':dance:', 'dance.gif', null, 'gif', 0, 0, 1), 
(146, ':dead:', 'dead.gif', null, 'gif', 0, 0, 1), 
(147, ':director:', 'director.gif', null, 'gif', 0, 0, 1), 
(148, ':doctor:', 'doctor.gif', null, 'gif', 0, 0, 1), 
(149, ':dribble:', 'dribble.gif', null, 'gif', 0, 0, 1), 
(150, ':drive:', 'drive.gif', null, 'gif', 0, 0, 1), 
(151, ':edonkey:', 'edonkey.gif', null, 'gif', 0, 0, 1), 
(152, ':evil:', 'evil.gif', null, 'gif', 0, 0, 1), 
(153, ':excl:', 'excl.gif', null, 'gif', 0, 0, 1), 
(154, ':fear:', 'fear.gif', null, 'gif', 0, 0, 1), 
(155, ':fight:', 'fight.gif', null, 'gif', 0, 0, 1), 
(156, ':flirt:', 'flirt.gif', null, 'gif', 0, 0, 1), 
(157, ':flower:', 'flower.gif', null, 'gif', 0, 0, 1), 
(158, ':flush:', 'flush.gif', null, 'gif', 0, 0, 1), 
(159, ':folle:', 'folle.gif', null, 'gif', 0, 0, 1), 
(160, ':fuckyou:', 'fuckyou.gif', null, 'gif', 0, 0, 1), 
(161, ':giggle:', 'giggle.gif', null, 'gif', 0, 0, 1), 
(162, ':glare:', 'glare.gif', null, 'gif', 0, 0, 1), 
(163, ':gogo:', 'gogo.gif', null, 'gif', 0, 0, 1), 
(164, ':group:', 'group.gif', null, 'gif', 0, 0, 1), 
(165, ':gun:', 'gun.gif', null, 'gif', 0, 0, 1), 
(166, ':haha:', 'haha.gif', null, 'gif', 0, 0, 1), 
(167, ':clap2:', 'clap2.gif', null, 'gif', 0, 0, 1), 
(168, ':harp:', 'harp.gif', null, 'gif', 0, 0, 1), 
(169, ':hello:', 'hello.gif', null, 'gif', 0, 0, 1), 
(170, ':hysterical:', 'hysterical.gif', null, 'gif', 0, 0, 1), 
(171, ':idea:', 'idea.gif', null, 'gif', 0, 0, 1), 
(172, ':injured:', 'injured.gif', null, 'gif', 0, 0, 1), 
(173, ':italy:', 'italy.gif', null, 'gif', 0, 0, 1), 
(174, ':jason:', 'jason.gif', null, 'gif', 0, 0, 1), 
(175, ':jawdrop:', 'jawdrop.gif', null, 'gif', 0, 0, 1), 
(176, ':jumpon:', 'jumpon.gif', null, 'gif', 0, 0, 1), 
(177, ':kicking:', 'kicking.gif', null, 'gif', 0, 0, 1), 
(178, ':kisskiss:', 'kisskiss.gif', null, 'gif', 0, 0, 1), 
(179, ':kissme-santa:', 'kissme-santa.gif', null, 'gif', 0, 0, 1), 
(180, ':laser:', 'laser.gif', null, 'gif', 0, 0, 1), 
(181, ':letto:', 'letto.gif', null, 'gif', 0, 0, 1), 
(182, ':linguaccia:', 'linguaccia.gif', null, 'gif', 0, 0, 1), 
(183, ':linux:', 'linux.gif', null, 'gif', 0, 0, 1), 
(184, ':lock:', 'lock.gif', null, 'gif', 0, 0, 1), 
(185, ':lol:', 'lol.gif', null, 'gif', 0, 0, 1), 
(186, ':lollone:', 'lollone.gif', null, 'gif', 0, 0, 1), 
(187, ':loveh:', 'loveh.gif', null, 'gif', 0, 0, 1), 
(188, ':macosx:', 'macosx.gif', null, 'gif', 0, 0, 1), 
(189, ':megalol:', 'megalol.gif', null, 'gif', 0, 0, 1), 
(190, ':mitico:', 'mitico.gif', null, 'gif', 0, 0, 1), 
(191, ':muletto:', 'muletto.gif', null, 'gif', 0, 0, 1), 
(192, ':napoleon:', 'napoleon.gif', null, 'gif', 0, 0, 1), 
(193, ':ninja:', 'ninja.gif', null, 'gif', 0, 0, 1), 
(194, ':nono2:', 'nono2.gif', null, 'gif', 0, 0, 1), 
(195, ':nyanya:', 'nyanya.gif', null, 'gif', 0, 0, 1), 
(196, ':ola:', 'ola.gif', null, 'gif', 0, 0, 1), 
(197, ':oops:', 'oops.gif', null, 'gif', 0, 0, 1), 
(198, ':pcthrow:', 'pcthrow.gif', null, 'gif', 0, 0, 1), 
(199, ':pcwhack:', 'pcwhack.gif', null, 'gif', 0, 0, 1), 
(200, ':pirate:', 'pirate.gif', null, 'gif', 0, 0, 1), 
(201, ':plane:', 'plane.gif', null, 'gif', 0, 0, 1), 
(202, ':please:', 'please.gif', null, 'gif', 0, 0, 1), 
(203, ':popcorn:', 'popcorn.gif', null, 'gif', 0, 0, 1), 
(204, ':pope:', 'pope.gif', null, 'gif', 0, 0, 1), 
(205, ':poppe:', 'poppe.gif', null, 'gif', 0, 0, 1), 
(206, ':protest:', 'protest.gif', null, 'gif', 0, 0, 1), 
(207, ':ranting2:', 'ranting2.gif', null, 'gif', 0, 0, 1), 
(208, ':rocket:', 'rocket.gif', null, 'gif', 0, 0, 1), 
(209, ':rofl:', 'rofl.gif', null, 'gif', 0, 0, 1), 
(210, ':saacrede:', 'saacrede.gif', null, 'gif', 0, 0, 1), 
(211, ':sadbye:', 'sadbye.gif', null, 'gif', 0, 0, 1), 
(212, ':scratch:', 'scratch.gif', null, 'gif', 0, 0, 1), 
(213, ':scream:', 'scream.gif', null, 'gif', 0, 0, 1), 
(214, ':senzaundente:', 'senzaundente.gif', null, 'gif', 0, 0, 1), 
(215, ':shark:', 'shark.gif', null, 'gif', 0, 0, 1), 
(216, ':shit:', 'shit.gif', null, 'gif', 0, 0, 1), 
(217, ':shrug:', 'shrug.gif', null, 'gif', 0, 0, 1), 
(218, ':smoke:', 'smoke.gif', null, 'gif', 0, 0, 1), 
(219, ':snack:', 'snack.gif', null, 'gif', 0, 0, 1), 
(220, ':sofa:', 'sofa.gif', null, 'gif', 0, 0, 1), 
(221, ':sorry:', 'sorry.gif', null, 'gif', 0, 0, 1), 
(222, ':spacecraft:', 'spacecraft.gif', null, 'gif', 0, 0, 1), 
(223, ':spam:', 'spam.gif', null, 'gif', 0, 0, 1), 
(224, ':spank:', 'spank.gif', null, 'gif', 0, 0, 1), 
(225, ':startrek:', 'startrek.gif', null, 'gif', 0, 0, 1), 
(226, ':stopspam:', 'stopspam.gif', null, 'gif', 0, 0, 1), 
(227, ':stretcher:', 'stretcher.gif', null, 'gif', 0, 0, 1), 
(228, ':sweatdrop:', 'sweatdrop.gif', null, 'gif', 0, 0, 1), 
(229, ':sweatdrop2:', 'sweatdrop2.gif', null, 'gif', 0, 0, 1), 
(230, ':swordfight:', 'swordfight.gif', null, 'gif', 0, 0, 1), 
(231, ':tease:', 'tease.gif', null, 'gif', 0, 0, 1), 
(232, ':think:', 'think.gif', null, 'gif', 0, 1, 1), 
(233, ':triste:', 'triste.gif', null, 'gif', 0, 0, 1), 
(234, ':tvhappy:', 'tvhappy.gif', null, 'gif', 0, 0, 1), 
(235, ':type:', 'type.gif', null, 'gif', 0, 0, 1), 
(236, ':urinal:', 'urinal.gif', null, 'gif', 0, 0, 1), 
(237, ':village:', 'village.gif', null, 'gif', 0, 0, 1), 
(238, ':vomit:', 'vomit.gif', null, 'gif', 0, 0, 1), 
(239, ':war:', 'war.gif', null, 'gif', 0, 0, 1), 
(240, ':welcome:', 'welcome.gif', null, 'gif', 0, 0, 1), 
(241, ':wheelchair:', 'wheelchair.gif', null, 'gif', 0, 0, 1), 
(242, ':whip:', 'whip.gif', null, 'gif', 0, 0, 1), 
(243, ':windows:', 'windows.gif', null, 'gif', 0, 0, 1), 
(244, ':worthy2:', 'worthy2.gif', null, 'gif', 0, 0, 1), 
(245, ':yeah:', 'yeah.gif', null, 'gif', 0, 0, 1), 
(246, ':zao:', 'zao.gif', null, 'gif', 0, 0, 1), 
(247, ':zzz:', 'zzz.gif', null, 'gif', 0, 0, 1);
#----------------------------
# Table structure for keyfo_localkey
#----------------------------
CREATE TABLE `keyfo_localkey` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `kname` varchar(30) collate latin1_general_ci NOT NULL,
  `kvalue` text collate latin1_general_ci NOT NULL,
  `ktype` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
#----------------------------
# No records for table keyfo_localkey
#----------------------------

#----------------------------
# Table structure for keyfo_localmember
#----------------------------
CREATE TABLE `keyfo_localmember` (
  `HASH` char(32) character set latin1 collate latin1_general_ci NOT NULL,
  `PASSWORD` mediumtext character set latin1 collate latin1_general_ci NOT NULL,
  `LANG` char(3) NOT NULL default 'eng',
  `TPP` smallint(6) NOT NULL default '20',
  `PPP` smallint(6) NOT NULL default '10',
  `HIDESIG` tinyint(1) NOT NULL default '0',
  `LASTREAD` int(10) NOT NULL default '0',
  `LEVEL` tinyint(2) unsigned NOT NULL default '0',
  PRIMARY KEY  (`HASH`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
#----------------------------
# No records for table keyfo_localmember
#----------------------------

#----------------------------
# Table structure for keyfo_membri
#----------------------------
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
#----------------------------
# No records for table keyfo_membri
#----------------------------

#----------------------------
# Table structure for keyfo_msghe
#----------------------------
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
#----------------------------
# No records for table keyfo_msghe
#----------------------------

#----------------------------
# Table structure for keyfo_newmsg
#----------------------------
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
#----------------------------
# No records for table keyfo_newmsg
#----------------------------

#----------------------------
# Table structure for keyfo_priority
#----------------------------
CREATE TABLE `keyfo_priority` (
  `HASH` binary(16) NOT NULL,
  `PRIOR` int(10) NOT NULL default '0',
  PRIMARY KEY  (`HASH`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
#----------------------------
# No records for table keyfo_priority
#----------------------------

#----------------------------
# Table structure for keyfo_purgatorio
#----------------------------
CREATE TABLE `keyfo_purgatorio` (
  `HASH` binary(16) NOT NULL,
  `TYPE` enum('1','2','3','4') collate latin1_general_ci NOT NULL,
  `DELETE_DATE` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`HASH`),
  KEY `DELETE_DATE` (`DELETE_DATE`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
#----------------------------
# No records for table keyfo_purgatorio
#----------------------------

#----------------------------
# Table structure for keyfo_reply
#----------------------------
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
#----------------------------
# No records for table keyfo_reply
#----------------------------

#----------------------------
# Table structure for keyfo_sez
#----------------------------
CREATE TABLE `keyfo_sez` (
  `ID` int(8) unsigned NOT NULL,
  `SEZ_NAME` varchar(250) character set latin1 collate latin1_general_ci default '',
  `SEZ_DESC` text character set latin1 collate latin1_general_ci,
  `MOD` varchar(250) character set latin1 collate latin1_general_ci NOT NULL default '',
  `PKEY` tinyblob NOT NULL,
  `PRKEY` tinyblob NOT NULL,
  `THR_NUM` int(8) unsigned NOT NULL default '0',
  `REPLY_NUM` int(8) unsigned NOT NULL default '0',
  `ONLY_AUTH` int(8) unsigned NOT NULL default '1',
  `AUTOFLUSH` int(10) unsigned NOT NULL default '0',
  `ORDINE` int(10) unsigned NOT NULL default '0',
  `FIGLIO` int(10) unsigned NOT NULL default '0',
  `last_admin_edit` int(8) unsigned NOT NULL default '0',
  `LAST_POST` int(10) unsigned NOT NULL default '0',
  `LAST_TITLE` tinytext,
  `LAST_HASH` binary(16) NOT NULL default '0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0',
  `LAST_POSTER_NAME` varchar(30) NOT NULL default '',
  `LAST_POSTER_HASH` binary(16) NOT NULL default '0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0',
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
#----------------------------
# No records for table keyfo_sez
#----------------------------

#----------------------------
# Table structure for log
#----------------------------
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
#----------------------------
# Records for table log
#----------------------------


insert  into `log` values 
(1, 1131460133, 0, 0, 0, 3, 0, ''), 
(2, 1131460144, 9, 2, 178468434, 7, 5, ''), 
(3, 1131460144, 16, 2, 0, 12, 5, '');
#----------------------------
# Table structure for session
#----------------------------
CREATE TABLE `session` (
  `SESSID` varchar(32) collate latin1_general_ci NOT NULL default '',
  `IP` varchar(32) collate latin1_general_ci NOT NULL default '',
  `FORUM` varchar(10) collate latin1_general_ci NOT NULL default '',
  `NICK` varchar(32) collate latin1_general_ci NOT NULL default '',
  `DATE` int(10) unsigned NOT NULL default '0',
  `PASSWORD` tinyblob NOT NULL,
  PRIMARY KEY  (`SESSID`,`IP`,`FORUM`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
#----------------------------
# No records for table session
#----------------------------

#----------------------------
# Table structure for stat
#----------------------------
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
#----------------------------
# No records for table stat
#----------------------------

#----------------------------
# Table structure for temp
#----------------------------
CREATE TABLE `temp` (
  `CHIAVE` varchar(150) collate latin1_general_ci NOT NULL default '',
  `VALORE` text collate latin1_general_ci NOT NULL,
  `TTL` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`CHIAVE`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
#----------------------------
# No records for table temp
#----------------------------

#----------------------------
# Table structure for tstkf_admin
#----------------------------
CREATE TABLE `tstkf_admin` (
  `HASH` binary(16) NOT NULL,
  `TITLE` tinytext collate latin1_general_ci NOT NULL,
  `COMMAND` mediumtext collate latin1_general_ci NOT NULL,
  `TYPE` enum('3') collate latin1_general_ci NOT NULL default '3',
  `DATE` int(10) unsigned NOT NULL default '0',
  `SIGN` tinyblob NOT NULL,
  PRIMARY KEY  (`HASH`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
#----------------------------
# No records for table tstkf_admin
#----------------------------

#----------------------------
# Table structure for tstkf_conf
#----------------------------
CREATE TABLE `tstkf_conf` (
  `GROUP` varchar(100) collate latin1_general_ci NOT NULL default '',
  `FKEY` varchar(100) collate latin1_general_ci NOT NULL default '',
  `SUBKEY` varchar(100) collate latin1_general_ci NOT NULL default '',
  `VALUE` varchar(255) collate latin1_general_ci NOT NULL default '',
  PRIMARY KEY  (`GROUP`,`FKEY`,`SUBKEY`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
#----------------------------
# No records for table tstkf_conf
#----------------------------

#----------------------------
# Table structure for tstkf_congi
#----------------------------
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
#----------------------------
# No records for table tstkf_congi
#----------------------------

#----------------------------
# Table structure for tstkf_emoticons
#----------------------------
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
#----------------------------
# Records for table tstkf_emoticons
#----------------------------


insert  into tstkf_emoticons values 
(1, ':mellow:', 'mellow.gif', null, 'gif', 0, 1, 1), 
(2, ':huh:', 'huh.gif', null, 'gif', 0, 1, 1), 
(3, '^_^', 'happy.gif', null, 'gif', 0, 1, 1), 
(4, ':o', 'ohmy.gif', null, 'gif', 0, 1, 1), 
(5, ';)', 'wink.gif', null, 'gif', 0, 1, 1), 
(6, ':P', 'tongue.gif', null, 'gif', 0, 1, 1), 
(7, ':D', 'biggrin.gif', null, 'gif', 0, 1, 1), 
(8, ':lol2:', 'laugh.gif', null, 'gif', 0, 1, 1), 
(9, 'B-)', 'cool.gif', null, 'gif', 0, 1, 1), 
(10, ':rolleyes:', 'rolleyes.gif', null, 'gif', 0, 1, 1), 
(11, '-_-', 'sleep.gif', null, 'gif', 0, 1, 1), 
(12, '&lt;_&lt;', 'dry.gif', null, 'gif', 0, 1, 1), 
(13, ':)', 'smile.gif', null, 'gif', 0, 1, 1), 
(14, ':wub:', 'wub.gif', null, 'gif', 0, 1, 1), 
(15, ':mad:', 'mad.gif', null, 'gif', 0, 1, 1), 
(16, ':(', 'sad.gif', null, 'gif', 0, 1, 1), 
(17, ':unsure:', 'unsure.gif', null, 'gif', 0, 1, 1), 
(18, ':wacko:', 'wacko.gif', null, 'gif', 0, 1, 1), 
(19, ':blink:', 'blink.gif', null, 'gif', 0, 1, 1), 
(20, ':ph34r:', 'ph34r.gif', null, 'gif', 0, 1, 1), 
(21, ':ambulance:', 'ambulance.gif', null, 'gif', 0, 0, 1), 
(22, ':angel:', 'angel.gif', null, 'gif', 0, 0, 1), 
(23, ':applause:', 'applause.gif', null, 'gif', 0, 0, 1), 
(24, ':artist:', 'artist.gif', null, 'gif', 0, 0, 1), 
(25, ':baby:', 'baby.gif', null, 'gif', 0, 0, 1), 
(26, ':bag:', 'bag.gif', null, 'gif', 0, 0, 1), 
(27, ':band:', 'band.gif', null, 'gif', 0, 0, 1), 
(28, ':banned:', 'banned.gif', null, 'gif', 0, 0, 1), 
(29, ':beer:', 'beer.gif', null, 'gif', 0, 0, 1), 
(30, ':beer2:', 'beer2.gif', null, 'gif', 0, 0, 1), 
(31, ':blowup:', 'blowup.gif', null, 'gif', 0, 0, 1), 
(32, ':boat:', 'boat.gif', null, 'gif', 0, 0, 1), 
(33, ':book:', 'book.gif', null, 'gif', 0, 0, 1), 
(34, ':bow:', 'bow.gif', null, 'gif', 0, 0, 1), 
(35, ':boxe:', 'boxe.gif', null, 'gif', 0, 0, 1), 
(36, ':boxing:', 'boxing.gif', null, 'gif', 0, 0, 1), 
(37, ':canadian:', 'canadian.gif', null, 'gif', 0, 0, 1), 
(38, ':censored:', 'censored.gif', null, 'gif', 0, 0, 1), 
(39, ':chair:', 'chair.gif', null, 'gif', 0, 0, 1), 
(40, ':chef:', 'chef.gif', null, 'gif', 0, 0, 1), 
(41, ':cool2:', 'cool2.gif', null, 'gif', 0, 0, 1), 
(42, ':cowboy:', 'cowboy.gif', null, 'gif', 0, 0, 1), 
(43, ':crutch:', 'crutch.gif', null, 'gif', 0, 0, 1), 
(44, ':cry:', 'cry.gif', null, 'gif', 0, 0, 1), 
(45, ':death:', 'death.gif', null, 'gif', 0, 0, 1), 
(46, ':devil:', 'devil.gif', null, 'gif', 0, 0, 1), 
(47, ':dj:', 'dj.gif', null, 'gif', 0, 0, 1), 
(48, ':drunk:', 'drunk.gif', null, 'gif', 0, 0, 1), 
(49, ':eat:', 'eat.gif', null, 'gif', 0, 0, 1), 
(50, ':farewell:', 'farewell.gif', null, 'gif', 0, 0, 1), 
(51, ':gathering:', 'gathering.gif', null, 'gif', 0, 0, 1), 
(52, ':ghost:', 'ghost.gif', null, 'gif', 0, 0, 1), 
(53, ':gossip:', 'gossip.gif', null, 'gif', 0, 0, 1), 
(54, ':graduate:', 'graduate.gif', null, 'gif', 0, 1, 1), 
(55, ':guillotine:', 'guillotine.gif', null, 'gif', 0, 0, 1), 
(56, ':guitar:', 'guitar.gif', null, 'gif', 0, 0, 1), 
(57, ':gunbandana:', 'gunbandana.gif', null, 'gif', 0, 0, 1), 
(58, ':hammerer:', 'hammerer.gif', null, 'gif', 0, 0, 1), 
(59, ':happybday:', 'happybday.gif', null, 'gif', 0, 0, 1), 
(60, ':help:', 'help.gif', null, 'gif', 0, 0, 1), 
(61, ':hmm:', 'hmm.gif', null, 'gif', 0, 0, 1), 
(62, ':hoover:', 'hoover.gif', null, 'gif', 0, 0, 1), 
(63, ':horse:', 'horse.gif', null, 'gif', 0, 0, 1), 
(64, ':king:', 'king.gif', null, 'gif', 0, 0, 1), 
(65, ':kiss:', 'kiss.gif', null, 'gif', 0, 0, 1), 
(66, ':kiss2:', 'kiss2.gif', null, 'gif', 0, 0, 1), 
(67, ':laughing:', 'laughing.gif', null, 'gif', 0, 0, 1), 
(68, ':love:', 'love.gif', null, 'gif', 0, 0, 1), 
(69, ':mad2:', 'mad2.gif', null, 'gif', 0, 0, 1), 
(70, ':mobile:', 'mobile.gif', null, 'gif', 0, 0, 1), 
(71, ':nono:', 'nono.gif', null, 'gif', 0, 0, 1), 
(72, ':nugget:', 'nugget.gif', null, 'gif', 0, 0, 1), 
(73, ':phone:', 'phone.gif', null, 'gif', 0, 0, 1), 
(74, ':photo:', 'photo.gif', null, 'gif', 0, 0, 1), 
(75, ':pizza:', 'pizza.gif', null, 'gif', 0, 0, 1), 
(76, ':punk:', 'punk.gif', null, 'gif', 0, 0, 1), 
(77, ':ranting:', 'ranting.gif', null, 'gif', 0, 0, 1), 
(78, ':rotfl:', 'rotfl.gif', null, 'gif', 0, 1, 1), 
(79, ':runaway:', 'runaway.gif', null, 'gif', 0, 0, 1), 
(80, ':sbav:', 'sbav.gif', null, 'gif', 0, 0, 1), 
(81, ':sbav2:', 'sbav2.gif', null, 'gif', 0, 0, 1), 
(82, ':scared:', 'scared.gif', null, 'gif', 0, 0, 1), 
(83, ':scooter:', 'scooter.gif', null, 'gif', 0, 0, 1), 
(84, ':secret:', 'secret.gif', null, 'gif', 0, 0, 1), 
(85, ':serenade:', 'serenade.gif', null, 'gif', 0, 0, 1), 
(86, ':shifty:', 'shifty.gif', null, 'gif', 0, 0, 1), 
(87, ':shock:', 'shock.gif', null, 'gif', 0, 0, 1), 
(88, ':sign-ban:', 'sign-ban.gif', null, 'gif', 0, 0, 1), 
(89, ':sign-dots:', 'sign-dots.gif', null, 'gif', 0, 0, 1), 
(90, ':sign-offtopic:', 'sign-offtopic.gif', null, 'gif', 0, 0, 1), 
(91, ':sign-spam:', 'sign-spam.gif', null, 'gif', 0, 0, 1), 
(92, ':sign-stupid:', 'sign-stupid.gif', null, 'gif', 0, 0, 1), 
(93, ':sleeping:', 'sleeping.gif', null, 'gif', 0, 0, 1), 
(94, ':starwars:', 'starwars.gif', null, 'gif', 0, 0, 1), 
(95, ':surrender:', 'surrender.gif', null, 'gif', 0, 0, 1), 
(96, ':terafin-grin:', 'terafin-grin.gif', null, 'gif', 0, 0, 1), 
(97, ':thumbdown:', 'thumbdown.gif', null, 'gif', 0, 0, 1), 
(98, ':thumbup:', 'thumbup.gif', null, 'gif', 0, 0, 1), 
(99, ':tomato:', 'tomato.gif', null, 'gif', 0, 0, 1), 
(100, ':tongue2:', 'tongue2.gif', null, 'gif', 0, 1, 1), 
(101, ':tooth:', 'tooth.gif', null, 'gif', 0, 0, 1), 
(102, ':tv:', 'tv.gif', null, 'gif', 0, 0, 1), 
(103, ':uh:', 'uh.gif', null, 'gif', 0, 0, 1), 
(104, ':wallbash:', 'wallbash.gif', null, 'gif', 0, 0, 1), 
(105, ':whistling:', 'whistling.gif', null, 'gif', 0, 0, 1), 
(106, ':wine:', 'wine.gif', null, 'gif', 0, 0, 1), 
(107, ':worthy:', 'worthy.gif', null, 'gif', 0, 0, 1), 
(108, ':wub2:', 'wub2.gif', null, 'gif', 0, 0, 1), 
(109, ':xmas:', 'xmas.gif', null, 'gif', 0, 0, 1), 
(110, ':yeahright:', 'yeahright.gif', null, 'gif', 0, 0, 1), 
(111, ':yes:', 'yes.gif', null, 'gif', 0, 0, 1), 
(112, ':adminpower:', 'adminpower.gif', null, 'gif', 0, 0, 1), 
(113, ':afro:', 'afro.gif', null, 'gif', 0, 0, 1), 
(114, ':angry:', 'angry.gif', null, 'gif', 0, 0, 1), 
(115, ':apple:', 'apple.gif', null, 'gif', 0, 0, 1), 
(116, ':argue:', 'argue.gif', null, 'gif', 0, 0, 1), 
(117, ':arrow:', 'arrow.gif', null, 'gif', 0, 0, 1), 
(118, ':asd:', 'asd.gif', null, 'gif', 0, 0, 1), 
(119, ':baboso:', 'baboso.gif', null, 'gif', 0, 0, 1), 
(120, ':badmood:', 'badmood.gif', null, 'gif', 0, 0, 1), 
(121, ':ban:', 'ban.gif', null, 'gif', 0, 0, 1), 
(122, ':banana:', 'banana.gif', null, 'gif', 0, 0, 1), 
(123, ':bastardinside:', 'bastardinside.gif', null, 'gif', 0, 0, 1), 
(124, ':beg:', 'beg.gif', null, 'gif', 0, 0, 1), 
(125, ':biggrin-santa:', 'biggrin-santa.gif', null, 'gif', 0, 0, 1), 
(126, ':biggrin2:', 'biggrin2.gif', null, 'gif', 0, 0, 1), 
(127, ':bleh:', 'bleh.gif', null, 'gif', 0, 0, 1), 
(128, ':blow:', 'blow.gif', null, 'gif', 0, 0, 1), 
(129, ':blush:', 'blush.gif', null, 'gif', 0, 0, 1), 
(130, ':blush2:', 'blush2.gif', null, 'gif', 0, 0, 1), 
(131, ':bond:', 'bond.gif', null, 'gif', 0, 0, 1), 
(132, ':bounce:', 'bounce.gif', null, 'gif', 0, 0, 1), 
(133, ':bustedcop:', 'bustedcop.gif', null, 'gif', 0, 0, 1), 
(134, ':bye:', 'bye.gif', null, 'gif', 0, 0, 1), 
(135, ':cheers:', 'cheers.gif', null, 'gif', 0, 0, 1), 
(136, ':cheese:', 'cheese.gif', null, 'gif', 0, 0, 1), 
(137, ':clap:', 'clap.gif', null, 'gif', 0, 0, 1), 
(138, ':closedeyes:', 'closedeyes.gif', null, 'gif', 0, 0, 1), 
(139, ':cold:', 'cold.gif', null, 'gif', 0, 0, 1), 
(140, ':console:', 'console.gif', null, 'gif', 0, 0, 1), 
(141, ':crackegg:', 'crackegg.gif', null, 'gif', 0, 0, 1), 
(142, ':crazy-santa:', 'crazy-santa.gif', null, 'gif', 0, 0, 1), 
(143, ':crybaby:', 'crybaby.gif', null, 'gif', 0, 0, 1), 
(144, ':cupid:', 'cupid.gif', null, 'gif', 0, 0, 1), 
(145, ':dance:', 'dance.gif', null, 'gif', 0, 0, 1), 
(146, ':dead:', 'dead.gif', null, 'gif', 0, 0, 1), 
(147, ':director:', 'director.gif', null, 'gif', 0, 0, 1), 
(148, ':doctor:', 'doctor.gif', null, 'gif', 0, 0, 1), 
(149, ':dribble:', 'dribble.gif', null, 'gif', 0, 0, 1), 
(150, ':drive:', 'drive.gif', null, 'gif', 0, 0, 1), 
(151, ':edonkey:', 'edonkey.gif', null, 'gif', 0, 0, 1), 
(152, ':evil:', 'evil.gif', null, 'gif', 0, 0, 1), 
(153, ':excl:', 'excl.gif', null, 'gif', 0, 0, 1), 
(154, ':fear:', 'fear.gif', null, 'gif', 0, 0, 1), 
(155, ':fight:', 'fight.gif', null, 'gif', 0, 0, 1), 
(156, ':flirt:', 'flirt.gif', null, 'gif', 0, 0, 1), 
(157, ':flower:', 'flower.gif', null, 'gif', 0, 0, 1), 
(158, ':flush:', 'flush.gif', null, 'gif', 0, 0, 1), 
(159, ':folle:', 'folle.gif', null, 'gif', 0, 0, 1), 
(160, ':fuckyou:', 'fuckyou.gif', null, 'gif', 0, 0, 1), 
(161, ':giggle:', 'giggle.gif', null, 'gif', 0, 0, 1), 
(162, ':glare:', 'glare.gif', null, 'gif', 0, 0, 1), 
(163, ':gogo:', 'gogo.gif', null, 'gif', 0, 0, 1), 
(164, ':group:', 'group.gif', null, 'gif', 0, 0, 1), 
(165, ':gun:', 'gun.gif', null, 'gif', 0, 0, 1), 
(166, ':haha:', 'haha.gif', null, 'gif', 0, 0, 1), 
(167, ':clap2:', 'clap2.gif', null, 'gif', 0, 0, 1), 
(168, ':harp:', 'harp.gif', null, 'gif', 0, 0, 1), 
(169, ':hello:', 'hello.gif', null, 'gif', 0, 0, 1), 
(170, ':hysterical:', 'hysterical.gif', null, 'gif', 0, 0, 1), 
(171, ':idea:', 'idea.gif', null, 'gif', 0, 0, 1), 
(172, ':injured:', 'injured.gif', null, 'gif', 0, 0, 1), 
(173, ':italy:', 'italy.gif', null, 'gif', 0, 0, 1), 
(174, ':jason:', 'jason.gif', null, 'gif', 0, 0, 1), 
(175, ':jawdrop:', 'jawdrop.gif', null, 'gif', 0, 0, 1), 
(176, ':jumpon:', 'jumpon.gif', null, 'gif', 0, 0, 1), 
(177, ':kicking:', 'kicking.gif', null, 'gif', 0, 0, 1), 
(178, ':kisskiss:', 'kisskiss.gif', null, 'gif', 0, 0, 1), 
(179, ':kissme-santa:', 'kissme-santa.gif', null, 'gif', 0, 0, 1), 
(180, ':laser:', 'laser.gif', null, 'gif', 0, 0, 1), 
(181, ':letto:', 'letto.gif', null, 'gif', 0, 0, 1), 
(182, ':linguaccia:', 'linguaccia.gif', null, 'gif', 0, 0, 1), 
(183, ':linux:', 'linux.gif', null, 'gif', 0, 0, 1), 
(184, ':lock:', 'lock.gif', null, 'gif', 0, 0, 1), 
(185, ':lol:', 'lol.gif', null, 'gif', 0, 0, 1), 
(186, ':lollone:', 'lollone.gif', null, 'gif', 0, 0, 1), 
(187, ':loveh:', 'loveh.gif', null, 'gif', 0, 0, 1), 
(188, ':macosx:', 'macosx.gif', null, 'gif', 0, 0, 1), 
(189, ':megalol:', 'megalol.gif', null, 'gif', 0, 0, 1), 
(190, ':mitico:', 'mitico.gif', null, 'gif', 0, 0, 1), 
(191, ':muletto:', 'muletto.gif', null, 'gif', 0, 0, 1), 
(192, ':napoleon:', 'napoleon.gif', null, 'gif', 0, 0, 1), 
(193, ':ninja:', 'ninja.gif', null, 'gif', 0, 0, 1), 
(194, ':nono2:', 'nono2.gif', null, 'gif', 0, 0, 1), 
(195, ':nyanya:', 'nyanya.gif', null, 'gif', 0, 0, 1), 
(196, ':ola:', 'ola.gif', null, 'gif', 0, 0, 1), 
(197, ':oops:', 'oops.gif', null, 'gif', 0, 0, 1), 
(198, ':pcthrow:', 'pcthrow.gif', null, 'gif', 0, 0, 1), 
(199, ':pcwhack:', 'pcwhack.gif', null, 'gif', 0, 0, 1), 
(200, ':pirate:', 'pirate.gif', null, 'gif', 0, 0, 1), 
(201, ':plane:', 'plane.gif', null, 'gif', 0, 0, 1), 
(202, ':please:', 'please.gif', null, 'gif', 0, 0, 1), 
(203, ':popcorn:', 'popcorn.gif', null, 'gif', 0, 0, 1), 
(204, ':pope:', 'pope.gif', null, 'gif', 0, 0, 1), 
(205, ':poppe:', 'poppe.gif', null, 'gif', 0, 0, 1), 
(206, ':protest:', 'protest.gif', null, 'gif', 0, 0, 1), 
(207, ':ranting2:', 'ranting2.gif', null, 'gif', 0, 0, 1), 
(208, ':rocket:', 'rocket.gif', null, 'gif', 0, 0, 1), 
(209, ':rofl:', 'rofl.gif', null, 'gif', 0, 0, 1), 
(210, ':saacrede:', 'saacrede.gif', null, 'gif', 0, 0, 1), 
(211, ':sadbye:', 'sadbye.gif', null, 'gif', 0, 0, 1), 
(212, ':scratch:', 'scratch.gif', null, 'gif', 0, 0, 1), 
(213, ':scream:', 'scream.gif', null, 'gif', 0, 0, 1), 
(214, ':senzaundente:', 'senzaundente.gif', null, 'gif', 0, 0, 1), 
(215, ':shark:', 'shark.gif', null, 'gif', 0, 0, 1), 
(216, ':shit:', 'shit.gif', null, 'gif', 0, 0, 1), 
(217, ':shrug:', 'shrug.gif', null, 'gif', 0, 0, 1), 
(218, ':smoke:', 'smoke.gif', null, 'gif', 0, 0, 1), 
(219, ':snack:', 'snack.gif', null, 'gif', 0, 0, 1), 
(220, ':sofa:', 'sofa.gif', null, 'gif', 0, 0, 1), 
(221, ':sorry:', 'sorry.gif', null, 'gif', 0, 0, 1), 
(222, ':spacecraft:', 'spacecraft.gif', null, 'gif', 0, 0, 1), 
(223, ':spam:', 'spam.gif', null, 'gif', 0, 0, 1), 
(224, ':spank:', 'spank.gif', null, 'gif', 0, 0, 1), 
(225, ':startrek:', 'startrek.gif', null, 'gif', 0, 0, 1), 
(226, ':stopspam:', 'stopspam.gif', null, 'gif', 0, 0, 1), 
(227, ':stretcher:', 'stretcher.gif', null, 'gif', 0, 0, 1), 
(228, ':sweatdrop:', 'sweatdrop.gif', null, 'gif', 0, 0, 1), 
(229, ':sweatdrop2:', 'sweatdrop2.gif', null, 'gif', 0, 0, 1), 
(230, ':swordfight:', 'swordfight.gif', null, 'gif', 0, 0, 1), 
(231, ':tease:', 'tease.gif', null, 'gif', 0, 0, 1), 
(232, ':think:', 'think.gif', null, 'gif', 0, 1, 1), 
(233, ':triste:', 'triste.gif', null, 'gif', 0, 0, 1), 
(234, ':tvhappy:', 'tvhappy.gif', null, 'gif', 0, 0, 1), 
(235, ':type:', 'type.gif', null, 'gif', 0, 0, 1), 
(236, ':urinal:', 'urinal.gif', null, 'gif', 0, 0, 1), 
(237, ':village:', 'village.gif', null, 'gif', 0, 0, 1), 
(238, ':vomit:', 'vomit.gif', null, 'gif', 0, 0, 1), 
(239, ':war:', 'war.gif', null, 'gif', 0, 0, 1), 
(240, ':welcome:', 'welcome.gif', null, 'gif', 0, 0, 1), 
(241, ':wheelchair:', 'wheelchair.gif', null, 'gif', 0, 0, 1), 
(242, ':whip:', 'whip.gif', null, 'gif', 0, 0, 1), 
(243, ':windows:', 'windows.gif', null, 'gif', 0, 0, 1), 
(244, ':worthy2:', 'worthy2.gif', null, 'gif', 0, 0, 1), 
(245, ':yeah:', 'yeah.gif', null, 'gif', 0, 0, 1), 
(246, ':zao:', 'zao.gif', null, 'gif', 0, 0, 1), 
(247, ':zzz:', 'zzz.gif', null, 'gif', 0, 0, 1);

#----------------------------
# Table structure for tstkf_localkey
#----------------------------
CREATE TABLE `tstkf_localkey` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `kname` varchar(30) collate latin1_general_ci NOT NULL,
  `kvalue` text collate latin1_general_ci NOT NULL,
  `ktype` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
#----------------------------
# No records for table tstkf_localkey
#----------------------------

#----------------------------
# Table structure for tstkf_localmember
#----------------------------
CREATE TABLE `tstkf_localmember` (
  `HASH` char(32) character set latin1 collate latin1_general_ci NOT NULL,
  `PASSWORD` mediumtext character set latin1 collate latin1_general_ci NOT NULL,
  `LANG` char(3) NOT NULL default 'eng',
  `TPP` smallint(6) NOT NULL default '20',
  `PPP` smallint(6) NOT NULL default '10',
  `HIDESIG` tinyint(1) NOT NULL default '0',
  `LASTREAD` int(10) NOT NULL default '0',
  `LEVEL` tinyint(2) unsigned NOT NULL default '0',
  PRIMARY KEY  (`HASH`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
#----------------------------
# No records for table tstkf_localmember
#----------------------------

#----------------------------
# Table structure for tstkf_membri
#----------------------------
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
#----------------------------
# No records for table tstkf_membri
#----------------------------

#----------------------------
# Table structure for tstkf_msghe
#----------------------------
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
#----------------------------
# No records for table tstkf_msghe
#----------------------------

#----------------------------
# Table structure for tstkf_newmsg
#----------------------------
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
#----------------------------
# No records for table tstkf_newmsg
#----------------------------

#----------------------------
# Table structure for tstkf_priority
#----------------------------
CREATE TABLE `tstkf_priority` (
  `HASH` binary(16) NOT NULL,
  `PRIOR` int(10) NOT NULL default '0',
  PRIMARY KEY  (`HASH`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
#----------------------------
# No records for table tstkf_priority
#----------------------------

#----------------------------
# Table structure for tstkf_purgatorio
#----------------------------
CREATE TABLE `tstkf_purgatorio` (
  `HASH` binary(16) NOT NULL,
  `TYPE` enum('1','2','3','4') collate latin1_general_ci NOT NULL,
  `DELETE_DATE` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`HASH`),
  KEY `DELETE_DATE` (`DELETE_DATE`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
#----------------------------
# No records for table tstkf_purgatorio
#----------------------------

#----------------------------
# Table structure for tstkf_reply
#----------------------------
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
#----------------------------
# No records for table tstkf_reply
#----------------------------

#----------------------------
# Table structure for tstkf_sez
#----------------------------
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
  `LAST_POST` int(10) unsigned NOT NULL default '0',
  `LAST_TITLE` tinytext collate latin1_general_ci,
  `LAST_HASH` binary(16) NOT NULL default '0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0',
  `LAST_POSTER_NAME` varchar(30) collate latin1_general_ci NOT NULL default '',
  `LAST_POSTER_HASH` binary(16) NOT NULL default '0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0',
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
#----------------------------
# No records for table tstkf_sez
#----------------------------


