<?php
/**
 * The Waardepapieren short code (for example purposes)
 */

class WaardepapierenPlugingGafityforms {

    public function __construct() {
        add_action( 'gform_loaded', array( 'GF_Simple_AddOn_Bootstrap', 'load' ), 5 );
    }

    /*
     * Let register the plugin to grafity forms (if grafity forms is pressent and active)
     *
     */
    public static function load() {

        if ( ! method_exists( 'GFForms', 'include_addon_framework' ) ) {
            return;
        }

        require_once( 'class-gfwaardepapierenaddon.php' );

        GFAddOn::register( 'GFWaarepapierAddOn' );
    }

    function gf_simple_addon() {
        return GFWaarepapierAddOn::get_instance();
    }
}

$WaardepapierenPlugingGafityforms = new WaardepapierenPlugingGafityforms();