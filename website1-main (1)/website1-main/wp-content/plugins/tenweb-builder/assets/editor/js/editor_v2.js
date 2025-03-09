jQuery(document).ready(function() {
    // add 10web builder menu
    if ( !jQuery('.twbb-top-bar-icon-parent').length ) {
        jQuery('#elementor-editor-wrapper-v2 .MuiGrid-root:nth-child(1)').prepend(
            '<div class="eui-stack twbb-top-bar-icon-parent">' + '<span class="twbb-top-bar-icon">' +
            '<span></span><span class="twbb-vertical-row"></span><span class="twbb-dropdown-icon"></span></span></div>');
    }
    setTimeout(function(){
        jQuery('#elementor-editor-wrapper-v2 .MuiGrid-root:nth-child(3) > div:eq(1) > button')
            .prepend('<span class="twbb-added-text-to-button" style="padding-right:5px">Save &</span>')
            .on('click', function(){
                jQuery('.twbb-added-text-to-button').parent().addClass('twbb-save-loader-visible');
            })
            .css('min-width', '158px');
    },3000);

    jQuery('.MuiToolbar-root .MuiBox-root .MuiGrid-root:first-child .MuiStack-root:eq(1) .MuiBox-root:first-child button')
        .html('<span class="twbb_changed-elements-plus">+</span>Elements')
        .css({
            'border-radius': '8px',
            'padding': '7px 10px',
            'letter-spacing': '0.1px',
            'font-size': '12px',
            'line-height': '18px',
        });
    request_developer_show = false;
    request_developer_popup = false;
    if ( twbb_options.is_tenweb_hosted == '1' && twbb_options.is_ai_plan == '1' && false ) {
        request_developer_popup = true;
        request_developer_show = true;
    } else if( twbb_options.is_tenweb_hosted == '1' && twbb_options.is_profesional_plan == '1' && false ){
        request_developer_popup = false;
        request_developer_show = true;
    }
    if ( request_developer_show ) {
        if (request_developer_popup) {
            jQuery('#elementor-editor-wrapper-v2 .MuiGrid-root:nth-child(3)')
                .prepend('<a href="#" requested-from="top_bar" class="twbb-top-menu-request-developer twbb-icon-with-tooltip twbb-action-request-developer"></a>');
        } else {
            jQuery('#elementor-editor-wrapper-v2 .MuiGrid-root:nth-child(3)')
                .prepend('<a href="' + twbb_options.request_developer_url + '?requested=top_bar" target="_blank" class="twbb-top-menu-request-developer twbb-icon-with-tooltip"></a>');
        }
    }
    jQuery('#elementor-editor-wrapper-v2 .twbb-top-menu-request-developer')
        .append('<div class="twbb-top-bar-tooltip-container"><span class="twbb-top-bar-tooltip">Request a developer</span></div> ');

    //  hide elementor logo
    jQuery('#elementor-editor-wrapper-v2 .MuiGrid-root:nth-child(1) div:nth-child(2)').css({
        'position': 'absolute',
        'top': '8px',
        'opacity': 0
    });

    //set trigger elementor menu opening via our menu item
    jQuery('#elementor-editor-wrapper-v2 .twbb-top-bar-icon').click(function(){
        jQuery('#elementor-editor-wrapper-v2 .MuiGrid-root:nth-child(1) div:nth-child(2) button').trigger('click');
    });

    jQuery(document).on('click','#elementor-editor-wrapper-v2 .twbb-top-bar-icon', function() {
        jQuery('#elementor-v2-app-bar-main-menu .MuiMenu-list').parent().addClass("twbb-topbar-navigation-container");
        //add 10web dashboard link to menu
        setTimeout(function () {
            if ( !jQuery('.twbb-main-menu-10web-dashboard').length ) {
                jQuery('#elementor-v2-app-bar-main-menu .MuiMenu-list>div:first-child').remove();
                jQuery('.twbb-top-bar-icon .twbb-dropdown-icon').addClass('twbb-rotated-icon');
                jQuery('#elementor-v2-app-bar-main-menu .MuiMenu-list').append('' +
                    '<a class="MuiButtonBase-root MuiMenuItem-root MuiMenuItem-gutters MuiMenuItem-root ' +
                    'MuiMenuItem-gutters eui-menu-item twbb-main-menu-10web-dashboard twbb-main-menu-items"' +
                    ' href="' + twbb_options.dashboard_url + '" target="_blank">' + twbb_options.dashboard_text + '</a>');
                if ( request_developer_show ) {
                    if (request_developer_popup) {
                        jQuery('#elementor-v2-app-bar-main-menu .MuiMenu-list').append('' +
                            '<a class="MuiButtonBase-root MuiMenuItem-root MuiMenuItem-gutters MuiMenuItem-root ' +
                            'MuiMenuItem-gutters eui-menu-item twbb-main-menu-request-developer twbb-action-request-developer twbb-main-menu-items"' +
                            ' href="#" requested-from="top_menu">' + twbb_options.request_developer_text + '</a>');
                    } else {
                        jQuery('#elementor-v2-app-bar-main-menu .MuiMenu-list').append('' +
                            '<a class="MuiButtonBase-root MuiMenuItem-root MuiMenuItem-gutters MuiMenuItem-root ' +
                            'MuiMenuItem-gutters eui-menu-item twbb-main-menu-request-developer twbb-main-menu-items"' +
                            ' href="' + twbb_options.request_developer_url + '?requested=top_menu" target="_blank">' + twbb_options.request_developer_text + '</a>');
                    }
                }


                let templ = jQuery('#elementor-preview-iframe').contents().find('#twbb_topbar-buttons-template').html();
                jQuery('#elementor-v2-app-bar-main-menu .MuiMenu-list').append(templ);


                jQuery(document).find('#elementor-v2-app-bar-main-menu .MuiMenu-list .MuiMenuItem-root:nth-child(1)').unbind('click').on('click',function(){
                    analyticsDataPush ( '10Web Builder menu item', '10Web Builder menu item', 'History' );
                    jQuery(this).off('click');
                });
                jQuery(document).find('#elementor-v2-app-bar-main-menu .MuiMenu-list .MuiMenuItem-root:nth-child(2)').unbind('click').on('click',function(){
                    analyticsDataPush ( '10Web Builder menu item', '10Web Builder menu item', 'User preferences,' );
                    jQuery(this).off('click');
                });
                jQuery(document).find('#elementor-v2-app-bar-main-menu .MuiMenu-list .MuiMenuItem-root:nth-child(3)').unbind('click').on('click',function(){
                    analyticsDataPush ( '10Web Builder menu item', '10Web Builder menu item', 'Keyboard Shortcuts' );
                    jQuery(this).off('click');
                });
                jQuery(document).find('#elementor-v2-app-bar-main-menu .MuiMenu-list .MuiMenuItem-root:nth-child(4)').unbind('click').on('click',function(){
                    analyticsDataPush ( '10Web Builder menu item', '10Web Builder menu item', 'Exit to WordPress' );
                    jQuery(this).off('click');
                });
                jQuery(document).find('#elementor-v2-app-bar-main-menu .MuiMenu-list .MuiMenuItem-root:nth-child(5)').unbind('click').on('click',function(){
                    analyticsDataPush ( '10Web Builder menu item', '10Web Builder menu item', '10Web Dashboard' );
                    jQuery(this).off('click');
                });


            }}, 50);
    });

    jQuery(document).on('click','#elementor-v2-app-bar-main-menu',function() {
        jQuery('.twbb-top-bar-icon .twbb-dropdown-icon').removeClass('twbb-rotated-icon');
    });

    // change recent pages list to our website structure
    jQuery(document).on('click','#elementor-editor-wrapper-v2 .MuiGrid-root:nth-child(2) button:eq(0)', function() {
        analyticsDataPush ( 'Site Structure', 'Site Structure' );
        setTimeout(function() {
            let rootList = jQuery('#elementor-v2-top-bar-recently-edited .MuiList-root');
            rootList.parent().addClass('twbb-topbar-structure-container')
            rootList.css('background-color','#0B0D0D');
            rootList.empty();

            //let websiteStructure = jQuery('#elementor-preview-iframe').contents().find('.twbb_website_structure_top_bar').clone();
            let websiteStructure = jQuery('#elementor-preview-iframe').contents().find('#twbb_website_structure_top_bar-template').html();
            rootList.empty();
            rootList.append(websiteStructure);

            let buttonsTemplate = jQuery('#elementor-preview-iframe').contents().find('#twbb_topbar-buttons-template').html();
            rootList.append(buttonsTemplate);

            jQuery('.twbb_sub_menu .site_menu .twbb-website-structure-sub').trigger('click');
        }, 30);
    });

    jQuery(document).on('click', '.MuiList-root .twbb_website_structure_top_bar .twbb-website-structure-sub',function(){
        if(jQuery(this).parent().find(".title_container").hasClass('opened')) {
            jQuery(this).parent().find(".title_container").removeClass('opened');
            jQuery(this).parent().find(".title_container").addClass('closed');
            jQuery(this).parent().removeClass('active');
            jQuery(this).parent().find(".twbb-dropdown-icon").removeClass("twbb-rotated-icon");
        } else {
            jQuery(this).parent().find(".title_container").removeClass('closed');
            jQuery(this).parent().find(".title_container").addClass('opened');
            jQuery(this).parent().addClass('active');
            jQuery(this).parent().find(".twbb-dropdown-icon").addClass("twbb-rotated-icon");
        }
    });

    //add condition on public icon
    if( twbb_options.header_button == 'condition' && !jQuery('.MuiDivider-root .twbb_advanced').length ) {
        jQuery('#elementor-editor-wrapper-v2 .MuiGrid-root:nth-child(3) > div:eq(1) span').click(function () {
            setTimeout(function() {
                jQuery('#document-save-options .MuiMenu-list').append(
                    '<hr class="MuiDivider-root MuiDivider-fullWidth eui-divider twbb_advanced_before_hr">' +
                    '<div class="MuiButtonBase-root MuiMenuItem-root MuiMenuItem-gutters MuiMenuItem-root ' +
                    'MuiMenuItem-gutters eui-menu-item twbb_advanced">' +
                    twbb_options.display_conditions_text + '</div>');
            }, 50);
        });

        jQuery(document).on('click', '.twbb_advanced', (function() {
            jQuery('.twbb-condition-popup-overlay').show();
            if (conditions_added === false) {
                saved_conditions_length = twbb_editor.conditions.length;

                if (saved_conditions_length > 0) {
                    show_popup_loading();
                }

                for (var i in twbb_editor.conditions) {
                    add_condtion_html(twbb_editor.conditions[i]);
                }
                conditions_added = true;
            }
        }));
    }

    //TODO move this popup texts to php
    jQuery(document).on('click','.twbb-action-request-developer', function() {
        requested_from = jQuery(this).attr('requested-from');
        let requestDeveloperPopup = '' +
            '<div class="twbb-rd-overlay">' +
            '<div class="twbb-rd-main-container"><span class="twbb-rd-close" onclick="destroy_popup()"></span>' +
            '<div class="twbb-rd-text-container">' +
            '<div class="twbb-rd-title">' +
            'Let <span class="twbb-rd-blue">10Web’s</span><span class="twbb-rd-green"> professionals</span>' +
            ' build<br> your ideal website, affordably</div>' +
            '<div class="twbb-rd-description">Our expert team delivers custom websites optimized for desktop<br>' +
            ' and mobile, making your business standout on every device.' +
            '</div>' +
            '<div class="twbb-rd-lists-container">' +
            '<div class="twbb-rd-list">' +
            '<p class="twbb-rd-list-item">A stunning website ready for your needs</p>' +
            '<p class="twbb-rd-list-item">Responsive design for all screen sizes</p>' +
            '<p class="twbb-rd-list-item">90+ PageSpeed score to boost SEO</p>' +
            '<p class="twbb-rd-list-item">One year free hosting</p>' +
            '<p class="twbb-rd-list-item">Forever free SSL certificate</p>' +
            '<p class="twbb-rd-list-item">3-rd party integrations</p>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '<div class="twbb-rd-button-conatiner">' +
            '<a href="' + twbb_options.request_developer_url +'?requested=' + requested_from +'" class="twbb-rd-redirect-button" target="_blank">Learn More</a>'
        '</div>' +
        '</div>' +
        '</div>';
        jQuery('body').append(requestDeveloperPopup);
    });


    /* No need to run actions in the Elementor preview iframe */
    if( !inIframe() ) {
        add_builder_buttons_to_topbar();

        jQuery(document).find(".twbb_undo").on("click", twbb_undo);
        jQuery(document).find(".twbb_redo").on("click", twbb_redo);


        window.$e.commands.on('run:before', function (component, command, args) {
            /* Case when manualy changed revision from the menu */
            if ( command == 'document/history/do' ) {
                twbb_check_undo_redo(args.index);
            }
        });
        window.$e.commandsInternal.on('run:before', function (component, command, args) {
            if (command == 'document/history/end-log') {
                let history = elementor.documents.getCurrent().history.items;
                if ( history.length ) {
                    twbb_check_undo_redo();
                }
            }
        });
    }

    disableElementorPromotionIntroduction();

    jQuery(document).on("click", ".twbb-add-blank-page-button", function() {
        /* Close opened menu */
        jQuery(document).find('#elementor-v2-top-bar-recently-edited > div.MuiBackdrop-invisible').trigger("click");
        let template = jQuery('#elementor-preview-iframe').contents().find("#twbb_new_blank_page-template").html();
        jQuery("body").append(template);
    });

    jQuery(document).on("click", ".twbb-new-blank-page-cancel-button, .twbb-new-blank-page-layout", function() {
        jQuery(document).find(".twbb-new-blank-page-layout").remove();
        jQuery(document).find(".twbb-new-blank-page-container").remove();
    });

    jQuery(document).on("click", ".twbb-new-blank-page-create-button, .twbb-new-blank-page-layout", function() {
        if( jQuery(this).hasClass("twbb-create-button-disabled" ) || jQuery(this).hasClass("twbb-create-button-loading" ) ) {
            return false;
        }
        twbb_create_blank_page(this);
    });


    jQuery(document).on("input", ".twbb-new-blank-page-input", function() {
        if( jQuery(this).val() == "" ) {
            jQuery(document).find(".twbb-new-blank-page-create-button").addClass("twbb-create-button-disabled");
        } else {
            jQuery(document).find(".twbb-new-blank-page-create-button").removeClass("twbb-create-button-disabled");
        }
    })
});

jQuery(window).on( "load", function() {
    if( typeof window.$e != 'undefined' ) {
        elementor.on( 'document:loaded', function () {
            $e.components.get('navigator').close();
        });
    }
});

function add_builder_buttons_to_topbar() {
    const $targetNode = jQuery('#elementor-editor-wrapper-v2')[0];
    // Function to add the custom button if it doesn't already exist
    const callback = () => {
        // Select the parent container of the "Add Element" button
        const header_add_element_button = jQuery('.MuiToolbar-root .MuiBox-root .MuiGrid-root:first-child .MuiStack-root:eq(1) .MuiBox-root:first-child button');

        if (header_add_element_button.length) {
            // Check if the custom button already exists to avoid adding it multiple times
            if (!jQuery('.twbb-customize-button').length) {
                jQuery(document).find("#elementor-editor-wrapper-v2 .MuiGrid-root:first-child .MuiStack-root:nth-child(3)").append("<span class='twbb-customize-button'></span>")
            }

            if (!jQuery('.twbb-undo-container').length) {
                add_undo_redo_buttons();
            }
        }
    };

    // Use a mutation observer to ensure the button gets added even if the DOM changes
    const observerTopBar = new MutationObserver((mutationsList, obs) => {
        mutationsList.forEach((mutation) => {
            if (mutation.type === 'childList') {
                callback();
            }
        });
    });

    // Start observing the target node
    observerTopBar.observe($targetNode, { childList: true, subtree: true });

    // Automatically stop observing after 20 seconds
    setTimeout(() => {
        observerTopBar.disconnect(); // Stop observing
    }, 20000); // 20 seconds in milliseconds

    // Initial check in case the button is already loaded
    callback();
}


function twbb_create_blank_page(that) {

    jQuery(that).addClass("twbb-create-button-loading");
    const pageData = {
        title: jQuery(document).find(".twbb-new-blank-page-input").val(),
        content: '',
        status: 'draft',
    };

    jQuery.ajax({
        url: twbb_options.restUrl, // URL from wp_localize_script
        method: 'POST',
        beforeSend: function (xhr) {
            xhr.setRequestHeader('X-WP-Nonce', twbb_options.restNonce); // Set the nonce for authentication
        },
        data: pageData,
        success: function (response) {
            jQuery(document).find(".twbb-new-blank-page-layout").remove();
            jQuery(document).find(".twbb-new-blank-page-container").remove();

            // Redirect to the Elementor edit page on success
            let editWithElementorUrl = twbb_options.adminUrl+'&post='+parseInt(response.id);
            window.open(editWithElementorUrl, '_blank');
        },
        error: function (error) {
            jQuery(that).removeClass("twbb-create-button-loading");
            console.log('Failed to create page: ' + error.responseJSON.message);
        },
    });
}

/* TODO move function to Elementor_upsell */
/**
 * Elementor keeps ai_promotion_introduction_editor_session_key in session storage and run Promotion if absent.
 * This function add key to session storage to prevent promotion run in Elementor
*/
function disableElementorPromotionIntroduction() {
    let editorSessionValue = sessionStorage.getItem('ai_promotion_introduction_editor_session_key');
    if( typeof window.EDITOR_SESSION_ID !== 'undefined' && !editorSessionValue ) {
        let currentTimestamp = new Date().getTime();
        sessionStorage.setItem('ai_promotion_introduction_editor_session_key', "".concat(window.EDITOR_SESSION_ID, "#").concat(currentTimestamp));
    }
}

function destroy_popup() {
    jQuery('.twbb-rd-overlay').remove();
}


/**
 *  Detect if the js action called from iframe JS as we includeing file in both places
 *  @return bool
* */
function inIframe() {
    try {
        return window.self !== window.top;
    } catch (e) {
        return true;
    }
}

/**
 *  Creating html content for buttons and addin to the topbar
* */
function add_undo_redo_buttons() {
    const conteinerDiv = document.createElement("div");
    conteinerDiv.classList.add('twbb-undo-container');

    const containerUndo = document.createElement("span");
    containerUndo.classList.add('twbb_undo','twbb-undo-disabled', 'twbb-icon-with-tooltip');

    const tooltipContainerUndo = document.createElement("div");
    tooltipContainerUndo.classList.add('twbb-top-bar-tooltip-container');

    const tooltipUndo = document.createElement("span");
    tooltipUndo.classList.add('twbb-top-bar-tooltip');
    const undoText = document.createTextNode("Undo");
    tooltipUndo.appendChild(undoText);

    tooltipContainerUndo.appendChild(tooltipUndo);
    containerUndo.appendChild(tooltipContainerUndo);

    const containerRedo = document.createElement("span");
    containerRedo.classList.add('twbb_redo','twbb-undo-disabled', 'twbb-icon-with-tooltip');

    const tooltipContainerRedo = document.createElement("div");
    tooltipContainerRedo.classList.add('twbb-top-bar-tooltip-container');
    const tooltipRedo = document.createElement("span");
    tooltipRedo.classList.add('twbb-top-bar-tooltip');
    const redoText = document.createTextNode("Redo");
    tooltipRedo.appendChild(redoText);

    tooltipContainerRedo.appendChild(tooltipRedo);
    containerRedo.appendChild(tooltipContainerRedo);

    conteinerDiv.appendChild(containerUndo);
    conteinerDiv.appendChild(containerRedo);

    const topBar = document.querySelector(".MuiGrid-root");
    if( topBar !== null ) {
        topBar.appendChild(conteinerDiv);
    }
}

/* Current key in Elementor history actions list */
let current_item_key = 0;

/**
 *  Checking if Elementor history(action) object has items and activate/decativate our undo/redo buttons
**/
function twbb_check_undo_redo( current_index ) {
    let undo_active = false;
    let redo_active = false;
    let histories = elementor.documents.getCurrent().history;
    let current_item_id = histories.currentItem.cid;

    let items = histories.getItems().models;
    if( typeof current_index != 'undefined' && current_index !== '') {
        current_item_id = items[current_index].cid;
    }
    for (const [key, value] of Object.entries(items)) {
        if( value.cid == current_item_id ) {
            current_item_key = key;
            break
        }
    }

    if( (items.length-1) > current_item_key ) {
        undo_active = true;
    }

    if( items.length > 0 && current_item_key > 0 ) {
        redo_active = true;
    }

    if( undo_active ) {
        jQuery(".twbb_undo").removeClass("twbb-undo-disabled");
    } else {
        jQuery(".twbb_undo").addClass("twbb-undo-disabled");
    }

    if( redo_active ) {
        jQuery(".twbb_redo").removeClass("twbb-undo-disabled");
    } else {
        jQuery(".twbb_redo").addClass("twbb-undo-disabled");
    }
}

/* undo elementor actions */
function twbb_undo() {
    if( jQuery(".twbb_undo").hasClass("twbb-undo-disabled") ) return;
    let undoIndex = parseInt(current_item_key) + 1;
    elementor.history.history.doItem(undoIndex);
    twbb_check_undo_redo();
}


/* redo elementor actions */
function twbb_redo() {
    if( jQuery(".twbb_redo").hasClass("twbb-undo-disabled") ) return;
    let redoIndex = parseInt(current_item_key) - 1;
    elementor.history.history.doItem(redoIndex);
    twbb_check_undo_redo();
}

function twbbSectionGenerationDeviceModeChanged(mode) {
    if( mode === 'desktop' ) {
        twbbIframeScale(1, 522);
    } else if( mode !== 'desktop') {
        twbbIframeScale(0);
        let windowWidth = jQuery(window).width();
        let iframeWidth = windowWidth - 524;
        let elementor_panel = jQuery("#elementor-panel").width();
        jQuery("#elementor-preview").css({
            "width": iframeWidth + 'px',
            "margin-left": 522 - parseInt(elementor_panel) + 'px',
        });
    }
}

jQuery(document).ready(function() {
    elementor.on( 'preview:loaded', function() {
        //fires when editor just loaded

        /* This functionality prevent menu items and logo click actions in preview iframe */
        let iframe = jQuery("#elementor-preview-iframe").contents();
        iframe.on("click","div[data-elementor-type='twbb_header'] .elementor-widget-twbb-nav-menu a," +
            "div[data-elementor-type='twbb_header'] .elementor-widget-tenweb-site-logo a," +
            "div[data-elementor-type='twbb_header'] .elementor-widget-image",function(e) {
            e.preventDefault();
            return false;
        })
        elementor.listenTo( elementor.channels.deviceMode, 'change', function(){
            let currentDeviceMode = elementor.channels.deviceMode.request( 'currentMode' );
            if( jQuery('body').hasClass('twbb-sg-sidebar-opened') ) {
                twbbSectionGenerationDeviceModeChanged(currentDeviceMode)
            } else {
                if (currentDeviceMode !== 'desktop') {
                    twbbIframeScale(0);
                } else {
                    twbbIframeScale(1);
                }
            }
        } );
        if( typeof twbb_options.smart_scale_option !== "undefined" && twbb_options.smart_scale_option === 'active' ) {
            const elementor_panel = jQuery("#elementor-panel");
            twbbIframeScale(1);
            elementor_panel.on( 'resizestop', function(){
                twbbIframeScale(1);
            });
            jQuery('#elementor-mode-switcher').on('click', function(e) {
                const activeMode = elementor.channels.dataEditMode.request( 'activeMode' );
                if( activeMode === 'edit' ) {
                    //because active mode is not switched yet, it is going to switch to 'preview'
                    twbbIframeScale(0, 0);
                } else {
                    twbbIframeScale(1);
                }
            });
            jQuery('.twbb-top-bar-icon, button[value="Add Element"], button[value="Site Settings"]').on('click', function() {
                twbbIframeScale(1);
            });
        }
    });
});

