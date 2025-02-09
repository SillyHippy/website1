<?php

namespace Tenweb_Builder;

use Tenweb_Builder\DynamicTags\Module as TagsModule;
use Elementor\Widget_Image;

if(!defined('ABSPATH')) exit; // Exit if accessed directly

class Site_Logo extends Widget_Image {

  public function get_name() {
    return 'tenweb-site-logo';
  }

  public function get_title() {
    return __( 'Site Logo', 'tenweb-builder' );
  }

  public function get_icon() {
    return 'twbb-site-logo twbb-widget-icon';
  }

  public function get_categories() {
    return [ 'tenweb-builder-widgets' ];
  }

  protected function register_controls() {
    parent::register_controls();

    $this->update_control(
      'image',
      [
        'dynamic' => [
          'default' => \Elementor\Plugin::instance()->dynamic_tags->tag_data_to_tag_text( null, 'tenweb-tag-site-logo' ),
        ],
        'label' => __('Choose Image <div class="twbb-im-description"> The logo image can be set from Appearance > Customize > Site Identity section.</div>', 'tenweb-builder '),
      ],
      [
        'recursive' => true,
      ]
    );

    $this->update_control(
      'image_size',
      [
        'default' => 'full',
      ]
    );

    $this->update_control(
      'link_to',
      [
        'default' => 'custom',
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

    $this->remove_control( 'caption_source' );
  }

  protected function get_html_wrapper_class() {
    return parent::get_html_wrapper_class() . ' elementor-widget-' . parent::get_name();
  }
}

\Elementor\Plugin::instance()->widgets_manager->register(new Site_Logo());
