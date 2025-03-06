class TestimonialCarousel extends _base {
    getDefaultSettings() {
        const defaultSettings = super.getDefaultSettings();
        defaultSettings.slidesPerView = {
            desktop: 1
        };
        Object.keys(elementorFrontend.config.responsive.activeBreakpoints).forEach(breakpointName => {
            defaultSettings.slidesPerView[breakpointName] = 1;
        });

        if (defaultSettings.loop) {
            defaultSettings.loopedSlides = this.getSlidesCount();
        }

        return defaultSettings;
    }

    getEffect() {
        return 'slide';
    }

}

jQuery( window ).on( 'elementor/frontend/init', function() {
    elementorFrontend.hooks.addAction('frontend/element_ready/twbb_reviews.default', function ($scope) {
        new TestimonialCarousel({$element: $scope});
    });
});