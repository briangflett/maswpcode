<?php

namespace Maswpcode;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

use ElementorPro\Modules\Forms\Classes\Action_Base;

class Elementor_Form_Action extends Action_Base
{
    /**
     * Get action name
     */
    public function get_name()
    {
        return 'maswpcode_custom_action';
    }

    /**
     * Get action label
     */
    public function get_label()
    {
        return __('MAS Custom Action', 'maswpcode');
    }

    /**
     * Register custom settings controls for this action.
     */
    public function register_settings_section($widget)
    {
        $widget->add_control(
            'maswpcode_custom_field',
            [
                'label' => __('Custom Field', 'maswpcode'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => __('Enter your custom field value', 'maswpcode'),
            ]
        );
    }

    /**
     * Handle form submission
     */
    public function run($record, $handler)
    {
        $form_data = $record->get('fields');
        error_log('MAS Custom Form Action Triggered: ' . print_r($form_data, true));

        // Custom action logic here (e.g., send API request, log data, etc.)
    }

    /**
     * Export form action settings
     *
     * @param array $element
     */
    public function on_export($element)
    {
        // Handle export functionality (if needed)
    }
}
