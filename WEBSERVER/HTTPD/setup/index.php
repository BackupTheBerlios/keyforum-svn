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

echo"
<head>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=windows-1252\">
<title>".$lang['index_title']."</title>
</head>

<body bgcolor=\"#FFFFFF\">

<p align=\"center\"><b><font face=\"Verdana\" size=\"6\">".$lang['index_title']."</font></b></p>
<p align=\"center\">&nbsp;</p>
<p align=\"center\"><b><a href=\"addboard.php\">".$lang['index_importbrd']."</a></b></p>
<p align=\"center\"><b><a href=\"newboard.php\">".$lang['index_createnewbrd']."</a></b></p>
<hr>
<p align=\"center\"><b><a href=\"addon.php\">".$lang['index_addon']."</a></b></p>
<hr>
<p align=\"center\"><b><a target=_blank href=\"restart.php\">".$lang['index_restart']."</a></b></p>


</body>

</html>";?>
