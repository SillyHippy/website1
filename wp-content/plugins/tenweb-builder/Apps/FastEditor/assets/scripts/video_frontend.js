class TWBB_VIDEO_TOOL extends FE_TOOL_FRONTEND {

    constructor() {
        super();
        self.tool_control = '';
        self.tool_source_control = '';
    }

    init() {
        super.init();
    }

    open_editor_command() {
        let self = this;
        let tools_container = this.getToolsContainer();

        self.tool_control = tools_container.find('.twbb-video-tool-container').attr('data-control');
        self.tool_source_control = 'video_type';
        if( self.tool_control ) {
            self.video_source = self.model.getSetting(self.tool_source_control);
            self.url_control = self.video_source+'_'+self.tool_control;
            self.video_url = self.model.getSetting(self.url_control);

            tools_container.find(".twbb-video-tool-url").val(self.video_url);
            tools_container.find(".twbb-fe-selected-display").text(self.video_source);
        }
    }



    registerEvents() {

        let self = this;
        jQuery(document).on( "click", ".twbb-video-tool img.twbb-video-tool-upload", function() {
            self.open_editor_command();
            let content = jQuery(this).closest(".twbb-video-tool-container").find(".twbb-video-tool-content");
            let content_to_show = jQuery(this).closest(".twbb-video-tool-container").find(".twbb-video-link-tool-content");
            let twbb_video_source_tool_content = jQuery(this).closest(".twbb-video-tool-container").find(".twbb-video-source-tool-content");
            twbb_video_source_tool_content.hide();
            jQuery(this).closest('.twbb-video-tool-container').find('.twbb_active_popup').removeClass('twbb_active_popup');
            if( !content_to_show.is(":visible") ) {
                self.setCurrentVideoValues();
                content_to_show.show();
                jQuery(this).closest('.twbb-video-tool-cont-icon').addClass('twbb_active_popup');
            } else {
                jQuery(this).closest('.twbb-video-tool-cont-icon').removeClass('twbb_active_popup');
                content_to_show.hide();
            }
        })
        jQuery(document).on( "click", ".twbb-video-tool img.twbb-video-tool-link", function() {
            self.open_editor_command();
            let content = jQuery(this).closest(".twbb-video-tool-container").find(".twbb-video-tool-content");
            let content_to_show = jQuery(this).closest(".twbb-video-tool-container").find(".twbb-video-source-tool-content");
            let twbb_video_link_tool_content = jQuery(this).closest(".twbb-video-tool-container").find(".twbb-video-link-tool-content");
            twbb_video_link_tool_content.hide();
            jQuery(this).closest('.twbb-video-tool-container').find('.twbb_active_popup').removeClass('twbb_active_popup');
            if( !content_to_show.is(":visible") ) {
                self.setCurrentVideoValues();
                content_to_show.show();
                jQuery(this).closest('.twbb-video-tool-cont-icon').addClass('twbb_active_popup');
            } else {
                content_to_show.hide();
                jQuery(this).closest('.twbb-video-tool-cont-icon').removeClass('twbb_active_popup');
            }
        })

        jQuery(document).on('click', 'body , .elementor-widget-container, .elementor-element', function (e) {
            if ( e.target.closest(".twbb-video-tool-container") === null ) {
                let twbb_video_tool_content = jQuery(document).find(".twbb-video-tool-content");
                twbb_video_tool_content.hide();
                twbb_video_tool_content.closest('.twbb-video-tool-container').find('.twbb_active_popup').removeClass('twbb_active_popup')
            }
        });

        jQuery(document).on('click', '.twbb-video-tool-content .twbb-fe-select-tool', function () {
            self.onToolClick(jQuery(this));
        });

        jQuery(document).on('click', '.twbb-video-tool-content .twbb-fe-select-tool .twbb-fe-dropdown li', function () {
            let container, value, settings;
            container = self.container;
            value = jQuery(this).attr('data-action');
            settings = {
                [self.tool_source_control]: value,
            };

            self.video_source = value;
            self.url_control = value + '_' + self.tool_control;
            self.video_url = self.model.getSetting(self.url_control);
            let tools_container = self.getToolsContainer();
            if( self.video_source == 'hosted' || self.video_source == 'videopress' ) {
                tools_container.find(".twbb-video-tool-url").hide();
                tools_container.find(".twbb-video-tool-link-title").hide();
                tools_container.find('.twbb-video-tool-url-container').hide();
                tools_container.find(".twbb-video-self-hosted").show();
                tools_container.find(".twbb-video-tool-file-title").show();
            } else {
                tools_container.find(".twbb-video-tool-url").show();
                tools_container.find('.twbb-video-tool-url-container').show();
                tools_container.find(".twbb-video-tool-link-title").show();
                tools_container.find(".twbb-video-self-hosted").hide();
                tools_container.find(".twbb-video-tool-file-title").hide();
            }


            self.setSetting(container, settings);
        });

        jQuery(document).on('change', '.twbb-video-tool-content .twbb-video-tool-url', function () {
            let container, value, settings;
            container = self.container;
            value = jQuery(this).val();
            settings = {
                [self.url_control]: value,
            };
            self.video_url = value;
            self.dataPush(jQuery(this));
            self.setSetting(container, settings);
        });

        jQuery(document).on('click', '.twbb-video-tool-content .twbb-video-self-hosted',function () {
            if( !jQuery(self.panel.el).find(".elementor-panel-navigation-tab.elementor-tab-control-content").hasClass("elementor-active") ) {
                jQuery(self.panel.el).find(".elementor-panel-navigation-tab").removeClass("elementor-active");
                jQuery(self.panel.el).find(".elementor-panel-navigation-tab.elementor-tab-control-content").addClass("elementor-active");
                self.panel.currentPageView.activateTab('content');
            }
            jQuery(self.panel.el).find(".elementor-control-media__tool[data-media-type='video']").trigger("click");
        });

    }

    setCurrentVideoValues() {
        let self = this;
        let tools_container = this.getToolsContainer();
        tools_container.find(".twbb-video-tool-url").val(self.video_url);
        tools_container.find(".twbb-video-tool-open-url").attr('href', self.video_url);
        let curr = tools_container.find(".twbb-fe-dropdown li[data-action='"+self.video_source+"']").text();
        curr = curr.trim();
        tools_container.find(".twbb-fe-selected-display").text(curr);
        if( self.video_source == 'hosted' || self.video_source == 'videopress' ) {
            tools_container.find(".twbb-video-tool-url").hide();
            tools_container.find(".twbb-video-tool-link-title").hide();
            tools_container.find('.twbb-video-tool-url-container').hide();
            tools_container.find(".twbb-video-self-hosted").show();
            tools_container.find(".twbb-video-tool-file-title").show();
        } else {
            tools_container.find(".twbb-video-tool-url").show();
            tools_container.find(".twbb-video-tool-link-title").show();
            tools_container.find('.twbb-video-tool-url-container').show();
            tools_container.find(".twbb-video-self-hosted").hide();
            tools_container.find(".twbb-video-tool-file-title").hide();
        }
    }

    setSetting(container, settings) {
        super.setSetting(container, settings);
    }

    onToolClick(tool) {
        selectToolClick(tool);
    }

}

let video_tool;
jQuery(document).on('ready', function () {
    video_tool= new TWBB_VIDEO_TOOL();
    window['video_tool'] = video_tool;
    video_tool.init();
});
