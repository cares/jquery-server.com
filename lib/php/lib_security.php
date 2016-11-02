<?php
/*
* CONCEPT:
* after user login, backend creates cookie with a random session, that only user-browser and server know about.
* unless user visits other sites that read/steal the cookie.
* 
* PREVENT COOKIE STEALING: 
* the attacker (and attacker's website) not only need to steal cookie but also need to change sender-IP.
* the IP is logged (passwd -> ip_login) during login process.
* 
* PASSWORDS:
* all passwords are md5 hashed on client side and never submitted clear-text.
* md5 is a 'cleartext'-one-way->'5f4dcc3b5aa765d61d8327deb882cf99' encryption
* that was not broken yet.
* except: if you use a simple dictionary based password.
*
* SO ONLY USE AT LEAST 8 CHARS CONSISTING OF ALPHABET, DIGITS AND SPECIAL CHARS LIKE !&()
* OR YOUR ACCOUNT IS NOT SAFE!
* 
* SQL INJECTION:
* per default all incoming arguments are mysql-real-escaped, so this should prevent an sql injection input form.
 */

// include lib_general.php, should be on top of every file
if(file_exists('lib_general.php')) { require_once('lib_general.php'); } else { if(file_exists('./lib/php/lib_general.php')) { require_once('./lib/php/lib_general.php'); } else { trigger_error(basename(__FILE__, '.php')."-> could not include library lib_general.php, it should be on top of every file.php", E_USER_ERROR); }}

if(!class_exists("mysqli_interface"))
{
	include_missing_lib("lib_mysqli_interface.php");
}

// init database
$mysqli_interface_instance = new mysqli_interface(); # create instance from class
config::set('mysqli_object',$mysqli_interface_instance);

// escape everything
foreach ($_REQUEST as $key => $value)
{
	$_REQUEST[$key]= config::get('mysqli_object')->escape($value);
}	
?>