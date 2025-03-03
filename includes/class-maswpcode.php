<?php

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

class MASWPCode
{
	protected $loader;
	protected $plugin_name;
	protected $version;

	public function __construct()
	{
		$this->version = MASWPCODE_VERSION;
		$this->plugin_name = 'maswpcode';

		$this->load_dependencies();
	}

	private function load_dependencies()
	{
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-maswpcode-loader.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-maswpcode-i18n.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-maswpcode-admin.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-maswpcode-public.php';

		$this->loader = new MASWPCode_Loader();
	}

	public function run()
	{
		$this->loader->run();
		add_action('plugins_loaded', [$this, 'define_elementor_hooks'], 20);
		add_action('init', [$this, 'set_locale']);
		add_action('admin_init', [$this, 'define_admin_hooks']);
		add_action('wp', [$this, 'define_public_hooks']);
	}

	private function define_elementor_hooks()
	{
		if (!did_action('elementor/loaded')) {
			add_action('admin_notices', [$this, 'elementor_missing_notice']);
			return;
		}

		require_once MASWPCODE_PLUGIN_DIR . 'includes/class-maswpcode-elementor.php';

		add_action('elementor/widgets/register', ['MASWPCode_Elementor', 'register_widgets']);

		if (defined('ELEMENTOR_PRO_VERSION') && class_exists('ElementorPro\Plugin')) {
			add_action('elementor_pro/forms/actions/register', ['MASWPCode_Elementor', 'register_form_actions']);
		} else {
			add_action('admin_notices', [$this, 'elementor_pro_missing_notice']);
		}
	}

	public function elementor_missing_notice()
	{
		echo '<div class="error"><p><strong>MAS WPCode</strong> requires Elementor to be installed and activated.</p></div>';
	}

	public function elementor_pro_missing_notice()
	{
		echo '<div class="error"><p><strong>MAS WPCode</strong> requires Elementor Pro to enable custom form actions.</p></div>';
	}

	public function set_locale()
	{
		$plugin_i18n = new MASWPCode_i18n();
		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
	}

	public function define_admin_hooks()
	{
		$plugin_admin = new MASWPCode_Admin($this->get_plugin_name(), $this->get_version());
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
	}

	public function define_public_hooks()
	{
		$plugin_public = new MASWPCode_Public($this->get_plugin_name(), $this->get_version());
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
	}

	public function get_plugin_name()
	{
		return $this->plugin_name;
	}

	public function get_loader()
	{
		return $this->loader;
	}

	public function get_version()
	{
		return $this->version;
	}
}
