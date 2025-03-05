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
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Define the plugin version at the root level
define('MASWPCODE_VERSION', '1.0.0');
define('MASWPCODE_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('MASWPCODE_PLUGIN_URL', plugin_dir_url(__FILE__));


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
define( 'MASWPCODE_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 */
function activate_maswpcode() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-maswpcode-activator.php';
	Maswpcode_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_maswpcode() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-maswpcode-deactivator.php';
	Maswpcode_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_maswpcode' );
register_deactivation_hook( __FILE__, 'deactivate_maswpcode' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-maswpcode.php';

// Include the Elementor Form Action class.
require plugin_dir_path(__FILE__) . 'includes/class-maswpcode-elementor-form-action.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_maswpcode() {

	$plugin = new Maswpcode();
	$plugin->run();
}

// Ensure this runs after Elementor (priority 20)
add_action('plugins_loaded', 'maswpcode_init', 20);
