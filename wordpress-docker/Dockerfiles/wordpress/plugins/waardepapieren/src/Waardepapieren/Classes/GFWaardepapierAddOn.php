<?php

namespace OWC\Waardepapieren\Classes;

/**
 * The Waardepapieren extension for gravity forms
 */
\GFForms::include_addon_framework();

class GFWaardepapierAddOn extends \GFAddOn
{
    protected $_version = '1.0.0';
    protected $_min_gravityforms_version = '1.9';
    protected $_slug = 'waardepapieren';
    protected $_path = 'waardepapieren/waardepapieren.php';
    protected $_full_path = __FILE__;
    protected $_title = 'Gravity Forms Waardepapieren Add-On';
    protected $_short_title = 'Waardepapieren Add-On';

    public function init(): void
    {
        parent::init();
        add_filter('gform_submit_button', [$this, 'form_submit_button'], 10, 2);
    }

    public function scripts(): array
    {
        $scripts = [
            [
                'handle'  => 'my_script_js',
                'src'     => $this->get_base_url() . '/../Assets/js/script.js',
                'version' => $this->_version,
                'deps'    => ['jquery'],
                'strings' => [
                    'first'  => esc_html__('First Choice', 'waardepapierenaddon'),
                    'second' => esc_html__('Second Choice', 'waardepapierenaddon'),
                    'third'  => esc_html__('Third Choice', 'waardepapierenaddon')
                ],
                'enqueue' => [
                    [
                        'admin_page' => ['form_settings'],
                        'tab'        => 'waardepapierenaddon'
                    ]
                ]
            ],

        ];

        return array_merge(parent::scripts(), $scripts);
    }

    public function styles(): array
    {
        $styles = [
            [
                'handle'  => 'my_styles_css',
                'src'     => $this->get_base_url() . '/../Assets/css/style.css',
                'version' => $this->_version,
                'enqueue' => [
                    ['field_types' => ['poll']]
                ]
            ]
        ];

        return array_merge(parent::styles(), $styles);
    }

    public function form_submit_button($button, $form): string
    {
        $settings = $this->get_form_settings($form);

        if (empty($settings['enabled']) || (isset($settings['enabled']) && !$settings['enabled'])) {
            return $button;
        }

        $text   = $this->get_plugin_setting('mytextbox');
        $button = "<div>{$text}</div>" . $button;

        return $button;
    }

    public function plugin_page(): void
    {
        echo 'This page appears in the Forms menu';
    }

    public function plugin_settings_fields(): array
    {
        return [
            [
                'title'  => esc_html__('Waardepapieren Add-On Settings', 'waardepapierenaddon'),
                'fields' => [
                    [
                        'name'              => 'mytextbox',
                        'tooltip'           => esc_html__('This is the tooltip', 'waardepapierenaddon'),
                        'label'             => esc_html__('This is the label', 'waardepapierenaddon'),
                        'type'              => 'text',
                        'class'             => 'small',
                        'feedback_callback' => [$this, 'is_valid_setting'],
                    ]
                ]
            ]
        ];
    }

    public function form_settings_fields($form): array
    {
        return [
            [
                'title'  => esc_html__('Waardepapieren Form Settings', 'waardepapierenaddon'),
                'fields' => [
                    [
                        'label'   => esc_html__('Person', 'waardepapierenaddon'),
                        'type'    => 'select',
                        'name'    => 'person',
                        'tooltip' => esc_html__('Select a person for wich to require a certificate', 'waardepapierenaddon'),
                        'choices' => [
                            [
                                'label' => esc_html__('First Choice', 'waardepapierenaddon'),
                                'value' => 'first',
                            ],
                            [
                                'label' => esc_html__('Second Choice', 'waardepapierenaddon'),
                                'value' => 'second',
                            ],
                            [
                                'label' => esc_html__('Third Choice', 'waardepapierenaddon'),
                                'value' => 'third',
                            ],
                        ],
                    ],
                    [
                        'label'   => esc_html__('Certificate', 'waardepapierenaddon'),
                        'type'    => 'select',
                        'name'    => 'certificate',
                        'tooltip' => esc_html__('The certificate to be required', 'waardepapierenaddon'),
                        'choices' => [
                            [
                                'label' => esc_html__('Akte van geboorte', 'waardepapierenaddon'),
                                'value' => 'akte_van_geboorte',
                            ],
                            [
                                'label' => esc_html__('Akte van huwelijk', 'waardepapierenaddon'),
                                'value' => 'akte_van_huwelijk',
                            ],
                            [
                                'label' => esc_html__('Akte van overlijden', 'waardepapierenaddon'),
                                'value' => 'akte_van_overlijden',
                            ],
                            [
                                'label' => esc_html__('Akte van registratie van een partnershap', 'waardepapierenaddon'),
                                'value' => 'akte_van_registratie_van_een_partnerschap',
                            ],
                            [
                                'label' => esc_html__('Akte van omzetting van een huwelijk in een registratie van een partnerschap', 'waardepapierenaddon'),
                                'value' => 'akte_van_omzetting_van_een_huwelijk_in_een_registratie_van_een_partnerschap',
                            ],
                            [
                                'label' => esc_html__('Akte van omzetting van een registratie van een partnerschap', 'waardepapierenaddon'),
                                'value' => 'akte_van_omzetting_van_een_registratie_van_een_partnerschap',
                            ],
                            [
                                'label' => esc_html__('Verklaring diploma\'s', 'waardepapierenaddon'),
                                'value' => 'verklaring_diplomas',
                            ],
                            [
                                'label' => esc_html__('Verklaring inkomen', 'waardepapierenaddon'),
                                'value' => 'verklaring_inkomen',
                            ],
                            [
                                'label' => esc_html__('Verklaring studieschuld', 'waardepapierenaddon'),
                                'value' => 'verklaring_studieschuld',
                            ],
                            [
                                'label' => esc_html__('Verklaring van huwelijksbevoegdheid', 'waardepapierenaddon'),
                                'value' => 'verklaring_van_huwelijksbevoegdheid',
                            ],
                            [
                                'label' => esc_html__('Verklaring van in leven zijn', 'waardepapierenaddon'),
                                'value' => 'verklaring_van_in_leven_zijn',
                            ],
                            [
                                'label' => esc_html__('Verklaring van nederlandershap', 'waardepapierenaddon'),
                                'value' => 'verklaring_van_nederlandershap',
                            ],
                            [
                                'label' => esc_html__('Uittreksel basis registratie personen', 'waardepapierenaddon'),
                                'value' => 'uittreksel_basis_registratie_personen',
                            ],
                            [
                                'label' => esc_html__('Uittreksel registratie niet ingezetenen', 'waardepapierenaddon'),
                                'value' => 'uittreksel_registratie_niet_ingezetenen',
                            ],
                            [
                                'label' => esc_html__('Historisch uittreksel basis registratie personen', 'waardepapierenaddon'),
                                'value' => 'historisch_uittreksel_basis_registratie_personen',
                            ]
                        ],
                    ],
                ],
            ],
        ];
    }

    public function settings_my_custom_field_type(array $field, bool $echo = true): void
    {
        echo '<div>' . esc_html__('My custom field contains a few settings:', 'waardepapierenaddon') . '</div>';

        // get the text field settings from the main field and then render the text field
        $text_field = $field['args']['text'];
        $this->settings_text($text_field);

        // get the checkbox field settings from the main field and then render the checkbox field
        $checkbox_field = $field['args']['checkbox'];
        $this->settings_checkbox($checkbox_field);
    }

    public function is_valid_setting(string $value): bool
    {
        return strlen($value) > 5;
    }
}
