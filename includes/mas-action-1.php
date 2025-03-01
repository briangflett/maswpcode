<?php

namespace MasWPCode\ElementorForms;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

use ElementorPro\Modules\Forms\Classes\Action_Base;
use ElementorPro\Modules\Forms\Classes\Form_Record;

class MAS_Action_1 extends Action_Base
{

    public function get_name()
    {
        return 'mas_action_1';
    }

    public function get_label()
    {
        return __('MAS Action 1', 'maswpcode');
    }

    public function run(Form_Record $record, \ElementorPro\Modules\Forms\Classes\Ajax_Handler $ajax_handler)
    {
        $raw_fields = $record->get('fields');
        $fields = [];
        foreach ($raw_fields as $id => $field) {
            $fields[$id] = $field['value'];
        }

        // Your custom action logic here.
        // For example, you can log the form data:
        error_log('MAS Action 1 Form Data: ' . print_r($fields, true));

        // You can also send emails, save data to a database, etc.
        // If there's an error, use $ajax_handler->add_error_message('Your error message');

        $ajax_handler->add_success_message(__('MAS Action 1 processed successfully!', 'maswpcode'));
    }

    public function register_settings_section(\Elementor\Widget_Base $widget)
    {
        // You can add settings for your action here if needed.
        // Example:
        $widget->start_controls_section(
            'section_mas_action_1',
            [
                'label' => __('MAS Action 1 Settings', 'maswpcode'),
                'condition' => [
                    'actions' => $this->get_name(),
                ],
            ]
        );

        $widget->add_control(
            'mas_action_1_setting',
            [
                'label' => __('Example Setting', 'maswpcode'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Default Value', 'maswpcode'),
            ]
        );

        $widget->end_controls_section();
    }
}
