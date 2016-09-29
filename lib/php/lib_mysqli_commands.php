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
		$mysqli_interface_instance = new mysqli_interface(config::get('database')['name']);
	}
	
	/* ============ USERS */
	
	/* describe a table-structure, returns array/object full of keys = columns
	 * $mode could be "array" or "object" and defines the way the result is returned
	* default is "object"
	* */
	public function describe($table,$mode = "object")
	{
		$output = new stdClass();
		mysqli_interface::set('worked',false);
		mysqli_interface::set('output','');

		$tableDefinition = config::get('mysqli_object')->query("DESCRIBE ".$table);
	
		$target = count($tableDefinition);
		
		if($target == 0)
		{
			// empty table, no columns
			mysqli_interface::set('worked',true);
			mysqli_interface::set('output',$tableDefinition);
		}
		else
		{
			for($i=0;$i<$target;$i++)
			{
				$key = $tableDefinition[$i]->Field;
				$output->$key = "";
			}
		
			if($mode == "array")
			{
				$output = object2array($output);
			}
			
			mysqli_interface::set('worked',true);
			mysqli_interface::set('output',$output);
		}
	
		return mysqli_interface::get('output');
	}
	
	/* get definition for a new user teamplate-object from database
	 * meaning: the properties of the user-object depend on the structure of your your passwd (or config::get('database')['auth_table']) table in the database
	* effectively linking your user-Objects-layout to the database */
	public function NewUser()
	{
		return $this->describe(config::get('database')['auth_table']);
	}
	
	/* get definition for a new group teamplate-object from database
	 * meaning: the properties of the group-object depend on the structure of your your passwd (or config::get('database')['auth_table']) table in the database
	* effectively linking your group-Objects-layout to the database */
	public function NewGroup()
	{
		return $this->describe(config::get('database')['groups_table']);
	}
	
	/* create a new record-teamplate-array-object as defined in database */
	public function NewRecord($tableName)
	{
		return $this->describe($tableName);
	}
	
	/* checks if the user exists */
	public function UserExist($user,$uniqueKey = "id")
	{
		$result = null;
		mysqli_interface::set('worked',false);
		$output = "";
		$query = "";
	
		if(haspropertyandvalue($user,$uniqueKey,"UserExist"))
		{
			// filter list
			$query = "SELECT * FROM `".config::get('database')['auth_table']."` WHERE `".$uniqueKey."` = '".$user->$uniqueKey."'";
			
			
			config::get('database')['lastColumn'] = $uniqueKey;
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
		mysqli_interface::set('worked',false);
		$output = "";
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
				$preFilteredUsersList = $this->users($user,null,"WHERE `groups` LIKE '%".$user->groupname."%'");
					
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
				$query = "SELECT * FROM `".config::get('database')['auth_table']."` WHERE `".$uniqueKey."` = '".$user_string."'";
			}
			
			
			config::get('database')['lastColumn'] = $uniqueKey;
		}
		else
		{
			if(empty($where))
			{
				// return all users
				$query = "SELECT * FROM `".config::get('database')['auth_table']."`";
				
				
				config::get('database')['lastColumn'] = "";
			}
			else
			{
				$query = "SELECT * FROM `".config::get('database')['auth_table']."` ".$where;
				
				
			}
		}
	
		// execute sql query
		$user_array = config::get('mysqli_object')->query($query);
		$result = $user_array; // even when only one record is returned, always return an array
	
		if(!empty($result)) mysqli_interface::set('worked',true);
	
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
		mysqli_interface::set('worked',false);
		$output = "";

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
				$query = "SELECT * FROM `".config::get('database')['groups_table']."` WHERE `".$uniqueKey."` = '".$group_string."'";
				
				
				config::get('database')['lastColumn'] = $uniqueKey;
			}
		}
		else
		{
			if(empty($where))
			{
				// return all groups
				$query = "SELECT * FROM `".config::get('database')['groups_table']."`";
				
				config::get('database')['lastColumn'] = "";
			}
			else
			{
				$query = "SELECT * FROM `".config::get('database')['groups_table']."` ".$where;
				
				
				config::get('database')['lastColumn'] = "";
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
	
		if(!empty($result)) mysqli_interface::set('worked',true);
	
		return $result;
	}
	
	/* set $session
	 * set session to browser as cookie and to mysql database
	* iterate over the list:
	*/
	public function SetSession($username,$password)
	{
		mysqli_interface::set('worked',false);
		$output = "";
	
		// the ip that the user had during login
		$ip_login = $_SERVER['REMOTE_ADDR'];
	
		// when the user logged in (ms since 1.1.1970
		$logintime = time();
	
		$salt = "";
		$salt = salt();
	
		$_SESSION['session'] = md5($username . $password . $salt);
	
		$valid_until = time(); // get current time
		$valid_until = $valid_until+(config::get('login_session_timeout')*1000);
	
		config::get('mysqli_object') -> query("UPDATE `".config::get('database')['name']."`.`".config::get('database')['auth_table']."` SET `logintime` = '".$logintime."', `ip_login` = '".$ip_login."', `loginexpires` = '".$valid_until."', `session` = '".$_SESSION['session']."' WHERE `".config::get('database')['auth_table']."`.`username` = '".$username."' AND `".config::get('database')['auth_table']."`.`password` = '".$password."';");
	
		return $valid_until;
	}
	
	/*
	 * get $session
	* $session hash is md5($username.$password.$salt)
	* returns timestamp until when the session is valid
	*/
	public function GetSessionExpiration($session, $user) {
		$valid_until = null;
		// check if an user object was handed over
		if (! $user) {
			// no user object was handed over -> get user
			$user = GetUserBySession ( $session );
		}
	
		if ($user) {
			// hash found
			$valid_until = $user->loginexpires;
		}
	
		return $valid_until;
	}
	
	/* get user by session
	 */
	public function GetUserBySession($session)
	{
		mysqli_interface::set('worked',false);
		$output = "";
		$result = "";

		if($session)
		{
			$valid_until = null;
			$user_array = config::get('mysqli_object')->query("SELECT * FROM `".config::get('database')['auth_table']."` WHERE `session` = '".$session."'");
			// $user = config::get('mysqli_object')->query("SELECT * FROM `".config::get('database')['name']."`.`".config::get('database')['auth_table']."` WHERE `session` = '".$session."'");
			if(isset($user_array[0]))
			{
				// hash found
				$result = $user_array[0];
			}
		}
	
		if(!empty($result)) mysqli_interface::set('worked',true);
	
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
	* $user = NewUser();
	* $user->username = "joe";
	* UserDel($user,"username");
	* */
	public function UserDel($user,$identifyByKey = "id")
	{
		if(!is_object($user))
		{
			return error("function UserDel: expected input \$user to be an object");
		}
		mysqli_interface::set('worked',false);
		$output = "";
		mysqli_interface::set('worked',false);
	
		if(haspropertyandvalue($user,$identifyByKey,"UserDel"))
		{
			$output = config::get('mysqli_object')->query("DELETE FROM  `".config::get('database')['name']."`.`".config::get('database')['auth_table']."` WHERE `".config::get('database')['auth_table']."`.`".$identifyByKey."` = '".$user->$identifyByKey."';");
			mysqli_interface::set('worked',true);
		}
	
		return mysqli_interface::get('worked');
	}
	
	/* add/register a new user
	 *
	* the properties a $user-array-object can have is defined through the database
	* (table defined in config/config.php -> config::get('database')['auth_table'] e.g. passwd)
	*
	* add a column there, and you have a new property attached to $user.
	*
	* To create/add a $user you first need to get this database-defined-layout
	*
	* $user = NewUser();
	*
	* Then you modify the array: username is required, anything else is optional.
	*
	* $user->username= "user";
	*
	* adduser($user);
	*
	* That's it!
	* */
	public function UserAdd($user) // $requested_username = "",$requested_password = "",$groups = "",$data = ""
	{
		mysqli_interface::set('worked',false);
		$output = "";
	
		if(!haspropertyandvalue($user, "username", "UserAdd"))
		{
			mysqli_interface::set('worked',false);
			return mysqli_interface::get('worked');
		}
	
		// check if user allready exists
		if(config::get('uniqueUsernames'))
		{
			if($this->UserExist($user,"username"))
			{
				return error("function UserAdd: can not continue, user ".$user->username." is taken and \config::get('uniqueUsernames') is set to true.");
			}
		}
	
		// under linux, when creating users there is always a a group created with the same name, that per default this user belongs to (it's "his" group)
		// search for username in groups, if not found add.
		if(empty($user->home))
		{
			$user->home = config::get('default_home_after_login');
		}
	
		// Create a unique  activation code:
		$user->activation = md5(uniqid(rand(), true));
	
		// under linux, when creating users there is always a a group created with the same name, that per default this user belongs to (it's "his" group)
		// check if given groups already exist, if not add
		$group = $this->NewGroup();
		$group->groupname = $user->username;
	
		if(!$this->GroupExist($group))
		{
			$this->GroupAdd($group);
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
		$query = "INSERT INTO `".config::get('database')['name']."`.`".config::get('database')['auth_table']."` ".$values;
		
		
		config::get('database')['lastColumn'] = "";
	
		// return data = false, return errors = true
		$output = config::get('mysqli_object') -> query($query,false,true);
	
		// get the id of the just created user-object
		global $id_last;
		$user->id = $id_last;
	
		mysqli_interface::set('worked',true);
	
		return $user;
	}
	
	/* edit/update/change a user
	 * $groups = a,comma,separated,list,of,groupnames
	* arbitrary additional details data about the user
	* data -> $data = "key:value,key:value," */
	public function UserEdit($UpdatedUser,$uniqueKey = "id") // $userID, $requested_username = "",$requested_password = "",$groups = "",$data = ""
	{
		// check if user with this username allready exists -> warn
	
		mysqli_interface::set('worked',false);
		$output = "";
	
		// get all info about user
		$user_database = getFirstElementOfArray($this->users($UpdatedUser,$uniqueKey));
	
		// merge it
		$UpdatedUser = mergeObject($UpdatedUser,$user_database);
	
		// if config::get('uniqueUsernames') enabled -> check if username is allready in use/exists
		if(config::get('uniqueUsernames'))
		{
			if($user_database->username != $UpdatedUser->username)
			{
				if($this->UserExist($UpdatedUser,"username"))
				{
					return error("function UserEdit: can not rename username from ".$user_database->username." to ".$UpdatedUser->username." because the username is allready in use.");
				}
			}
		}
	
		$values = arrayobject2sqlvalues($UpdatedUser,"UPDATE");
	
		$query = "UPDATE `".config::get('database')['name']."`.`".config::get('database')['auth_table']."` SET ".$values." WHERE `".config::get('database')['auth_table']."`.`".$uniqueKey."` = '".$UpdatedUser->$uniqueKey."';";
		
		
		config::get('database')['lastColumn'] = $uniqueKey;
	
		$output = config::get('mysqli_object') -> query($query,false,true);
	
		return $output;
	}
	
	/* ============ GROUP */
	/* add a group to the system (list of available groups) / add/register a new group
	 *
	* $systemgroup = 1 -> this group is a system-group (like admin, guest... that can not/should not be deleted, even if there are no users anymore using it)
	*
	* the properties a group-array-object can have is defined through the database
	* (table defined in config/config.php -> config::get('database')['auth_table'] e.g. passwd)
	*
	* add a column there, and you have a new property attached to group.
	*
	* To create/add a $group you first need to get this database-defined-layout
	*
	* $user = $this->NewGroup();
	*
	* Then you modify the array: groupname is required, anything else is optional.
	*
	* $group->groupname= "group";
	*
	* addgroup($group);
	*
	* That's it!
	* */
	public function GroupAdd($group,$systemgroup = 0)
	{
		if(!haspropertyandvalue($group, "groupname", "GroupAdd"))
		{
			mysqli_interface::set('worked',false);
			return mysqli_interface::get('worked');
		}
	
		mysqli_interface::set('worked',false);
		$output = "";
	
		// under linux, when creating groups there is always a a group created with the same name, that per default this group belongs to (it's "his" group)
		// check if given groups already exist, if not add
		if(!$this->GroupExist($group,"groupname"))
		{
			// search for groupname in groups, if not found add.
			// allready contains groupname in group-list
			$group->id = ""; // id will always be automatically set by database/backend/autoincrement, or things will become chaotic
			$group->system = $systemgroup;
	
			$values = arrayobject2sqlvalues($group,"INSERT");
			$query = "INSERT INTO `".config::get('database')['name']."`.`".config::get('database')['groups_table']."` ".$values;
			
			
			config::get('database')['lastColumn'] = "";
	
			// return data = false, return errors = true
			$output = config::get('mysqli_object') -> query($query,false,true);
			// get the id of the just created group-object
			global $id_last;
			$group->id = $id_last;
	
			mysqli_interface::set('worked',true);
		}
		else
		{
			$output = "function GroupAdd: group allready exists.";
			return error($output);
		}
	
		return $group;
	}
	
	/* edit/update/change a group
	 * $groups = a,comma,separated,list,of,groupnames
	* arbitrary additional details data about the group
	* data -> $data = "key:value,key:value,"
	*/
	public function GroupEdit($UpdatedGroup,$uniqueKey = "id") // $groupID, $requested_groupname = "",$requested_password = "",$groups = "",$data = ""
	{
		// check if group with this groupname allready exists -> warn
	
		mysqli_interface::set('worked',false);
		$output = "";
	
		// get all info about group
		$group_database = $this->GroupGet($UpdatedGroup,$uniqueKey);
	
		// merge it
		$UpdatedGroup = mergeObject($UpdatedGroup,$group_database);
	
		// if $settings_uniqueGroupnames enabled -> check if groupname is allready in use/exists
		if($this->GroupExist($UpdatedGroup,"groupname"))
		{
			return error("function GroupEdit: can not rename group from ".$group_database->groupname." to ".$UpdatedGroup->groupname." because the groupname is allready in use.");
		}
	
		$values = arrayobject2sqlvalues($UpdatedGroup,"UPDATE");
	
		$query = "UPDATE `".config::get('database')['name']."`.`".config::get('database')['groups_table']."` SET ".$values." WHERE `".config::get('database')['groups_table']."`.`".$uniqueKey."` = '".$UpdatedGroup->$uniqueKey."';";
		
		
		config::get('database')['lastColumn'] = $uniqueKey;
	
		$output = config::get('mysqli_object') -> query($query,false,true);
	
		return $output;
	}
	
	/* get $group as assoc-array
	 * by id, if no $uniqueKey is given (could also be groupname,mail if those values are unique)
	*/
	public function GroupGet($group = null,$uniqueKey = "id")
	{
		$result = null;
		mysqli_interface::set('worked',false);
		$output = "";
		$query = "";

		if(!is_null($group))
		{
			if(haspropertyandvalue($group,$uniqueKey,"GroupGet"))
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
				$query = "SELECT * FROM `".config::get('database')['groups_table']."` WHERE `".$uniqueKey."` = '".$group_string."'";
				
				
				config::get('database')['lastColumn'] = $uniqueKey;
			}
		}
		else
		{
			// return all groups
			$query = "SELECT * FROM `".config::get('database')['groups_table']."`";
			
			
			config::get('database')['lastColumn'] = "";
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
	
		if(!empty($result)) mysqli_interface::set('worked',true);
	
		return $result;
	}
	
	/* delete a group */
	public function GroupDel($group,$identifyByKey = "id")
	{
		if(is_string($group))
		{
			$group_object = $this->NewGroup();
			$group_object->$identifyByKey = $group;
			$group = $group_object;
		}
	
		if(!is_object($group))
		{
			return error("function GroupDel: expected input \$group to be an object");
		}
		mysqli_interface::set('worked',false);
		$output = "";
	
		if(haspropertyandvalue($group,$identifyByKey,"GroupDel"))
		{
			// check out if there are still users in this group -> refuse to delete
			$users = $this->users();
	
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
				error("function GroupDel: can not delete group with name: ".$groupname." - the group is still in use by group ".$groupname);
				mysqli_interface::set('worked',false);
				return mysqli_interface::get('worked');
			}
			else
			{
				$query = "DELETE FROM `".config::get('database')['name']."`.`".config::get('database')['groups_table']."` WHERE `".config::get('database')['groups_table']."`.`".$identifyByKey."` = '".$group->$identifyByKey."';";
				
				
				config::get('database')['lastColumn'] = $identifyByKey;
	
				$result = config::get('mysqli_object') -> query($query,false,true);
			}
		}
	
		return mysqli_interface::get('worked');
	}
	
	
	/* get a list of all available groups
	 * $option = as array
	* $option = as object
	*/
	public function GetGroups($option = "as object")
	{
		$result = null;
		mysqli_interface::set('worked',false);
		$output = "";
		$result = config::get('mysqli_object')->query("SELECT * FROM `".config::get('database')['groups_table']."`");
	
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
	public function GroupExist($group,$uniqueKey = "id")
	{
		if(is_string($group))
		{
			$group_object = $this->NewGroup();
			$group_object->$uniqueKey = $group;
			$group = $group_object;
		}
		if(!haspropertyandvalue($group, $uniqueKey, "GroupExist"))
		{
			mysqli_interface::set('worked',false);
			return mysqli_interface::get('worked');
		}
	
		config::set('result', false); // default result value
		mysqli_interface::set('worked',false);
		$output = "";
	
		$query = "SELECT * FROM `".config::get('database')['groups_table']."` WHERE `".$uniqueKey."` = '".$group->$uniqueKey."'";
		
		
		config::get('database')['lastColumn'] = $uniqueKey;
	
		$result_array = config::get('mysqli_object')->query($query);
	
		if($result_array)
		{
			config::set('result', true);
		}
	
		return config::get('result');
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
	$users = $this->users();
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
	$groups_of_element = getGetGroupsOfUser($user);
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
	<a href="frontend_UserEdit.php?selectUserId='.$user->id.'" rel="external" data-ajax="false">
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
	public function GetGroupsOfUser($user = null,$result_mode = "objects")
	{
		$result = Array();
		$query = "";
		mysqli_interface::set('worked',false);
		$output = "";
	
		$users = $this->users($user); // in case we got no $user->groups info
	
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
						$query = $query . "SELECT * FROM `".config::get('database')['groups_table']."` WHERE `groupname` = '".$group."'";
					}
					else
					{
						$query = $query . "UNION SELECT * FROM `".config::get('database')['groups_table']."` WHERE `groupname` = '".$group."'";
					}
					
					
					config::get('database')['lastColumn'] = "groupname";
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
	
	/* GroupAddUser - add user to a group */
	public function GroupAddUser($user,$group)
	{
		$user = $this->users($user); // get groups from database
	
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
	
		return $this->UserEdit($user);
	}
	
	/* GroupDelUser - add user to a group */
	public function GroupDelUser($user,$group)
	{
		$user = $this->users($user); // get groups from database
	
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
	
		return $this->UserEdit($user);
	}
	
	/* add/register a new record
	 *
	* the properties a $record-array-object can have is defined through the database
	* (table defined in config/config.php -> config::get('database')['auth_table'] e.g. passwd)
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
	public function RecordAdd($record,$table = null) // $requested_recordname = "",$requested_password = "",$groups = "",$data = ""
	{
		/* -----defaults------ */
		mysqli_interface::set('worked',false);
		$output = "";
	
		if(is_null($table))
		{
			$table = config::get('database')['lastTable'];
		}
		else
		{
			config::set('database')['lastTable'] = $table;
		}

		$query = "";
		/* -----defaults-end----- */
	
		$record->id = ""; // id will always be automatically set by database/backend/autoincrement, or things will become chaotic
	
		$values = arrayobject2sqlvalues($record,"INSERT");
		$query = "INSERT INTO `".config::get('database')['name']."`.`".$table."` ".$values;
		config::get('database')['lastColumn'] = "";
	
		// return data = false, return errors = true
		$output = config::get('mysqli_object') -> query($query,false,true);
	
		// get the id of the just created record-object
		global $id_last;
		$record->id = $id_last;
	
		mysqli_interface::set('worked',true);
	
		return $record;
	}
	
	/* edit/update/change a record
	 */
	public function RecordEdit($UpdatedRecord,$uniqueKey = "id",$table = null)
	{
		/* -----defaults------ */
		mysqli_interface::set('worked',false);
		$output = "";
	
		if(is_null($table))
		{
			$table = config::get('database')['lastTable'];
		}
		else
		{
			config::set('database')['lastTable'] = $table;
		}

		$query = "";
		/* -----defaults-end----- */
	
		// get all info about record
		$record_database = $this->records($UpdatedRecord,$uniqueKey);
	
		// merge it
		$UpdatedRecord = mergeObject($UpdatedRecord,$record_database);
	
		$values = arrayobject2sqlvalues($UpdatedRecord,"UPDATE");
		$query = "UPDATE `".config::get('database')['name']."`.`".$table."` SET ".$values." WHERE `".$table."`.`".$uniqueKey."` = '".$UpdatedRecord->$uniqueKey."';";
		config::get('database')['lastColumn'] = "";
	
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
	* RecordDel($record,"recordname");
	* */
	public function RecordDel($record,$identifyByKey = "id",$table = null)
	{
		if(!is_object($record))
		{
			return error("function RecordDel: expected input \$record to be an object");
		}
	
		/* -----defaults------ */
		$query = "";
		mysqli_interface::set('worked',false);
		$output = "";
	
		if(is_null($table))
		{
			$table = config::get('database')['lastTable'];
		}
		else
		{
			config::set('database')['lastTable'] = $table;
		}

		$query = "";
		/* -----defaults-end----- */
	
		if(haspropertyandvalue($record,$identifyByKey,"RecordDel"))
		{
			$query = "DELETE FROM  `".config::get('database')['name']."`.`".$table."` WHERE `".$table."`.`".$identifyByKey."` = '".$record->$identifyByKey."';";
			$output = config::get('mysqli_object')->query($query);
			mysqli_interface::set('worked',true);
		}
	
		return mysqli_interface::get('worked');
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
		mysqli_interface::set('worked',false);
		$output = "";
	
		if(is_null($table))
		{
			$table = config::get('database')['lastTable'];
		}
		else
		{
			config::set('database')['lastTable'] = $table;
		}
		
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
				config::get('database')['lastColumn'] = $uniqueKey;
			}
		}
		else
		{
			if(empty($where))
			{
				// return all records
				$query = "SELECT * FROM `".$table."`";
				config::get('database')['lastColumn'] = "";
			}
			else
			{
				$query = "SELECT * FROM `".$table."` ".$where;
				config::get('database')['lastColumn'] = "";
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
	
		if(!empty($result)) mysqli_interface::set('worked',true);
	
		return $result;
	}
	/*
	
	/* load sql-commands from a sql file */
	public function LoadSQLFromFile($url)
	{
		// ini_set ( 'memory_limit', '512M' );
		// set_time_limit ( 0 );
	
		mysqli_interface::set('worked',false);
		$output = "";
		$sql_query = "";
	
		$path = getcwd();
		
		// read line by line
		if(!file_exists ($url))
		{
			mysqli_interface::set('worked',false);
			trigger_error ( basename ( __FILE__, '.php' ) . "-> file ".$url.", does not exist.", E_USER_ERROR );
		}
		else
		{
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
		}
			
		return mysqli_interface::get('worked');
	}
	
	/* check if a given database exists */
	public function DatabaseExists($databaseName)
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
				mysqli_interface::set('result',true);
				break;
			}
		}

		return mysqli_interface::get('result');
	}
	
	/* check if a given table exists */
	public function TableExists($databasename,$tablename)
	{
		mysqli_interface::set('output',array()); // contains message to client e.g. the last success/error message
		mysqli_interface::set('result', false); // result / data returned of last query

		$query = "SHOW TABLES FROM `".$databasename."`;";
		$AllTablesOfDatabase = mysqli_interface::query($query);
	
		$target = count($AllTablesOfDatabase);
		
		if($target == 0)
		{
			// zero tables in database
			mysqli_interface::set('result', false);
		}
		else
		{
			for($i=0;$i<$target;$i++)
			{
				if($tablename == $AllTablesOfDatabase[$i]->Table)
				{
					mysqli_interface::set('result', true);
					break;
				}
			}
		}

		return mysqli_interface::get('result');
	}
}

/*
 * this file handles all sorts of user-database-operations, it can not be called directly via url?parameter=evil
* so it does not need all the ./lib/php/lib_session.php/./lib/php/lib_security.php, but the parent.php does!
*/

?>