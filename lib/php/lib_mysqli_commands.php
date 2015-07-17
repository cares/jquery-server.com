<?php
if (file_exists('config.php')) {
	// file was not called directly
	require_once 'config.php';
	require_once('./lib/php/lib_mysqli_interface.php');
	require_once('./lib/php/lib_convert.php');
	require_once('./lib/php/lib_general.php');
} else {
	// file called directly
	require_once '../../config.php';
	require_once('./lib_mysqli_interface.php');
	require_once('./lib_convert.php');
	require_once('./lib_general.php');
}

class lib_mysqli_commands extends mysqli_interface {

	function __construct()
	{
		// init database object
		config::set('mysqli_object');
		$stuff = new mysqli_interface(config::get('database')['name']);
	}
	
	/* ============ USERS */
	
	/* describe a table-structure, returns array/object full of keys = columns
	 * $mode could be "array" or "object" and defines the way the result is returned
	* default is "object"
	* */
	public function describe($table,$mode = "object")
	{
		$result = new stdClass();
		config::get('mysqli_object');
		$worked = false;
		$output; $output = "";
		config::get('database')['name'];
		$tableDefinition = config::get('mysqli_object')->query("DESCRIBE ".$table);
	
		$target = count($tableDefinition);
		for($i=0;$i<$target;$i++)
		{
			$key = $tableDefinition[$i]->Field;
			$result->$key = "";
		}
	
		if($mode == "array")
		{
			$result = object2array($result);
		}
	
		return $result;
	}
	
	/* get definition for a new user teamplate-object from database
	 * meaning: the properties of the user-object depend on the structure of your your passwd (or $config_database_auth_table) table in the database
	* effectively linking your user-Objects-layout to the database */
	public function newUser()
	{
		global $config_database_auth_table;
		return describe($config_database_auth_table);
	}
	
	/* get definition for a new group teamplate-object from database
	 * meaning: the properties of the group-object depend on the structure of your your passwd (or $config_database_auth_table) table in the database
	* effectively linking your group-Objects-layout to the database */
	public function newGroup()
	{
		global $config_database_groups_table;
		return describe($config_database_groups_table);
	}
	
	/* create a new record-teamplate-array-object as defined in database */
	public function newRecord($tableName)
	{
		global $settings_lastTable;
		$settings_lastTable = $tableName;
		return describe($tableName);
	}
	
	/* checks if the user exists */
	public function userexist($user,$uniqueKey = "id")
	{
		$result = null;
	
		config::get('mysqli_object');
		$worked = false;
		$output;
		$output = "";
		config::get('database')['name'];
		$config_database_auth_table;
		$config_database_groups_table;
		$settings_uniqueUsernames;
		$settings_lastTable;
		$settings_lastColumn;
		$query = "";
	
		if(haspropertyandvalue($user,$uniqueKey,"userexist"))
		{
			// filter list
			$query = "SELECT * FROM `".$config_database_auth_table."` WHERE `".$uniqueKey."` = '".$user->$uniqueKey."'";
			$settings_lastDatabase = config::get('database')['name'];
			$settings_lastTable = $config_database_auth_table;
			$settings_lastColumn = $uniqueKey;
		}
	
		$user_array = config::get('mysqli_object')->query($query);
	
		if(empty($user_array))
		{
			config::set('result', false);
		}
		else
		{
			config::set('result', true);
		}
	
		return $result;
	}
	
	/* returns an array of all users available (if no parameter given)
	 *
	* if $user given -> get $user as assoc-array
	* by id (default) if no $uniqueKey is given
	* (you can also specify get user by username,mail -> $uniqueKey)
	*
	* via $where you can filter the users you want with your own sql query
	*/
	public function users($user = null,$uniqueKey = "id",$uniqueValue = null,$where = "")
	{
		$result = null;
	
		config::get('mysqli_object');
		$worked = false;
		$output = "";
		config::get('database')['name'];
		$config_database_auth_table;
		$config_database_groups_table;
		$settings_uniqueUsernames;
		$settings_lastTable;
		$settings_lastColumn;
		$query = "";

		if((!is_null($user)) && haspropertyandvalue($user,$uniqueKey,"users") && (!is_null($uniqueKey)))
		{
			$user_string = "";
			if(is_array($user))
			{
				$user_string = $user[$uniqueKey];
			}
			else if(is_object($user))
			{
				$user_string = $user->$uniqueKey;
			}
	
			// assemble sql-query
			if($uniqueKey == "groups")
			{
				// if it's about groups
				$preFilteredUsersList = users($user,null,"WHERE `groups` LIKE '%".$user->groupname."%'");
					
				$target = count($preFilteredUsersList);
				for ($i = 0; $i <= $target; $i++) {
					$userInstance = $preFilteredUsersList[$i];
					$groupsArray = string2array($userInstance->groups,null);
					if(in_array($user->groupname,$groupsArray))
					{
						array_push($result,$userInstance);
					}
				}
			}
			else
			{
				$query = "SELECT * FROM `".$config_database_auth_table."` WHERE `".$uniqueKey."` = '".$user_string."'";
			}
			$settings_lastDatabase = config::get('database')['name'];
			$settings_lastTable = $config_database_auth_table;
			$settings_lastColumn = $uniqueKey;
		}
		else
		{
			if(empty($where))
			{
				// return all users
				$query = "SELECT * FROM `".$config_database_auth_table."`";
				$settings_lastDatabase = config::get('database')['name'];
				$settings_lastTable = $config_database_auth_table;
				$settings_lastColumn = "";
			}
			else
			{
				$query = "SELECT * FROM `".$config_database_auth_table."` ".$where;
				$settings_lastDatabase = config::get('database')['name'];
				$settings_lastTable = $config_database_auth_table;
			}
		}
	
		// execute sql query
		$user_array = config::get('mysqli_object')->query($query);
		$result = $user_array; // even when only one record is returned, always return an array
	
		if(!empty($result)) $worked = true;
	
		return $result;
	}
	
	/* returns an array of all groups available (if no parameter given)
	 *
	* if $group given -> get $group as assoc-array
	* by id (default) if no $uniqueKey is given
	* (you can also specify get group by groupname,mail -> $uniqueKey)
	*
	* via $where you can filter the groups you want with your own sql query
	*/
	public function groups($group = null,$uniqueKey = "id",$where = "")
	{
		$result = null;
	
		config::get('mysqli_object');
		$worked = false;
		$output = "";
		config::get('database')['name'];
		$config_database_auth_table;
		$config_database_groups_table;
		$settings_uniqueUsernames;
		$settings_lastTable;
		$settings_lastColumn;
		$query = "";
		if((!is_null($group)) && haspropertyandvalue($group,$uniqueKey,"groups") && (!is_null($uniqueKey)))
		{
			if(haspropertyandvalue($group,$uniqueKey,"groups"))
			{
				$group_string = "";
				if(is_array($group))
				{
					$group_string = $group[$uniqueKey];
				}
				else if(is_object($group))
				{
					$group_string = $group->$uniqueKey;
				}
				// filter list
				$query = "SELECT * FROM `".$config_database_groups_table."` WHERE `".$uniqueKey."` = '".$group_string."'";
				$settings_lastDatabase = config::get('database')['name'];
				$settings_lastTable = $config_database_auth_table;
				$settings_lastColumn = $uniqueKey;
			}
		}
		else
		{
			if(empty($where))
			{
				// return all groups
				$query = "SELECT * FROM `".$config_database_groups_table."`";
				$settings_lastDatabase = config::get('database')['name'];
				$settings_lastTable = $config_database_groups_table;
				$settings_lastColumn = "";
			}
			else
			{
				$query = "SELECT * FROM `".$config_database_groups_table."` ".$where;
				$settings_lastDatabase = config::get('database')['name'];
				$settings_lastTable = $config_database_groups_table;
				$settings_lastColumn = "";
			}
		}
	
		$group_array = config::get('mysqli_object')->query($query);
		if(isset($group_array))
		{
			if(count($group_array) <= 1)
			{
				if(isset($group_array[0]))
				{
					$result = $group_array[0];
				}
			}
			else
			{
				$result = $group_array; // multiple records returned
			}
		}
	
		if(!empty($result)) $worked = true;
	
		return $result;
	}
	
	/* set $session
	 * set session to browser as cookie and to mysql database
	* iterate over the list:
	*/
	public function setSession($username,$password)
	{
		config::get('mysqli_object');$worked = false;$output = "";
		config::get('database')['name']; global $config_database_auth_table; global $config_database_groups_table; global $settings_uniqueUsernames;  global $settings_lastTable; global $settings_lastColumn;
		global $settings_login_session_timeout;
	
		// the ip that the user had during login
		$ip_login = $_SERVER['REMOTE_ADDR'];
	
		// when the user logged in (ms since 1.1.1970
		$logintime = time();
	
		$salt = "";
		$salt = salt();
	
		$_SESSION['session'] = md5($username . $password . $salt);
	
		$valid_until = time(); // get current time
		$valid_until = $valid_until+($settings_login_session_timeout*1000);
	
		config::get('mysqli_object') -> query("UPDATE `".config::get('database')['name']."`.`".$config_database_auth_table."` SET `logintime` = '".$logintime."', `ip_login` = '".$ip_login."', `loginexpires` = '".$valid_until."', `session` = '".$_SESSION['session']."' WHERE `".$config_database_auth_table."`.`username` = '".$username."' AND `".$config_database_auth_table."`.`password` = '".$password."';");
	
		return $valid_until;
	}
	
	/*
	 * get $session
	* $session hash is md5($username.$password.$salt)
	* returns timestamp until when the session is valid
	*/
	public function getSessionExpiration($session, $user) {
		$valid_until = null;
		// check if an user object was handed over
		if (! $user) {
			// no user object was handed over -> get user
			$user = getUserBySession ( $session );
		}
	
		if ($user) {
			// hash found
			$valid_until = $user->loginexpires;
		}
	
		return $valid_until;
	}
	
	/* get user by session
	 */
	public function getUserBySession($session)
	{
		config::get('mysqli_object');$worked = false;$output = "";
		config::get('database')['name']; global $config_database_auth_table; global $config_database_groups_table; global $settings_uniqueUsernames;  global $settings_lastTable; global $settings_lastColumn;
		$result = "";
		if($session)
		{
			$valid_until = null;
			$user_array = config::get('mysqli_object')->query("SELECT * FROM `".$config_database_auth_table."` WHERE `session` = '".$session."'");
			// $user = config::get('mysqli_object')->query("SELECT * FROM `".config::get('database')['name']."`.`".$config_database_auth_table."` WHERE `session` = '".$session."'");
			if(isset($user_array[0]))
			{
				// hash found
				$result = $user_array[0];
			}
		}
	
		if(!empty($result)) $worked = true;
	
		return $result;
	}
	
	/* delete user
	 * $identifyByKey -> the key by which you want to identify your user
	* usually every user has a unique id given by the database
	* so it's savest to use id
	*
	* but you might also want to delete all users named "joe"
	*
	* so go
	* $user = newUser();
	* $user->username = "joe";
	* userdel($user,"username");
	* */
	public function userdel($user,$identifyByKey = "id")
	{
		if(!is_object($user))
		{
			return error("function userdel: expected input \$user to be an object");
		}
		config::get('mysqli_object');$worked = false;$output = "";
		config::get('database')['name']; global $config_database_auth_table; global $config_database_groups_table; global $settings_uniqueUsernames;  global $settings_lastTable; global $settings_lastColumn;
		$worked = false;
	
		if(haspropertyandvalue($user,$identifyByKey,"userdel"))
		{
			$output = config::get('mysqli_object')->query("DELETE FROM  `".config::get('database')['name']."`.`".$config_database_auth_table."` WHERE `".$config_database_auth_table."`.`".$identifyByKey."` = '".$user->$identifyByKey."';");
			$worked = true;
		}
	
		return $worked;
	}
	
	/* add/register a new user
	 *
	* the properties a $user-array-object can have is defined through the database
	* (table defined in config/config.php -> $config_database_auth_table e.g. passwd)
	*
	* add a column there, and you have a new property attached to $user.
	*
	* To create/add a $user you first need to get this database-defined-layout
	*
	* $user = newUser();
	*
	* Then you modify the array: username is required, anything else is optional.
	*
	* $user->username= "user";
	*
	* adduser($user);
	*
	* That's it!
	* */
	public function useradd($user) // $requested_username = "",$requested_password = "",$groups = "",$data = ""
	{
		config::get('mysqli_object');$worked = false;$output = "";
		config::get('database')['name']; global $config_database_auth_table; global $config_database_groups_table; global $settings_uniqueUsernames;  global $settings_lastTable; global $settings_lastColumn;
		global $settings_default_home_after_login;
	
		if(!haspropertyandvalue($user, "username", "useradd"))
		{
			$worked = false;
			return $worked;
		}
	
		// check if user allready exists
		if($settings_uniqueUsernames)
		{
			if(userexist($user,"username"))
			{
				return error("function useradd: can not continue, user ".$user->username." is taken and \$settings_uniqueUsernames is set to true.");
			}
		}
	
		// under linux, when creating users there is always a a group created with the same name, that per default this user belongs to (it's "his" group)
		// search for username in groups, if not found add.
		if(empty($user->home))
		{
			$user->home = $settings_default_home_after_login;
		}
	
		// Create a unique  activation code:
		$user->activation = md5(uniqid(rand(), true));
	
		// under linux, when creating users there is always a a group created with the same name, that per default this user belongs to (it's "his" group)
		// check if given groups already exist, if not add
		$group = newGroup();
		$group->groupname = $user->username;
	
		if(!groupexist($group))
		{
			groupadd($group);
		}
	
		// add user to this group
		if(empty($user->groups))
		{
			$user->groups = $group->groupname.",";
		}
		else
		{
			$user->groups = $user->groups.",".$group->groupname.",";
		}
	
		// search for username in groups, if not found add.
		// allready contains username in group-list
		$user->id = ""; // id will always be automatically set by database/backend/autoincrement, or things will become chaotic
	
		$values = arrayobject2sqlvalues($user,"INSERT");
		$query = "INSERT INTO `".config::get('database')['name']."`.`".$config_database_auth_table."` ".$values;
		$settings_lastDatabase = config::get('database')['name'];
		$settings_lastTable = $config_database_auth_table;
		$settings_lastColumn = "";
	
		// return data = false, return errors = true
		$output = config::get('mysqli_object') -> query($query,false,true);
	
		// get the id of the just created user-object
		global $id_last;
		$user->id = $id_last;
	
		$worked = true;
	
		return $user;
	}
	
	/* edit/update/change a user
	 * $groups = a,comma,separated,list,of,groupnames
	* arbitrary additional details data about the user
	* data -> $data = "key:value,key:value," */
	public function useredit($UpdatedUser,$uniqueKey = "id") // $userID, $requested_username = "",$requested_password = "",$groups = "",$data = ""
	{
		// check if user with this username allready exists -> warn
	
		config::get('mysqli_object');$worked = false;$output = "";
		config::get('database')['name']; global $config_database_auth_table; global $config_database_groups_table; global $settings_uniqueUsernames;  global $settings_lastTable; global $settings_lastColumn;
		global $settings_default_home_after_login;
	
		// get all info about user
		$user_database = getFirstElementOfArray(users($UpdatedUser,$uniqueKey));
	
		// merge it
		$UpdatedUser = mergeObject($UpdatedUser,$user_database);
	
		// if $settings_uniqueUsernames enabled -> check if username is allready in use/exists
		if($settings_uniqueUsernames)
		{
			if($user_database->username != $UpdatedUser->username)
			{
				if(userexist($UpdatedUser,"username"))
				{
					return error("function useredit: can not rename username from ".$user_database->username." to ".$UpdatedUser->username." because the username is allready in use.");
				}
			}
		}
	
		$values = arrayobject2sqlvalues($UpdatedUser,"UPDATE");
	
		$query = "UPDATE `".config::get('database')['name']."`.`".$config_database_auth_table."` SET ".$values." WHERE `".$config_database_auth_table."`.`".$uniqueKey."` = '".$UpdatedUser->$uniqueKey."';";
		$settings_lastDatabase = config::get('database')['name'];
		$settings_lastTable = $config_database_auth_table;
		$settings_lastColumn = $uniqueKey;
	
		$output = config::get('mysqli_object') -> query($query,false,true);
	
		return $output;
	}
	
	/* ============ GROUP */
	/* add a group to the system (list of available groups) / add/register a new group
	 *
	* $systemgroup = 1 -> this group is a system-group (like admin, guest... that can not/should not be deleted, even if there are no users anymore using it)
	*
	* the properties a group-array-object can have is defined through the database
	* (table defined in config/config.php -> $config_database_auth_table e.g. passwd)
	*
	* add a column there, and you have a new property attached to group.
	*
	* To create/add a $group you first need to get this database-defined-layout
	*
	* $user = newGroup();
	*
	* Then you modify the array: groupname is required, anything else is optional.
	*
	* $group->groupname= "group";
	*
	* addgroup($group);
	*
	* That's it!
	* */
	public function groupadd($group,$systemgroup = 0)
	{
		if(!haspropertyandvalue($group, "groupname", "groupadd"))
		{
			$worked = false;
			return $worked;
		}
	
		config::get('mysqli_object');$worked = false;$output = "";
		global $config_database_auth_table; global $config_database_groups_table;
		config::get('database')['name'];
		global $settings_default_home_after_login;
	
		// under linux, when creating groups there is always a a group created with the same name, that per default this group belongs to (it's "his" group)
		// check if given groups already exist, if not add
		if(!groupexist($group,"groupname"))
		{
			// search for groupname in groups, if not found add.
			// allready contains groupname in group-list
			$group->id = ""; // id will always be automatically set by database/backend/autoincrement, or things will become chaotic
			$group->system = $systemgroup;
	
			$values = arrayobject2sqlvalues($group,"INSERT");
			$query = "INSERT INTO `".config::get('database')['name']."`.`".$config_database_groups_table."` ".$values;
			$settings_lastDatabase = config::get('database')['name'];
			$settings_lastTable = $config_database_groups_table;
			$settings_lastColumn = "";
	
			// return data = false, return errors = true
			$output = config::get('mysqli_object') -> query($query,false,true);
			// get the id of the just created group-object
			global $id_last;
			$group->id = $id_last;
	
			$worked = true;
		}
		else
		{
			$output = "function groupadd: group allready exists.";
			return error($output);
		}
	
		return $group;
	}
	
	/* edit/update/change a group
	 * $groups = a,comma,separated,list,of,groupnames
	* arbitrary additional details data about the group
	* data -> $data = "key:value,key:value,"
	*/
	public function groupedit($UpdatedGroup,$uniqueKey = "id") // $groupID, $requested_groupname = "",$requested_password = "",$groups = "",$data = ""
	{
		// check if group with this groupname allready exists -> warn
	
		config::get('mysqli_object');$worked = false;$output = "";
		global $config_database_auth_table; global $config_database_groups_table;
		config::get('database')['name'];
		global $settings_default_home_after_login;
	
		// get all info about group
		$group_database = groupget($UpdatedGroup,$uniqueKey);
	
		// merge it
		$UpdatedGroup = mergeObject($UpdatedGroup,$group_database);
	
		// if $settings_uniqueGroupnames enabled -> check if groupname is allready in use/exists
		if(groupexist($UpdatedGroup,"groupname"))
		{
			return error("function groupedit: can not rename group from ".$group_database->groupname." to ".$UpdatedGroup->groupname." because the groupname is allready in use.");
		}
	
		$values = arrayobject2sqlvalues($UpdatedGroup,"UPDATE");
	
		$query = "UPDATE `".config::get('database')['name']."`.`".$config_database_groups_table."` SET ".$values." WHERE `".$config_database_groups_table."`.`".$uniqueKey."` = '".$UpdatedGroup->$uniqueKey."';";
		$settings_lastDatabase = config::get('database')['name'];
		$settings_lastTable = $config_database_groups_table;
		$settings_lastColumn = $uniqueKey;
	
		$output = config::get('mysqli_object') -> query($query,false,true);
	
		return $output;
	}
	
	/* get $group as assoc-array
	 * by id, if no $uniqueKey is given (could also be groupname,mail if those values are unique)
	*/
	public function groupget($group = null,$uniqueKey = "id")
	{
		$result = null;
	
		config::get('mysqli_object');$worked = false;$output = "";
		config::get('database')['name'];
		global $config_database_auth_table; global $config_database_groups_table;
		$query = "";
		if(!is_null($group))
		{
			if(haspropertyandvalue($group,$uniqueKey,"groupget"))
			{
				$group_string = "";
				if(is_array($group))
				{
					$group_string = $group[$uniqueKey];
				}
				else if(is_object($group))
				{
					$group_string = $group->$uniqueKey;
				}
				// filter list
				$query = "SELECT * FROM `".$config_database_groups_table."` WHERE `".$uniqueKey."` = '".$group_string."'";
				$settings_lastDatabase = config::get('database')['name'];
				$settings_lastTable = $config_database_groups_table;
				$settings_lastColumn = $uniqueKey;
			}
		}
		else
		{
			// return all groups
			$query = "SELECT * FROM `".$config_database_groups_table."`";
			$settings_lastDatabase = config::get('database')['name'];
			$settings_lastTable = $config_database_groups_table;
			$settings_lastColumn = "";
		}
	
		$group_array = config::get('mysqli_object')->query($query);
		if(isset($group_array))
		{
			if(count($group_array) <= 1)
			{
				if(isset($group_array[0]))
				{
					$result = $group_array[0];
				}
			}
			else
			{
				$result = $group_array; // multiple records returned
			}
		}
	
		if(!empty($result)) $worked = true;
	
		return $result;
	}
	
	/* delete a group */
	public function groupdel($group,$identifyByKey = "id")
	{
		if(is_string($group))
		{
			$group_object = newGroup();
			$group_object->$identifyByKey = $group;
			$group = $group_object;
		}
	
		if(!is_object($group))
		{
			return error("function groupdel: expected input \$group to be an object");
		}
		config::get('mysqli_object');$worked = false;$output = "";
		config::get('database')['name']; global $config_database_auth_table; global $config_database_groups_table; global $settings_uniqueUsernames;  global $settings_lastTable; global $settings_lastColumn;
		$worked = false;
	
		if(haspropertyandvalue($group,$identifyByKey,"groupdel"))
		{
			// check out if there are still users in this group -> refuse to delete
			$users = users();
	
			$group_in_use = false;
	
			if(!empty($users))
			{
				if(is_object($users))
				{
					$users_array[] = $users;
					$users = $users_array;
				}
	
				$count = count($users);
	
				$username = "";
				for($i=0;$i<$count;$i++)
				{
					$username = $users[$i]->username;
					$groups = $users[$i]->groups;
					$groups_array = explode(",",$groups);
					$groupname = $group->groupname;
					if(in_array($groupname, $groups_array))
					{
						$group_in_use = true;
						break;
					}
				}
			}
	
			if($group_in_use)
			{
				error("function groupdel: can not delete group with name: ".$groupname." - the group is still in use by group ".$groupname);
				$worked = false;
				return $worked;
			}
			else
			{
				$query = "DELETE FROM `".config::get('database')['name']."`.`".$config_database_groups_table."` WHERE `".$config_database_groups_table."`.`".$identifyByKey."` = '".$group->$identifyByKey."';";
				$settings_lastDatabase = config::get('database')['name'];
				$settings_lastTable = $config_database_groups_table;
				$settings_lastColumn = $identifyByKey;
	
				$result = config::get('mysqli_object') -> query($query,false,true);
			}
		}
	
		return $worked;
	}
	
	
	/* get a list of all available groups
	 * $option = as array
	* $option = as object
	*/
	public function getGroups($option = "as object")
	{
		$result = null;
		config::get('mysqli_object');$worked = false;$output = "";
		config::get('database')['name']; global $config_database_auth_table; global $config_database_groups_table; global $settings_uniqueUsernames;  global $settings_lastTable; global $settings_lastColumn;
		$result = config::get('mysqli_object')->query("SELECT * FROM `".$config_database_groups_table."`");
	
		if($option == "as array")
		{
			$result_tmp = array();
			$target = count($result);
			for($i=0;$i<$target;$i++)
			{
				$result_tmp[] = $result[$i]->groupname;
			}
	
			$result = $result_tmp;
		}
	
		return $result;
	}
	
	/* checks if a group exists
	 *
	* you can either pass a $group object with ->groupname set, or the name of the group as string
	*
	* alternative way to do it:
	* $groups = groups(null,"WHERE `groupname` = '".$user->username."'");
	* if(!empty($groups))
		* {
	* 		// yes group does exist
	* }
	* -> then check if $groups array is empty.
	* */
	public function groupexist($group,$uniqueKey = "id")
	{
		if(is_string($group))
		{
			$group_object = newGroup();
			$group_object->$uniqueKey = $group;
			$group = $group_object;
		}
		if(!haspropertyandvalue($group, $uniqueKey, "groupexist"))
		{
			$worked = false;
			return $worked;
		}
	
		config::set('result', false); // default result value
		config::get('mysqli_object');$worked = false;$output = "";
		config::get('database')['name']; global $config_database_auth_table; global $config_database_groups_table; global $settings_uniqueUsernames;  global $settings_lastTable; global $settings_lastColumn;
	
		$query = "SELECT * FROM `".$config_database_groups_table."` WHERE `".$uniqueKey."` = '".$group->$uniqueKey."'";
		$settings_lastDatabase = config::get('database')['name'];
		$settings_lastTable = $config_database_groups_table;
		$settings_lastColumn = $uniqueKey;
	
		$result_array = config::get('mysqli_object')->query($query);
	
		if($result_array)
		{
			config::set('result', true);
		}
	
		return $result;
	}
	
	/* output all users of a given group as selectable <html> list
	 *
	// if $goup == * -> all users of all groups are
	// if $goup == "users" -> all users that are not admin
	// if $goup == "yourself" -> the currently logged in user
	*/
	/*
	 function generateUserList($group = "*")
	 {
	if(($group == "*") || ($group == "users"))
	{
	$users = users();
	}
	else if($group == "yourself")
	{
	global $user;
	$users[] = $user;
	}
	else
	{
	$users = getUsersByGroup($group); // must be replaced with something like: $users = users($user,"groups");
	}
	if($group == "*") $group = "All Users:";
	echo '
	<h4>'.$group.'</h4>
	<ul data-role="listview">';
	
	// paint a list of users
	foreach($users as $key => $user)
	{
	$paint = true;
	
	// if $goup == users -> all users that are not admin
	if($group == "users")
	{
	$groups_of_element = getgetGroupsOfUser($user);
	if(in_array("admins",$groups_of_element))
	{
	$paint = false;
	}
	}
		
	if($paint)
	{
	$data = string2array($user->data);
	if(!isset($data['profilepicture']))
	{
	$data['profilepicture'] = "";
	}
	echo '
	<li>
	<input type="checkbox" class="checkbox" name="checkbox_'.$user->username.'" id="checkbox_'.$user->username.'" data-mini="true" value="0" userid="'.$user->id.'"/>
	<a href="frontend_useredit.php?selectUserId='.$user->id.'" rel="external" data-ajax="false">
	<img id="profilepicture'.$user->id.'" src="'.$data['profilepicture'].'" class="profilepicture"/>
	<h3 id="username'.$user->id.'">'.$user->username.'</h3>
	<p>UserID:'.$user->id.','.$user->data.'</p>
	</a>
	</li>';
	}
	}
	echo '
	</ul>';
	}
	*/
	
	/* get all groups of given user(s) as
	 * -> $result_mode = "objects" array of database-objects
	* -> $result_mode = "strings" */
	public function getGroupsOfUser($user = null,$result_mode = "objects")
	{
		$result = Array();
		$query = "";
		config::get('mysqli_object');$worked = false;$output = "";
		config::get('database')['name']; global $config_database_auth_table; global $config_database_groups_table; global $settings_uniqueUsernames;  global $settings_lastTable; global $settings_lastColumn;
	
		$users = users($user); // in case we got no $user->groups info
	
		if(is_object($users))
		{
			// if only one user given make it an array
			$users_array[] = $users;
			$users = $users_array;
		}
	
		if($result_mode == "objects")
		{
			$target = count($users);
			for($i=0;$i<$target;$i++)
			{
				$groups = $users[$i]->groups;
				$groups_array = explode(",",$groups);
				$groups_array = array_filter( $groups_array, 'strlen' );
	
				$targetj = count($groups_array);
				for($j=0;$j<$target;$j++)
				{
					$group = $groups_array[$j];
					if($j == 0)
					{
						$query = $query . "SELECT * FROM `".$config_database_groups_table."` WHERE `groupname` = '".$group."'";
					}
					else
					{
						$query = $query . "UNION SELECT * FROM `".$config_database_groups_table."` WHERE `groupname` = '".$group."'";
					}
					$settings_lastDatabase = config::get('database')['name'];
					$settings_lastTable = $config_database_groups_table;
					$settings_lastColumn = "groupname";
				}
			}
	
			$result = config::get('mysqli_object')->query($query);
		}
	
		if($result_mode == "strings")
		{
			$target = count($users);
			for($i=0;$i<$target;$i++)
			{
				$groups = $users[$i]->groups;
				$groups_array = explode(",",$groups);
				$groups_array = array_filter( $groups_array, 'strlen' );
	
				for($j=0;$j<$target;$j++)
				{
					$result[] = $groups_array[$j];
				}
			}
			$result = array_unique($result);
		}
	
		return $result;
	}
	
	/* groupadduser - add user to a group */
	public function groupadduser($user,$group)
	{
		$user = users($user); // get groups from database
	
		$groupname = "";
	
		if(is_object($group))
		{
			$groupname = $group->groupname;
		}
		else if(is_string($group))
		{
			$groupname = $group;
		}
	
		$lastChar = substr($user->groups, -1);
		if($lastChar != ",")
		{
			$user->groups .= ",".$groupname;
		}
		else
		{
			$user->groups .= $groupname;
		}
	
		return useredit($user);
	}
	
	/* groupdeluser - add user to a group */
	public function groupdeluser($user,$group)
	{
		$user = users($user); // get groups from database
	
		$groupname = "";
	
		if(is_object($group))
		{
			$groupname = $group->groupname;
		}
		else if(is_string($group))
		{
			$groupname = $group;
		}
	
		$groups_array = string2array($user->groups, null, ",");
		$groups_array = arrayRemoveEmpty($groups_array);
		$groups_array = arrayRemoveElement($groups_array,null,$groupname);
		$groups = array2string($groups_array, null, ",");
	
		$user->groups = $groups;
	
		return useredit($user);
	}
	
	/* add/register a new record
	 *
	* the properties a $record-array-object can have is defined through the database
	* (table defined in config/config.php -> $config_database_auth_table e.g. passwd)
	*
	* add a column there, and you have a new property attached to $record.
	*
	* To create/add a $record you first need to get this database-defined-layout
	*
	* $record = newRecord("tableName");
	*
	* Then you modify the array like this:
	*
	* $record->columnName= "value"; // columnName is the name of a column in tableName
	* addrecord($record);
	*
	* That's it!
	* */
	public function recordadd($record,$table = null) // $requested_recordname = "",$requested_password = "",$groups = "",$data = ""
	{
		/* -----defaults------ */
		config::get('mysqli_object');$worked = false;$output = "";
		config::get('database')['name']; global $config_database_auth_table;  global $settings_lastTable; global $settings_lastColumn;
	
		if(is_null($table))
		{
			$table = $settings_lastTable;
		}
		$settings_lastTable = $table;
		$settings_lastDatabase = config::get('database')['name'];
		$query = "";
		/* -----defaults-end----- */
	
		$record->id = ""; // id will always be automatically set by database/backend/autoincrement, or things will become chaotic
	
		$values = arrayobject2sqlvalues($record,"INSERT");
		$query = "INSERT INTO `".config::get('database')['name']."`.`".$table."` ".$values;
		$settings_lastTable = $table;
		$settings_lastColumn = "";
	
		// return data = false, return errors = true
		$output = config::get('mysqli_object') -> query($query,false,true);
	
		// get the id of the just created record-object
		global $id_last;
		$record->id = $id_last;
	
		$worked = true;
	
		return $record;
	}
	
	/* edit/update/change a record
	 */
	public function recordedit($UpdatedRecord,$uniqueKey = "id",$table = null)
	{
		/* -----defaults------ */
		config::get('mysqli_object');$worked = false;$output = "";
		config::get('database')['name']; global $config_database_auth_table;  global $settings_lastTable; global $settings_lastColumn;
	
		if(is_null($table))
		{
			$table = $settings_lastTable;
		}
		$settings_lastTable = $table;
		$settings_lastDatabase = config::get('database')['name'];
		$query = "";
		/* -----defaults-end----- */
	
		// get all info about record
		$record_database = records($UpdatedRecord,$uniqueKey);
	
		// merge it
		$UpdatedRecord = mergeObject($UpdatedRecord,$record_database);
	
		$values = arrayobject2sqlvalues($UpdatedRecord,"UPDATE");
		$query = "UPDATE `".config::get('database')['name']."`.`".$table."` SET ".$values." WHERE `".$table."`.`".$uniqueKey."` = '".$UpdatedRecord->$uniqueKey."';";
		$settings_lastTable = $table;
		$settings_lastColumn = "";
	
		$output = config::get('mysqli_object') -> query($query,false,true);
	
		return $output;
	}
	
	/* delete record
	 * $identifyByKey -> the key by which you want to identify your record
	* usually every record has a unique id given by the database
	* so it's savest to use id
	*
	* but you might also want to delete all records named "joe"
	*
	* so go
	* $record = newRecord();
	* $record->recordname = "joe";
	* recorddel($record,"recordname");
	* */
	public function recorddel($record,$identifyByKey = "id",$table = null)
	{
		if(!is_object($record))
		{
			return error("function recorddel: expected input \$record to be an object");
		}
	
		/* -----defaults------ */
		$query = "";
		config::get('mysqli_object');$worked = false;$output = "";
		config::get('database')['name'];  global $settings_lastTable; global $settings_lastColumn;
	
		if(is_null($table))
		{
			$table = $settings_lastTable;
		}
		$settings_lastTable = $table;
		$settings_lastDatabase = config::get('database')['name'];
		$query = "";
		/* -----defaults-end----- */
	
		if(haspropertyandvalue($record,$identifyByKey,"recorddel"))
		{
			$query = "DELETE FROM  `".config::get('database')['name']."`.`".$table."` WHERE `".$table."`.`".$identifyByKey."` = '".$record->$identifyByKey."';";
			$output = config::get('mysqli_object')->query($query);
			$worked = true;
		}
	
		return $worked;
	}
	
	/* returns an array of all records available (if no parameter given)
	 *
	* if $record given -> get $record as assoc-array
	* by id (default) if no $uniqueKey is given
	* (you can also specify get record by recordname,mail -> $uniqueKey)
	*
	* via $where you can filter the records you want with your own sql query
	*/
	public function records($record = null,$uniqueKey = "id",$where = "",$table = null)
	{
		$result = null;
	
		/* -----defaults------ */
		config::get('mysqli_object');$worked = false;$output = "";
		config::get('database')['name']; global $config_database_auth_table;  global $settings_lastTable; global $settings_lastColumn;
	
		if(is_null($table))
		{
			$table = $settings_lastTable;
		}
		$settings_lastTable = $table;
		$settings_lastDatabase = config::get('database')['name'];
		$query = "";
		/* -----defaults-end----- */
	
		if(!is_null($record))
		{
			if(haspropertyandvalue($record,$uniqueKey,"records"))
			{
				$record_string = "";
				if(is_array($record))
				{
					$record_string = $record[$uniqueKey];
				}
				else if(is_object($record))
				{
					$record_string = $record->$uniqueKey;
				}
	
				$query = "SELECT * FROM `".$table."` WHERE `".$uniqueKey."` = '".$record_string."'";
				$settings_lastColumn = $uniqueKey;
			}
		}
		else
		{
			if(empty($where))
			{
				// return all records
				$query = "SELECT * FROM `".$table."`";
				$settings_lastColumn = "";
			}
			else
			{
				$query = "SELECT * FROM `".$table."` ".$where;
				$settings_lastColumn = "";
			}
		}
	
		if(!empty($query))
		{
			$output = config::get('mysqli_object')->query($query);
		}
	
		$record_array = config::get('mysqli_object')->query($query);
		if(isset($record_array))
		{
			if(count($record_array) <= 1)
			{
				if(isset($record_array[0]))
				{
					$result = $record_array[0];
				}
			}
			else
			{
				$result = $record_array; // multiple records returned
			}
		}
	
		if(!empty($result)) $worked = true;
	
		return $result;
	}
	/*
	
	function getDevices($where = "")
	{
	config::get('mysqli_object');$worked = false;$output = "";
	// config::get('database')['name'];
	return config::get('mysqli_object')->query("SELECT * FROM `devices` ".$where);
	}
	function getDeviceByMac($mac = "")
	{
	config::get('mysqli_object');$worked = false;$output = "";
	// config::get('database')['name'];
	return config::get('mysqli_object')->query("SELECT * FROM `devices` WHERE `mac` = '".$mac."';");
	}
	function getButtons($where = "")
	{
	config::get('mysqli_object');$worked = false;$output = "";
	config::get('database')['name'];
	return config::get('mysqli_object')->query("SELECT * FROM `buttons` ".$where);
	}
	function getOutputs($where = "")
	{
	config::get('mysqli_object');$worked = false;$output = "";
	config::get('database')['name'];
	
	return config::get('mysqli_object')->query("SELECT * FROM `outputs` ".$where);
	}
	function getInputs($where = "")
	{
	config::get('mysqli_object');$worked = false;$output = "";
	config::get('database')['name'];
	
	return config::get('mysqli_object')->query("SELECT * FROM `inputs` ".$where);
	}
	*/
	
	/* load sql-commands from a sql file */
	public function loadSQLFromFile($url)
	{
		// ini_set ( 'memory_limit', '512M' );
		// set_time_limit ( 0 );
	
		config::get('database')['name'];
		config::get('mysqli_object');$worked = false;$output = "";
	
		$sql_query = "";
	
		// read line by line
		$lines = file($url);
		$count = count($lines);
	
		for($i = 0;$i<$count;$i++)
		{
			$line = $lines[$i];
			$cmd3 = substr($line, 0, 3);
			$cmd4 = substr($line, 0, 4);
			$cmd6 = substr($line, 0, 6);
			if($cmd3 == "USE")
			{
				// cut away USE ``;
				config::set('name',substr($line, 5, -3));
			}
			else if($cmd4 == "DROP")
			{
				config::get('mysqli_object')->query($line); // execute this line
			}
			else if(($cmd6 == "INSERT") || ($cmd6 == "CREATE"))
			{
				// sum all lines up until ; is detected
				$multiline = $line;
				while(!strstr($line, ';'))
				{
					$i++;
					$line = $lines[$i];
					$multiline .= $line;
				}
				$multiline = str_replace("\n", "", $multiline); // remove newlines/linebreaks
				config::get('mysqli_object')->query($multiline); // execute this line
			}
		}
	
		return $worked;
	}
	
	/* check if a given database exists */
	public function databaseExists($databaseName)
	{
		mysqli_interface::set('output',array()); // contains message to client e.g. the last success/error message
		mysqli_interface::set('result', false); // result / data returned of last query

		$query = "SHOW DATABASES;";
		$allDatabaseNames = mysqli_interface::query($query);
	
		$target = count($allDatabaseNames);
		for($i=0;$i<$target;$i++)
		{
			if($databaseName == $allDatabaseNames[$i]->Database)
			{
				$result = true;
				break;
			}
		}

		mysqli_interface::set('result', $result); // save result, then return
		return $result;
	}
}

/*
 * this file handles all sorts of user-database-operations, it can not be called directly via url?parameter=evil
* so it does not need all the ./lib/php/lib_session.php/./lib/php/lib_security.php, but the parent.php does!
*/

?>