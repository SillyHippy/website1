jQuery( window ).on( 'elementor/frontend/init', function() {

    var logosHandler = elementorModules.frontend.handlers.Base.extend({
        getDefaultSettings: function getDefaultSettings() {
            return {
                selectors: {
                    main_logos_container: '.twbb-main-logos-slider-container',
                    logos_container: '.twbb-logos-slider-container',
                    logos: '.twbb-logos',
                    each_logo: '.twbb-logos__item',
                },
                classes: {
                    animated: 'twbb-logos-animated'
                },
            };
        },
        onInit: function onInit() {
            elementorModules.frontend.handlers.Base.prototype.onInit.apply(this, arguments);
            var selectors = this.getSettings('selectors');
            var classes = this.getSettings('classes');
            var self = this;
            setTimeout(function() {
                var width = 0;
                var logosElement = self.$element.find(selectors.logos);
                logosElement.addClass(classes.animated);
                self.$element.find(selectors.each_logo).each(function() {
                    width = width + jQuery(this).outerWidth(true);
                });
                let logosContainerElement = self.$element.find(selectors.logos_container);
                let main_container_double_width = 2 * self.$element.find(selectors.main_logos_container).width();
                let fragment = document.createDocumentFragment();
                while (width < main_container_double_width) {
                    let clonedElement = logosElement.clone();
                    fragment.appendChild(clonedElement[0]);
                    width = width + self.$element.find(logosElement).outerWidth(true);
                }
                logosContainerElement.append(fragment);
                if( self.$element.find(selectors.logos).length < 2 ) {
                    let clonedElement = logosElement.clone();
                    fragment.appendChild(clonedElement[0]);
                    logosContainerElement.append(fragment);
                }
                self.$element.find(selectors.logos).addClass(classes.animated);
                let static_window_width = 1180; //just number for static calculations
                let slow_speed = 35;
                let normal_speed = 15;
                let fast_speed = 5;
                let speed = normal_speed;
                if( self.$element.hasClass('twbb-logos-animation-speed-slow') ) {
                    speed = slow_speed;
                } else if (self.$element.hasClass('twbb-logos-animation-speed-fast')) {
                    speed = fast_speed;
                }
                let duration = speed * self.$element.find(selectors.logos).width() / static_window_width;
                self.$element.find(selectors.logos).css('animation-duration', duration + 's');
            },200);
        }
    });

    elementorFrontend.hooks.addAction( 'frontend/element_ready/twbb_logos.default', function ( $scope ) {
        new logosHandler({ $element: $scope });
    });
});