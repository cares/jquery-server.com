<?php
/* ================= please put this on top of every page, modify the allowed users/groups entries to manage access per page. */
error_reporting(E_ALL); // turn the reporting of php errors on
require_once("config/config.php"); // load project-config file
require_once("./lib/php/lib_detectLang.php");
/* ================= */
/* get appropriate translation for the $keyword */
function translate($keyword,$lang)
{
	if(empty($lang))
	{
		$lang = detectLang();
	}
	$result_database = null;
	$result_string = "";
	require_once './lib/php/lib_mysqli_interface.php';
	// init database object
	$mysqli_object = new class_mysqli_interface();

	global $mysqli_object;
	global $settings_database_auth_table; global $settings_database_groups_table;
	global $settings_database_name;
	$result_database = $mysqli_object->query("SELECT * FROM `translations` WHERE `keyword` = '".$keyword."'");
	
	$ClassObject = $result_database[0];
	$result_string = $ClassObject->$lang;

	return $result_string;
}
?>