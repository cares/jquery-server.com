<?php
require_once 'config.php'; // check if config is accessable - if not make accessable
/*
 detected browser-language, save as two-letter codes http://www.sitepoint.com/web-foundations/iso-2-letter-language-codes/
 Contains functions to detect language
 Contains translations and functions to translate stuff
 TODO:
 */

class lang {

	public $lang = 'en'; // en is default, holds the two-letter language code in lowercase "en" "de"

	/************ CONSTRUCTOR ************/ 
	public function __construct()
	{
		$this->lang = $this->detectLang(); // detect lang
	}

	/************ GETTER AND SETTER ************/ 
	public function GetLang() {
		return $this->lang;
	}
	
	public function setLang($lang) {
		$this->lang = $lang;
	}

	/* either get translations from a file or from database.
	 * get appropriate translation for the $keyword */
	function translate($keyword,$lang)
	{
		if(config::set('translations_source') == "database")
		{
			if(empty($lang))
			{
				$this->lang = detectLang();
			}
			$result_database = null;
			$result_string = "";

			require_once './lib/php/lib_mysqli_interface.php';

			$mysqli_object = mysqli_interface::get('mysqli_object');
			$result_database = $mysqli_object>query("SELECT * FROM `translations` WHERE `keyword` = '".$keyword."'");
		
			$ClassObject = $result_database[0];
			$result_string = $ClassObject->$lang;
		}
		else
		{
			if(file_exists($instance_config->translations))
			{
				include_once $instance_config->translations;
			}
			else
			{
				echo "lang.php: function translate -> file with translations not found at: ".$instance_config->translations;
			}
				
			if(empty($lang))
			{
				$lang = $this->lang;
			}
			if(!empty($lang))
			{
				if(!empty($keyword))
				{
					/* iterate over key-value array */
					global $array_translations;
					if(isset($array_translations[$keyword][$lang]))
					{
						return $array_translations[$keyword][$lang];
					}
					else
					{
						return "function translate: translation not found for keyword: ".$keyword." and language: ".$lang;
					}
				}
				else
				{
					return "function translate: keyword parameter empty";
				}
			}
			else
			{
				return "function translate: lang parameter empty";
			}
		}	
		
	
		return $result_string;
	}
	/* BACKUP OF OLD FUNCTION: DOES ONE STILL NEED THIS? lookup keyword and return the translation for the $lang provided
	function translate($keyword = "",$lang = "")
	{
		if(empty($lang))
		{
			$lang = $this->lang;
		}
		if(!empty($lang))
		{
			if(!empty($keyword))
			{
				// iterate over key-value array
				foreach ($this->translations as $current_keyword => $array_translations)
				{
					if($keyword == $current_keyword)
					{
						return $array_translations[$lang];
					}
				}
			}
			else
			{
				return "function translate: keyword parameter empty";
			}
		}
		else
		{
			return "function translate: lang parameter empty";
		}
	
		return "function translate: translation not found for keyword: ".$keyword." and language: ".$lang;
	}
	*/
	
	/* function which should detect the language set in Browser/OS
	 * right now all english speaking countries us/uk/nz/au are converted into en 
	 */
	public function detectLang($verbose = "")
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
				$this->lang = $_COOKIE['lang'];
			}
			else
			{
				$acceptedLang = getenv("HTTP_ACCEPT_LANGUAGE");
				$acceptedLang = substr($acceptedLang, 0, 2);
				$set_lang = explode(',', $acceptedLang);
				$this->lang = $set_lang[0];
				setcookie("lang", $this->lang);
			}
		}
		else
		{
			$this->lang = $setlang;
			setcookie("lang", $setlang);
		}
	
		// convert all english-speaking languages to en
		if(($this->lang == "us")||($this->lang == "uk")||($this->lang == "nz")||($this->lang == "au")||($this->lang == "undefined"))
		{
			$this->lang = "en";
		}
	
		// if the detected language is not available -> set it to english(en) default
		if(($this->lang != "de") && ($this->lang != "en") && ($this->lang != "es") && ($this->lang != "pt") && ($this->lang != "ru") && ($this->lang != "cz") && ($this->lang != "pl")) // && ($this->lang != "it") && ($this->lang != "fr")
		{
			$this->lang = "en";
		}
	
		if(!empty($verbose))
		{
			$acceptedLang = getenv("HTTP_ACCEPT_LANGUAGE");
			echo "ACCEPTED LANG:".$acceptedLang;
			echo '<br>';
			echo "DETECTED LANG:".$this->lang;
			echo '<br>';
			
			echo "COOKIE INFO: ";
			if(isset($_COOKIE['lang']))
			{
				echo $_COOKIE['lang'];
			}
			else
			{
				echo "not yet set.";
			}
		}
		return $this->lang;
	}
}
?>