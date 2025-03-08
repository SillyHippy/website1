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