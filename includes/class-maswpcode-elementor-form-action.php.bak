<?php

/**
 * Elementor Form Action - MAS Action 1
 *
 * @package Maswpcode
 */

if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Maswpcode_Elementor_Form_Action
{

    public function __construct()
    {
        add_action('elementor_pro/forms/actions/register', [$this, 'register_actions']);
    }

    public function register_actions($form_actions_registrar)
    {

        require_once __DIR__ . '/actions/class-maswpcode-form-action-1.php';

        $form_actions_registrar->register(new \ElementorPro\Modules\Forms\Actions\MASWPCode_Form_Action_1());
    }
}

new Maswpcode_Elementor_Form_Action();
