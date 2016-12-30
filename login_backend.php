<?php
/* ================= please put this on top of every page, modify the allowed users/groups entries to manage access per page. */
error_reporting(E_ALL); // turn the reporting of php errors on
$allowed_users = "all users including guests"; // a list of userIDs that are allowed to access this page 
$allowed_groups = "all groups including guests"; // a list of groups, that are allowed to access this page
require_once('./lib/php/lib_security.php'); // will mysql-real-escape all input
// // login needs to be open for all in order to login! require_once('./lib/php/lib_session.php'); // will immediately exit and redirect to login if the session is not valid/has expired/user is not allowed to access the page
/* ================= */

$result = Array();

require_once('./lib/php/lib_mysqli_commands.php');

if(isset($_REQUEST['action']) && ($_REQUEST['action'] == "login"))
{
	if(!empty($_REQUEST['username']) && !empty($_REQUEST['password_encrypted']))
	{
		// old way:
		// $user = getUserByUsername($_REQUEST['username']);
		// new way:
		$lib_mysqli_commands_instance = new lib_mysqli_commands(config::get("db_name"));
		$user = $lib_mysqli_commands_instance->NewUser();
		$user->username = $_REQUEST['username'];
		$users = $lib_mysqli_commands_instance->users($user,"username");
		$user = getFirstElementOfArray($users);
		
		if(!empty($user)) // check if username exists
		{
			// at this point we know the username exists
			// let's compare the submitted password_encrypted to value of the array key (the right password)
			if(($user->username == $_REQUEST['username']) && ($user->password == $_REQUEST['password_encrypted'])) // check if username with that password exists
			{
				// password is correct
				session_start();
				$lib_mysqli_commands_instance->SetSession($_REQUEST['username'],$_REQUEST['password_encrypted']);
				
				if(config::get('login_session_timeout') > 0)
				{
					$home = "";
					if(isset($user->home))
					{
						// if not empty
						if($user->home)
						{
							$home = $user->home;
						}
						else
						{
							$home = config::get('default_home_after_login');
						}
					}
					else
					{
						$home = config::get('default_home_after_login');
					}
	
					$result["goto"] = $home; // header("Location: ".$home);
					$result["expires"] = seconds2minutes(config::get('login_session_timeout'));
					answer($result,"login","success","success","you have now access. live long and prosper! Login expires in ".seconds2minutes(config::get('login_session_timeout'))." minutes.");
				}
				else
				{
					answer(null,"login","failed","failed","session expired please login again.");
				}
			}
			else
			{
				answer(null,"login","failed","failed","wrong username or password.");
			}
		} else {
			answer(null,"login","failed","failed","wrong username or password.");
		}
	}	
}
?>