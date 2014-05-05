/* does all sorts of default initialization stuff */
var settingsServer = null;
var search = window.location["search"];
$(document).ready(function() {

	/* get settings from server and apply them */
	settings(function(settings){
		settingsServer = settings;
		/* load the logo  - fill all <div class="logo"> with content */
		$(".logo").html('<img id="logo" src="'+settingsServer["settings_platform_logo"]+'" style="width:200px;"/>');
		$(".settings_platform_name").append(settingsServer["settings_platform_name"]);
	});

	// setTitle
	setTitle();

	/* all checkboxes should change their value="0" to "1" if checked, because this is transmitted when submitting a form */
	/* modify checkboxes, so that form "sees" the change done */
	$("input[type='checkbox']").bind( "change", function(event, ui) {
		if($(this).prop("checked"))
		{
			$(this).val("1");
		}
		else
		{
			$(this).val("0");
		}
	});
});

/*
 * displays the anser/result/info/error messages coming from the server inside a div of this format:

 the html:
	<!-- where errors are displayed (put it directly next to the interactive element, that can produce an error) -->
	<div id="error2" class="error" data-role="collapsible" data-content-theme="c">
		<h3>error/status2</h3>
		<p>
			<div id="details2">details</div>
		</p>
	</div>

 * after a request in colorful ways, without reloading the entire page (ajax) to immediately give the user feedback about the user's last action.
 * 
 * * {action":"login","resultType":"success","resultValue":"success","details":"you have now access. live long and prosper! Login expires in 30 minutes."}
 * */
function ServerStatusMessage(answer, output)
{
	if(!output)
	{
		// if no output object given, try to access one via class="status"
		output = $(".status");
	}

	// does one have a element where to display the result/answer/output
	if(output)
	{
		// then display
		$(output).html("<div class='status' data-role='collapsible'> "+answer["resultType"]+": "+answer["action"]+" "+answer["details"]+"</div>");
		var outputId = "'#"+$(output).attr("id");
		
		var status = output.children(":first");
		
		// color the message
		if((answer["resultType"] == "success") || (answer["resultType"] == "true"))
		{
			$(status).css("background","green"); // default is: linear-gradient(#FFFFFF, #F1F1F1) repeat scroll 0 0 #EEEEEE;
		}
		else if((answer["resultType"] == "failed") || (answer["resultType"] == "error") || (answer["resultType"] == "false"))
		{
			$(status).css("background","orange");
		}

		$(output).fadeIn(400); // fade in
		
		// wait 1 second
		var aktiv = window.setInterval(function(){
			$(output).fadeOut(400); // fade out again
			window.clearInterval(aktiv);
		}, 3000);
	}
}

/* pass a $("#form") and get a object with key=value input data of that form */
function form2Object(form)
{
	var formObject = {};
	$(form).find(":input").each(function() {
		// The selector will match buttons; if you want to filter
		// them out, check `this.tagName` and `this.type`; see
		// below
		formObject[this.name] = $(this).val();
	});
	
	return formObject;
}
/* creates a dialog that asks the User for confirmation, to delete the users */
function createDialogDoYouReallyWantTo(title, text, executeFunction)
{
    return $("<div class='dialog' title='" + title + "'><p>" + text + "</p></div>")
    .dialog({
        resizable: false,
        height:140,
        modal: true,
        buttons: {
            "Confirm": function() {
                $( this ).dialog( "close" );
                executeFunction();
            },
            Cancel: function() {
                $( this ).dialog( "close" );
            }
        }
    });
}

/* converts a form into a json compatible javascript object */
$.fn.serializeObject = function()
{
    var o = {};
    var a = this.serializeArray();
    $.each(a, function() {
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};

/* clear a form - empty all inputs
 * credits go to: Peppered Lemons
 * http://stackoverflow.com/questions/6653556/jquery-javascript-function-to-clear-all-the-fields-of-a-form
 * http://www.learningjquery.com/2007/08/clearing-form-data/
 * */
$.fn.clearForm = function()
{
	$(this).find(':input').each(function() {
		switch(this.type) {
		case 'password':
		case 'select-multiple':
		case 'select-one':
		case 'text':
		case 'textarea':
			$(this).val('');
			break;
		case 'checkbox':
		case 'radio':
			this.checked = false;
		}
	});
};

/* change the label of a jQM button */
function buttonChangeLabel(button,value)
{
	$(button).prev('span').find('span.ui-btn-text').text(value);
	$(button).prop('value', value);
}

/* checks if it is empty */
function validate(input) {
	var valid = true;
	var label = $(input).prev(); // get label
	var elementName = $(input).attr("name");
	
	var labelval = label.text();
	
	var star = returnLastXChars(labelval,1);
	
	if(star == "*")
	{
		if($(input).val() == "")
		{
			valid = false;
			$(label).css("color", "red");
		}
		else
		{
			$(label).css("color", "#777777");
		}
	}
	
	if(valid)
	{
		if(elementName == "email")
		{
			$(input).next().fadeOut("slow"); // email_error
		}
	}
	else
	{
		$(input).next().fadeIn("slow"); // email_error
	}
	
	return valid;
}

/* read all input fields of a form, assemble target.php?key=value url and submit via jqxhr request.
 * 
 * form -> the form to be serialized and send
 * additionalData -> if one wants to pass additionalData that is not part of the form to the server
 * ResultHandler -> will be processing server response
 * */
function submitForm(form,ResultHandler,additionalData) {
	
	var data = null;
	
	if(typeof additionalData === 'object')
	{
		data = additionalData;
	}

	var title = getTitle();
	
	// find url where to send the serialized form to
	var url = $(form).attr("action");
	if(url == "")
	{
		url = title+"_backend.php?"; // that was a convention... that when there is a _frontend.php there should be a belonging _background.php
	}
	else
	{
		url += "?";
	}

	// serialize form and attach to url
	url += $(form).serialize();

	submitUrl(url,data,ResultHandler);
}

/* get settings of server (no confidental stuff) if debug mode is on or off... what logo to load */
function settings(ResultHandler) {
	
	var url = "lib/php/lib_settingsServer.php?get=settings";
	var data = null;
	submitUrl(url,data,ResultHandler);
}

/* the "manual" way of communicating with the backend is to assemble your custom url, simply submit a specific url to the backend
 * url = can.php?be=like&that=you&know=what -> will be send to server
 * data = {"key1":value1,"key2":value2} -> will be added to the above url parameters
 * */
function submitUrl(url,data,ResultHandler)
{
	var jqxhr = $.post(url, data, function(response, responseText, jqXHR) {
		if (response) {
			// process json result/response
			var response_array = null;
			try {
				response_array = jQuery.parseJSON(response);
			}
			catch(e){
				 // catch error
				response_array = {status:"failed",message:"no valid json from server, could note decode json, server responded: "+response};
			}

			// if not empty
			if(response_array)
			{
				// execute ResultHandler
				ResultHandler(response_array);
			}
		}
	});
}

/* decodes a json string into a javascript-object
 * 
 * example:
 * {"0":{"id":"240","usern...Value":"","details":""}", "success", Object { readyState=4, responseText="{"0":{"id":"240","usern...Value":"","details":""}", status=200, more...}]
 * */
function jsonDecode(JsonEncodedString)
{
	return jQuery.parseJSON(JsonEncodedString);
}

/* set title automatically, by getting the name from the url-filename, login_frontend -> login */
function setTitle()
{
	document.title = getTitle();
	
	return document.title;
}

/* set title automatically, by getting the name from the url-filename, login_frontend -> login */
function getTitle() 
{
	// set title automatically depending on CurrentFilename
	CurrentFilename = getCurrentFilename();

	var CurrentFilename_array = CurrentFilename.split("_");
	var title = CurrentFilename_array[0];
	document.title = title;
	
	return title;
}

/* write message to firebug-browser-javascript-console */
function log(message)
{
	console.log(message);
}

/* change the current page to that url */
function goToPage(url)
{
	// keep xdebug login_frontend.php?XDEBUG_SESSION_START=ECLIPSE_DBGP&KEY=13830555615557 session if debug mode is on
	if(settingsServer["settings_debug_mode"] == true)
	{
		window.location = url+search;
	}
	else
	{
		window.location = url;
	}
}

/* get filename of current file.php -> file */
function getCurrentFilename(withEnding)
{
	withEnding = typeof withEnding !== 'undefined' ? withEnding : false;

	//this gets the full url
	var url = window.location.pathname;
	var filename = url.substring(url.lastIndexOf('/')+1);
	if(!withEnding)
	{
		filename = filename.substring(0, filename.length - 4);
	}
	
	return filename;
}

/* open a dialog
 * what=id of dialog-div
 * 
 * example of definition of dialog:
 
 	<!-- dialog_deleteUser -->
	<div data-role="dialog" id="dialog_deleteUser">
		<div data-role="header" data-theme="d">
			<h1>Do you really ...</h1>
		</div>
		<div data-role="content">
			<h1>...want to delete these Users?</h1>
			<div id="dialog_deleteUser_content">
			</div>
			<a href="#" onclick="deleteUser();" data-role="button" data-rel="back" data-theme="b">Delete</a>       
			<a href="#" data-role="button" data-rel="back" data-theme="c">Cancel</a>    
		</div>
	</div>
 * */
function openDialog(what)
{
	$("#"+what).dialog('open');
}

/* take template-html code with $variables and replace those variables with the informations from the data-json-array */
(function($) {
    $.fn.fillTemplate = function(data,template) {
    	var result = "";
    	
    	for(var key in data)
    	{
    		var currentTemplate = template;

    		var user = data[key];
    		if(typeof user === 'object')
			{
    			for(var property in user)
    			{
    				// search for "$"+property
    				var searchFor = new RegExp("\\$"+property,"g");
    				currentTemplate = currentTemplate.replace(searchFor, user[property]);
    			}
    			result += currentTemplate;
			}
    	}
		
		$(this).html(result);
	};
})(jQuery);

/* find exact match of elements that have exact text "value" -> <button>value</button>
 * (contains also would also return this <button>NotvalueThatIamLookingFor</button>)
 * execute ResultHandler on every found element
 * */
(function($) {
	$.fn.thatHaveText = function(text,ResultHandler) {
		$(this).each(function() {
			if($(this).text() == text)
			{
				ResultHandler(this);
			}
		});
	};
})(jQuery);


/* scroll to the given element section of the page */
function scrollTo(element)
{
	$('html, body').animate({scrollTop: $(element).offset().top-60}, 500);
}

/* show tooltip */
function toolTipOn(element,text)
{
	$(element).tooltip('hide').attr('data-original-title',text).tooltip('show');
}