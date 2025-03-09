<?php
namespace Tenweb_Builder\Widgets\Woocommerce\Products;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Tenweb_Builder\Widgets\Woocommerce\Products\Widgets\Products_Base;
use Tenweb_Builder\Classes\Woocommerce\Woocommerce;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Product_Upsell extends Products_Base {

	public function get_name() {
		return 'twbb_woocommerce-product-upsell';
	}

	public function get_title() {
		return '';
	}

	public function get_icon() {
		return 'twbb-upsellls twbb-widget-icon';
	}

	public function get_categories() {
        return array('tenweb-depreciated');
	}

	protected function register_controls() {

    $this->start_controls_section(
      'section_upsell_content',
      [
        'label' => __( 'Upsells', 'tenweb-builder' ),
      ]
    );

        //This option is added to avoid big changes connected with adding skins to Products widget
        //TODO maybe we will delete it when other solution will be implemented or we will add skins for this widget
        $this->add_control(
            '_skin',
            [
                'label' => esc_html__( 'Skin', 'elementor' ),
                'type' => Controls_Manager::HIDDEN,
                'default' => '',
            ]
        );

    $this->add_responsive_control(
      'columns',
      [
        'label' => __( 'Columns', 'tenweb-builder' ),
        'type' => Controls_Manager::NUMBER,
        'prefix_class' => 'elementor-products-columns%s-',
        'default' => 4,
        'min' => 1,
        'max' => 12,
      ]
    );

    $this->add_control(
      'orderby',
      [
        'label' => __( 'Order By', 'tenweb-builder' ),
        'type' => Controls_Manager::SELECT,
        'default' => 'date',
        'options' => [
          'date' => __( 'Date', 'tenweb-builder' ),
          'title' => __( 'Title', 'tenweb-builder' ),
          'price' => __( 'Price', 'tenweb-builder' ),
          'popularity' => __( 'Popularity', 'tenweb-builder' ),
          'rating' => __( 'Rating', 'tenweb-builder' ),
          'rand' => __( 'Random', 'tenweb-builder' ),
          'menu_order' => __( 'Menu Order', 'tenweb-builder' ),
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

		parent::register_controls();

		$this->start_injection( [
			'at' => 'before',
			'of' => 'section_design_box',
		] );

		$this->start_controls_section(
			'section_heading_style',
			[
				'label' => __( 'Heading', 'tenweb-builder' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'show_heading',
			[
				'label' => __( 'Heading', 'tenweb-builder' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'tenweb-builder' ),
				'label_on' => __( 'Show', 'tenweb-builder' ),
				'default' => 'yes',
				'return_value' => 'yes',
				'prefix_class' => 'show-heading-',
			]
		);

		$this->add_control(
			'heading_color',
			[
				'label' => __( 'Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
                'global' => [
                    'default' => Global_Colors::COLOR_PRIMARY,
                ],
				'selectors' => [
					'{{WRAPPER}}.elementor-wc-products .products > h2' => 'color: {{VALUE}}',
				],
				'condition' => [
					'show_heading!' => '',
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
				'selector' => '{{WRAPPER}}.elementor-wc-products .products > h2',
				'condition' => [
					'show_heading!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'heading_text_align',
			[
				'label' => __( 'Text Align', 'tenweb-builder' ),
				'type' => Controls_Manager::CHOOSE,
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
				'selectors' => [
					'{{WRAPPER}}.elementor-wc-products .products > h2' => 'text-align: {{VALUE}}',
				],
				'condition' => [
					'show_heading!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'heading_spacing',
			[
				'label' => __( 'Spacing', 'tenweb-builder' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}}.elementor-wc-products .products > h2' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'show_heading!' => '',
				],
			]
		);

		$this->end_controls_section();

		$this->end_injection();
	}

	protected function render() {
    global $product;
    if ( Woocommerce::is_template_page() && Woocommerce::get_preview_product() ) {
      $product = Woocommerce::get_upsell_product();
    }
    else {
      $product = wc_get_product();
    }
    if ( empty($product) ) {
      return;
    }


		$settings = $this->get_settings_for_display();
		$limit = '-1';
		$columns = 4;
		$orderby = 'rand';
		$order = 'desc';

		if ( ! empty( $settings['columns'] ) ) {
			$columns = $settings['columns'];
		}

		if ( ! empty( $settings['orderby'] ) ) {
			$orderby = $settings['orderby'];
		}

		if ( ! empty( $settings['order'] ) ) {
			$order = $settings['order'];
		}

		woocommerce_upsell_display( $limit, $columns, $orderby, $order );
	}

	public function render_plain_content() {}
}
\Elementor\Plugin::instance()->widgets_manager->register(new Product_Upsell());
