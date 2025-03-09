<?php
namespace Tenweb_Builder;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Repeater;
use Elementor\Widget_Base;
use Tenweb_Builder\ElementorPro\Modules\QueryControl\Module as QueryControlModule;
use Elementor\Core\Base\Document;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Slides extends Widget_Base {

	public function get_name() {
		return Builder::$prefix . '_slides';
	}

	public function get_title() {
		return __( 'Slides', 'tenweb-builder' );
	}

	public function get_icon() {
		return 'twbb-slides twbb-widget-icon';
	}

	public function get_categories() {
		return [ 'tenweb-widgets' ];
	}

	public function get_keywords() {
		return [ 'slides', 'carousel', 'image', 'title', 'slider' ];
	}

	public function get_script_depends() {
		return [ 'imagesloaded' ];
	}

	public static function get_button_sizes() {
		return [
			'xs' => __( 'Extra Small', 'tenweb-builder' ),
			'sm' => __( 'Small', 'tenweb-builder' ),
			'md' => __( 'Medium', 'tenweb-builder' ),
			'lg' => __( 'Large', 'tenweb-builder' ),
			'xl' => __( 'Extra Large', 'tenweb-builder' ),
		];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_slides',
			[
				'label' => __( 'Slides', 'tenweb-builder' ),
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'slider_type',
			[
				'label' => __( 'Slider Type', 'tenweb-builder' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'standard_slide',
				'options' => [
					'standard_slide' => __( 'Standard Slide', 'tenweb-builder' ),
					'template_slide' => __( 'Template Slide', 'tenweb-builder' ),
				],
				'render_type' => 'template',
				'prefix_class' => 'twbb-slider-',
			]
		);

		$repeater->add_control(
			'template_id',
			[
				'label' => __( 'Choose Template', 'tenweb-builder' ),
				'type' => QueryControlModule::QUERY_CONTROL_ID,
				'label_block' => true,
				'autocomplete' => [
					'object' => QueryControlModule::QUERY_OBJECT_LIBRARY_TEMPLATE,
					'query' => [
						'meta_query' => [ //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
							[
								'key' => Document::TYPE_META_KEY,
								'value' => 'twbb_slide',
								'compare' => 'IN',
							],
						],
					],
				],
				'render_type' => 'template',
				'condition' => [
					'slider_type' => 'template_slide',
				],
				'separator' => 'before',
			]
		);

		$repeater->start_controls_tabs( 'tenweb-builder',
			[
			    'condition' => [
				    'slider_type' => 'standard_slide',
			    ],
				'separator' => 'before',
			]
        );

		$repeater->start_controls_tab( 'background', [ 'label' => __( 'Background', 'tenweb-builder' ) ] );

		$repeater->add_control(
			'background_color',
			[
				'label' => __( 'Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#bbbbbb',
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .swiper-slide-bg' => 'background-color: {{VALUE}}',
				],
			]
		);

		$repeater->add_control(
			'background_image',
			[
				'label' => _x( 'Image', 'Background Control', 'tenweb-builder' ),
				'type' => Controls_Manager::MEDIA,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .swiper-slide-bg' => 'background-image: url({{URL}})',
				],
			]
		);

		$repeater->add_control(
			'background_size',
			[
				'label' => _x( 'Size', 'Background Control', 'tenweb-builder' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'cover',
				'options' => [
					'cover' => _x( 'Cover', 'Background Control', 'tenweb-builder' ),
					'contain' => _x( 'Contain', 'Background Control', 'tenweb-builder' ),
					'auto' => _x( 'Auto', 'Background Control', 'tenweb-builder' ),
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .swiper-slide-bg' => 'background-size: {{VALUE}}',
				],
				'conditions' => [
					'terms' => [
						[
							'name' => 'background_image[url]',
							'operator' => '!=',
							'value' => '',
						],
					],
				],
			]
		);

		$repeater->add_control(
			'background_ken_burns',
			[
				'label' => __( 'Ken Burns Effect', 'tenweb-builder' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'conditions' => [
					'terms' => [
						[
							'name' => 'background_image[url]',
							'operator' => '!=',
							'value' => '',
						],
					],
				],
			]
		);

		$repeater->add_control(
			'zoom_direction',
			[
				'label' => __( 'Zoom Direction', 'tenweb-builder' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'in',
				'options' => [
					'in' => __( 'In', 'tenweb-builder' ),
					'out' => __( 'Out', 'tenweb-builder' ),
				],
				'conditions' => [
					'terms' => [
						[
							'name' => 'background_ken_burns',
							'operator' => '!=',
							'value' => '',
						],
					],
				],
			]
		);

		$repeater->add_control(
			'background_overlay',
			[
				'label' => __( 'Background Overlay', 'tenweb-builder' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'conditions' => [
					'terms' => [
						[
							'name' => 'background_image[url]',
							'operator' => '!=',
							'value' => '',
						],
					],
				],
			]
		);

		$repeater->add_control(
			'background_overlay_color',
			[
				'label' => __( 'Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'default' => 'rgba(0,0,0,0.5)',
				'conditions' => [
					'terms' => [
						[
							'name' => 'background_overlay',
							'value' => 'yes',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .elementor-background-overlay' => 'background-color: {{VALUE}}',
				],
			]
		);

		$repeater->add_control(
			'background_overlay_blend_mode',
			[
				'label' => __( 'Blend Mode', 'tenweb-builder' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => __( 'Normal', 'tenweb-builder' ),
					'multiply' => 'Multiply',
					'screen' => 'Screen',
					'overlay' => 'Overlay',
					'darken' => 'Darken',
					'lighten' => 'Lighten',
					'color-dodge' => 'Color Dodge',
					'color-burn' => 'Color Burn',
					'hue' => 'Hue',
					'saturation' => 'Saturation',
					'color' => 'Color',
					'exclusion' => 'Exclusion',
					'luminosity' => 'Luminosity',
				],
				'conditions' => [
					'terms' => [
						[
							'name' => 'background_overlay',
							'value' => 'yes',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .elementor-background-overlay' => 'mix-blend-mode: {{VALUE}}',
				],
			]
		);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab( 'content', [ 'label' => __( 'Content', 'tenweb-builder' ) ] );

		$repeater->add_control(
			'heading',
			[
				'label' => __( 'Title & Description', 'tenweb-builder' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Slide Heading', 'tenweb-builder' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'description',
			[
				'label' => __( 'Description', 'tenweb-builder' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => __( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'tenweb-builder' ),
				'show_label' => false,
			]
		);

		$repeater->add_control(
			'button_text',
			[
				'label' => __( 'Button Text', 'tenweb-builder' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Click Here', 'tenweb-builder' ),
			]
		);

		$repeater->add_control(
			'link',
			[
				'label' => __( 'Link', 'tenweb-builder' ),
				'type' => Controls_Manager::URL,
				'placeholder' => __( 'https://your-link.com', 'tenweb-builder' ),
			]
		);

		$repeater->add_control(
			'link_click',
			[
				'label' => __( 'Apply Link On', 'tenweb-builder' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'slide' => __( 'Whole Slide', 'tenweb-builder' ),
					'button' => __( 'Button Only', 'tenweb-builder' ),
				],
				'default' => 'slide',
				'conditions' => [
					'terms' => [
						[
							'name' => 'link[url]',
							'operator' => '!=',
							'value' => '',
						],
					],
				],
			]
		);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab( 'style', [ 'label' => __( 'Style', 'tenweb-builder' ) ] );

		$repeater->add_control(
			'custom_style',
			[
				'label' => __( 'Custom', 'tenweb-builder' ),
				'type' => Controls_Manager::SWITCHER,
				'description' => __( 'Set custom style that will only affect this specific slide.', 'tenweb-builder' ),
			]
		);

		$repeater->add_control(
			'horizontal_position',
			[
				'label' => __( 'Horizontal Position', 'tenweb-builder' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'tenweb-builder' ),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'tenweb-builder' ),
						'icon' => 'eicon-h-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'tenweb-builder' ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .swiper-slide-contents' => '{{VALUE}}',
				],
				'selectors_dictionary' => [
					'left' => 'margin-right: auto',
					'center' => 'margin: 0 auto',
					'right' => 'margin-left: auto',
				],
				'conditions' => [
					'terms' => [
						[
							'name' => 'custom_style',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$repeater->add_control(
			'vertical_position',
			[
				'label' => __( 'Vertical Position', 'tenweb-builder' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'top' => [
						'title' => __( 'Top', 'tenweb-builder' ),
						'icon' => 'eicon-v-align-top',
					],
					'middle' => [
						'title' => __( 'Middle', 'tenweb-builder' ),
						'icon' => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' => __( 'Bottom', 'tenweb-builder' ),
						'icon' => 'eicon-v-align-bottom',
					],
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .swiper-slide-inner' => 'align-items: {{VALUE}}',
				],
				'selectors_dictionary' => [
					'top' => 'flex-start',
					'middle' => 'center',
					'bottom' => 'flex-end',
				],
				'conditions' => [
					'terms' => [
						[
							'name' => 'custom_style',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$repeater->add_control(
			'text_align',
			[
				'label' => __( 'Text Align', 'tenweb-builder' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'tenweb-builder' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'tenweb-builder' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'tenweb-builder' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .swiper-slide-inner' => 'text-align: {{VALUE}}',
				],
				'conditions' => [
					'terms' => [
						[
							'name' => 'custom_style',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$repeater->add_control(
			'content_color',
			[
				'label' => __( 'Content Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .swiper-slide-inner .elementor-slide-heading' => 'color: {{VALUE}}',
					'{{WRAPPER}} {{CURRENT_ITEM}} .swiper-slide-inner .elementor-slide-description' => 'color: {{VALUE}}',
					'{{WRAPPER}} {{CURRENT_ITEM}} .swiper-slide-inner .elementor-slide-button' => 'color: {{VALUE}}; border-color: {{VALUE}}',
				],
				'conditions' => [
					'terms' => [
						[
							'name' => 'custom_style',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$repeater->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'repeater_text_shadow',
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .swiper-slide-contents',
				'conditions' => [
					'terms' => [
						[
							'name' => 'custom_style',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$repeater->end_controls_tab();

		$repeater->end_controls_tabs();

		$this->add_control(
			'slides',
			[
				'label' => __( 'Slides', 'tenweb-builder' ),
				'type' => Controls_Manager::REPEATER,
				'show_label' => true,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'heading' => __( 'Slide 1 Heading', 'tenweb-builder' ),
						'description' => __( 'Lorem ipsum dolor sit amet consectetur adipiscing elit dolor', 'tenweb-builder' ),
						'button_text' => __( 'Click Here', 'tenweb-builder' ),
						'background_color' => '#833ca3',
					],
					[
						'heading' => __( 'Slide 2 Heading', 'tenweb-builder' ),
						'description' => __( 'Lorem ipsum dolor sit amet consectetur adipiscing elit dolor', 'tenweb-builder' ),
						'button_text' => __( 'Click Here', 'tenweb-builder' ),
						'background_color' => '#4054b2',
					],
					[
						'heading' => __( 'Slide 3 Heading', 'tenweb-builder' ),
						'description' => __( 'Lorem ipsum dolor sit amet consectetur adipiscing elit dolor', 'tenweb-builder' ),
						'button_text' => __( 'Click Here', 'tenweb-builder' ),
						'background_color' => '#1abc9c',
					],
				],
				'title_field' => '{{{ heading }}}', //phpcs:ignore WordPressVIPMinimum.Security.Mustache.OutputNotation
			]
		);

		$this->add_control(
			'slider_inner_position',
			[
				'label' => __( 'Slider Inner Position', 'tenweb-builder' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'inside',
				'options' => [
					'inside' => __( 'Inside', 'tenweb-builder' ),
					'outside' => __( 'Outside', 'tenweb-builder' ),
				],
				'prefix_class' => 'twbb-slider-inner-position-',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'images_slides_height',
			[
				'label' => __( 'Image Height', 'tenweb-builder' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 1000,
					],
					'vh' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'default' => [
					'size' => 250,
				],
				'size_units' => [ 'px', 'vh', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .swiper-slide-bg' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
				        'slider_inner_position' => 'outside',
                ],
			]
		);

		$this->add_responsive_control(
			'slides_height',
			[
				'label' => __( 'Height', 'tenweb-builder' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 1000,
					],
					'vh' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'default' => [
					'size' => 500,
				],
				'size_units' => [ 'px', 'vh', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .swiper-slide' => 'height: {{SIZE}}{{UNIT}};',
				],
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
				'frontend_available' => true,
			]
		);

		$this->add_responsive_control(
			'slides_to_scroll',
			[
				'type' => Controls_Manager::SELECT,
				'label' => __( 'Slides to Scroll', 'tenweb-builder' ),
				'description' => __( 'Set how many slides are scrolled per swipe.', 'tenweb-builder' ),
				'options' => [ '' => __( 'Default', 'tenweb-builder' ) ] + $slides_per_view,
				'frontend_available' => true,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_slider_options',
			[
				'label' => __( 'Slider Options', 'tenweb-builder' ),
				'type' => Controls_Manager::SECTION,
			]
		);

		$this->add_control(
			'navigation',
			[
				'label' => __( 'Navigation', 'tenweb-builder' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'both',
				'options' => [
					'both' => __( 'Arrows and Dots', 'tenweb-builder' ),
					'arrows' => __( 'Arrows', 'tenweb-builder' ),
					'dots' => __( 'Dots', 'tenweb-builder' ),
					'none' => __( 'None', 'tenweb-builder' ),
				],
				'render_type' => 'template',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label' => __( 'Autoplay', 'tenweb-builder' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'pause_on_hover',
			[
				'label' => __( 'Pause on Hover', 'tenweb-builder' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'frontend_available' => true,
				'condition' => [
					'autoplay!' => '',
				],
			]
		);

		$this->add_control(
			'pause_on_interaction',
			[
				'label' => __( 'Pause on Interaction', 'tenweb-builder' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'frontend_available' => true,
				'condition' => [
					'autoplay!' => '',
				],
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
				'selectors' => [
					'{{WRAPPER}} .swiper-slide' => 'transition-duration: calc({{VALUE}}ms*1.2)',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'infinite',
			[
				'label' => __( 'Infinite Loop', 'tenweb-builder' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'speed_liner',
			[
				'label' => __( 'Speed linear', 'tenweb-builder' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
				'frontend_available' => true,
                'prefix_class' => 'twbb-speed-linear-'
			]
		);

		$this->add_control(
			'transition',
			[
				'label' => __( 'Transition', 'tenweb-builder' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'slide',
				'options' => [
					'slide' => __( 'Slide', 'tenweb-builder' ),
					'fade' => __( 'Fade', 'tenweb-builder' ),
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'transition_speed',
			[
				'label' => __( 'Transition Speed', 'tenweb-builder' ) . ' (ms)',
				'type' => Controls_Manager::NUMBER,
				'default' => 500,
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'content_animation',
			[
				'label' => __( 'Content Animation', 'tenweb-builder' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'fadeInUp',
				'options' => [
					'' => __( 'None', 'tenweb-builder' ),
					'fadeInDown' => __( 'Down', 'tenweb-builder' ),
					'fadeInUp' => __( 'Up', 'tenweb-builder' ),
					'fadeInRight' => __( 'Right', 'tenweb-builder' ),
					'fadeInLeft' => __( 'Left', 'tenweb-builder' ),
					'zoomIn' => __( 'Zoom', 'tenweb-builder' ),
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_slides',
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
				'frontend_available' => true,
			]
		);

		$this->add_responsive_control(
			'content_max_width',
			[
				'label' => __( 'Content Width', 'tenweb-builder' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units' => [ '%', 'px' ],
				'default' => [
					'size' => '66',
					'unit' => '%',
				],
				'tablet_default' => [
					'unit' => '%',
				],
				'mobile_default' => [
					'unit' => '%',
				],
				'selectors' => [
					'{{WRAPPER}} .swiper-slide-contents' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'slides_padding',
			[
				'label' => __( 'Padding', 'tenweb-builder' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .swiper-slide-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'slides_horizontal_position',
			[
				'label' => __( 'Horizontal Position', 'tenweb-builder' ),
				'type' => Controls_Manager::CHOOSE,
				'default' => 'center',
				'options' => [
					'left' => [
						'title' => __( 'Left', 'tenweb-builder' ),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'tenweb-builder' ),
						'icon' => 'eicon-h-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'tenweb-builder' ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'prefix_class' => 'elementor--h-position-',
			]
		);

		$this->add_control(
			'slides_vertical_position',
			[
				'label' => __( 'Vertical Position', 'tenweb-builder' ),
				'type' => Controls_Manager::CHOOSE,
				'default' => 'middle',
				'options' => [
					'top' => [
						'title' => __( 'Top', 'tenweb-builder' ),
						'icon' => 'eicon-v-align-top',
					],
					'middle' => [
						'title' => __( 'Middle', 'tenweb-builder' ),
						'icon' => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' => __( 'Bottom', 'tenweb-builder' ),
						'icon' => 'eicon-v-align-bottom',
					],
				],
				'condition' => [
					'slider_inner_position' => 'inside',
                ],
				'prefix_class' => 'elementor--v-position-',
			]
		);

		$this->add_control(
			'slides_text_align',
			[
				'label' => __( 'Text Align', 'tenweb-builder' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'tenweb-builder' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'tenweb-builder' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'tenweb-builder' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}} .swiper-slide-inner' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'text_shadow',
				'selector' => '{{WRAPPER}} .swiper-slide-contents',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_title',
			[
				'label' => __( 'Title', 'tenweb-builder' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'heading_spacing',
			[
				'label' => __( 'Spacing', 'tenweb-builder' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .swiper-slide-inner .elementor-slide-heading:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'heading_color',
			[
				'label' => __( 'Text Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
                'global' => [
                    'default' => Global_Colors::COLOR_PRIMARY,
                ],
				'selectors' => [
					'{{WRAPPER}} .elementor-slide-heading' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'heading_typography',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
                ],
				'selector' => '{{WRAPPER}} .elementor-slide-heading',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_description',
			[
				'label' => __( 'Description', 'tenweb-builder' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'description_spacing',
			[
				'label' => __( 'Spacing', 'tenweb-builder' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .swiper-slide-inner .elementor-slide-description:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'description_color',
			[
				'label' => __( 'Text Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
                'global' => [
                    'default' => Global_Colors::COLOR_TEXT,
                ],
				'selectors' => [
					'{{WRAPPER}} .elementor-slide-description' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'description_typography',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_TEXT,
                ],
				'selector' => '{{WRAPPER}} .elementor-slide-description',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_button',
			[
				'label' => __( 'Button', 'tenweb-builder' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'button_size',
			[
				'label' => __( 'Size', 'tenweb-builder' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'sm',
				'options' => self::get_button_sizes(),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'button_typography',
				'selector' => '{{WRAPPER}} .elementor-slide-button',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_ACCENT,
                ],
			]
		);

    $this->start_controls_tabs( 'button_tabs' );

    $this->start_controls_tab( 'normal', [ 'label' => __( 'Normal', 'tenweb-builder' ) ] );

    $this->add_control(
	 'button_text_color',
	 [
	   'label' => __( 'Text Color', 'tenweb-builder' ),
	   'type' => Controls_Manager::COLOR,
	   'selectors' => [
		'{{WRAPPER}} .elementor-slide-button' => 'color: {{VALUE}};',
	   ],
	 ]
    );

    $this->add_control(
	 'button_background_color',
	 [
	   'label' => __( 'Background Color', 'tenweb-builder' ),
	   'type' => Controls_Manager::COLOR,
       'global' => [
          'default' => Global_Colors::COLOR_ACCENT,
       ],
	   'selectors' => [
		'{{WRAPPER}} .elementor-slide-button' => 'background-color: {{VALUE}};',
	   ],
	 ]
    );

    $this->add_control(
	 'button_border_color',
	 [
	   'label' => __( 'Border Color', 'tenweb-builder' ),
	   'type' => Controls_Manager::COLOR,
	   'selectors' => [
		'{{WRAPPER}} .elementor-slide-button' => 'border-color: {{VALUE}};',
	   ],
	 ]
    );

    $this->end_controls_tab();

    $this->start_controls_tab( 'hover', [ 'label' => __( 'Hover', 'tenweb-builder' ) ] );

    $this->add_control(
	 'button_hover_text_color',
	 [
	   'label' => __( 'Text Color', 'tenweb-builder' ),
	   'type' => Controls_Manager::COLOR,
	   'selectors' => [
		'{{WRAPPER}} .elementor-slide-button:hover' => 'color: {{VALUE}};',
	   ],
	 ]
    );

    $this->add_control(
	 'button_hover_background_color',
	 [
	   'label' => __( 'Background Color', 'tenweb-builder' ),
	   'type' => Controls_Manager::COLOR,
	   'selectors' => [
		'{{WRAPPER}} .elementor-slide-button:hover' => 'background-color: {{VALUE}};',
	   ],
	 ]
    );

    $this->add_control(
	 'button_hover_border_color',
	 [
	   'label' => __( 'Border Color', 'tenweb-builder' ),
	   'type' => Controls_Manager::COLOR,
	   'selectors' => [
		'{{WRAPPER}} .elementor-slide-button:hover' => 'border-color: {{VALUE}};',
	   ],
	 ]
    );

    $this->end_controls_tab();

    $this->end_controls_tabs();

		$this->add_control(
			'button_border_width',
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
					'{{WRAPPER}} .elementor-slide-button' => 'border-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'button_border_radius',
			[
				'label' => __( 'Border Radius', 'tenweb-builder' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-slide-button' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_navigation',
			[
				'label' => __( 'Navigation', 'tenweb-builder' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'navigation' => [ 'arrows', 'dots', 'both' ],
				],
			]
		);

		$this->add_control(
			'heading_style_arrows',
			[
				'label' => __( 'Arrows', 'tenweb-builder' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'navigation' => [ 'arrows', 'both' ],
				],
			]
		);

		$this->add_control(
			'arrows_position',
			[
				'label' => __( 'Arrows Position', 'tenweb-builder' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'inside',
				'options' => [
					'inside' => __( 'Inside', 'tenweb-builder' ),
					'outside' => __( 'Outside', 'tenweb-builder' ),
				],
				'prefix_class' => 'elementor-arrows-position-',
				'condition' => [
					'navigation' => [ 'arrows', 'both' ],
				],
			]
		);

		$this->add_control(
			'arrows_size',
			[
				'label' => __( 'Arrows Size', 'tenweb-builder' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 20,
						'max' => 60,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-swiper-button' => 'font-size: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'navigation' => [ 'arrows', 'both' ],
				],
			]
		);

		$this->add_control(
			'arrows_color',
			[
				'label' => __( 'Arrows Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-swiper-button' => 'color: {{VALUE}}',
				],
				'condition' => [
					'navigation' => [ 'arrows', 'both' ],
				],
			]
		);

		$this->add_control(
			'heading_style_dots',
			[
				'label' => __( 'Dots', 'tenweb-builder' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'navigation' => [ 'dots', 'both' ],
				],
			]
		);

		$this->add_control(
			'dots_position',
			[
				'label' => __( 'Dots Position', 'tenweb-builder' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'inside',
				'options' => [
					'outside' => __( 'Outside', 'tenweb-builder' ),
					'inside' => __( 'Inside', 'tenweb-builder' ),
				],
				'prefix_class' => 'elementor-pagination-position-',
				'condition' => [
					'navigation' => [ 'dots', 'both' ],
				],
			]
		);

		$this->add_control(
			'dots_size',
			[
				'label' => __( 'Dots Size', 'tenweb-builder' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 5,
						'max' => 15,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullet' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .swiper-container-horizontal .swiper-pagination-progressbar' => 'height: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .swiper-pagination-fraction' => 'font-size: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'navigation' => [ 'dots', 'both' ],
				],
			]
		);

		$this->add_control(
			'dots_color',
			[
				'label' => __( 'Dots Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullet-active' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'navigation' => [ 'dots', 'both' ],
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings();

		if ( empty( $settings['slides'] ) ) {
			return;
		}

		$this->add_render_attribute( 'button', 'class', [ 'elementor-button', 'elementor-slide-button' ] );

		if ( ! empty( $settings['button_size'] ) ) {
			$this->add_render_attribute( 'button', 'class', 'elementor-size-' . $settings['button_size'] );
		}

		$slides = [];
		$slide_count = 0;

        foreach ( $settings['slides'] as $slide ) {
	        if ( $slide[ 'slider_type' ] === 'template_slide' ) {
		        $slides [] = $this->template_slide_render( $slide );
	        } else {
		        $slide_html       = '';
		        $btn_attributes   = '';
		        $slide_attributes = '';
		        $slide_element    = 'div';
		        $btn_element      = 'div';

		        if ( ! empty( $slide['link']['url'] ) ) {
			        $this->add_link_attributes( 'slide_link' . $slide_count, $slide['link'] );

			        if ( 'button' === $slide['link_click'] ) {
				        $btn_element    = 'a';
				        $btn_attributes = $this->get_render_attribute_string( 'slide_link' . $slide_count );
			        } else {
				        $slide_element    = 'a';
				        $slide_attributes = $this->get_render_attribute_string( 'slide_link' . $slide_count );
			        }
		        }

		        $slide_html .= '<' . $slide_element . ' class="swiper-slide-inner" ' . $slide_attributes . '>';

		        $slide_html .= '<div class="swiper-slide-contents">';

		        if ( $slide['heading'] ) {
			        $slide_html .= '<div class="elementor-slide-heading">' . $slide['heading'] . '</div>';
		        }

		        if ( $slide['description'] ) {
			        $slide_html .= '<div class="elementor-slide-description">' . $slide['description'] . '</div>';
		        }

		        if ( $slide['button_text'] ) {
			        $slide_html .= '<' . $btn_element . ' ' . $btn_attributes . ' ' . $this->get_render_attribute_string( 'button' ) . '>' . $slide['button_text'] . '</' . $btn_element . '>';
		        }

		        $slide_html .= '</div></' . $slide_element . '>';

		        if ( 'yes' === $slide['background_overlay'] ) {
			        $slide_html = '<div class="elementor-background-overlay"></div>' . $slide_html;
		        }

		        $ken_class = '';

		        if ( $slide['background_ken_burns'] ) {
			        $ken_class = ' elementor-ken-burns elementor-ken-burns--' . $slide['zoom_direction'];
		        }

		        $slide_html = '<div class="swiper-slide-bg' . $ken_class . '"></div>' . $slide_html;

		        $slides[] = '<div class="elementor-repeater-item-' . $slide['_id'] . ' swiper-slide">' . $slide_html . '</div>';
	        }
            $slide_count ++;
        }

		$prev = 'left';
		$next = 'right';
		$direction = 'ltr';

		if ( is_rtl() ) {
			$prev = 'right';
			$next = 'left';
			$direction = 'rtl';
		}

		$show_dots = ( in_array( $settings['navigation'], [ 'dots', 'both' ], true ) );
		$show_arrows = ( in_array( $settings['navigation'], [ 'arrows', 'both' ], true ) );

		$slides_count = count( $settings['slides'] );
		?>
		<div class="elementor-swiper">
			<div class="twbb_slides-wrapper elementor-main-swiper swiper-container" dir="<?php echo esc_attr($direction); ?>" data-animation="<?php echo esc_attr( $settings['content_animation']); ?>">
				<div class="swiper-wrapper twbb_slides-widget">
                    <?php // PHPCS - Slides for each is safe. ?>
                    <?php echo implode( '', $slides ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
				<?php if ( 1 < $slides_count ) : ?>
					<?php if ( $show_dots ) : ?>
						<div class="swiper-pagination"></div>
					<?php endif; ?>
					<?php if ( $show_arrows ) : ?>
						<div class="elementor-swiper-button elementor-swiper-button-prev">
							<i class="eicon-chevron-<?php echo esc_attr($prev); ?>" aria-hidden="true"></i>
							<span class="elementor-screen-only"><?php esc_html_e( 'Previous', 'tenweb-builder' ); ?></span>
						</div>
						<div class="elementor-swiper-button elementor-swiper-button-next">
							<i class="eicon-chevron-<?php echo esc_attr($next); ?>" aria-hidden="true"></i>
							<span class="elementor-screen-only"><?php esc_html_e( 'Next', 'tenweb-builder' ); ?></span>
						</div>
					<?php endif; ?>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

    protected function template_slide_render( $slide ) {

        $template_id = $slide[ 'template_id' ];
        if ( 'publish' !== get_post_status( $template_id ) ) {
            return;
        }

        if ( $template_id === "" ) {
	        $slide_html = '<div class="elementor-template empty-template-desc">'. __( 'To view your slide, please choose a template.', 'tenweb-builder' ) . '</div>';
        } else {
	        $slide_html = '<div class="elementor-template">' . \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $template_id ) . '</div>';
        }

        $template_slide = '<div class="elementor-repeater-item-' . $slide['_id'] . ' swiper-slide swiper-slide-template" data-template-id="' . $template_id . '">' . $slide_html . '</div>';

	    return $template_slide;
    }

	/**
	 * Render Slides widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 2.9.0
	 * @access protected
	 */
    //phpcs:disable
	protected function content_template_backup() {
	    ?>
		<#
			var direction        = elementorFrontend.config.is_rtl ? 'rtl' : 'ltr',
				next             = elementorFrontend.config.is_rtl ? 'left' : 'right',
				prev             = elementorFrontend.config.is_rtl ? 'right' : 'left',
				navi             = settings.navigation,
				showDots         = ( 'dots' === navi || 'both' === navi ),
				showArrows       = ( 'arrows' === navi || 'both' === navi ),
				buttonSize       = settings.button_size;
		#>
		<div class="elementor-swiper">
			<div class="twbb_slides-wrapper elementor-main-swiper swiper-container" dir="{{ direction }}" data-animation="{{ settings.content_animation }}">
				<div class="swiper-wrapper twbb_slides-widget">
					<# jQuery.each( settings.slides, function( index, slide ) {
            if ( slide.slider_type == 'template_slide' ) {
              let template_id = slide.template_id; #>
              <div class="elementor-repeater-item-{{ slide._id }} swiper-slide swiper-slide-template" data-template-id="{{template_id}}">
              <# if ( template_id == null ) { #>
                  <div class="elementor-template empty-template-desc"><?php _e( 'To view your slide, please choose a template.', 'tenweb-builder' ); ?> </div>
              <# } else {
                /* TODO: Try to implement a solution for template load in content template. */
                } #>
              </div>
            <# } else { #>
                <div class="elementor-repeater-item-{{ slide._id }} swiper-slide">
                    <#
                    var kenClass = '';

                    if ( '' != slide.background_ken_burns ) {
                    kenClass = ' elementor-ken-burns elementor-ken-burns--' + slide.zoom_direction;
                    }
                    #>
                    <div class="swiper-slide-bg{{ kenClass }}"></div>
                        <# if ( 'yes' === slide.background_overlay ) { #>
                        <div class="elementor-background-overlay"></div>
                        <# } #>
                        <div class="swiper-slide-inner">
                            <div class="swiper-slide-contents">
                                <# if ( slide.heading ) { #>
                                    <div class="elementor-slide-heading">{{{ slide.heading }}}</div>
                                <# }
                                if ( slide.description ) { #>
                                    <div class="elementor-slide-description">{{{ slide.description }}}</div>
                                <# }
                                if ( slide.button_text ) { #>
                                    <div class="elementor-button elementor-slide-button elementor-size-{{ buttonSize }}">{{{ slide.button_text }}}</div>
                                <# } #>
                            </div>
                        </div>
                    </div>
                <# };
            } ); #>
				</div>
				<# if ( 1 < settings.slides.length ) { #>
					<# if ( showDots ) { #>
						<div class="swiper-pagination"></div>
					<# } #>
					<# if ( showArrows ) { #>
						<div class="elementor-swiper-button elementor-swiper-button-prev">
							<i class="eicon-chevron-{{ prev }}" aria-hidden="true"></i>
							<span class="elementor-screen-only"><?php _e( 'Previous', 'tenweb-builder' ); ?></span>
						</div>
						<div class="elementor-swiper-button elementor-swiper-button-next">
							<i class="eicon-chevron-{{ next }}" aria-hidden="true"></i>
							<span class="elementor-screen-only"><?php _e( 'Next', 'tenweb-builder' ); ?></span>
						</div>
					<# } #>
				<# } #>
			</div>
		</div>
		<?php
	}

    //phpcs:enable
}

\Elementor\Plugin::instance()->widgets_manager->register(new Slides());
