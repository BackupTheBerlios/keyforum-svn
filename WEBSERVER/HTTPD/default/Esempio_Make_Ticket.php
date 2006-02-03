<?php
require ("testa.php");
require("lib/admin.php");
$PRIVKEY='AQQAAAABB1ZlcnNpb24GBAAAADEuOTEBCElkZW50aXR5BgIAAABpbwEHQ2hlY2tlZAEAAQdwcml2YXRlBAkAAAABBF9waGkGNQEAADE1NDA0MzgxMjE2NjQ2NTM0ODUyNzkxMjUzMTE1MjE4NjIyMTAzOTAzOTU1Mzg1NDg3OTAyODM4MjEzMzkzNDE2MzI4MjM1NDM0OTgxNDY1NDg5MTk1ODA5MjY5Nzg4OTAwOTQ2NDIzOTYzOTEzODE2MTA0Mzk5NzkzODcwNDU3NDAwODYxNTEzMDQyNTU4MDE3NDc1NTU5Njc0NzI5MDc0MzExOTMwOTIyMjgzMzUwOTg2NTM4MDI4NzIwMzE4NTExMDA1NjM0NzQwNzU3Mjk5MTQ0MzU2ODI4NDc5ODk5NTQ4Njc5MjA5NTcwOTc5OTYzODY4MTIzNTAwNDkwMjUwMTc4MjAxMDM4MTEyODYxODQ4ODE4ODM0OTQ4OTE1NjkyNzEzNjY1MzU4ODcxMzU3MTI5NgECX24GNQEAADE1NDA0MzgxMjE2NjQ2NTM0ODUyNzkxMjUzMTE1MjE4NjIyMTAzOTAzOTU1Mzg1NDg3OTAyODM4MjEzMzkzNDE2MzI4MjM1NDM0OTgxNDY1NDg5MTk1ODA5MjY5Nzg4OTAwOTQ2NDIzOTYzOTEzODE2MTA0Mzk5NzkzODcwNDU3NDAwODYxNTEzMDQyNTU4MDE3NDc1NTU5Njc3MjE3MTY4OTgxMTEyNTY5NjQ5MTM5OTA0Mzc2NTc3MjczMDcxNjc2NjA1NTg5Mzk4ODkxMTc4MDU3NDg0NjA0NDc2NjE0NzcxMzc1OTQ4NDYwMTE1NTgyMzkwNjA4OTI5NzQwNTYwODY0ODM5OTgwMzAyNTU5OTkyNDY0ODYwMzA5MjExMDk4NzY3NDcyOTA3NTIyMTMwNDUxOQECX3EGmwAAADExNTkxMDIzNTgwNTk2NjM2NjUxMTA0NjE5NDY2NTY0NzU4ODEzMDM3NTQ2NzUxMzE5MzA3MDI3Mjk5MjkzOTA3OTY3NDk3OTI2OTMwOTQ2NzcxMDYxMTYwMTA3NDk5MzEzNzM2MDAyMTk2NTEzMDEyMzg4OTI0MzExMTk5MjQ0MDA1MTI3NDA2MTIzNzM2NzExMzIxMjc5NzY3AQJfcAabAAAAMTMyODk5MjMxMTEyMTk4MzcwMDY3ODQ1NTg5MTg5MjA3Njg3MTg2MTg0NTI3OTUyNjIwMzE3NjE4MzE5ODM4NTE5OTk2NTQzMDAwMzY0NDIxMjAyOTUwMjUxMTczNTQ5Nzg3NjcxMDQ2Njk4NzY0MDk1MDgwNTcxMjUyNjEwMDk1OTc4MjY2NTQ0MTQzMzg3NzUxODY0NTM0NTcBA19kcAaaAAAANTgzNjE1MzQyNjkzMjk4MzAzMzMyODY0ODAyNjEwMDM2NjU2NzMxMDY2NTI5NTE0MDc3OTYyMjI4MjQ0MzQzMjg3ODM3NDgyMjY5NjMyMTIyNjUxNDY1MzE0MDYzMDEyMTc0Njc1Nzg5ODU3NzAzMzgyMjc1NDg0MTc2ODk1Mjc0NzY5MTM5NzQ0MDI5NTg2MjY0MDQzNDExMwECX3UGmgAAADYxMTA1NTM5MjQ1OTQ0NzM0MjYyNzg0NDA1MjE0OTI2NTA4MzA1MTQ0NTYxNTYwMTE1MTM4ODQ0NzY0OTgzNjY0NzQxODEyNDc5MTc3MTAyNzc4OTY3ODgyNDYxOTM2NDMxNTIxMjY5ODE1NjI5NTkwNzEwOTQxODk5Nzc2NDY0NjA3OTEzODI3OTc1MDM3MzY1Mzk0MjYxNzEBA19kcQaaAAAAMjA2MjAzNzM4MjMzNjMzMTk0NTU0NTcwMzMxODEzNTk5MjIzMzQxMzI1OTAzODAwMzQ0NTM5MTYzMDQxNDM4Njg4MDU4NzQyODkzNDYxNTY4ODkwNTUzNTI4MDczMjAyMTU4ODUzODUyOTUxOTg5Mjc1NDM1OTY1MjQ3NTI3MzI5MzgwMDE0OTk2MTAzOTUwOTI0MzU1NDAzNQECX2QGMwEAADgwMzg2NjI3MDM2NTMwNzM3MTM1NTgxNTU3OTgxMDYwNjAzMzE2MjIwNjUwMDQyNTIzNTAyMzA2NjIwMzkwNzQ2OTcxMjc2MDU0MTkzMjIyMTA4MTk3OTE1MjI3NTQ3ODYwMzQ4NzY0NzUzNjI4MTA0ODUyMjkzMDQ4MjU4MTgxMzQ5Mzg0ODg4ODQ5ODgzOTEyNTQ3NzQyNjMwMjI5Mzg4MjAzMzEwNzMxNjYxNTk2MzEwNDIwMTU2ODkzNzk5MzkyMTU5MDQxMTcyNzU3MzE3MzUzMDk4Nzg5MDgyODk0MzI5MTMxNDc3OTkzNzMwOTg2MjA0NzEyNDA5MjkwNDA5NDAxNDc5MzkzODQ3NzMxMDg3NzIyMDczODI4OTY5Mzg5OTMzNTI2OTA0NDA1MjA4MzMBAl9lAwEAAQA=';

/* Questo pezzo è commentato perchè le chiavi dei bigliettai si generano una volta sola
# $forum_id deve avere un valore
print $forum_id;

$admin=new Admin(base64_decode($PRIVKEY));
$core=new CoreSock;
# Si generano delle chiavi non protette da password
$risp=$core->GenRsaKey('',1);
# Si stampa la privata così ce la salviamo
print $risp[priv];
# Questa funzione permette di salvare chiavi pubbliche da usare per validare utenti o generare ticket.
# Ad ogni chiave che inseriamo nel nostro forum dobbiamo assegnargli un ID che sarà unico.
# I parametri della funzione KeyRing sono (PKEY_ID,'TICKET','',CHIAVE_PUBBLICA)
$admin->KeyRing(1,'TICKET','',$risp[pub]);
# Si crea il messaggio admin per modificare sti dati
$messaggio['BODY']=$core->Var2BinDump($admin->ReturnVar());
$messaggio['TITLE']='Aggiungo chiave pubblica per ticket';
$messaggio['TYPE']=1;
$messaggio['_PRIVATE']=base64_decode($PRIVKEY);
print "\n<br>Aggiungo un messaggio admin:";
$risp=$core->AddMsg($messaggio);
print $risp[ERRORE]."\n<br>";

print "\n<br>Aggiungo un nuovo ticket:";
*/

# segue la chiave privata usata per generare i ticket
$private='AQQAAAABB1ZlcnNpb24GBAAAADEuOTEBCElkZW50aXR5BgIAAABpbwEHQ2hlY2tlZAEAAQdwcml2YXRlBAkAAAABBF9waGkGNQEAADExODMzMzIzNzg2MTQ3NzgyNDU3Mjk0Mjc4ODk1NzY4NDM3OTM4MzQ5MDU4OTYwNDUwNzc1OTM2NDkxOTI3MjU0MzE5MzI4Nzk3ODA3NzUxOTIxNjc1MTExNTQzNDgzMDYxNTUyOTE4Nzc0NTA5MjE2ODk0MTE0MzU0OTY4MjI1MDEwODI5NjU0NTI2NTQ3Mzc2MTc3NzkwNDY2MDIwMDEyNjAyODk4NjY0MjcwMjA1MjA3NzUyNTE5NjQ5NzM4Njg4OTE1Mzc2Mjc2NjAzNjUxMjYxODQ3NTYwNjY3NzYwMTg0MDQzMTkxMTk2MzkxNDAxNDUzOTE2NzE3NjI0ODM0NTU1NjQxMTEzMzIyODI0NTM2ODY5MTc3MTY4ODA5OTUyOTY0NDc5NDExNjQzMzM2MzMyOAECX24GNQEAADExODMzMzIzNzg2MTQ3NzgyNDU3Mjk0Mjc4ODk1NzY4NDM3OTM4MzQ5MDU4OTYwNDUwNzc1OTM2NDkxOTI3MjU0MzE5MzI4Nzk3ODA3NzUxOTIxNjc1MTExNTQzNDgzMDYxNTUyOTE4Nzc0NTA5MjE2ODk0MTE0MzU0OTY4MjI1MDEwODI5NjU0NTI2NTQ3Mzc2MTc3NzkwNDY4MjQwOTY3MTc3NDgxNzU2NjUxNjU2NTk5NTE3MDE3NTgyMTI5MzMzNzMyNTUwNjQ4MzA2NTAxNjA1OTQ0MDMzNjIyMjAwNjc4NDkzOTgzMDkxNjk1MzM4MjkzMDM1NDA3OTMzMjg3MTAwODI4MzY4MDYxNzMyNDUyMjU0NTg4NzM2MTc0MTU5ODU1NjQ3MjY5NzA4NDg4ODk4MwECX3EGmwAAADEzMzM2OTc4NjY0ODUzNDA0Mzc5NDU0NTA5NDQ1OTA1ODY5OTQ5Njg3NTY2NDc2MDk2MTA1NTI2OTI2MTAwODc2NzQyMjkxMDAwMTI2MzkyMTU5NDAxMjE3Njk1NjY1NzM1MjE4NjU4MTM3NjI3NDQwMTU4MDA4ODA4OTU2MTk4MTcwNzkxNDMwNDIxOTQ4MjcxMzUyMTczNTI3AQJfcAaaAAAAODg3MjU2NzA4MDk3NzUxOTQzNTA1OTQwODE5OTA3MzQ1Mzk1Njc2MDYwNTI2NzYyMDkyMjk3NjUxNDg2Mzg1MjgwMjExMzk0NDM4MTUyNjc5MzYzODE1MDY5NTUyMTE2Nzg2NTg2NzMxNDI0NTEwNzIzMTA3MDM0NDg5NzkxNzUwMjg1MDYzODQ4OTczMDMwOTI5OTM1MjEyOQEDX2RwBpoAAAA1OTQ1MDU0NTIyMjg0NTk5NzA2Mjk5NzA1MzkxNTQ4NDc3NzA4ODI0NDU3MzE1OTc0NzU2MTAyMTY2NjcyNTExMjI0MTgyMjEyMTc5Nzc2MDk3MzA0MzAzMDg4MjE2MTM4MTI3OTg2MTk2NjU5MDY4ODI4ODc1MTcwNTk4MzIyMzQxNjE4OTM0MTU5MzIyNjI4MDE1NjYyMTQ1AQJfdQaaAAAAODM3NzE3NTUwODkzMzE1MzA2NzcxNTI1MDU5NjU0MTk5ODU5MDIwMTI2MzI3NjM2NTIyMzI3ODg0NzkzMTM5OTEzODUzMzg4MDg3OTIzODkxMDU2MjQwNzEwMzk5NTgxMDczMTE5MDAzMjA4ODI0MTc0MTMwNTA1NTE3Njk0NzEwMjQ4MzE0NTYzODU4NTM1NDYzNzcxMzc4OAEDX2RxBpoAAAAxMTM4NTk5NTAzMDI2NjA3ODMyMjYzNDIzNDE1MDE1MDgwNjc3NjA5NjI0MDk2ODI3MTAwODgwNzcxOTUzNzcyNzYwMDE1MjMwMjYyNDAzODk1OTk1Mzg5MDYyNzc3NTExNzY1MDg1MjUzODI2NDcyNDkxNjMxNTg2NTI1MzIwNDg3MTM4MjI4MDcyODU2NTYzMTM1NTYzMjgzAQJfZAYzAQAAODk4ODI0ODc0NjExOTU0NDg0ODMxNjM1ODc1MDQ5NzQ3MjI3NjI4NjkyNDI1NzMwODY5MDE0NjMwNzcwNjE2MTcxMDQyNTk4MTU4MDkzNzM0MzE5NTI0OTI4ODcxNjA5NjMxMzU5ODM3MzM2MjY2MjU3MjExOTc1ODE1NjczOTU2NzU1ODc4NjY3NTE4MzkxNzI3MDA5Nzk1MDc1MjY0NzAxNDI0MDc0MTk4MzQ3MjE2NDQ1NTUwNDk1MzkwMzc3ODU0MDQ3OTg5NzYwNDg2MDc2Mjg0ODAzMzQ3Nzg5OTUzNzA4MDg4NjQ4Nzg4MjYzMDY0MTg2NjAzMjk3OTI5ODE3MDE5MTEwNzYzNzA2MzExNjE4Mzk1MTY1MjcwNTQ4NjM0NDY4NzUwNjQ3MTIzNDMwNQECX2UDAQABAA==';
# l'id deve essere sempre differente, cioè, non ci possono essere TICKET che hanno tutti gli stessi dati
# l'ID può essere sempre uguale se decidiamo di generare ticket che hanno date di scadenza differenti gli uni dagli altri.
# Un consiglio per tutti:
# E' preferibile generare migliaia di ticket che però entrano in vigore solo quando raggiungono la loro effetiva data di validità.
# Cioè, mi spiego meglio. Ammettiamo che devo pertire per un mese. COme risolvo il problema dei ticket?
# Genero alcuni ticket che entrano in vigore subito e altri che entrano in vigore sempre più tardi.
# Alcuni possono entrare in vigore anche tra un mese. Prima di un mese questi ticket non verrano considerati da keyforum e quindi non saranno distribuiti!
# E' importante capire che i ticket futuri non possono essere distribuiti.
# Per quanto riguarda il periodo di validità dei ticket non so che dire.
# Penso che nello stesso periodo di tempo non ci debbano essere più di 4-5 ticket validi per utente (e sono anche tanti!)...però sono cose che
# vanno sperimentate con il tempo.
$id=2;

# Parliamo della funzione MakeTicket
# Parametri (START_DATE,END_DATE,KEY_ID,ID,PRIVATE_KEY,[password]);
# START_DATE =  La data di entrata in vogore del biglietto.
#           Se vale 0 viene usata la data corrente e quindi entra in vogore subito.
# END_DATE =    Oltre la data specificata i ticket non sono più validi e vengono cancellati automaticamente.
#           Se vale meno di 10000 viene considerato come la durata di validità in ore.
#           Dunque viene fatto un calcolo del genere START_DATE + ( END_DATE * 3600)
# KEY_ID    = L'identificativo della chiave pubblica che abbiamo salvato nel database prima.
#           Se l'id è errato darà errore che non è valido nulla.
# ID        = Nel caso i dati precedenti sono uguali usiamo questo campo per cambiare l'hash finale. Altrimenti otteniamo un ticket uguale.
# PRIVATE_KEY= La chiave priivata decodificata da base64.
# Se la private key è protetta da password la inseriamo
print $core->MakeTicket(0,3,1,$id++,base64_decode($private));
print $core->MakeTicket(0,3,1,$id++,base64_decode($private));
print $core->MakeTicket(0,3,1,$id++,base64_decode($private));
print $core->MakeTicket(0,3,1,$id++,base64_decode($private));
print $core->MakeTicket(0,3,1,$id++,base64_decode($private));
print $core->MakeTicket(0,3,1,$id++,base64_decode($private));
print $core->MakeTicket(0,3,1,$id++,base64_decode($private));
print $core->MakeTicket(0,3,1,$id++,base64_decode($private));
print $core->MakeTicket(0,3,1,$id++,base64_decode($private));
print $core->MakeTicket(0,3,1,$id++,base64_decode($private));
print $core->MakeTicket(0,3,1,$id++,base64_decode($private));
print $core->MakeTicket(0,3,1,$id++,base64_decode($private));
print $core->MakeTicket(0,3,1,$id++,base64_decode($private));
print $core->MakeTicket(0,3,1,$id++,base64_decode($private));
print $core->MakeTicket(0,3,1,$id++,base64_decode($private));
include ("end.php");

?>