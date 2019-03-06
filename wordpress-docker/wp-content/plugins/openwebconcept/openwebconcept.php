<?php
/**
 * Plugin Name: Openwebconcept plugin
 * Plugin URI: http://www.openwebconcept.nl/plugins/openwebconcept/
 * Description: Short description of this great plugin. No more than 150 characters, no markup
 * Version: 1.0.0
 * Author: S. Woudstra
 * Author URI: https://www.openwebconcept.nl
 * Requires at least: 4.8
 * Tested up to: 5.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! defined('OWC_PLUGIN_VERSION')) define('OWC_PLUGIN_VERSION', '1.0.0');

if ( ! defined('OWC_PLUGIN_FILE')) define('OWC_PLUGIN_FILE', __FILE__);
if ( ! defined('OWC_PLUGIN_DIR')) define('OWC_PLUGIN_DIR', dirname(__FILE__));
if ( ! defined('OWC_PLUGIN_BASENAME')) define('OWC_PLUGIN_BASENAME', basename(OWC_PLUGIN_DIR));
if ( ! defined('OWC_PLUGIN_URL')) define('OWC_PLUGIN_URL', plugins_url( OWC_PLUGIN_BASENAME ));
if ( ! defined('OWC_PLUGIN_AUTH_KEY')) define('OWC_PLUGIN_AUTH_KEY', AUTH_KEY); 

/**
 * Main Plugin Class
 *
 * @class OwcPlugin
 * @version  1.0.0
 */
class OwcPlugin {

	public function __construct() {

		if( !class_exists('Stevenmaguire\OAuth2\Client\Provider\Bitbucket') ) {
			require_once OWC_PLUGIN_DIR . '/vendor/autoload.php';
		}

		// Include the helper functions
		require_once OWC_PLUGIN_DIR . '/lib/helpers.php';
		require_once OWC_PLUGIN_DIR . '/lib/bitbucket.php';

		// Include readme converter class
		include OWC_PLUGIN_DIR . '/classes/class-owc-plugin-readme-converter.php';

		// Include the init class
		include OWC_PLUGIN_DIR . '/classes/class-owc-plugin-init.php';
		
		// Include the admin settings
		include OWC_PLUGIN_DIR . '/classes/class-owc-plugin-admin-settings.php';

		if( !class_exists('Parsedown') ) {
			// Include the parsedown class to convert markdown to html
			include OWC_PLUGIN_DIR . '/classes/class-owc-plugin-parsedown.php';
		}

	}

}
$GLOBALS['owc-plugin'] = new OwcPlugin();