<?php

namespace Tenweb_Builder\Widgets\Woocommerce\Products;

use Tenweb_Builder\DynamicTags\Module as TagsModule;
use Tenweb_Builder\Classes\Woocommerce\Woocommerce;
use Elementor\Widget_Heading;

if ( !defined('ABSPATH') ) {
  exit;
} // Exit if accessed directly

class Product_Title extends Widget_Heading {

  public function get_name() {
    return 'twbb_woocommerce-product-title';
  }

  public function get_title() {
    return __('Product Title', 'tenweb-builder');
  }

  public function get_icon() {
    return 'twbb-product_title twbb-widget-icon';
  }

  public function get_categories() {
    return [ Woocommerce::WOOCOMMERCE_BUILDER_GROUP ];
  }

  public function get_keywords() {
    return [ 'woocommerce', 'shop', 'store', 'product', 'title', 'heading' ];
  }

  protected function register_controls() {

    parent::register_controls();
    $this->update_control('title', [
      'dynamic' => [
        'default' => \Elementor\Plugin::instance()->dynamic_tags->tag_data_to_tag_text(NULL, 'tenweb-tag-post-title'),
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
    $this->update_control('header_size', [
      'default' => 'h1',
    ]);
  }

  public function get_common_args() {
    return [
      '_css_classes' => [
        'default' => 'entry-title',
      ],
    ];
  }

  protected function content_template() {
    if ( Woocommerce::is_template_page() ) {
      $title = __('Product Title', 'tenweb-builder');
      if ( Woocommerce::get_preview_product() ) {
        $preview_product = Woocommerce::get_preview_product();
        $title = $preview_product->post_title;
      }
      ?>
      <#
      var defaultObj = new Object();
      defaultObj.before = "";
      defaultObj.after = "";
      defaultObj.fallback = "";

      var dynamic =  settings.__dynamic__;
      dynamic = decodeURIComponent(dynamic.title);
      dynamic = dynamic.split('settings="');
      var dynamic_settings = (dynamic[1]) ? dynamic[1].replace(/\"]/,'') : defaultObj;
      dynamic_settings = JSON.parse(dynamic_settings);
      settings.title = ( (dynamic_settings.before) ? dynamic_settings.before : '') + '<?php echo esc_html(addslashes($title)); ?>' + ( (dynamic_settings.after) ? dynamic_settings.after : '' );
      #>
      <?php
    }
    parent::content_template();
  }

  public function get_settings_for_display( $setting_key = NULL ) {
    $settings = parent::get_settings_for_display($setting_key);
    $dynamic_settings = $this->_dynamic_settings($settings);
    if ( Woocommerce::is_template_page() ) {
      $title = __('Product Title', 'tenweb-builder');
      if ( Woocommerce::get_preview_product() ) {
        $preview_product = Woocommerce::get_preview_product();
        $title = $preview_product->post_title;
      }
      $title = ( !empty($dynamic_settings->before) ? $dynamic_settings->before : '' ) . '' . $title . '' . ( !empty($dynamic_settings->after) ? $dynamic_settings->after : '');
      $settings['title'] = $title;
    }

    return $settings;
  }

  protected function get_html_wrapper_class() {
    return parent::get_html_wrapper_class() . ' elementor-page-title tenweb-page-title elementor-widget-' . parent::get_name();
  }

  private function _dynamic_settings( $settings = [] ) {
    $defaultObj = (object) [ 'before' => '', 'after' => '', 'fallback' ];
    if (isset($settings['__dynamic__']) && isset($settings['__dynamic__']['title'])) {
      $dynamic_settings = explode('settings="', urldecode($settings['__dynamic__']['title']));
    }
    $dynamic_settings = !empty($dynamic_settings[1]) ? str_replace(array( '"]' ), array( '' ), $dynamic_settings[1]) : [];
    $dynamic_settings = !empty($dynamic_settings) ? json_decode($dynamic_settings) : $defaultObj;

    return $dynamic_settings;
  }
}

\Elementor\Plugin::instance()->widgets_manager->register(new Product_Title());
