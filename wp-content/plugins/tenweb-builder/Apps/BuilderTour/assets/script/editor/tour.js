class BuilderTour {
    constructor() {
        this.TourSteps = tour_data.tour_content_variables;
    }

    init() {
        let self = this;
        jQuery(document).on('click', '.twbb-tour-guide__button-done', function(e) {
            e.preventDefault();
            if (!jQuery('.twbb-tour-guide__button').hasClass('twbb-tour-guide__button-disabled')) {
                self.twbbDestroyTour();
                if (tour_data.tour_status !== 'passed') {
                    self.statusAjax('passed');
                }
            }
        });

        jQuery(document).on('click','.twbb-tour-guide__button-next', function(e){
            e.preventDefault();
            if (!jQuery('.twbb-tour-guide__button').hasClass('twbb-tour-guide__button-disabled')) {
                jQuery('.twbb-tour-guide__button').addClass('twbb-tour-guide__button-disabled');
                jQuery(this).text('');
                jQuery(this).addClass('twbb-tour-guide__button-loading');
                jQuery('.twbb-tour-guide__button-loading').append('<span></span>');
                self.callTheStep(jQuery(e.target), 'add');
            }
        });
        jQuery(document).on('click','.twbb-tour-guide__button-back', function(e){
            e.preventDefault();
            if (!jQuery('.twbb-tour-guide__button').hasClass('twbb-tour-guide__button-disabled')) {
                jQuery('.twbb-tour-guide__button').addClass('twbb-tour-guide__button-disabled');
                jQuery(this).text('');
                jQuery(this).addClass('twbb-tour-guide__button-loading');
                jQuery('.twbb-tour-guide__button-loading').append('<span></span>');
                self.callTheStep(jQuery(e.target), 'sub');
            }
        });
        jQuery(document).on('click','.twbb-tour-guide__button-stop', function(){
            analyticsDataPush('Remind me later', 'Editor Tour');
            self.twbbDestroyTour();
            if ( tour_data.tour_status !== 'passed' ) {
                self.statusAjax('not_passed');
            }
        });
        jQuery(document).on('click', '.twbb-tour-guide__video', function() {
            self.zoomInVideo();
        });
        jQuery(document).on('click', '.twbb-tour-main-overlay.twbb-tour-guide', function(e) {
            if( e.target === e.currentTarget ){
                self.zoomOutVideo();
            }

        });

        jQuery(document).on('click', '.twbb-start_tour-send-ga', function() {
            analyticsDataPush('Get Started' , 'Editor Tour');
        });
    }

    twbbStartTour() {
        if( !jQuery('.twbb-tour-guide__container').length ) {
            let tour_html = jQuery('#twbb-editor-tour-template').html();
            jQuery('body').append(tour_html);
        }

        if ( tour_data.tour_status !== 'passed' ) {
            this.statusAjax('started');
        }
        if ( jQuery('#elementor-panel-header-kit-close').css('display') !== 'none' ) {
            jQuery('#elementor-panel-header-kit-close').trigger('click');
        }
        let addElementButton = jQuery('.MuiToolbar-root .MuiBox-root .MuiGrid-root:first-child .MuiStack-root:eq(1) .MuiBox-root:first-child button');
        addElementButton.trigger('click');
        if ( jQuery('#elementor-navigator').css('display') !== 'none' ) {
            let structureElementButton = jQuery('.MuiToolbar-root .MuiBox-root .MuiGrid-root:eq(1) .MuiStack-root:eq(1) .MuiBox-root:eq(2) button');
            structureElementButton.trigger('click');
        }
        this[this.TourSteps['0']['actionFunction']]();
    }
    getStarted() {
        if ( jQuery('#elementor-panel-header-kit-close').css('display') !== 'none' ) {
            jQuery('#elementor-panel-header-kit-close').trigger('click');
        }
        this.tourGuide('0');
        jQuery('body').addClass('twbb-tour-body-class');
        /* Close coPilot chat container */
        if( jQuery(document).find(".twbb-copilot-header-minimize").is(':visible') ) {
            jQuery(document).find(".twbb-copilot-header-minimize").trigger("click");
        }

        jQuery('#elementor-editor-wrapper-v2 header').addClass('twbb-change-bg-color');
        jQuery('#elementor-preview-iframe').contents().find('body').addClass('twbb-height-auto');
        jQuery('#elementor-preview-iframe').contents().find('body').prepend('<div class="twbb-tour-overlay-preview-part"></div>');

        /*
        this is for adding height to body to be able to scroll to needed place
         */
        jQuery('#elementor-preview-iframe').contents().find('body')
            .css('min-height','1000px')
            .append('<div class="twbb-just-for-body-height" style="height: 1000px"></div>');
        jQuery('#elementor-editor-wrapper-v2 .MuiGrid-root:nth-child(3) > div:eq(1) button').css('opacity','30%');

    }

    sectionGeneration() {
        jQuery('#elementor-preview-iframe').contents().find('html, body').animate({
            scrollTop: 0
        }, 200);
        jQuery('body').addClass('twbb-highligted-settings');
        jQuery('#elementor-editor-wrapper-v2 .twbb-sg-header-button-container').addClass('twbb-highlighted-header-part')
            .trigger('click');
        jQuery('#elementor-editor-wrapper-v2 .twbb-sg-sidebar').addClass('twbb-highlighted-header-part');
    }

    destroy_sectionGeneration() {
        jQuery('#elementor-preview-iframe').contents().find('html, body').animate({
            scrollTop: 0
        }, 200);
        jQuery('body').removeClass('twbb-highligted-settings');
        jQuery('#elementor-editor-wrapper-v2 .twbb-sg-header-button-container').removeClass('twbb-highlighted-header-part');
        jQuery('#elementor-editor-wrapper-v2 .twbb-sg-sidebar').removeClass('twbb-highlighted-header-part');
        twbb_animate_sidebar(0);
        return true;
    }
    quickEdit() {
        var self = this;
        let text_editor = jQuery('#elementor-preview-iframe').contents().find('.elementor-edit-area-active .elementor-widget-text-editor').eq(0);
        let item = 'text_editor';
        if ( text_editor.length == 0) {
            text_editor = jQuery('#elementor-preview-iframe').contents().find('.elementor-edit-area-active .elementor-widget-heading').eq(0);
            item = 'heading';
        }
        if ( text_editor.length == 0) {
            jQuery('.elementor-element-wrapper').each(function(){
                if(jQuery(this).find('.title').text().toLowerCase() == 'text editor') {
                    jQuery(this).addClass('twbb-widget-highlighted');

                    return false;
                }
            });
        } else {
            let height = text_editor.height();
            let left = text_editor.offset().left;
            let width = text_editor.width();
            let top = text_editor.offset().top;
            let overlayTop = 120;

            if ( top > overlayTop ) {
                jQuery('#elementor-preview-iframe').contents().find('html, body').animate({
                    scrollTop: top - overlayTop
                }, 200);
            } else {
                overlayTop = top;
            }

            if ( item == 'text_editor' ) {
                jQuery('#elementor-preview-iframe').contents().find('.elementor-edit-area-active .elementor-widget-text-editor').trigger('click');
            } else if ( item == 'heading' ) {
                jQuery('#elementor-preview-iframe').contents().find('.elementor-edit-area-active .elementor-widget-heading').trigger('click');
            }
            self.drawOverlayHighlighted(width,left,height,overlayTop);
            //Adding class for design in css
            text_editor.addClass('twbb-highlighted-element');
        }
    }

    destroy_quickEdit() {
        let addElementButton = jQuery('.MuiToolbar-root .MuiBox-root .MuiGrid-root:first-child .MuiStack-root:eq(1) .MuiBox-root:first-child button');
        if ( jQuery('.twbb-widget-highlighted').length == 0 &&
            jQuery('#elementor-preview-iframe').contents().find('.twbb-highlighted-element').length == 0 &&
            jQuery('#elementor-preview-iframe ').contents().find('.twbb-tour-preview-highlighted-part').length == 0 &&
            addElementButton.attr('tabindex') == 0
        ) {
            return true;
        } else {
            jQuery('.twbb-widget-highlighted').removeClass('twbb-widget-highlighted');
            jQuery('#elementor-preview-iframe').contents().find('.twbb-highlighted-element').removeClass('twbb-highlighted-element');
            addElementButton.trigger('click');
            jQuery('#elementor-preview-iframe ').contents().find('.twbb-tour-preview-highlighted-part').remove();
            return false;
        }
    }

    containerSection() {
        var self = this;
        let containerSection = jQuery('#elementor-preview-iframe').contents().find('.elementor-edit-area-active #elementor-add-new-section');
        containerSection.addClass('twbb-highlighted-element');

        let height = containerSection.height();
        let left = containerSection.offset().left;
        let width = containerSection.width();
        let top = containerSection.offset().top;
        let overlayTop = 208;

        if ( top > overlayTop ) {
            jQuery('#elementor-preview-iframe').contents().find('html, body').animate({
                scrollTop: top - overlayTop
            }, 200);
        } else {
            overlayTop = top;
        }

        jQuery('#elementor-panel-category-layout').addClass('twbb-containers-highlighted');
        self.drawOverlayHighlighted(width + 20,left,height,overlayTop);
    }

    destroy_containerSection() {
        if ( jQuery('#elementor-preview-iframe').contents().find('.twbb-highlighted-element').length == 0 &&
            jQuery('#elementor-panel-category-layout.twbb-containers-highlighted').length == 0 &&
            jQuery('#elementor-preview-iframe ').contents().find('.twbb-tour-preview-highlighted-part').length == 0
        ) {
            return true;
        } else {
            jQuery('#elementor-preview-iframe').contents().find('.twbb-highlighted-element').removeClass('twbb-highlighted-element');
            jQuery('#elementor-panel-category-layout').removeClass('twbb-containers-highlighted');
            jQuery('#elementor-preview-iframe ').contents().find('.twbb-tour-preview-highlighted-part').remove();
            return false;
        }
    }

    visualElement() {
        var self = this;
        setTimeout(function(){
            jQuery('#elementor-preview-iframe').contents().find('.elementor-add-section-button').trigger('click');
            let visualElement = jQuery('#elementor-preview-iframe').contents().find('.elementor-edit-area-active #elementor-add-new-section');
            visualElement.addClass('twbb-highlighted-element');

            let height = visualElement.height();
            let left = visualElement.offset().left;
            let width = visualElement.width();
            let top = jQuery('#elementor-preview-iframe').contents().find('.elementor-edit-area-active #elementor-add-new-section').offset().top;
            let overlayTop = 208;

            if ( top > overlayTop ) {
                jQuery('#elementor-preview-iframe').contents().find('html, body').animate({
                    scrollTop: top - overlayTop
                }, 100);
            } else {
                overlayTop = top;
            }

            setTimeout(function(){
                jQuery('#elementor-panel-category-basic').addClass('twbb-containers-highlighted');
                self.drawOverlayHighlighted(width + 20 ,left,height,overlayTop);
            },50);
        },100);
    }

    destroy_visualElement() {
        if (jQuery('#elementor-preview-iframe').contents().find('.elementor-edit-area-active .elementor-add-section-close').css('display') == 'none' &&
            jQuery('#elementor-panel-category-basic.twbb-containers-highlighted').length == 0 &&
            jQuery('#elementor-preview-iframe').contents().find('.twbb-highlighted-element').length == 0 &&
            jQuery('#elementor-preview-iframe ').contents().find('.twbb-tour-preview-highlighted-part').length == 0
        ) {
            return true;
        } else {
            jQuery('#elementor-preview-iframe').contents().find('.elementor-edit-area-active .elementor-add-section-close').trigger('click');
            jQuery('#elementor-panel-category-basic').removeClass('twbb-containers-highlighted');
            jQuery('#elementor-preview-iframe ').contents().find('.twbb-tour-preview-highlighted-part').remove();
            jQuery('#elementor-preview-iframe').contents().find('.twbb-highlighted-element').removeClass('twbb-highlighted-element');
            return false;
        }
    }

    responsiveness() {
        jQuery('#elementor-preview-iframe').contents().find('html, body').animate({
            scrollTop: 0
        }, 200);
        // switch device
        jQuery('#elementor-editor-wrapper-v2 div[aria-label="Switch Device"] button:nth-child(3)').trigger('click');
        //open website navigation popup
        jQuery('#elementor-editor-wrapper-v2 .MuiGrid-root:nth-child(2) button').eq(0).trigger('click');

        setTimeout(function(){
            jQuery('#elementor-v2-top-bar-recently-edited .MuiList-root').css('background-color','#0B0D0D')
            jQuery('.MuiList-root .twbb_website_structure_top_bar .twbb_sub_menu .title.site_menu').addClass('active');
            jQuery('.MuiList-root .twbb_website_structure_top_bar .twbb_sub_menu .title.site_menu .title_container').removeClass('closed');
            jQuery('.MuiList-root .twbb_website_structure_top_bar .twbb_sub_menu .title.site_menu .title_container').addClass('opened');
            jQuery('#elementor-v2-top-bar-recently-edited .MuiPaper-root ').addClass('twbb-highlighted-mui-paper').css('left', '350px');

            jQuery('#elementor-editor-wrapper-v2 .MuiGrid-root:nth-child(2)>div').addClass('twbb-highlighted-header-part');

        },50);
    }

    destroy_responsiveness() {
        if (jQuery('.MuiBackdrop-root.MuiBackdrop-invisible').length == 0 &&
            jQuery('#elementor-editor-wrapper-v2 div[aria-label="Switch Device"] button:nth-child(1)').attr('tabindex') == '0' &&
            jQuery('#elementor-editor-wrapper-v2 .MuiGrid-root:nth-child(2)>div.twbb-highlighted-header-part').length == 0 &&
            jQuery('#elementor-v2-top-bar-recently-edited .MuiPaper-root.twbb-highlighted-mui-paper').length == 0
        ) {
            return true;
        } else {
            jQuery('.MuiBackdrop-root.MuiBackdrop-invisible').trigger('click');
            jQuery('#elementor-editor-wrapper-v2 div[aria-label="Switch Device"] button:nth-child(1)').trigger('click');
            jQuery('#elementor-editor-wrapper-v2 .MuiGrid-root:nth-child(2)>div').removeClass('twbb-highlighted-header-part');
            jQuery('#elementor-v2-top-bar-recently-edited .MuiPaper-root ').removeClass('twbb-highlighted-mui-paper');
            return false;
        }
    }

    globalStyles() {
        jQuery('#elementor-preview-iframe').contents().find('html, body').animate({
            scrollTop: 0
        }, 200);
        jQuery('body').addClass('twbb-highligted-settings');
        jQuery('#elementor-editor-wrapper-v2 .MuiGrid-root:nth-child(1)>div:nth-child(3)').addClass('twbb-highlighted-header-part');
        // ultimate kit themes case
        if(jQuery('.twbb-customize-button').length > 0) {
            jQuery('.twbb-customize-button').trigger('click');
            jQuery('.twbb-customize-button').addClass('twbb-theme-tour');
            jQuery('.twbb-customize-container').addClass('twbb-highlighted-mui-paper');
        }
    }

    destroy_globalStyles() {
        if(jQuery('.twbb-customize-layout .twbb-theme-customize-container').length > 0) {
            return this.destroy_globalStyles_ultimate();
        }
    }

    destroy_globalStyles_ultimate() {
        if (jQuery('.twbb-customize-button.selected').length == 0 &&
            jQuery('#elementor-editor-wrapper-v2 .MuiGrid-root:nth-child(1)>div:nth-child(3).twbb-highlighted-header-part').length == 0
        ) {
            return true;
        } else {
            if ( jQuery('.twbb-customize-layout .twbb-theme-customize-container').length > 0 ) {
                jQuery('.twbb-customize-button').trigger('click');
            }
            jQuery('body').removeClass('twbb-highligted-settings');
            jQuery('#elementor-editor-wrapper-v2 .MuiGrid-root:nth-child(1)>div:nth-child(3)').removeClass('twbb-highlighted-header-part');
            return false;
        }
    }

    publishWebsite() {
        jQuery('#elementor-editor-wrapper-v2 .MuiGrid-root:nth-child(3) > div:eq(1) span button').trigger('click');
        setTimeout(function(){
            jQuery('#document-save-options .MuiPaper-root').addClass('twbb-highlighted-mui-paper');
        },30);

        jQuery('#elementor-editor-wrapper-v2 .MuiGrid-root:nth-child(3) > div:eq(1)').addClass('twbb-highlighted-header-part');
        jQuery('#elementor-editor-wrapper-v2 .MuiGrid-root:nth-child(3) > div:eq(1) button').css('opacity','1');
    }

    destroy_publishWebsite() {
        if (jQuery('#document-save-options .MuiPaper-root.twbb-highlighted-mui-paper').length == 0 &&
            jQuery('#elementor-editor-wrapper-v2 .MuiGrid-root:nth-child(3) > div:eq(1).twbb-highlighted-header-part').length == 0 &&
            jQuery('#elementor-editor-wrapper-v2 .MuiGrid-root:nth-child(3) > div:eq(1) button').css('opacity') == 0.3
        ) {
            return true;
        } else {
            jQuery('#document-save-options .MuiPaper-root').removeClass('twbb-highlighted-mui-paper');
            jQuery('#elementor-editor-wrapper-v2 .MuiGrid-root:nth-child(3) > div:eq(1)').removeClass('twbb-highlighted-header-part');
            jQuery('.MuiBackdrop-root.MuiBackdrop-invisible').trigger('click');
            jQuery('#elementor-editor-wrapper-v2 .MuiGrid-root:nth-child(3) > div:eq(1) button').css('opacity','30%');
            return false;
        }
    }

    twbbDestroyTour() {
        let last_step = this.TourSteps['all_steps_count'];
        if( typeof this['destroy_' + this.TourSteps[last_step]['actionFunction']] == 'function') {
            this['destroy_' + this.TourSteps[last_step]['actionFunction']]();
        }
        jQuery('body').removeClass('twbb-tour-body-class');
        jQuery('#elementor-preview-iframe').contents().find('.twbb-tour-overlay-preview-part').remove();
        jQuery('#elementor-preview-iframe').contents().find('body .twbb-just-for-body-height').remove();
        jQuery('#elementor-editor-wrapper-v2 header').removeClass('twbb-change-bg-color');
        jQuery('#elementor-editor-wrapper-v2 .MuiGrid-root:nth-child(3) > div:eq(1) button').css('opacity','1');
        if ( jQuery('#elementor-navigator').css('display') === 'none' ) {
            let structureElementButton = jQuery('.MuiToolbar-root .MuiBox-root .MuiGrid-root:eq(1) .MuiStack-root:eq(1) .MuiBox-root:eq(2) button');
            structureElementButton.trigger('click');
        }
    }

    /*
    * Draw the tour popup for each step
     */
    tourGuide(step) {
        let info = this.TourSteps[step];
        jQuery('.twbb-tour-guide__container').attr('data-step',step);
        if ( step == 0 ) {
            jQuery('.twbb-tour-guide__steps').text('');
            jQuery('.twbb-tour-guide__description').css('padding-left', '0');
        } else {
            jQuery('.twbb-tour-guide__steps').text( 'Step ' + step + '/' + this.TourSteps['all_steps_count']);
            jQuery('.twbb-tour-guide__description').attr('class','twbb-tour-guide__description twbb-tour-guide-desc__special');
            jQuery('.twbb-tour-guide__description').css('padding-left', '26px');
        }

        if ( info['video_link'] !== '' ) {
            if ( step == 0 ) {
                jQuery('.twbb-tour-guide__video').css('background-color','rgb(50,54,56)');
            } else {
                jQuery('.twbb-tour-guide__video').css('background-color','#ffffff');
            }
            if ( info['poster_link'] !== '' ) {
                jQuery('.twbb-tour-guide__video video').attr('poster', info['poster_link']);
            } else {
                jQuery('.twbb-tour-guide__video video').attr('poster', '');
            }
            if ( jQuery('.twbb-tour-guide__video video source').length > 0 ) {
                jQuery('.twbb-tour-guide__video video').attr( 'src', info['video_link'] );
                jQuery('.twbb-tour-guide__video video').get(0).load();
                jQuery('.twbb-tour-guide__video video').get(0).play();
            } else {
                let source = jQuery("<source>");
                source.attr("src", info['video_link']);
                source.attr("type", "video/mp4");
                jQuery('.twbb-tour-guide__video video').append(source);
                jQuery('.twbb-tour-guide__video').css('display', 'inline-block');
                jQuery('.twbb-tour-guide__video video').get(0).load();
                jQuery('.twbb-tour-guide__video video').get(0).play();
            }
        } else {
            jQuery('.twbb-tour-guide__video video').empty();
            jQuery('.twbb-tour-guide__video').css('display','none');
        }
        if ( info['title'] !== '' ) {
            jQuery('.twbb-tour-guide__title').text(info['title']).attr('class', 'twbb-tour-guide__title ' + info['icon']);
        }
        if ( info['description'] !== '' ) {
            jQuery('.twbb-tour-guide__description').html(info['description']);
        }
        if ( info['buttons'] !== '' ) {
            jQuery('.twbb-tour-guide__buttons').attr('class',info['buttons']['class']);
        }
        if ( info['buttons']['buttonLeft'] !== '' ) {
            jQuery('.twbb-tour-guide__left_button').html();
            jQuery('.twbb-tour-guide__left_button').text(info['buttons']['buttonLeft']['text'])
                .attr('class', 'twbb-tour-guide__button twbb-tour-guide__left_button ' + info['buttons']['buttonLeft']['classes']);
        }
        if ( info['buttons']['buttonRight'] !== '' ) {
            jQuery('.twbb-tour-guide__left_button').html();
            jQuery('.twbb-tour-guide__right_button').text(info['buttons']['buttonRight']['text'])
                .attr('class', 'twbb-tour-guide__button twbb-tour-guide__right_button ' + info['buttons']['buttonRight']['classes']);
        }
    }

    drawOverlayHighlighted(width,left,height,top) {
        jQuery('#elementor-preview-iframe ').contents().find('.twbb-tour-overlay-preview-part').prepend(
            '<div class="twbb-tour-preview-highlighted-part" style="width:' + width + 'px;left:' + left + 'px;' +
            'height:' + (height + 24) + 'px;background-position:'+ ( width - 25 ) + 'px;top:' + top + 'px;' +'">' +
            '</div>');
    }

    callTheStep(that, fact) {
        let self = this;
        let step = that.closest('.twbb-tour-guide__container').attr('data-step');
        let next_step;
        if (fact == 'add') {
            next_step = parseInt(step) + 1;
        } else {
            next_step = parseInt(step) - 1;
        }
        if (typeof this['destroy_' + this.TourSteps[step]['actionFunction']] == 'function') {
            var checkFinished = setInterval(function () {
                if (self['destroy_' + self.TourSteps[step]['actionFunction']]()) {
                    clearInterval(checkFinished);
                    self[self.TourSteps[next_step]['actionFunction']]();
                    self.tourGuide(next_step);
                } else {
                    self['destroy_' + self.TourSteps[step]['actionFunction']]()
                }
            }, 20);
        } else {
            this[this.TourSteps[next_step]['actionFunction']]();
            this.tourGuide(next_step);
        }
    }

    statusAjax(status) {
        jQuery.ajax({
            type: 'POST',
            url: tour_data.ajaxurl,
            dataType: 'json',
            data: {
                'tour_status': status,
                action: "twbb_update_tour_status",
                nonce: tour_data.nonce,
            }
        }).success(function(res){
            if( res['success'] === true ) {
                console.log('Tour Status Updated to "' + status + '"');}
        }).error(function () {
            console.log('Tour Status Not Updated due to some error');
        });
    }

    zoomInVideo() {
        if ( !jQuery('.twbb-tour-guide__video').hasClass('twbb-tour-guide__video-zoomed') ) {
            jQuery('.twbb-tour-guide__video video')
                .attr('width', 616)
                .attr('height', 344)
                .attr('controls', 'controls');
            jQuery('.twbb-tour-guide__video').addClass('twbb-tour-guide__video-zoomed');
            jQuery('.twbb-tour-guide__video video').get(0).load();
            jQuery('.twbb-tour-guide__video video').get(0).play();
        }
    }

    zoomOutVideo() {
        if ( jQuery('.twbb-tour-guide__video').hasClass('twbb-tour-guide__video-zoomed') ) {
            jQuery('.twbb-tour-guide__video').removeClass('twbb-tour-guide__video-zoomed');
            jQuery('.twbb-tour-guide__video video')
                .attr('width', 310)
                .attr('height', 170)
                .removeAttr('controls');
            jQuery('.twbb-tour-guide__video video').get(0).load();
            jQuery('.twbb-tour-guide__video video').get(0).play();
        }
    }

}
interval = '';
jQuery(document).ready(function() {
    let tour =  new  BuilderTour();
    tour.init();

    if ( tour_data.tour_status === 'not_started' && tour_data.show_tour == true && jQuery('#elementor-loading').length > 0 ) {
        interval = setInterval(function() {
            if( jQuery('.twbb-top-bar-icon-parent').length > 0 ) {
                clearInterval(interval);
                jQuery('.twbb-top-bar-icon-parent').addClass('twbb-tour-not-passed');
            }
        }, 400);
    }

    jQuery(document).on('click', '.twbb-copilot-tour-button', function() {
        let tour_notif = jQuery('#twbb-editor-tour-small-notif-template').html();
        jQuery('.twbb-top-bar-icon-parent').append(tour_notif);
    });

    jQuery(document).on('click','#elementor-editor-wrapper-v2 .twbb-top-bar-icon', function() {
        setTimeout(function () {
            if (!jQuery('a.twbb-main-menu-editor-tour').length ) {
                let tour_class_attr = '';
                let tour_html = '';
                if (tour_data.tour_status !== 'passed') {
                    tour_class_attr = 'twbb-tour-not-passed';
                }
                if (tour_data.show_tour == true) {
                    tour_html = '<a class="MuiButtonBase-root MuiMenuItem-root MuiMenuItem-gutters MuiMenuItem-root ' +
                        'MuiMenuItem-gutters eui-menu-item twbb-main-menu-items twbb-main-menu-editor-tour ' + tour_class_attr + '"' +
                        ' href="#" onclick="twbbStartTour()">Editor Tour</a>'
                }

                if( jQuery('.twbb_website_structure-footer').length ) {
                    jQuery('.twbb_website_structure-footer').before(tour_html);
                } else {
                    jQuery('#elementor-v2-app-bar-main-menu .MuiMenu-list').append(tour_html);
                }

            }
            //timeout for adding class to the element should be more then adding 10Web dashboard element in editor_v2.js
        },70);
    });

    jQuery(document).on('click', '.twbb-tour-guide__button-done', function() {
        jQuery('#elementor-v2-app-bar-main-menu .MuiMenu-list .twbb-main-menu-editor-tour').removeClass('twbb-tour-not-passed');
        jQuery('.twbb-top-bar-icon-parent').removeClass('twbb-tour-not-passed');
    });
});

function twbbStartTour() {
    deleteNotification();
    //not to send when we open tour
    if ( tour_data.tour_status !== 'not_started' ) {
        analyticsDataPush('Top Bar', 'Editor Tour');
    }

    let tour =  new  BuilderTour();
    tour.twbbStartTour();
}

function deleteNotification() {
    jQuery('.twbb-editor-tour-small-notif-main-container').remove();
}