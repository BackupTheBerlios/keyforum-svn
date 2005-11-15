<?PHP
  ob_start('ob_gzhandler');
  include ("testa.php");
 ?>
 
 <table border=0 cellspacing=1 cellpadding=1 align=center>
  
  
 <?PHP
$vet_log[1]='print "Connessione riuscita con ".Num2Ip($ris[IP])." . Assegnato ID $ris[ACT_VAL].";';
$vet_log[2]='print "Connessione fallita con ".Num2Ip($ris[IP]).".";';
$vet_log[3]='print "Avviato Keyforum";';
$vet_log[4]='print "Bannato l\'utente con ip ".Num2Ip($ris[IP]).".";';
$vet_log[5]='print "Provo a connettermi con ".Num2Ip($ris[IP]).".";';
$vet_log[6]='print "Il nodo ".Num2Ip($ris[IP])." con ID $ris[ACT_VAL] si è disconnesso.";';
$vet_log[7]='print "Il nodo ".Num2Ip($ris[IP])." si è connesso al forum con ID $ris[ACT_VAL].";';
$vet_log[8]='print "Eseguo un messaggio amministrativo.";';
$vet_log[9]='print "Ricevuto un messaggio admin con firma non valida. Scartato.";';
$vet_log[10]='print "Il nodo ".Num2Ip($ris[IP])." mi sta inviando $ris[ACT_VAL] messaggi.";';
$vet_log[11]='print "Fine sessione di inserimento messaggi";';
$vet_log[12]='print "Il nodo con ID $ris[ACT_VAL] richiede una lista di IP validi.";';
$vet_log[13]='print "Il nodo con ID $ris[ACT_VAL] mi invia una lista di IP.";';
$vet_log[14]='print "Ridondanza ciclica con il nodo $ris[ACT_VAL].";';
$vet_log[15]='print "Il NODO $ris[ACT_VAL] ha richiesto un HASH REQ.";';
$vet_log[16]='print "In risposta all\'HASH REQ sono stati spediti $ris[ACT_VAL] HASH.";';
$vet_log[17]='print "Cancellati $ris[ACT_VAL] utenti non validi dal database.";';
$vet_log[18]='print "Reset dell\'indice del vettore HASH. $ris[ACT_VAL] messaggi nel DB.";';
$vet_log[19]='print "Offro $ris[ACT_VAL] HASH ai nodi connessi.";';
$vet_log[20]='print "Siamo all\'indice $ris[ACT_VAL] del vottore degli HASH.";';
$vet_log[21]='print "Mi offrono $ris[ACT_VAL] HASH.";';
$vet_log[22]='print "Richiedo $ris[ACT_VAL] HASH che mi hanno offerto e non ho.";';
$vet_log[23]='print "Richiesta di $ris[ACT_VAL] messaggi.";';
$vet_log[24]='print "Spediti $ris[ACT_VAL] messaggi.";';
$vet_log[25]='print "Ricevo una modifica di risposta da $ris[STRINGA].";';
$vet_log[26]='print "Ricevo una risposta da $ris[STRINGA].";';
$vet_log[27]='print "Ricevo una modifica di un thread da $ris[STRINGA].";';
$vet_log[28]='print "Ricevo un nuovo thread da $ris[STRINGA].";';
$vet_log[29]='print "l\'utente $ris[STRINGA] si è registrato duplicando una chiave.Scartato.";';
$vet_log[30]='print "l\'utente $ris[STRINGA] aggiunto al DB.";';
$vet_log[31]='print "l\'utente $ris[STRINGA] aggiunto al DB con l\'autorizzazione ADMIN.";';
$risultato=mysql_query("SELECT * FROM log ORDER BY ID DESC LIMIT 150;");
while($ris=mysql_fetch_assoc($risultato)) {
  print "<tr><tD bgcolor=#EEEEFF>".strftime("%d/%m/%y  - %H:%M:%S",$ris['DATA'])."</td>";
  print "<td bgcolor=#FFEEEE>$ris[LIVELLO]</td><td bgcolor=#FFEEEE>$ris[TIPO]</td><td bgcolor=#EEFFEE>";
  eval ($vet_log[$ris[ACT_ID]]);
  #eval("print \"ciao\".Num2ip(5645644);");
  print "</td></tr>";
  
}
  
  
  
  ?>
  
  </table>
  <br><br>
  * Un HASH REQ &egrave; una ricerca controllata all'interno dei tuo database secondo criteri precisi.
  
