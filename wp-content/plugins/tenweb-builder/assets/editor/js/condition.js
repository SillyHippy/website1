    var rest_route = twbb_editor.rest_route + "10webBuilder/conditions/";
    var conditions_added = false;
    var saved_conditions_length = 0;
    var btn_type = "publish";

    function hide_condition_popup() {
        var condition_text = '', condition_count;
        if ( twbb_editor.admin_condition_class ) {
            window.parent.tb_remove();
        } else {
            jQuery('.twbb-condition-popup-overlay').hide();
            jQuery('.twbb-condition-popup-overlay .twbb-condition-notif-container').html('').hide();
            jQuery('.twbb-condition-section-wrapper').css('display', 'block');
            jQuery('#twbb-condition-save').html(twbb_editor.texts.publish);
            btn_type = "publish";
        }
        condition_count = jQuery('.twbb-condition-section-wrapper').children().length;
        if( condition_count != 0 ) {
            if(jQuery('.twbb-condition-section-wrapper').children().length === 1) {
                condition_text = condition_count + ' condition';
            } else {
                condition_text = condition_count + ' conditions';
            }
        } else {
            condition_text = 'Add Condition';
        }
        window.parent.jQuery('.display_admin_condition_popup.selected_condition').html(condition_text);
    }

    jQuery(document).ready(function () {

        if ( twbb_editor.admin_condition_class ) {
            jQuery('.twbb-condition-popup-overlay').addClass(twbb_editor.admin_condition_class);
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
        }

        jQuery('.twbb-condition-popup-overlay').on('click', function (e) {
            var el = jQuery(e.target);
            if (
                el.hasClass('twbb-condition-popup-overlay') ||
                el.hasClass('twbb-condition-popup-close') ||
                el.closest('.twbb-condition-popup-close').length > 0
            ) {
                hide_condition_popup()
            }
        });

        jQuery(document).on('keyup', function (evt) {
            var $popup = jQuery('.twbb-condition-popup-overlay');
            if (evt.key === "Escape" && $popup.is(':visible')) {
                hide_condition_popup()
            }
        });

        jQuery('#twbb-condition-add-new').on('click', function (e) {
            e.preventDefault();
            add_condtion_html([]);
            return false;
        });

        jQuery('#twbb-condition-save').on('click', function (e) {
            e.preventDefault();

            var data_for_save = [];
            var sections = jQuery('.twbb-condition-section');

            if (check_widgets_type() === false) {
                return;
            }
            show_popup_loading();
            if (sections.length > 0) {
                sections.each(function () {

                    var $_this = jQuery(this);
                    var condition_data = {};
                    $_this.find('select').each(function () {
                        if (this.getAttribute('data-level') == '5') {
                            condition_data[this.getAttribute('data-name')] = jQuery(this).select2('val');
                        } else {
                            condition_data[this.getAttribute('data-name')] = this.value;
                        }
                    });
                    data_for_save.push(condition_data);
                });
            } else {
                data_for_save = [];
            }

            analyticsDataPush ( 'Display Conditions', 'Display Conditions', 'conditions_published');

            jQuery.ajax({
                type: "POST",
                url: rest_route + "save_conditions",
                data: {
                    conditions: JSON.stringify(data_for_save),
                    post_id: twbb_editor.post_id
                },
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', twbb_editor.rest_nonce);
                }
            }).done(function (data) {
                hide_popup_loading();
                hide_condition_popup();
            }).fail(function (data) {
                hide_popup_loading();
                alert('Failed');
            });

            return false;
        });
    });

    function add_condtion_html(options) {
        var html = '<div class="twbb-condition-section">' +
            '<div class="twbb-condition-section-selects"></div>' +
            '<span class="twbb_condition_change_loader"><i class="fas fa-spin fa-circle-notch" aria-hidden="true"></i></span>' +
            '<div class="twbb-condition-delete"></div>' +
            '</div>';
        if(jQuery("#elementor-preview-iframe").contents().find('.twbb-condition-section-wrapper').length) {
              jQuery("#elementor-preview-iframe").contents().find('.twbb-condition-section-wrapper').append(html);
              var condition_section = jQuery("#elementor-preview-iframe").contents().find('.twbb-condition-section').last().find('.twbb-condition-section-selects');
        } else {
              jQuery('.twbb-condition-section-wrapper').append(html);
              var condition_section = jQuery('.twbb-condition-section').last().find('.twbb-condition-section-selects');
        }
        new condition(condition_section, options);
    }

    function show_popup_loading() {
      jQuery('.twbb-condition-popup-content').addClass('twbb-condition-popup-loading');
    }

    function hide_popup_loading() {
      jQuery('.twbb-condition-popup-content').removeClass('twbb-condition-popup-loading');
    }

    function saved_condition_added() {
        saved_conditions_length--;
        if (saved_conditions_length <= 0) {
            hide_popup_loading();
        }
    }

    function check_widgets_type() {
        var notifications = [];

        var notif_container = jQuery('.twbb-condition-notif-container');

        notif_container.html('').hide();


        if (twbb_editor.twbb_template_type === "twbb_archive") {
            if (twbb_is_widget_added("twbb-posts-archive") === false) {
                // notifications.push('<p class="twbb_c_error">Archive template required Posts Archive widget.</p>');
            }
        } else if (twbb_editor.twbb_template_type === "twbb_single" ) {
            if ( false === twbb_is_widget_added("twbbpost-content") && false === twbb_is_widget_added("post-content") && false === twbb_is_widget_added("twbb_product-content") && false === twbb_is_widget_added("product-content") ) {
                notifications.push('<p class="twbb_c_error"><span>!</span> ' + twbb_editor.texts.content_missing + '</p>');
            }
        } else if (twbb_editor.twbb_template_type === "twbb_single_product" ) {
            if ( false === twbb_is_widget_added("twbb_product-content") && false === twbb_is_widget_added("product-content") ) {
                notifications.push('<p class="twbb_c_error"><span>!</span> ' + twbb_editor.texts.content_missing + '</p>');
            }
        }

        if (notifications.length > 0 && btn_type === "publish") {

            notif_container.html(notifications.join('')).show();
            btn_type = "continue";
            jQuery('.twbb-condition-section-wrapper').css('display', 'none');
            jQuery('#twbb-condition-save').html(twbb_editor.texts.continue);

            return false;
        } else {
            return true;
        }
    }

    var condition = function (condition_section, twbb_options) {
        var _this = this;

        var archive_static_pages = ['author', 'date', 'search'];
        var singular_static_pages = ['front_page', 'not_found'];
        var last_select_id;
        var saved_options_added = false;

        this.condition_section = condition_section;
        this.cache = [];
        this.options = twbb_options;
        this.template_type = twbb_editor.twbb_template_type;

        this.init = function () {

            set_last_select_id();
            add_condition_type();
            add_page_type();

            // this.options = [];
            this.condition_section.on('change', function (e) {

                var el = jQuery(e.target);
                var level = parseInt(el.data('level'));
                if (level < 2) {
                    return true;
                }
            });

            this.condition_section.closest('.twbb-condition-section').find('.twbb-condition-delete').on('click', function () {
                if (confirm(twbb_editor.texts.are_your_sure)) {
                    _this.condition_section.closest('.twbb-condition-section').remove();
                }
            });
        };

        function add_condition_type() {
            var html = '<select class="twbb-condition-type" data-name="condition_type" data-level="1">' +
                '<option ' + get_selected('condition_type', 'include') + ' value="include">' + twbb_editor.texts.include + '</option>' +
                '<option ' + get_selected('condition_type', 'exclude') + ' value="exclude">' + twbb_editor.texts.exclude + '</option>' +
                '</select>';

            remove_saved_option('condition_type');
            _this.condition_section.append(html);
        }

        function add_page_type() {
            var style = "";
            if (_this.template_type === "twbb_single" ) {
                _this.options.page_type = "singular";
                style = "style='display:none;'";
            }
            else if (_this.template_type === "twbb_single_product" ) {
                _this.options.page_type = "singular";
                style = "style='display:none;'";
            } else if (_this.template_type === "twbb_archive") {
                _this.options.page_type = "archive";
                style = "style='display:none;'";
            } else if (_this.template_type === "twbb_archive_products") {
                _this.options.page_type = "archive";
                style = "style='display:none;'";
            }

            var html = '<select ' + style + ' class="twbb-condition-page-type" data-name="page_type" data-level="2">' +
                '<option ' + get_selected('page_type', 'general') + ' value="general">' + twbb_editor.texts.general + '</option>' +
                '<option ' + get_selected('page_type', 'archive') + ' value="archive">' + twbb_editor.texts.archive + '</option>' +
                '<option ' + get_selected('page_type', 'singular') + ' value="singular">' + twbb_editor.texts.singular + '</option>' +
                '</select>';

            remove_saved_option('page_type');
            _this.condition_section.append(html);

            _this.condition_section.find('.twbb-condition-page-type').on('change', function (e) {
              var value = e.target.value;

                level_changed(2);
                if (value === 'general') {
                    return true;
                }

                add_new_select({}, 'post_types/' + value, add_page_type_options, {"type": value}, value);
            }).trigger('change');
        }

        function add_page_type_options() {
            var options = this.response.options;
            var args = this.args;
            var options_html = '';
            /* Check if no condition is set or condition is removed, as it's post type is removed. */
            var got_selected = typeof _this.options['post_type'] === "undefined" || _this.options['post_type'] === '';
            for (var i in options) {
                got_selected = got_selected || 'selected' === get_selected('post_type', options[i].id);
                options_html += '<option ' + get_selected('post_type', options[i].id) + ' value="' + options[i].id + '">' + options[i].text + '</option>';
            }

            if ( got_selected === true ) {
                remove_saved_option('post_type');
                var class_name = 'twbb-condition-post-types twbb-condition-post-types-' + args.type;

                var html = '<select class="' + class_name + '" data-name="post_type" data-level="3">' +
                  options_html +
                  '</select>';

                _this.condition_section.append(html);
                _this.condition_section.find('.twbb-condition-post-types-' + args.type).on('change', function (e) {

                    level_changed(3);

                    if (args.type === 'archive') {
                        archive_type_on_change(e);
                    } else {
                        singular_type_on_change(e);
                    }
                }).trigger('change');
            }
            else {
                alert(twbb_editor.texts.condition_removed);
                _this.condition_section.closest('.twbb-condition-section').remove();
                saved_condition_added();
            }
        }

        function singular_type_on_change(e) {
            var value = e.target.value;

            if (value === 'all' || in_array(value, singular_static_pages)) {
                return true;
            }

            var cache_key = 'singular_type_' + value;

            add_new_select({}, 'post_filter_types/' + value, add_post_filter_types, {"type": value}, cache_key);
        }

        function add_post_filter_types() {
            var options = this.response.options;
            var options_html = '';

          for (var i in options) {
                options_html += '<option ' + get_selected('filter_type', options[i].id) + ' value="' + options[i].id + '">' + options[i].text + '</option>';
            }
            remove_saved_option('filter_type');
            var class_name = 'twbb-condition-post-filter-type';

            var html = '<select class="' + class_name + '" data-name="filter_type" data-level="4">' +
                options_html +
                '</select>';

          _this.condition_section.append(html);

            _this.condition_section.find('.twbb-condition-post-filter-type').on('change', function (e) {
                var value = e.target.value;

                level_changed(4);
                if (value === 'all') {
                    return;
                }

                add_select2(value);

            }).trigger('change');
        }

        function archive_type_on_change(e) {

            var value = e.target.value;

            if (value === 'all' || in_array(value, archive_static_pages)) {
                return true;
            }

            var cache_key = 'archive_type_' + value;

            add_new_select({}, 'archive_filter_types/' + value, add_archive_filter_types, {"type": value}, cache_key);
        }

        function add_archive_filter_types() {
            var options = this.response.options;
            var options_html = '';

            for (var i in options) {
                options_html += '<option ' + get_selected('filter_type', options[i].id) + ' value="' + options[i].id + '">' + options[i].text + '</option>';
            }
            remove_saved_option('filter_type');
            var class_name = 'twbb-condition-archive-filter-type';

            var html = '<select class="' + class_name + '" data-name="filter_type" data-level="4">' +
                options_html +
                '</select>';

            _this.condition_section.append(html);

            _this.condition_section.find('.twbb-condition-archive-filter-type').on('change', function (e) {
                var value = e.target.value;

                level_changed(4);
                if (value === 'all') {
                    return true;
                }

                add_select2(value);

            }).trigger('change');
        }

        function add_select2(search_in) {
            var rest_url = rest_route + ((search_in === 'specific_posts') ? 'posts/' : 'taxonomy/');


            var options_html = "";

            for (var i in _this.options.specific_pages_options) {
                var value = _this.options.specific_pages_options[i]['id'];
                var title = _this.options.specific_pages_options[i]['text'];

                options_html += '<option selected value="' + value + '">' + title + '</option>';
            }
            remove_saved_option('specific_pages_options');

            var html = '<select class="twbb-condition-get-specific-filters" multiple="multiple" data-name="specific_pages" data-level="5">' + options_html + '</select>';
            _this.condition_section.append(html);
            _this.condition_section.find('.twbb-condition-get-specific-filters').select2({
                ajax: {
                    url: rest_url,
                    data: function (params) {

                        var query = {
                            search_in: search_in,
                            search: params.term,
                            post_type: _this.condition_section.find('.twbb-condition-post-types-singular').val()
                        };

                        return query;
                    },
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('X-WP-Nonce', twbb_editor.rest_nonce);
                    },
                    processResults: function (data) {
                        return {
                            "results": data.data.options,
                        };

                    },
                }
            });

            level_changed(5);
        }

        function level_changed(level) {
            if (_this.template_type === 'twbb_archive' || _this.template_type === 'twbb_single') {
                _this.condition_section.attr('data-currentLevel', level - 1);
            } else {
                _this.condition_section.attr('data-currentLevel', level);
            }

            for (var i = level + 1; i <= 5; i++) {
                var el = _this.condition_section.find('select[data-level="' + i + '"]');

                if (el.length === 0) {
                    continue;
                }

                if (i === 5) {
                  el.select2('destroy');
                }

                el.remove();
            }
      _this.condition_section.find("#condition_change_loader").remove();


    }

        function add_new_select(args, endpoint, done, callback_args, cache_key) {

             if (cache_key === null || typeof _this.cache[cache_key] === 'undefined') {
                _this.condition_section.parent().find(".twbb_condition_change_loader").css("visibility", "visible");
                do_ajax(args, endpoint, done, callback_args, cache_key)
            } else {
                done.apply({
                    'response': _this.cache[cache_key],
                    'args': callback_args,
                });
            }
        }

        function do_ajax(args, endpoint, done, callback_args, cache_key) {

            var url = rest_route + endpoint;

            jQuery.ajax({
                type: "GET",
                url: url,
                data: args,
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', twbb_editor.rest_nonce);
                }
            }).done(function (data) {

                if (data.success === false) {
                    alert('Failed');
                    return true;
                }

                if (typeof cache_key !== "undefined") {
                    _this.cache[cache_key] = data.data;
                }

                if (typeof done === "function") {

                    var done_args = {
                        response: data.data,
                        args: callback_args
                    };

                    done.apply(done_args);
                }
              if(jQuery("#elementor-preview-iframe").contents().find('.twbb-condition-section-wrapper').length) {
                jQuery("#elementor-preview-iframe").contents().find(".twbb_condition_change_loader").css('visibility', 'hidden');
              } else {
                jQuery(".twbb_condition_change_loader").css('visibility', 'hidden');
              }



            }).fail(function (data) {
                alert('Failed');
            });


        }

        function get_selected(key, value) {

            if (saved_options_added === true) {
                return "";
            }

            if (typeof _this.options[key] !== "undefined" && _this.options[key] == value) {
                return "selected";
            } else {
                return "";
            }
        }

        function remove_saved_option(key) {

            if (saved_options_added === true) {
                return;
            }

            if (typeof _this.options[key] !== "undefined") {
                if (key === "specific_pages_options") {
                    _this.options.specific_pages_options = [];
                } else {
                    delete _this.options[key];
                }
            }

            /* Last selection reached or first selection does not exist */
            if (last_select_id === key/* || ('post_type' == key && saved_options_added !== true)*/) {
                saved_options_added = true;
                saved_condition_added();
            }


        }

        function set_last_select_id() {
            if (_this.options.page_type === 'general') {
                last_select_id = 'page_type';
                return;
            }

            if (
                _this.options.post_type === 'all' ||
                in_array(_this.options.post_type, singular_static_pages) ||
                in_array(_this.options.post_type, archive_static_pages)
            ) {
                last_select_id = 'post_type';
                return;
            }

            if (_this.options.filter_type === 'all') {
                last_select_id = 'filter_type';
            } else {
                last_select_id = 'specific_pages_options';
            }
        }

        function in_array(key, arr) {
            for (var i in arr) {
                if (arr[i] === key) {
                    return true;
                }
            }

            return false;
        }

        this.init();
    };
