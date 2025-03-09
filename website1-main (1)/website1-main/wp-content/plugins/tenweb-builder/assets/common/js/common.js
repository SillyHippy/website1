var start_data;
var end_data;
var diff_data={};
var loaded_templates;
var smallBG = true;

jQuery(document).ready(function () {
  jQuery("#wp-admin-bar-elementor_edit_page .ab-sub-wrapper").remove();

  jQuery("#template_popup_container select").on("click",function () {
    if(jQuery(this).hasClass("opened")) {
      jQuery(this).removeClass("opened");
    } else {
      jQuery(this).addClass("opened");
    }
  });

  /*Edit button*/
    if(twbb_options.is_post_template == 1){
        jQuery(".template_popup").addClass("template");
        jQuery(".template_popup h2#website_structure, .template_popup #website_structure_content").addClass("active");
    }
    else {
        jQuery(".template_popup").addClass("page");
    }

    if(!twbb_options.track_publish_button){
    jQuery(document).on('click', '#elementor-editor-wrapper-v2 .MuiGrid-root:nth-child(3) > div:eq(1)', function(){
      setTimeout(function() {
        jQuery.ajax({
          type: "POST",
          url: twbb_options.track_publish_ajax,
          data: {},
          beforeSend: function (xhr) {
            xhr.setRequestHeader('X-WP-Nonce', twbb_options.rest_nonce);
          }
        }).done(function (data) {
          //pass
        });
      }, 50);
    });
  }

    if (typeof twbb_editor !== "undefined" && twbb_editor.twbb_template_type != "" && twbb_editor.twbb_template_type != "false") {
        jQuery('body').addClass(twbb_editor.twbb_template_type + '_template' + " twbb_template");
    }

  /* Template edit button Show/Hide */
  jQuery( "div[data-elementor-type='twbb_header'], div[data-elementor-type='twbb_single'], div[data-elementor-type='twbb_archive'], div[data-elementor-type='twbb_footer']" ).hover(function() {
      if( self == top || twbb_options.is_post_template == '1' || jQuery(this).find(".edit_templ").length == 0 ) { /* Check if the parent has iframe */
        return;
      }
      jQuery(this).css("position","relative");
      jQuery(this).find( ".edit_templ" ).show();
      jQuery(this).addClass("twbb_template-border");
  }, function() {
      if( self == top || twbb_options.is_post_template == '1' || jQuery(this).find(".edit_templ").length == 0 ) { /* Check if the parent has iframe */
        return;
      }
    jQuery(this).find( ".edit_templ" ).hide();
      jQuery(this).removeClass("twbb_template-border");
    }
  );

   /* Popup Show template section click */
  jQuery(document).on("click", ".edit_templ_button", function (e) {
    if( self == top ) { /* Check if the parent has iframe */
      return;
    }
    var loaded_tmp = twbb_options.loaded_templates;
    Object.keys(loaded_tmp).forEach( function(k){
      jQuery('#'+k+'_template').val(loaded_tmp[k]); /* Set current to select */
      if(jQuery('#'+k+'_template').length) {
        var edit_href = jQuery('#' + k + '_template').closest(".template_row").find('.edit_template_global').attr('href').replace('{post_id}', loaded_tmp[k]);
        jQuery('#' + k + '_template').closest(".template_row").find('.edit_template_global').attr('href', edit_href);
      }
    });
    var template = (jQuery(this).parent().data("template") != "") ? jQuery(this).parent().data("template") : "twbb_header";

    jQuery("#twbb_header_container, #twbb_footer_container, #twbb_single_container, #twbb_archive_container").hide();
    /* hide select open arrow icon */
    jQuery("#" + template + "_container #" + template + "_template").attr("class", "");
    /* If template has only one template hide arrow icons */
    if(jQuery("#" + template + "_container #" + template + "_template").data("single") == '1') {
      jQuery("#" + template + "_container #" + template + "_template").css({'background':'none'});
      jQuery("#" + template + "_container #" + template + "_template").attr("disabled","disabled");
    }

    jQuery(".template_popup.page_layout, #" + template + "_container").show();

    start_data = {
      "header_template": jQuery("#twbb_header_template").val(),
      "single_template": jQuery("#twbb_single_template").val(),
      "archive_template": jQuery("#twbb_archive_template").val(),
      "footer_template": jQuery("#twbb_footer_template").val(),
    };
    jQuery("#template_popup_container .template_select select").each(function () {
      jQuery(this).attr("data-current", jQuery(this).val());
    });

  });

  jQuery(document).on("click", ".twbb_nav_footer_menu, .twbb_nav_header_menu", function (e) {
    var loaded_tmp = twbb_options.loaded_templates;
    var iframe = jQuery("#elementor-preview-iframe").contents();
    Object.keys(loaded_tmp).forEach( function(k){

      iframe.find('#'+k+'_template').val(loaded_tmp[k]); /* Set current to select */
      var edit_href = iframe.find('#'+k+'_template').closest(".template_row").find('.edit_template_global').attr('href').replace('{post_id}',loaded_tmp[k]);
      iframe.find('#'+k+'_template').closest(".template_row").find('.edit_template_global').attr('href', edit_href);
    });
    var template = (jQuery(this).parent().data("template") != "") ? jQuery(this).parent().data("template") : "twbb_header";

    /* Check if template is created */
    if(!iframe.find("#" + template + "_container #"+template+"_template option").length) {
      iframe.find("#twbb_header_container, #twbb_footer_container, #twbb_single_container, #twbb_archive_container").hide();
      iframe.find("#" + template + "_container #" + template + "_template").attr("class", "");
      iframe.find(".template_popup.page_layout, #" + template + "_container .template_row").css({'display':'none'});
      iframe.find(".template_popup.page_layout, #" + template + "_container").css({'height':'unset'});
      iframe.find(".template_popup.page_layout, #" + template + "_container").show();
      jQuery(".twbb_sub_menu").hide();
      return;
    } else if( !( template in loaded_tmp ) ) {
      //Removed an existing one to be sure the newly created will be selected.
      iframe.find("#" + template + "_container #"+template+"_template").find("option[value=0]").remove();
      iframe.find("#" + template + "_container #"+template+"_template").prepend('<option selected value="0">Choose template</option>');
    }

    iframe.find("#twbb_header_container, #twbb_footer_container, #twbb_single_container, #twbb_archive_container").hide();
    iframe.find("#" + template + "_container #" + template + "_template").attr("class", "");
    if(iframe.find("#" + template + "_container #" + template + "_template").data("single") == '1' && iframe.find("#" + template + "_container #" + template + "_template").val() != '0' ) {
      iframe.find("#" + template + "_container #" + template + "_template").addClass('single');
      iframe.find("#" + template + "_container #" + template + "_template").attr("disabled","disabled");
      iframe.find("#" + template + "_container #" + template + "_template").css({'background':'none'});

    }
    iframe.find(".template_popup.page_layout, #" + template + "_container").show();
    if( self == top ) {
      start_data = {
        "header_template": jQuery("#elementor-preview-iframe").contents().find("#twbb_header_template").val(),
        "single_template": jQuery("#elementor-preview-iframe").contents().find("#twbb_single_template").val(),
        "archive_template": jQuery("#elementor-preview-iframe").contents().find("#twbb_archive_template").val(),
        "footer_template": jQuery("#elementor-preview-iframe").contents().find("#twbb_footer_template").val(),
      };
    } else {
      start_data = {
        "header_template": jQuery("#twbb_header_template").val(),
        "single_template": jQuery("#twbb_single_template").val(),
        "archive_template": jQuery("#twbb_archive_template").val(),
        "footer_template": jQuery("#twbb_footer_template").val(),
      };

    }
    jQuery("#template_popup_container .template_select select").each(function () {
      jQuery(this).attr("data-current", jQuery(this).val());
    });
    jQuery(".twbb_sub_menu").hide();
  });

  jQuery(document).on("click",".add-template-link", function(){
    window.open(jQuery(this).attr("href"));
  });
  /* Show save button in popup */
  jQuery(document).on("change", "#template_popup_container .template_select select", function (e) {
    if(!jQuery(this).hasClass("active")){
      jQuery(this).parent().find(".edit_template_global").hide();
      jQuery(this).closest(".template_row").find(".template_select").addClass("active");
    }

    /* return edit button when select changing to start position */
    if(jQuery(this).attr("data-current") == jQuery(this).val()) {
      jQuery(this).closest(".template_row").find(".template_select").removeClass("active");
      if(jQuery(this).val() != 0) {
        jQuery(this).parent().find(".edit_template_global").show();

      }
    }

  });

  /* Save page templates action from popup */
  jQuery(document).on("click", "#twbb_popup_save", function () {
    jQuery(".twbb-save-popup-loader").css("display","inline-block");
    if( self == top ) {
      end_data = {
        "header_template" : jQuery("#elementor-preview-iframe").contents().find("#twbb_header_template").val(),
        "single_template" : jQuery("#elementor-preview-iframe").contents().find("#twbb_single_template").val(),
        "archive_template" : jQuery("#elementor-preview-iframe").contents().find("#twbb_archive_template").val(),
        "footer_template" : jQuery("#elementor-preview-iframe").contents().find("#twbb_footer_template").val(),
      };
    } else {
      end_data = {
        "header_template" : jQuery("#twbb_header_template").val(),
        "single_template" : jQuery("#twbb_single_template").val(),
        "archive_template" : jQuery("#twbb_archive_template").val(),
        "footer_template" : jQuery("#twbb_footer_template").val(),
      };
    }
    Object.keys(end_data).forEach(function(k){
      if( typeof start_data == 'undefined' ) {
        start_data = window.parent.start_data;
      }
      if( start_data[k] != end_data[k] ) {
          diff_data[k] = end_data[k];
      }
    });
    start_data = new Object;
    data = diff_data;
    data['current_post_id'] = twbb_options.post_id;
    data['task'] = 'save_popup';
    data['page_type'] = twbb_options.twbb_page_type;
    jQuery.ajax({
      type: "GET",
      url: twbb_options.popup_template_ajax,
      data: data,
      beforeSend: function (xhr) {
        xhr.setRequestHeader('X-WP-Nonce', twbb_options.rest_nonce);
      }
    }).done(function (data) {
      elementor.reloadPreview();
    }).fail(function (data) {
      alert('Failed');
    });
  });


  /* Close/Hide popup */
  jQuery(document).mouseup(function (e){
    var container = jQuery("#template_popup_container,#template_popup_container, .twbb-condition-popup-overlay");
    if (!container.is(e.target) && container.has(e.target).length === 0) {
      jQuery(".template_popup").hide();
      jQuery(".template_popup.page_layout").hide();
      jQuery(".template_popup.website_structure").hide();
    }
  });

  jQuery(document).on("click", "#template_popup_container .close_popup, #template_popup_container .close_popup", function (e) {
      jQuery(".template_popup.page_layout").hide();
      jQuery(".template_popup.website_structure").hide();
  });

  /* Edit current template from popup */
  jQuery(document).on("click", ".edit_template_global, .edit_template, .structure-section-edit, .more a", function () {
      if(jQuery(this).closest("body.elementor-editor-active").length){
          window.open( jQuery(this).attr("href"), "_blank" );
      }
  });

  /* Show finder popup */
  jQuery(document).on("click", ".twbb_finder", function () {
    if ( 'function' == typeof( parent.elementorCommon.finder.getLayout ) ) {
      parent.elementorCommon.finder.getLayout().showModal();
    }
    else {
      parent.$e.route('finder');
    }
    jQuery("#elementor-preview-iframe").contents().find(".template_popup").hide();
  });

  if( self != top ) {
    window.parent.twbb_options.loaded_templates = twbb_options.loaded_templates;
  }
});

jQuery (window).on('elementor:loaded', function () {
  elementor.on('preview:loaded', function () {
    var iframe = jQuery("#elementor-preview-iframe").contents();

    /* Hide Custom Header if Panel closed */
    jQuery("#elementor-mode-switcher").on("click", function () {
      if ( !jQuery("body").hasClass("elementor-editor-preview") ) {
        jQuery("#twbb_custom_header").hide();
        jQuery("#elementor-panel-header-title img").hide();
      } else {
        jQuery("#twbb_custom_header").show();
        jQuery("#elementor-panel-header-title img").show();
      }
    });

  /*--Move header from iframe --*/
  var html = iframe.find("#twbb_custom_header").wrap('<p/>').parent().html();
  iframe.find("#twbb_custom_header").unwrap();
  iframe.find("#twbb_custom_header").remove();
  // Remove from top as well as header is being recreated each time the preview is loaded.
  jQuery("#twbb_custom_header").remove();
  jQuery("#elementor-responsive-bar").after(html);

  if( twbb_options.header_button != 'header_footer' ) {
    jQuery("#twbb_custom_header .header_footer").hide();
    if (twbb_options.header_button == 'condition') {
      jQuery("#twbb_custom_header .advanced").show();
    }
  }
  jQuery("#twbb_custom_header").show();


    jQuery('.advanced').click(  function (e) {
      jQuery('.twbb-condition-popup-overlay').show();
      if (conditions_added === false) {
        saved_conditions_length = twbb_editor.conditions.length;

        if (saved_conditions_length > 0) {
          show_popup_loading();
        }

        for (var i in twbb_editor.conditions) {
          add_condtion_html(twbb_editor.conditions[i]);
        }
        conditions_added = true;
      }
    });

    jQuery(document).on("click",".elementor-templates-modal__header__close .eicon-close",function(){
      iframe.find(".template_popup.twbb_finder_popup_layout").hide();
    });

    jQuery(document).on("click", "#elementor-finder__modal", function(e) {
      if(!jQuery(e.target).closest(".dialog-widget-content.dialog-lightbox-widget-content.ui-draggable.ui-draggable-handle").length) {
        iframe.find(".template_popup.twbb_finder_popup_layout").hide();
      }
    });


    var width = jQuery("#elementor-preview").width();
    responsive_css_header( width );
    preview_resize();

  /* Scroll using perfect-scrollbar.js library which include elementor */
  var ps = '';
  if ( typeof PerfectScrollbar != "undefined" && jQuery('.twbb_sub_menu_cont .twbb_sub_menu').length ) {
    ps = new PerfectScrollbar('.twbb_sub_menu_cont .twbb_sub_menu',{
      suppressScrollX: true
    });
  }

    var is_current = jQuery("#twbb_custom_header .website_structure .twbb_sub_menu .title_container .current").length;
    /**/
    jQuery( "#twbb_custom_header .website_structure .twbb_sub_menu_cont .twbb_sub_menu .title").each( function() {
      var title_container = jQuery(this).find(".title_container");
      if( (title_container.hasClass('opened') && !is_current) || jQuery(this).find(".current").length) {
        title_container.removeClass('closed');
        title_container.addClass('opened');
        title_container.parent().addClass('active');
        title_container.parent().find(".twbb-widget-icon.twbb-arrow-down").removeClass("twbb-arrow-down").addClass("twbb-arrow-up");
        is_current = true;
      } else {
        title_container.addClass('closed');
        title_container.removeClass('opened');
        title_container.parent().removeClass('active');
        title_container.parent().find(".twbb-widget-icon.twbb-arrow-up").removeClass("twbb-arrow-up").addClass("twbb-arrow-down");
      }
    });

    if( !is_current ) {
      jQuery("#twbb_custom_header .website_structure .twbb_sub_menu_cont .twbb_sub_menu .title .title_container").first().removeClass('closed').addClass('opened');
    }

    /* open/close sections in Website Structure menu */
    jQuery("#twbb_custom_header .website_structure .twbb_sub_menu_cont .twbb_sub_menu .title>label").on("click", function() {
      if(jQuery(this).parent().find(".title_container").hasClass('opened')) {
        jQuery(this).parent().find(".title_container").removeClass('opened');
        jQuery(this).parent().find(".title_container").addClass('closed');
        jQuery(this).parent().removeClass('active');
        jQuery(this).parent().find(".twbb-widget-icon.twbb-arrow-up").removeClass("twbb-arrow-up").addClass("twbb-arrow-down");
      } else {
        jQuery(this).parent().find(".title_container").removeClass('closed');
        jQuery(this).parent().find(".title_container").addClass('opened');
        jQuery(this).parent().addClass('active');
        jQuery(this).parent().find(".twbb-widget-icon.twbb-arrow-down").removeClass("twbb-arrow-down").addClass("twbb-arrow-up");

      }
      /* Perfect scroll */
      if(ps != '') {
        ps.update();
      }
    });

    /* Disable preview icon if template */
    if( twbb_options.is_post_template == 1 ) {
      jQuery("#elementor-panel-footer-saver-preview, #elementor-panel-footer-saver-preview-label").on("click", function () {
        return false;
      });

      jQuery("#elementor-panel-footer-saver-preview").css({
        "opacity":"0.2",
        "cursor":"default"
      });

      jQuery("#elementor-panel-footer-saver-preview").attr("data-tooltip","");
    }
  });
});

jQuery( window ).on( 'elementor:init', function() {
  /* Hide 'Have a look message.' */
  if( twbb_options.is_post_template == 1 ) {
    elementor.saver.on( 'page:status:change', function () {
      setTimeout( function () {
        jQuery('#elementor-toast').hide();
        elementor.notifications.getToast().hide();
      } );
    } );
  }

} );

jQuery(window).on('resize', function(){
  var width = jQuery("#elementor-preview").width();
  responsive_css_header( width )
});

function twbb_add_widget(name, widget) {

  if (typeof twbb_widgets[name] === "undefined") {
    twbb_widgets[name] = [];
  }
  twbb_widgets[name].push(widget);
}

function twbb_get_widgets(name) {
  if (typeof twbb_widgets[name] === "undefined") {
    return [];
  } else {
    return twbb_widgets[name];
  }
}

function twbb_is_widget_added(name) {
  var previewIframe = jQuery('#elementor-preview-iframe').contents();
  return (jQuery('.elementor-widget-' + name).length > 0 || previewIframe.find('.elementor-widget-' + name).length > 0);
}

function preview_resize() {
  /* -- Header & Footer menu submenu show/hide -- */
  jQuery(".twbb_nav li").on("mouseover", function() {
      jQuery(".twbb_nav .twbb_sub_menu, .twbb_nav .twbb_sub_menu_cont").hide();
      jQuery(this).find(".twbb_sub_menu").show();
      jQuery(this).find(".twbb_sub_menu_cont").show();
  }).mouseleave(function() {
      jQuery(this).find(".twbb_sub_menu").hide();
      jQuery(this).find(".twbb_sub_menu_cont").hide();
    });
  jQuery(".twbb_upgrade_for_trial_users").on("mouseover", function() {
    jQuery(this).find(".twbb_upgrade_submenu").show();
  }).mouseleave(function() {
    jQuery(this).find(".twbb_upgrade_submenu").hide();
  });

  var clicking = false;
  jQuery('.ui-resizable-handle').mousedown(function(){
    clicking = true;
  });
  jQuery(document).mouseup(function(){
    clicking = false;
  });

  jQuery(document).mousemove('.ui-resizable-handle',function(){
    if(clicking == false) return;
    // Mouse click + moving logic here
    var width = jQuery("#elementor-preview").width();
    responsive_css_header( width );
  });
}

var resp_status = {'default': false, 'default_small': false, '900': false, '950': false, '1150': false, '1250': false, '1370': false};

function reset_resp_status( resp_status, current_key ) {
  for (var key in resp_status) {
    if( key != current_key ) {
      resp_status[key] = false;
    } else {
      resp_status[key] = true;
    }
  }
}
function responsive_css_header( width ) {

  /* During the window load width return null */
  if( !width ) {
    width = 1300;
  }

  if ( width < 1150) {
    if ( !resp_status['default_small'] ) {
      reset_resp_status(resp_status, 'default_small');
      jQuery("#twbb_custom_header .twbb_dashboard a").css({"margin": "0 20px 0 10px"});
      jQuery("#twbb_custom_header .twbb_upgrade_for_trial_users").css({"padding-right": "10px"});
      jQuery("#twbb_custom_header .twbb_nav li").css("margin-right", "10px");
      jQuery("#twbb_custom_header .twbb_nav .twbb_sub_menu .title_container li a").css({"padding": "0px 70px 0 10px", "overflow-wrap": "break-word"});
      jQuery("#twbb_custom_header a").css({"font-size": "12px"});
      jQuery("#twbb_custom_header label").css({"font-size": "12px"});
      jQuery("#twbb_custom_header #twbb_website_structure").css({"padding": "0 25px"});
    }
  }

  if ( width < 900 ) {
      if ( !resp_status['900'] ) {
        reset_resp_status(resp_status, '900');
        jQuery("#twbb_custom_header .nav_prev_next").hide();
        jQuery("#twbb_custom_header .twbb_watch_video").hide();
        jQuery(".twbb_finder").find("label").hide();

      }
  } else if ( width < 950 ) {
      if ( !resp_status['950'] ) {
        reset_resp_status(resp_status, '950');
        jQuery(".nav_prev_next").hide();
        jQuery("#twbb_custom_header .twbb_watch_video").show();
        jQuery("#twbb_custom_header #display_finder").show();
        jQuery(".twbb_finder").find("label").show();
      }
  } else if ( width < 1150 ) {
      if ( !resp_status['1150'] ) {
        reset_resp_status(resp_status, '1150');
        jQuery(".nav_prev_next").show();
        jQuery("#twbb_custom_header .twbb_nav li").css("margin-right", "10px");
        jQuery("#twbb_custom_header a").css({"font-size": "12px"});
        jQuery("#twbb_custom_header #twbb_website_structure").css({"padding": "0 25px"});
        jQuery("#twbb_custom_header .twbb_dashboard a").css({"margin": "0 20px 0 15px"});
        jQuery("#twbb_custom_header .twbb_upgrade_for_trial_users").css({"padding-right": "15px"});
      }
  } else if ( width < 1250 ) {
      if ( !resp_status['1250'] ) {
        reset_resp_status(resp_status, '1250');
        jQuery(".twbb_finder").find("label").show();
        jQuery(".nav_prev_next").hide();
        jQuery("#twbb_custom_header .twbb_nav li").css("margin-right", "10px");
        jQuery("#twbb_custom_header a").css("font-size", "12px");
        jQuery("#twbb_custom_header .twbb_dashboard a").css({"font-size": "12px", "line-height": "18px"});
        jQuery("#twbb_custom_header label").css("font-size", "12px");
      }
  } else if ( width < 1370 ) {
      if ( !resp_status['1370'] ) {
        reset_resp_status(resp_status, '1370');
        jQuery("#twbb_custom_header .twbb_dashboard a").css({"margin": "0 20px 0 20px"});
        jQuery("#twbb_custom_header .twbb_nav li").css("margin-right", "10px");
        jQuery("#twbb_custom_header #display_finder").show();
        jQuery(".twbb_finder").find("label").show();
        jQuery(".nav_prev_next").show();
      }
  } else {
    if ( !resp_status['default'] ) {
      reset_resp_status(resp_status, 'default');
      jQuery(".twbb_finder").find("label").show();
      jQuery("#twbb_custom_header #display_finder").show();
      jQuery("#twbb_custom_header a").css({"font-size": "12px"});
      jQuery("#twbb_custom_header .twbb_dashboard a").css({"font-size": "12px", "line-height": "18px"});
      jQuery("#twbb_custom_header .website_structure .title_container a").css({"font-size": "14px"});
      jQuery("#twbb_custom_header .website_structure .twbb_sub_menu li a.view_more").css({"font-size": "12px"});
      jQuery("#twbb_custom_header label").css({"font-size": "12px"});
      jQuery("#twbb_custom_header .twbb_dashboard a").css({"margin": "0px 30px 0 20px"});
      jQuery("#twbb_custom_header .twbb_upgrade_for_trial_users").css({"padding-right": "20px"});
      jQuery("#twbb_custom_header .twbb_nav .twbb_sub_menu .title_container li a").css({"padding": "0px 70px 0 10px", "overflow-wrap": "break-word"});
      jQuery("#twbb_custom_header #twbb_website_structure").css({"padding": "0 25px"});
      jQuery("#twbb_custom_header .twbb_nav li.header_footer, #twbb_custom_header .twbb_nav li.advanced").css("margin-right", "20px");
    }
  }
}
