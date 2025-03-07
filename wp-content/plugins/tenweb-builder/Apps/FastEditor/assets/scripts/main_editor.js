jQuery(window).on('elementor:loaded', function () {
    let preview_iframe = document.querySelector("#elementor-preview-iframe");
    let $preview_iframe = jQuery(preview_iframe);

    jQuery(document).mouseup(function(e) {
        if (!$preview_iframe.is(e.target) && $preview_iframe.has(e.target).length === 0){
            if(e.target.closest("#elementor-panel-page-editor") === null) {
                preview_iframe.contentWindow.FE_TOOL_FRONTEND.deactivateTool();
            }
            preview_iframe.contentWindow.FE_TOOL_FRONTEND.deleteAllActiveToolData();
            preview_iframe.contentWindow.FE_TOOL_FRONTEND.closeAllTools();
        }
    });

    jQuery(document).on('click', '.elementor-control-responsive-switchers__holder button', function (){
        setTimeout(function (){
            preview_iframe.contentWindow.FE_TOOL_FRONTEND.changeDeviceMode();
        });
    });
});