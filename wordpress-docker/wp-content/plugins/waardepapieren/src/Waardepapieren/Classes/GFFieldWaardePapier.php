<?php

namespace OWC\Waardepapieren\Classes;

class GFFieldWaardePapier extends \GF_Field_Select
{
    public $type = 'waardepapier';

    public function get_form_editor_field_title()
    {
        return esc_attr__('Waardepapieren', 'gravityforms');
    }

    public function get_form_editor_button()
    {
        return array(
            'group' => 'advanced_fields',
            'text'  => $this->get_form_editor_field_title()
        );
    }

    public function get_choices($value)
    {
        return \GFCommon::get_select_choices($this, $value);
    }
}
