class TWBAI_IFRAME extends FE_TOOL_FRONTEND{
    constructor() {
        super();
        this.cache = {};
        this.widgetId;
        this.frontAiType = '';
    }

    setCacheElements() {
        let self = this;
        self.cache = WriteWithAIHelper.getCacheElements();
    }

    init() {
        super.init();
        this.setCacheElements();
    }

    registerEvents() {
        let self = this;
        self.aiButtonClick();
        self.onBodyClick();
        self.actionButtonClick();
        self.pressEscape();
        self.newPromptClick();
        self.askAiEvents();
    }

    askAiEvents() {
        let self = this;
        jQuery(document).on('click', '.twbb_ask_to_ai_submit_button', function(that) {
            self.askToAi(this);
        });
        jQuery(document).on('keydown', '.twbb_ask_to_ai', function (event) {
            // Check if the Enter key is pressed
            if (event.key === "Enter" || event.keyCode === 13) {
                event.preventDefault();
                jQuery(this).closest('.ask_to_ai_input_container').find('.twbb_ask_to_ai_submit_button').trigger('click');
            }
        });
    }
    askToAi(_this){
        let user_input = jQuery(_this).closest('.ask_to_ai_input_container').find('.twbb_ask_to_ai').val();
        if(user_input.length>0){
            jQuery(_this).closest('.ask_to_ai_input_container').find('.twbb_ask_to_ai').val('');
            jQuery(_this).closest('.ask_to_ai_input_container').find('.twbb_ask_to_ai').css({'height':'32px'})
            jQuery(_this).addClass('twbb_ai_loading');
            jQuery(_this).closest('.twbb-fast-editor-tools-container').addClass('twbb_disabled_loading');
            jQuery(_this).closest('.twbb-fe-tool').addClass('twbb_ask_to_ai_empty');
            window.parent.TWBBCoPilotInstance.getDataToSend(user_input);
        }
    }
    aiButtonClick() {
        let self = this;
        jQuery(document).on('click','.twbb-ai-front-button-layer', function () {
            self.onToolClick(jQuery(this));
        })
    }

    onToolClick(tool) {
        this.setActiveToolData(tool);
        FE_TOOL_FRONTEND.closeOtherTools(TWBAI_IFRAME);

        let self = this;
        let twb_ai_front = tool.closest(".twbb-ai-front");
        // GA event params
        let analyticsInfo = tool.closest('.elementor-widget').data('widget_type').split(".default")[0];
        twb_ai_front.attr('data-eventName','write with AI content');
        twb_ai_front.attr('data-info',analyticsInfo);
        if ( tool.parents('.elementor-widget-container').find('.pen').length == 0 ) {
            self.callDataAnalytics('write with AI',analyticsInfo);
        }
        if ( !tool.parent().find('.twbb-ai-front-action-cont').is(":visible") &&
            !tool.parent().find('.twbb-ai-front-new_prompt-container').is(":visible")) {
            if ( jQuery('.twbb-ai-front-action-cont').is(":visible") ||
                jQuery('.twbb-ai-front-new_prompt-container').is(":visible") ||
                tool.parents('.elementor-widget-text-editor.twbb-set-visible-ai-popup').length == 0 ) {
                self.popupClose();
            }
            //close all tool dropdowns
            let currentActive = jQuery(".twbb-fe-select-tool.active");

            if (currentActive.length > 0) {
                handleDropdown(currentActive,false);
            }

            tool.addClass('twbb-ai-front-button-layer-visible');
            tool.parent().parent().addClass('twbb-ai-front-open');
            this.position_element(tool.parent().find('.twbb-ai-front-action-cont'));
        } else {
            if( tool.parents('.elementor-widget-text-editor.twbb-set-visible-ai-popup').length == 0 ||
                tool.parents('.elementor-widget-text-editor.twbb-set-visible-ai-popup').attr('data-ai-popup-visible') == 'not_visible' ) {
                self.popupClose();
            } else {
                tool.parents('.elementor-widget-text-editor').attr('data-ai-popup-visible', 'not_visible' );
            }
        }
    }

    onBodyClick() {
        let self = this;
        jQuery('body').on('click',function(event) {
            if( jQuery(event.target).closest('.twbb-ai-front').length !== 1 ) {
                self.popupClose();
            }
        })
    }

    position_element(el) {
        el.removeAttr('style');
        el.css({'display':'flex','opacity':1});
        let windowWidth = jQuery(window).width();
        if ( windowWidth < 500 ) {
            el.css({'left': '50%', 'right': 'unset', 'transform': 'translate(-50%, 0)',
                'width':' max-content'});
        }
    }

    callDataAnalytics(data, info) {
        window.parent.analyticsDataPush( data, 'write with AI content', info );
    }

    actionButtonClick() {
        let self = this;
        jQuery(document).on('click', '.twbb-ai-action-button', function(that) {
            self.generateTextPreparation('current_text', jQuery(this));
        });

        jQuery(document).on('click', '.twbb-ai-generate-image', function(that) {
            if( self.model.get( 'elType' ) == 'widget' &&
                jQuery(self.panel.el).find('.twb-ai-image-button').length == 0 && !jQuery(self.panel.el).find(".elementor-panel-navigation-tab.elementor-tab-control-content").hasClass("elementor-active") ) {
                jQuery(self.panel.el).find(".elementor-panel-navigation-tab.elementor-tab-control-content").trigger("click");
            }
            parent.window.twbb_image_generation_view = jQuery(this).data('view');
            jQuery(self.panel.el).find(".twb-ai-image-button").trigger("click");
        });
    }

    pressEscape() {
        let self = this;
        jQuery(document).keyup(function(e) {
            if (e.key === "Escape") { // escape key maps to keycode `27`
                self.popupClose();
            }
        })
    }

    newPromptClick() {
        let self = this;
        jQuery(document).on('click', '.twbb-ai-front-new-prompt-button', function() {
            let twbb_ai_front = jQuery(this).closest(".twbb-ai-front");
            twbb_ai_front.addClass("twbb-ai-front-newprompt-open");
            // GA event params
            let analyticsEventName, analyticsAction, analyticsInfo;
            analyticsEventName = jQuery(this).closest('.twbb-ga-data-save').attr('data-eventName');
            analyticsAction = 'new_prompt_button';
            analyticsInfo = jQuery(this).closest('.twbb-ga-data-save').attr('data-info');
            window.parent.analyticsDataPush(analyticsAction, analyticsEventName, analyticsInfo);
            if( twbb_ai_front.find(".twbb-ai-front-new_prompt-container").length ) {
                twbb_ai_front.find(".twbb-ai-front-new_prompt-container").show();
                self.position_element(twbb_ai_front.find(".twbb-ai-front-new_prompt-container"));
            } else {
                jQuery(this).parent().closest("#twbb-ai-front-new_prompt-template").show();
                twbb_ai_front.find(".twbb-ai-front-new_prompt-container").show();
                self.position_element(twbb_ai_front.find(".twbb-ai-front-new_prompt-container"));
            }

            jQuery(this).parent().hide();
            self.newpromptClickEvent();
            jQuery(".twbb-ai-front-new_prompt-textarea").on('keyup', function(e) {
                if( jQuery(this).val() == "" ) {
                    jQuery(this).parent().find(".twbb-ai-front-new_prompt-action-button").addClass("twbb-ai-front-button-disabled");
                } else {
                    jQuery(this).parent().find(".twbb-ai-front-new_prompt-action-button").removeClass("twbb-ai-front-button-disabled");
                }
            });
        })
    }

    generateTextPreparation(text,that) {
        self = this;
        let current_text;
        for (let i in self.cache.controls) {
            if( window.parent.$e.components.get("panel/editor").manager.$el.find("div.elementor-control-" + self.cache.controls[i]).length ) {
                current_text = window.parent.$e.components.get("panel/editor").manager.currentPageView.model.getSetting(self.cache.controls[i]);
                self.widgetId = window.parent.$e.components.get("panel/editor").activeModelId;
                if( current_text != "" ) {
                    self.frontAiType = self.cache.controls[i];
                    break;
                }
            }
        }
        self.showHideFrontLoading(1, that);
        if( text == 'current_text' ) {
            twbb_send_request( that, current_text);
        } else if( text == 'new_text' ){
            let new_text = that.parent().find(".twbb-ai-front-new_prompt-textarea").val();
            twbb_send_request( that, new_text);
        }
    }

    newpromptClickEvent() {
        let self = this;

        jQuery(document).on('click', '.twbb-ai-front-new_prompt-action-button', function(){
            if( jQuery(this).hasClass("twbb-ai-front-button-disabled") ) {
                return false;
            }
            self.generateTextPreparation('new_text', jQuery(this));
        });

        self.newPromptShiftEnter();

    }

    newPromptShiftEnter() {
        jQuery(document).on('keydown', '.twbb-ai-front-new_prompt-textarea', function (event) {
            if ( event.key == 'Enter' && !event.shiftKey ) {
                // prevent default behavior
                event.preventDefault();
                jQuery(event.target).parent().find(".twbb-ai-front-new_prompt-action-button").trigger("click");
            }
        });
    }

    popupClose() {
        jQuery('.elementor-widget-text-editor').removeClass('twbb-set-visible-ai-popup');
        jQuery('.elementor-widget-text-editor').removeAttr('data-ai-popup-visible');
        jQuery('.twbb-ai-front-action-cont').parent().parent().removeClass('twbb-ai-front-open');
        jQuery('.twbb-ai-front-button-layer').removeClass('twbb-ai-front-button-layer-visible');
        jQuery('.twbb-ai-front.twbb-ai-front-newprompt-open').removeClass("twbb-ai-front-newprompt-open");
        jQuery('.twbb-ai-front').removeClass("twbb-ai-front-open-newprompt");
        jQuery('.twbb-ai-front-new_prompt-container').hide();
        jQuery('.twbb-ai-front-action-cont').attr('style','');

        jQuery('.twb-help-button-layer').removeClass('twbb-not-visible');

        // This part is for click outside the iframe and ESC button
        jQuery('#elementor-preview-iframe').contents().find('.twbb-ai-front-action-cont').parent().parent().removeClass('twbb-ai-front-open');
        jQuery('#elementor-preview-iframe').contents().find('.twbb-ai-front-button-layer').removeClass('twbb-ai-front-button-layer-visible');
        jQuery('#elementor-preview-iframe').contents().find('.twbb-ai-front.twbb-ai-front-newprompt-open').removeClass("twbb-ai-front-newprompt-open");
    }

    showHideFrontLoading( show, that ) {
        if( show ) {
            that.closest('.twbb-fast-editor-tools-container').addClass('twbb_disabled_loading');
            that.closest('.twbb-fe-tool').find('.twbb_ask_to_ai_submit_button').addClass('twbb_ai_loading');
        } else {
            that.closest('.twbb-fast-editor-tools-container').removeClass('twbb_disabled_loading');
            that.closest('.twbb-fe-tool').find('.twbb_ask_to_ai_submit_button').removeClass('twbb_ai_loading');
        }
    }

    addResult(output) {
        let self = this;
        output =WriteWithAIHelper.validateOutput(output, self.frontAiType);

        let args = self.generateSetting(output);

        FastEditorHelper.setSetting(args[0], args[1]);
    }

    generateSetting(output) {
        let widgetType = self.frontAiType;
        let settings = { [widgetType]: output };
        let container = window.parent.$e.components.get('document').utils.findContainerById(self.widgetId);

        return [ container, settings ];
    }

    closeTool(container){
        this.popupClose();
    }
}

let write_with_ai_tool;
jQuery(document).on('ready', function () {
    write_with_ai_tool = new TWBAI_IFRAME();
    window['write_with_ai_tool'] = write_with_ai_tool;
    write_with_ai_tool.init();
});

function twbb_send_request( that, selectedText) {
    if ( selectedText == "" ) {
        return false;
    }

    jQuery(document).find(".twbb-ai-error-message").hide();
    let action = jQuery(that).data("action");
    let params = {};

    // GA event params
    let analyticsInfo, analyticsEventName, analyticsAction;
    analyticsAction = action;
    if ( jQuery('.twbb-ga-data-save.twbb-ai-front-open').length > 0 ) {
        analyticsEventName = jQuery('.twbb-ga-data-save.twbb-ai-front-open').attr('data-eventName');
        analyticsInfo = jQuery('.twbb-ga-data-save.twbb-ai-front-open').attr('data-info');
        window.parent.analyticsDataPush(analyticsAction, analyticsEventName, analyticsInfo);
    }
    if( action == 'change_tone') {
        let tone = jQuery(that).data("value");
        params = {"text": selectedText, "tone": tone};
    } else if(action == 'translate_to') {
        let language = jQuery(that).data("value");
        params = {"text": selectedText, "language": language};
    } else {
        params = {"text": selectedText}
    }
    let ob;
    let front_ai = true;
    if( typeof window.parent.restRequestInstance == 'function' ) {
        ob = window.parent.restRequestInstance("builder/" + action, params, "POST", function (success) {
            let output = success['data']['output'];
            write_with_ai_tool.addResult(output);
            write_with_ai_tool.showHideFrontLoading(0, that);
        }, function (err) {
            write_with_ai_tool.showHideFrontLoading(0, that);
        }, function (err) {
            write_with_ai_tool.showHideFrontLoading(0, that);
        });
        ob.twbb_send_rest_request(front_ai);
    }
}
