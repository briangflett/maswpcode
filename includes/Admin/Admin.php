<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/briangflett/maswpcode
 * @since      1.0.0
 * @package    Maswpcode
 * @subpackage Maswpcode/admin
 * @author     Brian Flett <brian.g.flett@gmail.com>
 */

namespace Maswpcode\Admin;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

use Maswpcode\Admin\Mas_Form_Processor;
use Maswpcode\Admin\Ping;

class Admin
{
    /**
     * The plugin name.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The name of this plugin.
     */
    private $plugin_name;

    /**
     * The plugin version.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of the plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param    string    $plugin_name    The name of the plugin.
     * @param    string    $version        The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        add_action('elementor_pro/forms/actions/register', [$this, 'add_new_ping_action']);
        add_action('elementor_pro/forms/actions/register', [$this, 'mas_form_processor']);
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in MASWPCode_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The MASWPCode_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         *
         * wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/maswpcode-admin.css', array(), $this->version, 'all');
         */
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in MASWPCode_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The MASWPCode_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         * 
         * wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/maswpcode-admin.js', array('jquery'), $this->version, false);
         */

        // wp_enqueue_script('maswpcode-admin', MASWPCODE_PLUGIN_DIR . 'admin/js/maswpcode-admin.js', ['jquery'], '1.0.0', true);
        // wp_localize_script('maswpcode-admin', 'maswpcode', [
        //     'nonce' => wp_create_nonce('maswpcode_nonce'),
        //     'ajaxurl' => admin_url('admin-ajax.php')
        // ]);
    }

    /**
     * Add new form action after form submission.
     *
     * @since 1.0.0
     * @param ElementorPro\Modules\Forms\Registrars\Form_Actions_Registrar $form_actions_registrar
     * @return void
     */
    public function add_new_ping_action($form_actions_registrar)
    {

        include_once(__DIR__ .  '/ping.php');

        $form_actions_registrar->register(new Ping());
    }
    public function mas_form_processor($form_actions_registrar)
    {

        include_once(__DIR__ .  '/mas-form-processor.php');

        $form_actions_registrar->register(new Mas_Form_Processor());
    }
}
