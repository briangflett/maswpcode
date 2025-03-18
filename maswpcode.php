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

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

// Define the plugin constants
define('MASWPCODE_VERSION', '1.0.0');
define('MASWPCODE_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('MASWPCODE_PLUGIN_FILE', __FILE__);
define('MASWPCODE_PLUGIN_URL', plugin_dir_url(__FILE__));

// Including autoloader
require_once MASWPCODE_PLUGIN_DIR . 'autoload.php';

// Use namespaced classes
use Maswpcode\Plugin;
use Maswpcode\Activator;
use Maswpcode\Deactivator;

// Initialize plugin on `plugins_loaded`
add_action('plugins_loaded', function () {
	Plugin::instance();
});

// Register activation & deactivation hooks
register_activation_hook(__FILE__, function () {
	Activator::activate();
});

register_deactivation_hook(__FILE__, function () {
	Deactivator::deactivate();
});

