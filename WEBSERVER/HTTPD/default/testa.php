<?php
include ("lib/lib.php"); # Librerie per creare la connessione MySQL

CheckSession();
?>
<!-- v.0.22 -->
<html>
<head>
<title><?php
$blanguage='eng'; // Lingua di visualizzazione
// carico la lingua per la testa
$lang = $std->load_lang('lang_testa', $blanguage );
$ThreadXPage = 20;
$PostXPage = 10;
$UserXPage = 100;
$BoardXPage = 20;
$Section = 3; # Numero di pagine da visualizzare a sn e a ds dell'attuale (es. .. 2 3 [4] 5 6 ..)
$SNAME=$_ENV["sesname"];

$queryacbd="SELECT SUBKEY FROM config WHERE VALUE='".$SNAME."' LIMIT 1;";
$responseacbd=mysql_query($queryacbd) or Muori ($lang['inv_query'] . mysql_error());
$valueacbd=mysql_fetch_assoc($responseacbd);
$BNAME=$valueacbd['SUBKEY'];

  if (pack("H*",$_REQUEST[THR_ID])){
    $MSGID=mysql_escape_string(pack("H*",$_REQUEST[THR_ID]));
    $query="SELECT title FROM {$SNAME}_newmsg WHERE EDIT_OF='$MSGID' ORDER BY DATE DESC LIMIT 1;";
    $risultato=mysql_query($query) or Muori ($lang['inv_query'] . mysql_error());
    $title=mysql_fetch_assoc($risultato);
    $title=secure_v($title["title"]);
    echo $title." - ";
  }else{
    if ($SEZ_DATA['ID'])
      echo $SEZ_DATA['SEZ_NAME']." - ";
  }
  echo $BNAME;
?> Forum</title>
<link type="text/css" rel="stylesheet" href="style_page.css" />
</head>
<body>

<script type="text/javascript" language="JavaScript">
<!--
function confirmThis(url) {
  if (confirm("<?php echo $lang['j_close_pup']; ?>")) {
    window.location = url;
  }
}

function togglevis(id) {
  var obj = "";

  if(document.getElementById) obj = document.getElementById(id).style;
  else if(document.all) obj = document.all[id];
  else if(document.layers) obj = document.layers[id];
  else return 1;

  if(obj.visibility == "visible") obj.visibility = "hidden";
  else if(obj.visibility != "hidden") obj.visibility = "hidden";
  else obj.visibility = "visible";
}
// -->
</script>

<div class="borderwrap">
  <div id='logostrip'>
    <a href=index.php><div id='logographic'></div></a>
  </div>
  <div id="submenu">
    <p class="home">
<?php
   $querysetup="SELECT SUBKEY, FKEY, VALUE FROM config WHERE SUBKEY='SETUP' OR SUBKEY='".$BNAME."';";
   $responsetup=mysql_query($querysetup) or Muori ($lang['inv_query'] . mysql_error());
   while($valuesetup=mysql_fetch_assoc($responsetup)){
      if(($valuesetup['FKEY']=="BIND")AND($valuesetup['SUBKEY']=="SETUP")){
         $bindsetup=$valuesetup['VALUE'];
      }elseif(($valuesetup['FKEY']=="PORTA")AND($valuesetup['SUBKEY']=="SETUP")){
         $portsetup=$valuesetup['VALUE'];
      }elseif($valuesetup['FKEY']=="BIND"){
         $bindboard=$valuesetup['VALUE'];
      }
   }
   if($portsetup){
      if((!$bindsetup)OR($bindsetup==$_SERVER['REMOTE_ADDR'])OR($bindsetup==$bindboard)){
         $addrsetup=substr($_SERVER['HTTP_HOST'], 0, strlen($_SERVER['HTTP_HOST'])-strlen($_SERVER['SERVER_PORT'])-1);
         print '<a href="http://'.$addrsetup.':'.$portsetup.'/">Setup</a>';
      }
   }
?>
    </p>
      <form method="POST" name="boardlinkform">
       <p>
        <a href="search.php<? if ($_REQUEST["SEZID"]) echo "?SEZID=".$_REQUEST["SEZID"];?>"><? echo $lang['search']; ?></a>
        |
        <select class="content" name="boardlink" size="1" onchange="if(document.boardlinkform.boardlink.selectedIndex)window.open(document.boardlinkform.boardlink.options[document.boardlinkform.boardlink.selectedIndex].value)">
          <option selected value=''><? echo $lang['sel_otherbrd']; ?></option>
   <?php
      $querywse="SELECT DISTINCT SUBKEY FROM config WHERE MAIN_GROUP='SHARE' AND FKEY='PKEY';";
      $responsewse=mysql_query($querywse) or Muori ("Query non valida: " . mysql_error());
      while($valuewse=mysql_fetch_assoc($responsewse)){
       if($valuewse['SUBKEY']!=$SNAME){
         $queryws="SELECT DISTINCT SUBKEY FROM config WHERE FKEY='SesName' AND VALUE='".$valuewse['SUBKEY']."';";
         $responsews=mysql_query($queryws) or Muori ("Query non valida: " . mysql_error());
         while($valuews=mysql_fetch_assoc($responsews)){
            $querywsl="SELECT SUBKEY, FKEY, VALUE FROM config WHERE SUBKEY='".$valuews['SUBKEY']."' OR SUBKEY='".$BNAME."';";
            $responsewsl=mysql_query($querywsl) or Muori ($lang['inv_query'] . mysql_error());
            while($valuewsl=mysql_fetch_assoc($responsewsl)){
               if(($valuewsl['FKEY']=="BIND")AND($valuewsl['SUBKEY']==$valuews['SUBKEY'])){
                  $bindwsl=$valuewsl['VALUE'];
               }elseif(($valuewsl['FKEY']=="PORTA")AND($valuewsl['SUBKEY']==$valuews['SUBKEY'])){
                  $portwsl=$valuewsl['VALUE'];
               }elseif($valuewsl['FKEY']=="BIND"){
                  $bindboard=$valuewsl['VALUE'];
               }
            }
            if($portwsl){
               if((!$bindwsl)OR($bindwsl==$_SERVER['REMOTE_ADDR'])OR($bindwsl==$bindboard)){
                  $addrwsl=substr($_SERVER['HTTP_HOST'], 0, strlen($_SERVER['HTTP_HOST'])-strlen($_SERVER['SERVER_PORT'])-1);
                  echo '<option value="http://'.$addrwsl.':'.$portwsl.'/">'.$valuews['SUBKEY'].'</option>';
               }
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
  <p class="home"><b><a href="javascript:confirmThis('chiudi.pm')"><? echo $lang['userlink_close']; ?></a></b></p>
  <p><b><a href="index.php"><? echo $lang['userlink_home']; ?></a></b> | <a href="gestip.php"><? echo $lang['userlink_ipman']; ?></a> | <a href="userlist.php?validati=1&nonvalidati=1"><? echo $lang['userlink_usrlst']; ?></a> | <a href="boardlist.pl"><? echo $lang['userlink_brdlst']; ?></a> | <? echo $lang['userlink_wlcm']; ?>
   <?php
      if ($sess_auth)
        echo '<b>'.$sess_nick.'</b> ( <a href="logout.php?SEZID='.$_REQUEST["SEZID"].'&THR_ID='.$_REQUEST["THR_ID"].'">'.$lang['userlink_logout'].'</a> )';
      else
        echo $lang['userlink_guest'].' <a href="login.php?SEZID='.$_REQUEST["SEZID"].'&THR_ID='.$_REQUEST["THR_ID"].'">'.$lang['userlink_login'].'</a> <a href="register.php">'.$lang['userlink_signup'].'</a>';
    ?>
   </p>
</div>
<div id="navstrip">
 <img src='img/3.gif'>
 <a href="index.php"><?php echo $lang['navstrp_findex']; ?></a>
 <?php
    if ($SEZ_DATA['ID'])
      echo "\t<img src=\"img/3.gif\"></td>\n\t<td><a href=\"sezioni.php?SEZID=".$SEZ_DATA['ID']."\">".$SEZ_DATA['SEZ_NAME']."</a>\n";
    if ($title)
      echo "\t<img src=\"img/3.gif\"></td>\n\t<td>".$title."\n";
  ?>
</div>
<table border=0 cellspacing=0 cellpadding=0 align=center width="100%">
