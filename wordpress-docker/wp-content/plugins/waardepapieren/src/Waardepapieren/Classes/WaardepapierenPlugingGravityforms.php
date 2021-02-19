<?php

namespace OWC\Waardepapieren\Classes;

class WaardepapierenPlugingGravityforms
{

    public function __construct()
    {
        add_action('gform_loaded', [$this, 'load'], 5, 0);
    }

    /*
     * Let register the plugin to grafity forms (if grafity forms is pressent and active)
     *
     */
    public static function load()
    {

        if (!method_exists('GFForms', 'include_addon_framework')) {
            return;
        }

        \GFAddOn::register(GFWaardepapierAddOn::class);
        \GF_Fields::register(new GFFieldWaardePapierType());
        \GF_Fields::register(new GFFieldWaardePapierPerson());
    }

    public function gf_simple_addon()
    {
        return GFWaardepapierAddOn::get_instance();
    }
}
