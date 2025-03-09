jQuery( window ).on( 'elementor/frontend/init', function() {

	var StickyHandler = elementorModules.frontend.handlers.Base.extend({
		currentConfig: {},
		debouncedReactivate: null,

		bindEvents: function bindEvents() {
			elementorFrontend.addListenerOnce(this.getUniqueHandlerID() + 'sticky', 'resize', this.reactivateOnResize);
		},
		unbindEvents: function unbindEvents() {
			elementorFrontend.removeListeners(this.getUniqueHandlerID() + 'sticky', 'resize', this.reactivateOnResize);
		},
		isStickyInstanceActive: function isStickyInstanceActive() {
			return undefined !== this.$element.data('sticky');
		},
		/**
		 * Get the current active setting value for a responsive control.
		 *
		 * @param {string} setting
		 * @return {any} - Setting value.
		 */
		getResponsiveSetting: function getResponsiveSetting(setting) {
			const elementSettings = this.getElementSettings();
			return elementorFrontend.getCurrentDeviceSetting(elementSettings, setting);
		},
		/**
		 * Return an array of settings names for responsive control (e.g. `settings`, `setting_tablet`, `setting_mobile` ).
		 *
		 * @param {string} setting
		 * @return {string[]} - List of settings.
		 */
		getResponsiveSettingList: function getResponsiveSettingList(setting) {
			const breakpoints = Object.keys(elementorFrontend.config.responsive.activeBreakpoints);
			return ['', ...breakpoints].map(suffix => {
				return suffix ? `${setting}_${suffix}` : setting;
			});
		},
		getConfig: function getConfig() {
			const elementSettings = this.getElementSettings(),
				stickyOptions = {
					to: elementSettings.tenweb_sticky,
					offset: this.getResponsiveSetting('tenweb_sticky_offset'),
					effectsOffset: this.getResponsiveSetting('tenweb_sticky_effects_offset'),
					classes: {
						sticky: 'elementor-sticky',
						stickyActive: 'elementor-sticky--active elementor-section--handles-inside',
						stickyEffects: 'elementor-sticky--effects',
						spacer: 'elementor-sticky__spacer'
					},
					isRTL: elementorFrontend.config.is_rtl,
					// In edit mode, since the preview is an iframe, the scrollbar is on the left. The scrollbar width is
					// compensated for in this case.
					handleScrollbarWidth: elementorFrontend.isEditMode()
				},
				$wpAdminBar = elementorFrontend.elements.$wpAdminBar,
				isParentContainer = this.isContainerElement(this.$element[0]) && !this.isContainerElement(this.$element[0].parentElement);
			if ($wpAdminBar.length && 'top' === elementSettings.tenweb_sticky && 'fixed' === $wpAdminBar.css('position')) {
				stickyOptions.offset += $wpAdminBar.height();
			}

			// The `stickyOptions.parent` value should only be applied to inner elements, and not to top level containers.
			if (elementSettings.tenweb_sticky_parent && !isParentContainer) {
				// TODO: The e-container classes should be removed in the next update.
				stickyOptions.parent = '.e-container, .e-container__inner, .e-con, .e-con-inner, .elementor-widget-wrap';
			}
			return stickyOptions;
		},
		activate: function activate() {
			this.currentConfig = this.getConfig();
			this.$element.sticky(this.currentConfig);
		},
		deactivate: function deactivate() {
			if (!this.isStickyInstanceActive()) {
				return;
			}

			this.$element.sticky('destroy');
		},
		run: function run(refresh) {
			if (!this.getElementSettings('tenweb_sticky')) {
				this.deactivate();
				return;
			}

			var currentDeviceMode = elementorFrontend.getCurrentDeviceMode(),
				activeDevices = this.getElementSettings('tenweb_sticky_on');

			if (-1 !== activeDevices.indexOf(currentDeviceMode)) {
				if (true === refresh) {
					this.reactivate();
				} else if (!this.isStickyInstanceActive()) {
					this.activate();
				}
			} else {
				this.deactivate();
			}
		},
		/**
		 * Reactivate the sticky instance on resize only if the new sticky config is different from the current active one,
		 * in order to avoid re-initializing the sticky when not needed, and avoid layout shifts.
		 * The config can be different between devices, so this need to be checked on each screen resize to make sure that
		 * the current screen size uses the appropriate Sticky config.
		 *
		 * @return {void}
		 */
		reactivateOnResize: function reactivateOnResize() {
			clearTimeout(this.debouncedReactivate);
			this.debouncedReactivate = setTimeout(() => {
				const config = this.getConfig(),
					isDifferentConfig = JSON.stringify(config) !== JSON.stringify(this.currentConfig);

				if (isDifferentConfig) {
					this.run(true);
				}
			}, 300);
		},
		reactivate: function reactivate() {
			this.deactivate();
			this.activate();
		},
		onElementChange: function onElementChange(settingKey) {
			if (-1 !== ['tenweb_sticky', 'tenweb_sticky_on'].indexOf(settingKey)) {
				this.run(true);
			}
			const settings = [...this.getResponsiveSettingList('tenweb_sticky_offset'), ...this.getResponsiveSettingList('tenweb_sticky_effects_offset'), 'tenweb_sticky_parent'];

			if (-1 !== settings.indexOf(settingKey)) {
				this.reactivate();
			}
		},
		/**
		 * Listen to device mode changes and re-initialize the sticky.
		 *
		 * @return {void}
		 */
		onDeviceModeChange: function onDeviceModeChange() {
			// Wait for the call stack to be empty.
			// The `run` function requests the current device mode from the CSS so it's not ready immediately.
			// (need to wait for the `deviceMode` event to change the CSS).
			// See `elementorFrontend.getCurrentDeviceMode()` for reference.
			setTimeout(() => this.run(true));
		},
		onInit: function onInit() {
			elementorModules.frontend.handlers.Base.prototype.onInit.apply(this, arguments);
			if (elementorFrontend.isEditMode()) {
				elementor.listenTo(elementor.channels.deviceMode, 'change', () => this.onDeviceModeChange());
			}
			this.run();
		},
		onDestroy: function onDestroy() {
			elementorModules.frontend.handlers.Base.prototype.onDestroy.apply(this, arguments);
			this.deactivate();
		},
		/**
		 *
		 * @param {HTMLElement|null|undefined} element
		 * @return {boolean} Is the passed element a container.
		 */
		isContainerElement(element) {
			const containerClasses = [
				// TODO: The e-container classes should be removed in the next update.
				'e-container', 'e-container__inner', 'e-con', 'e-con-inner'];
			return containerClasses.some(containerClass => {
				return element?.classList.contains(containerClass);
			});
		}
	});


	elementorFrontend.hooks.addAction( 'frontend/element_ready/section', function ( $scope ) {
		new StickyHandler({ $element: $scope });
	});
	elementorFrontend.hooks.addAction( 'frontend/element_ready/container', function ( $scope ) {
		new StickyHandler({ $element: $scope });
	});
	elementorFrontend.hooks.addAction( 'frontend/element_ready/widget', function ( $scope ) {
		new StickyHandler({ $element: $scope });
	});
});
