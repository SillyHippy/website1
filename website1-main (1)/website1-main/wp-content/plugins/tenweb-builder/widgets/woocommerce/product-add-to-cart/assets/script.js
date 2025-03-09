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