<?php
/*
Plugin Name: MASWPCode
Description: My custom WordPress code.
Version: 1.0.0
Author: Your Name
Requires Plugins: elementor-pro
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Include your custom action class.
require_once plugin_dir_path(__FILE__) . 'includes/mas-action-1.php';

// Register the custom action.
function maswpcode_register_elementor_form_actions($form_actions_registrar)
{
    require_once plugin_dir_path(__FILE__) . 'includes/mas-action-1.php';
    $form_actions_registrar->register(new \MasWPCode\ElementorForms\MAS_Action_1());
}
add_action('elementor_pro/forms/actions/register', 'maswpcode_register_elementor_form_actions');

// Add text domain for translations.
function maswpcode_load_textdomain()
{
    load_plugin_textdomain('maswpcode', false, basename(dirname(__FILE__)) . '/languages/');
}
add_action('plugins_loaded', 'maswpcode_load_textdomain');
