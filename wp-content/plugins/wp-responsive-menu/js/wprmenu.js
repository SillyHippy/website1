(function(window) {
    'use strict';

    function classReg(className) {
        return new RegExp("(^|\\s+)" + className + "(\\s+|$)");
    }

    var hasClass, addClass, removeClass;

    if ('classList' in document.documentElement) {
        hasClass = function(elem, c) {
            return elem.classList.contains(c);
        };
        addClass = function(elem, c) {
            elem.classList.add(c);
        };
        removeClass = function(elem, c) {
            elem.classList.remove(c);
        };
    } else {
        hasClass = function(elem, c) {
            return classReg(c).test(elem.className);
        };
        addClass = function(elem, c) {
            if (!hasClass(elem, c)) {
                elem.className = elem.className + ' ' + c;
            }
        };
        removeClass = function(elem, c) {
            elem.className = elem.className.replace(classReg(c), ' ');
        };
    }

    function toggleClass(elem, c) {
        var fn = hasClass(elem, c) ? removeClass : addClass;
        fn(elem, c);
    }

    window.classie = {
        hasClass: hasClass,
        addClass: addClass,
        removeClass: removeClass,
        toggleClass: toggleClass,
        has: hasClass,
        add: addClass,
        remove: removeClass,
        toggle: toggleClass
    };

})(window);

jQuery(document).ready(function($) {
    var Mgwprm = document.getElementById('mg-wprm-wrap');
    var wprm_menuDir = document.getElementById('wprMenu');
    var body = document.body;

    if (jQuery('.wprmenu_bar').hasClass('bodyslide'))
        jQuery('body').addClass('cbp-spmenu-push');

    jQuery('.wprmenu_bar').click(function(e) {
        if ($(e.target).hasClass('bar_logo')) return;

        classie.toggle(this, 'active');
        jQuery(this).find('div.hamburger').toggleClass('is-active');

        if (jQuery(this).hasClass('active')) {
            jQuery('html').addClass('wprmenu-body-fixed');
            if (wprmenu.enable_overlay == '1') {
                jQuery('div.wprm-wrapper').find('.wprm-overlay').addClass('active');
            }
        } else {
            jQuery('html').removeClass('wprmenu-body-fixed');
            if (wprmenu.enable_overlay == '1') {
                jQuery('div.wprm-wrapper').find('.wprm-overlay').removeClass('active');
            }
        }

        classie.toggle(Mgwprm, 'cbp-spmenu-open');
        close_sub_uls();
    });

    var mt = $('meta[name=viewport]');
    mt = mt.length ? mt : $('<meta name="viewport" />').appendTo('head');
    mt.attr('content', wprmenu.zooming == 0 ? 'user-scalable=no, width=device-width, maximum-scale=1, minimum-scale=1'
        : 'user-scalable=yes, width=device-width, initial-scale=1.0, minimum-scale=1');

    $('body').click(function(event) {
        if ($('#wprmenu_bar').hasClass('active')) {
            $('#wprmenu_bar .wprmenu_icon').addClass('open');
        } else {
            $('#wprmenu_bar .wprmenu_icon').removeClass('open');
        }
    });

    var menu = jQuery('#mg-wprm-wrap');
    var menu_ul = jQuery('#wprmenu_menu_ul');

    jQuery(document).mouseup(function(e) {
        if (($(e.target).hasClass('wprmenu_bar') || $(e.target).parents('.wprmenu_bar').length == 0) &&
            ($(e.target).hasClass('cbp-spmenu') || $(e.target).parents('.cbp-spmenu').length == 0)) {
            if (menu.is(':visible')) {
                $('.hamburger.is-active').trigger('click');
            }
        }
    });

    menu.find('ul.sub-menu').each(function() {
        var sub_ul = $(this),
            parent_a = sub_ul.prev('a'),
            parent_li = parent_a.parent('li').first();

        parent_a.addClass('wprmenu_parent_item');
        parent_li.addClass('wprmenu_parent_item_li');
        parent_a.before('<span class="wprmenu_icon wprmenu_icon_par icon_default"></span> ');
        sub_ul.hide();
    });

    $('.wprmenu_icon_par').on('click', function() {
        var t = $(this),
            child_ul = t.parent('li').find('ul.sub-menu').first();
        child_ul.slideToggle(300);
        t.toggleClass('wprmenu_par_opened');
        t.parent('li').first().toggleClass('wprmenu_no_border_bottom');
    });

    function close_sub_uls() {
        menu.find('ul.sub-menu').each(function() {
            var ul = $(this),
                icon = ul.parent('li').find('.wprmenu_icon_par'),
                li = ul.parent('li');

            if (ul.is(':visible')) ul.slideUp(300);
            icon.removeClass('wprmenu_par_opened');
            li.removeClass('wprmenu_no_border_bottom');
        });
    }

    if (wprmenu.parent_click == 'yes') {
        $('a.wprmenu_parent_item').on('click', function(event) {
            event.preventDefault();
            $('.wprmenu_icon_par').trigger('click');
        });
    }

    /** 
     * **Fix for Mobile Navigation Issue**  
     * Redirects hash links to `https://justlegalsolutions.org/#Courier`
     */
    $('#wprmenu_menu_ul a').click(function(event) {
        var targetHref = $(this).attr('href');

        if (targetHref.startsWith("#")) {
            event.preventDefault();
            window.location.href = "https://justlegalsolutions.org/" + targetHref; // Full absolute URL
        }

        if (wprmenu.parent_click != 'yes' || (wprmenu.parent_click == 'yes' && !$(this).hasClass('wprmenu_parent_item'))) {
            $('.hamburger.is-active').trigger('click');
        }
    });

    if (wprmenu.swipe == '1') {
        jQuery('body').swipe({
            excludedElements: "button, input, select, textarea, .noSwipe",
            longTapThreshold: 200,
            swipe: function(event, direction) {
                var menu_el = $('.wprmenu_bar .hamburger, .wprmenu_bar .wpr-custom-menu');
                if (direction == 'left' && menu_el.hasClass('is-active')) menu_el.trigger('click');
                if (direction == 'right' && !menu_el.hasClass('is-active')) menu_el.trigger('click');
            }
        });
    }

    function toggle_sub_uls($action) {
        $('#mg-wprm-wrap').find('ul.sub-menu').each(function() {
            var ul = $(this),
                icon = ul.parent('li').find('.wprmenu_icon_par'),
                li = ul.parent('li');

            if ($action == 'open') {
                ul.slideDown(300);
                icon.removeClass(wprmenu.submenu_open_icon).addClass(wprmenu.submenu_close_icon);
            } else {
                if (ul.is(':visible')) ul.slideUp(300);
                icon.removeClass(wprmenu.submenu_close_icon).addClass(wprmenu.submenu_open_icon);
                li.removeClass('wprmenu_no_border_bottom');
            }
        });
    }
});
