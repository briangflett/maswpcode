<?php
if (!defined('ABSPATH')) exit; // Prevent direct access

// Ensure Elementor Pro is installed before defining the class
if (!class_exists('ElementorPro\Modules\Forms\Classes\Action_Base')) {
    return;
}

use ElementorPro\Modules\Forms\Classes\Action_Base;

class MASWPCode_Form_Action extends Action_Base
{
    /**
     * Get unique identifier for the form action.
     *
     * @return string
     */
    public function get_name()
    {
        return 'mas_form_action';
    }

    /**
     * Get the form action title (visible in Elementor settings).
     *
     * @return string
     */
    public function get_label()
    {
        return __('MAS Form Action', 'maswpcode');
    }

    /**
     * Register settings in Elementor form panel.
     *
     * @param \Elementor\Widget_Base $widget
     */
    public function register_settings_section($widget)
    {
        $widget->start_controls_section(
            'section_mas_form_action',
            [
                'label' => __('MAS Form Action Settings', 'maswpcode'),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $widget->add_control(
            'mas_message',
            [
                'label'       => __('Success Message', 'maswpcode'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => __('Your form was processed successfully!', 'maswpcode'),
                'placeholder' => __('Enter success message...', 'maswpcode'),
            ]
        );

        $widget->end_controls_section();
    }

    /**
     * Process form submission.
     *
     * @param \ElementorPro\Modules\Forms\Classes\Submission $submission
     * @param \ElementorPro\Modules\Forms\Classes\Ajax_Handler $handler
     */
    public function on_submit($submission, $handler)
    {
        global $wpdb;
        $data = $submission->get_formatted_data();

        // Debug logging (only if WP_DEBUG is enabled)
        if (WP_DEBUG) {
            error_log('[MAS Form Action] Form Submission Data: ' . print_r($data, true));
        }

        // Send email notification to WordPress admin email
        $admin_email = get_option('admin_email', 'admin@example.com');
        wp_mail($admin_email, 'New Form Submission', print_r($data, true));

        // Save form data securely in the database
        $wpdb->query(
            $wpdb->prepare(
                "INSERT INTO {$wpdb->prefix}mas_form_submissions (form_data, created_at) VALUES (%s, %s)",
                json_encode($data),
                current_time('mysql')
            )
        );

        // Set a custom success message
        $handler->add_response_data('message', __('Thank you! Your submission was received.', 'maswpcode'));
    }
}
