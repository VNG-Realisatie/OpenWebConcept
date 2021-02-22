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

        $_SESSION['certificate'] = $body;
    }

    public function waardepapieren_result_shortcode($test): string
    {
        $document = '';
        $x = true;
        $i = 0;
        while ($x) {
            if (isset($_SESSION['certificate']['document'][$i])){
                $document = $document . $_SESSION['certificate']['document'][$i];
            } else {
                $x = false;
            }
            $i++;
        }
        $documentButton = '<button style="margin-right: 15px"><a href="' . $document . '" download>download document</a></button>';

        $image = '';
        $x = true;
        $i = 0;
        while ($x) {
            if (isset($_SESSION['certificate']['image'][$i])){
                $image = $image . $_SESSION['certificate']['image'][$i];
            } else {
                $x = false;
            }
            $i++;
        }
        $imageButton = '<button style="margin-right: 15px"><a href="' . $image . '" download>download image</a></button>';

        $claim = '';
        $x = true;
        $i = 0;
        while ($x) {
            if (isset($_SESSION['certificate']['claim'][$i])){
                $claim = $claim . $_SESSION['certificate']['claim'][$i];
            } else {
                $x = false;
            }
            $i++;
        }
        $claim = base64_encode(json_encode($claim));
        $claimButton = '<button><a href="data:application/json;base64,' . $claim . '" download>download claim</a></button>';

        return '<div style="text-align: center">' . $imageButton . $documentButton . $claimButton . '</div>';
    }
}
