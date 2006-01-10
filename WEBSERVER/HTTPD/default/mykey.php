<?php

include ("testa.php");

// carico la lingua per la index
$lang += $std->load_lang('lang_index', $blanguage );

$corereq['RSA']['GENKEY']['CONSOLE_OUTPUT']=0;

$coresk = new CoreSock;

if ( !$coresk->Send($corereq) ) $std->Error("Errore in send!");

$coreresp = $coresk->Read(60);

if ( !$coreresp ) $std->Error("Errore in read!");

echo "pub: " . $coreresp['RSA']['GENKEY']['pub'] . "<br>";
echo "priv: " . $coreresp['RSA']['GENKEY']['priv'] . "<br>";


//include("end.php");
?>
