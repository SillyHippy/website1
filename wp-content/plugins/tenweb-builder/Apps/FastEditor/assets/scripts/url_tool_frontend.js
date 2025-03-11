class TWBB_URL_TOOL extends FE_TOOL_FRONTEND {

    constructor() {
        super();
        self.tool_control = '';
        self.button_url = '';
    }

    open_editor_command() {
        this.getControlValues();
    }

    getControlValues() {
        let self = this;
        let tool_container = this.getToolsContainer();

        self.tool_control = tool_container.find('.twbb-url-tool-container').attr('data-control');
        if( self.tool_control ) {
            self.tool_control_value = self.model.getSetting(self.tool_control);
            self.button_url = self.tool_control_value.url;
            tool_container.find(".twbb-url-tool-url").val(self.button_url);
        }
    }

    checkButtonDisabled() {
        let self = this;
        let tools_container = this.getToolsContainer();

        let buttonEl = tools_container.find(".twbb-url-tool-content .twbb-url-tool-content-button");
        if( tools_container.find(".twbb-url-tool-url").val() == "" ) {
            buttonEl.addClass("twbb-button-disabled");
        } else {
            buttonEl.removeClass("twbb-button-disabled");
        }
    }

    registerEvents() {

        let self = this;

        jQuery(document).on( "click", ".twbb-url-tool", function() {
            let content = jQuery(this).closest(".twbb-url-tool-container").find(".twbb-url-tool-content");
            FE_TOOL_FRONTEND.closeOtherTools(TWBB_URL_TOOL);
            if( !content.is(":visible") ) {
                self.setCurrentLinkValues();
                self.checkButtonDisabled();
                twbb_position_element(content);
                content.show();
            } else {
                content.hide();
            }
        });

        jQuery(document).on('click', 'body', function (e) {
            if ( e.target.closest(".twbb-url-tool-container") === null ) {
                jQuery(document).find(".twbb-url-tool-content").hide();
            }
        });

        jQuery(document).on( "input", ".twbb-url-tool-url", function() {
            self.checkButtonDisabled();
        });

        jQuery(document).on('click', '.twbb-url-tool-content-button', function () {
            if(jQuery(this).hasClass("twbb-button-disabled")) return false;
            let container, value, settings;
            container = self.container;
            value = self.getToolsContainer().find(".twbb-url-tool-url").val();
            self.tool_control_value.url = value;
            self.button_url = value;
            settings = {
                [self.tool_control]: self.tool_control_value,
            };
            self.dataPush(jQuery(this));
            self.setSetting(container, settings);
        });
    }

    setCurrentLinkValues() {
        let self = this;
        if( !self.tool_control ) {
            self.getControlValues();
        }
        self.getToolsContainer().find(".twbb-url-tool-url").val(self.button_url);
    }


    onToolClick(tool) {
        selectToolClick(tool);
    }

    closeTool(container){
        if(container === null){
            container = this.getToolsContainer();
        }

        container.find('.twbb-url-tool-content').hide();
    }
}

let url_tool;
jQuery(document).on('ready', function () {
    url_tool= new TWBB_URL_TOOL();
    window['url_tool'] = url_tool;
    url_tool.init();
});
