<?php
namespace Tenweb_Builder;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Widget_Base;
use Elementor\Icons_Manager;

if ( ! defined( 'ABSPATH' ) ) exit;

class Testimonial_Carousel extends Widget_Base {
	
	private $slide_prints_count = 0;

	public function get_name() {
		return Builder::$prefix . '-testimonial-carousel';
	}

	public function get_title() {
		return __( 'Testimonials', 'tenweb-builder' );
	}

	public function get_icon() {
		return 'twbb-testimonial-carousel twbb-widget-icon';
	}
	
	public function get_categories() {
		return ['tenweb-widgets'];
	}

    private function add_starts_style() {
        $this->start_controls_section(
            'section_icon_style',
            [
                'label' => esc_html__( 'Rating stars icon', 'tenweb-builder' ),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => ['tenweb_enable_stars' => 'yes', 'view_type' => 'masonry']
            ]
        );

        $this->add_responsive_control(
            'stars_gap',
            [
                'label' => __( 'Gap', 'tenweb-builder' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [ 'unit' => 'px', 'size' => 32 ],
                'selectors' => [
                    '{{WRAPPER}}.elementor-widget-twbb-testimonial-carousel .e-rating' => 'margin-bottom: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'icon_size',
            [
                'label' => esc_html__( 'Size', 'tenweb-builder' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'em' => [
                        'min' => 0,
                        'max' => 10,
                        'step' => 0.1,
                    ],
                    'rem' => [
                        'min' => 0,
                        'max' => 10,
                        'step' => 0.1,
                    ],
                ],
                'size_units' => [ 'px', 'em', 'rem', 'vw', 'custom' ],
                'default' => [ 'unit' => 'px', 'size' => 20 ],
                'selectors' => [
                    '{{WRAPPER}}' => '--e-rating-icon-font-size: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'icon_gap',
            [
                'label' => esc_html__( 'Spacing', 'tenweb-builder' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'em' => [
                        'min' => 0,
                        'max' => 10,
                        'step' => 0.1,
                    ],
                    'rem' => [
                        'min' => 0,
                        'max' => 10,
                        'step' => 0.1,
                    ],
                ],
                'size_units' => [ 'px', 'em', 'rem', 'vw', 'custom' ],
                'default' => [ 'unit' => 'px', 'size' => 4 ],
                'selectors' => [
                    '{{WRAPPER}}' => '--e-rating-gap: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->add_control(
            'icon_color',
            [
                'label' => esc_html__( 'Color', 'tenweb-builder' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#000000',
                'selectors' => [
                    '{{WRAPPER}}' => '--e-rating-icon-marked-color: {{VALUE}}',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'icon_unmarked_color',
            [
                'label' => esc_html__( 'Unmarked Color', 'tenweb-builder' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}}' => '--e-rating-icon-color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function register_starts_controls() {
        $start_logical = is_rtl() ? 'end' : 'start';
        $end_logical = is_rtl() ? 'start' : 'end';

        $this->start_controls_section(
            'section_rating',
            [
                'label' => esc_html__( 'Stars', 'elementor' ),
                'condition' => ['view_type' => 'masonry']
            ]
        );

        $this->add_control(
            'tenweb_enable_stars',
            [
                'label' => __( 'Show stars', 'tenweb-builder' ),
                'type' => Controls_Manager::SWITCHER,
                'label_off' => __( 'Off', 'tenweb-builder' ),
                'label_on' => __( 'On', 'tenweb-builder' ),
                'default' => 'yes',
                'return_value' => 'yes',
            ]
        );

        $this->add_control(
            'rating_scale',
            [
                'label' => esc_html__( 'Rating Scale', 'elementor' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 10,
                    ],
                ],
                'step' => 1,
                'default' => [
                    'size' => '5',
                ],
                'condition' => ['tenweb_enable_stars' => 'yes']
            ]
        );


        $this->add_control(
            'rating_value',
            [
                'label' => esc_html__( 'Rating', 'elementor' ),
                'type' => Controls_Manager::NUMBER,
                'min' => 0,
                'step' => 0.5,
                'dynamic' => [
                    'active' => true,
                ],
                'condition' => ['tenweb_enable_stars' => 'yes']
            ]
        );

        $this->add_control(
            'rating_icon',
            [
                'label' => esc_html__( 'Icon', 'elementor' ),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'icon',
                'skin' => 'inline',
                'label_block' => false,
                'skin_settings' => [
                    'inline' => [
                        'icon' => [
                            'icon' => 'eicon-star',
                        ],
                    ],
                ],
                'default' => [
                    'value' => 'eicon-star',
                    'library' => 'eicons',
                ],
                'separator' => 'before',
                'exclude_inline_options' => [ 'none' ],
                'condition' => ['tenweb_enable_stars' => 'yes']
            ]
        );


        $this->add_responsive_control( 'icon_alignment', [
            'label' => esc_html__( 'Alignment', 'elementor' ),
            'type' => Controls_Manager::CHOOSE,
            'options' => [
                'start' => [
                    'title' => esc_html__( 'Start', 'elementor' ),
                    'icon' => "eicon-align-$start_logical-h",
                ],
                'center' => [
                    'title' => esc_html__( 'Center', 'elementor' ),
                    'icon' => 'eicon-align-center-h',
                ],
                'end' => [
                    'title' => esc_html__( 'End', 'elementor' ),
                    'icon' => "eicon-align-$end_logical-h",
                ],
            ],
            'selectors_dictionary' => [
                'start' => '--e-rating-justify-content: flex-start;',
                'center' => '--e-rating-justify-content: center;',
                'end' => '--e-rating-justify-content: flex-end;',
            ],
            'selectors' => [
                '{{WRAPPER}}' => '{{VALUE}}',
            ],
            'separator' => 'before',
            'condition' => ['tenweb_enable_stars' => 'yes']
        ] );

        $this->end_controls_section();

    }

    /**
     * Add Elementor controls that are specific to the masonry view.
     *
     * @param $control_type string which is detect what type of control should work
     * @param $control_id string unique id of control
     *
    */
    protected function add_masonry_control( $control_type, $control_id ) {
        $slides_per_view = range( 1, 10 );
        $slides_per_view = array_combine( $slides_per_view, $slides_per_view );

        $args = [
            'column_count_masonry' => [
                'type' => Controls_Manager::SELECT,
                'label' => __( 'Columns count', 'tenweb-builder' ),
                'options' => [ '' => __( 'Default', 'tenweb-builder' ) ] + $slides_per_view,
                'devices' => [ 'desktop', 'tablet', 'mobile' ],
                'desktop_default' => 3,
                'tablet_default' => 2,
                'mobile_default' => 1,
                'condition' => [
                    'view_type' => 'masonry',
                ],
                'frontend_available' => true,
                'selectors' => [
                    '{{WRAPPER}} .tenweb-masonry ' => 'grid-template-columns: repeat({{UNIT}}, 1fr);',
                ],
            ],
            'space_between_masonry' => [
                'label' => __( 'Space Between', 'tenweb-builder' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 50,
                    ],
                ],
                'desktop_default' => [
                    'size' => 32,
                ],
                'tablet_default' => [
                    'size' => 10,
                ],
                'mobile_default' => [
                    'size' => 10,
                ],
                'render_type' => 'ui',
                'frontend_available' => true,
                'condition' => ['view_type' => 'masonry'],
                'selectors' => [
                    '{{WRAPPER}} .tenweb-masonry' => 'grid-gap: {{SIZE}}px !important;',
                ],
            ],
            'slide_border_size_masonry' => [
                'label' => __( 'Border Size', 'tenweb-builder' ),
                'type' => Controls_Manager::DIMENSIONS,
                'default' => [
                    'top' => '1',
                    'bottom' => '1',
                    'left' => '1',
                    'right' => '1',
                    'unit' => 'px',
                ],
                'condition' => ['view_type' => 'masonry'],
                'selectors' => [
                    '{{WRAPPER}} .tenweb-masonry .tenweb-item' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ],
            ],
            'slide_padding_masonry' => [
                'label' => __( 'Padding', 'tenweb-builder' ),
                'type' => Controls_Manager::DIMENSIONS,
                'default' => [
                    'top' => '32',
                    'bottom' => '32',
                    'left' => '32',
                    'right' => '32',
                    'unit' => 'px',
                ],
                'condition' => ['view_type' => 'masonry'],
                'selectors' => [
                    '{{WRAPPER}} .tenweb-masonry .tenweb-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ],
                'separator' => 'before',
            ],
            'alignment_masonry' => [
                'label' => __( 'Alignment', 'tenweb-builder' ),
                'type' => Controls_Manager::CHOOSE,
                'label_block' => false,
                'default' => 'left',
                'options' => [
                    'left' => [
                        'title' => __( 'Left', 'tenweb-builder' ),
                        'icon' => 'fa fa-align-left',
                    ],
                    'center' => [
                        'title' => __( 'Center', 'tenweb-builder' ),
                        'icon' => 'fa fa-align-center',
                    ],
                    'right' => [
                        'title' => __( 'Right', 'tenweb-builder' ),
                        'icon' => 'fa fa-align-right',
                    ],
                ],
                'condition' => ['view_type' => 'masonry'],
                'prefix_class' => 'tenweb-testimonial--align-',
            ],
            'content_typography_masonry' => [
                'name' => 'content_typography_masonry',
                'fields_options' => [
                    'typography' => ['default' => 'yes'],
                    'font_size' => [
                        'default' => ['unit' => 'px', 'size' => 18]
                    ],
                    'line_height' => [
                        'default' => ['unit' => 'px', 'size' => 27]
                    ],
                    'font_weight' => ['default' =>  '400'],
                    'font_family' => ['default' => 'Roboto'],
                ],
                'condition' => ['view_type' => 'masonry'],
                'selector' => '{{WRAPPER}} .tenweb-testimonial__text',
            ],
            'name_typography_masonry' => [
                'name' => 'name_typography_masonry',
                'fields_options' => [
                    'typography' => ['default' => 'yes'],
                    'font_size' => [
                        'default' => ['unit' => 'px', 'size' => 16]
                    ],
                    'line_height' => [
                        'default' => ['unit' => 'px', 'size' => 24]
                    ],
                    'font_weight' => ['default' =>  '600'],
                    'font_family' => ['default' => 'Roboto'],
                ],
                'condition' => ['view_type' => 'masonry'],
                'selector' => '{{WRAPPER}} .tenweb-testimonial__name',
            ],
            'title_typography_masonry' => [
                'name' => 'title_typography_masonry',
                'condition' => ['view_type' => 'masonry'],
                'selector' => '{{WRAPPER}} .tenweb-testimonial__title',
                'fields_options' => [
                    'typography' => ['default' => 'yes'],
                    'font_size' => [
                        'default' => ['unit' => 'px', 'size' => 16],
                        'size_units' => [ 'px' ],
                    ],
                    'line_height' => [
                        'default' => ['unit' => 'px', 'size' => 24],
                        'size_units' => [ 'px' ],
                    ],
                    'font_weight' => ['default' =>  '400'],
                    'font_family' => ['default' => 'Roboto'],
                ],
            ],
        ];

        if( !isset($args[$control_id]) ) return;

        if ( $control_type === 'add_responsive_control' ) {
            $this->add_responsive_control( $control_id, $args[$control_id] );
        } elseif ( $control_type === 'add_control' ) {
            $this->add_control( $control_id, $args[$control_id] );
        } elseif ( $control_type === 'add_group_control' ) {
            $this->add_group_control( Group_Control_Typography::get_type(), $args[$control_id] );
        }
    }

    protected function register_controls() {

		$this->start_controls_section(
			'section_slides',
			[
				'label' => __( 'Slides', 'tenweb-builder' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new Repeater();

		$this->add_repeater_controls( $repeater );

        $this->add_control(
            'view_type',
            [
                'type' => Controls_Manager::SELECT,
                'label' => __( 'View Type', 'tenweb-builder' ),
                'default' => 'carousel',
                'options' => [
                    'carousel' => esc_html__( 'Carousel', 'tenweb-builder' ),
                    'masonry' => esc_html__( 'Masonry', 'tenweb-builder' ),
                ],
                'frontend_available' => true,
            ]
        );


        $this->add_control(
			'slides',
			[
				'label' => __( 'Slides', 'tenweb-builder' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => $this->default_slides(),
			]
		);

		$this->add_control(
			'effect',
			[
				'type' => Controls_Manager::SELECT,
				'label' => __( 'Effect', 'tenweb-builder' ),
				'default' => 'slide',
				'options' => [
					'slide' => __( 'Slide', 'tenweb-builder' ),
					'fade' => __( 'Fade', 'tenweb-builder' ),
					'cube' => __( 'Cube', 'tenweb-builder' ),
				],
				'separator' => 'before',
				'frontend_available' => true,
			]
		);

		$slides_per_view = range( 1, 10 );
		$slides_per_view = array_combine( $slides_per_view, $slides_per_view );

		$this->add_responsive_control(
			'slides_per_view',
			[
				'type' => Controls_Manager::SELECT,
				'label' => __( 'Slides Per View', 'tenweb-builder' ),
				'options' => [ '' => __( 'Default', 'tenweb-builder' ) ] + $slides_per_view,
				'condition' => [
                    'view_type!' => 'masonry',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'slides_to_scroll',
			[
				'type' => Controls_Manager::SELECT,
				'label' => __( 'Slides to Scroll', 'tenweb-builder' ),
				'description' => __( 'Set how many slides are scrolled per swipe.', 'tenweb-builder' ),
				'options' => [ '' => __( 'Default', 'tenweb-builder' ) ] + $slides_per_view,
				'condition' => [
                    'view_type!' => 'masonry',
				],
				'frontend_available' => true,
			]
		);

        $this->add_masonry_control( 'add_responsive_control', 'column_count_masonry' );

        $this->add_responsive_control(
			'height',
			[
				'type' => Controls_Manager::SLIDER,
				'label' => __( 'Height', 'tenweb-builder' ),
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
					'{{WRAPPER}} .tenweb-testimonial-carousel-swiper' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'width',
			[
				'type' => Controls_Manager::SLIDER,
				'label' => __( 'Width', 'tenweb-builder' ),
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
					'{{WRAPPER}} .tenweb-testimonial-carousel-swiper' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

        $this->register_starts_controls();

		$this->start_controls_section(
			'section_additional_options',
			[
				'label' => __( 'Additional Options', 'tenweb-builder' ),
                'condition' => ['view_type' => 'carousel']
			]
		);

		$this->add_control(
			'show_arrows',
			[
				'type' => Controls_Manager::SWITCHER,
				'label' => __( 'Arrows', 'tenweb-builder' ),
				'default' => 'yes',
				'label_off' => __( 'Hide', 'tenweb-builder' ),
				'label_on' => __( 'Show', 'tenweb-builder' ),
				'frontend_available' => true,
				'prefix_class' => 'tenweb-arrows-',
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'pagination',
			[
				'label' => __( 'Pagination', 'tenweb-builder' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'bullets',
				'options' => [
					'' => __( 'None', 'tenweb-builder' ),
					'bullets' => __( 'Dots', 'tenweb-builder' ),
					'fraction' => __( 'Fraction', 'tenweb-builder' ),
				],
				'prefix_class' => 'tenweb-pagination-type-',
				'render_type' => 'template',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'speed',
			[
				'label' => __( 'Transition Duration', 'tenweb-builder' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 500,
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label' => __( 'Autoplay', 'tenweb-builder' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'separator' => 'before',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'autoplay_speed',
			[
				'label' => __( 'Autoplay Speed', 'tenweb-builder' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 5000,
				'condition' => [
					'autoplay' => 'yes',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'loop',
			[
				'label' => __( 'Infinite Loop', 'tenweb-builder' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'pause_on_interaction',
			[
				'label' => __( 'Pause on Interaction', 'tenweb-builder' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => [
					'autoplay' => 'yes',
				],
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

		$this->end_controls_section();

		$this->start_controls_section(
			'section_slides_style',
			[
				'label' => __( 'Slides', 'tenweb-builder' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

        $this->add_responsive_control(
			'space_between',
			[
                'label' => __( 'Space Between', 'tenweb-builder' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 50,
                    ],
                ],
                'desktop_default' => [
                    'size' => 10,
                ],
                'tablet_default' => [
                    'size' => 10,
                ],
                'mobile_default' => [
                    'size' => 10,
                ],
                'render_type' => 'ui',
                'frontend_available' => true,
                'condition' => ['view_type' => 'carousel'],
                'selectors' => [
                    '{{WRAPPER}} .tenweb-testimonial-carousel-swiper .swiper-slide' => 'margin-right: {{SIZE}}{{UNIT}} !important;',
                ],
			]
		);

        $this->add_masonry_control( 'add_responsive_control', 'space_between_masonry' );

		$this->add_control(
			'slide_background_color',
			[
				'label' => __( 'Background Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tenweb-testimonial-carousel-swiper .swiper-slide' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .tenweb-masonry .tenweb-item' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'slide_border_size',
			[
                'label' => __( 'Border Size', 'tenweb-builder' ),
                'type' => Controls_Manager::DIMENSIONS,
                'condition' => ['view_type' => 'carousel'],
                'selectors' => [
                    '{{WRAPPER}} .tenweb-testimonial-carousel-swiper .swiper-slide' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ],
			]
		);

        $this->add_masonry_control( 'add_control', 'slide_border_size_masonry' );


        $this->add_control(
			'slide_border_color',
			[
				'label' => __( 'Border Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
                'default' => '#000000',
				'selectors' => [
					'{{WRAPPER}} .tenweb-testimonial-carousel-swiper .swiper-slide' => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .tenweb-masonry .tenweb-item' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'slide_padding',
			[
                'label' => __( 'Padding', 'tenweb-builder' ),
                'type' => Controls_Manager::DIMENSIONS,
                'condition' => ['view_type' => 'carousel'],
                'selectors' => [
                    '{{WRAPPER}} .tenweb-testimonial-carousel-swiper .swiper-slide' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ],
                'separator' => 'before',
			]
		);

        $this->add_masonry_control( 'add_control', 'slide_padding_masonry' );

		$this->add_control(
			'slide_border_radius',
			[
				'label' => __( 'Border Radius', 'tenweb-builder' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'%' => [
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tenweb-testimonial-carousel-swiper .swiper-slide' => 'border-radius: {{SIZE}}{{UNIT}}',
                    '{{WRAPPER}} .tenweb-masonry .tenweb-item' => 'border-radius: {{SIZE}}{{UNIT}}',
				],
			]
		);

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'box_shadow_massonry',
                'selector' => '{{WRAPPER}} .tenweb-masonry .tenweb-item',
                'condition' => ['view_type' => 'masonry'],
            ]
        );


        $this->end_controls_section();

        $this->add_starts_style();

		$this->start_controls_section(
			'section_navigation',
			[
				'label' => __( 'Navigation', 'tenweb-builder' ),
				'tab' => Controls_Manager::TAB_STYLE,
                'condition' => ['view_type' => 'carousel'],
			]
		);

		$this->add_control(
			'heading_arrows',
			[
				'label' => __( 'Arrows', 'tenweb-builder' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'none',
			]
		);

		$this->add_control(
			'arrows_size',
			[
				'label' => __( 'Size', 'tenweb-builder' ),
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
					'{{WRAPPER}} .tenweb-swiper-button' => 'font-size: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'arrows_color',
			[
				'label' => __( 'Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tenweb-swiper-button' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'heading_pagination',
			[
				'label' => __( 'Pagination', 'tenweb-builder' ),
				'type' => Controls_Manager::HEADING,
				'condition' => [
					'pagination!' => '',
				],
			]
		);

		$this->add_control(
			'pagination_position',
			[
				'label' => __( 'Position', 'tenweb-builder' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'outside',
				'options' => [
					'outside' => __( 'Outside', 'tenweb-builder' ),
					'inside' => __( 'Inside', 'tenweb-builder' ),
				],
				'prefix_class' => 'tenweb-pagination-position-',
				'condition' => [
					'pagination!' => '',
				],
			]
		);

		$this->add_control(
			'pagination_size',
			[
				'label' => __( 'Size', 'tenweb-builder' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 20,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullet' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .swiper-container-horizontal .swiper-pagination-progress' => 'height: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .swiper-pagination-fraction' => 'font-size: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'pagination!' => '',
				],
			]
		);

		$this->add_control(
			'pagination_color',
			[
				'label' => __( 'Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullet-active, {{WRAPPER}} .swiper-pagination-progressbar' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .swiper-pagination-fraction' => 'color: {{VALUE}}',
				],
				'condition' => [
					'pagination!' => '',
				],
			]
		);

		$this->end_controls_section();

		$this->start_injection([
			'of' => 'slides',
		]);

		$this->add_control(
			'skin',
			[
				'label' => __( 'Skin', 'tenweb-builder' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => __( 'Default', 'tenweb-builder' ),
					'bubble' => __( 'Bubble', 'tenweb-builder' ),
				],
				'prefix_class' => 'tenweb-testimonial--skin-',
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'layout',
			[
				'label' => __( 'Layout', 'tenweb-builder' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'image_inline',
				'options' => [
					'image_inline' => __( 'Image Inline', 'tenweb-builder' ),
					'image_stacked' => __( 'Image Stacked', 'tenweb-builder' ),
					'image_above' => __( 'Image Above', 'tenweb-builder' ),
					'image_left' => __( 'Image Left', 'tenweb-builder' ),
					'image_right' => __( 'Image Right', 'tenweb-builder' ),
				],
				'prefix_class' => 'tenweb-testimonial--layout-',
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'alignment',
			[
				'label' => __( 'Alignment', 'tenweb-builder' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'default' => 'center',
				'options' => [
					'left' => [
						'title' => __( 'Left', 'tenweb-builder' ),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'tenweb-builder' ),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'tenweb-builder' ),
						'icon' => 'fa fa-align-right',
					],
				],
				'prefix_class' => 'tenweb-testimonial--align-',
                'condition' => ['view_type' => 'carousel'],

            ]
		);

        $this->add_masonry_control( 'add_control', 'alignment_masonry' );

        $this->end_injection();

		$this->start_controls_section(
			'section_skin_style',
			[
				'label' => __( 'Bubble', 'tenweb-builder' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'skin' => 'bubble',
				],
			]
		);

		$this->add_control(
			'background_color',
			[
				'label' => __( 'Background Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'alpha' => false,
				'selectors' => [
					'{{WRAPPER}} .tenweb-testimonial__content, {{WRAPPER}} .tenweb-testimonial__content:after' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'text_padding',
			[
				'label' => __( 'Padding', 'tenweb-builder' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'top' => '20',
					'bottom' => '20',
					'left' => '20',
					'right' => '20',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .tenweb-testimonial__content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
					'{{WRAPPER}}.tenweb-testimonial--layout-image_left .tenweb-testimonial__footer,
					{{WRAPPER}}.tenweb-testimonial--layout-image_right .tenweb-testimonial__footer' => 'padding-top: {{TOP}}{{UNIT}}',
					'{{WRAPPER}}.tenweb-testimonial--layout-image_above .tenweb-testimonial__footer,
					{{WRAPPER}}.tenweb-testimonial--layout-image_inline .tenweb-testimonial__footer,
					{{WRAPPER}}.tenweb-testimonial--layout-image_stacked .tenweb-testimonial__footer' => 'padding: 0 {{RIGHT}}{{UNIT}} 0 {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'border_radius',
			[
				'label' => __( 'Border Radius', 'tenweb-builder' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .tenweb-testimonial__content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'border',
			[
				'label' => __( 'Border', 'tenweb-builder' ),
				'type' => Controls_Manager::SWITCHER,
				'selectors' => [
					'{{WRAPPER}} .tenweb-testimonial__content, {{WRAPPER}} .tenweb-testimonial__content:after' => 'border-style: solid',
				],
			]
		);

		$this->add_control(
			'border_color',
			[
				'label' => __( 'Border Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000',
				'selectors' => [
					'{{WRAPPER}} .tenweb-testimonial__content' => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .tenweb-testimonial__content:after' => 'border-color: transparent {{VALUE}} {{VALUE}} transparent',
				],
				'condition' => [
					'border' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'border_width',
			[
				'label' => __( 'Border Width', 'tenweb-builder' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 20,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tenweb-testimonial__content, {{WRAPPER}} .tenweb-testimonial__content:after' => 'border-width: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}}.tenweb-testimonial--layout-image_stacked .tenweb-testimonial__content:after,
					{{WRAPPER}}.tenweb-testimonial--layout-image_inline .tenweb-testimonial__content:after' => 'margin-top: -{{SIZE}}{{UNIT}}',
					'{{WRAPPER}}.tenweb-testimonial--layout-image_above .tenweb-testimonial__content:after' => 'margin-bottom: -{{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'border' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		$this->start_injection( [
			'at' => 'before',
			'of' => 'section_navigation',
		] );

		$this->start_controls_section(
			'section_content_style',
			[
				'label' => __( 'Content', 'tenweb-builder' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'content_gap',
			[
				'label' => __( 'Gap', 'tenweb-builder' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}}.tenweb-testimonial--layout-image_inline .tenweb-testimonial__footer,
					{{WRAPPER}}.tenweb-testimonial--layout-image_stacked .tenweb-testimonial__footer' => 'margin-top: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}}.tenweb-testimonial--layout-image_above .tenweb-testimonial__footer' => 'margin-bottom: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}}.tenweb-testimonial--layout-image_left .tenweb-testimonial__footer' => 'padding-right: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}}.tenweb-testimonial--layout-image_right .tenweb-testimonial__footer' => 'padding-left: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'content_color',
			[
				'label' => __( 'Text Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
                'default' => '#000000',
				'selectors' => [
					'{{WRAPPER}} .tenweb-testimonial__text' => 'color: {{VALUE}}',
				],
                'global' => [
                    'default' => Global_Colors::COLOR_TEXT,
                ],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
                'name' => 'content_typography',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_TEXT,
                ],
                'condition' => ['view_type' => 'carousel'],
                'selector' => '{{WRAPPER}} .tenweb-testimonial__text',
			]
		);

        $this->add_masonry_control( 'add_group_control', 'content_typography_masonry' );

        $this->add_control(
			'name_title_style',
			[
				'label' => __( 'Name', 'tenweb-builder' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'name_color',
			[
				'label' => __( 'Text Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
                'default' => '#000000',
				'selectors' => [
					'{{WRAPPER}} .tenweb-testimonial__name' => 'color: {{VALUE}}',
				],
                'global' => [
                    'default' => Global_Colors::COLOR_TEXT,
                ],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
                'name' => 'name_typography',
                'selector' => '{{WRAPPER}} .tenweb-testimonial__name',
                'condition' => ['view_type' => 'carousel'],
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
                ],
			]
		);

        $this->add_masonry_control( 'add_group_control', 'name_typography_masonry' );

        $this->add_control(
			'heading_title_style',
			[
				'label' => __( 'Title', 'tenweb-builder' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __( 'Text Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
                'default' => '#000000',
				'selectors' => [
					'{{WRAPPER}} .tenweb-testimonial__title' => 'color: {{VALUE}}',
				],
                'global' => [
                    'default' => Global_Colors::COLOR_PRIMARY,
                ],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .tenweb-testimonial__title',
                'condition' => ['view_type' => 'carousel'],
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
                ],
			]
		);

        $this->add_masonry_control( 'add_group_control', 'title_typography_masonry' );

		$this->end_controls_section();

		$this->start_controls_section(
			'section_image_style',
			[
				'label' => __( 'Image', 'tenweb-builder' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'image_size',
			[
				'label' => __( 'Image Size', 'tenweb-builder' ),
				'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 56
                ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tenweb-testimonial__image' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .tenweb-testimonial__image img' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}}.tenweb-testimonial--layout-image_left .tenweb-testimonial__content:after,
					 {{WRAPPER}}.tenweb-testimonial--layout-image_right .tenweb-testimonial__content:after' => 'top: calc( {{text_padding.TOP}}{{text_padding.UNIT}} + ({{SIZE}}{{UNIT}} / 2) - 8px );',

					'body:not(.rtl) {{WRAPPER}}.tenweb-testimonial--layout-image_stacked:not(.tenweb-testimonial--align-center):not(.tenweb-testimonial--align-right) .tenweb-testimonial__content:after,
					 body:not(.rtl) {{WRAPPER}}.tenweb-testimonial--layout-image_inline:not(.tenweb-testimonial--align-center):not(.tenweb-testimonial--align-right) .tenweb-testimonial__content:after,
					 {{WRAPPER}}.tenweb-testimonial--layout-image_stacked.tenweb-testimonial--align-left .tenweb-testimonial__content:after,
					 {{WRAPPER}}.tenweb-testimonial--layout-image_inline.tenweb-testimonial--align-left .tenweb-testimonial__content:after' => 'left: calc( {{text_padding.LEFT}}{{text_padding.UNIT}} + ({{SIZE}}{{UNIT}} / 2) - 8px ); right:auto;',

					'body.rtl {{WRAPPER}}.tenweb-testimonial--layout-image_stacked:not(.tenweb-testimonial--align-center):not(.tenweb-testimonial--align-left) .tenweb-testimonial__content:after,
					 body.rtl {{WRAPPER}}.tenweb-testimonial--layout-image_inline:not(.tenweb-testimonial--align-center):not(.tenweb-testimonial--align-left) .tenweb-testimonial__content:after,
					 {{WRAPPER}}.tenweb-testimonial--layout-image_stacked.tenweb-testimonial--align-right .tenweb-testimonial__content:after,
					 {{WRAPPER}}.tenweb-testimonial--layout-image_inline.tenweb-testimonial--align-right .tenweb-testimonial__content:after' => 'right: calc( {{text_padding.RIGHT}}{{text_padding.UNIT}} + ({{SIZE}}{{UNIT}} / 2) - 8px ); left:auto;',

					'body:not(.rtl) {{WRAPPER}}.tenweb-testimonial--layout-image_above:not(.tenweb-testimonial--align-center):not(.tenweb-testimonial--align-right) .tenweb-testimonial__content:after,
					 {{WRAPPER}}.tenweb-testimonial--layout-image_above.tenweb-testimonial--align-left .tenweb-testimonial__content:after' => 'left: calc( {{text_padding.LEFT}}{{text_padding.UNIT}} + ({{SIZE}}{{UNIT}} / 2) - 8px ); right:auto;',

					'body.rtl {{WRAPPER}}.tenweb-testimonial--layout-image_above:not(.tenweb-testimonial--align-center):not(.tenweb-testimonial--align-left) .tenweb-testimonial__content:after,
					 {{WRAPPER}}.tenweb-testimonial--layout-image_above.tenweb-testimonial--align-right .tenweb-testimonial__content:after' => 'right: calc( {{text_padding.RIGHT}}{{text_padding.UNIT}} + ({{SIZE}}{{UNIT}} / 2) - 8px ); left:auto;',
				],
			]
		);

		$this->add_responsive_control(
			'image_gap',
			[
				'label' => __( 'Image Gap', 'tenweb-builder' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'body.rtl {{WRAPPER}}.tenweb-testimonial--layout-image_inline.tenweb-testimonial--align-left .tenweb-testimonial__image + cite, 
					 body.rtl {{WRAPPER}}.tenweb-testimonial--layout-image_above.tenweb-testimonial--align-left .tenweb-testimonial__image + cite,
					 body:not(.rtl) {{WRAPPER}}.tenweb-testimonial--layout-image_inline .tenweb-testimonial__image + cite, 
					 body:not(.rtl) {{WRAPPER}}.tenweb-testimonial--layout-image_above .tenweb-testimonial__image + cite' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: 0;',

					'body:not(.rtl) {{WRAPPER}}.tenweb-testimonial--layout-image_inline.tenweb-testimonial--align-right .tenweb-testimonial__image + cite, 
					 body:not(.rtl) {{WRAPPER}}.tenweb-testimonial--layout-image_above.tenweb-testimonial--align-right .tenweb-testimonial__image + cite,
					 body.rtl {{WRAPPER}}.tenweb-testimonial--layout-image_inline .tenweb-testimonial__image + cite,
					 body.rtl {{WRAPPER}}.tenweb-testimonial--layout-image_above .tenweb-testimonial__image + cite' => 'margin-right: {{SIZE}}{{UNIT}}; margin-left:0;',

					'{{WRAPPER}}.tenweb-testimonial--layout-image_stacked .tenweb-testimonial__image + cite, 
					 {{WRAPPER}}.tenweb-testimonial--layout-image_left .tenweb-testimonial__image + cite,
					 {{WRAPPER}}.tenweb-testimonial--layout-image_right .tenweb-testimonial__image + cite' => 'margin-top: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'image_border',
			[
				'label' => __( 'Border', 'tenweb-builder' ),
				'type' => Controls_Manager::SWITCHER,
				'selectors' => [
					'{{WRAPPER}} .tenweb-testimonial__image img' => 'border-style: solid',
				],
			]
		);

		$this->add_control(
			'image_border_color',
			[
				'label' => __( 'Border Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000',
				'selectors' => [
					'{{WRAPPER}} .tenweb-testimonial__image img' => 'border-color: {{VALUE}}',
				],
				'condition' => [
					'image_border' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'image_border_width',
			[
				'label' => __( 'Border Width', 'tenweb-builder' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 20,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tenweb-testimonial__image img' => 'border-width: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'image_border' => 'yes',
				],
			]
		);

		$this->add_control(
			'image_border_radius',
			[
				'label' => __( 'Border Radius', 'tenweb-builder' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .tenweb-testimonial__image img' => 'border-radius: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();

		$this->end_injection();

		$this->update_responsive_control(
			'width',
			[
				'selectors' => [
					'{{WRAPPER}}.tenweb-arrows-yes .tenweb-testimonial-carousel-swiper' => 'width: calc( {{SIZE}}{{UNIT}} - 40px )',
					'{{WRAPPER}} .tenweb-testimonial-carousel-swiper' => 'width: {{SIZE}}{{UNIT}}',
				],
                'condition' => ['view_type' => 'carousel'],
			]
		);

		$this->remove_control( 'effect' );
		$this->remove_responsive_control( 'height' );
		$this->remove_control( 'pagination_position' );
	}

	protected function add_repeater_controls( Repeater $repeater ) {
		$repeater->add_control(
			'content',
			[
				'label' => __( 'Content', 'tenweb-builder' ),
				'type' => Controls_Manager::TEXTAREA,
			]
		);

		$repeater->add_control(
			'image',
			[
				'label' => __( 'Image', 'tenweb-builder' ),
				'type' => Controls_Manager::MEDIA,
			]
		);

		$repeater->add_control(
			'name',
			[
				'label' => __( 'Name', 'tenweb-builder' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
			]
		);

		$repeater->add_control(
			'title',
			[
				'label' => __( 'Title', 'tenweb-builder' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
			]
		);
	}

	private function default_slides() {
		$image_src = Utils::get_placeholder_image_src();
		return [
			[
				'content' => __( '"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse varius enim in eros elementum tristique. Duis cursus, mi quis viverra ornare."', 'tenweb-builder' ),
				'name' => __( 'Name Surname', 'tenweb-builder' ),
				'title' => __( 'Position, Company name', 'tenweb-builder' ),
				'image' => [
					'url' => $image_src,
				],
			],
			[
				'content' => __( '"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse varius enim in eros elementum tristique. Duis cursus, mi quis viverra ornare, eros dolor interdum nulla, ut commodo diam libero vitae erat."', 'tenweb-builder' ),
				'name' => __( 'Name Surname', 'tenweb-builder' ),
				'title' => __( 'Position, Company name', 'tenweb-builder' ),
				'image' => [
					'url' => $image_src,
				],
			],
			[
				'content' => __( '"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse varius enim in eros elementum tristique. Duis cursus, mi quis viverra ornare."', 'tenweb-builder' ),
				'name' => __( 'Name Surname', 'tenweb-builder' ),
				'title' => __( 'Position, Company name', 'tenweb-builder' ),
				'image' => [
					'url' => $image_src,
				],
			],
			[
				'content' => __( '"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse varius enim in eros elementum tristique. Duis cursus, mi quis viverra ornare, eros dolor interdum nulla, ut commodo diam libero vitae erat."', 'tenweb-builder' ),
				'name' => __( 'Name Surname', 'tenweb-builder' ),
				'title' => __( 'Position, Company name', 'tenweb-builder' ),
				'image' => [
					'url' => $image_src,
				],
			],
			[
				'content' => __( '"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse varius enim in eros elementum tristique. Duis cursus, mi quis viverra ornare, eros dolor interdum nulla, ut commodo diam libero vitae erat."', 'tenweb-builder' ),
				'name' => __( 'Name Surname', 'tenweb-builder' ),
				'title' => __( 'Position, Company name', 'tenweb-builder' ),
				'image' => [
					'url' => $image_src,
				],
			],
			[
				'content' => __( '"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse varius enim in eros elementum tristique. Duis cursus, mi quis viverra ornare."', 'tenweb-builder' ),
				'name' => __( 'Name Surname', 'tenweb-builder' ),
				'title' => __( 'Position, Company name', 'tenweb-builder' ),
				'image' => [
					'url' => $image_src,
				],
			],
		];
	}

	private function print_cite( $slide = array(), $location = '' ) {
		if ( empty( $slide['name'] ) && empty( $slide['title'] ) ) {
			return '';
		}

		$skin = $this->get_settings( 'skin' );
		$layout = 'bubble' === $skin ? 'image_inline' : $this->get_settings( 'layout' );
		$locations_outside = [ 'image_above', 'image_right', 'image_left' ];
		$locations_inside = [ 'image_inline', 'image_stacked' ];

		$print_outside = ( 'outside' === $location && in_array( $layout, $locations_outside, true ) );
		$print_inside  = ( 'inside' === $location && in_array( $layout, $locations_inside, true ) );

		$html = '';
		if ( $print_outside || $print_inside ) {
			$html = '<cite class="tenweb-testimonial__cite">';
			if ( ! empty( $slide['name'] ) ) {
				$html .= '<span class="tenweb-testimonial__name">' . $slide['name'] . '</span>';
			}
			if ( ! empty( $slide['title'] ) ) {
				$html .= '<span class="tenweb-testimonial__title">' . $slide['title'] . '</span>';
			}
			$html .= '</cite>';
		}

		return $html;
	}

	protected function print_slide( $slide = array(), $settings = array(), $key = '' ) {
		$this->add_render_attribute( $key . '-testimonial', [
			'class' => 'tenweb-testimonial',
		] );

		if ( ! empty( $slide['image']['url'] ) ) {
			$image_src = Group_Control_Image_Size::get_attachment_image_src( $slide['image']['id'], 'image_size', $settings );
			if ( ! $image_src ) {
				$image_src = $slide['image']['url'];
			}

			$this->add_render_attribute( $key . '-image', [
				'src' => $image_src,
				'alt' => !empty( $slide['name'] ) ? $slide['name'] : '',
			] );
		}
		?>
		<div <?php $this->print_render_attribute_string( $key . '-testimonial' ); ?>>

			<?php if ( $slide['content'] ) { ?>


                <div class="tenweb-testimonial__content">
                    <?php
                    if ( $settings['tenweb_enable_stars'] === 'yes' ) { ?>
                        <div <?php $this->print_render_attribute_string( 'widget' ); ?>>
                            <div <?php $this->print_render_attribute_string( 'widget_wrapper' ); ?>>
                                <?php echo $this->get_icon_markup(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                            </div>
                        </div>
                    <?php } ?>
					<div class="tenweb-testimonial__text">
                        <?php
                        // Main content allowed
                        echo $slide['content']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
                    <?php echo $this->print_cite( $slide, 'outside' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			<?php } ?>
			<div class="tenweb-testimonial__footer">
				<?php if ( $slide['image']['url'] ) { ?>
					<div class="tenweb-testimonial__image">
						<img <?php $this->print_render_attribute_string( $key . '-image' ); ?>>
					</div>
				<?php } ?>
                <?php echo $this->print_cite( $slide, 'inside' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		</div>
		<?php
	}
	
	function print_slider() {
		$settings = $this->get_active_settings();
		$slides_count = count( $settings['slides'] );

		$swiperObj = [
			'view_type' => $settings['view_type'],
			'slides_per_view' => $settings['slides_per_view'],
			'slides_per_view_tablet' => $settings['slides_per_view_tablet'],
			'slides_per_view_mobile' => $settings['slides_per_view_mobile'],
			'column_count_masonry' => $settings['column_count_masonry'],
			'column_count_masonry_tablet' => $settings['column_count_masonry_tablet'],
			'column_count_masonry_mobile' => $settings['column_count_masonry_mobile'],
			'slides_to_scroll' => $settings['slides_to_scroll'],
			'slides_count' => $slides_count,
			'pagination' => $settings['pagination'],
			'show_arrows' => $settings['show_arrows'],
			'speed' => $settings['speed'],
			'autoplay' => $settings['autoplay'],
			'autoplay_speed' => $settings['autoplay_speed'],
			'loop' => $settings['loop'],
			'pause_on_interaction' => $settings['pause_on_interaction'],
			'breakpoints' => [
						'space_between' => $settings['space_between'],
						'space_between_tablet' => $settings['space_between_tablet'],
						'space_between_mobile' => $settings['space_between_mobile'],
					],
            'tenweb_enable_stars' => $settings['tenweb_enable_stars'],
		];

            $this->add_render_attribute('widget', [
                'class' => 'e-rating',
                'itemtype' => 'https://schema.org/Rating',
                'itemscope' => '',
                'itemprop' => 'reviewRating',
            ]);

            $this->add_render_attribute('widget_wrapper', [
                'class' => 'e-rating-wrapper',
                'itemprop' => 'ratingValue',
                'content' => $this->get_rating_value(),
                'role' => 'img',
                'aria-label' => sprintf(esc_html__('Rated %1$s out of %2$s', 'elementor'),
                    $this->get_rating_value(),
                    $this->get_rating_scale()
                ),
                'data-settings' => json_encode($swiperObj),//phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode
            ]);

        $this->add_render_attribute( 'tenweb-testimonial-carousel-swiper', [
			'class' => 'tenweb-testimonial-carousel-swiper swiper-container swiper-container-horizontal',
			'data-settings' => json_encode($swiperObj), //phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode
			]
		);

        if($swiperObj['view_type'] !== 'masonry') {
		?>
		<div class="tenweb-swiper">
			<div <?php echo $this->get_render_attribute_string( 'tenweb-testimonial-carousel-swiper' );//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<div class="swiper-wrapper">
					<?php
					foreach ( $settings['slides'] as $index => $slide ) {
						$this->slide_prints_count++;
						?>
						<div class="swiper-slide">
							<?php $this->print_slide( $slide, $settings, 'slide-' . $index . '-' . $this->slide_prints_count ); ?>
						</div>
					<?php } ?>
				</div>
				<?php if ( 1 < $slides_count ) { ?>
					<?php if ( $settings['pagination'] ) { ?>
						<div class="swiper-pagination"></div>
					<?php } ?>
					<?php if ( $settings['show_arrows'] ) { ?>
						<div class="tenweb-swiper-button tenweb-swiper-button-prev">
							<i class="eicon-chevron-left"></i>
						</div>
						<div class="tenweb-swiper-button tenweb-swiper-button-next">
							<i class="eicon-chevron-right"></i>
						</div>
					<?php } ?>
				<?php } ?>
			</div>
		</div>
		<?php
        } else {
        ?>
            <div class="tenweb-masonry" <?php echo $this->get_render_attribute_string( 'tenweb-testimonial-carousel-swiper' );//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
                <?php
                foreach ( $settings['slides'] as $index => $slide ) {
                    $this->slide_prints_count++;
                    ?>
                    <div class="tenweb-item">
                        <?php $this->print_slide( $slide, $settings, 'slide-' . $index . '-' . $this->slide_prints_count ); ?>
                    </div>
                <?php } ?>
            </div>
            <?php
        }
	}

    protected function get_rating_value(): float {
        $initial_value = $this->get_rating_scale();
        $rating_value = $this->get_settings_for_display( 'rating_value' );

        if ( '' === $rating_value ) {
            $rating_value = $initial_value;
        }

        $rating_value = floatval( $rating_value );

        return round( $rating_value, 2 );
    }

    protected function get_rating_scale(): int {
        $rating_scale = $this->get_settings_for_display( 'rating_scale' );
        if( !isset($rating_scale['size']) ) {
            return 5;
        }
        return intval( $rating_scale['size'] );
    }

    protected function get_icon_marked_width( $icon_index ): string {
        $rating_value = $this->get_rating_value();

        $width = '0%';

        if ( $rating_value >= $icon_index ) {
            $width = '100%';
        } elseif ( intval( ceil( $rating_value ) ) === $icon_index ) {
            $width = ( $rating_value - ( $icon_index - 1 ) ) * 100 . '%';
        }

        return $width;
    }

    protected function get_icon_markup(): string {
        $icon = $this->get_settings_for_display( 'rating_icon' );
        $rating_scale = $this->get_rating_scale();

        ob_start();

        for ( $index = 1; $index <= $rating_scale; $index++ ) {
            $this->add_render_attribute( 'icon_marked_' . $index, [
                'class' => 'e-icon-wrapper e-icon-marked',
            ] );

            $icon_marked_width = $this->get_icon_marked_width( $index );

            if ( '100%' !== $icon_marked_width ) {
                $this->add_render_attribute( 'icon_marked_' . $index, [
                    'style' => '--e-rating-icon-marked-width: ' . $icon_marked_width . ';',
                ] );
            }
            ?>
            <div class="e-icon">
                <div <?php $this->print_render_attribute_string( 'icon_marked_' . $index ); ?>>
                    <?php echo Icons_Manager::try_get_icon_html( $icon, [ 'aria-hidden' => 'true' ] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </div>
                <div class="e-icon-wrapper e-icon-unmarked">
                    <?php echo Icons_Manager::try_get_icon_html( $icon, [ 'aria-hidden' => 'true' ] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </div>
            </div>
            <?php
        }

        return ob_get_clean();
    }



    protected function render() {
      $this->print_slider();
    }
}

\Elementor\Plugin::instance()->widgets_manager->register( new Testimonial_Carousel() );