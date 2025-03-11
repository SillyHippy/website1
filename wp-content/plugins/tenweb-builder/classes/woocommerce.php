<?php
/**
 * Class Woocommerce
 *
 * @package Tenweb_Builder\Classes\Woocommerce
 */

namespace Tenweb_Builder\Classes\Woocommerce;
if( class_exists('woocommerce') ) {
    include_once(TWBB_DIR . '/widgets/woocommerce/products/classes/products-renderer.php');
}
use Elementor\Core\Common\Modules\Ajax\Module as Ajax;
use Elementor\Icons_Manager;
use Elementor\Settings;
use Elementor\Utils;
use \Tenweb_Builder\Templates;
use \Tenweb_Builder\Widgets\Woocommerce\Settings_Woocommerce;
use \Tenweb_Builder\Widgets\Woocommerce\Products\Classes\Products_Renderer;
use \Tenweb_Builder\Widgets\Woocommerce\Products\Products as Products_Widget;

class Woocommerce {

  const WOOCOMMERCE_GROUP = 'tenweb-woocommerce-widgets';
  const WOOCOMMERCE_BUILDER_GROUP = 'tenweb-woocommerce-builder-widgets';
  const OPTION_NAME_USE_MINI_CART = 'use_mini_cart_template';
  const MENU_CART_FRAGMENTS_ACTION = 'twbb_menu-cart-fragments';
  protected static $instance = NULL;
  protected $woocommerce_notices_elements = [];
  protected $use_mini_cart_template;

  /**
   * Woocommerce constructor.
   */
  public function __construct() {
    if ( $this->is_active() ) {

      $this->use_mini_cart_template = 'yes' === get_option( 'elementor_' . self::OPTION_NAME_USE_MINI_CART, 'no' );
      if ( is_admin() && !defined('ELEMENTOR_PRO_VERSION') ) {
         add_action( 'elementor/admin/after_create_settings/' . Settings::PAGE_ID, [ $this, 'register_admin_fields' ], 15 );
      }

      $this->get_actions();
        //phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $action = self::_unstable_get_super_global_value( $_REQUEST, 'action' );

        // Allow viewing of Checkout page in the Editor with an empty cart.
        if (
            ( 'elementor' === $action && is_admin() ) // Elementor Editor
            || 'elementor_ajax' === $action // Elementor Editor Preview - Ajax Render Widget
            //phpcs:ignore WordPress.Security.NonceVerification.Recommended
            || self::_unstable_get_super_global_value( $_REQUEST, 'elementor-preview' ) // Elementor Editor Preview
        ) {
            add_filter( 'woocommerce_checkout_redirect_empty_cart', '__return_false', 5 );
        }
    }
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

  public static function is_template_page() {
    if ( \Elementor\Plugin::instance()->editor->is_edit_mode() && Templates::get_instance()->is_elementor_template_type() ) {
      return TRUE;
    }

    return FALSE;
  }

  public static function get_preview_product() {
	  //phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.get_posts_get_posts
    $posts = get_posts([
                         'post_type' => 'product',
                         'posts_per_page' => 1,
                         'orderby' => 'id',
                         'order' => 'ASC',
                       ]);
    if ( !empty($posts[0]) ) {
      return $posts[0];
    }

    return FALSE;
  }

  public static function get_upsell_product() {
	  //phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.get_posts_get_posts
    $posts = get_posts([
                         'post_type' => 'product',
                         'orderby' => 'id',
                         'order' => 'ASC',
                       ]);
    foreach ( $posts as $post ) {
      if ( isset($post->ID) ) {
        $product = wc_get_product($post->ID);
        $upsell_ids = $product->get_upsell_ids();
        if ( isset($upsell_ids) && is_array($upsell_ids) && !empty($upsell_ids) ) {
          return $product;
        }
      }
    }

    return FALSE;
  }

  public static function add_new_product_link() {
    $new_product_href = add_query_arg(array( 'post_type' => 'product' ), admin_url('post-new.php'));
    $new_product_link = '<a href="' . $new_product_href . '" target="_blank">' . __("New Product", "tenweb-builder") . '</a>';
    $pattern = __('Product not found, please add %s', 'tenweb-builder');

    return wp_sprintf($pattern, $new_product_link);
  }

  public function get_actions() {
    // On Editor - Register WooCommerce frontend hooks before the Editor init.
    // Priority = 5, in order to allow plugins remove/add their wc hooks on init.
	  //phpcs:ignore WordPress.Security.NonceVerification.Recommended
    if ( !empty($_REQUEST['action']) && 'elementor' === $_REQUEST['action'] && is_admin() && $this->is_active() ) {
      add_action('init', [ $this, 'register_wc_hooks' ], 5);
    }
    add_action('elementor/widgets/register', [ $this, 'register_widgets' ]);
    if ( TWBB_DEV === TRUE ) {
      add_action('twbb_after_enqueue_styles', [ $this, 'after_enqueue_styles' ], 10, 2);
      add_action('twbb_after_enqueue_scripts', [ $this, 'after_enqueue_scripts' ], 10, 2);
    }
	  add_action('elementor/editor/before_enqueue_scripts', [ $this, 'maybe_init_cart' ]);
      add_filter( 'tenweb_builder_settings', [ $this, 'add_localize_data' ] );
      add_filter( 'elementor_tenweb/frontend/localize_settings', [ $this, 'localized_settings_frontend' ] );
	  add_action('wp_enqueue_scripts', [ $this, 'enqueue_woocommerce_scripts' ], 11);
      if ( $this->use_mini_cart_template ) {
          add_filter('woocommerce_locate_template', [$this, 'twbb_woocommerce_locate_template'], 10, 3);
      }
      //phpcs:ignore WordPress.Security.NonceVerification.Recommended
      $wc_ajax = self::_unstable_get_super_global_value( $_REQUEST, 'wc-ajax' );
      if ( 'get_refreshed_fragments' === $wc_ajax ) {
          add_action( 'woocommerce_add_to_cart_fragments', [ $this, 'products_query_sources_fragments' ] );
          // Render the Empty Cart Template on WC fragment refresh
          add_action( 'woocommerce_add_to_cart_fragments', [ $this, 'empty_cart_fragments' ] );
      }

      add_filter( 'elementor/widgets/wordpress/widget_args', [ $this, 'woocommerce_wordpress_widget_css_class' ], 10, 2 );

      // Load our widget Before WooCommerce Ajax. See the variable's PHPDoc for details.
      add_action( 'woocommerce_checkout_update_order_review', [ $this, 'load_widget_before_wc_ajax' ] );
      add_action( 'woocommerce_before_calculate_totals', [ $this, 'load_widget_before_wc_ajax' ] );

      add_action( 'wp_ajax_elementor_woocommerce_checkout_login_user', [ $this, 'elementor_woocommerce_checkout_login_user' ] );
      add_action( 'wp_ajax_nopriv_elementor_woocommerce_checkout_login_user', [ $this, 'elementor_woocommerce_checkout_login_user' ] );

      add_action( 'wp_ajax_twbb_menu_cart_fragments', [ $this, 'menu_cart_fragments' ] );
      add_action( 'wp_ajax_nopriv_twbb_menu_cart_fragments', [ $this, 'menu_cart_fragments' ] );

      add_filter( 'woocommerce_add_to_cart_fragments', [ $this, 'e_cart_count_fragments' ] );

      add_action( 'wp', [ $this, 'maybe_define_woocommerce_checkout' ] );
      add_filter( 'woocommerce_get_endpoint_url', [ $this, 'get_order_received_endpoint_url' ], 10, 3 );

      // Filters for messages on the Shipping calculator
      add_filter( 'woocommerce_shipping_may_be_available_html', function ( $html ) {
          return $this->print_woocommerce_shipping_message( $html, 'woocommerce-shipping-may-be-available-html e-checkout-message e-cart-content' );
      }, 10, 1 );

      add_filter( 'woocommerce_shipping_not_enabled_on_cart_html', function ( $html ) {
          return $this->print_woocommerce_shipping_message( $html, 'woocommerce-shipping-not_enabled-on-cart-html e-checkout-message e-cart-content' );
      }, 10, 1 );

      add_filter( 'woocommerce_shipping_estimate_html', function ( $html ) {
          return $this->print_woocommerce_shipping_message( $html, 'woocommerce-shipping-estimate-html e-checkout-message e-cart-content' );
      }, 10, 1 );

      add_filter( 'woocommerce_cart_no_shipping_available_html', function ( $html ) {
          return $this->print_woocommerce_shipping_message( $html, 'woocommerce-cart-no-shipping-available-html e-checkout-message e-cart-content' );
      }, 10, 1 );

      add_filter( 'woocommerce_no_available_payment_methods_message', function ( $html ) {
          return $this->print_woocommerce_shipping_message( $html, 'woocommerce-no-available-payment-methods-message e-description' );
      }, 10, 1 );

      add_filter( 'woocommerce_no_shipping_available_html', function ( $html ) {
          return $this->print_woocommerce_shipping_message( $html, 'woocommerce-no-shipping-available-html e-checkout-message' );
      }, 10, 1 );

      add_action( 'woocommerce_add_to_cart', [ $this, 'localize_added_to_cart_on_product_single' ] );

      // Woocommerce tab in Site Settings is working in same way as ElementorPro one, so it is deactivated when elementorPro is active
      if ( !defined( 'ELEMENTOR_PRO_VERSION' ) ) {
          add_action('elementor/ajax/register_actions', array($this, 'register_ajax_actions'));

          add_action('elementor/kit/register_tabs', [$this, 'init_site_settings'], 1, 40);
          $this->add_update_kit_settings_hooks();

          // WooCommerce Notice Site Settings
          add_filter('body_class', [$this, 'e_notices_body_classes']);
          add_action('wp_enqueue_scripts', [$this, 'e_notices_css']);

          add_filter( 'elementor/query/query_args', function( $query_args, $widget ) {
              return $this->loop_query( $query_args, $widget );
          }, 10, 2 );
      }
      // Make the Logout redirect go to our my account widget page instead of the set My Account Page.
      add_action( 'init', [ $this, 'elementor_wc_my_account_logout' ], 5 );
  }

    /**
     * Register Ajax Actions.
     *
     * Registers ajax action used by the Editor js.
     *
     * @since 3.5.0
     *
     * @param Ajax $ajax
     */
    public function register_ajax_actions( Ajax $ajax ) {
        // `woocommerce_update_page_option` is called in the editor save-show-modal.js.
        $ajax->register_ajax_action( 'twbb_woocommerce_update_page_option', [ $this, 'update_page_option' ] );
        $ajax->register_ajax_action( 'twbb_woocommerce_mock_notices', [ $this, 'woocommerce_mock_notices' ] );
    }

    public function woocommerce_mock_notices( $data ) {
        if ( in_array( 'wc_error', $data['notice_elements'], true ) ) {
            $notice_message = sprintf(
                '%1$s <a href="#" class="wc-backward">%2$s</a>',
                esc_html__( 'This is how an error notice would look.', 'tenweb-builder' ),
                esc_html__( 'Here\'s a link', 'tenweb-builder' )
            );
            wc_add_notice( $notice_message, 'error' );
        }

        if ( in_array( 'wc_message', $data['notice_elements'], true ) ) {
            $notice_message = sprintf(
                '<a href="#" tabindex="1" class="button wc-forward">%1$s</a> %2$s <a href="#" class="restore-item">%3$s</a>',
                esc_html__( 'Button', 'tenweb-builder' ),
                esc_html__( 'This is what a WooCommerce message notice looks like.', 'tenweb-builder' ),
                esc_html__( 'Here\'s a link', 'tenweb-builder' )
            );
            wc_add_notice( $notice_message, 'success' );
        }

        if ( in_array( 'wc_info', $data['notice_elements'], true ) ) {
            $notice_message = sprintf(
                '<a href="#" tabindex="1" class="button wc-forward">%1$s</a> %2$s',
                esc_html__( 'Button', 'tenweb-builder' ),
                esc_html__( 'This is how WooCommerce provides an info notice.', 'tenweb-builder' )
            );
            wc_add_notice( $notice_message, 'notice' );
        }

        return '<div class="woocommerce-notices-wrapper">' . wc_print_notices( true ) . '</div>';
    }

  /**
   * Is active
   *
   * @return bool
   */
  private function is_active() {
    return class_exists('woocommerce');
  }

    /**
     * Is Preview
     *
     * Helper to check if we are doing either:
     * - Viewing the WP Preview page - also used as the Elementor preview page when clicking "Preview Changes" in the editor
     * - Viewing the page in the editor, but not the active page being edited e.g. if you click Edit Header while editing a page
     *
     * @since 3.7.0
     *
     * @return bool
     */
    public static function is_preview() {
        return \Elementor\Plugin::instance()->preview->is_preview_mode() || is_preview();
    }

  /**
   * Register wc_hooks
   * Include woocommerce php file and run WC Session
   */
  public function register_wc_hooks() {
    WC()->frontend_includes();
    if ( !WC()->session ) {
      $session_class = apply_filters('woocommerce_session_handler', 'WC_Session_Handler');
      WC()->session = new $session_class();
      WC()->session->init();
    }
  }

  public function menu_cart_fragments( $fragments ) {
      $all_fragments = [];
      if ( ! isset( $_POST['_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['_nonce'] ), self::MENU_CART_FRAGMENTS_ACTION ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, it's used only for nonce verification

          wp_send_json( [] );
      }

      $templates = self::_unstable_get_super_global_value( $_POST, 'templates' );

      if ( ! is_array( $templates ) ) {

          wp_send_json( [] );
      }

      if ( 'true' === self::_unstable_get_super_global_value( $_POST, 'is_editor' ) ) {
          \Elementor\Plugin::instance()->editor->set_edit_mode( true );
      }

      foreach ( $templates as $id ) {
          $this->get_all_fragments( $id, $all_fragments );
      }

      wp_send_json( [ 'fragments' => $all_fragments ] );
  }

    public function get_all_fragments( $id, &$all_fragments ) {
        $fragments_in_document = $this->get_fragments_in_document( $id );

        if ( $fragments_in_document ) {
            $all_fragments += $fragments_in_document;
        }
    }

    /**
     * Get Fragments In Document.
     *
     * A general function that will return any needed fragments for a Post.
     *
     * @since 3.7.0
     * @access public
     *
     * @param int $id
     *
     * @return mixed $fragments
     */
    public function get_fragments_in_document( $id ) {
        $document = \Elementor\Plugin::instance()->documents->get( $id );

        if ( ! is_object( $document ) ) {
            return false;
        }

        $fragments = [];

        $data = $document->get_elements_data();

        \Elementor\Plugin::instance()->db->iterate_data(
            $data,
            $this->get_fragments_handler( $fragments )
        );

        return ! empty( $fragments ) ? $fragments : false;
    }

    public function get_fragments_handler( array &$fragments ) {
        return function ( $element ) use ( &$fragments ) {
            if ( ! isset( $element['widgetType'] ) ) {
                return;
            }

            $fragment_data = $this->get_fragment_data( $element );
            $total_fragments = count( $fragment_data );

            for ( $i = 0; $i < $total_fragments; $i++ ) {
                if( isset( $fragment_data['selector'] ) && isset( $fragment_data['selector'][ $i ] ) && $fragment_data['html'][$i] ) {
                    $fragments[$fragment_data['selector'][$i]] = $fragment_data['html'][$i];
                }
            }
        };
    }

    /**
     * Empty Cart Fragments
     *
     * When the Cart is emptied, the selected 'Empty Cart Template' needs to be added as an item
     * in the WooCommerce `$fragments` array, so that WC will push the custom Template content into the DOM.
     * This is done to prevent the need for a page refresh after the cart is cleared.
     *
     * @since 3.7.0
     *
     * @param array $fragments
     * @return array
     */
    public function empty_cart_fragments( $fragments ) {
        // Only do this when the cart is empty.
        if ( WC()->cart->get_cart_contents_count() !== 0 ) {
            return $fragments;
        }
        //phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.url_to_postid_url_to_postid
        $document = \Elementor\Plugin::instance()->documents->get( url_to_postid( wp_get_referer() ) );

        if ( is_object( $document ) ) {
            $data = $document->get_elements_data();

            \Elementor\Plugin::instance()->db->iterate_data( $data, function( $element ) use ( &$fragments ) {
                if (
                    isset( $element['widgetType'] )
                    && 'twbb_woocommerce-cart' === $element['widgetType']
                    && ( isset( $element['settings']['additional_template_switch'] ) && 'active' === $element['settings']['additional_template_switch'] )
                    && ( isset( $element['settings']['additional_template_select'] ) && 0 < $element['settings']['additional_template_select'] )
                ) {
                    $fragments[ 'div.elementor-element-' . $element['id'] . ' .elementor-widget-container' ] = '<div class="elementor-widget-container">' . do_shortcode( '[elementor-template id="' . $element['settings']['additional_template_select'] . '"]' ) . '</div>';
                }
            } );
        }

        return $fragments;
    }

    private function get_fragment_data( $element ) {
        $fragment_data = [];

        if ( 'twbb_woocommerce-menu-cart' === $element['widgetType'] ) {
            ob_start();
            self::render_menu_cart_toggle_button( $element['settings'] );
            $fragment_data['html'][] = ob_get_clean();

            $fragment_data['selector'][] = 'div.elementor-element-' . $element['id'] . ' div.twbb_menu-cart__toggle';
        }

        return $fragment_data;
    }

  public function maybe_init_cart() {
    $has_cart = is_a(WC()->cart, 'WC_Cart');
    if ( !$has_cart ) {
      $session_class = apply_filters('woocommerce_session_handler', 'WC_Session_Handler');
      WC()->session = new $session_class();
      WC()->session->init();
      WC()->cart = new \WC_Cart();
      WC()->customer = new \WC_Customer(get_current_user_id(), TRUE);
    }
  }

  public function enqueue_woocommerce_scripts() {
    // In preview mode it's not a real Product page.
    if ( Templates::get_instance()->is_elementor_template_type() && self::get_preview_product() ) {
      add_filter('body_class', [ $this, 'wc_body_class' ]);
      if ( current_theme_supports('wc-product-gallery-zoom') ) {
        wp_enqueue_script('zoom');
      }
      if ( current_theme_supports('wc-product-gallery-slider') ) {
        wp_enqueue_script('flexslider');
      }
      if ( current_theme_supports('wc-product-gallery-lightbox') ) {
        wp_enqueue_script('photoswipe-ui-default');
        wp_enqueue_style('photoswipe-default-skin');
        add_action('wp_footer', 'woocommerce_photoswipe');
      }
      wp_enqueue_script('wc-single-product');
      wp_enqueue_style('photoswipe');
      wp_enqueue_style('photoswipe-default-skin');
      wp_enqueue_style('woocommerce_prettyPhoto_css');
    }
  }

  function wc_body_class( $classes = [] ) {
    $classes = (array) $classes;
    $classes[] = 'woocommerce';
    $classes[] = 'woocommerce-page';

    return array_unique($classes);
  }

  public static function render_menu_cart_toggle_button( $settings ) {
      if ( null === WC()->cart ) {
          return;
      }
      $product_count = WC()->cart->get_cart_contents_count();
      $sub_total = WC()->cart->get_cart_subtotal();
      $icon = ! empty( $settings['icon'] ) ? $settings['icon'] : 'cart-medium';
      ?>
      <div class="twbb_menu-cart__toggle elementor-button-wrapper">
          <a id="twbb_menu-cart__toggle_button" href="#" class="twbb_menu-cart__toggle_button elementor-button elementor-size-sm" aria-expanded="false">
              <span class="elementor-button-text"><?php echo $sub_total; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
              <span class="elementor-button-icon">
					<span class="elementor-button-icon-qty" data-counter="<?php echo esc_attr( $product_count ); ?>"><?php echo $product_count; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<?php
                    self::render_menu_icon( $settings, $icon );
                    ?>
					<span class="elementor-screen-only"><?php esc_html_e( 'Cart', 'tenweb-builder' ); ?></span>
				</span>
          </a>
      </div>
      <?php
	}

  public static function render_menu_cart($settings) {
      if ( null === WC()->cart ) {
          return;
      }

      $widget_cart_is_hidden = apply_filters( 'woocommerce_widget_cart_is_hidden', false );
      $is_edit_mode = \Elementor\Plugin::instance()->editor->is_edit_mode();
      ?>
      <div class="twbb_menu-cart__wrapper">
          <?php if ( ! $widget_cart_is_hidden ) : ?>
              <div class="twbb_menu-cart__toggle_wrapper">
                  <div class="twbb_menu-cart__container elementor-lightbox" aria-hidden="true">
                      <div class="twbb_menu-cart__main" aria-hidden="true">
                          <?php self::render_menu_cart_close_button( $settings ); ?>
                          <div class="widget_shopping_cart_content">
                              <?php if ( $is_edit_mode ) {
                                  woocommerce_mini_cart();
                              } ?>
                          </div>
                      </div>
                  </div>
                  <?php self::render_menu_cart_toggle_button( $settings ); ?>
              </div>
          <?php endif; ?>
      </div> <!-- close twbb_menu-cart__wrapper -->
      <?php
  }

    public static function render_menu_cart_close_button( $settings ) {
        $has_custom_icon = ! empty( $settings['close_cart_icon_svg']['value'] ) && 'yes' === $settings['close_cart_button_show'];
        $toggle_button_class = 'twbb_menu-cart__close-button';
        if ( $has_custom_icon ) {
            $toggle_button_class .= '-custom';
        }
        ?>
        <div class="<?php echo sanitize_html_class( $toggle_button_class ); ?>">
            <?php
            if ( $has_custom_icon ) {
                Icons_Manager::render_icon( $settings['close_cart_icon_svg'], [
                    'class' => 'e-close-cart-custom-icon',
                    'aria-hidden' => 'true',
                ] );
            }
            ?>
        </div>
        <?php
    }

    public static function render_menu_icon( $settings, string $icon ) {
        if ( ! empty( $settings['icon'] ) && 'custom' === $settings['icon'] ) {
            self::render_custom_menu_icon( $settings );
        } else {
            Icons_Manager::render_icon( [
                'library' => 'eicons',
                'value' => 'eicon-' . $icon,
            ] );
        }
    }

    private static function render_custom_menu_icon( $settings ) {
        if ( empty( $settings['menu_icon_svg'] ) ) {
            echo '<i class="fas fa-shopping-cart"></i>'; // Default Custom icon.
        } else {
            Icons_Manager::render_icon( $settings['menu_icon_svg'], [
                'class' => 'e-toggle-cart-custom-icon',
                'aria-hidden' => 'true',
            ] );
        }
    }

  /*
   * for getting mini-cart.php template
   */
    public function twbb_woocommerce_locate_template( $template, $template_name, $template_path ){

        if ( 'cart/mini-cart.php' !== $template_name ) {
            return $template;
        }

        $plugin_path = plugin_dir_path( __DIR__ ) . 'widgets/woocommerce/menu-cart/template/';
        if ( file_exists( $plugin_path . $template_name ) ) {
            $template = $plugin_path . $template_name;
        }

        return $template;
    }

    /**
     * WooCommerce/WordPress widget(s), some of the widgets have css classes that used by final selectors.
     * before this filter, all those widgets were warped by `.elementor-widget-container` without chain original widget
     * classes, now they will be warped by div with the original css classes.
     *
     * @param array $default_widget_args
     * @param \Elementor\Widget_WordPress $widget
     *
     * @return array $default_widget_args
     */
    public function woocommerce_wordpress_widget_css_class( $default_widget_args, $widget ) {
        $widget_instance = $widget->get_widget_instance();

        if ( ! empty( $widget_instance->widget_cssclass ) ) {
            $default_widget_args['before_widget'] .= '<div class="' . $widget_instance->widget_cssclass . '">';
            $default_widget_args['after_widget'] .= '</div>';
        }

        return $default_widget_args;
    }


  /**
   * Get widgets.
   *
   * @return array
   */
  private function get_widgets() {
    return [
      'products',
      'product-page',
      'product-price',
      'product-title',
      'product-rating',
      'product-images',
      'product-content',
      'product-related',
      'product-data-tabs',
      'product-meta',
      'product-stock',
      'product-short-description',
      'product-additional-information',
      'categories',
      'breadcrumb',
      'add-to-cart',
      'menu-cart',
      'product-add-to-cart',
      'product-upsell',
      'checkout',
//      'price-filter',
      'my-account',
      'cart',
      'purchase-summary',
      'notices'
    ];
  }

  /**
   * Register widgets.
   */
  public function register_widgets() {
    if ( $this->is_active() ) {
      require_once TWBB_DIR . '/widgets/woocommerce/base-widget.php';
      foreach ( $this->get_widgets() as $widget ) {
        $file = TWBB_DIR . '/widgets/woocommerce/' . $widget . '/controller.php';
        if ( is_file($file) ) {
          require_once $file;//phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingVariable
        }
      }
    }
  }

  public function after_enqueue_styles() {
    wp_enqueue_style('twbb-woocommerce-main-style', TWBB_URL . '/widgets/woocommerce/assets/style.css', [], TWBB_VERSION);
    wp_enqueue_style('twbb-woocommerce-products-style', TWBB_URL . '/widgets/woocommerce/products/assets/style.css', [], TWBB_VERSION);
    wp_enqueue_style('twbb-woocommerce-product-rating-style', TWBB_URL . '/widgets/woocommerce/product-rating/assets/style.css', [], TWBB_VERSION);
    wp_enqueue_style('twbb-woocommerce-menu-cart-style', TWBB_URL . '/widgets/woocommerce/menu-cart/assets/style.css', [], TWBB_VERSION);
    wp_enqueue_style('twbb-woocommerce-add-to-cart-style', TWBB_URL . '/widgets/woocommerce/add-to-cart/assets/style.css', [], TWBB_VERSION);
    wp_enqueue_style('twbb-woocommerce-categories-style', TWBB_URL . '/widgets/woocommerce/categories/assets/style.css', [], TWBB_VERSION);
    wp_enqueue_style('twbb-woocommerce-product-related-style', TWBB_URL . '/widgets/woocommerce/product-related/assets/style.css', [], TWBB_VERSION);
    wp_enqueue_style('twbb-woocommerce-product-meta-style', TWBB_URL . '/widgets/woocommerce/product-meta/assets/style.css', [], TWBB_VERSION);
    wp_enqueue_style('twbb-woocommerce-product-add-to-cart-style', TWBB_URL . '/widgets/woocommerce/product-add-to-cart/assets/style.css', [], TWBB_VERSION);
    wp_enqueue_style('twbb-woocommerce-product-upsell-style', TWBB_URL . '/widgets/woocommerce/product-upsell/assets/style.css', [], TWBB_VERSION);
    wp_enqueue_style('twbb-woocommerce-product-additional-information', TWBB_URL . '/widgets/woocommerce/product-additional-information/assets/style.css', [], TWBB_VERSION);
    wp_enqueue_style('twbb-woocommerce-pricing-filter-style', TWBB_URL . '/widgets/woocommerce/price-filter/assets/style.css', [], TWBB_VERSION);
    wp_enqueue_style('twbb-woocommerce-my-account-style', TWBB_URL . '/widgets/woocommerce/my-account/assets/style.css', [], TWBB_VERSION);
    wp_enqueue_style('twbb-woocommerce-cart-style', TWBB_URL . '/widgets/woocommerce/cart/assets/style.css', [], TWBB_VERSION);
    wp_enqueue_style('twbb-woocommerce-checkout-style', TWBB_URL . '/widgets/woocommerce/checkout/assets/style.css', [], TWBB_VERSION);
    wp_enqueue_style('twbb-woocommerce-notices-style', TWBB_URL . '/widgets/woocommerce/notices/assets/style.css', [], TWBB_VERSION);
    wp_enqueue_style('twbb-woocommerce-purchase-summary-style', TWBB_URL . '/widgets/woocommerce/purchase-summary/assets/style.css', [], TWBB_VERSION);
  }

  public function after_enqueue_scripts() {
    wp_enqueue_script(TWBB_PREFIX . '-pro-features-frontend-script', TWBB_URL . '/pro-features/assets/js/frontend.js', [ TWBB_PREFIX . '-pro-features-webpack-runtime', 'elementor-frontend-modules' ], TWBB_VERSION, TRUE);
    wp_register_script('twbb-woocommerce-base', TWBB_URL . '/widgets/woocommerce/assets/script.js', ['elementor-frontend-modules'], TWBB_VERSION, TRUE);
    wp_enqueue_script('twbb-woocommerce-products-script', TWBB_URL . '/widgets/woocommerce/products/assets/script.js', [], TWBB_VERSION, TRUE);
    wp_enqueue_script('twbb-woocommerce-products-admin-scripts', TWBB_URL . '/widgets/woocommerce/products/assets/admin-scripts.js', [], TWBB_VERSION, TRUE);
    wp_enqueue_script('twbb-woocommerce-product-add-to-cart-script', TWBB_URL . '/widgets/woocommerce/product-add-to-cart/assets/script.js', [], TWBB_VERSION, TRUE);
    wp_enqueue_script('twbb-woocommerce-menu-cart-script', TWBB_URL . '/widgets/woocommerce/menu-cart/assets/script.js', ['wc-cart-fragments'], TWBB_VERSION, TRUE);
    wp_enqueue_script('twbb-woocommerce-product-data-tabs-script', TWBB_URL . '/widgets/woocommerce/product-data-tabs/assets/script.js', [], TWBB_VERSION, TRUE);
    wp_enqueue_script('twbb-woocommerce-product-page-script', TWBB_URL . '/widgets/woocommerce/product-page/assets/script.js', [], TWBB_VERSION, TRUE);
    wp_enqueue_script('twbb-woocommerce-product-images-script', TWBB_URL . '/widgets/woocommerce/product-images/assets/script.js', [], TWBB_VERSION, TRUE);
    wp_enqueue_script('twbb-woocommerce-pricing-filter-script', TWBB_URL . '/widgets/woocommerce/price-filter/assets/script.js', [], TWBB_VERSION);
    wp_enqueue_script('twbb-woocommerce-my-account-script', TWBB_URL . '/widgets/woocommerce/my-account/assets/script.js', ['twbb-woocommerce-base'], TWBB_VERSION, TRUE);
    wp_enqueue_script('twbb-woocommerce-cart-script', TWBB_URL . '/widgets/woocommerce/cart/assets/script.js', ['twbb-woocommerce-base'], TWBB_VERSION, TRUE);
    wp_enqueue_script('twbb-woocommerce-checkout-script', TWBB_URL . '/widgets/woocommerce/checkout/assets/script.js', ['twbb-woocommerce-base'], TWBB_VERSION, TRUE);
    wp_enqueue_script('twbb-woocommerce-notices-script', TWBB_URL . '/widgets/woocommerce/notices/assets/script.js', [], TWBB_VERSION, TRUE);
    wp_enqueue_script('twbb-woocommerce-purchase-summary-script', TWBB_URL . '/widgets/woocommerce/purchase-summary/assets/script.js', [], TWBB_VERSION, TRUE);
  }

    /**
     * Load Widget Before WooCommerce Ajax.
     *
     * When outputting the complex WooCommerce shortcodes (which we use in our widgets) e.g. Checkout, Cart, etc. WC
     * immediately does more ajax calls and retrieves updated html fragments based on the data in the forms that may
     * be autofilled by the current user's browser e.g. the Payment section holding the "Place order" button.
     *
     * This function runs before these ajax calls. Using the `elementorPageId` and `elementorWidgetId` querystring
     * appended to the forms `_wp_http_referer` url field, or the referer page ID, it loads the relevant Elementor widget.
     * The rendered Elementor widget replaces the default WooCommerce template used to refresh WooCommerce elements in the page.
     *
     * This is necessary for example in the Checkout Payment section where we modify the Terms & Conditions text
     * using settings from the widget or when updating shipping methods on the Cart.
     *
     * @since 3.5.0
     */
    public function load_widget_before_wc_ajax() {
        // Make sure is a WooCommerce ajax call.
        //phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $wc_ajax = self::_unstable_get_super_global_value( $_GET, 'wc-ajax' );
        if ( ! $wc_ajax ) {
            return;
        }

        // Only handle relevant WC AJAX calls
        if ( ! in_array( $wc_ajax, [ 'update_order_review', 'update_shipping_method' ], true ) ) {
            return;
        }

        // Security checks.
        switch ( $wc_ajax ) {
            case 'update_order_review':
                check_ajax_referer( 'update-order-review', 'security' );
                break;
            case 'update_shipping_method':
                check_ajax_referer( 'update-shipping-method', 'security' );
                break;
        }

        $page_id = false;
        $widget_id = false;

        // Try to get the `$page_id` and `$widget_id` we added as a query string to `_wp_http_referer` in `post_data`.
        // This is only available when a form is submitted.
        $raw_post_data = self::_unstable_get_super_global_value( $_POST, 'post_data' );
        if ( $raw_post_data ) {
            $raw_post_data = html_entity_decode( $raw_post_data );

            parse_str( $raw_post_data, $post_data );

            if ( isset( $post_data['_wp_http_referer'] ) ) {
                $wp_http_referer = wp_unslash( $post_data['_wp_http_referer'] );

                $wp_http_referer_query_string = wp_parse_url( $wp_http_referer, PHP_URL_QUERY );
                parse_str( $wp_http_referer_query_string, $wp_http_referer_query_string );

                if ( isset( $wp_http_referer_query_string['elementorPageId'] ) ) {
                    $page_id = $wp_http_referer_query_string['elementorPageId'];
                }

                if ( isset( $wp_http_referer_query_string['elementorWidgetId'] ) ) {
                    $widget_id = $wp_http_referer_query_string['elementorWidgetId'];
                }
            }
        }

        if ( ! $page_id ) {
            $page_id = url_to_postid( wp_get_referer() );//phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.url_to_postid_url_to_postid
        }

        // Bail if there is no `$page_id`.
        if ( ! $page_id ) {
            return;
        }

        // Get Elementor document from `$page_id`.
        $document = \Elementor\Plugin::instance()->documents->get_doc_for_frontend( $page_id );

        // Bail if not Elementor page.
        if ( ! $document ) {
            return;
        }

        // Setup $page_id as the WP global $post, so is available to our widgets.
        $post = get_post( $page_id, OBJECT );
        setup_postdata( $post );

        $widget_data = false;
        if ( $widget_id ) {
            // If we did manage to pass `$widget_id` to this ajax call we get the widget data by its ID.
            $widget_data = Utils::find_element_recursive( $document->get_elements_data(), $widget_id );
        } else {
            // If we didn't manage to pass `$widget_id` to this ajax call we use this alternate method and get the first
            // of the type of widget used on the WC endpoint pages responsible for these ajax calls - cart or checkout widget.
            $woocommerce_widgets = [ TWBB_PREFIX . '_woocommerce-cart', TWBB_PREFIX . '_woocommerce-checkout-page' ];

            $document_data = $document->get_elements_data();
            \Elementor\Plugin::instance()->db->iterate_data( $document_data, function( $element ) use ( $woocommerce_widgets, &$widget_data ) {
                if ( $widget_data && ( ! isset( $element['widgetType'] ) || ! in_array( $element['widgetType'], $woocommerce_widgets, true ) ) ) {
                    return;
                }
                $widget_data = $element;
            } );
        }

        // If we found a widget then run `add_render_hooks()` widget method.
        if ( $widget_data ) {
            $widget_instance = \Elementor\Plugin::instance()->elements_manager->create_element_instance( $widget_data );
            if ( method_exists( $widget_instance, 'add_render_hooks' ) ) {
                $widget_instance->add_render_hooks();
            }
        }
    }

    public static function _unstable_get_super_global_value( $super_global, $key ) {
        if ( ! isset( $super_global[ $key ] ) ) {
            return null;
        }

        if ( $_FILES === $super_global ) {
            $super_global[ $key ]['name'] = sanitize_file_name( $super_global[ $key ]['name'] );
            return $super_global[ $key ];
        }

        return wp_kses_post_deep( wp_unslash( $super_global[ $key ] ) );
    }

    public function maybe_define_woocommerce_checkout() {
        $woocommerce_purchase_summary_page_id = get_option( 'elementor_woocommerce_purchase_summary_page_id' );

        if ( $woocommerce_purchase_summary_page_id && intval( $woocommerce_purchase_summary_page_id ) === get_queried_object_id() ) {
            if ( ! defined( 'WOOCOMMERCE_CHECKOUT' ) ) {
                define( 'WOOCOMMERCE_CHECKOUT', true );
            }
        }
    }
    public function elementor_woocommerce_checkout_login_user() {
        if ( is_user_logged_in() ) {
            wp_logout();
        }

        $error = false;
        $error_message = '';

        if ( ! wp_verify_nonce( self::_unstable_get_super_global_value( $_POST, 'nonce' ), 'woocommerce-login' ) ) {
            $error = true;
            $error_message = sprintf(
            /* translators: 1: Bold text opening tag, 2: Bold text closing tag. */
                esc_html__( '%1$sError:%2$s The nonce security check didn’t pass. Please reload the page and try again. You may want to try clearing your browser cache as a last attempt.', 'tenweb-builder' ),
                '<strong>',
                '</strong>'
            );
        } else {
            $info = [
                'user_login' => trim( self::_unstable_get_super_global_value( $_POST, 'username' ) ),
                'user_password' => $_POST['password'] ?? '', // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, A password should not be sanitized.
                'remember' => self::_unstable_get_super_global_value( $_POST, 'remember' ),
            ];

            $user_signon = wp_signon( $info, false );

            if ( is_wp_error( $user_signon ) ) {
                $error = true;
                $error_message = $user_signon->get_error_message();
            }
        }

        if ( $error ) {
            wc_add_notice(
                $error_message,
                'error'
            );
            $response = [
                'logged_in' => false,
                'message' => wc_print_notices( true ),
            ];
        } else {
            $response = [ 'logged_in' => true ];
        }

        echo wp_json_encode( $response );
        wp_die();
    }

    public function init_site_settings( \Elementor\Core\Kits\Documents\Kit $kit ) {
        $kit->register_tab( 'settings-woocommerce', Settings_Woocommerce::class );
    }

    /**
     * Add Update Kit Settings Hooks
     *
     * Add hooks that update the corresponding kit setting when the WooCommerce option is updated.
     */
    public function add_update_kit_settings_hooks() {
        add_action( 'update_option_woocommerce_cart_page_id', function( $old_value, $value ) {
            \Elementor\Plugin::instance()->kits_manager->update_kit_settings_based_on_option( 'woocommerce_cart_page_id', $value );
        }, 10, 2 );

        add_action( 'update_option_woocommerce_checkout_page_id', function( $old_value, $value ) {
            \Elementor\Plugin::instance()->kits_manager->update_kit_settings_based_on_option( 'woocommerce_checkout_page_id', $value );
        }, 10, 2 );

        add_action( 'update_option_woocommerce_myaccount_page_id', function( $old_value, $value ) {
            \Elementor\Plugin::instance()->kits_manager->update_kit_settings_based_on_option( 'woocommerce_myaccount_page_id', $value );
        }, 10, 2 );

        add_action( 'update_option_woocommerce_terms_page_id', function( $old_value, $value ) {
            \Elementor\Plugin::instance()->kits_manager->update_kit_settings_based_on_option( 'woocommerce_terms_page_id', $value );
        }, 10, 2 );
    }

    /**
     * Update Page Option.
     *
     * Ajax action can be used to update any WooCommerce option.
     *
     * @since 3.5.0
     *
     * @param array $data
     */
    public function update_page_option( $data ) {
        $is_admin = current_user_can( 'manage_options' );
        $is_shop_manager = current_user_can( 'manage_woocommerce' );
        $is_allowed = $is_admin || $is_shop_manager;

        if ( ! $is_allowed ) {
            return new \WP_Error( 401 );
        }

        $allowed_options = [
            'woocommerce_checkout_page_id',
            'woocommerce_cart_page_id',
            'woocommerce_myaccount_page_id',
            'elementor_woocommerce_purchase_summary_page_id',
        ];

        $option_name = $data['option_name'];
        $post_id = absint( $data['editor_post_id'] );

        if ( ! in_array( $option_name, $allowed_options, true ) ) {
            return new \WP_Error( 400 );
        }

        update_option( $option_name, $post_id );
    }

    public function e_notices_css( $classes ) {
        if ( $this->should_load_wc_notices_styles() ) {
            wp_enqueue_style(
                'e-woocommerce-notices',
                TWBB_URL . '/pro-features/' . 'assets/css/woocommerce-notices.css',
                [],
                TWBB_VERSION
            );
        }
    }

    public function e_notices_body_classes( $classes ) {
        if ( $this->should_load_wc_notices_styles() ) {
            foreach ( $this->woocommerce_notices_elements as $notice_element ) {
                $classes[] = 'e-' . str_replace( '_', '-', $notice_element ) . '-notice';
            }
        }

        return $classes;
    }

    /**
     * Should load WC Notices Styles
     *
     * Determine if we should load the WooCommerce notices CSS.
     * It should only load:
     * - When we are in the Editor, regardless if any notices have been activated.
     * - If WooCoomerce is active.
     * - When we are on the front end, if at least one notice is activated.
     *
     * It should not load in WP Admin.
     *
     * @return boolean
     */
    private function should_load_wc_notices_styles() {
        $woocommerce_active = $this->is_active();
        $is_editor = self::_unstable_get_super_global_value( $_GET, 'elementor-preview' );//phpcs:ignore WordPress.Security.NonceVerification.Recommended

        // Editor checks.
        if ( $woocommerce_active && $is_editor ) {
            return true;
        }

        $kit = \Elementor\Plugin::instance()->kits_manager->get_active_kit_for_frontend();
        $this->woocommerce_notices_elements = is_array( $kit->get_settings_for_display( 'woocommerce_notices_elements' ) ) ? $kit->get_settings_for_display( 'woocommerce_notices_elements' ) : [];

        // Front end checks.
        if (
            0 < count( $this->woocommerce_notices_elements ) // At least one notice has been activated.
            && $woocommerce_active // WooCommerce is active.
            && ( ! is_admin() || $is_editor ) // We are not in WP Admin.
        ) {
            return true;
        }

        return false;
    }

    /**
     * Add Localize Data
     *
     * Makes `woocommercePages` available with the page name and the associated post ID for use with the various
     * widgets site settings modal.
     *
     * @param $settings
     * @return array
     */
    public function add_localize_data( $settings ) {
        $settings['woocommerce']['woocommercePages'] = [
            'checkout' => wc_get_page_id( 'checkout' ),
            'cart' => wc_get_page_id( 'cart' ),
            'myaccount' => wc_get_page_id( 'myaccount' ),
            'purchase_summary' => get_option( 'elementor_woocommerce_purchase_summary_page_id' ),
        ];

        return $settings;
    }

    public function register_admin_fields( Settings $settings ) {
        $settings->add_section( Settings::TAB_INTEGRATIONS, 'woocommerce', [
            'callback' => function() {
                echo '<hr><h2>' . esc_html__( 'WooCommerce', 'tenweb-builder' ) . '</h2>';
            },
            'fields' => [
                self::OPTION_NAME_USE_MINI_CART => [
                    'label' => esc_html__( 'Mini Cart Template', 'tenweb-builder' ),
                    'field_args' => [
                        'type' => 'select',
                        'std' => 'initial',
                        'options' => [
                            'initial' => '', // Relevant until either menu-cart widget is used or option is explicitly set to 'no'.
                            'no' => esc_html__( 'Disable', 'tenweb-builder' ),
                            'yes' => esc_html__( 'Enable', 'tenweb-builder' ),
                        ],
                        'desc' => esc_html__( 'Set to `Disable` in order to use your Theme\'s or WooCommerce\'s mini-cart template instead of Elementor\'s.', 'tenweb-builder' ),
                    ],
                ],
            ],
        ] );
    }

    public function localized_settings_frontend( $settings ) {
        $has_cart = is_a( WC()->cart, 'WC_Cart' );

        if ( $has_cart ) {
            $settings['woocommerce']['menu_cart'] = [
                'cart_page_url' => wc_get_cart_url(),
                'checkout_page_url' => wc_get_checkout_url(),
                'fragments_nonce' => wp_create_nonce( self::MENU_CART_FRAGMENTS_ACTION ),
            ];
        }
        return $settings;
    }

    /**
     * Localize Added To Cart On Product Single
     *
     * WooCommerce doesn't trigger `added_to_cart` event on its products single page which is required for us to
     * automatically open our Menu Cart if the settings is chosen. We make the `productAddedToCart` setting
     * available that we can use in the Menu Cart js to check if a product has just been added.
     *
     */
    public function localize_added_to_cart_on_product_single() {
        add_filter( 'elementor_tenweb/frontend/localize_settings', function ( $settings ) {
            $settings['woocommerce']['productAddedToCart'] = true;
            return $settings;
        } );
    }

    /**
     * Products Query Sources Fragments.
     *
     * Since we introduced additional query sources to the Products Widget,
     * some of these query sources can now be used outside of the Single Product template.
     *
     * For example the Related Products and Cross-Sells.
     *
     * But now we'll need to make those sections also update when the Cart is updated. So
     * we'll do this by creating fragments for each of these.
     *
     * @since 3.7.0
     *
     * @param array $fragments
     *
     * @return array
     */
    public function products_query_sources_fragments( $fragments ) {
        if ( WC()->cart->get_cart_contents_count() !== 0 ) {
            //phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.url_to_postid_url_to_postid
            $document = \Elementor\Plugin::instance()->documents->get( url_to_postid( wp_get_referer() ) );

            if ( is_object( $document ) ) {
                $data = $document->get_elements_data();

                \Elementor\Plugin::instance()->db->iterate_data( $data, function( $element ) use ( &$fragments ) {
                    if (
                        isset( $element['widgetType'] )
                        && 'twbb_woocommerce-products' === $element['widgetType']
                    ) {
                        $settings = $element['settings'];
                        if ( isset( $settings[ Products_Renderer::QUERY_CONTROL_NAME . '_post_type' ] ) ) {
                            $query_type = $settings[ Products_Renderer::QUERY_CONTROL_NAME . '_post_type' ];
                            $query_types_to_check = [ 'related_products', 'upsells', 'cross_sells' ];

                            if ( in_array( $query_type, $query_types_to_check, true ) ) {
                                switch ( $query_type ) {
                                    case 'related_products':
                                        $content = self::get_products_related_content( $settings );
                                        break;
                                    case 'upsells':
                                        $content = self::get_upsells_content( $settings );
                                        break;
                                    case 'cross_sells':
                                        $content = self::get_cross_sells_content( $settings );
                                        break;
                                    default:
                                        $content = null;
                                }

                                if ( $content ) {
                                    $fragments[ 'div.elementor-element-' . $element['id'] . ' div.elementor-widget-container' ] = '<div class="elementor-widget-container">' . $content . '</div>';
                                }
                            }
                        }
                    }
                } );
            }
        } else {
            $fragments['div.elementor-widget-container .woocommerce .cross-sells'] = '<div class="cross-sells"></div>';

            $fragments['div.elementor-widget-container .woocommerce section.up-sells'] = '<section class="up-sells upsells products"></section>';
        }

        return $fragments;
    }

    /**
     * Get Products Related Content.
     *
     * A function to return content for the 'related' products query type in the Products widget.
     * This function is declared in the Module file so it can be accessed during a WC fragment refresh
     * and also be used in the Product widget's render method.
     *
     * @since 3.7.0
     * @access public
     *
     * @param array $settings
     *
     * @return mixed The content or false
     */
    public static function get_products_related_content( $settings ) {
        global $product;

        $product = wc_get_product();

        if ( ! $product ) {
            return;
        }

        return self::get_product_widget_content(
            $settings,
            'related_products',
            'woocommerce_product_related_products_heading',
            'products_related_title_text'
        );
    }

    /**
     * Get Upsells Content.
     *
     * A function to return content for the 'upsell' query type in the Products widget.
     * This function is declared in the Module file so it can be accessed during a WC fragment refresh
     * and also be used in the Product widget's render method.
     *
     * @since 3.7.0
     * @access public
     *
     * @param array $settings
     *
     * @return mixed The content or false
     */
    public static function get_upsells_content( $settings ) {
        return self::get_product_widget_content(
            $settings,
            'upsells',
            'woocommerce_product_upsells_products_heading',
            'products_upsells_title_text'
        );
    }

    /**
     * Get Cross Sells Content.
     *
     * A function to return content for the 'cross_sells' query type in the Products widget.
     * This function is declared in the Module file so it can be accessed during a WC fragment refresh
     * and also be used in the Product widget's render method.
     *
     * @since 3.7.0
     * @access public
     *
     * @param array $settings
     *
     * @return mixed The content or false
     */
    public static function get_cross_sells_content( $settings ) {
        return self::get_product_widget_content(
            $settings,
            'cross_sells',
            'woocommerce_product_cross_sells_products_heading',
            'products_cross_sells_title_text'
        );
    }

    public function e_cart_count_fragments( $fragments ) {
        $product_count = WC()->cart->get_cart_contents_count();

        $fragments['.twbb_menu-cart__toggle_button span.elementor-button-text'] = '<span class="elementor-button-text">' . WC()->cart->get_cart_subtotal() . '</span>';
        $fragments['.twbb_menu-cart__toggle_button span.elementor-button-icon-qty'] = '<span class="elementor-button-icon-qty" data-counter=' . $product_count . '>' . $product_count . '</span>';

        return $fragments;
    }

    /**
     * Elementor WC My Account Logout
     *
     * Programatically log out if $_REQUEST['twbb_wc_logout'] is set.
     * The $_REQUEST variables we have generated a custom logout URL for in the My Account menu.
     *
     */
    public function elementor_wc_my_account_logout() {
        //phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $twbb_wc_logout = self::_unstable_get_super_global_value( $_REQUEST, 'twbb_wc_logout' );
	      //phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $nonce = self::_unstable_get_super_global_value( $_REQUEST, '_wpnonce' );

        if ( $twbb_wc_logout && $nonce && wp_verify_nonce( $nonce, 'customer-logout' ) ) {
            wp_logout(); // Log the user out Programatically.
            wp_safe_redirect( esc_url( self::_unstable_get_super_global_value( $_REQUEST, 'twbb_my_account_redirect' ) ) ); // Redirect back to the widget page.
            exit;
        }
    }

    public function get_order_received_endpoint_url( $url, $endpoint, $value ) {
        $order_received_endpoint = get_option( 'woocommerce_checkout_order_received_endpoint', 'order-received' );

        if ( $order_received_endpoint === $endpoint ) {
            // option name same as in elementor because we use same option to save wc pages id's
            $woocommerce_purchase_summary_page_id = get_option( 'elementor_woocommerce_purchase_summary_page_id' );
            $order = wc_get_order( $value );

            if ( $woocommerce_purchase_summary_page_id && $order ) {
                $url = trailingslashit( trailingslashit( trailingslashit( get_permalink( $woocommerce_purchase_summary_page_id ) ) . $order_received_endpoint ) . $order->get_id() );
            }
        }

        return $url;
    }

    /**
     * Print Woocommerce Shipping Message
     *
     * Format the shipping messages that will be displayed on the Cart and Checkout Widgets.
     * This will add extra classes to those messages so that we can target certain messages
     * with certain style controls.
     *
     * @since 3.5.0
     *
     * @param string $html the original HTML from WC
     * @param string $classes the classes we will surround $html with
     * @return string the final formatted HTML that will be rendered
     */
    private function print_woocommerce_shipping_message( $html, $classes ) {
        return '<span class="' . wp_sprintf( '%s', $classes ) . '">' . $html . '</span>';
    }

    /**
     * Get Product Widget Content.
     *
     * A general function to create markup for the new query types in the Products widget.
     *
     * @since 3.7.0
     * @access private
     *
     * @param array $settings The widget settings.
     * @param string $type The query type to create content for.
     * @param string $title_hook The hook name to filter in the widget title.
     * @param string $title_key The control ID for the section title.
     *
     * @return mixed The content or false
     */
    private static function get_product_widget_content( $settings, $type, $title_hook, $title_key = '' ) {
        add_filter( $title_hook, function ( $heading ) use ( $settings, $title_key ) {
            $title_text = isset( $settings[ $title_key ] ) ? $settings[ $title_key ] : '';

            if ( ! empty( $title_text ) ) {
                return $title_text;
            }

            return $heading;
        }, 10, 1 );

        ob_start();

        $args = self::parse_product_widget_args( $settings, $type );

        if ( 'related_products' === $type ) {
            woocommerce_related_products( $args );
        } elseif ( 'upsells' === $type ) {
            woocommerce_upsell_display( $args['limit'], $args['columns'], $args['orderby'], $args['order'] );
        } else {
            /**
             * We need to wrap this content in the 'woocommerce' class for the layout to have the correct styling.
             * Because this will only be used as a separate widget on the Cart page,
             * the normal 'woocommerce' div from the cart widget will be closed before this content.
             */
            echo '<div class="woocommerce">';
            woocommerce_cross_sell_display( $args['limit'], $args['columns'], $args['orderby'], $args['order'] );
            echo '</div>';
        }

        $products_html = ob_get_clean();

        remove_filter( $title_hook, function(){}, 10 );

        if ( $products_html ) {
            $products_html = str_replace( '<ul class="products', '<ul class="products elementor-grid', $products_html );

            return $products_html;
        }

        return false;
    }

    /**
     * Parse Product Widget Args.
     *
     * A general function to construct an arguments array for the new query types in the
     * Products widget according to the widget's settings.
     * These arguments will later be passed to the WooCommerce template functions.
     *
     * @since 3.7.0
     * @access private
     *
     * @param array $settings The widget settings.
     * @param string $type The query type to create arguments for.
     *
     * @return array $args
     */
    private static function parse_product_widget_args( $settings, $type = 'related_products' ) {
        $limit_key = 'related_products' === $type ? 'posts_per_page' : 'limit';
        $query_name = Products_Renderer::QUERY_CONTROL_NAME;

        $args = [
            $limit_key => '-1',
            'columns' => ! empty( $settings['columns'] ) ? $settings['columns'] : 4,
            'orderby' => ! empty( $settings[ "{$query_name}_orderby" ] ) ? $settings[ "{$query_name}_orderby" ] : 'rand',
            'order' => ! empty( $settings[ "{$query_name}_order" ] ) ? $settings[ "{$query_name}_order" ] : 'desc',
        ];

        if ( ! empty( $settings['rows'] ) ) {
            $args[ $limit_key ] = $args['columns'] * $settings['rows'];
        }

        return $args;
    }

    public function loop_query( $query_args, $widget ) {
        if ( ! $this->is_product_query( $widget ) ) {
            return $query_args;
        }

        return $this->parse_loop_query_args( $widget );
    }

    private function is_product_query( $widget ) {
        $widget_config = $widget->get_config();

        return ( ! empty( $widget_config['is_loop'] ) && 'product' === $widget->get_current_skin_id() );
    }

    private function parse_loop_query_args( $widget ) {
        $settings = $this->adjust_setting_for_product_renderer( $widget );

        // For Products_Renderer.
        if ( ! isset( $GLOBALS['post'] ) ) {
            $GLOBALS['post'] = null; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
        }

        $shortcode = Products_Widget::get_shortcode_object( $settings );

        $query_args = $shortcode->parse_query_args();
        unset( $query_args['fields'] );

        return $query_args;
    }

    private function adjust_setting_for_product_renderer( $widget ) {
        $settings = $widget->get_settings_for_display();

        $query_name = $widget->get_query_name();

        $unique_query_settings = array_filter( $settings, function( $key ) use ( $query_name ) {
            return 0 === strpos( $key, $query_name );
        }, ARRAY_FILTER_USE_KEY );

        $query_settings = [];

        foreach ( $unique_query_settings as $key => $value ) {
            $query_settings[ 'query' . str_replace( $query_name, '', $key ) ] = $value;
        }

        $settings = array_merge( $settings, $query_settings );

        if ( isset( $settings['posts_per_page'] ) && isset( $settings['columns'] ) ) {
            $settings['rows'] = ceil( $settings['posts_per_page'] / $settings['columns'] );
        }

        $settings['paginate'] = 'yes';
        $settings['allow_order'] = 'no';
        $settings['show_result_count'] = 'no';
        $settings['query_fields'] = false;

        return $settings;
    }
}
