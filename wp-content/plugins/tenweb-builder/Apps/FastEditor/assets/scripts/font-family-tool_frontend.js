class TWBB_FONT_FAMILY_TOOL extends TWBB_DROPDOWN_SELECT_TOOL {
    constructor() {
        super();
        self.tool_control = '';
        this.settings_to_disable_global = {typography_typography: ''}
    }

    open_editor_command() {
        this.setToolControl();
    }

    setRenderValue() {
        let self = this;
        if (self.tool_control) {
            let tool_value = self.getAppliedSettingValue(self.tool_control);
            this.setSelectActiveElement(this.getToolsContainer(), tool_value)
        }
        setFontFamilyOnScroll(self);
    }

    registerEvents() {
        let self = this;

        jQuery(document).on('click', '.twbb-font-family-tool-container.twbb-fe-select-tool', function (e) {
            if (!self.tool_control) {
                // case when we add new widget (e.g. text editor)
                self.setToolControl();
            }

            // if clicked to input element
            if (e.target.classList.contains('.twbb-select-tool-search-input') || e.target.getAttribute('id') === 'twbb-select-input') {
                return;
            }

            self.setActiveToolData(jQuery(this));
            self.onToolClickDelay(jQuery(this))
            self.dataPush(jQuery(this));
        });

        jQuery(document).on('click', '.twbb-font-family-tool-container.twbb-fe-select-tool .twbb-fe-dropdown li', function (e) {
            let settings = {
                [self.tool_control]: jQuery(this).text(),
                typography_typography: 'custom'
            };

            self.changeWidgetSetting(self.tool_control, settings)
        });
    }


    onToolClickDelay(tool) {
        let self = this;

        let run_delayed = true;
        self.view.on('before:render', function () {
            run_delayed = false;
        });

        setTimeout(function () {
            if (run_delayed) {
                self.onToolClick(tool);
            }
        }, 50);
    }

    onToolClick(tool) {
        super.onToolClick(tool);

        this.setRenderValue();
        this.setActiveToolData(tool);

        selectToolClick(tool);
    }

    scrollStopDetection() {
        let self = this;
        setTimeout( function() {
            self.enqueueFontsInView();
        }, 100 );
    }

    enqueueFontsInView() {
        let previewContainer = jQuery( '.twbb-font-family-tool-container .twbb-fe-dropdown:not(.twbb-select-tool-search-input):visible' );
        if( previewContainer ) {
            const containerOffset = previewContainer.offset(),
                top = containerOffset.top,
                bottom = top + previewContainer.innerHeight(),
                fontsInView = [];

            previewContainer.find('li:visible').each(function (index, font) {
                const $font = jQuery(font),
                    offset = $font.offset();
                if (offset && offset.top > top && offset.top < bottom) {
                    fontsInView.push($font);
                }
            });

            fontsInView.forEach(function (font) {
                const fontFamily = jQuery(font).find('span').html();
                elementor.helpers.enqueueFont(fontFamily);
            });
        }
    }

    setToolControl(){
        this.tool_control = this.getToolsContainer().find('.twbb-font-family-tool-container').attr('data-control');
    }

    collectLi(options) {
        let script = '';
        jQuery.each(options['value'], function (key, value) {
            if ( key === 'no-result' ) {
                script += '<li class="twbb-select-no-results">' + value + '</li>';
            } else {
                script += '<li><span style="font-family: ' + value + '">' + value + '</span></li>';
            }
        })

        return script;
    }
}

let font_family_tool;
jQuery(document).on('ready', function () {
    font_family_tool= new TWBB_FONT_FAMILY_TOOL();
    window['font_family_tool'] = font_family_tool;
    font_family_tool.init();
});

function setFontFamilyOnScroll(font_family_tool) {
    let setScrollAction = null;
    setScrollAction = setInterval( function() {
        let tool_container = font_family_tool.getToolsContainer();

        if( tool_container.find('.twbb-font-family-tool-container .twbb-fe-dropdown').length > 0 ) {
            tool_container.find('.twbb-font-family-tool-container .twbb-fe-dropdown').on( 'scroll', function() {
                font_family_tool.scrollStopDetection();
            } );

            clearInterval( setScrollAction );
        }
    }, 500 );
}