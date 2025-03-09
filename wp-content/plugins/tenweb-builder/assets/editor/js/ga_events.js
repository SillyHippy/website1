jQuery(document).on('click','#elementor-editor-wrapper-v2 .MuiGrid-root:nth-child(2) span button',function(){
    analyticsDataPush ( 'Page Settings', 'Page Settings' );
});

jQuery(document).on('click','#elementor-editor-wrapper-v2 .MuiGrid-root:nth-child(3) > div:eq(0) span:eq(0) > button', function() {
    analyticsDataPush ( 'Finder', 'Finder' );
});

jQuery(document).on('click','#elementor-editor-wrapper-v2 .MuiGrid-root:nth-child(3) > div:eq(0) span:eq(2) > button', function() {
    analyticsDataPush ( 'Preview Changes', 'Preview Changes' );
});

jQuery(document).on('click', '#elementor-editor-wrapper-v2 .MuiGrid-root:nth-child(3) > div:eq(1) > button', function() {
    analyticsDataPush ( 'Save & Publish', 'Save Events' );
});

jQuery(document).on('click','#document-save-options .MuiPaper-root .MuiList-root > div:eq(0)', function() {
    analyticsDataPush ( 'Save Draft', 'Save Events' );
});

jQuery(document).on('click','#document-save-options .MuiPaper-root .MuiList-root > div:eq(1)', function() {
    analyticsDataPush ( 'Save as Template', 'Save Events' );
});

jQuery(document).on('click','#elementor-editor-wrapper-v2 .MuiGrid-root:nth-child(1) .eui-stack:nth-child(3) span.eui-box:nth-child(1) button',function(){
    analyticsDataPush ( 'Add Element', 'Add Element' );
});
jQuery(document).on('click','#elementor-editor-wrapper-v2 .MuiGrid-root:nth-child(1) .eui-stack:nth-child(3) span.eui-box:nth-child(2) button',function(){
    analyticsDataPush ( 'Site Settings', 'Site Settings' );
});

jQuery(document).on('click','#elementor-editor-wrapper-v2 .MuiGrid-root:nth-child(1) .eui-stack:nth-child(3) span.eui-box:nth-child(3) button',function(){
    analyticsDataPush ( 'Structure', 'Structure' );
});

jQuery(document).on('click','#elementor-editor-wrapper-v2 .MuiGrid-root:nth-child(2) .eui-stack>span.eui-box button',function(){
    analyticsDataPush ( 'Page Settings', 'Page Settings' );
});

jQuery(document).on('click','.twbb-topbar-navigation-container .twbb-add-blank-page-button',function() {
    analyticsDataPush ( 'Add page - Blank', 'Editor Action', 'Left menu' );
});

jQuery(document).on('click','.twbb-topbar-navigation-container .twbb-add-new-ai-page-button',function() {
    analyticsDataPush ( 'Add page - AI', 'Editor Action', 'Left menu' );
});

jQuery(document).on('click','.twbb-topbar-structure-container .twbb-add-blank-page-button',function() {
    analyticsDataPush ( 'Add page - Blank', 'Editor Action', 'Site Structure menu' );
});

jQuery(document).on('click','.twbb-topbar-structure-container .twbb-add-new-ai-page-button',function() {
    analyticsDataPush ( 'Add page - AI', 'Editor Action', 'Site Structure menu' );
});

jQuery(document).on('click','#elementor-editor-wrapper-v2 .MuiGrid-root:nth-child(2) .eui-stack>div.MuiTabs-root button',function(){
    let index = jQuery(this).index();
    let info = '';
    switch (index) {
        case 0:
            info = 'Desktop';
            break;
        case 1:
            info = 'Tablet';
            break;
        case 2:
            info = 'Mobile';
            break;
    }
    analyticsDataPush ( 'Switch Device', 'Switch Device', info);
});

jQuery( window ).on( "load", function() {
    let iframe = jQuery("#elementor-preview-iframe").contents();
    iframe.on('click', "div[data-elementor-type='twbb_header'] .elementor-document-handle", function () {
        analyticsDataPush('Edit Header', 'Edit Header');
    });

    iframe.on('click', "div[data-elementor-type='twbb_single'] .elementor-document-handle", function () {
        analyticsDataPush('Edit Content', 'Edit Content');
    });

    iframe.on('click', "div[data-elementor-type='twbb_footer'] .elementor-document-handle", function () {
        analyticsDataPush('Edit Footer', 'Edit Footer');
    });

    iframe.on('click', ".elementor-widget .elementor-editor-element-edit", function () {
        let widget_type = jQuery(this).closest(".elementor-widget").attr("data-widget_type");
        analyticsDataPush('Edit Widget', 'Edit Widget', 'Widget Edit: ' + widget_type);
    });

    /* Using interval to wait while html content will be loaded */
    function interval_html_load( className, callback ) {
        let count = 0;
        const intervalID = setInterval(() => {
            if( jQuery(document).find(className).length ) {
                clearInterval(intervalID);
                callback();
            } else if( count == 10 ) {
                clearInterval(intervalID);
            }
            count++;
        }, 500);
    }

    function site_settings_click_events() {
        jQuery(document).find(".elementor-panel-menu-item-global-colors").on("click", function() {
            interval_html_load("#elementor-kit-panel-content-controls .pickr", global_color_pickr_click_event);
        });
        jQuery(document).find(".elementor-panel-menu-item-global-typography").on("click", function() {
            interval_html_load("#elementor-kit-panel-content-controls .eicon-edit", global_fonts_edit_click_event);
        });

        jQuery(document).find(".elementor-panel-menu-item-settings-site-identity").on("click", function() {
            analyticsDataPush('Settings', 'Settings', 'Site Identity');
        });
        jQuery(document).find(".elementor-panel-menu-item-settings-background").on("click", function() {
            analyticsDataPush('Settings', 'Settings', 'Background');
        });
        jQuery(document).find(".elementor-panel-menu-item-settings-layout").on("click", function() {
            analyticsDataPush('Settings', 'Settings', 'Layout');
        });
        jQuery(document).find(".elementor-panel-menu-item-settings-lightbox").on("click", function() {
            analyticsDataPush('Settings', 'Settings', 'Lightbox');
        });
        jQuery(document).find(".elementor-panel-menu-item-settings-page-transitions").on("click", function() {
            analyticsDataPush('Settings', 'Settings', 'Page transitions');
        });
        jQuery(document).find(".elementor-panel-menu-item-settings-custom-css").on("click", function() {
            analyticsDataPush('Settings', 'Settings', 'Custom CSS');
        });
        jQuery(document).find(".elementor-panel-menu-item-settings-additional-settings").on("click", function() {
            analyticsDataPush('Settings', 'Settings', 'Additional Settings');
        });
    }

    function global_color_pickr_click_event() {
        jQuery(document).find("#elementor-kit-panel-content-controls .pickr").on("click", function() {
            analyticsDataPush('Design System', 'Design System', 'Edit Global Color');
        });
    }
    function global_fonts_edit_click_event() {
        jQuery(document).find("#elementor-kit-panel-content-controls .eicon-edit").on("click", function() {
            analyticsDataPush('Design System', 'Design System', 'Edit Global Fonts');
        });
    }

    if( typeof window.$e != 'undefined' ) {
        let create_command_active = 0;
        window.$e.commands.on('run:before', function (component, command, args) {
            let widget_type = '';
            /* Add widget command */
            if( 'document/elements/create' === command ) {
                if( typeof args.model != "undefined" && typeof args.model.widgetType != "undefined" ) {
                    create_command_active = 1;
                    widget_type = args.model.widgetType;
                    analyticsDataPush('Widgets', 'Widgets', 'Widget Add: ' + widget_type);
                }

            }

            /* Edit widget command */
            if( 'panel/editor/open' == command ) {
                if( create_command_active ) {
                    create_command_active = 0;
                } else {
                    if( typeof args.model != "undefined" && typeof args.model.attributes != "undefined" && typeof args.model.attributes.widgetType != "undefined" ) {
                        widget_type = args.model.attributes.widgetType;
                        analyticsDataPush('Widgets', 'Widgets', 'Widget Edit: ' + widget_type);
                    }
                }

            }

            /* Delete widget command */
            if ( 'document/elements/delete' == command ) {
                if ( typeof args.containers != 'undefined' && typeof args.containers[0] != 'undefined' &&  args.containers[0].type == 'widget') {
                    widget_type = args.containers[0].label;
                    analyticsDataPush('Widgets', 'Widgets', 'Widget Delete: ' + widget_type);
                }
            }

            /* Site settings open/back commands */
            if( 'editor/documents/open' == command || 'panel/global/back' == command) {
                interval_html_load(".elementor-panel-menu-item-global-colors", site_settings_click_events);
            }

            /* Listen command to add new color or typography */
            if( 'document/repeater/insert' == command) {
                if ( typeof args.name !== 'undefined' && args.name == 'custom_colors' ) {
                    analyticsDataPush('Design System', 'Design System', 'Add Global Color');
                }
                if ( typeof args.name !== 'undefined' && args.name == 'custom_typography' ) {
                    analyticsDataPush('Design System', 'Design System', 'Add Global Fonts');
                }
            }
            /* Listen command to remove color or typography */
            if( 'document/repeater/remove' == command) {
                if ( typeof args.name !== 'undefined' && args.name == 'custom_colors' ) {
                    analyticsDataPush('Design System', 'Design System', 'Remove Global Color');
                }
                if ( typeof args.name !== 'undefined' && args.name == 'custom_typography' ) {
                    analyticsDataPush('Design System', 'Design System', 'Remove Global Fonts');
                }
            }
        });
    }

    /**
    *  Check safe mode popup the 35 seconds and push data to analytics is popup visible
    *  Elementor is show popup using again timeout after 30 sconds
    *
    *  @param iterations integer to avoid unlimited check job and finish after max 35 seconds
    * */
    function safe_mode_popup( iterations ) {
            let $notice = jQuery( '#elementor-try-safe-mode' );
            if ( ! $notice.data( 'visible' ) ) {
                iterations++;
                if(  iterations > 20 ) {
                    return false;
                }
                setTimeout( safe_mode_popup, 500, iterations );
            } else {
                analyticsDataPush('Safe Mode', 'Safe Mode', 'Safe mode fired');
            }
    }
    setTimeout( safe_mode_popup, 25000, 0 );

})
