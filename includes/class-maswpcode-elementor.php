<?php

/**
 * Elementor Form Action - MAS Action 1
 *
 * @package MASWPCode
 */

if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class MASWPCode_Elementor
{

    public static function register_widgets($widgets_manager)
    {
        require_once MASWPCODE_PLUGIN_DIR . 'includes/widgets/class-maswpcode-hello-widget.php';
        $widgets_manager->register(new MASWPCode_Hello_Widget());
    }

    /**
     * Register all custom Elementor form actions.
     */
    public static function register_form_actions($form_manager)
    {
        // Ensure Elementor Pro is active before registering form actions
        if (!class_exists('ElementorPro\Plugin')) {
            return;
        }

        require_once MASWPCODE_PLUGIN_DIR . 'includes/form-actions/class-maswpcode-form-action.php';

        // Register the form action
        $form_manager->register_action('mas_form_action', new MASWPCode_Form_Action());
    }

    /**
     * Initialize Elementor support: widgets & form actions.
     */
    public static function init()
    {
        add_action('elementor/widgets/register', [__CLASS__, 'register_widgets']);

        // Only register form actions if Elementor Pro is installed
        if (class_exists('ElementorPro\Plugin')) {
            add_action('elementor_pro/forms/actions/register', [__CLASS__, 'register_form_actions']);
        }
    }
}

// Initialize only if Elementor is active
if (did_action('elementor/loaded')) {
    MASWPCode_Elementor::init();
}
