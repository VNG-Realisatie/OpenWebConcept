<?php

namespace OWC\Waardepapieren\Classes;

class GFFieldWaardePapierPerson extends \GF_Field_Select
{
    public $type = 'person';
    public $label;
    public $choices;
    public $isRequired;

    public function __construct($data = array())
    {
        parent::__construct($data);

        $this->label = 'Person';
        $this->choices = [
            [
                'text' => 'Buren test persoon',
                'value' => '900198424',
            ]
        ];
        $this->isRequired = true;
    }

    public function get_form_editor_field_title()
    {
        return esc_attr__('Waardepapier Person', 'gravityforms');
    }

    public function get_form_editor_button()
    {
        return array(
            'group' => 'advanced_fields',
            'text'  => $this->get_form_editor_field_title()
        );
    }
}