<?php

namespace Tenweb_Builder\Widgets\Woocommerce\Products;

use Tenweb_Builder\Classes\Woocommerce\Woocommerce;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( !defined('ABSPATH') ) {
  exit; // Exit if accessed directly
}

class Product_Rating extends Widget_Base {

  public function get_name() {
    return 'twbb_woocommerce-product-rating';
  }

  public function get_title() {
    return __('Product Rating', 'tenweb-builder');
  }

  public function get_icon() {
    return 'twbb-product_rating twbb-widget-icon';
  }

  public function get_categories() {
    return [ Woocommerce::WOOCOMMERCE_BUILDER_GROUP ];
  }

  public function get_keywords() {
    return [ 'woocommerce', 'shop', 'store', 'product', 'rating', 'review', 'comments', 'stars' ];
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
      $this->start_controls_section('section_product_rating_style', [
        'label' => __('Style', 'tenweb-builder'),
        'tab' => Controls_Manager::TAB_STYLE,
      ]);
      $this->add_control('wc_style_warning', [
        'type' => Controls_Manager::RAW_HTML,
        'raw' => __('The style of this widget is often affected by your theme and plugins. If you experience any such issue, try to switch to a basic theme and deactivate related plugins.', 'tenweb-builder'),
        'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
      ]);
      $this->add_control('star_color', [
        'label' => __('Star Color', 'tenweb-builder'),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .star-rating' => 'color: {{VALUE}}',
        ],
      ]);
      $this->add_control('empty_star_color', [
        'label' => __('Empty Star Color', 'tenweb-builder'),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .star-rating::before' => 'color: {{VALUE}}',
        ],
      ]);
      $this->add_control('link_color', [
        'label' => __('Link Color', 'tenweb-builder'),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .woocommerce-review-link' => 'color: {{VALUE}}',
        ],
      ]);
      $this->add_group_control(Group_Control_Typography::get_type(), [
        'name' => 'text_typography',
        'selector' => '{{WRAPPER}} .woocommerce-review-link',
      ]);
      $this->add_control('star_size', [
        'label' => __('Star Size', 'tenweb-builder'),
        'type' => Controls_Manager::SLIDER,
        'default' => [
          'unit' => 'em',
        ],
        'range' => [
          'em' => [
            'min' => 0,
            'max' => 4,
            'step' => 0.1,
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .star-rating' => 'font-size: {{SIZE}}{{UNIT}}',
        ],
      ]);
      $this->add_control('space_between', [
        'label' => __('Space Between', 'tenweb-builder'),
        'type' => Controls_Manager::SLIDER,
        'size_units' => [ 'px', 'em' ],
        'default' => [
          'unit' => 'em',
        ],
        'range' => [
          'em' => [
            'min' => 0,
            'max' => 4,
            'step' => 0.1,
          ],
          'px' => [
            'min' => 0,
            'max' => 50,
            'step' => 1,
          ],
        ],
        'selectors' => [
          'body:not(.rtl) {{WRAPPER}} .star-rating' => 'margin-right: {{SIZE}}{{UNIT}}',
          'body.rtl {{WRAPPER}} .star-rating' => 'margin-left: {{SIZE}}{{UNIT}}',
        ],
      ]);
      $this->add_responsive_control('alignment', [
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
          'justify' => [
            'title' => __('Justified', 'tenweb-builder'),
            'icon' => 'fa fa-align-justify',
          ],
        ],
        'prefix_class' => 'elementor-product-rating--align-',
      ]);
      $this->end_controls_section();
    }
  }

  protected function render() {
    if ( !post_type_supports('product', 'comments') ) {
      return;
    }
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
    wc_get_template('single-product/rating.php');
  }

  public function render_plain_content() {
  }
}

\Elementor\Plugin::instance()->widgets_manager->register(new Product_Rating());
