var plugin_status = {};
/* 1 -- installed, 2 -- activated*/
var currentElType;
/* 1 -- if other plugin already installed or activated but not refresh, 2 -- if the action is in progress */
var need_reload = 0;

var plugin_slug;


jQuery(window).on('elementor:init', function () {
    elementor.channels.editor.on('section:activated', function (sectionName, editor) {
        var editedElement = editor.getOption('editedElementView');
        var model = editedElement.getEditModel();
        var currentElementType = model.get('elType');
        if ('widget' === currentElementType) {
            currentElementType = model.get('widgetType');
        }
        currentElType = currentElementType;

        if ( need_reload == 2 ) {
          setTimeout(function () {
            jQuery('.one_click_action').parent().html("<p class='twbb_description'>" + twbb.inprogress_msg + "</p>");
          }, 0);
        } else if ( need_reload == 1 ) {
            setTimeout(function () {
                jQuery('.one_click_action').parent().html("<p class='twbb_description'>" + twbb.reload_msg + "</p>");
            }, 0);
        } else {

            if (!(currentElementType in plugin_status)) { /* if obj has key plugin_status */
                plugin_status[currentElementType] = 0;
            }
            else if (plugin_status[currentElType] == 1) { /* if installed and activated  */
                setTimeout(function () {
                    jQuery('#install_plugin').parent().html("<p class='twbb_description'>" + twbb.install_success + "</p>");
                }, 0);
            }
            else if (plugin_status[currentElType] == 2) { /* if activated  */
                setTimeout(function () {
                    jQuery('#activate_plugin').parent().html("<p class='twbb_description'>" + twbb.activate_success + "</p>");
                }, 0);
            }
            else if (plugin_status[currentElType] == 3) { /* if updated  */
                setTimeout(function () {
                    jQuery('#activate_plugin').parent().html("<p class='twbb_description'>" + twbb.update_success + "</p>");
                }, 0);
            }
        }
    });
});

jQuery(document).ready(function () {

    jQuery(document).on('click', "#install_plugin", function () {
        jQuery(this).find(".spinner").css({"display": "inline-block", "visibility": "visible"});
        plugin_slug = jQuery(this).data("slug");
        if( jQuery(this).attr('data-is_paid') != '0' ) {
          jQuery.ajax({
            type: "POST",
            url: twbb.action_endpoint,
            data: {
              action: "install-activate",
              origin: "10web",
              product_id: jQuery(this).data("id"),
              tenweb_nonce: twbb.ajaxnonce
            },
            beforeSend: function (xhr) {
              xhr.setRequestHeader('X-WP-Nonce', twbb.ajaxnonce);
              need_reload = 2;
            },
            success: function (response) {
              jQuery(this).find(".spinner").css({"display": "none", "visibility": "hidden"});
              jQuery('#install_plugin').parent().html("<p class='twbb_description'>" + twbb.install_success + "</p>");
              plugin_status[currentElType] = 1;
              need_reload = 1;
            },
            failure: function (errorMsg) {
              need_reload = 1;
            },
            error: function (error) {
              window.location.reload();
            }
          });
        } else {
          twbb_install_external_plugin_free( plugin_slug );
        }
    });

    jQuery(document).on('click', "#activate_plugin", function () {
        jQuery(this).find(".spinner").css({"display": "inline-block", "visibility": "visible"});
        var plugin_slug = jQuery(this).data("slug");
        jQuery.ajax({
            type: "POST",
            url: twbb.action_endpoint,
            data: {
                action: "activate",
                origin: "10web",
                product_id: jQuery(this).data("id"),
                tenweb_nonce: twbb.ajaxnonce
            },
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-WP-Nonce', twbb.ajaxnonce);
                need_reload = 2;
            },
            success: function (response) {
                jQuery("#plugin_status").val("1");
                jQuery(this).find(".spinner").css({"display": "none", "visibility": "hidden"});
                jQuery('#activate_plugin').parent().html("<p class='twbb_description'>" + twbb.activate_success + "</p>");
                plugin_status[currentElType] = 2;
                need_reload = 1;
            },
            failure: function (errorMsg) {
                window.location.reload();
            },
            error: function (error) {
                window.location.reload();
            }
        });
    });

    jQuery(document).on('click', "#update_plugin", function () {

        jQuery(this).find(".spinner").css({"display": "inline-block", "visibility": "visible"});
        var plugin_slug = jQuery(this).data("slug");

        jQuery.ajax({
            type: "POST",
            url: twbb.action_endpoint,
            data: {
                action: "update",
                origin: "10web",
                product_id: jQuery(this).data("id"),
                tenweb_nonce: twbb.ajaxnonce
            },
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-WP-Nonce', twbb.ajaxnonce);
                need_reload = 2;
            },
            success: function (response) {
                jQuery("#plugin_status").val("1");
                jQuery(this).find(".spinner").css({"display": "none", "visibility": "hidden"});
                jQuery('#update_plugin').parent().html("<p class='twbb_description'>" + twbb.update_success + "</p>");
                plugin_status[currentElType] = 3;
                need_reload = 1;
            },
            failure: function (errorMsg) {
                window.location.reload();
            },
            error: function (error) {
                window.location.reload();
            }
        });
    });

    function twbb_install_external_plugin_free( plugin_slug ) {
        jQuery.ajax({
            type: "POST",
            url: twbb.action_endpoint,
            data: {
                action: "install-activate",
                origin: "wp.org",
                type: "plugin",
                slug: plugin_slug,
                tenweb_nonce: twbb.ajaxnonce
            },
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-WP-Nonce', twbb.ajaxnonce);
                need_reload = 2;
            },
            success: function (response) {
                jQuery(this).find(".spinner").css({"display": "none", "visibility": "hidden"});
                jQuery('#install_plugin').parent().html("<p class='twbb_description'>" + twbb.install_success + "</p>");
                plugin_status[currentElType] = 1;
                need_reload = 1;
            },
            failure: function (errorMsg) {
                window.location.reload();
            },
            error: function (error) {
                window.location.reload();
            }
        });
    }
});
