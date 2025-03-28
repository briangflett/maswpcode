<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/briangflett/maswpcode
 * @since      1.0.0
 * @package    Maswpcode
 * @subpackage Maswpcode/public
 * @author     Brian Flett <brian.g.flett@gmail.com>
 */

namespace Maswpcode\Public;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class PublicArea
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
    }

    /**
     * Register the stylesheets for the public area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Maswpcode_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Maswpcode_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         * 
         * wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/maswpcode-public.css', array(), $this->version, 'all' );
         */
    }

    /**
     * Register the JavaScript for the public area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Maswpcode_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Maswpcode_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         * 
         * wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/maswpcode-public.js', array( 'jquery' ), $this->version, false );
         */
    }
}
