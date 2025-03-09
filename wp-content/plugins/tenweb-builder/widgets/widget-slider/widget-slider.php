<?php
namespace Tenweb_Builder;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Widget_Slider {
  public const ITEM_CLASS = 'swiper-slide';

	public static function init_slider_option($widget, $position, $default = 'yes') {
		$widget->start_injection( $position );

		$widget->add_control(
			'slider_view',
			[
				'type'               => Controls_Manager::SWITCHER,
				'label'              => __( 'Slider View', 'tenweb-builder' ),
				'default' => $default,
				'label_off' => __( 'Off', 'tenweb-builder' ),
				'label_on' => __( 'On', 'tenweb-builder' ),
				'prefix_class' => 'tenweb-widget-slider-',
        'render_type' => 'template'
			]
		);

		$widget->end_injection();
	}

	public static function add_slider_controls($widget, $position) {
		$widget->start_injection( $position );

		$widget->start_controls_section(
			'section_slider',
			[
				'label' => __( 'Slider Settings', 'tenweb-builder' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'slider_view' => 'yes',
				],
			]
		);

		$widget->add_responsive_control(
			'carousel_full_width',
			[
				'type' => Controls_Manager::SWITCHER,
				'label' => __( 'Full-Width Slider', 'tenweb-builder' ),
				'description' => __( 'This option overrides the container box width, impacting the layout design.', 'tenweb-builder' ),
				'default' => 'yes',
        'tablet_default' => 'yes',
        'mobile_default' => 'no',
				'prefix_class' => 'tenweb-widget-slider--full-width%s-',
				'label_off' => __( 'No', 'tenweb-builder' ),
				'label_on' => __( 'Yes', 'tenweb-builder' ),
			]
		);

		$widget->add_responsive_control(
			'slider_full_width_layout',
			[
				'type' => Controls_Manager::SELECT,
				'label' => __( 'Slider Layout', 'tenweb-builder' ),
				'options' => [
					'full-width' => __( 'Full-Width', 'tenweb-builder' ),
					'cut-from-right' => __( 'Cut From Right', 'tenweb-builder' ),
				],
				'default' => 'full-width',
        'tablet_default' => 'full-width',
        'mobile_default' => 'full-width',
				'prefix_class' => 'tenweb-widget-slider--full-width-layout%s-',
				'condition' => [
					'carousel_full_width' => 'yes',
				],
			]
		);

		$count_options = range( 1, 10 );
		$count_options = array_combine( $count_options, $count_options );

		$widget->add_responsive_control(
			'slides_per_view',
			[
				'type' => Controls_Manager::SELECT,
				'label' => __( 'Slides Per View', 'tenweb-builder' ),
				'options' => [ '' => __( 'Default', 'tenweb-builder' ) ] + $count_options,
				'frontend_available' => true,
			]
		);

		$widget->add_responsive_control(
			'slides_to_scroll',
			[
				'type' => Controls_Manager::SELECT,
				'label' => __( 'Slides to Scroll', 'tenweb-builder' ),
				'description' => __( 'Set how many slides are scrolled per swipe.', 'tenweb-builder' ),
				'options' => [ '' => __( 'Default', 'tenweb-builder' ) ] + $count_options,
				'frontend_available' => true,
			]
		);

		$widget->add_control(
			'speed',
			[
				'label' => __( 'Transition Duration', 'tenweb-builder' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 500,
				'frontend_available' => true,
			]
		);

		$widget->add_responsive_control(
			'show_arrows',
			[
				'type' => Controls_Manager::SWITCHER,
				'label' => __( 'Arrows', 'tenweb-builder' ),
				'default' => 'yes',
                'tablet_default' => 'yes',
                'mobile_default' => 'no',
				'label_off' => __( 'Hide', 'tenweb-builder' ),
				'label_on' => __( 'Show', 'tenweb-builder' ),
				'prefix_class' => 'tenweb-arrows%s-',
				'separator' => 'before',
			]
		);

        $widget->add_control(
            'arrows_icon',
            [
                'label' => __( 'Arrows icon', 'plugin-name' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'arrow1' => __( 'Arrow  1 ', 'plugin-name' ),
                    'arrow2' => __( 'Arrow  2', 'plugin-name' ),
                ],
                'default' => 'arrow2',
                'condition' => [
                    'show_arrows!' => '',
                ],
            ]
        );

		$widget->add_control(
			'hide_edge_arrows',
			[
				'label' => __( 'First/Last Slide Arrow', 'tenweb-builder' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'disable',
				'options' => [
					'disable' => __( 'Disable', 'tenweb-builder' ),
					'hide' => __( 'Hide', 'tenweb-builder' ),
				],
				'description' => __( 'In case autoloop is off you can hide or disable first/last slides arrows.', 'tenweb-builder' ),
				'prefix_class' => 'tenweb-widget-slider--edge-arrows-',
				'condition' => [
					'show_arrows!' => '',
				],
			]
		);

		$widget->add_responsive_control(
			'navigation_position',
			[
				'label' => __( 'Arrows Position', 'tenweb-builder' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'outside',
				'tablet_default' => 'outside',
				'mobile_default' => 'outside',
				'options' => [
					'inside' => __( 'Inside', 'tenweb-builder' ),
					'outside' => __( 'Outside', 'tenweb-builder' ),
				],
				'prefix_class' => 'tenweb-widget-slider--navigation-position%s-',
				'condition' => [
					'show_arrows!' => '',
				],
			]
		);

		$widget->add_responsive_control(
			'arrows_alignment',
			[
				'label' => __( 'Arrows Alignment', 'tenweb-builder' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'default' => 'right',
        'tablet_default' => 'right',
        'mobile_default' => 'right',
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
				'condition' => [
					'show_arrows!' => '',
					'navigation_position' => 'outside',
				],
				'prefix_class' => 'tenweb-widget-slider--arrows-position%s-',
			]
		);

	  $widget->add_responsive_control(
		  'show_pagination',
		  [
			  'type' => Controls_Manager::SWITCHER,
			  'label' => __( 'Pagination', 'tenweb-builder' ),
			  'default' => 'yes',
        'tablet_default' => 'yes',
        'mobile_default' => 'yes',
			  'label_off' => __( 'Hide', 'tenweb-builder' ),
			  'label_on' => __( 'Show', 'tenweb-builder' ),
			  'frontend_available' => true,
			  'prefix_class' => 'tenweb-pagination%s-',
			  'separator' => 'before',
		  ]
	  );

	  $widget->add_responsive_control(
		  'pagination_position',
		  [
			  'label' => __( 'Pagination Position', 'tenweb-builder' ),
			  'type' => Controls_Manager::SELECT,
			  'default' => 'outside',
			  'tablet_default' => 'outside',
			  'mobile_default' => 'outside',
			  'options' => [
				  'inside' => __( 'Inside', 'tenweb-builder' ),
				  'outside' => __( 'Outside', 'tenweb-builder' ),
			  ],
			  'prefix_class' => 'tenweb-widget-slider--pagination-position%s-',
			  'condition' => [
				  'show_pagination!' => '',
			  ],
		  ]
	  );

		$widget->add_control(
			'pagination',
			[
				'label' => __( 'Pagination Type', 'tenweb-builder' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'bullets',
				'options' => [
					'bullets' => __( 'Dots', 'tenweb-builder' ),
					'fraction' => __( 'Fraction', 'tenweb-builder' ),
				],
				'prefix_class' => 'tenweb-pagination-type-',
				'render_type' => 'template',
				'frontend_available' => true,
        'condition' => [
          'show_pagination!' => '',
        ],
			]
		);

		$widget->add_responsive_control(
			'pagination_alignment',
			[
				'label' => __( 'Pagination Alignment', 'tenweb-builder' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'default' => 'left',
        'tablet_default' => 'left',
        'mobile_default' => 'center',
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
				'condition' => [
					'show_pagination!' => '',
				],
				'prefix_class' => 'tenweb-widget-slider--pagination-position%s-',
			]
		);

		$widget->add_control(
			'autoplay',
			[
				'label' => __( 'Autoplay', 'tenweb-builder' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
				'separator' => 'before',
				'frontend_available' => true,
			]
		);

		$widget->add_control(
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

		$widget->add_control(
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

		$widget->add_control(
			'loop',
			[
				'label' => __( 'Infinite Loop', 'tenweb-builder' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
				'frontend_available' => true,
			]
		);

		$widget->end_controls_section();

		$widget->end_injection();
	}

	public static function add_slider_style_controls($widget, $position) {
		$widget->start_injection( $position );

		$widget->start_controls_section(
			'section_navigation',
			[
				'label' => __( 'Navigation', 'tenweb-builder' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'conditions' => [
          'relation' => 'or',
          'terms' => [
            [
              'name' => 'show_arrows',
              'operator' => '==',
              'value' => 'yes',
            ],
            [
              'name' => 'show_pagination',
              'operator' => '==',
              'value' => 'yes',
            ],
          ],
				],
			]
		);

	  $widget->add_responsive_control(
		  'navigation_gap',
		  [
			  'label' => __( 'Gap', 'tenweb-builder' ),
			  'type' => Controls_Manager::SLIDER,
			  'range' => [
				  'px' => [
					  'min' => 0,
					  'max' => 100,
				  ],
			  ],
			  'default' => [
				  'size' => 20,
				  'unit' => 'px',
			  ],
			  'selectors' => [
  		    'body[data-elementor-device-mode="desktop"] {{WRAPPER}}.tenweb-widget-slider--navigation-position-outside .swiper-pagination-container' => 'margin-top: {{SIZE}}{{UNIT}};',
  		    'body[data-elementor-device-mode="desktop"] {{WRAPPER}}.tenweb-widget-slider--pagination-position-outside .swiper-pagination-container' => 'margin-top: {{SIZE}}{{UNIT}};',
  		    'body[data-elementor-device-mode="tablet"] {{WRAPPER}}.tenweb-widget-slider--navigation-position-tablet-outside .swiper-pagination-container' => 'margin-top: {{SIZE}}{{UNIT}};',
  		    'body[data-elementor-device-mode="tablet"] {{WRAPPER}}.tenweb-widget-slider--pagination-position-tablet-outside .swiper-pagination-container' => 'margin-top: {{SIZE}}{{UNIT}};',
  		    'body[data-elementor-device-mode="mobile"] {{WRAPPER}}.tenweb-widget-slider--navigation-position-mobile-outside .swiper-pagination-container' => 'margin-top: {{SIZE}}{{UNIT}};',
  		    'body[data-elementor-device-mode="mobile"] {{WRAPPER}}.tenweb-widget-slider--pagination-position-mobile-outside .swiper-pagination-container' => 'margin-top: {{SIZE}}{{UNIT}};',
			  ],
		  ]
	  );

		$widget->add_control(
			'heading_arrows',
			[
				'label' => __( 'Arrows', 'tenweb-builder' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'none',
				'condition' => [
					'show_arrows!' => '',
				],
			]
		);

		$widget->add_responsive_control(
			'arrows_size',
			[
				'label' => __( 'Arrow icon size', 'tenweb-builder' ),
				'type' => Controls_Manager::SLIDER,
                'default' => [
                  'size' => 34,
                ],
                'tablet_default' => [
                  'size' => 34,
                ],
                'mobile_default' => [
                  'size' => 34,
                ],
				'range' => [
					'px' => [
						'min' => 10,
					],
				],
				'condition' => [
					'show_arrows!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .swiper-button-next:after, {{WRAPPER}} .swiper-button-prev:after' => 'height: calc({{SIZE}}{{UNIT}}/3); width: calc({{SIZE}}{{UNIT}}/3);font-size: calc({{SIZE}}{{UNIT}}/3);',
					'{{WRAPPER}} .swiper-button-next, {{WRAPPER}} .swiper-button-prev' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
					'body[data-elementor-device-mode="desktop"] {{WRAPPER}}.tenweb-widget-slider--navigation-position-outside .swiper-pagination-container' => 'min-height: {{SIZE}}{{UNIT}};',
					'body[data-elementor-device-mode="tablet"] {{WRAPPER}}.tenweb-widget-slider--navigation-position-tablet-outside .swiper-pagination-container' => 'min-height: {{SIZE}}{{UNIT}};',
					'body[data-elementor-device-mode="mobile"] {{WRAPPER}}.tenweb-widget-slider--navigation-position-mobile-outside .swiper-pagination-container' => 'min-height: {{SIZE}}{{UNIT}};',
					'body[data-elementor-device-mode="desktop"] {{WRAPPER}}.tenweb-widget-slider--pagination-position-inside.tenweb-widget-slider--navigation-position-outside .swiper-pagination' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'body[data-elementor-device-mode="tablet"] {{WRAPPER}}.tenweb-widget-slider--pagination-position-tablet-inside.tenweb-widget-slider--navigation-position-tablet-outside .swiper-pagination' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'body[data-elementor-device-mode="mobile"] {{WRAPPER}}.tenweb-widget-slider--pagination-position-mobile-inside.tenweb-widget-slider--navigation-position-mobile-outside .swiper-pagination' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'body[data-elementor-device-mode="desktop"] {{WRAPPER}}.tenweb-widget-slider--navigation-position-outside .swiper-pagination-container .swiper-pagination-arrows-container' => 'height: {{SIZE}}{{UNIT}};',
					'body[data-elementor-device-mode="tablet"] {{WRAPPER}}.tenweb-widget-slider--navigation-position-tablet-outside .swiper-pagination-container .swiper-pagination-arrows-container' => 'height: {{SIZE}}{{UNIT}};',
					'body[data-elementor-device-mode="mobile"] {{WRAPPER}}.tenweb-widget-slider--navigation-position-mobile-outside .swiper-pagination-container .swiper-pagination-arrows-container' => 'height: {{SIZE}}{{UNIT}};',
					'body[data-elementor-device-mode="desktop"] {{WRAPPER}}.tenweb-widget-slider--navigation-position-outside .swiper-pagination-container .swiper-pagination-arrows-wrapper' => 'width: calc(2 * {{SIZE}}{{UNIT}} + 15px);',
					'body[data-elementor-device-mode="tablet"] {{WRAPPER}}.tenweb-widget-slider--navigation-position-tablet-outside .swiper-pagination-container .swiper-pagination-arrows-wrapper' => 'width: calc(2 * {{SIZE}}{{UNIT}} + 15px);',
					'body[data-elementor-device-mode="mobile"] {{WRAPPER}}.tenweb-widget-slider--navigation-position-mobile-outside .swiper-pagination-container .swiper-pagination-arrows-wrapper' => 'width: calc(2 * {{SIZE}}{{UNIT}} + 15px);',
                    '{{WRAPPER}}' => ' --twbb-widget-slider-arrows-width: {{SIZE}}{{UNIT}}'
				],
			]
		);

		$widget->add_responsive_control(
			'arrows_color',
			[
				'label' => __( 'Arrows color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'show_arrows!' => '',
				],
                'global' => [
                    'default' => 'globals/colors?id=twbb_bg_inv',
                ],
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-arrows-container .swiper-button' => 'color: {{VALUE}}',
				],
			]
		);

		$widget->add_responsive_control(
			'arrows_hover_color',
			[
				'label' => __( 'Arrows hover color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'show_arrows!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-arrows-container .swiper-button:hover' => 'color: {{VALUE}}',
				],
			]
		);

        $widget->add_responsive_control(
            'arrows_background_color',
            [
                'label' => __( 'Background color', 'tenweb-builder' ),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'show_arrows!' => '',
                ],
                'global' => [
                    'default' => 'globals/colors?id=twbb_bg_3',
                ],
                'selectors' => [
                    '{{WRAPPER}} .swiper-pagination-arrows-container .swiper-button' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $widget->add_responsive_control(
            'arrows_background_hover_color',
            [
                'label' => __( 'Background hover color', 'tenweb-builder' ),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'show_arrows!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .swiper-pagination-arrows-container .swiper-button:hover' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $widget->add_group_control(Group_Control_Border::get_type(), [
            'name' => 'arrows_border',
            'selector' => '{{WRAPPER}} .swiper-pagination-arrows-container .swiper-button',
            'fields_options' => [
                'border' => [
                    'responsive' => true, // Enable responsiveness
                ],
            ],
            'separator' => 'before',
        ]);

        $widget->add_responsive_control('arrows_border_radius', [
            'label' => __('Border Radius', 'tenweb-builder'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%' ],
            'default' => [
                'top' => 17,
                'right' => 17,
                'bottom' => 17,
                'left' => 17,
                'unit' => 'px', // Default unit
            ],
            'tablet_default' => [
                'top' => 20,
                'right' => 20,
                'bottom' => 20,
                'left' => 20,
                'unit' => 'px',
            ],
            'mobile_default' => [
                'top' => 20,
                'right' => 20,
                'bottom' => 20,
                'left' => 20,
                'unit' => 'px',
            ],
            'selectors' => [
                '{{WRAPPER}} .swiper-pagination-arrows-container .swiper-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);


        $widget->add_control(
			'heading_pagination',
			[
				'label' => __( 'Pagination', 'tenweb-builder' ),
				'type' => Controls_Manager::HEADING,
				'condition' => [
					'show_pagination!' => '',
				],
			]
		);

		$widget->add_responsive_control(
			'pagination_size',
			[
				'label' => __( 'Size', 'tenweb-builder' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 20,
					],
				],
				'default' => [
					'size' => 8,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullet' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .swiper-container-horizontal .swiper-pagination-progress' => 'height: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .swiper-pagination-fraction' => 'font-size: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'show_pagination!' => '',
				],
			]
		);

		$widget->add_control(
			'pagination_color_active',
			[
				'label' => __( 'Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullet-active, {{WRAPPER}} .swiper-pagination-progressbar' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .swiper-pagination-fraction' => 'color: {{VALUE}}',
				],
				'condition' => [
					'show_pagination!' => '',
				],
			]
		);

		$widget->add_control(
			'pagination_color_secondary',
			[
				'label' => __( 'Secondary Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullet' => 'background-color: {{VALUE}}',
				],
				'condition' => [
			    'show_pagination!' => '',
					'pagination' => 'bullets',
				],
			]
		);

		$widget->end_controls_section();

		$widget->end_injection();
	}

	public static function get_slider_attributes($settings, $items_count, $slides_per_view_default = '') {
    $swiperObj = [
      'slider_view' => $settings['slider_view'],
      'slides_per_view' => !empty($settings['slides_per_view']) ? $settings['slides_per_view'] : $settings[$slides_per_view_default],
      'slides_per_view_tablet' => !empty($settings['slides_per_view_tablet']) ? $settings['slides_per_view_tablet'] : $settings[$slides_per_view_default . '_tablet'],
      'slides_per_view_mobile' => !empty($settings['slides_per_view_mobile']) ? $settings['slides_per_view_mobile'] : $settings[$slides_per_view_default . '_mobile'],
      'slides_to_scroll' => $settings['slides_to_scroll'],
      'slides_to_scroll_tablet' => $settings['slides_to_scroll_tablet'],
      'slides_to_scroll_mobile' => $settings['slides_to_scroll_mobile'],
      'slides_count' => $items_count,
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
    ];
    return [
      'class'         => 'tenweb-widget-slider swiper-container swiper-container-horizontal',
      'data-settings' => json_encode( $swiperObj ),//phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode
    ];
	}

	public static function slider_wrapper_start() {
    ?>
      <div class="swiper-wrapper">
    <?php
	}

	public static function slider_wrapper_end($args) {
      ?>
    </div>
    <?php
    $arrows_icon = !isset($args['arrows_icon']) ? 'arrow2' : $args['arrows_icon'];
    $arrows_icon_class = ' twbb-slider-arrows-icon ' . $arrows_icon;
    if ( 1 < $args['items_count'] ) { ?>
      <div class="swiper-pagination-container">
        <div class="swiper-pagination"></div>
        <div class="swiper-pagination-arrows-container">
          <div class="swiper-pagination-arrows-wrapper<?php echo esc_attr($arrows_icon_class); ?>">
            <div class="swiper-button swiper-button-prev"></div>
            <div class="swiper-button swiper-button-next"></div>
          </div>
        </div>
      </div>
    <?php
    }
	}
}
