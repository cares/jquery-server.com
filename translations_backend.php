<?php
require_once("./lib/php/lib_translations.php");
require_once("./lib/php/lib_detectLang.php");
/* receives ajax/javascript-post-requests with hash#separated#keywords of translations
 * returns the translated texts again #hash#separated
 */ 
if(isset($_REQUEST["translations"]))
{
	$result = "";
	$lang = "";
	if(isset($_REQUEST["lang"]))
	{
		$lang = $_REQUEST["lang"];
	}
	if(empty($lang)||($lang == "undefined"))
	{
		$lang = detectLang();
	}

	$translations = $_REQUEST["translations"];
	$translations_array = explode('#',$translations);
	
	$translations_array_count = count($translations_array);
	for($i = 0;$i < $translations_array_count;$i++)
	{
		if(!empty($translations_array[$i]))
		{
			$value = translate($translations_array[$i],$lang);
			$result_array[] = $value;
			// $result = $result."#".$value;
		}
	}

	// if there are any translations return them
	if($result_array)
	{
		echo json_encode($result_array);
	}
}
?>