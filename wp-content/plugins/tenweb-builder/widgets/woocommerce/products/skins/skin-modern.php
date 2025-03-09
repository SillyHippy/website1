<?php
namespace Tenweb_Builder\widgets\woocommerce\products\skins;

use Elementor\Controls_Manager;
use WC_Product_Variable;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Skin_Modern extends Skin_Base {

	protected function _register_controls_actions() {
		parent::_register_controls_actions();
    }

    public function add_injected_controls() {
        add_action( 'elementor/element/twbb_woocommerce-products/modern_skin_section_design_box/before_section_end', [ $this, 'inject_contrasting_controls' ] );
    }

    public function render() {
        parent::render();
    }

	public function get_id() {
		return 'modern';
	}

	public function get_title() {
		return esc_html__( 'Modern', 'tenweb-builder' );
	}

    protected function add_filters() {
        $settings = $this->parent->get_settings_for_display();
        remove_filter( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );
        add_action( 'woocommerce_after_shop_loop_item_title', array($this, 'get_product_info_open'), 4 );
        add_action( 'woocommerce_after_shop_loop_item_title', array($this, 'product_title_desc_close'), 9 );
        add_action( 'woocommerce_after_shop_loop_item_title', array($this, 'get_product_info_close'), 11 );
        add_filter( 'woocommerce_loop_add_to_cart_link', [ $this, 'add_to_cart_container_open' ], 9, 1 );
        add_filter( 'woocommerce_loop_add_to_cart_link', [ $this, 'add_to_cart_container_close' ], 12, 1 );

        //10web customization
        if ( (empty( $settings[$this->get_control_id('skin_hide_products_titles')] ) &&
            'default' === $settings[$this->get_control_id('skin_product_title')] ) ||
            'yes' === $settings[$this->get_control_id('skin_product_title')]) {
            add_action( 'woocommerce_after_shop_loop_item_title', array($this, 'get_title_of_products'), 7 );
        }
        if ( 'yes' === $settings[$this->get_control_id('skin_hide_products_description')] ) {
            add_action( 'woocommerce_after_shop_loop_item_title', array($this, 'get_description_of_products'), 8 );
        }

	    if ('yes' === $settings['slider_view']) {
		    add_filter('woocommerce_product_loop_start', array($this, 'slider_wrapper_start'));
		    add_filter('woocommerce_product_loop_end', array($this, 'slider_wrapper_end'));
		    add_filter('post_class', array($this, 'slider_item_class'));
	    }
    }

    protected function remove_added_filters() {
        remove_action( 'woocommerce_after_shop_loop_item_title', array($this, 'get_title_of_products'), 7 );
        remove_action( 'woocommerce_after_shop_loop_item_title', array($this, 'get_description_of_products'), 8 );
        remove_action( 'woocommerce_after_shop_loop_item_title', array($this, 'get_product_info_open'), 6 );
        remove_action( 'woocommerce_after_shop_loop_item_title', array($this, 'get_product_info_close'), 11 );
        //10web customization
        remove_action( 'woocommerce_after_shop_loop_item_title', array($this, 'get_title_of_products'), 6 );
        remove_action( 'woocommerce_after_shop_loop_item_title', array($this, 'get_description_of_products'), 7 );
        remove_filter( 'woocommerce_loop_add_to_cart_link', [ $this, 'remove_add_to_cart' ], 10 );
        remove_filter( 'woocommerce_loop_add_to_cart_link', [ $this, 'quantity_add_to_cart' ], 10 );
        remove_filter('woocommerce_product_loop_start', array($this, 'slider_wrapper_start'));
        remove_filter('woocommerce_product_loop_end', array($this, 'slider_wrapper_end'));
        remove_filter('post_class', array($this, 'slider_item_class'));
        remove_filter( 'woocommerce_loop_add_to_cart_link', [ $this, 'add_to_cart_container_open' ], 9,1 );
        remove_action( 'woocommerce_before_shop_loop_item_title', [$this->parent,'before_image_open_tag'], 9 );
        remove_action('woocommerce_before_shop_loop_item_title', [$this->parent, 'replace_main_image_template'], 10);
        remove_action( 'woocommerce_before_shop_loop_item_title', [$this->parent,'after_image_close_tag'], 11 );

    }

    public function get_description_of_products() {
        $settings = $this->parent->get_settings_for_display();
        $excerpt_length = isset( $settings[$this->get_control_id('skin_description_length' )] ) ? $settings[$this->get_control_id('skin_description_length' )]: 25;
        echo '<p class="twbb_woocommerce-loop-product__desc">' . $this->parent->get_woocommerce_excerpt($excerpt_length) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    public function get_title_of_products() {
        echo '<h2 class="' . esc_attr( apply_filters( 'woocommerce_product_loop_title_classes', 'woocommerce-loop-product__title' ) ) . '">' . get_the_title() . '</h2>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    public function get_product_info_open() {
        echo '<div class="product_info_div">';
        $this->parent->get_woocommerce_product_variations();
        echo '<div class="product_modern_info_container">';
        echo '<div class="product_title_desc_container">';
    }
    public function product_title_desc_close() {
        echo '</div><div class="product_price_container">';
    }
    public function get_product_info_close() {
        echo '</div></div></div>';
    }

    public function add_to_cart_container_open( $string ) {
        $settings = $this->parent->get_settings_for_display();
        $html = $string;
        echo '<div class="twbb-add_to_cart_container_open twbb-product-loop-buttons">';
        if( count( $this->getProductVariations() ) > 0 ) {
            return $string;
        }
        if ( (empty( $settings[$this->get_control_id('skin_hide_products_buttons')]) &&
                'default' === $settings[$this->get_control_id('skin_product_buttons')]) ||
            ''=== $settings[$this->get_control_id('skin_product_buttons')]) {
            $html = '';
        } else {
            if ( (empty( $settings[$this->get_control_id('skin_hide_product_quantity')] ) &&
                    $settings[$this->get_control_id('skin_product_quantity')] === 'default') ||
                $settings[$this->get_control_id('skin_product_quantity')] === 'yes' ) {
                $html = '<div class="twbb-product-quantity-container"><span class="twbb-minus-quantity twbb-product-quantity-change">-</span>' .
                    '<input class="twbb-product-quantity-input" type="number" min="1" value="1">' .
                    '<span class="twbb-plus-quantity twbb-product-quantity-change">+</span></div><div class="twbb_add_to_cart_cont">' . $string.'</div>';

            }
        }
         return $html;
    }
    public function add_to_cart_container_close( $string ) {
        return $string . '</div>';  // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    public function inject_contrasting_controls() {
        $this->parent->start_injection( [
            'at' => 'after',
            'of' => 'hide_products_buttons',
        ] );
        $this->add_control('skin_hide_products_buttons', [
            'label' => __('Product buttons', 'tenweb-builder'),
            'type' => Controls_Manager::SELECT,
            'options' => [
                '' => __( 'Hide', 'tenweb-builder' ),
                'show_on_hover' => __( 'Show on hover', 'tenweb-builder' ),
                'always_show' => __( 'Always Show', 'tenweb-builder' ),
            ],
            'default' => 'show_on_hover',
            'prefix_class' => 'product_buttons_visibility__',
        ]);

        $this->add_control('skin_product_buttons', [
            'label' => __('Product buttons', 'tenweb-builder'),
            'type' => Controls_Manager::SELECT,
            'options' => [
                '' => __( 'Hide', 'tenweb-builder' ),
                'show_on_hover' => __( 'Show on hover', 'tenweb-builder' ),
                'always_show' => __( 'Always Show', 'tenweb-builder' ),
            ],
            'render_type' => 'template',
            'default' => 'default',
            'prefix_class' => 'product_buttons_visibility__',
        ]);

        $this->parent->end_injection();
        $this->parent->start_injection( [
            'at' => 'after',
                'of' => 'modern_skin_description_length',
        ] );

        $this->parent->end_injection();

        $this->parent->start_injection( [
            'at' => 'after',
            'of' => 'modern_skin_box_border_color',
        ] );

        $this->add_control('skin_content_box_style', [
            'label' => esc_html__('Content Box', 'tenweb-builder'),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_responsive_control('skin_content_padding', [
            'label' => __('Content Padding', 'tenweb-builder'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em' ],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 50,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}}.elementor-wc-products ul.products li.product .product_info_div' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
            ],
        ]);

        $this->add_responsive_control('skin_buttons_padding', [
            'label' => __('Buttons Padding', 'tenweb-builder'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em' ],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 50,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}}.elementor-wc-products ul.products li.product .twbb-add_to_cart_container_open' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
            ],
            'condition' => [
                $this->get_control_id( 'skin_product_buttons!' ) => '',
            ],
        ]);

        $this->start_controls_tabs('skin_content_box_style_tabs');
        $this->start_controls_tab('skin_content_box_style_normal', [
            'label' => __('Normal', 'tenweb-builder'),
        ]);
        $this->add_control('skin_content_box_bg_color', [
            'label' => __('Background Color', 'tenweb-builder'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}}.elementor-wc-products ul.products li.product .product_info_div,
                {{WRAPPER}}.elementor-wc-products ul.products li.product .twbb-add_to_cart_container_open' => 'background-color: {{VALUE}}',
            ],
        ]);
        $this->end_controls_tab();
        $this->start_controls_tab('skin_content_box_style_hover', [
            'label' => __('Hover', 'tenweb-builder'),
        ]);
        $this->add_control('skin_content_box_bg_color_hover', [
            'label' => __('Background Color', 'tenweb-builder'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}}.elementor-wc-products ul.products li.product:hover .product_info_div,
                {{WRAPPER}}.elementor-wc-products ul.products li.product:hover .twbb-add_to_cart_container_open' => 'background-color: {{VALUE}}',
            ],
        ]);
        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->parent->end_injection();
    }
}
