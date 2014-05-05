<?php
/* function which should detect the language set in Browser/OS
 * right now all english speaking countries us/uk/nz/au are converted into en 
 */
function detectLang($verbose = "")
{
	$setlang = "";
	
	// 1. falls lang mit request oder get kommt, setzten
	if(isset($_REQUEST["lang"]))
	{
		if($_REQUEST["lang"] != "undefined")
		{
			$setlang = $_REQUEST["lang"];
		}
	}

	if(isset($_GET["lang"]))
	{
		if($_GET["lang"] != "undefined")
		{
			$setlang = $_GET["lang"];
		}
	}
	
	// 2. falls nicht, use cookie
	if(empty($setlang)){
	
		if (isset($_COOKIE['lang']) && ($_COOKIE['lang'] != "undefined"))
		{
			$lang = $_COOKIE['lang'];
		}
		else
		{
			$acceptedLang = getenv("HTTP_ACCEPT_LANGUAGE");
			$acceptedLang = substr($acceptedLang, 0, 2);
			$set_lang = explode(',', $acceptedLang);
			$lang = $set_lang[0];
			setcookie("lang", $lang);
		}
	}
	else
	{
		$lang = $setlang;
		setcookie("lang", $setlang);
	}

	// convert languages
	if(($lang == "us")||($lang == "uk")||($lang == "nz")||($lang == "au")||($lang == "undefined"))
	{
		$lang = "en";
	}

	if(!empty($verbose))
	{
		$acceptedLang = getenv("HTTP_ACCEPT_LANGUAGE");
		echo "ACCEPTED LANG:".$acceptedLang;
		echo '<br>';
		echo "DETECTED LANG:".$lang;
		echo '<br>';
		echo "COOKIE INFO: ".$_COOKIE['lang'];
	}
	
	// if the detected language, is none of the available languages -> set it to english/us default
	if(($lang != "de") && ($lang != "us") && ($lang != "es") && ($lang != "pt") && ($lang != "ru") && ($lang != "cz") && ($lang != "pl")) // && ($lang != "it") && ($lang != "fr")
	{
		$lang = "us";
	}

	return $lang;
}   
?>