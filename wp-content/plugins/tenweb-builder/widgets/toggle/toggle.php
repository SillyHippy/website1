<?php

namespace Tenweb_Builder\Widgets\Toggle;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class Toggle {

    function __construct() {
        $this->add_action();
    }

    public function add_action() {
        add_action('elementor/element/toggle/section_toggle_style_icon/before_section_end', [$this, 'add_injection'], 10, 2);
    }

    public function add_injection($element, $args) {
        $element->start_injection([
            'at' => 'before',
            'of' => 'icon_align',
        ]);
        // add a control
        $element->add_control(
            'twbb_icon_vertical_position_left',
            [
                'label' => 'Vertical Position',
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'top' => 'Top',
                    'center' => 'Center',
                    'bottom' => 'Bottom',
                ],
                'condition' => [
                    'icon_align' => 'left'
                ],
                'prefix_class' => 'twbb-icon-position twbb-icon-position-left-',
            ]
        );

        $element->add_control(
            'twbb_icon_vertical_position_right',
            [
                'label' => 'Vertical Position',
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'top' => 'Top',
                    'center' => 'Center',
                    'bottom' => 'Bottom',
                ],
                'condition' => [
                    'icon_align' => 'right'
                ],
                'prefix_class' => 'twbb-icon-position twbb-icon-position-right-',
            ]
        );

        $element->end_injection();

    }
}

new Toggle();