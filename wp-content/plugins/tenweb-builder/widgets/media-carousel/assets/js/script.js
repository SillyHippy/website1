jQuery( window ).on( 'elementor/frontend/init', function() {

	var TWBase = elementorModules.frontend.handlers.Base.extend({

		getDefaultSettings: function getDefaultSettings() {
			return {
				selectors: {
					mainSwiper: '.tenweb-media-carousel-swiper',
					swiperSlide: '.swiper-slide'
				},
				slidesPerView: {
					desktop: 3,
					tablet: 2,
					mobile: 1
				}
			};
		},

		getDefaultElements: function getDefaultElements() {
			var selectors = this.getSettings('selectors');

			var elements = {
				$mainSwiper: this.$element.find(selectors.mainSwiper)
			};

			elements.$mainSwiperSlides = elements.$mainSwiper.find(selectors.swiperSlide);

			return elements;
		},

		getSlidesCount: function getSlidesCount() {
			return this.elements.$mainSwiperSlides.length;
		},

		getInitialSlide: function getInitialSlide() {
			var editSettings = this.getEditSettings();

			return editSettings.activeItemIndex ? editSettings.activeItemIndex - 1 : 0;
		},

		getEffect: function getEffect() {
			return this.getElementSettings('effect');
		},

		getDeviceSlidesPerView: function getDeviceSlidesPerView(device) {
			var slidesPerViewKey = 'slides_per_view' + ('desktop' === device ? '' : '_' + device);

			return Math.min(this.getSlidesCount(), +this.getElementSettings(slidesPerViewKey) || this.getSettings('slidesPerView')[device]);
		},

		getSlidesPerView: function getSlidesPerView(device) {
			if ('slide' === this.getEffect()) {
				return this.getDeviceSlidesPerView(device);
			}

			return 1;
		},

		getDesktopSlidesPerView: function getDesktopSlidesPerView() {
			return this.getSlidesPerView('desktop');
		},

		getTabletSlidesPerView: function getTabletSlidesPerView() {
			return this.getSlidesPerView('tablet');
		},

		getMobileSlidesPerView: function getMobileSlidesPerView() {
			return this.getSlidesPerView('mobile');
		},

		getDeviceSlidesToScroll: function getDeviceSlidesToScroll(device) {
			var slidesToScrollKey = 'slides_to_scroll' + ('desktop' === device ? '' : '_' + device);

			return Math.min(this.getSlidesCount(), +this.getElementSettings(slidesToScrollKey) || 1);
		},

		getSlidesToScroll: function getSlidesToScroll(device) {
			if ('slide' === this.getEffect()) {
				return this.getDeviceSlidesToScroll(device);
			}

			return 1;
		},

		getDesktopSlidesToScroll: function getDesktopSlidesToScroll() {
			return this.getSlidesToScroll('desktop');
		},

		getTabletSlidesToScroll: function getTabletSlidesToScroll() {
			return this.getSlidesToScroll('tablet');
		},

		getMobileSlidesToScroll: function getMobileSlidesToScroll() {
			return this.getSlidesToScroll('mobile');
		},

		getSpaceBetween: function getSpaceBetween(device) {
			var propertyName = 'space_between';

			if (device && 'desktop' !== device) {
				propertyName += '_' + device;
			}

			return this.getElementSettings(propertyName).size || 0;
		},

		getSwiperOptions: function getSwiperOptions() {
			var elementSettings = this.getElementSettings();

			if ('progress' === elementSettings.pagination) {
				elementSettings.pagination = 'progressbar';
			}

			var swiperOptions = {
				grabCursor: true,
				initialSlide: this.getInitialSlide(),
				loop: 'yes' === elementSettings.loop,
				speed: elementSettings.speed,
				effect: this.getEffect()
			};

			if (elementSettings.show_arrows) {
				swiperOptions.navigation = {
					prevEl: '.elementor-swiper-button-prev',
					nextEl: '.elementor-swiper-button-next'
				};
			}

			if (elementSettings.pagination) {
				swiperOptions.pagination = {
					el: '.swiper-pagination',
					type: elementSettings.pagination,
					clickable: true
				};
			}

			if ('cube' !== this.getEffect()) {
				var breakpointsSettings = {},
					breakpoints = elementorFrontend.config.breakpoints;

				breakpointsSettings[breakpoints.lg-1] = {
					slidesPerView: this.getDesktopSlidesPerView(),
					slidesPerGroup: this.getDesktopSlidesToScroll(),
					spaceBetween: this.getSpaceBetween('desktop'),
				}

				breakpointsSettings[breakpoints.md-1] = {
					slidesPerView: this.getTabletSlidesPerView(),
					slidesPerGroup: this.getTabletSlidesToScroll(),
					spaceBetween: this.getSpaceBetween('tablet')
				};

				breakpointsSettings[breakpoints.xs] = {
					slidesPerView: this.getMobileSlidesPerView(),
					slidesPerGroup: this.getMobileSlidesToScroll(),
					spaceBetween: this.getSpaceBetween('mobile')
				};

				swiperOptions.breakpoints = breakpointsSettings;
			}

			if (!this.isEdit && elementSettings.autoplay) {
				swiperOptions.autoplay = {
					delay: elementSettings.autoplay_speed,
					disableOnInteraction: !!elementSettings.pause_on_interaction
				};
			}

			return swiperOptions;

		},

		updateSpaceBetween: function updateSpaceBetween(swiper, propertyName) {
			var deviceMatch = propertyName.match('space_between_(.*)'),
				device = deviceMatch ? deviceMatch[1] : 'desktop',
				newSpaceBetween = this.getSpaceBetween(device),
				breakpoints = elementorFrontend.config.breakpoints;

			if ('desktop' !== device) {
				var breakpointDictionary = {
					tablet: breakpoints.lg - 1,
					mobile: breakpoints.md - 1
				};

				swiper.params.breakpoints[breakpointDictionary[device]].spaceBetween = newSpaceBetween;
			} else {
				swiper.originalParams.spaceBetween = newSpaceBetween;
			}

			swiper.params.spaceBetween = newSpaceBetween;

			swiper.update();
		},

		async onInit() {
			elementorModules.frontend.handlers.Base.prototype.onInit.apply(this, arguments);

			this.swipers = {};

			if (1 >= this.getSlidesCount()) {
				return;
			}

			const Swiper = elementorFrontend.utils.swiper;
			this.swipers.main = await new Swiper(this.elements.$mainSwiper, this.getSwiperOptions());
		},

		onElementChange: function onElementChange(propertyName) {
			if (1 >= this.getSlidesCount()) {
				return;
			}

			if (0 === propertyName.indexOf('width')) {
				this.swipers.main.update();
			}

			if (0 === propertyName.indexOf('space_between')) {
				this.updateSpaceBetween(this.swipers.main, propertyName);
			}
		},

		onEditSettingsChange: function onEditSettingsChange(propertyName) {
			if (1 >= this.getSlidesCount()) {
				return;
			}

			if ('activeItemIndex' === propertyName) {
				this.swipers.main.slideToLoop(this.getEditSettings('activeItemIndex') - 1);
			}
		}
	});

	var TWmedia_carousel = TWBase.extend({

		slideshowSpecialElementSettings: ['slides_per_view', 'slides_per_view_tablet', 'slides_per_view_mobile'],

		isSlideshow: function isSlideshow() {
			return 'slideshow' === this.getElementSettings('skin');
		},

		getDefaultSettings: function getDefaultSettings() {
			var defaultSettings = TWBase.prototype.getDefaultSettings.apply(this, arguments);

			if (this.isSlideshow()) {
				defaultSettings.selectors.thumbsSwiper = '.elementor-thumbnails-swiper';

				defaultSettings.slidesPerView = {
					desktop: 5,
					tablet: 4,
					mobile: 3
				};
			}

			return defaultSettings;
		},

		getElementSettings: function getElementSettings(setting) {
			if (-1 !== this.slideshowSpecialElementSettings.indexOf(setting) && this.isSlideshow()) {
				setting = 'slideshow_' + setting;
			}

			return TWBase.prototype.getElementSettings.call(this, setting);
		},

		getDefaultElements: function getDefaultElements() {
			var selectors = this.getSettings('selectors'),
				defaultElements = TWBase.prototype.getDefaultElements.apply(this, arguments);

			if (this.isSlideshow()) {
				defaultElements.$thumbsSwiper = this.$element.find(selectors.thumbsSwiper);
			}

			return defaultElements;
		},

		getEffect: function getEffect() {
			if ('coverflow' === this.getElementSettings('skin')) {
				return 'coverflow';
			}

			return TWBase.prototype.getEffect.apply(this, arguments);
		},

		getSlidesPerView: function getSlidesPerView(device) {
			if (this.isSlideshow()) {
				return 1;
			}

			if ('coverflow' === this.getElementSettings('skin')) {
				return this.getDeviceSlidesPerView(device);
			}

			return TWBase.prototype.getSlidesPerView.apply(this, arguments);
		},

		getSwiperOptions: function getSwiperOptions() {
			var options = TWBase.prototype.getSwiperOptions.apply(this, arguments);

			if (this.isSlideshow()) {
				options.loopedSlides = this.getSlidesCount();

				delete options.pagination;
				delete options.breakpoints;
			}

			return options;
		},

		async onInit() {
			await TWBase.prototype.onInit.apply(this, arguments);

			var slidesCount = this.getSlidesCount();

			if (!this.isSlideshow() || 1 >= slidesCount) {
				return;
			}

			var elementSettings = this.getElementSettings(),
				loop = 'yes' === elementSettings.loop,
				breakpointsSettings = {},
				breakpoints = elementorFrontend.config.breakpoints;

			breakpointsSettings[breakpoints.lg - 1] = {
				slidesPerView: this.getDeviceSlidesPerView('desktop'),
				spaceBetween: this.getSpaceBetween('desktop')
			};

			breakpointsSettings[breakpoints.md - 1] = {
				slidesPerView: this.getDeviceSlidesPerView('tablet'),
				spaceBetween: this.getSpaceBetween('tablet')
			};

			breakpointsSettings[breakpoints.xs] = {
				slidesPerView: this.getDeviceSlidesPerView('mobile'),
				spaceBetween: this.getSpaceBetween('mobile')
			};

			var thumbsSliderOptions = {
				initialSlide: this.getInitialSlide(),
				centeredSlides: elementSettings.centered_slides,
				slideToClickedSlide: true,
				loopedSlides: slidesCount,
				loop: loop,
				onSlideChangeEnd: function onSlideChangeEnd(swiper) {
					if (loop) {
						swiper.fixLoop();
					}
				},
				breakpoints: breakpointsSettings
			};

			this.swipers.main.controller.control = this.swipers.thumbs = new Swiper(this.elements.$thumbsSwiper, thumbsSliderOptions);

			this.swipers.thumbs.controller.control = this.swipers.main;
		},

		onElementChange: function onElementChange(propertyName) {
			if (1 >= this.getSlidesCount()) {
				return;
			}

			if (!this.isSlideshow()) {
				TWBase.prototype.onElementChange.apply(this, arguments);

				return;
			}

			if (0 === propertyName.indexOf('width')) {
				this.swipers.main.update();
				this.swipers.thumbs.update();
			}

			if (0 === propertyName.indexOf('space_between')) {
				this.updateSpaceBetween(this.swipers.thumbs, propertyName);
			}
		}
	});

	elementorFrontend.hooks.addAction('frontend/element_ready/twbb_media-carousel.default',  function ( $scope ) {
		var $element = $scope.find( '.elementor-widget-twbb_media-carousel .tenweb-media-carousel-swiper' );

		new TWmedia_carousel( { $element: $scope } );
	});
});