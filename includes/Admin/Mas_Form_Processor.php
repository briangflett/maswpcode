<?php

namespace Maswpcode\Admin;

if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Elementor form ping action.
 *
 * Custom Elementor form action which will ping an external server.
 *
 * @since 1.0.0
 */
class Mas_Form_Processor extends \ElementorPro\Modules\Forms\Classes\Action_Base
{

    /**
     * Get action name.
     *
     * Retrieve ping action name.
     *
     * @since 1.0.0
     * @access public
     * @return string
     */
    public function get_name(): string
    {
        return 'Mas_Form_Processor';
    }

    /**
     * Get action label.
     *
     * Retrieve ping action label.
     *
     * @since 1.0.0
     * @access public
     * @return string
     */
    public function get_label(): string
    {
        return 'MasFormProcessor';
    }

    /**
     * Run action.
     *
     * Ping an external server after form submission.
     *
     * @since 1.0.0
     * @access public
     * @param \ElementorPro\Modules\Forms\Classes\Form_Record  $record
     * @param \ElementorPro\Modules\Forms\Classes\Ajax_Handler $ajax_handler
     */
    public function run($record, $ajax_handler): void
    {
        // Get submitted form data.
        $raw_fields = $record->get('fields');
        xdebug_break();

        // Normalize form data.
        $fields = [];
        $fields['welcomeback'] = null;
        $fields['hear_about_us'] = null;
        foreach ($raw_fields as $id => $field) {
            $fields[$id] = $field['value'];
        }

        civicrm_api3('FormProcessor', 'request_for_assistance', $fields);
    }

    /**
     * Register action controls.
     *
     * Mas_Form_Processor action has no input fields to the form widget.
     *
     * @since 1.0.0
     * @access public
     * @param \Elementor\Widget_Base $widget
     */
    public function register_settings_section($widget): void {}

    /**
     * On export.
     *
     * Mas_Form_Processor action has no fields to clear when exporting.
     *
     * @since 1.0.0
     * @access public
     * @param array $element
     */
    public function on_export($element): array {}
}
