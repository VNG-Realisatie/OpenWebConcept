$(document).ready(function() {
	checkRadioGroups();
	checkDateSelectType();
});

$(function(){

	$(document).on('click', '.goto--page', function(e) {
		
		e.preventDefault();
		var $page_id 	= $(this).data('goto');
		var $validate 	= $(this).data('validate');
		gotoPageNumber($page_id, $validate, $('.section--page.active').data('page'));

	});

	$(document).on('click', '.reset--upload', resetImageUpload);

	$(document).on('change', '.radio--group input[type=radio]', checkRadioGroups);
	$(document).on('change', '.input--date_type', checkDateSelectType);
	$(document).on('change', '#input--picture', function() { setPreviewImage(this);	});

});

function resetImageUpload() {
	var input_pic = $("#input--picture");

	input_pic.replaceWith( input_pic = input_pic.clone( true ) );
	$('.upload-preview').find('img').attr('src', '');

	$('.upload-preview').hide();
	$('.upload-btn-wrapper').show();

}

function checkRadioGroups() {
	
	$('.radio--group input[type=radio]').each(function(){
		
		if( $(this).is(':checked') ) {
			$(this).closest('label').addClass('active');
		} else {
			$(this).closest('label').removeClass('active');
		}

	});

}

function checkDateSelectType() {
	
	var $selected_type = $('.input--date_type:checked');
	if( $selected_type.length == 1 && $selected_type.val() == 'past' ) {
		$('.date-select-wrapper').show();
	} else {
		$('.date-select-wrapper').hide();
	}

}

function setPreviewImage($input) {

	if( $input.files && $input.files[0] ) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $('.upload-preview img').attr('src', e.target.result);
            $('.upload-preview').show();
            $('.upload-btn-wrapper').hide();
        }

        reader.readAsDataURL($input.files[0]);
    }

}

function gotoPageNumber(page_id, validate, previous_page) {
	
	if( validate !== true || validatePage(previous_page) ) {
	
		$('.container--pages .section--page').removeClass('active');
		$('.container--pages .section--page[data-page="' + page_id + '"]').addClass('active');

		if( map && typeof map !== 'undefined' ) {
			map.invalidateSize();
		}

	}

}

function validatePage(page_id) {
	
	if( page_id == 1 ) {

		if( $('#input--location_lat').val() == '' || $('#input--location_lng').val() == '' ) {

			alert('Locatie is verplicht om de melding te kunnen verwerken');

			return false;

		}

	} else if( page_id == 2 ) {

		if( $('#input--message').val() == '' ) {

			alert('Omschrijving is een verplicht veld');
			return false;

		} else {

			if( $('.input--date_type:checked').length > 0 ) {

				if( $('.input--date_type:checked').val() == 'past' && ($('#input--date').val() == '' || $('#input--time').val() == '') ) {

					alert('Datum en tijd zijn verplichte velden');
					return false;

				}

			} else {
				
				alert('Datum is een verplicht veld');
				return false;

			}
		}

	}

	return true;

}

(function( $ ){
   $.fn.getAlertContainer = function(class_name) {
      if( $(this).find('.alert--custom').length > 0 ) {
      	$(this).find('.alert--custom').remove();
      }

      if( class_name == 'error' ) {
      	class_name = 'danger';
      }

      $(this).prepend('<div class="alert alert--custom alert-' + class_name + '"></div>');

      return this;
   }; 
})( jQuery );