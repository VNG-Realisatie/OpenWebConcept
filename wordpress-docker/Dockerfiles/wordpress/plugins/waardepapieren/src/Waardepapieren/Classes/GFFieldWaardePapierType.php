<?php

namespace OWC\Waardepapieren\Classes;

class GFFieldWaardePapierType extends \GF_Field_Select
{
    public $type = 'waardepapier';
    public $label;
    public $choices;
    public $isRequired;

    public function __construct($data = array())
    {
        parent::__construct($data);

        $this->label = 'Waardepapieren Type';
        $this->choices = [
            [
                'text' => 'Akte van geboorte',
                'value' => 'akte_van_geboorte',
            ],
            [
                'text' => 'Akte van huwelijk',
                'value' => 'akte_van_huwelijk',
            ],
            [
                'text' => 'Akte van overlijden',
                'value' => 'akte_van_overlijden',
            ],
            [
                'text' => 'Akte van registratie van een partnershap',
                'value' => 'akte_van_registratie_van_een_partnerschap',
            ],
            [
                'text' => 'Akte van omzetting van een huwelijk in een registratie van een partnerschap',
                'value' => 'akte_van_omzetting_van_een_huwelijk_in_een_registratie_van_een_partnerschap',
            ],
            [
                'text' => 'Akte van omzetting van een registratie van een partnerschap',
                'value' => 'akte_van_omzetting_van_een_registratie_van_een_partnerschap',
            ],
            [
                'text' => 'Verklaring diploma\'s',
                'value' => 'verklaring_diplomas',
            ],
            [
                'text' => 'Verklaring inkomen',
                'value' => 'verklaring_inkomen',
            ],
            [
                'text' => 'Verklaring studieschuld',
                'value' => 'verklaring_studieschuld',
            ],
            [
                'text' => 'Verklaring van huwelijksbevoegdheid',
                'value' => 'verklaring_van_huwelijksbevoegdheid',
            ],
            [
                'text' => 'Verklaring van in leven zijn',
                'value' => 'verklaring_van_in_leven_zijn',
            ],
            [
                'text' => 'Verklaring van nederlandershap',
                'value' => 'verklaring_van_nederlandershap',
            ],
            [
                'text' => 'Uittreksel basis registratie personen',
                'value' => 'uittreksel_basis_registratie_personen',
            ],
            [
                'text' => 'Uittreksel registratie niet ingezetenen',
                'value' => 'uittreksel_registratie_niet_ingezetenen',
            ],
            [
                'text' => 'Historisch uittreksel basis registratie personen',
                'value' => 'historisch_uittreksel_basis_registratie_personen',
            ]
        ];
        $this->isRequired = true;
    }

    public function get_form_editor_field_title()
    {
        return esc_attr__('Waardepapier Type', 'gravityforms');
    }

    public function get_form_editor_button()
    {
        return array(
            'group' => 'advanced_fields',
            'text'  => $this->get_form_editor_field_title()
        );
    }

    public function get_field_label($bool, $value) {
        $this->label = 'Waardepapieren Type';
        return \GFCommon::get_label($this);
    }
}