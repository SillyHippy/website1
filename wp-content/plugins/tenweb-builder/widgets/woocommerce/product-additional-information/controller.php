<?php
namespace Tenweb_Buider\Widgets\Woocommerce;

use Tenweb_Builder\Classes\Woocommerce\Woocommerce;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

class Product_Additional_Information extends Widget_Base {

  public function get_name() {
    return 'twbb_woocommerce-product-additional-information';
  }

  public function get_title() {
    return __( 'Additional Information', 'tenweb-builder' );
  }

  public function get_icon() {
    return 'twbb-additional_information twbb-widget-icon';
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
      $this->start_controls_section( 'section_additional_info_style', [
      'label' => __( 'General', 'tenweb-builder' ),
      'tab' => Controls_Manager::TAB_STYLE,
      ] );

      $this->add_control(
      'show_heading',
      [
      'label' => __( 'Heading', 'tenweb-builder' ),
      'type' => Controls_Manager::SWITCHER,
      'label_on' => __( 'Show', 'tenweb-builder' ),
      'label_off' => __( 'Hide', 'tenweb-builder' ),
      'render_type' => 'ui',
      'return_value' => 'yes',
      'default' => 'yes',
      'prefix_class' => 'elementor-show-heading-',
      ]
      );

      $this->add_control(
      'heading_color',
      [
      'label' => __( 'Color', 'tenweb-builder' ),
      'type' => Controls_Manager::COLOR,
      'selectors' => [
      '{{WRAPPER}} h2' => 'color: {{VALUE}}',
      ],
      'condition' => [
      'show_heading!' => '',
      ],
      ]
      );

      $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
      'name' => 'heading_typography',
      'label' => __( 'Typography', 'tenweb-builder' ),
      'selector' => '{{WRAPPER}} h2',
      'condition' => [
      'show_heading!' => '',
      ],
      ]
      );

      $this->add_control(
      'content_color',
      [
      'label' => __( 'Color', 'tenweb-builder' ),
      'type' => Controls_Manager::COLOR,
      'selectors' => [
      '{{WRAPPER}} .shop_attributes' => 'color: {{VALUE}}',
      ],
      'separator' => 'before',
      ]
      );

      $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
      'name' => 'content_typography',
      'label' => __( 'Typography', 'tenweb-builder' ),
      'selector' => '{{WRAPPER}} .shop_attributes',
      ]
      );

      $this->end_controls_section();
    }
  }

  protected function render() {
    global $product;
    if ( Woocommerce::is_template_page() && Woocommerce::get_preview_product() ) {
      $preview_product = Woocommerce::get_preview_product();
      $product = wc_get_product($preview_product->ID);
      if( $product ) {
        $heading =  apply_filters( 'woocommerce_product_additional_information_heading', __( 'Additional information', 'tenweb-builder' ) );
        if ( $heading ) {
          echo ( '<h2>' . esc_html($heading) . '</h2>' );
          do_action( 'woocommerce_product_additional_information', $product );
        }
      }
    } else {
      $product = wc_get_product();

      if ( empty( $product ) ) {
        return;
      }

      wc_get_template( 'single-product/tabs/additional-information.php' );
    }
  }

  public function render_plain_content() {}
}

\Elementor\Plugin::instance()->widgets_manager->register(new Product_Additional_Information());
