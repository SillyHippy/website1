<?php

namespace Tenweb_Builder\Widgets\Woocommerce\Products;

use Tenweb_Builder\Classes\Woocommerce\Woocommerce;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Utils;

if ( !defined('ABSPATH') ) {
  exit; // Exit if accessed directly
}

class Product_Images extends Widget_Base {

  public function get_name() {
    return 'twbb_woocommerce-product-images';
  }

  public function get_title() {
    return __('Product Images', 'tenweb-builder');
  }

  public function get_icon() {
    return 'twbb-product_images twbb-widget-icon';
  }

  public function get_keywords() {
    return [ 'woocommerce', 'shop', 'store', 'product', 'image', 'gallery', 'lightbox' ];
  }

  public function get_categories() {
    return [ Woocommerce::WOOCOMMERCE_BUILDER_GROUP ];
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
      $this->start_controls_section('section_product_gallery_style', [
        'label' => __('Style', 'tenweb-builder'),
        'tab' => Controls_Manager::TAB_STYLE,
      ]);
      $this->add_control('wc_style_warning', [
        'type' => Controls_Manager::RAW_HTML,
        'raw' => __('The style of this widget is often affected by your theme and plugins. If you experience any such issue, try to switch to a basic theme and deactivate related plugins.', 'tenweb-builder'),
        'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
      ]);
      $this->add_control('sale_flash', [
        'label' => __('Sale Flash', 'tenweb-builder'),
        'type' => Controls_Manager::SWITCHER,
        'label_on' => __('Show', 'tenweb-builder'),
        'label_off' => __('Hide', 'tenweb-builder'),
        'render_type' => 'template',
        'return_value' => 'yes',
        'default' => 'yes',
        'prefix_class' => '',
      ]);
      $this->add_group_control(Group_Control_Border::get_type(), [
        'name' => 'image_border',
        'selector' => '{{WRAPPER}} .woocommerce-product-gallery .flex-viewport',
        'separator' => 'before',
      ]);
      $this->add_responsive_control('image_border_radius', [
        'label' => __('Border Radius', 'tenweb-builder'),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%' ],
        'selectors' => [
          '{{WRAPPER}} .woocommerce-product-gallery .flex-viewport' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
        ],
      ]);
      $this->add_control('spacing', [
        'label' => __('Spacing', 'tenweb-builder'),
        'type' => Controls_Manager::SLIDER,
        'size_units' => [ 'px', 'em' ],
        'selectors' => [
          '{{WRAPPER}} .woocommerce-product-gallery .flex-viewport' => 'margin-bottom: {{SIZE}}{{UNIT}}',
        ],
      ]);
      $this->add_control('heading_thumbs_style', [
        'label' => __('Thumbnails', 'tenweb-builder'),
        'type' => Controls_Manager::HEADING,
        'separator' => 'before',
      ]);
      $this->add_group_control(Group_Control_Border::get_type(), [
        'name' => 'thumbs_border',
        'selector' => '{{WRAPPER}} .woocommerce-product-gallery .flex-control-thumbs img',
      ]);
      $this->add_responsive_control('thumbs_border_radius', [
        'label' => __('Border Radius', 'tenweb-builder'),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%' ],
        'selectors' => [
          '{{WRAPPER}} .woocommerce-product-gallery .flex-control-thumbs img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
        ],
      ]);
	  $this->add_control(
			'spacing_thumbs',
			[
				'label' => __( 'Spacing', 'tenweb-builder' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .flex-control-thumbs li' => 'padding-right: calc({{SIZE}}{{UNIT}} / 2); padding-left: calc({{SIZE}}{{UNIT}} / 2); padding-bottom: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .flex-control-thumbs' => 'margin-right: calc(-{{SIZE}}{{UNIT}} / 2); margin-left: calc(-{{SIZE}}{{UNIT}} / 2)',
				],
			]
		);
      $this->end_controls_section();
    }
  }

  public function render() {
    $settings = $this->get_settings_for_display();
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
    if ( 'yes' === $settings['sale_flash'] ) {
      wc_get_template('loop/sale-flash.php');
    }
    wc_get_template('single-product/product-image.php');
    // On render widget from Editor - trigger the init manually.
    if ( wp_doing_ajax() ) {
      ?>
      <script>
		jQuery('.woocommerce-product-gallery').each(function () {
          jQuery(this).wc_product_gallery();
        });
      </script>
      <?php
    }
  }
}

\Elementor\Plugin::instance()->widgets_manager->register(new Product_Images());
