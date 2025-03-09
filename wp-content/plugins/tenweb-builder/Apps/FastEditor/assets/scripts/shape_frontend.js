class TWBB_SHAPE_TOOL extends TWBB_DROPDOWN_SELECT_TOOL {

    constructor() {
        super();
        this.tool_control = '';
        this.shape = '';
    }

    open_editor_command() {
        this.setToolControl();
        if( this.tool_control ) {
            this.setShape();
        }
    }

    registerEvents() {
        let self = this;
        jQuery(document).on('click', '.twbb-shape-tool-container.twbb-fe-select-tool', function () {

            if (!self.tool_control) {
                self.setToolControl();
            }

            self.setShape();
            self.onToolClick(jQuery(this));
            self.dataPush(jQuery(this));
        });

        jQuery(document).on('click', '.twbb-shape-tool-container.twbb-fe-select-tool .twbb-fe-dropdown li', function () {

            let value = jQuery(this).find("span").data('key');
            let settings = {
                [self.tool_control]: value,
            };
            self.changeWidgetSetting(self.tool_control, settings);
        });
    }

    onToolClick(tool) {
        super.onToolClick(tool);
        this.setActiveToolData(tool);
        this.setSelectActiveElement(tool, this.shape);
        selectToolClick(tool);
    }

    setToolControl() {
        this.tool_control = this.getToolsContainer().find('.twbb-shape-tool-container').attr('data-control');
    }

    setShape(){
        this.shape = this.getAppliedSettingValue(this.tool_control);
    }
}

let shape_tool;
jQuery(document).on('ready', function () {
    shape_tool = new TWBB_SHAPE_TOOL();
    window['shape_tool'] = shape_tool;
    shape_tool.init();
});