<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
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
if (! defined('WPINC')) {
	die;
}

/**
 * Check if Elementor Pro is active before running MAS WPCode.
 */
function maswpcode_check_elementor_pro()
{
	if (!did_action('elementor/loaded') || !class_exists('ElementorPro\Plugin')) {
		add_action('admin_notices', 'maswpcode_elementor_pro_missing_notice');
		deactivate_plugins(plugin_basename(__FILE__)); // Deactivate plugin if Elementor Pro is missing
		return false;
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

// Stop execution if Elementor Pro is not active
if (!maswpcode_check_elementor_pro()) {
	return;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('MASWPCODE_VERSION', '1.0.0');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-maswpcode-activator.php
 */
function activate_maswpcode()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-maswpcode-activator.php';
	MASWPCode_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-maswpcode-deactivator.php
 */
function deactivate_maswpcode()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-maswpcode-deactivator.php';
	MASWPCode_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_maswpcode');
register_deactivation_hook(__FILE__, 'deactivate_maswpcode');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-maswpcode.php';

// Load Elementor-related features
require_once plugin_dir_path(__FILE__) . 'includes/class-maswpcode-elementor.php';

// Register Elementor widgets and form actions
add_action('elementor/widgets/register', ['MASWPCode_Elementor', 'register_widgets']);
add_action('elementor_pro/forms/actions/register', ['MASWPCode_Elementor', 'register_form_actions']);

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_maswpcode()
{

	$plugin = new MASWPCode();
	$plugin->run();
}
run_maswpcode();
