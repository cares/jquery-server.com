<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="">
<meta name="author" content="">
<link rel="shortcut icon" href="images/opensource_icon.png">
<!-- <link rel="apple-touch-icon" href="images/opensource_icon.png"/> -->

<title>login with your bootstrapped on</title>

<!-- Bootstrap core CSS -->
<link href="css/bootstrap.css" rel="stylesheet">

<!-- Custom styles for this template -->
<link href="css/signin.css" rel="stylesheet">

<!-- global gigalaunch -->
<link href="css/global.css" type="text/css" rel="stylesheet"/>

<!-- Just for debugging purposes. Don't actually copy this line! -->
<!--[if lt IE 9]><script src="../../docs-assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="js/respond.min.js"></script>
    <![endif]-->
</head>

<body>
	<div class="container">
		<div class="logo"></div> <!-- where the logo (as configured in conf/config.php will be shown -->
		<form class="form-signin" action="login_backend.php" onsubmit="javascript: return false;">
			<h2 class="form-signin-heading">Please sign in</h2>
			<!-- credentials -->
			<!-- username input -->
			<input id="username" name="username" type="text" class="form-control" placeholder="username" required autofocus value="username">
			<!-- password input -->
			<!-- should not be submitted, because it has no name -->
			<input id="password" type="password" class="form-control" placeholder="5f4dcc3b5aa765d61d8327deb882cf99" required value="5f4dcc3b5aa765d61d8327deb882cf99">
			<!-- onkeypress this hidden field is updated and transmitted  type="hidden" -->
			<input id="password_encrypted" name="password_encrypted" type="text" class="form-control" placeholder="encrypted Password" required value="5f4dcc3b5aa765d61d8327deb882cf99">
			<label class="checkbox">
				<input type="checkbox" value="remember-me"> Remember me
			</label>
			<button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
			<!-- where errors are displayed (put it directly next to the interactive element, that can produce an error) -->
			<div class="error_div"></div>
		</form>
	</div>
	<!-- /container -->

	<!-- Bootstrap core JavaScript
    ================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->
    <script src="lib/js/jquery.js"></script>
    <script src="lib/js/bootstrap.min.js"></script>
    <script src="lib/js/lib_webtoolkit.md5.js"></script>
    <script src="lib/js/lib_general.js"></script>
    <script>
    $(document).ready(function() {
    	/* handles the submit of the form javascript way (not calling an url) */
    	// validate signup form on keyup and submit
    	$('.form-signin').submit(function() {
    	    submitForm('.form-signin',	function(result)
					    	    	    {
					    					ServerStatusMessage(result,$(".error_div")); // visualize the response
					
					    					// after a successful login
					    					if((result["action"] == "login") && (result["resultType"] == "success"))
					    					{
					    						// go to user's home
					    						goToPage(result["goto"]);
					    					}
					    	    	    }
        	    );
    	    return false; // we don't want our form to be submitted
    	});

    	// manually syncing fields
    	$("#password").keyup(
    	    function()
    	    {
    			password = $("#password").val();
    			password_encrypted = MD5(password); 
    			$("#password_encrypted").val(password_encrypted);
    	    }
    	);
    });
    </script>
</body>
</html>