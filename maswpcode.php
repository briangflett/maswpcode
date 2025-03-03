<?php

/**
 * The plugin bootstrap file
 *
 * @link              https://www.linkedin.com/in/brian-flett-2a43691/?originalSubdomain=ca
 * @since             1.0.0
 * @package           MASWPCode
 *
 * @wordpress-plugin
 * Plugin Name:       MASWPCode
 * Plugin URI:        https://github.com/briangflett/maswpcode
 * Description:       Wordpress custom code developed for http://www.masadvise.org
 * Version:           1.0.0
 * Author:            Brian Flett
 * Author URI:        https://www.linkedin.com/in/brian-flett-2a43691/?originalSubdomain=ca/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       maswpcode
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

// Define the plugin version at the root level
define('MASWPCODE_VERSION', '1.0.0');

/**
 * Check if Elementor Pro is active before running MAS WPCode.
 */
function maswpcode_check_elementor_pro()
{
	if (!did_action('elementor/loaded') || !class_exists('ElementorPro\Plugin')) {
		add_action('admin_notices', 'maswpcode_elementor_pro_missing_notice');
		return false; // Prevents further execution
	}
	return true;
}

/**
 * Display an admin notice if Elementor Pro is missing.
 */
function maswpcode_elementor_pro_missing_notice()
{
	echo '<div class="error"><p><strong>MAS WPCode</strong> requires Elementor Pro to be installed and activated.</p></div>';
}

/**
 * The code that runs during plugin activation.
 */
function activate_maswpcode()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-maswpcode-activator.php';
	MASWPCode_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_maswpcode()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-maswpcode-deactivator.php';
	MASWPCode_Deactivator::deactivate();
}

// Register activation and deactivation hooks (must be outside of functions)
register_activation_hook(__FILE__, 'activate_maswpcode');
register_deactivation_hook(__FILE__, 'deactivate_maswpcode');

/**
 * Begins execution of the plugin.
 */
function maswpcode_init()
{
	// Ensure Elementor Pro is loaded
	if (!maswpcode_check_elementor_pro()) {
		return; // Stop execution if Elementor Pro is missing
	}

	require_once plugin_dir_path(__FILE__) . 'includes/class-maswpcode.php';
	require_once plugin_dir_path(__FILE__) . 'includes/class-maswpcode-elementor.php';

	// Register Elementor widgets and form actions
	add_action('elementor/widgets/register', ['MASWPCode_Elementor', 'register_widgets']);
	add_action('elementor_pro/forms/actions/register', ['MASWPCode_Elementor', 'register_form_actions']);

	$plugin = new MASWPCode();
	$plugin->run();
}

// Ensure this runs after Elementor (priority 20)
add_action('plugins_loaded', 'maswpcode_init', 20);
