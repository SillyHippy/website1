<?php

namespace Tenweb_Builder\Widgets\Woocommerce\Products;

use Tenweb_Builder\Classes\Woocommerce\Woocommerce;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Utils;

if ( !defined('ABSPATH') ) {
  exit; // Exit if accessed directly
}

class Product_Data_Tabs extends Widget_Base {

  public function get_name() {
    return 'twbb_woocommerce-product-data-tabs';
  }

  public function get_title() {
    return __('Product Data Tabs', 'tenweb-builder');
  }

  public function get_icon() {
    return 'twbb-product_tabs twbb-widget-icon';
  }

  public function get_categories() {
    return [ Woocommerce::WOOCOMMERCE_BUILDER_GROUP ];
  }

  public function get_keywords() {
    return [ 'woocommerce', 'shop', 'store', 'product', 'data', 'tabs' ];
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
      $this->start_controls_section('section_product_tabs_style', [
        'label' => __('Tabs', 'tenweb-builder'),
        'tab' => Controls_Manager::TAB_STYLE,
      ]);
      $this->add_control('wc_style_warning', [
        'type' => Controls_Manager::RAW_HTML,
        'raw' => __('The style of this widget is often affected by your theme and plugins. If you experience any such issue, try to switch to a basic theme and deactivate related plugins.', 'tenweb-builder'),
        'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
      ]);
      $this->start_controls_tabs('tabs_style');
      $this->start_controls_tab('normal_tabs_style', [
        'label' => __('Normal', 'tenweb-builder'),
      ]);
      $this->add_control('tab_text_color', [
        'label' => __('Text Color', 'tenweb-builder'),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .woocommerce-tabs ul.wc-tabs li a' => 'color: {{VALUE}}'
        ],
      ]);
      $this->add_control('tab_bg_color', [
        'label' => __('Background Color', 'tenweb-builder'),
        'type' => Controls_Manager::COLOR,
        'alpha' => FALSE,
        'selectors' => [
          '{{WRAPPER}} .woocommerce-tabs ul.wc-tabs li' => 'background-color: {{VALUE}}',
        ],
      ]);
      $this->add_control('tabs_border_color', [
        'label' => __('Border Color', 'tenweb-builder'),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .woocommerce-tabs .woocommerce-Tabs-panel' => 'border-color: {{VALUE}}',
          '{{WRAPPER}} .woocommerce-tabs ul.wc-tabs li' => 'border-color: {{VALUE}}',
        ],
      ]);
      $this->end_controls_tab();
      $this->start_controls_tab('active_tabs_style', [
        'label' => __('Active', 'tenweb-builder'),
      ]);
      $this->add_control('active_tab_text_color', [
        'label' => __('Text Color', 'tenweb-builder'),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .woocommerce-tabs ul.wc-tabs li.active a' => 'color: {{VALUE}}',
		  '{{WRAPPER}} .woocommerce-tabs ul.tabs li.active::after' => 'color: {{VALUE}}'
        ],
      ]);
      $this->add_control('active_tab_bg_color', [
        'label' => __('Background Color', 'tenweb-builder'),
        'type' => Controls_Manager::COLOR,
        'alpha' => FALSE,
        'selectors' => [
          '{{WRAPPER}} .woocommerce-tabs .woocommerce-Tabs-panel, {{WRAPPER}} .woocommerce-tabs ul.wc-tabs li.active' => 'background-color: {{VALUE}}',
          '{{WRAPPER}} .woocommerce-tabs ul.wc-tabs li.active' => 'border-bottom-color: {{VALUE}}',
        ],
      ]);
      $this->add_control('active_tabs_border_color', [
        'label' => __('Border Color', 'tenweb-builder'),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .woocommerce-tabs .woocommerce-Tabs-panel' => 'border-color: {{VALUE}}',
          '{{WRAPPER}} .woocommerce-tabs ul.wc-tabs li.active' => 'border-color: {{VALUE}} {{VALUE}} {{active_tab_bg_color.VALUE}} {{VALUE}}',
          '{{WRAPPER}} .woocommerce-tabs ul.wc-tabs li:not(.active)' => 'border-bottom-color: {{VALUE}}',
        ],
      ]);
      $this->end_controls_tab();
      $this->end_controls_tabs();
      $this->add_control('separator_tabs_style', [
        'type' => Controls_Manager::DIVIDER,
        'style' => 'thick',
      ]);
      $this->add_group_control(Group_Control_Typography::get_type(), [
        'name' => 'tab_typography',
        'label' => __('Typography', 'tenweb-builder'),
        'selector' => '{{WRAPPER}} .woocommerce-tabs ul.wc-tabs li a',
      ]);
      $this->add_control('tab_border_radius', [
        'label' => __('Border Radius', 'tenweb-builder'),
        'type' => Controls_Manager::SLIDER,
        'selectors' => [
          '{{WRAPPER}} .woocommerce-tabs ul.wc-tabs li' => 'border-radius: {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}} 0 0',
        ],
      ]);
      $this->end_controls_section();
      $this->start_controls_section('section_product_panel_style', [
        'label' => __('Panel', 'tenweb-builder'),
        'tab' => Controls_Manager::TAB_STYLE,
      ]);
      $this->add_control('text_color', [
        'label' => __('Text Color', 'tenweb-builder'),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .woocommerce-Tabs-panel' => 'color: {{VALUE}}',
        ],
      ]);
      $this->add_group_control(Group_Control_Typography::get_type(), [
        'name' => 'content_typography',
        'label' => __('Typography', 'tenweb-builder'),
        'selector' => '{{WRAPPER}} .woocommerce-tabs .woocommerce-Tabs-panel',
      ]);
      $this->add_control('heading_panel_heading_style', [
        'type' => Controls_Manager::HEADING,
        'label' => __('Heading', 'tenweb-builder'),
        'separator' => 'before',
      ]);
      $this->add_control('heading_color', [
        'label' => __('Text Color', 'tenweb-builder'),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .woocommerce-Tabs-panel h2' => 'color: {{VALUE}}',
        ],
      ]);
      $this->add_group_control(Group_Control_Typography::get_type(), [
        'name' => 'content_heading_typography',
        'label' => __('Typography', 'tenweb-builder'),
        'selector' => '{{WRAPPER}} .woocommerce-tabs .woocommerce-Tabs-panel h2',
      ]);
      $this->add_control('separator_panel_style', [
        'type' => Controls_Manager::DIVIDER,
        'style' => 'thick',
      ]);
      $this->add_control('panel_border_width', [
        'label' => __('Border Width', 'tenweb-builder'),
        'type' => Controls_Manager::DIMENSIONS,
        'selectors' => [
          '{{WRAPPER}} .woocommerce-tabs .woocommerce-Tabs-panel' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; margin-top: -{{TOP}}{{UNIT}}',
        ],
      ]);
      $this->add_control('panel_border_radius', [
        'label' => __('Border Radius', 'tenweb-builder'),
        'type' => Controls_Manager::DIMENSIONS,
        'selectors' => [
          '{{WRAPPER}} .woocommerce-tabs .woocommerce-Tabs-panel' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
          '{{WRAPPER}} .woocommerce-tabs ul.wc-tabs' => 'margin-left: {{TOP}}{{UNIT}}; margin-right: {{RIGHT}}{{UNIT}}',
        ],
      ]);
      $this->add_group_control(Group_Control_Box_Shadow::get_type(), [
        'name' => 'panel_box_shadow',
        'selector' => '{{WRAPPER}} .woocommerce-tabs .woocommerce-Tabs-panel',
      ]);
      $this->end_controls_section();
    }
  }

  protected function render() {
    global $post;
    global $product;
	$template_page_ID = get_the_ID();
	$is_template_page = FALSE;
    if ( Woocommerce::is_template_page() && Woocommerce::get_preview_product() ) {
	  $is_template_page = TRUE;
      $preview_product = Woocommerce::get_preview_product();
      $product = wc_get_product($preview_product->ID);
	  $post = get_post($preview_product->ID); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
    }
    else {
      $product = wc_get_product();
    }
    if ( empty($product) ) {
      return;
    }
    setup_postdata( $product->get_id() );
    wc_get_template('single-product/tabs/tabs.php');

	// It is important for the post to be restored!
	if ( $is_template_page ) {
		unset($post);
		global $post; // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.VariableRedeclaration
		$post = get_post( $template_page_ID ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
	}
    // On render widget from Editor - trigger the init manually.
    if ( wp_doing_ajax() ) {
      ?>
      <script>
        jQuery('.wc-tabs-wrapper, .woocommerce-tabs, #rating').trigger('init');
      </script>
      <?php
    }
  }

  public function render_plain_content() {
  }
}

\Elementor\Plugin::instance()->widgets_manager->register( new Product_Data_Tabs() );
