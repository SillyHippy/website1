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

	elementorFrontend.hooks.addAction('frontend/element_ready/twbb-testimonial-carousel.default',  function () {
		jQuery('.tenweb-testimonial-carousel-swiper').each(async function(i,elem) {

			var id = jQuery(elem).parents('.elementor-widget-twbb-testimonial-carousel').attr('data-id');
			jQuery(elem).attr('id', 'tenweb-testimonial-carousel-swiper-' + id);
			var settings = jQuery(elem).data('settings');

			if ( ! jQuery.isEmptyObject(settings) ) {

				settings.slidesPerView = {
					desktop: 1,
					tablet: 1,
					mobile: 1
				};
				var swiperOptions = {
					navigation: {
						prevEl: '.tenweb-swiper-button-prev',
						nextEl: '.tenweb-swiper-button-next'
					},
					pagination: {
						el: '.swiper-pagination',
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
					spaceBetween: getSpaceBetween( 'desktop', settings ),
					handleElementorBreakpoints: true,
				}

				const elementorBreakpoints = elementorFrontend.config.responsive.activeBreakpoints;

				swiperOptions.breakpoints = {};
				Object.keys(elementorBreakpoints).reverse().forEach(breakpointName => {
					swiperOptions.breakpoints[elementorBreakpoints[breakpointName].value] = {
						slidesPerView: getDeviceSlidesPerView( breakpointName, settings ),
						slidesPerGroup: getSlidesToScroll( breakpointName, settings ),
						spaceBetween: getSpaceBetween( breakpointName, settings )
					};
				});

				if ( settings.autoplay == 'yes' ) {
					swiperOptions.autoplay = {
						delay: settings.autoplay_speed,
						disableOnInteraction: !! settings.pause_on_interaction
					}
				}

				const Swiper = elementorFrontend.utils.swiper;
				await new Swiper( jQuery('#tenweb-testimonial-carousel-swiper-' + id), swiperOptions );
			}
		});
	});

	var TWBtestimonialMasonry = elementorModules.frontend.handlers.Base.extend({
		bindEvents() {
			elementorFrontend.addListenerOnce(this.getModelCID(), 'resize', this.onWindowResize);
		},
		unbindEvents() {
			elementorFrontend.removeListeners(this.getModelCID(), 'resize', this.onWindowResize);
		},
		getClosureMethodsNames() {
			return elementorModules.frontend.handlers.Base.prototype.getClosureMethodsNames.apply(this, arguments).concat(['fitImages', 'onWindowResize', 'runMasonry']);
		},
		getDefaultSettings() {
			return {
				classes: {
				},
				selectors: {
					testimonialContainer: '.tenweb-masonry',
					item: '.tenweb-item',
				}
			};
		},
		getDefaultElements() {
			var selectors = this.getSettings('selectors');
			return {
				$postsContainer: this.$element.find(selectors.testimonialContainer),
				$posts: this.$element.find(selectors.item)
			};
		},
		setColsCountSettings() {
			let colsCount = elementorFrontend.utils.controls.getResponsiveControlValue(this.getElementSettings(), 'column_count_masonry') || 0;
			this.setSettings('column_count_masonry', colsCount);
		},
		getVerticalSpaceBetween() {
			const currentDevice = elementorFrontend.getCurrentDeviceMode();
			let verticalSpaceBetween = elementorFrontend.utils.controls.getResponsiveControlValue(this.getElementSettings(), 'space_between_masonry', '', currentDevice);
			if ( '' === verticalSpaceBetween ) {
				verticalSpaceBetween = this.getElementSettings('space_between_masonry.size');
			} else {
				verticalSpaceBetween = verticalSpaceBetween.size;
			}
			return verticalSpaceBetween;
		},
		runMasonry() {
			var elements = this.elements;
			elements.$posts.css({
				marginTop: '',
				transitionDuration: ''
			});
			this.setColsCountSettings();
				elements.$postsContainer.height('');

			const verticalSpaceBetween = this.getVerticalSpaceBetween();
			var masonry = new elementorModules.utils.Masonry({
				container: elements.$postsContainer,
				items: elements.$posts.filter(':visible'),
				columnsCount: this.getSettings('column_count_masonry'),
				verticalSpaceBetween: verticalSpaceBetween || 0
			});
			masonry.run();
		},
		run() {
			this.runMasonry();
		},
		onInit() {
			elementorModules.frontend.handlers.Base.prototype.onInit.apply(this, arguments);
			this.bindEvents();
			this.run();
		},
		onWindowResize() {
			this.runMasonry();
		},
		onElementChange() {
			setTimeout(this.runMasonry);
		}
	});
	elementorFrontend.hooks.addAction( 'frontend/element_ready/twbb-testimonial-carousel.default', function ( $scope ) {
		if( $scope.find( '.tenweb-masonry .tenweb-item' ).length ) {
			let $element = $scope.find('.tenweb-masonry .tenweb-item');
			new TWBtestimonialMasonry({$element: $scope});
		}

	});

});
