<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://github.com/briangflett/maswpcode
 * @since      1.0.0
 * @package    Maswpcode
 * @subpackage Maswpcode/includes
 * @author     Brian Flett <brian.g.flett@gmail.com>
 */

// Define plugin namespace
namespace Maswpcode;

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

class i18n
{

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain()
	{

		load_plugin_textdomain(
			'maswpcode',
			false,
			dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
		);
	}
}
