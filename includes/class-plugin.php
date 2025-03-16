<?php

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
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

class Plugin
{
	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct()
	{
		$this->version = MASWPCODE_VERSION;
		$this->plugin_name = 'maswpcode';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Loader: Orchestrates the hooks of the plugin.
	 * - i18n: Defines internationalization functionality.
	 * - Admin: Defines all hooks for the admin area.
	 * - PublicArea: Defines all hooks for the public side of the site.
	 * - Elementor Form Action: Adds a custom form action to Elementor Pro.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies()
	{
		require_once MASWPCODE_PLUGIN_DIR . 'includes/class-loader.php';
		require_once MASWPCODE_PLUGIN_DIR . 'includes/class-i18n.php';
		require_once MASWPCODE_PLUGIN_DIR . 'admin/class-admin.php';
		require_once MASWPCODE_PLUGIN_DIR . 'public/class-public.php';
		// require_once MASWPCODE_PLUGIN_DIR . 'public/class-elementor-form-action.php';

		$this->loader = new \Maswpcode\Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	public function set_locale()
	{
		$plugin_i18n = new \Maswpcode\i18n();
		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
	}

	/**
	 * Register all of the hooks related to the admin area functionality.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	public function define_admin_hooks()
	{
		$plugin_admin = new \Maswpcode\Admin($this->get_plugin_name(), $this->get_version());
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
	}

	/**
	 * Register all of the hooks related to the public-facing functionality.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	public function define_public_hooks()
	{
		$plugin_public = new \Maswpcode\PublicArea($this->get_plugin_name(), $this->get_version());
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
	}

	// /**
	//  * Register Elementor form actions.
	//  *
	//  * @since    1.0.0
	//  */
	// public function register_elementor_form_action($actions)
	// {
	// 	$actions['maswpcode_custom_action'] = new \Maswpcode\Elementor_Form_Action();
	// 	return $actions;
	// }

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run()
	{
		$this->loader->run();
		// add_action('init', [$this, 'set_locale']);
		// add_action('admin_init', [$this, 'define_admin_hooks']);
		// add_action('wp', [$this, 'define_public_hooks']);
		// add_action('elementor_pro/forms/actions/register', [$this, 'register_elementor_form_action']);
	}

	/**
	 * Retrieve the plugin name.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name()
	{
		return $this->plugin_name;
	}

	/**
	 * Retrieve the loader instance.
	 *
	 * @since     1.0.0
	 * @return    Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader()
	{
		return $this->loader;
	}

	/**
	 * Retrieve the plugin version.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version()
	{
		return $this->version;
	}
}
