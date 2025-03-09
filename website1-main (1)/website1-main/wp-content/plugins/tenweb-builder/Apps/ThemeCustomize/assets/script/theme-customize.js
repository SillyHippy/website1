class ThemeCustomize {
    constructor() {
        this.from_demo = twbb_theme_customize.from_demo;
        this.active_font = twbb_theme_customize.active_font;
        this.fonts = twbb_theme_customize.fonts;
        this.active_color = twbb_theme_customize.active_color;
        this.colors = twbb_theme_customize.colors;
        this.kitEnablePopupOpened = 0;
        this.ultimateKitSaved = false;
        this.existingBodyStyles = '';
    }

    init() {
        this.registerEvents();
        /* get active theme from cookie and set during the page change in the preview */
        if( this.from_demo ) {
            this.setActiveTheme();
        }

        if ( this.getCookie("tbdemo_first_time") != 0 ) {
            this.open_customize('color');
        }

    }

    registerEvents() {
        let self = this;
        /* Tabs font/color click event */
        jQuery(document).off("click", ".twbb-theme-customize-tab")
            .on("click", ".twbb-theme-customize-tab", function() {
                let id = jQuery(this).attr("id");
                let tab_container = jQuery(this).closest(".twbb-theme-customize-container");
                jQuery(this).closest(".twbb-theme-customize-tabs").find(".twbb-theme-customize-tab").removeClass("twbb-theme-customize-tab-active");
                jQuery(this).addClass("twbb-theme-customize-tab-active");
                tab_container.find(".twbb-theme-customize-tab-content").hide();
                tab_container.find("." + id + "-content").show();
                if( id == 'twbb-theme-customize-font' ) {
                    self.open_customize('font');
                } else {
                    self.open_customize('color');
                }

            })

        jQuery(document).on("input", ".twbb-font-search-input", function() {
            let text = jQuery(this).val();
            let search_list = self.sortBySearch(text, self.fonts);
            let list_html = '';
            if( !search_list.length ) {
                list_html += '<div class="twbb-font-noresult">0 results found for "'+text+'"</div>';
            } else {
                search_list.forEach(function (item) {
                    if( item == self.active_font ) {
                        list_html += "<div class='twbb-font-active'><span style='font-family: " + item + "'>" + item + "</span></div>";
                    } else {
                        list_html += "<div><span style='font-family: " + item + "'>" + item + "</span></div>";
                    }
                })
            }
            jQuery(document).find(".twbb-font-list").empty().append(list_html);
        });

        jQuery(document).off("click", ".twbb-font-list > div")
            .on("click", ".twbb-font-list > div", function() {
                if ( jQuery(this).hasClass('twbb-font-noresult') ) {
                    return false;
                }
                self.active_font = jQuery(this).text();
                jQuery(document).find(".twbb-font-list > div").removeClass("twbb-font-active");
                jQuery(this).addClass("twbb-font-active");
                let font = jQuery(this).find('span').text();
                self.changeThemeStyle(font, 'font');
            });

        jQuery(document).off("click", ".twbb-color-item")
            .on("click", ".twbb-color-item", function() {
                self.ultimateKitSaved = false;
                window.ultimateKitSaved = false;
                //if user doesn't save the changes, we need to delete theme color and fonts inline css
                // but save his existing style tag inner css
                let iframeBody = jQuery("#tbdemo-device-iframe").contents().find("body");
                self.existingBodyStyles = iframeBody.attr('style');
                self.active_color = jQuery(this).data('pallet_id');
                jQuery(document).find(".twbb-color-item").removeClass("twbb-color-active");
                jQuery(this).addClass("twbb-color-active");
                self.changeThemeStyle(jQuery(this), 'color');
            });

        /* Demo frontend customize button click */
        jQuery(document).on("click", ".tbdemo-customize", function() {
            self.open_customize('color');
        })

        jQuery(document).on("click", ".tbdemo-device-mobile", function () {
            if ( jQuery(this).hasClass("tbdemo-device-active") ) {
                return
            }

            if( typeof WebFont != 'undefined' && self.active_font != null ) {
                let iframe = document.getElementById("tbdemo-device-iframe");
                WebFont.load({
                    google: {
                        families: [self.active_font]
                    },
                    context: iframe.contentWindow
                });
            } else if( typeof elementor != 'undefined' ) {
                elementor.helpers.enqueueFont(self.active_font, 'editor');
                elementor.helpers.enqueueFont(self.active_font);
            }

            let iframeBody = jQuery("#tbdemo-device-iframe").contents().find("body");
            if( self.active_color != null && self.active_color != "" ) {
                let kit = self.colors.filter((value) => value['id'] === self.active_color);
                if( kit.length ) {
                    kit = kit[0]['kit'];
                    if (iframeBody.length) {
                        self.changeColors(iframeBody, kit);
                    }
                }
            }
            if ( iframeBody.length && self.active_font != null ) {
                self.changeFonts(iframeBody, self.active_font);

            }
        });


        /* Elementor editor topbar button*/
        jQuery(document).on("click", ".twbb-customize-button", function() {
            if( jQuery(this).hasClass('selected') ) {
                self.close_customization();
                return;
            }
            analyticsDataPush(
                '10Web Styles',
                '10Web Styles'
            );
            if ( twbb_options.show_ultimate_kit ) {
                jQuery(document).find("#elementor-mode-switcher").hide();
                jQuery(this).addClass('selected');
                let header_add_element_button = jQuery('#elementor-editor-wrapper-v2 .MuiButtonBase-root[aria-label="Add Element"]');
                header_add_element_button.removeClass('Mui-selected');
                if (jQuery(document).find(".twbb-customize-layout").length) {
                    jQuery(document).find(".twbb-customize-layout").show();
                    jQuery("#elementor-preview-iframe").contents().find("body").find(".twbb-customize-preview-layout").show();
                } else {
                    let template = jQuery(document).find("#twbb-customize-template").html();
                    jQuery(document).find("#elementor-editor-wrapper-v2").append(template);
                    let layout_template = jQuery(document).find("#twbb-customize-preview-layout-template").html();
                    let iframeBody = jQuery("#elementor-preview-iframe").contents().find("body");
                    iframeBody.append(layout_template);
                }

                setTimeout(function () {
                    /* Set active color */
                    jQuery(document).find(".twbb-color-item").removeClass("twbb-color-active");
                    jQuery(document).find(".twbb-color-item[data-pallet_id='" + self.active_color + "']").addClass("twbb-color-active");
                }, 500)
            }
            else {
                self.kitEnablePopupOpened = 1;
                self.openCustomizeEnablePopup();
            }

        })

        jQuery(document).on('click', '.twbb-customize-enable-popup-activate-button', function() {
            self.addUltimateKit(this);
        });

        jQuery(document).on('click', '.twbb-customize-enable-popup-cancel-button, .twbb-customize-enable-popup-close, .twbb-customize-enable-popup-layout', function() {
            self.closeCustomizeEnablePopup();
        });


        jQuery(document).on('click', 'header button, .twbb-top-bar-icon-parent, .twbb-theme-customize-close, .twbb-sg-header-button-container', function() {
            self.close_customization();
        });

        jQuery(document).on('click', '.twbb-theme-customize-button-editor', function() {
            analyticsDataPush(
                'Open Global Styles Update pop-up',
                '10Web Styles',
                ''
            );
            let popupTemplate = jQuery(document).find("#twbb-customize-save-popup-template").html();
            jQuery(document).find("#elementor-editor-wrapper-v2").append(popupTemplate);
        });

        jQuery(document).on('click', '.twbb-customize-save', function() {
            let current_color_pallet = jQuery('.twbb-theme-customize-tab-content .twbb-color-item.twbb-color-active').attr('data-pallet_id');
            analyticsDataPush(
                'Save Global Styles Update pop-up',
                '10Web Styles',
                current_color_pallet
            );
            self.saveCustomization(jQuery(document).find('.twbb-theme-customize-button-editor'));
        });

        jQuery(document).on('click', '.twbb-customize-cancel, .twbb-customize-save-popup-close, .twbb-customize-save-popup-layout', function() {
            analyticsDataPush(
                'Cancel Global Styles Update pop-up',
                '10Web Styles',
                ''
            );
            self.closeSaveCustomizationPopup();
        });
    }

    addUltimateKit(that) {
        if( jQuery(that).hasClass("twbb-customize-enable-popup-loading") ) {
            return false;
        }
        let self = this;
        jQuery(that).addClass("twbb-customize-enable-popup-loading");
        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                'action': 'twbb_mergeUltimateKit_kit',
                'nonce': twbb_theme_customize.ajaxnonce,
            }
        }).success(function (response) {
            if( response.success ) {
                twbb_options.show_ultimate_kit = 1;
                self.closeCustomizeEnablePopup();
                /* Case when clicked theme icon */
                if( self.kitEnablePopupOpened ) {
                    jQuery(document).find(".twbb-customize-button").trigger("click");
                    let reload_template = jQuery('#twbb-customize-empty-reload-content-template').html();
                    jQuery(document).find(".twbb-theme-customize-tabs, .twbb-theme-customize-content, .twbb-theme-customize-footer").remove();
                    jQuery(document).find(".twbb-theme-customize-container").append(reload_template);
                }
                else { /* Case when clicked section icon */
                    jQuery(document).find(".twbb-sg-header-button-container").trigger("click");
                    let reload_template = jQuery('#twbb-sg-sidebar-empty-reload-content-template').html();
                    jQuery(document).find(".twbb-sg-sidebar-content").empty().append(reload_template);
                }
            }
        }).complete(function (response) {
            jQuery(that).removeClass("twbb-customize-enable-popup-loading");
        }).error(function (request, status, error) {
            console.log('Something Wrong when inserting generated section template.');
            return false;
        });

    }

    openCustomizeEnablePopup() {
        let template = jQuery(document).find("#twbb-customize-activate-popup-template").html();
        if ( !jQuery(document).find(".twbb-customize-enable-popup-layout").length ) {
            jQuery('body').append(template);
        }
        jQuery(document).find('.twbb-customize-enable-popup-layout').show();
        jQuery(document).find('.twbb-customize-enable-popup').show();
    }

    closeCustomizeEnablePopup() {
        jQuery(document).find('.twbb-customize-enable-popup-layout').hide();
        jQuery(document).find('.twbb-customize-enable-popup').hide();
    }

    open_customize( type ) {
        let self = this;
        if( type == 'font' ) {
            setTimeout(function () {
                jQuery(document).find('.twbb-font-list').scroll(function () {
                    // Get the height of the <div> element
                    self.visibleFontsList(jQuery(this));
                });

                self.visibleFontsList(jQuery(document).find('.twbb-font-list'));
                self.scrollToActiveFont();
            }, 500);
        } else {
            /* Set active color */
            jQuery(document).find(".twbb-color-item").removeClass("twbb-color-active");
            jQuery(document).find(".twbb-color-item[data-pallet_id='" + self.active_color + "']").addClass("twbb-color-active");

        }

    }

    closeSaveCustomizationPopup() {
        jQuery(document).find(".twbb-customize-save-popup-layout, .twbb-customize-save-popup").remove();
    }

    saveCustomization(that) {
        this.closeSaveCustomizationPopup();
        if( jQuery(that).hasClass("twbb-theme-customize-button-loading") ) {
            return;
        }
        jQuery(that).addClass("twbb-theme-customize-button-loading");
        let rest_route = twbb_theme_customize.rest_route + "/save_customization";
        let form_data = new FormData();
        form_data.append('font', this.active_font);
        form_data.append('color', this.active_color);
        fetch(rest_route, {
            method: 'POST',
            headers: {
                'X-WP-Nonce': twbb_theme_customize.ajaxnonce
            },
            body: form_data,
        })
            .then((response) => response.json())
            .then((data) => {
                if (data['success']) {
                    self.ultimateKitSaved = true;
                    window.ultimateKitSaved = true;
                    jQuery(document).find(".twbb-customize-button.selected").trigger("click");
                }
                jQuery(that).removeClass("twbb-theme-customize-button-loading");
            }).catch((error) => {
            jQuery(that).removeClass("twbb-theme-customize-button-loading");
        });

    }


    close_customization() {
        jQuery(document).find('.twbb-customize-button').removeClass('selected');
        jQuery(document).find('.MuiButtonBase-root[aria-label="Add Element"]').addClass('Mui-selected');
        jQuery(document).find(".twbb-customize-layout").hide();
        jQuery(document).find("#elementor-mode-switcher").show();
        let iframeBody = jQuery("#elementor-preview-iframe").contents().find("body");
        iframeBody.find(".twbb-customize-preview-layout").hide();
        //window.ultimateKitSaved is to be sure that variable is changed correctly in time only ultimateKitSaved was not working properly
        if( !this.ultimateKitSaved && !window.ultimateKitSaved ) {
            this.changeThemeStyle( this, 'color', 'remove' );
            this.changeThemeStyle( this, 'font', 'remove' );
        }
    }

    getCookie(cname) {
        let name = cname + "=";
        let decodedCookie = decodeURIComponent(document.cookie);
        let ca = decodedCookie.split(';');
        for(let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        return "";
    }

    setActiveTheme() {
        let self = this;
        let twbb_theme_color = this.getCookie('twbb_theme_color');
        let twbb_theme_font = this.getCookie('twbb_theme_font');

        if(!twbb_theme_color){
            twbb_theme_color = self.active_color;
        }

        if(!twbb_theme_font){
            twbb_theme_font = self.active_font;
        }

        if( twbb_theme_color != "" ) {
            this.active_color = twbb_theme_color;
            this.changeThemeStyle( this, 'color' );
        }
        if( twbb_theme_font != "" ) {
            this.active_font = twbb_theme_font;
            if( typeof WebFont != 'undefined' && this.active_font != null ) {
                WebFont.load({
                    google: {
                        families: [this.active_font]
                    },
                });

                /* 2 seconds as iframe append has timeout 1 second */
                setTimeout(function(){
                    jQuery('#tbdemo-device-iframe').load(function () {
                        let iframe = document.getElementById("tbdemo-device-iframe");
                        WebFont.load({
                            google: {
                                families: [self.active_font]
                            },
                            context: iframe.contentWindow
                        });
                        self.changeThemeStyle(twbb_theme_font, 'font' );
                    });
                }, 2000);
            } else if( typeof elementor != 'undefined' ) {
                elementor.helpers.enqueueFont(this.active_font, 'editor');
                elementor.helpers.enqueueFont(this.active_font);
            }

            this.changeThemeStyle(twbb_theme_font, 'font' );
        }
    }

    changeThemeStyle( param, type, visibility = 'add' ) {
        let self = this;
        let iframeBody = '';
        let iframeSection = '';
        if( self.from_demo ) {
            /* Iframe of mobile view */
            iframeBody = jQuery("#tbdemo-device-iframe").contents().find("body");

        } else {
            /* Iframe of elementor preview */
            iframeBody = jQuery("#elementor-preview-iframe").contents().find("body");
            iframeSection = jQuery('.twbb-sg-sidebar-navigated-content iframe');
        }

        if( type == 'font' ) {
            let font = param;
            if( self.from_demo ) {
                document.cookie = "twbb_theme_font=" + font + "; path=/";
            }
            this.changeFonts(jQuery('body'), font, visibility );
            if( iframeBody.length ) {
                this.changeFonts(iframeBody, font, visibility );
            }
            if( iframeSection !== '' && iframeSection.contents().find('body').length ) {
                iframeSection.each(function() {
                    let contents = jQuery(this).contents();
                    let fontUrl = 'https://fonts.googleapis.com/css?family=' + font + ':100,100italic,200,200italic,300,300italic,400,400italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic';
                    elementor.helpers.enqueueCSS(fontUrl, contents);
                    self.changeFonts(contents.find("body"), font, visibility );
                })
            }
        } else if( type == 'color' ) {
            if( self.from_demo ) {
                document.cookie = "twbb_theme_color=" + this.active_color + "; path=/";
            }
            let kit = this.colors.filter((value) => value['id'] === this.active_color);
            if( kit.length ) {
                kit = kit[0]['kit'];

                this.changeColors(jQuery('body'), kit, visibility );
                if(iframeBody.length) {
                    this.changeColors(iframeBody, kit);
                }
                if( iframeSection !== '' && iframeSection.contents().find('body').length ) {
                    iframeSection.each(function() {
                        let contents = jQuery(this).contents();
                        self.changeColors(contents.find('body'), kit, visibility );
                    })
                }
            }
        }
    }

    changeColors(bodyEl, kit, visibility = 'add' ) {
        let colors = [].concat(kit['custom_colors'], kit['system_colors'])
        if ( visibility === 'add' ) {
            for (let color of colors) {
                bodyEl.css('--e-global-color-' + color['_id'], color['color']);
            }
        } else {
            if( this.existingBodyStyles !== undefined ) {
                bodyEl.attr('style', this.existingBodyStyles);
            } else {
                bodyEl.removeAttr('style');
            }
        }
    }

    changeFonts(bodyEl, font, visibility = 'add' ){
        if ( visibility === 'add' ) {
            for (let id of twbb_theme_customize.typography_ids) {
                bodyEl.css('--e-global-typography-' + id + '-font-family', font);
            }
        } else {
            if( this.existingBodyStyles !== undefined ) {
                bodyEl.attr('style', this.existingBodyStyles);
            } else {
                bodyEl.removeAttr('style');
            }
        }
    }

    scrollToActiveFont() {
        let self = this;
        jQuery(document).find(".twbb-font-list:visible > div").removeClass("twbb-font-active");

        jQuery(document).find(".twbb-font-list:visible > div").filter(function() {
            return jQuery(this).text().trim() === self.active_font;
        }).addClass("twbb-font-active"); // Example: change background color of matching elements

        let activeFont = '';
        let list = jQuery(document).find(".twbb-font-list:visible");
        if( typeof list != 'undefined' && list.length ) {
            list.each( function() {
                activeFont = jQuery(this).find(".twbb-font-active");
                if( typeof activeFont != 'undefined' && activeFont.length ) {
                    if (self.from_demo) {
                        jQuery(this).scrollTop(jQuery(this).scrollTop() + activeFont.position().top - 100);
                    } else {
                        // Scroll to the top
                        jQuery(this).scrollTop(jQuery(this).scrollTop() + activeFont.position().top - 200);
                    }
                }
            })
        }
    }

    sortBySearch(searchTerm, texts) {
        jQuery(document).find(".twbb-font-list:visible").scrollTop(0);
        const sortedArray = Object.values(texts);

        const filteredArray = sortedArray.filter(text => text.toLowerCase().startsWith(searchTerm.toLowerCase()));

        if(filteredArray.length === 0) return []; // If no results found, return an empty array

        sortedArray.sort((a, b) => {
            const startsWithSearchA = a.toLowerCase().startsWith(searchTerm.toLowerCase());
            const startsWithSearchB = b.toLowerCase().startsWith(searchTerm.toLowerCase());

            if (startsWithSearchA && !startsWithSearchB) {
                return -1; // If only a starts with the search term, move it before b
            } else if (!startsWithSearchA && startsWithSearchB) {
                return 1; // If only b starts with the search term, move it before a
            } else {
                // If either both start with the search term or neither does, sort alphabetically
                return a.toLowerCase().localeCompare(b.toLowerCase());
            }
        });

        return sortedArray;
    }

    visibleFontsList(that) {
        clearTimeout(this.scrollTimeout);

        this.scrollTimeout = setTimeout(function() {
            let visibleLiList = [];
            let $ul = jQuery(that);
            let ulTop = $ul.offset().top;
            let ulBottom = ulTop + $ul.height();
            let $liElements = $ul.find('div');

            $liElements.each(function() {
                let $li = jQuery(this);
                let liTop = $li.offset().top;
                let liBottom = liTop + $li.outerHeight();

                if ((liTop >= ulTop && liTop <= ulBottom) || (liBottom >= ulTop && liBottom <= ulBottom)) {
                    let font = $li.text().trim();
                    visibleLiList.push(font);
                    /* Demo frontend case */
                    if( typeof WebFont != 'undefined' && font != null && font != "" ) {
                        let iframe = document.getElementById("tbdemo-device-iframe");
                        if( iframe != null ) {
                            WebFont.load({
                                google: {
                                    families: [font]
                                },
                                context: iframe.contentWindow
                            });
                        }
                        WebFont.load({
                            google: {
                                families: [font]
                            },
                        });
                    } /* Editor backend case */
                    else if( typeof elementor != 'undefined' ) {
                        elementor.helpers.enqueueFont(font, 'editor');
                        elementor.helpers.enqueueFont(font);
                    }
                }
            });
        }, 250);
    }

}

let theme_Customize;
jQuery(document).on('ready', function () {
    theme_Customize = new ThemeCustomize();
    theme_Customize.init();
});