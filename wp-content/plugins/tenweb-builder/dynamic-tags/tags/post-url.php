<?php
namespace Tenweb_Builder\DynamicTags\Tags;

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Data_Tag;
use Tenweb_Builder\DynamicTags\Module;

if(!defined('ABSPATH')) exit; // Exit if accessed directly

class Post_URL extends Data_Tag {

  public function get_name() {
    return 'tenweb-tag-post-url';
  }

  public function get_title() {
    return __( 'Current URL', 'tenweb-builder' );
  }

  public function get_group() {
    return Module::TENWEB_GROUP;
  }

  public function get_categories() {
    return [ Module::URL_CATEGORY ];
  }

  public function get_value( array $options = [] ) {
    return 'yes' === $this->get_settings( 'enabled' ) ? get_permalink() : '';
  }

  protected function register_controls() {
    $this->add_control(
      'enabled',
      [
        'label' => __('Enabled', 'tenweb-builder'),
        'type' => Controls_Manager::SWITCHER,
        'label_on' => __( 'Yes', 'tenweb-builder' ),
        'label_off' => __( 'No', 'tenweb-builder' ),
        'default' => 'yes',
      ]
    );
  }
}
