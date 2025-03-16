<?php

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
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

class Activator
{
	/**
	 * Runs on plugin activation.
	 *
	 * @since    1.0.0
	 */
	public static function activate()
	{
		// Activation logic here (e.g., database setup, default options)
	}
}
