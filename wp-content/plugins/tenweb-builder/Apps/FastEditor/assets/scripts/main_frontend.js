
jQuery(document).ready(function () {

    jQuery(document).on('click', '.elementor-element[data-element_type="widget"]', function (event) {
        event.stopPropagation();

        if(event.target.closest('.twbb-fe-tools') === null){
            // if clicked outside of tools container
            FE_TOOL_FRONTEND.deleteActiveToolDataIfWidgetHasChanged(jQuery(this).data('id'));
            FE_TOOL_FRONTEND.closeAllTools();
            FE_TOOL_FRONTEND.reRenderToolsWithVisibleValues();
            activate_tool_by_widget_id();
        }
    });


    jQuery(document).on('click', '.elementor-element[data-element_type="container"]', function (event) {
        event.stopPropagation();

        if(event.target.closest('.twbb-fe-tools') === null){
            // if clicked outside of tools container
            FE_TOOL_FRONTEND.deleteActiveToolDataIfWidgetHasChanged(jQuery(this).data('id'));
            FE_TOOL_FRONTEND.closeAllTools();
            FE_TOOL_FRONTEND.reRenderToolsWithVisibleValues();
            activate_tool_by_widget_id();
        }
    });

    elementor.hooks.addAction( 'panel/open_editor/widget', function( panel, model, view ) {
        FE_TOOL_FRONTEND.activateTool(view.$el);
        twbb_position_element(view.$el.find('.twbb-fast-editor-tools-container'));
        FE_TOOL_FRONTEND.reInitTools(panel, model, view);
    });

    elementor.hooks.addAction( 'panel/open_editor/container', function( panel, model, view ) {
        FE_TOOL_FRONTEND.activateTool(view.$el);
        twbb_position_element(view.$el.find('>.twbb-fast-editor-tools-container'));
        FE_TOOL_FRONTEND.reInitTools(panel, model, view);
    });


    window.top.$e.commands.on('run:after', function (component, command, args) {
        if(command === "panel/change-device-mode"){
            FE_TOOL_FRONTEND.changeDeviceMode();
        }
    });

    FE_TOOL_FRONTEND.changeDeviceMode();

    /* trigger click part after remote:render */
    let pointer_coordinates = null;

    elementor.hooks.addAction('panel/open_editor/widget', function (panel, model, view) {
        let is_text_editor = model.attributes.widgetType === "text-editor";
        jQuery('.elementor-element').removeAttr('data-clicked-tool');

        if (!is_text_editor) {
            document.removeEventListener('mousemove', store_pointer_coordinates);
            pointer_coordinates = null;
            return;
        }

        document.addEventListener('mousemove', store_pointer_coordinates);

        view.off('before:render', on_tool_click);
        view.on('before:render', on_tool_click);
    });

    jQuery(document).on('blur', '.elementor-text-editor', function (e) {

        if (!pointer_coordinates) {
            return;
        }

        let pointer_x = pointer_coordinates['clientX'];
        let pointer_y = pointer_coordinates['clientY'];
        let tool_el = null;

        for (let el of Array.from(e.target.closest('.elementor-element').querySelectorAll('.twbb-fe-tool'))) {
            let rect = el.getBoundingClientRect();
            if (pointer_x >= rect['left'] && pointer_x <= rect['left'] + rect['width'] && pointer_y >= rect['top'] && pointer_y <= rect['top'] + rect['height']) {
                tool_el = el;
                break;
            }
        }

        if (tool_el) {
            let tool = jQuery(tool_el).attr('data-tool');
            jQuery(tool_el).closest('.elementor-element').attr('data-clicked-tool', tool);
        }
    });

    function on_tool_click() {
        let view = this;
        let tool_name = view.$el.attr('data-clicked-tool');
        view.$el.removeAttr('data-clicked-tool');

        if(!tool_name){
            return;
        }

        let tool_obj_key = tool_name + '_tool';
        if(!window[tool_obj_key]){
            return;
        }

        let tool_el = view.$el.find('.twbb-fe-tool[data-tool="' + tool_name + '"]');
        if(tool_el){
            window[tool_obj_key].setActiveToolData(tool_el)
        }
    }

    function store_pointer_coordinates(e) {
        pointer_coordinates = e;
    }

    /* end */


    function activate_tool_by_widget_id(){

        if(FE_TOOL_FRONTEND.getActiveToolContainer() !== null){
            return;
        }

        let widget_id = window.parent.$e.components.get("panel/editor").activeModelId;

        if(!widget_id){
            return;
        }

        let el = document.querySelector('[data-id="'+widget_id+'"]');
        if(el === null){
            return;
        }

        FE_TOOL_FRONTEND.activateTool(jQuery(el));
    }

    jQuery(document).on('mouseenter', '.twbb-fe-tools > div', function(event) {
        jQuery(event.target).closest(".twbb-fe-tools").addClass("twbb-fe-tools-hovered");
    });

    jQuery(document).on( "mouseleave", '.twbb-fe-tools', function(event) {
        jQuery(".twbb-fe-tools").removeClass("twbb-fe-tools-hovered");
    });

    jQuery(document).on('keyup', '.twbb_ask_to_ai', function() {
        if (jQuery(this).val().trim() !== '') {
            jQuery(this).closest('.ask_to_ai_input_container').removeClass('ask_to_ai_disabled');
            jQuery(this).closest('.twbb-fe-tool').removeClass('twbb_ask_to_ai_empty');
            jQuery(this).closest('.twbb-fe-tool').find('.twbb_ask_to_ai_actions').removeClass('twbb_ask_to_ai_actions_active');
        } else {
            jQuery(this).css('height', '32px');
            jQuery(this).css('border-radius', '20px');
            jQuery(this).css('overflow-y', 'hidden');
            jQuery(this).closest('.ask_to_ai_input_container').addClass('ask_to_ai_disabled');
            jQuery(this).closest('.twbb-fe-tool').addClass('twbb_ask_to_ai_empty');
            jQuery(this).closest('.twbb-fe-tool').find('.twbb_ask_to_ai_actions').addClass('twbb_ask_to_ai_actions_active');
        }
    });
    jQuery(document).on('focus', '.twbb_ask_to_ai', function() {
        if(jQuery(this).data('type') === 'image'){
            let widgetId = window.parent.$e.components.get("panel/editor").activeModelId;
            let container = window.parent.$e.components.get('document').utils.findContainerById(widgetId);
            let image_url = container.args.model.attributes.settings.attributes.image.url;
            if(image_url.includes("/placeholder.png") || typeof image_url === 'undefined' || image_url === ''){
                jQuery(this)
                    .closest('.twbb-fe-tool')
                    .find('.twbb-ai-generate-image')
                    .each(function () {
                        if (jQuery(this).data('view') !== 'new_image_view') {
                            jQuery(this).addClass('twbb-ai-generate-image-disabled');
                        }
                    });
            }else{
                jQuery(this).closest('.twbb-fe-tool').find('.twbb-ai-generate-image').removeClass('twbb-ai-generate-image-disabled');
            }
        }
    });
    jQuery(document).on('click', '.twbb_ask_to_ai_action_remove_bg', function() {
        let twbb_ask_to_ai = jQuery(this).closest('.twbb-fe-tool').find('.twbb_ask_to_ai');
        twbb_ask_to_ai.val('Remove background');
        jQuery(this).closest('.twbb-fe-tool').find('.twbb_ask_to_ai_submit_button').trigger('click');
    });



    jQuery(document).on('input','.twbb_ask_to_ai', function () {
        if(this.scrollHeight<44){
            jQuery(this).css('height', '32px');
            jQuery(this).css('border-radius', '20px');
            jQuery(this).css('overflow-y', 'hidden');
        }else{
            jQuery(this).css('border-radius', '16px');
            if(this.scrollHeight>80){
                jQuery(this).css('overflow-y', 'auto');
            }
            jQuery(this).css('height', this.scrollHeight + 'px');
        }
    });


});

function twbb_position_element(el) {
    el.removeAttr('style');
    if (el[0] !== undefined &&
        el[0].getBoundingClientRect().left < 0) {
        el.css({'left': 0, 'right': 'unset'});
        let windowWidth = jQuery(window).width();
        if ( windowWidth < 500 ) {
            el.css({'left': '50%', 'right': 'unset', 'transform': 'translate(-50%, 0)',
                'width':' max-content'});
        }
    }
    if (el[0] !== undefined &&
        el[0].getBoundingClientRect().top < 0) {
        el.css({'top': 'unset', 'bottom': '-50px'});
    }
}

function twbb_onToolClick(that, tool_name) {
    window[tool_name].onToolClick(that);
}

function twbb_fast_edit_tools_events(that, type) {
    let widgetId = window.parent.$e.components.get("panel/editor").activeModelId;
    if(typeof window.twbb_fast_editor_tools_state === "undefined"){
        window.twbb_fast_editor_tools_state = {};
    }
    window.twbb_fast_editor_tools_state[widgetId] = type;
    if(type==='ask_ai'){
        jQuery(that).closest('.twbb-fast-editor-tools-container').addClass('twbb_ask_to_ai_opened');
    }else if(type==='tools'){
        jQuery(that).closest('.twbb-fast-editor-tools-container').removeClass('twbb_ask_to_ai_opened');
    }
}