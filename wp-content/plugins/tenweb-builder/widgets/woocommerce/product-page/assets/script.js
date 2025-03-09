jQuery( window ).on( 'elementor/frontend/init', function() {
  /*
10web customization
 */
  jQuery(document).on('click', '.elementor-widget-twbb_woocommerce-page .twbb-product-quantity-change', function() {
    var $input = jQuery(this).parent().find('input');
    if ( jQuery(this).hasClass( 'twbb-minus-quantity' ) ) {
      if( (parseInt($input.val()) - 1) > 0 ) {
        $input.val(parseInt($input.val()) - 1);
      }
    } else {
      $input.val(parseInt($input.val()) + 1);
    }
    $input.change();
    jQuery('button[name=update_cart]').trigger('click');
    return false;
  });
  /*
    end customization
  */

  elementorFrontend.hooks.addAction( 'frontend/element_ready/twbb_woocommerce-page.default', function ( $scope ) {
    var single_product = jQuery('body .elementor-widget-twbb_woocommerce-page');
    if( single_product.length > 1 ) {
      alert("The page already includes a WooCommerce Pages Widget element.");
      elementor.getPanelView().getCurrentPageView().getOption('editedElementView').removeElement();
    }
  });

  if( jQuery("body").hasClass("single-product") ) {
    jQuery("div[data-elementor-type=twbb_single]").addClass("product");
  }
});
