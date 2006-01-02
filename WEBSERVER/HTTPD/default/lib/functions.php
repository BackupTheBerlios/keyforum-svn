<?php
/*
| -------------------------------------------------------------------------
| KeyForum
| Web:       http://www.keyforum.net/
| Licence:   GPL v2.0
| -------------------------------------------------------------------------
| Module:     Global Funtions
| -------------------------------------------------------------------------
*/

/*
+--------------------------------------------------------------------------
|   > KeyForum - Global Funtions
+--------------------------------------------------------------------------
*/

class FUNC {


	function FUNC() {


	}


  /*-------------------------------------------------------------------------*/
  // Carica l'array con la lingua specifica di un modulo
  /*-------------------------------------------------------------------------*/

	function load_lang($module, $lang_id) {
	    require "lang/".$lang_id."/".$module.".php";

		foreach ($lang as $key => $val) {
			$lang_array[$key] = stripslashes($val);
		}
	
		unset($lang);
		return $lang_array;
	}
  
  
 
 // ************************************************************
 // funzioni per trasformare array <-> stringhe binarie
 // ***********************************************************

 
   /*-------------------------------------------------------------------------*/
   // da ARRAY a stringa BINARIA
  /*-------------------------------------------------------------------------*/
	function var2binary($var) {
		return "\x52\x03".$this->_vardump($var);
		# Inizio il dump di tutto il contenuto
	}
 
    /*-------------------------------------------------------------------------*/
    // da stringa BINARIA a ARRAY
  /*-------------------------------------------------------------------------*/
	function binary2var(&$stringa,&$var) {
		if ($this->_substr($stringa,0,2,'')=="\x52\x03") {
			$var=$this->_strparse($stringa);
			return true;
		}
		return false;
	}

 
	function _vardump($var) {
		if (is_array($var)) return ("\x01".$this->_arraydump($var));
		if(is_int($var)) return ("\x02".pack("i",$var));
		return ("\x03".pack("I", strlen("$var"))."$var");
	}
	
	
	function _arraydump($var) {
		if (!is_array($var)) return(false); # Ritorna FALSE se non è un array
		$indice=0;
		$stringa='';
		foreach ($var as $key => $value) {
			$indice++;
			$stringa.=chr(strlen("$key"))."$key".$this->_vardump($value);
		}
		return pack("I",$indice).$stringa;
	}
 
	function _strparse(&$stringa) {
		list(,$tipo)=unpack("C",$this->_substr($stringa,0,1,''));
		if ($tipo==3) {
			list(,$dim)=unpack("I",$this->_substr($stringa,0,4,''));
			return $this->_substr($stringa,0,$dim,'');
		} elseif($tipo==2) {
			list(,$dim)=unpack("i",$this->_substr($stringa,0,4,''));
			return $dim;
		} elseif($tipo==1) {
			return ($this->_arrayparse($stringa));
		}
		return(false);
	}
 
 
	function _arrayparse(&$stringa) {
		list(,$numero)=unpack("I",$this->_substr($stringa,0,4,''));
		$vettore=array();
		for($index=0; $index<$numero;$index++) {
			list(,$dim_key)=unpack("C",$this->_substr($stringa,0,1,''));
			$key=$this->_substr($stringa,0,$dim_key,'');
			$vettore[$key]=$this->_strparse($stringa);
		}
		return ($vettore);
	}
	 

	function _substr(&$stringa,$start,$length,$replace) {
		$ritorno=substr($stringa,$start,$length);
		$stringa=substr_replace($stringa,$replace,$start,$length);
		return $ritorno;
	}

    /*-------------------------------------------------------------------------*/
    // PKEY della sessione, in Base64
    /*-------------------------------------------------------------------------*/

        function getpkey($sesname) 
		{
			global $db;
			$query = "SELECT value FROM config WHERE subkey='$sesname' AND fkey='PKEY'";
			$pkey = $db->get_var($query);
			if(!$pkey) die("pkey vuota!\n");
			return($pkey);
        }
        
 // ********************************************************************* 
  
  
// *********************************
// Personal User Data
// *********************************

	function GetUserData($sess_name,$sess_nick,$sess_password) 
	{
		global $db;
		$userdata = array();
		
		if(!$sess_nick) { return $userdata; }
		
		// hex id
		$hash=md5($sess_password.$sess_nick);
				
		// all user data
		$query = "SELECT * FROM {$sess_name}_localmember  WHERE HASH='$hash'";
		$userdata = $db->get_row($query);
		return $userdata;
	 }


	function UpdateUserData($sess_name,$userdata) 
	{
		global $db;
		if(!$userdata->HASH)
		{
			return FALSE; 
		}
		while (list ($chiave, $valore) = each ($userdata)) 
		{
			 if($chiave <>"HASH" AND $chiave<>"PASSWORD"){$queryset .= "$chiave='$valore',";};
		}
		$queryset = substr($queryset,0,-1);
		
		$db->query("update {$sess_name}_localmember set $queryset where HASH='{$userdata->HASH}'"); 
		return TRUE;
	}


// *******************************
// Redirector
// *******************************

function Redirect($pagetitle,$url,$msgtitle,$msg,$clickinvite="") {

global $std,$blanguage;
if(!$blanguage) {$blanguage="eng";}

$lang = $std->load_lang('lang_functions', $blanguage );

if(!$clickinvite) {$clickinvite=$lang['redirect_invite'];}

$html="
<html>
 <head>
  <link rel=\"shortcut icon\" href=\"favicon.ico\">
  <title>$pagetitle</title>
   <meta http-equiv='refresh' content='2; url=$url'>
  <link type=\"text/css\" rel=\"stylesheet\" href=\"style_page_redirect.css\">
 </head>
 <body>
  <div id=\"redirectwrap\">
   <h4>$msgtitle</h4>
   <p>
   $msg
   </p>
   <p class=\"redirectfoot\">{$lang['redirect_wait']}<br>(<a href=\"$url\">$clickinvite</a>)
   </p>
  </div>
 </body>
</html>";

echo $html;

}

// *******************************
// GetUserColor, ritorna la coppia di colori a partire dal hash
// *******************************

function GetUserColor($hash) {
  $userhash=unpack("H32hex",$hash);
  // colori normali
  $usercolor['sx_color']=substr($userhash['hex'],0,6);
  $usercolor['dx_color']=substr($userhash['hex'],-6);
  // colori invertiti
  $usercolor['sx_color_i']=dechex(16777215- hexdec($usercolor['sx_color']));
  $usercolor['dx_color_i']=dechex(16777215- hexdec($usercolor['dx_color']));
  return $usercolor;
}


//***********************************
// Gestione errori
//***********************************

function Error($ermsg,$txtsave="")
{

global $std,$blanguage,$sess_auth, $sess_nick,$sess_password,$SEZ_DATA,$userdata;

if(!$blanguage) {$blanguage="eng";}

$lang = $std->load_lang('lang_functions', $blanguage );

include_once("testa.php");

// errore
echo"
<div id=\"keywrapper\">
<div class=\"borderwrap\">
	<h3><img src='style_images/1/nav_m.gif' border='0'  alt='&gt;' width='8' height='8' />&nbsp;Messaggio Forum</h3><p>E' stato riscontrato un errore.</p>
	<div class=\"errorwrap\">
		<h4>L'errore riscontrato &#232;:</h4><p>$ermsg</p>
	</div>";
	
// testo salvato
if($txtsave)
{
echo "<h4>Informazioni messaggio salvate!</h4>
<p>Le informazioni del messaggio sono state salvate. In alcuni casi, premere il tasto 'indietro' sul vostro browser significa anche la perdità di tutti i dati non salvati. Ti consigliamo di selezionare e salvare quello che non vuoi perdere.</p>
<div class=\"fieldwrap\">
	<h4>Informazioni messaggio salvate!</h4>
	<form name=\"mehform\">
		<textarea cols=\"70\" rows=\"5\" name=\"saved\" tabindex=\"2\">".stripslashes($txtsave)."</textarea>
	</form>
	<p class=\"formbuttonrow1\"><input class=\"button\" type=\"button\" tabindex=\"1\" value=\"Seleziona tutto\" onclick=\"document.mehform.saved.select()\" /></p>
</div>";
}

// link utili
echo "<h4>Link utili</h4>
	<ul>
		<li><a href=\"http://www.keyforum.net/\">Il sito di supporto ufficiale</a></li>
		<li><a href=\"http://www.keyforum.net/forum/\">Il forum di supporto ufficiale</a></li>
	</ul>
	<p class=\"formbuttonrow\"><b><a href=\"javascript:history.go(-1)\">Torna indietro</a></b></p>
</div>
</div>";

include_once("end.php");
die();

}

function ForumJumper($forumid=0)
{
global $db;

include "TreeClass.php";

$tree=new Tree;

$tree->AddNode(" 0","root");

$result = $db->get_results("select id,sez_name,figlio,ordine from keyfo_sez order by figlio,ordine");

foreach ( $result as $row )
{
$tree->AddNode(" ".$row->id," ".$row->figlio);
$forum[$row->id+0]=$row->sez_name;
}

/* Draw tree */

$ris=$tree->drawTree();

$output .= "<form method='POST' name='jumpform' action=''>";
$output .= "<select name='forumjump' class='content' size='1' onchange='location.href=document.jumpform.forumjump.options[document.jumpform.forumjump.selectedIndex].value' style='font-family: Verdana; font-size: 8pt'>";
$output .= "<optgroup label='Salta a un forum'>"; 			


while (list ($key, $value) = each ($ris)) {
  $l=$value['lev']-4;  
  if ($l >=0) 
    {
    $fid=trim($value['id'])+0;
    if ($fid == $forumid) {$selected="selected";} else {$selected="";}	
    $output .= "<option  $selected value='sezioni.php?SEZID=$fid'>";
    if ($l >0) { $output .= "|"; }
    $output .= str_repeat("-",$l).$forum[$fid]."<br>";  
    $output .= "</option>";
    }  
}

$output .= "</optgroup></select></form>";


return $output;

}



}

?>