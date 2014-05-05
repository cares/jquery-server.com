<?php
/* check if a user has successfully logged in (username, password hash match)
 * This can now be included at the top of any file that needs protecting (before any other HTML/PHP content), and it will force any user who�s not logged in properly to �exit()�.
 *
 // 0. init database
 // 1. check if user/group variables are present
 // 2. check for valid session
 // 3. second check for user rights
 // 4. check for group rights
 */

// 0. init database
require_once("config/config.php");
global $settings_datasource;
if($settings_datasource == "mysql")
{
	require_once('./lib/php/lib_mysqli_commands.php');

	global $mysqli_object;
	if(!class_exists("mysqli"))
	{
		require_once('mysqli.php');
	};
}

 // 1. check if user/group variables are present
$session_valid = false;
$user_valid = false;
$group_valid = false;
$open_to_all = false; // if the user should be able to see the page without login/further checking session/credentials and so on
$open_to_all_logged_in_users = false;

// 2. check if user/group variables are present
global $allowed_users;
if(!isset($allowed_users))
{
	// echo('type:error,id:allowed_users not defined,details:the called php file did not have an $allowed_users variable defined. can not determine what logged in user is allowed to access this page.');
	// sleep(3);
	header("Location: servermessages/allowed_users_missing.php"); // redirect or exit();
}
else
{
	if(($allowed_users == "*") || ($allowed_users == "all users including guests"))
	{
		$open_to_all = true;
	}
	if($allowed_users == "all logged in users")
	{
		$open_to_all_logged_in_users = true;
	}
}

global $allowed_groups;
if(!isset($allowed_groups))
{
	// echo('type:error,id:allowed_users not defined,details:the called php file did not have an $allowed_users variable defined. can not determine what logged in user is allowed to access this page.');
	// sleep(3);
	header("Location: servermessages/allowed_groups_missing.php"); // redirect or exit();
}
else
{
	if(($allowed_groups == "*") || ($allowed_groups == "all groups including guests"))
	{
		$open_to_all = true;
	}
}

// 2. check for valid session
if(!$open_to_all) // no further check if you shall pass 
{
	session_start();
	if (!isset($_SESSION['session']))
	{
		// no session is set, redirect to login
		header("Location: frontend_login.php");
		exit;
	}
	else
	{
		// the session variable exists, let's check it's valid:
		$user = getUserBySession($_SESSION['session']);
			
		if(!$user)
		{
			// no session set
			header("Location: servermessages/session_expired.php");
		}

		$valid_until = getSessionExpiration($_SESSION['session'],$user);
			
		// check if the user is allowed to access this page
	}
	
	// check for session validity
	if($valid_until)
	{
		$now = time();
			
		if($now < $valid_until)
		{
			$session_valid = true;
		}
		else
		{
			$session_valid = false;
			// log em out
			$_SESSION['session'] = "";
			// exit('type:error,id:session expired,details:Please re-login!. ');
			// sleep(3);
			header("Location: servermessages/session_expired.php");
		}
	}
	else
	{
		$session_valid = false;
		// exit('type:error,id:session expired,details:Please re-login!. ');
		// sleep(3);
		header("Location: servermessages/session_expired.php");
	}
	
	if($session_valid == true)
	{
		// is used by the calling file
		$groups = getGroupsOfUser($user);

		if(!$open_to_all_logged_in_users)
		{
			// 3. second check for user rights
			$allowed_users_array = explode(',', $allowed_users);
			if((in_array($user->id, $allowed_users_array)) || ($allowed_users == "*"))
			{
				// all ok
				$user_valid = true;
			}
			else
			{
				// exit('type:error,id:session expired,details:Please re-login!. ');
				// sleep(3);
				$user_valid = false;
			}
			
			// 4. check for group rights
			/* is the editing user in an allowed group? */
			// check if the user is allowed to access this page
			$allowed_groups_array = explode(',', $allowed_groups);
	
			foreach ($groups as $key => $group)
			{
				if(in_array($group, $allowed_groups_array))
				{
					// all ok
					$group_valid = true;
					break;
				}
			}
		}

		// user needs to be under the list of valid userIDs or belong to an allowed group
		if($group_valid || $user_valid || $open_to_all_logged_in_users)
		{
			// everything is OK
		}
		else
		{
			if(!$user_valid) header("Location: servermessages/user_not_allowed.php");
			if(!$group_valid) header("Location: servermessages/group_not_allowed.php");
			// exit('type:error,id:session expired,details:you are not allowed to view this page');
		}
	}
}
?>