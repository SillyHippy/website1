<?php

/**
 * Class TenWeb_WC_Theme_Support
 */
class TenWeb_WC_Theme_Support {
  /**
   * Theme init.
   */
  public static function init() {
	add_filter( 'woocommerce_enqueue_styles', array( __CLASS__, 'enqueue_styles' ) );
    // Register theme features.
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
    add_theme_support('woocommerce');

    $wc_thumbnail_image_width = get_option('twbb_wc_thumbnail_image_width', 600);
    $wc_single_image_width = get_option('twbb_wc_single_image_width', 800);
    add_theme_support('woocommerce', array(
      'thumbnail_image_width' => $wc_thumbnail_image_width,
      'single_image_width' => $wc_single_image_width,
    ));
  }

	/**
	 * Enqueue CSS for this theme.
	 *
	 * @param  array $styles Array of registered styles.
	 * @return array
	 */
	public static function enqueue_styles( $styles ) {
		wp_enqueue_style( 'dashicons' );
		unset( $styles['woocommerce-general'] );
    if ( TWBT_DEV === TRUE ) {
      $styles['tenweb-woocommerce-support'] = array(
        'src' => TWBT_URL . '/assets/css/theme-support-wc.css',
        'deps' => '',
        'version' => TWBT_VERSION,
        'media' => 'all',
        'has_rtl' => TRUE,
      );
    }
		return apply_filters( 'woocommerce_twenty_seventeen_styles', $styles );
	}
}

TenWeb_WC_Theme_Support::init();