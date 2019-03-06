<?php
	class OwcPluginInit {

		public function __construct() {

			add_action( 'init', array($this, 'init') );

			// Enqueue admin styles and scripts
			add_action( 'admin_enqueue_scripts', array($this, 'admin_enqueue_scripts') );

			// add ajax import action
			add_action( 'wp_ajax_get_owc_plugin_readme_html', array($this, 'get_owc_plugin_readme_html') );

			// add ajax filter repo list
			add_action( 'wp_ajax_get_owc_repo_list_html', array($this, 'get_owc_repo_list_html') );

			// add package action
			add_action( 'wp_ajax_owc_plugin_run_package_actions', array($this, 'owc_plugin_run_package_actions') );

			// add bitbucket authentication action
			add_action( 'wp_ajax_owc_plugin_authenticate_bitbucket', array($this, 'owc_plugin_authenticate_bitbucket') );

			// Load translation files
			add_action( 'plugins_loaded', array($this, 'owc_load_textdomain') );

		}

		public function init() {

			if ( ! session_id() ) {
				session_start();    
			}

			require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

			// Include the installer class
			require_once OWC_PLUGIN_DIR . '/classes/class-owc-plugin-installer.php';

		}

		public function admin_enqueue_scripts() {

			// Enqueue the plugins admin style
			wp_enqueue_style( 'owc_plugin_style', OWC_PLUGIN_URL . '/assets/css/admin/style.css', false, OWC_PLUGIN_VERSION );

			// Enqueue the plugins admin script
			wp_enqueue_script( 'owc_plugin_script', OWC_PLUGIN_URL. '/assets/js/admin/base.js', array('jquery'), OWC_PLUGIN_VERSION, true );
			wp_localize_script( 'owc_plugin_script', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );

		}

		public function get_owc_plugin_readme_html() {

			$slug 			= sanitize_text_field($_POST['slug']);
			$repo_array 	= owc_get_repo_array(false);
			if( isset($repo_array['Packages'][$slug]) && !empty($repo_array['Packages'][$slug]) ) {

				$package 		= $repo_array['Packages'][$slug];

				$title 			= owc_get_repo_title($package);
				$type 			= owc_get_repo_type($package);
				$banner 		= owc_get_repo_banner($package);
				$readme 		= owc_get_repo_readme($package);
				$private 		= owc_get_repo_private($package);

				$readme_tabs 	= OwcPluginReadmeConverter::convert($readme);
				$readme_info 	= '';

				if( isset($readme_tabs['info']) ) {
					$readme_info = $readme_tabs['info'];
					unset($readme_tabs['info']);
				}

				include OWC_PLUGIN_DIR . '/views/admin/view-owc-plugin-repo-readme-html.php';

			}

			wp_die();

		}

		public function get_owc_repo_list_html() {

			$filter 		= (isset($_POST['search']) ? sanitize_text_field($_POST['search']) : false);
			$repo_array 	= owc_get_repo_array(true, $filter);

			include OWC_PLUGIN_DIR . '/views/admin/view-owc-plugin-repo-list.php';

			wp_die();
			
		}

		public function owc_plugin_run_package_actions() {

			$allowed_actions = array(
				'deactivate',
				'activate',
				'install'
			);

			$action 	= sanitize_text_field($_POST['action_type']);
			$packages 	= $_POST['packages'];
			$results 	= array();

			if( in_array($action, $allowed_actions) ) {
				
				// Set function name
				$function 	= 'owc_plugin_package_action_' . $action;
				$results 	= array(
					'status' 	=> 'success',
					'message' 	=> ''
				);

				// Loop through the packages 
				foreach($packages as $package) {
					
					$package_status = $this->{$function}($package, ($action !== 'install'));

					if( $package_status && isset($package_status['status']) && isset($package_status['message']) && $package_status['status'] == 'success' ) {
						
						$results['message'] .= $package_status['message'];
					
					} else if( $action == 'install' ) {

						$results = $package_status;

					}

				}

			} else {

				$results = array(
					'status' 	=> 'error',
					'message' 	=> owc_plugin_get_admin_notice(__('No valid action found to execute!', 'owc-plugin'), 'error')
				);

			}

			echo json_encode($results);

			wp_die();

		}

		public function owc_plugin_authenticate_bitbucket() {

			if( empty($_GET['state']) || !isset($_SESSION['oauth2state']) || ($_GET['state'] !== $_SESSION['oauth2state']) ) {

				unset($_SESSION['oauth2state']);
				wp_die('Invalid state');

			} else if( isset($_GET['code']) && $_GET['code'] !== '' ) {

				$provider = owc_get_provider_bitbucket();

				try {

					 // Try to get an access token (using the authorization code grant)
					$token = $provider->getAccessToken('authorization_code', [
						'code' => $_GET['code']
					]);

					// Save the
					owc_plugin_save_token_bitbucket($token);

				} catch( Exception $e ) {

					// Failed to get user details
					wp_die('Er is een fout opgetreden tijdens het ophalen van de token');
				}

				wp_redirect( admin_url( 'tools.php?page=owc_plugin_repository&tab=settings' ) );

			}

			exit;

		}

		public function owc_load_textdomain() {

			load_plugin_textdomain( 'owc-plugin', FALSE, OWC_PLUGIN_BASENAME . '/languages/' );

		}

		public function owc_plugin_package_action_deactivate($package, $suppress_error = false) {
			
			$plugins = owc_get_all_plugins();
			
			if( isset($plugins[$package]['Slug']) && isset($plugins[$package]['Active']) && $plugins[$package]['Active'] == 1 ) {

				$slug 	= $plugins[$package]['Slug'];
				deactivate_plugins( $slug );

				$active_plugin = is_plugin_active($slug);
				
				if( !$active_plugin ) {

					$plugin_title 	= (isset($plugins[$package]['Name']) ? $plugins[$package]['Name'] : 'Plugin');
					$message 		= sprintf(__('%s successfully deactivated', 'owc-plugin'), $plugin_title);

					return array(
						'status' 	=> 'success',
						'message' 	=> owc_plugin_get_admin_notice($message, 'success')
					);

				}

			}

			if( !$suppress_error ) {

				$plugin_title 	= (isset($plugins[$package]['Name']) ? $plugins[$package]['Name'] : ucfirst($package));
				$message 		= sprintf(__('%s could not be deactivated', 'owc-plugin'), $plugin_title);

				return array(
					'status' 	=> 'error',
					'message' 	=> owc_plugin_get_admin_notice($message, 'error')
				);
			}

		}

		public function owc_plugin_package_action_activate($package, $suppress_error = false) {
			
			$plugins = owc_get_all_plugins();
			
			if( isset($plugins[$package]['Slug']) && isset($plugins[$package]['Active']) && $plugins[$package]['Active'] !== 1 ) {

				$slug 	= $plugins[$package]['Slug'];
				activate_plugins( $slug );

				$active_plugin = is_plugin_active($slug);
				
				if( $active_plugin ) {

					$plugin_title 	= (isset($plugins[$package]['Name']) ? $plugins[$package]['Name'] : 'Plugin');
					$message 		= sprintf(__('%s successfully activated', 'owc-plugin'), $plugin_title);

					return array(
						'status' 	=> 'success',
						'message' 	=> owc_plugin_get_admin_notice($message, 'success')
					);

				}

			}

			if( !$suppress_error ) {

				$plugin_title 	= (isset($plugins[$package]['Name']) ? $plugins[$package]['Name'] : ucfirst($package));
				$message 		= sprintf(__('%s could not be activated', 'owc-plugin'), $plugin_title);

				return array(
					'status' 	=> 'error',
					'message' 	=> owc_plugin_get_admin_notice($message, 'error')
				);
			}

		}

		public function owc_plugin_package_action_install($package, $suppress_error = false) {

			$packages 		= owc_get_repo_array(false);
			$plugin_title 	= ucfirst($package);
			$error_message 	= false;

			if( isset($packages['Packages'][$package]) ) {

				$the_package 	= $packages['Packages'][$package];
				$plugin_title 	= (isset($the_package['@attributes']['name']) ? $the_package['@attributes']['name'] : $plugin_title);

				if( isset($the_package['installed']) && !$the_package['installed'] ) {
					
					$installer 	= new OwcPluginInstaller();
					$type 		= (isset($the_package['@attributes']['type']) ? $the_package['@attributes']['type'] : 'plugin');

					if( $type == 'plugin' ) {

						$results = $installer->install_plugin( $the_package );

						if( is_array($results) && isset($results['status']) ) {

							if( $results['status'] == 'success' ) {
								
								$default_success 	= __('Plugin successfully installed', 'owc-plugin');
								$results['message'] = owc_plugin_get_admin_notice( (isset($results['message']) ? $results['message'] : $default_success), 'success' );
								
								return $results;

							} else {

								$error_message = (isset($results['message']) ? $results['message'] : false);

							}

						}

					}

				}

			}

			if( !$suppress_error ) {

				$message = ($error_message ? $error_message : sprintf(__('%s could not be installed', 'owc-plugin'), $plugin_title));

				return array(
					'status' 	=> 'error',
					'message' 	=> owc_plugin_get_admin_notice($message, 'error')
				);
			}

		}

	}
	new OwcPluginInit();