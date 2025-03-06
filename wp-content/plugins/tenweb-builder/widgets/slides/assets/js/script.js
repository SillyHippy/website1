jQuery( window ).on( 'elementor/frontend/init', function() {
    var SlidesHandler = elementorModules.frontend.handlers.Base.extend({
        getDefaultSettings: function getDefaultSettings() {
            return {
                selectors: {
                    slider: '.twbb_slides-wrapper',
                    slideContent: '.swiper-slide',
                    slideInnerContents: '.swiper-slide-contents'
                },
                classes: {
                    animated: 'animated'
                },
                attributes: {
                    dataSliderOptions: 'slider_options',
                    dataAnimation: 'animation'
                },
                slidesPerView: {
                    desktop: 1,
                    tablet: 1,
                    mobile: 1
                },
            };
        },

        getDefaultElements: function getDefaultElements() {
            var selectors = this.getSettings('selectors');

            var elements = {
                $slider: this.$element.find(selectors.slider)
            };

            elements.$mainSwiperSlides = elements.$slider.find(selectors.slideContent);

            return elements;
        },

        getSlidesCount: function getSlidesCount() {
            return this.elements.$mainSwiperSlides.length;
        },

        getInitialSlide: function getInitialSlide() {
            var editSettings = this.getEditSettings();

            return editSettings.activeItemIndex ? editSettings.activeItemIndex - 1 : 0;
        },

        getDeviceSlidesPerView: function getDeviceSlidesPerView(device) {
            var slidesPerViewKey = 'slides_per_view' + ('desktop' === device ? '' : '_' + device);

            return Math.min(this.getSlidesCount(), +this.getElementSettings(slidesPerViewKey) || this.getSettings('slidesPerView')[device]);
        },

        getSlidesPerView: function getSlidesPerView(device) {
            return this.getDeviceSlidesPerView(device);
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
            return this.getDeviceSlidesToScroll(device);
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

        getSwiperOptions: function getSwiperOptions() {
            var elementSettings = this.getElementSettings();

            var swiperOptions = {
                grabCursor: true,
                initialSlide: this.getInitialSlide(),
                loop: 'yes' === elementSettings.infinite,
                speed: elementSettings.transition_speed,
                effect: elementSettings.transition,
                observer: true,
                observeParents: true,
                observeSlideChildren: true,
                on: {
                    slideChange: function slideChange() {
                        var kenBurnsActiveClass = 'elementor-ken-burns--active';

                        if (this.$activeImage) {
                            this.$activeImage.removeClass(kenBurnsActiveClass);
                        }

                        this.$activeImage = jQuery(this.slides[this.activeIndex]).children();

                        this.$activeImage.addClass(kenBurnsActiveClass);
                    }
                }
            };
            var breakpointsSettings = {},
                breakpoints = elementorFrontend.config.breakpoints;

            breakpointsSettings[breakpoints.lg - 1] = {
                slidesPerView: this.getDesktopSlidesPerView(),
                slidesPerGroup: this.getDesktopSlidesToScroll(),
                spaceBetween: this.getSpaceBetween('desktop'),
            }

            breakpointsSettings[breakpoints.md - 1] = {
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

            var showArrows = 'arrows' === elementSettings.navigation || 'both' === elementSettings.navigation,
                pagination = 'dots' === elementSettings.navigation || 'both' === elementSettings.navigation;

            if (showArrows) {
                swiperOptions.navigation = {
                    prevEl: '.elementor-swiper-button-prev',
                    nextEl: '.elementor-swiper-button-next'
                };
            }

            if (pagination) {
                swiperOptions.pagination = {
                    el: '.swiper-pagination',
                    type: 'bullets',
                    clickable: true
                };
            }

            if (!this.isEdit && elementSettings.autoplay) {
                swiperOptions.autoplay = {
                    delay: elementSettings.autoplay_speed,
                    disableOnInteraction: !!elementSettings.pause_on_hover
                };
            }

            if (true === swiperOptions.loop) {
                swiperOptions.loopedSlides = this.getSlidesCount();
            }

            if ('fade' === swiperOptions.effect) {
                swiperOptions.fadeEffect = {crossFade: true};
            }

            return swiperOptions;
        },

        async initSlider() {
            var $slider = this.elements.$slider,
                settings = this.getSettings(),
                animation = $slider.data(settings.attributes.dataAnimation);

            if (!$slider.length) {
                return;
            }

            this.swipers = {};

            if (1 >= this.getSlidesCount()) {
                return;
            }

            const Swiper = elementorFrontend.utils.swiper;
            this.swipers.main = await new Swiper(this.elements.$slider, this.getSwiperOptions());

            this.editButtonChange();
            if (!animation) {
                return;
            }

            this.swipers.main.on('slideChangeTransitionStart', function () {
                var $sliderContent = $slider.find(settings.selectors.slideInnerContents);

                $sliderContent.removeClass(settings.classes.animated + ' ' + animation).hide();
            });

            this.swipers.main.on('slideChangeTransitionEnd', function () {
                var $currentSlide = $slider.find(settings.selectors.slideInnerContents);

                $currentSlide.show().addClass(settings.classes.animated + ' ' + animation);
            });
        },

        editButtonChange: function editButtonChange( panel ) {
            // try to get better solution
            if ( jQuery('body').hasClass('elementor-editor-active' ) ) {

                elementor.getPanelView().getCurrentPageView().$el.find( '.elementor-repeater-fields .elementor-edit-template' ).remove();
                if ( this.$element.find( '.elementor-widget-container .elementor-swiper .twbb_slides-wrapper .swiper-wrapper .swiper-slide-template.swiper-slide-active' ).length ) {
                    var templateID = this.$element.find( '.elementor-widget-container .elementor-swiper .twbb_slides-wrapper .swiper-wrapper .swiper-slide-template.swiper-slide-active' ).attr( 'data-template-id' );
                    var editUrl = twbb.home_url + '/wp-admin/edit.php?post_type=elementor_library&tabs_group=twbb_templates&elementor_library_type=twbb_slide';
                    var buttonName = 'Add';

                    if ( templateID ) {
                        editUrl = twbb.home_url + '/wp-admin/post.php?post=' + templateID + '&action=elementor';
                        buttonName = 'Edit';
                    }

                    var editButtonHTML = jQuery( '<a />', {
                        target: '_blank',
                        class: 'elementor-button elementor-button-default elementor-edit-template',
                        href: editUrl,
                        html: '<i class="eicon-pencil"></i>' + buttonName
                    } );

                    elementor.getPanelView().getCurrentPageView().$el.find('.elementor-control-template_id').after( editButtonHTML );
                }
            }

        },

        onInit: function onInit() {
            elementorModules.frontend.handlers.Base.prototype.onInit.apply(this, arguments);
            // try to get better solution
            if ( jQuery('body').hasClass('elementor-editor-active' ) ) {
                elementor.hooks.addAction('panel/open_editor/widget/twbb_slides', this.editButtonChange);
            }
            this.initSlider();
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

            this.editButtonChange();
        },
    });

    elementorFrontend.hooks.addAction( 'frontend/element_ready/twbb_slides.default', function ( $scope ) {
        new SlidesHandler({ $element: $scope });
    });
});
