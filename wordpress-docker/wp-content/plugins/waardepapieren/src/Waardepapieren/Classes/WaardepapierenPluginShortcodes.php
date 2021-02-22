<?php

namespace OWC\Waardepapieren\Classes;

use OWC\Waardepapieren\Foundation\Plugin;

class WaardepapierenPluginShortcodes
{
    /** @var Plugin */
    protected $plugin;

    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;

        // The actual short codes
        add_shortcode('waardepapieren-form', [$this, 'waardepapieren_form_shortcode']);
        add_shortcode('waardepapieren-list', [$this, 'waardepapieren_list_shortcode']);

        // Form handling
        add_action('admin_post_nopriv_waardepapieren_form', [$this, 'waardepapieren_post']);
        add_action('admin_post_waardepapieren_form', [$this, 'waardepapieren_post']);
    }

    public function waardepapieren_form_shortcode(): string
    {
        $url = esc_url(admin_url('admin-post.php'));
        $formtag = '<form action="' . $url . '" method="post">';

        return $formtag . file_get_contents($this->plugin->getRootPath() . '/src/Waardepapieren/public/form.php');
    }

    public function waardepapieren_list_shortcode(): string
    {
        $url = esc_url(admin_url('admin-post.php'));
        $formtag = '<form action="' . $url . '" method="post">';

        return $formtag . file_get_contents($this->plugin->getRootPath() . '/src/Waardepapieren/public/list.php');
    }

    /**
     * Catching the custom post
     */
    public function waardepapieren_post()
    {
        $organization = get_option('waardepapieren_organization');
        $key          = get_option('waardepapieren_api_key');
        $endpoint     = get_option('waardepapieren_api_endpoint');

        $post = ["person" => $_POST["bsn"], "type" => $_POST["type"], "organization" => $organization];

        $data = wp_remote_post($endpoint, array(
            'headers'     => array('Content-Type' => 'application/json; charset=utf-8', 'Authorization' => $key),
            'body'        => json_encode($post),
            'method'      => 'POST',
            'data_format' => 'body',
        ));

        $body = json_decode(wp_remote_retrieve_body($data), true);

        if ($_POST["format"] == "png") {
            header("Cache-Control: public"); // needed for internet explorer
            header("Content-Type: image/png");
            header("Content-Transfer-Encoding: Binary");
            header("Content-Disposition: attachment; filename=claim_" . $body["id"] . ".png");
            $image = explode(",", $body['image']);
            echo base64_decode($image[1]);
            die;
        }

        header("Cache-Control: public"); // needed for internet explorer
        header("Content-Type: application/pdf");
        header("Content-Transfer-Encoding: Binary");
        header("Content-Disposition: attachment; filename=claim_" . $body["id"] . ".pdf");
        $document = explode(",", $body['document']);
        echo base64_decode($document[1]);
        die;
    }
}
