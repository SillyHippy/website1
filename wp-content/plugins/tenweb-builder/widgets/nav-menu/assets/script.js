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
