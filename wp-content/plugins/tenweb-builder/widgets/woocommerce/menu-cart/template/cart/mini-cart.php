<?php

defined( 'ABSPATH' ) || exit;

if ( !function_exists( 'twbb_render_cart_item' ) ) {
  function twbb_render_cart_item( $cart_item_key, $cart_item ) {
    $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
    $is_product_visible = ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_widget_cart_item_visible', TRUE, $cart_item, $cart_item_key));
    if ( !$is_product_visible ) {
      return;
    }
    $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);
    $product_price = apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key);
    $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
    ?>
    <div class="twbb_menu-cart__product woocommerce-cart-form__cart-item <?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?>">
      <div class="twbb_menu-cart__product-image product-thumbnail">
        <?php
        $thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key);
        if ( !$product_permalink ) :
          echo wp_kses_post($thumbnail);
        else :
          printf('<a href="%s">%s</a>', esc_url($product_permalink), wp_kses_post($thumbnail));
        endif;
        ?>
      </div>
      <div class="twbb_menu-cart__product-name product-name" data-title="<?php esc_attr_e('Product', 'tenweb-builder'); ?>">
        <?php
        if ( !$product_permalink ) :
          echo wp_kses_post(apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key) . '&nbsp;');
        else :
          echo wp_kses_post(apply_filters('woocommerce_cart_item_name', sprintf('<a href="%s">%s</a>', esc_url($product_permalink), $_product->get_name()), $cart_item, $cart_item_key));
        endif;
        do_action('woocommerce_after_cart_item_name', $cart_item, $cart_item_key);
        // Meta data.
        echo wc_get_formatted_cart_item_data($cart_item); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        ?>
      </div>
      <div class="twbb_menu-cart__product-price product-price" data-title="<?php esc_attr_e('Price', 'tenweb-builder'); ?>">
        <?php echo apply_filters('woocommerce_widget_cart_item_quantity', '<span class="quantity">' . sprintf('%s &times; %s', $cart_item['quantity'], $product_price) . '</span>', $cart_item, $cart_item_key); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
      </div>
      <div class="twbb_menu-cart__product-remove product-remove">
				<?php foreach ( [ 'twbb_remove_from_cart_button', 'remove_from_cart_button' ] as $class ) {
					echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						'<a href="%s" class="%s" aria-label="%s" data-product_id="%s" data-cart_item_key="%s" data-product_sku="%s"></a>',
						esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
						$class,
						__( 'Remove this item', 'tenweb-builder' ),
						esc_attr( $product_id ),
						esc_attr( $cart_item_key ),
						esc_attr( $_product->get_sku() )
					), $cart_item_key );
				} ?>
			</div>
		</div>
		<?php
	}
}

$cart_items = WC()->cart->get_cart();

if ( empty( $cart_items ) ) { ?>
	<div class="woocommerce-mini-cart__empty-message"><?php esc_html_e( 'No products in the cart.', 'tenweb-builder' ); ?></div>
<?php } else { ?>
	<div class="twbb_menu-cart__products woocommerce-mini-cart cart woocommerce-cart-form__contents">
		<?php
		do_action( 'woocommerce_before_mini_cart_contents' );

		foreach ( $cart_items as $cart_item_key => $cart_item ) {
            twbb_render_cart_item( $cart_item_key, $cart_item );
		}

		do_action( 'woocommerce_mini_cart_contents' );
		?>
	</div>

<div class="twbb_menu-cart__subtotal">
		<strong><?php echo esc_html__( 'Subtotal', 'woocommerce' ); // phpcs:ignore WordPress.WP.I18n ?>:</strong>
		<?php echo WC()->cart->get_cart_subtotal(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</div>
<div class="twbb_menu-cart__footer-buttons">
  <a href="<?php echo esc_url(wc_get_cart_url()); ?>" class="elementor-button elementor-button--view-cart elementor-size-md">
    <span class="elementor-button-text"><?php esc_html_e('View cart', 'tenweb-builder'); ?></span>
  </a>
  <a href="<?php echo esc_url(wc_get_checkout_url()); ?>" class="elementor-button elementor-button--checkout elementor-size-md">
    <span class="elementor-button-text"><?php esc_html_e('Checkout', 'tenweb-builder'); ?></span>
  </a>
</div>
<?php
}

?>
