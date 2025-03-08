var twbb_widgets = [];
var twbb_posts = function (args, name) {

    var _this = this;

    var current_page = 1;
    var template = "";
    var $container = null;
    var $pagination = null;
    var $widget_container = null;
    var $loading = null;
    var is_editor = (typeof elementor !== "undefined");

    this.query_args = args.query_args;
    this.query_args_hash = args.query_args_hash;
    this.widget_id = args.widget_id;
    this.settings = args.settings;
    this.posts = [];
    this.pages_count = 1;

    this.init = function () {
        set_html_elements();
        set_template();
        this.get_posts();
    };

    this.render = function () {
        var html, i;

        this.clear_html();
        var compiled = _.template(template);

        if (this.posts.length === 0) {
            $widget_container.addClass('empty-posts');
            $widget_container.append('<p>No posts found.</p>');
            return;
        }

        for (i in this.posts) {
            html = compiled(this.posts[i]);
            $widget_container.append(html);
        }
        this.display_separators();

        if (this.settings.masonry === "yes") {
            this.masonry();
        }

        if (this.settings.pagination === "yes" && this.pages_count > 1) {
            this.pagination();
        }
    };

    this.get_posts = function () {

        this.show_loading();

        if (current_page === 1 && typeof args.first_page_data !== "undefined") {
            _this.posts = args.first_page_data.posts;
            _this.pages_count = args.first_page_data.pages_count;
            _this.render();
            _this.hide_loading();
            return;
        }

        jQuery.post(twbb.ajaxurl, {
            action: 'twbb_widgets',
            widget_name: "posts",
            query_args: _this.query_args,
            query_args_hash: _this.query_args_hash,
            page: current_page,
            nonce: twbb.nonce
        }).done(function (data) {
            _this.posts = data.data.posts;
            _this.pages_count = parseInt(data.data.pages_count);
            _this.render();
            _this.hide_loading();
        }).fail(function (data) {
            _this.hide_loading();
        });

    };

    this.display_separators = function () {
        jQuery('.twbb-posts-meta-data').each(function () {

            var last_item = null;
            jQuery(this).find('.twbb-posts-meta-separator').each(function () {
                if (jQuery(this).prev().html() !== "") {
                    jQuery(this).addClass('twbb-posts-active-meta-separator');
                    last_item = jQuery(this);
                }
            });
            if ( last_item !== null ) {
                last_item.removeClass('twbb-posts-active-meta-separator');
            }
        });
    };

    this.masonry = function () {
        var $msnry = $widget_container.imagesLoaded(function () {
            // init Masonry after all images have loaded
            $msnry.masonry({
                gutter: _this.settings.masonry_column_gap.size,
                itemSelector: '.twbb-posts-item'
            }).masonry('reloadItems');
        });

    };

    this.pagination = function () {
        var html = "";

        var deactive_class = 'twbb-posts-page-deactive';
        var class_name = "";

        if (this.settings.pagination_first_last_buttons === "yes") {
            class_name = 'twbb-posts-page twbb-posts-page-first';
            if (current_page === 1) {
                class_name += ' ' + deactive_class;
            }

            html += get_page_link_html(class_name, 1, this.settings.pagination_first_label);
        }

        if (this.settings.pagination_next_prev_buttons === "yes") {
            class_name = 'twbb-posts-page twbb-posts-page-prev';
            if (current_page === 1) {
                class_name += ' ' + deactive_class;
            }

            html += get_page_link_html(class_name, current_page - 1, this.settings.pagination_prev_label);
        }

        var length = (this.pages_count > this.settings.pagination_page_limit) ? this.settings.pagination_page_limit : this.pages_count;
        if (this.settings.pagination_number_buttons === "yes") {
            for (var i = 1; i <= length; i++) {
                class_name = 'twbb-posts-page twbb-posts-page-num';
                if (i === current_page) {
                    class_name += ' twbb-posts-current-page ' + deactive_class;
                }

                html += get_page_link_html(class_name, i, i);

            }
        }

        if (this.settings.pagination_next_prev_buttons === "yes") {
            class_name = 'twbb-posts-page twbb-posts-page-next';
            if (current_page === this.pages_count) {
                class_name += ' ' + deactive_class;
            }

            html += get_page_link_html(class_name, current_page + 1, this.settings.pagination_next_label);
        }

        if (this.settings.pagination_first_last_buttons === "yes") {
            class_name = 'twbb-posts-page twbb-posts-page-last';
            if (current_page === this.pages_count) {
                class_name += ' ' + deactive_class;
            }

            html += get_page_link_html(class_name, length, this.settings.pagination_last_label);
        }

        if ($pagination === null) {
            if (this.settings.pagination_scroll_top === "yes") {
                html = "<div class='twbb-posts-pagination twbb-pagination_scroll_top'>" + html + "</div>";
            } else {
                html = "<div class='twbb-posts-pagination'>" + html + "</div>";
            }
            $widget_container.parent().append(html);
            $pagination = $container.find('.twbb-posts-pagination');
        } else {
            $pagination.append(html);
        }

        $pagination.find('.twbb-posts-page').on('click', function (e) {
            e.preventDefault();

            if (is_editor === true) {
                return false;
            }

            var page = parseInt(jQuery(this).data('page'));
            if (page < 1 || page > _this.pages_count) {
                return false;
            }

            current_page = page;
            _this.get_posts();
            if ( jQuery(this).parent().hasClass('twbb-pagination_scroll_top')) {
                jQuery(window).scrollTop(0);
            }
            return false;
        });
    };

    this.show_loading = function () {
        if ($loading === null) {
            $container.append('<div class="twbb-posts-loading"><i class="twbb-spinner-solid"></i></div>');
            $loading = jQuery($container.find('.twbb-posts-loading'));
        } else {
            $loading.show();
        }
    };

    this.hide_loading = function () {
        $loading.hide();
    };

    function set_html_elements() {
        $container = jQuery('div[data-id="' + _this.widget_id + '"]');
        if ( 0 == $container.length ) { /* Global widget */
            $container = jQuery('.elementor-global-' + _this.widget_id);
        }
        $widget_container = $container.find('.twbb-posts-widget-container');
    }

    function set_template() {
        settings = _this.settings;

        template = "";


        var img_template = "";
        var title_template = "";

        if (settings.show_image === "yes") {
            img_template = "<% if(twbb_image != '') { %><div class='twbb-posts-image'><img src='<%= twbb_image %>'/></div><% } %>";
        }

        if (settings.show_title === "yes") {
            title_template += "<div class='twbb-posts-title'>" +
                "<" + settings.title_tag + " class='twbb-posts-title-tag'><a href='<%= twbb_permalink %>'><%= post_title %></a></" + settings.title_tag + ">" +
                "</div>";
        }

        if (settings.image_position === "above_title") {
            template += img_template + title_template;
        } else {
            template += title_template + img_template;
        }

        if (typeof settings.meta_data !== "undefined" && settings.meta_data.length > 0) {
            template += "<div class='twbb-posts-meta-data'>";
            for (var i = 0; i < settings.meta_data.length; i++) {
                switch (settings.meta_data[i]) {
                    case "author":
                        template += '<span class="twbb-posts-author-meta"><% print(posts_print_author(twbb_author)) %></span>';
                        break;
                    case "date":
                        template += '<span class="twbb-posts-date-meta"><%= twbb_date %></span>';
                        break;
                    case "time":
                        template += '<span class="twbb-posts-time-meta"><%= twbb_time %></span>';
                        break;
                    case "comments":
                        template += '<span class="twbb-posts-comments-meta">' +
                            '<% if(twbb_comments > 0) { %><%=  twbb_comments %> <% }else{ print("No") } print(" comments")%>' +
                            '</span>';
                        break;
                    case "categories":
                        template += '<span class="twbb-posts-categories-meta"><% print(posts_print_terms(twbb_categories, "categories")) %></span>';
                        break;
                    case "tags":
                        template += '<span class="twbb-posts-tags-meta"><% print(posts_print_terms(twbb_tags, "tags")) %></span>';
                        break;
                }

                template += '<span class="twbb-posts-meta-separator">' + settings.meta_separator + '</span>';
            }
            template += "</div>";

        }

        if (settings.show_excerpt === "yes") {
            template += "<div class='twbb-posts-content'><%= twbb_excerpt %></div>";
        }

        if (settings.show_read_more === "yes") {
            template += "<div class='twbb-posts-read-more'>" +
                "<a href='<%= twbb_permalink %>'>" + settings.read_more_text + "</a>" +
                "</div>";
        }

        template = '<div class="twbb-posts-item">' + template + '</div>';
    }

    get_page_link_html = function (class_name, page, text) {
        return "<a href='#' class='" + class_name + "' data-page='" + page + "'>" + text + "</a>";
    };

    posts_print_author = function (twbb_author) {

        if (_this.settings.author_meta_link === "yes") {
            return "<a href='" + twbb_author.link + "'>" + twbb_author.name + "</a>";
        } else {
            return twbb_author.name;
        }
    };

    posts_print_terms = function (terms, tax) {
        var html = "";
        var prefix = (tax === "tags") ? "#" : "";
        var link = (
            (tax === "categories" && _this.settings.categories_meta_link === "yes") ||
            (tax === "tags" && _this.settings.tags_meta_link === "yes")
        );


        for (var i in terms) {
            if (link === true) {
                html += "<a href='" + terms[i].link + "'>" + prefix + terms[i].name + "</a>, ";
            } else {
                html += prefix + terms[i].name + ", ";
            }
        }

        return html.trim().slice(0, html.length - 2);
    };

    this.clear_html = function () {

        if ($widget_container !== null) {
            $widget_container.html('');
        }

        if ($pagination !== null) {
            $pagination.html('');

            if (_this.settings.masonry === "yes") {
                $widget_container.masonry('destroy');
            }
        }

    };

    this.init();
    twbb_add_widget(name, this);
};

function twbb_add_widget(name, widget) {
  if (typeof twbb_widgets[name] === "undefined") {
    twbb_widgets[name] = [];
  }
  twbb_widgets[name].push(widget);
}

function twbb_get_widgets(name) {
  if (typeof twbb_widgets[name] === "undefined") {
    return [];
  }
  else {
    return twbb_widgets[name];
  }
}

function twbb_is_widget_added(name) {
  return (jQuery('.elementor-widget-' + name).length > 0);
}

jQuery( window ).on( 'elementor/frontend/init', function () {
    var twbb_posts_ready = function ( $scope ) {
        var $element = $scope.find( '.twbb-posts-widget-container' );

        new twbb_posts( JSON.parse( $element.attr('data-params') ), $element.attr('data-widget'));
    };
    elementorFrontend.hooks.addAction('frontend/element_ready/twbb-posts.default', twbb_posts_ready );
    elementorFrontend.hooks.addAction('frontend/element_ready/twbb-posts-archive.default', twbb_posts_ready );
});


