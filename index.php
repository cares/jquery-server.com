<?php
/* TODO:
 * eleminate the usage of global $VARIABLE
 * and pass the variables around as function($VARIABLE) parameters instead
 */
require_once 'config.php'; // check if config is accessable - if not make accessable

include_once("./lib/php/lib_mysqli_commands.php");

if(lib_mysqli_commands::DatabaseExists(config::get("db_name")))
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
					<h1 color="red">database '.config::get("db_name").' does not exist, creating it now... </h1>
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
	ImportSQLFromFile("./database/jqueryserver.com.sql");
}
?>