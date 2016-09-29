<?php
/* TODO:
 * do not use GLOBAL variables to store MySQL database & username credentials... possible security flaw.
*
* 		// also test: $this->translation_type = "database"; // specify path and filename (have a look at translations.php) or "database" which means, the translations for all texts will be stored in database
* */

class config {

    private static $config = array();

    public static function set( $key, $value ) {
        self::$config[$key] = $value;
    }

    public static function Get( $key ) {
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
config::set('platform_name', 'jqueryserver.com');
config::set('platform_logo', 'images/projectlogo.png');
config::set('platform_url', 'http://jqueryserver.com');
config::set('log_errors', 'log.errors.txt'); // if empty, no errors are logged to file
config::set('log_operations', 'log.operations.txt'); // if empty, no errors are logged to file
config::set('uniqueUsernames', true); // please leave this at true, otherwise UserEdit may fail, true: two users can NOT have the same username, false: two users can have the same username (identification is mainly done over an unique database generated id)

/* ======================= DEVELOPMENT */
config::set('debugMode', true); // if you want additional info about whats going on. will also perserve xdebug ?Session parameters.

config::set('debugMode', true); // if you want additional info about whats going on. will also perserve xdebug ?Session parameters.

/* ======================= DATABASE */
config::set('database', array(
		"datasource" => "mysql", // right now can only be "mysql", could be postgress (not implemented) sqlite (not implemented)
		"server" => "localhost",
		"charset" => "utf8", // if you want special chars to be properly displayed in the database/phpmyadmin etc.
		"name" => config::get('platform_name'),
		"user" => "root",
		"pass" => "root",
		"auth_table" => "passwd", // what the table is called, where the users & passwords (md5 hashes) are stored
		"groups_table" => "groups", // what the table is called, where the groups are stored
		"lastDatabase" => "",
		"lastTable" => ""
));

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
config::set('upload_maximumFileSize', 2048);

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