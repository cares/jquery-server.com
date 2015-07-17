<?php
/* mysqli.php
 * 1. loads config.php (database credentials) per default
* 2. establishes a link to the mysql database
* 3. selects a default database (the one given in config/config.php)
* 4. handles all mysql interaction, databaseresults are returned as Array.
*
* return each record as as key=>value element of an array (where key is column-name, and value is corresponding value)

"$output" =
0 = Array
ID = 2
username = test
password = password
name = name
email = email

1 = Array
2 = Array
...

... if you want to replace mysql with postgress,
there needs to be a postgress.php which similar structure than this one.

just for info: this is how flash player / AIR apps needs it's xml data output
$output = "<?xml version=\"1.0\" encoding=\"utf-8\"?><sqlxml>";
$output .= " <database_connection>0</database_connection>\n";
$output .= " <error>".utf8_encode(mysqli_error())." </error>\n";
*/
if (file_exists('config.php')) {
	require_once 'config.php'; // file was not called directly
} else {
	require_once '../../config.php'; // file called directly
}

class mysqli_interface extends config {

	function __construct()
	{
	}
	
	private static $mysqli_interface = array();
	
	public static function set( $key, $value ) {
		self::$mysqli_interface[$key] = $value;
	}
	
	public static function get( $key ) {
		if( mysqli_interface::isKeySet( $key ) ) {
			return isset( self::$mysqli_interface[$key] ) ? self::$mysqli_interface[$key] : null;
		}
		else
		{
			trigger_error ( "key: ".$key." does not exist in config.php");
		}
	}
	
	public static function setAll( array $array ) {
		self::$mysqli_interface = $array;
	}
	
	public static function isKeySet( $key ) {
		return isset( self::$mysqli_interface[ $key ] );
	}
	
	/* send query to database and return each element as key=>value array
	 *
	* "$output" = Array [3]
	0 = Array [5]
	ID = 2
	username = test
	password = password
	name = name
	email = email

	1 = Array [5]
	2 = Array [5]
	...
	*
	* $return_data -> true or false
	* true means: parse the mysql-result and return all data that is there (select = read)
	* false means: i do not expect any data to be returned (insert/update does not read any data)
	*/
	public static function query($query,$return_data = true)
	{
		mysqli_interface::set('output',array());
		mysqli_interface::set('worked',false);
		mysqli_interface::set('id_last','');
		$mysqli_link = mysqli_interface::get('mysqli_link');
		mysqli_interface::get('mysqli_object');
		config::get('database')['name']['server'];
		config::get('database')['name']['user'];
		config::get('database')['name']['pass'];
		config::get('database')['name']['datasource'];

		$detectCreateDatabase = substr($query, 0, 15);
		if("CREATE DATABASE" == $detectCreateDatabase)
		{
			$result = true;
		}
		else
		{
			$result = $mysqli_link->select_db(config::get('database')['name']);
		}

		if(!$result)
		{
			// could not select database, something went wrong
			$worked = false;
			mysqli_interface::set('worked', false); // status, if the last query was successfull (true) or failed (false)

			// check if database exists
			$query = "SHOW DATABASES;";
			$result = mysqli_query($query);
			// $id_last = mysqli_insert_id($mysqli_link);

			$output = "type:error,id:select_db failed,details:"." Selecting database failed: ".mysqli_connect_error();
			trigger_error($output);
		}
		else
		{
			// 2. execute query, check for query errors
			$result = mysqli_query($mysqli_link,$query);
			mysqli_interface::set('result',$result);
			$id_last = mysqli_insert_id($mysqli_link);
			mysqli_interface::set('id_last',$id_last);

			if(!$result)
			{
				$error = $query." returns error: ".mysqli_errno($mysqli_link). ": ".mysqli_error($mysqli_link);
				$error = str_replace(",", " ", $error);
				$error = str_replace(":", " ", $error);
				$settings_datasource = str_replace(",", " ", $settings_datasource);
				$settings_datasource = str_replace(":", " ", $settings_datasource);

				$output = 'type:error,id:database error,details:'.$error.',datasource:'.$settings_datasource;
				mysqli_interface::set('output',$output);
				trigger_error($output);
			}

			if($return_data)
			{
				$worked = true;
				if(!is_bool($result)) // query(UPDATE) = returns true/false
				{
					while ($obj = $result->fetch_object()) {
						$output[] = $obj;
					}
					mysqli_free_result($result);
				}
			}
		}

		// save
		
		mysqli_interface::set('output',$output); // contains message to client e.g. the last success/error message
		mysqli_interface::set('worked', $worked); // status, if the last query was successfull (true) or failed (false)
		mysqli_interface::set('result', $result); // result / data returned of last query
		
		return $output; // return
	}

	/* filter evil characters that could make mysql stumble or return a file that contains the whole database
	 *
	* CAN ONLY BE USED IF THERE IS A DATABASE CONNECTION/LINK OPEN!
	*
	* do it like this:
	// init database
	config::get('mysqli_object') = new mysql("".config::get('database')['name']."");

	* */
	function escape($input)
	{
		return mysqli_escape_string($mysqli_link,$input);
	}
}

/* this stuff is added here */
mysqli_interface::set('output',array()); // contains message to client e.g. the last success/error message
mysqli_interface::set('worked', false); // status, if the last query was successfull (true) or failed (false)
mysqli_interface::set('result', false); // result / data returned of last query

mysqli_interface::set('id_last', ''); // the auto-increment id of the last record inserted
mysqli_interface::set('mysqli_link', null); // a pointer symbolizing the connection to the mysql database, set during construction
mysqli_interface::set('mysqli_object', null); // this class, which contains functions and objects such as mysqli_interface::get('mysqli_link')

mysqli_interface::set('lastDatabase', '');  // remember database last in use
mysqli_interface::set('errors', '');

/* init mysql object */
$config_database = config::get('database');

$mysqli_link = mysqli_connect($config_database["server"], $config_database["user"], $config_database["pass"], $config_database['name']);
mysqli_interface::set('mysqli_link', $mysqli_link); // save for later reuse
$mysqli_link->set_charset($config_database["charset"]);

if (!$mysqli_link)
{
	// something went wrong, find out what and send back details to jquery-ajax-request
	$error_details = mysqli_connect_errno().":".mysqli_connect_error();
	mysqli_interface::set('output','type:error,id:mysqli_connect failed,details:'.$error_details);
	exit(mysqli_interface::get('output'));
}
else
{
	mysqli_interface::set( 'lastDatabase', $config_database["name"]);  // contains message to client e.g. the last success/error message
}


?>