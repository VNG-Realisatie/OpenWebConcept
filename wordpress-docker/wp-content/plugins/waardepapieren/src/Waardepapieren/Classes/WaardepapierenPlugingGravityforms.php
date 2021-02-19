<?php

namespace OWC\Waardepapieren\Classes;

class WaardepapierenPlugingGravityforms
{
    public function __construct()
    {
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

        // TODO go to a response page with download buttons...
//        if ($_POST["format"] == "png") {
//            header("Cache-Control: public"); // needed for internet explorer
//            header("Content-Type: image/png");
//            header("Content-Transfer-Encoding: Binary");
//            header("Content-Disposition: attachment; filename=claim_" . $body["id"] . ".png");
//            $image = explode(",", $body['image']);
//            echo base64_decode($image[1]);
//            die;
//        } else {
//            header("Cache-Control: public"); // needed for internet explorer
//            header("Content-Type: application/pdf");
//            header("Content-Transfer-Encoding: Binary");
//            header("Content-Disposition: attachment; filename=claim_" . $body["id"] . ".pdf");
//            $document = explode(",", $body['document']);
//            echo base64_decode($document[1]);
//            die;
//        }
    }
}
