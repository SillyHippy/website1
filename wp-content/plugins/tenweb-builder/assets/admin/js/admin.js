jQuery(document).ready(function () {
  function templates_page() {
    jQuery(document).on('change', function (e) {
      if (jQuery(e.target).attr('id') !== "elementor-new-template__form__template-type") {
        return true;
      }
      if (e.target.value == "twbb_single") {
        jQuery('#twbb-post-type-form-field').show();
      }
      else {
        jQuery('#twbb-post-type-form-field').hide();
      }
    });
  }

  jQuery( '.display_admin_condition_popup' ).on( 'click', function() {
    jQuery ( '.display_admin_condition_popup' ).removeClass( 'selected_condition' );
    jQuery( this ).addClass( 'selected_condition' );
  });
  
});
