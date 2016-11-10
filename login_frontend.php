<?php
/* TODO: */
require_once 'header.php';
?>
		<!-- page-specific css -->
		<style>
			body {
			  padding-top: 40px;
			  padding-bottom: 40px;
			  background-color: #eee;
			}
			
			.form-signin {
			  max-width: 330px;
			  padding: 15px;
			  margin: 0 auto;
			}
			.form-signin .form-signin-heading,
			.form-signin .checkbox {
			  margin-bottom: 10px;
			}
			.form-signin .checkbox {
			  font-weight: normal;
			}
			.form-signin .form-control {
			  position: relative;
			  font-size: 16px;
			  height: auto;
			  padding: 10px;
			  -webkit-box-sizing: border-box;
			     -moz-box-sizing: border-box;
			          box-sizing: border-box;
			}
			.form-signin .form-control:focus {
			  z-index: 2;
			}
			.form-signin input[type="text"] {
			  margin-bottom: -1px;
			  border-bottom-left-radius: 0;
			  border-bottom-right-radius: 0;
			}
			.form-signin input[type="password"] {
			  margin-bottom: 10px;
			  border-top-left-radius: 0;
			  border-top-right-radius: 0;
			}
		</style>

		<div class="logo">
			<?php
			echo '<img id="logo" src="'.config::get('platform_logo').'" style="width:200px;"/>';
			?>
		</div>
		<form class="form-signin" action="login_backend.php" onsubmit="javascript: return false;">
			<!-- what backend function to trigger -->
			<input name="action" value="login" hidden>
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
			<a href="register.php" class="btn btn-lg btn-primary btn-block" style="background-color: #76bffe;">register</a>
			<!-- where errors are displayed (put it directly next to the interactive element, that can produce an error) -->
			<div class="error_div"></div>
		</form>
    
    <!-- include page-specific js libraries -->
    <script src="lib/js/lib_webtoolkit.md5.js"></script>
    <!-- page-specific js code -->
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
<?php
/* TODO: */
include 'footer.php';
?>