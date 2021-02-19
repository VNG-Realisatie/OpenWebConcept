<?php

add_action('gform_after_submission', function ($entry, $form) {
    foreach ($form['fields'] as $field) {
        if ($field['type'] === 'waardepapier') {
            $type = rgar($entry, (string) $field->id);
        }
    }

    if (empty($type)) {
        return;
    }

    $organization = get_option('waardepapieren_organization');

    $body = [
        "person" => "https://waardepapieren-gemeentehoorn.commonground.nu/api/v1/brp/ingeschrevenpersonen/900198424",
        "type" => $type,
        "organization" => $organization
    ];

    waardepapieren_post($body);
}, 10, 2);
