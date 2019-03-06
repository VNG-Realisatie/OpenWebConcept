<?php
	class OwcPluginAdminSettings {

		public function __construct() {

			add_action('admin_menu', array($this, 'add_settings_menu_item'), 20);

		}

		public function add_settings_menu_item() {

			add_submenu_page( 'tools.php', __('OWC - ', 'owc-plugin'), __('OWC - projecten', 'owc-plugin'), 'manage_options', 'owc_plugin_repository', array(&$this, 'load_settings_view') );

		}

		public function load_settings_view() {

			global $repo_array;
			$repo_array = owc_get_repo_array(true);

			if( isset($repo_array['Packages']) ) {
				
				if( $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] !== '' && $_POST['action'] ) {

					$action 		= str_replace('-selected', '', $_POST['action']);
					$nice_action 	= owc_plugin_translate($action, false);
					$return_url 	= admin_url( 'tools.php?page=owc_plugin_repository');

					include OWC_PLUGIN_DIR . '/views/admin/view-owc-plugin-repo-bulk.php';

				} else if( isset($_GET['tab']) && $_GET['tab'] == 'settings' ) {

					$alert_message = $this->save_setting();
				
					include OWC_PLUGIN_DIR . '/views/admin/view-owc-plugin-repo-settings.php';

				} else {
					
					include OWC_PLUGIN_DIR . '/views/admin/view-owc-plugin-repo-index.php';

				}

			} else {

				echo 'Op dit moment is het niet mogelijk om een lijst op te halen';

			}

		}

		public function save_setting() {

			if( isset($_POST['owc_action']) && $_POST['owc_action'] == 'save' ) {

				$secure_fields = array(
					'bitbucket_key',
					'bitbucket_secret'
				);
				$this->save_fields( $secure_fields, true );

				return owc_plugin_get_admin_notice( __('Settings successfully saved!', 'owc-plugin') ,'success' );

			}

			return false;

		}

		public function save_fields( $fields, $secure = false ) {

			foreach( $fields as $field_name ) {
				
				$value 		= (isset($_POST[$field_name]) ? sanitize_text_field($_POST[$field_name]) : '');

				owc_plugin_update_option($field_name, $value, $secure);

			}

		}

	}
	new OwcPluginAdminSettings();