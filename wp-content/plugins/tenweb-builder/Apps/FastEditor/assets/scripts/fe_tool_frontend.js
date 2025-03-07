class FE_TOOL_FRONTEND {
    static INSTANCES = [];
    static DEVICE_MODE = "desktop";

    static CURRENT_ACTIVE_TOOL_CONTAINER = null;
    static PREVIOUS_ACTIVE_TOOL_CONTAINER = null;


    constructor() {
        this.model = '';
        this.view = '';
        this.panel = '';
        this.container = '';
        this.widgetID = '';
        this.isGlobalActive = false;
        this.settings_to_disable_global = null;
        this.hasVisibleValue = false;
        this.toolSelector = "";
        this.activeToolData = null;
        FE_TOOL_FRONTEND.INSTANCES.push(this);
    }

    init() {
        this.registerEvents();
    }

    reInit(panel, model, view){
        let self = this;

        self.model = model;
        self.panel = panel;
        self.view = view;
        self.container = self.view.container;
        self.widgetID = self.model.attributes.id;

        if(!self.view.$el.hasClass('twbb-active-fast-editor-tools')){
            FE_TOOL_FRONTEND.activateTool(self.view.$el);
        }

        self.addRenderEventListener();

        if(self.hasVisibleValue || self.toolsAreRendered() === false){
            self.open_editor_command_when_ready();
        }else{
            self.open_editor_command();
            self.renderRightTools();
        }

    }

    renderRightTools() {}

    open_editor_command() {};

    renderTool(new_value=false, set_active=true, tool_element=null) {}

    registerEvents() {
        jQuery(document).on('keyup', '.twbb-video-tool-url', function() {
            if (jQuery(this).val().trim() !== '') {
                jQuery(this).closest('.twbb-video-tool-url-container').removeClass('twbb-video-tool-url_inactive');
                jQuery(this).closest('.twbb-video-tool-url-container').find(".twbb-video-tool-open-url").attr('href', jQuery(this).val());
            } else {
                jQuery(this).closest('.twbb-video-tool-url-container').addClass('twbb-video-tool-url_inactive')
            }
        });
        jQuery(document).on('click', '.twbb-video-tool-open-url', function() {
            window.open( jQuery(this).attr('href'), '_blank');
        });
    }

    onToolClick(tool, event = '') {}

    dataPush(tool){
        this.callDataAnalytics(jQuery(tool).attr('data-analytics'));
    }

    callDataAnalytics( action, eventName = '', info = '' ) {
        if ( eventName  === '') {
            eventName = 'Fast editing tool';
        }
        if ( info  === '' && this.model ) {
            info = this.model.attributes.elType;
            if( typeof this.model.attributes.widgetType != 'undefined' ) {
                info += ': ' + this.model.attributes.widgetType;
            }
        }
        window.parent.analyticsDataPush ( action, eventName, info );
    }

    setSetting(container, settings, options = '') {
        if ( options === '' )  {
            options = {
                external: true,
                render: true,
            };
        }

        window.parent.$e.commands.run('document/elements/settings', {
            "container": container,
            "options": options,
            "settings": settings
        });
    }


    getResponsiveControl(setting_name) {

        if (FE_TOOL_FRONTEND.DEVICE_MODE === "desktop") {
            return setting_name;
        }

        let widget_type = this.getWidgetType();
        let controls = window.top.ElementorConfig.widgets[widget_type]['controls'];

        let responsive_setting_name = setting_name + '_' + FE_TOOL_FRONTEND.DEVICE_MODE;
        if (controls[responsive_setting_name]) {
            // if the setting support responsive values
            return responsive_setting_name;
        }

        return setting_name;
    }

    disableGlobals(container, settings) {
        return parent.window.$e.commands.run('document/globals/disable', {
            "container": container,
            settings: settings,
            "options": {
                restore: true,
            },
        });
    }

    changeWidgetSetting(setting_name, settings, options = '', container=null, settings_to_disable_global=null) {
        if(container === null){
            container = this.container;
        }

        if(settings_to_disable_global === null){
            settings_to_disable_global = this.settings_to_disable_global;
        }

        let disable_globals = false;

        if (settings_to_disable_global !== null) {
            let setting_value = this.getAppliedSettingValueWithType(setting_name);
            disable_globals = setting_value['type'] === 'global';
        }


        let self = this;
        if (disable_globals) {
            this.disableGlobals(container, settings_to_disable_global).then(function () {
                self.setSetting(container, settings, options);
                if(options && options['render'] === false){
                    self.view.renderUI();
                }
                self.afterChangeWidgetSetting();
                FE_TOOL_FRONTEND.reRenderToolsWithVisibleValues();
            });
        } else {

            self.setSetting(container, settings, options);
            if(options && options['render'] === false){
                self.view.renderUI();
            }
            self.afterChangeWidgetSetting();
            FE_TOOL_FRONTEND.reRenderToolsWithVisibleValues();
        }
    }

    getAppliedSettingValue(setting_name){
        let setting_value = new TWBB_SETTING_VALUE(setting_name, this.model, this.container, FE_TOOL_FRONTEND.DEVICE_MODE);
        return setting_value.get_setting_value();
    }

    getAppliedSettingValueWithType(setting_name){
        let setting_value = new TWBB_SETTING_VALUE(setting_name, this.model, this.container,FE_TOOL_FRONTEND.DEVICE_MODE);
        return {
            "value": setting_value.get_setting_value(),
            "type": setting_value.setting_type
        };
    }

    setSelectActiveElement(tool, active_value){
        if (typeof active_value === 'string' || active_value instanceof String){
            active_value = active_value.toLowerCase();
        }

        jQuery(tool.find('ul.twbb-fe-dropdown li span')).each(function() {
            let $el = jQuery(this);
            if ( jQuery(this).text().toLowerCase() == active_value ) {
                $el.addClass('twbb-fe-select-tool-active');
            }else{
                $el.removeClass('twbb-fe-select-tool-active');
            }
        })
    }
    addRenderEventListener(){
        this.view.off('render', this.renderEventListenerCallbackForTool, this);
        this.view.on('render', this.renderEventListenerCallbackForTool, this);

        this.view.off('render', FE_TOOL_FRONTEND.renderEventListenerCallback, FE_TOOL_FRONTEND);
        this.view.on('render', FE_TOOL_FRONTEND.renderEventListenerCallback, FE_TOOL_FRONTEND);

        // added for social icon widget`s elementor-repeater-add control
        this.model.off('request:edit', FE_TOOL_FRONTEND.renderEventListenerCallback, FE_TOOL_FRONTEND);
        this.model.on('request:edit', FE_TOOL_FRONTEND.renderEventListenerCallback, FE_TOOL_FRONTEND);

    }

    renderEventListenerCallbackForTool(){
        let self = this;
        setTimeout(function (){
            self.onWidgetRender();
        }, 50);
    }

    onWidgetRender() {

        let self = this;

        if (self.activeToolData === null) {
            return;
        }

        if (self.widgetID !== self.activeToolData['widget_id']) {
            return;
        }
        let el = self.activeToolData['widget_el'].find('.twbb-fe-tools [data-control="' + self.activeToolData['control'] + '"]');

        if (el) {
            if(self.activeToolData['render_tool']){
                self.renderTool();
            }else{
                self.onToolClick(el)
            }
        }

    }

    setActiveToolData(active_tool, render_tool=false){
        FE_TOOL_FRONTEND.deleteAllActiveToolData();

        this.activeToolData = {
            "widget_id": this.widgetID,
            "widget_el": this.view.$el,
            "control": active_tool.attr('data-control'),
            "render_tool": render_tool
        };

    }

    open_editor_command_when_ready(){
        let self = this;
        let max_try = 5;

        let interval_id = setInterval(function (){
            max_try--;

            if(self.toolsAreRendered() || max_try === 0){
                self.open_editor_command();
                self.renderRightTools();
                clearInterval(interval_id);
            }
        }, 50);
    }

    toolsAreRendered(){
        return this.getToolsContainer() !== null;
    }

    renderIfEmpty(){}

    afterChangeWidgetSetting(){}

    closeTool(container=null){}

    widgetHasTool(){
        if(!this.toolSelector || !this.view.$el){
            return false;
        }

        return this.getToolsContainer().find(this.toolSelector).length > 0;
    }

    getToolsContainer(tool_control = null){
        let tools_container = FE_TOOL_FRONTEND.getChildWithClass(this.view.$el, 'twbb-fast-editor-tools-container');

        if(this.model.attributes.elType === 'container'){
            return tools_container;
        }

        if(tools_container !== null){
            return FE_TOOL_FRONTEND.getChildWithClass(this.view.$el, 'twbb-fast-editor-tools-container');
        }


        let el = this.view.$el.find('.elementor-widget-container:first');
        if (el.length > 0) {
            if(tool_control === 'editor'){
                return el;
            }
            return FE_TOOL_FRONTEND.getChildWithClass(el, 'twbb-fast-editor-tools-container');
        }

        return null;
    }

    getWidgetType(){
        if(!this.model){
            return null;
        }

        return (this.model.attributes['elType'] === "container") ? "container" : this.model.attributes['widgetType'];
    }


    static renderEventListenerCallback(){
        setTimeout(function (){

            let container = FE_TOOL_FRONTEND.getToolsContainer();

            if(!container){
                FE_TOOL_FRONTEND.reRenderToolsWithVisibleValues();
                return;
            }

            if(container.data('rendered') === '1'){
                return;
            }

            FE_TOOL_FRONTEND.reRenderToolsWithVisibleValues();
            container.data('rendered', '1')

        }, 51);
    }

    static deleteAllActiveToolData() {
        for (let tool of FE_TOOL_FRONTEND.INSTANCES) {
            tool.activeToolData = null;
        }
    }

    static deleteActiveToolDataIfWidgetHasChanged(new_widget_id){
        for (let tool of FE_TOOL_FRONTEND.INSTANCES) {
            if(tool.activeToolData === null){
                continue;
            }

            if(tool.activeToolData['widget_id'] !== new_widget_id){
                tool.activeToolData = null;
            }
        }

    }

    static reInitTools(panel, model, view) {
        for (let tool of FE_TOOL_FRONTEND.INSTANCES) {
            tool.reInit(panel, model, view);
        }
    }

    static reRenderToolsWithVisibleValues(){
        for(let tool of FE_TOOL_FRONTEND.INSTANCES){
            if(tool.hasVisibleValue && tool.widgetHasTool()){
                tool.renderTool(true, false);
            }
        }
        if(typeof window.twbb_fast_editor_tools_state === "object"){
            let widgetId = window.parent.$e.components.get("panel/editor").activeModelId;
            if(typeof window.twbb_fast_editor_tools_state[widgetId] !== "undefined"){
                let widget_state = window.twbb_fast_editor_tools_state[widgetId];
                if(widget_state === 'tools'){
                    FE_TOOL_FRONTEND.CURRENT_ACTIVE_TOOL_CONTAINER.find('.twbb_ask_to_ai_opened').removeClass('twbb_ask_to_ai_opened');
                }
            }
            if(window.parent.jQuery('body').hasClass('twbb-sg-sidebar-opened')){
                jQuery('.twbb-fast-editor-tools-container').addClass('twbb_zoom');
            }else{
                jQuery('.twbb-fast-editor-tools-container').removeClass('twbb_zoom');
            }
        }
    }

    static changeDeviceMode(re_render=true){
        let new_device_mode = elementor.channels.deviceMode.request('currentMode');
        if(new_device_mode === FE_TOOL_FRONTEND.DEVICE_MODE){
            return false;
        }

        FE_TOOL_FRONTEND.DEVICE_MODE = new_device_mode;
        if(re_render){
            FE_TOOL_FRONTEND.reRenderToolsWithVisibleValues();
        }
    }

    /**
     * Closes All tools if tool_type is null or closes the tools which are instances of tool_type.
     * Used without tool_type when we should close all open tools. e.g. when user clicks on new widget.
     * Used with tool_type when we want to close specific tools. e.g. when user clicks on body element we should close
     * all dropwdowns.
     *
     * */
    static closeAllTools(tool_type=null) {

        let containers = [];
        if(FE_TOOL_FRONTEND.CURRENT_ACTIVE_TOOL_CONTAINER){
            containers.push(FE_TOOL_FRONTEND.CURRENT_ACTIVE_TOOL_CONTAINER)
        }

        if(FE_TOOL_FRONTEND.PREVIOUS_ACTIVE_TOOL_CONTAINER && !FE_TOOL_FRONTEND.PREVIOUS_ACTIVE_TOOL_CONTAINER.is(FE_TOOL_FRONTEND.CURRENT_ACTIVE_TOOL_CONTAINER)){
            containers.push(FE_TOOL_FRONTEND.PREVIOUS_ACTIVE_TOOL_CONTAINER)
        }

        if(containers.length === 0){
            return;
        }

        for (let tool of FE_TOOL_FRONTEND.INSTANCES) {
            for(let c of containers){
                if(tool_type === null || tool instanceof tool_type){
                    tool.closeTool(c);
                }
            }
        }
    }

    /**
     * Close all tools except tool which is instance of tool_type. Used when user clicks on tool to open it. e.g. If
     * user clicks on font size tool, the color picker should be closed
    **/
    static closeOtherTools(tool_type) {
        if (!FE_TOOL_FRONTEND.CURRENT_ACTIVE_TOOL_CONTAINER) {
            return;
        }

        for (let tool of FE_TOOL_FRONTEND.INSTANCES) {
            if (tool instanceof tool_type) {
                continue;
            }

            tool.closeTool(FE_TOOL_FRONTEND.CURRENT_ACTIVE_TOOL_CONTAINER);
        }
    }

    static activateTool(widget_element){
        widget_element = jQuery(widget_element);
        if(FE_TOOL_FRONTEND.CURRENT_ACTIVE_TOOL_CONTAINER && FE_TOOL_FRONTEND.CURRENT_ACTIVE_TOOL_CONTAINER.is(widget_element)){
            return;
        }

        FE_TOOL_FRONTEND.PREVIOUS_ACTIVE_TOOL_CONTAINER = FE_TOOL_FRONTEND.CURRENT_ACTIVE_TOOL_CONTAINER;
        FE_TOOL_FRONTEND.deactivateTool();
        FE_TOOL_FRONTEND.CURRENT_ACTIVE_TOOL_CONTAINER = widget_element;
        FE_TOOL_FRONTEND.CURRENT_ACTIVE_TOOL_CONTAINER.addClass('twbb-active-fast-editor-tools');

        if(typeof window.twbb_fast_editor_tools_state === "object"){
            let widgetId = window.parent.$e.components.get("panel/editor").activeModelId;
            if(typeof window.twbb_fast_editor_tools_state[widgetId] !== "undefined"){
                let widget_state = window.twbb_fast_editor_tools_state[widgetId];
                if(widget_state === 'tools'){
                    FE_TOOL_FRONTEND.CURRENT_ACTIVE_TOOL_CONTAINER.find('.twbb_ask_to_ai_opened').removeClass('twbb_ask_to_ai_opened');
                }
            }
        }
    }

    static deactivateTool(){
        if(FE_TOOL_FRONTEND.CURRENT_ACTIVE_TOOL_CONTAINER === null){
            return;
        }

        FE_TOOL_FRONTEND.CURRENT_ACTIVE_TOOL_CONTAINER.removeClass('twbb-active-fast-editor-tools');
        FE_TOOL_FRONTEND.CURRENT_ACTIVE_TOOL_CONTAINER = null;
    }

    static getActiveToolContainer(){
        return FE_TOOL_FRONTEND.CURRENT_ACTIVE_TOOL_CONTAINER;
    }

    static closestWithLimit(el, class_name, limit){
        if (el instanceof jQuery){
            el = el[0];
        }

        while (limit > 0){
            if(el.classList.contains(class_name)){
                return true;
            }
            el = el.parentElement;
            limit--;
        }

        return false;
    }

    static getChildWithClass(el, class_name){
        for(let child of el.children()){
            child = jQuery(child);

            if(child.hasClass(class_name)){
                return child;
            }
        }

        return null;
    }


    static getToolsContainer(){
        return FE_TOOL_FRONTEND.INSTANCES[0].getToolsContainer();
    }

}

window['FE_TOOL_FRONTEND'] = FE_TOOL_FRONTEND; // to access from edit bar