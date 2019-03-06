<?php
	class OwcPluginInstaller {

		/**
		 * Holds the current dependency's slug.
		 *
		 * @var string $current_slug
		 */
		protected $current_slug;

		public function get_package_slug( $package, $default = false ) {

			return $this->get_package_value( 'slug', $package, $default );

		}

		public function get_package_branch( $package, $default = false ) {

			return $this->get_package_value( 'branch', $package, $default );

		}

		public function get_package_uri( $package, $default = false ) {

			return $this->get_package_value( 'uri', $package, $default );

		}

		public function get_package_method( $package, $default = false ) {

			return $this->get_package_value( 'method', $package, $default );

		}

		public function get_package_title( $package ) {

			$plugin_title 	= ucfirst( $this->get_package_slug($package, 'plugin') );
			$attributes 	= $this->get_package_attribute($package);

			return $this->get_package_value( 'name', $attributes, $plugin_title );

		}

		public function get_package_host( $package, $default = false ) {

			$attributes = $this->get_package_attribute($package);
			
			return $this->get_package_value( 'host', $attributes, $default );

		}

		public function get_package_private( $package, $default = false ) {

			$attributes = $this->get_package_attribute($package);
			
			return $this->get_package_value( 'private', $attributes, $default );

		}

		public function plugin_is_installed( $slug ) {
			
			$plugins = get_plugins();

			return isset( $plugins[$slug] );

		}

		/**
		 * Correctly rename dependency for activation.
		 *
		 * @param string $source
		 * @param string $remote_source
		 *
		 * @return string $new_source
		 */
		public function upgrader_source_selection( $source, $remote_source ) {

			global $wp_filesystem;
			
			$new_source = trailingslashit( $remote_source ) . $this->current_slug;

			$wp_filesystem->move( $source, $new_source );

			return trailingslashit( $new_source );

		}

		public function install_plugin( $package ) {
			
			$slug 	= $this->get_package_slug($package);
			$title 	= $this->get_package_title($package);

			if ( $slug && $this->plugin_is_installed( $slug ) || ! current_user_can( 'update_plugins' ) ) {
				return false;
			}

			$this->current_slug = $slug;
			$download_link 		= $this->get_download_link( $package );
			add_filter( 'upgrader_source_selection', array( &$this, 'upgrader_source_selection' ), 10, 2 );

			$skin     = new OwcPluginInstallerSkin(
				array(
					'type'  => 'plugin',
					'nonce' => wp_nonce_url( $download_link )
				)
			);

			$upgrader = new Plugin_Upgrader( $skin );
			$result   = $upgrader->install( $download_link );

			if ( is_wp_error( $result ) ) {
				
				return array(
					'status'  => 'error',
					'message' => $result->get_error_message(),
				);

			}
			wp_cache_flush();

			if( $result === true ) {
				
				return array(
					'status'  => 'success',
					'message' => sprintf( esc_html__( '%s has been installed', 'owc-plugin' ), $title )
				);

			} else {

				return array(
					'status'  => 'error',
					'message' => sprintf( esc_html__('%s could not be installed', 'owc-plugin'), $title )
				);

			}


		}

		public function get_download_link( $package ) {

			$host 		= $this->get_package_host($package, 'direct');
			$private 	= $this->get_package_private($package, false);
			$branch 	= $this->get_package_branch($package, 'master');
			$uri 		= $this->get_package_uri($package, 'master');
			$method 	= $this->get_package_method($package, 'get');

			
			switch ($host) {
				case 'direct':
					return $uri;
					break;
				case 'github':
					return rtrim($uri) . '/archive/' . $branch . '.zip';
					break;
				case 'bitbucket':
					if( $private === 'true' ) {
						return $this->pre_download_from_bitbucket($uri, $branch, $method);					
					} else {
						return rtrim($uri) . '/archive/' . $branch . '.zip';
					}
					break;
			}

			return 'https://github.com/woocommerce/woocommerce';

		}

		public function pre_download_from_bitbucket($uri, $branch, $method) {

			$tmp_name 		= 'owc-tmp-' . $this->get_random_string() . '-' . $branch . '.zip'; 
			$destination 	= WP_CONTENT_DIR . '/upgrade/' . $tmp_name;
			$downloaded 	= owc_plugin_download_file_bitbucket($uri, $method, $branch, $destination);
			
			if( $downloaded ) {
				
				return content_url( '/upgrade/' . $tmp_name );

			}

			return false;

		}

		public function get_package_value( $key, $package, $default = false ) {
			return (isset($package[$key]) ? $package[$key] : $default);
		}

		public function get_package_attribute( $package, $default = array() ) {
			return (isset($package['@attributes']) ? $package['@attributes'] : $default);
		}

		public function get_random_string($length = 10) {
			
			$characters 		= '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$charactersLength 	= strlen($characters);
			$randomString 		= '';
			
			for ($i = 0; $i < $length; $i++) {
			
				$randomString .= $characters[rand(0, $charactersLength - 1)];
			
			}
			
			return $randomString;

		}

	}

	/**
	 * Class OwcPluginInstallerSkin
	 */
	class OwcPluginInstallerSkin extends Plugin_Installer_Skin {
		public function header() {}
		public function footer() {}
		public function error( $errors ) {}
		public function feedback( $string ) {}
	}