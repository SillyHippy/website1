<?php

namespace Tenweb_Builder\Widgets\Facebook;
include_once(TWBB_DIR . '/widgets/facebook/classes/facebook-sdk-manager.php');

use Tenweb_Builder\Widgets\Facebook\Classes\Facebook_SDK_Manager;
use Elementor\Core\Base\Module;

if ( !defined('ABSPATH') ) {
  exit; // Exit if accessed directly
}

/**
 * Class Module
 *
 * @package Tenweb_Builder\Widgets\Facebook
 */
class FB_Module extends Module {

  const URL_TYPE_CUSTOM = 'custom';
  const URL_FORMAT_PLAIN = 'plain';
  const URL_FORMAT_PRETTY = 'pretty';
  const URL_TYPE_CURRENT_PAGE = 'current_page';
  protected static $instance = NULL;

  public function get_name() {
    return 'social';
  }

  /**
   * @return FB_Module
   */
  public static function get_instance() {
    if ( self::$instance === NULL ) {
      self::$instance = new self();
    }

    return self::$instance;
  }
  /**
   * Get widgets.
   *
   * @return array
   */
  public function get_widgets() {
    return [
      'page',
      'button',
      'comments',
      'embed'
    ];
  }

  /**
   * Module constructor.
   */
  public function __construct() {
    $this->get_actions();
    $this->add_component('facebook_sdk', new Facebook_SDK_Manager());
  }

  /**
   * Get actions.
   */
  public function get_actions() {
    add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );
    add_action( 'twbb_after_enqueue_scripts', [ $this, 'enqueue_frontend_scripts' ] );
  }

  /**
   * Register widgets.
   */
  public function register_widgets() {
    foreach ( $this->get_widgets() as $widget ) {
      $file = TWBB_DIR . '/widgets/facebook/widgets/' . $widget . '/controller.php';
      if ( is_file($file) ) {
        require_once $file; //phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingVariable
      }
    }
  }

  /**
   * Localize settings.
   * @return mixed
   */
  public function localize_settings() {
    $settings = [];
    $settings['facebook_sdk'] = [
      'lang' => Facebook_SDK_Manager::get_lang(),
      'app_id' => Facebook_SDK_Manager::get_app_id(),
    ];

    return $settings;
  }

  /**
   * Enqueue frontend scripts.
   */
  public function enqueue_frontend_scripts( $formtend_handle ) {
    wp_localize_script($formtend_handle, 'TWBBFrontendConfig', $this->localize_settings() );
    if ( TWBB_DEV === true ) {
      $modules = twbb_get_group_widgets();
      foreach ( $modules as $module => $data ) {
        if ( !empty( $data[ 'scripts' ] ) ) {
          foreach ( $data[ 'scripts' ] as $handle => $scripts ) {
            wp_enqueue_script( 'twbb-' . $module . '-' . $handle . '-scripts', $scripts[ 'src' ], $scripts[ 'deps' ], TWBB_VERSION );
          }
        }
	    if ( !empty( $data[ 'styles' ] ) ) {
	      foreach ( $data[ 'styles' ] as $handle => $styles ) {
		      wp_enqueue_style( 'twbb-' . $module . '-' . $handle . '-styles', $styles[ 'src' ], TWBB_VERSION, true );
	      }
	    }
      }
    }
  }
}

\Tenweb_Builder\Widgets\Facebook\FB_Module::get_instance();
