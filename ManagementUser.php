<!-- ================= TODO
o test adding users
o test deleting users
o change all php to javascript, php generated sources out
currently on: getting page useradd to work (form like login)
o test profile picture upload :-D
... is broken. i don't know yet how to jquery->upload without page refresh. (it's a bigger problem so i won't fix it now)
... submit is wrong... because it submitts the whole form instead of triggering a upload
-->
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="">
<meta name="author" content="">
<link rel="shortcut icon" href="../../docs-assets/ico/favicon.png">

<title>ManagementUser</title>

<!-- Bootstrap core CSS -->
<link href="css/bootstrap.min.css" rel="stylesheet">

<!-- Custom styles for this template -->
<link href="css/offcanvas.css" rel="stylesheet">

<!-- Just for debugging purposes. Don't actually copy this line! -->
<!--[if lt IE 9]><script src="../../docs-assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
</head>

<body>
	<div class="navbar navbar-fixed-top navbar-inverse" role="navigation">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse"
					data-target=".navbar-collapse">
					<span class="sr-only">Toggle navigation</span> <span
						class="icon-bar"></span> <span class="icon-bar"></span> <span
						class="icon-bar"></span>
				</button>
				<a class="settings_platform_name navbar-brand" href="#"><img src="images/opensource_icon_18x18.png"></a>
			</div>
			<div class="collapse navbar-collapse">
				<ul class="nav navbar-nav">
					<li><a href="#home">Home</a></li>
					<li class="active"><a href="ManagementUser.php">Users</a></li>
					<li ><a href="ManagementDevice.php">Devices</a></li>
					<li><a href="#about">About</a></li>
					<li><a href="#contact">Contact</a></li>
				</ul>
			</div>
			<!-- /.nav-collapse -->
		</div>
		<!-- /.container -->
	</div>
	<!-- /.navbar -->

	<div class="container">

		<div class="row row-offcanvas row-offcanvas-right">

			<div class="col-xs-12 col-sm-9">
				<p class="pull-right visible-xs">
					<button type="button" class="btn btn-primary btn-xs"
						data-toggle="offcanvas">Toggle nav</button>
				</p>
				<div class="jumbotron">
					<h1>UserManagement</h1>
					<p>Here you can edit or delete existing or add new Users. To delete
						a user, please hit the edit button first.</p>
				</div>
				<div class="listOfusers row"></div>
				<!--/row-->
			</div>
			<!--/span-->

			<div class="col-xs-6 col-sm-3 sidebar-offcanvas" id="sidebar"
				role="navigation">
				<div class="list-group">
					<a href="#listOfusers" class="list-group-item active">List of Users</a>
					<a href="#form-userEdit" class="list-group-item">EditForm</a>
				</div>
			</div>
			<!--/span-->
		</div>
		<!--/row-->

		<hr>

		<!-- user add/edit form -->
		<h4>Edit/Add User:</h4>
		<form id="form-userEdit" class="form-userEdit" action="lib/php/lib_users_and_groups.php" onsubmit="javascript: return false;">
			<p>
				<img id="profilepicture" class="profilepicture" src="" alt="profile Picture">
			</p>
			<input id="UserID" name="UserID" type="text" class="hidden form-control">
			<input id="action" name="action" type="text" class="hidden form-control"> <!-- default-mode: create new user -->
			<label>firstname:</label>
			<input id="firstname" name="firstname" type="text" class="form-control">
			<label>lastname:</label>
			<input id="lastname" name="lastname" type="text" class="form-control">

			<!-- username -->
			<label>UserName:</label>
			<input id="username" name="username" type="text" class="form-control">
			<!-- password -->
			<label>Password:</label>
			<!-- should not be submitted, because it has no name -->
			<input id="password" type="password" placeholder="password" class="form-control" title="something wrong here" data-placement="bottom">
			<!-- password check -->
			<input id="password_check" type="password" placeholder="password Again" class="form-control" title="something wrong here" data-placement="bottom">
			<input id="password_encrypted" name="password_encrypted" type="text" placeholder="generated encrypted password" class="form-control" type="submit">
		</form>

		<!-- groups -->
		<label>belongs to these Groups:</label>
		<p>tip on one of these buttons to make the user belong to this group
			(blue) or remove user from group (grey).</p>
		<div class="row groups"></div>

		<!-- controls -->
		<div class="row">
			<div class="col-6 col-sm-6 col-lg-4">
				<button id="add" class="btn btn-lg btn-warning btn-block">add</button>
			</div>
			<div class="col-6 col-sm-6 col-lg-4">
				<button id="save" class="btn btn-lg btn-warning btn-block">save</button>
			</div>
			<div class="col-6 col-sm-6 col-lg-4">
				<button id="delete" class="btn btn-lg btn-danger btn-block">delete</button>
			</div>
			<!-- where errors are displayed (put it directly next to the interactive element, that can produce an error) -->
			<div class="error_div col-6 col-sm-6 col-lg-4"></div>
		</div>
		<!-- confirm delete dialog -->
		<div id="confirm_deletion" class="row" style="display: none;">
			<div class="col-6 col-sm-6 col-lg-4">
				<div class="well">Are you shure?</div>
			</div>
			<div class="col-6 col-sm-6 col-lg-4">
				<button id="delete_confirm" class="btn btn-lg btn-danger btn-block">confirm deletion</button>
				<button id="delete_cancel" class="btn btn-lg btn-primary btn-block">cancel deletion</button>
			</div>
		</div>
	</div>

	<footer>
		<!-- <p>&copy; Company 2013</p>  -->
	</footer>

	</div>
	<!--/.container-->

	<!-- Bootstrap core JavaScript
    ================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->
	<script src="lib/js/jquery.js"></script>
	<script src="lib/js/bootstrap.min.js"></script>
	<script src="lib/js/lib_webtoolkit.md5.js"></script>
	<script src="lib/js/lib_general.js"></script>
	<script src="lib/js/lib_users_and_groups.js"></script>
	<script>
	var users_store = null; // save for later use
	var groups_store = null; // save for later use
    $(document).ready(function() {
        // get users from server and display them via template
        users(function(data)
		{
        	users_store = data; // save for later use

			$(".listOfusers").fillTemplate(data,'<div class="col-6 col-sm-6 col-lg-4"><div class="thumbnail"><img class="profilepicture" src="$profilepicture" alt="profile Picture"><div class="caption"><h3>$firstname $lastname</h3><p>Username: $username</p><p>mail: <a href="mailto:$mail">$mail</a></p><button class="edit btn btn-lg btn-block">Edit</button><div class="UserID hidden">$id</div></div></div></div>');

			$(".edit").click(function()
			{
				scrollTo("#form-userEdit");

				// what happens if the user clicks on edit button below profile picture
				var UserID = $(this).next();

				UserID = $(UserID).text();

		    	for(var key in users_store)
		    	{
		    		var user = users_store[key];
				
	   				if(user["id"] == UserID)
	   				{
	   					$("#UserID").val(user["id"]);
	   					$("#action").val("update");
	   					$("#profilepicture").attr('src',user["profilepicture"]);
	   					$("#username").val(user["username"]);
	   					$("#firstname").val(user["firstname"]);
	   					$("#lastname").val(user["lastname"]);
	   					$("#password").val(user["password"]);
	   					$("#password_check").val(user["password"]);
	   					$("#password_encrypted").val(user["password"]);

	   					// setting group-buttons
	   					var groups_array = user["groups"].split(",");

	   						$('.group').removeClass("btn-primary"); // disable all group buttons
		   					for(var i=0;i < groups_array.length;i++)
		   					{
			   					if(groups_array[i])
			   					{
				   					$(".group").thatHaveText(groups_array[i],function(element)
				   					{
			   							$(element).addClass("btn-primary"); // find element by text and enable it
				   					});
	   							}
		   					}
	   					
	   					break;
	   				}
   				}
			});
		});

		// get groups from server and display them via template
        groups(function(data)
		{
        	groups_store = data; // save for later use

			$(".groups").fillTemplate(data,'<div class="col-6 col-sm-6 col-lg-4"><button class="group toggle btn btn-lg btn-block">$groupname</button></div>');

			// make button-groups toggle-able
			$(".toggle").click(function(){
				if($(this).hasClass("btn-primary"))
				{
					$(this).removeClass("btn-primary");
				}
				else
				{
					$(this).addClass("btn-primary");
				}
			});
		});

    	// when hitting save trigger submit
		$("#save").click(function() {
			$('.form-userEdit').submit();
		});

    	// delete user
		$("#delete").click(function() {
			$("#action").val("delete");
			$("#confirm_deletion").fadeIn(400);
			$("#confirm_deletion").css("display","block");
			scrollTo("#confirm_deletion");
		});

    	// confirm deletion user
		$("#delete_confirm").click(function() {

			var UserID = $("#UserID").val(); // read selected user id
			var data = {"UserID":UserID,"action":"delete"};
			var url = "lib/php/lib_users_and_groups.php?";
			submitUrl(url,data,function(result)
		    	    {
						ServerStatusMessage(result,$(".error_div")); // visualize the response

						$("#confirm_deletion").fadeOut(400); // hide the confirm dialog
						$("#action").val(""); // reset action

						if(result["resultType"] == "success")
						{
							// after a successful deletion -> what now?
							// $('.form-userEdit').clearForm();
							// $("#profilepicture").attr("src","");
							document.location.reload(true); // reload the page, will probably also clear and reset form but also update the user-list
						}
		    	    }
			);
		});

    	// cancel deletion user
		$("#delete_cancel").click(function() {

			$("#confirm_deletion").fadeOut(400);
			$("#action").val("");
		});

    	// when hitting save trigger submit
		$("#add").click(function() {
			$("#firstname").focus(); // will bring focus
			$("#firstname").addClass("focusedInput"); // will give it the boostraped focus style
			$('.form-userEdit').clearForm();
			$("#profilepicture").attr("src","");
			$('.group').removeClass("btn-primary"); // disable all group buttons
			$("#UserID").val(""); // reset selected user id
			$("#action").val("new");
			scrollTo("#firstname");
		});

    	// this is executed when user hits enter on input fields or touches down on the save button
    	$('.form-userEdit').submit(function() {
	    	// validate form
			var valid = true;

	    	if(($("#password").val() != "") && ($("#password_check").val() != "")) // test if both fields are filled
	    	{
				if($("#password").val() != $("#password_check").val()) // test if both fields contain the same
	            {
					valid = false;
	            }
	    	}
	    	else
	    	{
	    		valid = false;
	    	}

	    	// display error
	    	if(valid == false)
	    	{
		    	toolTipOn('#password','empty or did not match the below');
		    	toolTipOn('#password_check','empty or did not match the below');
	    	}

            if(valid)
            {
                // read values of group-buttons (are outside of form, because they trigger form-submit)
				var groups = "";
				$(".group").each(function() {
					if($(this).hasClass("btn-primary")) // all buttons that have this class set are "active"
					{
						if(groups == "")
						{
							if($(this).text() != "")
							{
								groups = $(this).text();
							}
						}
						else
						{
							if($(this).text() != "")
							{
								groups += ","+$(this).text();
							}
						}
					}
				});

				var additionalData = {"groups":groups};
            
		        submitForm(this,function(result)
			    	    	    {
			    					ServerStatusMessage(result,$(".error_div")); // visualize the response

			    					if(result["resultType"] == "success")
			    					{
			    						document.location.reload(true); // reload the page, will probably also clear and reset form but also update the user-list
			    						// after a successful save -> what now?
			    					}
			    	    	    },
			    	    	    additionalData
				);
			}
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