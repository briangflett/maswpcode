<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.linkedin.com/in/brian-flett-2a43691/?originalSubdomain=ca
 * @since      1.0.0
 *
 * @package    MASWPCode
 * @subpackage MASWPCode/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    MASWPCode
 * @subpackage MASWPCode/includes
 * @author     Brian Flett <brian.g.flett@gmail.com>
 */
class MASWPCode
{

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      MASWPCode_Loader    $loader    Maintains and registers all hooks for the plugin.
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
		if (defined('MASWPCODE_VERSION')) {
			$this->version = MASWPCODE_VERSION;
		} else {
			$this->version = '1.0.0';
		}
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
	 * - MASWPCode_Loader. Orchestrates the hooks of the plugin.
	 * - MASWPCode_i18n. Defines internationalization functionality.
	 * - MASWPCode_Admin. Defines all hooks for the admin area.
	 * - MASWPCode_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies()
	{

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-maswpcode-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-maswpcode-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-maswpcode-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-maswpcode-public.php';

		$this->loader = new MASWPCode_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the MASWPCode_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale()
	{

		$plugin_i18n = new MASWPCode_i18n();

		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks()
	{

		$plugin_admin = new MASWPCode_Admin($this->get_plugin_name(), $this->get_version());

		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks()
	{

		$plugin_public = new MASWPCode_Public($this->get_plugin_name(), $this->get_version());

		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
	}

	private function define_elementor_hooks()
	{
		// Ensure Elementor is loaded
		if (!did_action('elementor/loaded')) {
			add_action('admin_notices', [$this, 'elementor_missing_notice']);
			return;
		}

		require_once MASWPCODE_PLUGIN_DIR . 'includes/class-maswpcode-elementor.php';

		// Register Elementor widgets
		add_action('elementor/widgets/register', ['MASWPCode_Elementor', 'register_widgets']);

		// Register Elementor Pro form actions (only if Elementor Pro is active)
		if (class_exists('ElementorPro\Plugin')) {
			add_action('elementor_pro/forms/actions/register', ['MASWPCode_Elementor', 'register_form_actions']);
		} else {
			add_action('admin_notices', [$this, 'elementor_pro_missing_notice']);
		}
	}

	/**
	 * Show admin notice if Elementor (free) is missing.
	 */
	public function elementor_missing_notice()
	{
		echo '<div class="error"><p><strong>MAS WPCode</strong> requires Elementor to be installed and activated.</p></div>';
	}

	/**
	 * Show admin notice if Elementor Pro is missing (for form actions).
	 */
	public function elementor_pro_missing_notice()
	{
		echo '<div class="error"><p><strong>MAS WPCode</strong> requires Elementor Pro to enable custom form actions.</p></div>';
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run()
	{
		$this->loader->run();
		$this->define_elementor_hooks();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name()
	{
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    MASWPCode_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader()
	{
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version()
	{
		return $this->version;
	}
}
