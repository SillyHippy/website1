jQuery( window ).on( 'elementor/frontend/init', function() {
    /* Elementor toggle widget content open/close animation */
    jQuery(document).on("click", ".elementor-tab-title", function (e) {
        let that = jQuery(this);
        that.closest(".elementor-toggle-item").addClass("twbb-tab-active");

        setTimeout(function() {
            that.closest(".elementor-toggle-item").removeClass("twbb-tab-active");
        },400)

    })
});
