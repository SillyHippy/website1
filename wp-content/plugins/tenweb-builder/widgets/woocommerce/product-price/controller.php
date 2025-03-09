<?php

namespace Tenweb_Builder\Widgets\Woocommerce\Products;

use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Tenweb_Builder\Classes\Woocommerce\Woocommerce;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( !defined('ABSPATH') ) {
  exit; // Exit if accessed directly
}

class Product_Price extends Widget_Base {

  public function get_name() {
    return 'twbb_woocommerce-product-price';
  }

  public function get_title() {
    return __('Product Price', 'tenweb-builder');
  }

  public function get_icon() {
    return 'twbb-product_prices twbb-widget-icon';
  }

  public function get_categories() {
    return [ Woocommerce::WOOCOMMERCE_BUILDER_GROUP ];
  }

  public function get_keywords() {
    return [ 'woocommerce', 'shop', 'store', 'product', 'price', 'sale' ];
  }

  protected function register_controls() {
    if ( !Woocommerce::get_preview_product() ) {
      $this->start_controls_section('general', [
        'label' => $this->get_title(),
      ]);
      $this->add_control('msg', [
        'type' => \Elementor\Controls_Manager::RAW_HTML,
        'raw' => Woocommerce::add_new_product_link(),
      ]);
      $this->end_controls_section();
    }
    else {
      $this->start_controls_section('section_price_style', [
        'label' => __('Price', 'tenweb-builder'),
        'tab' => Controls_Manager::TAB_STYLE,
      ]);
      $this->add_control('wc_style_warning', [
        'type' => Controls_Manager::RAW_HTML,
        'raw' => __('The style of this widget is often affected by your theme and plugins. If you experience any such issue, try to switch to a basic theme and deactivate related plugins.', 'tenweb-builder'),
        'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
      ]);
      $this->add_responsive_control('text_align', [
        'label' => __('Alignment', 'tenweb-builder'),
        'type' => Controls_Manager::CHOOSE,
        'options' => [
          'left' => [
            'title' => __('Left', 'tenweb-builder'),
            'icon' => 'fa fa-align-left',
          ],
          'center' => [
            'title' => __('Center', 'tenweb-builder'),
            'icon' => 'fa fa-align-center',
          ],
          'right' => [
            'title' => __('Right', 'tenweb-builder'),
            'icon' => 'fa fa-align-right',
          ],
        ],
        'selectors' => [
          '{{WRAPPER}}' => 'text-align: {{VALUE}}',
        ],
      ]);
      $this->add_control('price_color', [
        'label' => __('Color', 'tenweb-builder'),
        'type' => Controls_Manager::COLOR,
          'global' => [
              'default' => Global_Colors::COLOR_PRIMARY,
          ],
        'selectors' => [
          '{{WRAPPER}} .price' => 'color: {{VALUE}}',
        ],
      ]);
      $this->add_group_control(Group_Control_Typography::get_type(), [
        'name' => 'typography',
          'global' => [
              'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
          ],
        'selector' => '{{WRAPPER}} .price',
      ]);
      $this->add_control('sale_heading', [
        'label' => __('Sale Price', 'tenweb-builder'),
        'type' => Controls_Manager::HEADING,
        'separator' => 'before',
      ]);
      $this->add_control('sale_price_color', [
        'label' => __('Color', 'tenweb-builder'),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .price ins' => 'color: {{VALUE}};',
        ],
      ]);
      $this->add_group_control(Group_Control_Typography::get_type(), [
        'name' => 'sale_price_typography',
        'selector' => '{{WRAPPER}} .price ins',
      ]);
      $this->add_control('price_block', [
        'label' => __('Stacked', 'tenweb-builder'),
        'type' => Controls_Manager::SWITCHER,
        'return_value' => 'yes',
        'prefix_class' => 'elementor-product-price-block-',
      ]);
      $this->add_responsive_control('sale_price_spacing', [
        'label' => __('Spacing', 'tenweb-builder'),
        'type' => Controls_Manager::SLIDER,
        'size_units' => [ 'px', 'em' ],
        'range' => [
          'em' => [
            'min' => 0,
            'max' => 5,
            'step' => 0.1,
          ],
        ],
        'selectors' => [
          'body:not(.rtl) {{WRAPPER}}:not(.elementor-product-price-block-yes) del' => 'margin-right: {{SIZE}}{{UNIT}}',
          'body.rtl {{WRAPPER}}:not(.elementor-product-price-block-yes) del' => 'margin-left: {{SIZE}}{{UNIT}}',
          '{{WRAPPER}}.elementor-product-price-block-yes del' => 'margin-bottom: {{SIZE}}{{UNIT}}',
        ],
      ]);
      $this->end_controls_section();
    }
  }

  protected function render() {
    global $product;
    if ( Woocommerce::is_template_page() && Woocommerce::get_preview_product() ) {
      $preview_product = Woocommerce::get_preview_product();
      $product = wc_get_product($preview_product->ID);
    }
    else {
      $product = wc_get_product();
    }
    if ( empty($product) ) {
      return;
    }
    wc_get_template('/single-product/price.php');
  }

  public function render_plain_content() {
  }
}

\Elementor\Plugin::instance()->widgets_manager->register(new Product_Price());
