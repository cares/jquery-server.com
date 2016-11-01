<?php
/* TODO:
 * do not use GLOBAL variables to store MySQL database & username credentials... possible security flaw.
 *
 * also test: $this->translation_type = "database"; // specify path and filename (have a look at translations.php) or "database" which means, the translations for all texts will be stored in database
 **/

class config {
	
	function __construct()
	{
		// nothing here
	}

    private static $config = array();

    public static function set( $key, $value ) {
        self::$config[$key] = $value;
    }

    public static function get( $key ) {
    	if( config::isKeySet( $key ) ) {
        	return isset( self::$config[$key] ) ? self::$config[$key] : null;
    	}
    	else
    	{
    		trigger_error ( "key: ".$key." does not exist in config.php");
    	}
    }

    public static function setAll( array $array ) {
        self::$config = $array;
    }

    public static function isKeySet( $key ) {
        return isset( self::$config[ $key ] );
    }
}

// set valuable values

/* ======================= ABOUT THE PLATFORM */
config::set('platform_name'		, 'jqueryserver.com');		# name of the platform (may appear in title="" tag
config::set('platform_logo'		, 'images/projectlogo.png');# logo of platform
config::set('platform_url'		, 'http://jqueryserver.com'); # base-url of platform
config::set('log_errors'		, './log/errors_platform.log'); # put empty string here if you do not want errors to get logged to file
config::set('log_operations'	, '');						# leave empty string here if you do not want database operations to be logged, per default only errors are logged. you could put log.operations.txt here
config::set('uniqueUsernames'	, true);					# please leave this at true, otherwise UserEdit may fail, true: two users can NOT have the same username, false: two users can have the same username (identification is mainly done over an unique database generated id)

/* ======================= DEVELOPMENT */
config::set('debugMode',		true);						# if you want additional info about whats going on. will also perserve xdebug ?Session parameters.

/* ======================= DATABASE */
config::set("db_srv_address","localhost");				# address of database server
config::set("db_datasource","mysql");					# right now can only be "mysql", could be postgress (not implemented) sqlite (not implemented)
config::set("db_name",config::get('platform_name'));	# the database one will deal with, for conveniance same name as platform
config::set("db_charset","utf8");						# if you want special chars to be properly displayed in the database/phpmyadmin etc.
config::set("db_user","root");							# what database user to use for accessing the database
config::set("db_pass","root");							# what database password to use for accessing the database
config::set("db_auth_table","passwd"); 					# name of table where platform's usernames & passwords (md5 hashed) are stored (passwd)
config::set("db_groups_table","groups");				# what the table is called, where the groups are stored (groups)

// will be reset to defaults before every query of database
config::set("db_result",null);							# -> mysql-result-pointer, pointing to RAW mysql result of last query, no post-processing (sometimes you can not work directly with that), can be any type
config::set("db_output",null);							# -> data extracted from RAW mysql result, "the result" ready for further processing, can be any type
config::set('db_log_errors', './log/errors_db.log');		# put empty string here if you do not want database query errors to be logged
config::set("feedback","");								# -> contains message to client e.g. the last detailed success/error message, it is structured like this: "type:error,id:unique_id_of_feedback_message,details:"." Selecting database failed: ".mysqli_connect_error() so the JavaScript-client can display it
// id:unique_id_of_feedback_message -> you could have error messages translated into different languages, but i guess that is a lot of work and it is more important to focus on precise error messages that actually help debug the problem. Most programmers should know some english.
config::set("db_worked",false);							# -> this is the status of the last query possible values are true (worked) false (failed, mysql error will be thrown)
config::set("db_last_id",'');							# -> if there was an insert, return the auto-generated id of the record inserted.

/* will hold the link to mysql, as soon as it is initialized like this:
 * 
 * config::set('database')['name'] = "MyDataBaseName"; // overwrite settings from config.php
 * $lib_mysqli_commands_instance = new lib_mysqli_commands(); # create instance from class
 * */ 
config::set('mysqli_object',new stdClass());

/* set all config in one big array-go (overwriting the config::set('key','value'); lines
config::setAll( array(
'overwrite' => 'everything',
'database' => array('a','b','c')
					 )
              );
*/
/* ======================= USERS */
/* ================ DEFAULTS */

// config::get('default_home_after_login') = "frontend_template.php"; // redirect all users, that have no home:somefile.php set in data field of passwd table, to this file after login
config::set('default_home_after_login', "manage.users.php"); // redirect all users, that have no home:somefile.php set in data field of passwd table, to this file after login
config::set('translations_source', "./lang.translations.php"); // specify path and filename (have a look at translations.php) or "database" which means, the translations for all texts will be stored in database
// also test: $this->translation_type = "database"; // specify path and filename (have a look at translations.php) or "database" which means, the translations for all texts will be stored in database

/* ======================= UPLOADS */
/* ================ GENERAL */
config::set('upload_allowedExtensions', array("gif", "jpeg", "jpg", "png"));
config::set('upload_maximumFileSize', 4096);				# in KBytes, per default 4MByte maximum file size for upload

/* ================ PROFILE PICTURES */
config::set('profilepicture_upload_dir', "images/profilepictures/");
config::set('profilepicture_dimensions', "115x115"); // what resolution do you allow for profile pictures

/* ======================= WHO IS THE ADMIN? WHO IS RESPONSIBLE? */
config::set('mail_admin', "admin@server.org"); // where notification go
config::set('login_session_timeout', "1800"); // 1800seconds = 30min, 0 = no timeout, amounts of seconds that login-cookies are valid, after login (time until user has to re-login)

/* ======================= SINGLE/MULTIPLE PROJECTS? */
/*
 * personally i use /var/www/projectname as pdt/eclipse/aptana workspace
*
* 1x project: if you are only hosting one project: than you probably have this project structure
* /var/www/index.php
* /var/www/frontend_login.php
* ...
* /var/www/library <- library is located here
*
* multiple projects with virtualhosts in subdirectories of web-root
* than your folder structure is probably like this:
* /var/www/projectnamX/
* /var/www/projectnamY/
* /var/www/library/ <- library is still located here, and not redundant in projectnameX/library and projectnameY/library
* so you need to update library in only one place
*/
config::set('multiple_projects', true);

// config::set('current_filename', "filename.js"); // js way to determine the current filename

$url = $_SERVER['PHP_SELF']; // php way to determine the current filename (fails sometimes?)
$filename_and_ending = explode('/', $url);
$filename_and_ending = $filename_and_ending[count($filename_and_ending) - 1];
$filename_and_ending = explode('.', $filename_and_ending);
config::set('current_filename', $filename_and_ending[0]); // automatically load filename.js

// if lang needs to be available in javascript, uncomment the following line and move it 2 lines up
// <script>var lang = "'.$settings_lang.'";</script>

/* o detect mobile browser, if yes -> load different css do not paint a lot of blue stuff around the UI */
// require_once('detectmobilebrowser.php');
// $settings_detected_browser = 'desktop'; // is detected automatically/overwritten automatically, possible values are desktop,


?>