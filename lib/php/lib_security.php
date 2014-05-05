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

if(!class_exists("./lib/php/class_mysqli_interface"))
{
	require_once('./lib/php/lib_mysqli_interface.php');
}

// init database
$mysqli_object = new class_mysqli_interface();

// escape everything
foreach ($_REQUEST as $key => $value)
{
	$_REQUEST[$key]= $mysqli_object->escape($value);
}	
?>