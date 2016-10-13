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
	
	public static function Get( $key ) {
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
	* true means: parse the mysql-result and return all data that is there (select = read)
	* false means: i do not expect any data to be returned (insert/update does not read any data)
	*/
	public static function query($query)
	{
 		/* in general:
 		yes PHPs interaction with MySQL and any other database is pretty complicated and one tries to simplify, but it's still complicated */
		
		// reset to defaults for every query
		mysqli_interface::set('result',null);		// -> mysql-result-pointer, pointing to RAW mysql result of last query, no post-processing (sometimes you can not work directly with that), can be any type
		mysqli_interface::set('output',null);		// -> data extracted from RAW mysql result, "the result" ready for further processing, can be any type
		mysqli_interface::set('feedback','');		// -> contains message to client e.g. the last detailed success/error message, it is structured like this: "type:error,id:unique_id_of_feedback_message,details:"." Selecting database failed: ".mysqli_connect_error() so the JavaScript-client can display it
		// id:unique_id_of_feedback_message -> you could have error messages translated into different languages, but i guess that is a lot of work and it is more important to focus on precise error messages that actually help debug the problem. Most programmers should know some english.
		mysqli_interface::set('worked',false);		// -> this is the status of the last query possible values are true (worked) false (failed, mysql error will be thrown)
		mysqli_interface::set('last_id','');		// -> if there was an insert, return the auto-generated id of the record inserted.

		$records = Array();							// -> temporary storage of result or output

		$mysqli_link = mysqli_interface::get('mysqli_link');
		// mysqli_interface::get('mysqli_object');

		// try to detect a CREATE DATABASE
		$detectCreateDatabase = substr($query, 0, 15);
		if("CREATE DATABASE" == $detectCreateDatabase)
		{
			// yes -> there is no database to select
			mysqli_interface::set('result',true);
		}
		else
		{
			// no -> select database defined/set in config.php before applying the query
			mysqli_interface::set('result',$mysqli_link->select_db(config::get("db_name")));
		}

		if(!mysqli_interface::get('result'))
		{
			// could not select database, something went wrong
			mysqli_interface::set('worked',false); // status, if the last query was successfull (true) or failed (false)
			mysqli_interface::set('feedback',"type:error,id:select_db failed,details:"." Selecting database failed, does database ".config::get("db_name")." exist?: ".mysqli_connect_error());
			trigger_error(mysqli_interface::get('feedback'));
		}
		else
		{
			// 2. execute query, check for query errors
			mysqli_interface::set('result',mysqli_query($mysqli_link,$query));
			$last_id = mysqli_insert_id($mysqli_link);

			// was the query successful?
			if(!mysqli_interface::get('result'))
			{
				// no
				$error = $query." returns error: ".mysqli_errno($mysqli_link). ": ".mysqli_error($mysqli_link);
				$error = str_replace(",", " ", $error);
				$error = str_replace(":", " ", $error);
				
				// just for correct decoding of the message on client side, remove all possible delimiters from 'datasource' (mysql)
				$temp = config::get("db_datasource");
				$temp = str_replace(",", " ", $temp);
				$temp = str_replace(":", " ", $temp);

				// mysqli_interface::set('result',null);	// -> mysql-result-pointer, pointing to RAW mysql result of last query, no post-processing (sometimes you can not work directly with that), can be any type
				mysqli_interface::set('feedback','type:error,id:database error,details:'.$error.',datasource:'.$temp); // -> contains message to client e.g. the last detailed success/error message.
				mysqli_interface::set('worked',false);		// -> this is the status of the last query possible values are true (worked) false (failed, mysql error will be thrown)
				mysqli_interface::set('last_id',$last_id);	// -> if there was an insert, return the auto-generated id of the record inserted.

				trigger_error(mysqli_interface::get('feedback')); // send error to output
			}
			else
			{
				// yes
				// try to detect if query is returning data (DROP pr CREATE DATABASE will not return any data, neither will any UPDATE command)
				if(!is_bool(mysqli_interface::get('result'))) // query(UPDATE) = returns no data just true/false (status if sql-command worked or not)
				{
					// while ($record = mysqli_interface::get('result')->fetch_object()) // probably the same
					while ($record = mysqli_fetch_object(mysqli_interface::get('result')))
					{
						$records[] = $record;
					}
					/*
						mysqli_result::free -- mysqli_free_result — Frees the memory associated with a result
						You should always free your result with mysqli_free_result(),
						when your result object is not needed anymore.
						
						http://php.net/manual/de/mysqli-result.free.php
					*/
					mysqli_interface::set('output',$records);	// -> data extracted from RAW mysql result, "the result" ready for further processing, can be any type
					mysqli_free_result(mysqli_interface::get('result'));
				}

				// mysqli_interface::set('result',null);	// -> mysql-result-pointer, pointing to RAW mysql result of last query, no post-processing (sometimes you can not work directly with that), can be any type
				mysqli_interface::set('feedback','type:success,id:query_successful,details: database query ran without errors');// -> contains message to client e.g. the last detailed success/error message.
				mysqli_interface::set('worked',true);		// -> this is the status of the last query possible values are true (worked) false (failed, mysql error will be thrown)
				mysqli_interface::set('last_id',$last_id);	// -> if there was an insert, return the auto-generated id of the record inserted.
			}
		}

		return $records; // return mysql data records
	}

	/* filter evil characters that could make mysql stumble or return a file that contains the whole database
	 *
	* CAN ONLY BE USED IF THERE IS A DATABASE CONNECTION/LINK OPEN!
	*
	* do it like this:
	// init database
	config::get('mysqli_object') = new mysql("".config::get("db_name")."");

	* */
	function escape($input)
	{
		$mysqli_link = mysqli_interface::get('mysqli_link');
		return mysqli_escape_string($mysqli_link,$input);
	}
}

/* in general: yes PHPs interaction with MySQL and any other database is pretty complicated and one tries to simplify, but it's still complicated */
mysqli_interface::set('result',null);		// -> mysql-result-pointer, pointing to RAW mysql result of last query, no post-processing (sometimes you can not work directly with that), can be any type
mysqli_interface::set('output',null);		// -> data extracted from RAW mysql result, "the result" ready for further processing, can be any type
mysqli_interface::set('feedback','');		// -> contains message to client e.g. the last detailed success/error message.
mysqli_interface::set('worked',false);		// -> this is the status of the last query possible values are true (worked) false (failed, mysql error will be thrown)
mysqli_interface::set('last_id','');		// -> if there was an insert, return the auto-generated id of the record inserted.

mysqli_interface::set('mysqli_link', null); // a pointer symbolizing the connection to the mysql database, set during construction
mysqli_interface::set('mysqli_object', null); // this class, which contains functions and objects such as mysqli_interface::get('mysqli_link')

mysqli_interface::set('lastDatabase', '');  // remember database last in use
mysqli_interface::set('errors', '');

/* init mysql object */
$mysqli_link = mysqli_connect(config::get("db_srv_address"), config::get("db_user"), config::get("db_pass"), config::get("db_datasource"));
mysqli_interface::set('mysqli_link', $mysqli_link); // save for later reuse
$mysqli_link->set_charset(config::get("db_charset"));

if (!$mysqli_link)
{
	// something went wrong, find out what and send back details to jquery-ajax-request
	$error_details = mysqli_connect_errno().":".mysqli_connect_error();
	mysqli_interface::set('feedback','type:error,id:mysqli_connect failed,details:'.$error_details);
	exit(mysqli_interface::get('feedback'));
}
?>