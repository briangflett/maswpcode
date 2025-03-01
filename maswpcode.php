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
 * @package           Maswpcode
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

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'MASWPCODE_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-maswpcode-activator.php
 */
function activate_maswpcode() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-maswpcode-activator.php';
	Maswpcode_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-maswpcode-deactivator.php
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
run_maswpcode();
