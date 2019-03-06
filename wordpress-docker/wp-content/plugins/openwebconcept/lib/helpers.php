<?php
	function owc_get_repo_array($group = true, $filter = false) {

		$location 	= 'https://raw.githubusercontent.com/VNG-Realisatie/OpenWebConcept/master/componenten.xml';
		$xmlstring 	= @file_get_contents($location);

		if( $xmlstring !== false ) {

			$xml 	= simplexml_load_string($xmlstring);
			$json 	= json_encode($xml);
			$data 	= json_decode($json, true);

			if( isset($data['Packages']) ) {

				// Set Packages in nice groups
				$data['Packages'] = owc_format_repo_packages( $data['Packages'], $group, $filter );

			}

			return $data;

		} else {

			// Error bestand kon niet opgehaald worden

		}

		return array();

	}

	function owc_format_repo_packages( $packages, $group_packages = true, $filter = false ) {

		$result 	= array();
		$main_group = __('General', 'owc-plugin');

		// Make Group items an array if not already
		if( isset($packages['Group']) && isset($packages['Group']['Package']) ) {
			$packages['Group'] = array($packages['Group']);
		}

		// Make Package items an array if not already
		if( isset($packages['Package']) && isset($packages['Package']['@attributes']) ) {
			$packages['Package'] = array($packages['Package']);
		}

		// Format the packages in the right group
		if( isset($packages['Group']) && !empty($packages['Group']) ) {

			foreach ($packages['Group'] as $group) {
				
				if( isset($group['@attributes']['title']) && isset($group['Package']) ) {

					if( isset($group['Package']) && isset($group['Package']['@attributes']) ) {
						$group['Package'] = array($group['Package']);
					}
					

					// Add install/update status
					$group['Package'] 				= owc_format_repo_package_data($group['Package'], !$group_packages, $filter);

					if( $group_packages ) {
						
						$group_key = sanitize_title($group['@attributes']['title']);
						if( !isset($result[$group_key]) ) {
							$result[$group_key] = array(
								'title' 	=> sanitize_text_field($group['@attributes']['title']),
								'packages' 	=> array()
							);
						}
					
						$result[$group_key]['packages'] = array_merge($result[$group_key]['packages'], $group['Package']);
					
					} else {

						$result = array_merge($result, $group['Package']);

					}

				}

			}

		}

		// Check for packages without a group and push them into the default group
		if( isset($packages['Package']) && !empty($packages['Package']) ) {

			$packages['Package'] 	= owc_format_repo_package_data($packages['Package'], !$group_packages, $filter);
			if( $group_packages ) {
			
				$group_key 				= sanitize_title($main_group);
				
				if( !isset($result[$group_key]) ) {
					$result[$group_key] = array(
						'title' 	=> sanitize_text_field($main_group),
						'packages' 	=> array()
					);
				}
				$result[$group_key]['packages'] = array_merge($result[$group_key]['packages'], $packages['Package']);

			} else {

				$result = array_merge($result, $packages['Package']);

			}

		}

		ksort($result);

		return $result;

	}

	function owc_format_repo_package_data( $packages, $slug_as_key = false, $filter = false ) {

		$plugins = owc_get_all_plugins();

		foreach ($packages as $key => $package) {
			$continue = true;

			if( $filter && isset($package['@attributes']['name']) && stripos($package['@attributes']['name'], $filter) === false ) {
				$continue = false;
				unset($packages[$key]);
			}
			
			if( $continue && isset($package['slug']) && isset($package['@attributes']['type']) ) {

				if( $package['@attributes']['type'] == 'plugin' ) {

					$packages[$key]['installed'] 	= isset($plugins[$package['slug']]);
					$packages[$key]['active'] 		= (isset($plugins[$package['slug']]['Active']) ? $plugins[$package['slug']]['Active'] : false);

				}

				if( $slug_as_key ) {
					$packages[$package['slug']] = $packages[$key];
					unset($packages[$key]);
				}

			}

		}

		return $packages;

	}

	function owc_get_all_plugins() {

		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$results = array();
		$plugins = get_plugins();

		foreach ($plugins as $plugin_slug => $plugin) {

			$slug 						= dirname($plugin_slug);
			$results[$slug] 			= $plugin;
			$results[$slug]['Slug'] 	= $plugin_slug;
			$results[$slug]['Active'] 	= is_plugin_active($plugin_slug);
			
		}

		
		return $results;

	}

	function owc_get_repo_status_class($package) {

		if( isset($package['active']) && $package['active'] == 1 ) {

			return 'owc_plugin__item--installed';
		}

		return '';
		
	}

	function owc_get_repo_title($package) {

		return( isset($package['@attributes']['name']) ? $package['@attributes']['name'] : ucfirst($package['slug']) );

	}

	function owc_get_repo_type($package) {

		return( isset($package['@attributes']['type']) ? $package['@attributes']['type'] : 'plugin' );

	}

	function owc_get_repo_banner($package, $default = false) {
		
		return ( isset($package['banner']) ? $package['banner'] : $default );
	}

	function owc_get_repo_private($package, $default = false) {
		
		return ( isset($package['@attributes']['private']) ? $package['@attributes']['private'] : $default );
		
	}

	function owc_get_repo_readme($package) {
		
		$readme_url = $readme_content = false;
		
		if( isset($package['readme']) && $package['readme'] ) {
			$readme_url = $package['readme'];
		} else if( isset($package['uri']) ) {

			if( isset($package['@attributes']['host']) ) {

				switch ($package['@attributes']['host']) {
					case 'github':
						$branch 	= (isset($package['branch']) ? $package['branch'] : 'master'); 
						$readme_url = owc_get_github_readme($package['uri'], $branch);
						break;
					case 'bitbucket':
						$branch 	= (isset($package['branch']) ? $package['branch'] : 'master');
						$private 	= (isset($package['@attributes']['private']) && $package['@attributes']['private'] === 'true');

						if( $private ) { 
							$readme_content = owc_plugin_src_file_bitbucket($package['uri'], 'readme.txt', $branch);
						} else {
							$readme_url = owc_get_bitbucket_readme($package['uri'], $branch);
						}
						break;
					
					default:
						# code...
						break;
				}
				
			}

		}

		$readme = ($readme_content ? $readme_content : @file_get_contents($readme_url));

		if( $readme !== false ) {
			return $readme;
		}
		
		return '';

	}

	function owc_get_github_readme($uri, $branch = 'master') {

		$url_path = parse_url($uri, PHP_URL_PATH);

		return 'https://raw.githubusercontent.com' . $url_path . '/' . $branch . '/readme.txt';

	}

	function owc_get_bitbucket_readme($uri, $branch = 'master') {

		return $uri . '/raw/' . $branch . '/readme.txt';

	}

	function owc_get_repo_button_html($package) {
		$action = $class = $text = false;
		$slug 	= $package['slug'];
		$type 	= owc_get_repo_type($package);

		if( isset($package['installed']) && $package['installed'] == 1 ) {

			if( $package['active'] == 1 ) {
				if( $type == 'plugin' ) {
					$action = 'deactivate';
					$class 	= 'button-secondary';
					$text 	=  __('Deactivate plugin', 'owc-plugin');
				}
			} else {
				$action = 'activate';
				$class 	= 'button-primary';
				$text 	= ($type == 'theme' ? __('Activate theme', 'owc-plugin') : __('Activate plugin', 'owc-plugin'));
			}

		} else {

			$action = 'install';
			$class 	= 'button-secondary';
			$text 	= ($type == 'theme' ? __('Install theme', 'owc-plugin') : __('Install plugin', 'owc-plugin'));

		}

		if( $action ) {

			return '<a href="javascript:void(0)" data-slug="' . $slug . '" data-type="' . $type . '" data-action="' . $action . '" class="owc-plugin_repro--action button ' . $class . ' right">' . $text . '</a>';
			
		}

		return '';

	}

	function owc_plugin_translate( $string, $ucfirst = false ) {

		$translation_array = array(
			'description' 					=> __('description', 'owc-plugin'),
			'installation' 					=> __('installation', 'owc-plugin'),
			'changelog' 					=> __('changelog', 'owc-plugin'),
			'frequently asked questions' 	=> __('frequently asked questions', 'owc-plugin'),
			'install' 						=> __('installed', 'owc-plugin'),
			'activate' 						=> __('activated', 'owc-plugin'),
			'deactivate' 					=> __('deactivated', 'owc-plugin')
		);

		$new_string = (isset($translation_array[$string]) ? $translation_array[$string] : $string);

		return ($ucfirst ? ucfirst($new_string) : $new_string);

	}

	function owc_plugin_get_admin_notice($message, $status = 'error') {

		$html = '<div class="notice notice-' . $status . ' is-dismissible">';
        $html .= '<p>' . sanitize_text_field($message) . '</p>';
    	$html .= '</div>';

    	return $html;

	}

	function owc_plugin_encrypt_string( $string ) {

		$key 			= OWC_PLUGIN_AUTH_KEY;

		$ivlen 			= openssl_cipher_iv_length($cipher="AES-128-CBC");
		$iv 			= openssl_random_pseudo_bytes($ivlen);
		$ciphertext_raw = openssl_encrypt($string, $cipher, $key, OPENSSL_RAW_DATA, $iv);
		$hmac 			= hash_hmac('sha256', $ciphertext_raw, $key, true);

		return base64_encode( $iv.$hmac.$ciphertext_raw );

	}

	function owc_plugin_decrypt_string( $string ) {

		$key 				= OWC_PLUGIN_AUTH_KEY;

		$c 					= base64_decode($string);
		$ivlen 				= openssl_cipher_iv_length($cipher="AES-128-CBC");
		$iv 				= substr($c, 0, $ivlen);
		$hmac 				= substr($c, $ivlen, $sha2len=32);
		$ciphertext_raw 	= substr($c, $ivlen+$sha2len);
		$original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $key, OPENSSL_RAW_DATA, $iv);
		$calcmac 			= hash_hmac('sha256', $ciphertext_raw, $key, true);
		
		if( hash_equals($hmac, $calcmac) ) {
		
		    return $original_plaintext;
		
		}

	}


	function owc_plugin_update_option( $field_name, $field_value = '', $secure = false ) {

		if( $secure ) {
			$field_value = ($field_value && $field_value !== '' ? owc_plugin_encrypt_string($field_value) : '');
		}


		update_option('owc_plugin_' . $field_name, $field_value);

	}

	function owc_plugin_get_option( $field_name, $secure = false ) {

		$value = '';
		if( isset($_POST[$field_name]) ) {

			$value = $_POST[$field_name];

		} else {

			$field_value = get_option('owc_plugin_' . $field_name);

			$value = ($secure && $field_value && $field_value !== '' ? owc_plugin_decrypt_string($field_value) : $field_value);

		}

		if( owc_plugin_value_is_json($value) ) {
			return json_decode($value, true);
		}

		return $value;

	}

	function owc_plugin_value_is_json($string) {
		
		json_decode($string);
		
		return (json_last_error() == JSON_ERROR_NONE);

	}