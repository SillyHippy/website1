jQuery( window ).on( 'elementor/frontend/init', function() {
  var InitSwiper = async function ( $scope ) {
    var swiper_container = $scope.find('.woocommerce-product-gallery--with-images');
    var swiper_wrapper = swiper_container.find('ol.flex-control-thumbs');
    var swiper_slides = swiper_container.find('ol.flex-control-thumbs li');
    if ( 4 < swiper_container.find('ol.flex-control-thumbs li').length ) {
      swiper_wrapper.addClass('swiper-wrapper');
      swiper_slides.addClass('swiper-slide');
      if ( typeof twbb.swiper_latest != "undefined" && twbb.swiper_latest == 'inactive' ) {
        swiper_container.append(jQuery('<div class="swiper-button-prev"></div><div class="swiper-button-next"></div>'));
      } else {
        swiper_container.append(jQuery('<div class="swiper-button-prev twbb-swiper-last"></div><div class="swiper-button-next twbb-swiper-last"></div>'));
      }
      var fixNavigationButtonsPositions = function () {
        swiper_container.find('.swiper-button-prev, .swiper-button-next').css('top', 'calc(100% - ' + swiper_container.find('.swiper-slide').height() / 2 + 'px)');
      };
      const Swiper = elementorFrontend.utils.swiper;
      var swiper = await new Swiper(swiper_container, {
        slidesPerView: 4,
        spaceBetween: 0,
        navigation: {
          nextEl: '.swiper-button-next',
          prevEl: '.swiper-button-prev',
        },
        on: {
          imagesReady: fixNavigationButtonsPositions,
          resize: fixNavigationButtonsPositions,
        },
      });
    }
  };

  elementorFrontend.hooks.addAction( 'frontend/element_ready/twbb_woocommerce-product-images.default', InitSwiper );
  elementorFrontend.hooks.addAction( 'frontend/element_ready/twbb_woocommerce-page.default', InitSwiper );
});