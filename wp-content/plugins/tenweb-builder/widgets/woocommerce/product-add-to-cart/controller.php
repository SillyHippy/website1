<?php
namespace Tenweb_Builder\Widgets\Woocommerce\Products;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Tenweb_Builder\Classes\Woocommerce\Woocommerce;

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

class Product_Add_To_Cart extends Widget_Base {

  public function get_name() {
    return 'twbb_woocommerce-product-add-to-cart';
  }

  public function get_title() {
    return esc_html__( 'Add To Cart', 'tenweb-builder' );
  }

  public function get_icon() {
    return 'twbb-add_to_cart twbb-widget-icon';
  }

  public function get_categories() {
    return [ Woocommerce::WOOCOMMERCE_BUILDER_GROUP ];
  }

  public function get_keywords() {
    return [ 'woocommerce', 'shop', 'store', 'cart', 'product', 'button', 'product add to cart', 'add to cart' ];
  }

  protected function register_controls() {

		$this->start_controls_section(
			'section_layout',
			[
				'label' => esc_html__( 'Layout', 'tenweb-builder' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'layout',
			[
				'label' => esc_html__( 'Layout', 'tenweb-builder' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => esc_html__( 'Inline', 'tenweb-builder' ),
					'stacked' => esc_html__( 'Stacked', 'tenweb-builder' ),
					'auto' => esc_html__( 'Auto', 'tenweb-builder' ),
				],
				'prefix_class' => 'elementor-add-to-cart--layout-',
				'render_type' => 'template',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_atc_button_style',
			[
				'label' => esc_html__( 'Button', 'tenweb-builder' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

    $this->add_control(
      'wc_style_warning',
      [
        'type' => Controls_Manager::RAW_HTML,
        'raw' => esc_html__( 'The style of this widget is often affected by your theme and plugins. If you experience any such issue, try to switch to a basic theme and deactivate related plugins.', 'tenweb-builder' ),
        'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
      ]
    );

    $this->add_responsive_control(
      'alignment',
      [
        'label' => esc_html__( 'Alignment', 'tenweb-builder' ),
        'type' => Controls_Manager::CHOOSE,
        'options' => [
          'left' => [
            'title' => esc_html__( 'Left', 'tenweb-builder' ),
						'icon' => 'eicon-text-align-left',
          ],
          'center' => [
            'title' => esc_html__( 'Center', 'tenweb-builder' ),
						'icon' => 'eicon-text-align-center',
          ],
          'right' => [
            'title' => esc_html__( 'Right', 'tenweb-builder' ),
						'icon' => 'eicon-text-align-right',
          ],
          'justify' => [
            'title' => esc_html__( 'Justified', 'tenweb-builder' ),
						'icon' => 'eicon-text-align-justify',
          ],
        ],
        'prefix_class' => 'elementor-add-to-cart%s--align-',
      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'button_typography',
				'selector' => '{{WRAPPER}} .cart button, {{WRAPPER}} .cart .button',
      ]
    );

    $this->add_group_control(
      Group_Control_Border::get_type(),
      [
        'name' => 'button_border',
				'selector' => '{{WRAPPER}} .cart button, {{WRAPPER}} .cart .button',
        'exclude' => [ 'color' ], // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude
      ]
    );

    $this->add_control(
      'button_border_radius',
      [
        'label' => esc_html__( 'Border Radius', 'tenweb-builder' ),
        'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
        'selectors' => [
					'{{WRAPPER}} .cart button, {{WRAPPER}} .cart .button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
      ]
    );

    $this->add_control(
      'button_padding',
      [
        'label' => esc_html__( 'Padding', 'tenweb-builder' ),
        'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
        'selectors' => [
					'{{WRAPPER}} .cart button, {{WRAPPER}} .cart .button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
      ]
    );

    $this->start_controls_tabs( 'button_style_tabs' );

    $this->start_controls_tab( 'button_style_normal',
                               [
                                 'label' => esc_html__( 'Normal', 'tenweb-builder' ),
                               ]
    );

    $this->add_control(
      'button_text_color',
      [
        'label' => esc_html__( 'Text Color', 'tenweb-builder' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
					'{{WRAPPER}} .cart button, {{WRAPPER}} .cart .button' => 'color: {{VALUE}}',
        ],
      ]
    );

    $this->add_control(
      'button_bg_color',
      [
        'label' => esc_html__( 'Background Color', 'tenweb-builder' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
					'{{WRAPPER}} .cart button, {{WRAPPER}} .cart .button' => 'background-color: {{VALUE}}',
        ],
      ]
    );

    $this->add_control(
      'button_border_color',
      [
        'label' => esc_html__( 'Border Color', 'tenweb-builder' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
					'{{WRAPPER}} .cart button, {{WRAPPER}} .cart .button' => 'border-color: {{VALUE}}',
        ],
      ]
    );

    $this->end_controls_tab();

    $this->start_controls_tab( 'button_style_hover',
                               [
                                 'label' => esc_html__( 'Hover', 'tenweb-builder' ),
                               ]
    );

    $this->add_control(
      'button_text_color_hover',
      [
        'label' => esc_html__( 'Text Color', 'tenweb-builder' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
					'{{WRAPPER}} .cart button:hover, {{WRAPPER}} .cart .button:hover' => 'color: {{VALUE}}',
        ],
      ]
    );

    $this->add_control(
      'button_bg_color_hover',
      [
        'label' => esc_html__( 'Background Color', 'tenweb-builder' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
					'{{WRAPPER}} .cart button:hover, {{WRAPPER}} .cart .button:hover' => 'background-color: {{VALUE}}',
        ],
      ]
    );

    $this->add_control(
      'button_border_color_hover',
      [
        'label' => esc_html__( 'Border Color', 'tenweb-builder' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
					'{{WRAPPER}} .cart button:hover, {{WRAPPER}} .cart .button:hover' => 'border-color: {{VALUE}}',
        ],
      ]
    );

    $this->add_control(
      'button_transition',
      [
        'label' => esc_html__( 'Transition Duration', 'tenweb-builder' ),
        'type' => Controls_Manager::SLIDER,
        'default' => [
          'size' => 0.2,
        ],
        'range' => [
          'px' => [
            'max' => 2,
            'step' => 0.1,
          ],
        ],
        'selectors' => [
					'{{WRAPPER}} .cart button, {{WRAPPER}} .cart .button' => 'transition: all {{SIZE}}s',
        ],
      ]
    );

    $this->end_controls_tab();

    $this->end_controls_tabs();

		$this->add_control(
			'heading_view_cart_style',
			[
				'label' => esc_html__( 'View Cart', 'tenweb-builder' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'view_cart_color',
			[
				'label' => esc_html__( 'Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .added_to_cart' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'view_cart_typography',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				],
				'selector' => '{{WRAPPER}} .added_to_cart',
			]
		);

		$this->add_responsive_control(
			'view_cart_spacing',
			[
				'label' => esc_html__( 'Spacing', 'tenweb-builder' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
						'step' => 1,
					],
					'em' => [
						'min' => 0,
						'max' => 3.5,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--view-cart-spacing: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

    $this->start_controls_section(
      'section_atc_quantity_style',
      [
        'label' => esc_html__( 'Quantity', 'tenweb-builder' ),
        'tab' => Controls_Manager::TAB_STYLE,
      ]
    );

		$this->add_control(
			'show_quantity',
			[
				'label' => esc_html__( 'Quantity', 'tenweb-builder' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'tenweb-builder' ),
				'label_off' => esc_html__( 'Hide', 'tenweb-builder' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'prefix_class' => 'e-add-to-cart--show-quantity-',
				'render_type' => 'template',
			]
		);

		$this->add_responsive_control(
			'spacing',
			[
				'label' => esc_html__( 'Spacing', 'tenweb-builder' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'custom' ],
				'selectors' => [
					'{{WRAPPER}}' => '--button-spacing: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'show_quantity!' => '',
				],
			]
		);

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'quantity_typography',
        'selector' => '{{WRAPPER}} .quantity .qty',
				'condition' => [
					'show_quantity!' => '',
				],
      ]
    );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'quantity_border',
				'selector' => '{{WRAPPER}} .quantity .qty',
				'exclude' => [ 'color' ], // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude
				'condition' => [
					'show_quantity!' => '',
				],
			]
		);

		$this->add_control(
			'quantity_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'tenweb-builder' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .quantity .qty' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'show_quantity!' => '',
				],
			]
		);

    $this->add_control(
      'quantity_padding',
      [
        'label' => esc_html__( 'Padding', 'tenweb-builder' ),
        'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
        'selectors' => [
          '{{WRAPPER}} .quantity .qty' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
				'condition' => [
					'show_quantity!' => '',
				],
      ]
    );

		$this->start_controls_tabs( 'quantity_style_tabs',
			[
				'condition' => [
					'show_quantity!' => '',
				],
			]
		);

    $this->start_controls_tab( 'quantity_style_normal',
                               [
                                 'label' => esc_html__( 'Normal', 'tenweb-builder' ),
                               ]
    );

    $this->add_control(
      'quantity_text_color',
      [
        'label' => esc_html__( 'Text Color', 'tenweb-builder' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .quantity .qty' => 'color: {{VALUE}}',
        ],
      ]
    );

    $this->add_control(
      'quantity_bg_color',
      [
        'label' => esc_html__( 'Background Color', 'tenweb-builder' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .quantity .qty' => 'background-color: {{VALUE}}',
        ],
      ]
    );

    $this->add_control(
      'quantity_border_color',
      [
        'label' => esc_html__( 'Border Color', 'tenweb-builder' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .quantity .qty' => 'border-color: {{VALUE}}',
        ],
      ]
    );

    $this->end_controls_tab();

    $this->start_controls_tab( 'quantity_style_focus',
                               [
                                 'label' => esc_html__( 'Focus', 'tenweb-builder' ),
                               ]
    );

    $this->add_control(
      'quantity_text_color_focus',
      [
        'label' => esc_html__( 'Text Color', 'tenweb-builder' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .quantity .qty:focus' => 'color: {{VALUE}}',
        ],
      ]
    );

    $this->add_control(
      'quantity_bg_color_focus',
      [
        'label' => esc_html__( 'Background Color', 'tenweb-builder' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .quantity .qty:focus' => 'background-color: {{VALUE}}',
        ],
      ]
    );

    $this->add_control(
      'quantity_border_color_focus',
      [
        'label' => esc_html__( 'Border Color', 'tenweb-builder' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .quantity .qty:focus' => 'border-color: {{VALUE}}',
        ],
      ]
    );

    $this->add_control(
      'quantity_transition',
      [
        'label' => esc_html__( 'Transition Duration', 'tenweb-builder' ),
        'type' => Controls_Manager::SLIDER,
        'default' => [
          'size' => 0.2,
        ],
        'range' => [
          'px' => [
            'max' => 2,
            'step' => 0.1,
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .quantity .qty' => 'transition: all {{SIZE}}s',
        ],
      ]
    );

    $this->end_controls_tab();

    $this->end_controls_tabs();

    $this->end_controls_section();

    $this->start_controls_section(
      'section_atc_variations_style',
      [
        'label' => esc_html__( 'Variations', 'tenweb-builder' ),
        'tab' => Controls_Manager::TAB_STYLE,
      ]
    );

		$this->add_control(
			'variations_width',
			[
				'label' => esc_html__( 'Width', 'tenweb-builder' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'default' => [
					'unit' => '%',
				],
				'selectors' => [
					'.woocommerce {{WRAPPER}} form.cart .variations' => 'width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'variations_spacing',
			[
				'label' => esc_html__( 'Spacing', 'tenweb-builder' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'custom' ],
				'selectors' => [
					'.woocommerce {{WRAPPER}} form.cart .variations' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

    $this->add_control(
      'variations_space_between',
      [
        'label' => esc_html__( 'Space Between', 'tenweb-builder' ),
        'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'custom' ],
        'selectors' => [
					'.woocommerce {{WRAPPER}} form.cart table.variations tr th, .woocommerce {{WRAPPER}} form.cart table.variations tr td' => 'padding-top: calc( {{SIZE}}{{UNIT}}/2 ); padding-bottom: calc( {{SIZE}}{{UNIT}}/2 );',
        ],
      ]
    );

    $this->add_control(
      'heading_variations_label_style',
      [
        'label' => esc_html__( 'Label', 'tenweb-builder' ),
        'type' => Controls_Manager::HEADING,
        'separator' => 'before',
      ]
    );

    $this->add_control(
      'variations_label_color_focus',
      [
        'label' => esc_html__( 'Color', 'tenweb-builder' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
					'.woocommerce {{WRAPPER}} form.cart table.variations label' => 'color: {{VALUE}}',
        ],
      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'variations_label_typography',
				'selector' => '.woocommerce {{WRAPPER}} form.cart table.variations label',
      ]
    );

    $this->add_control(
      'heading_variations_select_style',
      [
        'label' => esc_html__( 'Select field', 'tenweb-builder' ),
        'type' => Controls_Manager::HEADING,
        'separator' => 'before',
      ]
    );

    $this->add_control(
      'variations_select_color',
      [
        'label' => esc_html__( 'Color', 'tenweb-builder' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
					'.woocommerce {{WRAPPER}} form.cart table.variations td.value select' => 'color: {{VALUE}}',
        ],
      ]
    );

    $this->add_control(
      'variations_select_bg_color',
      [
        'label' => esc_html__( 'Background Color', 'tenweb-builder' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
					'.woocommerce {{WRAPPER}} form.cart table.variations td.value select, .woocommerce {{WRAPPER}} form.cart table.variations td.value:before' => 'background-color: {{VALUE}}',
        ],
      ]
    );

    $this->add_control(
      'variations_select_border_color',
      [
        'label' => esc_html__( 'Border Color', 'tenweb-builder' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
					'.woocommerce {{WRAPPER}} form.cart table.variations td.value select, .woocommerce {{WRAPPER}} form.cart table.variations td.value:before' => 'border: 1px solid {{VALUE}}',
        ],
      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'variations_select_typography',
				'selector' => '.woocommerce {{WRAPPER}} form.cart table.variations td.value select, .woocommerce div.product.elementor{{WRAPPER}} form.cart table.variations td.value:before',
      ]
    );

    $this->add_control(
      'variations_select_border_radius',
      [
        'label' => esc_html__( 'Border Radius', 'tenweb-builder' ),
        'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
        'selectors' => [
					'.woocommerce {{WRAPPER}} form.cart table.variations td.value select, .woocommerce {{WRAPPER}} form.cart table.variations td.value:before' => 'border-radius: {{SIZE}}{{UNIT}}',
        ],
      ]
    );

    $this->end_controls_section();
  }

    protected function render() {
        global $product;

        $product = $this->get_product();
        if ( ! $product ) {
            return;
        }
        add_action( 'woocommerce_before_add_to_cart_quantity', [ $this, 'before_add_to_cart_quantity' ], 95 );
        add_action( 'woocommerce_before_add_to_cart_button', [ $this, 'before_add_to_cart_quantity' ], 5 );
        add_action( 'woocommerce_after_add_to_cart_button', [ $this, 'after_add_to_cart_button' ], 5 );
        ?>

        <div class="elementor-add-to-cart elementor-product-<?php echo esc_attr( $product->get_type() ); ?>">
            <?php if ( $this->is_loop_item() ) {
                $this->render_loop_add_to_cart();
            } else {
                woocommerce_template_single_add_to_cart();
            } ?>
        </div>

        <?php
        remove_action( 'woocommerce_before_add_to_cart_quantity', [ $this, 'before_add_to_cart_quantity' ], 95 );
        remove_action( 'woocommerce_before_add_to_cart_button', [ $this, 'before_add_to_cart_quantity' ], 5 );
        remove_action( 'woocommerce_after_add_to_cart_button', [ $this, 'after_add_to_cart_button' ], 5 );
    }

    private function render_loop_add_to_cart() {
        $quantity_args = $this->get_loop_quantity_args();
        $button_args = [ 'quantity' => $quantity_args['min_value'] ];
        ?>
        <div class="e-loop-add-to-cart-form-container">
            <form class="cart e-loop-add-to-cart-form">
                <?php
                $this->before_add_to_cart_quantity();

                $this->render_loop_quantity_input( $quantity_args );
                woocommerce_template_loop_add_to_cart( $button_args );

                $this->after_add_to_cart_button();
                ?>
            </form>
        </div>
        <?php
    }

    private function render_loop_quantity_input( $quantity_args ) {
        global $product;

        if (
            'simple' === $product->get_type()
            && 'yes' === $this->get_settings_for_display( 'show_quantity' )
        ) {
            woocommerce_quantity_input( $quantity_args );
        }
    }

    private function get_loop_quantity_args() {
        global $product;

        $quantity_args = [
            'min_value' => apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
            'max_value' => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
            'input_value' => $product->get_min_purchase_quantity(),
            'classes' => [ 'input-text', 'qty', 'text' ],
        ];

        if ( 'no' === get_option( 'woocommerce_enable_ajax_add_to_cart' ) ) {
            $quantity_args['min_value'] = $product->get_min_purchase_quantity();
            $quantity_args['input_value'] = $product->get_min_purchase_quantity();
            $quantity_args['classes'][] = 'disabled';
        }

        return $quantity_args;
    }

    private function is_loop_item() {
        return 'loop-item' === \Elementor\Plugin::instance()->documents->get_current()->get_type();
    }

    private function is_loop_item_template_edit() {
        return ( \Elementor\Plugin::instance()->editor->is_edit_mode() && $this->is_loop_item() );
    }

    public function should_add_container() {
        global $product;

        if ( ! in_array( $this->get_settings_for_display( 'layout' ), [ 'auto', 'stacked' ], true ) ) {
            return false;
        }

        switch ( current_action() ) {
            case 'woocommerce_before_add_to_cart_quantity':
                return in_array( $product->get_type(), [ 'simple', 'variable' ], true );
            case 'woocommerce_before_add_to_cart_button':
                return in_array( $product->get_type(), [ 'grouped', 'external' ], true );
            case 'woocommerce_after_add_to_cart_button':
            default:
                return true;
        }
    }

    /**
     * Before Add to Cart Quantity
     *
     * Added wrapper tag around the quantity input and "Add to Cart" button
     * used to more solidly accommodate the layout when additional elements
     * are added by 3rd party plugins.
     *
     * @since 3.6.0
     */
    public function before_add_to_cart_quantity() {
        if ( ! $this->should_add_container() ) {
            return;
        }
        ?>
        <div class="e-atc-qty-button-holder">
        <?php
    }

    /**
     * After Add to Cart Button
     *
     * @since 3.6.0
     */
    public function after_add_to_cart_button() {
        if ( ! $this->should_add_container() ) {
            return;
        }
        ?>
        </div>
        <?php
    }

    public function get_product( $product_id = false ) {
        if ( 'product_variation' === get_post_type() ) {
            return $this->get_product_variation( $product_id );
        }
        if ( Woocommerce::is_template_page() && Woocommerce::get_preview_product() ) {
            $preview_product = Woocommerce::get_preview_product();
            $product = wc_get_product($preview_product->ID);
        } else {
            $product = wc_get_product($product_id);
            if (!$product) {
                $product = wc_get_product();
            }
        }

        return $product;
    }

    public function get_product_variation( $product_id = false ) {
        return wc_get_product( get_the_ID() );
    }

  public function render_plain_content() {}
}

\Elementor\Plugin::instance()->widgets_manager->register( new Product_Add_To_Cart() );
