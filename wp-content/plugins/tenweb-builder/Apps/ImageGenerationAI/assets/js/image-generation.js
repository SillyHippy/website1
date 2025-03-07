class TWBIMGGEN extends ImagesVersions {

    constructor( model, panel, view, type ) {
        super();

        /* In progress action */
        this.current_action = '';

        /* image key which is active and editable */
        this.editble_image = '';

        /* Credits amount which user has */
        this.credits = '';

        /* Use for detect if current popup open from existing image */
        this.existing_image_edit = 0;

        /* Widget model/panel/view */
        this.model = model;
        this.panel = panel;
        this.view = view;
        this.type = type;

        this.session_id = (+new Date).toString(36);

        /* Variables should keep values of selected options and set as default during the tab change/back */
        this.image_style_option = '';
        this.ratio_option = '';
        this.new_images_count_option = '';
        this.multiview_images_count_option = '';
        this.image_resolution_option = '';
    }

    init() {
        this.show();
        this.registerEvents();
        this.set_existing_image_edit();
        this.selectField();
    }

    clear_stack() {
        this.images = {};
        this.undoStack = {};
        this.redoStack = {};
        this.existing_image_edit = 0;
        this.image_style_option = '';
        this.ratio_option = '';
        this.new_images_count_option = '';
        this.multiview_images_count_option = '';
        this.image_resolution_option = '';
    }

    set_existing_image_edit() {
        let image_setting = this.model.getSetting(this.type);
        if( typeof image_setting.url !== 'undefined' && image_setting.url && !image_setting.url.includes("/placeholder.png") ) {
            const getMeta = (url, callback) => {
                const img = new Image();
                img.onload = () => callback(null, img);
                img.onerror = (err) => callback(err);
                img.src = url;
            };

            let imageWidth, imageHeight;
            getMeta(image_setting.url, (err, img) => {
                if( err ) {
                    return;
                }
                imageWidth = img.naturalWidth;
                imageHeight = img.naturalHeight;

                if( imageWidth != 0 && imageHeight != 0 ) {
                    let aspectRatio = parseFloat(imageWidth / imageHeight);
                    let imageRatioOb = new ImageRatio(aspectRatio);
                    this.ratio_option = imageRatioOb.process();
                }

                let output = {
                    "image_0": {
                        "v1": {
                            "original_image": image_setting.url,
                            "thumbnail": image_setting.url,
                            "full_size_image": image_setting.url
                        }
                    }
                }
                this.updateImages(output);
                this.editble_image = "image_0";
                this.existing_image_edit = 1;
                jQuery(document).find(".twbb-menu-item.twbb-menu-item-edit").trigger("click");

            });
        }
    }

    /**
     * Show image generation popup
    */
    show() {
        let cont_templ = jQuery(document).find("#twbb-image-gen-template").html();
        jQuery("body").append(cont_templ);
        jQuery(".twbb-menu-item").removeClass("twbb-menu-item-active");
        jQuery(".twbb-image-gen-editor").removeClass("twbb-image-gen-editor-scroll");
        setTimeout(function (){
            if(window.twbb_image_generation_view == 'edit_image_view') {
                jQuery('.twbb-menu-item[data-action="edit_image_view"]').addClass("twbb-menu-item-active");
                jQuery('.twbb-menu-item[data-action="edit_image_view"]').trigger('click');
            }else if (window.twbb_image_generation_view == 'multiple_view'){
                jQuery('.twbb-menu-item[data-action="multiple_view"]').addClass("twbb-menu-item-active");
                jQuery('.twbb-menu-item[data-action="multiple_view"]').trigger('click');
            }else{
                jQuery('.twbb-menu-item[data-action="new_image_view"]').addClass("twbb-menu-item-active");
                jQuery('.twbb-menu-item[data-action="new_image_view"]').trigger('click');
            }
        },100);


        /*
        TODO should open according to credit limitation needs
        this.update_credits(twbb_img.limitations);
        */
    }

    /**
     * Register all action events
    */
    registerEvents() {
        let self = this;
        /* Menu action click event new_image/edit/multiple */
        jQuery(document).off( "click", ".twbb-menu-item").on("click", ".twbb-menu-item", function() {
            if( self.current_action != "" || (Object.keys(self.images).length == 0 && !jQuery(this).hasClass("twbb-menu-item-add")) ) {
                return false;
            }
            jQuery(".twbb-menu-item").removeClass("twbb-menu-item-active");
            jQuery(".twbb-image-gen-editor").removeClass("twbb-image-gen-editor-scroll");
            jQuery(this).addClass("twbb-menu-item-active");
            let action = jQuery(this).attr("data-action");
            if( typeof self[action] == 'function' ) {
                self[action]();
            }
        })

        /* Tooltip open/close */
        jQuery(document).on( "mouseover", ".twbb-image-editor-row label",function(e) {
                e.preventDefault();
                let position = jQuery(this).find(".twbb-help-tooltip").position();
                let toolTip = jQuery(this).find(".twbb-help-tooltip-content");
                if( toolTip.is(":hidden") ) {
                    jQuery(document).find("label .twbb-help-tooltip-content").hide();
                    toolTip.css({'left':position.left, 'top': parseInt(position.top + 22)});
                    toolTip.show();
                }
            })
            .on( "mouseleave", ".twbb-image-editor-row label", function(e) {
                e.preventDefault();
                jQuery(this).find(".twbb-help-tooltip-content").hide();
            });


        /* Close info/dropdown containers on scroll */
        jQuery(".twbb-image-gen-editor").on("scroll", function() {
            jQuery(".twbb-help-tooltip-content").hide();
            jQuery(document).find(".twbb-select").removeClass("twbb-select-opened");
            jQuery(document).find(".twbb-select-value i.twbb-select-arrow").removeClass("twbb-select-arrow-up");
            jQuery(document).find(".twbb-select-dropdown").hide();
        })

        /* Open/close topbar Available Credits submenu on hover */
        jQuery(document).find(".twbb-image-gen-credits-container")
            .on( "mouseover", function(e) {
            e.preventDefault();
            jQuery(this).find(".twbb-image-gen-credits-layer").show();
        })
            .on( "mouseleave", function(e) {
                e.preventDefault();
                jQuery(this).find(".twbb-image-gen-credits-layer").hide();
        });

        /* Show Limit exceed popup on generate image button mouseover */
        jQuery(document).on( "mouseover", ".twbb-image-editor-row.twbb-generate_image", function(e) {
            e.preventDefault();
            jQuery(this).find(".twbb-image-gen-credits-layer").show();
        });
        jQuery(document).on( "mouseleave", ".twbb-image-editor-row.twbb-generate_image", function(e) {
            e.preventDefault();
            jQuery(this).find(".twbb-image-gen-credits-layer").hide();
        });

        /* Image gen popup close alert popup open action */
        jQuery(document).off( "click", ".twbb-image-gen-layout, .twbb-close-image-gen-popup")
            .on("click", ".twbb-image-gen-layout, .twbb-close-image-gen-popup", function() {
            let alert_templ = jQuery("#twbb-image-gen-alert-template").html();
            jQuery(".twbb-image-gen-container").after(alert_templ);
            jQuery(document).find(".twbb-image-gen-container").addClass("twbb-image-alert-popup-active");
            jQuery(document).find(".twbb-image-description").prop('disabled', true);
        });

        /* Close Image generation popup */
        jQuery(document).off( "click", ".twbb-image-gen-alert-button-close").on("click", ".twbb-image-gen-alert-button-close", function() {
            self.images = {};
            self.current_action = '';
            self.editble_image = '';
            jQuery(document).find(".twbb-image-gen-layout, .twbb-image-gen-container, .twbb-image-gen-alert-container").remove();
        })

        /* Close alert popup action */
        jQuery(document).off( "click", ".twbb-image-gen-alert-button-cancel, .twbb-image-gen-alert-close").on("click", ".twbb-image-gen-alert-button-cancel, .twbb-image-gen-alert-close", function() {
            jQuery(document).find(".twbb-image-gen-alert-container").remove();
            jQuery(document).find(".twbb-image-gen-container").removeClass("twbb-image-alert-popup-active");
            jQuery(document).find(".twbb-image-description").prop('disabled', false);
        })

        /* Enable/disable generate button for action */
        jQuery(document).on("input", ".twbb-new_image-editor-image_description .twbb-image-description, .twbb-edit_image-editor-image_description .twbb-image-description", function() {
            if( self.current_action != '' ) {
                return false;
            }
            let generate_imag_button = jQuery(this).closest(".twbb-image-gen-editor").find(".twbb-generate_imag-description-button");
            let text = jQuery(this).val();
            let charCount = text.trim().length;

            if( charCount == 0 ) {
                jQuery(this).closest(".twbb-image-editor-row").removeClass("twbb-image-editor-row-error");
                generate_imag_button.addClass("twbb-generate_imag-button-disabled");
            }
            else if( charCount < 10 ) {
                generate_imag_button.addClass("twbb-generate_imag-button-disabled");
            }
            else if( charCount > 350 ) {
                generate_imag_button.addClass("twbb-generate_imag-button-disabled");
            }
            else {
                jQuery(this).closest(".twbb-image-editor-row").removeClass("twbb-image-editor-row-error");
                generate_imag_button.removeClass("twbb-generate_imag-button-disabled");
            }
        });

        /* Show error message in case of description chars count not keep rules */
        jQuery(document).on("change",
            ".twbb-new_image-editor-image_description .twbb-image-description, " +
            ".twbb-edit_image-editor-image_description .twbb-image-description",
            function(){
            let text = jQuery(this).val();
            let charCount = text.trim().length;

            if( charCount == 0 ) {
                jQuery(this).closest(".twbb-image-editor-row").removeClass("twbb-image-editor-row-error");
            }
            else if( charCount < 10 ) {
                jQuery(document).find(".twbb-image-error-text").empty().text('Use no less than 10 symbols for better results.');
                jQuery(this).closest(".twbb-image-editor-row").addClass("twbb-image-editor-row-error");
            }
            else if( charCount > 350 ) {
                jQuery(document).find(".twbb-image-error-text").empty().text('Character limit reached.');
                jQuery(this).closest(".twbb-image-editor-row").addClass("twbb-image-editor-row-error");
            }
        });

        /* Generate buttons click action */
        jQuery(document).off( "click", ".twbb-request-button").on("click", ".twbb-request-button", function() {
            if( jQuery(this).hasClass("twbb-generate_imag-button-disabled") ||
                jQuery(this).hasClass("twbb-generate_imag-loading") || self.current_action != "" ) {
                return false;
            }

            self.current_action = jQuery(this).attr("data-action");
            jQuery(document).find(".twbb-image-gen-container").addClass("twbb-image-gen-inprogress");
            jQuery(document).find(".twbb-image-description").prop('disabled', true);
            self.request_action(this);
        });

        /* Image edit tool click */
        jQuery(document)
            .off( "click", ".twbb-image-edit-tool.twbb-image-edit, .twbb-image-preview img")
            .on("click", ".twbb-image-edit-tool.twbb-image-edit, .twbb-image-preview img",
                function() {
            if( self.current_action != "" ) {
                return false;
            }
            let data_key;
            if( jQuery(this).parents('.twbb-edit_image').length ) {
                data_key = jQuery(this).closest(".twbb-edit_image").attr("data-key");
            } else {
                data_key = jQuery(this).closest(".twbb-image-preview-item").attr("data-key");
            }
            if( typeof data_key !== 'undefined' ) {
                self.editble_image = data_key;
            }
            jQuery(document).find(".twbb-menu-item-edit").trigger('click');
        });

        /* Thumb click in the edit image view */
        jQuery(document).off( "click", ".twbb-edit_image-thumb").on("click", ".twbb-edit_image-thumb", function() {
            if( self.current_action != "" ) {
                return false;
            }
            self.editble_image = jQuery(this).attr("data-key");
            if( jQuery(document).find(".twbb-menu-item-add").hasClass("twbb-menu-item-active") )  {
                jQuery(document).find(".twbb-menu-item-edit").trigger('click');
            }
            else if( jQuery(document).find(".twbb-menu-item-multiview").hasClass("twbb-menu-item-active") &&
                jQuery(document).find(".twbb-image-preview-item").length ) {
                jQuery(document).find(".twbb-menu-item-edit").trigger('click');
            }
            else {
                jQuery(document).find(".twbb-edit_image-thumb").removeClass("twbb-edit_image-thumb-active");
                jQuery(this).addClass("twbb-edit_image-thumb-active");
                let image_url = self.get_image_version_url(self.images[self.editble_image], self.editble_image,'original');
                let full_url = self.get_image_version_url(self.images[self.editble_image], self.editble_image,'full_size');
                jQuery(document).find(".twbb-image-gen-container .twbb-image-gen-preview .twbb-edit_image img").attr("src", image_url);
                jQuery(document).find(".twbb-image-gen-container .twbb-image-gen-preview .twbb-edit_image").attr("data-key", self.editble_image);
                jQuery(document).find(".twbb-image-gen-container .twbb-image-gen-preview .twbb-edit_image .twbb-image-download").attr({"data-href": full_url, "href" : ""});
                self.update_edit_page_thumbs();
                self.set_active_undo_redo();
            }
        });

        /* Download image */
        jQuery(document).off( "click", ".twbb-image-download").on("click", ".twbb-image-download", function(e) {
            let href = jQuery(this).attr("href");
            if( !href ) {
                e.preventDefault();
                let url = jQuery(this).attr("data-href");
                self.download_image(url, this);
            }
        });


        /* Enable action button near the select fields in the edit image view */
        jQuery(document).off( "click", ".twbb-generate_imag-action-row .twbb-select-dropdown li").on("click", ".twbb-generate_imag-action-row .twbb-select-dropdown li", function() {
            jQuery(this).closest(".twbb-generate_imag-action-row").find(".twbb-generate_imag-button")
                .removeClass("twbb-generate_imag-button-disabled");
        });

        /* Use image alert popup open */
        jQuery(document).off( "click", ".twbb-image-gen-use_image").on("click", ".twbb-image-gen-use_image", function() {
            if( self.current_action != "" ) {
                return false;
            }
            if( !jQuery(this).hasClass("twbb-generate_imag-button-disabled") && !jQuery(this).hasClass("twbb-generate_imag-loading") ) {
                let alert_template = jQuery(document).find("#twbb-image-gen-alert-useImage-template").html();
                jQuery(".twbb-image-gen-container").after(alert_template);
                jQuery(document).find(".twbb-image-gen-container").addClass("twbb-image-alert-popup-active");
                jQuery(document).find(".twbb-image-description").prop('disabled', true);
            }
        });

        /* Use image */
        jQuery(document).off( "click", ".twbb-image-gen-alert-button-use_image").on("click", ".twbb-image-gen-alert-button-use_image", function() {
            jQuery(document).find(".twbb-image-gen-alert-container").remove();
            jQuery(document).find(".twbb-image-gen-container").removeClass("twbb-image-alert-popup-active");
            self.use_image();
        });



        /* Undo click */
        jQuery(document).off( "click", ".twbb-image-gen-undo").on("click", ".twbb-image-gen-undo", function() {
            if( self.current_action != "" ) {
                return false;
            }
            if( !jQuery(this).hasClass("twbb-undo-redo-disabled") ) {
                self.undo_redo('undo');
            }
        });

        /* Redo click */
        jQuery(document).off( "click", ".twbb-image-gen-redo").on("click", ".twbb-image-gen-redo", function() {
            if( self.current_action != "" ) {
                return false;
            }
            if( !jQuery(this).hasClass("twbb-undo-redo-disabled") ) {
                self.undo_redo('redo');
            }
        });

        /* Close selects */
        jQuery(document).off( "click", ".twbb-image-gen-container").on("click", ".twbb-image-gen-container", function(event) {
            if( jQuery(event.target).attr('class') != "twbb-select" && !jQuery(event.target).closest(".twbb-select").length ) {
                jQuery(document).find(".twbb-select-opened .twbb-select-dropdown").hide();
                jQuery(document).find(".twbb-select-opened").removeClass("twbb-select-opened");
                jQuery(document).find(".twbb-select-value i.twbb-select-arrow").removeClass("twbb-select-arrow-up");
            }
        });
    }

    undo_redo( action ) {
        if(action == 'undo') {
            this.undoImage(this.editble_image);
        } else {
            this.redoImage(this.editble_image);
        }
        this.set_active_undo_redo();
        let full_url = this.get_image_version_url( this.images[this.editble_image], this.editble_image, 'full_size');
        let image_url = this.get_image_version_url( this.images[this.editble_image], this.editble_image, 'original');
        jQuery(document).find(".twbb-image-gen-container .twbb-edit_image-preview img").attr("src", image_url);
        jQuery(document).find(".twbb-image-gen-container .twbb-edit_image-preview img").attr("src", image_url);
        jQuery(document).find(".twbb-image-gen-container .twbb-image-gen-preview .twbb-edit_image .twbb-image-download").attr({"data-href": full_url, "href" : ""});

        this.update_edit_page_thumbs();
    }

    /* Checking and activate or deactivate undo/redo buttons */
    set_active_undo_redo() {
        let current_version = this.getImageCurrentVersion( this.editble_image );
        let image_versions = Object.keys(this.images[this.editble_image]);
        if ( image_versions.length <= 1 ) {
            jQuery(document).find(".twbb-image-gen-undo, .twbb-image-gen-redo").addClass("twbb-undo-redo-disabled");
        } else if( image_versions.indexOf(current_version) == (image_versions.length-1) ) {
            jQuery(document).find(".twbb-image-gen-redo").addClass("twbb-undo-redo-disabled");
            jQuery(document).find(".twbb-image-gen-undo").removeClass("twbb-undo-redo-disabled");
        } else if( image_versions.indexOf(current_version) == 0 ) {
            jQuery(document).find(".twbb-image-gen-redo").removeClass("twbb-undo-redo-disabled");
            jQuery(document).find(".twbb-image-gen-undo").addClass("twbb-undo-redo-disabled");
        } else {
            jQuery(document).find(".twbb-image-gen-redo, .twbb-image-gen-undo").removeClass("twbb-undo-redo-disabled");
        }
    }

    /**
     * Action during the click use image button
    */
    use_image() {
        let self = this;
        if( self.editble_image == "" || Object.keys(this.images).length == 0 ) {
            return false;
        }
        jQuery(document).find(".twbb-request-button").addClass('twbb-generate_imag-button-disabled');

        jQuery(document).find(".twbb-image-gen-use_image").addClass("twbb-generate_imag-loading");

        let image_url = self.get_image_version_url(self.images[self.editble_image], this.editble_image,'original');
        let data = {
            'action': 'twbb_use_image',
            'task': 'twbb_use_image',
            'nonce': twbb_img.ajaxnonce,
            'image': image_url
        }
        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data:  data,
            success: function (response){
                if( response.success ) {
                    let post_id = response.data.id;
                    let url = response.data.url;
                    self.setSetting(post_id, url);

                    self.images = {}
                    self.current_action = '';
                    self.editble_image = '';
                    jQuery(document).find(".twbb-image-gen-layout, .twbb-image-gen-container").remove();
                }
            },
            complete: function() {
                jQuery(document).find(".twbb-image-gen-use_image").removeClass("twbb-generate_imag-loading");
                jQuery(document).find(".twbb-image-gen-container").removeClass("twbb-image-gen-inprogress");
                jQuery(document).find(".twbb-image-description").prop('disabled', false);
            },
            error: function (jqXHR, exception) {
                console.log(jqXHR);
            },

        });
    }

    /**
     * Set settings of Elementor to change image
    */
    setSetting(post_id, url) {
        let self = this;
        let options = {
            external: true,
            render: true,
        };
        let container = {};
        let settings = {
            alt: "",
            id: post_id,
            size: "",
            source: "library",
            url: url
        };

        let widget_with_galleries = ['twbb_gallery', 'image-carousel'];
        let widget_with_repeaters = ['twbb-testimonial-carousel', 'twbb_reviews', 'twbb-team', 'twbb_media-carousel', 'twbb_slides', 'twbb_price-list', 'twbb_video-playlist'];
        if( widget_with_repeaters.includes(this.model.attributes.widgetType) && self.type.indexOf('_background_image' ) === -1 && self.type.indexOf('_background_hover_image' ) === -1 ) {
            let activeItemIndex = this.model.attributes.editSettings.attributes.activeItemIndex;
            if( this.model.attributes.widgetType === 'twbb_video-playlist' ) {
                if( self.type === 'thumbnail' ) {
                    container = self.view.getContainer().repeaters.tabs.children[(activeItemIndex - 1)];
                } else if ( self.type === 'image_overlay' ) {
                    let widgetId = self.model.attributes.id;
                    container = window.$e.components.get('document').utils.findContainerById(widgetId);
                }
            } else if( this.model.attributes.widgetType === 'twbb_price-list' && self.type === 'image' ) {
                container = self.view.getContainer().repeaters.price_list.children[(activeItemIndex - 1)];
            } else if( this.model.attributes.widgetType === 'twbb-team' ) {
                container = self.view.getContainer().repeaters.members.children[(activeItemIndex - 1)];
            } else {
                container = self.view.getContainer().repeaters.slides.children[(activeItemIndex - 1)];
            }
        }
        else if( widget_with_galleries.includes(this.model.attributes.widgetType) ) {
            let widgetId = self.model.attributes.id;
            container = window.$e.components.get('document').utils.findContainerById(widgetId);

            let currentSettings = self.model.getSetting(this.type);
            currentSettings.push({
                id: post_id,
                url: url
            });
            settings = currentSettings;
        }
        else {
            let widgetId = self.model.attributes.id;
            container = window.$e.components.get('document').utils.findContainerById(widgetId);
        }
        window.$e.commands.run('document/elements/settings', {
            "container": container,
            "options": options,
            settings: {[self.type]: settings,}
        });
    }

    /**
     * Send request to generate/edit images
    */
    request_action(that) {
        let self = this;
        let params = {}
        let description;
        let image_style;
        let aspect_ratio;
        let n_images;
        let image;
        /* Remove message container */
        jQuery(document).find(".twbb-image-gen-message").remove();
        switch (this.current_action) {
            case 'image_generate':
                jQuery(document).find(".twbb-image-preview-item").addClass("twbb-generate_imag-loading");
                description = jQuery(document).find(".twbb-image-description").val();
                image_style = jQuery(document).find(".twbb-select-dropdown-image_style-content li.twbb-select-active").attr("data-value");
                /* Get random style in case of None */
                if( !image_style || image_style == "None" ) {
                    const allStyles = jQuery(document).find(".twbb-select-dropdown-image_style-content li");
                    /* Excluded 0(None) case */
                    const randLi = allStyles[ Math.floor(( Math.random() * (allStyles.length-1) )+1) ];
                    image_style = jQuery(randLi).attr("data-value");
                }
                aspect_ratio = jQuery(document).find(".twbb-new_image-editor-image_ratio li.twbb-select-active").attr("data-value");
                if ( !aspect_ratio ) aspect_ratio = '';
                n_images = jQuery(document).find(".twbb-new_image-editor-image_count li.twbb-select-active").attr("data-value");
                if ( !n_images ) n_images = 4;
                params = {
                    'description': description,
                    'image_style': image_style,
                    'aspect_ratio' : aspect_ratio,
                    'n_images': n_images,
                };

                /* Hide image item related to the chosen count */
                jQuery(document).find('.twbb-image-preview-item').show();
                jQuery(document).find('.twbb-image-preview-item').filter(function(i) {
                    return (i >= n_images);
                }).hide();
                break;
            case 'image_edit':
                description = jQuery(document).find(".twbb-image-description").val();
                image = this.get_image_version_url(this.images[this.editble_image], this.editble_image, 'original');
                params = {
                    'description': description,
                    'image': image,
                };

                break;
            case 'image_remove_bg':
                image = this.get_image_version_url(this.images[this.editble_image], this.editble_image, 'original');
                params = {
                    'image': image,
                };
                break;
            case 'image_upscale':
                image = this.get_image_version_url(this.images[this.editble_image], this.editble_image, 'original');
                let factor = jQuery(document).find(".twbb-edit_image-editor-image_resolution  li.twbb-select-active").attr("data-value");
                params = {
                    'image' : image,
                    'factor' : factor
                };
                break;
            case 'image_variations':
                image = this.get_image_version_url(this.images[this.editble_image], this.editble_image, 'original');
                let full_url = self.get_image_version_url(this.images[this.editble_image], this.editble_image, 'full_size');
                description = jQuery(document).find(".twbb-image-description").val();
                n_images = jQuery(document).find(".twbb-multi_image-editor-image_count li.twbb-select-active").attr("data-value");
                params = {
                    'image' : image,
                    'description' : description,
                    'n_images': n_images,
                };
                let edit_tools_template = jQuery("#twbb-image-edit_image-tools-template").html();

                let empty_preview_templ = jQuery(document).find("#twbb-image-four_image-empty-template").html();
                jQuery(document).find(".twbb-image-gen-container .twbb-image-gen-preview").empty().append(empty_preview_templ);

                /* Hide image item related to the chosen count */
                jQuery(document).find('.twbb-image-preview-item').show();
                jQuery(document).find('.twbb-image-preview-item').filter(function(i) {
                    return (i > n_images);
                }).hide();

                jQuery(document).find(".twbb-image-preview-item").addClass("twbb-generate_imag-loading");
                let preview_item = jQuery(".twbb-image-preview-container .twbb-image-preview-item").eq(0);
                preview_item.removeClass("twbb-generate_imag-loading");
                preview_item.find(".twbb-image-preview").empty().append("<img src='"+image+"'>");
                preview_item.attr("data-key", this.editble_image);
                preview_item.append(edit_tools_template);
                preview_item.find(".twbb-image-download").attr({"data-href": full_url, "href" : ""});
                self.set_edit_page_thumbs();
                break;
            case 'image_expand':
                aspect_ratio = jQuery(document).find(".twbb-edit_image-editor-image_ratio li.twbb-select-active").attr("data-value");
                image = this.get_image_version_url(this.images[this.editble_image], this.editble_image,'original');
                params = {
                    'image' : image,
                    'aspect_ratio' : aspect_ratio,
                };
                break;
        }
        jQuery(that).addClass("twbb-generate_imag-loading");
        jQuery(document).find(".twbb-edit_image-thumb-container").addClass("twbb-thumbs-disabled");
        params['session_id'] = this.session_id;
        params['action_type'] = 'builder_image';
        params['existing_image_edit'] = this.existing_image_edit;

        let ob;
        let front_ai = false;
        ob = new RestRequest("builder_image/" + this.current_action, params, "POST", function (success) {
            let output = success['data']['output'];
            if( self.set_request_result(output) ) {
                self.show_message( true, '' );
            } else {
                self.show_message( false, 'Something went wrong, please try again!' );
            }

            /*
            TODO should open according to credit limitation needs
            self.update_credits(success['data']['limitation']);
            */
        }, function (err) {
            if( typeof err.data !== 'undefined' && err.data == 'there_is_in_progress_request' ) {
                self.show_message( false, 'It seems like another generation request is in progress. Please retry once its finished.' );
            } else {
                self.show_message( false, '' );
            }
            self.current_action = '';
            jQuery(document).find(".twbb-image-gen-container").removeClass("twbb-image-gen-inprogress");
            jQuery(document).find(".twbb-image-description").prop('disabled', false);
        }, function (err) {
            self.show_message( false, '' );
            self.current_action = '';
            jQuery(document).find(".twbb-image-gen-container").removeClass("twbb-image-gen-inprogress");
            jQuery(document).find(".twbb-image-description").prop('disabled', false);
        });
        ob.twbb_send_rest_request(front_ai, 'builder_image');
    }

    update_credits(limitation) {
        if( typeof limitation == 'object' && !Object.keys(limitation).length ) {
            return false;
        }
        let planLimit = parseInt(limitation['planLimit']);
        let KplanLimit = planLimit/1000;
        let alreadyUsed = parseInt(limitation['alreadyUsed']);

        this.credits = planLimit - alreadyUsed;
        twbb_img.limitations['alreadyUsed'] = alreadyUsed;
        let imageCount = parseInt(this.credits / 2);
        if( this.credits <= 0 ) {
            let template = jQuery(document).find("#twbb-image-gen-credits-exceed-template").html();
            if( !jQuery(document).find(".twbb-generate_imag-button.twbb-request-button").find(".twbb-image-gen-credits-content").length ) {
                jQuery(document).find(".twbb-generate_imag-button.twbb-request-button").append(template);
            }
        }

        jQuery(document).find(".twbb-image-gen-credits-amount, .twbb-image-gen-credit-amount").text(this.credits);
        jQuery(document).find(".twbb-image-gen-credits-total").text('/' + KplanLimit + 'K');
        jQuery(document).find(".twbb-image-gen-credits-image_count").text(imageCount);
    }

    /**
     * Show success/error messages popup
    */
    show_message( success, message ) {
        let template = '';
        if( success ) {
            template = jQuery(document).find("#twbb-image-gen_success-template").html();
        } else {
            template = jQuery(document).find("#twbb-image-gen_error-template").html();
        }
        jQuery(document).find(".twbb-image-gen-container").append(template);
        if( message != '' ) {
            jQuery(document).find(".twbb-image-gen-container .twbb-image-gen-message").empty().text(message);
        }
        /* Hide loading */
        jQuery(document).find(".twbb-generate_imag-loading").removeClass("twbb-generate_imag-loading");

        /* Remove message after 4 seconds */
        setTimeout(() => {
            jQuery(document).find(".twbb-image-gen-message").remove();
        }, 4000);
    }

    /**
     * Set request results in the image generation popup
    */
    set_request_result( output ) {
        /* Hide loading */
        jQuery(document).find(".twbb-generate_imag-loading").removeClass("twbb-generate_imag-loading");
        jQuery(document).find(".twbb-thumbs-disabled").removeClass("twbb-thumbs-disabled");
        if( typeof output != 'object' || !Object.keys(output).length ) {
            this.current_action = '';
            jQuery(document).find(".twbb-image-gen-container").removeClass("twbb-image-gen-inprogress");
            jQuery(document).find(".twbb-image-description").prop('disabled', false);
            return false;
        }
        if( this.existing_image_edit ) {
            this.clear_stack();
        }
        switch (this.current_action) {
            case 'image_generate':
                this.updateImages(output);
                this.set_newImage_result();
                break;
            case 'image_edit':
            case 'image_remove_bg':
            case 'image_upscale':
            case 'image_expand':
                this.updateImages(output, this.editble_image);
                this.set_editImage_result();
                break;
            case 'image_variations':
                this.updateImages(output);
                this.set_variation_result();
                break;
        }

        /* Empty current action */
        this.current_action = '';
        jQuery(document).find(".twbb-image-gen-container").removeClass("twbb-image-gen-inprogress");
        jQuery(document).find(".twbb-image-description").prop('disabled', false);
        return true;
    }

    /**
     * Set edit image request results
    */
    set_editImage_result() {
        let image_url = this.get_image_version_url(this.images[this.editble_image], this.editble_image, 'original');
        let full_url = this.get_image_version_url( this.images[this.editble_image], this.editble_image, 'full_size');
        jQuery(document).find(".twbb-image-gen-container .twbb-edit_image img").attr("src", image_url);
        jQuery(document).find(".twbb-image-gen-container .twbb-image-gen-preview .twbb-edit_image .twbb-image-download").attr({"data-href": full_url, "href" : ""});
        if( this.editble_image && !this.existing_image_edit ) {
            jQuery(document).find(".twbb-image-gen-use_image").removeClass("twbb-generate_imag-button-disabled");
        }
        this.set_active_undo_redo();
        this.set_edit_page_thumbs();
    }

    /**
     * Set variation image request results
    */
    set_variation_result() {
        let self = this;
        let n_images = jQuery(document).find(".twbb-multi_image-editor-image_count li.twbb-select-active").attr("data-value");
        let index = 1;
        let url;
        let preview_item;
        let edit_tools_template = jQuery("#twbb-image-edit_image-tools-template").html();
        if( self.editble_image && !self.existing_image_edit ) {
            jQuery(document).find(".twbb-image-gen-use_image").removeClass("twbb-generate_imag-button-disabled");
        }
        for( let i = (n_images-1); i >= 0; i-- ) {
            url = self.get_image_version_url(Object.values(this.images).at(i), Object.keys(this.images).at(i), 'original');
            let full_url = self.get_image_version_url(Object.values(this.images).at(i), Object.keys(this.images).at(i), 'full_size');
            preview_item = jQuery(".twbb-image-preview-container .twbb-image-preview-item").eq(index);
            preview_item.attr("data-key", Object.keys(this.images).at(i));
            preview_item.find(".twbb-image-preview").empty().append("<img src='"+url+"'>");
            preview_item.append(edit_tools_template);
            preview_item.find(".twbb-image-download").attr({"data-href": full_url, "href" : ""});
            index++;
        }
        this.set_edit_page_thumbs();
    }

    /**
     * Set new image request results
    */
    set_newImage_result() {
        let self = this;
        let index = 0
        let edit_tools_template = jQuery("#twbb-image-edit_image-tools-template").html();
        jQuery(document).find(".twbb-image-gen-use_image").removeClass("twbb-generate_imag-button-disabled");
        jQuery.each( self.images, function(key, value) {
            if( index == 0 ) {
                self.editble_image = key;
            }

            let url = self.get_image_version_url(value, key, 'original');
            let full_url = self.get_image_version_url(value, key, 'full_size');
            let preview_item = jQuery(".twbb-image-preview-container .twbb-image-preview-item").eq(index);
            if( preview_item.length ) {
                preview_item.attr("data-key", key);
                preview_item.find(".twbb-image-preview").empty().append("<img src='" + url + "'>");
                preview_item.append(edit_tools_template);
                preview_item.find(".twbb-image-download").attr({"data-href": full_url, "href" : ""});
                index++;
            }
        })
        this.set_edit_page_thumbs();
        jQuery(document).find(".twbb-edit_image-thumb").removeClass("twbb-edit_image-thumb-active");
    }

    /**
     * Get image url from the all data object
     *
     * @param image object
     * @param image_name string key of the object
     * @param original string
     *
     * @return url string
    */
    get_image_version_url( image, image_name, original ) {
        if( typeof image != 'object' ) {
            return '';
        }
        let current_version = this.getImageCurrentVersion(image_name);
        let last_generate = {};
        if( current_version && typeof image[current_version] !== 'undefined') {
            last_generate = image[current_version];
        } else {
            last_generate = Object.values(image).pop();
        }

        if( original == 'original' ) {
            return last_generate['original_image'];
        }
        else if( original == 'full_size' ) {
            return last_generate['full_size_image'];
        }
        return last_generate['thumbnail'];
    }

    /**
     * New image menu button callback
    */
    new_image_view() {
        jQuery(document).find(".twbb-image-gen-topbar-undo-container").empty();
        jQuery(document).find(".twbb-image-gen-topbar-title").text("New Image");
        let new_image_templ = jQuery(document).find("#twbb-image-new_image-editor-template").html();
        jQuery(document).find(".twbb-image-gen-container .twbb-image-gen-editor").empty().append(new_image_templ);
        let empty_preview_templ = jQuery(document).find("#twbb-image-four_image-empty-template").html();
        jQuery(document).find(".twbb-image-gen-container .twbb-image-gen-preview").empty().append(empty_preview_templ);
        if( Object.keys(this.images).length && !this.existing_image_edit ) {
            this.set_edit_page_thumbs();
        }

        /* Set previously selected values to options */
        if( this.image_style_option != '' ) {
            jQuery(document).find(".twbb-new_image-editor-image_style .twbb-select-value").attr('data-value', this.image_style_option);
            let text = jQuery(document).find(".twbb-select-dropdown-image_style-content li[data-value='"+this.image_style_option+"']").text();
            jQuery(document).find(".twbb-new_image-editor-image_style .twbb-select-value > span").empty().text(text);
            jQuery(document).find(".twbb-select-dropdown-image_style-content li").removeClass("twbb-select-active");
            jQuery(document).find(".twbb-select-dropdown-image_style-content li[data-value='"+this.image_style_option+"']").addClass("twbb-select-active");
        }
        if( this.ratio_option != '' ) {
            jQuery(document).find(".twbb-new_image-editor-image_ratio .twbb-select-value").attr('data-value', this.ratio_option);
            jQuery(document).find(".twbb-new_image-editor-image_ratio .twbb-select-value > span").empty().text(this.ratio_option);
            jQuery(document).find(".twbb-new_image-editor-image_ratio .twbb-select-dropdown li").removeClass("twbb-select-active");
            jQuery(document).find(".twbb-new_image-editor-image_ratio .twbb-select-dropdown li[data-value='"+this.ratio_option+"']").addClass("twbb-select-active");
        }

        if( this.new_images_count_option != '' ) {
            jQuery(document).find(".twbb-new_image-editor-image_count .twbb-select-value").attr('data-value', this.new_images_count_option);
            jQuery(document).find(".twbb-new_image-editor-image_count .twbb-select-value > span").empty().text(this.new_images_count_option+' images');
            jQuery(document).find(".twbb-new_image-editor-image_count .twbb-select-dropdown li").removeClass("twbb-select-active");
            jQuery(document).find(".twbb-new_image-editor-image_count .twbb-select-dropdown li[data-value='"+this.new_images_count_option+"']").addClass("twbb-select-active");
        }
    }

    /**
     * Edit image menu button callback
    */
    edit_image_view() {
        let undo_redo_template = jQuery(document).find("#twbb-image-undo-redo-template").html();
        if( !jQuery(document).find(".twbb-image-gen-topbar-action .twbb-image-gen-undo").length ) {
            jQuery(document).find(".twbb-image-gen-topbar-action .twbb-image-gen-topbar-undo-container").append(undo_redo_template);
        }
        this.set_active_undo_redo();
        jQuery(".twbb-image-gen-editor").addClass("twbb-image-gen-editor-scroll");
        jQuery(document).find(".twbb-image-gen-topbar-title").text("Edit Image");
        let edit_image_templ = jQuery(document).find("#twbb-image-edit_image-editor-template").html();
        jQuery(document).find(".twbb-image-gen-container .twbb-image-gen-editor").empty().append(edit_image_templ);

        let edit_image_preview_templ = jQuery(document).find("#twbb-image-edit_image-preview-template").html();
        jQuery(document).find(".twbb-image-gen-container .twbb-image-gen-preview").empty().append(edit_image_preview_templ);

        let image_url = this.get_image_version_url(this.images[this.editble_image], this.editble_image,'original');
        jQuery(document).find(".twbb-image-gen-container .twbb-edit_image img").attr("src", image_url);

        let full_size_url = this.get_image_version_url(this.images[this.editble_image], this.editble_image,'full_size');
        let edit_tools_template = jQuery("#twbb-image-edit_image-tools-template").html();
        jQuery(document).find(".twbb-edit_image .twbb-edit_image-container").append(edit_tools_template);
        jQuery(document).find(".twbb-edit_image .twbb-image-download").attr({"data-href": full_size_url, "href" : ""});
        jQuery(document).find(".twbb-edit_image .twbb-image-edit").remove();

        /* Set previously selected values to options */
        if( this.ratio_option != '' ) {
            jQuery(document).find(".twbb-edit_image-editor-image_ratio .twbb-select-value").attr('data-value', this.ratio_option);
            jQuery(document).find(".twbb-edit_image-editor-image_ratio .twbb-select-value > span").text(this.ratio_option);
            jQuery(document).find(".twbb-edit_image-editor-image_ratio .twbb-select-dropdown li").removeClass("twbb-select-active");
            jQuery(document).find(".twbb-edit_image-editor-image_ratio .twbb-select-dropdown li[data-value='"+this.ratio_option+"']").addClass("twbb-select-active");
        }

        if( this.image_resolution_option != '' ) {
            jQuery(document).find(".twbb-edit_image-editor-image_resolution .twbb-select-value").attr('data-value', this.image_resolution_option);
            jQuery(document).find(".twbb-edit_image-editor-image_resolution .twbb-select-value > span").text(this.image_resolution_option);
            jQuery(document).find(".twbb-edit_image-editor-image_resolution .twbb-select-dropdown li").removeClass("twbb-select-active");
            jQuery(document).find(".twbb-edit_image-editor-image_resolution .twbb-select-dropdown li[data-value='"+this.image_resolution_option+"']").addClass("twbb-select-active");
        }

        this.set_edit_page_thumbs();
    }

    /**
     * Set Edit page thumbs
    */
    set_edit_page_thumbs() {
        let self = this;
        jQuery(document).find(".twbb-edit_image-thumb-container").empty();
        let thumb_template = jQuery("#twbb-image-edit_image-preview-thumb-template").html();
        let url = '';
        jQuery.each(self.images, function (index, value) {
            jQuery(document).find(".twbb-edit_image-thumb-container").append(thumb_template);
            url = self.get_image_version_url(value, index,'thumbnail');
            jQuery(document).find(".twbb-edit_image-thumb-new").attr("data-key", index);
            jQuery(document).find(".twbb-edit_image-thumb-new img").attr("src", url);
            if( index == self.editble_image ) {
                jQuery(document).find(".twbb-edit_image-thumb-new").addClass("twbb-edit_image-thumb-active");
            }
            jQuery(document).find(".twbb-edit_image-thumb-new").removeClass("twbb-edit_image-thumb-new");
        })
    }

    update_edit_page_thumbs() {
        let self = this;
        let url = self.get_image_version_url(self.images[self.editble_image], self.editble_image,'thumbnail');
        let editable_thumb = jQuery(document).find(".twbb-edit_image-thumb[data-key='"+self.editble_image+"']");
        editable_thumb.find("img").attr("src", url);
    }


    /**
     * Multiple view menu button callback
    */
    multiple_view(){
        jQuery(document).find(".twbb-image-gen-topbar-undo-container").empty();
        jQuery(document).find(".twbb-image-gen-topbar-title").text("Multiple views");
        let multi_image_templ = jQuery(document).find("#twbb-image-multi_image-editor-template").html();
        jQuery(document).find(".twbb-image-gen-container .twbb-image-gen-editor").empty().append(multi_image_templ);

        let image_preview_templ = jQuery(document).find("#twbb-image-edit_image-preview-template").html();
        jQuery(document).find(".twbb-image-gen-container .twbb-image-gen-preview").empty().append(image_preview_templ);

        let image_url = this.get_image_version_url(this.images[this.editble_image], this.editble_image,'original');
        let full_size_url = this.get_image_version_url(this.images[this.editble_image], this.editble_image,'full_size');
        jQuery(document).find(".twbb-image-gen-container .twbb-edit_image-preview img").attr("src", image_url);
        let edit_tools_template = jQuery("#twbb-image-edit_image-tools-template").html();
        jQuery(document).find(".twbb-edit_image .twbb-edit_image-container").append(edit_tools_template);
        jQuery(document).find(".twbb-edit_image .twbb-image-download").attr({"data-href": full_size_url, "href" : ""});

        /* Set previously selected values to options */
        if( this.multiview_images_count_option != '' ) {
            jQuery(document).find(".twbb-multi_image-editor-image_count .twbb-select-value").attr('data-value', this.multiview_images_count_option);
            jQuery(document).find(".twbb-multi_image-editor-image_count .twbb-select-value > span").text(this.multiview_images_count_option+' images');
            jQuery(document).find(".twbb-multi_image-editor-image_count .twbb-select-dropdown li").removeClass("twbb-select-active");
            jQuery(document).find(".twbb-multi_image-editor-image_count .twbb-select-dropdown li[data-value='"+this.multiview_images_count_option+"']").addClass("twbb-select-active");
        }

        this.set_edit_page_thumbs();

    }

    /**
     * Select fields functionality
    */
    selectField() {
        let self = this;
        /* Open/close dropdown */
        jQuery(document).off( "click", ".twbb-select .twbb-select-value, .twbb-image_style-close").on("click", ".twbb-select .twbb-select-value, .twbb-image_style-close", function() {
            if( self.current_action != "" ) {
                return;
            }
            let dropdown = jQuery(this).closest(".twbb-select").find(".twbb-select-dropdown");
            if( dropdown.is(":hidden") ) {
                jQuery(document).find(".twbb-select-dropdown").hide();
                jQuery(document).find(".twbb-select").removeClass("twbb-select-opened");
                jQuery(document).find(".twbb-select-value i.twbb-select-arrow").removeClass("twbb-select-arrow-up");
                jQuery(this).closest(".twbb-select").addClass("twbb-select-opened");
                jQuery(this).closest(".twbb-select").find(".twbb-select-value i.twbb-select-arrow").addClass("twbb-select-arrow-up");
                dropdown.show();
                if( dropdown.hasClass("twbb-select-dropdown-image_style") ) {
                    dropdown = dropdown.find(".twbb-select-dropdown-image_style-content");
                }
                let scrollTo = dropdown.find('.twbb-select-active');
                // Calculating new position of scrollbar
                let position = scrollTo.offset().top - dropdown.offset().top + dropdown.scrollTop();
                // Setting the value of scrollbar
                dropdown.scrollTop(position);

            } else {
                jQuery(this).closest(".twbb-select").find(".twbb-select-value i.twbb-select-arrow").removeClass("twbb-select-arrow-up");
                jQuery(this).closest(".twbb-select").removeClass("twbb-select-opened");
                dropdown.hide();
            }
        });

        /* Choose option from select action */
        jQuery(document).off( "click", ".twbb-select-dropdown li").on("click", ".twbb-select-dropdown li", function() {
            let val = jQuery(this).attr("data-value");
            let title = jQuery(this).find(".twbb-image-menu-preview-title").text();
            if( !title ) {
                title = jQuery(this).text();
            }
            jQuery(this).closest(".twbb-select-dropdown").find(".twbb-select-active").removeClass("twbb-select-active");
            jQuery(this).addClass("twbb-select-active");
            jQuery(this).closest(".twbb-select").find(".twbb-select-value").attr("data-value", val);
            jQuery(this).closest(".twbb-select").find(".twbb-select-value > span").text(title);
            jQuery(this).closest(".twbb-select").find(".twbb-select-dropdown").hide();
            jQuery(this).closest(".twbb-select").find(".twbb-select-value i.twbb-select-arrow").removeClass("twbb-select-arrow-up");

            /* Set new values to appropriat option which will use when user back the previous tab */
            let parent = jQuery(this).closest(".twbb-image-editor-row");
            if( parent.hasClass("twbb-new_image-editor-image_style") ) {
                self.image_style_option = val;
            } else if( parent.hasClass("twbb-new_image-editor-image_ratio") || parent.hasClass("twbb-edit_image-editor-image_ratio") ) {
                self.ratio_option = val;
            } else if( parent.hasClass("twbb-new_image-editor-image_count") ) {
                self.new_images_count_option = val;
            } else if( parent.hasClass("twbb-edit_image-editor-image_resolution") ) {
                self.image_resolution_option = val;
            } else if( parent.hasClass("twbb-multi_image-editor-image_count") ) {
                self.multiview_images_count_option = val;
            }
        });
    }

    download_image( image_url, that ) {
        let rest_route = twbb_write_with_ai_data.rest_route + "/ai_image_download";
        let form_data = new FormData();
        form_data.append('image_url', image_url);
        fetch(rest_route, {
            method: 'POST',
            headers: {
                'X-WP-Nonce': twbb_write_with_ai_data.ajaxnonce
            },
            body: form_data,
        })
            .then((response) => response.json())
            .then((data) => {
                if (data['success']) {
                    let download_path = data['data']['download_url'];
                    jQuery(that).attr("href", download_path);
                    if( typeof jQuery(that)[0] != 'undefined' ) {
                        jQuery(that)[0].click();
                    }

                }
            }).catch((error) => {
        });

    }
}