<?php
/* is username still available? / unregistered? */
/* ================= */
error_reporting(E_ALL); // turn the reporting of php errors on
$allowed_users = "*"; // a list of userids that are allowed to access this page
$allowed_groups = "*"; // a list of groups, that are allowed to access this page
require_once('./lib/php/lib_security.php'); // will mysql-real-escape all input
require_once('./lib/php/lib_session.php'); // will immediately exit and redirect to login if the session is not valid/has expired

// is it a username-taken test?
/* ================= */

if(isset($_REQUEST['username']) && (!empty($_REQUEST['username']))) // if parameter is given and not empty
{
	if(userexist($_REQUEST['username'],null,null))
	{
		exit('type:error,id:Username already taken,details:Username already taken. Please choose different one.');
	}
	else
	{
		exit('type:success,id:username available,details:checked with server and username is still available. please continue with registration.');
	}
}
?>