<?php
/*
 * Lets define the basic settings page
 */
function waardepapieren_options_page_html()
{
    // check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }
?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form action="options.php" method="post">
            <?php
            // output security fields for the registered setting "wporg_options"
            settings_fields('waardepapieren_options');
            // output setting sections and their fields
            // (sections are registered for "wporg", each field is registered to a specific section)
            do_settings_sections('waardepapieren_api');
            // output save settings button
            submit_button(__('Save Settings', 'textdomain'));
            ?>
        </form>
    </div>
<?php
}

/*
 * The settings menu item
 */
function waardepapieren_options_page()
{

    add_submenu_page(
        'options-general.php',
        'Waardepapieren',
        'Waardepapieren',
        'manage_options',
        'waardepapieren',
        'waardepapieren_options_page_html'
    );
}


/*
 * Lets define some settings
 */
function wporg_settings_init()
{
    // register a new setting for "reading" page
    register_setting('waardepapieren_options', 'waardepapieren_api_endpoint');
    register_setting('waardepapieren_options', 'waardepapieren_api_key');
    register_setting('waardepapieren_options', 'waardepapieren_organization');

    // register a new section in the "reading" page
    add_settings_section(
        'default', // id
        'API  Configuration', // title
        'wporg_settings_section_callback', // callback
        'waardepapieren_api' // page
    );

    // register a new field in the "wporg_settings_section" section, inside the "reading" page
    add_settings_field(
        'waardepapieren_api_endpoint_field', // id
        'API Endpoint',  // title
        'waardepapieren_api_endpoint_field_callback', //callback
        'waardepapieren_api',
        'default'
    );

    // register a new field in the "wporg_settings_section" section, inside the "reading" page
    add_settings_field(
        'waardepapieren_api_key_field',
        'API  KEY',
        'waardepapieren_api_key_field_callback',
        'waardepapieren_api',
        'default'
    );

    // register a new field in the "wporg_settings_section" section, inside the "reading" page

    add_settings_field(
        'waardepapieren_organization_field',
        'Organization',
        'waardepapieren_organization_field_callback',
        'waardepapieren_api',
        'default'
    );
}

/**
 * callback functions
 */

// section content cb
function wporg_settings_section_callback()
{
    echo '<p>In order to use the waardenpapieren api you wil need to provide api credentials.</p>';
}

// field content cb
function waardepapieren_api_endpoint_field_callback()
{
    // get the value of the setting we've registered with register_setting()
    $setting = get_option('waardepapieren_api_endpoint');
    // output the field
?>
    <input type="text" name="waardepapieren_api_endpoint" value="<?php echo isset($setting) ? esc_attr($setting) : ''; ?>">
<?php
}

function waardepapieren_api_key_field_callback()
{
    // get the value of the setting we've registered with register_setting()
    $setting = get_option('waardepapieren_api_key');
    // output the field
?>
    <input type="text" name="waardepapieren_api_key" value="<?php echo isset($setting) ? esc_attr($setting) : ''; ?>">
<?php
}

function waardepapieren_organization_field_callback()
{
    // get the value of the setting we've registered with register_setting()
    $setting = get_option('waardepapieren_organization');
    // output the field
?>
    <input type="text" name="waardepapieren_organization" value="<?php echo isset($setting) ? esc_attr($setting) : ''; ?>">
<?php
}
