<?php
/* let jquery-js-client side query basic server settings */
// include lib_general.php, should be on top of every file
if(file_exists('lib_general.php')) { require_once('lib_general.php'); } else { if(file_exists('./lib/php/lib_general.php')) { require_once('./lib/php/lib_general.php'); } else { trigger_error(basename(__FILE__, '.php')."-> could not include library lib_general.php, it should be on top of every file.php", E_USER_ERROR); }}
include_missing_lib("config.php");

$settings = Array();

if($_REQUEST["get"] == "settings")
{
	$settings["settings_platform_name"]		= config::get('platform_name');
	$settings["settings_platform_url"]		= config::get('platform_url');
	$settings["settings_errorLog"]			= config::get('errorLog');

	$settings["settings_log_errors"]		= config::get('log_errors');
	$settings["settings_log_operations"]	= config::get('log_operations');

	/* ======================= DEVELOPMENT */
	$settings["settings_debug_mode"]		= config::get('debug_mode');

	echo json_encode($settings);
}
?>