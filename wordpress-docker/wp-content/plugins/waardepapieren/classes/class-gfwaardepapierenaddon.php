<?php
/**
 * The Waardepapieren extension for grafit forms
 */

GFForms::include_addon_framework();

class GFWaarepapierAddOn extends GFAddOn {

    protected $_version = GF_WAARDEPAPIEREN_ADDON_VERSION;
    protected $_min_gravityforms_version = '1.9';
    protected $_slug = 'waardepapieren';
    protected $_path = 'waardepapieren/waardepapieren.php';
    protected $_full_path = __FILE__;
    protected $_title = 'Gravity Forms Waardepapieren Add-On';
    protected $_short_title = 'Waardepapieren Add-On';

    private static $_instance = null;

    public static function get_instance() {
        if ( self::$_instance == null ) {
            self::$_instance = new GFWaarepapierAddOn();
        }

        return self::$_instance;
    }

    public function init() {
        parent::init();
        add_filter( 'gform_submit_button', array( $this, 'form_submit_button' ), 10, 2 );
    }

    public function scripts() {
        $scripts = array(
            array(
                'handle'  => 'my_script_js',
                'src'     => $this->get_base_url() . '/js/my_script.js',
                'version' => $this->_version,
                'deps'    => array( 'jquery' ),
                'strings' => array(
                    'first'  => esc_html__( 'First Choice', 'waardepapierenaddon' ),
                    'second' => esc_html__( 'Second Choice', 'waardepapierenaddon' ),
                    'third'  => esc_html__( 'Third Choice', 'waardepapierenaddon' )
                ),
                'enqueue' => array(
                    array(
                        'admin_page' => array( 'form_settings' ),
                        'tab'        => 'waardepapierenaddon'
                    )
                )
            ),

        );

        return array_merge( parent::scripts(), $scripts );
    }

    public function styles() {
        $styles = array(
            array(
                'handle'  => 'my_styles_css',
                'src'     => $this->get_base_url() . '/css/my_styles.css',
                'version' => $this->_version,
                'enqueue' => array(
                    array( 'field_types' => array( 'poll' ) )
                )
            )
        );

        return array_merge( parent::styles(), $styles );
    }

    function form_submit_button( $button, $form ) {
        $settings = $this->get_form_settings( $form );
        if ( isset( $settings['enabled'] ) && true == $settings['enabled'] ) {
            $text   = $this->get_plugin_setting( 'mytextbox' );
            $button = "<div>{$text}</div>" . $button;
        }

        return $button;
    }

    public function plugin_page() {
        echo 'This page appears in the Forms menu';
    }

    public function plugin_settings_fields() {
        return array(
            array(
                'title'  => esc_html__( 'Waardepapieren Add-On Settings', 'waardepapierenaddon' ),
                'fields' => array(
                    array(
                        'name'              => 'mytextbox',
                        'tooltip'           => esc_html__( 'This is the tooltip', 'waardepapierenaddon' ),
                        'label'             => esc_html__( 'This is the label', 'waardepapierenaddon' ),
                        'type'              => 'text',
                        'class'             => 'small',
                        'feedback_callback' => array( $this, 'is_valid_setting' ),
                    )
                )
            )
        );
    }

    public function form_settings_fields( $form ) {
        return array(
            array(
                'title'  => esc_html__( 'Waardepapieren Form Settings', 'waardepapierenaddon' ),
                'fields' => array(
                    /*
                    array(
                        'label'   => esc_html__( 'My checkbox', 'waardepapierenaddon' ),
                        'type'    => 'checkbox',
                        'name'    => 'enabled',
                        'tooltip' => esc_html__( 'This is the tooltip', 'waardepapierenaddon' ),
                        'choices' => array(
                            array(
                                'label' => esc_html__( 'Enabled', 'waardepapierenaddon' ),
                                'name'  => 'enabled',
                            ),
                        ),
                    ),
                    array(
                        'label'   => esc_html__( 'My checkboxes', 'waardepapierenaddon' ),
                        'type'    => 'checkbox',
                        'name'    => 'checkboxgroup',
                        'tooltip' => esc_html__( 'This is the tooltip', 'waardepapierenaddon' ),
                        'choices' => array(
                            array(
                                'label' => esc_html__( 'First Choice', 'waardepapierenaddon' ),
                                'name'  => 'first',
                            ),
                            array(
                                'label' => esc_html__( 'Second Choice', 'waardepapierenaddon' ),
                                'name'  => 'second',
                            ),
                            array(
                                'label' => esc_html__( 'Third Choice', 'waardepapierenaddon' ),
                                'name'  => 'third',
                            ),
                        ),
                    ),
                    array(
                        'label'   => esc_html__( 'My Radio Buttons', 'waardepapierenaddon' ),
                        'type'    => 'radio',
                        'name'    => 'myradiogroup',
                        'tooltip' => esc_html__( 'This is the tooltip', 'waardepapierenaddon' ),
                        'choices' => array(
                            array(
                                'label' => esc_html__( 'First Choice', 'waardepapierenaddon' ),
                            ),
                            array(
                                'label' => esc_html__( 'Second Choice', 'waardepapierenaddon' ),
                            ),
                            array(
                                'label' => esc_html__( 'Third Choice', 'waardepapierenaddon' ),
                            ),
                        ),
                    ),
                    array(
                        'label'      => esc_html__( 'My Horizontal Radio Buttons', 'waardepapierenaddon' ),
                        'type'       => 'radio',
                        'horizontal' => true,
                        'name'       => 'myradiogrouph',
                        'tooltip'    => esc_html__( 'This is the tooltip', 'waardepapierenaddon' ),
                        'choices'    => array(
                            array(
                                'label' => esc_html__( 'First Choice', 'waardepapierenaddon' ),
                            ),
                            array(
                                'label' => esc_html__( 'Second Choice', 'waardepapierenaddon' ),
                            ),
                            array(
                                'label' => esc_html__( 'Third Choice', 'waardepapierenaddon' ),
                            ),
                        ),
                    ),
                    */
                    array(
                        'label'   => esc_html__( 'Person', 'waardepapierenaddon' ),
                        'type'    => 'select',
                        'name'    => 'person',
                        'tooltip' => esc_html__( 'This is the tooltip', 'waardepapierenaddon' ),
                        'choices' => array(
                            array(
                                'label' => esc_html__( 'First Choice', 'waardepapierenaddon' ),
                                'value' => 'first',
                            ),
                            array(
                                'label' => esc_html__( 'Second Choice', 'waardepapierenaddon' ),
                                'value' => 'second',
                            ),
                            array(
                                'label' => esc_html__( 'Third Choice', 'waardepapierenaddon' ),
                                'value' => 'third',
                            ),
                        ),
                    ),
                    array(
                        'label'   => esc_html__( 'Certificate', 'waardepapierenaddon' ),
                        'type'    => 'select',
                        'name'    => 'certificate',
                        'tooltip' => esc_html__( 'This is the tooltip', 'waardepapierenaddon' ),
                        'choices' => array(
                            array(
                                'label' => esc_html__( 'First Choice', 'waardepapierenaddon' ),
                                'value' => 'first',
                            ),
                            array(
                                'label' => esc_html__( 'Second Choice', 'waardepapierenaddon' ),
                                'value' => 'second',
                            ),
                            array(
                                'label' => esc_html__( 'Third Choice', 'waardepapierenaddon' ),
                                'value' => 'third',
                            ),
                        ),
                    ),
                    /*
                    array(
                        'label'             => esc_html__( 'My Text Box', 'waardepapierenaddon' ),
                        'type'              => 'text',
                        'name'              => 'mytext',
                        'tooltip'           => esc_html__( 'This is the tooltip', 'waardepapierenaddon' ),
                        'class'             => 'medium',
                        'feedback_callback' => array( $this, 'is_valid_setting' ),
                    ),
                    array(
                        'label'   => esc_html__( 'My Text Area', 'waardepapierenaddon' ),
                        'type'    => 'textarea',
                        'name'    => 'mytextarea',
                        'tooltip' => esc_html__( 'This is the tooltip', 'waardepapierenaddon' ),
                        'class'   => 'medium merge-tag-support mt-position-right',
                    ),
                    array(
                        'label' => esc_html__( 'My Hidden Field', 'waardepapierenaddon' ),
                        'type'  => 'hidden',
                        'name'  => 'myhidden',
                    ),
                    array(
                        'label' => esc_html__( 'My Custom Field', 'waardepapierenaddon' ),
                        'type'  => 'my_custom_field_type',
                        'name'  => 'my_custom_field',
                        'args'  => array(
                            'text'     => array(
                                'label'         => esc_html__( 'A textbox sub-field', 'waardepapierenaddon' ),
                                'name'          => 'subtext',
                                'default_value' => 'change me',
                            ),
                            'checkbox' => array(
                                'label'   => esc_html__( 'A checkbox sub-field', 'waardepapierenaddon' ),
                                'name'    => 'my_custom_field_check',
                                'choices' => array(
                                    array(
                                        'label'         => esc_html__( 'Activate', 'waardepapierenaddon' ),
                                        'name'          => 'subcheck',
                                        'default_value' => true,
                                    ),
                                ),
                            ),
                        ),
                    ),
                    */
                ),
            ),
        );
    }

    public function settings_my_custom_field_type( $field, $echo = true ) {
        echo '<div>' . esc_html__( 'My custom field contains a few settings:', 'waardepapierenaddon' ) . '</div>';

        // get the text field settings from the main field and then render the text field
        $text_field = $field['args']['text'];
        $this->settings_text( $text_field );

        // get the checkbox field settings from the main field and then render the checkbox field
        $checkbox_field = $field['args']['checkbox'];
        $this->settings_checkbox( $checkbox_field );
    }

    public function is_valid_setting( $value ) {
        return strlen( $value ) > 5;
    }

}