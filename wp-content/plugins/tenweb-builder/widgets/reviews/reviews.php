<?php
namespace Tenweb_Builder\Widgets\Reviews;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Widget_Base;
use Tenweb_Builder\Builder;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class Reviews extends Widget_Base {

    public function get_name() {
        return Builder::$prefix . '_reviews';
    }

    public function get_title() {
        return esc_html__( 'Reviews', 'tenweb-builder' );
    }

    public function get_categories() {
        return ['tenweb-widgets'];
    }

    public function get_icon() {
        return 'twbb-reviews twbb-widget-icon';
    }

    public function get_keywords() {
        return [ 'reviews', 'social', 'rating', 'testimonial', 'carousel' ];
    }
    private $slide_prints_count = 0;

    public function get_script_depends() {
        return [ 'imagesloaded' ];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_slides',
            [
                'label' => esc_html__( 'Slides', 'elementor-pro' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new Repeater();

        $this->add_repeater_controls( $repeater );

        $this->add_control(
            'slides',
            [
                'label' => esc_html__( 'Slides', 'elementor-pro' ),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => $this->get_repeater_defaults(),
                'separator' => 'after',
            ]
        );

        $this->add_control(
            'effect',
            [
                'type' => Controls_Manager::SELECT,
                'label' => esc_html__( 'Effect', 'elementor-pro' ),
                'default' => 'slide',
                'options' => [
                    'slide' => esc_html__( 'Slide', 'elementor-pro' ),
                    'fade' => esc_html__( 'Fade', 'elementor-pro' ),
                    'cube' => esc_html__( 'Cube', 'elementor-pro' ),
                ],
                'frontend_available' => true,
            ]
        );

        $slides_per_view = range( 1, 10 );
        $slides_per_view = array_combine( $slides_per_view, $slides_per_view );

        $this->add_responsive_control(
            'slides_per_view',
            [
                'type' => Controls_Manager::SELECT,
                'label' => esc_html__( 'Slides Per View', 'elementor-pro' ),
                'options' => [ '' => esc_html__( 'Default', 'elementor-pro' ) ] + $slides_per_view,
                'inherit_placeholders' => false,
                'condition' => [
                    'effect' => 'slide',
                ],
                'frontend_available' => true,
            ]
        );

        $this->add_responsive_control(
            'slides_to_scroll',
            [
                'type' => Controls_Manager::SELECT,
                'label' => esc_html__( 'Slides to Scroll', 'elementor-pro' ),
                'description' => esc_html__( 'Set how many slides are scrolled per swipe.', 'elementor-pro' ),
                'options' => [ '' => esc_html__( 'Default', 'elementor-pro' ) ] + $slides_per_view,
                'inherit_placeholders' => false,
                'condition' => [
                    'effect' => 'slide',
                ],
                'frontend_available' => true,
            ]
        );

        $this->add_responsive_control(
            'height',
            [
                'type' => Controls_Manager::SLIDER,
                'label' => esc_html__( 'Height', 'elementor-pro' ),
                'size_units' => [ 'px', 'vh' ],
                'range' => [
                    'px' => [
                        'min' => 100,
                        'max' => 1000,
                    ],
                    'vh' => [
                        'min' => 20,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-main-swiper' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'width',
            [
                'type' => Controls_Manager::SLIDER,
                'label' => esc_html__( 'Width', 'elementor-pro' ),
                'range' => [
                    'px' => [
                        'min' => 100,
                        'max' => 1140,
                    ],
                    '%' => [
                        'min' => 50,
                    ],
                ],
                'size_units' => [ '%', 'px' ],
                'default' => [
                    'unit' => '%',
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-main-swiper' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_additional_options',
            [
                'label' => esc_html__( 'Additional Options', 'elementor-pro' ),
            ]
        );

        $this->add_control(
            'show_arrows',
            [
                'type' => Controls_Manager::SWITCHER,
                'label' => esc_html__( 'Arrows', 'elementor-pro' ),
                'default' => 'yes',
                'label_off' => esc_html__( 'Hide', 'elementor-pro' ),
                'label_on' => esc_html__( 'Show', 'elementor-pro' ),
                'prefix_class' => 'elementor-arrows-',
                'render_type' => 'template',
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'pagination',
            [
                'label' => esc_html__( 'Pagination', 'elementor-pro' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'bullets',
                'options' => [
                    '' => esc_html__( 'None', 'elementor-pro' ),
                    'bullets' => esc_html__( 'Dots', 'elementor-pro' ),
                    'fraction' => esc_html__( 'Fraction', 'elementor-pro' ),
                    'progressbar' => esc_html__( 'Progress', 'elementor-pro' ),
                ],
                'prefix_class' => 'elementor-pagination-type-',
                'render_type' => 'template',
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'speed',
            [
                'label' => esc_html__( 'Transition Duration', 'elementor-pro' ),
                'type' => Controls_Manager::NUMBER,
                'default' => 500,
                'render_type' => 'none',
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'autoplay',
            [
                'label' => esc_html__( 'Autoplay', 'elementor-pro' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'separator' => 'before',
                'render_type' => 'none',
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'autoplay_speed',
            [
                'label' => esc_html__( 'Autoplay Speed', 'elementor-pro' ),
                'type' => Controls_Manager::NUMBER,
                'default' => 5000,
                'condition' => [
                    'autoplay' => 'yes',
                ],
                'render_type' => 'none',
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'loop',
            [
                'label' => esc_html__( 'Infinite Loop', 'elementor-pro' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'pause_on_hover',
            [
                'label' => esc_html__( 'Pause on Hover', 'elementor-pro' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'condition' => [
                    'autoplay' => 'yes',
                ],
                'render_type' => 'none',
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'pause_on_interaction',
            [
                'label' => esc_html__( 'Pause on Interaction', 'elementor-pro' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'condition' => [
                    'autoplay' => 'yes',
                ],
                'render_type' => 'none',
                'frontend_available' => true,
            ]
        );

        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'image_size',
                'default' => 'full',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'lazyload',
            [
                'label' => esc_html__( 'Lazyload', 'elementor-pro' ),
                'type' => Controls_Manager::SWITCHER,
                'separator' => 'before',
                'frontend_available' => true,
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_slides_style',
            [
                'label' => esc_html__( 'Slides', 'elementor-pro' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $space_between_config = [
            'label' => esc_html__( 'Space Between', 'elementor-pro' ),
            'type' => Controls_Manager::SLIDER,
            'range' => [
                'px' => [
                    'max' => 50,
                ],
            ],
            'render_type' => 'none',
            'frontend_available' => true,
        ];

        // TODO: Once Core 3.4.0 is out, get the active devices using Breakpoints/Manager::get_active_devices_list().
        $active_breakpoint_instances = \Elementor\Plugin::instance()->breakpoints->get_active_breakpoints();
        // Devices need to be ordered from largest to smallest.
        $active_devices = array_reverse( array_keys( $active_breakpoint_instances ) );

        // Add desktop in the correct position.
        if ( in_array( 'widescreen', $active_devices, true ) ) {
            $active_devices = array_merge( array_slice( $active_devices, 0, 1 ), [ 'desktop' ], array_slice( $active_devices, 1 ) );
        } else {
            $active_devices = array_merge( [ 'desktop' ], $active_devices );
        }

        foreach ( $active_devices as $active_device ) {
            $space_between_config[ $active_device . '_default' ] = [
                'size' => 10,
            ];
        }

        $this->add_responsive_control(
            'space_between',
            $space_between_config
        );

        $this->add_control(
            'slide_background_color',
            [
                'label' => esc_html__( 'Background Color', 'elementor-pro' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-main-swiper .swiper-slide' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'slide_border_size',
            [
                'label' => esc_html__( 'Border Size', 'elementor-pro' ),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .elementor-main-swiper .swiper-slide' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ],
            ]
        );

        $this->add_control(
            'slide_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'elementor-pro' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    '%' => [
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-main-swiper .swiper-slide' => 'border-radius: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->add_control(
            'slide_border_color',
            [
                'label' => esc_html__( 'Border Color', 'elementor-pro' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-main-swiper .swiper-slide' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'slide_padding',
            [
                'label' => esc_html__( 'Padding', 'elementor-pro' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-main-swiper .swiper-slide' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ],
                'separator' => 'before',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_navigation',
            [
                'label' => esc_html__( 'Navigation', 'elementor-pro' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'heading_arrows',
            [
                'label' => esc_html__( 'Arrows', 'elementor-pro' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'none',
            ]
        );

        $this->add_control(
            'arrows_size',
            [
                'label' => esc_html__( 'Size', 'elementor-pro' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 20,
                ],
                'range' => [
                    'px' => [
                        'min' => 10,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-swiper-button' => 'font-size: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->add_control(
            'arrows_color',
            [
                'label' => esc_html__( 'Color', 'elementor-pro' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-swiper-button' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .elementor-swiper-button svg' => 'fill: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'heading_pagination',
            [
                'label' => esc_html__( 'Pagination', 'elementor-pro' ),
                'type' => Controls_Manager::HEADING,
                'condition' => [
                    'pagination!' => '',
                ],
            ]
        );

        $this->add_control(
            'pagination_position',
            [
                'label' => esc_html__( 'Position', 'elementor-pro' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'outside',
                'options' => [
                    'outside' => esc_html__( 'Outside', 'elementor-pro' ),
                    'inside' => esc_html__( 'Inside', 'elementor-pro' ),
                ],
                'prefix_class' => 'elementor-pagination-position-',
                'condition' => [
                    'pagination!' => '',
                ],
            ]
        );

        $this->add_control(
            'pagination_size',
            [
                'label' => esc_html__( 'Size', 'elementor-pro' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 20,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .swiper-pagination-bullet' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}',
                    '{{WRAPPER}} .swiper-container-horizontal .swiper-pagination-progressbar' => 'height: {{SIZE}}{{UNIT}}',
                    '{{WRAPPER}} .swiper-pagination-fraction' => 'font-size: {{SIZE}}{{UNIT}}',
                ],
                'condition' => [
                    'pagination!' => '',
                ],
            ]
        );

        $this->add_control(
            'pagination_color_inactive',
            [
                'label' => esc_html__( 'Color', 'elementor-pro' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    // The opacity property will override the default inactive dot color which is opacity 0.2.
                    '{{WRAPPER}} .swiper-pagination-bullet:not(.swiper-pagination-bullet-active)' => 'background-color: {{VALUE}}; opacity: 1;',
                ],
                'condition' => [
                    'pagination!' => '',
                ],
            ]
        );

        $this->add_control(
            'pagination_color',
            [
                'label' => esc_html__( 'Active Color', 'elementor-pro' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .swiper-pagination-bullet-active, {{WRAPPER}} .swiper-pagination-progressbar-fill' => 'background-color: {{VALUE}}',
                    '{{WRAPPER}} .swiper-pagination-fraction' => 'color: {{VALUE}}',
                ],
                'condition' => [
                    'pagination!' => '',
                ],
            ]
        );

        $this->end_controls_section();

        $this->update_control(
            'slide_padding',
            [
                'selectors' => [
                    '{{WRAPPER}} .elementor-testimonial__header' => 'padding-top: {{TOP}}{{UNIT}}; padding-left: {{LEFT}}{{UNIT}}; padding-right: {{RIGHT}}{{UNIT}};',
                    '{{WRAPPER}} .elementor-testimonial__content' => 'padding-bottom: {{BOTTOM}}{{UNIT}}; padding-left: {{LEFT}}{{UNIT}}; padding-right: {{RIGHT}}{{UNIT}};',
                ],
            ]
        );

        $this->start_injection( [
            'of' => 'slide_padding',
        ] );

        $this->add_control(
            'heading_header',
            [
                'label' => esc_html__( 'Header', 'tenweb-builder' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'header_background_color',
            [
                'label' => esc_html__( 'Background Color', 'tenweb-builder' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-testimonial__header' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'content_gap',
            [
                'label' => esc_html__( 'Gap', 'tenweb-builder' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-testimonial__header' => 'padding-bottom: calc({{SIZE}}{{UNIT}} / 2)',
                    '{{WRAPPER}} .elementor-testimonial__content' => 'padding-top: calc({{SIZE}}{{UNIT}} / 2)',
                ],
            ]
        );

        $this->add_control(
            'show_separator',
            [
                'label' => esc_html__( 'Separator', 'tenweb-builder' ),
                'type' => Controls_Manager::SWITCHER,
                'label_off' => esc_html__( 'Hide', 'tenweb-builder' ),
                'label_on' => esc_html__( 'Show', 'tenweb-builder' ),
                'default' => 'has-separator',
                'return_value' => 'has-separator',
                'prefix_class' => 'elementor-review--',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'separator_color',
            [
                'label' => esc_html__( 'Color', 'tenweb-builder' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-testimonial__header' => 'border-bottom-color: {{VALUE}}',
                ],
                'condition' => [
                    'show_separator!' => '',
                ],
            ]
        );

        $this->add_control(
            'separator_size',
            [
                'label' => esc_html__( 'Size', 'tenweb-builder' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 20,
                    ],
                ],
                'condition' => [
                    'show_separator!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-testimonial__header' => 'border-bottom-width: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->end_injection();

        $this->start_injection( [
            'at' => 'before',
            'of' => 'section_navigation',
        ] );

        $this->start_controls_section(
            'section_content_style',
            [
                'label' => esc_html__( 'Text', 'tenweb-builder' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'name_title_style',
            [
                'label' => esc_html__( 'Name', 'tenweb-builder' ),
                'type' => Controls_Manager::HEADING,
            ]
        );

        $this->add_control(
            'name_color',
            [
                'label' => esc_html__( 'Color', 'tenweb-builder' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-testimonial__name' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'name_typography',
                'selector' => '{{WRAPPER}} .elementor-testimonial__header, {{WRAPPER}} .elementor-testimonial__name',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
                ],
            ]
        );

        $this->add_control(
            'heading_title_style',
            [
                'label' => esc_html__( 'Title', 'tenweb-builder' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => esc_html__( 'Color', 'tenweb-builder' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-testimonial__title' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .elementor-testimonial__title',
            ]
        );

        $this->add_control(
            'heading_review_style',
            [
                'label' => esc_html__( 'Review', 'tenweb-builder' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'content_color',
            [
                'label' => esc_html__( 'Color', 'tenweb-builder' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-testimonial__text' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'content_typography',
                'selector' => '{{WRAPPER}} .elementor-testimonial__text',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_TEXT,
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_image_style',
            [
                'label' => esc_html__( 'Image', 'tenweb-builder' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'image_size',
            [
                'label' => esc_html__( 'Size', 'tenweb-builder' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 70,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-testimonial__image img' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'image_gap',
            [
                'label' => esc_html__( 'Gap', 'tenweb-builder' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    'body:not(.rtl) {{WRAPPER}} .elementor-testimonial__image + cite' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: 0;',
                    'body.rtl {{WRAPPER}} .elementor-testimonial__image + cite' => 'margin-right: {{SIZE}}{{UNIT}}; margin-left:0;',
                ],
            ]
        );

        $this->add_control(
            'image_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'tenweb-builder' ),
                'type' => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .elementor-testimonial__image img' => 'border-radius: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_icon_style',
            [
                'label' => esc_html__( 'Icon', 'tenweb-builder' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'icon_color',
            [
                'label' => esc_html__( 'Color', 'tenweb-builder' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'default',
                'options' => [
                    'default' => esc_html__( 'Official', 'tenweb-builder' ),
                    'custom' => esc_html__( 'Custom', 'tenweb-builder' ),
                ],
            ]
        );

        $this->add_control(
            'icon_custom_color',
            [
                'label' => esc_html__( 'Color', 'tenweb-builder' ),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'icon_color' => 'custom',
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-testimonial__icon:not(.elementor-testimonial__rating)' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .elementor-testimonial__icon:not(.elementor-testimonial__rating) svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'icon_size',
            [
                'label' => esc_html__( 'Size', 'tenweb-builder' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-testimonial__icon' => 'font-size: {{SIZE}}{{UNIT}}',
                    '{{WRAPPER}} .elementor-testimonial__icon svg' => 'width: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_rating_style',
            [
                'label' => esc_html__( 'Rating', 'tenweb-builder' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'star_style',
            [
                'label' => esc_html__( 'Icon', 'tenweb-builder' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'star_fontawesome' => 'Font Awesome',
                    'star_unicode' => 'Unicode',
                ],
                'default' => 'star_fontawesome',
                'render_type' => 'template',
                'prefix_class' => 'elementor--star-style-',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'unmarked_star_style',
            [
                'label' => esc_html__( 'Unmarked Style', 'tenweb-builder' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'solid' => [
                        'title' => esc_html__( 'Solid', 'tenweb-builder' ),
                        'icon' => 'eicon-star',
                    ],
                    'outline' => [
                        'title' => esc_html__( 'Outline', 'tenweb-builder' ),
                        'icon' => 'eicon-star-o',
                    ],
                ],
                'default' => 'solid',
            ]
        );

        $this->add_control(
            'star_size',
            [
                'label' => esc_html__( 'Size', 'tenweb-builder' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-star-rating' => 'font-size: {{SIZE}}{{UNIT}}',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'star_space',
            [
                'label' => esc_html__( 'Spacing', 'tenweb-builder' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    'body:not(.rtl) {{WRAPPER}} .elementor-star-rating i:not(:last-of-type)' => 'margin-right: {{SIZE}}{{UNIT}}',
                    'body.rtl {{WRAPPER}} .elementor-star-rating i:not(:last-of-type)' => 'margin-left: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->add_control(
            'stars_color',
            [
                'label' => esc_html__( 'Color', 'tenweb-builder' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-star-rating i:before' => 'color: {{VALUE}}',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'stars_unmarked_color',
            [
                'label' => esc_html__( 'Unmarked Color', 'tenweb-builder' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-star-rating i' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();

        $this->end_injection();

        $this->update_responsive_control(
            'width',
            [
                'selectors' => [
                    '{{WRAPPER}}.elementor-arrows-yes .elementor-main-swiper' => 'width: calc( {{SIZE}}{{UNIT}} - 40px )',
                    '{{WRAPPER}} .elementor-main-swiper' => 'width: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->update_responsive_control(
            'slides_per_view',
            [
                'condition' => null,
            ]
        );

        $this->update_control(
            'slides_to_scroll',
            [
                'condition' => null,
            ]
        );

        $this->remove_control( 'effect' );
        $this->remove_responsive_control( 'height' );
        $this->remove_control( 'pagination_position' );
    }

    protected function print_slider( array $settings = null ) {
        if ( null === $settings ) {
            $settings = $this->get_settings_for_display();
        }

        $default_settings = [
            'container_class' => 'elementor-main-swiper',
            'video_play_icon' => true,
        ];

        $settings = array_merge( $default_settings, $settings );

        $slides_count = count( $settings['slides'] );
        ?>
        <div class="elementor-swiper">
            <div class="<?php echo esc_attr( $settings['container_class'] ); ?> swiper-container">
                <div class="swiper-wrapper">
                    <?php
                    foreach ( $settings['slides'] as $index => $slide ) :
                        $this->slide_prints_count++;
                        ?>
                        <div class="swiper-slide">
                            <?php $this->print_slide( $slide, $settings, 'slide-' . $index . '-' . $this->slide_prints_count ); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php if ( 1 < $slides_count ) : ?>
                    <?php if ( $settings['pagination'] ) : ?>
                        <div class="swiper-pagination"></div>
                    <?php endif; ?>
                    <?php if ( $settings['show_arrows'] ) : ?>
                        <div class="elementor-swiper-button elementor-swiper-button-prev">
                            <?php $this->render_swiper_button( 'previous' ); ?>
                            <span class="elementor-screen-only"><?php echo esc_html__( 'Previous', 'elementor-pro' ); ?></span>
                        </div>
                        <div class="elementor-swiper-button elementor-swiper-button-next">
                            <?php $this->render_swiper_button( 'next' ); ?>
                            <span class="elementor-screen-only"><?php echo esc_html__( 'Next', 'elementor-pro' ); ?></span>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }

    protected function get_slide_image_url( $slide, array $settings ) {
        $image_url = Group_Control_Image_Size::get_attachment_image_src( $slide['image']['id'], 'image_size', $settings );

        if ( ! $image_url ) {
            $image_url = $slide['image']['url'];
        }

        return $image_url;
    }

    protected function get_slide_image_alt_attribute( $slide ) {
        if ( ! empty( $slide['name'] ) ) {
            return $slide['name'];
        }

        if ( ! empty( $slide['image']['alt'] ) ) {
            return $slide['image']['alt'];
        }

        return '';
    }

    private function render_swiper_button( $type ) {
        $direction = 'next' === $type ? 'right' : 'left';

        if ( is_rtl() ) {
            $direction = 'right' === $direction ? 'left' : 'right';
        }

        $icon_value = 'eicon-chevron-' . $direction;

        Icons_Manager::render_icon( [
            'library' => 'eicons',
            'value' => $icon_value,
        ], [ 'aria-hidden' => 'true' ] );
    }

    public function get_inline_css_depends() {
        $slides = $this->get_settings_for_display( 'slides' );

        foreach ( $slides as $slide ) {
            if ( $slide['rating'] ) {
                return [
                    [
                        'name' => 'star-rating',
                        'is_core_dependency' => true,
                    ],
                ];
            }
        }

        return [];
    }

    protected function add_repeater_controls( Repeater $repeater ) {
        $repeater->add_control(
            'image',
            [
                'label' => esc_html__( 'Image', 'tenweb-builder' ),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $repeater->add_control(
            'name',
            [
                'label' => esc_html__( 'Name', 'tenweb-builder' ),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__( 'John Doe', 'tenweb-builder' ),
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $repeater->add_control(
            'title',
            [
                'label' => esc_html__( 'Title', 'tenweb-builder' ),
                'type' => Controls_Manager::TEXT,
                'default' => '@username',
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $repeater->add_control(
            'rating',
            [
                'label' => esc_html__( 'Rating', 'tenweb-builder' ),
                'type' => Controls_Manager::NUMBER,
                'min' => 0,
                'max' => 5,
                'step' => 0.1,
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $repeater->add_control(
            'selected_social_icon',
            [
                'label' => esc_html__( 'Icon', 'tenweb-builder' ),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'social_icon',
                'default' => [
                    'value' => 'fab fa-twitter',
                    'library' => 'fa-brands',
                ],
                'recommended' => [
                    'fa-solid' => [
                        'rss',
                        'shopping-cart',
                        'thumbtack',
                    ],
                    'fa-brands' => [
                        'android',
                        'apple',
                        'behance',
                        'bitbucket',
                        'codepen',
                        'delicious',
                        'digg',
                        'dribbble',
                        'envelope',
                        'facebook',
                        'flickr',
                        'foursquare',
                        'github',
                        'google-plus',
                        'houzz',
                        'instagram',
                        'jsfiddle',
                        'linkedin',
                        'medium',
                        'meetup',
                        'mix',
                        'mixcloud',
                        'odnoklassniki',
                        'pinterest',
                        'product-hunt',
                        'reddit',
                        'skype',
                        'slideshare',
                        'snapchat',
                        'soundcloud',
                        'spotify',
                        'stack-overflow',
                        'steam',
                        'telegram',
                        'tripadvisor',
                        'tumblr',
                        'twitch',
                        'twitter',
                        'vimeo',
                        'fa-vk',
                        'weibo',
                        'weixin',
                        'whatsapp',
                        'wordpress',
                        'xing',
                        'yelp',
                        'youtube',
                        '500px',
                    ],
                ],
            ]
        );

        $repeater->add_control(
            'link',
            [
                'label' => esc_html__( 'Link', 'tenweb-builder' ),
                'type' => Controls_Manager::URL,
                'dynamic' => [
                    'active' => true,
                ],
                'placeholder' => esc_html__( 'https://your-link.com', 'tenweb-builder' ),

            ]
        );

        $repeater->add_control(
            'content',
            [
                'label' => esc_html__( 'Review', 'tenweb-builder' ),
                'type' => Controls_Manager::TEXTAREA,
                'default' => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'tenweb-builder' ),
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );
    }

    protected function get_repeater_defaults() {
        $placeholder_image_src = Utils::get_placeholder_image_src();

        return [
            [
                'content' => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'tenweb-builder' ),
                'name' => esc_html__( 'John Doe', 'tenweb-builder' ),
                'title' => '@username',
                'image' => [
                    'url' => $placeholder_image_src,
                ],
            ],
            [
                'content' => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'tenweb-builder' ),
                'name' => esc_html__( 'John Doe', 'tenweb-builder' ),
                'title' => '@username',
                'image' => [
                    'url' => $placeholder_image_src,
                ],
            ],
            [
                'content' => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'tenweb-builder' ),
                'name' => esc_html__( 'John Doe', 'tenweb-builder' ),
                'title' => '@username',
                'image' => [
                    'url' => $placeholder_image_src,
                ],
            ],
        ];
    }

    private function print_cite( $slide, $settings ) {
        if ( empty( $slide['name'] ) && empty( $slide['title'] ) ) {
            return '';
        }

        $html = '<cite class="elementor-testimonial__cite">';

        if ( ! empty( $slide['name'] ) ) {
            $html .= '<span class="elementor-testimonial__name">' . $slide['name'] . '</span>';
        }

        if ( ! empty( $slide['rating'] ) ) {
            $html .= $this->render_stars( $slide, $settings );
        }

        if ( ! empty( $slide['title'] ) ) {
            $html .= '<span class="elementor-testimonial__title">' . $slide['title'] . '</span>';
        }
        $html .= '</cite>';

        // PHPCS - the main text of a widget should not be escaped.
        echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    protected function render_stars( $slide, $settings ) {
        $icon = '&#xE934;';

        if ( 'star_fontawesome' === $settings['star_style'] ) {
            if ( 'outline' === $settings['unmarked_star_style'] ) {
                $icon = '&#xE933;';
            }
        } elseif ( 'star_unicode' === $settings['star_style'] ) {
            $icon = '&#9733;';

            if ( 'outline' === $settings['unmarked_star_style'] ) {
                $icon = '&#9734;';
            }
        }

        $rating = (float) $slide['rating'] > 5 ? 5 : $slide['rating'];
        $floored_rating = (int) $rating;
        $stars_html = '';

        for ( $stars = 1; $stars <= 5; $stars++ ) {
            if ( $stars <= $floored_rating ) {
                $stars_html .= '<i class="elementor-star-full">' . $icon . '</i>';
            } elseif ( $floored_rating + 1 === $stars && $rating !== $floored_rating ) {
                $stars_html .= '<i class="elementor-star-' . ( $rating - $floored_rating ) * 10 . '">' . $icon . '</i>';
            } else {
                $stars_html .= '<i class="elementor-star-empty">' . $icon . '</i>';
            }
        }

        return '<div class="elementor-star-rating">' . $stars_html . '</div>';
    }

    private function print_icon( $slide, $element_key ) {
        $migration_allowed = Icons_Manager::is_migration_allowed();
        if ( ! isset( $slide['social_icon'] ) && ! $migration_allowed ) {
            // add old default
            $slide['social_icon'] = 'fa fa-twitter';
        }

        if ( empty( $slide['social_icon'] ) && empty( $slide['selected_social_icon'] ) ) {
            return '';
        }

        $migrated = isset( $slide['__fa4_migrated']['selected_social_icon'] );
        $is_new = empty( $slide['social_icon'] ) && $migration_allowed;
        $social = '';

        if ( $is_new || $migrated ) {
            ob_start();
            Icons_Manager::render_icon( $slide['selected_social_icon'], [ 'aria-hidden' => 'true' ] );
            $icon = ob_get_clean();
        } else {
            $icon = '<i class="' . esc_attr( $slide['social_icon'] ) . '" aria-hidden="true"></i>';
        }

        if ( ! empty( $slide['social_icon'] ) ) {
            $social = str_replace( 'fa fa-', '', $slide['social_icon'] );
        }

        if ( ( $is_new || $migrated ) && 'svg' !== $slide['selected_social_icon']['library'] ) {
            $social = explode( ' ', $slide['selected_social_icon']['value'], 2 );
            if ( empty( $social[1] ) ) {
                $social = '';
            } else {
                $social = str_replace( 'fa-', '', $social[1] );
            }
        }
        if ( 'svg' === $slide['selected_social_icon']['library'] ) {
            $social = '';
        }

        $this->add_render_attribute( 'icon_wrapper_' . $element_key, 'class', 'elementor-testimonial__icon elementor-icon' );

        $icon .= '<span class="elementor-screen-only">' . esc_html__( 'Read More', 'tenweb-builder' ) . '</span>';
        $this->add_render_attribute( 'icon_wrapper_' . $element_key, 'class', 'elementor-icon-' . $social );

        // Icon is escaped above, get_render_attribute_string() is safe
        echo '<div ' . $this->get_render_attribute_string( 'icon_wrapper_' . $element_key ) . '>' . $icon . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    protected function print_slide( array $slide, array $settings, $element_key ) {
        $lazyload = 'yes' === $this->get_settings( 'lazyload' );

        $this->add_render_attribute( $element_key . '-testimonial', [
            'class' => 'elementor-testimonial',
        ] );

        $this->add_render_attribute( $element_key . '-testimonial', [
            'class' => 'elementor-repeater-item-' . $slide['_id'],
        ] );

        if ( ! empty( $slide['image']['url'] ) ) {
            $img_src = $this->get_slide_image_url( $slide, $settings );
            $img_attribute = [];
            if ( $lazyload ) {
                $img_attribute['class'] = 'swiper-lazy';
                $img_attribute['data-src'] = $img_src;
            } else {
                $img_attribute['src'] = $img_src;
            }

            $img_attribute['alt'] = $this->get_slide_image_alt_attribute( $slide );

            $this->add_render_attribute( $element_key . '-image', $img_attribute );
        }

        ?>
    <div <?php $this->print_render_attribute_string( $element_key . '-testimonial' ); ?>>
        <?php if ( $slide['image']['url'] || ! empty( $slide['name'] ) || ! empty( $slide['title'] ) ) :

            $link_url = empty( $slide['link']['url'] ) ? false : $slide['link']['url'];
            $header_tag = ! empty( $link_url ) ? 'a' : 'div';
            $header_element = 'header_' . $slide['_id'];

            $this->add_render_attribute( $header_element, 'class', 'elementor-testimonial__header' );

            if ( ! empty( $link_url ) ) {
                $this->add_link_attributes( $header_element, $slide['link'] );
            }
            ?>
            <<?php Utils::print_validated_html_tag( $header_tag ); ?> <?php $this->print_render_attribute_string( $header_element ); ?>>
            <?php if ( $slide['image']['url'] ) : ?>
            <div class="elementor-testimonial__image">
                <img <?php $this->print_render_attribute_string( $element_key . '-image' ); ?>>
                <?php if ( $lazyload ) : ?>
                    <div class="swiper-lazy-preloader"></div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
            <?php $this->print_cite( $slide, $settings ); ?>
            <?php $this->print_icon( $slide, $element_key ); ?>
            </<?php Utils::print_validated_html_tag( $header_tag ); ?>>
        <?php endif; ?>
        <?php if ( $slide['content'] ) : ?>
            <div class="elementor-testimonial__content">
                <div class="elementor-testimonial__text">
                    <?php
                    // Main content allowed
                    echo $slide['content']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </div>
            </div>
        <?php endif; ?>
        </div>
        <?php
    }

    protected function render() {
        $this->print_slider();
    }
}

\Elementor\Plugin::instance()->widgets_manager->register(new Reviews());
