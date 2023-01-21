(function($){
    let xhr;
    $(document).ready(function(){   
        // search filter
		let input = $(document).find('#murdeni_keyword_search [name="q"]');
		

		$(document).on("keyup", '#murdeni_keyword_search [name="q"]', function () {
				let value = $(this).val();
				if (value.length > 2) {
					setTimeout(get_suggestion, 200, value, input);
				}
		});

		function get_suggestion(request, input){
			console.log(request);

		  	xhr = $.ajax({
				url: murdeni.ajaxurl,
				type: 'post',
				data: { action: 'sac_get_posts', s: request },
				beforeSend : function()    {           
				    if(xhr) { 
				    	xhr.abort(); 
				    }

				    $(document).find('.murdeni-autocomplete').remove();

				    // Loading
				    input.parents('form').addClass('loading');

				},
				success: function (response) {
					let regex = new RegExp("(" + request + ")", 'g');
					input.parents('form').after(response);

					$(".title:contains('" + request + "')").each(function() {
					  $(this).html($(this).text().replace(regex, '<span style="text-decoration: underline">$1</span>'));
					});

					// remove loading
					input.parents('form').removeClass('loading');
				}
		  	});
		}

		// Show hide autocomplete
		input.on('focusin', function(){
			$(document).find('.murdeni-autocomplete').addClass('active');
		});

		$(document).on("click", function (e) {
	        if ($(e.target).is(".text-search *") === false) {
	            $(document).find('.murdeni-autocomplete').removeClass('active');
	        }
	    });
		
    });
})(jQuery)