jQuery(document).ready(function() {
    let templ = jQuery(document).find("#twbb-topbar-domain-template").html();
    jQuery("#elementor-preview").prepend(templ);


    jQuery(document).on("click", ".twbb-topbar-domain-connect:not(.twbb-topbar-domain-connect-demo)", function() {
        let action = 'Connect your domain';
        let eventName = 'Editor Action';
        let info = 'AI Builder Interface';
        window.analyticsDataPush ( action, eventName, info );
    })
})