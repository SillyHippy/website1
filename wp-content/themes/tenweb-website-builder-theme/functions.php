<?php
include_once 'config.php';

if ( ! function_exists( 'tenweb_builder_theme_setup' ) ) :
  /**
   * Sets up theme defaults and registers support for various WordPress features.
   *
   * Note that this function is hooked into the after_setup_theme hook, which
   * runs before the init hook. The init hook is too late for some features, such
   * as indicating support for post thumbnails.
   */
  function tenweb_builder_theme_setup() {
    /*
     * Make theme available for translation.
     * Translations can be filed in the /languages/ directory.
     * If you're building a theme based on tenweb-website-builder-theme, use a find and replace
     * to change 'tenweb-website-builder-theme' to the name of your theme in all the template files.
     */
    load_theme_textdomain( 'tenweb-website-builder-theme', get_template_directory() . '/languages' );

    // Add default posts and comments RSS feed links to head.
    add_theme_support( 'automatic-feed-links' );

    /*
     * Let WordPress manage the document title.
     * By adding theme support, we declare that this theme does not use a
     * hard-coded <title> tag in the document head, and expect WordPress to
     * provide it for us.
     */
    add_theme_support( 'title-tag' );

    /*
     * Enable support for Post Thumbnails on posts and pages.
     *
     * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
     */
    add_theme_support( 'post-thumbnails' );

    // This theme uses wp_nav_menu() in one location.
    register_nav_menus( array(
                          'header_menu'   => __('Header Menu', 'tenweb-website-builder-theme' ),
                          'footer_menu'   => __('Footer Menu', 'tenweb-website-builder-theme' ),
                          'sidebar_menu'  => __('Secondary Menu', 'tenweb-website-builder-theme' ),
                        ) );
    /*
     * Switch default core markup for search form, comment form, and comments
     * to output valid HTML5.
     */
    add_theme_support( 'html5', array(
      'search-form',
      'comment-form',
      'comment-list',
      'gallery',
      'caption',
    ) );

    // Set up the WordPress core custom background feature.
    add_theme_support( 'custom-background', apply_filters( 'tenweb_builder_theme_custom_background_args', array(
      'default-color' => 'ffffff',
      'default-image' => '',
    ) ) );

    // Add theme support for selective refresh for widgets.
    add_theme_support( 'customize-selective-refresh-widgets' );

    /**
     * Add support for core custom logo.
     *
     * @link https://codex.wordpress.org/Theme_Logo
     */
    add_theme_support( 'custom-logo', array(
      'height'      => 250,
      'width'       => 250,
      'flex-width'  => true,
      'flex-height' => true,
    ) );

    // Enabling the themes that declare WC support
	require get_template_directory() . '/inc/class-wc-theme-support.php';
  }
endif;
add_action( 'after_switch_theme', 'flush_rewrite_rules' );
add_action( 'after_switch_theme', 'tenweb_set_elementor_settings' );
add_action( 'after_setup_theme', 'tenweb_builder_theme_setup' );

/**
 * UnSet Elementor Disable Default Colors and Disable Default Fonts in settings
 *
 */
function tenweb_set_elementor_settings() {
    update_option('elementor_disable_typography_schemes', '');
    update_option('elementor_disable_color_schemes', '');
}

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function tenweb_builder_theme_content_width() {
  // This variable is intended to be overruled from themes.
  // Open WPCS issue: {@link https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/1043}.
  // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
  $GLOBALS['content_width'] = apply_filters( 'tenweb_builder_theme_content_width', 640 );
}
add_action( 'after_setup_theme', 'tenweb_builder_theme_content_width', 0 );



/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function tenweb_builder_theme_widgets_init() {
  register_sidebar( array(
                      'name'          => __( 'Sidebar Widget1', 'tenweb-website-builder-theme' ),
                      'id'            => 'sidebar-1',
                      'description'   => __( 'Add widgets here to appear in your sidebar on blog posts and archive pages.', 'tenweb-website-builder-theme' ),
                      'before_widget' => '<section id="%1$s" class="widget %2$s">',
                      'after_widget'  => '</section>',
                      'before_title'  => '<h2 class="widget-title">',
                      'after_title'   => '</h2>',
                    ) );
  register_sidebar( array(
                      'name'          => __( 'Sidebar Widget2', 'tenweb-website-builder-theme' ),
                      'id'            => 'sidebar-4',
                      'description'   => __( 'Add widgets here to appear in your sidebar on blog posts and archive pages.', 'tenweb-website-builder-theme' ),
                      'before_widget' => '<section id="%1$s" class="widget %2$s">',
                      'after_widget'  => '</section>',
                      'before_title'  => '<h2 class="widget-title">',
                      'after_title'   => '</h2>',
                    ) );

  register_sidebar( array(
                      'name'          => __( 'Sidebar Widget3', 'tenweb-website-builder-theme' ),
                      'id'            => 'sidebar-5',
                      'description'   => __( 'Add widgets here to appear in your sidebar on blog posts and archive pages.', 'tenweb-website-builder-theme' ),
                      'before_widget' => '<section id="%1$s" class="widget %2$s">',
                      'after_widget'  => '</section>',
                      'before_title'  => '<h2 class="widget-title">',
                      'after_title'   => '</h2>',
                    ) );

  register_sidebar( array(
                      'name'          => __( 'Footer 1', 'tenweb-website-builder-theme' ),
                      'id'            => 'sidebar-2',
                      'description'   => __( 'Add widgets here to appear in your footer.', 'tenweb-website-builder-theme' ),
                      'before_widget' => '<section id="%1$s" class="widget %2$s">',
                      'after_widget'  => '</section>',
                      'before_title'  => '<h2 class="widget-title">',
                      'after_title'   => '</h2>',
                    ) );

  register_sidebar( array(
                      'name'          => __( 'Footer 2', 'tenweb-website-builder-theme' ),
                      'id'            => 'sidebar-3',
                      'description'   => __( 'Add widgets here to appear in your footer.', 'tenweb-website-builder-theme' ),
                      'before_widget' => '<section id="%1$s" class="widget %2$s">',
                      'after_widget'  => '</section>',
                      'before_title'  => '<h2 class="widget-title">',
                      'after_title'   => '</h2>',
                    ) );
}
add_action( 'widgets_init', 'tenweb_builder_theme_widgets_init' );


/**
 * Enqueue scripts and styles.
 */
function tenweb_builder_theme_scripts() {
  wp_enqueue_style( 'tenweb-website-builder-open-sanse', 'https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700;800&display=swap', array(), TWBT_VERSION );
  wp_enqueue_script( 'jquery' );

  if ( TWBT_DEV === FALSE ) {
      $is_shop = ( class_exists('woocommerce') ) ? '-wc' : '';
      if ( TWBT_DEBUG === FALSE ) {
          wp_enqueue_style( 'tenweb-website-builder-theme-style', get_template_directory_uri() . '/assets/css/styles' . $is_shop . '.min.css', array(), TWBT_VERSION );
			wp_enqueue_script( 'tenweb-website-builder-theme-script', get_template_directory_uri() . '/assets/js/scripts.min.js', array(), TWBT_VERSION );
		}
		else {
			wp_enqueue_style( 'tenweb-website-builder-theme-style', get_template_directory_uri() . '/assets/css/styles' . $is_shop . '.css', array(), TWBT_VERSION );
			wp_enqueue_script( 'tenweb-website-builder-theme-script', get_template_directory_uri() . '/assets/js/scripts.js', array(), TWBT_VERSION );
		}
    }
	else {
		wp_enqueue_style( 'tenweb-website-builder-theme-style', get_stylesheet_uri() );

		wp_enqueue_script( 'tenweb-website-builder-theme-script', get_template_directory_uri() . '/assets/js/script.js', array(), TWBT_VERSION, true );
		wp_enqueue_script( 'tenweb-website-builder-theme-navigation', get_template_directory_uri() . '/assets/js/navigation.js', array(), TWBT_VERSION, true );
		wp_enqueue_script( 'tenweb-website-builder-theme-skip-link-focus-fix', get_template_directory_uri() . '/assets/js/skip-link-focus-fix.js', array(), TWBT_VERSION, true );
	}

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'tenweb_builder_theme_scripts' );

/**
 * Notice if tenweb-builder plugin doesn't working
 */

function tenweb_general_admin_notice(){
  if ( class_exists('Tenweb_Builder\Builder') ) {
    return;
  }

  $dismiss_url = add_query_arg(array( 'action' => 'twbth_dismiss' ), admin_url('admin-ajax.php'));
  ?>
  <input type="hidden" id="dismiss_url" value="<?php echo $dismiss_url; ?>">
  <?php
  if ( !class_exists('Tenweb_Manager\Manager') ) {
      echo '<div class="twbth notice notice-warning">
               <p>'.  __( "The theme is designed to work with 10Web Builder plugin. Connect your website via 10Web Manager to install it or activate it from the Plugins page.", "tenweb-website-builder-theme" ) .'</p>
            </div>';
  } else {
      $slug = 'tenweb-builder';
      $manager = \Tenweb_Manager\Manager::get_product_by('slug', $slug, 'plugin');

      // get null when the user has not logged in
      if ( empty( $manager ) ) {
          echo '<div class="twbth notice notice-warning" style="display: flex">
                  <p>'.  __( "The theme is designed to work with 10Web Builder plugin. Connect your website via 10Web Manager to install it or activate it from the Plugins page.", "tenweb-website-builder-theme" ) .'</p>
                </div>';
      } else {
          // case when plugin installed but not active
          if ( method_exists( $manager, 'get_state' ) ) {
            echo '<div class="twbth notice notice-warning">
                      <p>'.  __( "The theme is designed to work with 10Web Builder plugin. ", "tenweb-website-builder-theme" ) .'</p>
                      <a class="twbth twbb_activate_button button button-primary" id="activate_plugin" data-id="' . $manager->id . '" data-slug="'.$slug.'">'.__("Activate", "tenweb-builder").'<span class="spinner"></span></a>            
                      <p class="twbth_failed">'.__( "Failed to activate. ", "tenweb-website-builder-theme" ).'</p>
                  </div>';
          } else {  // case when plugin is not installed
            echo '<div class="twbth notice notice-warning">
                      <p>'.  __( "The theme is designed to work with 10Web Builder plugin.", "tenweb-website-builder-theme" ) .'</p>
                      <a class="twbb_install_button button button-primary" id="install_plugin" data-id="' . $manager->id . '" data-slug="'.$slug.'">'.__("Install", "tenweb-builder").'<span class="spinner"></span></a>            
                      <p class="twbth_failed">'.__( "Failed to install. ", "tenweb-website-builder-theme" ).'</p>
                    </div>';
          }
      }
  }
  ?>
  <style>
    .twbth {
      display: flex;
      position: relative;
    }

    .twbth a.button.button-primary {
      margin: 3px 0 0 10px;
    }
    .twbth a .spinner {
      display: none;
      background: url(<?php echo get_template_directory_uri(); ?>/images/spinner.gif)  no-repeat;
      float: none;
      width: 15px;
      height: 15px;
      background-size: contain;
      margin: -5px 0 -3px 8px;
    }

    .twbth .dashicons.dashicons-dismiss {
      position: absolute;
      right: 10px;
      top:10px;
      font-size: 17px;
      cursor: pointer;
    }
    .twbth .dashicons.dashicons-dismiss:hover {
      color:#f00;
    }

    .twbth_failed {
      display: none;
      color:#f00;
    }
  </style>
<?php
}

add_action('admin_notices', 'tenweb_general_admin_notice');

function tenweb_enqueue_my_scripts() {
  wp_enqueue_script('jquery');
  wp_register_script('my_script', get_template_directory_uri() . '/assets/js/notify-builder-ajax.js', FALSE, '1.0.0');
  wp_enqueue_script('my_script');
  if ( class_exists('Tenweb_Manager\Manager') ) {
    $rest_route = add_query_arg(array(
                                  'rest_route' => '/' . TENWEB_REST_NAMESPACE . '/action'
                                ), get_home_url() . "/");

    wp_localize_script('my_script', 'twbth', array(
      'ajaxurl' => admin_url('admin-ajax.php'),
      'ajaxnonce' => wp_create_nonce('wp_rest'),
      'action_endpoint' => $rest_route,
    ));
  }

}

add_action( 'admin_enqueue_scripts', 'tenweb_enqueue_my_scripts' );

function tenweb_builder_comment( $comment, $args, $depth ) {
  switch ( $comment->comment_type ) :
    case 'pingback' :
    case 'trackback' :
      ?>
        <li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
        <p><?php _e( 'Pingback:', 'theme_10web' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( __( '(Edit)', 'theme_10web' ), '<span class="edit-link">', '</span>' ); ?></p>
      <?php
      break;
    default : ?>
    <li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
        <article id="comment-<?php comment_ID(); ?>" class="comment">
            <header class="comment-meta comment-author vcard clear">
              <div class="avatar_container">
                <?php echo get_avatar( $comment, 50 ); ?>
              </div>
              <div class="comment_info">
                  <?php
                  if(get_comment_author_url( $comment->comment_ID)){
                    printf( '<div class="author"><a href="%1$s" rel="external nofollow" class="url" target="_blank"><span>%2$s</span></a></div>',
                      get_comment_author_url( $comment->comment_ID),
                      get_comment_author($comment->comment_ID)
                    );
                  } else {
                    printf( '<div class="author">%1$s</div>',
                      get_comment_author($comment->comment_ID)
                    );
                  }
                  printf( '<time datetime="%1$s">%2$s</time>',
                    get_comment_time( 'c' ) ,
                    /* translators: 1: date, 2: time */
                    sprintf( __( '%1$s', 'theme_10web' ), get_comment_date() )
                  );
                  ?>
              </div>
            </header><!-- .comment-meta -->


            <section class="comment-content comment">
              <?php if ( '0' == $comment->comment_approved ) : ?>
                  <p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'theme_10web' ); ?></p>
              <?php endif; ?>
                <div id="tenweb-comment<?php comment_ID(); ?>">
                  <?php comment_text(); ?>
                </div>
            </section><!-- .comment-content -->
            <div class="reply">
            <?php if($comment->get_children()): ?>
                <span class="view_all_comments show">View all <?php echo count($comment->get_children()); ?> replies</span>
            <?php endif; ?>
                <div class="reply_div">
                  <?php comment_reply_link( array_merge( $args, array( 'reply_text' => __( 'Reply', 'theme_10web' ), 'after' => '', 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>

                </div>
            </div>
        </article><!-- #comment-## -->
      <?php
      break;
  endswitch;
}


/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
if ( class_exists('woocommerce') && defined('ELEMENTOR_VERSION') ) {
    require 'woo/cart_checkout.php';
    new CartCheckout();
}