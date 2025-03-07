class TWBB_COUNT_CONTROL_TOOL extends TWBB_DROPDOWN_SELECT_TOOL {
    constructor() {
        super();
        this.tool_control = [];
        this.tool_values = {};
        this.hasVisibleValue = true;
        this.toolSelector = ".twbb-fe-counter-select-tool";
        this.dropdown_type = "number";
    }

    open_editor_command() {

        this.setControls();
        this.renderTool();
        if (this.model.attributes.elType === 'container') {
            this.view.off('render', this.renderRightTools, this)
            this.view.on('render', this.renderRightTools, this)
        }
    }

    registerEvents() {
        let self = this;

        jQuery(document).on('click', '.twbb-fe-counter-select-tool', function () {
            if (!self.tool_control.length) {
                self.setControls();
            }
            self.onToolClick(jQuery(this));
        });

        jQuery(document).on('click', '.twbb-fe-counter-select-tool .twbb-fe-dropdown li', function (e) {
            if (e.keyCode !== 13) {
                self.renderNewValue(jQuery(this).text(), jQuery(this).closest('.twbb-fe-counter-select-tool'))
            }
        });

        jQuery(document).on("change", ".twbb-count_control", function () {
            self.renderNewValue(jQuery(this).val(), jQuery(this).closest('.twbb-fe-counter-select-tool'))
        });
    }

    onToolClick(tool) {
        super.onToolClick(tool);
        this.renderTool();
        this.setSelectActiveElement(tool, this.getNumberValue(tool));

        selectToolClick(tool);
        this.setActiveToolData(tool, true);
    }

    renderNewValue(size, tool) {
        if (!size || parseInt(size) <= 0) {
            return;
        }

        let control = tool.data('control');

        if (typeof this.tool_values[control] === "undefined") {
            return;
        }

        if (typeof this.tool_values[control] === "object") {
            this.tool_values[control]['size'] = size;
        } else {
            this.tool_values[control] = size;
        }

        let tool_control = this.getResponsiveControl(control);
        let settings = {
            [tool_control]: this.tool_values[control],
        };

        this.changeWidgetSetting(null, settings);

        if (this.model.attributes.elType === 'container') {
            this.renderRightTools();
        }
        this.dataPush(tool);
    }

    renderRightTools() {
        let container = this.getToolsContainer();

        container.find('.twbb-fe-counter-select-tool').removeAttr('style');
        this.view.$el.attr('right-tool', "1")
        let content_width = this.model.getSetting('content_width');
        if (content_width === 'boxed') {
            container.find('.twbb-fe-counter-select-tool[data-tool_type="boxed_width"]').css('display', 'flex');
        } else if (content_width === 'full') {
            container.find('.twbb-fe-counter-select-tool[data-tool_type="width"]').css('display', 'flex');
        }
        this.renderTool();
    }

    setControls() {
        let self = this;

        /* Empty array before set if command run again */
        self.tool_control = [];
        this.getToolsContainer().find('.twbb-fe-counter-select-tool').each(function () {
            let control = jQuery(this).attr('data-control');
            self.tool_control.push(control);
            self.tool_values[control] = self.getAppliedSettingValue(control);
        });
    }

    renderTool(new_value=false, set_active=true, tool_element) {

        if(new_value){
            this.setControls();
        }

        let self = this;
        this.getToolsContainer().find('.twbb-fe-counter-select-tool').each(function (){
            let el = jQuery(this);
            el.find("input[type='number']").val(self.getNumberValue(el))
        });
    }

    getNumberValue(control) {
        let is_string = (typeof control === 'string' || control instanceof String);
        if (!is_string) {
            control = jQuery(control).data('control');
        }

        let value = this.tool_values[control];
        if (typeof value === "object") {
            value = value['size'];
        }

        return value;
    }

}

let count_control_tool;
jQuery(document).on('ready', function () {
    count_control_tool = new TWBB_COUNT_CONTROL_TOOL();
    window['count_control_tool'] = count_control_tool;
    count_control_tool.init();
});