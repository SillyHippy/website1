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