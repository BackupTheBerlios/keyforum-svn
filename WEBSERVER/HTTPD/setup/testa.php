<?
include("langsupport.php");

// determino la lingua
if(!$_REQUEST['lang'])
{
$blanguage=GetUserLanguage();
} else {
$blanguage=$_REQUEST['lang'];
}

// lingua
$lang = load_lang('lang_index', $blanguage ); 


?>

<html>
<head>
<link rel="shortcut icon" href="favicon.ico">
<link type="text/css" rel="stylesheet" href="style_page.css" />

<script> 
function goTo(screen)
{
document.toolbar.action = screen;
document.toolbar.submit();
}
</script>


</head>
<body>

<div class="borderwrap">
  <div id='logostrip'>
    <a href=index.php><div id='logographic'></div></a>
  </div>
  </div>
<form method="POST" name="toolbar" action="">
  <div align=center>
  <?
  echo "
  <input onClick=\"javascript:goTo('addboard.php')\" class=\"button\" type=\"button\" value=\"{$lang['index_importbrd']}\" name=\"B1\">
  <input onClick=\"javascript:goTo('newboard.php')\" class=\"button\" type=\"button\" value=\"{$lang['index_createnewbrd']}\" name=\"B2\">
  <input onClick=\"javascript:goTo('mngws.php')\" class=\"button\" type=\"button\" value=\"{$lang['index_managews']}\" name=\"B3\">
  <input onClick=\"javascript:goTo('addon.php')\" class=\"button\" type=\"button\" value=\"{$lang['index_addon']}\" name=\"B3\">
  <input onClick=\"javascript:goTo('mngbd.php')\" class=\"button\" type=\"button\" value=\"{$lang['index_managebd']}\" name=\"B3\">
  <input onClick=\"javascript:goTo('restart.php')\" class=\"button\" type=\"button\" value=\"{$lang['index_restart']}\" name=\"B4\">
  ";
  ?>
  </div>
</form>
<br><br>