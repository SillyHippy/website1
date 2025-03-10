class TWBB_FONTSTYLE_TOOL extends TWBB_DROPDOWN_SELECT_TOOL {

    constructor() {
        super();
        self.tool_control = '';
        self.fontStyle = '';
        this.settings_to_disable_global = {typography_typography: ''}
    }
    open_editor_command() {
        this.setToolControl();
        if( this.tool_control ) {
            this.setFontStyle();
        }
    }

    registerEvents() {
        let self = this;
        jQuery(document).on('click', '.twbb-font_style-tool-container.twbb-fe-select-tool', function () {

            if (!self.tool_control) {
                self.setToolControl();
            }

            self.setFontStyle();
            self.onToolClick(jQuery(this));
            self.dataPush(jQuery(this));
        });

        jQuery(document).on('click', '.twbb-font_style-tool-container.twbb-fe-select-tool .twbb-fe-dropdown li', function () {

            let value = jQuery(this).find("span").data('key');
            let settings = {
                [self.tool_control]: value,
                typography_typography: 'custom'
            };
            self.changeWidgetSetting(self.tool_control, settings);
        });
    }

    onToolClick(tool) {
        super.onToolClick(tool);
        this.setActiveToolData(tool);
        this.setSelectActiveElement(tool, this.fontStyle);
        selectToolClick(tool);
    }

    setToolControl() {
        this.tool_control = this.getToolsContainer().find('.twbb-font_style-tool-container').attr('data-control');
    }

    setFontStyle(){
        this.fontStyle = this.getAppliedSettingValue(this.tool_control);
    }
}

let fontStyle_tool;
jQuery(document).on('ready', function () {
    fontStyle_tool= new TWBB_FONTSTYLE_TOOL();
    window['font_style_tool'] = fontStyle_tool;
    fontStyle_tool.init();
});

