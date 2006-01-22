<?

	function load_lang($module, $lang_id) {

	    require "lang/".$lang_id."/".$module.".php";

		foreach ($lang as $key => $val) {
			$lang_array[$key] = stripslashes($val);
		}
	
		unset($lang);
		return $lang_array;
	}


function GetUserLanguage()
{
global $HTTP_ACCEPT_LANGUAGE;
	switch (substr(trim($HTTP_ACCEPT_LANGUAGE),0,2)) 
	{
		case "it":
			$blanguage="ita";
		break;
		case "fr":
			$blanguage="eng";
		break;
		case "de":
			$blanguage="eng";
		break;
		case "es":
			$blanguage="eng";
		break;

		default:
			$blanguage="eng";
		break;
	} 
return $blanguage;

}


?>