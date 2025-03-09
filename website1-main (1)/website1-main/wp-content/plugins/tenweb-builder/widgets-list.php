<?php
if (!defined('ABSPATH')) {
    exit;
}
function twbb_get_group_widgets( $name = NULL ) {
    $modules = array(
        'facebook' => array(
            'styles' => array(
                'main' => array(
                    'src' => TWBB_URL . '/widgets/facebook/assets/styles.css',
                    'deps' => array()
                )
            ),
            'scripts' => array(
                'main' => array(
                    'src' => TWBB_URL . '/widgets/facebook/assets/scripts.js',
                    'deps' => array()
                )
            ),
        ),
        'code-highlight' => array(
            'styles' => array(
                'main' => array(
                    'src' => TWBB_URL . '/widgets/code-highlight/assets/styles.css',
                    'deps' => array()
                )
            ),
            'scripts' => array(
                'main' => array(
                    'src' => TWBB_URL . '/widgets/code-highlight/assets/script.js',
                    'deps' => array()
                )
            ),
        ),
        'posts-skins' => array(
            'styles' => array(
                'main' => array(
                    'src' => TWBB_URL . '/widgets/posts-skins/assets/style.css',
                    'deps' => array(),
                )
            ),
            'scripts' => array(
                'main' => array(
                    'src' => TWBB_URL . '/widgets/posts-skins/assets/script.js',
                    'deps' => array(),
                ),
            ),

        ),
    );
    $modules = apply_filters('twbb_get_group_widgets', $modules);
    if ( $name === NULL ) {
        return $modules;
    }
    else {
        if ( isset($modules[$name]) ) {
            return $modules[$name];
        }
        else {
            return NULL;
        }
    }
}

function twbb_get_widgets($widget_name = null) {
    $twbb_widgets = array(
        'logos' => array(
            'styles' => array(
                'logos' => array(
                    'src' => TWBB_URL . '/widgets/logos/assets/style.css',
                    'deps' => array(),
                )
            ),
            'scripts' => array(
                'logos' => array(
                    'src' => TWBB_URL . '/widgets/logos/assets/script.js',
                    'deps' => array('jquery'),
                )
            )
        ),
        'toggle' => array(
            'styles' => array(
                'toggle' => array(
                    'src' => TWBB_URL . '/widgets/toggle/assets/style.css',
                    'deps' => array(),
                )
            ),
            'scripts' => array(
                'toggle' => array(
                    'src' => TWBB_URL . '/widgets/toggle/assets/script.js',
                    'deps' => array('jquery'),
                )
            )
        ),
        'pricing-table' => array(
            'styles' => array(
                'pricing-table' => array(
                    'src' => TWBB_URL . '/widgets/pricing-table/assets/style.css',
                    'deps' => array(),
                )
            ),
            'scripts' => array(
                'pricing-table' => array(
                    'src' => TWBB_URL . '/widgets/pricing-table/assets/script.js',
                    'deps' => array(),
                )
            )
        ),
        'price-list' => array(
            'styles' => array(
                'price-list' => array(
                    'src' => TWBB_URL . '/widgets/price-list/assets/style.css',
                    'deps' => array(),
                )),
            'scripts' => array()
        ),
        'posts' => array(
            'styles' => array(
                'posts' => array(
                    'src' => TWBB_URL . '/widgets/posts-base/assets/style.css',
                    'deps' => array(),
                )
            ),
            'scripts' => array(
                'posts' => array(
                    'src' => TWBB_URL . '/widgets/posts-base/assets/script.js',
                    'deps' => array('underscore', 'jquery', 'imagesloaded', 'masonry'),
                ),
            ),
            'ajax' => true,
        ),
        'portfolio' => array(
            'styles' => array(
                'portfolio' => array(
                    'src' => TWBB_URL . '/widgets/portfolio/assets/css/style.css',
                    'deps' => array(),
                )
            ),
            'scripts' => array(
                'posts-base' => array(
                    'src' => TWBB_URL . '/widgets/portfolio/assets/js/posts-base.js',
                    'deps' => array(),
                ),
                'portfolio' => array(
                    'src' => TWBB_URL . '/widgets/portfolio/assets/js/script.js',
                    'deps' => array('twbb-posts-base-scripts'),
                )
            )
        ),
        'blockquote' => array(
            'styles' => array(
                'blockquote' => array(
                    'src' => TWBB_URL . '/widgets/blockquote/assets/style.css',
                    'deps' => array('elementor-icons-fa-brands'),
                )
            ),
            'scripts' => array()
        ),
        'call-to-action' => array(
            'styles' => array(
                'call-to-action' => array(
                    'src' => TWBB_URL . '/widgets/call-to-action/assets/style.css',
                    'deps' => array('elementor-icons-fa-solid'),
                )
            ),
        ),
        'countdown' => array(
            'styles' => array(
                'countdown' => array(
                    'src' => TWBB_URL . '/widgets/countdown/assets/style.css',
                    'deps' => array(),
                )
            ),
            'scripts' => array(
                'countdown' => array(
                    'src' => TWBB_URL . '/widgets/countdown/assets/script.js',
                    'deps' => array('jquery'),
                )
            )
        ),
        'flip-box' => array(
            'styles' => array(
                'flip-box' => array(
                    'src' => TWBB_URL . '/widgets/flip-box/assets/style.css',
                    'deps' => array('elementor-icons-fa-regular', 'elementor-icons-fa-solid'),
                )
            ),
            'admin-scripts' => array(
                'flip-box-admin' => array(
                    'src' => TWBB_URL . '/widgets/flip-box/assets/admin-scripts.js',
                    'deps' => array('jquery'),
                )
            )
        ),
	    //phpcs:ignore Squiz.PHP.CommentedOutCode.Found
        /*'form' => array(
          'admin-scripts' => array(
            'form-admin' => array(
              'src' => TWBB_URL . '/widgets/form/assets/js/admin-script.js',
              'deps' => array('jquery',
                'backbone-marionette',
                'elementor-common',
                'elementor-editor-modules',
                'elementor-editor-document',),
            )
          ),
            'styles' => array(
                'form' => array(
                    'src' => TWBB_URL . '/widgets/form/assets/css/style.css',
                    'deps' => '',
                )
            ),
            'scripts' => array(
                'form' => array(
                    'src' => TWBB_URL . '/widgets/form/assets/js/script.js',
                    'deps' => array(
                      'jquery',
                'elementor-frontend-modules',
                'twbb-sticky-lib-scripts',
                ),
                )
            )
        ),*/
        'testimonial-carousel' => array(
            'styles' => array(
                'testimonial-carousel' => array(
                    'src' => TWBB_URL . '/widgets/testimonial-carousel/assets/css/style.css',
                    'deps' => array(),
                )
            ),
            'scripts' => array(
                'testimonial-carousel' => array(
                    'src' => TWBB_URL . '/widgets/testimonial-carousel/assets/js/script.js',
                    'deps' => array(),
                ),
            ),
        ),
        'team' => array(
	        'styles' => array(
		        'team' => array(
			        'src' => TWBB_URL . '/widgets/team/assets/css/style.css',
			        'deps' => array(),
		        )
	        ),
        ),
        'widget-slider' => array(
	        'styles' => array(
		        'widget-slider' => array(
			        'src' => TWBB_URL . '/widgets/widget-slider/assets/css/style.css',
			        'deps' => array(),
		        ),
	        ),
	        'scripts' => array(
		        'widget-slider' => array(
			        'src' => TWBB_URL . '/widgets/widget-slider/assets/js/script.js',
			        'deps' => array(),
		        ),
	        ),
        ),
        'reviews' => array(
            'styles' => array(
                'base-carousel' => array(
                    'src' => TWBB_URL . '/widgets/reviews/assets/css/base-styles.css',
                    'deps' => array(),
                ),
                'reviews' => array(
                    'src' => TWBB_URL . '/widgets/reviews/assets/css/style.css',
                    'deps' => array(),
                ),
            ),
            'scripts' => array(
                'base-carousel' => array(
                    'src' => TWBB_URL . '/widgets/reviews/assets/js/base-script.js',
                    'deps' => array(),
                ),
                'reviews' => array(
                    'src' => TWBB_URL . '/widgets/reviews/assets/js/script.js',
                    'deps' => array('twbb-base-carousel-scripts'),
                ),
            ),
        ),
        'media-carousel' => array(
            'styles' => array(
                'media-carousel' => array(
                    'src' => TWBB_URL . '/widgets/media-carousel/assets/css/style.css',
                    'deps' => array('elementor-icons-fa-regular', 'elementor-icons-fa-solid'),
                )
            ),
            'scripts' => array(
                'media-carousel' => array(
                    'src' => TWBB_URL . '/widgets/media-carousel/assets/js/script.js',
                    'deps' => array('jquery'),
                ),
            )
        ),
        'share-buttons' => array(
            'admin-scripts' => array(
                'share-buttons-admin' => array(
                    'src' => TWBB_URL . '/widgets/share-buttons/assets/admin-scripts.js',
                    'deps' => array('jquery'),
                )
            ),
            'styles' => array(
                'share-buttons' => array(
                    'src' => TWBB_URL . '/widgets/share-buttons/assets/style.css',
                    'deps' => array('elementor-icons-fa-brands'),
                )
            ),
            'scripts' => array(
                'share-buttons' => array(
                    'src' => TWBB_URL . '/widgets/share-buttons/assets/script.js',
                    'deps' => array('jquery'),
                )
            )
        ),
        'login' => array(
            'styles' => array(),
        ),
        'widgets-conditions' => array(),
        /*, v1.0 disabled*/
        'custom-html' => array(
            'oninit' => TRUE,
        ),
        'quick-navigation' => array(),
        /*SITE BUILDING WIDGETS*/
        'site-title' => array(),
        'site-logo' => array(),
        'post-title' => array(),
        'archive-title' => array(),
        'nav-menu' => array(
            'styles' => array(
                'nav-menu' => array(
                    'src' => TWBB_URL . '/widgets/nav-menu/assets/style.css',
                    'deps' => array(),
                )
            ),
            'scripts' => array(
                'smartmenus' => array(
                    'src' => TWBB_URL . '/widgets/nav-menu/assets/jquery.smartmenus.js',
                    'deps' => array('underscore', 'jquery'),
                ),
                'nav-menu' => array(
                    'src' => TWBB_URL . '/widgets/nav-menu/assets/script.js',
                    'deps' => array('underscore', 'jquery'),
                ),
            ),
        ),
        'posts-archive' => array(
            'styles' => array(
                'posts' => array(
                    'src' => TWBB_URL . '/widgets/posts-base/assets/style.css',
                    'deps' => array(),
                )
            ),
            'scripts' => array(
                'posts' => array(
                    'src' => TWBB_URL . '/widgets/posts-base/assets/script.js',
                    'deps' => array('underscore', 'jquery', 'imagesloaded', 'masonry'),
                ),
            ),
            'ajax' => true,
        ),//logo
        'post-content' => array('styles' => array(), 'scripts' => array()),
        'product-content' => array('styles' => array(), 'scripts' => array()),
        'post-excerpt' => array('styles' => array(), 'scripts' => array()),
        'featured-image' => array(),
        'post-info' => array(
            'styles' => array(
                'post-info' => array(
                    'src' => TWBB_URL . '/widgets/post-info/assets/style.css',
                    'deps' => array('elementor-icons-fa-regular', 'elementor-icons-fa-solid'),
                )
            )
        ),
        'post-comments' => array(
            'styles' => array(
                'post-comments' => array(
                    'src' => TWBB_URL . '/widgets/post-comments/assets/style.css',
                    'deps' => array(),
                )
            ),),
        'post-navigation' => array(
            'styles' => array(
                'post-navigation' => array(
                    'src' => TWBB_URL . '/widgets/post-navigation/assets/style.css',
                    'deps' => array('elementor-icons-fa-solid'),
                )
            ),
        ),
        'author-box' => array(
            'styles' => array(
                'author-box' => array(
                    'src' => TWBB_URL . '/widgets/author-box/assets/style.css',
                    'deps' => array(),
                )
            ),
        ),
        'search-form' => array(
            'styles' => array(
                'search-form' => array(
                    'src' => TWBB_URL . '/widgets/search-form/assets/style.css',
                    'deps' => array(),
                )
            ),
            'scripts' => array(
                'search-form' => array(
                    'src' => TWBB_URL . '/widgets/search-form/assets/script.js',
                    'deps' => array(),
                )
            )
        ),
        'animated-headline' => array(
            'styles' => array(
                'animated-headline' => array(
                    'src' => TWBB_URL . '/widgets/animated-headline/assets/style.css',
                    'deps' => array(),
                )
            ),
            'scripts' => array(
                'animated-headline' => array(
                    'src' => TWBB_URL . '/widgets/animated-headline/assets/script.js',
                    'deps' => array(),
                )
            )
        ),
        'sitemap' => array(
            'styles' => array(
                'sitemap' => array(
                    'src' => TWBB_URL . '/widgets/sitemap/assets/style.css',
                    'deps' => array(),
                ),
            ),
        ),
        'slides' => array(
            'styles' => array(
                'slides' => array(
                    'src' => TWBB_URL . '/widgets/slides/assets/css/style.css',
                    'deps' => array(),
                ),
            ),
            'scripts' => array(
                'slides' => array(
                    'src' => TWBB_URL . '/widgets/slides/assets/js/script.js',
                    'deps' => array(),
                ),
            ),
        ),
        'image-sprite' => array(),
        'table-of-contents' => array(
            'styles' => array(
                'table-of-contents' => array(
                    'src' => TWBB_URL . '/widgets/table-of-contents/assets/style.css',
                    'deps' => array(),
                )
            ),
            'scripts' => array(
                'table-of-contents' => array(
                    'src' => TWBB_URL . '/widgets/table-of-contents/assets/script.js',
                    'deps' => array(),
                )
            )
        ),
        'hotspot' => array(
            'styles' => array(
                'hotspot' => array(
                    'src' => TWBB_URL . '/widgets/hotspot/assets/style.css',
                    'deps' => array(),
                )
            ),
            'scripts' => array(
                'hotspot' => array(
                    'src' => TWBB_URL . '/widgets/hotspot/assets/script.js',
                    'deps' => array(),
                )
            )
        ),
        'video-playlist' => array(
            'styles' => array(
                'video-playlist' => array(
                    'src' => TWBB_URL . '/widgets/video-playlist/assets/css/style.css',
                    'deps' => array(),
                )
            ),
            'scripts' => array(
                'video-playlist' => array(
                    'src' => TWBB_URL . '/widgets/video-playlist/assets/js/script.js',
                    'deps' => array(),
                )
            ),
            'admin-scripts' => array(
                'video-playlist' => array(
                    'src' => TWBB_URL . '/widgets/video-playlist/assets/js/admin-script.js',
                    'deps' => array('jquery'),
                )
            )
        ),
        'progress-tracker' => array(
            'styles' => array(
                'progress-tracker' => array(
                    'src' => TWBB_URL . '/widgets/progress-tracker/assets/css/style.css',
                    'deps' => array(),
                )
            ),
            'scripts' => array(
                'progress-tracker' => array(
                    'src' => TWBB_URL . '/widgets/progress-tracker/assets/js/script.js',
                    'deps' => array(),
                )
            ),
        ),
        'breadcrumb' => array(
            'styles' => array(
                'breadcrumb' => array(
                    'src' => TWBB_URL . '/widgets/breadcrumb/assets/style.css',
                    'deps' => array(),
                )
            ),
            'scripts' => array(
            ),
        ),
    );

    $twbb_widgets = apply_filters('twbb_get_widgets', $twbb_widgets);

    if ($widget_name === null) {
        return $twbb_widgets;
    }
    else {
        if (isset($twbb_widgets[$widget_name])) {
            return $twbb_widgets[$widget_name];
        }
        else {
            return null;
        }
    }
}

function get_custom_options() {
    return array(
        'sticky' => array(
            'styles' => array(
                'sticky' => array(
                    'src' => TWBB_URL . '/widgets/sticky/assets/css/style.css',
                    'deps' => array(),
                )
            ),
            'scripts' => array(
                'sticky-lib' => array(
                    'src' => TWBB_URL . '/widgets/sticky/assets/js/jquery.sticky.js',
                    'deps' => array('jquery'),
                ),
                'sticky' => array(
                    'src' => TWBB_URL . '/widgets/sticky/assets/js/script.js',
                    'deps' => array('twbb-sticky-lib-scripts'),
                )
            )
        ),
        'parallax' => array(
            'scripts' => array(
                'parallax-lib' => array(
                    'src' => TWBB_URL . '/widgets/parallax/assets/js/jquery.parallax.js',
                    'deps' => array('jquery'),
                ),
                'parallax' => array(
                    'src' => TWBB_URL . '/widgets/parallax/assets/js/script.js',
                    'deps' => array('twbb-parallax-lib-scripts'),
                )
            ),
            'styles' => array(
                'parallax' => array(
                    'src' => TWBB_URL . '/widgets/parallax/assets/css/style.css',
                    'deps' => array(),
                )
            )
        ),
    );
}

function twbb_get_tags($tag_name = null) {
    $twbb_tags = array(
        'post-title' => array(
            'class-name' => 'Post_Title'
        ),
        'archive-title' => array(
            'class-name' => 'Archive_Title'
        ),
        'post-url' => array(
            'class-name' => 'Post_URL'
        ),
        'site-title' => array(
            'class-name' => 'Site_Title'
        ),
        'site-url' => array(
            'class-name' => 'Site_URL'
        ),
        'featured-image' => array(
            'class-name' => 'Featured_Image'
        ),
        'site-logo' => array(
            'class-name' => 'Site_Logo'
        ),
        'sitemap' => array(
            'class-name' => 'Sitemap'
        ),
    );

    $twbb_tags = apply_filters('twbb_get_tags', $twbb_tags);

    if ($tag_name === null) {
        return $twbb_tags;
    }
    else {

        if (isset($twbb_tags[$tag_name])) {
            return $twbb_tags[$tag_name];
        }
        else {
            return null;
        }

    }

}
