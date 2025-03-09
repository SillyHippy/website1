jQuery( window ).on( 'elementor/frontend/init', function() {

    var Hotspot = elementorModules.frontend.handlers.Base.extend ({
        getDefaultSettings: function getDefaultSettings() {
            return {
                selectors: {
                    hotspot: '.e-hotspot',
                    tooltip: '.e-hotspot__tooltip'
                }
            };
        },

        getDefaultElements: function getDefaultElements() {
            const selectors = this.getSettings('selectors');
            return {
                $hotspot: this.$element.find(selectors.hotspot),
                $hotspotsExcludesLinks: this.$element.find(selectors.hotspot).filter(':not(.e-hotspot--no-tooltip)'),
                $tooltip: this.$element.find(selectors.tooltip)
            };
        },

        bindEvents: function bindEvents() {
            const tooltipTrigger = this.getCurrentDeviceSetting('tooltip_trigger'),
                tooltipTriggerEvent = 'mouseenter' === tooltipTrigger ? 'mouseleave mouseenter' : tooltipTrigger;

            if (tooltipTriggerEvent !== 'none') {
                this.elements.$hotspotsExcludesLinks.on(tooltipTriggerEvent, event => this.onHotspotTriggerEvent(event));
            }
        },

        onDeviceModeChange: function onDeviceModeChange() {
            this.elements.$hotspotsExcludesLinks.off();
            this.bindEvents();
        },

        onHotspotTriggerEvent: function onHotspotTriggerEvent(event) {
            const elementTarget = jQuery(event.target),
                isHotspotButtonEvent = elementTarget.closest('.e-hotspot__button').length,
                isTooltipMouseLeave = 'mouseleave' === event.type && (elementTarget.is('.e-hotspot--tooltip-position') || elementTarget.parents('.e-hotspot--tooltip-position').length),
                isMobile = 'mobile' === elementorFrontend.getCurrentDeviceMode(),
                isHotspotLink = elementTarget.closest('.e-hotspot--link').length,
                triggerTooltip = !(isHotspotLink && isMobile && ('mouseleave' === event.type || 'mouseenter' === event.type));

            if (triggerTooltip && (isHotspotButtonEvent || isTooltipMouseLeave)) {
                const currentHotspot = jQuery(event.currentTarget);
                this.elements.$hotspot.not(currentHotspot).removeClass('e-hotspot--active');
                currentHotspot.toggleClass('e-hotspot--active');
            }
        },


        editorAddSequencedAnimation: function editorAddSequencedAnimation() {
            this.elements.$hotspot.toggleClass('e-hotspot--sequenced', 'yes' === this.getElementSettings('hotspot_sequenced_animation'));
        },

        hotspotSequencedAnimation: function hotspotSequencedAnimation() {
            const elementSettings = this.getElementSettings(),
                isSequencedAnimation = elementSettings.hotspot_sequenced_animation;

            if ('no' === isSequencedAnimation) {
                return;
            } //start sequenced animation when element on viewport


            const hotspotObserver = elementorModules.utils.Scroll.scrollObserver({
                callback: event => {
                    if (event.isInViewport) {
                        hotspotObserver.unobserve(this.$element[0]); //add delay for each hotspot

                        this.elements.$hotspot.each((index, element) => {
                            if (0 === index) {
                                return;
                            }

                            const sequencedAnimation = elementSettings.hotspot_sequenced_animation_duration,
                                sequencedAnimationDuration = sequencedAnimation ? sequencedAnimation.size : 1000,
                                animationDelay = index * (sequencedAnimationDuration / this.elements.$hotspot.length);
                            element.style.animationDelay = animationDelay + 'ms';
                        });
                    }
                }
            });
            hotspotObserver.observe(this.$element[0]);
        },

        setTooltipPositionControl: function setTooltipPositionControl() {
            const elementSettings = this.getElementSettings(),
                isDirectionAnimation = 'undefined' !== typeof elementSettings.tooltip_animation && elementSettings.tooltip_animation.match(/^e-hotspot--(slide|fade)-direction/);

            if (isDirectionAnimation) {
                this.elements.$tooltip.removeClass('e-hotspot--tooltip-animation-from-left e-hotspot--tooltip-animation-from-top e-hotspot--tooltip-animation-from-right e-hotspot--tooltip-animation-from-bottom');
                this.elements.$tooltip.addClass('e-hotspot--tooltip-animation-from-' + elementSettings.tooltip_position);
            }
        },

        onInit: function onInit() {
            elementorModules.frontend.handlers.Base.prototype.onInit.apply(this, arguments);
            this.hotspotSequencedAnimation();
            this.setTooltipPositionControl();

            if (window.elementor) {
                elementor.listenTo(elementor.channels.deviceMode, 'change', () => this.onDeviceModeChange());
            }
        },

        onElementChange: function onElementChange(propertyName) {
            if (propertyName.startsWith('tooltip_position')) {
                this.setTooltipPositionControl();
            }

            if (propertyName.startsWith('hotspot_sequenced_animation')) {
                this.editorAddSequencedAnimation();
            }
        }

    });

    elementorFrontend.hooks.addAction( 'frontend/element_ready/twbb_hotspot.default', function ( $scope ) {
        new Hotspot({ $element: $scope });
    });

});
