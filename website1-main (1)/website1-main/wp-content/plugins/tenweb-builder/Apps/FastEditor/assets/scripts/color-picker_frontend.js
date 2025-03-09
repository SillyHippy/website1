class TWBB_COLORPICKER_TOOL extends FE_TOOL_FRONTEND {

    constructor() {
        super();
        this.tool_control = [];
        this.tool_values = {};
        /* keeping pickr class object */
        this.pickr = '';
        /* Need for using in multi control case */
        this.active_control = '';
        this.active_control_color = '#000000';
        this.multi_controls = false;
    }

    open_editor_command() {
        this.setControls();
    }


    registerEvents() {
        let self = this;
        jQuery(document).on("click", ".twbb-color_picker-tool", function (event) {
            FE_TOOL_FRONTEND.closeOtherTools(TWBB_COLORPICKER_TOOL);

            if (!self.tool_control.length) {
                self.setControls();
            }

            let tools_container = self.getToolsContainer();

            /* Case when multicontrol and need to open menu and not pickr */
            if (jQuery(this).hasClass("twbb-color_picker-open-menu")) {

                if (tools_container.find(".twbb-color_picker-tool-content").is(":visible")) {
                    tools_container.find(".twbb-color_picker-tool-content").hide();
                    jQuery(this).closest('.twbb-color_picker-tool-container').removeClass('twbb_color_popup_active');
                    return;
                }

                self.positionElement(
                    tools_container.find(".twbb-color_picker-tool"),
                    tools_container.find(".twbb-color_picker-tool-content"), 212, 135, -10, 0, 28 );
                tools_container.find(".twbb-color_picker-tool-content").show();
                jQuery(this).closest('.twbb-color_picker-tool-container').addClass('twbb_color_popup_active');

                tools_container.find('.twbb-color_picker-open').each(function (index) {
                    /* In case of pickr not inited yet (if init already worked wbls-pickr-run class and content already replaced to pickr content)*/
                    if (jQuery(this).find('.wbls-pickr-run').length) {
                        jQuery(this).find('.wbls-pickr-run').addClass('twbb-color_picker-tool_' + self.tool_control[index] + '_' +  self.widgetID);
                        self.initPickr(self.tool_control[index], 0);
                    }
                });
            } else {
                self.onToolClick(jQuery(this));
            }
        });

        jQuery(document).on('click', 'body', function (e) {
            if (e.target.closest(".twbb-color_picker-tool-container") === null) {
                self.closeTool();
            }
        });

        jQuery(window).scroll(function () {
            if (self.pickr) {
                self.pickr.hide();
            }
        });
    }

    onToolClick(that) {
        let self = this;

        this.setActiveToolData(that.closest('.twbb-color_picker-open'))

        //for containers open default classic unit
        if (jQuery(that).closest('.twbb-fast-editor-tools-container').parent().attr('data-element_type') === 'container') {
            window.parent.jQuery('.elementor-component-tab.elementor-panel-navigation-tab.elementor-tab-control-style').trigger('click');
            window.parent.jQuery('.elementor-control-background_background .tooltip-target[data-tooltip="Classic"]').trigger('click');
        }

        if(!that.hasClass('twbb-color_picker-tool')){
            that = that.find('.twbb-color_picker-tool');
        }

        jQuery(that).addClass('twbb-color_picker-tool_' + this.tool_control[0] + "_" + this.widgetID);
        this.initPickr(self.tool_control[0], 1);
    }

    initPickr(tool_control, show) {
        let self = this;
        let default_color = self.tool_values[tool_control];
        if (!default_color) {
            default_color = '#000000';
        }

        let selector = '.twbb-color_picker-tool_' + tool_control + "_" + this.widgetID;
        this.pickr = new Pickr({
            el: selector,
            theme: 'monolith',
            useAsButton: false,
            default: default_color,
            defaultRepresentation: 'HEX',
            closeWithKey: 'Escape',
            position: 'bottom-middle',
            autoReposition: 0,
            inline: true,
            adjustableNumbers: true,
            components: {
                palette: true,
                opacity: true, // Display opacity slider
                hue: true,     // Display hue slider
                interaction: {
                    hex: true,  // Display 'input/output format as hex' button  (hexadecimal representation of the rgba value)
                    rgba: true, // Display 'input/output format as rgba' button (red green blue and alpha)
                    hsla: true, // Display 'input/output format as hsla' button (hue saturation lightness and alpha)
                    input: true, // Display input/output textbox which shows the selected color value.
                },
            },
        });


        this.pickr.on('init', instance => {
            if (show) {
                instance.show();
            }
        }).on('show', (color, instance) => {
            self.active_control = jQuery(document).find(instance._root.button).closest(".twbb-color_picker-open").attr('data-control');

            let leftPosition = -10;
            let rightPosition = 0;
            let top = 28;

            let tools_container = self.getToolsContainer();

            /* In case of color picker in the dropdown */
            if( tools_container.find(".twbb-color_picker-tool-content").length ) {
                let windowWidth = jQuery(window).width();
                if ( windowWidth < 500 ) {
                    leftPosition = 45;
                    rightPosition = -50;
                    top = 40;
                } else {
                    leftPosition = 45;
                    rightPosition = 35;
                }
            } else {
                rightPosition = 0;
            }
            self.positionElement(tools_container.find(".twbb-color_picker-open"),
                jQuery('.pcr-app.visible'), 270, 171, leftPosition, rightPosition, top);


            if (self.active_control) {
                self.active_control_color = self.tool_values[self.active_control];
            } else {
                self.active_control = jQuery(document).find(instance._root.button).closest(".twbb-color_picker-open").attr('data-control');
                if (self.active_control) {
                    self.active_control_color = self.getAppliedSettingValue(self.active_control);
                }
            }
            instance.setColor(self.active_control_color, false);
            self.dataPush( jQuery(document).find(instance._root.button).closest(".twbb-color_picker-open") );
        }).on('change', (color, source, instance) => {
            if (self.tool_values[self.active_control] != color.toHEXA().toString()) {
                self.tool_values[self.active_control] = color.toHEXA().toString();
                self.changeColorSetting(self.container, {[self.active_control]: self.tool_values[self.active_control],});
            }
            /* Need change only multicontrol as in single case only icon visible */
            if (this.multi_controls) {
                instance.setColor(self.tool_values[self.active_control], false);
            }
        });
    }



    changeColorSetting(container, settings) {
        let options = {
            external: true,
            render: false,
        };

        this.changeWidgetSetting(this.active_control, settings, options, container,  {[this.active_control]: ''})
    }

    /* Collect all controls and set colors for every control data for using in multicontrol */
    setControls() {
        let self = this;

        let tools_container = this.getToolsContainer();

        /* Empty array before set if command run again */
        self.tool_control = [];
        let color_pickers = tools_container.find('.twbb-color_picker-open');
        if (jQuery(self.view.$el).attr('data-element_type') === 'container') {
            color_pickers = tools_container.find('.twbb-color_picker-open');
        }

        jQuery(color_pickers).each(function () {
            let control = jQuery(this).attr('data-control');
            self.tool_control.push(control);
            if (control) {
                self.tool_values[control] = self.getAppliedSettingValue(control);
            }
        });

        if (self.tool_control.length > 1) {
            this.multi_controls = true;
        }
    }

    positionElement(el, dropdownCont, width, height, leftPosition, rightPosition, top = undefined) {

        let windowWidth = jQuery(window).width();
        if (typeof el[0] !== undefined ) {
            let elLeft = el[0].getBoundingClientRect().left;
            if( (windowWidth - elLeft) < width ) {
                if ( top !== undefined ) {
                    dropdownCont.css({'right': rightPosition, 'left': 'unset', 'top' : top });
                    if ( windowWidth < 500 ) {
                        let mobile_position = width - elLeft;
                        dropdownCont.css({'right': - mobile_position, 'left': 'unset', 'top' : top });
                    }
                }
            } else {
                dropdownCont.css({'left': leftPosition, 'right': 'unset'});
            }
        }
    }

    closeTool(container = null) {
        if(container === null){
            container = this.getToolsContainer();
        }

        if(container.length === 0){
            return false;
        }

        container.find(".twbb-color_picker-tool-content").hide();
        container.find('.twbb-color_picker-tool-container').removeClass('twbb_color_popup_active');
        return true;
    }
}

let color_picker_tool;
jQuery(document).on('ready', function () {
    color_picker_tool = new TWBB_COLORPICKER_TOOL();
    window['color_picker_tool'] = color_picker_tool;
    color_picker_tool.init();
});


