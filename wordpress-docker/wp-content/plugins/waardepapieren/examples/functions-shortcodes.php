<?php

/**
 * The Waardepapieren short code (for example purposes)
 */
function waardepapieren_form_shortcode()
{
    // do something to $content
    // always return
    $url = esc_url(admin_url('admin-post.php'));
    $formtag = "<form action=\"" . $url . "\" method=\"post\">";
    return $formtag . file_get_contents(plugin_dir_url(__FILE__) . 'public/form.php');
}


function waardepapieren_list_shortcode()
{
    // do something to $content
    // always return
    $url = esc_url(admin_url('admin-post.php'));
    $formtag = "<form action=\"" . $url . "\" method=\"post\">";
    return $formtag . file_get_contents(plugin_dir_url(__FILE__) . 'public/list.php');
}


/**
 * Catching the custom post
 */

function waardepapieren_post()
{
    $organization = get_option('waardepapieren_organization');
    $key = get_option('waardepapieren_api_key');
    $endpoint = get_option('waardepapieren_api_endpoint');
    //var_dump($organization);
    //var_dump($key);
    //var_dump($endpoint);
    //var_dump($_POST);

    $post = ["person" => $_POST["bsn"], "type" => $_POST["type"], "organization" => $organization];

    $data = wp_remote_post($endpoint, array(
        'headers'     => array('Content-Type' => 'application/json; charset=utf-8', 'Authorization' => $key),
        'body'        => json_encode($post),
        'method'      => 'POST',
        'data_format' => 'body',
    ));

    $body     = json_decode(wp_remote_retrieve_body($data), true);

    if ($_POST["format"] == "png") {
        header("Cache-Control: public"); // needed for internet explorer
        header("Content-Type: image/png");
        header("Content-Transfer-Encoding: Binary");
        header("Content-Disposition: attachment; filename=claim_" . $body["id"] . ".png");
        $image = explode(",", $body['image']);
        echo base64_decode($image[1]);
        die;
    } else {
        header("Cache-Control: public"); // needed for internet explorer
        header("Content-Type: application/pdf");
        header("Content-Transfer-Encoding: Binary");
        header("Content-Disposition: attachment; filename=claim_" . $body["id"] . ".pdf");
        $document = explode(",", $body['document']);
        echo base64_decode($document[1]);
        die;
    }
}
