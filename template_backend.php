<?php
/* ================= please put this on top of every page */
error_reporting(E_ALL); // turn the reporting of php errors on
$allowed_users = "1,2,3,4,5,"; // a list of userIDs that are allowed to access this page
require_once("config/config.php");
// require_once('./lib/php/lib_session.php'); // will immediately exit and redirect to login if the session is not valid/has expired/user is not allowed to access the page
/* ================= */
?>