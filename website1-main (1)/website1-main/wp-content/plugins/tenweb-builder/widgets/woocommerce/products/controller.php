<?php

namespace Tenweb_Builder\Widgets\Woocommerce\Products;
include_once(TWBB_DIR . '/widgets/woocommerce/products/widgets/products-base.php');
include_once(TWBB_DIR . '/widgets/woocommerce/products/classes/products-renderer.php');
include_once(TWBB_DIR . '/widgets/woocommerce/products/classes/current-query-renderer.php');
include_once(TWBB_DIR . '/widgets/woocommerce/products/traits/products-trait.php');
include_once(TWBB_DIR . '/widgets/woocommerce/products/skins/skin-base.php');
include_once(TWBB_DIR . '/widgets/woocommerce/products/skins/skin-classic.php');
include_once(TWBB_DIR . '/widgets/woocommerce/products/skins/skin-modern.php');


use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use Tenweb_Builder\Classes\Woocommerce\Woocommerce;
use Tenweb_Builder\Widgets\Woocommerce\Products\Widgets\Products_Base;
use Tenweb_Builder\Widgets\Woocommerce\Products\Classes\Products_Renderer;
use Tenweb_Builder\Widgets\Woocommerce\Products\Classes\Current_Query_Renderer;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Typography;
use Tenweb_Builder\Widgets\Woocommerce\Products\Traits\Products_Trait;
use Tenweb_Builder\Widget_Slider;
use WC_Product_Variable;

if ( !defined('ABSPATH') ) {
    exit; // Exit if accessed directly
}

class Products extends Products_Base {

	use Products_Trait;

    public $twbb_current_skin = '';

    public function get_name() {
        return 'twbb_woocommerce-products';
    }

    public function get_title() {
        return esc_html__('Products', 'tenweb-builder');
    }

    public function get_icon() {
        return 'twbb-products twbb-widget-icon';
    }

	public function get_keywords() {
		return [ 'woocommerce', 'shop', 'store', 'product', 'archive', 'upsells', 'cross-sells', 'cross sells', 'related' ];
	}
    public function get_categories() {
        return [ Woocommerce::WOOCOMMERCE_GROUP ];
    }

    protected function register_skins() {
        $this->add_skin( new Skins\Skin_Classic( $this ) );
        $this->add_skin( new Skins\Skin_Modern( $this ) );
    }

	/**
	 * @throws \Exception
	 */
    protected function register_query_section() {
		$this->start_controls_section(
			'section_query',
			[
				'label' => esc_html__( 'Select Products', 'tenweb-builder' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_query_controls( Products_Renderer::QUERY_CONTROL_NAME );

		$this->end_controls_section();
	}

    protected function register_badge_section() {
        $this->start_controls_section(
            'section_badge',
            [
                'label' => esc_html__( 'Badge', 'tenweb-builder' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control('show_onsale_flash', [
            'label' => __('Show Badge', 'tenweb-builder'),
            'type' => Controls_Manager::SWITCHER,
            'label_off' => __('Hide', 'tenweb-builder'),
            'label_on' => __('Show', 'tenweb-builder'),
            'separator' => 'before',
            'default' => 'yes',
            'return_value' => 'yes',
            'render_type' => 'template',
        ]);

        $this->add_control('badge_type', [
            'label' => __('Badge Type', 'tenweb-builder'),
            'type' => Controls_Manager::SELECT,
            'default' => 'sale',
            'render_type' => 'template',
            'options' => [
                'sale' => __('Sale', 'tenweb-builder'),
                'custom' => __('Custom Text', 'tenweb-builder'),
            ],
            'condition' => [
                'show_onsale_flash' => 'yes',
            ],
            'prefix_class' => 'twbb-badge-type-',
        ]);

        $this->add_control('badge_type_custom', [
            'label' => __('Custom Text', 'tenweb-builder'),
            'type' => Controls_Manager::TEXT,
            'default' => __('Sale', 'tenweb-builder'),
            'render_type' => 'template',
            'condition' => [
                'badge_type' => 'custom',
                'show_onsale_flash' => 'yes',
            ],
        ]);

        $this->end_controls_section();
    }

	public function register_controls() {
		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'General', 'tenweb-builder' ),
			]
		);

        $this->add_responsive_control('columns', [
            'label' => __('Columns', 'tenweb-builder'),
            'type' => Controls_Manager::NUMBER,
            'prefix_class' => 'elementor-grid%s-',
            'min' => 1,
            'max' => 12,
            'default' => 4,
            'tablet_default' => '2',
            'mobile_default' => '1',
            'render_type' => 'template',
            'required' => TRUE,
            'device_args' => [
                Controls_Stack::RESPONSIVE_TABLET => [
                    'required' => FALSE,
                ],
                Controls_Stack::RESPONSIVE_MOBILE => [
                    'required' => FALSE,
                ],
            ],
            'min_affected_device' => [
                Controls_Stack::RESPONSIVE_DESKTOP => Controls_Stack::RESPONSIVE_TABLET,
                Controls_Stack::RESPONSIVE_TABLET => Controls_Stack::RESPONSIVE_TABLET,
            ],
            'condition' => [
                '_skin' => '',
            ],
        ]);

		$this->add_control(
			'rows',
			[
				'label' => esc_html__( 'Rows', 'tenweb-builder' ),
				'type' => Controls_Manager::NUMBER,
				'default' => Products_Renderer::DEFAULT_COLUMNS_AND_ROWS,
				'render_type' => 'template',
				'range' => [
					'px' => [
						'max' => 20,
					],
				],
                'condition' => [
                    '_skin' => '',
                ],
			]
		);

		$this->add_control(
			'paginate',
			[
				'label' => esc_html__( 'Pagination', 'tenweb-builder' ),
				'type' => Controls_Manager::SWITCHER,
                'label_on' => __('On', 'tenweb-builder'),
                'label_off' => __('Off', 'tenweb-builder'),
                'return_value' => 'yes',
				'default' => '',
				'condition' => [
					Products_Renderer::QUERY_CONTROL_NAME . '_post_type!' => [
						'related_products',
						'upsells',
						'cross_sells',
					],
				],
			]
		);

        //10Web Customization for ajax pagination
        $this->add_control('ajax_paginate', [
            'label' => __('Ajax Pagination', 'tenweb-builder'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => __('On', 'tenweb-builder'),
            'label_off' => __('Off', 'tenweb-builder'),
            'return_value' => 'yes',
            'default' => '',
            'condition' => [
                'paginate' => 'yes',
            ],
        ]);

		$this->add_control(
			'allow_order',
			[
				'label' => esc_html__( 'Allow Order', 'tenweb-builder' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'condition' => [
					'paginate' => 'yes',
				],
			]
		);

		$this->add_control(
			'wc_notice_frontpage',
			[
				'type' => Controls_Manager::RAW_HTML,
				'raw' => esc_html__( 'Ordering is not available if this widget is placed in your front page. Visible on frontend only.', 'tenweb-builder' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition' => [
					'paginate' => 'yes',
					'allow_order' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_result_count',
			[
				'label' => esc_html__( 'Show Result Count', 'tenweb-builder' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'condition' => [
					'paginate' => 'yes',
				],
			]
		);
        $this->add_control('hide_products_images', [
            'label' => __('Hide Products Images', 'tenweb-builder'),
            'type' => Controls_Manager::SWITCHER,
            'separator' => 'before',
            'default' => '',
            'selectors' => [
                '{{WRAPPER}}.elementor-wc-products.twbb-product-images-default .twbb-image-container,
                {{WRAPPER}}.elementor-wc-products.twbb-product-images-default .woocommerce ul.products li.product a img.attachment-woocommerce_thumbnail,
{{WRAPPER}}.elementor-wc-products.twbb-product-images-default .woocommerce ul.products li.product a img.woocommerce-placeholder' => 'display: none',
            ],
            'condition' => [
                '_skin' => '',
            ],
        ]);

        $this->add_control('product_images', [
            'label' => __('Product Images', 'tenweb-builder'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => __( 'Show', 'tenweb-builder' ),
            'label_off' => __( 'Hide', 'tenweb-builder' ),
            'separator' => 'before',
            'default' => 'default',
            'prefix_class' => 'twbb-product-images-',
            'condition' => [
                '_skin' => '',
            ],
        ]);
        $this->add_control('variation_images', [
            'label' => __('Variation Images', 'tenweb-builder'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => __( 'Show', 'tenweb-builder' ),
            'label_off' => __( 'Hide', 'tenweb-builder' ),
            'default' => 'yes',
            'selectors' => [
                '{{WRAPPER}}.elementor-wc-products ul.products li.product:hover .twbb-woocommerce-products-variations' => 'display: flex',
            ],
            'condition' => [
                'product_images' => 'yes',
                '_skin' => '',
            ],
        ]);
        $this->add_control('hide_products_title', [
            'label' => __('Hide Products Titles', 'tenweb-builder'),
            'type' => Controls_Manager::SWITCHER,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}}.twbb-product-title-default ul.products li.product .woocommerce-loop-product__title' => 'display: none',
            ],
            'condition' => [
                '_skin' => '',
            ],
        ]);
        $this->add_control('product_title', [
            'label' => __('Product Title', 'tenweb-builder'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => __( 'Show', 'tenweb-builder' ),
            'label_off' => __( 'Hide', 'tenweb-builder' ),
            'render_type' => 'template',
            'default' => 'default',
            'prefix_class' => 'twbb-product-title-',
            'condition' => [
                '_skin' => '',
            ],
        ]);
        $this->add_control('hide_products_description', [
            'label' => __('Hide Products Descriptions', 'tenweb-builder'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'selectors' => [
                '{{WRAPPER}}.elementor-wc-products.twbb-product-description-default ul.products li.product .twbb_woocommerce-loop-product__desc' => 'display: none',
            ],
            'condition' => [
                '_skin' => '',
            ],
        ]);
        $this->add_control('product_description', [
            'label' => __('Product Description', 'tenweb-builder'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => __( 'Show', 'tenweb-builder' ),
            'label_off' => __( 'Hide', 'tenweb-builder' ),
            'separator' => 'before',
            'default' => 'default',
            'render_type' => 'template',
            'prefix_class' => 'twbb-product-description-',
            'condition' => [
                '_skin' => '',
            ],
        ]);
        $this->add_control(
            'description_length',
            [
                'label' => __('Description Length', 'tenweb-builder'),
                'type' => Controls_Manager::NUMBER,
                /** This filter is documented in wp-includes/formatting.php */
                'default' => apply_filters('excerpt_length', 25),
                'condition' => [
                    'product_description' => 'yes',
                    '_skin' => '',
                ],
            ]
        );

		//10web customization
        $this->add_control('image_gallery', [
            'label' => __('Image Gallery', 'tenweb-builder'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => __('Show', 'tenweb-builder'),
            'label_off' => __('Hide', 'tenweb-builder'),
            'return_value' => 'yes',
            'default' => 'yes',
            'render_type' => 'template',
            'condition' => [
                'product_images' => 'yes',
                '_skin' => '',
            ],
        ]);

        $this->add_control('show_second_image', [
            'label' => __('Show only one image', 'tenweb-builder'),
            'type' => Controls_Manager::SWITCHER,
            'label_off' => __('Hide', 'tenweb-builder'),
            'label_on' => __('Show', 'tenweb-builder'),
            'return_value' => 'yes',
            'default' => '',
            'condition' => [
                '_skin' => '',
                'image_gallery!' => ''
            ],
        ]);


        $this->end_controls_section();

        $this->start_controls_section(
            'section_buttons_quantity',
            [
                'label' => esc_html__( 'Buttons & quantity', 'tenweb-builder' ),
            ]
        );

        $this->add_control('hide_products_buttons', [
            'label' => __('Hide Poducts Buttons', 'tenweb-builder'),
            'type' => Controls_Manager::SWITCHER,
            'default' => '',
            'condition' => [
                '_skin' => '',
            ],
        ]);

        $this->add_control('product_buttons', [
			'label' => __('Product buttons', 'tenweb-builder'),
			'type' => Controls_Manager::SWITCHER,
            'label_on' => __( 'Show', 'tenweb-builder' ),
            'label_off' => __( 'Hide', 'tenweb-builder' ),
            'default' => 'default',
            'render_type' => 'template',
            'condition' => [
                '_skin' => '',
            ],
		]);

        $this->add_control('product_add_to_cart_custom_text', [
            'label' => __('Add to cart text', 'tenweb-builder'),
            'type' => Controls_Manager::TEXT,
            'default' => __('Add to cart', 'tenweb-builder'),
            'render_type' => 'template',
            'conditions' => [
                'relation' => 'or',
                'terms' => [
                    [
                        'relation' => 'and',
                        'terms' => [
                            [
                                'name' => 'modern_skin_product_buttons',
                                'operator' => '!==',
                                'value' => '',
                            ],
                            [
                                'name' => '_skin',
                                'operator' => '===',
                                'value' => 'modern',
                            ],
                        ],
                    ],
                    [
                        'relation' => 'and',
                        'terms' => [
                            [
                                'name' => 'product_buttons',
                                'operator' => '!==',
                                'value' => '',
                            ],
                            [
                                'name' => '_skin',
                                'operator' => '!==',
                                'value' => 'modern',
                            ],
                        ],
                    ]
                ]
            ],
        ]);

        $this->add_control('product_select_options_custom_text', [
            'label' => __('Select Options text', 'tenweb-builder'),
            'description' => __('If available Variation button text.', 'tenweb-builder'),
            'type' => Controls_Manager::TEXT,
            'default' => __('Select options', 'tenweb-builder'),
            'render_type' => 'template',
            'conditions' => [
                'relation' => 'or',
                'terms' => [
                    [
                        'relation' => 'and',
                        'terms' => [
                            [
                                'name' => 'modern_skin_product_buttons',
                                'operator' => '!==',
                                'value' => '',
                            ],
                            [
                                'name' => '_skin',
                                'operator' => '===',
                                'value' => 'modern',
                            ],
                        ],
                    ],
                    [
                        'relation' => 'and',
                        'terms' => [
                            [
                                'name' => 'product_buttons',
                                'operator' => '!==',
                                'value' => '',
                            ],
                            [
                                'name' => '_skin',
                                'operator' => '!==',
                                'value' => 'modern',
                            ],
                        ],
                    ]
                ]
            ],
        ]);

        $this->add_control('hide_product_quantity', [
            'label' => __('Hide Product Quantity', 'tenweb-builder'),
            'type' => Controls_Manager::SWITCHER,
            'default' => '',
            'condition' => [
                'product_buttons!' => '',
                '_skin' => '',
            ],
        ]);

        $this->add_control('product_quantity', [
            'label' => __('Product quantity', 'tenweb-builder'),
            'description' => __('If the product has variations quantity will be hidden by default.', 'tenweb-builder'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => __( 'Show', 'tenweb-builder' ),
            'label_off' => __( 'Hide', 'tenweb-builder' ),
            'default' => 'default',
            'condition' => [
                'product_buttons!' => '',
                '_skin' => '',
            ],
        ]);

        $this->add_responsive_control(
            'quantity_position',
            [
                'label' => esc_html__( 'Quantity position', 'tenweb-builder' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'row' => [
                        'title' => esc_html__( 'Left', 'tenweb-builder' ),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'column' => [
                        'title' => esc_html__( 'Top', 'tenweb-builder' ),
                        'icon' => 'eicon-v-align-top',
                    ],
                    'row-reverse' => [
                        'title' => esc_html__( 'Right', 'tenweb-builder' ),
                        'icon' => 'eicon-h-align-right',
                    ],
                    'column-reverse' => [
                        'title' => esc_html__( 'Bottom', 'tenweb-builder' ),
                        'icon' => 'eicon-v-align-bottom',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .twbb-product-loop-buttons' => 'flex-direction:{{VALUE}};',
                ],
                // I don't like this solution too
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'relation' => 'and',
                            'terms' => [
                                [
                                    'name' => 'product_quantity',
                                    'operator' => '!==',
                                    'value' => '',
                                ],
                                [
                                    'name' => '_skin',
                                    'operator' => '===',
                                    'value' => '',
                                ],
                                [
                                    'name' => 'product_buttons',
                                    'operator' => '!==',
                                    'value' => '',
                                ],
                            ],
                        ],
                        [
                            'relation' => 'and',
                            'terms' => [
                                [
                                    'name' => 'modern_skin_product_quantity',
                                    'operator' => '===',
                                    'value' => 'yes',
                                ],
                                [
                                    'name' => '_skin',
                                    'operator' => '===',
                                    'value' => 'modern',
                                ],
                                [
                                    'name' => 'modern_skin_product_buttons',
                                    'operator' => '!==',
                                    'value' => '',
                                ],
                            ],
                        ],
                        [
                        'relation' => 'and',
                        'terms' => [
                            [
                                'name' => 'classic_skin_product_quantity',
                                'operator' => '===',
                                'value' => 'yes',
                            ],
                            [
                                'name' => '_skin',
                                'operator' => '===',
                                'value' => 'classic',
                            ],
                            [
                                'name' => 'classic_skin_product_buttons',
                                'operator' => '!==',
                                'value' => '',
                            ],
                        ],
                    ]
                    ]
                ],
            ]
        );

		//End 10web customization
		$this->end_controls_section();

		$this->register_query_section();

        $this->register_badge_section();

		$this->start_controls_section(
			'section_products_title',
			[
				'label' => esc_html__( 'Title', 'tenweb-builder' ),
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => Products_Renderer::QUERY_CONTROL_NAME . '_post_type',
							'operator' => '===',
							'value' => 'related_products',
						],
						[
							'name' => Products_Renderer::QUERY_CONTROL_NAME . '_post_type',
							'operator' => '===',
							'value' => 'upsells',
						],
						[
							'name' => Products_Renderer::QUERY_CONTROL_NAME . '_post_type',
							'operator' => '===',
							'value' => 'cross_sells',
						],
					],
				],
			]
		);

		$this->add_control(
			'products_title_show',
			[
				'label' => esc_html__( 'Title', 'tenweb-builder' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'tenweb-builder' ),
				'label_off' => esc_html__( 'Hide', 'tenweb-builder' ),
				'default' => '',
				'return_value' => 'show',
				'prefix_class' => 'products-heading-',
			]
		);

		$query_type_strings = [
			'related_products' => esc_html__( 'Related Products', 'tenweb-builder' ),
			'upsells' => esc_html__( 'You may also like...', 'tenweb-builder' ),
			'cross_sells' => esc_html__( 'You may be interested in...', 'tenweb-builder' ),
		];

		foreach ( $query_type_strings as $query_type => $string ) {
			$this->add_control(
				'products_' . $query_type . '_title_text',
				[
					'label' => esc_html__( 'Section Title', 'tenweb-builder' ),
					'type' => Controls_Manager::TEXT,
					'label_block' => true,
					'placeholder' => $string,
					'default' => $string,
					'dynamic' => [
						'active' => true,
					],
					'condition' => [
						'products_title_show!' => '',
						Products_Renderer::QUERY_CONTROL_NAME . '_post_type' => $query_type,
					],
				]
			);
		}

		$this->add_responsive_control(
			'products_title_alignment',
			[
				'label' => esc_html__( 'Alignment', 'tenweb-builder' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'start' => [
						'title' => esc_html__( 'Start', 'tenweb-builder' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'tenweb-builder' ),
						'icon' => 'eicon-text-align-center',
					],
					'end' => [
						'title' => esc_html__( 'End', 'tenweb-builder' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--products-title-alignment: {{VALUE}};',
				],
				'condition' => [
					'products_title_show!' => '',
				],
			]
		);

		$this->end_controls_section();

		parent::register_controls();

		$this->start_injection( [
			'type' => 'section',
			'at' => 'start',
			'of' => 'section_design_box',
            'condition' => [
                '_skin' => '',
            ],
		] );

		$this->start_controls_section(
			'products_title_style',
			[
				'label' => esc_html__( 'Title', 'tenweb-builder' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'products_title_show!' => '',
				],
				'conditions' => [
                    'relation' => 'and',
                    'terms' => [
                        [
                            'name' => '_skin',
                            'operator' => '=',
                            'value' => '',
                        ],
                        [
                            'relation' => 'or',
                            'terms' => [
                                [
                                    'name' => Products_Renderer::QUERY_CONTROL_NAME . '_post_type',
                                    'operator' => '===',
                                    'value' => 'related_products',
                                ],
                                [
                                    'name' => Products_Renderer::QUERY_CONTROL_NAME . '_post_type',
                                    'operator' => '===',
                                    'value' => 'upsells',
                                ],
                                [
                                    'name' => Products_Renderer::QUERY_CONTROL_NAME . '_post_type',
                                    'operator' => '===',
                                    'value' => 'cross_sells',
                                ],
                            ],
                        ]
                    ],
				],
			]
		);

		$this->add_control(
			'products_title_color',
			[
				'label' => esc_html__( 'Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'global' => [
					'default' => Global_Colors::COLOR_PRIMARY,
				],
				'selectors' => [
					'{{WRAPPER}}' => '--products-title-color: {{VALUE}};',
				],
                'condition' => [
                    '_skin' => '',
                ],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'products_title_typography',
				'selector' => '{{WRAPPER}}.products-heading-show .related-products > h2, {{WRAPPER}}.products-heading-show .upsells > h2, {{WRAPPER}}.products-heading-show .cross-sells > h2',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
                'condition' => [
                    '_skin' => '',
                ],
			]
		);

		$this->add_responsive_control(
			'products_title_spacing',
			[
				'label' => esc_html__( 'Spacing', 'tenweb-builder' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [ 'px' => 0 ],
				'selectors' => [
					'{{WRAPPER}}' => '--products-title-spacing: {{SIZE}}{{UNIT}};',
				],
                'condition' => [
                    '_skin' => '',
                ],
			]
		);

		$this->end_controls_section();

		$this->end_injection();

		$this->inject_slider();
	}

	protected function inject_slider() {
		Widget_Slider::init_slider_option($this, [
			'at' => 'after',
			'of' => '_skin',
		], '');

		Widget_Slider::add_slider_controls($this, [
			'type' => 'section',
			'at' => 'end',
			'of' => 'section_content',
		]);

		Widget_Slider::add_slider_style_controls($this, [
			'type' => 'section',
			'at' => 'end',
			'of' => 'section_products_style',
		]);

		$this->update_control('paginate', ['condition' => [
			'slider_view!' => 'yes',
		]]);
	}

	public static function get_shortcode_object( $settings ) {
		if ( 'current_query' === $settings[ Products_Renderer::QUERY_CONTROL_NAME . '_post_type' ] ) {
			return new Current_Query_Renderer( $settings, 'current_query' );
		}
		return new Products_Renderer( $settings, 'products' );
	}
    public function get_description_of_products() {
        $settings = $this->get_settings_for_display();
        $excerpt_length = isset( $settings['description_length'] ) ? $settings['description_length']: 25;
        echo '<p class="twbb_woocommerce-loop-product__desc">' . $this->get_woocommerce_excerpt($excerpt_length) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
    public function get_woocommerce_excerpt($excerpt_length){
        $settings = $this->get_settings_for_display();
        $excerpt = get_the_excerpt();
        $excerpt = strip_shortcodes($excerpt);
        $excerpt = wp_strip_all_tags($excerpt);
        $excerpt = trim($excerpt);
        $excerpt = substr($excerpt, 0, $excerpt_length);
        return $excerpt;
    }

    public function get_script_depends()
    {
        return ['slick-slider'];
    }

    public function get_style_depends()
    {
        return ['slick-slider'];
    }

    //10web customization
    public function product_gallery_slider() {
        global $product;

        // Get Product Gallery Images
        $gallery_image_ids = $product->get_gallery_image_ids();

        // Check if Gallery Images Exist
        if (!empty($gallery_image_ids)) {
            echo '<div class="product-gallery-slider swiper-container">';
            echo '<div class="swiper-wrapper">';

            // Loop Through Gallery Images and Print Each Image
            foreach ($gallery_image_ids as $image_id) {
                $image_url = wp_get_attachment_image_url($image_id, 'woocommerce_thumbnail');
                echo '<div class="swiper-slide">';
                echo '<img src="' . esc_url($image_url) . '" alt="Product Gallery Image" />';
                echo '</div>';
            }

            echo '</div>'; // Close Swiper Wrapper
            echo '<div class="swiper-pagination"></div>';
            echo '<div class="swiper-button-next"></div>';
            echo '<div class="swiper-button-prev"></div>';
            echo '</div>'; // Close Swiper Container
        }
    }

    public function product_second_image() {
        global $product;

        // Get Product Gallery Images
        $gallery_image_ids = $product->get_gallery_image_ids();

        // Check if Gallery Images Exist
        if (!empty($gallery_image_ids)) {
            $gallery_image_id = $gallery_image_ids[0];
            echo '<div class="product-gallery-second-image">';
            // Loop Through Gallery Images and Print Each Image
            $image_url = wp_get_attachment_image_url($gallery_image_id, 'woocommerce_thumbnail');
            echo '<img src="' . esc_url($image_url) . '" alt="Product Gallery Image" />';
            echo '</div>'; // Close Swiper Container
        }
    }

    //10web customization
    public function before_image_open_tag() {
        $settings = $this->get_settings();
        // Retrieve the selected image resolution from the Group Control
        $aspect_ratio = isset($settings[$this->twbb_current_skin.'image_aspect_ratio']) ? $settings[$this->twbb_current_skin.'image_aspect_ratio'] : '1:1';
        // Extract ratio values
        [$width, $height] = explode(':', $aspect_ratio);
        $hover_class = !empty($settings[$this->twbb_current_skin.'image_hover_animation']) ? ' hover-active hover-' . $settings[$this->twbb_current_skin.'image_hover_animation'] : '';
        echo '<div class="twbb-image-container'.esc_attr($hover_class).'" style="aspect-ratio: ' . esc_attr($width . '/' . $height) . ';">';

        $this->render_badge();
        if( empty($settings['slider_view']) && !empty($settings[$this->twbb_current_skin.'image_gallery']) &&
            empty($settings[$this->twbb_current_skin.'show_second_image']) &&
            ($this->twbb_current_skin === 'modern_skin_' ||
            !empty($settings[$this->twbb_current_skin.'product_images'])) ) {
            $this->product_gallery_slider();
        }

        if( !empty($settings[$this->twbb_current_skin.'show_second_image']) && !empty($settings[$this->twbb_current_skin.'image_gallery'])) {
            $this->product_second_image();
        }

    }

    public function get_woocommerce_product_variations() {
        global $product;
        $variations_count_to_show = 4;
        $product_variable = new WC_Product_Variable( $product->get_id() );
        $variations_all = $product_variable->get_available_variations();
        $variations = $variations_all;
        $additional = 0;
        if( count( $variations_all ) === 0 ) {
            return;
        }
        if ( count( $variations_all ) > $variations_count_to_show ) {
            $additional = count( $variations_all ) - $variations_count_to_show;
            $variations = array_slice($variations_all, 0, $variations_count_to_show);
        }
        echo '<div class="twbb-woocommerce-products-variations">';
        foreach ( $variations as $variation ) {
            $the_variation = wc_get_product($variation['variation_id']);
            $variation_attrs = $the_variation->get_variation_attributes();
            $attrs_html = '';
            $attrs_as_params = '';
            foreach ( $variation_attrs as $attr_key => $attr_value ) {
                $attrs_html .= 'data-' . $attr_key . '="' . esc_attr($attr_value) . '"';
                $attrs_as_params .= $attr_key . '=' . $attr_value . '&';
            }
            echo '<div class="variation-image" style="background-image:url(' . esc_url($variation['image']['thumb_src']) . ');" ' . $attrs_html . ' data-attrs_as_params="' . esc_attr($attrs_as_params) .'"></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }
        if( $additional !== 0 ) {
            echo "<span class='twbb-additional-variations'>+".esc_html($additional)."</span></div>";
        } else {
            echo '</div>';
        }
    }


    private function render_badge() {
        $settings = $this->get_settings_for_display();
        if( $settings['badge_type'] === 'custom' ) {
            $text = $settings['badge_type_custom'];
            if( $text === '' ) {
                return;
            }
            echo '<span class="twbb_badge_type_custom twbb_products_badge">' . esc_html__( $text, 'tenweb-builder' ) . '</span>';
        } else if( $settings['badge_type'] === 'sale' ) {
            add_filter( 'woocommerce_sale_flash', function() {
                return '<span class="onsale">' . esc_html__( 'Sale', 'tenweb-builder' ) . '</span>';
            }, 10, 3 );
        }
    }

    //10web customization
    public function after_image_close_tag() {
        $settings = $this->get_settings_for_display();
        echo "</div>";
        if( !empty($settings[$this->twbb_current_skin.'product_images']) && $this->twbb_current_skin !== 'modern_skin_') {
            $this->get_woocommerce_product_variations();
        }

    }

    //10web customization
    public function replace_main_image_template($size = 'woocommerce_thumbnail', $attr = array(), $placeholder = true) {
        global $product;

        $settings = $this->get_settings_for_display();

        if ( ! is_array( $attr ) ) {
            $attr = array();
        }

        if ( ! is_bool( $placeholder ) ) {
            $placeholder = true;
        }

        $custom_size = $size;
        if (isset($settings[$this->twbb_current_skin.'image_resolution_size'])) {
            if ($settings['image_resolution_size'] === 'custom') {

                if (isset($settings[$this->twbb_current_skin.'image_resolution_custom_dimension'])) {

                    $dimensions = $settings[$this->twbb_current_skin.'image_resolution_custom_dimension'];

                    $custom_size = [$dimensions['width'], $dimensions['height']];
                } else {
                    $custom_size = 'medium_large';
                }

            } else {
                $custom_size = $settings[$this->twbb_current_skin.'image_resolution_size'];
            }
        }

        $image_size = apply_filters( 'single_product_archive_thumbnail_size', $custom_size );
        $attr['data-image'] = 'main';
		//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo $product ? $product->get_image( $image_size, $attr, $placeholder ) : '';

    }

    public function render() {
		if ( WC()->session ) {
			wc_print_notices();
		}

		$settings = $this->get_settings_for_display();
        $post_type_setting = $settings[ Products_Renderer::QUERY_CONTROL_NAME . '_post_type' ];

        /* Start 10web customize */
        add_action( 'woocommerce_before_shop_loop_item_title', [$this,'before_image_open_tag'], 9 );

        // Remove the default product thumbnail
        remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);
        // Add your custom product thumbnail function
        add_action('woocommerce_before_shop_loop_item_title', [$this, 'replace_main_image_template'], 10);

        add_action( 'woocommerce_before_shop_loop_item_title', [$this,'after_image_close_tag'], 11 );
        if ( ('yes' === $settings['product_description'] ) ||
            ( 'default' === $settings['product_description'] && !empty( $settings['hide_products_description']) ) ) {
            add_action('woocommerce_after_shop_loop_item', array($this, 'get_description_of_products'), 7);
        }

		if ( (!empty($settings['hide_products_buttons']) && $settings['product_buttons'] === 'default' ) ||
            $settings['product_buttons'] === '' ) {
			add_filter( 'woocommerce_loop_add_to_cart_link', [ $this, 'remove_add_to_cart' ], 10, 1 );
		} else {
            if ( (empty($settings['hide_product_quantity']) && $settings['product_quantity'] === 'default' ) ||
                $settings['product_quantity'] === 'yes' ) {
                add_filter( 'woocommerce_loop_add_to_cart_link', [ $this, 'quantity_add_to_cart' ], 10, 1 );
            }
        }

		if ('yes' === $settings['slider_view']) {
			add_filter('woocommerce_product_loop_start', array($this, 'slider_wrapper_start'));
			add_filter('woocommerce_product_loop_end', array($this, 'slider_wrapper_end'));
			add_filter('post_class', array($this, 'slider_item_class'));
		}
        $this->change_buttons_texts();
		//End 10web customization

		if ( 'related_products' === $post_type_setting ) {
			$content = Woocommerce::get_products_related_content( $settings );
		} elseif ( 'upsells' === $post_type_setting ) {
			$content = Woocommerce::get_upsells_content( $settings );
		} elseif ( 'cross_sells' === $post_type_setting ) {
			$content = Woocommerce::get_cross_sells_content( $settings );
		} else {
			// For Products_Renderer.
			if ( ! isset( $GLOBALS['post'] ) ) {
				$GLOBALS['post'] = null; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			}

            $this->get_products_min_max_prices();
            $this->set_wc_thumbnail_single_image_width();
			$shortcode = static::get_shortcode_object( $settings );
			$content = $shortcode->get_content();
		}

        //TODO think about better solution without count
		if ( $content && strlen( $content ) > 100 ) {
			$content = str_replace( '<ul class="products', '<ul class="products elementor-grid', $content );
            //10Web Customization for ajax pagination
            if( $this->get_settings()['ajax_paginate'] === '' ) {
                echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            } else {
                echo '<div class="twbb_woocommerce-products-ajax-paginate">' . $content . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            }
        } elseif ( \Elementor\Plugin::instance()->editor->is_edit_mode() ||
            (!empty($_GET['twbb_template_preview']) && !empty($_GET['twbb_template_preview_from']) && !empty($_GET['twbb_template_preview_nonce'])) //phpcs:ignore WordPress.Security.NonceVerification.Recommended
        ) {
            $this->handle_no_products();
        } elseif( !\Elementor\Plugin::instance()->editor->is_edit_mode() ) {
            $this->handle_no_posts_found_preview();
        }

        //10web customization
        if ( ('yes' === $settings['product_description'] ) ||
            ( 'default' === $settings['product_description'] && !empty( $settings['hide_products_description']) ) ) {
            remove_action('woocommerce_after_shop_loop_item', array($this, 'get_description_of_products'), 7);
        }
        if ( (!empty($settings['hide_products_buttons']) && $settings['product_buttons'] === 'default' ) ||
            $settings['product_buttons'] === '' ) {
            remove_filter( 'woocommerce_loop_add_to_cart_link', [ $this, 'remove_add_to_cart' ], 10 );
        } else {
            if ( (empty($settings['hide_product_quantity']) && $settings['product_quantity'] === 'default' ) ||
                $settings['product_quantity'] === 'yes' )  {
                remove_filter( 'woocommerce_loop_add_to_cart_link', [ $this, 'quantity_add_to_cart' ], 10 );
            }
        }
		if ('yes' === $settings['slider_view']) {
			remove_filter('woocommerce_product_loop_start', array($this, 'slider_wrapper_start'));
			remove_filter('woocommerce_product_loop_end', array($this, 'slider_wrapper_end'));
			remove_filter('post_class', array($this, 'slider_item_class'));
		}

        remove_action( 'woocommerce_before_shop_loop_item_title', [$this,'before_image_open_tag'], 9 );
        remove_action('woocommerce_before_shop_loop_item_title', [$this, 'replace_main_image_template'], 10);
        remove_action( 'woocommerce_before_shop_loop_item_title', [$this,'after_image_close_tag'], 11 );
        remove_filter( 'woocommerce_product_add_to_cart_text', [$this,'woo_change_buttons_texts'] );

        //End 10web customization
	}

    public function change_buttons_texts() {
        $settings = $this->get_settings_for_display();
        $cart = !empty($settings['product_add_to_cart_custom_text']) ? $settings['product_add_to_cart_custom_text'] : 'Add to cart';
        $select = !empty($settings['product_select_options_custom_text']) ? $settings['product_select_options_custom_text'] : 'Select options';
        update_option('twbb_custom_woocommerce_add_to_cart_text', $cart);
        update_option('twbb_product_select_options_custom_text', $select);
        add_filter( 'woocommerce_product_add_to_cart_text', [$this,'woo_change_buttons_texts'], 10, 2 );
    }

    public function woo_change_buttons_texts($text, $wc_product_simple) {
        global $product;
        $product_variable = new WC_Product_Variable( $product->get_id() );
        $variations_all = $product_variable->get_available_variations();
        if( count($variations_all) > 0 ) {
            $text_from_option = get_option('twbb_product_select_options_custom_text', 'Select options');
        } else {
            $text_from_option = get_option('twbb_custom_woocommerce_add_to_cart_text', 'Add to cart');
        }
        $custom_text = $wc_product_simple->is_purchasable() && $wc_product_simple->is_in_stock() ? __( $text_from_option, 'tenweb-builder' ) : __( 'View details', 'tenweb-builder' );
        return $custom_text;
    }

	public function slider_wrapper_start($woocommerce_product_loop_start) {
		$settings = $this->get_settings();
		$settings['space_between'] = $settings['column_gap'];
		$settings['space_between_tablet'] = $settings['column_gap'];
		$settings['space_between_mobile'] = $settings['column_gap'];
		$items_count = $settings['columns'] * $settings['rows'];
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
		$items_count = $settings['columns'] * $settings['rows'];
        $arrows_icon = isset($settings['arrows_icon']) ? $settings['arrows_icon'] : 'arrow2';
        ob_start();
        Widget_Slider::slider_wrapper_end(['items_count' => $items_count, 'arrows_icon' => $arrows_icon]);
		return ob_get_clean() . $woocommerce_product_loop_end;
	}

	public function slider_item_class($classes) {
		$classes[] = Widget_Slider::ITEM_CLASS;
		return $classes;
	}

	public function set_wc_thumbnail_single_image_width() {
        $settings = $this->get_settings();
        foreach ( [ 'thumbnail_image_width' => 600, 'single_image_width' => 800 ] as $key => $val ) {
            if ( !empty($settings[$key]) ) {
                $separator = '%%';
                $image_width = explode($separator, $settings[$key]);
                $wc_image_width = $val;
                if ( !empty($image_width[1]) ) {
                    $wc_image_width = $image_width[1];
                }
                if ( $settings[$key] === 'custom' && !empty($settings[$key . '_custom']) && !empty($settings[$key . '_custom']) ) {
                    $wc_image_width = $settings[$key . '_custom'];
                }
                update_option( 'twbb_wc_' . $key, intval($wc_image_width) );
            }
        }
    }

    /**
     * Get min and max prices from current products,compare
     * with last values and set actual min and max values
     *
     * @return null
     */
    public function get_products_min_max_prices() {
        $settings = $this->get_settings();
        $is_excludes = in_array('manual_selection', (isset($settings['exclude']) && $settings['exclude'] ? $settings['exclude'] : array()),true);
        $is_excludes_current = in_array('current_post', (isset($settings['exclude']) && $settings['exclude'] ? $settings['exclude'] : array()),true);
        $excluded_products_id = $is_excludes ? (!empty($settings['exclude_ids']) ? $settings['exclude_ids'] : array()) : array();
        if ($is_excludes_current) {
            global $product;
            $current_id = (isset($product) && $product ? $product->get_id() : null); // get current product id if these are single product pages
            array_push($excluded_products_id, $current_id);
        }

        if ( !empty($settings['orderby']) && $settings['orderby'] === 'rand') {
            $args = array(
                'post_type' => 'product',
                'posts_per_page' => '-1',
            );
        } else {
            $shortcode_products_count = $settings['paginate'] ? -1 : $settings['rows']*$settings['columns'];
            $args = array(
                'post_type' => 'product',
                'post__not_in' => $excluded_products_id, // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn
                'posts_per_page' => $shortcode_products_count,
                'order' => $settings['order'] ?? ''
            );
        }
        $loop = new \WP_Query($args);
        $available_products_price = array();

        foreach ( $loop->posts as $post ) {
            $product = wc_get_product( $post->ID );
            array_push($available_products_price, $product->get_price());
        }
        if ( count($available_products_price)) {
            if (!isset(self::$products_min_max_prices['allMinPrice'])) {
                self::$products_min_max_prices['allMinPrice'] = min($available_products_price);
            } else if (isset(self::$products_min_max_prices['allMinPrice']) && self::$products_min_max_prices['allMinPrice'] > min($available_products_price)) {
                self::$products_min_max_prices['allMinPrice'] = min($available_products_price);
            }
            if (!isset(self::$products_min_max_prices['allMaxPrice'])) {
                self::$products_min_max_prices['allMaxPrice'] = max($available_products_price);
            } else if (isset(self::$products_min_max_prices['allMaxPrice']) && self::$products_min_max_prices['allMaxPrice'] < max($available_products_price)) {
                self::$products_min_max_prices['allMaxPrice'] = max($available_products_price);
            }
        }
        if (!isset($_POST['min_price']) && !isset(self::$products_min_max_prices['currentMinPrice'])) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
            self::$products_min_max_prices['currentMinPrice'] = (int)self::$products_min_max_prices['allMinPrice'];
        }
        if ( isset($_POST['min_price'])) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
            if ((int)self::$products_min_max_prices['allMinPrice'] <= (int)$_POST['min_price']) // phpcs:ignore WordPress.Security.NonceVerification.Missing
                self::$products_min_max_prices['currentMinPrice'] = (int)$_POST['min_price']; // phpcs:ignore WordPress.Security.NonceVerification.Missing
        }
        if (!isset($_POST['max_price']) && !isset(self::$products_min_max_prices['currentMaxPrice'])) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
            self::$products_min_max_prices['currentMaxPrice'] = (int)self::$products_min_max_prices['allMaxPrice'];
        }
        if ( isset($_POST['max_price'])) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
            if ((int)self::$products_min_max_prices["allMaxPrice"] >= (int)$_POST['max_price']) // phpcs:ignore WordPress.Security.NonceVerification.Missing
                self::$products_min_max_prices['currentMaxPrice'] = (int)$_POST['max_price']; // phpcs:ignore WordPress.Security.NonceVerification.Missing
        }
        return NULL;
    }

    public static $products_min_max_prices = array(
        'currentMinPrice' => NULL,
        'allMinPrice' => NULL,
        'currentMaxPrice' => NULL,
        'allMaxPrice' => NULL
    );

	public function render_plain_content() {}

    protected function handle_no_posts_found_preview() {
        $args = [
            'title' => 'No Products Found',
            'desc' => 'There are currently no products to display.',
        ];
        \Tenweb_Builder\Modules\Utils::handleArchiveNoContentPreviewRender($args);
    }

    private function handle_no_products() {
        $args = [
            'mobile_desc' => 'This is a preview of what your future product list will look like. You haven’t created any products yet. This view will not be visible on your live website.',
            'desktop_desc' => 'This is a preview of what your future product list will look like. You haven’t created any products yet.<br> This view will not be visible on your live website.',
            'el_count' => 3,
        ];
        \Tenweb_Builder\Modules\Utils::handleArchiveNoContentRender($args);
    }

}

\Elementor\Plugin::instance()->widgets_manager->register(new Products());
