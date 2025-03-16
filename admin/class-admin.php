<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/briangflett/maswpcode
 * @since      1.0.0
 * @package    Maswpcode
 * @subpackage Maswpcode/admin
 * @author     Brian Flett <brian.g.flett@gmail.com>
 */

namespace Maswpcode;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Admin
{
    /**
     * The plugin name.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The name of this plugin.
     */
    private $plugin_name;

    /**
     * The plugin version.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of the plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param    string    $plugin_name    The name of the plugin.
     * @param    string    $version        The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        add_filter('elementor_pro/forms/admin/submissions/columns', [$this, 'add_approve_button_column']);
        add_action('elementor_pro/forms/admin/submissions/column_data', [$this, 'render_approve_button'], 10, 2);
        add_action('wp_ajax_maswpcode_approve_submission', [$this, 'approve_submission']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
    }

    // Add a new column for the "Approve" button
    public function add_approve_button_column($columns)
    {
        $columns['approve'] = 'Approve';
        return $columns;
    }

    // Render the "Approve" button in each row
    public function render_approve_button($column, $submission)
    {
        if ($column === 'approve') {
            $submission_id = $submission->get_id();
            $approved = get_post_meta($submission_id, '_maswpcode_approved', true);

            if ($approved) {
                echo '<button class="button disabled">Approved</button>';
            } else {
                echo '<button class="button approve-submission" data-id="' . esc_attr($submission_id) . '">Approve</button>';
            }
        }
    }

    // Handle AJAX request to approve the submission
    public function approve_submission()
    {
        check_ajax_referer('maswpcode_nonce', 'nonce');

        $submission_id = intval($_POST['submission_id']);
        if (!$submission_id) {
            wp_send_json_error(['message' => 'Invalid submission ID']);
        }

        // Fetch submission data
        $submission = get_post($submission_id);
        $form_data = get_post_meta($submission_id, '_elementor_form_data', true);

        // Send data to CiviCRM (implement your logic here)
        $result = $this->send_to_civicrm($form_data);

        if ($result) {
            update_post_meta($submission_id, '_maswpcode_approved', true);
            wp_send_json_success(['message' => 'Approved successfully']);
        } else {
            wp_send_json_error(['message' => 'Failed to send to CiviCRM']);
        }
    }

    // Function to send data to CiviCRM
    private function send_to_civicrm($form_data)
    {
        // Implement API integration here (e.g., CiviCRM API call)
        return true; // Return true on success, false on failure
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in MASWPCode_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The MASWPCode_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         *
         * wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/maswpcode-admin.css', array(), $this->version, 'all');
         */
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in MASWPCode_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The MASWPCode_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         * 
         * wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/maswpcode-admin.js', array('jquery'), $this->version, false);
         */

        wp_enqueue_script('maswpcode-admin', MASWPCODE_PLUGIN_DIR . 'admin/js/maswpcode-admin.js', ['jquery'], '1.0.0', true);
        wp_localize_script('maswpcode-admin', 'maswpcode', [
            'nonce' => wp_create_nonce('maswpcode_nonce'),
            'ajaxurl' => admin_url('admin-ajax.php')
        ]);
    }
}
