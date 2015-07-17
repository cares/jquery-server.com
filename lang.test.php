<?php
	/* TODO:
	 * DATE: 2015-07-13 TIME: 17:49:57
	 // ok turn lib_lang.php into class
	 // ok test lang detection functions
	 // o test translate
	 // oo from_file
	 // oo from_database
	 */
	require_once 'config.php'; // check if config is accessable - if not make accessable

	echo "============= testing browser-language detection: <br>";
	require_once 'lang.php';
	$instance_lang = new lang(); // get access to settings
	$lang = $instance_lang->detectLang("verbose");

	echo "<br>============= testing translation: <br>";
	// o test translate
	// oo from_file
	$keyword = "Table";
	$lang = "de";
	echo "Please translate keyword ".$keyword." to ".$lang;
	echo "<br>".$keyword." in ".$lang." translates as \"".$instance_lang->translate($keyword,$lang)."\".";
	// oo from_database
?>