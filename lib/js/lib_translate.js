$(document).ready(function() {
	translateDocument();
});

/* starts the translation process */
function translateDocument() 
{
	var keywords = "";
	$(".translate").each(function() {
		if($(this).prop("textContent"))
		{
			keywords = keywords +"#" + $(this).text();
		}
		else if($(this).prop("value"))
		{
			keywords = keywords +"#" + $(this).val();
		}
	});
	
	if (typeof lang !== 'undefined') {
		translate(keywords,lang);
	}
	else
	{
		if(keywords != "")
		{
			translate(keywords);
		}
	}
}

/* iterate over all elemnts that need translation and insert translation */
function apply_translations(response_array)
{
	var translateThis = $(".translate");
	for( var i = 0; i < translateThis.length; i++)
	{
		if($(translateThis[i]).prop("textContent"))
		{
			$(translateThis[i]).text(response_array[i]);
		}
		else if($(translateThis[i]).is("input"))
		{
			if(($(translateThis[i]).prop("type") == "submit") || ($(translateThis[i]).prop("type") == "button"))
			{
				buttonChangeLabel(translateThis[i],response_array[i]);
			}
		}
	}
}
/* translate single/multiple keywords as object*/
function translate(keywords,lang)
{
	if(lang != "") { keywords = keywords + "&lang="+lang; }

	keywords = "translations="+keywords;
	
	var jqxhr = $.post("translations_backend.php", keywords, function(response, responseText, jqXHR) {
		
		// process json result/response
		var response_array = jQuery.parseJSON(response);
		
		if(jQuery.isArray(response_array))
		{
			if(response_array.length > 0)
			{
				// fill in the translations
				apply_translations(response_array);
			}
		}
	});
}
