<?php
require_once("lib/lib.php"); # Librerie per creare la connessione MySQL

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/loose.dtd">
<html>
<HEAD>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=ISO-8859-1">

<?
echo "<title>";


if($userdata->TPP) {
$ThreadXPage=$userdata->TPP; // N° di thread per pagina
} else {$ThreadXPage = 20;}
if($userdata->PPP) {
$PostXPage=$userdata->PPP; // N° di post per pagina
} else {$PostXPage = 10;}
$UserXPage = 100;
$BoardXPage = 20;
$Section = 3; # Numero di pagine da visualizzare a sn e a ds dell'attuale (es. .. 2 3 [4] 5 6 ..)

/*$queryacbd="SELECT SUBKEY FROM config WHERE VALUE='".$SNAME."' LIMIT 1;";
$BNAME=$db->get_var($queryacbd);*/

$BNAME = $keyforum['nome'];

// carico la lingua per la testa
if(is_array($lang)){$lang += $std->load_lang('lang_testa', $blanguage );}else { $lang = $std->load_lang('lang_testa', $blanguage ); }

// carico le stringhe globali
if(is_array($lang)) {$lang += $std->load_lang('lang_global', $blanguage );} else {$lang = $std->load_lang('lang_global', $blanguage);};

// carico la lingua per la testa (accodandolo)
$lang += $std->load_lang('lang_testa', $blanguage );

  if (pack("H*",$_REQUEST[THR_ID])){
    $MSGID=mysql_escape_string(pack("H*",$_REQUEST[THR_ID]));
    $query="SELECT title, subtitle FROM {$SNAME}_newmsg WHERE EDIT_OF='$MSGID' ORDER BY DATE DESC LIMIT 1;";
    $result=$db->get_row($query);
    $title=$result->title;
    $title=secure_v($title);
    if(!trim($title)){$title="(untitled)";}
    $title2=" - ".$title;
  }else{
    if ($SEZ_DATA->ID)
      $title2=secure_v(" - ".$SEZ_DATA->SEZ_NAME);
  }
  echo ucfirst($BNAME);
  echo " Forum $title2</title>";
  
  $userlinks =array(
    	 'index.php' 		=> $lang['userlink_home']
  	,'gestip.php' 		=> $lang['userlink_ipman']
  	,'userlist.php?validati=1&amp;nonvalidati=1' 	=> $lang['userlink_usrlst']
  	,'boardlist.php'	=> $lang['userlink_brdlst']
  	);
  	if ($_SESSION[$SNAME]['sess_auth'])
  	{
  		$userlinks['options_forum.php'] = $lang['user_panel'];
  		$userlinks['shownewmsg.php']	= $lang['shownewmsg'];
  		$userlinks[] 					= $lang['userlink_wlcm'].' <b>'.$_SESSION[$SNAME]['sess_nick'].'</b>';
  		$userlinks['logout.php?SEZID='.$_REQUEST["SEZID"].'&amp;THR_ID='.$_REQUEST["THR_ID"]] = $lang['userlink_logout'];
  	}
  	else
  	{
  		$userlinks[] = $lang['userlink_wlcm'].' '.$lang['userlink_guest'];
  		$userlinks['login.php?SEZID='.$_REQUEST["SEZID"].'&amp;THR_ID='.$_REQUEST["THR_ID"]] = $lang['userlink_login'];
  		$userlinks['register.php'] = $lang['userlink_signup'];
	}

?>
<link type="text/css" rel="stylesheet" href="style_page.css">
<link rel="shortcut icon" href="favicon.ico">
</HEAD>
<body>

<script type="text/javascript" language="JavaScript">
<!--
var pname = "<?php echo md5($BNAME.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]); ?>";

// Exit confirm
function confirmThis(url) {
  if (confirm("<?php echo $lang["j_close_pup"]; ?>")) {
    window.location = url;
  }
}
// -->
</script>

<script type="text/javascript" src='global.js'></script>

<div class="borderwrap">
  <div id="logostrip">
    <div id="logographic" OnMouseOver="this.className='cursor';" onClick="location.href='index.php'"></div>
  </div>
  <div id="submenu">
    <p class="home">
<?php

  /* $querysetup="SELECT SUBKEY, FKEY, VALUE FROM config WHERE SUBKEY='SETUP' OR SUBKEY='".$BNAME."';";
   $responsetup=$db->get_results($querysetup);
   foreach($responsetup as $valuesetup){
      if(($valuesetup->FKEY=="BIND")AND($valuesetup->SUBKEY=="SETUP")){
         $bindsetup=$valuesetup->VALUE;
      }elseif(($valuesetup->FKEY=="PORTA")AND($valuesetup->SUBKEY=="SETUP")){
         $portsetup=$valuesetup->VALUE;
      }elseif($valuesetup->FKEY=="BIND"){
         $bindboard=$valuesetup->VALUE;
      }
   }*/
   $bindsetup = $config['WEBSERVER']['SETUP']['BIND'];
   $portsetup = $config['WEBSERVER']['SETUP']['PORTA'];
   $bindboard = $config['WEBSERVER'][$BNAME]['PORTA'];
   
   if($portsetup){
      if((!$bindsetup)OR($bindsetup==$_SERVER['REMOTE_ADDR'])OR($bindsetup==$bindboard)){
         $addrsetup=substr($_SERVER['HTTP_HOST'], 0, strlen($_SERVER['HTTP_HOST'])-strlen($_SERVER['SERVER_PORT'])-1);
         print '<a href="http://'.$addrsetup.':'.$portsetup.'/" target="_blank">Setup</a>';
      }
   }
?>

    </p>
      <form method="post" name="boardlinkform" action="">
       <p>
        <a href="search.php<?php if ($_REQUEST["SEZID"]) echo "?SEZID=".$_REQUEST["SEZID"];?>"><?php echo $lang['search']; ?></a>
        | <a href="gestip.php"><?php echo $lang['userlink_ipman']; ?></a> 
	| <a href="userlist.php?validati=1&amp;nonvalidati=1"><?php echo $lang['userlink_usrlst']; ?></a> 
	| <a href="boardlist.php"><?php echo $lang['userlink_brdlst']; ?></a>
        |<select class="forminput" name="boardlink" size="1"  onchange="if(document.boardlinkform.boardlink.selectedIndex)window.open(document.boardlinkform.boardlink.options[document.boardlinkform.boardlink.selectedIndex].value)">
          <option value="" selected="selected"><?php echo $lang['sel_otherbrd']; ?></option>
<?php
foreach($config['WEBSERVER'] as $nome_board=>$array)
{
	if(($nome_board != $BNAME)AND($config['SHARE'][$array['SesName']]['PKEY']))
	{
		$bindwsl = $config['WEBSERVER'][$nome_board]['BIND'];
		$portwsl = $config['WEBSERVER'][$nome_board]['PORTA'];
		if($portwsl)
		{
			if((!$bindwsl) OR ($bindwsl==$_SERVER['REMOTE_ADDR']) OR ($bindwsl==$bindboard))
			{
				$addrwsl=substr($_SERVER['HTTP_HOST'], 0, strlen($_SERVER['HTTP_HOST'])-strlen($_SERVER['SERVER_PORT'])-1);
				echo "<option value='http://$addrwsl:$portwsl/'>$nome_board</option>";
			}
		}
	}	  	
}
?>
        </select>
       </p>
      </form>
  </div>
</div>
<div id="userlinks">
  <p class="home"><b><a href="javascript:confirmThis('chiudi.php')"><?php echo $lang['userlink_close']; ?></a></b></p>
  <p><b><a href="index.php"><?php echo $lang['userlink_home']; ?></a></b> 
	| <a href='shownewmsg.php'><?php echo $lang['shownewmsg']; ?></a>
	<?	
	if($_SESSION[$SNAME]['sess_auth'])
	{

		echo "| <a href='options_forum.php'>{$lang['user_panel']}</a>";
	}
	?>	
	| <?php echo $lang['userlink_wlcm']; ?> 
   <?php
      if ($_SESSION[$SNAME]['sess_auth'])
        echo '<b>'.$_SESSION[$SNAME]['sess_nick'].'</b> ( <a href="logout.php?SEZID='.$_REQUEST["SEZID"].'&amp;THR_ID='.$_REQUEST["THR_ID"].'">'.$lang['userlink_logout'].'</a> )';
      else
        echo $lang['userlink_guest'].' <a href="login.php?SEZID='.$_REQUEST["SEZID"].'&amp;THR_ID='.$_REQUEST["THR_ID"].'">'.$lang['userlink_login'].'</a> <a href="register.php">'.$lang['userlink_signup'].'</a>'; 
    ?> 
	</p>
</div>


<form name="reloader" style="display:inline;float:right;" action="">
<select class="forminput" name="reload_value" size="1" onchange='setc(reload_cname,this.value);runit(reload_cname);'>
  <option value="null" selected="selected"><?php echo $lang['reload_no']; ?></option>
  <option value="60"><?php echo $lang['reload_60s']; ?></option>
  <option value="120"><?php echo $lang['reload_120s']; ?></option>
  <option value="180"><?php echo $lang['reload_180s']; ?></option>
  <option value="300"><?php echo $lang['reload_300s']; ?></option>
  <option value="600"><?php echo $lang['reload_600s']; ?></option>
</select>
</form>

<div class="navstrip">
  <img src="img/3.gif" alt="" /> <a href="index.php"><?php echo $lang['navstrp_findex']; ?></a>
<?php

$seznum=0;
if ($SEZ_DATA->ID) {
  $notlastid=$SEZ_DATA->ID;
  $seznum=1;
  while($notlastid){
    $querysez="SELECT ID, SEZ_NAME, FIGLIO FROM {$SNAME}_sez WHERE ID='$notlastid';";
    $notlast=$db->get_row($querysez);
    $notlastid=$notlast->ID;
    $sezvet[$seznum]="<img src='img/3.gif' alt=''> <a href='sezioni.php?SEZID=".$notlastid."'>".secure_v($notlast->SEZ_NAME)."</a>\n";
    $notlastid=$notlast->FIGLIO;
    $seznum++;
  }
  $seznum2=$seznum;
  while($seznum2){
    echo $sezvet[$seznum2];
    $seznum2--;
  }
}
if ($title) {
  if($title1->subtitle) $title=$title.", ".$title1->subtitle;
  echo "  <img src=\"img/3.gif\" alt=\"\" /> ".$title."\n";
}
?>
</div>

<?
// avvisi ai registrati
if ($_SESSION[$SNAME]['sess_auth'])
{
//print_r($userdata);
//echo $userdata->IS_AUTH;
// avvisi ai non validati
if(!$userdata->IS_AUTH)
{
$me=WhoIsMe();
if($me->is_auth)
{
 $userdata->IS_AUTH=$me->is_auth;
 $std->UpdateUserData($SNAME,$userdata);
 } else {
   $std->Notice($lang['user_info_auth']);
}
}
// avvisi per l'admin
if($userdata->LEVEL >=9)
{
// utenti in coda di validazione
if(!$db->get_var("SELECT is_auth FROM {$SNAME}_membri ORDER BY date DESC LIMIT 1")) 
  {$std->Notice($lang['user_to_auth']);}
}
}
?>


<table border="0" cellspacing="0" cellpadding="0" align="center" width="100%">


<?
//FUNZIONI
function userlinks($array)
{
	global $whereiam;
	foreach($array as $url=>$label)
	{
		if($whereiam == $url or $whereiam == substr($url,0,-4))
		{
			$return .="| <b>$label</b>";
		}
		else
		{
			$return .="| <a href='$url'>$label</a> ";
		}
	}
	return $return;
}
?>