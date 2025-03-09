jQuery( window ).on( 'elementor/frontend/init', function() {

  function getInitialSlide ( settings ) {
    return Math.floor( ( settings.slides_count - 1 ) / 2 );
  }

  function getSlidesToScroll ( view, settings ) {
    var str = "slides_to_scroll" + ("desktop" === view ? "" : "_" + view);
    var num =	Math.min( settings.slides_count, +settings[str] || 1 );
    return num;
  }

  function getDeviceSlidesPerView( view , settings ) {
    var str = "slides_per_view" + ("desktop" === view ? "" : "_" + view);
    var num =	Math.min( settings.slides_count, +settings[str] || settings['slidesPerView'][view] );
    return num;
  }

  function getSpaceBetween( view, settings ) {
    var str = "space_between";
    return view && "desktop" !== view && (str += "_" + view), settings.breakpoints[str].size || 0;
  }

  elementorFrontend.hooks.addAction('frontend/element_ready/widget',  function ( $scope ) {
    $scope.find('.tenweb-widget-slider').each(async function(i,elem) {

      var id = jQuery(elem).parents('.elementor-widget').attr('data-id');
      jQuery(elem).attr('id', 'tenweb-widget-slider-' + id);
      var settings = jQuery(elem).data('settings');
      const elementorBreakpoints = elementorFrontend.config.responsive.activeBreakpoints;

      if ( ! jQuery.isEmptyObject(settings) ) {

        settings.slidesPerView = {
          desktop: 3,
          tablet: 2,
          mobile: 1
        };
        var swiperOptions = {
          navigation: {
            prevEl: '#tenweb-widget-slider-' + id + ' .swiper-button-prev',
            nextEl: '#tenweb-widget-slider-' + id + ' .swiper-button-next'
          },
          pagination: {
            el: '#tenweb-widget-slider-' + id + ' .swiper-pagination',
            type: settings.pagination,
            clickable: true
          },
          grabCursor: true,
          speed: settings.speed,
          effect: 'slide',
          initialSlide: 0, //getInitialSlide( settings ),
          slidesPerView: getDeviceSlidesPerView( 'desktop', settings ),
          loop: 'yes' === settings.loop,
          loopedSlides:settings.slides_count,
          slidesPerGroup: getSlidesToScroll( 'desktop', settings ),
          spaceBetween: getSpaceBetween( '', settings ),
          handleElementorBreakpoints: true,
        }

        swiperOptions.breakpoints = {};
        Object.keys(elementorBreakpoints).reverse().forEach(breakpointName => {
          swiperOptions.breakpoints[elementorBreakpoints[breakpointName].value] = {
            slidesPerView: getDeviceSlidesPerView( breakpointName, settings ),
            slidesPerGroup: getSlidesToScroll( breakpointName, settings ),
            spaceBetween: getSpaceBetween( breakpointName, settings )
          };
        });

        if ( settings.autoplay === 'yes' ) {
          swiperOptions.autoplay = {
            delay: settings.autoplay_speed,
            disableOnInteraction: !! settings.pause_on_interaction
          }
        }

        const Swiper = elementorFrontend.utils.swiper;
        await new Swiper( jQuery('#tenweb-widget-slider-' + id), swiperOptions );
      }
    });
  });
});