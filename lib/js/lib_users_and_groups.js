/* =============== user management functons */

/* returns an array of all users available (if no parameter given)
 * 
* if $user given -> get $user as assoc-array
* by id (default) if no $uniqueKey is given
* (you can also specify get user by username,mail -> $uniqueKey)
*
* via $where you can filter the users you want with your own sql query
*/
function users(ResultHandler,user,uniqueKey,uniqueValue,where)
{
	user = typeof user !== 'undefined' ? user : null;
	uniqueKey = typeof uniqueKey !== 'undefined' ? uniqueKey : null;
	uniqueValue = typeof uniqueValue !== 'undefined' ? uniqueValue : null;
	where = typeof where !== 'undefined' ? where : "";
	
	if(!uniqueValue)
	{
		if(user)
		{
			uniqueValue = user[uniqueKey];
		}
	}

	var urlObject = new Object();
	urlObject["action"] = "users";
	urlObject["uniqueKey"] = uniqueValue;
	urlObject["uniqueValue"] = uniqueValue;
	urlObject["where"] = where;

	var urlString = $.param(urlObject);

	submitUrl("lib/php/lib_users_and_groups.php?"+urlString,null,ResultHandler);
}

/* checks if username is allready taken
*/
function usernameTaken(username,ResultHandler)
{
	var urlObject = new Object();
	urlObject["action"] = "users";
	urlObject["uniqueKey"] = "username";
	urlObject["uniqueValue"] = username;
	urlObject["where"] = "";

	var urlString = $.param(urlObject);

	submitUrl("lib/php/lib_users_and_groups.php?"+urlString,null,ResultHandler);
}

/* delete the selected (checkbox) users */
function deleteUser()
{
	var counter = 0;
	var data2server = {};
	$("input[type='checkbox']").each(function() {
		if($(this).prop('checked'))
		{
			var value = $(this).attr('userid');
			var key = "user2delete"+counter++;
			data2server[key] = value; // adds key:value to object2
		}
	});
	
	// backend in php/ruby/python/java needs to implement this command: lib_users_and_groups.php
	if(data2server)
	{
		var jqxhr = $.post("lib_users_and_groups.php", data2server, function(response, responseText, jqXHR) {
			if(response)
			{
				ServerStatusMessage(response, responseText, jqXHR);
				if(responseText == "success")
				{
					// refresh user list, complete page refresh
					document.location.href = window.location.href;
				}
			}
		});
	}
}

/* =============== group management functons */

/* returns an array of all groups available (if no parameter given)
*
* if $group given -> get $group as assoc-array
* by id (default) if no $uniqueKey is given
* (you can also specify get group by groupname,mail -> $uniqueKey)
*
* via $where you can filter the groups you want with your own sql query
*/
function groups(ResultHandler,group,uniqueKey,uniqueValue,where)
{
	group = typeof group !== 'undefined' ? group : null;
	uniqueKey = typeof uniqueKey !== 'undefined' ? uniqueKey : null;
	uniqueValue = typeof uniqueValue !== 'undefined' ? uniqueValue : null;
	where = typeof where !== 'undefined' ? where : "";

	if(!uniqueValue)
	{
		if(group)
		{
			uniqueValue = group[uniqueKey];
		}
	}

	var urlObject = new Object();
	urlObject["action"] = "groups";
	urlObject["uniqueKey"] = uniqueValue;
	urlObject["uniqueValue"] = uniqueValue;
	urlObject["where"] = where;

	var urlString = $.param(urlObject);

	submitUrl("lib/php/lib_users_and_groups.php?"+urlString,null,ResultHandler);
}