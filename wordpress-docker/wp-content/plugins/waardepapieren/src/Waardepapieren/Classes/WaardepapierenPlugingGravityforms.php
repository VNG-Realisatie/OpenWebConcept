<?php

namespace OWC\Waardepapieren\Classes;

use OWC\Waardepapieren\Foundation\Plugin;

class WaardepapierenPlugingGravityforms
{
    protected $plugin;

    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;

        add_shortcode('waardepapieren-result', [$this, 'waardepapieren_result_shortcode']);

        add_action('gform_loaded', [$this, 'load'], 5, 0);
        // Handle Gravity Form post:
        add_action('gform_after_submission', function ($entry, $form) {
            foreach ($form['fields'] as $field) {
                switch ($field['type']) {
                    case 'waardepapier':
                        $type = rgar($entry, (string) $field->id);
                        break;
                    case 'person':
                        $bsn = rgar($entry, (string) $field->id);
                        break;
                }
            }

            if (!isset($type) || !isset($bsn)){
                return;
            }

            $organization = get_option('waardepapieren_organization');

            $data = [
                "person" => "https://waardepapieren-gemeentehoorn.commonground.nu/api/v1/brp/ingeschrevenpersonen/".$bsn,
                "type" => $type,
                "organization" => $organization
            ];

            $this->waardepapieren_post($data);
        }, 10, 2);
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

    /**
     * Handles post from this Gravity Form that uses the advanced fields Waardepapier Person and Waardepapier Type.
     *
     * @param array $data should contain an array with a person, type and organization value.
     */
    function waardepapieren_post($data)
    {
        $key = get_option('waardepapieren_api_key');
        $endpoint = get_option('waardepapieren_api_endpoint');

        //Do Post
        $data = wp_remote_post($endpoint, array(
            'headers'     => array('Content-Type' => 'application/json; charset=utf-8', 'Authorization' => $key),
            'body'        => json_encode($data),
            'method'      => 'POST',
            'data_format' => 'body',
        ));

        //Response body
        $body     = json_decode(wp_remote_retrieve_body($data), true);

//        var_dump($body);die();
        var_dump(do_shortcode('[waardepapieren-result test="'.$body['@id'].'"]'));

        // TODO go to a response page with download buttons...
    }

    public function waardepapieren_result_shortcode($test): string
    {
//        $test['test'] = 'hoi';

        return $test['test'] . file_get_contents($this->plugin->getRootPath() . '/src/Waardepapieren/public/result.php');
    }
}
