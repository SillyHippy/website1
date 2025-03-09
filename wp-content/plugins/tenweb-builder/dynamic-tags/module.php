<?php
namespace Tenweb_Builder\DynamicTags;

use Elementor\Modules\DynamicTags\Module as TagsModule;

if(!defined('ABSPATH')) exit; // Exit if accessed directly

class Module extends TagsModule {

  const TENWEB_GROUP = 'tenweb';

  public function __construct() {
    spl_autoload_register( [ $this, 'autoload' ] );
    parent::__construct();

    // ACF 5 and up
    if ( class_exists( '\acf' ) && function_exists( 'acf_get_field_groups' ) ) {
      $this->add_component( 'acf', new ACF\Module() );
    }
//phpcs:ignore Squiz.PHP.CommentedOutCode.Found
//    if ( function_exists( 'wpcf_admin_fields_get_groups' ) ) {
//      $this->add_component( 'toolset', new Toolset\Module() );
//    }

    if ( function_exists( 'pods' ) ) {
      $this->add_component( 'pods', new Pods\Module() );
    }
  }

  public function get_name() {
    return 'tenweb-tags';
  }

  public function get_tag_classes_names() {
    return [
      'Post_Title',
      'Archive_Title',
      'Post_URL',
      'Site_Title',
      'Site_URL',
      'Featured_Image',
      'Site_Logo',
    ];
  }


  public function get_groups() {
    $tag_title = '10Web Tags';
    if ( TENWEB_WHITE_LABEL ) {
      $tag_title = 'Tags';
    }
    return [
      self::TENWEB_GROUP => [
        'title' => __( $tag_title, 'tenweb-builder' ),
      ],
    ];
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
      $filename = TWBB_DIR . '/dynamic-tags/' . $filename . '.php';
      if ( is_readable( $filename ) ) {
        include( $filename );//phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingVariable
      }
    }
  }
}
