<?php

namespace Tenweb_Builder;

use Tenweb_Builder\DynamicTags\Module as TagsModule;
use Elementor\Widget_Heading;

if(!defined('ABSPATH')) exit; // Exit if accessed directly

class Site_Title extends Widget_Heading {

  public function get_name() {
    return 'tenweb-site-title';
  }

  public function get_title() {
    return __( 'Site Title', 'tenweb-builder' );
  }

  public function get_icon() {
    return 'twbb-site-title twbb-widget-icon';
  }

  public function get_categories() {
    return [ 'tenweb-builder-widgets' ];
  }

  protected function register_controls() {
    parent::register_controls();

    $this->update_control(
      'title',
      [
        'dynamic' => [
          'default' => \Elementor\Plugin::instance()->dynamic_tags->tag_data_to_tag_text( null, 'tenweb-tag-site-title' ),
        ],
      ],
      [
        'recursive' => true,
      ]
    );

    $this->update_control(
      'link',
      [
        'dynamic' => [
          'default' => \Elementor\Plugin::instance()->dynamic_tags->tag_data_to_tag_text( null, 'tenweb-tag-site-url' ),
        ],
      ],
      [
        'recursive' => true,
      ]
    );
  }

  protected function get_html_wrapper_class() {
    return parent::get_html_wrapper_class() . ' elementor-widget-' . parent::get_name();
  }
}

\Elementor\Plugin::instance()->widgets_manager->register(new Site_Title());
