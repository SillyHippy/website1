class TWBB_CHOICE_TOOL extends FE_TOOL_FRONTEND {

    constructor() {
        super();
        this.tool_control = [];
        this.tool_values = {};
        this.hasVisibleValue = true;
        this.toolSelector = ".twbb-choice-tool-container";
        this.active_class = "twbb-choice-active";
    }

    open_editor_command() {
        this.setToolControls();
        if (this.tool_control.length > 0) {
            this.renderTool();
        }
    }

    onToolClick(tool, event = '') {

        FE_TOOL_FRONTEND.closeAllTools();

        if (!this.tool_control) {
            this.setToolControls();
        }

        let tool_el = jQuery(tool).closest('.twbb-choice-tool-container');
        let control = tool_el.attr('data-control');
        this.tool_values[control] = jQuery(tool).attr('data-tool-value');

        this.renderTool();
        this.setActiveToolData(tool_el,  true);

        let tool_control = this.getResponsiveControl(control);
        let settings = {
            [tool_control]: this.tool_values[control],
        };

        this.changeWidgetSetting(null, settings);
        this.dataPush(tool_el);
    }

    renderTool(new_value = false, set_active = true, tool_element) {
        if (new_value) {
            this.setToolControls();
        }

        let self = this;
        this.getToolsContainer().find('.twbb-choice-tool-container').each(function () {
            let $el = jQuery(this);
            let control = $el.attr('data-control');

            $el.find('.twbb-choice-tool').each(function () {
                let $opt_el = jQuery(this);

                if ($opt_el.attr('data-tool-value') === self.tool_values[control]) {
                    $opt_el.addClass(self.active_class);
                } else {
                    $opt_el.removeClass(self.active_class);
                }
            });
        });

    }

    setToolControls() {
        let self = this;
        this.getToolsContainer().find('.twbb-choice-tool-container').each(function () {
            let $el = jQuery(this);
            let control = $el.attr('data-control');

            self.tool_control.push(control);
            self.tool_values[control] = self.getAppliedSettingValue(control);
        })
    }

}

let choice_tool;
jQuery(document).on('ready', function () {
    choice_tool = new TWBB_CHOICE_TOOL();
    window['choice_tool'] = choice_tool;
    choice_tool.init();
});

