<?php
require ("testa.php");
require("lib/admin.php");
$PRIVKEY='AQQAAAABB1ZlcnNpb24GBAAAADEuOTEBCElkZW50aXR5BgIAAABpbwEHQ2hlY2tlZAEAAQdwcml2YXRlBAkAAAABBF9waGkGNQEAADE1NDA0MzgxMjE2NjQ2NTM0ODUyNzkxMjUzMTE1MjE4NjIyMTAzOTAzOTU1Mzg1NDg3OTAyODM4MjEzMzkzNDE2MzI4MjM1NDM0OTgxNDY1NDg5MTk1ODA5MjY5Nzg4OTAwOTQ2NDIzOTYzOTEzODE2MTA0Mzk5NzkzODcwNDU3NDAwODYxNTEzMDQyNTU4MDE3NDc1NTU5Njc0NzI5MDc0MzExOTMwOTIyMjgzMzUwOTg2NTM4MDI4NzIwMzE4NTExMDA1NjM0NzQwNzU3Mjk5MTQ0MzU2ODI4NDc5ODk5NTQ4Njc5MjA5NTcwOTc5OTYzODY4MTIzNTAwNDkwMjUwMTc4MjAxMDM4MTEyODYxODQ4ODE4ODM0OTQ4OTE1NjkyNzEzNjY1MzU4ODcxMzU3MTI5NgECX24GNQEAADE1NDA0MzgxMjE2NjQ2NTM0ODUyNzkxMjUzMTE1MjE4NjIyMTAzOTAzOTU1Mzg1NDg3OTAyODM4MjEzMzkzNDE2MzI4MjM1NDM0OTgxNDY1NDg5MTk1ODA5MjY5Nzg4OTAwOTQ2NDIzOTYzOTEzODE2MTA0Mzk5NzkzODcwNDU3NDAwODYxNTEzMDQyNTU4MDE3NDc1NTU5Njc3MjE3MTY4OTgxMTEyNTY5NjQ5MTM5OTA0Mzc2NTc3MjczMDcxNjc2NjA1NTg5Mzk4ODkxMTc4MDU3NDg0NjA0NDc2NjE0NzcxMzc1OTQ4NDYwMTE1NTgyMzkwNjA4OTI5NzQwNTYwODY0ODM5OTgwMzAyNTU5OTkyNDY0ODYwMzA5MjExMDk4NzY3NDcyOTA3NTIyMTMwNDUxOQECX3EGmwAAADExNTkxMDIzNTgwNTk2NjM2NjUxMTA0NjE5NDY2NTY0NzU4ODEzMDM3NTQ2NzUxMzE5MzA3MDI3Mjk5MjkzOTA3OTY3NDk3OTI2OTMwOTQ2NzcxMDYxMTYwMTA3NDk5MzEzNzM2MDAyMTk2NTEzMDEyMzg4OTI0MzExMTk5MjQ0MDA1MTI3NDA2MTIzNzM2NzExMzIxMjc5NzY3AQJfcAabAAAAMTMyODk5MjMxMTEyMTk4MzcwMDY3ODQ1NTg5MTg5MjA3Njg3MTg2MTg0NTI3OTUyNjIwMzE3NjE4MzE5ODM4NTE5OTk2NTQzMDAwMzY0NDIxMjAyOTUwMjUxMTczNTQ5Nzg3NjcxMDQ2Njk4NzY0MDk1MDgwNTcxMjUyNjEwMDk1OTc4MjY2NTQ0MTQzMzg3NzUxODY0NTM0NTcBA19kcAaaAAAANTgzNjE1MzQyNjkzMjk4MzAzMzMyODY0ODAyNjEwMDM2NjU2NzMxMDY2NTI5NTE0MDc3OTYyMjI4MjQ0MzQzMjg3ODM3NDgyMjY5NjMyMTIyNjUxNDY1MzE0MDYzMDEyMTc0Njc1Nzg5ODU3NzAzMzgyMjc1NDg0MTc2ODk1Mjc0NzY5MTM5NzQ0MDI5NTg2MjY0MDQzNDExMwECX3UGmgAAADYxMTA1NTM5MjQ1OTQ0NzM0MjYyNzg0NDA1MjE0OTI2NTA4MzA1MTQ0NTYxNTYwMTE1MTM4ODQ0NzY0OTgzNjY0NzQxODEyNDc5MTc3MTAyNzc4OTY3ODgyNDYxOTM2NDMxNTIxMjY5ODE1NjI5NTkwNzEwOTQxODk5Nzc2NDY0NjA3OTEzODI3OTc1MDM3MzY1Mzk0MjYxNzEBA19kcQaaAAAAMjA2MjAzNzM4MjMzNjMzMTk0NTU0NTcwMzMxODEzNTk5MjIzMzQxMzI1OTAzODAwMzQ0NTM5MTYzMDQxNDM4Njg4MDU4NzQyODkzNDYxNTY4ODkwNTUzNTI4MDczMjAyMTU4ODUzODUyOTUxOTg5Mjc1NDM1OTY1MjQ3NTI3MzI5MzgwMDE0OTk2MTAzOTUwOTI0MzU1NDAzNQECX2QGMwEAADgwMzg2NjI3MDM2NTMwNzM3MTM1NTgxNTU3OTgxMDYwNjAzMzE2MjIwNjUwMDQyNTIzNTAyMzA2NjIwMzkwNzQ2OTcxMjc2MDU0MTkzMjIyMTA4MTk3OTE1MjI3NTQ3ODYwMzQ4NzY0NzUzNjI4MTA0ODUyMjkzMDQ4MjU4MTgxMzQ5Mzg0ODg4ODQ5ODgzOTEyNTQ3NzQyNjMwMjI5Mzg4MjAzMzEwNzMxNjYxNTk2MzEwNDIwMTU2ODkzNzk5MzkyMTU5MDQxMTcyNzU3MzE3MzUzMDk4Nzg5MDgyODk0MzI5MTMxNDc3OTkzNzMwOTg2MjA0NzEyNDA5MjkwNDA5NDAxNDc5MzkzODQ3NzMxMDg3NzIyMDczODI4OTY5Mzg5OTMzNTI2OTA0NDA1MjA4MzMBAl9lAwEAAQA=';


$admin=new Admin(base64_decode($PRIVKEY));
$core=new CoreSock;
# ordine figlio auth=1 richiesto_permesso=0
$admin->EditCat(5,'Categoria di Servizio','',1,0);
$admin->EditSez(10,'Richieste di Iscrizione','In questa sezione possono scrivere anche i non autorizzati',10,5,0);
$admin->EditCat(15,'Categoria di prova','Proviamo fino allo sfinimento :P',15,0);
$admin->EditSez(20,'News','Solo chi ha i permessi può scrivere',20,15,1,1);
$admin->EditSez(25,'Spam','Cosa significa spam? hmm vediamoo....chi lo sa?',25,15,1);
$admin->EditSez(30,'Bugs?','La vita del rogrammatore narrata in: <b>A bugs life!</b>',30,15,1,0);
$admin->EditSez(35,'Cestino','Vuoi entrare dentro di me?',35,15,1,1);

# In un ora al massimo si possono scrivere 30 messaggi
$admin->ConfTable('ANTIFLOOD_base',1,'RANGE_TIME',1800);
$admin->ConfTable('ANTIFLOOD_base',1,'MAX_MSG',15);

# Al massimo 40 al giorno
$admin->ConfTable('ANTIFLOOD_base',2,'RANGE_TIME',86400);
$admin->ConfTable('ANTIFLOOD_base',2,'MAX_MSG',40);

# massimo 100 alla settimana
$admin->ConfTable('ANTIFLOOD_base',3,'RANGE_TIME',604800);
$admin->ConfTable('ANTIFLOOD_base',3,'MAX_MSG',100);

# Ai reply e thread hanno lo stesso gruppo AntiFlood. Se hai già sfruttato il tuo spazio con i reply lo hai sfruttato anche con il thread.
# L'antiflood è unico anche se i tipo sono due!! 
$admin->ConfTable('TYPE',3,'ANTIFLOOD','base');
$admin->ConfTable('TYPE',4,'ANTIFLOOD','base');

# I messaggi che ricevono i nodi non possono essere più vecchi di questa data
$admin->ConfTable('CORE','MSG','MAX_OLD',time()-3800);

# Questi dati servono solo per la parte in php
$admin->ConfTable('FORUM','DATA','NAME','Test Forum DanieleG');
$admin->ConfTable('FORUM','DATA','ADMIN_NAME','DanieleG');
$admin->ConfTable('FORUM','DATA','DESCRIZIONE','Questo è un forum di prova che serve per provare le funzioni');
$admin->ConfTable('FORUM','DATA','LOGO_URL','http://naruteamplus.altervista.org/v4/home/naruteam2.jpg');
$admin->ConfTable('FORUM','DATA','NOTE','Questo forum è aperto solo temporaneamente e serve per prova.<br>Non postate cose illegali plz :)');

#mio hash 0C9BDDA0649DA89CC4F289E57B1CC7A7
$danieleg=pack("H*",'7E025BF04C441DD381BEA1A65BB19126');
# Diamo i permessi di moderazione a me stesso!
$admin->EditPerm($danieleg,20,'CAN_WRITE_THREAD',1); # Può scrivere/spostare in NEWS
$admin->EditPerm($danieleg,35,'CAN_WRITE_THREAD',1); # Può scrivere/spostare in CESTINO
$admin->EditPerm($danieleg,10,'IS_MOD',1); # E' mod
$admin->EditPerm($danieleg,20,'IS_MOD',1); # E' mod
$admin->EditPerm($danieleg,25,'IS_MOD',1); # E' mod
$admin->EditPerm($danieleg,30,'IS_MOD',1); # E' mod
$admin->EditPerm($danieleg,35,'IS_MOD',1); # E' mod
$admin->EditPerm($danieleg,'CAN','EDIT_ALL_AVAT',1); # Può modificare tutti gli avatar, firme, e dati di tutti gli utenti (oltre che il titolo)
*/
$danieleg=pack("H*",'7E025BF04C441DD381BEA1A65BB19126');
$admin->AuthMem($danieleg);

$messaggio['BODY']=$core->Var2BinDump($admin->ReturnVar());
$messaggio['TITLE']='Valido daniele';
$messaggio['TYPE']=1;
$messaggio['_PRIVATE']=base64_decode($PRIVKEY);
print $core->AddMsg($messaggio);

include ("end.php");

?>