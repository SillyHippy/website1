jQuery(document).ready(function() {
    appendEcommerceLabel();
    if ( window.parent.jQuery('.twbb-sg-sidebar-navigated-contents-container').hasClass('twbb-some-section-in-process') ) {
        jQuery('body').addClass('twbb-some-section-in-process');
    }

    var generation_messages = jQuery('#twbb-sg-messages').html();
    var sections_overlay = jQuery('#twbb-sg-overlay').html();
    jQuery(document).on('mouseenter', '.twbb-sg-each-section', function() {
        jQuery(this).addClass('twbb-sg-now-hovered');
        add_element_to_section_template_iframe( jQuery(this), '.twbb-sg-messages', generation_messages);
        add_element_to_section_template_iframe( jQuery(this), '.twbb-sg-overlay', sections_overlay);
        set_content_generation_status();
        var element = jQuery(this);
        setTimeout(function() {
            if( element.hasClass('twbb-sg-now-hovered') ) {
                element.addClass('twbb-sg-hovered');
            }
        }, 2000); // 2000ms = 2s
    });
    jQuery(document).on('mouseleave','.twbb-sg-each-section',function() {
        jQuery(this).removeClass('twbb-sg-now-hovered');
        jQuery(this).removeClass('twbb-sg-hovered');
    });

    //event for triggering generate section sidebar open from bottom of editor
    jQuery(document).on('click', '.elementor-add-twbb-section-generation-button', function() {
        if( window.parent.jQuery('.twbb-sg-header-button-container').hasClass('selected') ) {
            return;
        }
        analyticsDataPush(
            'Add Section',
            'Section Generation',
            'Inline'
        );
        window.parent.twbb_trigger_sections_button(window.parent.jQuery('.twbb-sg-header-button-container'));
    });

    jQuery(document).on('click',
        'body:not(.twbb-sg-with-description) .twbb-sg-each-section',
        function() {
            twbb_insert_section(jQuery(this), 'dummy_content');
        });
    jQuery(document).on('click', 'body.twbb-sg-with-description .twbb-sg-add-with-generated-content', function() {
        twbb_insert_section(jQuery(this).closest('.twbb-sg-each-section'), 'generate_content');
    });
    jQuery(document).on('click', 'body.twbb-sg-with-description .twbb-sg-add-with-dummy-content,.twbb-sg-each-section[id*=ai-generated-sections]', function() {
        twbb_insert_section(jQuery(this).closest('.twbb-sg-each-section'), 'dummy_content');
    });

    jQuery(document).on('click', '.elementor-editor-element-edit',function() {
        window.parent.twbb_animate_sidebar(0);
    });
});

function appendEcommerceLabel() {
    let label = '<div class="twbb-sg-recommended-ecommerce-label">' + twbb_sg_embed.ecommerce_label + '</div>';
    jQuery('.twbb-sg-recommended-ecommerce').each(function() {
        jQuery(this).append(label);
    })
}

function add_element_to_section_template_iframe( that, element_class, element) {
    if ( that.find(element_class).length ) {
        return;
    }
    jQuery(that).append(element);
}

function set_content_generation_status() {
    let user_desc = window.parent.jQuery('.twbb-generate-section_description').val();
    let business_desc = twbb_sg_embed.business_description;
    if( user_desc || business_desc ) {
        if( !jQuery('body').hasClass('twbb-sg-with-description') ) {
            jQuery('body').addClass('twbb-sg-with-description');
        }
    } else {
        if( jQuery('body').hasClass('twbb-sg-with-description') ) {
            jQuery('body').removeClass('twbb-sg-with-description');
        }
    }
}

function twbb_insert_section(selected_section, content_type = 'generate_content') {
    var generation_loading = jQuery('#twbb-sg-loading').html();
    if( jQuery('body').hasClass('twbb-some-section-in-process')
        || selected_section.hasClass('twbb-the-sections-generation-in-process')
        || window.parent.jQuery('.twbb-sg-sidebar-navigated-contents-container').hasClass('twbb-some-section-in-process') ) {
        return;
    }
    let preview_iframe = window.parent.jQuery('#elementor-preview-iframe').contents(),
        section_id, section_path, data, add_section_at = -1, user_description = '', nearest_section_data = {}, selected_section_type = '';
    selected_section_type = window.parent.jQuery('.twbb-sg-sidebar-navigator-menu-li.twbb-sg-navigation-item.selected').attr('data-type');
    section_id = selected_section.attr('id');
    section_path = section_id.replace('twbb-sg-section-', '');
    user_description = window.parent.jQuery('.twbb-generate-section_description').val();
    if( preview_iframe.find('.elementor-add-section:not(#elementor-add-new-section)').length ) {
        add_section_at = preview_iframe.find('.elementor-add-section:not(#elementor-add-new-section)').index();
    }

    if( content_type === 'generate_content' && !(section_path.indexOf('ai-generated-sections/ai_generated') >= 0) ) {
        //add generation in process classes
        window.parent.twbb_generate_with_ai_navigation_tab_status('disable');
        selected_section.addClass('twbb-the-sections-generation-in-process');
        jQuery('body').addClass('twbb-some-section-in-process');
        window.parent.jQuery('.twbb-sg-sidebar-navigated-contents-container .twbb-sg-sidebar-navigated-content iframe').each(function(){
            jQuery(this).contents().find('body').addClass('twbb-some-section-in-process');
        })
        window.parent.jQuery('.twbb-sg-sidebar-navigated-contents-container').addClass('twbb-some-section-in-process');
        add_element_to_section_template_iframe(selected_section, '.twbb-sg-loading', generation_loading);
    }

    if( section_path.indexOf('ai-generated-sections/ai_generated') >= 0 ) {
        let ai_section_path = extractSubstring(jQuery(selected_section).attr('class'));
        let unique_id = window.parent.jQuery('.twbb-sg-sidebar-generated-with-ai').attr('data-unique_id');
        twbb_send_data_to_analytics( ai_section_path, 'already_generated_content', { unique_id: unique_id } );
    } else {
        twbb_send_data_to_analytics( section_path, content_type );
    }

    let closest_sections_data = window.parent.collect_data_for_request(add_section_at, selected_section_type , user_description);
    data = {
        'section_path': section_path,
        'content_type': content_type,
        'closest_sections_data': JSON.stringify(closest_sections_data),
        'action': "twbb_get_section_generated_data_for_request",
        'user_description': user_description,
        'nonce': twbb_sg_embed.twbb_sg_nonce,
    }
    window.parent.twbb_insert_section_premade(add_section_at, data);
}

function extractSubstring(str) {
    var match = str.match(/ai20-sections[^ ]*/);
    return match ? match[0] : null;
}

function twbb_send_data_to_analytics( section_path, content_type, additional_data = {} ) {
    let action = '';
    if ( content_type === 'generate_content' ) {
        action = 'Insert Section With Generated Content to the page';
    } else if ( content_type === 'dummy_content' ) {
        action = 'Insert Section With Dummy Content to the page';
    } else if( content_type === 'already_generated_content' ) {
        action = 'Insert Generated Section to the page';
    }
    analyticsDataPush(
        action,
        'Section Generation',
        section_path,
        additional_data
    );
}
