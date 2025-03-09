class TWBCWidgetSettings {
    constructor(container) {
        this.container = container; // container is an Elementor object, which contains info about widgets
        this.settings = container.settings.attributes;
        this.controls = container.settings.controls;
        this.defaults = container.settings.defaults;
        this.globals = (this.settings["__globals__"]) ? this.settings["__globals__"] : {};
        this.isRepeater = this.container.type === "repeater";
    }

    getModifiedSettings() {
        let modifiedSettings = {};
        for (let settingName in this.settings) {

            let defaultValue = this.defaults[settingName];
            let currentValue = this.settings[settingName];

            if(this.isRepeatedFieldAsString(settingName)){
                console.log("repeated field as string", settingName);
                continue;
            }

            if (typeof defaultValue === "undefined") {
                continue;
            }

            if(this.controls[settingName]["type"] === "repeater"){
                continue;
            }

            if (settingName.includes("letter_spacing") && currentValue === "normal") {
                continue;
            }

            if (this.hasDynamicValue(settingName) === false && !this.isModified(defaultValue, currentValue)) {
                continue;
            }

            modifiedSettings[settingName] = this.settings[settingName];
        }
        return modifiedSettings;
    }

    isModified(currentValue, defaultValue) {
        // if types are different, then setting value is modified
        if (typeof currentValue !== typeof defaultValue) {
            return true;
        }

        // if value is not object (it is string, boolean, number)
        if (typeof currentValue !== "object") {
            return currentValue != defaultValue;
        }

        return !this.equalObjects(currentValue, defaultValue);
    }

    hasDynamicValue(settingName){
        // if setting has dynamic value, it should be considered as modified because we don't have default value in the backend
        let dynamicSettings = {
            "twbb-nav-menu": ["menu"]
        }

        let type = this.getType();
        return (!!dynamicSettings[type] && dynamicSettings[type].includes(settingName))
    }

    /**
     * Compares 2 objects. If both objects have the same keys and values, then returns true, otherwise returns false
     *
     * */
    equalObjects(obj1, obj2) {
        if (obj1 === obj2) {
            return true;
        }

        if (typeof obj1 !== "object" || typeof obj2 !== "object" || obj1 === null || obj2 === null) {
            return false;
        }

        const keys1 = Object.keys(obj1);
        const keys2 = Object.keys(obj2);

        if (keys1.length !== keys2.length) {
            return false
        }

        for (const key of keys1) {
            if (!keys2.includes(key) || !this.equalObjects(obj1[key], obj2[key])) {
                return false
            }
        }

        return true;
    }

    getGlobals() {
        return this.globals;
    }

    getType() {
        if(this.isRepeater){
            let parentWidgetType = this.container.parent.parent.model.attributes.widgetType;
            return parentWidgetType + "_repeater_" + this.container.parent.id;
        }
        return this.container.type === "container" ? "container" : this.settings.widgetType;
    }

    getID() {
        return this.container.id;
    }

    isRepeatedFieldAsString(settingName){
        if(!this.container["repeaters"]){
            return false;
        }

        return !!this.container["repeaters"][settingName];
    }

    getContainerDimensions() {
        let iframeContent = jQuery('#elementor-preview-iframe').contents();
        let widget = iframeContent.find('.elementor-element-' + this.getID());
        return {"width": widget.width(), "height": widget.height()};
    }
}