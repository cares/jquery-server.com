<?php
echo "<hr><h1 color='red'>test lib_mysqli_commands database management commands</h1><br>";
chdir('../../');
include_once("./lib/php/lib_mysqli_commands.php");
include_once("config/config.php");

echo "<hr><h1 color='red'>test database user management commands</h1><br>";

comment("create database");
loadSQLFromFile("./lib/php/lib_mysqli_commands.test.sql");
success();

comment("get definition of user from database");
$user = newUser();
success();

// userdel with id
$user->id = 0;
comment("delete User by id (default)");
success(userdel($user));

// userdel with username
comment("delete user by username (all users with this username if reuse allowed)");
$user->username = "user";
success(userdel($user,"username"));
$user->username = "superuser";
success(userdel($user,"username"));

// groupdel - delete a group ALSO UPDATE USER RECORDS!
$group = newGroup();
comment("groupdel - delete a group ALSO UPDATE USER RECORDS!");
$group->groupname = "user";
success(groupdel($group,"groupname"));
$group->groupname = "changedTest";
success(groupdel($group,"groupname"));

// useradd
comment("add user to database");
$user->username = "user";
$user->mail = "mail@mail.de";
$user->firstname = "firstname";
$user->lastname = "lastname";
$users = useradd($user); // returns the user-object from database, containing a new, database generated id, that is important for editing/deleting the user later
success();

// get user by id/Mail/Username
comment("get user by ID");
/* this may look confusing $user -> is essentially: 
 * 
 * the way this operates:
 * 1. $user = newUser(); // get database layout of an UserObject-Instance (basically all the keys but no values, not a real user record just the layout of it)
 * 2. $user->id = 0; // set the user id of the UserObject-Instance to 0, so we are looking for a user with id == 0
 * */
$user = getFirstElementOfArray(users($user)); // now passing this $user[id] = 0 to the function users which then extracts a real user with this id.
// advantage of this approach: you always have the definition of the user-object at hand and do not need to look it up
success();

// getUserByUsername
comment("get User by Username");
$user = users($user,"username");
success();

// getUserByMail
comment("get User by mail");
$users = users(null,"mail","mail@mail.de");
success();

// get all users in this group
comment("get all users in this group");
$users = users(null,"groups","username");
success();

// the session variable exists, let's check it's valid:
comment("getUserBySession - important to check if user is logged in or not.");
$_SESSION['session'] = "5217840915ed98901b610f61132a6c56"; // set some session
$user = getUserBySession($_SESSION['session']);
success();

// users
comment("get a list of all users");
$users = users();
success();

// get all users with custom filter
comment("get all users with custom filter");
$users = users(null,"id","WHERE `mail` = 'mail@mail.de'");
success();

// useredit
comment("edit User (if it exists)");
$user->mail = "new@mail.de";
$user->username = "superuser";
success(useredit($user));

// userexist
comment("userexist");
success(userexist($user));

echo "<hr><h1 color='red'>test database Group management commands</h1><br>";

// groupdel - delete a group ALSO UPDATE USER RECORDS!
comment("groupdel - delete a group (can not be deleted if users are still in a group)");
$group = newGroup();
$group->groupname = "test";
success(groupdel($group,"groupname"));

// groupadd
/* the database-concept behind groups is like this:
 * 1. there is a column in the passwd table which contains a comma-separated list of all groups that the user belongs to.
 * 2. the table groups contains all available groups, you can add your own column-properties to the table, enriching the amounts of properties a group can have.
 */
comment("groupadd");
$group = groupadd($group); // returns the group-object from database, containing a new, database generated id, that is important for editing/deleting the group later
success();

// groupexist
comment("groupexist - test if a group exists by id");
if(groupexist($group))
{
	echo "yes group ".$group->groupname." exists.";
}
else
{
	echo "no group ".$group->groupname." does not exists.";
}

success();

// groupchange, also update the name in all user records!!!
comment("groupedit");
$group->groupname = "changedTest";
$group->mail = "groupA@mail.com";
success(groupedit($group));

// get group by id
comment("get group by id");
$groups = groups($group,"id");
success();

// get group by groupname
comment("get group by groupname");
$group->groupname = "changedTest";
$groups = groups($group,"groupname");
success();

// get group by groupname
comment("get group by groupname");
$groups = groups($group,"mail");
success();

// get all available groups
comment("get all available groups");
$groups = groups();
success();

// get all groups that the user belongs to
comment("get all groups that the user belongs to");
comment("get groups of user as object");
$groups = getGroupsOfUser($user);
success();

comment("get groups of user as an array of strings");
$groups = getGroupsOfUser($user,"strings");
// one big , separated string
$groups = array2string($groups,null,",");
success();

// get system groups
comment("get system groups");
$groups = groups(null,"id","WHERE `system` = 1");
success();

// get groups with custom filter
comment("get groups with custom filter");
$groups = groups(null,"id","WHERE `mail` = 'groupA@mail.com'");
success();

// get all groups with this groupname
$groupname = "user";
comment("get all groups with this groupname");
$groups = groups(null,"id","WHERE `groupname` = '".$groupname."'");
success();

// groupadduser - add user to a group
comment("groupadduser - add user to a group");
success(groupadduser($user,$group));

// groupremuser - remove user from group
comment("groupremuser - remove user from group");
success(groupdeluser($user,$group));

// recordget
comment("get definition of arbitrary record from database");
$NewRecord = newRecord("datarecord");

// recordadd
comment("recordadd - add a arbitrary record to a arbitrary table");
$NewRecord->id = "auto";
$NewRecord->key1 = "value1";
$NewRecord->key2 = "value2";
$NewRecord->key3 = "value3";
$NewRecord = recordadd($NewRecord); // returns the record-object from database, containing a new, database generated id, that is important for editing/deleting the record later
success();

// recordchange
comment("recordedit: change record");
$NewRecord->key2 = "newvalue2";
$NewRecord->key3 = "newvalue3";
success(recordedit($NewRecord));

// records by id/Mail/Username
comment("get record by ID");
$records = records($NewRecord);
success();

// getUserByUsername
comment("get User by key1");
$records = records($NewRecord,"key1");
success();

// getUserByMail
comment("get User by key2");
$records = records($NewRecord,"key2");
success();

// records
comment("get a list of all records");
$records = records();
success();

// get all records with custom filter
comment("get all records with custom filter");
$records = records(null,"id","WHERE `key1` = 'value1'");
success();

// recorddel
comment("recorddel: del record");
success(recorddel($NewRecord));

// this functionalities need to be implemented with the very general functions above:
// getDevices
// getDeviceByMac
// getButtons
// getOutputs
// getInputs
// getSessionExpiration
// setSession
// getUserBySession
// getGroups
// getSystemGroups
// groupexist
// generateUserList
// usersByGroup

/* print an array or variable like print_r would do it but with browser readable <br> instead of \n linebreaks */
function print_r_html($input)
{
	echo str_replace(array("\r\n", "\r","\n"), "<br>", print_r($input,true));
}

/* explain what is being done */
function comment($input)
{
	echo "<h3>".strval($input)."____________________________________________________________</h3><br>";
}
// colorful output about the outcomes of the functions
function success()
{
	global $worked;
	if($worked)
	{
		echo "<h3 style='color:green;'>worked</h3><br>";
	}
	else
	{
		echo "<h3 style='color:red;'>failed</h3><br>";
	}
}
?>