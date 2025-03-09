class FastEditorHelper {
    static setSetting(container, settings, options = '') {
        if ( options == '' )  {
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

    static disableGlobals(container, globalValue, setting) {
        window.parent.$e.commands.run('document/globals/unlink', {
            "container": container,
            "globalValue": globalValue,
            "options": {
                external: true,
            },
            setting: setting
        });
    }
}