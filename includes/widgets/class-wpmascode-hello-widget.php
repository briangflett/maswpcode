<?php
if (!defined('ABSPATH')) exit; // Prevent direct access

class MASWPCode_Hello_Widget extends \Elementor\Widget_Base
{

    // Widget unique identifier
    public function get_name()
    {
        return 'maswpcode_hello_widget';
    }

    // Widget title in Elementor editor
    public function get_title()
    {
        return __('Hello World', 'maswpcode');
    }

    // Widget icon in Elementor
    public function get_icon()
    {
        return 'eicon-code';
    }

    // Widget category (basic, general, etc.)
    public function get_categories()
    {
        return ['general'];
    }

    // Register controls for the widget (optional)
    protected function register_controls()
    {
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Content', 'maswpcode'),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'custom_text',
            [
                'label'       => __('Text', 'maswpcode'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => __('Hello, World!', 'maswpcode'),
                'placeholder' => __('Enter custom text...', 'maswpcode'),
            ]
        );

        $this->end_controls_section();
    }

    // Output widget content in Elementor preview & frontend
    protected function render()
    {
        $settings = $this->get_settings_for_display();
        echo '<h2>' . esc_html($settings['custom_text']) . '</h2>';
    }
}
