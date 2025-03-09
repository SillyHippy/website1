<?php
namespace Tenweb_Builder\Widgets\Woocommerce\Products\Skins;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Skin_Classic extends Skin_Base {

	protected function _register_controls_actions() {
		parent::_register_controls_actions();
    }

    public function add_injected_controls() {
        add_action( 'elementor/element/twbb_woocommerce-products/classic_skin_section_design_box/before_section_end', [ $this, 'inject_contrasting_controls' ] );
    }

    public function render() {
        parent::render();

    }

	public function get_id() {
		return 'classic';
	}

	public function get_title() {
		return esc_html__( 'Classic', 'tenweb-builder' );
	}

    public function get_description_of_products() {
        $settings = $this->parent->get_settings_for_display();
        $excerpt_length = isset( $settings[$this->get_control_id('skin_description_length' )] ) ? $settings[$this->get_control_id('skin_description_length' )]: 25;
        echo '<p class="twbb_woocommerce-loop-product__desc">' . $this->parent->get_woocommerce_excerpt($excerpt_length) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    protected function add_filters() {
        $settings = $this->parent->get_settings_for_display();

        //10web customization
        if ( (empty( $settings[$this->get_control_id('skin_hide_products_description')]) &&
                'default' === $settings[$this->get_control_id('skin_product_description')]) ||
            'yes' === $settings[$this->get_control_id('skin_product_description')] ) {
            add_action('woocommerce_after_shop_loop_item_title', array($this, 'get_description_of_products'), 9);
        }
        if ( ( 'yes' === $settings[$this->get_control_id('skin_hide_products_buttons')]
        && 'default' === $settings[$this->get_control_id('skin_product_buttons')] ) ||
            empty( $settings[$this->get_control_id('skin_product_buttons')] ) ) {
            add_filter( 'woocommerce_loop_add_to_cart_link', [ $this, 'remove_add_to_cart' ], 10, 1 );
        } else {
            if ( (empty( $settings[$this->get_control_id('skin_hide_product_quantity')] ) &&
                $settings[$this->get_control_id('skin_product_quantity')] === 'default') ||
                $settings[$this->get_control_id('skin_product_quantity')] === 'yes' ) {
                add_filter( 'woocommerce_loop_add_to_cart_link', [ $this, 'quantity_add_to_cart' ], 10, 1 );
            }
        }

	    if ('yes' === $settings['slider_view']) {
		    add_filter('woocommerce_product_loop_start', array($this, 'slider_wrapper_start'));
		    add_filter('woocommerce_product_loop_end', array($this, 'slider_wrapper_end'));
		    add_filter('post_class', array($this, 'slider_item_class'));
	    }
    }

    protected function remove_added_filters() {
        $settings = $this->parent->get_settings_for_display();

        //10web customization
        if ( (empty( $settings[$this->get_control_id('skin_hide_products_description')]) &&
                'default' === $settings[$this->get_control_id('skin_product_description')]) ||
            'yes' === $settings[$this->get_control_id('skin_product_description')] ) {
            remove_action('woocommerce_after_shop_loop_item_title', [$this, 'get_description_of_products'], 9);
        }
        if ( ( 'yes' === $settings[$this->get_control_id('skin_hide_products_buttons')] &&
                'default' === $settings[$this->get_control_id('skin_product_buttons')] ) ||
            empty( $settings[$this->get_control_id('skin_product_buttons')] ) ) {
            remove_filter( 'woocommerce_loop_add_to_cart_link', [ $this, 'remove_add_to_cart' ], 10 );
        } else {
            if ( (empty( $settings[$this->get_control_id('skin_hide_product_quantity')] ) &&
                    $settings[$this->get_control_id('skin_product_quantity')] === 'default') ||
                $settings[$this->get_control_id('skin_product_quantity')] === 'yes' ) {
                remove_filter( 'woocommerce_loop_add_to_cart_link', [ $this, 'quantity_add_to_cart' ], 10 );
            }
        }

	    if ('yes' === $settings['slider_view']) {
		    remove_filter('woocommerce_product_loop_start', array($this, 'slider_wrapper_start'));
		    remove_filter('woocommerce_product_loop_end', array($this, 'slider_wrapper_end'));
		    remove_filter('post_class', array($this, 'slider_item_class'));
	    }
    }

    public function inject_contrasting_controls() {
        $this->parent->start_injection( [
            'at' => 'after',
            'of' => 'paginate',
        ] );
        $this->add_control('skin_hide_products_images', [
            'label' => __('Hide Products Images', 'tenweb-builder'),
            'type' => Controls_Manager::SWITCHER,
            'default' => '',
            'render_type' => 'template',
            'selectors' => [
                '{{WRAPPER}}.elementor-wc-products.twbb-product-images-default .twbb-image-container,
                {{WRAPPER}}.elementor-wc-products.twbb-product-images-default .woocommerce ul.products li.product a img.attachment-woocommerce_thumbnail,
{{WRAPPER}}.elementor-wc-products.twbb-product-images-default .woocommerce ul.products li.product a img.woocommerce-placeholder' => 'display: none',
            ],
        ]);
        $this->add_control('skin_product_images', [
            'label' => __('Product Images', 'tenweb-builder'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => __( 'Show', 'tenweb-builder' ),
            'label_off' => __( 'Hide', 'tenweb-builder' ),
            'default' => 'default',
            'render_type' => 'template',
            'prefix_class' => 'twbb-product-images-',
        ]);
        $this->parent->end_injection();



        $this->parent->start_injection( [
            'at' => 'after',
            'of' => 'hide_products_buttons',
        ] );
        $this->add_control('skin_hide_products_buttons', [
            'label' => __('Hide Add to Cart buttons', 'tenweb-builder'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
        ]);
        $this->add_control('skin_product_buttons', [
            'label' => __('Product buttons', 'tenweb-builder'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => __( 'Show', 'tenweb-builder' ),
            'label_off' => __( 'Hide', 'tenweb-builder' ),
            'default' => 'default',
        ]);
        $this->parent->end_injection();



        $this->parent->start_injection( [
            'at' => 'after',
            'of' => 'classic_skin_box_padding',
        ] );

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
                '{{WRAPPER}}.elementor-wc-products ul.products li.product .woocommerce-loop-product__title,
		{{WRAPPER}}.elementor-wc-products ul.products li.product .star-rating,
		{{WRAPPER}}.elementor-wc-products ul.products li.product .price,
		{{WRAPPER}}.elementor-wc-products ul.products li.product .add_to_cart_button,
		{{WRAPPER}}.elementor-wc-products ul.products li.product .twbb_woocommerce-loop-product__desc,
		{{WRAPPER}}.elementor-wc-products ul.products li.product .star-rating' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
            ],
        ]);

        $this->parent->end_injection();
    }
}
