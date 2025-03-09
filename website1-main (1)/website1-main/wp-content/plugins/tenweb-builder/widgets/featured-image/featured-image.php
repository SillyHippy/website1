<?php

namespace Tenweb_Builder;

use Tenweb_Builder\DynamicTags\Module as TagsModule;
use Elementor\Widget_Image;

if ( !defined('ABSPATH') ) {
  exit;
} // Exit if accessed directly

class Featured_Image extends Widget_Image {

  public function get_name() {
    return Builder::$prefix . '_featured-image';
  }

  public function get_title() {
    return __('Featured Image', 'tenweb-builder');
  }

  public function get_icon() {
    return 'twbb-featured-image twbb-widget-icon';
  }

  public function get_categories() {
    return [ 'tenweb-builder-widgets' ];
  }

  protected function register_controls() {
    parent::register_controls();
    $this->update_control('image', [
      'dynamic' => [
        'default' => \Elementor\Plugin::instance()->dynamic_tags->tag_data_to_tag_text(NULL, 'tenweb-tag-featured-image'),
      ],
    ], [
                            'recursive' => TRUE,
                          ]);
    $this->update_control('link', [
      'dynamic' => [
        'default' => \Elementor\Plugin::instance()->dynamic_tags->tag_data_to_tag_text(NULL, 'tenweb-tag-post-url'),
      ],
    ], [
                            'recursive' => TRUE,
                          ]);
    $this->update_control('link_to', [
      'options' => [
        'none' => __('None', 'tenweb-builder'),
        'file' => __('Media File', 'tenweb-builder'),
        'custom' => __('Current URL', 'tenweb-builder'),
      ],
    ]);
  }

  public function get_settings_for_display( $setting_key = NULL ) {
    $settings = parent::get_settings_for_display($setting_key);
    if (is_array($settings) && empty($settings['image']['url']) ) {
      $settings['image']['url'] = TWBB_URL . '/assets/images/default-featured-image.svg';
    }

    return $settings;
  }
//phpcs:disable
  protected function content_template() {
    ?>
    <#
    if ( settings.image.url == '' ) {
    settings.image.url = "<?php echo TWBB_URL . '/assets/images/default-featured-image.svg'; ?>"
    }
    #>
    <?php
    parent::content_template();
  }
//phpcs:enable
  protected function get_html_wrapper_class() {
    return parent::get_html_wrapper_class() . ' elementor-widget-' . parent::get_name();
  }
}

\Elementor\Plugin::instance()->widgets_manager->register(new Featured_Image());
