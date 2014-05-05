<?php
/* provides services for jquery-js-clients in concerns of user management by utilizing the lib/php/lib_mysqli_commands.php */
chdir(".."); // or all the require_once fail, because the paths are wrong.
chdir(".."); // or all the require_once fail, because the paths are wrong.
/* ================= please put this on top of every page, modify the allowed users/groups entries to manage access per page. */
error_reporting(E_ALL); // turn the reporting of php errors on
$allowed_users = "only logged in users"; // a list of userIDs that are allowed to access this page 
$allowed_groups = "root"; // a list of groups, that are allowed to access this page
require_once('./lib/php/lib_security.php'); // will mysql-real-escape all input
require_once('./lib/php/lib_mysqli_commands.php');
require_once("config/config.php"); // load project-config file
// // login needs to be open for all in order to login! require_once('./lib/php/lib_session.php'); // will immediately exit and redirect to login if the session is not valid/has expired/user is not allowed to access the page
/* ================= */

$result = Array();

require_once('./lib/php/lib_mysqli_commands.php');

if(isset($_REQUEST["action"]))
{
	/* list users */
	if($_REQUEST["action"] == "users")
	{
		// comment("get definition of user from database");
		$user = newUser();
		$users = users($user,$_REQUEST["uniqueKey"],$_REQUEST["uniqueValue"],$_REQUEST["where"]);

		if($users)
		{
			answer($users, "users", "success");
		}
		else
		{
			answer($users, "users", "failed");
		}
	}

	/* list groups */
	if($_REQUEST["action"] == "groups")
	{
		// comment("get definition of group from database");
		$group = newGroup();
		$groups = groups($group,$_REQUEST["uniqueKey"],$_REQUEST["uniqueValue"],$_REQUEST["where"]);

		if($groups)
		{
			answer($groups, "groups", "success");
		}
		else
		{
			answer($groups, "groups", "failed");
		}
	}
	/* create new  user */
	if($_REQUEST['action'] == "new")
	{
		$user = newUser(); // get database layout of an UserObject-Instance (basically all the keys but no values, not a real user record just the layout of it)
		$user->id = $_REQUEST['UserID']; // set the user id of the UserObject-Instance to 0, so we are looking for a user with id == 0

		// now editing/updating the properties
		$user->username = $_REQUEST['username'];
		$user->firstname = $_REQUEST['firstname'];
		$user->lastname = $_REQUEST['lastname'];
		$user->password = $_REQUEST['password_encrypted'];
		$user->groups = $_REQUEST['groups'];

		useradd($user); // returns the user-object from database, containing a new, database generated id, that is important for editing/deleting the user later

		global $output;

		if($output)
		{
			// if there is output on edit -> something is bad -> and $output should contain the error message that is forwareded to the client
			// answer($result = null,$action = "",$resultType = "",$resultValue = "",$details = "")
			answer(null,"update","failed","failed",$output,"lib_users_and_groups.php: action newUser failed");
		}
		else
		{
			// if there is no output on edit -> everything is okay
			answer($result,"update","success","success","successfully added a new user to the database.");
		}
	}
	/* update an existing user */
	if($_REQUEST['action'] == "update")
	{
		$user = newUser(); // get database layout of an UserObject-Instance (basically all the keys but no values, not a real user record just the layout of it)
		$user->id = $_REQUEST['UserID']; // set the user id of the UserObject-Instance to 0, so we are looking for a user with id == 0
		$user = getFirstElementOfArray(users($user)); // now passing this $user[id] to the function users which then extracts a real user with this id.

		// now editing/updating the properties
		$user->username = $_REQUEST['username'];
		$user->firstname = $_REQUEST['firstname'];
		$user->lastname = $_REQUEST['lastname'];
		$user->password = $_REQUEST['password_encrypted'];
		$user->groups = $_REQUEST['groups'];

		// writing to database, for more examples please check out: lib_mysqli_commands.test.php
		useredit($user);

		global $output;

		if($output)
		{
			// if there is output on edit -> something is bad -> and $output should contain the error message that is forwareded to the client
			// answer($result = null,$action = "",$resultType = "",$resultValue = "",$details = "")
			answer(null,"update","failed","failed",$output,"lib_users_and_groups.php: useredit/update failed.");
		}
		else
		{
			// if there is no output on edit -> everything is okay
			answer($result,"update","success","success","user updated successfully");
		}
	}

	/* delete user */
	if($_REQUEST['action'] == "delete")
	{
		$user = newUser(); // get database layout of an UserObject-Instance (basically all the keys but no values, not a real user record just the layout of it)
		$user->id = $_REQUEST['UserID']; // set the user id of the UserObject-Instance to 0, so we are looking for a user with id == 0
		userdel($user);

		global $output;

		if($output)
		{
			// if there is output on edit -> something is bad -> and $output should contain the error message that is forwareded to the client
			// answer($result = null,$action = "",$resultType = "",$resultValue = "",$details = "")
			answer(null,"update","failed","failed",$output,"lib_users_and_groups.php: action delete User failed");
		}
		else
		{
			// if there is no output on edit -> everything is okay
			answer($result,"update","success","success","deleted User successfully");
		}
	}
}
?>