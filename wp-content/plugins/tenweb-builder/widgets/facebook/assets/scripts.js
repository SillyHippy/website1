jQuery(window).on('elementor/frontend/init', function () {
  var config = TWBBFrontendConfig.facebook_sdk;
  loadSDK = function loadSDK() {
    // Don't load in parallel
    if ( config.isLoading || config.isLoaded ) {
      return;
    }
    config.isLoading = true;
    jQuery.ajax({
      url: 'https://connect.facebook.net/' + config.lang + '/sdk.js',
      dataType: 'script',
      cache: true,
      success: function success() {
        FB.init({
          appId: config.app_id,
          version: 'v2.10',
          xfbml: false
        });
        config.isLoaded = true;
        config.isLoading = false;
        jQuery(document).trigger('fb:sdk:loaded');
      }
    });
  };
  function parse_current_element( $scope ) {
    loadSDK(); // On FB SDK is loaded, parse current element
    var parse = function parse() {
      FB.XFBML.parse($scope[0]);
    };
    if ( config.isLoaded ) {
      parse();
    }
    else {
      jQuery(document).on('fb:sdk:loaded', parse);
    }
  };

  function parse_current_element( $scope ) {
    loadSDK(); // On FB SDK is loaded, parse current element
    var parse = function parse() {
      FB.XFBML.parse($scope[0]);
    };
    if ( config.isLoaded ) {
      parse();
    }
    else {
      jQuery(document).on('fb:sdk:loaded', parse);
    }
  }
  elementorFrontend.hooks.addAction('frontend/element_ready/twbb_facebook-page.default', function ( $scope ) {
    parse_current_element($scope);
  });
  
  elementorFrontend.hooks.addAction('frontend/element_ready/twbb_facebook-comments.default', function ( $scope ) {
    parse_current_element($scope);
  });
  
  elementorFrontend.hooks.addAction('frontend/element_ready/twbb_facebook-embed.default', function ( $scope ) {
    parse_current_element( $scope );
  });
  
  elementorFrontend.hooks.addAction('frontend/element_ready/twbb_facebook-button.default', function ( $scope ) {
    parse_current_element( $scope );
  });
});
