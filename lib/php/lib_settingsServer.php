<?php
/* provides server settings for jquery-js-clients  */
chdir(".."); // or all the require_once fail, because the paths are wrong.
chdir(".."); // or all the require_once fail, because the paths are wrong.
require_once('./config/config.php');

if($_REQUEST["get"] == "settings")
{
	$settings["settings_platform_name"] = $settings_platform_name;
	$settings["settings_platform_logo"] = $settings_platform_logo;
	$settings["settings_platform_url"] = $settings_platform_url;
	$settings["settings_errorLog"] = $settings_errorLog;

	$settings["settings_log_errors"] = $settings_log_errors;
	$settings["settings_log_operations"] = $settings_log_operations;

	/* ======================= DEVELOPMENT */
	$settings["settings_debug_mode"] = $settings_debug_mode;

	echo json_encode($settings);
}
?>