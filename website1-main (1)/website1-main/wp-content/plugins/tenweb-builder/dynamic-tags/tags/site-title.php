<?php
namespace Tenweb_Builder\DynamicTags\Tags;

use Elementor\Core\DynamicTags\Tag;
use Tenweb_Builder\DynamicTags\Module;

if(!defined('ABSPATH')) exit; // Exit if accessed directly

class Site_Title extends Tag {

  const TENWEB_GROUP = 'tenweb';

  public function get_name() {
    return 'tenweb-tag-site-title';
  }

  public function get_title() {
    return __( 'Site Title', 'tenweb-builder' );
  }

  public function get_group() {
    return Module::TENWEB_GROUP;
  }

  public function get_categories() {
    return [ Module::TEXT_CATEGORY ];
  }

  public function render() {
    echo wp_kses_post( get_bloginfo() );
  }
}
