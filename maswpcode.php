<?php

/**
 * The plugin bootstrap file
 *
 * @link        	  https://github.com/briangflett/maswpcode
 * @since             1.0.0
 * @package           Maswpcode
 *
 * @wordpress-plugin
 * Plugin Name:       Maswpcode
 * Plugin URI:        https://github.com/briangflett/maswpcode
 * Description:       WordPress custom code developed for http://www.masadvise.org
 * Version:           1.0.0
 * Author:            Brian Flett
 * Author URI:        https://www.linkedin.com/in/brian-flett-2a43691/?originalSubdomain=ca/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       maswpcode
 * Domain Path:       /languages
 */

// Define plugin namespace
namespace Maswpcode;

// Exit if accessed directly.
if (!defined('WPINC')) {
	exit;
}

// Define the plugin version at the root level
define('MASWPCODE_VERSION', '1.0.0');
define('MASWPCODE_PLUGIN_DIR', plugin_dir_path(__FILE__));

/**
 * Check if Elementor Pro is activated.
 * Deactivates the plugin if missing.
 */
function check_elementor_pro()
{
	if (!did_action('elementor_pro/init')) {
		add_action('admin_notices', function () {
			echo '<div class="notice notice-error"><p>' . __('Maswpcode requires Elementor Pro to be installed and activated.', 'maswpcode') . '</p></div>';
		});
		error_log('Maswpcode deactivated: Elementor Pro is not installed or activated.');
		deactivate_plugins(plugin_basename(__FILE__));
	}
}
add_action('admin_init', 'Maswpcode\check_elementor_pro');

/**
 * The code that runs during plugin activation.
 */
function activate()
{
	require_once MASWPCODE_PLUGIN_DIR . 'includes/class-activator.php';
	\Maswpcode\Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate()
{
	require_once MASWPCODE_PLUGIN_DIR . 'includes/class-deactivator.php';
	\Maswpcode\Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'Maswpcode\activate');
register_deactivation_hook(__FILE__, 'Maswpcode\deactivate');

/**
 * The core plugin class that initializes the plugin.
 */
require MASWPCODE_PLUGIN_DIR . 'includes/class-plugin.php';

/**
 * Begin plugin execution.
 */
function run()
{
	$plugin = new \Maswpcode\Plugin();
	$plugin->run();
}

// Ensure this runs after Elementor (priority 20)
add_action('plugins_loaded', 'Maswpcode\run', 20);
