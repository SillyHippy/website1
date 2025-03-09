<?php
use Elementor\Settings;
//use \Tenweb_Builder\Templates;

class CartCheckout {

    const OPTION_NAME_USE_CART = 'use_cart_template';
    const OPTION_NAME_USE_CHECKOUT = 'use_checkout_template';

    private $use_cart_template;
    private $use_checkout_template;

    protected static $instance = NULL;

    /**
     * Woocommerce constructor.
     */
    public function __construct() {
        if ( $this->is_active() ) {

            if ( is_admin() && !defined('ELEMENTOR_PRO_VERSION') ) {
                add_action( 'elementor/admin/after_create_settings/' . Settings::PAGE_ID, [ $this, 'register_admin_fields' ], 15 );
            }

            $this->use_cart_template = 'yes' === get_option( 'elementor_' . self::OPTION_NAME_USE_CART, 'no' );
            $this->use_checkout_template = 'yes' === get_option( 'elementor_' . self::OPTION_NAME_USE_CHECKOUT, 'no' );
            add_action( 'wp_enqueue_scripts', array($this, 'enqueue_tenweb_builder_checkout_scripts') );

            if ( $this->use_checkout_template ) {
                /**
                 * @snippet    WooCommerce Show Product Image @ Checkout Page
                 */
                add_filter( 'woocommerce_cart_item_name', array($this, 'tenweb_product_image_on_checkout'), 10, 3 );
                add_filter('woocommerce_order_button_text', array($this, 'tenweb_custom_order_button_text'), 25);

                add_action('woocommerce_before_checkout_form', function() {
                    ?>
                    <!--Start of theme style-->
                    <div class="tenweb-woocommerce-checkout">
                    <?php
                }, 25);
                add_action('woocommerce_after_checkout_form', function() {
                    ?>
                    <!--End of theme style-->
                    </div>
                    <?php
                }, 25);
            }


            if ( $this->use_cart_template ) {
                //add_action( 'woocommerce_cart_totals_before_order_total', array($this, 'tenweb_add_coupon'), 10 );
                add_action( 'woocommerce_proceed_to_checkout', array($this, 'tenweb_add_coupon'), 10 );
                add_action('woocommerce_before_cart', function() {
                    ?>
                    <!--Start of theme style-->
                    <div class="tenweb-woocommerce-cart">
                    <?php
                }, 5);
                add_action('woocommerce_after_cart', function() {
                    ?>
                    <!--End of theme style-->
                    </div>
                    <?php
                }, 5);
            }
        }
    }

    public function enqueue_tenweb_builder_checkout_scripts() {
        if ( $this->use_checkout_template ) {
            wp_enqueue_style('tenweb-website-builder-theme-checkout-style', get_template_directory_uri() . '/woo/assets/checkout.css', array(), TWBT_VERSION);
        }
        if ( $this->use_cart_template ) {
            wp_enqueue_style('tenweb-website-builder-theme-cart-style', get_template_directory_uri() . '/woo/assets/cart.css', array(), TWBT_VERSION);
        }
        if ( $this->use_cart_template || $this->use_checkout_template) {
            wp_enqueue_script( 'tenweb-website-builder-theme-checkout-script', get_template_directory_uri() . '/woo/assets/checkout.js', array('jquery'), TWBT_VERSION );
        }
    }

    public function tenweb_add_coupon() {
        if ( wc_coupons_enabled() ) { ?>
            <a class="tenweb-coupon-link" onclick="tenweb_show_coupon(this)"><?php esc_html_e('Have a coupon?', "tenweb-builder")?></a>
            <div class="coupon" style="display: none">
                <label for="coupon_code" class="screen-reader-text"><?php esc_html_e( 'Coupon:', 'woocommerce' ); ?></label>
                <input type="text" name="coupon_code" class="input-text tenweb-woocommerce-cart-coupon_text" id="coupon_code" value="" placeholder="<?php esc_attr_e( 'Coupon code', 'woocommerce' ); ?>" />
                <a class="tenweb-woocommerce-cart-coupon_link button<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" name="apply_coupon" value="<?php esc_attr_e( 'Apply', 'woocommerce' ); ?>"><?php esc_html_e( 'Apply', 'woocommerce' ); ?></a>
                <?php do_action( 'woocommerce_cart_coupon' ); ?>
            </div>
        <?php }
    }


    public function tenweb_product_image_on_checkout( $name, $cart_item, $cart_item_key ) {

        /* Return if not checkout page */
        if ( ! is_checkout() ) {
            return $name;
        }

        /* Get product object */
        $_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

        /* Get product thumbnail */
        $thumbnail = $_product->get_image();

        /* Add wrapper to image and add some css */
        $image = '<div class="tenweb-product-image">'
            . $thumbnail .
            '</div>';

        /* Prepend image to name and return it */
        return $image . $name;

    }

    public function tenweb_custom_order_button_text()
    {
        $cart_total = WC()->cart->total;
        $currency = get_woocommerce_currency();
        $symbol = get_woocommerce_currency_symbol( $currency );
        return __('Place Order ' . $symbol . $cart_total, 'woocommerce');
    }


    /**
     * @return null|Woocommerce
     */
    public static function get_instance() {
        if ( self::$instance === NULL ) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * Is active
     *
     * @return bool
     */
    private function is_active() {
        return class_exists('woocommerce');
    }

    public function register_admin_fields( Settings $settings ) {
        $settings->add_section( Settings::TAB_INTEGRATIONS, 'woocommerce_card', [
            'callback' => function() {
                echo '<hr><h2>' . esc_html__( 'Woocommerce Card and Checkout pages templates', 'tenweb-builder' ) . '</h2>';
            },
            'fields' => [
                self::OPTION_NAME_USE_CART => [
                    'label' => esc_html__( 'Cart Page Template', 'tenweb-builder' ),
                    'field_args' => [
                        'type' => 'select',
                        'std' => 'initial',
                        'options' => [
                            'no' => esc_html__( 'Disable', 'tenweb-builder' ),
                            'yes' => esc_html__( 'Enable', 'tenweb-builder' ),
                        ],
                        'desc' => esc_html__( 'Set to `Enable` in order to use your Theme\'s template.', 'tenweb-builder' ),
                    ],
                ],
                self::OPTION_NAME_USE_CHECKOUT => [
                    'label' => esc_html__( 'Checkout Page Template', 'tenweb-builder' ),
                    'field_args' => [
                        'type' => 'select',
                        'std' => 'initial',
                        'options' => [
                            'no' => esc_html__( 'Disable', 'tenweb-builder' ),
                            'yes' => esc_html__( 'Enable', 'tenweb-builder' ),
                        ],
                        'desc' => esc_html__( 'Set to `Enable` in order to use your Theme\'s template.', 'tenweb-builder' ),
                    ],
                ],
            ],
        ] );
    }
}
