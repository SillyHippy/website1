class WriteWithAIHelper {

    static getCacheElements() {
        let cache = {};
        cache.image_controls = [
            "image",
            "image_overlay",
            "thumbnail",
            "testimonial_image",
            "twbb_bg_image",
            "background_image",
            "background_image_tablet",
            "background_image_mobile",
            "background_hover_image",
            "background_overlay_image",
            "background_overlay_image_tablet",
            "background_overlay_image_mobile",
            "background_overlay_image_tablet",
            "background_overlay_image_mobile",
            "background_overlay_hover_image",
            "background_overlay_hover_image_tablet",
            "background_overlay_hover_image_mobile",
            "_background_image",
            "_background_image_tablet",
            "_background_image_mobile",
            "_background_hover_image",
            "_background_hover_image_tablet",
            "_background_hover_image_mobile",
            "background_a_image",
            "background_a_image_tablet",
            "background_a_image_mobile",
            "background_b_image",
            "background_b_image_tablet",
            "background_b_image_mobile",
            "identity_image",
            "background_border_background_overlay_group_image",
            "background_border_background_group_image",
            "gallery",
            "carousel",
        ];

        cache.controls = cache.image_controls.concat( [
            "text",
            "description_text",
            "editor",
            "title",
            "title_text",
            "tab_title",
            "tab_content",
            "inner_text",
            "testimonial_content",
            "testimonial_name",
            "testimonial_job",
            "alert_title",
            "alert_description",
            "link_text",
            "prefix",
            "suffix",
        ]);

        cache.sub_controls = [
            "tabs",
            "icon_list",
            "social_icon_list",
            "slides",
            "members",
            "price_list",
        ];
        cache.coming_soon_controls = [
            "selected_icon",
            "social_icon",
            "selected_active_icon",
            "dismiss_icon",
            "custom_css",
            "html",
        ];

        return cache;
    }

    static validateOutput(output, widgetType) {
        /* Remove first and last ', " if present */
        if((output.charAt(0) == "'" && output.charAt(output.length - 1)  == "'") ||
            (output.charAt(0) == '"' && output.charAt(output.length - 1)  == '"')) {
            output = output.slice(1, -1);
        }

        /* Remove last . if present for heading widget*/
        if(widgetType == 'title' && output.charAt(output.length - 1)  == ".") {
            output = output.slice(0, -1);
        }
        return output;
    }
}