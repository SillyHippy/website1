jQuery( window ).on( 'elementor/frontend/init', function() {
  var AnimatedHeadlineHandler = elementorModules.frontend.handlers.Base.extend({
    svgPaths: {
      circle: ['M325,18C228.7-8.3,118.5,8.3,78,21C22.4,38.4,4.6,54.6,5.6,77.6c1.4,32.4,52.2,54,142.6,63.7 c66.2,7.1,212.2,7.5,273.5-8.3c64.4-16.6,104.3-57.6,33.8-98.2C386.7-4.9,179.4-1.4,126.3,20.7'],
      underline_zigzag: ['M9.3,127.3c49.3-3,150.7-7.6,199.7-7.4c121.9,0.4,189.9,0.4,282.3,7.2C380.1,129.6,181.2,130.6,70,139 c82.6-2.9,254.2-1,335.9,1.3c-56,1.4-137.2-0.3-197.1,9'],
      x: ['M497.4,23.9C301.6,40,155.9,80.6,4,144.4', 'M14.1,27.6c204.5,20.3,393.8,74,467.3,111.7'],
      strikethrough: ['M3,75h493.5'],
      curly: ['M3,146.1c17.1-8.8,33.5-17.8,51.4-17.8c15.6,0,17.1,18.1,30.2,18.1c22.9,0,36-18.6,53.9-18.6 c17.1,0,21.3,18.5,37.5,18.5c21.3,0,31.8-18.6,49-18.6c22.1,0,18.8,18.8,36.8,18.8c18.8,0,37.5-18.6,49-18.6c20.4,0,17.1,19,36.8,19 c22.9,0,36.8-20.6,54.7-18.6c17.7,1.4,7.1,19.5,33.5,18.8c17.1,0,47.2-6.5,61.1-15.6'],
      diagonal: ['M13.5,15.5c131,13.7,289.3,55.5,475,125.5'],
      double: ['M8.4,143.1c14.2-8,97.6-8.8,200.6-9.2c122.3-0.4,287.5,7.2,287.5,7.2', 'M8,19.4c72.3-5.3,162-7.8,216-7.8c54,0,136.2,0,267,7.8'],
      double_underline: ['M5,125.4c30.5-3.8,137.9-7.6,177.3-7.6c117.2,0,252.2,4.7,312.7,7.6', 'M26.9,143.8c55.1-6.1,126-6.3,162.2-6.1c46.5,0.2,203.9,3.2,268.9,6.4'],
      underline: ['M7.7,145.6C109,125,299.9,116.2,401,121.3c42.1,2.2,87.6,11.8,87.3,25.7']
    },

    getDefaultSettings: function getDefaultSettings() {
      var settings = {
        animationDelay: 2500,
        //letters effect
        lettersDelay: 50,
        //typing effect
        typeLettersDelay: 150,
        selectionDuration: 500,
        //clip effect
        revealDuration: 600,
        revealAnimationDelay: 1500
      };

      settings.typeAnimationDelay = settings.selectionDuration + 800;

      settings.selectors = {
        headline: '.twbb-headline',
        dynamicWrapper: '.twbb-headline-dynamic-wrapper'
      };

      settings.classes = {
        dynamicText: 'twbb-headline-dynamic-text',
        dynamicLetter: 'twbb-headline-dynamic-letter',
        textActive: 'twbb-headline-text-active',
        textInactive: 'twbb-headline-text-inactive',
        letters: 'twbb-headline-letters',
        animationIn: 'twbb-headline-animation-in',
        typeSelected: 'twbb-headline-typing-selected'
      };

      return settings;
    },

    getDefaultElements: function getDefaultElements() {
      var selectors = this.getSettings('selectors');

      return {
        $headline: this.$element.find(selectors.headline),
        $dynamicWrapper: this.$element.find(selectors.dynamicWrapper)
      };
    },

    getNextWord: function getNextWord($word) {
      return $word.is(':last-child') ? $word.parent().children().eq(0) : $word.next();
    },

    switchWord: function switchWord($oldWord, $newWord) {
      $oldWord.removeClass('twbb-headline-text-active').addClass('twbb-headline-text-inactive');

      $newWord.removeClass('twbb-headline-text-inactive').addClass('twbb-headline-text-active');
    },

    singleLetters: function singleLetters() {
      var classes = this.getSettings('classes');

      this.elements.$dynamicText.each(function () {
        var $word = jQuery(this),
          letters = $word.text().split(''),
          isActive = $word.hasClass(classes.textActive);

        $word.empty();

        letters.forEach(function (letter) {
          var $letter = jQuery('<span>', { class: classes.dynamicLetter }).text(letter);

          if (isActive) {
            $letter.addClass(classes.animationIn);
          }

          $word.append($letter);
        });

        $word.css('opacity', 1);
      });
    },

    showLetter: function showLetter($letter, $word, bool, duration) {
      var self = this,
        classes = this.getSettings('classes');

      $letter.addClass(classes.animationIn);

      if (!$letter.is(':last-child')) {
        setTimeout(function () {
          self.showLetter($letter.next(), $word, bool, duration);
        }, duration);
      } else if (!bool) {
        setTimeout(function () {
          self.hideWord($word);
        }, self.getSettings('animationDelay'));
      }
    },

    hideLetter: function hideLetter($letter, $word, bool, duration) {
      var self = this,
        settings = this.getSettings();

      $letter.removeClass(settings.classes.animationIn);

      if (!$letter.is(':last-child')) {
        setTimeout(function () {
          self.hideLetter($letter.next(), $word, bool, duration);
        }, duration);
      } else if (bool) {
        setTimeout(function () {
          self.hideWord(self.getNextWord($word));
        }, self.getSettings('animationDelay'));
      }
    },

    showWord: function showWord($word, $duration) {
      var self = this,
        settings = self.getSettings(),
        animationType = self.getElementSettings('animation_type');

      if ('typing' === animationType) {
        self.showLetter($word.find('.' + settings.classes.dynamicLetter).eq(0), $word, false, $duration);

        $word.addClass(settings.classes.textActive).removeClass(settings.classes.textInactive);
      } else if ('clip' === animationType) {
        self.elements.$dynamicWrapper.animate({ width: $word.width() + 10 }, settings.revealDuration, function () {
          setTimeout(function () {
            self.hideWord($word);
          }, settings.revealAnimationDelay);
        });
      }
    },

    hideWord: function hideWord($word) {
      var self = this,
        settings = self.getSettings(),
        classes = settings.classes,
        letterSelector = '.' + classes.dynamicLetter,
        animationType = self.getElementSettings('animation_type'),
        nextWord = self.getNextWord($word);

      if ('typing' === animationType) {
        self.elements.$dynamicWrapper.addClass(classes.typeSelected);

        setTimeout(function () {
          self.elements.$dynamicWrapper.removeClass(classes.typeSelected);

          $word.addClass(settings.classes.textInactive).removeClass(classes.textActive).children(letterSelector).removeClass(classes.animationIn);
        }, settings.selectionDuration);
        setTimeout(function () {
          self.showWord(nextWord, settings.typeLettersDelay);
        }, settings.typeAnimationDelay);
      } else if (self.elements.$headline.hasClass(classes.letters)) {
        var bool = $word.children(letterSelector).length >= nextWord.children(letterSelector).length;

        self.hideLetter($word.find(letterSelector).eq(0), $word, bool, settings.lettersDelay);

        self.showLetter(nextWord.find(letterSelector).eq(0), nextWord, bool, settings.lettersDelay);
      } else if ('clip' === animationType) {
        self.elements.$dynamicWrapper.animate({ width: '2px' }, settings.revealDuration, function () {
          self.switchWord($word, nextWord);
          self.showWord(nextWord);
        });
      } else {
        self.switchWord($word, nextWord);

        setTimeout(function () {
          self.hideWord(nextWord);
        }, settings.animationDelay);
      }
    },

    animateHeadline: function animateHeadline() {
      var self = this,
        animationType = self.getElementSettings('animation_type'),
        $dynamicWrapper = self.elements.$dynamicWrapper;

      if ('clip' === animationType) {
        $dynamicWrapper.width($dynamicWrapper.width() + 10);
      } else if ('typing' !== animationType) {
        //assign to .elementor-headline-dynamic-wrapper the width of its longest word
        var width = 0;

        self.elements.$dynamicText.each(function () {
          var wordWidth = jQuery(this).width();

          if (wordWidth > width) {
            width = wordWidth;
          }
        });

        $dynamicWrapper.css('width', width);
      }

      //trigger animation
      setTimeout(function () {
        self.hideWord(self.elements.$dynamicText.eq(0));
      }, self.getSettings('animationDelay'));
    },

    getSvgPaths: function getSvgPaths(pathName) {
      var pathsInfo = this.svgPaths[pathName],
        $paths = jQuery();

      pathsInfo.forEach(function (pathInfo) {
        $paths = $paths.add(jQuery('<path>', { d: pathInfo }));
      });

      return $paths;
    },

    fillWords: function fillWords() {
      var elementSettings = this.getElementSettings(),
        classes = this.getSettings('classes'),
        $dynamicWrapper = this.elements.$dynamicWrapper;

      if ('rotate' === elementSettings.headline_style) {
        var rotatingText = (elementSettings.rotating_text || '').split('\n');

        rotatingText.forEach(function (word, index) {
          var $dynamicText = jQuery('<span>', { class: classes.dynamicText }).html(word.replace(/ /g, '&nbsp;'));

          if (!index) {
            $dynamicText.addClass(classes.textActive);
          }

          $dynamicWrapper.append($dynamicText);
        });
      } else {
        var $dynamicText = jQuery('<span>', { class: classes.dynamicText + ' ' + classes.textActive }).text(elementSettings.highlighted_text),
          $svg = jQuery('<svg>', {
            xmlns: 'http://www.w3.org/2000/svg',
            viewBox: '0 0 500 150',
            preserveAspectRatio: 'none'
          }).html(this.getSvgPaths(elementSettings.marker));

        $dynamicWrapper.append($dynamicText, $svg[0].outerHTML);
      }

      this.elements.$dynamicText = $dynamicWrapper.children('.' + classes.dynamicText);
    },

    rotateHeadline: function rotateHeadline() {
      var settings = this.getSettings();

      //insert <span> for each letter of a changing word
      if (this.elements.$headline.hasClass(settings.classes.letters)) {
        this.singleLetters();
      }

      //initialise headline animation
      this.animateHeadline();
    },

    initHeadline: function initHeadline() {
      if ('rotate' === this.getElementSettings('headline_style')) {
        this.rotateHeadline();
      }
    },

    onInit: function onInit() {
        elementorModules.frontend.handlers.Base.prototype.onInit.apply(this, arguments);

      this.fillWords();

      this.initHeadline();
    }
  });

  elementorFrontend.hooks.addAction( 'frontend/element_ready/twbbanimated-headline.default', function ( $scope ) {
    new AnimatedHeadlineHandler({ $element: $scope });
  });


});

jQuery( window ).on( 'elementor/frontend/init', function() {
    var codeHighlightHandler = elementorModules.frontend.handlers.Base.extend({

        onElementChange: function onElementChange() {
            // Handle the changes for "Word Wrap" feature
            Prism.highlightAllUnder(this.$element[0], false);
        },

        onInit: function onInit() {
            elementorModules.frontend.handlers.Base.prototype.onInit.apply(this, arguments);
            Prism.highlightAllUnder(this.$element[0], false);
        }
    });

    elementorFrontend.hooks.addAction( 'frontend/element_ready/twbb_code-highlight.default', function ( $scope ) {
        new codeHighlightHandler({ $element: $scope });
    });

});

var tenwebCountdown = function( $countdown, endTime ) {
  var timeInterval,
    elements = {
      $monthsSpan: $countdown.find( '.tenweb-countdown-months' ),
      $daysSpan: $countdown.find( '.tenweb-countdown-days' ),
      $hoursSpan: $countdown.find( '.tenweb-countdown-hours' ),
      $minutesSpan: $countdown.find( '.tenweb-countdown-minutes' ),
      $secondsSpan: $countdown.find( '.tenweb-countdown-seconds' )
    };

  var updateClock = function() {
    var timeRemaining = tenwebCountdown.getTimeRemaining( endTime, elements[ '$monthsSpan' ].length );

    jQuery.each( timeRemaining.parts, function( timePart ) {
      var $element = elements[ '$' + timePart + 'Span' ],
        partValue = this.toString();

      if ( 1 === partValue.length ) {
        partValue = 0 + partValue;
      }

      if ( $element.length ) {
        $element.text( partValue );
      }
    } );

    if ( timeRemaining.total <= 0 ) {
      var hideAfterExpiry = $countdown.data( 'hide-after-expiry' );
      if ( 'yes' == hideAfterExpiry ) {
        $countdown.find('.tenweb-countdown-item').addClass( 'tenweb-hidden' );
        $countdown.parent().find('.tenweb-countdown-description').addClass( 'tenweb-hidden' );
        $countdown.parent().find('.tenweb-countdown-expired').removeClass( 'tenweb-hidden' );
      }
      clearInterval( timeInterval );
    }
  };

  var initializeClock = function() {
    updateClock();

    timeInterval = setInterval( updateClock, 1000 );
  };

  initializeClock();
};

tenwebCountdown.getTimeRemaining = function( endTime, showMonths ) {
  var now = new Date();
  var timeRemaining = endTime - now;
  var days = Math.floor( timeRemaining / ( 1000 * 60 * 60 * 24 ) );
  var months = showMonths && days > 31 ? (endTime.getFullYear() - now.getFullYear()) * 12 + endTime.getMonth() - now.getMonth() : 0;
  if ( showMonths && months ) {
    days = endTime.getDate() - now.getDate();
  }
  var hours = Math.floor( ( timeRemaining / ( 1000 * 60 * 60 ) ) % 24 );
  var minutes = Math.floor( ( timeRemaining / 1000 / 60 ) % 60 );
  var seconds = Math.floor( ( timeRemaining / 1000 ) % 60 );

  if ( days < 0 || hours < 0 || minutes < 0 ) {
    seconds = minutes = hours = days = 0;
  }

  return {
    total: timeRemaining,
    parts: {
      months: months,
      days: days,
      hours: hours,
      minutes: minutes,
      seconds: seconds
    }
  };
};

jQuery( window ).on( 'elementor/frontend/init', function() {
  elementorFrontend.hooks.addAction( 'frontend/element_ready/twbbcountdown.default', function ( $scope ) {
    var $element = $scope.find( '.tenweb-countdown' ),
      date = new Date( $element.data( 'date' ) * 1000 );

    new tenwebCountdown( $element, date );
  } );
});
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
/*!
 * SmartMenus jQuery Plugin - v1.0.1 - November 1, 2016
 * http://www.smartmenus.org/
 *
 * Copyright Vasil Dinkov, Vadikom Web Ltd.
 * http://vadikom.com
 *
 * Licensed MIT
 */

(function(factory) {
	if (typeof define === 'function' && define.amd) {
		// AMD
		define(['jquery'], factory);
	} else if (typeof module === 'object' && typeof module.exports === 'object') {
		// CommonJS
		module.exports = factory(require('jquery'));
	} else {
		// Global jQuery
		factory(jQuery);
	}
} (function($) {

	var menuTrees = [],
		IE = !!window.createPopup, // detect it for the iframe shim
		mouse = false, // optimize for touch by default - we will detect for mouse input
		touchEvents = 'ontouchstart' in window, // we use this just to choose between toucn and pointer events, not for touch screen detection
		mouseDetectionEnabled = false,
		requestAnimationFrame = window.requestAnimationFrame || function(callback) { return setTimeout(callback, 1000 / 60); },
		cancelAnimationFrame = window.cancelAnimationFrame || function(id) { clearTimeout(id); };

	// Handle detection for mouse input (i.e. desktop browsers, tablets with a mouse, etc.)
	function initMouseDetection(disable) {
		var eNS = '.smartmenus_mouse';
		if (!mouseDetectionEnabled && !disable) {
			// if we get two consecutive mousemoves within 2 pixels from each other and within 300ms, we assume a real mouse/cursor is present
			// in practice, this seems like impossible to trick unintentianally with a real mouse and a pretty safe detection on touch devices (even with older browsers that do not support touch events)
			var firstTime = true,
				lastMove = null;
			$(document).bind(getEventsNS([
				['mousemove', function(e) {
					var thisMove = { x: e.pageX, y: e.pageY, timeStamp: new Date().getTime() };
					if (lastMove) {
						var deltaX = Math.abs(lastMove.x - thisMove.x),
							deltaY = Math.abs(lastMove.y - thisMove.y);
	 					if ((deltaX > 0 || deltaY > 0) && deltaX <= 2 && deltaY <= 2 && thisMove.timeStamp - lastMove.timeStamp <= 300) {
							mouse = true;
							// if this is the first check after page load, check if we are not over some item by chance and call the mouseenter handler if yes
							if (firstTime) {
								var $a = $(e.target).closest('a');
								if ($a.is('a')) {
									$.each(menuTrees, function() {
										if ($.contains(this.$root[0], $a[0])) {
											this.itemEnter({ currentTarget: $a[0] });
											return false;
										}
									});
								}
								firstTime = false;
							}
						}
					}
					lastMove = thisMove;
				}],
				[touchEvents ? 'touchstart' : 'pointerover pointermove pointerout MSPointerOver MSPointerMove MSPointerOut', function(e) {
					if (isTouchEvent(e.originalEvent)) {
						mouse = false;
					}
				}]
			], eNS));
			mouseDetectionEnabled = true;
		} else if (mouseDetectionEnabled && disable) {
			$(document).unbind(eNS);
			mouseDetectionEnabled = false;
		}
	}

	function isTouchEvent(e) {
		return !/^(4|mouse)$/.test(e.pointerType);
	}

	// returns a jQuery bind() ready object
	function getEventsNS(defArr, eNS) {
		if (!eNS) {
			eNS = '';
		}
		var obj = {};
		$.each(defArr, function(index, value) {
			obj[value[0].split(' ').join(eNS + ' ') + eNS] = value[1];
		});
		return obj;
	}

	$.SmartMenus = function(elm, options) {
		this.$root = $(elm);
		this.opts = options;
		this.rootId = ''; // internal
		this.accessIdPrefix = '';
		this.$subArrow = null;
		this.activatedItems = []; // stores last activated A's for each level
		this.visibleSubMenus = []; // stores visible sub menus UL's (might be in no particular order)
		this.showTimeout = 0;
		this.hideTimeout = 0;
		this.scrollTimeout = 0;
		this.clickActivated = false;
		this.focusActivated = false;
		this.zIndexInc = 0;
		this.idInc = 0;
		this.$firstLink = null; // we'll use these for some tests
		this.$firstSub = null; // at runtime so we'll cache them
		this.disabled = false;
		this.$disableOverlay = null;
		this.$touchScrollingSub = null;
		this.cssTransforms3d = 'perspective' in elm.style || 'webkitPerspective' in elm.style;
		this.wasCollapsible = false;
		this.init();
	};

	$.extend($.SmartMenus, {
		hideAll: function() {
			$.each(menuTrees, function() {
				this.menuHideAll();
			});
		},
		destroy: function() {
			while (menuTrees.length) {
				menuTrees[0].destroy();
			}
			initMouseDetection(true);
		},
		prototype: {
			init: function(refresh) {
				var self = this;

				if (!refresh) {
					menuTrees.push(this);

					this.rootId = (new Date().getTime() + Math.random() + '').replace(/\D/g, '');
					this.accessIdPrefix = 'sm-' + this.rootId + '-';

					if (this.$root.hasClass('sm-rtl')) {
						this.opts.rightToLeftSubMenus = true;
					}

					// init root (main menu)
					var eNS = '.smartmenus';
					this.$root
						.data('smartmenus', this)
						.attr('data-smartmenus-id', this.rootId)
						.dataSM('level', 1)
						.bind(getEventsNS([
							['mouseover focusin', $.proxy(this.rootOver, this)],
							['mouseout focusout', $.proxy(this.rootOut, this)],
							['keydown', $.proxy(this.rootKeyDown, this)]
						], eNS))
						.delegate('a', getEventsNS([
							['mouseenter', $.proxy(this.itemEnter, this)],
							['mouseleave', $.proxy(this.itemLeave, this)],
							['mousedown', $.proxy(this.itemDown, this)],
							['focus', $.proxy(this.itemFocus, this)],
							['blur', $.proxy(this.itemBlur, this)],
							['click', $.proxy(this.itemClick, this)]
						], eNS));

					// hide menus on tap or click outside the root UL
					eNS += this.rootId;
					if (this.opts.hideOnClick) {
						$(document).bind(getEventsNS([
							['touchstart', $.proxy(this.docTouchStart, this)],
							['touchmove', $.proxy(this.docTouchMove, this)],
							['touchend', $.proxy(this.docTouchEnd, this)],
							// for Opera Mobile < 11.5, webOS browser, etc. we'll check click too
							['click', $.proxy(this.docClick, this)]
						], eNS));
					}
					// hide sub menus on resize
					$(window).bind(getEventsNS([['resize orientationchange', $.proxy(this.winResize, this)]], eNS));

					if (this.opts.subIndicators) {
						this.$subArrow = $('<span/>').addClass('sub-arrow');
						if (this.opts.subIndicatorsText) {
							this.$subArrow.html(this.opts.subIndicatorsText);
						}
					}

					// make sure mouse detection is enabled
					initMouseDetection();
				}

				// init sub menus
				this.$firstSub = this.$root.find('ul').each(function() { self.menuInit($(this)); }).eq(0);

				this.$firstLink = this.$root.find('a').eq(0);

				// find current item
				if (this.opts.markCurrentItem) {
					var reDefaultDoc = /(index|default)\.[^#\?\/]*/i,
						reHash = /#.*/,
						locHref = window.location.href.replace(reDefaultDoc, ''),
						locHrefNoHash = locHref.replace(reHash, '');
					this.$root.find('a').each(function() {
						var href = this.href.replace(reDefaultDoc, ''),
							$this = $(this);
						if (href == locHref || href == locHrefNoHash) {
							$this.addClass('current');
							if (self.opts.markCurrentTree) {
								$this.parentsUntil('[data-smartmenus-id]', 'ul').each(function() {
									$(this).dataSM('parent-a').addClass('current');
								});
							}
						}
					});
				}

				// save initial state
				this.wasCollapsible = this.isCollapsible();
			},
			destroy: function(refresh) {
				if (!refresh) {
					var eNS = '.smartmenus';
					this.$root
						.removeData('smartmenus')
						.removeAttr('data-smartmenus-id')
						.removeDataSM('level')
						.unbind(eNS)
						.undelegate(eNS);
					eNS += this.rootId;
					$(document).unbind(eNS);
					$(window).unbind(eNS);
					if (this.opts.subIndicators) {
						this.$subArrow = null;
					}
				}
				this.menuHideAll();
				var self = this;
				this.$root.find('ul').each(function() {
						var $this = $(this);
						if ($this.dataSM('scroll-arrows')) {
							$this.dataSM('scroll-arrows').remove();
						}
						if ($this.dataSM('shown-before')) {
							if (self.opts.subMenusMinWidth || self.opts.subMenusMaxWidth) {
								$this.css({ width: '', minWidth: '', maxWidth: '' }).removeClass('sm-nowrap');
							}
							if ($this.dataSM('scroll-arrows')) {
								$this.dataSM('scroll-arrows').remove();
							}
							$this.css({ zIndex: '', top: '', left: '', marginLeft: '', marginTop: '', display: '' });
						}
						if (($this.attr('id') || '').indexOf(self.accessIdPrefix) == 0) {
							$this.removeAttr('id');
						}
					})
					.removeDataSM('in-mega')
					.removeDataSM('shown-before')
					.removeDataSM('ie-shim')
					.removeDataSM('scroll-arrows')
					.removeDataSM('parent-a')
					.removeDataSM('level')
					.removeDataSM('beforefirstshowfired')
					.removeAttr('role')
					.removeAttr('aria-hidden')
					.removeAttr('aria-labelledby')
					.removeAttr('aria-expanded');
				this.$root.find('a.has-submenu').each(function() {
						var $this = $(this);
						if ($this.attr('id').indexOf(self.accessIdPrefix) == 0) {
							$this.removeAttr('id');
						}
					})
					.removeClass('has-submenu')
					.removeDataSM('sub')
					.removeAttr('aria-haspopup')
					.removeAttr('aria-controls')
					.removeAttr('aria-expanded')
					.closest('li').removeDataSM('sub');
				if (this.opts.subIndicators) {
					this.$root.find('span.sub-arrow').remove();
				}
				if (this.opts.markCurrentItem) {
					this.$root.find('a.current').removeClass('current');
				}
				if (!refresh) {
					this.$root = null;
					this.$firstLink = null;
					this.$firstSub = null;
					if (this.$disableOverlay) {
						this.$disableOverlay.remove();
						this.$disableOverlay = null;
					}
					menuTrees.splice($.inArray(this, menuTrees), 1);
				}
			},
			disable: function(noOverlay) {
				if (!this.disabled) {
					this.menuHideAll();
					// display overlay over the menu to prevent interaction
					if (!noOverlay && !this.opts.isPopup && this.$root.is(':visible')) {
						var pos = this.$root.offset();
						this.$disableOverlay = $('<div class="sm-jquery-disable-overlay"/>').css({
							position: 'absolute',
							top: pos.top,
							left: pos.left,
							width: this.$root.outerWidth(),
							height: this.$root.outerHeight(),
							zIndex: this.getStartZIndex(true),
							opacity: 0
						}).appendTo(document.body);
					}
					this.disabled = true;
				}
			},
			docClick: function(e) {
				if (this.$touchScrollingSub) {
					this.$touchScrollingSub = null;
					return;
				}
				// hide on any click outside the menu or on a menu link
				if (this.visibleSubMenus.length && !$.contains(this.$root[0], e.target) || $(e.target).is('a')) {
					this.menuHideAll();
				}
			},
			docTouchEnd: function(e) {
				if (!this.lastTouch) {
					return;
				}
				if (this.visibleSubMenus.length && (this.lastTouch.x2 === undefined || this.lastTouch.x1 == this.lastTouch.x2) && (this.lastTouch.y2 === undefined || this.lastTouch.y1 == this.lastTouch.y2) && (!this.lastTouch.target || !$.contains(this.$root[0], this.lastTouch.target))) {
					if (this.hideTimeout) {
						clearTimeout(this.hideTimeout);
						this.hideTimeout = 0;
					}
					// hide with a delay to prevent triggering accidental unwanted click on some page element
					var self = this;
					this.hideTimeout = setTimeout(function() { self.menuHideAll(); }, 350);
				}
				this.lastTouch = null;
			},
			docTouchMove: function(e) {
				if (!this.lastTouch) {
					return;
				}
				var touchPoint = e.originalEvent.touches[0];
				this.lastTouch.x2 = touchPoint.pageX;
				this.lastTouch.y2 = touchPoint.pageY;
			},
			docTouchStart: function(e) {
				var touchPoint = e.originalEvent.touches[0];
				this.lastTouch = { x1: touchPoint.pageX, y1: touchPoint.pageY, target: touchPoint.target };
			},
			enable: function() {
				if (this.disabled) {
					if (this.$disableOverlay) {
						this.$disableOverlay.remove();
						this.$disableOverlay = null;
					}
					this.disabled = false;
				}
			},
			getClosestMenu: function(elm) {
				var $closestMenu = $(elm).closest('ul');
				while ($closestMenu.dataSM('in-mega')) {
					$closestMenu = $closestMenu.parent().closest('ul');
				}
				return $closestMenu[0] || null;
			},
			getHeight: function($elm) {
				return this.getOffset($elm, true);
			},
			// returns precise width/height float values
			getOffset: function($elm, height) {
				var old;
				if ($elm.css('display') == 'none') {
					old = { position: $elm[0].style.position, visibility: $elm[0].style.visibility };
					$elm.css({ position: 'absolute', visibility: 'hidden' }).show();
				}
				var box = $elm[0].getBoundingClientRect && $elm[0].getBoundingClientRect(),
					val = box && (height ? box.height || box.bottom - box.top : box.width || box.right - box.left);
				if (!val && val !== 0) {
					val = height ? $elm[0].offsetHeight : $elm[0].offsetWidth;
				}
				if (old) {
					$elm.hide().css(old);
				}
				return val;
			},
			getStartZIndex: function(root) {
				var zIndex = parseInt(this[root ? '$root' : '$firstSub'].css('z-index'));
				if (!root && isNaN(zIndex)) {
					zIndex = parseInt(this.$root.css('z-index'));
				}
				return !isNaN(zIndex) ? zIndex : 1;
			},
			getTouchPoint: function(e) {
				return e.touches && e.touches[0] || e.changedTouches && e.changedTouches[0] || e;
			},
			getViewport: function(height) {
				var name = height ? 'Height' : 'Width',
					val = document.documentElement['client' + name],
					val2 = window['inner' + name];
				if (val2) {
					val = Math.min(val, val2);
				}
				return val;
			},
			getViewportHeight: function() {
				return this.getViewport(true);
			},
			getViewportWidth: function() {
				return this.getViewport();
			},
			getWidth: function($elm) {
				return this.getOffset($elm);
			},
			handleEvents: function() {
				return !this.disabled && this.isCSSOn();
			},
			handleItemEvents: function($a) {
				return this.handleEvents() && !this.isLinkInMegaMenu($a);
			},
			isCollapsible: function() {
				return this.$firstSub.css('position') == 'static';
			},
			isCSSOn: function() {
				return this.$firstLink.css('display') == 'block';
			},
			isFixed: function() {
				var isFixed = this.$root.css('position') == 'fixed';
				if (!isFixed) {
					this.$root.parentsUntil('body').each(function() {
						if ($(this).css('position') == 'fixed') {
							isFixed = true;
							return false;
						}
					});
				}
				return isFixed;
			},
			isLinkInMegaMenu: function($a) {
				return $(this.getClosestMenu($a[0])).hasClass('mega-menu');
			},
			isTouchMode: function() {
				return !mouse || this.opts.noMouseOver || this.isCollapsible();
			},
			itemActivate: function($a, focus) {
				var $ul = $a.closest('ul'),
					level = $ul.dataSM('level');
				// if for some reason the parent item is not activated (e.g. this is an API call to activate the item), activate all parent items first
				if (level > 1 && (!this.activatedItems[level - 2] || this.activatedItems[level - 2][0] != $ul.dataSM('parent-a')[0])) {
					var self = this;
					$($ul.parentsUntil('[data-smartmenus-id]', 'ul').get().reverse()).add($ul).each(function() {
						self.itemActivate($(this).dataSM('parent-a'));
					});
				}
				// hide any visible deeper level sub menus
				if (!this.isCollapsible() || focus) {
					this.menuHideSubMenus(!this.activatedItems[level - 1] || this.activatedItems[level - 1][0] != $a[0] ? level - 1 : level);
				}
				// save new active item for this level
				this.activatedItems[level - 1] = $a;
				if (this.$root.triggerHandler('activate.smapi', $a[0]) === false) {
					return;
				}
				// show the sub menu if this item has one
				var $sub = $a.dataSM('sub');
				if ($sub && (this.isTouchMode() || (!this.opts.showOnClick || this.clickActivated))) {
					this.menuShow($sub);
				}
			},
			itemBlur: function(e) {
				var $a = $(e.currentTarget);
				if (!this.handleItemEvents($a)) {
					return;
				}
				this.$root.triggerHandler('blur.smapi', $a[0]);
			},
			itemClick: function(e) {
				var $a = $(e.currentTarget);
				if (!this.handleItemEvents($a)) {
					return;
				}
				if (this.$touchScrollingSub && this.$touchScrollingSub[0] == $a.closest('ul')[0]) {
					this.$touchScrollingSub = null;
					e.stopPropagation();
					return false;
				}
				if (this.$root.triggerHandler('click.smapi', $a[0]) === false) {
					return false;
				}
				var subArrowClicked = $(e.target).is('span.sub-arrow'),
					$sub = $a.dataSM('sub'),
					firstLevelSub = $sub ? $sub.dataSM('level') == 2 : false;
				// if the sub is not visible
				if ($sub && !$sub.is(':visible')) {
					if (this.opts.showOnClick && firstLevelSub) {
						this.clickActivated = true;
					}
					// try to activate the item and show the sub
					this.itemActivate($a);
					// if "itemActivate" showed the sub, prevent the click so that the link is not loaded
					// if it couldn't show it, then the sub menus are disabled with an !important declaration (e.g. via mobile styles) so let the link get loaded
					if ($sub.is(':visible')) {
						this.focusActivated = true;
						return false;
					}
				} else if (this.isCollapsible() && subArrowClicked) {
					this.itemActivate($a);
					this.menuHide($sub);
					return false;
				}
				if (this.opts.showOnClick && firstLevelSub || $a.hasClass('disabled') || this.$root.triggerHandler('select.smapi', $a[0]) === false) {
					return false;
				}
			},
			itemDown: function(e) {
				var $a = $(e.currentTarget);
				if (!this.handleItemEvents($a)) {
					return;
				}
				$a.dataSM('mousedown', true);
			},
			itemEnter: function(e) {
				var $a = $(e.currentTarget);
				if (!this.handleItemEvents($a)) {
					return;
				}
				if (!this.isTouchMode()) {
					if (this.showTimeout) {
						clearTimeout(this.showTimeout);
						this.showTimeout = 0;
					}
					var self = this;
					this.showTimeout = setTimeout(function() { self.itemActivate($a); }, this.opts.showOnClick && $a.closest('ul').dataSM('level') == 1 ? 1 : this.opts.showTimeout);
				}
				this.$root.triggerHandler('mouseenter.smapi', $a[0]);
			},
			itemFocus: function(e) {
				var $a = $(e.currentTarget);
				if (!this.handleItemEvents($a)) {
					return;
				}
				// fix (the mousedown check): in some browsers a tap/click produces consecutive focus + click events so we don't need to activate the item on focus
				if (this.focusActivated && (!this.isTouchMode() || !$a.dataSM('mousedown')) && (!this.activatedItems.length || this.activatedItems[this.activatedItems.length - 1][0] != $a[0])) {
					this.itemActivate($a, true);
				}
				this.$root.triggerHandler('focus.smapi', $a[0]);
			},
			itemLeave: function(e) {
				var $a = $(e.currentTarget);
				if (!this.handleItemEvents($a)) {
					return;
				}
				if (!this.isTouchMode()) {
					$a[0].blur();
					if (this.showTimeout) {
						clearTimeout(this.showTimeout);
						this.showTimeout = 0;
					}
				}
				$a.removeDataSM('mousedown');
				this.$root.triggerHandler('mouseleave.smapi', $a[0]);
			},
			menuHide: function($sub) {
				if (this.$root.triggerHandler('beforehide.smapi', $sub[0]) === false) {
					return;
				}
				$sub.stop(true, true);
				if ($sub.css('display') != 'none') {
					var complete = function() {
						// unset z-index
						$sub.css('z-index', '');
					};
					// if sub is collapsible (mobile view)
					if (this.isCollapsible()) {
						if (this.opts.collapsibleHideFunction) {
							this.opts.collapsibleHideFunction.call(this, $sub, complete);
						} else {
							$sub.hide(this.opts.collapsibleHideDuration, complete);
						}
					} else {
						if (this.opts.hideFunction) {
							this.opts.hideFunction.call(this, $sub, complete);
						} else {
							$sub.hide(this.opts.hideDuration, complete);
						}
					}
					// remove IE iframe shim
					if ($sub.dataSM('ie-shim')) {
						$sub.dataSM('ie-shim').remove().css({ '-webkit-transform': '', transform: '' });
					}
					// deactivate scrolling if it is activated for this sub
					if ($sub.dataSM('scroll')) {
						this.menuScrollStop($sub);
						$sub.css({ 'touch-action': '', '-ms-touch-action': '', '-webkit-transform': '', transform: '' })
							.unbind('.smartmenus_scroll').removeDataSM('scroll').dataSM('scroll-arrows').hide();
					}
					// unhighlight parent item + accessibility
					$sub.dataSM('parent-a').removeClass('highlighted').attr('aria-expanded', 'false');
					$sub.attr({
						'aria-expanded': 'false',
						'aria-hidden': 'true'
					});
					var level = $sub.dataSM('level');
					this.activatedItems.splice(level - 1, 1);
					this.visibleSubMenus.splice($.inArray($sub, this.visibleSubMenus), 1);
					this.$root.triggerHandler('hide.smapi', $sub[0]);
				}
			},
			menuHideAll: function() {
				if (this.showTimeout) {
					clearTimeout(this.showTimeout);
					this.showTimeout = 0;
				}
				// hide all subs
				// if it's a popup, this.visibleSubMenus[0] is the root UL
				var level = this.opts.isPopup ? 1 : 0;
				for (var i = this.visibleSubMenus.length - 1; i >= level; i--) {
					this.menuHide(this.visibleSubMenus[i]);
				}
				// hide root if it's popup
				if (this.opts.isPopup) {
					this.$root.stop(true, true);
					if (this.$root.is(':visible')) {
						if (this.opts.hideFunction) {
							this.opts.hideFunction.call(this, this.$root);
						} else {
							this.$root.hide(this.opts.hideDuration);
						}
						// remove IE iframe shim
						if (this.$root.dataSM('ie-shim')) {
							this.$root.dataSM('ie-shim').remove();
						}
					}
				}
				this.activatedItems = [];
				this.visibleSubMenus = [];
				this.clickActivated = false;
				this.focusActivated = false;
				// reset z-index increment
				this.zIndexInc = 0;
				this.$root.triggerHandler('hideAll.smapi');
			},
			menuHideSubMenus: function(level) {
				for (var i = this.activatedItems.length - 1; i >= level; i--) {
					var $sub = this.activatedItems[i].dataSM('sub');
					if ($sub) {
						this.menuHide($sub);
					}
				}
			},
			menuIframeShim: function($ul) {
				// create iframe shim for the menu
				if (IE && this.opts.overlapControlsInIE && !$ul.dataSM('ie-shim')) {
					$ul.dataSM('ie-shim', $('<iframe/>').attr({ src: 'javascript:0', tabindex: -9 })
						.css({ position: 'absolute', top: 'auto', left: '0', opacity: 0, border: '0' })
					);
				}
			},
			menuInit: function($ul) {
				if (!$ul.dataSM('in-mega')) {
					// mark UL's in mega drop downs (if any) so we can neglect them
					if ($ul.hasClass('mega-menu')) {
						$ul.find('ul').dataSM('in-mega', true);
					}
					// get level (much faster than, for example, using parentsUntil)
					var level = 2,
						par = $ul[0];
					while ((par = par.parentNode.parentNode) != this.$root[0]) {
						level++;
					}
					// cache stuff for quick access
					var $a = $ul.prevAll('a').eq(-1);
					// if the link is nested (e.g. in a heading)
					if (!$a.length) {
						$a = $ul.prevAll().find('a').eq(-1);
					}
					$a.addClass('has-submenu').dataSM('sub', $ul);
					$ul.dataSM('parent-a', $a)
						.dataSM('level', level)
						.parent().dataSM('sub', $ul);
					// accessibility
					var aId = $a.attr('id') || this.accessIdPrefix + (++this.idInc),
						ulId = $ul.attr('id') || this.accessIdPrefix + (++this.idInc);
					$a.attr({
						id: aId,
						'aria-haspopup': 'true',
						'aria-controls': ulId,
						'aria-expanded': 'false'
					});
					$ul.attr({
						id: ulId,
						'role': 'group',
						'aria-hidden': 'true',
						'aria-labelledby': aId,
						'aria-expanded': 'false'
					});
					// add sub indicator to parent item
					if (this.opts.subIndicators) {
						$a[this.opts.subIndicatorsPos](this.$subArrow.clone());
					}
				}
			},
			menuPosition: function($sub) {
				var $a = $sub.dataSM('parent-a'),
					$li = $a.closest('li'),
					$ul = $li.parent(),
					level = $sub.dataSM('level'),
					subW = this.getWidth($sub),
					subH = this.getHeight($sub),
					itemOffset = $a.offset(),
					itemX = itemOffset.left,
					itemY = itemOffset.top,
					itemW = this.getWidth($a),
					itemH = this.getHeight($a),
					$win = $(window),
					winX = $win.scrollLeft(),
					winY = $win.scrollTop(),
					winW = this.getViewportWidth(),
					winH = this.getViewportHeight(),
					horizontalParent = $ul.parent().is('[data-sm-horizontal-sub]') || level == 2 && !$ul.hasClass('sm-vertical'),
					rightToLeft = this.opts.rightToLeftSubMenus && !$li.is('[data-sm-reverse]') || !this.opts.rightToLeftSubMenus && $li.is('[data-sm-reverse]'),
					subOffsetX = level == 2 ? this.opts.mainMenuSubOffsetX : this.opts.subMenusSubOffsetX,
					subOffsetY = level == 2 ? this.opts.mainMenuSubOffsetY : this.opts.subMenusSubOffsetY,
					x, y;
				if (horizontalParent) {
					x = rightToLeft ? itemW - subW - subOffsetX : subOffsetX;
					y = this.opts.bottomToTopSubMenus ? -subH - subOffsetY : itemH + subOffsetY;
				} else {
					x = rightToLeft ? subOffsetX - subW : itemW - subOffsetX;
					y = this.opts.bottomToTopSubMenus ? itemH - subOffsetY - subH : subOffsetY;
				}
				if (this.opts.keepInViewport) {
					var absX = itemX + x,
						absY = itemY + y;
					if (rightToLeft && absX < winX) {
						x = horizontalParent ? winX - absX + x : itemW - subOffsetX;
					} else if (!rightToLeft && absX + subW > winX + winW) {
						x = horizontalParent ? winX + winW - subW - absX + x : subOffsetX - subW;
					}
					if (!horizontalParent) {
						if (subH < winH && absY + subH > winY + winH) {
							y += winY + winH - subH - absY;
						} else if (subH >= winH || absY < winY) {
							y += winY - absY;
						}
					}
					// do we need scrolling?
					// 0.49 used for better precision when dealing with float values
					if (horizontalParent && (absY + subH > winY + winH + 0.49 || absY < winY) || !horizontalParent && subH > winH + 0.49) {
						var self = this;
						if (!$sub.dataSM('scroll-arrows')) {
							$sub.dataSM('scroll-arrows', $([$('<span class="scroll-up"><span class="scroll-up-arrow"></span></span>')[0], $('<span class="scroll-down"><span class="scroll-down-arrow"></span></span>')[0]])
								.bind({
									mouseenter: function() {
										$sub.dataSM('scroll').up = $(this).hasClass('scroll-up');
										self.menuScroll($sub);
									},
									mouseleave: function(e) {
										self.menuScrollStop($sub);
										self.menuScrollOut($sub, e);
									},
									'mousewheel DOMMouseScroll': function(e) { e.preventDefault(); }
								})
								.insertAfter($sub)
							);
						}
						// bind scroll events and save scroll data for this sub
						var eNS = '.smartmenus_scroll';
						$sub.dataSM('scroll', {
								y: this.cssTransforms3d ? 0 : y - itemH,
								step: 1,
								// cache stuff for faster recalcs later
								itemH: itemH,
								subH: subH,
								arrowDownH: this.getHeight($sub.dataSM('scroll-arrows').eq(1))
							})
							.bind(getEventsNS([
								['mouseover', function(e) { self.menuScrollOver($sub, e); }],
								['mouseout', function(e) { self.menuScrollOut($sub, e); }],
								['mousewheel DOMMouseScroll', function(e) { self.menuScrollMousewheel($sub, e); }]
							], eNS))
							.dataSM('scroll-arrows').css({ top: 'auto', left: '0', marginLeft: x + (parseInt($sub.css('border-left-width')) || 0), width: subW - (parseInt($sub.css('border-left-width')) || 0) - (parseInt($sub.css('border-right-width')) || 0), zIndex: $sub.css('z-index') })
								.eq(horizontalParent && this.opts.bottomToTopSubMenus ? 0 : 1).show();
						// when a menu tree is fixed positioned we allow scrolling via touch too
						// since there is no other way to access such long sub menus if no mouse is present
						if (this.isFixed()) {
							$sub.css({ 'touch-action': 'none', '-ms-touch-action': 'none' })
								.bind(getEventsNS([
									[touchEvents ? 'touchstart touchmove touchend' : 'pointerdown pointermove pointerup MSPointerDown MSPointerMove MSPointerUp', function(e) {
										self.menuScrollTouch($sub, e);
									}]
								], eNS));
						}
					}
				}
				$sub.css({ top: 'auto', left: '0', marginLeft: x, marginTop: y - itemH });
				// IE iframe shim
				this.menuIframeShim($sub);
				if ($sub.dataSM('ie-shim')) {
					$sub.dataSM('ie-shim').css({ zIndex: $sub.css('z-index'), width: subW, height: subH, marginLeft: x, marginTop: y - itemH });
				}
			},
			menuScroll: function($sub, once, step) {
				var data = $sub.dataSM('scroll'),
					$arrows = $sub.dataSM('scroll-arrows'),
					end = data.up ? data.upEnd : data.downEnd,
					diff;
				if (!once && data.momentum) {
					data.momentum *= 0.92;
					diff = data.momentum;
					if (diff < 0.5) {
						this.menuScrollStop($sub);
						return;
					}
				} else {
					diff = step || (once || !this.opts.scrollAccelerate ? this.opts.scrollStep : Math.floor(data.step));
				}
				// hide any visible deeper level sub menus
				var level = $sub.dataSM('level');
				if (this.activatedItems[level - 1] && this.activatedItems[level - 1].dataSM('sub') && this.activatedItems[level - 1].dataSM('sub').is(':visible')) {
					this.menuHideSubMenus(level - 1);
				}
				data.y = data.up && end <= data.y || !data.up && end >= data.y ? data.y : (Math.abs(end - data.y) > diff ? data.y + (data.up ? diff : -diff) : end);
				$sub.add($sub.dataSM('ie-shim')).css(this.cssTransforms3d ? { '-webkit-transform': 'translate3d(0, ' + data.y + 'px, 0)', transform: 'translate3d(0, ' + data.y + 'px, 0)' } : { marginTop: data.y });
				// show opposite arrow if appropriate
				if (mouse && (data.up && data.y > data.downEnd || !data.up && data.y < data.upEnd)) {
					$arrows.eq(data.up ? 1 : 0).show();
				}
				// if we've reached the end
				if (data.y == end) {
					if (mouse) {
						$arrows.eq(data.up ? 0 : 1).hide();
					}
					this.menuScrollStop($sub);
				} else if (!once) {
					if (this.opts.scrollAccelerate && data.step < this.opts.scrollStep) {
						data.step += 0.2;
					}
					var self = this;
					this.scrollTimeout = requestAnimationFrame(function() { self.menuScroll($sub); });
				}
			},
			menuScrollMousewheel: function($sub, e) {
				if (this.getClosestMenu(e.target) == $sub[0]) {
					e = e.originalEvent;
					var up = (e.wheelDelta || -e.detail) > 0;
					if ($sub.dataSM('scroll-arrows').eq(up ? 0 : 1).is(':visible')) {
						$sub.dataSM('scroll').up = up;
						this.menuScroll($sub, true);
					}
				}
				e.preventDefault();
			},
			menuScrollOut: function($sub, e) {
				if (mouse) {
					if (!/^scroll-(up|down)/.test((e.relatedTarget || '').className) && ($sub[0] != e.relatedTarget && !$.contains($sub[0], e.relatedTarget) || this.getClosestMenu(e.relatedTarget) != $sub[0])) {
						$sub.dataSM('scroll-arrows').css('visibility', 'hidden');
					}
				}
			},
			menuScrollOver: function($sub, e) {
				if (mouse) {
					if (!/^scroll-(up|down)/.test(e.target.className) && this.getClosestMenu(e.target) == $sub[0]) {
						this.menuScrollRefreshData($sub);
						var data = $sub.dataSM('scroll'),
							upEnd = $(window).scrollTop() - $sub.dataSM('parent-a').offset().top - data.itemH;
						$sub.dataSM('scroll-arrows').eq(0).css('margin-top', upEnd).end()
							.eq(1).css('margin-top', upEnd + this.getViewportHeight() - data.arrowDownH).end()
							.css('visibility', 'visible');
					}
				}
			},
			menuScrollRefreshData: function($sub) {
				var data = $sub.dataSM('scroll'),
					upEnd = $(window).scrollTop() - $sub.dataSM('parent-a').offset().top - data.itemH;
				if (this.cssTransforms3d) {
					upEnd = -(parseFloat($sub.css('margin-top')) - upEnd);
				}
				$.extend(data, {
					upEnd: upEnd,
					downEnd: upEnd + this.getViewportHeight() - data.subH
				});
			},
			menuScrollStop: function($sub) {
				if (this.scrollTimeout) {
					cancelAnimationFrame(this.scrollTimeout);
					this.scrollTimeout = 0;
					$sub.dataSM('scroll').step = 1;
					return true;
				}
			},
			menuScrollTouch: function($sub, e) {
				e = e.originalEvent;
				if (isTouchEvent(e)) {
					var touchPoint = this.getTouchPoint(e);
					// neglect event if we touched a visible deeper level sub menu
					if (this.getClosestMenu(touchPoint.target) == $sub[0]) {
						var data = $sub.dataSM('scroll');
						if (/(start|down)$/i.test(e.type)) {
							if (this.menuScrollStop($sub)) {
								// if we were scrolling, just stop and don't activate any link on the first touch
								e.preventDefault();
								this.$touchScrollingSub = $sub;
							} else {
								this.$touchScrollingSub = null;
							}
							// update scroll data since the user might have zoomed, etc.
							this.menuScrollRefreshData($sub);
							// extend it with the touch properties
							$.extend(data, {
								touchStartY: touchPoint.pageY,
								touchStartTime: e.timeStamp
							});
						} else if (/move$/i.test(e.type)) {
							var prevY = data.touchY !== undefined ? data.touchY : data.touchStartY;
							if (prevY !== undefined && prevY != touchPoint.pageY) {
								this.$touchScrollingSub = $sub;
								var up = prevY < touchPoint.pageY;
								// changed direction? reset...
								if (data.up !== undefined && data.up != up) {
									$.extend(data, {
										touchStartY: touchPoint.pageY,
										touchStartTime: e.timeStamp
									});
								}
								$.extend(data, {
									up: up,
									touchY: touchPoint.pageY
								});
								this.menuScroll($sub, true, Math.abs(touchPoint.pageY - prevY));
							}
							e.preventDefault();
						} else { // touchend/pointerup
							if (data.touchY !== undefined) {
								if (data.momentum = Math.pow(Math.abs(touchPoint.pageY - data.touchStartY) / (e.timeStamp - data.touchStartTime), 2) * 15) {
									this.menuScrollStop($sub);
									this.menuScroll($sub);
									e.preventDefault();
								}
								delete data.touchY;
							}
						}
					}
				}
			},
			menuShow: function($sub) {
				if (!$sub.dataSM('beforefirstshowfired')) {
					$sub.dataSM('beforefirstshowfired', true);
					if (this.$root.triggerHandler('beforefirstshow.smapi', $sub[0]) === false) {
						return;
					}
				}
				if (this.$root.triggerHandler('beforeshow.smapi', $sub[0]) === false) {
					return;
				}
				$sub.dataSM('shown-before', true)
					.stop(true, true);
				if (!$sub.is(':visible')) {
					// highlight parent item
					var $a = $sub.dataSM('parent-a');
					if (this.opts.keepHighlighted || this.isCollapsible()) {
						$a.addClass('highlighted');
					}
					if (this.isCollapsible()) {
						$sub.removeClass('sm-nowrap').css({ zIndex: '', width: 'auto', minWidth: '', maxWidth: '', top: '', left: '', marginLeft: '', marginTop: '' });
					} else {
						// set z-index
						$sub.css('z-index', this.zIndexInc = (this.zIndexInc || this.getStartZIndex()) + 1);
						// min/max-width fix - no way to rely purely on CSS as all UL's are nested
						if (this.opts.subMenusMinWidth || this.opts.subMenusMaxWidth) {
							$sub.css({ width: 'auto', minWidth: '', maxWidth: '' }).addClass('sm-nowrap');
							if (this.opts.subMenusMinWidth) {
							 	$sub.css('min-width', this.opts.subMenusMinWidth);
							}
							if (this.opts.subMenusMaxWidth) {
							 	var noMaxWidth = this.getWidth($sub);
							 	$sub.css('max-width', this.opts.subMenusMaxWidth);
								if (noMaxWidth > this.getWidth($sub)) {
									$sub.removeClass('sm-nowrap').css('width', this.opts.subMenusMaxWidth);
								}
							}
						}
						this.menuPosition($sub);
						// insert IE iframe shim
						if ($sub.dataSM('ie-shim')) {
							$sub.dataSM('ie-shim').insertBefore($sub);
						}
					}
					var complete = function() {
						// fix: "overflow: hidden;" is not reset on animation complete in jQuery < 1.9.0 in Chrome when global "box-sizing: border-box;" is used
						$sub.css('overflow', '');
					};
					// if sub is collapsible (mobile view)
					if (this.isCollapsible()) {
						if (this.opts.collapsibleShowFunction) {
							this.opts.collapsibleShowFunction.call(this, $sub, complete);
						} else {
							$sub.show(this.opts.collapsibleShowDuration, complete);
						}
					} else {
						if (this.opts.showFunction) {
							this.opts.showFunction.call(this, $sub, complete);
						} else {
							$sub.show(this.opts.showDuration, complete);
						}
					}
					// accessibility
					$a.attr('aria-expanded', 'true');
					$sub.attr({
						'aria-expanded': 'true',
						'aria-hidden': 'false'
					});
					// store sub menu in visible array
					this.visibleSubMenus.push($sub);
					this.$root.triggerHandler('show.smapi', $sub[0]);
				}
			},
			popupHide: function(noHideTimeout) {
				if (this.hideTimeout) {
					clearTimeout(this.hideTimeout);
					this.hideTimeout = 0;
				}
				var self = this;
				this.hideTimeout = setTimeout(function() {
					self.menuHideAll();
				}, noHideTimeout ? 1 : this.opts.hideTimeout);
			},
			popupShow: function(left, top) {
				if (!this.opts.isPopup) {
					alert('SmartMenus jQuery Error:\n\nIf you want to show this menu via the "popupShow" method, set the isPopup:true option.');
					return;
				}
				if (this.hideTimeout) {
					clearTimeout(this.hideTimeout);
					this.hideTimeout = 0;
				}
				this.$root.dataSM('shown-before', true)
					.stop(true, true);
				if (!this.$root.is(':visible')) {
					this.$root.css({ left: left, top: top });
					// IE iframe shim
					this.menuIframeShim(this.$root);
					if (this.$root.dataSM('ie-shim')) {
						this.$root.dataSM('ie-shim').css({ zIndex: this.$root.css('z-index'), width: this.getWidth(this.$root), height: this.getHeight(this.$root), left: left, top: top }).insertBefore(this.$root);
					}
					// show menu
					var self = this,
						complete = function() {
							self.$root.css('overflow', '');
						};
					if (this.opts.showFunction) {
						this.opts.showFunction.call(this, this.$root, complete);
					} else {
						this.$root.show(this.opts.showDuration, complete);
					}
					this.visibleSubMenus[0] = this.$root;
				}
			},
			refresh: function() {
				this.destroy(true);
				this.init(true);
			},
			rootKeyDown: function(e) {
				if (!this.handleEvents()) {
					return;
				}
				switch (e.keyCode) {
					case 27: // reset on Esc
						var $activeTopItem = this.activatedItems[0];
						if ($activeTopItem) {
							this.menuHideAll();
							$activeTopItem[0].focus();
							var $sub = $activeTopItem.dataSM('sub');
							if ($sub) {
								this.menuHide($sub);
							}
						}
						break;
					case 32: // activate item's sub on Space
						var $target = $(e.target);
						if ($target.is('a') && this.handleItemEvents($target)) {
							var $sub = $target.dataSM('sub');
							if ($sub && !$sub.is(':visible')) {
								this.itemClick({ currentTarget: e.target });
								e.preventDefault();
							}
						}
						break;
				}
			},
			rootOut: function(e) {
				if (!this.handleEvents() || this.isTouchMode() || e.target == this.$root[0]) {
					return;
				}
				if (this.hideTimeout) {
					clearTimeout(this.hideTimeout);
					this.hideTimeout = 0;
				}
				if (!this.opts.showOnClick || !this.opts.hideOnClick) {
					var self = this;
					this.hideTimeout = setTimeout(function() { self.menuHideAll(); }, this.opts.hideTimeout);
				}
			},
			rootOver: function(e) {
				if (!this.handleEvents() || this.isTouchMode() || e.target == this.$root[0]) {
					return;
				}
				if (this.hideTimeout) {
					clearTimeout(this.hideTimeout);
					this.hideTimeout = 0;
				}
			},
			winResize: function(e) {
				if (!this.handleEvents()) {
					// we still need to resize the disable overlay if it's visible
					if (this.$disableOverlay) {
						var pos = this.$root.offset();
	 					this.$disableOverlay.css({
							top: pos.top,
							left: pos.left,
							width: this.$root.outerWidth(),
							height: this.$root.outerHeight()
						});
					}
					return;
				}
				// hide sub menus on resize - on mobile do it only on orientation change
				if (!('onorientationchange' in window) || e.type == 'orientationchange') {
					var isCollapsible = this.isCollapsible();
					// if it was collapsible before resize and still is, don't do it
					if (!(this.wasCollapsible && isCollapsible)) { 
						if (this.activatedItems.length) {
							this.activatedItems[this.activatedItems.length - 1][0].blur();
						}
						this.menuHideAll();
					}
					this.wasCollapsible = isCollapsible;
				}
			}
		}
	});

	$.fn.dataSM = function(key, val) {
		if (val) {
			return this.data(key + '_smartmenus', val);
		}
		return this.data(key + '_smartmenus');
	};

	$.fn.removeDataSM = function(key) {
		return this.removeData(key + '_smartmenus');
	};

	$.fn.smartmenus = function(options) {
		if (typeof options == 'string') {
			var args = arguments,
				method = options;
			Array.prototype.shift.call(args);
			return this.each(function() {
				var smartmenus = $(this).data('smartmenus');
				if (smartmenus && smartmenus[method]) {
					smartmenus[method].apply(smartmenus, args);
				}
			});
		}
		// [data-sm-options] attribute on the root UL
		var dataOpts = this.data('sm-options') || null;
		if (dataOpts) {
			try {
				dataOpts = eval('(' + dataOpts + ')');
			} catch(e) {
				dataOpts = null;
				alert('ERROR\n\nSmartMenus jQuery init:\nInvalid "data-sm-options" attribute value syntax.');
			};
		}
		return this.each(function() {
			new $.SmartMenus(this, $.extend({}, $.fn.smartmenus.defaults, options, dataOpts));
		});
	};

	// default settings
	$.fn.smartmenus.defaults = {
		isPopup:		false,		// is this a popup menu (can be shown via the popupShow/popupHide methods) or a permanent menu bar
		mainMenuSubOffsetX:	0,		// pixels offset from default position
		mainMenuSubOffsetY:	0,		// pixels offset from default position
		subMenusSubOffsetX:	0,		// pixels offset from default position
		subMenusSubOffsetY:	0,		// pixels offset from default position
		subMenusMinWidth:	'10em',		// min-width for the sub menus (any CSS unit) - if set, the fixed width set in CSS will be ignored
		subMenusMaxWidth:	'20em',		// max-width for the sub menus (any CSS unit) - if set, the fixed width set in CSS will be ignored
		subIndicators: 		true,		// create sub menu indicators - creates a SPAN and inserts it in the A
		subIndicatorsPos: 	'prepend',	// position of the SPAN relative to the menu item content ('prepend', 'append')
		subIndicatorsText:	'+',		// [optionally] add text in the SPAN (e.g. '+') (you may want to check the CSS for the sub indicators too)
		scrollStep: 		30,		// pixels step when scrolling long sub menus that do not fit in the viewport height
		scrollAccelerate:	true,		// accelerate scrolling or use a fixed step
		showTimeout:		250,		// timeout before showing the sub menus
		hideTimeout:		500,		// timeout before hiding the sub menus
		showDuration:		0,		// duration for show animation - set to 0 for no animation - matters only if showFunction:null
		showFunction:		null,		// custom function to use when showing a sub menu (the default is the jQuery 'show')
							// don't forget to call complete() at the end of whatever you do
							// e.g.: function($ul, complete) { $ul.fadeIn(250, complete); }
		hideDuration:		0,		// duration for hide animation - set to 0 for no animation - matters only if hideFunction:null
		hideFunction:		function($ul, complete) { $ul.fadeOut(200, complete); },	// custom function to use when hiding a sub menu (the default is the jQuery 'hide')
							// don't forget to call complete() at the end of whatever you do
							// e.g.: function($ul, complete) { $ul.fadeOut(250, complete); }
		collapsibleShowDuration:0,		// duration for show animation for collapsible sub menus - matters only if collapsibleShowFunction:null
		collapsibleShowFunction:function($ul, complete) { $ul.slideDown(200, complete); },	// custom function to use when showing a collapsible sub menu
							// (i.e. when mobile styles are used to make the sub menus collapsible)
		collapsibleHideDuration:0,		// duration for hide animation for collapsible sub menus - matters only if collapsibleHideFunction:null
		collapsibleHideFunction:function($ul, complete) { $ul.slideUp(200, complete); },	// custom function to use when hiding a collapsible sub menu
							// (i.e. when mobile styles are used to make the sub menus collapsible)
		showOnClick:		false,		// show the first-level sub menus onclick instead of onmouseover (i.e. mimic desktop app menus) (matters only for mouse input)
		hideOnClick:		true,		// hide the sub menus on click/tap anywhere on the page
		noMouseOver:		false,		// disable sub menus activation onmouseover (i.e. behave like in touch mode - use just mouse clicks) (matters only for mouse input)
		keepInViewport:		true,		// reposition the sub menus if needed to make sure they always appear inside the viewport
		keepHighlighted:	true,		// keep all ancestor items of the current sub menu highlighted (adds the 'highlighted' class to the A's)
		markCurrentItem:	false,		// automatically add the 'current' class to the A element of the item linking to the current URL
		markCurrentTree:	true,		// add the 'current' class also to the A elements of all ancestor items of the current item
		rightToLeftSubMenus:	false,		// right to left display of the sub menus (check the CSS for the sub indicators' position)
		bottomToTopSubMenus:	false,		// bottom to top display of the sub menus
		overlapControlsInIE:	true		// make sure sub menus appear on top of special OS controls in IE (i.e. SELECT, OBJECT, EMBED, etc.)
	};

	return $;
}));

jQuery( window ).on( 'elementor/frontend/init', function() {

    var MenuHandler = elementorModules.frontend.handlers.Base.extend({
      stretchElement: null,
      getDefaultSettings: function () {
        return {
          selectors: {
            menu: '.twbb-nav-menu',
            dropdownMenu: '.twbb-nav-menu__container.twbb-nav-menu--dropdown',
            menuToggle: '.twbb-menu-toggle'
          }
        };
      },
      getDefaultElements: function () {
        var selectors = this.getSettings('selectors'),
          elements = {};
        elements.$menu = this.$element.find(selectors.menu);
        elements.$dropdownMenu = this.$element.find(selectors.dropdownMenu);
        elements.$dropdownMenuFinalItems = elements.$dropdownMenu.find('.menu-item:not(.menu-item-has-children) > a');
        elements.$menuToggle = this.$element.find(selectors.menuToggle);
        return elements;
      },
      bindEvents: function () {
        if (!this.elements.$menu.length) {
          return;
        }
        this.elements.$menuToggle.on('click', this.toggleMenu.bind(this));
        this.elements.$dropdownMenuFinalItems.on('click', this.toggleMenu.bind(this, false));
        elementorFrontend.addListenerOnce(this.$element.data('model-cid'), 'resize', this.stretchMenu);
      },
      initStretchElement: function () {
        this.stretchElement = new elementorFrontend.modules.StretchElement({element: this.elements.$dropdownMenu});
      },
      toggleMenu: function (show) {
        var $dropdownMenu = this.elements.$dropdownMenu,
          isDropdownVisible = this.elements.$menuToggle.hasClass('twbb-active');
        if ('boolean' !== typeof show) {
          show = !isDropdownVisible;
        }
        this.elements.$menuToggle.toggleClass('twbb-active', show);
        if (show) {
          $dropdownMenu.hide().slideDown(250, function () {
            $dropdownMenu.css('display', '');
          });
          if (this.getElementSettings('full_width')) {
            this.stretchElement.stretch();
          }
        }
        else {
          $dropdownMenu.show().slideUp(250, function () {
            $dropdownMenu.css('display', '');
          });
        }
      },
      stretchMenu: function () {
        if (this.getElementSettings('full_width')) {
          this.stretchElement.stretch();
          this.elements.$dropdownMenu.css('top', this.elements.$menuToggle.outerHeight());
        }
        else {
          this.stretchElement.reset();
        }
      },
      onInit: function () {

        /*
        * Customization
        * for adding clickable button to navigate to 10Web dashboard
         */
        if ( jQuery('body').hasClass('elementor-editor-active') ) {
          jQuery('.ai-recreated-menu-item').parent().css('gap','3px');
          jQuery('.ai-recreated-menu-item').each(function () {
            if ( jQuery(this).children('button').length == 0 ) {
              jQuery(this).children('a').text('+ Add page');
              let item_width, item_height;
              item_width = jQuery(this).width();
              item_height = jQuery(this).height();
              jQuery(this).prepend('<button class="twbb-add_new_page"' +
                  'style="height:'+ item_height + 'px;width:' + item_width + 'px;"></button>');
            }
          });
          jQuery('.twbb-add_new_page').click(function (e) {
            let nav_ul_class, nav_li_class, re_menu_term_id, re_menu_item_id, re_menu_item_position, menu_term_id, menu_item_id,
                menu_item_position;
            nav_ul_class = jQuery(this).closest('ul').attr('class');
            nav_li_class = jQuery(this).closest('li').attr('class');

            re_menu_term_id = new RegExp('twbb-menu_term_id-' + "\\s*(\\d+)");
            re_menu_item_id = new RegExp('menu-item-' + "\\s*(\\d+)");
            re_menu_item_position = new RegExp('twbb_menu_order_' + "\\s*(\\d+)");

            menu_term_id = nav_ul_class.match(re_menu_term_id);
            menu_item_id = nav_li_class.match(re_menu_item_id);
            menu_item_position = nav_li_class.match(re_menu_item_position);

            if (menu_term_id && menu_item_id && menu_item_position) {
              window.open(twbb.tenweb_dashboard + '/websites/' + twbb.dashboard_website_id + '/ai-builder?add_page=1&menu_term_id=' + menu_term_id[1] + '&menu_item_id=' + menu_item_id[1] + '&menu_item_position=' + menu_item_position[1], '_blank');
            }
          });
        }
        // end of customization

          elementorModules.frontend.handlers.Base.prototype.onInit.apply(this, arguments);
        if (!this.elements.$menu.length) {
          return;
        }
        this.elements.$menu.smartmenus({
          subIndicatorsText: '<i class="fa"></i>',
          subIndicatorsPos: 'append',
          subMenusMaxWidth: '1000px',
        });
        this.initStretchElement();
        this.stretchMenu();
      },
      onElementChange: function (propertyName) {
        if ('full_width' === propertyName) {
          this.stretchMenu();
        }
      }
    });

  elementorFrontend.hooks.addAction( 'frontend/element_ready/twbb-nav-menu.default', function ( $scope ) {
    if ( jQuery.fn.smartmenus ) {
      // Override the default stupid detection
      jQuery.SmartMenus.prototype.isCSSOn = function() {
        return true;
      };

      if ( elementorFrontend.config.is_rtl  ) {
        jQuery.fn.smartmenus.defaults.rightToLeftSubMenus = true;
      }
    }

    new MenuHandler( { $element: $scope } );
  });
});

var twbb_widgets = [];
var twbb_posts = function (args, name) {

    var _this = this;

    var current_page = 1;
    var template = "";
    var $container = null;
    var $pagination = null;
    var $widget_container = null;
    var $loading = null;
    var is_editor = (typeof elementor !== "undefined");

    this.query_args = args.query_args;
    this.query_args_hash = args.query_args_hash;
    this.widget_id = args.widget_id;
    this.settings = args.settings;
    this.posts = [];
    this.pages_count = 1;

    this.init = function () {
        set_html_elements();
        set_template();
        this.get_posts();
    };

    this.render = function () {
        var html, i;

        this.clear_html();
        var compiled = _.template(template);

        if (this.posts.length === 0) {
            $widget_container.addClass('empty-posts');
            $widget_container.append('<p>No posts found.</p>');
            return;
        }

        for (i in this.posts) {
            html = compiled(this.posts[i]);
            $widget_container.append(html);
        }
        this.display_separators();

        if (this.settings.masonry === "yes") {
            this.masonry();
        }

        if (this.settings.pagination === "yes" && this.pages_count > 1) {
            this.pagination();
        }
    };

    this.get_posts = function () {

        this.show_loading();

        if (current_page === 1 && typeof args.first_page_data !== "undefined") {
            _this.posts = args.first_page_data.posts;
            _this.pages_count = args.first_page_data.pages_count;
            _this.render();
            _this.hide_loading();
            return;
        }

        jQuery.post(twbb.ajaxurl, {
            action: 'twbb_widgets',
            widget_name: "posts",
            query_args: _this.query_args,
            query_args_hash: _this.query_args_hash,
            page: current_page,
            nonce: twbb.nonce
        }).done(function (data) {
            _this.posts = data.data.posts;
            _this.pages_count = parseInt(data.data.pages_count);
            _this.render();
            _this.hide_loading();
        }).fail(function (data) {
            _this.hide_loading();
        });

    };

    this.display_separators = function () {
        jQuery('.twbb-posts-meta-data').each(function () {

            var last_item = null;
            jQuery(this).find('.twbb-posts-meta-separator').each(function () {
                if (jQuery(this).prev().html() !== "") {
                    jQuery(this).addClass('twbb-posts-active-meta-separator');
                    last_item = jQuery(this);
                }
            });
            if ( last_item !== null ) {
                last_item.removeClass('twbb-posts-active-meta-separator');
            }
        });
    };

    this.masonry = function () {
        var $msnry = $widget_container.imagesLoaded(function () {
            // init Masonry after all images have loaded
            $msnry.masonry({
                gutter: _this.settings.masonry_column_gap.size,
                itemSelector: '.twbb-posts-item'
            }).masonry('reloadItems');
        });

    };

    this.pagination = function () {
        var html = "";

        var deactive_class = 'twbb-posts-page-deactive';
        var class_name = "";

        if (this.settings.pagination_first_last_buttons === "yes") {
            class_name = 'twbb-posts-page twbb-posts-page-first';
            if (current_page === 1) {
                class_name += ' ' + deactive_class;
            }

            html += get_page_link_html(class_name, 1, this.settings.pagination_first_label);
        }

        if (this.settings.pagination_next_prev_buttons === "yes") {
            class_name = 'twbb-posts-page twbb-posts-page-prev';
            if (current_page === 1) {
                class_name += ' ' + deactive_class;
            }

            html += get_page_link_html(class_name, current_page - 1, this.settings.pagination_prev_label);
        }

        var length = (this.pages_count > this.settings.pagination_page_limit) ? this.settings.pagination_page_limit : this.pages_count;
        if (this.settings.pagination_number_buttons === "yes") {
            for (var i = 1; i <= length; i++) {
                class_name = 'twbb-posts-page twbb-posts-page-num';
                if (i === current_page) {
                    class_name += ' twbb-posts-current-page ' + deactive_class;
                }

                html += get_page_link_html(class_name, i, i);

            }
        }

        if (this.settings.pagination_next_prev_buttons === "yes") {
            class_name = 'twbb-posts-page twbb-posts-page-next';
            if (current_page === this.pages_count) {
                class_name += ' ' + deactive_class;
            }

            html += get_page_link_html(class_name, current_page + 1, this.settings.pagination_next_label);
        }

        if (this.settings.pagination_first_last_buttons === "yes") {
            class_name = 'twbb-posts-page twbb-posts-page-last';
            if (current_page === this.pages_count) {
                class_name += ' ' + deactive_class;
            }

            html += get_page_link_html(class_name, length, this.settings.pagination_last_label);
        }

        if ($pagination === null) {
            if (this.settings.pagination_scroll_top === "yes") {
                html = "<div class='twbb-posts-pagination twbb-pagination_scroll_top'>" + html + "</div>";
            } else {
                html = "<div class='twbb-posts-pagination'>" + html + "</div>";
            }
            $widget_container.parent().append(html);
            $pagination = $container.find('.twbb-posts-pagination');
        } else {
            $pagination.append(html);
        }

        $pagination.find('.twbb-posts-page').on('click', function (e) {
            e.preventDefault();

            if (is_editor === true) {
                return false;
            }

            var page = parseInt(jQuery(this).data('page'));
            if (page < 1 || page > _this.pages_count) {
                return false;
            }

            current_page = page;
            _this.get_posts();
            if ( jQuery(this).parent().hasClass('twbb-pagination_scroll_top')) {
                jQuery(window).scrollTop(0);
            }
            return false;
        });
    };

    this.show_loading = function () {
        if ($loading === null) {
            $container.append('<div class="twbb-posts-loading"><i class="twbb-spinner-solid"></i></div>');
            $loading = jQuery($container.find('.twbb-posts-loading'));
        } else {
            $loading.show();
        }
    };

    this.hide_loading = function () {
        $loading.hide();
    };

    function set_html_elements() {
        $container = jQuery('div[data-id="' + _this.widget_id + '"]');
        if ( 0 == $container.length ) { /* Global widget */
            $container = jQuery('.elementor-global-' + _this.widget_id);
        }
        $widget_container = $container.find('.twbb-posts-widget-container');
    }

    function set_template() {
        settings = _this.settings;

        template = "";


        var img_template = "";
        var title_template = "";

        if (settings.show_image === "yes") {
            img_template = "<% if(twbb_image != '') { %><div class='twbb-posts-image'><img src='<%= twbb_image %>'/></div><% } %>";
        }

        if (settings.show_title === "yes") {
            title_template += "<div class='twbb-posts-title'>" +
                "<" + settings.title_tag + " class='twbb-posts-title-tag'><a href='<%= twbb_permalink %>'><%= post_title %></a></" + settings.title_tag + ">" +
                "</div>";
        }

        if (settings.image_position === "above_title") {
            template += img_template + title_template;
        } else {
            template += title_template + img_template;
        }

        if (typeof settings.meta_data !== "undefined" && settings.meta_data.length > 0) {
            template += "<div class='twbb-posts-meta-data'>";
            for (var i = 0; i < settings.meta_data.length; i++) {
                switch (settings.meta_data[i]) {
                    case "author":
                        template += '<span class="twbb-posts-author-meta"><% print(posts_print_author(twbb_author)) %></span>';
                        break;
                    case "date":
                        template += '<span class="twbb-posts-date-meta"><%= twbb_date %></span>';
                        break;
                    case "time":
                        template += '<span class="twbb-posts-time-meta"><%= twbb_time %></span>';
                        break;
                    case "comments":
                        template += '<span class="twbb-posts-comments-meta">' +
                            '<% if(twbb_comments > 0) { %><%=  twbb_comments %> <% }else{ print("No") } print(" comments")%>' +
                            '</span>';
                        break;
                    case "categories":
                        template += '<span class="twbb-posts-categories-meta"><% print(posts_print_terms(twbb_categories, "categories")) %></span>';
                        break;
                    case "tags":
                        template += '<span class="twbb-posts-tags-meta"><% print(posts_print_terms(twbb_tags, "tags")) %></span>';
                        break;
                }

                template += '<span class="twbb-posts-meta-separator">' + settings.meta_separator + '</span>';
            }
            template += "</div>";

        }

        if (settings.show_excerpt === "yes") {
            template += "<div class='twbb-posts-content'><%= twbb_excerpt %></div>";
        }

        if (settings.show_read_more === "yes") {
            template += "<div class='twbb-posts-read-more'>" +
                "<a href='<%= twbb_permalink %>'>" + settings.read_more_text + "</a>" +
                "</div>";
        }

        template = '<div class="twbb-posts-item">' + template + '</div>';
    }

    get_page_link_html = function (class_name, page, text) {
        return "<a href='#' class='" + class_name + "' data-page='" + page + "'>" + text + "</a>";
    };

    posts_print_author = function (twbb_author) {

        if (_this.settings.author_meta_link === "yes") {
            return "<a href='" + twbb_author.link + "'>" + twbb_author.name + "</a>";
        } else {
            return twbb_author.name;
        }
    };

    posts_print_terms = function (terms, tax) {
        var html = "";
        var prefix = (tax === "tags") ? "#" : "";
        var link = (
            (tax === "categories" && _this.settings.categories_meta_link === "yes") ||
            (tax === "tags" && _this.settings.tags_meta_link === "yes")
        );


        for (var i in terms) {
            if (link === true) {
                html += "<a href='" + terms[i].link + "'>" + prefix + terms[i].name + "</a>, ";
            } else {
                html += prefix + terms[i].name + ", ";
            }
        }

        return html.trim().slice(0, html.length - 2);
    };

    this.clear_html = function () {

        if ($widget_container !== null) {
            $widget_container.html('');
        }

        if ($pagination !== null) {
            $pagination.html('');

            if (_this.settings.masonry === "yes") {
                $widget_container.masonry('destroy');
            }
        }

    };

    this.init();
    twbb_add_widget(name, this);
};

function twbb_add_widget(name, widget) {
  if (typeof twbb_widgets[name] === "undefined") {
    twbb_widgets[name] = [];
  }
  twbb_widgets[name].push(widget);
}

function twbb_get_widgets(name) {
  if (typeof twbb_widgets[name] === "undefined") {
    return [];
  }
  else {
    return twbb_widgets[name];
  }
}

function twbb_is_widget_added(name) {
  return (jQuery('.elementor-widget-' + name).length > 0);
}

jQuery( window ).on( 'elementor/frontend/init', function () {
    var twbb_posts_ready = function ( $scope ) {
        var $element = $scope.find( '.twbb-posts-widget-container' );

        new twbb_posts( JSON.parse( $element.attr('data-params') ), $element.attr('data-widget'));
    };
    elementorFrontend.hooks.addAction('frontend/element_ready/twbb-posts.default', twbb_posts_ready );
    elementorFrontend.hooks.addAction('frontend/element_ready/twbb-posts-archive.default', twbb_posts_ready );
});



jQuery( window ).on( 'elementor/frontend/init', function () {

    class LoadMore extends elementorModules.frontend.handlers.Base {
        getDefaultSettings() {
            return {
                selectors: {
                    postsContainer: '.elementor-posts-container',
                    postWrapperTag: 'article',
                    loadMoreButton: '.elementor-button',
                    loadMoreSpinnerWrapper: '.e-load-more-spinner',
                    loadMoreSpinner: '.e-load-more-spinner i, .e-load-more-spinner svg',
                    loadMoreAnchor: '.e-load-more-anchor'
                },
                classes: {
                    loadMoreSpin: 'eicon-animation-spin',
                    loadMoreIsLoading: 'e-load-more-pagination-loading',
                    loadMorePaginationEnd: 'e-load-more-pagination-end',
                    loadMoreNoSpinner: 'e-load-more-no-spinner'
                }
            };
        }
        getDefaultElements() {
            const selectors = this.getSettings('selectors');
            return {
                postsWidgetWrapper: this.$element[0],
                postsContainer: this.$element[0].querySelector(selectors.postsContainer),
                loadMoreButton: this.$element[0].querySelector(selectors.loadMoreButton),
                loadMoreSpinnerWrapper: this.$element[0].querySelector(selectors.loadMoreSpinnerWrapper),
                loadMoreSpinner: this.$element[0].querySelector(selectors.loadMoreSpinner),
                loadMoreAnchor: this.$element[0].querySelector(selectors.loadMoreAnchor)
            };
        }
        bindEvents() {
            super.bindEvents();

            // Handle load more functionality for on-click type.
            if (!this.elements.loadMoreButton) {
                return;
            }
            this.elements.loadMoreButton.addEventListener('click', event => {
                if (this.isLoading) {
                    return;
                }
                event.preventDefault();
                this.handlePostsQuery();
            });
        }
        onInit() {
            super.onInit();
            this.classes = this.getSettings('classes');
            this.isLoading = false;
            const paginationType = this.getElementSettings('pagination_type');
            if ('load_more_on_click' !== paginationType && 'load_more_infinite_scroll' !== paginationType) {
                return;
            }
            this.isInfinteScroll = 'load_more_infinite_scroll' === paginationType;

            // When spinner is not available, the button's text should not be hidden.
            this.isSpinnerAvailable = this.getElementSettings('load_more_spinner').value;
            if (!this.isSpinnerAvailable) {
                this.elements.postsWidgetWrapper.classList.add(this.classes.loadMoreNoSpinner);
            }
            if (this.isInfinteScroll) {
                this.handleInfiniteScroll();
            } else if (this.elements.loadMoreSpinnerWrapper && this.elements.loadMoreButton) {
                // Instead of creating 2 spinners for on-click and infinity-scroll, one spinner will be used so it should be appended to the button in on-click mode.
                this.elements.loadMoreButton.insertAdjacentElement('beforeEnd', this.elements.loadMoreSpinnerWrapper);
            }

            // Set the post id and element id for the ajax request.
            this.elementId = this.getID();
            this.postId = elementorFrontendConfig.post.id;

            // Set the current page and last page for handling the load more post and when no more posts to show.
            if (this.elements.loadMoreAnchor) {
                this.currentPage = parseInt(this.elements.loadMoreAnchor.getAttribute('data-page'));
                this.maxPage = parseInt(this.elements.loadMoreAnchor.getAttribute('data-max-page'));
                if (this.currentPage === this.maxPage || !this.currentPage) {
                    this.handleUiWhenNoPosts();
                }
            }
        }

        // Handle load more functionality for infinity-scroll type.
        handleInfiniteScroll() {
            if (this.isEdit) {
                return;
            }
            this.observer = elementorModules.utils.Scroll.scrollObserver({
                callback: event => {
                    if (!event.isInViewport || this.isLoading) {
                        return;
                    }

                    // When the observer is triggered it won't be triggered without scrolling, but sometimes there will be no scrollbar to trigger it again.
                    this.observer.unobserve(this.elements.loadMoreAnchor);
                    this.handlePostsQuery().then(() => {
                        if (this.currentPage !== this.maxPage) {
                            this.observer.observe(this.elements.loadMoreAnchor);
                        }
                    });
                }
            });
            this.observer.observe(this.elements.loadMoreAnchor);
        }
        handleUiBeforeLoading() {
            this.isLoading = true;
            if (this.elements.loadMoreSpinner) {
                this.elements.loadMoreSpinner.classList.add(this.classes.loadMoreSpin);
            }
            this.elements.postsWidgetWrapper.classList.add(this.classes.loadMoreIsLoading);
        }
        handleUiAfterLoading() {
            this.isLoading = false;
            if (this.elements.loadMoreSpinner) {
                this.elements.loadMoreSpinner.classList.remove(this.classes.loadMoreSpin);
            }
            if (this.isInfinteScroll && this.elements.loadMoreSpinnerWrapper && this.elements.loadMoreAnchor) {
                // Since the spinner has to be shown after the new content (posts), it should be appended after the anchor element.
                this.elements.loadMoreAnchor.insertAdjacentElement('afterend', this.elements.loadMoreSpinnerWrapper);
            }
            this.elements.postsWidgetWrapper.classList.remove(this.classes.loadMoreIsLoading);
        }
        handleUiWhenNoPosts() {
            this.elements.postsWidgetWrapper.classList.add(this.classes.loadMorePaginationEnd);
        }
        afterInsertPosts() {}
        handleSuccessFetch(result) {
            this.handleUiAfterLoading();
            const selectors = this.getSettings('selectors');

            // Grabbing only the new articles from the response without the existing ones (prevent posts duplication).
            const postsElements = result.querySelectorAll(`[data-id="${this.elementId}"] ${selectors.postsContainer} > ${selectors.postWrapperTag}`);
            const nextPageUrl = result.querySelector(`[data-id="${this.elementId}"] .e-load-more-anchor`).getAttribute('data-next-page');
            postsElements.forEach(element => this.elements.postsContainer.append(element));
            this.elements.loadMoreAnchor.setAttribute('data-page', this.currentPage);
            this.elements.loadMoreAnchor.setAttribute('data-next-page', nextPageUrl);
            if (this.currentPage === this.maxPage) {
                this.handleUiWhenNoPosts();
            }
            this.afterInsertPosts(postsElements, result);
        }
        handlePostsQuery() {
            this.handleUiBeforeLoading();
            this.currentPage++;
            const nextPageUrl = this.elements.loadMoreAnchor.getAttribute('data-next-page');
            return fetch(nextPageUrl).then(response => response.text()).then(html => {
                // Convert the HTML string into a document object
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                this.handleSuccessFetch(doc);
            });
        }
    }

    elementorFrontend.hooks.addAction('frontend/element_ready/tenweb-posts.cards', function ($scope) {
        let ob = new LoadMore({$element: $scope});
        ob.bindEvents();
    });

    elementorFrontend.hooks.addAction('frontend/element_ready/tenweb-posts.classic', function ($scope) {
        let ob = new LoadMore({$element: $scope});
        ob.bindEvents();
    });
    elementorFrontend.hooks.addAction('frontend/element_ready/tenweb-posts.full_content', function ($scope) {
        let ob = new LoadMore({$element: $scope});
        ob.bindEvents();
    });

    var TenwebPosts = elementorModules.frontend.handlers.Base.extend({
        getSkinPrefix() {

            let skinName = this.elements.$postsContainer.attr('skin');
            return skinName + '_';
        },
        bindEvents() {
            elementorFrontend.addListenerOnce(this.getModelCID(), 'resize', this.onWindowResize);
        },
        unbindEvents() {
            elementorFrontend.removeListeners(this.getModelCID(), 'resize', this.onWindowResize);
        },
        getClosureMethodsNames() {
            return elementorModules.frontend.handlers.Base.prototype.getClosureMethodsNames.apply(this, arguments).concat(['fitImages', 'onWindowResize']);
        },
        getDefaultSettings() {
            return {
                classes: {
                    fitHeight: 'elementor-fit-height',
                    hasItemRatio: 'elementor-has-item-ratio'
                },
                selectors: {
                    postsContainer: '.elementor-posts-container',
                    post: '.elementor-post',
                    postThumbnail: '.elementor-post__thumbnail',
                    postThumbnailImage: '.elementor-post__thumbnail img'
                }
            };
        },
        getDefaultElements() {
            var selectors = this.getSettings('selectors');
            return {
                $postsContainer: this.$element.find(selectors.postsContainer),
                $posts: this.$element.find(selectors.post)
            };
        },
        fitImage($post) {
            var settings = this.getSettings(),
                $imageParent = $post.find(settings.selectors.postThumbnail),
                $image = $imageParent.find('img'),
                image = $image[0];
            if (!image) {
                return;
            }
            var imageParentRatio = $imageParent.outerHeight() / $imageParent.outerWidth(),
                imageRatio = image.naturalHeight / image.naturalWidth;
            $imageParent.toggleClass(settings.classes.fitHeight, imageRatio < imageParentRatio);
        },
        fitImages() {
            var $ = jQuery,
                self = this,
                itemRatio = getComputedStyle(this.$element[0], ':after').content,
                settings = this.getSettings();
/*
            if (self.isMasonryEnabled()) {
                this.elements.$postsContainer.removeClass(settings.classes.hasItemRatio);
                return;
            }
*/
            this.elements.$postsContainer.toggleClass(settings.classes.hasItemRatio, !!itemRatio.match(/\d/));
            this.elements.$posts.each(function () {
                var $post = $(this),
                    $image = $post.find(settings.selectors.postThumbnailImage);
                self.fitImage($post);
                $image.on('load', function () {
                    self.fitImage($post);
                });
            });
        },
        setColsCountSettings() {
            const settings = this.getElementSettings(),
                skinPrefix = this.getSkinPrefix(),
                colsCount = elementorFrontend.utils.controls.getResponsiveControlValue(settings, `${skinPrefix}columns`);
            this.setSettings('colsCount', colsCount);
        },
        isMasonryEnabled() {
            return !!this.getElementSettings(this.getSkinPrefix() + 'masonry');
        },
        initMasonry() {
            imagesLoaded(this.elements.$posts, this.runMasonry);
        },
        getVerticalSpaceBetween() {
            /* The `verticalSpaceBetween` variable is set up in a way that supports older versions of the portfolio widget */
            let verticalSpaceBetween = elementorFrontend.utils.controls.getResponsiveControlValue(this.getElementSettings(), `${this.getSkinPrefix()}row_gap`, 'size');
            if ('' === this.getSkinPrefix() && '' === verticalSpaceBetween) {
                verticalSpaceBetween = this.getElementSettings('item_gap.size');
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
            var colsCount = this.getSettings('colsCount'),
                hasMasonry = this.isMasonryEnabled() && colsCount >= 2;
            elements.$postsContainer.toggleClass('elementor-posts-masonry', hasMasonry);
            if (!hasMasonry) {
                elements.$postsContainer.height('');
                return;
            }
            const verticalSpaceBetween = this.getVerticalSpaceBetween();
            var masonry = new elementorModules.utils.Masonry({
                container: elements.$postsContainer,
                items: elements.$posts.filter(':visible'),
                columnsCount: this.getSettings('colsCount'),
                verticalSpaceBetween: verticalSpaceBetween || 0
            });
            masonry.run();
        },
        run() {
            // For slow browsers
            setTimeout(this.fitImages, 0);
            this.initMasonry();
        },
        onInit() {
            elementorModules.frontend.handlers.Base.prototype.onInit.apply(this, arguments);
            this.bindEvents();
            this.run();
        },
        onWindowResize() {
            this.fitImages();
            this.runMasonry();
        },
        onElementChange() {
            this.fitImages();
            setTimeout(this.runMasonry);
        }
    });

    class PostsSlider {
        onInint() {
            var self = this;
            jQuery('.tenweb-posts-slider').each(async function(i,elem) {
                var id = jQuery(elem).parents('.elementor-widget-tenweb-posts').attr('data-id');
                jQuery(elem).attr('id', 'tenweb-posts-slider-swiper-' + id);
                var settings = jQuery(elem).data('settings');

                if ( ! jQuery.isEmptyObject(settings) ) {

                    settings.slidesPerView = {
                        desktop: 3,
                        tablet: 2,
                        mobile: 1
                    };
                    var swiperOptions = {
                        grabCursor: true,
                        effect: 'slide',
                        //initialSlide: self.getInitialSlide( settings ),
                        initialSlide: 0,
                        slidesPerView: self.getDeviceSlidesPerView( 'desktop', settings ),
                        loop: 'yes' === settings.loop,
                    }
                    var breakpointsSettings = {},
                        breakpoints = elementorFrontend.config.breakpoints;

                    breakpointsSettings[breakpoints.lg - 1] = {
                        slidesPerView: self.getDeviceSlidesPerView( 'desktop', settings ),
                        slidesPerGroup: self.getSlidesToScroll( settings ),
                        spaceBetween: self.getSpaceBetween( 'desktop', settings )
                    }

                    breakpointsSettings[breakpoints.md - 1] = {
                        slidesPerView: self.getDeviceSlidesPerView( 'tablet', settings ),
                        slidesPerGroup: self.getSlidesToScroll( settings ),
                        spaceBetween: self.getSpaceBetween( 'tablet', settings )
                    };

                    breakpointsSettings[breakpoints.xs] = {
                        slidesPerView: self.getDeviceSlidesPerView( 'mobile', settings ),
                        slidesPerGroup: self.getSlidesToScroll( settings ),
                        spaceBetween: self.getSpaceBetween( 'mobile', settings )
                    };

                    swiperOptions.breakpoints = breakpointsSettings;

                    var showArrows = 'arrows' === settings.slider_navigation || 'both' === settings.slider_navigation,
                        pagination = 'dots' === settings.slider_navigation || 'both' === settings.slider_navigation;

                    if (showArrows) {
                        swiperOptions.navigation = {
                            prevEl: '.swiper-button-prev',
                            nextEl: '.swiper-button-next'
                        };
                    }

                    if (pagination) {
                        swiperOptions.pagination = {
                            el: '.swiper-pagination',
                            type: 'bullets',
                            clickable: true
                        };
                    }

                    if (true === swiperOptions.loop) {
                        swiperOptions.loopedSlides = settings.slides_count;
                    }

                    if ( settings.autoplay === 'yes' ) {
                        swiperOptions.autoplay = {
                            delay: settings.autoplay_speed,
                            disableOnInteraction: settings.disable_on_interaction === 'yes',
                            pauseOnMouseEnter: settings.pause_on_mouseover === 'yes',
                        }
                    }

                    const Swiper = elementorFrontend.utils.swiper;
                    await new Swiper( jQuery('#tenweb-posts-slider-swiper-' + id), swiperOptions );
                }
            });
        }
        getInitialSlide ( settings ) {
            return Math.floor( ( settings.slides_count - 1 ) / 2 );
        }

        getSlidesToScroll ( settings ) {
            return Math.min( settings.slides_count, +settings.slides_to_scroll || 1 );
        }

        getDeviceSlidesPerView( view , settings ) {
            var str = "slides_per_view" + ("desktop" === view ? "" : "_" + view);
            var num =	Math.min( settings.slides_count, +settings[str] || settings['slidesPerView'][view] );
            return num;
        }

        getSpaceBetween( view, settings ) {
            var str = "space_between";
            return view && "desktop" !== view && (str += "_" + view), settings.breakpoints[str].size || 0;
        }
    }

    elementorFrontend.hooks.addAction('frontend/element_ready/tenweb-posts.classic', function ($scope) {
        new TenwebPosts({$element: $scope});
        let postsSlider = new PostsSlider();
        postsSlider.onInint();
    });
    elementorFrontend.hooks.addAction('frontend/element_ready/tenweb-posts.cards', function ($scope) {
        new TenwebPosts({$element: $scope});
        let postsSlider = new PostsSlider();
        postsSlider.onInint();
    });
    elementorFrontend.hooks.addAction('frontend/element_ready/tenweb-posts.image_left', function ($scope) {
        new TenwebPosts({$element: $scope});
        let postsSlider = new PostsSlider();
        postsSlider.onInint();
    });
    elementorFrontend.hooks.addAction('frontend/element_ready/tenweb-posts.on_image', function ($scope) {
        new TenwebPosts({$element: $scope});
        let postsSlider = new PostsSlider();
        postsSlider.onInint();
    });
    elementorFrontend.hooks.addAction('frontend/element_ready/tenweb-posts.full_content', function ($scope) {
        new TenwebPosts({$element: $scope});
        let postsSlider = new PostsSlider();
        postsSlider.onInint();
    });
})
// jQuery( window ).on( 'elementor:init', function() {
//     elementor.hooks.addAction('panel/open_editor/widget/tenweb-posts', function (panel, model, view) {
//         /*
// * change is done in 1.27.X version
// * this is for insuring backward compatibility with the old version of the widget,
// * we remove old control and replace it's values to new one's
//   */
//         const controlIds = {
//             'tenweb-posts_include_term_ids': [
//                 'tenweb-posts_include_taxonomy_post_category',
//                 'tenweb-posts_include_taxonomy_post_post_tag'
//             ],
//             'tenweb-posts_exclude_term_ids': [
//                 'tenweb-posts_exclude_taxonomy_post_category',
//                 'tenweb-posts_exclude_taxonomy_post_post_tag'
//             ],
//         }
//
//         for (const oldControlId in controlIds) {
//             if (controlIds.hasOwnProperty(oldControlId)) {
//                 const newControlIds = controlIds[oldControlId];
//                 var termIdsControl = model.get('settings').get(oldControlId);
//                 if (termIdsControl.length > 0) {
//                     var termIds = termIdsControl;
//
//                     termIds.forEach(function (termId) {
//                         newControlIds.forEach(function (newControlId) {
//                             replaceOldControlWithNew({
//                                 'model': model,
//                                 'container': view.container,
//                                 'oldControlValue': termId,
//                                 'newControlId': newControlId
//                             });
//                         });
//                     });
//
//                     window.parent.$e.commands.run('document/elements/settings', {
//                         "container": view.container,
//                         settings: {
//                             [oldControlId]: ""
//                         }
//                     });
//                     window.parent.$e.run('document/save/default');
//                 }
//
//             }
//         }
//     });
// });
function replaceOldControlWithNew(args) {
    const newControlOptions = Object.keys(args['model'].attributes.settings.options.controls[args['newControlId']].options);
    if ( jQuery.inArray(args['oldControlValue'], newControlOptions) !== -1 ) {
        let appliedValues = args['model'].getSetting(args['newControlId']);
        if( appliedValues.length === 0 ) {
            appliedValues = [];
        }
        appliedValues.push(args['oldControlValue']);
        let settings = {
            [args['newControlId']]: appliedValues,
        }
        window.parent.$e.commands.run('document/elements/settings', {
            "container": args['container'],
            settings: settings
        });
    }
}


jQuery( window ).on( 'elementor/frontend/init', function() {
  var tenwebSearchBerHandler = elementorModules.frontend.handlers.Base.extend({
    getDefaultSettings: function () {
      return {
        selectors: {
          wrapper: '.tenweb-search-form',
          container: '.tenweb-search-form__container',
          icon: '.tenweb-search-form__icon',
          input: '.tenweb-search-form__input',
          toggle: '.tenweb-search-form__toggle',
          submit: '.tenweb-search-form__submit',
          closeButton: '.dialog-close-button'
        },
        classes: {
          isFocus: 'tenweb-search-form--focus',
          isFullScreen: 'tenweb-search-form--full-screen',
          lightbox: 'tenweb-lightbox'
        }
      };
    },
    getDefaultElements: function () {
      var selectors = this.getSettings('selectors'),
        elements = {};
      elements.$wrapper = this.$element.find(selectors.wrapper);
      elements.$container = this.$element.find(selectors.container);
      elements.$input = this.$element.find(selectors.input);
      elements.$icon = this.$element.find(selectors.icon);
      elements.$toggle = this.$element.find(selectors.toggle);
      elements.$submit = this.$element.find(selectors.submit);
      elements.$closeButton = this.$element.find(selectors.closeButton);
      return elements;
    },
    bindEvents: function () {
      var self = this,
        $container = self.elements.$container,
        $closeButton = self.elements.$closeButton,
        $input = self.elements.$input,
        $wrapper = self.elements.$wrapper,
        $icon = self.elements.$icon,
        skin = this.getElementSettings('skin'),
        classes = this.getSettings('classes');
      if ('full_screen' === skin) {

        // Activate full-screen mode on click
        self.elements.$toggle.on('click', function () {
          $container.toggleClass(classes.isFullScreen).toggleClass(classes.lightbox);
          $input.focus();
        });
        // Deactivate full-screen mode on click or on esc.
        $container.on('click', function (event) {
          if ($container.hasClass(classes.isFullScreen) && ($container[0] === event.target)) {
            $container.removeClass(classes.isFullScreen).removeClass(classes.lightbox);
          }
        });
        $closeButton.on('click', function () {
          $container.removeClass(classes.isFullScreen).removeClass(classes.lightbox);
        });
        elementorFrontend.getElements('$document').keyup(function (event) {
          var ESC_KEY = 27;
          if (ESC_KEY === event.keyCode) {
            if ($container.hasClass(classes.isFullScreen)) {
              $container.click();
            }
          }
        });
      }
      else {

        // Apply focus style on wrapper element when input is focused
        $input.on({
          focus: function () {
            $wrapper.addClass(classes.isFocus);
          },
          blur: function () {
            $wrapper.removeClass(classes.isFocus);
          }
        });
      }
      if ('minimal' === skin) {

        // Apply focus style on wrapper element when icon is clicked in minimal skin
        $icon.on('click', function () {
          $wrapper.addClass(classes.isFocus);
          $input.focus();
        });
      }
    }
  });
  elementorFrontend.hooks.addAction('frontend/element_ready/twbbsearch-form.default', function ($scope) {
    new tenwebSearchBerHandler({$element: $scope});
  });
});







(function( $ ) {

  var ShareLink = function( element, userSettings ) {
    var $element,
      settings = {};

    var getNetworkLink = function( networkName ) {
      var link = ShareLink.networkTemplates[ networkName ].replace( /{([^}]+)}/g, function( fullMatch, pureMatch ) {
        if ( networkName == 'twitter' && pureMatch == 'text' ) {
          var text = jQuery(jQuery.parseHTML(settings[pureMatch])).text().replace(/\s\s+/g, ' ');
          var href = window.location.href;
          settings[pureMatch] = text.substr( 0, 345 - href.length ) + ' ...';
        }

        return settings[ pureMatch ];
      });

      return encodeURI( link );
    };

    var getNetworkNameFromClass = function( className ) {
      var classNamePrefix = className.substr( 0, settings.classPrefixLength );

      return classNamePrefix === settings.classPrefix ? className.substr( settings.classPrefixLength ) : null;
    };

    var bindShareClick = function( networkName ) {
      $element.on( 'click', function() {
        openShareLink( networkName );
      } );
    };

    var openShareLink = function( networkName ) {
      var shareWindowParams = '';

      if ( settings.width && settings.height ) {
        var shareWindowLeft = screen.width / 2 - settings.width / 2,
          shareWindowTop = screen.height / 2 - settings.height / 2;

        shareWindowParams = 'toolbar=0,status=0,width=' + settings.width + ',height=' + settings.height + ',top=' + shareWindowTop + ',left=' + shareWindowLeft;
      }

      var link = getNetworkLink( networkName ),
        isPlainLink = /^https?:\/\//.test( link ),
        windowName = isPlainLink ? '' : '_self';

      open( link, windowName, shareWindowParams );
    };

    var run = function() {
      $.each( element.classList, function() {
        var networkName = getNetworkNameFromClass( this );

        if ( networkName ) {
          bindShareClick( networkName );

          return false;
        }
      } );
    };

    var initSettings = function() {
      $.extend( settings, ShareLink.defaultSettings, userSettings );

      [ 'title', 'text' ].forEach( function( propertyName ) {
        settings[ propertyName ] = settings[ propertyName ].replace( '#', '' );
      } );

      settings.classPrefixLength = settings.classPrefix.length;
    };

    var initElements = function() {
      $element = $( element );
    };

    var init = function() {
      initSettings();

      initElements();

      run();
    };

    init();
  };

  ShareLink.networkTemplates = {
    twitter: 'https://twitter.com/intent/tweet?url={url}&text={text}',
    pinterest: 'https://www.pinterest.com/pin/find/?url={url}',
    facebook: 'https://www.facebook.com/sharer.php?u={url}',
    vk: 'https://vkontakte.ru/share.php?url={url}&title={title}&description={text}&image={image}',
    linkedin: 'https://www.linkedin.com/shareArticle?mini=true&url={url}&title={title}&summary={text}&source={url}',
    odnoklassniki: 'http://odnoklassniki.ru/dk?st.cmd=addShare&st.s=1&st._surl={url}',
    tumblr: 'https://tumblr.com/share/link?url={url}',
    delicious: 'https://del.icio.us/save?url={url}&title={title}',
    digg: 'https://digg.com/submit?url={url}',
    reddit: 'https://reddit.com/submit?url={url}&title={title}',
    /*mix: 'https://www.mix.com/submit?url={url}',*/
    pocket: 'https://getpocket.com/edit?url={url}',
    whatsapp: 'whatsapp://send?text=*{title}*\n{text}\n{url}',
    xing: 'https://www.xing.com/app/user?op=share&url={url}',
    print: 'javascript:print()',
    email: 'mailto:?subject={title}&body={url}',
    telegram: 'https://telegram.me/share/url?url={url}&text={text}',
    skype: 'https://web.skype.com/share?url={url}'
  };

  ShareLink.defaultSettings = {
    title: '',
    text: '',
    image: '',
    url: location.href,
    classPrefix: 's_',
    width: 640,
    height: 480
  };

  $.each( { shareLink: ShareLink }, function( pluginName ) {
    var PluginConstructor = this;

    $.fn[ pluginName ] = function( settings ) {
      return this.each( function() {
        $( this ).data( pluginName, new PluginConstructor( this, settings ) );
      } );
    };
  } );
})( jQuery );

jQuery( window ).on( 'elementor/frontend/init', function() {
  var HandlerModule = elementorModules.frontend.handlers.Base,
    tenwebShareButtonsHandler;

  tenwebShareButtonsHandler = HandlerModule.extend( {
    onInit: function() {
      HandlerModule.prototype.onInit.apply( this, arguments );

      var elementSettings = this.getElementSettings(),
        classes = this.getSettings( 'classes' ),
        isCustomURL = elementSettings.share_url && elementSettings.share_url.url,
        shareLinkSettings = {
          classPrefix: classes.shareLinkPrefix
        };

      if ( isCustomURL ) {
        shareLinkSettings.url = elementSettings.share_url.url;
      } else {
        shareLinkSettings.url = location.href;
        shareLinkSettings.title = elementorFrontend.config.post.title;
        shareLinkSettings.text = elementorFrontend.config.post.excerpt;
      }

      this.elements.$shareButton.shareLink( shareLinkSettings );
    },
    getDefaultSettings: function() {
      return {
        selectors: {
          shareButton: '.elementor-share-btn'
        },
        classes: {
          shareLinkPrefix: 'elementor-share-btn_'
        }
      };
    },
    getDefaultElements: function() {
      var selectors = this.getSettings( 'selectors' );

      return {
        $shareButton: this.$element.find( selectors.shareButton )
      };
    }
  } );

  if ( ! elementorFrontend.isEditMode() ) {
    elementorFrontend.hooks.addAction('frontend/element_ready/twbbshare-buttons.default', function ($scope) {
      new tenwebShareButtonsHandler({$element: $scope});
    });
  }
});

jQuery( window ).on( 'elementor/frontend/init', function() {
  var TOCHandler = elementorModules.frontend.handlers.Base.extend({
      getDefaultSettings: function getDefaultSettings() {
      var elementSettings = this.getElementSettings(),
          listWrapperTag = 'numbers' === elementSettings.marker_view ? 'ol' : 'ul';
      return {
        selectors: {
          widgetContainer: '.elementor-widget-container',
          postContentContainer: '.elementor:not([data-elementor-type="header"]):not([data-elementor-type="footer"]):not([data-elementor-type="popup"])',
          expandButton: '.elementor-toc__toggle-button--expand',
          collapseButton: '.elementor-toc__toggle-button--collapse',
          body: '.elementor-toc__body',
          headerTitle: '.elementor-toc__header-title'
        },
        classes: {
          anchor: 'elementor-menu-anchor',
          listWrapper: 'elementor-toc__list-wrapper',
          listItem: 'elementor-toc__list-item',
          listTextWrapper: 'elementor-toc__list-item-text-wrapper',
          firstLevelListItem: 'elementor-toc__top-level',
          listItemText: 'elementor-toc__list-item-text',
          activeItem: 'elementor-item-active',
          headingAnchor: 'elementor-toc__heading-anchor',
          collapsed: 'elementor-toc--collapsed'
        },
        listWrapperTag: listWrapperTag
      };
    },
    getDefaultElements: function getDefaultElements() {
      var settings = this.getSettings();
      return {
        $pageContainer: this.getContainer(),
        $widgetContainer: this.$element.find(settings.selectors.widgetContainer),
        $expandButton: this.$element.find(settings.selectors.expandButton),
        $collapseButton: this.$element.find(settings.selectors.collapseButton),
        $tocBody: this.$element.find(settings.selectors.body),
        $listItems: this.$element.find('.' + settings.classes.listItem)
      };
    },
    getContainer: function getContainer() {
      var settings = this.getSettings(),
          elementSettings = this.getElementSettings(); // If there is a custom container defined by the user, use it as the headings-scan container

      if (elementSettings.container) {
        return jQuery(elementSettings.container);
      } // Get the document wrapper element in which the TOC is located


      var $documentWrapper = this.$element.parents('.elementor'); // If the TOC container is a popup, only scan the popup for headings

      if ('popup' === $documentWrapper.attr('data-elementor-type')) {
        return $documentWrapper;
      } // If the TOC container is anything other than a popup, scan only the post/page content for headings


      return jQuery(settings.selectors.postContentContainer);
    },
    bindEvents: function bindEvents() {
      var _this = this;

      var elementSettings = this.getElementSettings();

      if (elementSettings.minimize_box) {
        this.elements.$expandButton.on('click', function () {
          return _this.expandBox();
        });
        this.elements.$collapseButton.on('click', function () {
          return _this.collapseBox();
        });
      }

      if (elementSettings.collapse_subitems) {
        this.elements.$listItems.on('hover', function (event) {
          return jQuery(event.target).slideToggle();
        });
      }
    },
    getHeadings: function getHeadings() {
    // Get all headings from document by user-selected tags
    var elementSettings = this.getElementSettings(),
        tags = elementSettings.headings_by_tags.join(','),
        selectors = this.getSettings('selectors'),
        excludedSelectors = elementSettings.exclude_headings_by_selector;
    return this.elements.$pageContainer.find(tags).not(selectors.headerTitle).filter(function (index, heading) {
      return !jQuery(heading).closest(excludedSelectors).length; // Handle excluded selectors if there are any
    });
  },
    addAnchorsBeforeHeadings: function addAnchorsBeforeHeadings() {
    var _this2 = this;

    var classes = this.getSettings('classes'); // Add an anchor element right before each TOC heading to create anchors for TOC links

    this.elements.$headings.before(function (index) {
      // Check if the heading element itself has an ID, or if it is a widget which includes a main heading element, whether the widget wrapper has an ID
      if (jQuery(_this2.elements.$headings[index]).data('hasOwnID')) {
        return;
      }

      return "<span id=\"".concat(classes.headingAnchor, "-").concat(index, "\" class=\"").concat(classes.anchor, " \"></span>");
    });
  },
    activateItem: function activateItem($listItem) {
    var classes = this.getSettings('classes');
    this.deactivateActiveItem($listItem);
    $listItem.addClass(classes.activeItem);
    this.$activeItem = $listItem;

    if (!this.getElementSettings('collapse_subitems')) {
      return;
    }

    var $activeList;

    if ($listItem.hasClass(classes.firstLevelListItem)) {
      $activeList = $listItem.parent().next();
    } else {
      $activeList = $listItem.parents('.' + classes.listWrapper).eq(-2);
    }

    if (!$activeList.length) {
      delete this.$activeList;
      return;
    }

    this.$activeList = $activeList;
    this.$activeList.stop().slideDown();
  },
    deactivateActiveItem: function deactivateActiveItem($activeToBe) {
    if (!this.$activeItem || this.$activeItem.is($activeToBe)) {
      return;
    }

    var _this$getSettings = this.getSettings(),
        classes = _this$getSettings.classes;

    this.$activeItem.removeClass(classes.activeItem);

    if (this.$activeList && (!$activeToBe || !this.$activeList[0].contains($activeToBe[0]))) {
      this.$activeList.slideUp();
    }
  },
    followAnchor: function followAnchor($element, index) {
    var _this3 = this;

    var anchorSelector = $element[0].hash;
    var $anchor;

    try {
      // `decodeURIComponent` for UTF8 characters in the hash.
      $anchor = jQuery(decodeURIComponent(anchorSelector));
    } catch (e) {
      return;
    }

    elementorFrontend.waypoint($anchor, function (direction) {
      if (_this3.itemClicked) {
        return;
      }

      var id = $anchor.attr('id');

      if ('down' === direction) {
        _this3.viewportItems[id] = true;

        _this3.activateItem($element);
      } else {
        delete _this3.viewportItems[id];

        _this3.activateItem(_this3.$listItemTexts.eq(index - 1));
      }
    }, {
      offset: 'bottom-in-view',
      triggerOnce: false
    });
    elementorFrontend.waypoint($anchor, function (direction) {
      if (_this3.itemClicked) {
        return;
      }

      var id = $anchor.attr('id');

      if ('down' === direction) {
        delete _this3.viewportItems[id];

        if ((_this3.viewportItems).length) {
          _this3.activateItem(_this3.$listItemTexts.eq(index + 1));
        }
      } else {
        _this3.viewportItems[id] = true;

        _this3.activateItem($element);
      }
    }, {
      offset: 0,
      triggerOnce: false
    });
  },
    followAnchors: function followAnchors() {
    var _this4 = this;

    this.$listItemTexts.each(function (index, element) {
      return _this4.followAnchor(jQuery(element), index);
    });
  },
    populateTOC: function populateTOC() {
    this.listItemPointer = 0;
    var elementSettings = this.getElementSettings();

    if (elementSettings.hierarchical_view) {
      this.createNestedList();
    } else {
      this.createFlatList();
    }

    this.$listItemTexts = this.$element.find('.elementor-toc__list-item-text');
    this.$listItemTexts.on('click', this.onListItemClick.bind(this));

    if (!elementorFrontend.isEditMode()) {
      this.followAnchors();
    }
  },
    createNestedList: function createNestedList() {
    var _this5 = this;

    this.headingsData.forEach(function (heading, index) {
      heading.level = 0;

      for (var i = index - 1; i >= 0; i--) {
        var currentOrderedItem = _this5.headingsData[i];

        if (currentOrderedItem.tag <= heading.tag) {
          heading.level = currentOrderedItem.level;

          if (currentOrderedItem.tag < heading.tag) {
            heading.level++;
          }

          break;
        }
      }
    });
    this.elements.$tocBody.html(this.getNestedLevel(0));
  },
    createFlatList: function createFlatList() {
    this.elements.$tocBody.html(this.getNestedLevel());
  },
    getNestedLevel: function getNestedLevel(level) {
    var settings = this.getSettings(),
        elementSettings = this.getElementSettings(),
        icon = this.getElementSettings('icon'); // Open new list/nested list

    var html = "<".concat(settings.listWrapperTag, " class=\"").concat(settings.classes.listWrapper, "\">"); // for each list item, build its markup.

    while (this.listItemPointer < this.headingsData.length) {
      var currentItem = this.headingsData[this.listItemPointer];
      var listItemTextClasses = settings.classes.listItemText;

      if (0 === currentItem.level) {
        // If the current list item is a top level item, give it the first level class
        listItemTextClasses += ' ' + settings.classes.firstLevelListItem;
      }

      if (level > currentItem.level) {
        break;
      }

      if (level === currentItem.level) {
        html += "<li class=\"".concat(settings.classes.listItem, "\">");
        html += "<div class=\"".concat(settings.classes.listTextWrapper, "\">");
        var liContent = "<a href=\"#".concat(currentItem.anchorLink, "\" class=\"").concat(listItemTextClasses, "\">").concat(currentItem.text, "</a>"); // If list type is bullets, add the bullet icon as an <i> tag

        if ('bullets' === elementSettings.marker_view && icon) {
          liContent = "<i class=\"".concat(icon.value, "\"></i>").concat(liContent);
        }

        html += liContent;
        html += '</div>';
        this.listItemPointer++;
        var nextItem = this.headingsData[this.listItemPointer];

        if (nextItem && level < nextItem.level) {
          // If a new nested list has to be created under the current item,
          // this entire method is called recursively (outside the while loop, a list wrapper is created)
          html += this.getNestedLevel(nextItem.level);
        }

        html += '</li>';
      }
    }

    html += "</".concat(settings.listWrapperTag, ">");
    return html;
  },
    handleNoHeadingsFound: function handleNoHeadingsFound() {
      var noHeadingsText = 'No headings were found on this page.';

    // var noHeadingsText = elementorProFrontend.config.i18n['toc_no_headings_found'];
    //
    // if (elementorFrontend.isEditMode()) {
    //   noHeadingsText = elementorPro.translate('toc_no_headings_found');
    // }

    return this.elements.$tocBody.html(noHeadingsText);
  },
    collapseOnInit: function collapseOnInit() {
    var minimizedOn = this.getElementSettings('minimized_on'),
        currentDeviceMode = elementorFrontend.getCurrentDeviceMode();

    if ('tablet' === minimizedOn && 'desktop' !== currentDeviceMode || 'mobile' === minimizedOn && 'mobile' === currentDeviceMode) {
      this.collapseBox();
    }
  },
    getHeadingAnchorLink: function getHeadingAnchorLink(index, classes) {
    var headingID = this.elements.$headings[index].id,
        wrapperID = this.elements.$headings[index].closest('.elementor-widget').id;
    var anchorLink = '';

    if (headingID) {
      anchorLink = headingID;
    } else if (wrapperID) {
      // If the heading itself has an ID, we don't want to overwrite it
      anchorLink = wrapperID;
    } // If there is no existing ID, use the heading text to create a semantic ID


    if (headingID || wrapperID) {
      jQuery(this.elements.$headings[index]).data('hasOwnID', true);
    } else {
      anchorLink = "".concat(classes.headingAnchor, "-").concat(index);
    }

    return anchorLink;
  },
    setHeadingsData: function setHeadingsData() {
    var _this6 = this;

    this.headingsData = [];
    var classes = this.getSettings('classes'); // Create an array for simplifying TOC list creation

    this.elements.$headings.each(function (index, element) {
      var anchorLink = _this6.getHeadingAnchorLink(index, classes);

      _this6.headingsData.push({
        tag: +element.nodeName.slice(1),
        text: element.textContent,
        anchorLink: anchorLink
      });
    });
  },
    run: function run() {
    this.elements.$headings = this.getHeadings();

    if (!this.elements.$headings.length) {
      return this.handleNoHeadingsFound();
    }

    this.setHeadingsData();

    if (!elementorFrontend.isEditMode()) {
      this.addAnchorsBeforeHeadings();
    }

    this.populateTOC();

    if (this.getElementSettings('minimize_box')) {
      this.collapseOnInit();
    }
  },
    expandBox: function expandBox() {
    var boxHeight = this.getCurrentDeviceSetting('min_height');
    this.$element.removeClass(this.getSettings('classes.collapsed'));
    this.elements.$tocBody.slideDown(); // return container to the full height in case a min-height is defined by the user

    this.elements.$widgetContainer.css('min-height', boxHeight.size + boxHeight.unit);
  },
    collapseBox: function collapseBox() {
    this.$element.addClass(this.getSettings('classes.collapsed'));
    this.elements.$tocBody.slideUp(); // close container in case a min-height is defined by the user

    this.elements.$widgetContainer.css('min-height', '0px');
  },
    onInit: function onInit() {
    var _get2,
        _this7 = this;

    for (var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
      args[_key] = arguments[_key];
    }

      _get2 = elementorModules.frontend.handlers.Base.prototype.onInit.apply(_this7, [this].concat(args));

    this.viewportItems = [];
      return _this7.run();
  },
    onListItemClick: function onListItemClick(event) {
    var _this8 = this;

    this.itemClicked = true;
    setTimeout(function () {
      return _this8.itemClicked = false;
    }, 2000);
    var $clickedItem = jQuery(event.target),
        $list = $clickedItem.parent().next(),
        collapseNestedList = this.getElementSettings('collapse_subitems');
    var listIsActive;

    if (collapseNestedList && $clickedItem.hasClass(this.getSettings('classes.firstLevelListItem'))) {
      if ($list.is(':visible')) {
        listIsActive = true;
      }
    }

    this.activateItem($clickedItem);

    if (collapseNestedList && listIsActive) {
      $list.slideUp();
    }
  },
  });

  elementorFrontend.hooks.addAction( 'frontend/element_ready/twbb_table-of-contents.default', function ( $scope ) {
    new TOCHandler({ $element: $scope });
  });

});

jQuery( window ).on( 'elementor/frontend/init', function() {
    /* Elementor toggle widget content open/close animation */
    jQuery(document).on("click", ".elementor-tab-title", function (e) {
        let that = jQuery(this);
        that.closest(".elementor-toggle-item").addClass("twbb-tab-active");

        setTimeout(function() {
            that.closest(".elementor-toggle-item").removeClass("twbb-tab-active");
        },400)

    })
});

class TWBB_WooCommerce_Base extends elementorModules.frontend.handlers.Base {
  getDefaultSettings() {
    return {
      selectors: {
        stickyRightColumn: '.e-sticky-right-column'
      },
      classes: {
        stickyRightColumnActive: 'e-sticky-right-column--active'
      }
    };
  }
  getDefaultElements() {
    const selectors = this.getSettings('selectors');
    return {
      $stickyRightColumn: this.$element.find(selectors.stickyRightColumn)
    };
  }
  bindEvents() {
    // Add our wrapper class around the select2 whenever it is opened.
    elementorFrontend.elements.$document.on('select2:open', event => {
      this.addSelect2Wrapper(event);
    });
  }
  addSelect2Wrapper(event) {
    // The select element is recaptured every time because the markup can refresh
    const selectElement = jQuery(event.target).data('select2');
    if (selectElement.$dropdown) {
      selectElement.$dropdown.addClass('e-woo-select2-wrapper');
    }
  }
  isStickyRightColumnActive() {
    const classes = this.getSettings('classes');
    return this.elements.$stickyRightColumn.hasClass(classes.stickyRightColumnActive);
  }
  activateStickyRightColumn() {
    const elementSettings = this.getElementSettings(),
      $wpAdminBar = elementorFrontend.elements.$wpAdminBar,
      classes = this.getSettings('classes');
    let stickyOptionsOffset = elementSettings.sticky_right_column_offset || 0;
    if ($wpAdminBar.length && 'fixed' === $wpAdminBar.css('position')) {
      stickyOptionsOffset += $wpAdminBar.height();
    }
    if ('yes' === this.getElementSettings('sticky_right_column')) {
      this.elements.$stickyRightColumn.addClass(classes.stickyRightColumnActive);
      this.elements.$stickyRightColumn.css('top', stickyOptionsOffset + 'px');
    }
  }
  deactivateStickyRightColumn() {
    if (!this.isStickyRightColumnActive()) {
      return;
    }
    const classes = this.getSettings('classes');
    this.elements.$stickyRightColumn.removeClass(classes.stickyRightColumnActive);
  }

  /**
   * Activates the sticky column
   *
   * @return {void}
   */
  toggleStickyRightColumn() {
    if (!this.getElementSettings('sticky_right_column')) {
      this.deactivateStickyRightColumn();
      return;
    }
    if (!this.isStickyRightColumnActive()) {
      this.activateStickyRightColumn();
    }
  }
  equalizeElementHeight($element) {
    if ($element.length) {
      $element.removeAttr('style'); // First remove the custom height we added so that the new height can be re-calculated according to the content

      let maxHeight = 0;
      $element.each((index, element) => {
        maxHeight = Math.max(maxHeight, element.offsetHeight);
      });
      if (0 < maxHeight) {
        $element.css({
          height: maxHeight + 'px'
        });
      }
    }
  }

  /**
   * WooCommerce prints the Purchase Note separated from the product name by a border and padding.
   * In Elementor's Order Summary design, the product name and purchase note are displayed un-separated.
   * To achieve this design, it is necessary to access the Product Name line before the Purchase Note line to adjust
   * its padding. Since this cannot be achieved in CSS, it is done in this method.
   *
   * @param {Object} $element
   *
   * @return {void}
   */
  removePaddingBetweenPurchaseNote($element) {
    if ($element) {
      $element.each((index, element) => {
        jQuery(element).prev().children('td').addClass('product-purchase-note-is-below');
      });
    }
  }

  /**
   * `elementorPageId` and `elementorWidgetId` are added to the url in the `_wp_http_referer` input which is then
   * received when WooCommerce does its cart and checkout ajax requests e.g `update_order_review` and `update_cart`.
   * These query strings are extracted from the url and used in our `load_widget_before_wc_ajax` method.
   */
  updateWpReferers() {
    const selectors = this.getSettings('selectors'),
      wpHttpRefererInputs = this.$element.find(selectors.wpHttpRefererInputs),
      url = new URL(document.location);
    url.searchParams.set('elementorPageId', elementorFrontend.config.post.id);
    url.searchParams.set('elementorWidgetId', this.getID());
    wpHttpRefererInputs.attr('value', url);
  }
}
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
var posts_base = elementorModules.frontend.handlers.Base.extend({
  getSkinPrefix() {
    return 'classic_';
  },

  bindEvents() {
    var cid = this.getModelCID();
    elementorFrontend.addListenerOnce(cid, 'resize', this.onWindowResize);
  },

  getClosureMethodsNames() {
    return elementorModules.frontend.handlers.Base.prototype.getClosureMethodsNames.apply(this, arguments).concat(['fitImages', 'onWindowResize', 'runMasonry']);
  },

  getDefaultSettings() {
    return {
      classes: {
        fitHeight: 'elementor-fit-height',
        hasItemRatio: 'elementor-has-item-ratio'
      },
      selectors: {
        postsContainer: '.elementor-posts-container',
        post: '.elementor-post',
        postThumbnail: '.elementor-post__thumbnail',
        postThumbnailImage: '.elementor-post__thumbnail img'
      }
    };
  },

  getDefaultElements() {
    var selectors = this.getSettings('selectors');
    return {
      $postsContainer: this.$element.find(selectors.postsContainer),
      $posts: this.$element.find(selectors.post)
    };
  },

  fitImage($post) {
    var settings = this.getSettings(),
      $imageParent = $post.find(settings.selectors.postThumbnail),
      $image = $imageParent.find('img'),
      image = $image[0];

    if (!image) {
      return;
    }

    var imageParentRatio = $imageParent.outerHeight() / $imageParent.outerWidth(),
      imageRatio = image.naturalHeight / image.naturalWidth;
    $imageParent.toggleClass(settings.classes.fitHeight, imageRatio < imageParentRatio);
  },

  fitImages() {
    var $ = jQuery,
      self = this,
      itemRatio = getComputedStyle(this.$element[0], ':after').content,
      settings = this.getSettings();
    this.elements.$postsContainer.toggleClass(settings.classes.hasItemRatio, !!itemRatio.match(/\d/));

    if (self.isMasonryEnabled()) {
      return;
    }

    this.elements.$posts.each(function () {
      var $post = $(this),
        $image = $post.find(settings.selectors.postThumbnailImage);
      self.fitImage($post);
      $image.on('load', function () {
        self.fitImage($post);
      });
    });
  },

  setColsCountSettings() {
    var currentDeviceMode = elementorFrontend.getCurrentDeviceMode(),
      settings = this.getElementSettings(),
      skinPrefix = this.getSkinPrefix(),
      colsCount;

    switch (currentDeviceMode) {
      case 'mobile':
        colsCount = settings[skinPrefix + 'columns_mobile'];
        break;

      case 'tablet':
        colsCount = settings[skinPrefix + 'columns_tablet'];
        break;

      default:
        colsCount = settings[skinPrefix + 'columns'];
    }

    this.setSettings('colsCount', colsCount);
  },

  isMasonryEnabled() {
    return !!this.getElementSettings(this.getSkinPrefix() + 'masonry');
  },

  initMasonry() {
    imagesLoaded(this.elements.$posts, this.runMasonry);
  },

  runMasonry() {
    var elements = this.elements;
    elements.$posts.css({
      marginTop: '',
      transitionDuration: ''
    });
    this.setColsCountSettings();
    var colsCount = this.getSettings('colsCount'),
      hasMasonry = this.isMasonryEnabled() && colsCount >= 2;
    elements.$postsContainer.toggleClass('elementor-posts-masonry', hasMasonry);

    if (!hasMasonry) {
      elements.$postsContainer.height('');
      return;
    }
    /* The `verticalSpaceBetween` variable is setup in a way that supports older versions of the portfolio widget */


    var verticalSpaceBetween = this.getElementSettings(this.getSkinPrefix() + 'row_gap.size');

    if ('' === this.getSkinPrefix() && '' === verticalSpaceBetween) {
      verticalSpaceBetween = this.getElementSettings(this.getSkinPrefix() + 'item_gap.size');
    }

    var masonry = new elementorModules.utils.Masonry({
      container: elements.$postsContainer,
      items: elements.$posts.filter(':visible'),
      columnsCount: this.getSettings('colsCount'),
      verticalSpaceBetween
    });
    masonry.run();
  },

  run() {
    // For slow browsers
    setTimeout(this.fitImages, 0);
    this.initMasonry();
  },

  onInit() {
    elementorModules.frontend.handlers.Base.prototype.onInit.apply(this, arguments);
    this.bindEvents();
    this.run();
  },

  onWindowResize() {
    this.fitImages();
    this.runMasonry();
  },

  onElementChange() {
    this.fitImages();
    setTimeout(this.runMasonry);
  }

});

var portfolio = posts_base.extend({
  isActive(settings) {
    return settings.$element.find('.elementor-portfolio').length;
  },

  getSkinPrefix() {
    return '';
  },

  getDefaultSettings() {
    var settings = posts_base.prototype.getDefaultSettings.apply(this, arguments);

    settings.transitionDuration = 450;
    jQuery.extend(settings.classes, {
      active: 'elementor-active',
      item: 'elementor-portfolio-item',
      ghostItem: 'elementor-portfolio-ghost-item'
    });
    return settings;
  },

  getDefaultElements() {
    var elements = posts_base.prototype.getDefaultElements.apply(this, arguments);

    elements.$filterButtons = this.$element.find('.elementor-portfolio__filter');
    return elements;
  },

  getOffset(itemIndex, itemWidth, itemHeight) {
    var settings = this.getSettings(),
      itemGap = this.elements.$postsContainer.width() / settings.colsCount - itemWidth;
    itemGap += itemGap / (settings.colsCount - 1);
    return {
      start: (itemWidth + itemGap) * (itemIndex % settings.colsCount),
      top: (itemHeight + itemGap) * Math.floor(itemIndex / settings.colsCount)
    };
  },

  getClosureMethodsNames() {
    var baseClosureMethods = posts_base.prototype.getClosureMethodsNames.apply(this, arguments);

    return baseClosureMethods.concat(['onFilterButtonClick']);
  },

  filterItems(term) {
    var $posts = this.elements.$posts,
      activeClass = this.getSettings('classes.active'),
      termSelector = '.elementor-filter-' + term;

    if ('__all' === term) {
      $posts.addClass(activeClass);
      return;
    }

    $posts.not(termSelector).removeClass(activeClass);
    $posts.filter(termSelector).addClass(activeClass);
  },

  removeExtraGhostItems() {
    var settings = this.getSettings(),
      $shownItems = this.elements.$posts.filter(':visible'),
      emptyColumns = (settings.colsCount - $shownItems.length % settings.colsCount) % settings.colsCount,
      $ghostItems = this.elements.$postsContainer.find('.' + settings.classes.ghostItem);
    $ghostItems.slice(emptyColumns).remove();
  },

  handleEmptyColumns() {
    this.removeExtraGhostItems();
    var settings = this.getSettings(),
      $shownItems = this.elements.$posts.filter(':visible'),
      $ghostItems = this.elements.$postsContainer.find('.' + settings.classes.ghostItem),
      emptyColumns = (settings.colsCount - ($shownItems.length + $ghostItems.length) % settings.colsCount) % settings.colsCount;

    for (var i = 0; i < emptyColumns; i++) {
      this.elements.$postsContainer.append(jQuery('<div>', {
        class: settings.classes.item + ' ' + settings.classes.ghostItem
      }));
    }
  },

  showItems($activeHiddenItems) {
    $activeHiddenItems.show();
    setTimeout(function () {
      $activeHiddenItems.css({
        opacity: 1
      });
    });
  },

  hideItems($inactiveShownItems) {
    $inactiveShownItems.hide();
  },

  arrangeGrid() {
    var $ = jQuery,
      self = this,
      settings = self.getSettings(),
      $activeItems = self.elements.$posts.filter('.' + settings.classes.active),
      $inactiveItems = self.elements.$posts.not('.' + settings.classes.active),
      $shownItems = self.elements.$posts.filter(':visible'),
      $activeOrShownItems = $activeItems.add($shownItems),
      $activeShownItems = $activeItems.filter(':visible'),
      $activeHiddenItems = $activeItems.filter(':hidden'),
      $inactiveShownItems = $inactiveItems.filter(':visible'),
      itemWidth = $shownItems.outerWidth(),
      itemHeight = $shownItems.outerHeight();
    self.elements.$posts.css('transition-duration', settings.transitionDuration + 'ms');
    self.showItems($activeHiddenItems);

    if (self.isEdit) {
      self.fitImages();
    }

    self.handleEmptyColumns();

    if (self.isMasonryEnabled()) {
      self.hideItems($inactiveShownItems);
      self.showItems($activeHiddenItems);
      self.handleEmptyColumns();
      self.runMasonry();
      return;
    }

    $inactiveShownItems.css({
      opacity: 0,
      transform: 'scale3d(0.2, 0.2, 1)'
    });
    $activeShownItems.each(function () {
      var $item = $(this),
        currentOffset = self.getOffset($activeOrShownItems.index($item), itemWidth, itemHeight),
        requiredOffset = self.getOffset($shownItems.index($item), itemWidth, itemHeight);

      if (currentOffset.start === requiredOffset.start && currentOffset.top === requiredOffset.top) {
        return;
      }

      requiredOffset.start -= currentOffset.start;
      requiredOffset.top -= currentOffset.top;

      if (elementorFrontend.config.is_rtl) {
        requiredOffset.start *= -1;
      }

      $item.css({
        transitionDuration: '',
        transform: 'translate3d(' + requiredOffset.start + 'px, ' + requiredOffset.top + 'px, 0)'
      });
    });
    setTimeout(function () {
      $activeItems.each(function () {
        var $item = $(this),
          currentOffset = self.getOffset($activeOrShownItems.index($item), itemWidth, itemHeight),
          requiredOffset = self.getOffset($activeItems.index($item), itemWidth, itemHeight);
        $item.css({
          transitionDuration: settings.transitionDuration + 'ms'
        });
        requiredOffset.start -= currentOffset.start;
        requiredOffset.top -= currentOffset.top;

        if (elementorFrontend.config.is_rtl) {
          requiredOffset.start *= -1;
        }

        setTimeout(function () {
          $item.css('transform', 'translate3d(' + requiredOffset.start + 'px, ' + requiredOffset.top + 'px, 0)');
        });
      });
    });
    setTimeout(function () {
      self.hideItems($inactiveShownItems);
      $activeItems.css({
        transitionDuration: '',
        transform: 'translate3d(0px, 0px, 0px)'
      });
      self.handleEmptyColumns();
    }, settings.transitionDuration);
  },

  activeFilterButton(filter) {
    var activeClass = this.getSettings('classes.active'),
      $filterButtons = this.elements.$filterButtons,
      $button = $filterButtons.filter('[data-filter="' + filter + '"]');
    $filterButtons.removeClass(activeClass);
    $button.addClass(activeClass);
  },

  setFilter(filter) {
    this.activeFilterButton(filter);
    this.filterItems(filter);
    this.arrangeGrid();
  },

  refreshGrid() {
    this.setColsCountSettings();
    this.arrangeGrid();
  },

  bindEvents() {
    posts_base.prototype.bindEvents.apply(this, arguments);

    this.elements.$filterButtons.on('click', this.onFilterButtonClick);
  },

  isMasonryEnabled() {
    return !!this.getElementSettings('masonry');
  },

  run() {
    posts_base.prototype.run.apply(this, arguments);

    this.setColsCountSettings();
    this.setFilter('__all');
    this.handleEmptyColumns();
  },

  onFilterButtonClick(event) {
    this.setFilter(jQuery(event.currentTarget).data('filter'));
  },

  onWindowResize() {
    posts_base.prototype.onWindowResize.apply(this, arguments);

    this.refreshGrid();
  },

  onElementChange(propertyName) {
    posts_base.prototype.onElementChange.apply(this, arguments);

    if ('classic_item_ratio' === propertyName) {
      this.refreshGrid();
    }
  }

});

jQuery( window ).on( 'elementor/frontend/init', function() {
  elementorFrontend.hooks.addAction( 'frontend/element_ready/twbb_portfolio.default', function ( $scope ) {
    new portfolio({ $element: $scope });
  });
});

var _circularProgress = class CircularProgress {
    constructor(element, settings) {
        this.settings = settings;
        this.lastKnownProgress = null;
        this.circularProgressTracker = element.find('.elementor-scrolling-tracker-circular')[0];
        this.circularCurrentProgress = this.circularProgressTracker.getElementsByClassName('current-progress')[0];
        this.circularCurrentProgressPercentage = this.circularProgressTracker.getElementsByClassName('current-progress-percentage')[0];
        const radius = this.circularCurrentProgress.r.baseVal.value;
        const circumference = radius * 2 * Math.PI;
        this.circularCurrentProgress.style.strokeDasharray = `${circumference} ${circumference}`;
        this.circularCurrentProgress.style.strokeDashoffset = circumference;
        this.elements = this.cacheElements();
        this.resizeObserver = new ResizeObserver(() => {
            if (this.lastKnownProgress) {
                this.updateProgress(this.lastKnownProgress);
            }
        });
        this.resizeObserver.observe(this.circularProgressTracker);
    }

    cacheElements() {
        return {
            circularProgressTracker: this.circularProgressTracker,
            circularCurrentProgress: this.circularCurrentProgress,
            circularCurrentProgressPercentage: this.circularCurrentProgressPercentage
        };
    }

    updateProgress(progress) {
        // On page load, there is no progress and some of the elements might be not fully rendered - so we hide the progress.
        if (progress <= 0) {
            this.elements.circularCurrentProgress.style.display = 'none';
            this.elements.circularCurrentProgressPercentage.style.display = 'none';
            return;
        }

        this.elements.circularCurrentProgress.style.display = 'block';
        this.elements.circularCurrentProgressPercentage.style.display = 'block';
        const radius = this.elements.circularCurrentProgress.r.baseVal.value,
            circumference = radius * 2 * Math.PI,
            offset = circumference - progress / 100 * circumference;
        this.lastKnownProgress = progress;
        this.elements.circularCurrentProgress.style.strokeDasharray = `${circumference} ${circumference}`;
        this.elements.circularCurrentProgress.style.strokeDashoffset = 'ltr' === this.settings.direction ? -offset : offset;

        if ('yes' === this.settings.percentage) {
            this.elements.circularCurrentProgressPercentage.innerHTML = Math.round(progress) + '%';
        }
    }

    onDestroy() {
        this.resizeObserver.unobserve(this.circularProgressTracker);
    }

}

var _linearProgress = class LinearProgress {
    constructor(element, settings) {
        this.settings = settings;
        this.linearProgressTracker = element.find('.elementor-scrolling-tracker-horizontal')[0];
        this.linearCurrentProgress = this.linearProgressTracker.getElementsByClassName('current-progress')[0];
        this.linearCurrentProgressPercentage = this.linearProgressTracker.getElementsByClassName('current-progress-percentage')[0];
        this.elements = this.cacheElements();
    }

    cacheElements() {
        return {
            linearProgressTracker: this.linearProgressTracker,
            linearCurrentProgress: this.linearCurrentProgress,
            linearCurrentProgressPercentage: this.linearCurrentProgressPercentage
        };
    }

    updateProgress(progress) {
        // On page load, there is no progress and some of the elements might be not fully rendered - so we hide the progress.
        if (progress < 1) {
            this.elements.linearCurrentProgress.style.display = 'none';
            return;
        }

        this.elements.linearCurrentProgress.style.display = 'flex';
        this.elements.linearCurrentProgress.style.width = progress + '%';

        if ('yes' === this.settings.percentage && // Multiplying the progress percentage width by 1.5 to make sure it has enough space to be shown correctly.
            this.elements.linearCurrentProgress.getBoundingClientRect().width > this.elements.linearCurrentProgressPercentage.getBoundingClientRect().width * 1.5) {
            this.elements.linearCurrentProgressPercentage.innerHTML = Math.round(progress) + '%';
            this.elements.linearCurrentProgressPercentage.style.color = getComputedStyle(this.linearCurrentProgress).getPropertyValue('--percentage-color');
        } else {
            this.elements.linearCurrentProgressPercentage.style.color = 'transparent';
        }
    }

}

var ProgressTracker = class ProgressTracker extends elementorModules.frontend.handlers.Base {
    onInit() {
        elementorModules.frontend.handlers.Base.prototype.onInit.apply(this, arguments);
        this.circular = 'circular' === this.getElementSettings().type;
        const Handler = this.circular ? _circularProgress : _linearProgress;
        this.progressBar = new Handler(this.$element, this.getElementSettings());
        this.progressPercentage = 0;
        this.scrollHandler();
        this.handler = this.scrollHandler.bind(this);
        this.initListeners();
    }

    getTrackingElementSelector() {
        const trackingElementSetting = this.getElementSettings().relative_to;
        let selector;

        switch (trackingElementSetting) {
            case 'selector':
                selector = jQuery(this.getElementSettings().selector);
                break;

            case 'post_content':
                selector = jQuery('.elementor-widget-theme-post-content');
                break;

            default:
                selector = this.isScrollSnap() ? jQuery('#e-scroll-snap-container') : elementorFrontend.elements.$body;
                break;
        }

        return selector;
    } // TODO: On Elementor-Pro-3.6.0 delete this function and instead
    // use the function isScrollSnapActivated() from \elementor\assets\dev\js\frontend\utils\utils.js


    isScrollSnap() {
        const scrollSnapStatus = this.isEdit ? elementor.settings.page.model.attributes.scroll_snap : elementorFrontend.config.settings.page.scroll_snap;
        return 'yes' === scrollSnapStatus ? true : false;
    }

    addScrollSnapContainer() {
        if (this.isScrollSnap() && !jQuery('#e-scroll-snap-container').length) {
            jQuery('body').wrapInner('<div id="e-scroll-snap-container" />');
        }
    }

    scrollHandler() {
        // Temporary solution to integrate Scroll-Snap with Progress-Tracker.
        // Add Scroll-Snap container to all content in order to calculate the viewport percentage.
        this.addScrollSnapContainer();
        const $trackingElementSelector = this.getTrackingElementSelector(),
            scrollStartPercentage = $trackingElementSelector.is(elementorFrontend.elements.$body) || $trackingElementSelector.is(jQuery('#e-scroll-snap-container')) ? -100 : 0;
        this.progressPercentage = elementorModules.utils.Scroll.getElementViewportPercentage(this.getTrackingElementSelector(), {
            start: scrollStartPercentage,
            end: -100
        });
        this.progressBar.updateProgress(this.progressPercentage);
    }

    initListeners() {
        window.addEventListener('scroll', this.handler);
        elementorFrontend.elements.$body[0].addEventListener('scroll', this.handler);
    }

    onDestroy() {
        if (this.progressBar.onDestroy) {
            this.progressBar.onDestroy();
        }

        window.removeEventListener('scroll', this.handler);
        elementorFrontend.elements.$body[0].removeEventListener('scroll', this.handler);
    }

}

jQuery( window ).on( 'elementor/frontend/init', function() {
    elementorFrontend.hooks.addAction( 'frontend/element_ready/twbb_progress-tracker.default', function ( $scope ) {
        new ProgressTracker({ $element: $scope });
    });
})
_base = class CarouselBase extends elementorModules.frontend.handlers.SwiperBase {
    getDefaultSettings() {
        return {
            selectors: {
                swiperContainer: '.elementor-main-swiper',
                swiperSlide: '.swiper-slide'
            },
            slidesPerView: {
                widescreen: 3,
                desktop: 3,
                laptop: 3,
                tablet_extra: 3,
                tablet: 2,
                mobile_extra: 2,
                mobile: 1
            }
        };
    }

    getDefaultElements() {
        const selectors = this.getSettings('selectors'),
            elements = {
                $swiperContainer: this.$element.find(selectors.swiperContainer)
            };
        elements.$slides = elements.$swiperContainer.find(selectors.swiperSlide);
        return elements;
    }

    getEffect() {
        return this.getElementSettings('effect');
    }

    getDeviceSlidesPerView(device) {
        const slidesPerViewKey = 'slides_per_view' + ('desktop' === device ? '' : '_' + device);
        return Math.min(this.getSlidesCount(), +this.getElementSettings(slidesPerViewKey) || this.getSettings('slidesPerView')[device]);
    }

    getSlidesPerView(device) {
        if ('slide' === this.getEffect()) {
            return this.getDeviceSlidesPerView(device);
        }

        return 1;
    }

    getDeviceSlidesToScroll(device) {
        const slidesToScrollKey = 'slides_to_scroll' + ('desktop' === device ? '' : '_' + device);
        return Math.min(this.getSlidesCount(), +this.getElementSettings(slidesToScrollKey) || 1);
    }

    getSlidesToScroll(device) {
        if ('slide' === this.getEffect()) {
            return this.getDeviceSlidesToScroll(device);
        }

        return 1;
    }

    getSpaceBetween(device) {
        let propertyName = 'space_between';

        if (device && 'desktop' !== device) {
            propertyName += '_' + device;
        }

        return this.getElementSettings(propertyName).size || 0;
    }

    getSwiperOptions() {
        const elementSettings = this.getElementSettings();
        const swiperOptions = {
            grabCursor: true,
            initialSlide: this.getInitialSlide(),
            slidesPerView: this.getSlidesPerView('desktop'),
            slidesPerGroup: this.getSlidesToScroll('desktop'),
            spaceBetween: this.getSpaceBetween(),
            loop: 'yes' === elementSettings.loop,
            speed: elementSettings.speed,
            effect: this.getEffect(),
            preventClicksPropagation: false,
            slideToClickedSlide: true,
            handleElementorBreakpoints: true
        };

        if ('yes' === elementSettings.lazyload) {
            swiperOptions.lazy = {
                loadPrevNext: true,
                loadPrevNextAmount: 1
            };
        }

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
            const breakpointsSettings = {},
                breakpoints = elementorFrontend.config.responsive.activeBreakpoints;
            Object.keys(breakpoints).forEach(breakpointName => {
                breakpointsSettings[breakpoints[breakpointName].value] = {
                    slidesPerView: this.getSlidesPerView(breakpointName),
                    slidesPerGroup: this.getSlidesToScroll(breakpointName),
                    spaceBetween: this.getSpaceBetween(breakpointName)
                };
            });
            swiperOptions.breakpoints = breakpointsSettings;
        }

        if (!this.isEdit && elementSettings.autoplay) {
            swiperOptions.autoplay = {
                delay: elementSettings.autoplay_speed,
                disableOnInteraction: !!elementSettings.pause_on_interaction
            };
        }

        return swiperOptions;
    }

    getDeviceBreakpointValue(device) {
        if (!this.breakpointsDictionary) {
            const breakpoints = elementorFrontend.config.responsive.activeBreakpoints;
            this.breakpointsDictionary = {};
            Object.keys(breakpoints).forEach(breakpointName => {
                this.breakpointsDictionary[breakpointName] = breakpoints[breakpointName].value;
            });
        }

        return this.breakpointsDictionary[device];
    }

    updateSpaceBetween(propertyName) {
        const deviceMatch = propertyName.match('space_between_(.*)'),
            device = deviceMatch ? deviceMatch[1] : 'desktop',
            newSpaceBetween = this.getSpaceBetween(device);

        if ('desktop' !== device) {
            this.swiper.params.breakpoints[this.getDeviceBreakpointValue(device)].spaceBetween = newSpaceBetween;
        } else {
            this.swiper.params.spaceBetween = newSpaceBetween;
        }

        this.swiper.params.spaceBetween = newSpaceBetween;
        this.swiper.update();
    }

    async onInit() {
        elementorModules.frontend.handlers.Base.prototype.onInit.apply(this, arguments);
        const elementSettings = this.getElementSettings();

        if (1 >= this.getSlidesCount()) {
            return;
        }

        const Swiper = elementorFrontend.utils.swiper;
        this.swiper = await new Swiper(this.elements.$swiperContainer, this.getSwiperOptions());

        if ('yes' === elementSettings.pause_on_hover) {
            this.togglePauseOnHover(true);
        } // Expose the swiper instance in the frontend


        this.elements.$swiperContainer.data('swiper', this.swiper);
    }

    getChangeableProperties() {
        return {
            autoplay: 'autoplay',
            pause_on_hover: 'pauseOnHover',
            pause_on_interaction: 'disableOnInteraction',
            autoplay_speed: 'delay',
            speed: 'speed',
            width: 'width'
        };
    }

    updateSwiperOption(propertyName) {
        if (0 === propertyName.indexOf('width')) {
            this.swiper.update();
            return;
        }

        const elementSettings = this.getElementSettings(),
            newSettingValue = elementSettings[propertyName],
            changeableProperties = this.getChangeableProperties();
        let propertyToUpdate = changeableProperties[propertyName],
            valueToUpdate = newSettingValue; // Handle special cases where the value to update is not the value that the Swiper library accepts

        switch (propertyName) {
            case 'autoplay':
                if (newSettingValue) {
                    valueToUpdate = {
                        delay: elementSettings.autoplay_speed,
                        disableOnInteraction: 'yes' === elementSettings.pause_on_interaction
                    };
                } else {
                    valueToUpdate = false;
                }

                break;

            case 'autoplay_speed':
                propertyToUpdate = 'autoplay';
                valueToUpdate = {
                    delay: newSettingValue,
                    disableOnInteraction: 'yes' === elementSettings.pause_on_interaction
                };
                break;

            case 'pause_on_hover':
                this.togglePauseOnHover('yes' === newSettingValue);
                break;

            case 'pause_on_interaction':
                valueToUpdate = 'yes' === newSettingValue;
                break;
        } // 'pause_on_hover' is implemented by the handler with event listeners, not the Swiper library


        if ('pause_on_hover' !== propertyName) {
            this.swiper.params[propertyToUpdate] = valueToUpdate;
        }

        this.swiper.update();
    }

    onElementChange(propertyName) {
        if (1 >= this.getSlidesCount()) {
            return;
        }

        if (0 === propertyName.indexOf('width')) {
            this.swiper.update(); // If there is another thumbs slider, like in the Media Carousel widget.

            if (this.thumbsSwiper) {
                this.thumbsSwiper.update();
            }

            return;
        } // This is for handling the responsive control 'space_between'.
        // Responsive controls require a separate way of handling, and some currently don't work
        // (Swiper bug, currently exists in v5.3.6) TODO: update Swiper when bug is fixed and handle responsive controls


        if (0 === propertyName.indexOf('space_between')) {
            this.updateSpaceBetween(propertyName);
            return;
        }

        const changeableProperties = this.getChangeableProperties();

        if (changeableProperties.hasOwnProperty(propertyName)) {
            this.updateSwiperOption(propertyName);
        }
    }

    onEditSettingsChange(propertyName) {
        if (1 >= this.getSlidesCount()) {
            return;
        }

        if ('activeItemIndex' === propertyName) {
            this.swiper.slideToLoop(this.getEditSettings('activeItemIndex') - 1);
        }
    }

}
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

var _baseTabs = class baseTabs extends elementorModules.frontend.handlers.Base {
    getDefaultSettings() {
        return {
            selectors: {
                tablist: '[role="tablist"]',
                tabTitle: '.e-tab-title',
                tabContent: '.e-tab-content'
            },
            classes: {
                active: 'e-active'
            },
            showTabFn: 'show',
            hideTabFn: 'hide',
            toggleSelf: true,
            hidePrevious: true,
            autoExpand: true,
            keyDirection: {
                ArrowLeft: elementorFrontendConfig.is_rtl ? 1 : -1,
                ArrowUp: -1,
                ArrowRight: elementorFrontendConfig.is_rtl ? -1 : 1,
                ArrowDown: 1
            }
        };
    }

    getDefaultElements() {
        const selectors = this.getSettings('selectors');
        return {
            $tabTitles: this.findElement(selectors.tabTitle),
            $tabContents: this.findElement(selectors.tabContent)
        };
    }

    activateDefaultTab(videoId) {
        const settings = this.getSettings();

        if (!settings.autoExpand || 'editor' === settings.autoExpand && !this.isEdit) {
            return;
        }

        const defaultActiveTab = this.getEditSettings('activeItemIndex') || videoId || 1,
            originalToggleMethods = {
                showTabFn: settings.showTabFn,
                hideTabFn: settings.hideTabFn
            }; // Toggle tabs without animation to avoid jumping.

        this.setSettings({
            showTabFn: 'show',
            hideTabFn: 'hide'
        });
        this.changeActiveTab(defaultActiveTab); // Return back original toggle effects.

        this.setSettings(originalToggleMethods);
    }

    handleKeyboardNavigation(event) {
        const tab = event.currentTarget,
            $tabList = jQuery(tab.closest(this.getSettings('selectors').tablist)),
            $tabs = $tabList.find(this.getSettings('selectors').tabTitle),
            isVertical = 'vertical' === $tabList.attr('aria-orientation');

        switch (event.key) {
            case 'ArrowLeft':
            case 'ArrowRight':
                if (isVertical) {
                    return;
                }

                break;

            case 'ArrowUp':
            case 'ArrowDown':
                if (!isVertical) {
                    return;
                }

                event.preventDefault();
                break;

            case 'Home':
                event.preventDefault();
                $tabs.first().trigger('focus');
                return;

            case 'End':
                event.preventDefault();
                $tabs.last().trigger('focus');
                return;

            default:
                return;
        }

        const tabIndex = tab.getAttribute('data-tab') - 1,
            direction = this.getSettings('keyDirection')[event.key],
            nextTab = $tabs[tabIndex + direction];

        if (nextTab) {
            nextTab.focus();
        } else if (-1 === tabIndex + direction) {
            $tabs.last().trigger('focus');
        } else {
            $tabs.first().trigger('focus');
        }
    }

    deactivateActiveTab(tabIndex) {
        const settings = this.getSettings(),
            activeClass = settings.classes.active,
            activeFilter = tabIndex ? '[data-tab="' + tabIndex + '"]' : '.' + activeClass,
            $activeTitle = this.elements.$tabTitles.filter(activeFilter),
            $activeContent = this.elements.$tabContents.filter(activeFilter);
        $activeTitle.add($activeContent).removeClass(activeClass);
        $activeTitle.attr({
            tabindex: '-1',
            'aria-selected': 'false'
        });
        $activeContent[settings.hideTabFn]();
        $activeContent.attr('hidden', 'hidden');
    }

    activateTab(tabIndex) {
        const settings = this.getSettings(),
            activeClass = settings.classes.active,
            $requestedTitle = this.elements.$tabTitles.filter('[data-tab="' + tabIndex + '"]'),
            $requestedContent = this.elements.$tabContents.filter('[data-tab="' + tabIndex + '"]'),
            animationDuration = 'show' === settings.showTabFn ? 0 : 400;
        $requestedTitle.add($requestedContent).addClass(activeClass);
        $requestedTitle.attr({
            tabindex: '0',
            'aria-selected': 'true'
        });
        $requestedContent[settings.showTabFn](animationDuration, () => elementorFrontend.elements.$window.trigger('resize'));
        $requestedContent.removeAttr('hidden');
    }

    isActiveTab(tabIndex) {
        return this.elements.$tabTitles.filter('[data-tab="' + tabIndex + '"]').hasClass(this.getSettings('classes.active'));
    }

    bindEvents() {
        this.elements.$tabTitles.on({
            keydown: event => {
                // Support for old markup that includes an `<a>` tag in the tab.
                if (jQuery(event.target).is('a') && `Enter` === event.key) {
                    event.preventDefault();
                } // We listen to keydowon event for these keys in order to prevent undesired page scrolling.


                if (['End', 'Home', 'ArrowUp', 'ArrowDown'].includes(event.key)) {
                    this.handleKeyboardNavigation(event);
                }
            },
            keyup: event => {
                switch (event.key) {
                    case 'ArrowLeft':
                    case 'ArrowRight':
                        this.handleKeyboardNavigation(event);
                        break;

                    case 'Enter':
                    case 'Space':
                        event.preventDefault();
                        this.changeActiveTab(event.currentTarget.getAttribute('data-tab'));
                        break;
                }
            },
            click: event => {
                event.preventDefault();
                this.changeActiveTab(event.currentTarget.getAttribute('data-tab'));
            }
        });
    }

    onInit(...args) {
        super.onInit(...args); //this.activateDefaultTab();
    }

    changeActiveTab(tabIndex) {
        const isActiveTab = this.isActiveTab(tabIndex),
            settings = this.getSettings();

        if ((settings.toggleSelf || !isActiveTab) && settings.hidePrevious) {
            this.deactivateActiveTab();
        }

        if (!settings.hidePrevious && isActiveTab) {
            this.deactivateActiveTab(tabIndex);
        }

        if (!isActiveTab) {
            this.activateTab(tabIndex);
        }
    }

};
var _playerBase = class PlayerBase {
    constructor(playlistItem, videoIndex) {
        this.playlistItem = playlistItem;
        this.positionInVideoList = videoIndex;
    }

    formatDuration(duration) {
        const dateObj = new Date(duration * 1000),
            hours = dateObj.getUTCHours(),
            minutes = dateObj.getUTCMinutes(),
            seconds = dateObj.getSeconds();

        if (hours !== 0) {
            return `${hours.toString()}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        }

        return `${minutes.toString()}:${seconds.toString().padStart(2, '0')}`;
    }

};
var _playerYoutube = class playerYoutube extends _playerBase {
    constructor(playlistItem, videoIndex) {
        super(playlistItem, videoIndex);
        this.apiProvider = elementorFrontend.utils.youtube;
        this.playerObject = null;
        this.watchCount = 0;
        this.isVideoPlaying = false;
        this.isVideoPausedLocal = false;
        this.isVideoEnded = false;
        this.seekSequenceArray = [];
        this.pauseCurrentTime = null;
        this.isReady = false;
    }

    create() {
        this.currentVideoID = this.apiProvider.getVideoIDFromURL(this.playlistItem.videoUrl);
        const videoPromise = new Promise(resolve => {
            this.apiProvider.onApiReady(apiObject => {
                const playerOptions = {
                    width: '773',
                    videoId: this.currentVideoID,
                    playerVars: {
                        rel: 0,
                        showinfo: 0,
                        ecver: 2
                    },
                    events: {
                        onReady: () => {
                            // Indication that the video is loaded and can be played and paused.
                            this.isReady = true;
                            resolve();
                        }
                    }
                };
                this.playerObject = new apiObject.Player(this.playlistItem.tabContent.querySelector('div'), playerOptions);
                this.playerObject.addEventListener('onStateChange', event => {
                    // Buffering state.
                    if (3 === event.data) {
                        // When user is seeking we want to prevent triggering for "pause" and "play".
                        // Seeking means a sequence as [2,3], so we check that 2 (pause) is exist before adding "3" (buffering).
                        // If there is no "2", it means that this is not a seeking event and we can reset the array.
                        if (2 === this.seekSequenceArray[this.seekSequenceArray.length - 1]) {
                            this.seekSequenceArray.push(3);
                        } else {
                            this.seekSequenceArray = [];
                            clearTimeout(this.seekTimeOut);
                        }
                    }
                });
            });
        });
        return videoPromise;
    }

    handleEnded(callback) {
        this.playerObject.addEventListener('onStateChange', event => {
            // Ended state.
            if (0 === event.data) {
                this.watchCount++; // Prevent "video start" event when we seek to "0" on video ended event.
                // We seek to "0" to prevent the display of suggested videos by youtube when video ended.

                this.isVideoEnded = true;
                event.target.seekTo(0);
                event.target.stopVideo();
                this.isVideoPlaying = false;
                callback();
            }
        });
    }

    handlePaused(callback) {
        this.playerObject.addEventListener('onStateChange', event => {
            // Pause state.
            if (2 === event.data) {
                // The pause event can be the start of seek event ([2,3] sequence) so we reset the sequence array and adding 2.
                this.seekSequenceArray = [];
                this.seekSequenceArray.push(2); // Save the current time when pause event occur.

                this.pauseCurrentTime = this.playerObject.playerInfo.currentTime; // We use here a setTimeout, since we don't want to fire the pause event before we can be sure that its not part of seek event.

                this.seekTimeOut = setTimeout(() => {
                    if (2 === this.seekSequenceArray.length && 2 === this.seekSequenceArray[0] && 3 === this.seekSequenceArray[1]) {
                        this.seekSequenceArray = [];
                        clearTimeout(this.seekTimeOut);
                    } else {
                        callback(this.positionInVideoList); // Indication to know when there is a resume trigger event.

                        this.isVideoPausedLocal = true;
                    }
                }, 1000);
            }
        });
    }

    handlePlayed(callback) {
        this.playerObject.addEventListener('onStateChange', event => {
            // Prevent "video start" event when we seek to "0" on video ended event.
            if (1 === event.data && !this.isVideoEnded) {
                // Prevent "play" event when it is a seek event ([2,3] sequence).
                if (!(2 === this.seekSequenceArray.length && 2 === this.seekSequenceArray[0] && 3 === this.seekSequenceArray[1])) {
                    callback();
                }
            } else {
                this.isVideoEnded = false;
            }
        });
    }

    handleError(callback) {
        this.playerObject.addEventListener('onError', () => {
            callback();
        });
    }

    handleFullScreenChange(callback) {
        this.playerObject.addEventListener('fullscreenchange', () => {
            callback(document.fullscreenElement);
        });
    }

    getCurrentTime() {
        const currentTime = this.pauseCurrentTime ? this.pauseCurrentTime : this.playerObject.playerInfo.currentTime;
        this.pauseCurrentTime = null;
        return currentTime;
    }

    play() {
        if (!this.isReady) {
            return;
        }

        this.isVideoPlaying = true;
        this.playerObject.playVideo();
    }

    pause() {
        if (!this.isReady) {
            return;
        }

        this.isVideoPlaying = false;
        this.playerObject.pauseVideo();
    }

    mute() {
        this.playerObject.mute();
    }

    async setVideoProviderData() {
        if (!this.isReady) {
            return;
        }

        if (this.currentVideoID && 11 === this.currentVideoID.length) {
            this.playlistItem.thumbnail = {
                url: 'http://img.youtube.com/vi/' + this.playerObject.getVideoData().video_id + '/maxresdefault.jpg'
            };
            this.playlistItem.video_title = this.playerObject.getVideoData().title;
            this.playlistItem.duration = this.formatDuration(this.playerObject.getDuration());
        } else {
            this.playlistItem.thumbnail = {
                url: ''
            };
            this.playlistItem.video_title = '';
            this.playlistItem.duration = '';
        }
    }

};
var _playerVimeo = class playerVimeo extends _playerBase {
    constructor(playlistItem, videoIndex) {
        super(playlistItem, videoIndex);
        this.apiProvider = elementorFrontend.utils.vimeo;
        this.playerObject = null;
        this.watchCount = 0;
        this.isVideoInFullScreenChange = false;
        this.isReady = false;
    }

    create() {
        this.currentVideoID = this.apiProvider.getVideoIDFromURL(this.playlistItem.videoUrl);
        return new Promise(resolve => {
            this.apiProvider.onApiReady(apiObject => {
                const playerOptions = {
                    id: this.currentVideoID,
                    autoplay: false
                };
                this.playerObject = new apiObject.Player(this.playlistItem.tabContent.querySelector('div'), playerOptions); // Indication that the video is loaded and can be played and paused.

                this.playerObject.ready().then(() => {
                    this.isReady = true;
                    resolve();
                });
            });
        });
    }

    handleEnded(callback) {
        this.playerObject.on('ended', () => {
            this.watchCount++;
            callback(this.playlistItem);
        });
    }

    handlePaused(callback) {
        this.playerObject.on('pause', event => {
            // Prevent "pause" event trigger when page loads with vimeo video and when vimeo video ended, or when entering/exiting full-screen mode.
            if (0 === event.percent || event.percent >= 1 || this.isVideoInFullScreenChange) {
                return;
            }

            callback(this.positionInVideoList);
        });
    }

    handlePlayed(callback) {
        this.playerObject.on('play', () => {
            if (this.isVideoInFullScreenChange) {
                // Full screen change ended with all the extra events (pause and play).
                this.isVideoInFullScreenChange = false;
                return;
            }

            callback(this.playlistItem);
        });
    }

    handleFullScreenChange(callback) {
        this.playerObject.element.addEventListener('fullscreenchange', () => {
            callback(document.fullscreenElement);
            this.isVideoInFullScreenChange = true;
        });
    }

    getCurrentTime() {
        return this.playerObject.getCurrentTime().then(seconds => seconds);
    }

    play() {
        if (!this.isReady) {
            return;
        }

        this.playerObject.play();
    }

    pause() {
        if (!this.isReady) {
            return;
        }

        this.playerObject.pause();
    }

    mute() {
        this.playerObject.setMuted(true);
    }

    async setVideoProviderData() {
        if (!this.currentVideoID && 9 === !this.currentVideoID.length) {
            return;
        }

        const videoId = await this.playerObject.getVideoId();
        const response = await fetch('https://vimeo.com/api/v2/video/' + videoId + '.json');
        const videoData = await response.json();
        this.playlistItem.duration = this.formatDuration(videoData[0].duration);
        this.playlistItem.video_title = videoData[0].title;
        this.playlistItem.thumbnail = {
            url: videoData[0].thumbnail_medium
        };
        return this.playlistItem;
    }

};
var _playerHosted = class playerHosted extends _playerBase {
    constructor(playlistItem, videoIndex) {
        super(playlistItem, videoIndex);
        this.playerObject = null;
        this.watchCount = 0;
        this.isVideoPlaying = false;
        this.isVideoPausedLocal = false;
        this.isVideoSeeking = false;
        this.isVideoEnded = false;
        this.isReady = false;
    }

    create() {
        const videoPromise = new Promise(resolve => {
            const video = document.createElement('video');
            video.setAttribute('controls', '');
            const text = document.createTextNode('Sorry, your browser doesn\'t support embedded videos.');
            const source = document.createElement('source');
            source.setAttribute('src', this.playlistItem.videoUrl);
            source.setAttribute('type', 'video/' + this.playlistItem.videoUrl.split('.').pop());
            video.appendChild(source);
            video.appendChild(text);
            this.playerObject = video;
            this.playlistItem.tabContent.querySelector('div').replaceWith(this.playerObject);
            this.playerObject.addEventListener('canplay', () => {
                // Indication that the video is loaded and can be played and paused.
                this.isReady = true;
                resolve();
            }); // Seeked event indicates that the seeking has been finished, so we reset the boolean for that.

            this.playerObject.addEventListener('seeked', () => {
                this.isVideoSeeking = false;
            }); // Seeking event indicates that the seeking is currently happening, so we change the boolean.

            this.playerObject.addEventListener('seeking', () => {
                clearTimeout(this.seekTimeOut);
                this.isVideoSeeking = true;
            });
        });
        return videoPromise;
    }

    handleEnded(callback) {
        this.playerObject.addEventListener('ended', () => {
            this.watchCount++; // This property will prevent automatic pause trigger when video ended.

            this.isVideoEnded = true;
            this.isVideoPlaying = false;
            callback(this.playlistItem);
        });
    }

    handlePaused(callback) {
        this.playerObject.addEventListener('pause', () => {
            // Prevent pause trigger when the user is seeking video or when the video automatically trigger pause event when ended.
            this.seekTimeOut = setTimeout(() => {
                if (!this.isVideoSeeking && !this.isVideoEnded) {
                    callback(this.positionInVideoList); // Indication to know when there is a resume trigger event.

                    this.isVideoPausedLocal = true;
                } else {
                    this.isVideoEnded = false;
                }
            }, 30);
        });
    }

    handlePlayed(callback) {
        this.playerObject.addEventListener('play', () => {
            // Prevent play trigger when user is seeking video.
            if (!this.isVideoSeeking) {
                callback(this.playlistItem);
            }
        });
    }

    handleFullScreenChange(callback) {
        // Wrapping with jQuery to easily listen all 3 prefixed screen change.
        jQuery(this.playerObject).on('webkitfullscreenchange mozfullscreenchange fullscreenchange', () => {
            callback(document.fullscreenElement);
        });
    }

    getCurrentTime() {
        return this.playerObject.currentTime;
    }

    play() {
        if (!this.isReady) {
            return;
        }

        this.isVideoPlaying = true;
        this.playerObject.play();
    }

    pause() {
        if (!this.isReady) {
            return;
        }

        this.isVideoPlaying = false;
        this.playerObject.pause();
    }

    mute() {
        this.playerObject.muted = true;
    }

};
var _scrollUtils = { handleVideosPanelScroll(elements, event) {
    if (!event) {
        if (elements.$tabsItems[0].offsetHeight < elements.$tabsItems[0].scrollHeight) {
            elements.$tabsWrapper.addClass('bottom-shadow');
        }

        return;
    }

    if (event.target.scrollTop > 0) {
        elements.$tabsWrapper.addClass('top-shadow');
    } else {
        elements.$tabsWrapper.removeClass('top-shadow');
    }

    if (event.target.offsetHeight + event.target.scrollTop >= event.target.scrollHeight) {
        elements.$tabsWrapper.removeClass('bottom-shadow');
    } else {
        elements.$tabsWrapper.addClass('bottom-shadow');
    }
}};
var _playlistEvent =  class PlaylistEvent {
    constructor(_ref) {
        let {
            event,
            tab,
            playlist,
            video
        } = _ref;
        this.event = {
            type: event.type || '',
            time: event.time || 0,
            element: event.element,
            trigger: event.trigger || '',
            watchCount: event.watchCount || 0
        };
        this.tab = {
            name: tab.name,
            index: tab.index
        };
        this.playlist = {
            name: playlist.name,
            currentItem: playlist.currentItem,
            amount: playlist.amount
        };
        this.video = {
            provider: video.provider,
            url: video.url,
            title: video.title,
            duration: video.duration
        };
    }

};
var _eventTrigger = {
    getEventTabsObject(widgetObject) {
        const currentInnerTabsTitleElements = widgetObject.elements.$innerTabs.filter('.e-active').find('.e-inner-tabs-wrapper .e-inner-tab-title');

        if (currentInnerTabsTitleElements.length) {
            const activeInnerTabTitleElement = currentInnerTabsTitleElements.filter('.e-inner-tab-active');
            return {
                name: activeInnerTabTitleElement.text().trim(),
                index: activeInnerTabTitleElement.index() + 1
            };
        }

        return {
            name: 'none',
            index: 'none'
        };
    },

    getEventPlaylistObject(widgetObject, positionInVideoList) {
        const currentVideoIndex = positionInVideoList || widgetObject.currentPlaylistItemIndex;
        return {
            name: widgetObject.getElementSettings('playlist_title'),
            currentItem: currentVideoIndex,
            amount: widgetObject.playlistItemsArray.filter(video => video.videoType !== 'section').length
        };
    },

    getEventVideoObject(widgetObject, positionInVideoList) {
        const currentVideoIndex = positionInVideoList || widgetObject.currentPlaylistItemIndex,
            currentVideo = widgetObject.playlistItemsArray[currentVideoIndex - 1];
        return {
            provider: currentVideo.videoType,
            url: currentVideo.videoUrl,
            title: currentVideo.videoTitle,
            duration: currentVideo.videoDuration
        };
    },

    async getEventEventObject(widgetObject, eventType, eventTrigger, positionInVideoList) {
        const currentVideoIndex = positionInVideoList || widgetObject.currentPlaylistItemIndex,
            currentVideo = widgetObject.playlistItemsArray[currentVideoIndex - 1];
        return {
            type: eventType,
            time: await currentVideo.playerInstance.getCurrentTime(),
            element: widgetObject.$element,
            trigger: eventTrigger,
            watchCount: currentVideo.playerInstance.watchCount
        };
    },

    async triggerEvent(widgetObject, eventType, eventTrigger, positionInVideoList) {
        const currentEvent = new _playlistEvent({
            event: await _eventTrigger.getEventEventObject(widgetObject, eventType, eventTrigger, positionInVideoList),
            tab: _eventTrigger.getEventTabsObject(widgetObject),
            playlist: _eventTrigger.getEventPlaylistObject(widgetObject, positionInVideoList),
            video: _eventTrigger.getEventVideoObject(widgetObject, positionInVideoList)
        });
        jQuery('body').trigger('elementor-twbb_video-playList', currentEvent);
    }
};
var _innerTabs = {
    toggleInnerTabs(event, clickedTab, widgetObject) {
        const activeTabWrapper = event.currentTarget,
            tabTitles = activeTabWrapper.querySelectorAll('.e-inner-tab-title');

        if (clickedTab.hasClass('e-inner-tab-active') || tabTitles.length < 2) {
            return;
        }

        const tabsContents = activeTabWrapper.querySelectorAll('.e-inner-tab-content');
        tabTitles.forEach(tabTitle => {
            tabTitle.classList.toggle('e-inner-tab-active');
        });
        tabsContents.forEach(tabContent => {
            tabContent.toggleAttribute('hidden');
            tabContent.classList.toggle('e-inner-tab-active');
        });
        _innerTabs.handleInnerTabsButtonsDisplay(Array.from(tabsContents), widgetObject.isCollapsible, widgetObject.innerTabsHeightLimit); // Trigger event when tab open.

        (0, _eventTrigger.triggerEvent)(widgetObject, 'tabOpened', 'click');
    },

    handleInnerTabs(event, widgetObject) {
        const clickedTarget = event.target;
        const clickedTagType = clickedTarget.tagName; // Handle click on tab on desktop mode.

        if (clickedTarget.classList.contains('e-inner-tab-title-text')) {
            event.preventDefault();
            const $clickedTab = jQuery(clickedTarget).parent('.e-inner-tab-title');
            _innerTabs.toggleInnerTabs(event, $clickedTab, widgetObject);
        } // Handle click on tab on mobile mode.


        if (clickedTarget.classList.contains('e-tab-mobile-title')) {
            const $clickedTab = jQuery(clickedTarget);
            _innerTabs.toggleInnerTabs(event, $clickedTab, widgetObject);
        } // Handle click on show-less buttons in tab content.


        if ('button' === clickedTagType.toLowerCase()) {
            _innerTabs.onTabContentButtonsClick(event, widgetObject);
        }
    },

   handleInnerTabsButtonsDisplay(tabsContents, isCollapsible, innerTabsHeightLimit) {
        if (!isCollapsible) {
            return;
        }

        const activeInnerTab = tabsContents.filter(tabsContent => tabsContent.classList.contains('e-inner-tab-active')),
            innerTabScrollableHeight = activeInnerTab[0].querySelector('.e-inner-tab-text > div').offsetHeight,
            innerTabsLimitHeight = parseInt(innerTabsHeightLimit.size);

        if (innerTabsLimitHeight && innerTabScrollableHeight > innerTabsLimitHeight) {
            activeInnerTab[0].classList.add('show-inner-tab-buttons');
        }
    },

    onTabContentButtonsClick(event, widgetObject) {
        const $tabsContent = jQuery(event.currentTarget).find('.e-inner-tab-content'),
            $activeTabContent = $tabsContent.filter('.e-inner-tab-active'),
            buttonsElements = $activeTabContent.find('button');
        buttonsElements.toggleClass('show-button');
        $activeTabContent.toggleClass('show-full-height');
        const eventType = $activeTabContent.hasClass('show-full-height') ? 'tabExpanded' : 'tabCollapsed'; // Trigger event when collapsed/expanded clicked.

        (0, _eventTrigger.triggerEvent)(widgetObject, eventType, 'click');
    }
};
var _urlParams = {
    handleURLParams(playlistId, playlistItemsArray) {
        const params = new URLSearchParams(location.search),
            videoId = params.get('video'),
            playlistName = params.get('playlist'),
            defaultTabIndex = 1; // When there is no data in params, the first video in the list should be active by returning false.

        if (!playlistName) {
            return false;
        } // When there is data in params, we return the tab number for the video.


        if (playlistName === playlistId) {
            const videoItem = playlistItemsArray.find(playlistItem => videoId === playlistItem.dataItemId),
                tabIndex = videoItem ? videoItem.dataTab : defaultTabIndex;

            if (!tabIndex) {
                setVideoParams(playlistId, playlistItemsArray, defaultTabIndex);
            }

            return tabIndex || false;
        }
    }, // Setting the playlist id and video id on the url.


    setVideoParams(playlistId, playlistItemsArray, videoId) {
        const params = new URLSearchParams(location.search);
        params.set('playlist', playlistId);
        params.set('video', playlistItemsArray[videoId - 1].dataItemId);
        history.replaceState({}, '', location.pathname + '?' + params);
    }
};
var VideoPlaylistHandler = class VideoPlaylistHandler extends _baseTabs {
    getDefaultSettings() {
        const defaultSettings = super.getDefaultSettings(),
            selectors = {
                tabsWrapper: '.e-tabs-items-wrapper',
                tabsItems: '.e-tabs-items',
                toggleVideosDisplayButton: '.e-tabs-toggle-videos-display-button',
                videos: '.e-tabs-content-wrapper .e-tab-content',
                innerTabs: '.e-tabs-inner-tabs .e-tab-content',
                imageOverlay: '.elementor-custom-embed-image-overlay'
            };
        return { ...defaultSettings,
            selectors: { ...defaultSettings.selectors,
                ...selectors
            }
        };
    }

    getDefaultElements() {
        const elements = super.getDefaultElements(),
            selectors = this.getSettings('selectors');
        return { ...elements,
            $tabsWrapper: this.findElement(selectors.tabsWrapper),
            $tabsItems: this.findElement(selectors.tabsItems),
            $toggleVideosDisplayButton: this.findElement(selectors.toggleVideosDisplayButton),
            $videos: this.findElement(selectors.videos),
            $innerTabs: this.findElement(selectors.innerTabs),
            $imageOverlay: this.findElement(selectors.imageOverlay)
        };
    }

    initEditorListeners() {
        super.initEditorListeners();
        this.editorListeners.push({
            event: 'elementorPlaylistWidget:fetchVideoData',
            to: elementor.channels.editor,
            callback: e => {
                this.getCurrentPlayerSelected().setVideoProviderData().then(() => {
                    e.currentItem = this.getCurrentItemSelected();
                    elementor.channels.editor.trigger('elementorPlaylistWidget:setVideoData', e);
                });
            }
        });
    }

    bindEvents() {
        super.bindEvents();

        this.elements.$imageOverlay.on({
            click: e => {
                // Remove image overlay if the user clicked it and play the video in case it is not playing.
                e.currentTarget.remove();
                this.getCurrentPlayerSelected().play();
            }
        }); // Handle the inner tab functionality.

        this.elements.$innerTabs.on({
            click: event => {
                (0, _innerTabs.handleInnerTabs)(event, this);
            }
        }); // Handle scroll on the right panel to make the "shadows" effect when the panel is scrollable.

        this.elements.$tabsItems.on({
            scroll: event => {
                (0, _scrollUtils.handleVideosPanelScroll)(this.elements, event);
            }
        }); // Handle the closing/opening right panel in mobile mode.

        this.elements.$toggleVideosDisplayButton.on({
            click: event => {
                jQuery(event.target).toggleClass('rotate-up');
                jQuery(event.target).toggleClass('rotate-down');
                this.elements.$tabsWrapper.slideToggle('slow');
            }
        });
    }

    onInit(...args) {
        super.onInit(...args);
        this.playlistId = this.getID(); // Handle watched videos.

        this.storageKey = 'watched_videos_' + this.getID();
        const storageObject = elementorFrontend.storage.get(this.storageKey);

        if (storageObject) {
            this.watchedVideosArray = JSON.parse(storageObject);
        } else {
            this.watchedVideosArray = [];
        }

        this.watchedIndication = this.getElementSettings('show_watched_indication'); // Handle indication for scrolling in the right panel.

        (0, _scrollUtils.handleVideosPanelScroll)(this.elements); // Handle the video player functionality, includes "on load" and "next up".

        this.isAutoplayOnLoad = 'yes' === this.getElementSettings('autoplay_on_load');
        this.isAutoplayNextUp = 'yes' === this.getElementSettings('autoplay_next');
        this.isFirstVideoActivated = true;
        this.createPlaylistItems(); // Handle display for show more/less button.

        this.isCollapsible = this.getElementSettings('inner_tab_is_content_collapsible');
        this.innerTabsHeightLimit = this.getElementSettings('inner_tab_collapsible_height'); // Keep track of the element that supposed to be paused since the user selected other video.

        this.currentPlayingPlaylistItemIndex = 1; // Handle the first initial activation of the video in the playlist.

        this.activateInitialVideo(); // Handle Inner Tab activation in edit mode.

        this.activateInnerTabInEditMode();
    }

    onEditSettingsChange(propertyName) {
        // The condition will be true when the user clicks the widget to open the edit panel.
        if ('panel' === propertyName) {
            // The boolean below will prevent running twice the activateDefaultTab function when widget first load and user click the item to play it.
            this.preventTabActivation = true;
        }

        if ('activeItemIndex' !== propertyName) {
            return;
        }

        if (this.preventTabActivation) {
            this.preventTabActivation = false;
            return;
        }

        this.activateDefaultTab();
    }

    activateInitialVideo() {
        this.isPageOnLoad = true;
        const isLazyLoad = !!this.getElementSettings('lazy_load'),
            initialTabIndex = (0, _urlParams.handleURLParams)(this.playlistId, this.playlistItemsArray);
        let isUrlParamsExist = false;

        if (initialTabIndex) {
            this.currentPlaylistItemIndex = initialTabIndex;
            this.currentPlayingPlaylistItemIndex = initialTabIndex;
            isUrlParamsExist = true;
        } else {
            this.currentPlaylistItemIndex = 1;
            this.currentPlayingPlaylistItemIndex = 1;
        } // When there are no url parameters and on-load is on, the video should be played, means the url parameters should be set.


        if (this.isAutoplayOnLoad && !isUrlParamsExist) {
            (0, _urlParams.setVideoParams)(this.playlistId, this.playlistItemsArray, this.currentPlaylistItemIndex);
        }

        this.handleFirstVideoActivation(isLazyLoad);
    }
    /*
        The scenarios for playing the first video after page load:
        - lazy load off - video will load on page load before user scroll video to view.
        - lazy load on - video will load when user scroll the video to view.
       */


    handleFirstVideoActivation(isLazyLoad) {
        if (!isLazyLoad) {
            this.activateDefaultTab(this.currentPlaylistItemIndex); // No need to use the observer since "lazy load is" off.

            return;
        }

        const playlistElement = document.querySelector('.elementor-element-' + this.playlistId + ' .e-tabs-main-area'),
            observer = elementorModules.utils.Scroll.scrollObserver({
                callback: event => {
                    if (event.isInViewport) {
                        this.activateDefaultTab(this.currentPlaylistItemIndex);
                        observer.unobserve(playlistElement);
                    }
                }
            });
        observer.observe(playlistElement);
    }

    getCurrentItemSelected() {
        return this.playlistItemsArray[this.currentPlaylistItemIndex - 1];
    }

    getCurrentPlayerSelected() {
        return this.getCurrentItemSelected().playerInstance;
    }

    getCurrentPlayerPlaying() {
        return this.playlistItemsArray[this.currentPlayingPlaylistItemIndex - 1].playerInstance;
    } // Handle video selection.


    isVideoShouldBePlayed() {
        // When user select other video, the current video will be paused if is playing.
        if (this.currentPlayingPlaylistItemIndex !== this.currentPlaylistItemIndex) {
            if (this.getCurrentPlayerPlaying()) {
                this.getCurrentPlayerPlaying().pause();
            }

            this.currentPlayingPlaylistItemIndex = this.currentPlaylistItemIndex; // When user select the same video, the current video will be paused if is playing.
        } else if (this.getCurrentPlayerPlaying().isVideoPlaying) {
            this.getCurrentPlayerPlaying().pause();
            return false;
        } // When none of the videos are playing, the selected video should be played.


        return true;
    }

    activateInnerTabInEditMode() {
        if (this.isEdit && this.getEditSettings('innerActiveIndex')) {
            const innerTabActivated = this.getEditSettings('innerActiveIndex'),
                innerTabs = jQuery(this.elements.$innerTabs.eq(this.currentPlaylistItemIndex - 1).find('.e-inner-tab-title a'));
            innerTabs[innerTabActivated].click();
        }
    } // Handle video creation including event listeners and playing video if needed.


    async handleVideo(playListItem) {
        // If the video already created (visited once), then just play it if it's not playing already, otherwise pause it.
        if (playListItem.playerInstance) {
            if (this.isVideoShouldBePlayed()) {
                // Remove image overlay if first video item is playing without clicking the image overlay.
                if (1 === this.currentPlaylistItemIndex && this.elements.$imageOverlay) {
                    this.elements.$imageOverlay.remove();
                }

                this.playVideoAfterCreation(playListItem);
            }
        } else {
            // If the video is not created yet (first visit), then create the video instance and the event listeners.
            const players = {
                youtube: _playerYoutube,
                vimeo: _playerVimeo,
                hosted: _playerHosted
            }; // Initiating player object.
            // The second parameter holds the video item when event trigger occur with setTimeout.

            playListItem.playerInstance = new players[playListItem.videoType](playListItem, this.currentPlaylistItemIndex);
            playListItem.playerInstance.create().then(() => {
                if (this.isVideoShouldBePlayed()) {
                    this.playVideoOnCreation(playListItem);
                } // Handle the functionality when video full screen mode changes.


                playListItem.playerInstance.handleFullScreenChange(isEnterFullScreenMode => {
                    // Trigger event when enter/exit full screen mode.
                    (0, _eventTrigger.triggerEvent)(this, isEnterFullScreenMode ? 'videoFullScreen' : 'videoExitFullScreen', 'click');
                }); // Handle the functionality when video play.

                playListItem.playerInstance.handlePlayed(() => {
                    const currentPlaylistItem = this.getCurrentItemSelected();
                    let videoTrigger = 'click';

                    if (currentPlaylistItem.isAutoplayOnLoad) {
                        videoTrigger = 'onLoad';
                        playListItem.isAutoplayOnLoad = false;
                    } else if (currentPlaylistItem.isAutoPlayNextUp) {
                        videoTrigger = 'nextVideo';
                    } // Trigger event when video started.


                    (0, _eventTrigger.triggerEvent)(this, currentPlaylistItem.playerInstance.isVideoPausedLocal ? 'videoResume' : 'videoStart', videoTrigger);
                }); // Handle the functionality when video ended.

                playListItem.playerInstance.handleEnded(() => {
                    // Trigger event when video ended.
                    (0, _eventTrigger.triggerEvent)(this, 'videoEnded', 'click'); // Handle the indication for videos that have been watched and ended.

                    if (this.watchedIndication) {
                        this.elements.$tabTitles.filter('.e-active').addClass('watched-video');
                    }

                    const endedVideoId = this.getCurrentItemSelected().dataItemId;

                    if (!this.watchedVideosArray.includes(endedVideoId) && this.watchedIndication) {
                        this.watchedVideosArray.push(this.getCurrentItemSelected().dataItemId);
                        elementorFrontend.storage.set(this.storageKey, JSON.stringify(this.watchedVideosArray));
                    } // Handle "next up" functionality.


                    if (this.isAutoplayNextUp) {
                        // If there are more videos in the list, play next video.
                        if (this.playlistItemsArray.length >= ++this.currentPlaylistItemIndex) {
                            // Handle the logic for playing next video.
                            while ('section' === this.getCurrentItemSelected().videoType) {
                                this.currentPlaylistItemIndex++; // When last video in the playlist ended, we reset the this.currentPlaylistItemIndex to the last playlist item index.

                                if (this.playlistItemsArray.length < this.currentPlaylistItemIndex) {
                                    this.currentPlaylistItemIndex = this.playlistItemsArray.length;
                                    return;
                                }
                            }

                            this.changeActiveTab(this.currentPlaylistItemIndex, true);
                        }
                    }
                }); // Handle the functionality when video paused.
                // The handlePaused will trigger event with setTimeout, positionInVideoList will keep track for the paused video when selecting other video.

                playListItem.playerInstance.handlePaused(positionInVideoList => {
                    // Trigger event when video paused.
                    (0, _eventTrigger.triggerEvent)(this, 'videoPaused', 'click', positionInVideoList);
                });
            });
        }
    } // Handle the actual playing of the video that already exists (already created before).


    playVideoAfterCreation(playListItem) {
        playListItem.playerInstance.play();
    } // Handle the actual playing of the video when the video is created.


    playVideoOnCreation(playListItem) {
        // Play the video according to "on load" and "next up" indications.
        if (this.isAutoplayOnLoad) {
            playListItem.isAutoplayOnLoad = true; // Mute the initiated video when "autoplay onload" and then play.

            playListItem.playerInstance.mute();
            playListItem.playerInstance.play();
            this.isAutoplayOnLoad = false;
        } else if (!this.isFirstVideoActivated) {
            playListItem.isAutoPlayNextUp = true;
            playListItem.playerInstance.play();
        }

        this.isFirstVideoActivated = false;
    }

    createPlaylistItems() {
        this.playlistItemsArray = [];
        this.elements.$videos.each((index, tabContent) => {
            const playListItem = {};
            const $tabContent = jQuery(tabContent);
            playListItem.videoUrl = $tabContent.attr('data-video-url');
            playListItem.videoType = $tabContent.attr('data-video-type');
            playListItem.videoTitle = $tabContent.attr('data-video-title');
            playListItem.videoDuration = $tabContent.attr('data-video-duration');
            playListItem.tabContent = tabContent;
            playListItem.dataTab = index + 1;
            playListItem.dataItemId = this.getElementSettings().tabs[index]._id;
            this.playlistItemsArray.push(playListItem);
        }); // When the page loads,the code checks which videos already watched and adding a class accordingly.

        if (this.watchedVideosArray.length > 0 && this.watchedIndication) {
            this.watchedVideosArray.forEach(watchedVideoId => {
                const watchedPlaylistItem = this.playlistItemsArray.find(playlistItem => playlistItem.dataItemId === watchedVideoId);
                this.elements.$tabTitles.filter('[data-tab="' + watchedPlaylistItem.dataTab + '"]').addClass('watched-video');
            });
        }
    }

    changeActiveTab(tabIndex, isVideoSelectedAutomatically) {
        super.changeActiveTab(tabIndex);

        if (this.playlistItemsArray[tabIndex - 1] && this.playlistItemsArray[tabIndex - 1].videoType !== 'section') {
            this.currentPlaylistItemIndex = parseInt(tabIndex);

            if (isVideoSelectedAutomatically) {
                this.currentPlayingPlaylistItemIndex = this.currentPlaylistItemIndex;
            } // Handle on creation of the video and working with it.


            this.handleVideo(this.getCurrentItemSelected(), isVideoSelectedAutomatically); // Set Video params in url only if its not the first video when page load.

            if (!this.isPageOnLoad) {
                (0, _urlParams.setVideoParams)(this.playlistId, this.playlistItemsArray, this.currentPlaylistItemIndex);
            }

            this.isPageOnLoad = false; // Handle the display for the inner tabs buttons as long there are actually inner tabs.

            if (jQuery(this.elements.$innerTabs.eq(tabIndex - 1)).find('.e-inner-tab-content').length > 0) {
                const innerTabsContent = this.elements.$innerTabs.filter('.e-active').find('.e-inner-tab-content');
                (0, _innerTabs.handleInnerTabsButtonsDisplay)(innerTabsContent.toArray(), this.isCollapsible, this.innerTabsHeightLimit);
            }
        }
    }

}

jQuery( window ).on( 'elementor/frontend/init', function() {
    elementorFrontend.hooks.addAction( 'frontend/element_ready/twbb_video-playlist.default', function ( $scope ) {
        new VideoPlaylistHandler({ $element: $scope });
    });
});

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
jQuery( window ).on( 'elementor/frontend/init', function() {
  class Cart extends TWBB_WooCommerce_Base {
    getDefaultSettings() {
      const defaultSettings = super.getDefaultSettings(...arguments);
      return {
        selectors: {
          ...defaultSettings.selectors,
          shippingForm: '.shipping-calculator-form',
          quantityInput: '.qty',
          updateCartButton: 'button[name=update_cart]',
          wpHttpRefererInputs: '[name=_wp_http_referer]',
          hiddenInput: 'input[type=hidden]',
          productRemove: '.product-remove a'
        },
        classes: defaultSettings.classes,
        ajaxUrl: elementorTenwebFrontend.config.ajaxurl
      };
    }
    getDefaultElements() {
      const selectors = this.getSettings('selectors');
      return {
        ...super.getDefaultElements(...arguments),
        $shippingForm: this.$element.find(selectors.shippingForm),
        $stickyColumn: this.$element.find(selectors.stickyColumn),
        $hiddenInput: this.$element.find(selectors.hiddenInput)
      };
    }
    bindEvents() {
      super.bindEvents();
      const selectors = this.getSettings('selectors');
      elementorFrontend.elements.$body.on('wc_fragments_refreshed', () => this.applyButtonsHoverAnimation());
      if ('yes' === this.getElementSettings('update_cart_automatically')) {
        this.$element.on('input', selectors.quantityInput, () => this.updateCart());
      }
      elementorFrontend.elements.$body.on('wc_fragments_loaded wc_fragments_refreshed', () => {
        this.updateWpReferers();
        if (elementorFrontend.isEditMode() || elementorFrontend.isWPPreviewMode()) {
          this.disableActions();
        }
      });
      elementorFrontend.elements.$body.on('added_to_cart', function (e, data) {
        // We do not want the page to reload in the Editor after we triggered the 'added_to_cart' event.
        if (data.e_manually_triggered) {
          return false;
        }
      });
    }
    onInit() {
      super.onInit(...arguments);
      this.toggleStickyRightColumn();
      this.hideHiddenInputsParentElements();
      if (elementorFrontend.isEditMode()) {
        this.elements.$shippingForm.show();
      }
      this.applyButtonsHoverAnimation();
      this.updateWpReferers();
      if (elementorFrontend.isEditMode() || elementorFrontend.isWPPreviewMode()) {
        this.disableActions();
      }

      /*
      10web customization
       */
      jQuery(document).on('click', '.elementor-widget-twbb_woocommerce-cart .twbb-product-quantity-change', function() {
        var $input = jQuery(this).parent().find('input');
        if ( jQuery(this).hasClass( 'twbb-minus-quantity' ) ) {
          if( (parseInt($input.val()) - 1) > 0 ) {
            $input.val(parseInt($input.val()) - 1);
          }
        } else {
          $input.val(parseInt($input.val()) + 1);
        }
        $input.change();
        jQuery('button[name=update_cart]').trigger('click');
        return false;
      });
      /*
        end customization
      */
    }

    /**
     * Using the WooCommerce Cart controls (quantity, remove product) in the editor will cause the cart to disappear.
     * This is because WooCommerce does an ajax round trip where it modifies the cart, then loads that cart into the
     * current page and attempts to grab the elements from that page via ajax. In the Editor, if the page is not
     * published yet, it fetches an empty page that does not contain the required elements. As a result, the cart
     * is rendered empty.
     *
     * Due to this issue, the cart controls (quantity, remove product) need to be disabled in the Editor.
     */
    disableActions() {
      const selectors = this.getSettings('selectors');
      this.$element.find(selectors.updateCartButton).attr({
        disabled: 'disabled',
        'aria-disabled': 'true'
      });
      if (elementorFrontend.isEditMode()) {
        this.$element.find(selectors.quantityInput).attr('disabled', 'disabled');
        this.$element.find(selectors.productRemove).css('pointer-events', 'none');
      }
    }
    onElementChange(propertyName) {
      if ('sticky_right_column' === propertyName) {
        this.toggleStickyRightColumn();
      }
      if ('additional_template_select' === propertyName) {
        elementorTenweb.modules.woocommerce.onTemplateIdChange('additional_template_select');
      }
    }
    onDestroy() {
      super.onDestroy(...arguments);
      this.deactivateStickyRightColumn();
    }
    updateCart() {
      const selectors = this.getSettings('selectors');
      clearTimeout(this._debounce);
      this._debounce = setTimeout(() => {
        this.$element.find(selectors.updateCartButton).trigger('click');
      }, 1500);
    }

    applyButtonsHoverAnimation() {
      const elementSettings = this.getElementSettings();
      if (elementSettings.checkout_button_hover_animation) {
        // This element is recaptured every time because the cart markup can refresh
        jQuery('.checkout-button').addClass('elementor-animation-' + elementSettings.checkout_button_hover_animation);
      }
      if (elementSettings.forms_buttons_hover_animation) {
        // This element is recaptured every time because the cart markup can refresh
        jQuery('.shop_table .button').addClass('elementor-animation-' + elementSettings.forms_buttons_hover_animation);
      }
    }

    /**
     * In the editor, WC Frontend JS does not fire (not registered).
     * This causes that hidden inputs parent paragraph elements do not get display:none
     * as they would have on the front end.
     * So this function manually display:none the parent elements of these hidden inputs to avoid having
     * gaps/spaces in the layout caused by these parent elements' margins/paddings.
     */
    hideHiddenInputsParentElements() {
      if (this.isEdit) {
        if (this.elements.$hiddenInput) {
          this.elements.$hiddenInput.parent('.form-row').addClass('elementor-hidden');
        }
      }
    }
  }

  elementorFrontend.hooks.addAction( 'frontend/element_ready/twbb_woocommerce-cart.default', function ( $scope ) {
    new Cart( { $element: $scope } );
  });
});

jQuery( window ).on( 'elementor/frontend/init', function() {
    class Checkout extends TWBB_WooCommerce_Base {
        getDefaultSettings() {
            const defaultSettings = super.getDefaultSettings(...arguments);
            return {
                selectors: {
                    ...defaultSettings.selectors,
                    container: '.elementor-widget-twbb_woocommerce-checkout-page',
                    loginForm: '.e-woocommerce-login-anchor',
                    loginSubmit: '.e-woocommerce-form-login-submit',
                    loginSection: '.e-woocommerce-login-section',
                    showCouponForm: '.e-show-coupon-form',
                    couponSection: '.e-coupon-anchor',
                    showLoginForm: '.e-show-login',
                    applyCoupon: '.e-apply-coupon',
                    checkoutForm: 'form.woocommerce-checkout',
                    couponBox: '.e-coupon-box',
                    address: 'address',
                    wpHttpRefererInputs: '[name="_wp_http_referer"]'
                },
                classes: defaultSettings.classes,
                ajaxUrl: elementorTenwebFrontend.config.ajaxurl
            };
        }
        getDefaultElements() {
            const selectors = this.getSettings('selectors');
            return {
                ...super.getDefaultElements(...arguments),
                $container: this.$element.find(selectors.container),
                $loginForm: this.$element.find(selectors.loginForm),
                $showCouponForm: this.$element.find(selectors.showCouponForm),
                $couponSection: this.$element.find(selectors.couponSection),
                $showLoginForm: this.$element.find(selectors.showLoginForm),
                $applyCoupon: this.$element.find(selectors.applyCoupon),
                $loginSubmit: this.$element.find(selectors.loginSubmit),
                $couponBox: this.$element.find(selectors.couponBox),
                $checkoutForm: this.$element.find(selectors.checkoutForm),
                $loginSection: this.$element.find(selectors.loginSection),
                $address: this.$element.find(selectors.address)
            };
        }
        bindEvents() {
            super.bindEvents(...arguments);
            this.elements.$showCouponForm.on('click', event => {
                event.preventDefault();
                this.elements.$couponSection.slideToggle();
            });
            this.elements.$showLoginForm.on('click', event => {
                event.preventDefault();
                this.elements.$loginForm.slideToggle();
            });
            this.elements.$applyCoupon.on('click', event => {
                event.preventDefault();
                this.applyCoupon();
            });
            this.elements.$loginSubmit.on('click', event => {
                event.preventDefault();
                this.loginUser();
            });
            elementorFrontend.elements.$body.on('updated_checkout', () => {
                this.applyPurchaseButtonHoverAnimation();
                this.updateWpReferers();
            });
        }
        onInit() {
            super.onInit(...arguments);
            this.toggleStickyRightColumn();
            this.updateWpReferers();
            this.equalizeElementHeight(this.elements.$address); // Equalize <address> boxes height

            if (elementorFrontend.isEditMode()) {
                this.elements.$loginForm.show();
                this.elements.$couponSection.show();
                this.applyPurchaseButtonHoverAnimation();

                this.compatabilitySettingUpdate();
                this.set_repeater_state_empty();
            }
        }
        onElementChange(propertyName) {
            if ('sticky_right_column' === propertyName) {
                this.toggleStickyRightColumn();
            }
        }
        onDestroy() {
            super.onDestroy(...arguments);
            this.deactivateStickyRightColumn();
        }
        applyPurchaseButtonHoverAnimation() {
            const purchaseButtonHoverAnimation = this.getElementSettings('purchase_button_hover_animation');
            if (purchaseButtonHoverAnimation) {
                // This element is recaptured every time because the checkout markup can refresh
                jQuery('#place_order').addClass('elementor-animation-' + purchaseButtonHoverAnimation);
            }
        }
        applyCoupon() {
            // Wc_checkout_params is required to continue, ensure the object exists
            // eslint-disable-next-line camelcase
            if (!wc_checkout_params) {
                return;
            }
            this.startProcessing(this.elements.$couponBox);
            const data = {
                // eslint-disable-next-line camelcase
                security: wc_checkout_params.apply_coupon_nonce,
                coupon_code: this.elements.$couponBox.find('input[name="coupon_code"]').val()
            };
            jQuery.ajax({
                type: 'POST',
                // eslint-disable-next-line camelcase
                url: wc_checkout_params.wc_ajax_url.toString().replace('%%endpoint%%', 'apply_coupon'),
                context: this,
                data,
                success(code) {
                    jQuery('.woocommerce-error, .woocommerce-message').remove();
                    this.elements.$couponBox.removeClass('processing').unblock();
                    if (code) {
                        this.elements.$checkoutForm.before(code);
                        this.elements.$couponSection.slideUp();
                        elementorFrontend.elements.$body.trigger('applied_coupon_in_checkout', [data.coupon_code]);
                        elementorFrontend.elements.$body.trigger('update_checkout', {
                            update_shipping_method: false
                        });
                    }
                },
                dataType: 'html'
            });
        }
        loginUser() {
            this.startProcessing(this.elements.$loginSection);
            const data = {
                action: 'elementor_woocommerce_checkout_login_user',
                username: this.elements.$loginSection.find('input[name="username"]').val(),
                password: this.elements.$loginSection.find('input[name="password"]').val(),
                nonce: this.elements.$loginSection.find('input[name="woocommerce-login-nonce"]').val(),
                remember: this.elements.$loginSection.find('input#rememberme').prop('checked')
            };
            jQuery.ajax({
                type: 'POST',
                url: this.getSettings('ajaxUrl'),
                context: this,
                data,
                success(code) {
                    code = JSON.parse(code);
                    this.elements.$loginSection.removeClass('processing').unblock();
                    const messages = jQuery('.woocommerce-error, .woocommerce-message');
                    messages.remove();
                    if (code.logged_in) {
                        location.reload();
                    } else {
                        this.elements.$checkoutForm.before(code.message);
                        elementorFrontend.elements.$body.trigger('checkout_error', [code.message]);
                    }
                }
            });
        }
        startProcessing($form) {
            if ($form.is('.processing')) {
                return;
            }

            /**
             * .block() is from a jQuery blockUI plugin loaded by WooCommerce. This code is based on WooCommerce
             * core in order for the Checkout widget to behave the same as WooCommerce Checkout pages.
             */
            $form.addClass('processing').block({
                message: null,
                overlayCSS: {
                    background: '#fff',
                    opacity: 0.6
                }
            });
        }

        /**
         * Function is set billing, shipping, additional fields repeater state empty
         * empty state allow to edit fields title/ placeholder
        */
        set_repeater_state_empty() {
            elementor.hooks.addAction('panel/open_editor/widget', function (panel, model) {
                // Check if the current widget is the Checkout widget
                if (model.attributes.widgetType === 'twbb_woocommerce-checkout-page') {
                    const settings = model.attributes.settings.attributes;

                    // Access the repeater fields collection
                    const billingFields = settings.billing_details_form_fields;

                    if (billingFields.models && billingFields.models.length > 0) {
                        // Iterate through each item in the repeater collection
                        billingFields.models.forEach(function (fieldModel) {
                            fieldModel.set('repeater_state', '');
                        });
                    }

                    const shippingFields = settings.shipping_details_form_fields;
                    if (shippingFields.models && shippingFields.models.length > 0) {
                        // Iterate through each item in the repeater collection
                        shippingFields.models.forEach(function (fieldModel) {
                            fieldModel.set('repeater_state', '');
                        });
                    }

                    const additionalFields = settings.additional_information_form_fields;
                    if (additionalFields.models && additionalFields.models.length > 0) {
                        // Iterate through each item in the repeater collection
                        additionalFields.models.forEach(function (fieldModel) {
                            fieldModel.set('repeater_state', '');
                        });
                    }
                }
            });
        }

        /**
        *   The function is changing new control values to old control values
        *   Changes was done to keep compatability from version 1.26
        */
        compatabilitySettingUpdate() {
            const widgetSelector = '.elementor-widget-twbb_woocommerce-checkout-page';
            const $widget = jQuery(widgetSelector);

            // Retrieve the model ID and settings container
            const modelId = $widget.data('id');
            const container = parent.window.$e.components.get('document').utils.findContainerById(modelId);

            if (!container) return;

            const settings = container.model.attributes.settings.attributes;
            let need_update = false;

            // Skip processing if 'backCampability' is already set
            if (settings['backCompability']) return;

            // Typography keys
            const typographyKeys = [
                '_typography',
                '_font_family',
                '_font_size',
                '_font_mobile',
                '_font_tablet',
                '_font_style',
                '_font_weight',
                '_line_height',
                '_line_height_mobile',
                '_line_height_tablet',
                '_letter_spacing',
                '_text_transform',
                '_text_decoration',
                '_word_spacing',
                '_word_spacing_mobile',
                '_word_spacing_desktop',
            ];

            // Mapping of old controls to new controls
            const replaceControls = {
                order_summary_shipping_price_typography: "order_summary_totals_typography",
                order_summary_tax_rate_price_typography: "order_summary_totals_typography",
                order_summary_items_titles_typography: "order_summary_totals_typography",
                order_summary_total_title_typography: "order_summary_totals_typography",
                order_summary_subtotal_price_typography: "order_summary_totals_typography",
                order_summary_total_price_typography: "order_summary_totals_typography",
                order_summary_product_typography: "order_summary_items_typography",
                order_summary_product_price_typography: "order_summary_items_typography",
            };

            // Update settings based on replace controls
            Object.entries(replaceControls).forEach(([newControl, oldControl]) => {
                if (settings[`${oldControl}_typography`] === 'custom') {
                    need_update = true;
                    typographyKeys.forEach((key) => {
                        const oldKey = `${oldControl}${key}`;
                        const newKey = `${newControl}${key}`;

                        // Update settings only if the old key exists
                        if (settings[oldKey] !== undefined) {
                            settings[newKey] = settings[oldKey];
                        }
                    });
                }
            });

            const replaceControlsColors = {
                order_summary_shipping_price_color: "order_summary_totals_color",
                order_summary_tax_rate_price_color: "order_summary_totals_color",
                order_summary_items_titles_color: "order_summary_totals_color",
                order_summary_subtotal_price_color: "order_summary_totals_color",
                order_summary_total_title_color: "order_summary_totals_color",
                order_summary_total_price_color: "order_summary_totals_color",
                order_summary_product_price_color: "order_summary_items_color",
                order_summary_product_color: "order_summary_items_color",
            };

            // Update settings based on replace controls
            Object.entries(replaceControlsColors).forEach(([newControl, oldControl]) => {
                if (settings[oldControl] !== '') {
                    need_update = true;
                    if (settings[newControl] !== undefined) {
                        settings[newControl] = settings[oldControl];
                    }
                }
            });


            // Save settings if updates were made
            settings['backCompability'] = 1;

            if( need_update ) {
                // Save settings and update the document
                window.parent.$e.commands.run('document/elements/settings', {
                    container: container,
                    options: { render: false },
                    settings: settings,
                });

                // Async save without triggering visual loading
                (async () => {
                    try {
                        await window.parent.$e.run('document/save/update', { force: true });

                        console.log("Settings updated and saved successfully.");
                    } catch (error) {
                        console.error("Error during save operation:", error);
                    }
                })();
            }
        }

    }
    elementorFrontend.hooks.addAction( 'frontend/element_ready/twbb_woocommerce-checkout-page.default', function ( $scope ) {
        new Checkout( { $element: $scope } );
    });
});

/* The shipping methods now have a new HTML structure in version 1.26.
Since the old structure is generated by the WooCommerce template,
we need to remove it from the DOM to prevent duplication of radio button IDs  */
document.addEventListener('DOMContentLoaded', function() {
    function removeOldShippingMethods() {
        // Select the old shipping elements
        const oldShippingElements = document.querySelectorAll('.woocommerce-checkout-review-order .woocommerce-shipping-totals:not(.twbb-shipping-totals)');

        // Select the old shipping elements
        const oldShipToDifferent = document.querySelectorAll('.col-2 #ship-to-different-address');

        // Remove them from the DOM
        oldShippingElements.forEach(element => element.remove());

        // Remove them from the DOM
        oldShipToDifferent.forEach(element => element.remove());

        /* WooCommerce updates the DOM dynamically during an AJAX render, and the checked attribute for the
        selected shipping method may not persist, that is why we need trigger select */
        const activeMethodInput = document.querySelector('.twbb-active-method input.shipping_method');
        if (activeMethodInput) {
            activeMethodInput.checked = true; // Set the input as checked if it exists
        }

    }

    // Remove old shipping on initial page load
    removeOldShippingMethods();

    // Listen for WooCommerce checkout updates
    jQuery(document.body).on('updated_checkout', function() {
        removeOldShippingMethods();
    });
});
jQuery( window ).on( 'elementor/frontend/init', function() {

	class MiniCartHandler extends elementorModules.frontend.handlers.Base {

		getDefaultSettings() {
			return {
				selectors: {
					container: '.twbb_menu-cart__container',
					main: '.twbb_menu-cart__main',
					toggle: '.twbb_menu-cart__toggle',
					toggleButton: '#twbb_menu-cart__toggle_button',
					toggleWrapper: '.twbb_menu-cart__toggle_wrapper',
					closeButton: '.twbb_menu-cart__close-button, .twbb_menu-cart__close-button-custom',
					productList: '.twbb_menu-cart__products'
				},
				classes: {
					isShown: 'twbb_menu-cart--shown'
				}
			};
		}

		getDefaultElements() {
			const selectors = this.getSettings('selectors');
			return {
				$container: this.$element.find(selectors.container),
				$main: this.$element.find(selectors.main),
				$toggleWrapper: this.$element.find(selectors.toggleWrapper),
				$closeButton: this.$element.find(selectors.closeButton)
			};
		}

		toggleCart() {
			if (!this.isCartOpen) {
				this.showCart();
			} else {
				this.hideCart();
			}
		}

		showCart() {
			if (this.isCartOpen) {
				return;
			}

			const classes = this.getSettings('classes'),
				selectors = this.getSettings('selectors');
			this.isCartOpen = true;
			this.$element.addClass(classes.isShown);
			this.$element.find(selectors.toggleButton).attr('aria-expanded', true);
			this.elements.$main.attr('aria-hidden', false);
			this.elements.$container.attr('aria-hidden', false);
		}

		hideCart() {
			if (!this.isCartOpen) {
				return;
			}

			const classes = this.getSettings('classes'),
				selectors = this.getSettings('selectors');
			this.isCartOpen = false;
			this.$element.removeClass(classes.isShown);
			this.$element.find(selectors.toggleButton).attr('aria-expanded', false);
			this.elements.$main.attr('aria-hidden', true);
			this.elements.$container.attr('aria-hidden', true);
		}

		automaticallyOpenCart() {
			const settings = this.getElementSettings();

			if ('yes' === settings.automatically_open_cart) {
				this.showCart();
			}
		}
		refreshFragments(eventType) {
			let data = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;

			if (elementorFrontend.isEditMode() && elementorTenweb.modules.woocommerce.didManuallyTriggerAddToCartEvent(data)) {
				return false;
			}
			const templatesInPage = [];
			jQuery.each(elementorFrontend.documentsManager.documents, index => {
				templatesInPage.push(index);
			});
			jQuery.ajax({
				type: 'POST',
				url: elementorTenwebFrontend.config.ajaxurl,
				context: this,
				data: {
					action: 'twbb_menu_cart_fragments',
					templates: templatesInPage,
					_nonce: ElementorTenwebFrontendConfig.woocommerce.menu_cart.fragments_nonce,
					is_editor: elementorFrontend.isEditMode()
				},
				success(successData) {
					if (successData?.fragments) {
						jQuery.each(successData.fragments, (key, value) => {
							jQuery(key).replaceWith(value);
						});
					}
				},
				complete() {
					if ('added_to_cart' === eventType) {
						this.automaticallyOpenCart();
					}
				}
			});
		}
		bindEvents() {
			const menuCart = elementorTenwebFrontend.config.woocommerce.menu_cart,
				noQueryParams = -1 === menuCart.cart_page_url.indexOf('?'),
				currentUrl = noQueryParams ? window.location.origin + window.location.pathname : window.location.href,
				cartUrl = menuCart.cart_page_url,
				isCart = menuCart.cart_page_url === currentUrl,
				isCheckout = menuCart.checkout_page_url === currentUrl,
				selectors = this.getSettings('selectors');

			if (isCart && isCheckout) {
				this.$element.find(selectors.toggleButton).attr('href', cartUrl);
				return;
			} // Cache cart open state.

			// Cache cart open state.
			const classes = this.getSettings('classes');
			this.isCartOpen = this.$element.hasClass(classes.isShown);
			const settings = this.getElementSettings();
			if ('mouseover' === settings.open_cart) {
				// Enable opening of mini-cart and side-cart by hover (include click so we can `preventDefault()` page-top jump on click).
				this.elements.$toggleWrapper.on('mouseover click', selectors.toggleButton, event => {
					event.preventDefault();
					this.showCart();
				}); // Close Cart on mouseleave.

				this.elements.$toggleWrapper.on('mouseleave', () => this.hideCart());
			} else {
				// Enable opening of mini-cart and side-cart by click.
				this.elements.$toggleWrapper.on('click', selectors.toggleButton, event => {
					event.preventDefault();
					this.toggleCart();
				});
			} // Listen for clicks outside to close any open cart.


			elementorFrontend.elements.$document.on('click', event => {
				if (!this.isCartOpen) {
					return;
				}

				const $target = jQuery(event.target); // Don't close if this is click on the main panel or toggle button.

				if ($target.closest(this.elements.$main).length || $target.closest(selectors.toggle).length) {
					return;
				}

				this.hideCart();
			});
			this.elements.$closeButton.on('click', event => {
				event.preventDefault();
				this.hideCart();
			});
			elementorFrontend.elements.$document.on('keyup', event => {
				const ESC_KEY = 27;

				if (ESC_KEY === event.keyCode) {
					this.hideCart();
				}
			});
			elementorFrontend.elements.$body.on('wc_fragments_refreshed removed_from_cart added_to_cart', (event, data) => this.refreshFragments(event.type, data));

			elementorFrontend.addListenerOnce(this.getUniqueHandlerID() + '_window_resize_dropdown', 'resize', () => this.governDropdownHeight());
			elementorFrontend.elements.$body.on('wc_fragments_loaded wc_fragments_refreshed', () => this.governDropdownHeight());
		}

		unbindEvents() {
			elementorFrontend.removeListeners(this.getUniqueHandlerID() + '_window_resize_dropdown', 'resize');
		}

		onInit() {
			super.onInit();
			/**
			 * When the page is reloaded after an item is added to cart, and the user activated the
			 * "Automatically Open Cart" option, the cart should open to show the updated contents.
			 */

			if (elementorTenwebFrontend.config.woocommerce.productAddedToCart) {
				this.automaticallyOpenCart();
			} // Govern the height of the mini-cart dropdown.


			this.governDropdownHeight();
		}

		governDropdownHeight() {
			const settings = this.getElementSettings(),
				selectors = this.getSettings('selectors'); // Only do this for mini-cart.

			if ('mini-cart' !== settings.cart_type) {
				return;
			} // Elements need to be re-instantiated every time as WooCommerce reloads the toggle button
			// and cart contents in our widget when the cart changes e.g. adding products to the cart.


			const $productList = this.$element.find(selectors.productList),
				$toggle = this.$element.find(selectors.toggle); // Make sure required elements exist.

			if (!$productList.length || !$toggle.length) {
				return;
			} // Remove max-height of productList so we can take new measurements.


			this.$element.find(selectors.productList).css('max-height', ''); // Calculate what the height of the productList should be based on elements above, below and it's vertical position.

			const windowHeight = document.documentElement.clientHeight,
				toggleHeight = $toggle.height() + parseInt(this.elements.$main.css('margin-top')),
				toggleTopPosition = $toggle[0].getBoundingClientRect().top,
				productListHeight = $productList.height(),
				dropdownWithoutViewportHeight = this.elements.$main.prop('scrollHeight') - productListHeight,
				extraBottomSpacing = 30,
				maxViewportHeight = windowHeight - toggleTopPosition - toggleHeight - dropdownWithoutViewportHeight - extraBottomSpacing,
				optimalViewportHeight = Math.max(120, maxViewportHeight); // Apply max-height to the productList.

			$productList.css('max-height', optimalViewportHeight);
		}
	}

	elementorFrontend.elementsHandler.attachHandler( 'twbb_woocommerce-menu-cart', MiniCartHandler );

	if (elementorFrontend.isEditMode()) {
		elementorFrontend.on('components:init', () => {
			if (!elementorFrontend.elements.$body.find('.twbb_widget-woocommerce-cart').length) {
				elementorFrontend.elements.$body.append('<div class="woocommerce-cart-form">');
			}
		});
	}

	//code is added to fix the issue with the topbars in demo and not only ( for example domain header)
	if( jQuery('.twbb-custom-topbars').length ) {
		let topbarHeight = jQuery('.twbb-custom-topbars').outerHeight();
		jQuery('.elementor-widget-twbb_woocommerce-menu-cart .twbb_menu-cart__container.elementor-lightbox').css('margin-top', topbarHeight);
	}

});

jQuery( window ).on( 'elementor/frontend/init', function() {
  class MyAccountHandler extends TWBB_WooCommerce_Base {
    getDefaultSettings() {
      return {
        selectors: {
          address: 'address',
          tabLinks: '.woocommerce-MyAccount-navigation-link a',
          viewOrderButtons: '.my_account_orders .woocommerce-button.view',
          viewOrderLinks: '.woocommerce-orders-table__cell-order-number a',
          authForms: 'form.login, form.register',
          tabWrapper: '.e-my-account-tab',
          tabItem: '.woocommerce-MyAccount-navigation li',
          allPageElements: '[e-my-account-page]',
          purchasenote: 'tr.product-purchase-note',
          contentWrapper: '.woocommerce-MyAccount-content-wrapper'
        }
      };
    }

    getDefaultElements() {
      const selectors = this.getSettings( 'selectors' );
      return {
        $address: this.$element.find( selectors.address ),
        $tabLinks: this.$element.find( selectors.tabLinks ),
        $viewOrderButtons: this.$element.find( selectors.viewOrderButtons ),
        $viewOrderLinks: this.$element.find( selectors.viewOrderLinks ),
        $authForms: this.$element.find( selectors.authForms ),
        $tabWrapper: this.$element.find( selectors.tabWrapper ),
        $tabItem: this.$element.find( selectors.tabItem ),
        $allPageElements: this.$element.find( selectors.allPageElements ),
        $purchasenote: this.$element.find( selectors.purchasenote ),
        $contentWrapper: this.$element.find( selectors.contentWrapper )
      };
    }

    editorInitTabs() {
      this.elements.$allPageElements.each( ( index, element ) => {
        const currentPage = element.getAttribute( 'e-my-account-page' );
        let $linksToThisPage;
        switch ( currentPage ) {
          case 'view-order':
            $linksToThisPage = this.elements.$viewOrderLinks.add( this.elements.$viewOrderButtons );
            break;
          default:
            $linksToThisPage = this.$element.find( '.woocommerce-MyAccount-navigation-link--' + currentPage );
        }
        $linksToThisPage.on( 'click', () => {
          this.currentPage = currentPage;
          this.editorShowTab();
        } );
      } );
    }

    editorShowTab() {
      const $currentPage = this.$element.find( '[e-my-account-page="' + this.currentPage + '"]' );
      this.$element.attr( 'e-my-account-page', this.currentPage );
      this.elements.$allPageElements.hide();
      $currentPage.show();
      this.toggleEndpointClasses();
      if ( 'view-order' !== this.currentPage ) {
        this.elements.$tabItem.removeClass( 'is-active' );
        this.$element.find( '.woocommerce-MyAccount-navigation-link--' + this.currentPage ).addClass( 'is-active' );
      }

      /**
       * We need to run equalizeElementHeights() again when the 'edit-address' or 'view-order' tab is shown, because jQuery cannot
       * get the height of hidden elements, and this tab was hidden on initial page load in the editor.
       */
      if ( 'edit-address' === this.currentPage || 'view-order' === this.currentPage ) {
        this.equalizeElementHeights();
      }
    }

    toggleEndpointClasses() {
      const wcPages = [ 'dashboard', 'orders', 'view-order', 'downloads', 'edit-account', 'edit-address', 'payment-methods' ];
      let wrapperClass = '';
      this.elements.$tabWrapper.removeClass( 'e-my-account-tab__' + wcPages.join( ' e-my-account-tab__' ) + ' e-my-account-tab__dashboard--custom' );
      if ( 'dashboard' === this.currentPage && this.elements.$contentWrapper.find( '.elementor' ).length ) {
        wrapperClass = ' e-my-account-tab__dashboard--custom';
      }
      if ( wcPages.includes( this.currentPage ) ) {
        this.elements.$tabWrapper.addClass( 'e-my-account-tab__' + this.currentPage + wrapperClass );
      }
    }

    applyButtonsHoverAnimation() {
      const elementSettings = this.getElementSettings();
      if ( elementSettings.forms_buttons_hover_animation ) {
        this.$element.find( '.woocommerce button.button,  #add_payment_method #payment #place_order' ).addClass( 'elementor-animation-' + elementSettings.forms_buttons_hover_animation );
      }
      if ( elementSettings.tables_button_hover_animation ) {
        this.$element.find( '.order-again .button, td .button, .woocommerce-pagination .button' ).addClass( 'elementor-animation-' + elementSettings.tables_button_hover_animation );
      }
    }

    equalizeElementHeights() {
      this.equalizeElementHeight( this.elements.$address ); // Equalize <address> boxes height

      if ( !this.isEdit ) {
        // Auth forms do not display in the Editor
        this.equalizeElementHeight( this.elements.$authForms ); // Equalize login/reg boxes height
      }
    }

    onElementChange( propertyName ) {
      // When the 'General Text' Typography or 'Section' Padding is changed, the height of the boxes need to update as well.
      if ( 0 === propertyName.indexOf( 'general_text_typography' ) || 0 === propertyName.indexOf( 'sections_padding' ) ) {
        this.equalizeElementHeights();
      }
      if ( 0 === propertyName.indexOf( 'forms_rows_gap' ) ) {
        this.removePaddingBetweenPurchaseNote( this.elements.$purchasenote );
      }
      if ( 'customize_dashboard_select' === propertyName ) {
        elementorTenweb.modules.woocommerce.onTemplateIdChange( 'customize_dashboard_select' );
      }
    }

    bindEvents() {
      super.bindEvents();

      // The heights of the Registration and Login boxes need to be recaclulated and equalized when
      // WooCommerce adds validation messages (such as the password strength meter) into these sections.
      elementorFrontend.elements.$body.on( 'keyup change', '.register #reg_password', () => {
        this.equalizeElementHeights();
      } );
    }

    onInit() {
      super.onInit( ...arguments );
      if ( this.isEdit ) {
        this.editorInitTabs();
        if ( !this.$element.attr( 'e-my-account-page' ) ) {
          this.currentPage = 'dashboard';
        }
        else {
          this.currentPage = this.$element.attr( 'e-my-account-page' );
        }
        this.editorShowTab();
      }
      this.applyButtonsHoverAnimation();
      this.equalizeElementHeights();
      this.removePaddingBetweenPurchaseNote( this.elements.$purchasenote );
    }
  }

  elementorFrontend.hooks.addAction( 'frontend/element_ready/twbb_woocommerce-my-account.default', function ( $scope ) {
    new MyAccountHandler( { $element: $scope } );
  });
});
jQuery( window ).on( 'elementor/frontend/init', function() {
  class TWBB_woocommerce_notices extends elementorModules.frontend.handlers.Base {
    getDefaultSettings() {
      return {
        selectors: {
          woocommerceNotices: '.woocommerce-NoticeGroup, :not(.woocommerce-NoticeGroup) .woocommerce-error, :not(.woocommerce-NoticeGroup) .woocommerce-message, :not(.woocommerce-NoticeGroup) .woocommerce-info',
          noticesWrapper: '.e-woocommerce-notices-wrapper'
        }
      };
    }
    getDefaultElements() {
      const selectors = this.getSettings('selectors');
      return {
        $documentScrollToElements: elementorFrontend.elements.$document.find('html, body'),
        $woocommerceCheckoutForm: elementorFrontend.elements.$body.find('.form.checkout'),
        $noticesWrapper: this.$element.find(selectors.noticesWrapper)
      };
    }
    moveNotices() {
      let scrollToNotices = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
      const selectors = this.getSettings('selectors');
      let $notices = elementorFrontend.elements.$body.find(selectors.woocommerceNotices);
      if (elementorFrontend.isEditMode() || elementorFrontend.isWPPreviewMode()) {
        $notices = $notices.filter(':not(.e-notices-demo-notice)');
      }
      if (scrollToNotices) {
        this.elements.$documentScrollToElements.stop();
      }
      this.elements.$noticesWrapper.prepend($notices);
      if (!this.is_ready) {
        this.elements.$noticesWrapper.removeClass('e-woocommerce-notices-wrapper-loading');
        this.is_ready = true;
      }
      if (scrollToNotices) {
        let $scrollToElement = $notices;
        if (!$scrollToElement.length) {
          $scrollToElement = this.elements.$woocommerceCheckoutForm;
        }
        if ($scrollToElement.length) {
          // Scrolls to the notice and puts it in the middle of the window so users' attention is drawn to it.
          this.elements.$documentScrollToElements.animate({
            scrollTop: $scrollToElement.offset().top - document.documentElement.clientHeight / 2
          }, 1000);
        }
      }
    }
    onInit() {
      super.onInit();
      this.is_ready = false;
      this.moveNotices(true);
    }
    bindEvents() {
      elementorFrontend.elements.$body.on('updated_wc_div updated_checkout updated_cart_totals applied_coupon removed_coupon applied_coupon_in_checkout removed_coupon_in_checkout checkout_error', () => this.moveNotices(true));
    }
  }

  elementorFrontend.hooks.addAction( 'frontend/element_ready/twbb_woocommerce-notices.default', function ( $scope ) {
    new TWBB_woocommerce_notices( { $element: $scope } );
  });
});
//for ie
(function () {
  if (typeof window.CustomEvent === "function") {
    return false;
  }

  function CustomEvent(event, params) {
    params = params || {bubbles: false, cancelable: false, detail: undefined};
    var evt = document.createEvent('CustomEvent');
    evt.initCustomEvent(event, params.bubbles, params.cancelable, params.detail);
    return evt;
  }

  CustomEvent.prototype = window.Event.prototype;
  window.CustomEvent = CustomEvent;
})();
(function () {
  function extend(o1, o2) {
    for (var key in o2) {
      if (o2.hasOwnProperty(key)) {
        o1[key] = o2[key];
      }
    }
  }

  this.MultiRange = function MultiRange(placeholderElm, settings) {
    settings = typeof settings == 'object' ? settings : {}; // make sure settings is an 'object'
    this.settings = {
      minRange: typeof settings.minRange == 'number' ? settings.minRange : 1,
      tickStep: settings.tickStep || 5,
      step: typeof settings.step == 'number' ? settings.step : 1,
      scale: 100,
      min: settings.min || 0,
      max: settings.max || 100,
    };
    this.delta = this.settings.max - this.settings.min;
    // if "ticks" count was defined, re-calculate the "tickStep"
    if (settings.ticks) {
      this.settings.tickStep = this.delta / settings.ticks;
    }
    // a list of ranges (ex. [5,20])
    this.ranges = settings.ranges || [
      this.settings.ranges[0],
      this.settings.ranges[1]
    ];
    this.id = Math.random().toString(36).substr(2, 9); // almost-random ID
    this.DOM = {}; // Store all relevant DOM elements in an Object
    extend(this, new this.EventDispatcher());
    this.build(placeholderElm);
    this.events.binding.call(this);
  };
  MultiRange.prototype = {
    build: function (placeholderElm) {
      var that = this,
        scopeClasses = placeholderElm.className.indexOf('multiRange') == -1 ?
          'multiRange ' + placeholderElm.className :
          placeholderElm.className;
      this.DOM.scope = document.createElement('div');
      this.DOM.scope.className = scopeClasses;
      this.DOM.rangeWrap = document.createElement('div');
      this.DOM.rangeWrap.className = 'multiRange__rangeWrap';
      this.DOM.rangeWrap.innerHTML = this.getRangesHTML();
      this.DOM.ticks = document.createElement('div');
      this.DOM.ticks.className = 'multiRange__ticks';
      this.DOM.ticks.innerHTML = this.generateTicks();
      // append to Scope
      this.DOM.scope.appendChild(this.DOM.rangeWrap);
      this.DOM.scope.appendChild(this.DOM.ticks);
      // replace the placeholder component element with the real one
      placeholderElm.parentNode.replaceChild(this.DOM.scope, placeholderElm);
    },
    generateTicks() {
      var steps = (this.delta) / this.settings.tickStep,
        HTML = '',
        value,
        i;
      for (i = 0; i <= steps; i++) {
        value = (+this.settings.min) + this.settings.tickStep * i; // calculate tick value
        value = value.toFixed(1).replace('.0', ''); // cleaup
        HTML += '<div data-value="' + value + '"></div>';
      }
      return HTML;
    },
    getRangesHTML() {
      var that = this,
        rangesHTML = '',
        ranges;
      this.ranges.unshift(0);
      //  if( this.ranges[0] > this.settings.min )
      //      this.ranges.unshift(this.settings.min)
      if (this.ranges[this.ranges.length - 1] <= this.settings.max) {
        this.ranges.push(this.settings.max);
      }
      ranges = this.ranges;
      ranges.forEach(function (range, i) {
        if (i == ranges.length - 1) {
          return;
        } // skip last ltem
        var leftPos = (range - that.settings.min) / (that.delta) * 100;
        // protection..
        if (leftPos < 0) {
          leftPos = 0;
        }
        // range =  ranges[i+1] - range;
        rangesHTML += '<div data-idx="' + i + '" class="multiRange__range" style="left:' + leftPos + '%"><div class="multiRange__handle"></div></div>';
        if (i == 1) {
          jQuery('.current_min_price').html(range.toFixed(1).replace('.0', ''));
        }
        else if (i == 2) {
          jQuery('.current_max_price').html(range.toFixed(1).replace('.0', ''));
        }
      })
      return rangesHTML;
    },
    /**
     * A constructor for exposing events to the outside
     */
    EventDispatcher: function () {
      // Create a DOM EventTarget object
      var target = document.createTextNode('');
      // Pass EventTarget interface calls to DOM EventTarget object
      this.off = target.removeEventListener.bind(target);
      this.on = target.addEventListener.bind(target);
      this.trigger = function (eventName, data) {
        if (!eventName) {
          return;
        }
        var e = new CustomEvent(eventName, {"detail": data});
        target.dispatchEvent(e);
      }
    },
    /**
     * DOM events listeners binding
     */
    events: {
      binding: function () {
        this.DOM.rangeWrap.addEventListener('mousedown', this.events.callbacks.onMouseDown.bind(this));
        this.DOM.rangeWrap.addEventListener('touchstart', this.events.callbacks.onMouseDown.bind(this));
        //prevent anything from being able to be dragged
        this.DOM.scope.addEventListener("dragstart", function (e) {
          return false;
        });
        // this.eventDispatcher.on('add', this.settings.callbacks.add)
      },
      callbacks: {
        onMouseDown: function (e) {
          var target = e.target;
          if (!target) {
            return;
          }
          if (target.className == 'multiRange__handle__value') {
            target = target.parentNode;
          }
          else if (target.className != 'multiRange__handle') {
            return;
          }
          // set some variables (so percentages could be calculated on mousemove)
          var _BCR = this.DOM.scope.getBoundingClientRect();
          this.offsetLeft = _BCR.left;
          this.scopeWidth = _BCR.width;
          this.DOM.currentSlice = target.parentNode;
          this.DOM.currentSlice.classList.add('grabbed');
          this.DOM.currentSliceValue = this.DOM.currentSlice.querySelector('.multiRange__handle__value');
          document.body.classList.add('multiRange-grabbing');
          // bind temporary events (save "bind" reference so events could later be removed)
          this.events.onMouseUpFunc = this.events.callbacks.onMouseUp.bind(this);
          this.events.mousemoveFunc = this.events.callbacks.onMouseMove.bind(this);
          window.addEventListener('mouseup', this.events.onMouseUpFunc);
          window.addEventListener('mousemove', this.events.mousemoveFunc);
          window.addEventListener('touchend', this.events.onMouseUpFunc);
          window.addEventListener('touchmove', this.events.mousemoveFunc);
        },
        onMouseUp: function (e) {
          this.DOM.currentSlice.classList.remove('grabbed');
          window.removeEventListener('mousemove', this.events.mousemoveFunc);
          window.removeEventListener('mouseup', this.events.onMouseUpFunc);
          window.removeEventListener('touchmove', this.events.mousemoveFunc);
          window.removeEventListener('touchend', this.events.onMouseUpFunc);
          document.body.classList.remove('multiRange-grabbing');
          // publish the event
          var value = parseInt(this.DOM.currentSlice.style.left);
          this.trigger('changed', {idx: +this.DOM.currentSlice.dataset.idx, value: value, ranges: this.ranges});
          this.DOM.currentSlice = null;
        },
        onMouseMove: function (e) {
          if (!this.DOM.currentSlice) {
            window.removeEventListener('mouseup', this.events.onMouseUpFunc);
            window.removeEventListener('touchend', this.events.onMouseUpFunc);
            return;
          }
          // do not continue if the mouse was overflowing of the left or the right side of the range
          //if(  e.clientX < this.offsetLeft || e.clientX > (this.offsetLeft + this.scopeWidth) )
          //  return;
          var that = this,
            value,
            xPosScopeLeft;// the numeric value
          if (e.touches) { //for touch devices
            xPosScopeLeft = e.touches[0].clientX - this.offsetLeft;
          }
          else {
            xPosScopeLeft = e.clientX - this.offsetLeft;
          }
          var leftPrecentage = xPosScopeLeft / this.scopeWidth * 100;
          var prevSliceValue = this.ranges[+this.DOM.currentSlice.dataset.idx - 1];
          var nextSliceValue = this.ranges[+this.DOM.currentSlice.dataset.idx + 1];
          value = this.settings.min + (this.delta / 100 * leftPrecentage);
          if (this.settings.step) {
            // if( value%this.settings.step > 1 ) return;
            value = Math.round((value) / this.settings.step) * this.settings.step;
          }
          // make sure a slice value doesn't go above the next slice value and not below the previous one
          if (value < prevSliceValue + this.settings.minRange) {
            value = prevSliceValue + this.settings.minRange;
          }
          if (value > nextSliceValue - this.settings.minRange) {
            value = nextSliceValue - this.settings.minRange;
          }
          // define min and max move points
          if (value < (this.settings.min + this.settings.minRange)) {
            value = this.settings.min + this.settings.minRange;
          }
          if (value > (this.settings.max - this.settings.minRange)) {
            value = this.settings.max - this.settings.minRange;
          }
          leftPrecentage = (value - this.settings.min) / this.delta * 100;
          // update the DOM
          window.requestAnimationFrame(function () {
            if (that.DOM.currentSlice) {
              that.DOM.currentSlice.style.left = leftPrecentage + '%';
              //that.DOM.currentSliceValue.innerHTML = value.toFixed(1).replace('.0', '');
            }
          });
          // update "ranged" Array
          this.ranges[this.DOM.currentSlice.dataset.idx] = +value.toFixed(1);
          this.currentMinPriceDOM = jQuery(this.DOM.scope).closest('.twbb_woo_price_filter').children('.twbb_woo_price_filter-info').children('.twbb_woo_price_filter-info-price_range').children('span').children('.current_min_price');
          this.currentMaxPriceDOM = jQuery(this.DOM.scope).closest('.twbb_woo_price_filter').children('.twbb_woo_price_filter-info').children('.twbb_woo_price_filter-info-price_range').children('span').children('.current_max_price');
          jQuery(this.currentMinPriceDOM).html(this.ranges[1]);
          jQuery(this.currentMaxPriceDOM).html(this.ranges[2]);
          // publish the event
          this.trigger('change', {idx: +this.DOM.currentSlice.dataset.idx, value: value, ranges: this.ranges})
          jQuery('.price1').attr('value', this.ranges[1]);
          jQuery('.price2').attr('value', this.ranges[2]);
        }
      }
    }
  }
})(this);
let priceFilters = document.querySelectorAll('.twbb_woo_price_filter');
let currentMinPrice = parseInt(jQuery('.price1').attr('value'));
let allMinPrice = parseInt(jQuery('.price1').attr('data-minPrice'));
let currentMaxPrice = parseInt(jQuery('.price2').attr('value'));
let allMaxPrice = parseInt(jQuery('.price2').attr('data-maxPrice'));
for (let i = 0; i < priceFilters.length; i++) {
  new MultiRange(document.querySelectorAll('.multiRange')[i], {
    ranges: [currentMinPrice, currentMaxPrice],
    min: allMinPrice,
    max: allMaxPrice,
    step: 1,
    minRange: 0,
    ticks: 4,
  });
}

jQuery(".twbb_woo_price_filter").submit(function(e) {
  e.preventDefault();
  var href = new URL(location.href);
  href.searchParams.delete('product-page');
  window.history.pushState(null, null, href.href);
  //location.href = href.href;
  this.submit();
});
jQuery( window ).on( 'elementor/frontend/init', function() {
    class ProductAddToCart extends TWBB_WooCommerce_Base {
        getDefaultSettings() {
            return {
                selectors: {
                    quantityInput: '.e-loop-add-to-cart-form input.qty',
                    addToCartButton: '.e-loop-add-to-cart-form .ajax_add_to_cart',
                    addedToCartButton: '.added_to_cart',
                    loopFormContainer: '.e-loop-add-to-cart-form-container'
                }
            };
        }

        getDefaultElements() {
            const selectors = this.getSettings('selectors');
            return {
                $quantityInput: this.$element.find(selectors.quantityInput),
                $addToCartButton: this.$element.find(selectors.addToCartButton)
            };
        }

        updateAddToCartButtonQuantity() {
            this.elements.$addToCartButton.attr('data-quantity', this.elements.$quantityInput.val());
        }

        handleAddedToCart($button) {
            const selectors = this.getSettings('selectors'),
                $addToCartButton = $button.siblings(selectors.addedToCartButton),
                $loopFormContainer = $addToCartButton.parents(selectors.loopFormContainer);
            $loopFormContainer.children(selectors.addedToCartButton).remove();
            $loopFormContainer.append($addToCartButton);
        }

        bindEvents() {
            super.bindEvents(...arguments);
            this.elements.$quantityInput.on('change', () => {
                this.updateAddToCartButtonQuantity();
            });
            elementorFrontend.elements.$body.off('added_to_cart.twbb_woocommerce-product-add-to-cart');
            elementorFrontend.elements.$body.on('added_to_cart.twbb_woocommerce-product-add-to-cart', (e, fragments, cartHash, $button) => {
                this.handleAddedToCart($button);
            });
        }
    }
    elementorFrontend.hooks.addAction( 'frontend/element_ready/twbb_woocommerce-product-add-to-cart.default', function ( $scope ) {
        new ProductAddToCart( { $element: $scope } );
    });
});
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
jQuery( window ).on( 'elementor/frontend/init', function() {
  /*
10web customization
 */
  jQuery(document).on('click', '.elementor-widget-twbb_woocommerce-page .twbb-product-quantity-change', function() {
    var $input = jQuery(this).parent().find('input');
    if ( jQuery(this).hasClass( 'twbb-minus-quantity' ) ) {
      if( (parseInt($input.val()) - 1) > 0 ) {
        $input.val(parseInt($input.val()) - 1);
      }
    } else {
      $input.val(parseInt($input.val()) + 1);
    }
    $input.change();
    jQuery('button[name=update_cart]').trigger('click');
    return false;
  });
  /*
    end customization
  */

  elementorFrontend.hooks.addAction( 'frontend/element_ready/twbb_woocommerce-page.default', function ( $scope ) {
    var single_product = jQuery('body .elementor-widget-twbb_woocommerce-page');
    if( single_product.length > 1 ) {
      alert("The page already includes a WooCommerce Pages Widget element.");
      elementor.getPanelView().getCurrentPageView().getOption('editedElementView').removeElement();
    }
  });

  if( jQuery("body").hasClass("single-product") ) {
    jQuery("div[data-elementor-type=twbb_single]").addClass("product");
  }
});

jQuery( window ).on( 'elementor/frontend/init', function () {

    image_gallery_run();
    image_gallery_second_image_run();

    jQuery(document).on('mouseenter', '.elementor-widget-twbb_woocommerce-products[data-widget_type="twbb_woocommerce-products.modern"].product_buttons_visibility__show_on_hover .product', function() {
        jQuery(this).find('.twbb-add_to_cart_container_open').css({'display':'flex'});
        let buttonsContainerHeight = parseInt(jQuery(this).find('.twbb-add_to_cart_container_open').outerHeight());
        jQuery(this).find('.product_info_div').css('bottom',buttonsContainerHeight + 'px');
    })
    jQuery(document).on('mouseleave ', '.elementor-widget-twbb_woocommerce-products[data-widget_type="twbb_woocommerce-products.modern"].product_buttons_visibility__show_on_hover .product', function() {
        jQuery(this).find('.twbb-add_to_cart_container_open').css({'display':'none'});
        jQuery(this).find('.product_info_div').css('bottom',0);
    })


    jQuery('.twbb_woocommerce-products-ajax-paginate .page-numbers li').on('click', function(e) {
        e.preventDefault();
        productsAjaxPagination(jQuery(this));
    });
    jQuery('.elementor-widget-twbb_woocommerce-products .add_to_cart_button').on('click',function() {
        changeProductQuantity(this);
    });
    /*
    10web customization
    */
    jQuery(document).on('click', '.elementor-widget-twbb_woocommerce-products .twbb-product-quantity-change', function() {
        var $input = jQuery(this).parent().find('input');
        if ( jQuery(this).hasClass( 'twbb-minus-quantity' ) ) {
            if( (parseInt($input.val()) - 1) > 0 ) {
                $input.val(parseInt($input.val()) - 1);
            }
        } else {
            $input.val(parseInt($input.val()) + 1);
        }
        $input.change();
        jQuery('button[name=update_cart]').trigger('click');
        return false;
    });

    jQuery('.twbb-woocommerce-products-variations .variation-image').on('mouseenter', function() {
        var src = jQuery(this).attr('src');
        var product = jQuery(this).closest('.twbb-woocommerce-products-variations').parent();
        var product_link = product.attr('href');
        var attrs = {};
        jQuery.each( jQuery(this).attr('data-attrs_as_params').split('&'), function(dataKey, dataValue){
            if( dataValue ) {
                let key, val;
                key = dataValue.split('=')[0];
                val = dataValue.split('=')[1];
                attrs[key] = val;
            }
        })
        var new_link = add_params_to_product_link(product_link, attrs);
        product.attr('href', new_link);
        jQuery(this).closest('.twbb-woocommerce-products-variations').parent().find('>.variation-image').css('background-image', 'url(' + src + ')');
    });

    jQuery(document).on('click', '.add_to_cart_button', function(){
        jQuery(this).addClass('twbb-change-button-text').text(twbb.woocommerce.added);
        setTimeout(function(){
            jQuery('.add_to_cart_button.twbb-change-button-text').text(twbb.woocommerce.add_to_cart);
        },1000);})
    /*
      end customization
    */
})

function image_gallery_run() {
    var swiperInstance = null; // Store Swiper instance
    jQuery(document).on('mouseenter', ".woocommerce-loop-product__link .twbb-image-container", function() {
        const $slider = jQuery(this).closest(".woocommerce-loop-product__link").find('.product-gallery-slider');
        if( !$slider.length ) {
            return true;
        }
        let $firstImage = jQuery(this).find("img[data-image='main']");

        let imgWidth = $firstImage.innerWidth() || 270; // Default width
        let imgHeight = $firstImage.innerHeight() || 270; // Default height

        $slider.css({
            'width': imgWidth + 'px',
            'height': imgHeight + 'px'
        });

        let loop = false;
        if( jQuery(this).find(".product-gallery-slider .swiper-wrapper .swiper-slide").length > 1 ) {
            loop = true;
            jQuery(this).removeClass("hover-active hover-zoom-in");
        }
        let swiperOptions = {
            loop: loop,
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            watchOverflow: true,
        };
        // Initialize Swiper only if not already initialized
        const Swiper = elementorFrontend.utils.swiper;
        if (typeof Swiper === 'undefined') {
            return;
        }
        if (!swiperInstance)
        {
            new Swiper($slider[0], swiperOptions);
        }

        // Show the slider
        $slider.css('display', 'block');
    });

    jQuery(document).on('mouseleave', ".woocommerce-loop-product__link", function() {
        const $slider = jQuery(this).closest(".woocommerce-loop-product__link").find('.product-gallery-slider');
        // Hide the slider
        $slider.css('display', 'none');
    });
}

function image_gallery_second_image_run() {
    jQuery(document).on('mouseenter', ".woocommerce-loop-product__link .twbb-image-container", function() {
        const $secondImage = jQuery(this).closest(".woocommerce-loop-product__link").find('.product-gallery-second-image');
        if( !$secondImage.length ) {
            return true;
        }
        let $firstImage = jQuery(this).find("img[data-image='main']");

        let imgWidth = $firstImage.innerWidth() || 270; // Default width
        let imgHeight = $firstImage.innerHeight() || 270; // Default height

        $secondImage.css({
            'width': imgWidth + 'px',
            'height': imgHeight + 'px'
        });

        // Show the slider
        $secondImage.css('display', 'block');
    });

    jQuery(document).on('mouseleave', ".woocommerce-loop-product__link", function() {
        const $secondImage = jQuery(this).closest(".woocommerce-loop-product__link").find('.product-gallery-second-image');
        // Hide the slider
        $secondImage.css('display', 'none');
    });
}

function add_params_to_product_link( link, attrs) {
    let url, new_params, new_link;
    url = new URL(link).searchParams;
    Object.keys(attrs).forEach(function(key) {
        url.set(key, attrs[key]);
    });
    new_params = url.toString();
    new_link = link.split('?')[0] + '?' + new_params;
    return new_link;
}

function productsAjaxPagination(element) {
    const url = element.find('a').attr('href');
    const container = element.closest('.elementor-widget-twbb_woocommerce-products');
    const container_id = element.closest('.elementor-widget-twbb_woocommerce-products').data('id');
    jQuery.ajax({
        url: url,
        type:'GET',
        dataType: 'html',
        success: function(data){
            let parser = new DOMParser();
            const doc = parser.parseFromString(data, 'text/html');
            const new_page = jQuery(doc).find('.elementor-widget-twbb_woocommerce-products[data-id="' + container_id + '"]').html();
            container.html(new_page);
            jQuery('.twbb_woocommerce-products-ajax-paginate .page-numbers li').on('click', function(e) {
                e.preventDefault();
                productsAjaxPagination(jQuery(this));
            })
        }
    })
}

function changeProductQuantity(that) {
    let input_value = jQuery(that).closest('li.product').find('input.twbb-product-quantity-input').val();
    jQuery(that).attr('data-quantity',input_value);
}

jQuery( window ).on( 'elementor/frontend/init', function() {
    class PurchaseSummaryHandler extends TWBB_WooCommerce_Base {
        getDefaultSettings() {
            return {
                selectors: {
                    container: '.elementor-widget-twbb_woocommerce-purchase-summary',
                    address: 'address',
                    purchasenote: '.product-purchase-note'
                }
            };
        }
        getDefaultElements() {
            const selectors = this.getSettings('selectors');
            return {
                $container: this.$element.find(selectors.container),
                $address: this.$element.find(selectors.address),
                $purchasenote: this.$element.find(selectors.purchasenote)
            };
        }
        onElementChange(propertyName) {
            // When the 'General Text' Typography, 'Section' Padding, or Border Width is changed, the height of the boxes need to update as well.
            const properties = ['general_text_typography', 'sections_padding', 'sections_border_width'];
            for (const property of properties) {
                if (propertyName.startsWith(property)) {
                    this.equalizeElementHeight(this.elements.$address);
                }
            }

            // Remove padding on the purchase notes.
            if (propertyName.startsWith('order_details_rows_gap')) {
                this.removePaddingBetweenPurchaseNote(this.elements.$purchasenote);
            }
        }
        applyButtonsHoverAnimation() {
            const elementSettings = this.getElementSettings();
            if (elementSettings.order_details_button_hover_animation) {
                this.$element.find('.order-again .button, td .button').addClass('elementor-animation-' + elementSettings.order_details_button_hover_animation);
            }
        }
        onInit() {
            super.onInit(...arguments);
            this.equalizeElementHeight(this.elements.$address);
            this.removePaddingBetweenPurchaseNote(this.elements.$purchasenote);
            this.applyButtonsHoverAnimation();
        }
    }

    elementorFrontend.hooks.addAction( 'frontend/element_ready/twbb_woocommerce-purchase-summary.default', function ( $scope ) {
        new PurchaseSummaryHandler( { $element: $scope } );
    });
})