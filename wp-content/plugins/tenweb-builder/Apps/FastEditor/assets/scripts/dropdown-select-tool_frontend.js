class TWBB_DROPDOWN_SELECT_TOOL extends FE_TOOL_FRONTEND {

    constructor() {
        super();
        this.tool_control = '';
        this.tool_value = '';
        this.dropdown_type = 'default';
    }

    onToolClick(tool) {
        FE_TOOL_FRONTEND.closeOtherTools(TWBB_DROPDOWN_SELECT_TOOL);
        tool = jQuery(tool);
        let self = this;

        if (tool.find('ul.twbb-fe-dropdown li').length > 0) {
            return;
        }

        let tool_controls_list = [];
        if (typeof self.tool_control === 'object') {
            tool_controls_list = self.tool_control;
        } else {
            tool_controls_list = [self.tool_control];
        }

        tool_controls_list.forEach(function (tool_control) {
            let script = '';
            let options = self.getDropdownOptions(tool_control);
            if (!options) {
                return;
            }

            let template_id_for_li_elements = options['id'] + '_dropdown_options';
            if (!jQuery('#' + template_id_for_li_elements).length > 0) {
                script = '<script type="text/html" id="' + template_id_for_li_elements + '">';
                script += self.collectLi(options);
                script += '</script>';
                jQuery('body').append(script);
            }

            jQuery(tool).find('ul.twbb-fe-dropdown').html(jQuery('#' + template_id_for_li_elements).html());
        });

    }

    collectLi(options) {
        if(this.dropdown_type === 'number'){
            return this.collectLiNumber(options)
        }

       return this.collectLiDefault(options);
    }

    collectLiNumber(options) {
        let script = '';

        for (let i = options['value']['min']; i <= options['value']['max']; i++) {
            script += '<li><span>' + i + '</span></li>';
        }

        return script;
    }

    collectLiDefault(options){
        let script = '';
        jQuery.each(options['value'], function (key, value) {
            script += '<li data-action="' + key + '"><span data-key="' + key + '">' + value + '</span></li>';
        });

        return script;
    }

    getDropdownOptions(tool_control) {
        let dropdown_options_key = this.getWidgetType() + '_' + tool_control + '_dropdown_options';
        return window['twbb_fe_localized_data'][dropdown_options_key];
    }

    closeTool(container = null) {
        if (container === null) {
            container = this.getToolsContainer();
        }

        container.find('.twbb-fe-select-tool.active').each(function () {
            handleDropdown(jQuery(this), false);
        });
    }
}
