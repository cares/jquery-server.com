<?php
/*
======== purpose of this file:
initialization
1. check if server-database exists, if not create it
2. check if database-credentials are correct

======== methology:

====== TODO

====== TestDocumentation

==== commands

=== first run: initialization of jquery-server.com database

o delete jquery_server database run index.php, check if it is beeing correctly created from file db/mysql/lib_mysqli_commands.test.sql

o if the jquery_server database exists, you should be forwarded to login_frontend.php

*/
include_once("config/config.php");
include_once("./lib/php/lib_mysqli_commands.php");

include_once("./lib/php/lib_detectLang.php");
global $lang;
$lang = detectLang();

global $settings_database_name;
if(databaseExists($settings_database_name))
{
	echo '<!doctype html>
	<html>
	<head>
	<meta http-equiv="refresh" content="0; URL=login_frontend.php">
	<title>redirect</title>
	</head>
	<body>
		<div id="parent">
			<div id="zentriert" class="gradientV">
				<fieldset>
					<p>
						You will be redirected to the <a href="login_frontend.php" rel="external">login page</a>
					</p>
				</fieldset>
				</form>
				</ul>
			</div>
		</div>
		</div>
	</body>
	</html>';
}
else
{
	echo '<!doctype html>
	<html>
	<head>
	<meta http-equiv="refresh" content="5; URL=login_frontend.php">
	<title>redirect</title>
	</head>
	<body>
		<div id="parent">
			<div id="zentriert" class="gradientV">
				<fieldset>
					<hr>
					<h1 color="red">database '.$settings_database_name.' does not exist, creating it now... </h1>
					<hr>
					<p>
						You will be redirected to the <a href="login_frontend.php" rel="external">login page</a> in a few seconds... 
					</p>
				</fieldset>
				</form>
				</ul>
			</div>
		</div>
		</div>
	</body>
	</html>';
	loadSQLFromFile("db/mysql/lib_mysqli_commands.test.sql");
}
?>