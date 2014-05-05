<?php
/* ================= please put this on top of every page, modify the allowed users/groups entries to manage access per page. */
error_reporting(E_ALL); // turn the reporting of php errors on
$allowed_users = "all users including guests"; // a list of userIDs that are allowed to access this page 
$allowed_groups = "all groups including guests"; // a list of groups, that are allowed to access this page
require_once('./lib/php/lib_security.php'); // will mysql-real-escape all input
require_once("config/config.php"); // load project-config file
/* ================= */
?>
<!DOCTYPE html> 
<html> 
<head> 
	
	<!-- meta -->
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">

	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- apple-iphone specific stuff -->
	<meta name="apple-mobile-web-app-capable" content="yes"/>
	<meta name="apple-mobile-web-app-status-bar-style" content="white">
	<link rel="apple-touch-icon" href="images/opensource_icon.png"/>

	<!-- credits: who made this world a better place? -->
	<meta name="author" content="user">

	<!-- tools: what was used to make this world a better place -->
	<meta name="editor" content="pdt eclipse">

	<script type="text/javascript" src="lib/js/dynamically_load_js_and_css.js"></script>
</head>
<body>
	<div data-role="page" id="forgottenPassword">
		<div data-role="header" data-position="inline">
			<?php global $settings_logo; echo $settings_logo; ?>
		</div>
		<div data-role="content">
			<h4 class="translate">password forgotten?</h4>
			<form id="forgottenPasswordForm" class="forgottenPasswordForm" action="forgottenPassword_backend.php" method="post" accept-charset="UTF-8" data-ajax="false">
				<!-- where errors are displayed (put it directly next to the interactive element, that can produce an error) -->
				<div id="error" class="error" data-role="collapsible">
					<h3>error/status</h3>
					<p>
					<div id="details">details</div>
					</p>
				</div>
				
				<label for="mail">mail*:</label>
				<input type="text" name="mail" id="mail" maxlength="250" value=""/>

				<!-- submit button -->
				<input type="submit" name="Submit" class="translate" value="Please mail me a new password." />

			</form>
		</div> 
		<div data-role="footer">
			<!-- if a user is not registered yet, they can click on this button -->
			<a href="frontend_useradd.php" rel="external">register</a>
		</div> 
	</div>
</body>
</html>