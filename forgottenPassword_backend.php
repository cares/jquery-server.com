<?php
/* ================= please put this on top of every page, modify the allowed users/groups entries to manage access per page. */
error_reporting(E_ALL); // turn the reporting of php errors on
$allowed_users = "all users including guests"; // a list of userIDs that are allowed to access this page 
$allowed_groups = "all groups including guests"; // a list of groups, that are allowed to access this page
require_once('./lib/php/lib_security.php'); // will mysql-real-escape all input
require_once('./lib/php/lib_translations.php'); // will mysql-real-escape all input
require_once("config/config.php"); // load project-config file
// // login needs to be open for all in order to login! require_once('./lib/php/lib_session.php'); // will immediately exit and redirect to login if the session is not valid/has expired/user is not allowed to access the page
/* ================= */

require_once('./lib/php/lib_mysqli_commands.php');
require_once('./lib/php/lib_general.php');

if(!empty($_REQUEST['mail']))
{
	require_once('config/config.php');

	$user = null;
	$user = getUserByMail($_REQUEST['mail']);

	$lang = detectLang();
	
	if($user != null)
	{
		$from = $settings_mail_admin;
		$to = $user->mail;
		$subject = translate("New password for", $lang)." ".$settings_platform_name;
		$user->password = md5(generatePassword());
		
		// update password in database
		// EDIT THE USER
		$output = useredit($user);
		
		// check if any error
		if(!$output)
		{
			logError('file:'.$settings_current_filename.',type:success,id:edit user successfull,details:The details/Credentials of the user where edited user successfully.');
		}
		else
		{
			logError('file:'.$settings_current_filename.',type:error,id:registration failed,details:'.$output);
		}

		$text = translate("Your new password for",$lang).' '.$sett .' <a href="'.$settings_platform_url.'">'.$settings_platform_url.'</a> '.translate("Password",$lang).': '. $pwd;
		
		sendMail($from, $to, $subjet, $text);
	}
}
?>