<?php
namespace Tenweb_Builder\DynamicTags\Tags;

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Data_Tag;
use Tenweb_Builder\DynamicTags\Module;

if(!defined('ABSPATH')) exit; // Exit if accessed directly

class Featured_Image extends Data_Tag {

  public function get_name() {
    return 'tenweb-tag-featured-image';
  }

  public function get_title() {
    return __( 'Featured Image', 'tenweb-builder' );
  }

  public function get_group() {
    return Module::TENWEB_GROUP;
  }

  public function get_categories() {
    return [ Module::IMAGE_CATEGORY ];
  }

  public function get_value( array $options = [] ) {
    $thumbnail_id = get_post_thumbnail_id();

    if ( $thumbnail_id ) {
      $image_data = [
        'id' => $thumbnail_id,
        'url' => wp_get_attachment_image_src( $thumbnail_id, 'full' )[0],
      ];
    } else {
      $image_data = $this->get_settings( 'fallback' );
    }

    return $image_data;
  }

  protected function register_controls() {
    $this->add_control(
      'fallback',
      [
        'label' => __( 'Fallback', 'tenweb-builder' ),
        'type' => Controls_Manager::MEDIA,
      ]
    );
  }
}
