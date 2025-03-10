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
