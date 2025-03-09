<?php
namespace Tenweb_Builder\Widgets\Sticky;

use Elementor\Controls_Manager;
use Elementor\Element_Base;
use Elementor\Element_Section;
use Elementor\Widget_Base;
use Elementor\Core\Base\Module;


/**
 * Class Sticky
 *
 * @package Tenweb_Builder\Widgets\Sticky
 */
class Sticky extends Module  {

    public function __construct() {
        $this->add_actions();
    }
    public function get_name() {
        return 'tenweb-sticky';
    }
    public function show_in_panel() {
        return false;
    }

    /**
     * Check if `$element` is an instance of a class in the `$types` array.
     *
     * @param $element
     * @param $types
     *
     * @return bool
     */
    private function is_instance_of( $element, array $types ) {
        foreach ( $types as $type ) {
            if ( $element instanceof $type ) {
                return true;
            }
        }

        return false;
    }

    public function register_controls( Element_Base $element ) {
        $element->add_control(
            'tenweb_sticky',
            [
                'label' => esc_html__( 'Sticky', 'tenweb-builder' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    '' => __( 'None', 'tenweb-builder' ),
                    'top' => __( 'Top', 'tenweb-builder' ),
                    'bottom' => __( 'Bottom', 'tenweb-builder' ),
                ],
                'separator' => 'before',
                'render_type' => 'none',
                'frontend_available' => true,
                'assets' => $this->get_asset_conditions_data(),
            ]
        );

        // TODO: In Pro 3.5.0, get the active devices using Breakpoints/Manager::get_active_devices_list().
        $active_breakpoint_instances = \Elementor\Plugin::instance()->breakpoints->get_active_breakpoints();
        // Devices need to be ordered from largest to smallest.
        $active_devices = array_reverse( array_keys( $active_breakpoint_instances ) );

        // Add desktop in the correct position.
        if ( in_array( 'widescreen', $active_devices, true ) ) {
            $active_devices = array_merge( array_slice( $active_devices, 0, 1 ), [ 'desktop' ], array_slice( $active_devices, 1 ) );
        } else {
            $active_devices = array_merge( [ 'desktop' ], $active_devices );
        }

        $sticky_device_options = [];

        foreach ( $active_devices as $device ) {
            $label = 'desktop' === $device ? esc_html__( 'Desktop', 'tenweb-builder' ) : $active_breakpoint_instances[ $device ]->get_label();
            $sticky_device_options[ $device ] = $label;
        }

        $element->add_control(
            'tenweb_sticky_on',
            [
                'label' => esc_html__( 'Sticky On', 'tenweb-builder' ),
                'type' => Controls_Manager::SELECT2,
                'multiple' => true,
                'label_block' => true,
                'default' => $active_devices,
                'options' => $sticky_device_options,
                'condition' => [
                    'tenweb_sticky!' => '',
                ],
                'render_type' => 'none',
                'frontend_available' => true,
            ]
        );

        $element->add_responsive_control(
            'tenweb_sticky_offset',
            [
                'label' => esc_html__( 'Offset', 'tenweb-builder' ),
                'type' => Controls_Manager::NUMBER,
                'default' => 0,
                'min' => 0,
                'max' => 500,
                'required' => true,
                'condition' => [
                    'tenweb_sticky!' => '',
                ],
                'render_type' => 'none',
                'frontend_available' => true,
            ]
        );

        $element->add_control(
            'tenweb_sticky_effects_offset',
            [
                'label' => esc_html__( 'Effects Offset', 'tenweb-builder' ),
                'type' => Controls_Manager::NUMBER,
                'default' => 0,
                'min' => 0,
                'max' => 1000,
                'required' => true,
                'condition' => [
                    'tenweb_sticky!' => '',
                ],
                'render_type' => 'none',
                'frontend_available' => true,
            ]
        );

        // Add `Stay In Column` only to the following types:
        $types = [
            Element_Section::class,
            Widget_Base::class,
        ];

        // TODO: Remove when Container is the default.
        if ( \Elementor\Plugin::instance()->experiments->is_feature_active( 'container' ) ) {
            $types[] = \Elementor\Includes\Elements\Container::class;
        }

        if ( $this->is_instance_of( $element, $types ) ) {
            $conditions = [
                'tenweb_sticky!' => '',
            ];

            // Target only inner sections.
            // Checking for `$element->get_data( 'isInner' )` in both editor & frontend causes it to work properly on the frontend but
            // break on the editor, because the inner section is created in JS and not rendered in PHP.
            // So this is a hack to force the editor to show the `sticky_parent` control, and still make it work properly on the frontend.
            if ( $element instanceof Element_Section && \Elementor\Plugin::instance()->editor->is_edit_mode() ) {
                $conditions['isInner'] = true;
            }

            $element->add_control(
                'tenweb_sticky_parent',
                [
                    'label' => esc_html__( 'Stay In Column', 'tenweb-builder' ),
                    'type' => Controls_Manager::SWITCHER,
                    'condition' => $conditions,
                    'render_type' => 'none',
                    'frontend_available' => true,
                ]
            );
        }

        $element->add_control(
            'sticky_divider',
            [
                'type' => Controls_Manager::DIVIDER,
            ]
        );
    }

    private function get_asset_conditions_data() {
        return [
            'scripts' => [
                [
                    'name' => 'e-sticky',
                    'conditions' => [
                        'terms' => [
                            [
                                'name' => 'tenweb_sticky',
                                'operator' => '!==',
                                'value' => '',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    private function add_actions() {
        add_action( 'elementor/element/section/section_effects/after_section_start', [ $this, 'register_controls' ] );
        add_action( 'elementor/element/container/section_effects/after_section_start', [ $this, 'register_controls' ] );
        add_action( 'elementor/element/common/section_effects/after_section_start', [ $this, 'register_controls' ] );
    }
}
new Sticky();
