class TWBAI {
  constructor() {
    this.cache = {};
    this.option = '';
    this.type = 'text';
    this.model = {};
    this.panel = {};
    this.view = {};
  }

  setCacheElements() {
    let self = this;
    self.cache = WriteWithAIHelper.getCacheElements();
  }

  ai_button(cont) {
    let self = this;
    let all_controls = self.cache.controls.concat(self.cache.coming_soon_controls);
    let coming_soon_controls = self.cache.coming_soon_controls.map(element => "elementor-control-" + element);
    for (let i in all_controls) {
      cont.find(".elementor-control-" + all_controls[i]).each(function () {
        if (jQuery(this).find(".twb-ai-button").length === 0) {
          let is_coming_soon = self.containsAny(jQuery(this).attr("class").split(" "), coming_soon_controls);
          let label = jQuery(this).find(".elementor-control-title");
          let button_text = "Write with AI";
          let twb_ai_button_class = "";
          if( jQuery.inArray(all_controls[i], self.cache.image_controls ) !== -1 ) {
            button_text = "Generate with AI";
            twb_ai_button_class = " twb-ai-image-button"
          }
          let button = jQuery("<button>", {
            class: "twb-ai-button" + twb_ai_button_class + ((window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches &&
                twbb_write_with_ai_data.twbb_ui_theme == 'auto' ||
                twbb_write_with_ai_data.twbb_ui_theme == 'dark') ? " twbb-ai-dark" : ""),
            "data-type": all_controls[i],
            html: jQuery(this).hasClass("elementor-label-block") ? button_text : "",
            onClick: is_coming_soon ? "" : "twbAIOb.event(this)"
          });
          // Add the button after the label.
          let layer = jQuery("<span>", {
            class: "twb-ai-button-layer",
            html: button
          });
          if( label.length > 1 ) {
            jQuery(label[0]).after(layer);
          } else {
            jQuery(label).after(layer);
          }
          // Add tooltip to the coming soon buttons.
          if (is_coming_soon) {
            let tooltip = jQuery("<div>", {
              class: "twb-ai-button-tooltip",
              html: "Coming soon",
              style: "display: none;"
            });
            jQuery(button).append(tooltip).hover(function () {
                jQuery(this).find(".twb-ai-button-tooltip").show();
              },
              function () {
                jQuery(this).find(".twb-ai-button-tooltip").hide();
              });
          }
        }
      });
    }
  }

  build() {
    let self = this;

    // On adding/editing widgets.
    elementor.hooks.addAction( 'panel/open_editor/widget', function( panel, model, view ) {
      self.model = model;
      self.panel = panel;
      self.view = view;
      self.ai_button(panel.$el);

      // On closing/opening sections.
      jQuery('#elementor-controls').on('mouseenter', function(){
        self.ai_button(jQuery("#elementor-controls"));
      });
    });

    // On adding/editing widgets.
    elementor.hooks.addAction( 'panel/open_editor/container', function( panel, model, view ) {
      self.model = model;
      self.panel = panel;
      self.view = view;
      self.ai_button(panel.$el);

      // On closing/opening sections.
      jQuery('#elementor-controls').on('mouseenter', function(){
        self.ai_button(jQuery("#elementor-controls"));
      });
    });

    // On changing between tabs/sections.
    jQuery(document).on('click', '#elementor-panel-page-editor', function(){
      self.ai_button(jQuery("#elementor-controls"));
      jQuery('.elementor-control-type-section').on('click', function(){
        self.ai_button(jQuery("#elementor-controls"));
      });
    });
  }

  event(that) {
    let self = this;
    self.option = that;
    self.type = jQuery(that).data("type");
    if( jQuery.inArray(self.type, self.cache.image_controls) !== -1 ) {
        let img_gen = new TWBIMGGEN(self.model, self.panel, self.view, self.type);
        img_gen.init();
    } else {
        let current_text = self.getSetting();

        self.show_ai_popup(current_text);
        self.use_text_click_event();
        self.new_prompt_click_event();
    }
  }

  new_prompt_click_event() {
    let self = this;
    jQuery(document).on("click", ".twbb-ai-new-prompt-button", function(){
      // GA event params
      if ( jQuery('.twbb-ai-popup-content').length > 0 ) {
        let analyticsInfo, analyticsEventName, analyticsAction;
        analyticsEventName = jQuery(this).closest('.twbb-ga-data-save').attr('data-eventName');
        analyticsAction = 'new_prompt_button';
        analyticsInfo = jQuery(this).closest('.twbb-ga-data-save').attr('data-info');
        analyticsDataPush(analyticsAction, analyticsEventName, analyticsInfo);
      }
      jQuery(".twbb-ai-error-message").hide();
      self.show_ai_popup( '' );
    });
  }

  use_text_click_event() {
    let self = this;
    jQuery(document).on("click", ".twbb-ai-use-text-button", function(){
      // GA event params
      if ( jQuery('.twbb-ai-popup-content').length > 0 ) {
        let analyticsInfo, analyticsEventName, analyticsAction;
        analyticsEventName = jQuery(this).closest('.twbb-ga-data-save').attr('data-eventName');
        analyticsAction = 'use_text';
        analyticsInfo = jQuery(this).closest('.twbb-ga-data-save').attr('data-info');
        analyticsDataPush(analyticsAction, analyticsEventName, analyticsInfo);
      }
        let selectedText = jQuery(this).closest(".twbb-ai-suggested-propmts-container").find(".twbb-ai-text").val();
        let args = self.generateSetting(selectedText);
        FastEditorHelper.setSetting(args[0], args[1]);
        self.hide_ai_popup();
    });
  }

  hide_ai_popup() {
    jQuery(".twbb-ai-popup-layout, .twbb-ai-popup-container, .twbb-ai-propmts-empty-container, .twbb-ai-text-prompts, .twbb-ai-headline-prompts, .twbb-ai-propmts-result-container").hide();
  }

  show_ai_popup( text ) {
    let self = this;
    const widgetType = self.type.indexOf("title") === -1 ? 'editor' : 'title';
    text = WriteWithAIHelper.validateOutput(text, widgetType);
    //for GA events
    jQuery(".twbb-ga-data-save").attr('data-info', self.model.attributes.widgetType );
    jQuery(".twbb-ga-data-save").attr('data-info-widget-type', widgetType );
    jQuery(".twbb-ga-data-save").attr('data-eventName', 'write with AI sidebar' );
    if ( text == '' ) {
        jQuery(document).find(".twbb-ai-description-input").val('').trigger("change");
        if( widgetType == 'editor' ) {
          jQuery(".twbb-ai-text-prompts").show();
        } else {
          jQuery(".twbb-ai-headline-prompts").show();
        }
        jQuery(".twbb-ai-propmts-result-container").hide();
        jQuery(".twbb-ai-popup-layout, .twbb-ai-popup-container, .twbb-ai-propmts-empty-container").show();
    }
    else {
        jQuery(".twbb-ai-result-textarea").val(text);
        jQuery(".twbb-ai-propmts-empty-container").hide();
        jQuery(".twbb-ai-popup-layout, .twbb-ai-popup-container, .twbb-ai-propmts-result-container").show();
    }
  }

  generateSetting(value) {

    let self = this;
    let widget_type = self.getType();
    let widget_id = window.$e.components.get("panel/editor").activeModelId;

    if ( 'object' !== typeof widget_type ) {
      var keyParts, isRepeaterKey, container, setting;
      keyParts = widget_type.split('.')
      isRepeaterKey = 3 === keyParts.length;
      container = window.$e.components.get('document').utils.findContainerById(widget_id);
      setting = widget_type;
      if (isRepeaterKey) {
        container = container.repeaters[ keyParts[0] ].children[ keyParts[1]]
        setting = keyParts[2];
      }
    }

    let settings = { [setting]: value };

    return [ container, settings ];
  }

  /**
   * Get setting by type.
   *
   * @returns {*}
   */
  getSetting() {
    let self = this;
    return self.model.getSetting(self.getType());
  }

  /**
   * Get the type of option. Difference is between dynamic options.
   *
   * @returns {string}
   */
  getType() {
    let self = this;
    let type = self.type;
    let tabsContainer = self.option.closest(".elementor-repeater-fields-wrapper");
    if ( tabsContainer != null ) {
      let childIndex = Array.prototype.indexOf.call(tabsContainer.children, self.option.closest(".elementor-repeater-fields"));
      let parentType = "";

      for (let i in self.cache.sub_controls) {
        if ( tabsContainer.closest(".elementor-control").classList.contains("elementor-control-" + self.cache.sub_controls[i]) ) {
          parentType = self.cache.sub_controls[i];
        }
      }

      type = parentType + "." + childIndex + "." + self.type;
    }

    return type;
  }

  init() {
    this.setCacheElements();
    this.build();
  }

  static hidePopupInIframe() {
    jQuery('#elementor-preview-iframe').contents().find('.twbb-ai-front').removeClass('twbb-ai-front-open');
    jQuery('#elementor-preview-iframe').contents().find('.twbb-ai-front .twbb-ai-front-button-layer').removeClass('twbb-ai-front-button-layer-visible');
    jQuery('#elementor-preview-iframe').contents().find('.twbb-ai-front .twbb-ai-front-new_prompt-container').css('display','none');
  }

  containsAny(source, target) {
    var result = source.filter(function (item) {
      return target.indexOf(item) > -1
    });
    return (result.length > 0);
  }
}

let twbAIOb;
jQuery (window).on('elementor:loaded', function () {
  twbAIOb = new TWBAI();
  twbAIOb.init();
});

jQuery(document).ready(function() {
  if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches &&
      twbb_write_with_ai_data.twbb_ui_theme == 'auto' ||
      twbb_write_with_ai_data.twbb_ui_theme == 'dark') {
    jQuery(".twbb-ai-popup-container").addClass("twbb-ai-popup-dark");
  }

  jQuery(document).on("click", ".twbb-ai-close, .twbb-ai-popup-layout", function(){
    // GA event params
    let analyticsInfo, analyticsEventName, analyticsAction;
    analyticsEventName = jQuery('.twbb-ai-popup-content.twbb-ga-data-save').attr('data-eventName');
    analyticsAction = 'close_popup';
    analyticsInfo = jQuery('.twbb-ai-popup-content.twbb-ga-data-save').attr('data-info');
    analyticsDataPush(analyticsAction, analyticsEventName, analyticsInfo);
    twbAIOb.hide_ai_popup();
  });


  jQuery(document).on("click", ".twbb-ai-suggested-propmt", function(){
    // GA event params
    if ( !jQuery(this).hasClass('twbb-ai-action-button') ) {
      let analyticsInfo, analyticsEventName, analyticsAction;
      analyticsEventName = jQuery(this).closest('.twbb-ga-data-save').attr('data-eventName');
      analyticsAction = 'suggested-prompts';
      analyticsInfo = jQuery(this).closest('.twbb-ga-data-save').attr('data-info');
      analyticsDataPush(analyticsAction, analyticsEventName, analyticsInfo);
    }
    let prompt = jQuery(this).text();
    jQuery(document).find(".twbb-ai-description-input").val(prompt).change();
  });

  jQuery(document).on("change paste keyup", ".twbb-ai-description-input", function(){
    if( jQuery(this).val() != '' ) {
      jQuery(".twbb-ai-propmts-empty-container .twbb-ai-suggested-propmts-content").hide();
    } else {
      jQuery(".twbb-ai-propmts-empty-container .twbb-ai-suggested-propmts-content").show();
    }
  });

  jQuery(document).on("click", ".twbb-ai-action-button", function(){
      let selectedText = jQuery(this).closest(".twbb-ai-suggested-propmts-container").find(".twbb-ai-text").val();
      if( selectedText == '' ) {
        let message = "Please fill out this field";
        jQuery(document).find(".twbb-ai-error-message").text(message).show();
        return;
      }
      twbb_send_request( jQuery(this), selectedText, false );
  });

  jQuery(document).on("click", ".twbb-ai-select-value", function() {
    jQuery(".twbb-ai-select-container").addClass("twbb-ai-select-closed");
    jQuery(".twbb-ai-select-options-container").hide();
    let parent = jQuery(this).closest(".twbb-ai-select-container");
    if( parent.hasClass("twbb-ai-select-closed") ) {
        parent.find(".twbb-ai-select-options-container").show();
        parent.removeClass("twbb-ai-select-closed");
    } else {
        parent.find(".twbb-ai-select-options-container").hide();
        parent.addClass("twbb-ai-select-closed");
    }
  });

  /* Close select if click on popup */
  jQuery(document).on("click", ".twbb-ai-popup-container", function(event ) {
    var target = jQuery( event.target );
    if( !target.is(".twbb-ai-select-container, .twbb-ai-select-value") ) {
      jQuery(".twbb-ai-select-options-container").hide();
      jQuery(".twbb-ai-select-container").addClass("twbb-ai-select-closed");
    }
  });
});


/** Show/hide loading
* @param show boolean
*/
function show_hide_loading( show ) {
  if( show ) {
      jQuery(".twbb-ai-loading").show();
      jQuery(".twbb-ai-popup-content").hide();
  } else {
      jQuery(".twbb-ai-loading").hide();
      jQuery(".twbb-ai-popup-content").show();
  }
}

function twbb_send_request( that, selectedText, front ) {
  if ( selectedText == "" ) {
    return false;
  }
  jQuery(document).find(".twbb-ai-error-message").hide();
  let action = jQuery(that).data("action");
  let params = {};

  // GA event params
  let analyticsInfo, analyticsEventName, analyticsAction;
  let widgetType = "text";
  analyticsAction = action;
  if( jQuery('.twbb-ai-popup-content.twbb-ga-data-save').length > 0 &&
      jQuery('.twbb-ai-popup-content.twbb-ga-data-save').is(':visible') ) {
    analyticsEventName = jQuery('.twbb-ai-popup-content.twbb-ga-data-save').attr('data-eventName');
    analyticsInfo = jQuery('.twbb-ai-popup-content.twbb-ga-data-save').attr('data-info');
    widgetType = jQuery('.twbb-ai-popup-content.twbb-ga-data-save').attr('data-info-widget-type');
    analyticsDataPush(analyticsAction, analyticsEventName, analyticsInfo);
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
  params["widget_type"] = widgetType === "title" ? "title" : "text";
  let ob;
  let front_ai = false;
  show_hide_loading(1);

  ob = new RestRequest("builder/" + action, params, "POST", function (success) {
    let output = success['data']['output'];
    twbAIOb.show_ai_popup(output);
    show_hide_loading(0);
  }, function (err) {
    show_hide_loading(0);
  }, function (err) {
    show_hide_loading(0);
  });
  ob.twbb_send_rest_request(front_ai, 'builder');

}