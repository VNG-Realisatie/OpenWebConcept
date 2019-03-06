<?php
	function owc_is_authenticated_bitbucket() {

		global $owc_is_authenticated_bitbucket;

		if( $owc_is_authenticated_bitbucket !== NULL ) {

			return $owc_is_authenticated_bitbucket;

		} else {

			$provider 	= owc_get_provider_bitbucket();
			$token 		= owc_plugin_get_token_bitbucket( $provider );
			if( $token ) {
				
				$url = 'https://api.bitbucket.org/2.0/user';

				$request 	= $provider->getAuthenticatedRequest('GET', $url, $token);
				$content 	= false;
				try {
					
					$response 						= $provider->getResponse($request);
					$content 						= (string) $response->getBody();

					$owc_is_authenticated_bitbucket = true;

				} catch ( Exception $e ) {
					
					$owc_is_authenticated_bitbucket = false;

				}

				return $owc_is_authenticated_bitbucket;

			}

		}

		$owc_is_authenticated_bitbucket = false;

		return $owc_is_authenticated_bitbucket;

	}

	function owc_get_authentication_url_bitbucket() {

		$provider = owc_get_provider_bitbucket();

		$authUrl 					= $provider->getAuthorizationUrl();
		$_SESSION['oauth2state'] 	= $provider->getState();

		return $authUrl;

	}

	function owc_get_provider_bitbucket() {
		
		$provider = new Stevenmaguire\OAuth2\Client\Provider\Bitbucket([
			'clientId'          => 'LQ5gMvJKgFpbaVuJYR',
			'clientSecret'      => 'qpkHXuPTXUTVHHCntCzyrP7FBBenNj4A',
			'redirectUri'       => owc_get_callback_url_bitbucket()
		]);

		return $provider;

	}

	function owc_get_callback_url_bitbucket() {

		return add_query_arg( 'action', 'owc_plugin_authenticate_bitbucket', admin_url( 'admin-ajax.php' ) );

	}

	function owc_plugin_get_icon_bitbucket() {

		$auth = owc_is_authenticated_bitbucket();

		if( $auth ) {
			return '<span class="owc-plugin_icon__status--success">';
		}
		return '<span class="owc-plugin_icon__status--error">';

	}

	function owc_plugin_download_file_bitbucket($bitbucket_url, $method = 'downloads', $branch = 'master', $destination = false) {
		
		$provider 	= owc_get_provider_bitbucket();
		$token 		= owc_plugin_get_token_bitbucket( $provider );

		if( $token ) {
			$file_name 	= $branch . '.zip';
			$api_url 	= 'https://api.bitbucket.org/2.0/repositories/' . owc_plugin_get_path_bitbucket($bitbucket_url) . '/downloads/' . $file_name;
			$request 	= $provider->getAuthenticatedRequest('GET', $api_url, $token);
			$content 	= false;
			try {
				
				$response 	= $provider->getResponse($request);
				$content 	= (string) $response->getBody();

			} catch ( Exception $e ) {
				
				wp_die('Error downloading file');

			}

			if( $content && $content !== '' ) {
				
				if( $destination ) {
					file_put_contents($destination, $content);
					return $destination;
				}

			}

		}

		return false;

	}

	function owc_plugin_src_file_bitbucket($bitbucket_url, $file, $branch = 'master') {

		$provider 	= owc_get_provider_bitbucket();
		$token 		= owc_plugin_get_token_bitbucket( $provider );
		$content 	= '';

		if( $token ) {

			$api_url 	= 'https://api.bitbucket.org/2.0/repositories/' . owc_plugin_get_path_bitbucket($bitbucket_url) . '/src/' . $branch . '/' . $file;

			$request 	= $provider->getAuthenticatedRequest('GET', $api_url, $token);
			try {
				
				$response 	= $provider->getResponse($request);
				$content 	= (string) $response->getBody();

			} catch ( Exception $e ) {
				
				// Couldn't download src file maybe no access?

			}

		}

		return $content;

	}

	function owc_plugin_get_token_bitbucket( $provider ) {

		$token_params 	= owc_plugin_get_option('bitbucket_token_parameters', true);

		if( $token_params && is_array($token_params) ) {
			
			$token = new League\OAuth2\Client\Token\AccessToken($token_params);

			if( $token->hasExpired() ) {
				return owc_plugin_refresh_token_bitbucket($token, $provider);
			} else {
				return $token;
			}

		}

		return false;

	}

	function owc_plugin_refresh_token_bitbucket($token, $provider) {

		// Get a new token
		$new_token = $provider->getAccessToken('refresh_token', [
			'refresh_token' => $token->getRefreshToken()
		]);
		
		// Save the new token and return the object
		return owc_plugin_save_token_bitbucket($new_token);

	}

	function owc_plugin_save_token_bitbucket($token) {

		// Get an array of parameters set by the AccessToken object
		$parameters = $token->jsonSerialize();

		// Save the token encrypted in the database
		owc_plugin_update_option('bitbucket_token', $token->getToken(), true);
		owc_plugin_update_option('bitbucket_token_parameters', json_encode($parameters), true);

		return $token;

	}

	function owc_plugin_get_path_bitbucket( $bitbucket_url ) {

		$path = parse_url($bitbucket_url, PHP_URL_PATH);

		return ltrim($path, '/');

	}