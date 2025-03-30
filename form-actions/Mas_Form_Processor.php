<?php

namespace Maswpcode;

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
        // xdebug_break();
        // Get submitted form data.
        $form_id = $record->get_form_settings('form_id');
        $formatted_fields = $record->get_formatted_data();
        $raw_fields = $record->get('fields');

        if (!$form_id || empty($formatted_fields)) {
            error_log('[maswpcode] Warning: form_id or formatted_fields are empty. Form settings: ' . print_r($record->get_form_settings(), true));
            return;
        }

        if (substr($form_id, 0, 4) === 'sub_') {
            // If the form id starts with "sub_", then save it in the submissions table
            $field_data_json = json_encode($formatted_fields);
            try {
                $result = \Civi\Api4\MascodeSubmission::create(TRUE)
                    ->addValue('form_id', $form_id)
                    ->addValue('field_data', $field_data_json)
                    ->execute();
            } catch (\Exception $e) {
                error_log('CiviCRM API4 error: ' . $e->getMessage());
            };
        } else {
            // If the form id does not start with "sub_", then the data to the form processor
            // $fields = $formatted_fields;  // might need to reformat
            $mapped_fields = [];
            foreach ($raw_fields as $field) {
                if (!empty($field['id']) && isset($field['value'])) {
                    $mapped_fields[$field['id']] = is_array($field['value'])
                        ? implode(', ', $field['value'])
                        : $field['value'];
                }
            }
            // try {
            //     $result = civicrm_api3('FormProcessor', 'request_for_assistance', $mapped_fields);
            // } catch (\Exception $e) {
            //     error_log('[maswpcode] API Error: ' . $e->getMessage());
            //     exit;
            // };
            try {
                // Log the raw data being passed
                error_log('[maswpcode] Submitting FormProcessor with fields: ' . print_r($mapped_fields, true));
                $result = civicrm_api3('FormProcessor', 'submit', [
                    'form_processor_id' => 'request_for_assistance',
                    'values' => $mapped_fields,
                ]);
                // Log the result for visibility
                error_log('[maswpcode] FormProcessor.submit result: ' . print_r($result, true));
            } catch (\Exception $e) {
                error_log('[maswpcode] FormProcessor.submit ERROR: ' . $e->getMessage());
                if (method_exists($e, 'getTraceAsString')) {
                    error_log('[maswpcode] Stack trace: ' . $e->getTraceAsString());
                };
                exit;
            }
        }
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
    public function on_export($element): array
    {
        return $element;
    }
}
