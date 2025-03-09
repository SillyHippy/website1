class TWBCUtils {
    static getElementorKit(data) {
        let kit = {
            "colors": {},
            "typographies": {},
        };

        if (data.colors) {
            Object.values(data.colors).forEach(function (item) {
                kit["colors"][item.id] = item.value;
            });
        } else {
            throw new Error("No colors in kit");
        }

        if (data.typography) {
            Object.values(data.typography).forEach(function (item) {
                if (item.value["typography_letter_spacing"] && item.value["typography_letter_spacing"] === "normal") {
                    delete item.value["typography_letter_spacing"];
                }

                if (item.value["typography_word_spacing"] && item.value["typography_word_spacing"] === "normal") {
                    delete item.value["typography_word_spacing"];
                }

                kit['typographies'][item.id] = item.value;
            });
        } else {
            throw new Error("No typographies in kit");
        }

        return kit;
    }

    static getDeviceMode() {
        return elementor.channels.deviceMode.request('currentMode');
    }

    static findContainerById(id){
        return window.$e.components.get('document').utils.findContainerById(id);
    }

    static isClass(variable) {
        return typeof variable === "function" && /^\s*class\s+/.test(variable.toString());
    }

    // in prod should be used FastEditorHelper class, from builder plugin
    static setSetting(container, settings, options = '') {
        if (options == '') {
            options = {
                render: true,
            };
        }
        window.parent.$e.commands.run('document/elements/settings', {
            "container": container,
            "options": options,
            settings: settings
        });
    }

    static disableGlobals(container, settings) {
        return parent.window.$e.commands.run('document/globals/disable', {
            "container": container,
            "settings": settings,
            "options": {
                restore: true,
            },
        });
    }

    static getChildrenContainers(element) {
        if (element.type === "widget" && element.settings.attributes.widgetType === "twbb_form") {
            return element["repeaters"]["form_fields"]["children"];
        }

        return element.children;
    }
}