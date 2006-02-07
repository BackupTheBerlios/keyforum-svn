<?php
require ("testa.php");
require("admin.php");

// chiave privata Admin
// va inserita in un file chiamato pkeytemp.php
// contenente
//$PRIVKEY=".....";
include("pkeytemp.php");



$admin=new Admin(base64_decode($PRIVKEY));
$core=new CoreSock;

// $admin->EditSez(id,"nnnn","dddd",oo,ff);
// id = ID sezione, progressivo
// nnnn = nome sezione
// dddd = descrizione sezione
// oo = ordine (posizione rispetto alle altre nella stessa sottosezione) - se >9000 il forum è una categoria, può contenere altri forum, ma non messaggi.
// ff = figlio di (forum a cui questo sottoforum appartiene, se 0 è un categoria principale)
// (opzionali: auth=1,richiesto_permesso=0)

$admin->EditSez(1,'Generale','categoria: Generale',9001,0);
$admin->EditSez(2,'Relax Zone','categoria: Relax Zone',9002,0);
$admin->EditSez(10,'News','novità e annunci',0,1);
$admin->EditSez(20,'KeyForum Tech','richieste di aiuto e informazioni su KeyForum',2,1);
$admin->EditSez(21,"Bug Report","Per favore aprite una discussione per ogni bug segnalato",0,20);
$admin->EditSez(22,"Richieste & Suggerimenti","Per favore aprite una discussione per ogni richiesta",0,20);
$admin->EditSez(30,"Community","chiacchere su ogni argomento tra utenti di KeyForum",0,2);
$admin->EditSez(40,"Manicomio","arena di spam selvaggio ed insensato",4,2);
$admin->EditSez(50,"Ridere per Ridere","barzellette ed umorismo",0,2);
$admin->EditSez(60,"Una Topa per KeyForum","foto erotiche.... SEVERAMENTE VIETATO IL PORNO !",0,2);
$admin->EditSez(70,"Cestino","per i messaggi inutili o indesiderati",0,1);
$admin->EditSez(80,"Sport","Area dedicata alle discussioni riguardanti il mondo dello sport",9003,0);
$admin->EditSez(81,"Calcio","Area dedicata alle discussioni riguardanti il mondo del calcio. Parliamo di tutto quello che ruota intorno allo sport più amato dagli italiani.",1,80);
$admin->EditSez(82,"Automobilismo & Motociclismo","Area dedicata alle discussioni riguardanti gli sport automobilistici e motociclistici.",2,80);
$admin->EditSez(83,"Altri sport","Il luogo dove parlare di tutti gli altri sport di cui siete appassionati.",3,80);
$admin->EditSez(90,"Multimedia","il mondo in digitale",9001,2);
$admin->EditSez(91,"Cinema, TV & Teatro","Area dedicata alla recensione, discussione e analisi di film, programmi televisivi e opere teatrali",1,90);
$admin->EditSez(92,"Libri & Comics","Area dedicata alla recensione e discussione di opere letterarie che vi hanno colpito, alla segnalazione dei vostri autori preferiti e all'universo dei comics",2,90);
$admin->EditSez(93,"Musica","Area dedicata alla recensione e discussione di album musicali che vi hanno colpito ed alla segnalazione dei vostri cantanti e gruppi preferiti",3,90);
$admin->EditSez(94,"Giochi","Area per discutere, recensire ed analizzare i giochi da voi provati",4,90);
$admin->EditSez(95,"Playstation","Giochi per tutte le versioni di playstation",0,94);
$admin->EditSez(96,"Xbox","Giochi per tutte le versioni di Xbox",0,94);
$admin->EditSez(97,"PC","Giochi per Personal Computer",0,94);
$admin->EditSez(98,"Emulazione","Emulatori di console, arcade, computer, ecc...",0,94);
$admin->EditSez(99,"Multiplayer","Giochi in rete",0,94);


$risp=$admin->Send2Core("edit sezioni");

if($risp[ERRORE])
{
$std->Error($risp[ERRORE]);
} else {
$std->Error("","","comando eseguito...");
}

include ("end.php");

?>