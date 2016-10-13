<?php
echo "<hr><h1 color='red'>test lib_mysqli_commands database management commands</h1><br>";
chdir('../../');

$current_working_dir = getcwd();

include_once("./lib/php/lib_mysqli_commands.php");

// init database
// $mysqli_interface_instance = new mysqli_interface(); # create instance from class
// config::set('mysqli_object',$mysqli_interface_instance);

$lib_mysqli_commands_instance = new lib_mysqli_commands("test"); # create instance from class and connect to database "test", overwrite default-settings from config.php

echo "<hr><h1 color='red'>test database user management commands</h1><br>";

comment("test if database 'test' exists");

if($lib_mysqli_commands_instance->DatabaseExists("test"))
{
	// yes
	comment("database 'test' exists");
}
else
{
	// no
	comment("database 'test' does not exist - create it now");
}

/* ================ create database ================ */

// one just drops and recreates the test database via this sql backup 
$lib_mysqli_commands_instance->ImportSQLFromFile("./lib/php/lib.mysqli.commands.test.sql");
success();

$exists = $lib_mysqli_commands_instance->TableExists("test","passwd");

if($exists)
{
	// yes
	comment("table 'passwd' exists in database test'");
}
success();

comment("get definition of user from database");
$user = $lib_mysqli_commands_instance->NewUser();
success();

/* ================ create data: users ================ */

// UserAdd
comment("add user to database");
$user->username = "user";
$user->mail = "mail@mail.de";
$user->firstname = "firstname";
$user->lastname = "lastname";
$users = $lib_mysqli_commands_instance->UserAdd($user); // returns the user-object from database, containing a new, database generated id, that is important for editing/deleting the user later
success();

/* ================ read data: users ================ */

// get user by id/Mail/Username
comment("get user by ID");
/* this may look confusing $user -> is essentially: 
 * 
 * the way this operates:
 * 1. $user = NewUser(); // get database layout of an UserObject-Instance (basically all the keys but no values, not a real user record just the layout of it)
 * 2. $user->id = 0; // set the user id of the UserObject-Instance to 0, so we are looking for a user with id == 0
 * */
$user = getFirstElementOfArray($lib_mysqli_commands_instance->users($user)); // now passing this $user[id] = 0 to the function users which then extracts a real user with this id.
// advantage of this approach: you always have the definition of the user-object at hand and do not need to look it up
success();

// getUserByUsername
comment("get User by Username");
$user = $lib_mysqli_commands_instance->users($user,"username"); // will return an array with only one entry if user with this "username" found
success();

// getUserByMail
comment("get User by mail");
$users = $lib_mysqli_commands_instance->users(null,"mail","mail@mail.de");
success();

// get all users in this group
comment("get all users in this group");
$users = $lib_mysqli_commands_instance->users(null,"groups","username");
success();

// the session variable exists, let's check it's valid:
comment("GetUserBySession - important to check if user is logged in or not.");
$_SESSION['session'] = "5217840915ed98901b610f61132a6c56"; // set some session
$user = $lib_mysqli_commands_instance->GetUserBySession($_SESSION['session']);
success();

// users
comment("get a list of all users");
$users = $lib_mysqli_commands_instance->users();
success();

// get all users with custom filter
comment("get all users with custom filter");
$users = $lib_mysqli_commands_instance->users(null,"id","WHERE `mail` = 'mail@mail.de'");
success();


/* ================ modify change data: groups ================ */
// UserEdit
comment("edit User (if it exists)");
$user->mail = "new@mail.de";
$user->username = "superuser";
success($lib_mysqli_commands_instance->UserEdit($user));

/* ================ delete data: users ================ */

// UserDel with id
$user->id = 0;
comment("delete User by id (default)");
success($lib_mysqli_commands_instance->UserDel($user)); // if the user does not exist, output error or not?

// UserDel with username
comment("delete user by username (all users with this username if reuse allowed)");
$user->username = "user";
success($lib_mysqli_commands_instance->UserDel($user,"username"));
$user->username = "superuser";
success($lib_mysqli_commands_instance->UserDel($user,"username"));

// UserExist
comment("UserExist");
success($lib_mysqli_commands_instance->UserExist($user));





echo "<hr><h1 color='red'>test database Group management commands</h1><br>";

/* ================ create data: groups ================ */

// GroupAdd
/* the database-concept behind groups is like this:
 * 1. there is a column in the passwd table which contains a comma-separated list of all groups that the user belongs to.
 * 2. the table groups contains all available groups, you can add your own column-properties to the table, enriching the amounts of properties a group can have.
 */
comment("GroupAdd");
$group = $lib_mysqli_commands_instance->GroupAdd($group); // returns the group-object from database, containing a new, database generated id, that is important for editing/deleting the group later
success();

/* ================ read data: groups ================ */
// GroupExist
comment("GroupExist - test if a group exists by id");
if($lib_mysqli_commands_instance->GroupExist($group))
{
	echo "yes group ".$group->groupname." exists.";
}
else
{
	echo "no group ".$group->groupname." does not exists.";
}
success();

// get group by id
comment("get group by id");
$groups = $lib_mysqli_commands_instance->groups($group,"id");
success();

// get group by groupname
comment("get group by groupname");
$group->groupname = "changedTest";
$groups = $lib_mysqli_commands_instance->groups($group,"groupname");
success();

// get group by groupname
comment("get group by groupname");
$groups = $lib_mysqli_commands_instance->groups($group,"mail");
success();

// get all available groups
comment("get all available groups");
$groups = $lib_mysqli_commands_instance->groups();
success();

// get all groups that the user belongs to
comment("get all groups that the user belongs to");
comment("get groups of user as object");
$groups = $lib_mysqli_commands_instance->GetGroupsOfUser($user);
success();

comment("get groups of user as an array of strings");
$groups = $lib_mysqli_commands_instance->GetGroupsOfUser($user,"strings");
// one big , separated string
$groups = array2string($groups,null,",");
success();

// get system groups
comment("get system groups");
$groups = $lib_mysqli_commands_instance->groups(null,"id","WHERE `system` = 1");
success();

// get groups with custom filter
comment("get groups with custom filter");
$groups = $lib_mysqli_commands_instance->groups(null,"id","WHERE `mail` = 'groupA@mail.com'");
success();

// get all groups with this groupname
$groupname = "user";
comment("get all groups with this groupname");
$groups = $lib_mysqli_commands_instance->groups(null,"id","WHERE `groupname` = '".$groupname."'");
success();

/* ================ modify change data: groups ================ */

// groupchange, also update the name in all user records!!!
comment("GroupEdit");
$group->groupname = "changedTest";
$group->mail = "groupA@mail.com";
success($lib_mysqli_commands_instance->GroupEdit($group));

// GroupAddUser - add user to a group
comment("GroupAddUser - add user to a group");
success($lib_mysqli_commands_instance->GroupAddUser($user,$group));

// groupremuser - remove user from group
comment("groupremuser - remove user from group");
success($lib_mysqli_commands_instance->GroupDelUser($user,$group));

/* ================ delete data: groups ================ */
// GroupDel - delete a group ALSO UPDATE USER RECORDS!
comment("GroupDel - delete a group (can not be deleted if users are still in a group)");
$group = $lib_mysqli_commands_instance->NewGroup();
$group->groupname = "test";
success($lib_mysqli_commands_instance->GroupDel($group,"groupname"));










/* ================ create data: records ================ */

// recordget
comment("get definition of arbitrary record from database");
$NewRecord = $lib_mysqli_commands_instance->newRecord("datarecord");

// RecordAdd
comment("RecordAdd - add a arbitrary record to a arbitrary table");
$NewRecord->id = "auto";
$NewRecord->key1 = "value1";
$NewRecord->key2 = "value2";
$NewRecord->key3 = "value3";
$NewRecord = $lib_mysqli_commands_instance->RecordAdd($NewRecord); // returns the record-object from database, containing a new, database generated id, that is important for editing/deleting the record later
success();

// recordchange
comment("RecordEdit: change record");
$NewRecord->key2 = "newvalue2";
$NewRecord->key3 = "newvalue3";
success($lib_mysqli_commands_instance->RecordEdit($NewRecord));

/* ================ read data: records ================ */

// records by id/Mail/Username
comment("get record by ID");
$records = $lib_mysqli_commands_instance->records($NewRecord);
success();

// getUserByUsername
comment("get User by key1");
$records = $lib_mysqli_commands_instance->records($NewRecord,"key1");
success();

// getUserByMail
comment("get User by key2");
$records = $lib_mysqli_commands_instance->records($NewRecord,"key2");
success();

// records
comment("get a list of all records");
$records = $lib_mysqli_commands_instance->records();
success();

// get all records with custom filter
comment("get all records with custom filter");
$records = $lib_mysqli_commands_instance->records(null,"id","WHERE `key1` = 'value1'");
success();

/* ================ modify change data: records ================ */

/* ================ delete data: records ================ */

// RecordDel
comment("RecordDel: del record");
success($lib_mysqli_commands_instance->RecordDel($NewRecord));

// this functionalities need to be implemented with the very general functions above:
// getDevices
// getDeviceByMac
// getButtons
// getOutputs
// getInputs
// GetSessionExpiration
// SetSession
// GetUserBySession
// GetGroups
// getSystemGroups
// GroupExist
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
	if(mysqli_interface::get('worked'))
	{
		echo "<h3 style='color:green;'>worked</h3><br>";
	}
	else
	{
		echo "<h3 style='color:red;'>failed</h3><br>";
	}
}
?>