<?php
/**
 * The Waardepapieren extension for grafit forms
 */

GFForms::include_addon_framework();

class GFWaarepapierAddOn extends GFAddOn {

    protected $_version = WAARDEPAPIEREN_PLUGIN_VERSION;
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
                    array(
                        'label'   => esc_html__( 'Person', 'waardepapierenaddon' ),
                        'type'    => 'select',
                        'name'    => 'person',
                        'tooltip' => esc_html__( 'Select a person for wich to require a certificate', 'waardepapierenaddon' ),
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
                        'tooltip' => esc_html__( 'The certificate to be required', 'waardepapierenaddon' ),
                        'choices' => array(
                            array(
                                'label' => esc_html__( 'Akte van geboorte', 'waardepapierenaddon' ),
                                'value' => 'akte_van_geboorte',
                            ),
                            array(
                                'label' => esc_html__( 'Akte van huwelijk', 'waardepapierenaddon' ),
                                'value' => 'akte_van_huwelijk',
                            ),
                            array(
                                'label' => esc_html__( 'Akte van overlijden', 'waardepapierenaddon' ),
                                'value' => 'akte_van_overlijden',
                            ),
                            array(
                                'label' => esc_html__( 'Akte van registratie van een partnershap', 'waardepapierenaddon' ),
                                'value' => 'akte_van_registratie_van_een_partnerschap',
                            ),
                            array(
                                'label' => esc_html__( 'Akte van omzetting van een huwelijk in een registratie van een partnerschap', 'waardepapierenaddon' ),
                                'value' => 'akte_van_omzetting_van_een_huwelijk_in_een_registratie_van_een_partnerschap',
                            ),
                            array(
                                'label' => esc_html__( 'Akte van omzetting van een registratie van een partnerschap', 'waardepapierenaddon' ),
                                'value' => 'akte_van_omzetting_van_een_registratie_van_een_partnerschap',
                            ),
                            array(
                                'label' => esc_html__( 'Verklaring diploma\'s', 'waardepapierenaddon' ),
                                'value' => 'verklaring_diplomas',
                            ),
                            array(
                                'label' => esc_html__( 'Verklaring inkomen', 'waardepapierenaddon' ),
                                'value' => 'verklaring_inkomen',
                            ),
                            array(
                                'label' => esc_html__( 'Verklaring studieschuld', 'waardepapierenaddon' ),
                                'value' => 'verklaring_studieschuld',
                            ),
                            array(
                                'label' => esc_html__( 'Verklaring van huwelijksbevoegdheid', 'waardepapierenaddon' ),
                                'value' => 'verklaring_van_huwelijksbevoegdheid',
                            ),
                            array(
                                'label' => esc_html__( 'Verklaring van in leven zijn', 'waardepapierenaddon' ),
                                'value' => 'verklaring_van_in_leven_zijn',
                            ),
                            array(
                                'label' => esc_html__( 'Verklaring van nederlandershap', 'waardepapierenaddon' ),
                                'value' => 'verklaring_van_nederlandershap',
                            ),
                            array(
                                'label' => esc_html__( 'Uittreksel basis registratie personen', 'waardepapierenaddon' ),
                                'value' => 'uittreksel_basis_registratie_personen',
                            ),
                            array(
                                'label' => esc_html__( 'Uittreksel registratie niet ingezetenen', 'waardepapierenaddon' ),
                                'value' => 'uittreksel_registratie_niet_ingezetenen',
                            ),
                            array(
                                'label' => esc_html__( 'Historisch uittreksel basis registratie personen', 'waardepapierenaddon' ),
                                'value' => 'historisch_uittreksel_basis_registratie_personen',
                            ),
                        ),
                    ),
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
