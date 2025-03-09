<?php
/**
 * Version: 3.4.1
 */
namespace Tenweb_Builder\ElementorPro;

use Elementor\Utils;

class ElementorPro {

  protected static $instance = null;

  public static function get_instance() {
    if(self::$instance === null) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  /**
   * ProFeatures constructor.
   */
  public function __construct() {
    spl_autoload_register( [ $this, 'autoload' ] );
    Modules\Forms\Module::instance();
    Modules\Gallery\Module::instance();

    if ( !defined( 'ELEMENTOR_PRO_VERSION' ) ) {
      Modules\GlobalWidget\Module::instance();
      Modules\Library\Module::instance();
      Modules\QueryControl\Module::instance();
      Modules\AssetsManager\Module::instance();
      Modules\CustomCss\Module::instance();
      Modules\MotionFX\Module::instance();
    }

    add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_scripts' ] );
    add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'editor_enqueue_scripts' ] );
    add_action( 'elementor/frontend/after_enqueue_styles', [ $this, 'enqueue_frontend_styles' ] );
    add_action( 'elementor/frontend/before_enqueue_scripts', [ $this, 'enqueue_frontend_scripts' ] );
    add_action( 'elementor/preview/enqueue_scripts', [ $this, 'register_preview_scripts' ] );
    add_action( 'elementor/preview/enqueue_styles', [ $this, 'preview_enqueue_styles' ] );
    add_filter( 'tenweb_builder_pro_features_settings', [ $this, 'localize_settings' ] );
  }

  public function localize_settings() {
      return [];
  }

  public function preview_enqueue_styles() {
      /* 10web */
      if ( TWBB_DEV === FALSE ) {
          wp_enqueue_style(
              TWBB_PREFIX . '-tenweb-preview',
              TWBB_URL . '/pro-features/assets/css/preview.min.css',
              [],
              TWBB_VERSION
          );
      } else {
          wp_enqueue_style(
              TWBB_PREFIX . '-tenweb-preview',
              TWBB_URL . '/pro-features/assets/css/preview.css',
              [],
              TWBB_VERSION
          );
      }
  }

  public function register_preview_scripts() {
      if ( TWBB_DEV === FALSE ) {
          wp_enqueue_script(
              TWBB_PREFIX . 'tenweb-preview',
              TWBB_URL . '/pro-features/assets/js/preview.min.js',
              [
                  'wp-i18n',
                  'elementor-frontend',
              ],
              TWBB_VERSION,
              true
          );
      } else {
          wp_enqueue_script(
              TWBB_PREFIX . 'tenweb-preview',
              TWBB_URL . '/pro-features/assets/js/preview.js',
              [
                  'wp-i18n',
                  'elementor-frontend',
              ],
              TWBB_VERSION,
              true
          );
      }
  }

  public function editor_enqueue_scripts() {
      if ( TWBB_DEV === FALSE ) {
          wp_enqueue_script(
              TWBB_PREFIX . '-pro-features',
              TWBB_URL . '/pro-features/assets/js/editor.min.js',
              array('backbone-marionette',
                  'elementor-common',
                  'elementor-editor-modules',
                  'elementor-editor-document'
              ),
              TWBB_VERSION,
              TRUE
          );

          wp_enqueue_style(
              TWBB_PREFIX . '-pro-features',
              TWBB_URL . '/pro-features/assets/css/editor.min.css',
              array(),
              TWBB_VERSION
          );
      } else {
          wp_enqueue_script(
              TWBB_PREFIX . '-pro-features',
              TWBB_URL . '/pro-features/assets/js/editor.js',
              array('backbone-marionette',
                  'elementor-common',
                  'elementor-editor-modules',
                  'elementor-editor-document'
              ),
              TWBB_VERSION,
              TRUE
          );

          wp_enqueue_style(
              TWBB_PREFIX . '-pro-features',
              TWBB_URL . '/pro-features/assets/css/editor.css',
              array(),
              TWBB_VERSION
          );
      }

    wp_localize_script(
      'twbb-editor-scripts',
      'elementorTenwebEditorConfig',
      $this->add_templates_localize_data()
    );
  }

  public function enqueue_admin_scripts() {
      if ( TWBB_DEV === FALSE ) {
          wp_enqueue_script(
              TWBB_PREFIX . '-tenweb-admin-script',
              TWBB_URL . '/pro-features/assets/js/admin.min.js',
              ['elementor-common'],
              TWBB_VERSION,
              TRUE
          );
          wp_enqueue_style(
              TWBB_PREFIX . '-tenweb-admin-style',
              TWBB_URL . '/pro-features/assets/css/admin.min.css',
              [],
              TWBB_VERSION
          );
      } else {
          wp_enqueue_script(TWBB_PREFIX . '-tenweb-admin-script', TWBB_URL . '/pro-features/assets/js/admin.js', ['elementor-common'], TWBB_VERSION, TRUE);
          wp_enqueue_style(TWBB_PREFIX . '-tenweb-admin-style', TWBB_URL . '/pro-features/assets/css/admin.css', [], TWBB_VERSION);
      }
	  Utils::print_js_config(
		  TWBB_PREFIX . '-tenweb-admin-script',
		  'ElementorTenwebConfig',
		  $this->add_admin_localize_data()
	  );
  }

  public function add_admin_localize_data() {
     return apply_filters( 'tenweb_builder_pro_features_settings', array() );
  }

  public function enqueue_frontend_styles() {
      if ( TWBB_DEV === FALSE ) {
          wp_enqueue_style(TWBB_PREFIX . '-pro-features', TWBB_URL . '/pro-features/assets/css/frontend.min.css', array(), TWBB_VERSION);
      } else {
          wp_enqueue_style(TWBB_PREFIX . '-pro-features', TWBB_URL . '/pro-features/assets/css/frontend.css', array(), TWBB_VERSION);
      }
  }

  public function enqueue_frontend_scripts() {
      if ( TWBB_DEV === FALSE ) {
          if ( TWBB_DEBUG === TRUE ) {
              wp_enqueue_script(
                  TWBB_PREFIX . '-pro-features-frontend-script',
                  TWBB_URL . '/pro-features/assets/js/concated_frontend.js',
                  ['elementor-frontend-modules','elementor-frontend'],
                  TWBB_VERSION,
                  TRUE
              );
          } else {
              wp_enqueue_script(
                  TWBB_PREFIX . '-pro-features-frontend-script',
                  TWBB_URL . '/pro-features/assets/js/concated_frontend.min.js',
                  ['elementor-frontend-modules','elementor-frontend'],
                  TWBB_VERSION,
                  TRUE
              );
          }
      } else {
          wp_register_script(TWBB_PREFIX . '-pro-features-webpack-runtime', TWBB_URL . '/pro-features/assets/js/webpack-pro.runtime.js', [], TWBB_VERSION, true);
          wp_enqueue_script(TWBB_PREFIX . '-pro-features-frontend-script', TWBB_URL . '/pro-features/assets/js/frontend.js', [TWBB_PREFIX . '-pro-features-webpack-runtime', 'elementor-frontend-modules'], TWBB_VERSION, TRUE);
          wp_enqueue_script(TWBB_PREFIX . '-pro-features-preloaded-elements-handlers', TWBB_URL . '/pro-features/assets/js/preloaded-elements-handlers.js', ['elementor-frontend'], TWBB_VERSION, true);
      }

    $locale_settings = [
      'ajaxurl' => admin_url( 'admin-ajax.php' ),
      'nonce' => wp_create_nonce( 'elementor-tenweb-frontend' ),
      'urls' => [
        'assets' => TWBB_URL . '/pro-features/assets/',
      ],
    ];

    $locale_settings = apply_filters( 'elementor_tenweb/frontend/localize_settings', $locale_settings );

    wp_localize_script(
      TWBB_PREFIX . '-pro-features-frontend-script',
      'ElementorTenwebFrontendConfig',
      $locale_settings
    );
  }

  public function add_templates_localize_data() {
    return apply_filters( 'tenweb_builder_settings', array() );
  }

  public function autoload( $class ) {
    if ( 0 !== strpos( $class, __NAMESPACE__ ) ) {
      return;
    }

    if ( ! class_exists( $class ) ) {
      $filename = strtolower(
        preg_replace(
          [ '/^' . __NAMESPACE__ . '\\\/', '/([a-z])([A-Z])/', '/_/', '/\\\/' ],
          [ '', '$1-$2', '-', DIRECTORY_SEPARATOR ],
          str_replace(__NAMESPACE__, '', $class)
        )
      );
      $filename = TWBB_DIR . '/pro-features/' . $filename . '.php';
      if ( is_readable( $filename ) ) {
        include( $filename );//phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingVariable
      }
    }
  }
}
