jQuery(document).ready(function($) {

	jQuery("#ytnf_reset_log").click(function($){
		if(!confirm("are you sure to clear the invalid video log ?"))
			return;
		else
			jQuery("#ytnf_log_msg").html("<div class='updated'>Please wait....!</div>");
		var data = {
			action: 'ytnf_reset_log',
			whatever: 'reset the log'      // We pass php values differently!
		};
		// We can also pass the url value separately from ajaxurl for front end AJAX implementations
		jQuery.post(ajaxurl, data, function(response) {
			if(response == 'reset')
			{
				jQuery("#ytnf_log_msg").html("<div class='updated'>Log cleared successfully!</div>");
				jQuery("#ytnf_log").html("");
			}
			else
				jQuery("#ytnf_log_msg").html("<div class='error'>Can not clear the log:<br> Server response: <br>" + response + "</div>");
		});
	});
});