<?php
/*
| -------------------------------------------------------------------------
| KeyForum
| Copyright: � 2004 - 2005 KeyForum Team
| Web:       http://www.keyforum.net/
| Licence:   GPL v2.0
| -------------------------------------------------------------------------
| Modulo:     Conversione e deconversione messaggi
| Scritto da: Oberon
| -------------------------------------------------------------------------
*/


function convert($text) {
 global $_ENV,$db;
 
 $SNAME=$_SERVER['sesname'];

  // BBCode che richiedono una funzione
  $text = preg_replace("#\[code\](.+?)\[/code\]#ies"        , "convert_code('\\1')"  , $text);
  $text = preg_replace("#\[codebox\](.+?)\[/codebox\]#ies"        , "convert_code('\\1',1)"  , $text);
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
  $text = preg_replace("#\[size=([^\]]+)\](.+?)\[/size\]#ies", "convert_style('size','\\1','\\2')", $text);

    // Link
  $text = preg_replace("#(^|\s|<br \/>)([\w]{1,8}://(\|)*[\w]+[^\s]+)#ie"               , "convert_link('\\2','\\2','\\1',1)", $text);

  $text = preg_replace("#\[url\](\S+?)\[/url\]#ie"                                      , "convert_link('\\1','\\1')", $text);
  $text = preg_replace("#\[url\s*=\s*\&quot\;\s*(\S+?)\s*\&quot\;\s*\](.*?)\[\/url\]#ie", "convert_link('\\1','\\2')", $text);
  $text = preg_replace("#\[url\s*=\s*(\S+?)\s*\](.*?)\[\/url\]#ie"                      , "convert_link('\\1','\\2')", $text);

  $text = preg_replace("#\[forum_url\s*=\s*\&quot\;\s*(\S+?)\s*\&quot\;\s*\](.*?)\[\/forum_url\]#ie", "convert_forum_link('\\1','\\2')", $text);  
  $text = preg_replace("#\[forum_url\](\S+?)\[/forum_url\]#ie"                                      , "convert_forum_link('\\1','\\1')", $text);
  $text = preg_replace("#\[forum_url\s*=\s*(\S+?)\s*\](.*?)\[\/forum_url\]#ie"                      , "convert_forum_link('\\1','\\2')", $text);  
  
  $text = preg_replace("#\[email\](\S+?)\[/email\]#i"                                                              , "<a href='mailto:\\1'>\\1</a>", $text);
  $text = preg_replace("#\[email\s*=\s*\&quot\;([\.\w\-]+\@[\.\w\-]+\.[\.\w\-]+)\s*\&quot\;\s*\](.*?)\[\/email\]#i", "<a href='mailto:\\1'>\\2</a>", $text);
  $text = preg_replace("#\[email\s*=\s*([\.\w\-]+\@[\.\w\-]+\.[\w\-]+)\s*\](.*?)\[\/email\]#i"                     , "<a href='mailto:\\1'>\\2</a>", $text);
  $text = preg_replace("#\[anchor\s*=(.+?)\]#is", "<a name=\"\\1\"><!--anchor--></a>", $text);

  // Immagini
  $text = preg_replace("#\[img\](.+?)\[/img\]#is","<img alt=\"user posted image\" border=\"0\" src=\"\\1\" />", $text);
  $text = preg_replace("#\[tmb\](.+?)\[/tmb\]#is","<a href=\"\\1\" target='_blank'><img src=\"\\1\" width='180'  border='0' alt='Thumbnail, click to enlarge'></a>", $text);
  $text = preg_replace("#\[img\s*=left\](.+?)\[/img\]#is","<img style=\"float: left\" align=\"left\" alt=\"user posted image\" border=\"0\" src=\"\\1\" />", $text);
  $text = preg_replace("#\[img\s*=center\](.+?)\[/img\]#is","<div align=\"center\"><img align=\"center\" alt=\"user posted image\" border=\"0\" src=\"\\1\" \/></div>", $text);
  $text = preg_replace("#\[img\s*=right\](.+?)\[/img\]#is","<img style=\"float: right\" align=\"right\" alt=\"user posted image\" border=\"0\" src=\"\\1\" \/>", $text);

  $text = preg_replace("#\[img\s*width\s*=(.+?)\](.+?)\[/img\]#is","<img width=\"\\1\" alt=\"user posted image\" border=\"0\" src=\"\\2\" />", $text );

  // Allineamento
  $text = preg_replace("#\[left\](.+?)\[/left\]#is", "<div align=\"left\">\\1</div>", $text);
  $text = preg_replace("#\[center\](.+?)\[/center\]#is", "<div align=\"center\">\\1</div>", $text);
  $text = preg_replace("#\[right\](.+?)\[/right\]#is", "<div align=\"right\">\\1</div>", $text);
  $text = preg_replace( "#\[justify\](.*?)\[\/justify\]#is", "<div style=\"width=80%\" align=\"justify\">\\1</div>", $text );

  // Linea colorata non implementata
  $text = str_replace("[hr]", "<hr />", $text);
  $text = str_replace("[cuthere]", "<!--cuthere-->", $text);
 
 
 // tabelle
 $text = preg_replace( "#\[table\](.+?)\[/table\]#is", "<table>\\1</table>", $text ); 
 $text = preg_replace( "#\[TABLE bd=(.+?) bgc=(.+?) bdc=(.+?) cp=(.+?) cs=(.+?)\](.+?)\[/TABLE\]#is", "<table border=\\1 bgcolor=\\2 bordercolor=\\3 cellpadding=\\4 cellspacing=\\5>\\6</table>", $text ); 
 $text = preg_replace( "#\[tr\](.+?)\[/tr\]#is", "<tr>\\1</tr>", $text ); 
 $text = preg_replace( "#\[td\](.+?)\[/td\]#is", "<td>\\1</td>", $text ); 
 $text = preg_replace( "#\[td c=(\S+?)\s*\](.+?)\[/td\]#is", "<td colspan=\"\\1\">\\2</td>", $text ); 
 $text = preg_replace( "#\[td r=(\S+?)\s*\](.+?)\[/td\]#is", "<td rowspan=\"\\1\">\\2</td>", $text ); 

 
 
  // bbcode di comodo
  $text = str_replace("[TOPIC-PINNED]", "<!--TOPIC-PINNED-->", $text);
  $text = str_replace("[TOPIC-CLOSED]", "<!--TOPIC-CLOSED-->", $text);
  $text = str_replace("[TOPIC-HIDDEN]", "<!--TOPIC-HIDDEN-->", $text);
  $text = str_replace("[TOPIC-FIXED]", "<!--TOPIC-FIXED-->", $text);
  $text = str_replace("[TOPIC-SPECIAL]", "<!--TOPIC-SPECIAL-->", $text);
  $text = str_replace("[TOPIC-HOME]", "<!--TOPIC-HOME-->", $text);
 
  // EMOTICON
  static $emo_res;
  if(!$emo_res)
  {
  $query="SELECT id,typed,image,internal from {$SNAME}_emoticons";
  $emo_res=$db->get_results($query);
  }

  if($emo_res) foreach($emo_res as $row)
  {
	$emo=$row->typed;
	$img=$row->image;
	$id=$row->id;
	$emoq = preg_quote($emo, "/");
	
	if ($row->internal)
	{
		$text = preg_replace("!(?<=^|[^\w&;/])$emoq(?=.\W|\W.|\W$|$)!i", "<!--emostart=".$emo."--><img alt='emoticon ".$emo."' title='emoticon ".$emo."' style='vertical-align:middle' border='0' src='showemo.php?id=".$id."' /><!--emoend-->", $text); 
	}
	else
	{
		$text = preg_replace("!(?<=^|[^\w&;/])$emoq(?=.\W|\W.|\W$|$)!i", "<!--emostart=".$emo."--><img alt='emoticon ".$emo."' title='emoticon ".$emo."' style='vertical-align:middle' border='0' src='./img/emoticons/".$img."' /><!--emoend-->", $text);
	}
  }
  return $text;
}

 
function convert_code($text,$wrap=0) {

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

  if($wrap)
  {
  $text = "<!--codewrapstart--><textarea rows='5' cols='70' readonly class='row2' style='border: none; overflow: auto'>".$text."</textarea><!--codewrapend-->";
  $text = str_replace("<br />","",$text);
  } else {
  $text = "<!--codestart--><div class=codetl>CODE</div><div class=codetx>".$text."</div><!--codeend-->";
  }
  
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

function convert_link($url,$show,$before="",$simple=0) {

  $newpage = 0;

  if (preg_match("/\[\/(quote|code|spoiler)/i", $url)) return $before.$url;

  // Javascript non ammessi
  if (preg_match("/javascript:/i", $url)) return $before.$url;
    
  $url = preg_replace("/\[/", "&#91;", $url);
  $url = preg_replace("/\]/", "&#93;", $url);
  $url = preg_replace("/&amp;/", "&", $url);

  // Controllo testo
  $show = preg_replace("/&amp;/", "&", $show);
  $show = preg_replace("/javascript:/i", "java script&#58;", $show);
  
  if($simple) {$url=str_replace("<br","",$url);$show=$url;}

  // Protocollo?
  if (!preg_match("#^([\w]{1,8}://|\#)#", $url))$url = "http://".$url;

  // Apertura in nuova pagina
  if (preg_match("#^((http|https|ftp)://|\#)#", $url)) $newpage = 1;

  if ($newpage) $text = "<a href='".$url."' target='_blank'>".$show."</a>";
  else $text = "<a href='".$url."' target='_self'>".$show."</a>";

  return $before.$text;
}

function convert_forum_link($url,$show,$before="") {

  if (preg_match("/\[\/(quote|code|spoiler)/i", $url)) return $before.$url;

  // Javascript non ammessi
  if (preg_match("/javascript:/i", $url)) return $before.$url;
    
  $url = preg_replace("/\[/", "&#91;", $url);
  $url = preg_replace("/\]/", "&#93;", $url);
  $url = preg_replace("/&amp;/", "&", $url);
  
  // divido indirizzo da script
 $url =  stristr(eregi_replace("http://", "", $url), "/");

  // Controllo testo
  $show = preg_replace("/&amp;/", "&", $show);
  $show = preg_replace("/javascript:/i", "java script&#58;", $show);

  $text = "<a href='".$url."' target='_blank'>".$show."</a>";
  
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
