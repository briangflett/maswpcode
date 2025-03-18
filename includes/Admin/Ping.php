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
class Ping extends \ElementorPro\Modules\Forms\Classes\Action_Base
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
        return 'ping';
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
        return 'Ping';
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

        wp_remote_post(
            'https://api.example.com/',
            [
                'method' => 'GET',
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'body' => wp_json_encode([
                    'site' => get_home_url(),
                    'action' => 'Form submitted',
                ]),
                'httpversion' => '1.0',
                'timeout' => 60,
            ]
        );
    }

    /**
     * Register action controls.
     *
     * Ping action has no input fields to the form widget.
     *
     * @since 1.0.0
     * @access public
     * @param \Elementor\Widget_Base $widget
     */
    public function register_settings_section($widget): void {}

    /**
     * On export.
     *
     * Ping action has no fields to clear when exporting.
     *
     * @since 1.0.0
     * @access public
     * @param array $element
     */
    public function on_export($element): array {}
}
