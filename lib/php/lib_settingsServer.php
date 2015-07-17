<?php
/* provides server settings for jquery-js-clients  */
$settings = Array();

if($_REQUEST["get"] == "settings")
{
	$settings["settings_platform_name"] = config::get('platform_name');
	$settings["settings_platform_url"] = config::get('platform_url');
	$settings["settings_errorLog"] = config::get('errorLog');

	$settings["settings_log_errors"] = config::get('log_errors');
	$settings["settings_log_operations"] = config::get('log_operations');

	/* ======================= DEVELOPMENT */
	$settings["settings_debug_mode"] = config::get('debug_mode');

	echo json_encode($settings);
}
?>