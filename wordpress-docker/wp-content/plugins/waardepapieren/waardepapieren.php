<?php

/**
 * Waardepapieren
 *
 * @package           WaardenpapierenPlugin
 * @author            Conduction
 * @copyright         2020 Conduction
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Waardepapieren
 * Plugin URI:        https://conduction.nl/waardepapieren
 * Description:       De waardepapieren plugin
 * Version:           1.0.8
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Conduction
 * Author URI:        https://conduction.nl
 * Text Domain:       plugin-slug
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! defined('WAARDEPAPIEREN_PLUGIN_VERSION')) define('WAARDEPAPIEREN_PLUGIN_VERSION', '1.0.0');
if ( ! defined('WAARDEPAPIEREN_PLUGIN_FILE')) define('WAARDEPAPIEREN_PLUGIN_FILE', __FILE__);
if ( ! defined('WAARDEPAPIEREN_PLUGIN_DIR')) define('WAARDEPAPIEREN_PLUGIN_DIR', dirname(__FILE__));
if ( ! defined('WAARDEPAPIEREN_PLUGIN_BASENAME')) define('WAARDEPAPIEREN_PLUGIN_BASENAME', basename(WAARDEPAPIEREN_PLUGIN_DIR));
if ( ! defined('WAARDEPAPIEREN_PLUGIN_URL')) define('WAARDEPAPIEREN_PLUGIN_URL', plugins_url( WAARDEPAPIEREN_PLUGIN_BASENAME ));
if ( ! defined('WAARDEPAPIEREN_PLUGIN_AUTH_KEY')) define('WAARDEPAPIEREN_PLUGIN_AUTH_KEY', AUTH_KEY);

class WaardepapierenPlugin {

    public function __construct()
    {
        // The function file
        require_once WAARDEPAPIEREN_PLUGIN_DIR . '/functions-shortcodes.php';

        // Include the init class
        include WAARDEPAPIEREN_PLUGIN_DIR . '/classes/class-waardepapieren-plugin-shortcodes.php';

        // The function file
        require_once WAARDEPAPIEREN_PLUGIN_DIR . '/functions-admin.php';

        // Include the admin settings
        include WAARDEPAPIEREN_PLUGIN_DIR . '/classes/class-waardepapieren-plugin-admin-settings.php';

        // Include the admin settings
        include WAARDEPAPIEREN_PLUGIN_DIR . '/classes/class-field-waardepapier.php';

        // The function file
        require_once WAARDEPAPIEREN_PLUGIN_DIR . '/functions-grafityforms.php';

        // Include the admin settings
        include WAARDEPAPIEREN_PLUGIN_DIR . '/classes/class-waardepapieren-plugin-grafityforms.php';
    }

}
$GLOBALS['waardepapieren-plugin'] = new WaardepapierenPlugin();
