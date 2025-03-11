
jQuery( document ).ready(function() {

  /*
    Install tenweb builder plugin
  */
  jQuery(document).on('click', "#install_plugin", function () {
    jQuery(this).find(".spinner").css({"display": "inline-block","visibility": "visible"});

    jQuery.ajax({
      type: "POST",
      url: twbth.action_endpoint,
      data: {
        action: "install-activate",
        origin: "10web",
        product_id: jQuery(this).data("id"),
        tenweb_nonce: twbth.ajaxnonce
      },
      beforeSend: function ( xhr ) {
        xhr.setRequestHeader( 'X-WP-Nonce', twbth.ajaxnonce );
      },
      success: function (response) {
        jQuery(".spinner").css({"display" : "none","visibility" : "hidden"});
        var dismiss_url = jQuery("#dismiss_url").val();
        jQuery.post(dismiss_url);
        jQuery(".twbth.notice.notice-warning").css("display","none");
      },
      failure: function (errorMsg) {
      },
      error: function (error) {
        jQuery(".spinner").css({"display" : "none","visibility" : "hidden"});
        jQuery(".twbth_failed").show();
      }
    });
  });

  /*
    Activate  tenweb builder plugin
  */
  jQuery(document).on('click', "#activate_plugin", function () {
    jQuery(this).find(".spinner").css({"display" : "inline-block","visibility" : "visible"});

    jQuery.ajax({
      type: "POST",
      url: twbth.action_endpoint,
      data: {
        action: "activate",
        origin: "10web",
        product_id: jQuery(this).data("id"),
        tenweb_nonce: twbth.ajaxnonce
      },
      beforeSend: function ( xhr ) {
        xhr.setRequestHeader( 'X-WP-Nonce', twbth.ajaxnonce );
      },
      success: function (response) {
        jQuery(".spinner").css({"display" : "none","visibility" : "hidden"});
        var dismiss_url = jQuery("#dismiss_url").val();
        jQuery.post(dismiss_url);
        jQuery(".twbth.notice.notice-warning").css("display","none");

      },
      failure: function (errorMsg) {
        jQuery(".spinner").css({"display" : "none","visibility" : "hidden"});
      },
      error: function (error) {
        jQuery(".spinner").css({"display" : "none","visibility" : "hidden"});
      }
    });
  });

  /*
    Dismiss notification
  */
  jQuery(".twbth .dashicons-dismiss").on("click", function () {
      var dismiss_url = jQuery("#dismiss_url").val();
      jQuery.post(dismiss_url);
      jQuery(".twbth.notice.notice-warning").css("display","none");
  });

});