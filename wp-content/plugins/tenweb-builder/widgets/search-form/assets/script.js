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






