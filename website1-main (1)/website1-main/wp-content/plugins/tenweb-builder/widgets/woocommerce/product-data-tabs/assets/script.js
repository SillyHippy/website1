var data_tabs_count = 0;
jQuery( window ).on( 'elementor/frontend/init', function() {
  elementorFrontend.hooks.addAction( 'frontend/element_ready/twbb_woocommerce-product-data-tabs.default', function ( $scope ) {
    var data_tabs = jQuery('body .elementor-widget-twbb_woocommerce-product-data-tabs');
    if( data_tabs.length > 1 ) {
      alert("The page already includes a Product Data Tabs widget.");
      elementor.getPanelView().getCurrentPageView().getOption('editedElementView').removeElement();
    }
  });
});