<?php

/**
 * Elementor Integration - Widgets & Form Actions
 *
 * @package MASWPCode
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class MASWPCode_Elementor
{
    /**
     * Register all custom Elementor widgets.
     *
     * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
     */
    public static function register_widgets($widgets_manager)
    {
        require_once MASWPCODE_PLUGIN_DIR . 'includes/widgets/class-maswpcode-hello-widget.php';

        // Register "Hello World" widget
        $widgets_manager->register(new MASWPCode_Hello_Widget());
    }

    /**
     * Register all custom Elementor form actions.
     *
     * @param \ElementorPro\Modules\Forms\Classes\Actions_Manager $form_manager Elementor forms manager.
     */
    public static function register_form_actions($form_manager)
    {
        // Ensure Elementor Pro is active before registering form actions
        if (!defined('ELEMENTOR_PRO_VERSION') || !class_exists('ElementorPro\Plugin')) {
            return;
        }

        require_once MASWPCODE_PLUGIN_DIR . 'includes/actions/class-maswpcode-form-action.php';

        // Register the custom form action
        $form_manager->register_action('mas_form_action', new MASWPCode_Form_Action());
    }

    /**
     * Initialize Elementor support: Widgets & Form Actions.
     */
    public static function init()
    {
        add_action('elementor/widgets/register', [__CLASS__, 'register_widgets']);

        // Only register form actions if Elementor Pro is installed
        if (defined('ELEMENTOR_PRO_VERSION') && class_exists('ElementorPro\Plugin')) {
            add_action('elementor_pro/forms/actions/register', [__CLASS__, 'register_form_actions']);
        }
    }
}

// Ensure this runs only after Elementor is fully loaded
add_action('elementor/loaded', ['MASWPCode_Elementor', 'init']);
