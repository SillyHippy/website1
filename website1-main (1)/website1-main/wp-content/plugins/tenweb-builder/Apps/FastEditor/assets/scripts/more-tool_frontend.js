class TWBB_MORE_TOOL extends FE_TOOL_FRONTEND {
    init() {
        super.init();
    }
    registerEvents() {
        let self = this;
        jQuery(document).on('click', '.twbb-more-tool', function (event) {
            if(jQuery(this).hasClass('twbb-more-tool-active')){
                jQuery(this).removeClass('twbb-more-tool-active');
            }else{
                jQuery(this).addClass('twbb-more-tool-active');
            }
            self.onToolClick(jQuery(this),event, this);
            self.dataPush(jQuery(this));
        });
        jQuery(document).on('click', 'body , .elementor-widget-container, .elementor-element', function (event) {
            var target = jQuery( event.target );
            if( !target.is('.twbb-more-tool') && window.parent.jQuery('.dialog-widget.dialog-simple-widget.elementor-context-menu').length > 0
                &&  window.parent.jQuery('.dialog-widget.dialog-simple-widget.elementor-context-menu').hasClass('twbb-contextmenu-opened') ) {
                window.parent.jQuery('.dialog-widget.dialog-simple-widget.elementor-context-menu').removeClass('twbb-contextmenu-opened');
                jQuery('.twbb-more-tool').removeClass('twbb-more-tool-active');
            }
        });
        jQuery(window).on('resize', function() {
            if(window.parent.jQuery('body').hasClass('twbb-sg-sidebar-opened')){
                jQuery('.twbb-fast-editor-tools-container').addClass('twbb_zoom');
            }else{
                jQuery('.twbb-fast-editor-tools-container').removeClass('twbb_zoom');
            }
        });
    }

    onToolClick(tool,event,_this=null) {
        if( window.parent.jQuery('.dialog-widget.dialog-simple-widget.elementor-context-menu').length > 0
            &&  window.parent.jQuery('.dialog-widget.dialog-simple-widget.elementor-context-menu').hasClass('twbb-contextmenu-opened') ) {
            window.parent.jQuery('.dialog-widget.dialog-simple-widget.elementor-context-menu').css('display') === 'none';
            window.parent.jQuery('.dialog-widget.dialog-simple-widget.elementor-context-menu').removeClass('twbb-contextmenu-opened');
            jQuery('.twbb-more-tool').removeClass('twbb-more-tool-active');
        } else {
            // let model = window.parent.$e.components.get("panel/editor").manager.currentPageView.model;
            // model.trigger('request:contextmenu', event);
            var offset = tool.offset(); // Get the top-left corner position
            var width = tool.outerWidth(); // Get the width of the div

            const mouseY = event.clientY;
            const event_object = new MouseEvent('contextmenu', {
                bubbles: true,
                cancelable: true,
                view: window,
                clientX: offset.left + width + 3,
                clientY: mouseY,
            });


            // Dispatch the event on the raw DOM element
            _this.dispatchEvent(event_object);



            window.parent.jQuery('.dialog-widget.dialog-simple-widget.elementor-context-menu').addClass('twbb-contextmenu-opened')
        }
    }
}

let more_tool;
jQuery(document).on('ready', function () {
    more_tool= new TWBB_MORE_TOOL();
    window['more_tool'] = more_tool;
    more_tool.init();
});