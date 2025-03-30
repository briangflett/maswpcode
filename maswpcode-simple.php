<?php

/**
 * Plugin Name: Maswpcode Simple
 * Description: Simplified WordPress custom code developed for http://www.masadvise.org
 * Version:     1.0.0
 * Author:      Brian Flett
 * Author URI:  https://www.linkedin.com/in/brian-flett-2a43691/?originalSubdomain=ca/
 * Text Domain: maswpcode-simple
 *
 * Requires Plugins: elementor
 * Elementor tested up to: 3.27.6
 * Elementor Pro tested up to: 3.26.2
 */

function mas_form_processor($form_actions_registrar)
{

    include_once(__DIR__ .  '/form-actions/Mas_Form_Processor.php');

    $form_actions_registrar->register(new Maswpcode\Mas_Form_Processor());
}

add_action('elementor_pro/forms/actions/register', 'mas_form_processor');
