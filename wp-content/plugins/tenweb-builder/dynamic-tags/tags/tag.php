<?php
namespace Tenweb_Builder\DynamicTags\Tags;

use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Core\DynamicTags\Tag;

if(!defined('ABSPATH')) exit; // Exit if accessed directly

abstract class Tenweb_Tag extends Tag {

  const TENWEB_GROUP = 'tenweb';
  const TENWEB_CATEGORY = 'tenweb';

  public function get_group() {
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

  public function get_categories() {
    return [ self::TENWEB_CATEGORY ];
  }
}

abstract class Tenweb_DataTag extends Data_Tag {

  const TENWEB_GROUP = 'tenweb';
  const TENWEB_CATEGORY = 'tenweb';

  public function get_group() {
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

  public function get_categories() {
    return [ self::TENWEB_CATEGORY ];
  }
}