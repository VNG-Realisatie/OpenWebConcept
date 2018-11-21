$(function(){
	
	$('.ajax--form_wrapper').on('submit', function() {


		var $active_page = $(this).find('.section--page.active');

		if( $active_page.find('input[type=submit], button[type=submit], .ajax--send_form').length > 0 ) {
			
			$('.ajax--send_form').trigger('click');

		} else {

			var $page_id = $active_page.data('page')+1;
			gotoPageNumber($page_id);

		}

		return false;

	});

	$(document).on('click', '.ajax--send_form', function() {
		// Show the loading icon
		showAjaxLoading();
		var $formData = getAjaxFormData();

		$.ajax({
			type: "POST",
			url: "http://api.digimelden.nl/api/notifications",
			dataType: 'json',
			processData: false,
    		contentType: false,
			data: $formData,
			success: function(data){
				
				gotoPageNumber('thanks');

			},
			error: function(data) {
				
				var $response_data = jQuery.parseJSON(data.responseText);
				if( typeof $response_data =='object' && typeof $response_data.errors !== 'undefined' && !jQuery.isEmptyObject($response_data.errors) ) {
					
					var $error_messages = [];
					$.each($response_data.errors, function(index, value) {
						if( value[0] != undefined ) {

							$error_messages.push("- " + value[0]);
						}
					});

					showSubmitAlert($error_messages.join('<br>'), 'error');

				} else {
					showSubmitAlert('Er is een fout opgetreden tijdens het versturen van het formulier', 'error');
				}

			},
			complete: function() {

				hideAjaxLoading();

			}
		});

	});

});

function formatFormDate() {

	var $selected_type = $('.input--date_type:checked');
	if( $selected_type.length == 1 && $selected_type.val() == 'past' ) {
		return $('#input--date').val() + ' ' + $('#input--time').val() + ':00';
	} else {
		return moment().format("YYYY-MM-DD HH:mm:ss");
	}

}

function showAjaxLoading() {
	$('.overlay--loading').show();
}

function hideAjaxLoading() {
	$('.overlay--loading').hide();
}

function showSubmitAlert(message, alert_class) {
	var $submit_page = $('.section--submit_page').getAlertContainer(alert_class);
	var $alert_container = $submit_page.find('.alert--custom');
	$alert_container.html(message);
}

function getAjaxFormData() {
	
	var $input_id 	= $('#input--picture').attr('id');
	var $file 		= document.getElementById($input_id).files[0];

	if( window.FormData !== undefined ) {
	    
	    var formData = new FormData();

	    formData.append("picture", $file);
	    formData.append("lat", $('#input--location_lat').val());
	    formData.append("lng", $('#input--location_lng').val());
	    formData.append("message", $('#input--message').val());
	    formData.append("phone", $('#input--phone').val());
	    formData.append("email", $('#input--email').val());
	    formData.append("time", formatFormDate());
	    
	}

	return formData;

}