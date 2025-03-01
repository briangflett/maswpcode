<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://www.linkedin.com/in/brian-flett-2a43691/?originalSubdomain=ca
 * @since      1.0.0
 *
 * @package    Maswpcode
 * @subpackage Maswpcode/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Maswpcode
 * @subpackage Maswpcode/includes
 * @author     Brian Flett <brian.g.flett@gmail.com>
 */
class Maswpcode_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'maswpcode',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
