<?php
namespace Tenweb_Builder\Widgets\Woocommerce\Add_To_Cart;
include_once TWBB_DIR . '/classes/product-id-trait.php';

use Elementor\Controls_Manager;
use Elementor\Widget_Button;
use Tenweb_Builder\Classes\Woocommerce\Woocommerce;
use Tenweb_Builder\Classes\Product_Id_Trait;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class AddToCart extends Widget_Button {

    use Product_Id_Trait;

	public function get_name() {
		return 'twbb_add-to-cart';
	}

	public function get_title() {
		return __( 'Custom Add To Cart', 'tenweb-builder' );
	}

	public function get_icon() {
		return 'twbb-custom_add_to_cart twbb-widget-icon';
	}

	public function get_categories() {
		return [ Woocommerce::WOOCOMMERCE_GROUP ];
	}

	public function get_keywords() {
		return [ 'woocommerce', 'shop', 'store', 'cart', 'product', 'add to cart' ];
	}

	public function on_export( $element ) {
		unset( $element['settings']['product_id'] );

		return $element;
	}

	public function unescape_html( $safe_text, $text ) {
		return $text;
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_product',
			[
				'label' => __( 'Product', 'tenweb-builder' ),
			]
		);

		$this->add_control(
			'product_id',
			[
				'label' => __( 'Product', 'tenweb-builder' ),
				'type' => 'TWBBSelectAjax',
				'post_type' => '',
				'options' => [],
				'label_block' => true,
				'filter_by' => 'product',
				'object_type' => [ 'product' ],
			]
		);

		$this->add_control(
			'show_quantity',
			[
				'label' => __( 'Show Quantity', 'tenweb-builder' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'tenweb-builder' ),
				'label_on' => __( 'Show', 'tenweb-builder' ),

                'description' => esc_html__( 'Please note that switching on this option will disable some of the design controls.', 'tenweb-builder' ),
			]
		);

		$this->add_control(
			'quantity',
			[
				'label' => __( 'Quantity', 'tenweb-builder' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 1,
				'condition' => [
					'show_quantity' => '',
				],
			]
		);

		$this->end_controls_section();

		parent::register_controls();

        $this->start_controls_section(
            'section_layout',
            [
                'label' => esc_html__( 'Layout', 'tenweb-builder' ),
                'tab' => Controls_Manager::TAB_CONTENT,
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

		$this->update_control(
			'link',
			[
				'type' => Controls_Manager::HIDDEN,
				'default' => [
					'url' => '',
				],
			]
		);

		$this->update_control(
			'text',
			[
				'default' => __( 'Add to Cart', 'tenweb-builder' ),
				'placeholder' => __( 'Add to Cart', 'tenweb-builder' ),
			]
		);

        $this->update_control(
            'selected_icon',
            [
                'default' => [
                    'value' => 'fas fa-shopping-cart',
                    'library' => 'fa-solid',
                ],
            ]
        );

        $this->update_control(
            'size',
            [
                'condition' => [
                    'show_quantity' => '',
                ],
            ]
        );
	}

	protected function render() {

		$settings = $this->get_settings_for_display();

		if ( ! empty( $settings['product_id'] ) ) {
			$product_id = $settings['product_id'];
		} elseif ( wp_doing_ajax() && ! empty( $settings['product_id'] ) ) {
            $product_id = (int) Utils::_unstable_get_super_global_value( $_POST, 'post_id' ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		} else {
			$product_id = get_queried_object_id();
		}

        global $product;
        $product = $this->get_product( $product_id );

        $settings = $this->get_settings_for_display();

        if ( in_array( $settings['layout'], [ 'auto', 'stacked' ], true ) ) {
            add_action( 'woocommerce_before_add_to_cart_quantity', [ $this, 'before_add_to_cart_quantity' ], 95 );
            add_action( 'woocommerce_after_add_to_cart_button', [ $this, 'after_add_to_cart_button' ], 5 );
        }

        if ( 'yes' === $settings['show_quantity'] ) {
            $this->render_form_button( $product );
        } else {
            $this->render_ajax_button( $product );
        }

        if ( in_array( $settings['layout'], [ 'auto', 'stacked' ], true ) ) {
            remove_action( 'woocommerce_before_add_to_cart_quantity', [ $this, 'before_add_to_cart_quantity' ], 95 );
            remove_action( 'woocommerce_after_add_to_cart_button', [ $this, 'after_add_to_cart_button' ], 5 );
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
        ?>
        <div class="e-atc-qty-button-holder">
        <?php
    }

    /**
     * After Add to Cart Quantity
     *
     * @since 3.6.0
     */
    public function after_add_to_cart_button() {
        ?>
        </div>
        <?php
    }

	/**
	 * @param \WC_Product $product
	 */
	private function render_ajax_button( $product ) {
		$settings = $this->get_settings_for_display();

		if ( $product ) {
			if ( version_compare( WC()->version, '3.0.0', '>=' ) ) {
				$product_type = $product->get_type();
			} else {
				$product_type = $product->product_type;
			}

			$class = implode( ' ', array_filter( [
				'product_type_' . $product_type,
				$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
				$product->supports( 'ajax_add_to_cart' ) ? 'ajax_add_to_cart' : '',
			] ) );

			$this->add_render_attribute( 'button',
				[
					'rel' => 'nofollow',
					'href' => $product->add_to_cart_url(),
					'data-quantity' => ( isset( $settings['quantity'] ) ? $settings['quantity'] : 1 ),
					'data-product_id' => $product->get_id(),
					'class' => $class,
				]
			);

		} elseif ( current_user_can( 'manage_options' ) ) {
			$settings['text'] = __( 'Please set a valid product', 'tenweb-builder' );
			$this->set_settings( $settings );
		}

		parent::render();
	}

	private function render_form_button( $product ) {
		if ( ! $product && current_user_can( 'manage_options' ) ) {
                echo esc_html__( 'Please set a valid product.', 'tenweb-builder' );

			return;
		}

		$text_callback = function() {
			ob_start();
			$this->render_text();

			return ob_get_clean();
		};

		add_filter( 'woocommerce_get_stock_html', '__return_empty_string' );
		add_filter( 'woocommerce_product_single_add_to_cart_text', $text_callback );
		add_filter( 'esc_html', [ $this, 'unescape_html' ], 10, 2 );

		ob_start();
		woocommerce_template_single_add_to_cart();
		$form = ob_get_clean();
		$form = str_replace( 'single_add_to_cart_button', 'single_add_to_cart_button elementor-button', $form );
        // PHPCS - The HTML from 'woocommerce_template_single_add_to_cart' is safe.
        echo $form; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		remove_filter( 'woocommerce_product_single_add_to_cart_text', $text_callback );
		remove_filter( 'woocommerce_get_stock_html', '__return_empty_string' );
		remove_filter( 'esc_html', [ $this, 'unescape_html' ] );
	}

	// Force remote render
	protected function content_template() {}
}

\Elementor\Plugin::instance()->widgets_manager->register( new AddToCart() );

