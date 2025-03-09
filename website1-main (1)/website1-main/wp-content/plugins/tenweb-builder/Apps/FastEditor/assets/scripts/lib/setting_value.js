class TWBB_SETTING_VALUE {

    static DEVICE_MODES = ['desktop', 'tablet', 'mobile']; // order is important

    static WIDGETS_HARDCODED_VALUES = {
        "progress": {
            "bar_inline_color": "#FFFFFF"
        },
        "container": {
            "background_color": "#00000000"
        },
        "image-carousel": {
            "slides_to_show": 3
        },
        "icon-box": {
            "text_align": "center"
        },
        "image-box": {
            "text_align": "center"
        },
        "image": {
            "align": "center"
        },
        "google_maps": {
            "height": 300
        },
        "text-path": {
            "align": "left"
        }
    }

    static WIDGETS_VALUES_FROM_STYLE = {
        "button": {
            "button_text_color": {
                "selector": ".elementor-button-text",
                "style_name": "color"
            }
        }
    }

    constructor(setting_name, model, container, device_mode) {
        this.setting_name = setting_name
        this.model = model;
        this.container = container;
        this.view = this.container.view;
        this.device_mode = device_mode;
        this.controls = this.model.attributes.settings.controls
        this.control_type = (this.controls[this.setting_name]) ? this.controls[this.setting_name]['type'] : null;
        this.widget_type = (this.model.attributes['elType'] === "container") ? "container" : this.model.attributes['widgetType'];
        this.group_setting_name = null;
        if (this.controls[this.setting_name]['groupPrefix'] && this.controls[this.setting_name]['groupType']) {
            this.group_setting_name = this.controls[this.setting_name]['groupPrefix'] + this.controls[this.setting_name]['groupType'];
        }

        this.setting_type = null;
    }

    get_setting_value() {
        // if setting value is applied to widget (e.g. custom color to heading widget)
        let value = this.get_applied_setting_value_responsive();
        let value_from_widget = value;
        if (!this.is_empty_value(value)) {
            this.setting_type = "custom";
            return value;
        }

        // if global styles is applied to widget (e.g. accent color to heading widget)
        value = this.get_applied_global_value(this.setting_name);
        if (!this.is_empty_value(value)) {
            this.setting_type = "global";
            return value;
        }

        // if no style is applied and global style is inherited (e.g. primary color for heading widget)
        value = this.get_global_default_value(this.setting_name);
        if (!this.is_empty_value(value)) {
            this.setting_type = "global";
            return value;
        }

        value = this.get_global_default_from_elementor_config();
        if (!this.is_empty_value(value)) {
            this.setting_type = "from_elementor_config";
            return value;
        }

        value = this.get_from_placeholders();
        if (!this.is_empty_value(value)) {
            this.setting_type = "from_placeholder";
            return value;
        }

        value = this.get_value_from_style();
        if (value !== null) {
            this.setting_type = "from_style";
            return value;
        }

        value = this.get_hardcoded_value();
        if (value !== null) {
            this.setting_type = "from_hardcoded";
            return value;
        }

        return value_from_widget; // to keep value structure
    }

    get_applied_setting_value_responsive() {

        let index = TWBB_SETTING_VALUE.DEVICE_MODES.indexOf(this.device_mode);
        // if device mode is desktop or unknown device mode
        if (index === 0 || index === -1) {
            return this.get_applied_setting_value(this.setting_name);
        }


        for (let i = index; i > 0; i--) {
            let setting_name = this.setting_name + "_" + TWBB_SETTING_VALUE.DEVICE_MODES[i];

            if(!this.controls[setting_name]){
                continue;
            }

            let value = this.get_applied_setting_value(setting_name);
            if(!this.is_empty_value(value)){
                return value;
            }
        }

        return this.get_applied_setting_value(this.setting_name);
    }

    get_applied_setting_value(setting_name) {
        return this.model.getSetting(setting_name);
    }

    /**
     * Get value from applied global styles. e.g. when accent color is applied for heading widget.
     * The part of this code is copied from elementor/assests/dev/js/editor/container/container.js
     * */
    get_applied_global_value(setting_name) {
        let global_key = this.container.getGlobalKey(setting_name);
        let global_args = window.top.$e.data.commandExtractArgs(global_key);
        if (global_args['command']) {
            let data = window.top.$e.data.getCache(window.top.$e.components.get('globals'), global_args.command, global_args.args.query);
            if (data) {
                return data['value'];
            }
        }

        if (this.group_setting_name && setting_name !== this.group_setting_name) {
            let value = this.get_applied_global_value(this.group_setting_name);
            if (value && value[this.setting_name]) {
                return value[this.setting_name];
            }
        }

        return null;
    }

    get_global_default_value(setting_name) {
        if (this.container.isGlobalApplied(setting_name)) {
            return this.container.getGlobalDefault(setting_name);
        }

        if (this.group_setting_name && setting_name !== this.group_setting_name) {
            let value = this.get_global_default_value(this.group_setting_name);
            if (value && value[this.setting_name]) {
                return value[this.setting_name];
            }
        }

        return null;
    }

    get_global_default_from_elementor_config() {
        let widget_type = this.model.attributes['widgetType'];
        if (!window.top.ElementorConfig.widgets[widget_type]) {
            return null;
        }
        let control = window.top.ElementorConfig.widgets[widget_type]['controls'][this.group_setting_name];

        let global_id = null;
        if (control && control['global'] && control['global']['active'] === true) {
            global_id = control['global']['default'];
        }

        if (!global_id) {
            return null;
        }

        let global_args = window.top.$e.data.commandExtractArgs(global_id);
        if (global_args['command']) {
            let data = window.top.$e.data.getCache(window.top.$e.components.get('globals'), global_args.command, global_args.args.query);
            if (data) {
                return data['value'][this.setting_name];
            }
        }

        return null;
    }

    get_from_placeholders() {
        return this.controls[this.setting_name]['placeholder'];
    }


    get_value_from_style(){
        if (!TWBB_SETTING_VALUE.WIDGETS_VALUES_FROM_STYLE[this.widget_type]) {
            return null;
        }

        if (!TWBB_SETTING_VALUE.WIDGETS_VALUES_FROM_STYLE[this.widget_type][this.setting_name]) {
            return null
        }

        let data = TWBB_SETTING_VALUE.WIDGETS_VALUES_FROM_STYLE[this.widget_type][this.setting_name];

        let $el = this.view.$el.find(data['selector']);

        if($el.length === 0){
            return null;
        }

        return  $el.css(data['style_name']);
    }
    get_hardcoded_value() {

        if (!TWBB_SETTING_VALUE.WIDGETS_HARDCODED_VALUES[this.widget_type]) {
            return null;
        }

        if (!TWBB_SETTING_VALUE.WIDGETS_HARDCODED_VALUES[this.widget_type][this.setting_name]) {
            return null
        }

        return TWBB_SETTING_VALUE.WIDGETS_HARDCODED_VALUES[this.widget_type][this.setting_name];
    }

    is_empty_value(value) {
        if (value === null || typeof value === "undefined" || value === "") {
            return true;
        }

        if (this.control_type === "slider") {
            return value.size == "";
        }

        return value === null;
    }


}