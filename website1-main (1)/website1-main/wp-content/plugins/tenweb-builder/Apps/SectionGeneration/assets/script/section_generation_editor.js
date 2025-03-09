jQuery(document).ready(function() {
    /*
        preload the sections iframe content before it is needed
     */
    twbb_preload_sections_iframe_content();

    /* Disable Custom buttons if Panel closed */
    jQuery(document).on("click", "#elementor-mode-switcher", function () {
        if ( jQuery("body").hasClass("elementor-editor-preview") ) {
            jQuery('.twbb-sg-header-button-container').removeClass('disabled');
            jQuery('.twbb-customize-button').removeClass('disabled');
        } else {
            if( !jQuery('.twbb-sg-header-button-container').hasClass('disabled')) {
                jQuery('.twbb-sg-header-button-container').addClass('disabled');
            }
            if( !jQuery('.twbb-customize-button').hasClass('disabled')) {
                jQuery('.twbb-customize-button').addClass('disabled');
            }
        }
    });

    //add status for Generate with AI button
    jQuery(document).on('input', '.twbb-generate-section_description', function() {
       set_content_generation_status();
    });

    //Add section button in editor preview bottom +sections part
    let template_add_section = jQuery("#tmpl-elementor-add-section");
    if (0 < template_add_section.length) {
        var old_template_button = template_add_section.html();
        old_template_button = old_template_button.replace(
            '<div class="e-view elementor-add-new-section">',
            '<div class="e-view elementor-add-new-section"><div class="elementor-add-section-area-button elementor-add-twbb-section-generation-button">Add Section</div>'
        );
        template_add_section.html(old_template_button);
    }

    if( twbb_sg_editor.sections_new  === 'not_passed' ) {
        jQuery('header').addClass('twbb-new-section-generation');
    }

    let twbb_generating_overlay = jQuery('#twbb-sg-sidebar-generated-with-ai_overlay-template').html();

    add_sections_button_topbar();

    let addElementButtonTarget = '.MuiToolbar-root .MuiBox-root .MuiGrid-root:first-child .MuiStack-root:eq(1) .MuiBox-root:first-child button';
    let siteSettingsElementButtonTarget = '.MuiToolbar-root .MuiBox-root .MuiGrid-root:eq(1) .MuiStack-root:eq(1) .MuiBox-root:eq(1) button';
    let documentSettingsElementButtonTarget = '.MuiToolbar-root .MuiBox-root .MuiGrid-root:eq(1) .MuiStack-root .MuiBox-root button';
    //Close section generation when any button in header is clicked
    jQuery(siteSettingsElementButtonTarget + ',' + addElementButtonTarget + ',' + documentSettingsElementButtonTarget + ', .twbb-top-bar-icon-parent, .twbb-customize-button').on('click', function() {
        twbb_animate_sidebar(0);
    });

    jQuery('document').on('click','.custom-select-container.is-disabled', function(event){
        event.stopPropagation();
    });

    //events
    jQuery(document).on('click', '.twbb-sg-header-button-container', function(){
        if( !jQuery(this).hasClass('disabled') && !jQuery(this).hasClass('selected') ) {
            analyticsDataPush(
                'Add Section',
                'Section Generation',
                'Topbar'
            );
        }
        twbb_trigger_sections_button(jQuery(this));
    });

    //Section types click
    jQuery(document).on('click', '.twbb-sg-navigation-item', function() {
        let last_selected = jQuery('.twbb-sg-navigation-item.selected').attr('data-type');
        if( jQuery(this).attr('data-type') == 'generate-with-ai' ) {
            jQuery('.twbb-generate-with-ai-button-input').parent().animate({
                scrollTop: 0
            }, 1000);
            if( jQuery(this).hasClass("twbb-sg-sidebar-navigator-menu-li") ) {
                last_selected = 'other';
            }
        }
        jQuery('.twbb-sg-navigation-item').removeClass('selected');
        let data_type = jQuery(this).attr('data-type');
        let post_id = jQuery(this).attr('data-post_id');
        let this_navigate_item = jQuery('.twbb-sg-navigation-item[data-type=' + data_type + ']');
        this_navigate_item.addClass('selected');
        //var navigated_content = jQuery('.twbb-sg-sidebar-navigated-contents-container .twbb-sg-sidebar-navigated-content[data-type="'+data_type+'"]');
        var navigated_content = jQuery('.twbb-sg-sidebar-navigated-contents-container .twbb-sg-sidebar-navigated-content[data-type="all"]'); //one iframe version
        jQuery('.twbb-sg-sidebar-navigated-contents-container .twbb-sg-sidebar-navigated-content.selected').removeClass('selected');
        if( data_type !== 'generate-with-ai' ) {
            jQuery('.twbb-sg-sidebar-navigated-contents-container').removeClass('twbb-sg-sidebar-navigated-contents-container-ai-generated');
            if( navigated_content.find('iframe').contents().find('body').html() === '' || navigated_content.html() === '' ) {
                twbb_create_preview_iframe(data_type, navigated_content, post_id);
            }
            navigated_content.find('iframe').contents().find('body').attr('data-twbb-please-show-sections', data_type );
            var dataValue = navigated_content.find('iframe').contents().find('body').attr('data-twbb-please-show-sections');

// Now, use this value to select all elements whose id contains this value
            navigated_content.find('iframe').contents().find('.twbb-sg-each-section').removeClass('twbb-visible');
            navigated_content.find('iframe').contents().find('body').attr('data-twbb-please-show-sections', data_type );
            navigated_content.find('iframe').contents().find('.twbb-sg-each-section[id*="/' + data_type + '/"]').addClass('twbb-visible');
            navigated_content.find('iframe').contents().find('html, body').animate({ scrollTop: 0 }, 'slow');
        } else {
            jQuery('.twbb-sg-sidebar-navigated-contents-container').addClass('twbb-sg-sidebar-navigated-contents-container-ai-generated');
            jQuery('.custom-select-panel .custom-select-option[data-value="' + last_selected + '"]').trigger('click');
        }

        navigated_content.addClass('selected');
        navigated_content.attr( 'style','height:calc( 100% + 90px - ' + navigated_content.offset().top + 'px)');
        navigated_content.find('iframe').attr( 'style','height:calc( 4 * ' + navigated_content.height() + 'px)');
    });

    //one iframe version
    jQuery(document).on('click','.twbb-sg-generate-with-ai-button', function() {
        if( jQuery(this).hasClass('disabled')
            || jQuery('.twbb-sg-sidebar-navigated-contents-container').hasClass('twbb-some-section-in-process')
            || jQuery('.twbb-sg-sidebar-navigated-content iframe').contents().find('body').hasClass('twbb-some-section-in-process') ) {
            return;
        }
        jQuery(this).addClass('disabled');

        let selected_section_type, user_description, add_section_at = -1;
        selected_section_type = jQuery('.twbb-sg-sidebar-generated-with-ai .custom-select-panel .custom-select-option.is-selected').attr('data-value');
        let unique_id = Math.random().toString(36).slice(2, 10);
        jQuery('.twbb-sg-sidebar-generated-with-ai').attr('data-unique_id', unique_id);
        analyticsDataPush(
            'Generate With AI Button from sidebar',
            'Section Generation',
            selected_section_type,
            {'unique_id': unique_id},
        );
        user_description = jQuery('.twbb-generate-section_description').val();
        if( jQuery('#elementor-preview-iframe').contents()
            .find('.elementor-add-section:not(#elementor-add-new-section)').length ) {
            add_section_at = jQuery('#elementor-preview-iframe').contents()
                .find('.elementor-add-section:not(#elementor-add-new-section)').index();
        }
        let closest_sections_data = collect_data_for_request(add_section_at, selected_section_type, user_description);
        jQuery('.twbb-sg-sidebar-navigated-contents-container').addClass('twbb-some-section-in-process twbb-sg-generation-in-process');
        jQuery('.twbb-sg-sidebar-navigated-contents-container .twbb-sg-sidebar-navigated-content iframe').each(function(){
            jQuery(this).contents().find('body').addClass('twbb-some-section-in-process');
        })
        jQuery('.twbb-sg-loading-container .step-4').removeClass('active');
        if( !jQuery('.twbb-sg-sidebar-generated-with-ai_overlay').length ) {
            jQuery('.twbb-sg-sidebar-generated-with-ai').append(twbb_generating_overlay);
        }
        move_progress_bar(0);
        var data = {
            'closest_sections_data' : JSON.stringify(closest_sections_data),
            'user_description' :  user_description,
            'section_type': selected_section_type,
            'action': "twbb_generate_with_ai_section_template",
            'nonce': twbb_sg_editor.twbb_sg_nonce,
            'unique_id': unique_id,
        }

        jQuery.ajax({
            type: 'POST',
            url: twbb_sg_editor.ajaxurl,
            dataType: 'json',
            data: data
        }).success(function(res){
            if( res.data['status'] === 'success' ) {

                /*  old version changed to get full data from ML team */
                // let params = res.data['params'];
                // let section_path = res.data['section_path'];
                // console.log('Got generated variation path ' + section_path, params);
                // twbb_request_generated_data( params, section_path, add_section_at, 'do_not_insert' );

//new version this code is duplicated
                console.log('Got generated section post ID ' + res.data['post_id']);
                let generated_content_iframe = jQuery('.twbb-generate-with-ai-iframes');
                if( generated_content_iframe.length ) {
                    generated_content_iframe.html('');
                    twbb_create_preview_iframe('ai-generated-sections', generated_content_iframe, res.data['post_id']);
                    jQuery('.twbb-ready-text').css('display', 'block');
                    //remove overlay
                    generated_content_iframe.find('iframe').on('load', function () {
                        twbb_remove_ai_generated_process_classes();
                        // if( section_path.indexOf('hero') < 0 ) {
                        //     generated_content_iframe.find('iframe').contents().find('.elementor-element.twbb-sg-each-section[id*=ai-generated-sections] > div.elementor-element[data-element_type=container]').attr('style','min-height:unset !important');
                        // }
                    });
                    let button_input = jQuery('.twbb-generate-with-ai-button-input');
                    if( button_input.length ) {
                        var parentDiv = button_input.parent();
                        var elementOffset = button_input.offset().top - parentDiv.offset().top - 50;
                        if( elementOffset > 0 ) {
                            parentDiv.animate({
                                scrollTop: elementOffset
                            }, 1000);
                        }
                    }
                }


            }
        }).error(function () {
            console.log('Something Wrong when getting generated section data for request.');
        });
    });

    // Add event listener to the textarea
    jQuery(document).on('keyup', '.twbb-generate-section_description', function() {
        let charLimit = 1000;
        let currentLength = jQuery(this).val().length;

        if (currentLength >= charLimit) {
            jQuery('.char-limit-warning').show();
        } else {
            jQuery('.char-limit-warning').hide();
        }
    });
});

/* Function run mutation observation to detect when topbar is available in dom and then add builder elements in the topbar */
function add_sections_button_topbar() {
    const $targetNode = jQuery('#elementor-editor-wrapper-v2')[0];
    // Function to add the custom button if it doesn't already exist
    const checkAndAddButton = () => {
        // Select the parent container of the "Add Element" button
        const header_add_element_button = jQuery('.MuiToolbar-root .MuiBox-root .MuiGrid-root:first-child .MuiStack-root:eq(1) .MuiBox-root:first-child button');

        if (header_add_element_button.length) {
            // Check if the custom button already exists to avoid adding it multiple times
            if (!jQuery('#twbb-custom-button').length) {
                let twbb_sg_header_button_template = jQuery('#twbb-sg-header-button-template').html();

                // Create the button, ensuring it has a unique ID or class
                const customButton = jQuery(twbb_sg_header_button_template).attr('id', 'twbb-custom-button');

                // Append the custom button after the "Add Element" button
                header_add_element_button.parent().after(customButton);
            }
        }
    };

    // Use a mutation observer to ensure the button gets added even if the DOM changes
    const observer = new MutationObserver((mutationsList, obs) => {
        mutationsList.forEach((mutation) => {
            if (mutation.type === 'childList') {
                checkAndAddButton();
            }
        });
    });

    // Start observing the target node
    observer.observe($targetNode, { childList: true, subtree: true });

    // Automatically stop observing after 20 seconds
    setTimeout(() => {
        observer.disconnect(); // Stop observing
    }, 20000); // 20 seconds in milliseconds

    // Initial check in case the button is already loaded
    checkAndAddButton();
}

function twbb_sections_reinstall() {
    jQuery.ajax({
        type: 'POST',
        url: twbb_sg_editor.ajaxurl,
        dataType: 'json',
        data: {
            'action': "twbb_sections_reinstall",
            'nonce': twbb_sg_editor.twbb_sg_nonce,
        }
    }).success(function (result) {
        if( !result.success ) {
            jQuery(document).find(".twbb-sg-sidebar-empty-loading-content").hide();
            jQuery(document).find(".twbb-sg-sidebar-empty-error-content").show();
            twbb_sg_editor.sections_exists = '';
        } else {
            /* Need timout to finish section folders/files creation */
            setTimeout(function(){ window.location.reload(); }, 3000);
        }
    }).error(function () {
        jQuery(document).find(".twbb-sg-sidebar-empty-loading-content").hide();
        jQuery(document).find(".twbb-sg-sidebar-empty-error-content").show();
        return false;
    });

}

function twbb_animate_sidebar(open) {
    if (open) {
        jQuery('.twbb-sg-sidebar').css('display', 'block');

        setTimeout(function() {
            let windowWidth = jQuery(window).width();
            let iframeWidth = windowWidth - 524;
            let elementor_panel = jQuery("#elementor-panel").width();
            jQuery("#elementor-preview").css({
                "width": iframeWidth + 'px',
                "margin-left": 522 - parseInt(elementor_panel) + 'px',
            });
            jQuery("body").addClass('twbb-sg-sidebar-opened');
            twbbIframeScale(1, 522);
            jQuery("#elementor-editor-wrapper").addClass('twbb-animate-sidebar-open');
            if( !jQuery('.twbb-sg-sidebar-generated-with-ai').hasClass('.selected') ) {
                jQuery('.twbb-sg-navigation-item[data-type="generate-with-ai"]').trigger('click');
            }
        }, 500);
    } else {
        twbb_close_section_generation();
        setTimeout(function() {
            jQuery("#elementor-editor-wrapper").removeClass('twbb-animate-sidebar-open');
        },500);

        jQuery("#elementor-preview").removeAttr("style");
        /* do not scale when option is deactive */
        if( typeof twbb_options.smart_scale_option !== "undefined" && twbb_options.smart_scale_option === 'active' ) {
            twbbIframeScale(1);
        } else {
            twbbIframeScale(0);
        }
        jQuery("body").removeClass('twbb-sg-sidebar-opened');

    }
}

function twbb_create_preview_iframe(data_type, parent_element, post_id) {
    let view_data_type = data_type;
    if( data_type !== 'ai-generated-sections' ) {
        let lazy_load_template = jQuery(document).find("#twbb-sg-iframe-lazy-load-template").html();
        parent_element.append(lazy_load_template);
        view_data_type = 'all';
    }
    if( data_type === 'ai-generated-sections' ) {
        let url = twbb_get_iframe_url(view_data_type);
        let iframe = '<iframe src="' + url.href + '" scrolling="no"></iframe>';
        parent_element.append(iframe);
    }
    if( data_type !== 'ai-generated-sections' ) {
        if( window.AllSectionsIframeContent !== undefined && jQuery('#twbb-sg-all-sections-iframe').contents().find('body').html() === '' ) {
            var TWBBiframe = document.getElementById('twbb-sg-all-sections-iframe');
            injectIframeContent(TWBBiframe, window.AllSectionsIframeContent, data_type);
        } else {
            let url = twbb_get_iframe_url(view_data_type)
            parent_element.find('iframe').remove();
            let iframe = '<iframe id="twbb-sg-all-sections-iframe" src="' + url.href + '" scrolling="no"></iframe>';
            parent_element.append(iframe);
            var TWBBiframe = document.getElementById('twbb-sg-all-sections-iframe');
        }
        TWBBiframe.onload = function(){
            setTimeout(function() {
                twbb_after_iframe_load(TWBBiframe, data_type);
            },1000);
        };
    }
}

function twbb_close_section_generation() {
    jQuery('.twbb-sg-header-button-container').removeClass('selected');
    jQuery('.MuiButtonBase-root[aria-label="Add Element"]').addClass('Mui-selected');
    jQuery('.twbb-sg-sidebar').css('display', 'none');
}

//function is created as elementor insert template command
function import_twbb_generated_template(at, data) {
    let TemplateLibraryTemplateModel = Backbone.Model.extend({
        defaults: {
            template_id: 0,
            title: '',
            source: '',
            type: '',
            subtype: '',
            author: '',
            thumbnail: '',
            url: '',
            export_link: '',
            tags: []
        }});

    var template_model = new TemplateLibraryTemplateModel({});
    window.$e.run('document/elements/import', {
        model: template_model,
        data: data,
        options: {at:at, withPageSettings: null}
    });
}

function twbb_remove_iframe_in_process_classes() {
    //remove in process classes
    jQuery('.twbb-sg-sidebar-navigated-contents-container .twbb-sg-sidebar-navigated-content iframe')
        .contents().find('.twbb-sg-each-section').removeClass('twbb-the-sections-generation-in-process');
    twbb_generate_with_ai_navigation_tab_status('enable');
    jQuery('.twbb-sg-sidebar-navigated-contents-container').removeClass('twbb-some-section-in-process twbb-sg-generation-in-process');
    jQuery('.twbb-sg-sidebar-navigated-contents-container .twbb-sg-sidebar-navigated-content iframe').each(function(){
        jQuery(this).contents().find('body').removeClass('twbb-some-section-in-process');
    })
}

function twbb_remove_ai_generated_process_classes() {
    jQuery('.twbb-sg-sidebar-navigated-contents-container').removeClass('twbb-sg-generation-in-process twbb-some-section-in-process');
    //jQuery('.twbb-generate-with-ai-iframes iframe').contents().find('body').removeClass('twbb-some-section-in-process');
    jQuery('.twbb-sg-sidebar-navigated-contents-container .twbb-sg-sidebar-navigated-content iframe').each(function(){
        jQuery(this).contents().find('body').removeClass('twbb-some-section-in-process');
    })
    twbb_generate_with_ai_navigation_tab_status('enable');
    jQuery('.twbb-sg-loading-container .step-4').removeClass('active');
}

function getTagTexts(section, tags_list = ['h1', 'h2', 'h3']) {
    let titles = {};
    tags_list.forEach(tag => {
        let texts = '';
        jQuery(section).find(tag).each(function(){
            texts += jQuery(this).text() + ',';
        })
        if (texts.endsWith(',')) {
            texts = texts.slice(0, -1);
        }
        if (texts !== '') {
            titles[tag] = texts;
        }
    });

    return titles;
}

function get_section_data(wrap_div, section_position) {
    let section = wrap_div.children()[section_position], section_data = {},
        iframe = jQuery('#elementor-preview-iframe');
    section_data['section_type'] = '';
    if( section_position === 'header' ) {
        section_data['section_type'] = 'header';
        if( iframe.contents()
            .find('div[data-elementor-type="twbb_header"]>div[data-element_type="container"]:last-child').length ) {
            section = iframe.contents().find('div[data-elementor-type="twbb_header"]>div[data-element_type="container"]:last-child');
        } else if( iframe.contents()
            .find('div[data-elementor-type="twbb_header"]>.elementor-section-wrap>div[data-element_type="container"]:last-child').length ) {
            section = iframe.contents().find('div[data-elementor-type="twbb_header"]>.elementor-section-wrap>div[data-element_type="container"]:last-child');
        }
    } else if( section_position === 'footer' ) {
        section_data['section_type'] = 'footer';
        if( iframe.contents()
            .find('div[data-elementor-type="twbb_footer"]>div[data-element_type="container"]:first-child').length ) {
            section = iframe.contents().find('div[data-elementor-type="twbb_footer"]>div[data-element_type="container"]:first-child');
        } else if( iframe.contents()
            .find('div[data-elementor-type="twbb_footer"]>.elementor-section-wrap>div[data-element_type="container"]:first-child').length ) {
            section = iframe.contents().find('div[data-elementor-type="twbb_footer"]>.elementor-section-wrap>div[data-element_type="container"]:first-child');
        }
    } else if( section_position === 'last' ) {
        section = wrap_div.children()[wrap_div.children().length - 1 ];
    }

    section_data['section_description'] = '';
    section_data['section_title'] = getTagTexts(section);
    section_data['background_color'] = jQuery(section).css('background-color');
    let background_media = 'none';
    if( jQuery(section).find('.elementor-background-slideshow').length ) {
        background_media = 'slider';
    } else if ( jQuery(section).find('iframe.elementor-background-video-embed').length ) {
        background_media = 'video';
    } else if ( jQuery(section).css('background-image') !== 'none' ){
        background_media = 'image';
    }
    section_data['background_media'] = background_media;

    return section_data;
}

function collect_data_for_request(section_position, selected_section_type = '', user_description = '') {
    let page_title = '', wrap_div,previews_section, next_section, iframe = jQuery('#elementor-preview-iframe');
    page_title = iframe.contents().find('head title').text();
    wrap_div = iframe.contents().find('div.elementor.elementor-edit-area.elementor-edit-mode.elementor-edit-area-active div.elementor-section-wrap');
    if( section_position === -1 ) {
        previews_section = get_section_data(wrap_div,'last');
        next_section = get_section_data(wrap_div,'footer');
    } else if( section_position === 0 ) {
        previews_section = get_section_data(wrap_div,'header');
        next_section = get_section_data(wrap_div,'last');
    } else {
        previews_section = get_section_data(wrap_div,section_position - 1);
        next_section = get_section_data(wrap_div,section_position + 1);
    }

    let data = {
        'current_section' : {
            'section_description': user_description,
            'section_title': selected_section_type,
            'section_type': selected_section_type
        },
        'context': {
            'page_title': page_title,
            'previous_section': previews_section,
            'next_section': next_section,
            'business_description': twbb_sg_editor.business_description,
            'business_name': twbb_sg_editor.business_name,
            'business_type': twbb_sg_editor.business_type
        }
    };

    return data;
}

function move_progress_bar( k = 0 ) {
    if (k == 0) {
        k = 1;
        var elem = document.getElementById("loading-progress-bar");
        var width = 1;
        var id = setInterval(frame, 60);
        function frame() {
            if (width >= 90) {
                clearInterval(id);
                k = 0;
            } else {
                width++;
                elem.style.width = width + "%";
            }
        }
    }
    let i = 2, j = 1;
    jQuery('.twbb-sg-loading-container .step-1').addClass('active');
    let set_loading = setInterval(function() {
        if(i < 5) {
            jQuery('.twbb-sg-loading-container .step-' + j).removeClass('active');
            jQuery('.twbb-sg-loading-container .step-' + i).addClass('active');
            i++;
            j++;
        } else {
            clearInterval(set_loading); // stop the interval when i is no longer less than 5
        }
    }, 2000);
}

function set_content_generation_status() {
    let user_desc = jQuery('.twbb-generate-section_description').val();
    let business_desc = twbb_sg_editor.business_description;
    if (!user_desc && !business_desc ) {
        if( !jQuery('.twbb-sg-sidebar-navigated-content-header-button.twbb-sg-generate-with-ai-button').hasClass('disabled')) {
            jQuery('.twbb-sg-sidebar-navigated-content-header-button.twbb-sg-generate-with-ai-button').addClass('disabled');
        }
    } else if( user_desc || business_desc ) {
        if( !jQuery('.twbb-sg-sidebar-navigated-contents-container').hasClass('twbb-some-section-in-process')
            && !jQuery('.twbb-sg-sidebar-navigated-content iframe').contents().find('body').hasClass('twbb-some-section-in-process') ) {
            jQuery('.twbb-sg-sidebar-navigated-content-header-button.twbb-sg-generate-with-ai-button').removeClass('disabled');
        }
    }
}

function scroll_to_editable_element() {
    let preview_iframe = jQuery('#elementor-preview-iframe').contents();
    if (preview_iframe.find('.elementor-element-editable').length) {
        if ( typeof coPilot !== 'undefined' && coPilot.newAddedWidgetModelId !== 'undefined' ) {
            coPilot.newAddedWidgetModelId = preview_iframe.find('.elementor-element-editable').attr("data-id");
        }
        let offsetTop = preview_iframe.find('.elementor-element-editable').offset().top;
        var adjustedOffsetTop = offsetTop - 100; // Adjust this value as needed
        preview_iframe.find('html,body').animate({
            scrollTop: adjustedOffsetTop
        }, 1000);
    }
}

function twbb_insert_section_premade( add_section_at, data ) {
    jQuery.ajax({
        type: 'POST',
        url: twbb_sg_editor.ajaxurl,
        dataType: 'json',
        data: data
    }).success(function (res) {
        if (res.data['status'] === 'success') {
            import_twbb_generated_template(add_section_at, res.data['params']);
            twbb_remove_iframe_in_process_classes();
            scroll_to_editable_element();
        }
    }).error(function () {
        console.log('Something Wrong when getting generated section data for request.');
        twbb_remove_iframe_in_process_classes();
    });
}

function twbb_generate_with_ai_navigation_tab_status(status = '') {
    if( status === 'disable' ) {
        jQuery('.twbb-sg-sidebar-navigated-content-header-button.twbb-sg-generate-with-ai-button').addClass('disabled');
        jQuery('.twbb-sg-sidebar-generated-with-ai .custom-select-container').addClass('is-disabled');
        jQuery('.twbb-sg-sidebar-generated-with-ai textarea').attr('disabled', true);
        if( !jQuery('.twbb-generate-with-ai-iframes iframe').length ) {
            jQuery('.twbb-generate-not-available').css('display', 'flex');
        }
    } else {
        jQuery('.twbb-sg-sidebar-navigated-content-header-button.twbb-sg-generate-with-ai-button').removeClass('disabled');
        jQuery('.twbb-sg-sidebar-generated-with-ai .custom-select-container').removeClass('is-disabled');
        jQuery('.twbb-sg-sidebar-generated-with-ai textarea').attr('disabled', false);
        jQuery('.twbb-generate-not-available').css('display', 'none');
    }
}

function twbb_trigger_sections_button(element) {
    if( element.hasClass('disabled') ) {
        return;
    }
    if( element.hasClass('selected') ) {
        twbb_animate_sidebar(0);
        return;
    }

    /* Open activate kit popup */
    if ( !twbb_options.show_ultimate_kit && typeof theme_Customize !== 'undefined' ) {
        theme_Customize.openCustomizeEnablePopup();
        return false;
    }

    element.addClass('selected');
    let header_add_element_button = jQuery('.MuiToolbar-root .MuiBox-root .MuiGrid-root:first-child .MuiStack-root:eq(1) .MuiBox-root:first-child button');
    header_add_element_button.removeClass('Mui-selected');
    //open section generation
    if ( !jQuery('.twbb-sg-sidebar').length ) {
        let section_generation_sidebar = jQuery('#twbb-sg-sidebar-template').html();
        jQuery('#elementor-editor-wrapper-v2').append(section_generation_sidebar);
        //accept custom-select
        jQuery("#twbb-select-generate-section_types").customSelect();
        if( window.AllSectionsIframeContent !== undefined && jQuery('#twbb-sg-all-sections-iframe').contents().find('body').html() === '' ) {
            var TWBBiframe = document.getElementById('twbb-sg-all-sections-iframe');
            injectIframeContent(TWBBiframe, window.AllSectionsIframeContent, 'all');
        }
    }

    //accept custom-select
    jQuery("#twbb-select-generate-section_types").customSelect();
    twbb_animate_sidebar(1);

    if( twbb_sg_editor.sections_exists == "no" ) {
        twbb_sections_reinstall();
    }
}

// Function to preload iframe content
function preloadIframeContent(iframeUrl) {
    return fetch(iframeUrl)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text();
        })
        .then(htmlContent => {
            return htmlContent;
        })
        .catch(error => {
            console.error('There has been a problem with your fetch operation:', error);
        });
}

// Function to inject preloaded content into the iframe
function injectIframeContent(TWBBiframe, content, data_type) {
    const iframeDoc = TWBBiframe.contentDocument || TWBBiframe.contentWindow.document;
    iframeDoc.open();
    iframeDoc.write(content);
    iframeDoc.close();
}

function twbb_preload_sections_iframe_content() {
    let url = twbb_get_iframe_url('all');
    // Example usage
    preloadIframeContent(url).then(content => {
        window.AllSectionsIframeContent = content;
    });
}

function twbb_after_iframe_load(iframe, data_type) {
    jQuery(iframe).contents().find('body')
        .attr('data-twbb-please-show-sections', data_type );
    var dataValue = jQuery(iframe).contents().find('body').attr('data-twbb-please-show-sections');
    if (twbb_sg_editor.woocommerceActiveStatus ) {
        jQuery(iframe).contents().find('body').addClass('twbb-show-woocommerce-sections');
    }
    jQuery(document).find(".twbb-sg-iframe-lazy-load-layer, .twbb-sg-iframe-lazy-load-container").remove();
    jQuery(iframe).contents().find('.twbb-sg-each-section[id*="' + dataValue + '"]').addClass('twbb-visible');
}

function twbb_get_iframe_url(view_data_type) {
    let url = new URL(twbb_editor.page_permalink);
    url.searchParams.set('twbb_sg_preview', 'generated_sections_' + view_data_type);
    url.searchParams.set('twbb_template_preview', view_data_type);
    url.searchParams.set('twbb_template_preview_from', twbb_editor.post_id);
    url.searchParams.set('twbb_template_preview_nonce', twbb_editor.template_preview_nonce);
    return url;
}
