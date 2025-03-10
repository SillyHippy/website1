<?php
namespace Tenweb_Builder;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) exit;

class Team extends Widget_Base {

	public function get_name() {
		return Builder::$prefix . '-team';
	}

	public function get_title() {
		return __( 'Team', 'tenweb-builder' );
	}

	public function get_icon() {
		return 'twbb-team twbb-widget-icon';
	}

	public function get_categories() {
		return [ 'tenweb-widgets' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_general',
			[
				'label' => __( 'General', 'tenweb-builder' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new Repeater();

		$this->add_repeater_controls( $repeater );

		$this->add_control(
			'members',
			[
				'label'   => __( 'Members', 'tenweb-builder' ),
				'type'    => Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
				'default' => $this->sample_members(),
		        'title_field' => '{{{ name }}}' //phpcs:ignore WordPressVIPMinimum.Security.Mustache.OutputNotation
			]
		);

	  $this->add_control(
		  'alignment',
		  [
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
			  'prefix_class' => 'tenweb-team--align-',
		  ]
	  );

	  $count_options = range( 1, 10 );
	  $count_options = array_combine( $count_options, $count_options );

	  $this->add_responsive_control(
		  'column_count',
		  [
			  'type' => Controls_Manager::SELECT,
			  'label' => __( 'Columns count', 'tenweb-builder' ),
			  'options' => [ '' => __( 'Default', 'tenweb-builder' ) ] + $count_options,
			  'devices' => [ 'desktop', 'tablet', 'mobile' ],
			  'desktop_default' => 3,
			  'tablet_default' => 2,
			  'mobile_default' => 1,
			  'frontend_available' => true,
	      'render_type' => 'template',
			  'prefix_class' => 'elementor-grid%s-',
			  'selectors' => [
				  '{{WRAPPER}} members-wrapper' => '--grid-template-columns: repeat({{VALUE}}, auto);',
			  ],
		  ]
	  );

	  $this->add_control(
		  'heading_social_icons',
		  [
			  'label' => __( 'Social Links', 'tenweb-builder' ),
			  'type' => Controls_Manager::HEADING,
			  'separator' => 'before',
		  ]
	  );

	  $this->add_control(
		  'show_social_links',
		  [
			  'type' => Controls_Manager::SWITCHER,
			  'label' => __( 'Social Links', 'tenweb-builder' ),
			  'default' => 'yes',
			  'label_off' => __( 'Hide', 'tenweb-builder' ),
			  'label_on' => __( 'Show', 'tenweb-builder' ),
			  'frontend_available' => true,
			  'render_type' => 'template',
		  ]
	  );

	  $this->add_control(
		  'social_links_shape',
		  [
			  'label' => esc_html__( 'Shape', 'tenweb-builder' ),
			  'type' => Controls_Manager::SELECT,
			  'default' => 'rounded',
			  'options' => [
				  'rounded' => esc_html__( 'Rounded', 'tenweb-builder' ),
				  'square' => esc_html__( 'Square', 'tenweb-builder' ),
				  'circle' => esc_html__( 'Circle', 'tenweb-builder' ),
			  ],
			  'prefix_class' => 'elementor-shape-',
			  'condition' => [
				  'show_social_links' => 'yes',
			  ],
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

	  $this->add_responsive_control(
		  'image_position',
		  [
			  'label' => esc_html__( 'Image Position', 'tenweb-builder' ),
			  'type' => Controls_Manager::CHOOSE,
			  'default' => 'top',
			  'options' => [
				  'left' => [
					  'title' => esc_html__( 'Left', 'tenweb-builder' ),
					  'icon' => 'eicon-h-align-left',
				  ],
				  'top' => [
					  'title' => esc_html__( 'Top', 'tenweb-builder' ),
					  'icon' => 'eicon-v-align-top',
				  ],
				  'right' => [
					  'title' => esc_html__( 'Right', 'tenweb-builder' ),
					  'icon' => 'eicon-h-align-right',
				  ],
				  'bottom' => [
					  'title' => esc_html__( 'Bottom', 'tenweb-builder' ),
					  'icon' => 'eicon-v-align-bottom',
				  ],
			  ],
			  'prefix_class' => 'tenweb-team--image-position-',
			  'toggle' => false,
		  ]
	  );

	  $this->add_control(
		  'image_shape',
		  [
			  'label' => __( 'Image Shape', 'tenweb-builder' ),
			  'type' => Controls_Manager::SELECT,
			  'default' => 'square',
			  'options' => [
				  'landscape' => __( 'Landscape 16:9', 'tenweb-builder' ),
				  'portrait' => __( 'Portrait 9:16', 'tenweb-builder' ),
				  'square' => __( 'Square', 'tenweb-builder' ),
				  'circle' => __( 'Circle', 'tenweb-builder' ),
			  ],
			  'prefix_class' => 'tenweb-team--image-shape-',
		  ]
	  );

	  $this->end_controls_section();

		$this->start_controls_section(
			'section_members_style',
			[
				'label' => __( 'Members', 'tenweb-builder' ),
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
					'size' => 48,
				],
				'tablet_default' => [
					'size' => 24,
				],
				'mobile_default' => [
					'size' => 24,
				],
				'render_type' => 'template',
				'frontend_available' => true,
				'selectors' => [
					'{{WRAPPER}} .tenweb-team-members .elementor-grid' => 'grid-column-gap: {{SIZE}}{{UNIT}} !important; grid-row-gap: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_control(
			'member_background_color',
			[
				'label' => __( 'Background Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tenweb-team-members .tenweb-team-member' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'member_border_size',
			[
				'label' => __( 'Border Size', 'tenweb-builder' ),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .tenweb-team-members .tenweb-team-member' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'member_border_color',
			[
				'label' => __( 'Border Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000000',
				'selectors' => [
					'{{WRAPPER}} .tenweb-team-members .tenweb-team-member' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'member_border_radius',
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
					'{{WRAPPER}} .tenweb-team-members .tenweb-team-member' => 'border-radius: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'member_padding',
			[
				'label' => __( 'Padding', 'tenweb-builder' ),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .tenweb-team-members .tenweb-team-member' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
				'separator' => 'before',
			]
		);

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
			  'label' => esc_html__( 'Width', 'elementor' ),
			  'type' => Controls_Manager::SLIDER,
			  'default' => [
				  'size' => 100,
				  'unit' => '%',
			  ],
			  'tablet_default' => [
				  'unit' => '%',
			  ],
			  'mobile_default' => [
				  'unit' => '%',
			  ],
			  'size_units' => [ 'px', '%' ],
			  'range' => [
				  '%' => [
					  'min' => 5,
					  'max' => 100,
				  ],
			  ],
			  'selectors' => [
				  '{{WRAPPER}} .tenweb-team-member__image_wrap' => 'width: {{SIZE}}{{UNIT}};',
				  '{{WRAPPER}}.tenweb-team--image-shape-square .tenweb-team-member__image_wrap' => 'padding-top: {{SIZE}}{{UNIT}};',
				  '{{WRAPPER}}.tenweb-team--image-shape-landscape .tenweb-team-member__image_wrap' => 'padding-top: calc({{SIZE}}{{UNIT}} * 0.5625);',
				  '{{WRAPPER}}.tenweb-team--image-shape-portrait .tenweb-team-member__image_wrap' => 'padding-top: calc({{SIZE}}{{UNIT}} * 1.7778);',
				  '{{WRAPPER}}.tenweb-team--image-shape-circle .tenweb-team-member__image_wrap' => 'padding-top: {{SIZE}}{{UNIT}};border-radius: 50%;',
				  '{{WRAPPER}}.tenweb-team--image-shape-circle .tenweb-team-member__image_wrap .tenweb-team-member__image img' => 'border-radius: 50%;',
			  ],
		  ]
	  );

	  $this->add_responsive_control(
		  'image_gap',
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
          'size' => 24,
          'unit' => 'px',
        ],
        'tablet_default' => [
          'size' => 20,
          'unit' => 'px',
        ],
        'mobile_default' => [
          'size' => 20,
          'unit' => 'px',
        ],
			  'selectors' => [
				  '{{WRAPPER}}.elementor-widget-twbb-team.tenweb-team--image-position-top .tenweb-team-member__image_container' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				  '{{WRAPPER}}.elementor-widget-twbb-team.tenweb-team--image-position-right .tenweb-team-member__image_container' => 'margin-left: {{SIZE}}{{UNIT}}',
				  '{{WRAPPER}}.elementor-widget-twbb-team.tenweb-team--image-position-bottom .tenweb-team-member__image_container' => 'margin-top: {{SIZE}}{{UNIT}}',
				  '{{WRAPPER}}.elementor-widget-twbb-team.tenweb-team--image-position-left .tenweb-team-member__image_container' => 'margin-right: {{SIZE}}{{UNIT}}',
			  ],
		  ]
	  );

	  $this->add_control(
		  'image_border',
		  [
			  'label' => __( 'Border', 'tenweb-builder' ),
			  'type' => Controls_Manager::SWITCHER,
			  'selectors' => [
				  '{{WRAPPER}} .tenweb-team-member__image img' => 'border-style: solid',
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
				  '{{WRAPPER}} .tenweb-team-member__image img' => 'border-color: {{VALUE}}',
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
				  '{{WRAPPER}} .tenweb-team-member__image img' => 'border-width: {{SIZE}}{{UNIT}}',
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
			  'size_units' => [ 'px', '%' ],
			  'range' => [
				  '%' => [
					  'max' => 50,
				  ],
			  ],
        'condition' => [
          'image_shape!' => 'circle',
        ],
			  'selectors' => [
				  '{{WRAPPER}} .tenweb-team-member__image img' => 'border-radius: {{SIZE}}{{UNIT}}',
			  ],
		  ]
	  );

	  $this->end_controls_section();

		$this->start_controls_section(
			'section_content_style',
			[
				'label' => __( 'Content', 'tenweb-builder' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'name_title_style',
			[
				'label' => __( 'Name', 'tenweb-builder' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

	  $this->add_responsive_control(
		  'name_gap',
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
				  '{{WRAPPER}}.elementor-widget-twbb-team .tenweb-team-member__name' => 'margin-bottom: {{SIZE}}{{UNIT}}',
			  ],
		  ]
	  );

		$this->add_control(
			'name_color',
			[
				'label' => __( 'Text Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tenweb-team-member__name' => 'color: {{VALUE}}',
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
				'selector' => '{{WRAPPER}} .tenweb-team-member__name',
        'fields_options' => [
          'font_size' => [
            'default' => ['unit' => 'px', 'size' => 20],
            'tablet_default' => ['unit' => 'px', 'size' => 18],
            'mobile_default' => ['unit' => 'px', 'size' => 18],
          ],
          'line_height' => [
            'default' => ['unit' => 'px', 'size' => 30],
            'tablet_default' => ['unit' => 'px', 'size' => 27],
            'mobile_default' => ['unit' => 'px', 'size' => 27],
          ],
          'font_weight' => [
            'default' => 600
          ],
        ],
			]
		);

		$this->add_control(
			'heading_title_style',
			[
				'label' => __( 'Title', 'tenweb-builder' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

	  $this->add_responsive_control(
		  'title_gap',
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
          'size' => 16,
          'unit' => 'px',
        ],
        'tablet_default' => [
          'size' => 12,
          'unit' => 'px',
        ],
        'mobile_default' => [
          'size' => 12,
          'unit' => 'px',
        ],
			  'selectors' => [
				  '{{WRAPPER}}.elementor-widget-twbb-team .tenweb-team-member__title' => 'margin-bottom: {{SIZE}}{{UNIT}}',
			  ],
		  ]
	  );

		$this->add_control(
			'title_color',
			[
				'label' => __( 'Text Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tenweb-team-member__title' => 'color: {{VALUE}}',
				],
        'global' => [
          'default' => Global_Colors::COLOR_TEXT,
        ],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .tenweb-team-member__title',
        'fields_options' => [
          'font_size' => [
	          'default' => ['unit' => 'px', 'size' => 18],
	          'tablet_default' => ['unit' => 'px', 'size' => 16],
	          'mobile_default' => ['unit' => 'px', 'size' => 16],
          ],
          'line_height' => [
            'default' => ['unit' => 'px', 'size' => 27],
            'tablet_default' => ['unit' => 'px', 'size' => 24],
            'mobile_default' => ['unit' => 'px', 'size' => 24],
          ],
          'font_weight' => [
	          'default' => 400
          ],
        ],
			]
		);

	  $this->add_control(
		  'heading_description_style',
		  [
			  'label' => __( 'Description', 'tenweb-builder' ),
			  'type' => Controls_Manager::HEADING,
			  'separator' => 'before',
		  ]
	  );

	  $this->add_responsive_control(
		  'description_gap',
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
          'size' => 24,
          'unit' => 'px',
        ],
			  'selectors' => [
				  '{{WRAPPER}}.elementor-widget-twbb-team .tenweb-team-member__description' => 'margin-bottom: {{SIZE}}{{UNIT}}',
			  ],
		  ]
	  );

	  $this->add_control(
		  'member_color',
		  [
			  'label' => __( 'Text Color', 'tenweb-builder' ),
			  'type' => Controls_Manager::COLOR,
			  'selectors' => [
				  '{{WRAPPER}} .tenweb-team-member__description' => 'color: {{VALUE}}',
			  ],
        'global' => [
          'default' => Global_Colors::COLOR_TEXT,
        ],
		  ]
	  );

	  $this->add_group_control(
		  Group_Control_Typography::get_type(),
		  [
			  'name' => 'description_typography',
			  'selector' => '{{WRAPPER}} .tenweb-team-member__description',
        'fields_options' => [
          'font_size' => [
            'default' => ['unit' => 'px', 'size' => 16]
          ],
          'line_height' => [
            'default' => ['unit' => 'px', 'size' => 24]
          ],
          'font_weight' => [
	          'default' => 400
          ],
        ],
		  ]
	  );

		$this->end_controls_section();

		$this->start_controls_section(
			'section_social_style',
			[
				'label' => esc_html__( 'Social Icon', 'tenweb-builder' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => ['show_social_links' => 'yes'],
			]
		);

	  $this->add_responsive_control(
		  'icon_gap',
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
          'size' => 28,
          'unit' => 'px',
        ],
			  'selectors' => [
				  '{{WRAPPER}}.elementor-widget-twbb-team .elementor-social-icons-wrapper' => 'margin-bottom: {{SIZE}}{{UNIT}}',
			  ],
		  ]
	  );

		$this->add_control(
			'icon_primary_color',
			[
				'label' => esc_html__( 'Primary Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
        'default' => '#ffffff00',
				'selectors' => [
					'{{WRAPPER}} .elementor-social-icon' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_secondary_color',
			[
				'label' => esc_html__( 'Secondary Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
		    'default' => '#000000',
				'selectors' => [
					'{{WRAPPER}} .elementor-social-icon i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementor-social-icon svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label' => esc_html__( 'Size', 'tenweb-builder' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 6,
						'max' => 300,
					],
				],
        'default' => [
          'size' => 18,
          'unit' => 'px',
        ],
				'selectors' => [
					'{{WRAPPER}}' => '--icon-size: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'icon_padding',
			[
				'label' => esc_html__( 'Padding', 'tenweb-builder' ),
				'type' => Controls_Manager::SLIDER,
				// The `%' unit is not supported.
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .elementor-social-icon' => '--icon-padding: {{SIZE}}{{UNIT}}',
				],
				'default' => [
          'size' => 0,
					'unit' => 'px',
				],
				'tablet_default' => [
			    'size' => 0,
					'unit' => 'px',
				],
				'mobile_default' => [
			    'size' => 0,
					'unit' => 'px',
				],
				'range' => [
					'px' => [
						'max' => 50,
					],
				],
			]
		);

		$this->add_responsive_control(
			'icon_spacing',
			[
				'label' => esc_html__( 'Spacing', 'tenweb-builder' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'max' => 100,
					],
				],
				'default' => [
					'size' => 14,
          'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-social-icons-wrapper span' => 'margin-right: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'icon_border',
				'selector' => '{{WRAPPER}} .elementor-social-icon',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'tenweb-builder' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .elementor-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'section_social_hover',
			[
				'label' => __( 'Icon Hover', 'tenweb-builder' ),
				'type' => Controls_Manager::HEADING,
		    'separator' => 'before',
			]
		);

		$this->add_control(
			'icon_hover_primary_color',
			[
				'label' => esc_html__( 'Primary Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-social-icon:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_hover_secondary_color',
			[
				'label' => esc_html__( 'Secondary Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-social-icon:hover i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementor-social-icon:hover svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_hover_border_color',
			[
				'label' => esc_html__( 'Border Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'condition' => [
					'icon_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-social-icon:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_hover_animation',
			[
				'label' => esc_html__( 'Hover Animation', 'tenweb-builder' ),
				'type' => Controls_Manager::HOVER_ANIMATION,
			]
		);

		$this->end_controls_section();

    $this->inject_slider();
	}

	protected function add_repeater_controls( Repeater $repeater ) {
		$repeater->add_control(
			'image',
			[
				'label' => __( 'Image', 'tenweb-builder' ),
				'type'  => Controls_Manager::MEDIA,
			]
		);

		$repeater->add_control(
			'name',
			[
				'label' => __( 'Name', 'tenweb-builder' ),
				'type'  => Controls_Manager::TEXT,
			]
		);

		$repeater->add_control(
			'title',
			[
				'label' => __( 'Title', 'tenweb-builder' ),
				'type'  => Controls_Manager::TEXT,
			]
		);

		$repeater->add_control(
			'description',
			[
				'label' => __( 'Description', 'tenweb-builder' ),
				'type'  => Controls_Manager::TEXTAREA,
			]
		);

		$repeater->add_control(
			'heading_social_links',
			[
				'label' => __( 'Social Links', 'tenweb-builder' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$repeater->add_control(
			'facebook_link',
			[
				'label' => esc_html__( 'Facebook', 'tenweb-builder' ),
				'type' => Controls_Manager::URL,
				'default' => [
					'is_external' => 'true',
          'url' => 'https://www.facebook.com/',
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'linkedin_link',
			[
				'label' => esc_html__( 'Linkedin', 'tenweb-builder' ),
				'type' => Controls_Manager::URL,
				'default' => [
					'is_external' => 'true',
          'url' => 'https://www.linkedin.com/',
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'x-twitter_link',
			[
				'label' => esc_html__( 'X Twitter', 'tenweb-builder' ),
				'type' => Controls_Manager::URL,
				'default' => [
					'is_external' => 'true',
          'url' => 'https://x.com/',
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

	}

  protected function inject_slider() {
	  Widget_Slider::init_slider_option($this, [
	    'at' => 'after',
	    'of' => 'section_general',
    ]);

	  Widget_Slider::add_slider_controls($this, [
      'type' => 'section',
	    'at' => 'end',
	    'of' => 'section_general',
    ]);

	  Widget_Slider::add_slider_style_controls($this, [
      'type' => 'section',
	    'at' => 'end',
	    'of' => 'section_social_style',
    ]);
  }

	private function sample_members() {
		$image_src     = Utils::get_placeholder_image_src();
		$sample_member = [
			'image'       => [
				'url' => $image_src,
			],
			'name'        => __( 'Full name', 'tenweb-builder' ),
			'title'       => __( 'Job title', 'tenweb-builder' ),
			'description' => __( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse varius enim in eros elementum tristique.', 'tenweb-builder' ),
		];

		return array_fill( 0, 6, $sample_member );
	}

	protected function render() {
		$settings = $this->get_controls_settings();

    $this->add_render_attribute( 'tenweb-team-view-type', [
        'class'         => 'elementor-grid',
      ]
    );
    $this->add_render_attribute( 'tenweb-team-member', [
        'class'         => 'elementor-grid-item',
      ]
    );
    $items_count = count( $settings['members'] );
	  if ('yes' === $settings['slider_view']) {
	    $this->set_render_attribute( 'tenweb-team-member', [
			    'class'         => Widget_Slider::ITEM_CLASS,
		    ]
	    );
      $this->set_render_attribute( 'tenweb-team-view-type', Widget_Slider::get_slider_attributes($settings, $items_count, 'column_count') );
    }
		?>
		<div class="tenweb-team-members">
			<div <?php echo $this->get_render_attribute_string( 'tenweb-team-view-type' );//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
        <?php
	      if ('yes' === $settings['slider_view']) {
          Widget_Slider::slider_wrapper_start();
        }
        foreach ( $settings['members'] as $index => $member ) {
          ?>
          <div <?php echo $this->get_render_attribute_string( 'tenweb-team-member' );//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
            <?php $this->print_member( $member, $settings, 'member-' . $index ); ?>
          </div>
        <?php }
        if ('yes' === $settings['slider_view']) {
            $arrows_icon = isset($settings['arrows_icon']) ? $settings['arrows_icon'] : 'arrow2';
	        Widget_Slider::slider_wrapper_end(['items_count' => $items_count, 'arrows_icon' => $arrows_icon]);
        }
        ?>
      </div>
		</div>
		<?php
	}

	protected function print_member( $member = array(), $settings = array(), $key = '' ) {
		$this->add_render_attribute( $key, [
			'class' => 'tenweb-team-member',
		] );

		if ( ! empty( $member['image']['url'] ) ) {
			$image_src = Group_Control_Image_Size::get_attachment_image_src( $member['image']['id'], 'image_size', $settings );
			if ( ! $image_src ) {
				$image_src = $member['image']['url'];
			}

			$this->add_render_attribute( $key . '-image', [
				'src' => $image_src,
				'alt' => !empty( $member['name'] ) ? $member['name'] : '',
			] );
		}
		?>
      <div <?php echo $this->get_render_attribute_string( $key );//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	      <?php if ( $member['image']['url'] ) { ?>
          <div class="tenweb-team-member__image_container">
            <div class="tenweb-team-member__image_wrap">
              <div class="tenweb-team-member__image">
                <img <?php echo $this->get_render_attribute_string( $key . '-image' );//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
              </div>
            </div>
          </div>
	      <?php }
        ?>
        <div class="tenweb-team-member__content">
        <?php
        if ( ! empty( $member['name'] ) ) {
          ?>
          <div class="tenweb-team-member__name"><?php echo esc_html( $member['name'] ); ?></div>
          <?php
        }
        if ( ! empty( $member['title'] ) ) {
          ?>
          <div class="tenweb-team-member__title"><?php echo esc_html( $member['title'] ); ?></div>
          <?php
        }
        if ( ! empty( $member['description'] ) ) {
          ?>
          <div class="tenweb-team-member__description"><?php echo esc_html( $member['description'] ); ?></div>
          <?php
        }
        if ( 'yes' === $settings['show_social_links'] && (! empty( $member['facebook_link']['url'] ) || ! empty( $member['x-twitter_link']['url'] ) || ! empty( $member['linkedin_link']['url'] ) ) ) {
          $this->print_icons($member, $settings);
        }
        ?>
        </div>
      </div>
		<?php
	}

	protected function print_icons($member, $settings) {
		$social_list = [
			'facebook',
			'linkedin',
      'x-twitter',
    ];

		$class_animation = '';

		if ( ! empty( $settings['icon_hover_animation'] ) ) {
			$class_animation = ' elementor-animation-' . $settings['icon_hover_animation'];
		}

		?>
      <div class="elementor-social-icons-wrapper">
		  <?php
		  foreach ( $social_list as $social ) {
        if ($member[$social . '_link']['url']) {
          $link_key = 'link_' . $social;

          $this->add_render_attribute( $link_key, 'class', [
            'elementor-icon',
            'elementor-social-icon',
            'elementor-social-icon-' . $social . $class_animation,
          ] );

          $this->add_link_attributes( $link_key, $member[$social . '_link'] );

          ?>
          <span>
            <a <?php $this->print_render_attribute_string( $link_key ); ?>>
              <span class="elementor-screen-only"><?php echo esc_html( ucwords( $social ) ); ?></span>
              <?php
                Icons_Manager::render_icon( ['library' => 'fa-brands', 'value' => 'fab fa-' . $social] );
              ?>
            </a>
          </span>
        <?php
        }
      } ?>
      </div>
		<?php
	}

}

\Elementor\Plugin::instance()->widgets_manager->register( new Team() );