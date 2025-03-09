class TWBB_FONTSIZE_TOOL extends TWBB_DROPDOWN_SELECT_TOOL {

    constructor() {
        super();
        this.tool_control = '';
        this.typography_font_size = '';
        this.font_size = '';
        this.settings_to_disable_global = {typography_typography: ''}
        this.hasVisibleValue = true;
        this.toolSelector = ".twbb-font_size-tool-container";
        this.dropdown_type = "number";
    }

    open_editor_command() {
        this.setToolControl();
        if (this.tool_control) {
            this.setFontSize();
            this.renderTool();
        }
    }

    registerEvents() {
        let self = this;
        jQuery(document).on('click', '.twbb-font_size-tool-container', function (e) {
            if (!jQuery(document).find(self.view.el).hasClass('elementor-loading')) {
                self.onToolClick(jQuery(this));
            }
        });

        jQuery(document).on('click', '.twbb-font_size-tool-container.twbb-fe-select-tool .twbb-fe-dropdown li', function (e) {
            if (e.keyCode !== 13) {
                self.renderNewValue(jQuery(this).text());
            }
        });

        jQuery(document).on("change", ".twbb-font_size", function () {
            self.renderNewValue(jQuery(this).val());
        });
    }

    onToolClick(tool) {
        if (!this.tool_control) {
            this.setToolControl();
        }

        this.setActiveToolData(tool);
        this.setFontSize()
        this.renderTool();

        super.onToolClick(tool);

        this.setSelectActiveElement(tool, this.font_size);
        selectToolClick(tool);
    }

    renderNewValue(size) {

        if (!size || parseInt(size) <= 0 || !this.typography_font_size) {
            return;
        }

        this.typography_font_size.size = parseInt(size);
        let tool_control = this.getResponsiveControl(this.tool_control);
        let settings = {
            [tool_control]: this.typography_font_size,
            typography_typography: 'custom'
        };
        let options = {
            render: false,
            external: true,
        };

        this.changeWidgetSetting(this.tool_control, settings, options);
        this.dataPush(this.getToolsContainer().find('.twbb-font_size-tool-container'));
    }

    setToolControl() {
        this.tool_control = this.getToolsContainer().find('.twbb-font_size-tool-container').attr('data-control')
    }

    renderTool(new_value=false, set_active=true) {
        if(set_active){
            this.setActiveToolData(this.getToolsContainer().find('.twbb-font_size-tool-container'), true);
        }

        if(new_value){
            this.setToolControl();
            this.setFontSize();
        }

        this.getToolsContainer().find("input[type='number'].twbb-font_size").val(this.font_size);
    }

    setFontSize() {
        this.typography_font_size = this.getAppliedSettingValue(this.tool_control);
        this.font_size = this.typography_font_size['size'];
    }

}

let font_size_tool;
jQuery(document).on('ready', function () {
    font_size_tool = new TWBB_FONTSIZE_TOOL();
    window['font_size_tool'] = font_size_tool;
    font_size_tool.init();
});