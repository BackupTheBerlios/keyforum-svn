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
			global $db,$config;
			/*$query = "SELECT value FROM config WHERE subkey='$sesname' AND fkey='PKEY'";
			$pkey = $db->get_var($query);*/
			$pkey = $config['SHARE'][$sesname]['PKEY'];
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

function Error($ermsg,$txtsave="",$info="")
{

global $lang,$std,$blanguage,$sess_auth, $sess_nick,$sess_password,$SEZ_DATA,$userdata,$_ENV,$db;

if(!$blanguage) {$blanguage="eng";}

include_once("testa.php");

$lang += $std->load_lang('lang_error', $blanguage );


// errore o informazione ?
if(!$info){
$info=$lang['err_info'];
$errordiv ="<div class=\"errorwrap\"><h4>{$lang['err_is'] }</h4><p>$ermsg</p></div>";
$usefull="<h4>{$lang['err_usefull']}</h4>{$lang['err_usefull_list']}";
}	

// errore
echo"
<div id=\"keywrapper\">
<div class=\"borderwrap\">
<h3><img src='img/nav_m.gif' border='0'  alt='&gt;' width='8' height='8' />&nbsp;{$lang['err_fmessage']}</h3><p>$info</p>$errordiv";
	
	
// testo salvato
if($txtsave)
{
echo "<h4>{$lang['err_saved_main']}</h4>
<p>{$lang['err_saved_info']}</p>
<div class=\"fieldwrap\">
	<h4>{$lang['err_saved_main']}</h4>
	<form name=\"saveform\">
		<textarea cols=\"70\" rows=\"5\" name=\"tsaved\" tabindex=\"2\">".stripslashes($txtsave)."</textarea>
	</form>
	<p class=\"formbuttonrow1\"><input class=\"button\" type=\"button\" tabindex=\"1\" value=\"{$lang['err_select_all']}\" onclick=\"document.saveform.tsaved.select()\" /></p>
</div>$usefull";
}

echo "<p class=\"formbuttonrow\"><b><a href=\"javascript:history.go(-1)\">{$lang['err_back']}</a></b></p>
</div>
</div>";

include_once("end.php");
die();

}

function ForumJumper($forumid=0)
{
global $db,$_ENV;

include_once "lib/TreeClass.php";

$tree=new Tree;

$tree->AddNode(" 0","root");

$result = $db->get_results("select id,sez_name,figlio,ordine from {$_ENV['sesname']}_sez order by figlio,ordine ");

if ($result) foreach ( $result as $row )
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
    if ($l >0) { $output .= "&nbsp;&nbsp;|"; }
    $output .= str_repeat("-",$l*2).$forum[$fid]."\n";  
    $output .= "</option>";
    }  
}

$output .= "</optgroup></select></form>";


return $output;

}

// k_date: date in selected language
// Adapted from KronoClass, released on GPL license by Holosoft - Tommaso D'Argenio

	/** Date like function. Using the same format functionality 
	*  @access public
	*  @return string The date according with format given
	*  @param string format ->
	*	+ valid format parameter:
	*	+ %l (L lowercase): Day textual long
	*	+ %d: Day of month, 2 digits with leading zeros
	*	+ %F: Month textual Long
	*	+ %Y: Year, 4 digits
	*	+ %y: Year, 2 digits
	*	+ %m: Month numeric, 2 digits with leading zeros
	*	+ %D: Day textual short
	*	+ %M: Month textual short
	*	+ %n: Month numeric, without leading zeros
	*	+ %j: Day of month, without leading zeros
	*  @param timestamp $timestamp The time to transform
	*/
	function k_date($format="%l %d %F %Y",$timestamp=0)
	{	
		if($timestamp==0)
			$timestamp=time();
			

		if(!preg_match('/\%l|\%F|\%D|\%M/',$format))
		{
			return date(str_replace('%','',$format),$timestamp);
		}
		else
		{
			$out=$format;
			if(strstr($format,'%l'))
			{
				$this->abbr=false;
				$out=str_replace('%l',$this->n_to_day(date('w',$timestamp)),$out);
			}
			if(strstr($format,'%F'))
			{
				$this->abbr=false;
				$out=str_replace('%F',$this->n_to_month(date('n',$timestamp)),$out);
			}
			if(strstr($format,'%D'))
			{
				$this->abbr=true;
				$out=str_replace('%D',$this->n_to_day(date('w',$timestamp)),$out);
			}
			if(strstr($format,'%M'))
			{
				$this->abbr=true;
				$out=str_replace('%M',$this->n_to_month(date('n',$timestamp)),$out);
			}			
			if(strstr($format,'%Y'))
				$out=str_replace('%Y',date('Y',$timestamp),$out);
			if(strstr($format,'%y'))
				$out=str_replace('%y',date('y',$timestamp),$out);
			if(strstr($format,'%d'))
				$out=str_replace('%d',date('d',$timestamp),$out);
			if(strstr($format,'%m'))
				$out=str_replace('%m',date('m',$timestamp),$out);
			if(strstr($format,'%n'))
				$out=str_replace('%n',date('n',$timestamp),$out);
			if(strstr($format,'%j'))
				$out=str_replace('%j',date('j',$timestamp),$out);

			return $out;
		}
	}

	function n_to_month($month)
	{
	global $lang;
		if($month>12 || $month<1){ die('Month range not valid. Must be 1 to 12!');}

		if($this->abbr===true)
			return($lang['bmonth'.$month]);
		elseif($this->abbr!=true)
			return($lang['month'.$month]);
	}

	function n_to_day($day)
	{
	global $lang;
		if($day>6 || $day<0){die('Day range not valid. Must be 0 to 6!');}
		
		if($this->abbr===true)
			return($lang['bday'.$day]);
		elseif($this->abbr!=true)
			return($lang['day'.$day]);
	}


function RelativeDate($epoch) {
global $lang;
   $day    = date('j',$epoch);
   $today  = date('j',time());
   $yday  = date('j',strtotime("-1 day"));

   if (date('Y') != date('Y',$epoch)) return "";
   if (date('m') != date('m',$epoch)) return "";

   switch($day) {
       case $today:    return $lang['today'].",";                break;
       case $yday:    return $lang['yesterday'].",";            break;
       default: 
        return "";
           }
 	}


function PostDate($timestamp)
 {
 global $lang;
 return $this->RelativeDate($timestamp)." ".$this->k_date($lang['post_date'],$timestamp)." ".date($lang['post_time'],$timestamp);
 }

Function ListMod($modstring)
 {
 global $db,$SNAME;
         $matr=explode("%",$modstring);
 		for($counter=0,$tmp=count($matr);$counter<$tmp; $counter++){
           if (!$nick[$matr[$counter]]) {
             $modhash=mysql_escape_string(pack("H*",$matr[$counter]));
             if($modhash)
			 {
			 	$modnick = $db->get_var("SELECT AUTORE FROM {$SNAME}_membri WHERE HASH='$modhash';");
			 }
             $nick[$matr[$counter]] = $modnick;
           }
           if($counter>0 && $counter != $tmp-1){
              $modlist .= ", ";
           }
          if($matr[$counter]){ $modlist .= '<a href="showmember.php?MEM_ID='.$matr[$counter].'"><font color="#808080">'.secure_v($nick[$matr[$counter]])."</font></a>";}
         }
return $modlist; 
 }



function GetKFcookie($name,$sname)
    {
    	
    	if (isset($_COOKIE[$sname.$name]))
    	{
    		return urldecode($_COOKIE[$sname.$name]);
    	}
    	else
    	{
    		return FALSE;
    	}
    	
    }
    



}

?>