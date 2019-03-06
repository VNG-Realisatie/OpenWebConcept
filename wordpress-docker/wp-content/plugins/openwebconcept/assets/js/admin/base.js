jQuery('.do-owc-plugin-repo_action').on('click', function(e) {
	
	e.preventDefault();

	var $bulk_action_field = jQuery(this).closest('.bulkactions').find('.bulk-action-selector');

	if( $bulk_action_field.length > 0 && $bulk_action_field.val() !== '' ) {
		
		jQuery('#owc-plugin-repo_action--input').val( $bulk_action_field.val() );
		jQuery('#owc-plugin-repo_action--form').submit();

	}

})

jQuery(document).on('click', '.owc_plugin__repro--has_sub > a', function(e) {

	if( jQuery(e.target).is('input') === false ) {

		e.preventDefault();

		jQuery(this).parent().toggleClass('open');
		
	}

});

jQuery(document).on('change', '.owc_plugin__repro--has_sub > a input[type=checkbox]', function() {

	var $parent_hassub = jQuery(this).closest('.owc_plugin__repro--has_sub');

	if( jQuery(this).is(':checked') ) {

		$parent_hassub.find('ul input[type=checkbox]').prop('checked', true);

	} else {
	
		$parent_hassub.find('ul input[type=checkbox]').prop('checked', false);

	}

});

jQuery(document).on('change', '.owc_plugin__repro--has_sub > ul input[type=checkbox]', function() {

	var $parent_hassub 	= jQuery(this).closest('.owc_plugin__repro--has_sub');
	var $parents_ul 	= $parent_hassub.find('ul');
	var $checkboxes		= $parents_ul.find('input[type=checkbox]');
	var $checkedboxes	= $parents_ul.find('input[type=checkbox]:checked');

	if( $checkedboxes.length == 0 ) {
		
		$parent_hassub.find(' > a input[type=checkbox]').prop('indeterminate', false).prop('checked', false);

	} else if( $checkedboxes.length >= $checkboxes.length ) {
		
		$parent_hassub.find(' > a input[type=checkbox]').prop('indeterminate', false).prop('checked', true);;

	} else {
		
		$parent_hassub.find(' > a input[type=checkbox]').prop('indeterminate', true).prop('checked', false);
		
	}

});

jQuery(document).on('click', '.owc_plugin__item > a', function(e) {
	
	if( jQuery(e.target).is('input') === false ) {
	
		e.preventDefault();

		var $slug = jQuery(this).find('input').val();

		jQuery('.owc_plugin__item > a').removeClass('active');
		jQuery(this).addClass('active');

		owc_get_readme_html( $slug );

	}

});

jQuery(document).ready(function() {
	
	if( jQuery('.owc_plugin__repro--list').length > 0 && jQuery('.owc_plugin__item').length > 0 && jQuery('.owc_plugin__item > a.active').length == 0 ) {

		jQuery('.owc_plugin__item').first().find('a').click();

	}

});

jQuery(window).on('load', function() {
	
	owc_plugin_init_bulk_action();

});

jQuery(document).on('click', '.owc-plugin_info--tab', function(e) {
	
	e.preventDefault();

	var $content_wrap 	= jQuery(this).closest('.owc_plugin__repro--content');
	var $tab_id 		= jQuery(this).attr('href');

	$content_wrap.find('.owc-plugin_info--tab.current').removeClass('current');
	jQuery(this).addClass('current').blur();

	$content_wrap.find('.owc-plugin_section').hide();
	$content_wrap.find($tab_id).show();


});

jQuery(document).on('click', '.owc-plugin_repro--action', function(e) {
	
	e.preventDefault();

	var $slug 	= jQuery(this).data('slug');
	var $action = jQuery(this).data('action');

	jQuery(this).closest('#plugin-information-footer').find('.spinner').addClass('is-active');
	jQuery(this).prop('disabled', true).attr('disabled', true);

	owc_plugin_run_package_actions([$slug], $action, 'owc_plugin_end_single_package_action');

});

jQuery(document).on('keyup', '#owc_plugin-filter_repo--list', function(e) {

	owc_filter_repo_list( jQuery(this).val() );

});

var owc_filter_request = null;
function owc_filter_repo_list($search_q, $end_function, $function_data) {

	if($end_function === undefined) { $end_function = false; }
	if($function_data === undefined) { $function_data = false; }

	if (owc_filter_request != null) {
        owc_filter_request.abort();
        owc_filter_request = null;
     }

     owc_filter_request = jQuery.post(
		ajax_object.ajax_url,
		{ 
			'action': 'get_owc_repo_list_html',
			'search': $search_q
		}, 
		function(data) {
			
			jQuery('.owc_plugin__repro--list').replaceWith(data);

			if( $end_function ) {
				window[$end_function]($function_data);
			}

		}
	);

}

var owc_readme_request = null;
function owc_get_readme_html($slug) {

	if (owc_readme_request != null) {
        owc_readme_request.abort();
        owc_readme_request = null;
     }

	jQuery('.owc_plugin__repro--loading-wrap').show();

	owc_readme_request = jQuery.post(
		ajax_object.ajax_url,
		{ 
			'action': 'get_owc_plugin_readme_html',
			'slug': $slug
		}, 
		function(data) {
			
			jQuery('.owc_plugin__repro--loading-wrap').hide();
			jQuery('.owc_plugin__repro--content').html(data);

		}
	);

}


function owc_plugin_run_package_actions($packages, $action, $end_function) {
	
	jQuery.post(
		ajax_object.ajax_url,
		{ 
			'action': 		'owc_plugin_run_package_actions',
			'action_type': 	$action,
			'packages': 	$packages
		}, 
		function(data) {
			
			if( data.message !== undefined ) {
				jQuery('#owc_plugin-repo--messages_wrap').html(data.message);
				jQuery('html,body').animate({scrollTop: (jQuery('#owc_plugin-repo--messages_wrap').offset().top-40)},'slow');
			}

			window[$end_function](data);

		},
		"json"
	);

}

function owc_plugin_end_single_package_action(data) {

	var $active_package = jQuery('.owc_plugin__item > a.active');
	if( $active_package.length == 1 ) {

		jQuery('#plugin-information-footer').find('.spinner').removeClass('is-active');

		owc_filter_repo_list( jQuery('#owc_plugin-filter_repo--list').val(), 'owc_plugin_click_package', $active_package.find('input').val() );

	} else {

		location.reload();

	}

}

function owc_plugin_click_package( $active_package ) {
	
	var $active_input = jQuery('.owc_plugin__item > a input[value=' + $active_package + ']');

	// set the active item
	$active_input.closest('a').addClass('active');

	// get the readme html
	owc_get_readme_html($active_package);

}

function owc_plugin_init_bulk_action() {

	var $bulk_wrap 	= jQuery('.owc-plugin-repo_action--init_wrap');
	var $bulk_i 	= 0;

	if( $bulk_wrap.length > 0 ) {

		var $bulk_packages 		= $bulk_wrap.find('.packages');
		var $bulk_packages_i 	= $bulk_packages.length;
		
		$bulk_packages.each(function() {
			
			jQuery.ajax({
				async: 		false,
				type: 		"POST",
				url: 		ajax_object.ajax_url,
				dataType: 	'json',
				data: 		{
					'action': 		'owc_plugin_run_package_actions',
					'action_type': 	$bulk_wrap.find('.action').val(),
					'packages': 	[jQuery(this).val()]
				},
				success: function(data) {
					if( data.message !== undefined ) {
						setTimeout(function(){
							jQuery('.owc-plugin-repo_action--console_wrap').append(data.message);
						}, 300);
					}
				},
				complete: function(data_2) {
					$bulk_i++;

					if( $bulk_i >= $bulk_packages_i ) {

						setTimeout(function(){
							jQuery('.owc-plugin-repo_action--message_wrap').show();
						}, 1000);

					}

				}
			});

		});

	}

}