class TWBB_CLICK_TOOL extends FE_TOOL_FRONTEND {

    constructor() {
        super();
        this.tool_control = '';
        this.toolSelector = ".twbb-click-tool-container";
    }


    open_editor_command() {
        this.setToolControl();
    }

    onToolClick(tool = '', event = '') {
        let self = this;

        if ( self.tool_control === undefined ) {
            //trigger panel open tool for new added widgets
            self.setToolControl();
        }

        /* Close all dropdowns */
        FE_TOOL_FRONTEND.deleteAllActiveToolData();

        //for containers open default classic unit
        if( jQuery(tool).closest('.twbb-fast-editor-tools-container').parent().attr('data-element_type') == 'container' ) {
            window.parent.jQuery('.elementor-component-tab.elementor-panel-navigation-tab.elementor-tab-control-style').trigger('click');
            window.parent.jQuery('.elementor-control-background_background .tooltip-target[data-tooltip="Classic"]').trigger('click');
        }

        if( jQuery(tool).closest('.twbb-fast-editor-tools-container').parent().parent().attr('data-element_type') == 'widget' &&
            jQuery(self.panel.el).find('.' + self.tool_control).length == 0 && !jQuery(self.panel.el).find(".elementor-panel-navigation-tab.elementor-tab-control-content").hasClass("elementor-active") ) {
            jQuery(self.panel.el).find(".elementor-panel-navigation-tab").removeClass("elementor-active");
            jQuery(self.panel.el).find(".elementor-panel-navigation-tab.elementor-tab-control-content").addClass("elementor-active");
            self.panel.currentPageView.activateTab('content');
        }

        let el_to_click = jQuery(self.panel.el).find('.' + self.tool_control+':visible');
        if(el_to_click.length === 0 && self.tool_control == "elementor-control-gallery-add"){
            el_to_click = jQuery(self.panel.el).find('.elementor-control-gallery-thumbnails:visible')
        }

        el_to_click.trigger("click");

        self.dataPush(jQuery(this).closest(".twbb-click-tool-container"));
    }

    setToolControl(){
        this.tool_control = this.getToolsContainer().find('.twbb-click-tool-container').attr('data-control');
    }
}

let click_tool;
jQuery(document).on('ready', function () {
    click_tool= new TWBB_CLICK_TOOL();
    window['click_tool'] = click_tool;
    click_tool.init();
});