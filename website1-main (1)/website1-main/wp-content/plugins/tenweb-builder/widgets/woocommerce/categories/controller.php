<?php
namespace Tenweb_Builder\Widgets\Woocommerce\Products;

use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Icons_Manager;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Tenweb_Builder\Classes\Woocommerce\Woocommerce;
use Tenweb_Builder\Widget_Slider;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Categories extends Widget_Base {

	protected $_has_template_content = false;

	public function get_name() {
		return 'twbb_woocommerce-categories';
	}

	public function get_title() {
		return __( 'Product Categories', 'tenweb-builder' );
	}

	public function get_icon() {
		return 'twbb-product_categories twbb-widget-icon';
	}

	public function get_categories() {
		return [ Woocommerce::WOOCOMMERCE_GROUP ];
	}

	public function get_keywords() {
		return [ 'woocommerce', 'shop', 'store', 'product', 'categories' ];
	}	

	protected function register_controls() {
		$this->start_controls_section(
			'section_layout',
			[
				'label' => __( 'Layout', 'tenweb-builder' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label' => __( 'Columns', 'tenweb-builder' ),
				'type' => Controls_Manager::NUMBER,
				'prefix_class' => 'elementor-products-columns%s-',
				'render_type' => 'template',
				'default' => 3,
        'tablet_default' => '2',
        'mobile_default' => '1',
				'min' => 1,
				'max' => 12,
			]
		);

		$this->add_control(
			'number',
			[
				'label' => __( 'Categories Count', 'tenweb-builder' ),
				'type' => Controls_Manager::NUMBER,
				'default' => '3',
			]
		);

        $this->add_control(
            'show_title',
            [
                'label' => __( 'Show Title', 'tenweb-builder' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'tenweb-builder' ),
                'label_off' => esc_html__( 'No', 'tenweb-builder' ),
                'return_value' => 'block',
                'default' => 'yes',
                'selectors' => [
                    '{{WRAPPER}} .woocommerce-loop-category__title' => 'display: {{SIZE}}',
                ],
            ]
        );

        $this->add_control(
            'categories_count',
            [
                'label' => __( 'Categories Count', 'tenweb-builder' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Hide', 'tenweb-builder' ),
                'label_off' => esc_html__( 'Show', 'tenweb-builder' ),
                'return_value' => 'none',
                'default' => 'none',
                'selectors' => [
                    '{{WRAPPER}} .woocommerce-loop-category__title .count' => 'display: {{SIZE}}',
                ],
                'condition' => [
                    'show_title' => 'block',
                ],
            ]
        );

        $this->add_control(
            'category_title_position',
            [
                'label' => __( 'Category Content Position', 'tenweb-builder' ),
                'label_block' => true,
                'type' => Controls_Manager::SELECT,
                'default' => 'outside-bottom',
                'options' => [
                    'outside-bottom' => __( 'Outside', 'tenweb-builder' ),
                    'inside' => __( 'Inside', 'tenweb-builder' ),
                ],
                'prefix_class' => 'twbb-category-title-position-',
            ]
        );

        $this->add_control(
            'regulate_image_height',
            [
                'label' => __( 'Regulate Image Height', 'tenweb-builder' ),
                'type' => Controls_Manager::SWITCHER,
                'render_type' => 'template',
                'label_on' => esc_html__( 'Yes', 'tenweb-builder' ),
                'label_off' => esc_html__( 'No', 'tenweb-builder' ),
                'default' => 'no',
                'prefix_class' => 'twbb-category-regulate-image-height-',
            ]
        );

        $this->add_responsive_control(
                'image_height',
            [
                'label' => __( 'Images Height', 'tenweb-builder' ),
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
                    'size' => 100,
                    'unit' => 'vh',
                ],
                'tablet_default' => [
                    'size' => 50,
                    'unit' => 'vh',
                ],
                'mobile_default' => [
                    'size' => 50,
                    'unit' => 'vh',
                ],
                'size_units' => [ 'px', 'vh', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .twbb-category-image-wrapper' => 'height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'regulate_image_height' => 'yes',
                ],
            ]
        );

		$this->end_controls_section();

        $this->start_controls_section(
            'button_content',
            [
                'label' => __( 'Button', 'tenweb-builder' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'show_button',
            [
                'label' => __( 'Show Button', 'tenweb-builder' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'label_on' => 'Show',
                'label_off' => 'Hide',
            ]
        );

        $this->add_control(
            'button_text',
            [
                'label' => esc_html__( 'Text', 'elementor' ),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => esc_html__( 'Shop Now', 'elementor' ),
                'placeholder' => esc_html__( 'Show Now', 'elementor' ),
                'condition' => [
                    'show_button' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'button_selected_icon',
            [
                'label' => esc_html__( 'Icon', 'elementor' ),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'icon',
                'skin' => 'inline',
                'label_block' => false,
                'condition' => [
                    'show_button' => 'yes',
                ],
            ]
        );

        $start = is_rtl() ? 'right' : 'left';
        $end = is_rtl() ? 'left' : 'right';

        $this->add_control(
            'button_icon_align',
            [
                'label' => esc_html__( 'Icon Position', 'elementor' ),
                'type' => Controls_Manager::CHOOSE,
                'default' => is_rtl() ? 'row-reverse' : 'row',
                'options' => [
                    'row' => [
                        'title' => esc_html__( 'Start', 'elementor' ),
                        'icon' => "eicon-h-align-{$start}",
                    ],
                    'row-reverse' => [
                        'title' => esc_html__( 'End', 'elementor' ),
                        'icon' => "eicon-h-align-{$end}",
                    ],
                ],
                'selectors_dictionary' => [
                    'left' => is_rtl() ? 'row-reverse' : 'row',
                    'right' => is_rtl() ? 'row' : 'row-reverse',
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-button-content-wrapper' => 'flex-direction: {{VALUE}};',
                ],
                'condition' => [
                    'button_text!' => '',
                    'button_selected_icon[value]!' => '',
                    'show_button' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'button_icon_indent',
            [
                'label' => esc_html__( 'Icon Spacing', 'elementor' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem', 'custom' ],
                'range' => [
                    'px' => [
                        'max' => 50,
                    ],
                    'em' => [
                        'max' => 5,
                    ],
                    'rem' => [
                        'max' => 5,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-button .elementor-button-content-wrapper' => 'gap: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'button_text!' => '',
                    'button_selected_icon[value]!' => '',
                    'show_button' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();

		$this->start_controls_section(
			'section_filter',
			[
				'label' => __( 'Select Category ', 'tenweb-builder' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'source',
			[
				'label' => __( 'Source', 'tenweb-builder' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => __( 'Show All', 'tenweb-builder' ),
					'by_id' => __( 'Manual Selection', 'tenweb-builder' ),
					'by_parent' => __( 'By Parent', 'tenweb-builder' ),
					'current_subcategories' => __( 'Current Subcategories', 'tenweb-builder' ),
				],
				'label_block' => true,
			]
		);

		$categories = get_terms( 'product_cat' );

		$options = [];
		foreach ( $categories as $category ) {
			$options[ $category->term_id ] = $category->name;
		}

		$this->add_control(
			'categories',
			[
				'label' => __( 'Categories', 'tenweb-builder' ),
				'type' => Controls_Manager::SELECT2,
				'options' => $options,
				'default' => [],
				'label_block' => true,
				'multiple' => true,
				'condition' => [
					'source' => 'by_id',
				],
			]
		);

		$parent_options = [ '0' => __( 'Only Top Level', 'tenweb-builder' ) ] + $options;
		$this->add_control(
			'parent',
			[
				'label' => __( 'Parent', 'tenweb-builder' ),
				'type' => Controls_Manager::SELECT,
				'default' => '0',
				'options' => $parent_options,
				'condition' => [
					'source' => 'by_parent',
				],
			]
		);

		$this->add_control(
			'hide_empty',
			[
				'label' => __( 'Hide Empty', 'tenweb-builder' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => 'Hide',
				'label_off' => 'Show',
				'condition' => [
					'source' => '',
				],
			]
		);

		$this->add_control(
			'orderby',
			[
				'label' => __( 'Order By', 'tenweb-builder' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'name',
				'options' => [
					'name' => __( 'Name', 'tenweb-builder' ),
					'slug' => __( 'Slug', 'tenweb-builder' ),
					'description' => __( 'Description', 'tenweb-builder' ),
					'count' => __( 'Count', 'tenweb-builder' ),
				],
			]
		);

		$this->add_control(
			'order',
			[
				'label' => __( 'Order', 'tenweb-builder' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'desc',
				'options' => [
					'asc' => __( 'ASC', 'tenweb-builder' ),
					'desc' => __( 'DESC', 'tenweb-builder' ),
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_products_style',
			[
				'label' => __( 'Products', 'tenweb-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'products_class',
			[
				'type' => Controls_Manager::HIDDEN,
				'default' => 'wc-products',
				'prefix_class' => 'elementor-',
			]
		);

		$this->add_control(
			'column_gap',
			[
				'label'     => __( 'Columns Gap', 'tenweb-builder' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => 20,
				],
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
        'render_type' => 'template',
				'selectors' => [
					'{{WRAPPER}}.elementor-wc-products  ul.products' => 'grid-column-gap: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'row_gap',
			[
				'label'     => __( 'Rows Gap', 'tenweb-builder' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => 40,
				],
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}}.elementor-wc-products  ul.products' => 'grid-row-gap: {{SIZE}}{{UNIT}}',
				],
			]
		);

        $this->add_responsive_control(
            'content_horizontal_position',
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
                'selectors' => [
                        '{{WRAPPER}} ul.products li.product .twbb-woocommerce_category_content_container' => 'text-align: {{VALUE}};'
                ]
            ]
        );

        $this->add_responsive_control(
            'content_vertical_position',
            [
                'label' => __( 'Vertical Position', 'tenweb-builder' ),
                'type' => Controls_Manager::CHOOSE,
                'default' => 'center',
                'options' => [
                    'flex-start' => [
                        'title' => __( 'Top', 'tenweb-builder' ),
                        'icon' => 'eicon-v-align-top',
                    ],
                    'center' => [
                        'title' => __( 'Middle', 'tenweb-builder' ),
                        'icon' => 'eicon-v-align-middle',
                    ],
                    'flex-end' => [
                        'title' => __( 'Bottom', 'tenweb-builder' ),
                        'icon' => 'eicon-v-align-bottom',
                    ],
                ],
                'condition' => [
                    'category_title_position' => 'inside',
                ],
                'selectors' => [
                    '{{WRAPPER}}.twbb-category-title-position-inside ul.products li.product .twbb-woocommerce_category_content_container' => 'justify-content: {{VALUE}};'
                ]
            ]
        );

        $this->add_responsive_control(
            'box_padding',
            [
                'label'          => __('Padding', 'tenweb-builder'),
                'type'           => Controls_Manager::DIMENSIONS,
                'size_units'     => ['px', '%', 'em'],
                'tablet_default' => [
                    'size' => 0,
                    'unit' => 'px',
                ],
                'mobile_default' => [
                    'size' => 0,
                    'unit' => 'px',
                ],
                'condition' => [
                    'category_title_position' => 'inside',
                ],
                'selectors'      => [
                    '{{WRAPPER}}.twbb-category-title-position-inside ul.products li.product .twbb-woocommerce_category_content_container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

		$this->add_control(
			'heading_image_style',
			[
				'label'     => __( 'Image', 'tenweb-builder' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

        $this->add_control(
            'image_overlay_color',
            [
                'label'     => __( 'Image Overlay', 'tenweb-builder' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#0000003D',
                'selectors' => [
                    '{{WRAPPER}} .twbb-category-image::before, {{WRAPPER}} .twbb-category-image-wrapper:before' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'image_hover_animation',
            [
                'label' => esc_html__( 'Hover Animation', 'tenweb-builder' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    '' => 'None',
                    'grow' => 'Zoom In',
                    'shrink-contained' => 'Zoom Out',
                    'move-contained-left' => 'Move Left',
                    'move-contained-right' => 'Move Right',
                    'move-contained-top' => 'Move Up',
                    'move-contained-bottom' => 'Move Down',
                ],
                'default' => 'grow',
                'prefix_class' => 'elementor-animated-item--',
            ]
        );

        $this->add_control(
            'content_animation_duration',
            [
                'label' => __('Animation Duration', 'tenweb-builder'),
                'type' => Controls_Manager::SLIDER,
                'render_type' => 'ui',
                'default' => [
                    'size' => 1000,
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 3000,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} ul.products[class*=columns-] li.product .twbb-category-image,
                    {{WRAPPER}} ul.products[class*=columns-] li.product img' => 'transition-duration: {{SIZE}}ms; transition-delay: calc( {{SIZE}}ms / 3 )',
                ]
            ]
        );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'image_border',
				'selector' => '{{WRAPPER}} a .twbb-category-image-wrapper',
			]
		);

		$this->add_responsive_control(
			'image_border_radius',
			[
				'label'      => __( 'Border Radius', 'tenweb-builder' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .product > a, {{WRAPPER}} .product > a .twbb-category-image-wrapper, a .twbb-category-image-wrapper img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'image_spacing',
			[
				'label'      => __( 'Spacing', 'tenweb-builder' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} a, {{WRAPPER}} a .twbb-category-image-wrapper' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
                'condition' => [
                    'category_title_position' => 'outside-bottom',
                ],
            ],
		);

		$this->end_controls_section();

        $this->start_controls_section(
            'categories_content_style',
            [
                'label' => __( 'Categories Style', 'tenweb-builder' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'heading_title_style',
            [
                'label'     => __( 'Title', 'tenweb-builder' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'show_title' => 'block',
                ],
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label'     => __( 'Color', 'tenweb-builder' ),
                'type'      => Controls_Manager::COLOR,
                'global' => [
                    'default' => 'globals/colors?id=twbb_primary_inv'
                ],
                'selectors' => [
                    '{{WRAPPER}} .woocommerce-loop-category__title' => 'color: {{VALUE}}',
                ],
                'condition' => [
                    'show_title' => 'block',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'title_typography',
                'global' => [
					'default' => 'globals/typography?id=twbb_h5',
				],
                'selector' => '{{WRAPPER}} .woocommerce-loop-category__title',
                'condition' => [
                    'show_title' => 'block',
                ],
            ]
        );

        $this->add_control(
            'title_background_color',
            [
                'label'     => __('Background Color', 'tenweb-builder'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .woocommerce-loop-category__title' => 'background-color: {{VALUE}}',
                ],
                'condition' => [
                    'show_title' => 'block',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'      => 'title_border',
                'selector' => '{{WRAPPER}} .woocommerce-loop-category__title',
                'condition' => [
                    'show_title' => 'block',
                ],
            ]
        );

        $this->add_control(
            'title_border_radius',
            [
                'label'      => __('Border Radius', 'tenweb-builder'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .woocommerce-loop-category__title' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'show_title' => 'block',
                ],
            ]
        );

        $this->add_responsive_control(
            'title_spacing',
            [
                'label'          => __('Spacing', 'tenweb-builder'),
                'type'           => Controls_Manager::SLIDER,
                'selectors'      => [
                    '{{WRAPPER}} .woocommerce-loop-category__title' => 'padding-bottom: {{SIZE}}px;',
                ],
                'condition' => [
                    'show_button' => 'yes',
                    'show_title' => 'block',
                ],
            ]
        );

        $this->button_style_controls();
        $this->categories_count_style_controls();

        $this->end_controls_section();

        $this->inject_slider();
	}

  protected function inject_slider() {
	  Widget_Slider::init_slider_option($this, [
	    'at' => 'after',
	    'of' => 'section_layout',
    ], '');

	  Widget_Slider::add_slider_controls($this, [
      'type' => 'section',
	    'at' => 'end',
	    'of' => 'section_layout',
    ]);

	  Widget_Slider::add_slider_style_controls($this, [
      'type' => 'section',
	    'at' => 'end',
	    'of' => 'categories_content_style',
    ]);
  }

    private function categories_count_style_controls()
    {
        $this->add_control(
            'heading_count_style',
            [
                'label' => __('Count', 'tenweb-builder'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'categories_count!' => 'none',
                    'show_title' => 'block',
                ],
            ]
        );

        $this->add_control(
            'count_color',
            [
                'label' => __('Color', 'tenweb-builder'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .woocommerce-loop-category__title .count' => 'color: {{VALUE}}',
                ],
                'condition' => [
                    'categories_count!' => 'none',
                    'show_title' => 'block',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'count_typography',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_TEXT,
                ],
                'selector' => '{{WRAPPER}} .woocommerce-loop-category__title .count',
                'condition' => [
                    'categories_count!' => 'none',
                    'show_title' => 'block',
                ],
            ]
        );
    }

    private function button_style_controls() {
        $start = is_rtl() ? 'right' : 'left';
        $end = is_rtl() ? 'left' : 'right';

        $this->add_control(
            'heading_button_style',
            [
                'label'     => __( 'Button', 'tenweb-builder' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'show_button' => 'yes',
                ],
            ]
        );

        $this->add_responsive_control(
            'content_align',
            [
                'label' => esc_html__( 'Alignment', 'elementor' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left'    => [
                        'title' => esc_html__( 'Start', 'elementor' ),
                        'icon' => "eicon-text-align-{$start}",
                    ],
                    'center' => [
                        'title' => esc_html__( 'Center', 'elementor' ),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__( 'End', 'elementor' ),
                        'icon' => "eicon-text-align-{$end}",
                    ],
                ],
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .twbb-woocommerce_category_content_container .elementor-button-wrapper' => 'text-align: {{VALUE}};',
                ],
                'condition' => [
                    'show_button' => 'yes',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'typography',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_ACCENT,
                ],
                'selector' => '{{WRAPPER}} .twbb-woocommerce_category_content_container .elementor-button',
                'condition' => [
                    'show_button' => 'yes',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'text_shadow',
                'selector' => '{{WRAPPER}} .twbb-woocommerce_category_content_container .elementor-button',
                'condition' => [
                    'show_button' => 'yes',
                ],
            ]
        );

        $this->start_controls_tabs( 'tabs_button_style', [
            'condition' => [
                'show_button' => 'yes',
            ],
        ] );

        $this->start_controls_tab(
            'tab_button_normal',
            [
                'label' => esc_html__( 'Normal', 'elementor' ),
                'condition' => [
                    'show_button' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'button_text_color',
            [
                'label' => esc_html__( 'Text Color', 'elementor' ),
                'type' => Controls_Manager::COLOR,
                'global' => [
                    'default' => 'globals/colors?id=twbb_button_inv',
                ],
                'selectors' => [
                    '{{WRAPPER}} .twbb-woocommerce_category_content_container .elementor-button' => 'fill: {{VALUE}}; color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'background',
                'types' => [ 'classic', 'gradient' ],
                'exclude' => [ 'image' ], //phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude
                'selector' => '{{WRAPPER}} .twbb-woocommerce_category_content_container .elementor-button',
                'fields_options' => [
                    'background' => [
                        'default' => 'classic',
                    ],
                    'color' => [
                        'global' => [
                            'default' => 'globals/colors?id=twbb_bg_inv',
                        ],
                    ],
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_button_hover',
            [
                'label' => esc_html__( 'Hover', 'elementor' ),
                'condition' => [
                    'show_button' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'hover_color',
            [
                'label' => esc_html__( 'Text Color', 'elementor' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .twbb-woocommerce_category_content_container .elementor-button:hover, {{WRAPPER}} .twbb-woocommerce_category_content_container .elementor-button:focus' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .twbb-woocommerce_category_content_container .elementor-button:hover svg, {{WRAPPER}} .twbb-woocommerce_category_content_container .elementor-button:focus svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'button_background_hover',
                'types' => [ 'classic', 'gradient' ],
                'exclude' => [ 'image' ], //phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude
                'selector' => '{{WRAPPER}} .twbb-woocommerce_category_content_container .elementor-button:hover, {{WRAPPER}} .twbb-woocommerce_category_content_container .elementor-button:focus',
                'fields_options' => [
                    'background' => [
                        'default' => 'classic',
                    ],
                ],
            ]
        );

        $this->add_control(
            'button_hover_border_color',
            [
                'label' => esc_html__( 'Border Color', 'elementor' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff00',
                'condition' => [
                    'border_border!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .twbb-woocommerce_category_content_container .elementor-button:hover, {{WRAPPER}} .twbb-woocommerce_category_content_container .elementor-button:focus' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_hover_transition_duration',
            [
                'label' => esc_html__( 'Transition Duration', 'elementor' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 's', 'ms', 'custom' ],
                'default' => [
                    'unit' => 's',
                ],
                'selectors' => [
                    '{{WRAPPER}} .twbb-woocommerce_category_content_container .elementor-button' => 'transition-duration: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'hover_animation',
            [
                'label' => esc_html__( 'Hover Animation', 'elementor' ),
                'type' => Controls_Manager::HOVER_ANIMATION,
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'border',
                'selector' => '{{WRAPPER}} .twbb-woocommerce_category_content_container .elementor-button',
                'fields_options' => [
					'border' => [
						'default' => 'none',
					],
				],
                'condition' => [
                    'show_button' => 'yes',
                ],
            ]
        );

        $this->add_responsive_control(
            'border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'elementor' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                'selectors' => [
                    '{{WRAPPER}} .twbb-woocommerce_category_content_container .elementor-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'show_button' => 'yes',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'button_box_shadow',
                'selector' => '{{WRAPPER}} .twbb-woocommerce_category_content_container .elementor-button',
                'condition' => [
                    'show_button' => 'yes',
                ],
            ]
        );

        $this->add_responsive_control(
            'button_inner_padding',
            [
                'label' => esc_html__( 'Text Padding', 'elementor' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
                'selectors' => [
                    '{{WRAPPER}} .twbb-woocommerce_category_content_container .elementor-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'show_button' => 'yes',
                ],
            ]
        );

    }

	private function get_shortcode() {
		$settings = $this->get_settings();

		$attributes = [
			'number' => $settings['number'],
			'columns' => ('yes' === $settings['slider_view']) ? 1 : $settings['columns'],
			'hide_empty' => ( 'yes' === $settings['hide_empty'] ) ? 1 : 0,
			'orderby' => $settings['orderby'],
			'order' => $settings['order'],
		];

		if ( 'by_id' === $settings['source'] ) {
			$attributes['ids'] = implode( ',', $settings['categories'] );
		} elseif ( 'by_parent' === $settings['source'] ) {
			$attributes['parent'] = $settings['parent'];
		} elseif ( 'current_subcategories' === $settings['source'] ) {
			$attributes['parent'] = get_queried_object_id();
		}

		$this->add_render_attribute( 'shortcode', $attributes );
		$shortcode = sprintf( '[product_categories %s]', $this->get_render_attribute_string( 'shortcode' ) );

		return $shortcode;
	}

	public function render() {
        $settings = $this->get_settings();
        //if image height is enabled we call actions for background image view
        if( isset($settings['regulate_image_height']) && $settings['regulate_image_height'] === 'yes' ) {
            remove_action('woocommerce_before_subcategory_title', 'woocommerce_subcategory_thumbnail');
            add_action('woocommerce_before_subcategory', array($this, 'woocommerce_before_image_tag'), 11);
            add_action('woocommerce_shop_loop_subcategory_title', array($this, 'woocommerce_before_title'), 9);
        } else {
            add_action('woocommerce_before_subcategory', array($this, 'woocommerce_before_image_tag_version0'), 11);
            add_action( 'woocommerce_shop_loop_subcategory_title', array($this, 'woocommerce_after_image_tag_version0'), 9 );
            add_action('woocommerce_shop_loop_subcategory_title', array($this, 'woocommerce_before_title_version0'), 9);
        }

        add_action('woocommerce_after_subcategory', array($this, 'woocommerce_after_title'), 11);

        if ('yes' === $settings['slider_view']) {
          add_filter('woocommerce_product_loop_start', array($this, 'slider_wrapper_start'));
          add_filter('woocommerce_product_loop_end', array($this, 'slider_wrapper_end'));
          add_filter('product_cat_class', array($this, 'slider_item_class'));
        }

	    	$content = do_shortcode( $this->get_shortcode() );

        if ( strpos( $content, 'product-category' ) !== false ) {
            echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        } elseif ( \Elementor\Plugin::instance()->editor->is_edit_mode() ||
            (!empty($_GET['twbb_template_preview']) && !empty($_GET['twbb_template_preview_from']) && !empty($_GET['twbb_template_preview_nonce'])) //phpcs:ignore WordPress.Security.NonceVerification.Recommended
        ) {
            $args = [
                'mobile_desc' => 'This is a preview of what your future product categories will look like. You haven’t created any categories yet. This view will not be visible on your live website.',
                'desktop_desc' => 'This is a preview of what your future product categories will look like. You haven’t created any categories yet.<br>This view will not be visible on your live website.',
                'el_count' => 2,
            ];
            \Tenweb_Builder\Modules\Utils::handleArchiveNoContentRender($args);
        } elseif( !\Elementor\Plugin::instance()->editor->is_edit_mode() ) {
            $this->handle_no_posts_found_preview();
        }

        $this->removeAddedActions( $settings );
	}

  public function slider_wrapper_start($woocommerce_product_loop_start) {
    $settings = $this->get_settings();
    $settings['space_between'] = $settings['column_gap'];
    $settings['space_between_tablet'] = $settings['column_gap'];
    $settings['space_between_mobile'] = $settings['column_gap'];
    $items_count = $settings['number'];
    $this->add_render_attribute('tenweb-slider-view-type', ['class' => 'products']);
    $this->add_render_attribute( 'tenweb-slider-view-type', Widget_Slider::get_slider_attributes($settings, $items_count, 'columns') );
    $woocommerce_product_loop_start = preg_replace_callback('/<(\w+)([^>]*)>/', function ($matches) {
      $tag = $matches[1];
      return '<' . $tag . ' ' . $this->get_render_attribute_string('tenweb-slider-view-type') . '>';
    }, $woocommerce_product_loop_start);

    ob_start();
    Widget_Slider::slider_wrapper_start();

    return $woocommerce_product_loop_start . ob_get_clean();
  }

  public function slider_wrapper_end($woocommerce_product_loop_end) {
    $settings = $this->get_settings();
    $items_count = $settings['number'];
    $arrows_icon = isset($settings['arrows_icon']) ? $settings['arrows_icon'] : 'arrow2';
    ob_start();
    Widget_Slider::slider_wrapper_end(['items_count' => $items_count, 'arrows_icon' => $arrows_icon]);
    return ob_get_clean() . $woocommerce_product_loop_end;
  }

  public function slider_item_class($classes) {
    $classes[] = Widget_Slider::ITEM_CLASS;
    return $classes;
  }

    public function woocommerce_before_image_tag_version0() {
        //open parent div for image tag
        echo '<div class="twbb-category-image-wrapper">';
    }

    public function woocommerce_after_image_tag_version0() {
        //close parent div for image tag
        echo '</div>';
    }

    public function woocommerce_before_image_tag($category) {
        $thumbnail_id         = get_term_meta( $category->term_id, 'thumbnail_id', true );

        if ( $thumbnail_id ) {
            $image        = wp_get_attachment_image_src( $thumbnail_id, 'full');
            $image        = $image[0];
        } else {
            $image        = wc_placeholder_img_src();
        }

        $this->add_render_attribute( 'category_image', 'class', 'twbb-category-image-wrapper' );

        if ( $image ) {
            $this->add_render_attribute( 'category_image_bg', 'class', 'twbb-category-image' );
            $this->add_render_attribute( 'category_image_bg', 'style', 'background-image: url(' . $image . ');' );
        }
        //open parent div for image tag
        ?><div <?php $this->print_render_attribute_string( 'category_image' ); ?>>
        <div <?php $this->print_render_attribute_string( 'category_image_bg' ); ?>><?php
    }

    public function woocommerce_before_title_version0() {
        ?> <div class="twbb-woocommerce_category_content_container"> <?php
    }

    public function woocommerce_before_title() {
        ?>
        </div></div>
        <div class="twbb-woocommerce_category_content_container"> <?php
    }

    public function woocommerce_after_title($category) {
        $this->render_button($category); ?>
        </div>
        <?php
    }

	public function render_plain_content() {
        // PHPCS - Already escaped in get_shortcode
        echo $this->get_shortcode(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

    private function removeAddedActions( $settings ) {
        if( isset($settings['regulate_image_height']) && $settings['regulate_image_height'] === 'yes' ) {
            remove_action('woocommerce_before_subcategory', array($this, 'woocommerce_before_image_tag'), 11);
            remove_action('woocommerce_shop_loop_subcategory_title', array($this, 'woocommerce_before_title'), 9);
        } else {
            remove_action('woocommerce_before_subcategory', array($this, 'woocommerce_before_image_tag_version0'), 11);
            remove_action( 'woocommerce_shop_loop_subcategory_title', array($this, 'woocommerce_after_image_tag_version0'), 9 );
            remove_action('woocommerce_shop_loop_subcategory_title', array($this, 'woocommerce_before_title_version0'), 9);
        }

        remove_action('woocommerce_after_subcategory', array($this, 'woocommerce_after_title'), 11);

        remove_filter('woocommerce_product_loop_start', array($this, 'slider_wrapper_start'));
        remove_filter('woocommerce_product_loop_end', array($this, 'slider_wrapper_end'));
        remove_filter('product_cat_class', array($this, 'slider_item_class'));
    }

    protected function render_text() {
        $settings = $this->get_settings_for_display();

        $migrated = isset( $settings['__fa4_migrated']['button_selected_icon'] );
        $is_new = empty( $settings['icon'] ) && Icons_Manager::is_migration_allowed();

        $this->add_render_attribute( [
            'content-wrapper' => [
                'class' => 'elementor-button-content-wrapper',
            ],
            'icon' => [
                'class' => 'elementor-button-icon',
            ],
            'button_text' => [
                'class' => 'elementor-button-text',
            ],
        ] );

        ?>
        <span <?php $this->print_render_attribute_string( 'content-wrapper' ); ?>>
			<?php if ( ! empty( $settings['icon'] ) || ! empty( $settings['button_selected_icon']['value'] ) ) : ?>
                <span <?php $this->print_render_attribute_string( 'icon' ); ?>>
				<?php if ( $is_new || $migrated ) :
                    Icons_Manager::render_icon( $settings['button_selected_icon'], [ 'aria-hidden' => 'true' ] );
                else : ?>
                    <i class="<?php echo esc_attr( $settings['icon'] ); ?>" aria-hidden="true"></i>
                <?php endif; ?>
			</span>
            <?php endif; ?>
            <?php if ( ! empty( $settings['button_text'] ) ) : ?>
                <span <?php $this->print_render_attribute_string( 'button_text' ); ?>><?php $this->print_unescaped_setting( 'button_text' ); ?></span>
            <?php endif; ?>
		</span>
        <?php
    }

    protected function render_button($category) {
        $settings = $this->get_settings_for_display();
        $category_link = [];
        $category_link['url'] = get_term_link( $category, 'product_cat' );
        if ( empty( $settings['button_text'] ) && empty( $settings['button_selected_icon']['value'] ) ) {
            return;
        }

        $this->add_render_attribute( 'wrapper', 'class', 'elementor-button-wrapper' );

        $this->add_render_attribute( 'button', 'class', 'elementor-button' );

        if ( ! empty( $category_link['url'] ) ) {
            $this->add_render_attribute( 'button', 'class', 'elementor-button-link' );
        } else {
            $this->add_render_attribute( 'button', 'role', 'button' );
        }

        if ( ! empty( $settings['hover_animation'] ) ) {
            $this->add_render_attribute( 'button', 'class', 'elementor-animation-' . $settings['hover_animation'] );
        }
        ?>
        <div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
            <?php if ( ! empty( $category_link['url'] ) ) { ?>
                <a <?php $this->print_render_attribute_string( 'button' ); ?> href="<?php echo esc_url($category_link['url']);?>">
                    <?php $this->render_text(); ?>
                </a>
            <?php } else { ?>
                <a <?php $this->print_render_attribute_string( 'button' ); ?> href="#">
                <?php $this->render_text(); ?>
                </a>
            <?php } ?>
        </div>
        <?php
    }

    protected function handle_no_posts_found_preview() {
                $args = [
            'title' => 'No Product Categories Found',
            'desc' => 'There are currently no product categories to display.',
        ];
        \Tenweb_Builder\Modules\Utils::handleArchiveNoContentPreviewRender($args);
    }
}
\Elementor\Plugin::instance()->widgets_manager->register( new Categories() );
