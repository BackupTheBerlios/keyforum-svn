<?php
/*
| -------------------------------------------------------------------------
| KeyForum
| Copyright: © 2004 - 2005 KeyForum Team
| Web:       http://www.keyforum.net/
| Licence:   GPL v2.0
| -------------------------------------------------------------------------
| Modulo:     Conversione e deconversione messaggi
| Scritto da: Oberon
| -------------------------------------------------------------------------
*/

// Lista emoticons
include_once("./img/emoticons/emoticons.php");

function convert($text) {
  global $emoticons;

  // BBCode che richiedono una funzione
  $text = preg_replace("#\[code\](.+?)\[/code\]#ies"        , "convert_code('\\1')"  , $text);
  $text = preg_replace("#\[spoiler\](.+?)\[/spoiler\]#ies" , "convert_spoiler('\\1')" , $text);
  $text = preg_replace("#(\[quote(.+?)?\].*\[/quote\])#ies" , "convert_quote('\\1')" , $text);
  
//-------------------------
// [LIST]    [*]    [/LIST]
//-------------------------

while( preg_match( "#\n?\[list\](.+?)\[/list\]\n?#ies" , $text ) )
{
	$text = preg_replace( "#\n?\[list\](.+?)\[/list\]\n?#ies", "regex_list('\\1')" , $text );
}

while( preg_match( "#\n?\[list=(a|A|i|I|1)\](.+?)\[/list\]\n?#ies" , $text ) )
{
	$text = preg_replace( "#\n?\[list=(a|A|i|I|1)\](.+?)\[/list\]\n?#ies", "regex_list('\\2','\\1')" , $text );
}


  // Stili
  $text = preg_replace("#\[b\](.+?)\[/b\]#is", "<b>\\1</b>", $text);
  $text = preg_replace("#\[i\](.+?)\[/i\]#is", "<i>\\1</i>", $text);
  $text = preg_replace("#\[u\](.+?)\[/u\]#is", "<u>\\1</u>", $text);
  $text = preg_replace("#\[s\](.+?)\[/s\]#is", "<s>\\1</s>", $text);
  $text = preg_replace("#\[color\s*=([^\]]+)\](.+?)\[/color\]#ies", "convert_style('color','\\1','\\2')", $text);
  $text = preg_replace("#\[bgcolor\s*=([^\]]+)\](.+?)\[/bgcolor\]#ies", "convert_style('bgcolor','\\1','\\2')", $text);
  $text = preg_replace("#\[font\s*=([^\]]+)\](.+?)\[/font\]#ies", "convert_style('font','\\1','\\2')", $text);
  $text = preg_replace("#\[size\s*=(\d)\](.+?)\[/size\]#ies", "convert_style('size','\\1','\\2')", $text);

    // Link
  $text = preg_replace("#(^|\s|<br \/>)([\w]{1,6}://(\|)*[\w]+[^\s]+)#ie"               , "convert_link('\\2','\\2','\\1')", $text);
  $text = preg_replace("#\[url\](\S+?)\[/url\]#ie"                                      , "convert_link('\\1','\\1')", $text);
  $text = preg_replace("#\[url\s*=\s*\&quot\;\s*(\S+?)\s*\&quot\;\s*\](.*?)\[\/url\]#ie", "convert_link('\\1','\\2')", $text);
  $text = preg_replace("#\[url\s*=\s*(\S+?)\s*\](.*?)\[\/url\]#ie"                      , "convert_link('\\1','\\2')", $text);
  $text = preg_replace("#\[email\](\S+?)\[/email\]#i"                                                              , "<a href='mailto:\\1'>\\1</a>", $text);
  $text = preg_replace("#\[email\s*=\s*\&quot\;([\.\w\-]+\@[\.\w\-]+\.[\.\w\-]+)\s*\&quot\;\s*\](.*?)\[\/email\]#i", "<a href='mailto:\\1'>\\2</a>", $text);
  $text = preg_replace("#\[email\s*=\s*([\.\w\-]+\@[\.\w\-]+\.[\w\-]+)\s*\](.*?)\[\/email\]#i"                     , "<a href='mailto:\\1'>\\2</a>", $text);
  $text = preg_replace("#\[anchor\s*=(.+?)\]#is", "<a name=\"\\1\"><!--anchor--></a>", $text);

  // Immagini
  $text = preg_replace("#\[img\](.+?)\[/img\]#is","<img alt=\"user posted image\" border=\"0\" src=\"\\1\" />", $text);
  $text = preg_replace("#\[img\s*=left\](.+?)\[/img\]#is","<img style=\"float: left\" align=\"left\" alt=\"user posted image\" border=\"0\" src=\"\\1\" />", $text);
  $text = preg_replace("#\[img\s*=center\](.+?)\[/img\]#is","<div align=\"center\"><img align=\"center\" alt=\"user posted image\" border=\"0\" src=\"\\1\" \/></div>", $text);
  $text = preg_replace("#\[img\s*=right\](.+?)\[/img\]#is","<img style=\"float: right\" align=\"right\" alt=\"user posted image\" border=\"0\" src=\"\\1\" \/>", $text);

  $text = preg_replace("#\[img\s*width\s*=(.+?)\](.+?)\[/img\]#is","<img width=\"\\1\" alt=\"user posted image\" border=\"0\" src=\"\\2\" />", $text );

  // Allineamento
  $text = preg_replace("#\[left\](.+?)\[/left\]#is", "<div align=\"left\">\\1</div>", $text);
  $text = preg_replace("#\[center\](.+?)\[/center\]#is", "<div align=\"center\">\\1</div>", $text);
  $text = preg_replace("#\[right\](.+?)\[/right\]#is", "<div align=\"right\">\\1</div>", $text);

  // Linea colorata non implementata
  $text = str_replace("[hr]", "<hr />", $text);
  $text = str_replace("[cuthere]", "<!--cuthere-->", $text);
    
  // Conversione faccine
  foreach ($emoticons as $emo => $img) {
    $emoq = preg_quote($emo, "/");
    $text = preg_replace("!(?<=^|[^\w&;/])$emoq(?=.\W|\W.|\W$|$)!i", "<!--emostart=".$emo."--><img alt='emoticon ".$img."' style='vertical-align:middle' border='0' src='./img/emoticons/".$img."' /><!--emoend-->", $text);
  }
    
     
    
  return $text;
}

// Nel caso servisse...
function unconvert($text) {

  $text = preg_replace("#<!--emostart=(.+?)-->.+?<!--emoend-->#", "\\1" , $text );
    
  $text = preg_replace("#<br />#", "\n", $text);
    
  // Prima quelle che richiedono/richiedevano una funzione
  $text = preg_replace("#<!--codestart--><div class=\"codetl\">CODE</div><div class=\"codetx\">#", "[code]", $text);
  $text = preg_replace("#</div><!--codeend-->#", "[/code]", $text);
  $text = preg_replace("#<!--quotestart--><div class=\"quotetl\">QUOTE</div><div class=\"quotetx\">#", "[quote]", $text);
  $text = preg_replace("#<!--quotestart--><div class=\"quotetl\">QUOTE \(([^>]+?)\)</div><div class=\"quotetx\">#", "[quote=\\1]", $text);
  $text = preg_replace("#</div><!--quoteend-->#", "[/quote]", $text);
  $text = preg_replace("#<!--spoilerstart-->(.+?)<!--spoilertext-->#", "[spoiler]", $text);
  $text = preg_replace("#</div></div><!--spoilerend-->#", "[/spoiler]", $text);
    
  // Stili
  $text = preg_replace("#<b>(.+?)</b>#is", "[b]\\1[/b]", $text);
  $text = preg_replace("#<i>(.+?)</i>#is", "[i]\\1[/i]", $text);
  $text = preg_replace("#<u>(.+?)</u>#is", "[u]\\1[/u]", $text);
  $text = preg_replace("#<s>(.+?)</s>#is", "[s]\\1[/s]", $text);
  $text = preg_replace("#<!--colorstart--><span style=['\"]color:(\S+?)['\"]>(.+?)</span><!--colorend-->#se", "unconvert_style('color','\\1','\\2')", $text);
  $text = preg_replace("#<!--bgcolorstart--><span style=['\"]background:(\S+?)['\"]>(.+?)</span><!--bgcolorend-->#se", "unconvert_style('bgcolor','\\1','\\2')", $text);
  $text = preg_replace("#<!--fontstart--><span style=['\"]font-family:(.+?)['\"]>(.+?)</span><!--fontend-->#se", "unconvert_style('font','\\1','\\2')", $text);
  $text = preg_replace("#<!--sizestart--><span style=['\"]line-height:100%;font-size:(\d\d?)pt['\"]>(.+?)</span><!--sizeend-->#se", "unconvert_style('size','\\1','\\2')", $text);
    
  // Link
  $text = preg_replace("#<a href=[\"'](\S+?)['\"] target=[\"'](\w+?)[\"']>(.+?)</a>#", "[url=\"\\1\"]\\3[/url]", $text);
  $text = preg_replace("#<a href=[\"']mailto:(\S+?)['\"]>(.+?)</a>#", "[email=\\1]\\2[/email]", $text);
  $text = preg_replace("#<a name=\"(.+?)\"><!--anchor--></a>#", "[anchor=\\1]", $text);
    
  // Immagini
  $text = preg_replace("#<img alt=[\"']user posted image[\"'] border=[\"']0[\"'] src=[\"'](.+?)[\"'] />#","[img]\\1[/img]", $text);
  $text = preg_replace("#<img style=[\"']float: left[\"'] align=[\"']left[\"'] alt=[\"']user posted image[\"'] border=[\"']0[\"'] src=[\"'](.+?)[\"'] />#"        , "[img=left]\\1[/img]", $text);
  $text = preg_replace("#<div align=[\"']center[\"']><img align=[\"']center[\"'] alt=[\"']user posted image[\"'] border=[\"']0[\"'] src=[\"'](.+?)[\"'] /></div>#", "[img=center]\\1[/img]", $text);
  $text = preg_replace("#<img style=[\"']float: right[\"'] align=[\"']right[\"'] alt=[\"']user posted image[\"'] border=[\"']0[\"'] src=[\"'](.+?)[\"'] />#"      , "[img=right]\\1[/img]", $text);
    
  $text = preg_replace("#<img width=[\"'](.+?)[\"'] alt=[\"']user posted image[\"'] border=[\"']0[\"'] src=[\"'](.+?)[\"'] />#","[img width=\\1]\\2[/img]", $text);
    
  // Allineamento
  $text = preg_replace("#<div align=[\"']center[\"']>(.+?)</div>#", "[center]\\1[/center]", $text);
  $text = preg_replace("#<div align=[\"']left[\"']>(.+?)</div>#"  , "[left]\\1[/left]", $text);
  $text = preg_replace("#<div align=[\"']right[\"']>(.+?)</div>#" , "[right]\\1[/right]", $text);
    
  // Conversioni semplici
  $text = str_replace("<hr />", "[hr]",$text);
  $text = str_replace("<!--cuthere-->", "[cuthere]",$text);
    
  return $text;
}
  
function convert_code($text) {

  if (preg_match( "/\[(quote|code|spoiler)\].+?\[(quote|code|spoiler)\].+?\[(quote|code|spoiler)\].+?\[(quote|code|spoiler)\].+?\[(quote|code|spoiler)\].+?\[(quote|code|spoiler)\]/i", $text) ) {
    return "\[code\]".$text."\[/code\]";
  }

  $text = preg_replace("#&lt;#"  , "&#60;"  , $text);
  $text = preg_replace("#&gt;#"  , "&#62;"  , $text);
  $text = preg_replace("#&quot;#", "&#34;"  , $text);
  $text = preg_replace("#:#"     , "&#58;"  , $text);
  $text = preg_replace("#\[#"    , "&#91;"  , $text);
  $text = preg_replace("#\]#"    , "&#93;"  , $text);
  $text = preg_replace("#\)#"    , "&#41;"  , $text);
  $text = preg_replace("#\(#"    , "&#40;"  , $text);
  $text = preg_replace("#\r#"    , "<br>"   , $text);
  $text = preg_replace("#\n#"    , "<br>"   , $text);
  $text = preg_replace("#\s{1};#", "&#59;"  , $text);
  $text = preg_replace("#\s{2}#" , " &nbsp;", $text);

  $text = "<!--codestart--><div class=codetl>CODE</div><div class=codetx>".$text."</div><!--codeend-->";

  return $text;
}
  
function convert_quote($text) {

  $text = preg_replace("#\[quote\]#is"          , "<!--quotestart--><div class=\"quotetl\">QUOTE</div><div class=\"quotetx\">"      , $text);
  $text = preg_replace("#\[quote=([^\]]+?)\]#is", "<!--quotestart--><div class=\"quotetl\">QUOTE (\\1)</div><div class=\"quotetx\">", $text);
  $text = preg_replace("#\[/quote\]#is"         , "</div><!--quoteend-->"                                                           , $text);

  $text = preg_replace("/\n/", "<br />", $text);

  return $text;
}

function convert_spoiler($text) {

  $spoilername = "spoiler".md5($text.microtime());
    
  $text = "<!--spoilerstart--><div class=\"spoilertl\" onclick=\"togglevis('".$spoilername."')\">SPOILER - Show/Hide: click here</div><div class=\"spoilertx\"><div id=\"".$spoilername."\" style=\"visibility:hidden\"><!--spoilertext-->".$text."</div></div><!--spoilerend-->";

  return $text;
}

function convert_link($url,$show,$before="") {

  $newpage = 0;

  if (preg_match("/\[\/(quote|code|spoiler)/i", $url)) return $before.$url;

  // Javascript non ammessi
  if (preg_match("/javascript:/i", $url)) return $before.$url;
    
  $url = preg_replace("/\[/", "&#91;", $url);
  $url = preg_replace("/\]/", "&#93;", $url);
  $url = preg_replace("/&amp;/", "&", $url);

  // Protocollo?
  if (!preg_match("#^([\w]{1,6}://|\#)#", $url))$url = "http://".$url;

  // Apertura in nuova pagina
  if (preg_match("#^((http|https|ftp)://|\#)#", $url)) $newpage = 1;

  // Controllo testo
  $show = preg_replace("/&amp;/", "&", $show);
  $show = preg_replace("/javascript:/i", "java script&#58;", $show);

  if ($newpage) $text = "<a href='".$url."' target='_blank'>".$show."</a>";
  else $text = "<a href='".$url."' target='_self'>".$show."</a>";

  return $before.$text;
}

function convert_style($type,$opt,$text) {

  $opt = explode(";",$opt);
  $opt = $opt[0];

  switch($type) {
    case "color":
      $attr = "color:".$opt;
      break;
    case "bgcolor":
      // Solo un colore, no immagini
      $opt = explode("url(",$opt);
      $attr = "background:".$opt[0];
      break;
    case "font":
      $attr = "font-family:".$opt;
      break;
    case "size":
      $opt = $opt+7;
      $attr = "line-height:100%;font-size:".$opt."pt";
      break;
    default:
      return $text;
      break;
  }
    
  $text = "<!--".$type."start--><span style='".$attr."'>".$text."</span><!--".$type."end-->";

  return $text;
}

function unconvert_style($type,$opt,$text) {

  $opt = explode(";",$opt);
  $opt = $opt[0];

  switch($type) {
    case "color":
      $text = "[color=".$opt."]".$text."[/color]";
      break;
    case "bgcolor":
      $text = "[bgcolor=".$opt."]".$text."[/bgcolor]";
      break;
    case "font":
      $text = "[font=".$opt."]".$text."[/font]";
      break;
    case "size":
      $opt = $opt-7;
      $text = "[size=".$opt."]".$text."[/size]";
      break;
    default:
      return $text;
      break;
  }

  return $text;
}


	/**************************************************/
	// List
	// 
	/**************************************************/
	
	function regex_list( $text="", $type="" )
	{
		if ($text == "")
		{
			return;
		}
		
		if ( $type == "" )
		{
		
			return "<ul>".regex_list_item($text)."</ul>";
		}
		else
		{
			return "<ol type='$type'>".regex_list_item($text)."</ol>";
		}
	}
	
	function regex_list_item($text)
	{
		$text = preg_replace( "#\[\*\]#", "</li><li>" , trim($text) );
		$text = preg_replace( "#^</?li>#"  , "", $text );
		return str_replace( "\n</li>", "</li>", $text."</li>" );
	}


?>
