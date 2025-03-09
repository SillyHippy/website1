jQuery(document).ready(function () {
    var editorIframe = document.getElementById('elementor-preview-iframe');
    editorIframe.onload = function(){
        jQuery(editorIframe).contents()
            .on('click',
                '.elementor-element[data-element_type="container"],' +
                '.elementor-element[data-element_type="container"] .e-con-inner,' +
                '.elementor-element[data-element_type="container"] .elementor-empty-view .elementor-first-add,' +
                '.elementor-motion-effects-layer', function(e){
                    if( !jQuery("body").hasClass('twbb-sg-sidebar-opened') ) {
                        e.stopPropagation();
                        if (e.target === e.currentTarget) {
                            var parentContainer, targetElement;
                            if (jQuery(this).is('[data-element_type="container"]')) {
                                //for cases when the click is on the container itself
                                parentContainer = jQuery(this);
                            } else {
                                //for other cases when the click is on the container's children
                                parentContainer = jQuery(this).parents('.elementor-element[data-element_type="container"]').first();
                            }
                            targetElement = parentContainer.find('li.elementor-editor-element-setting.elementor-editor-element-edit[title="Edit Container"]').first();
                            targetElement.trigger('click');
                            jQuery('.elementor-component-tab.elementor-panel-navigation-tab.elementor-tab-control-style').trigger('click');
                        }
                    }
                }
            );
    };

});

function twbbIframeScale(open, tab_width = null) {
    let mode = elementor.channels.deviceMode.request('currentMode');
    if( open ) {
        if( mode !== 'desktop' ){
            twbbIframeScale(0);
            return;
        }
        let elementor_panel = parseInt(jQuery("#elementor-panel").width());
        if( tab_width === null ) {
            tab_width = elementor_panel;
        }
        let windowWidth = jQuery(window).width();
        let iframeWidth = windowWidth - tab_width;
        let scale = 1;
        if( iframeWidth < windowWidth ) {
            scale = iframeWidth / windowWidth;
        }
        jQuery("#elementor-preview").css({
            "min-width": "unset",
            "overflow": "hidden",
            "width": iframeWidth + 'px',
            "margin-left": tab_width - elementor_panel + 'px',
        });
        let height = 100 / scale;
        jQuery('#elementor-preview-iframe').attr( 'style',
            'scale:' + scale + ';transform-origin: 0 0; min-width: ' + windowWidth + 'px; height: ' + height + 'vh;'
        );
    } else {
        jQuery('#elementor-preview-iframe').removeAttr('style');
        jQuery("#elementor-preview").removeAttr("style");
    }
}

function changeDefaultWidgetSetting(widgetType, settings) {
    if( typeof window.$e != 'undefined' ) {
        window.$e.commands.on('run:before', function (component, command, args) {
            //Change categories widget default settings only for new added widgets, not for existing ones
            if ('preview/drop' === command) {
                if (typeof args.model != "undefined" && typeof args.model.widgetType != "undefined"
                    && args.model.widgetType === widgetType) {
                    args.model.settings = settings;
                }
            }
        });
    }
}

//TODO: this code is not going to stay here, it will be moved to the widget folder
jQuery( window ).on( "load", function() {
    changeDefaultWidgetSetting("twbb_woocommerce-categories",
        {
            'regulate_image_height': 'yes',
            'category_title_position': 'inside',
            'show_button': 'yes',
            'column_gap': {unit: 'px', size: 0, sizes: Array(0)},
            'row_gap': {unit: 'px', size: 0, sizes: Array(0)},
        });
});


