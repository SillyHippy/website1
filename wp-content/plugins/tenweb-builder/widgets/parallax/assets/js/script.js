jQuery( window ).on( 'elementor/frontend/init', function() {
	var ParallaxHandler = elementorModules.frontend.handlers.Base.extend({
		defoult_settings:{
			"background_background": "classic",
			"tenweb_enable_parallax_efects": "no",
			"tenweb_vertical_scroll_efects-direction": "down",
			"tenweb_vertical_scroll_efects-speed": {"unit":"px","size":4.5,"sizes":[]},
			"tenweb_vertical_scroll_efects": "no",
			"tenweb_horizontal_scroll_efects": "no",
			"tenweb_transparency_efects": "no",
			"tenweb_blur_efects": "no",
			"tenweb_scale_efects": "no",
			"tenweb_horizontal_scroll_efects-direction": "left",
			"tenweb_horizontal_scroll_efects-speed": {"unit":"px","size":4,"sizes":[]},
			"tenweb_transparency_efects-direction": "in",
			"tenweb_transparency_efects-speed": {"unit":"px","size":4,"sizes":[]},
			"tenweb_blur_efects-direction": "in",
			"tenweb_blur_efects-speed": {"unit":"px","size":4,"sizes":[]},
			"tenweb_scale_efects-direction": "in",
			"tenweb_scale_efects-speed": {"unit":"px","size":4,"sizes":[]},
			"tenweb_parallax_on": ["desktop","tablet","mobile"]
		},
		current_settings:{
		},
		curParalax:{

		},
		elementBgImg:'',
		is_active:false,
		updateSettings: function(settings){
			var self = this;
			for(const [key, value] of Object.entries(self.defoult_settings)){
				if(typeof settings[key] != 'undefined')
					self.current_settings[key] = settings[key];
				else
					self.current_settings[key] = self.defoult_settings[key];
			}
		},
		isSectionParallax:function(sectionSettings){
			if (sectionSettings.hasOwnProperty('tenweb_enable_parallax_efects')){
				return true;
			}
			return false;
		},
		activate: function activate() {
			var self = this;
			var curElem = self.$element[0];
			if(self.is_active){
				self.deactivate();
			}
			self.curParalax = new tenwebParallax(curElem,{
				vertical_scroll:{
					active: self.current_settings['tenweb_vertical_scroll_efects'],
					speed: self.current_settings['tenweb_vertical_scroll_efects-speed']['size'],
					direction: self.current_settings['tenweb_vertical_scroll_efects-direction'],
				},
				horizontal_scroll:{
					active: self.current_settings['tenweb_horizontal_scroll_efects'],
					speed: self.current_settings['tenweb_horizontal_scroll_efects-speed']['size'],
					direction: self.current_settings['tenweb_horizontal_scroll_efects-direction'],
				},
				transparency:{
					active: self.current_settings['tenweb_transparency_efects'],
					speed: self.current_settings['tenweb_transparency_efects-speed']['size'],
					direction: self.current_settings['tenweb_transparency_efects-direction'],
				},
				blur:{
					active: self.current_settings['tenweb_blur_efects'],
					speed: self.current_settings['tenweb_blur_efects-speed']['size'],
					direction: self.current_settings['tenweb_blur_efects-direction'],
				},
				scale:{
					active:self.current_settings['tenweb_scale_efects'],
					speed: self.current_settings['tenweb_scale_efects-speed']['size'],
					direction: self.current_settings['tenweb_scale_efects-direction'],
				}
			}).start();
			self.is_active = true;
		},
		deactivate: function deactivate() {
			var self = this,
				curElem = self.$element[0];
			if(typeof  self.curParalax.destroy =="function")
				self.curParalax.destroy();
			self.is_active = false;
		},

		run: function run(refresh) {
			var sectionSettings = this.getElementSettings();
			if(this.isSectionParallax(sectionSettings)){
				this.updateSettings(sectionSettings);
				if(this.current_settings['tenweb_enable_parallax_efects'] === 'yes' && this.current_settings['background_background'] === 'classic') {
					var currentDeviceMode = elementorFrontend.getCurrentDeviceMode(),
						activedDevices = this.getElementSettings('tenweb_parallax_on');

					if (-1 !== activedDevices.indexOf(currentDeviceMode)) {
						this.activate();
					} else {
						this.deactivate();
					}
				}else{
					this.deactivate();
				}
			}else{
				this.deactivate();
			}
		},

		reactivate: function reactivate() {
			this.deactivate();
			this.activate();
		},

		onElementChange: function onElementChange(settingKey) {
			this.run();
		},

		onInit: function onInit() {
			var self=this;
			elementorModules.frontend.handlers.Base.prototype.onInit.apply(this, arguments);
			this.run();
		},

		onDestroy: function onDestroy() {
			elementorModules.frontend.handlers.Base.prototype.onDestroy.apply(this, arguments);
			this.deactivate();
		},
	});

	elementorFrontend.hooks.addAction( 'frontend/element_ready/section', function ( $scope ) {
		new ParallaxHandler({ $element: $scope });
	});
});