class TWBB_HTMLTAG_TOOL extends TWBB_DROPDOWN_SELECT_TOOL {

    constructor() {
        super();
        self.tool_control = '';
        self.tool_value = '';
    }

    open_editor_command() {
        this.setToolControl();
    }

    registerEvents() {
        let self = this;
        jQuery(document).on('click', '.twbb-dropdown-select-tool-container.twbb-fe-select-tool', function () {

            if(!self.tool_control){
                self.setToolControl();
            }

            self.onToolClick(jQuery(this));
            self.dataPush(jQuery(this));
        });

        jQuery(document).on('click', '.twbb-dropdown-select-tool-container.twbb-fe-select-tool .twbb-fe-dropdown li', function () {
            if(!self.tool_control){
                self.setToolControl();
            }

            self.render_new_value(jQuery(this).find("span").data('key'));
        });
    }

    onToolClick(tool) {
        super.onToolClick(tool);

        if (this.tool_control == 'editor') {
            this.tool_value = this.getContentCurrentTag(this.tool_control);
        }else{
            this.tool_value = this.getAppliedSettingValue(this.tool_control);
        }

        jQuery(tool).find('span[data-key="' + this.tool_value + '"').addClass('twbb-fe-select-tool-active');
        selectToolClick(tool);
    }

    render_new_value( tag ) {
        let self = this;
        self.tool_value = tag;
        let widgetId, container;
        let htmlStr = '';
        widgetId = window.parent.$e.components.get("panel/editor").activeModelId;
        container = window.parent.$e.components.get('document').utils.findContainerById(widgetId);
        let settings = {};
        if ( self.tool_control == 'editor' ) {
            htmlStr = this.getToolsContainer().closest('.elementor-widget-container').find('.elementor-text-editor').html();
            htmlStr = htmlStr.trim();
            if ( htmlStr == '' ) {
                return;
            }

            /* Clear tags in the content and add space */
            htmlStr = htmlStr.replace(/(<([^>]+)>)/gi, " ");
            let newhtmlStr = "<"+tag+">"+htmlStr.trim()+"</"+tag+">";
            settings = {
                [self.tool_control]: newhtmlStr,
            };

        } else {
            settings = {
                [self.tool_control]: tag,
            };
        }

        this.changeWidgetSetting(null, settings, '', container);
    }

    /* Searching our tags in the html string */
    getContentCurrentTag(tool_control = null) {
        let el = this.getToolsContainer(tool_control)[0].querySelector('.elementor-text-editor');
        if(el && el.firstElementChild){
            return el.firstElementChild.tagName.toLowerCase();
        }

        return null;
    }

    setToolControl(){
        this.tool_control = this.getToolsContainer().find('.twbb-dropdown-select-tool-container').attr('data-control');
    }
}

let htmlTag_tool;
jQuery(document).on('ready', function () {
    htmlTag_tool= new TWBB_HTMLTAG_TOOL();
    window['html_tag_tool'] = htmlTag_tool;
    htmlTag_tool.init();
});

