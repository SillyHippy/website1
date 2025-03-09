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