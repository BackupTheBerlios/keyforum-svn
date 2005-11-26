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
		if (!is_array($var)) return(false); # Ritorna FALSE se non � un array
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

        function getpkey($sesname) {
                $query = "SELECT value FROM config WHERE subkey='" . $sesname . "' AND fkey='PKEY'";
                $risultato = mysql_query($query) or die("Query non valida: " . mysql_error());
                $riga = mysql_fetch_assoc($risultato);
                if ( !$riga['value'] ) die("pkey vuota!\n");
                return($riga['value']);
        }
        
 // ********************************************************************* 
  
  
// *********************************
// Personal User Data
// *********************************

	function GetUserData($sess_name,$sess_nick,$sess_password) {
	 
	 $userdata = array();
	 
	 if(!$sess_nick) { return $userdata; }
	 
	 // hex id
	 $hash=md5($sess_password.$sess_nick);
	 
	 
	 // all user data
	 $query = "SELECT * FROM {$sess_name}_localmember  WHERE HASH='$hash'";
	 $result = mysql_query($query) or die("error on query: " . mysql_error());
	 $userdata = mysql_fetch_assoc($result);
	 return $userdata;
	 
	 }


	function UpdateUserData($sess_name,$userdata) {
	
	if(!$userdata['HASH']){ return 0; }
	
	
	while (list ($chiave, $valore) = each ($userdata)) {
	     if($chiave <>"HASH" AND $chiave<>"PASSWORD"){$queryset .= "$chiave='$valore',";};
	}

	$queryset = substr($queryset,0,-1);
	$query="update {$sess_name}_localmember set $queryset where HASH='{$userdata['HASH']}'"; 
	$result = mysql_query($query) or die("error on query: " . mysql_error());
	
	return 1;
	
	}


// *******************************
// Redirector
// *******************************

function Redirect($pagetitle,$url,$msgtitle,$msg,$clickinvite) {

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
   <p class=\"redirectfoot\">(<a href=\"$url\">$clickinvite</a>)
   </p>
  </div>
 </body>
</html>";

echo $html;

}

}

?>