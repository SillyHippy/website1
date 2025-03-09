<?php
namespace Tenweb_Buider\Widgets\Woocommerce;

use Tenweb_Builder\Classes\Woocommerce\Woocommerce;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Product_Short_Description extends Widget_Base {

	public function get_name() {
		return 'twbb_woocommerce-product-short-description';
	}

	public function get_title() {
		return __( 'Short Description', 'tenweb-builder' );
	}

	public function get_icon() {
		return 'twbb-short_description twbb-widget-icon';
	}

	public function get_categories() {
		return [ Woocommerce::WOOCOMMERCE_BUILDER_GROUP ];
	}

	public function get_keywords() {
		return [ 'woocommerce', 'shop', 'store', 'text', 'description', 'product' ];
	}

	protected function register_controls() {
    if (!Woocommerce::get_preview_product()) {
      $this->start_controls_section('general', [
        'label' => $this->get_title(),
      ]);
      $this->add_control('msg', [
        'type' => \Elementor\Controls_Manager::RAW_HTML,
        'raw' => Woocommerce::add_new_product_link(),
      ]);
      $this->end_controls_section();
    } else {
      $this->start_controls_section(
        'section_product_description_style',
        [
          'label' => __('Style', 'tenweb-builder'),
          'tab' => Controls_Manager::TAB_STYLE,
        ]
      );

      $this->add_control(
        'wc_style_warning',
        [
          'type' => Controls_Manager::RAW_HTML,
          'raw' => __('The style of this widget is often affected by your theme and plugins. If you experience any such issue, try to switch to a basic theme and deactivate related plugins.', 'tenweb-builder'),
          'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
        ]
      );

      $this->add_responsive_control(
        'text_align',
        [
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
          'selectors' => [
            '{{WRAPPER}}' => 'text-align: {{VALUE}}',
          ],
        ]
      );

      $this->add_control(
        'text_color',
        [
          'label' => __('Text Color', 'tenweb-builder'),
          'type' => Controls_Manager::COLOR,
          'selectors' => [
            '.woocommerce {{WRAPPER}} .woocommerce-product-details__short-description' => 'color: {{VALUE}}',
            '{{WRAPPER}}' => 'color: {{VALUE}}',
          ],
        ]
      );

      $this->add_group_control(
        Group_Control_Typography::get_type(),
        [
          'name' => 'text_typography',
          'label' => __('Typography', 'tenweb-builder'),
          'selectors' => ['.woocommerce {{WRAPPER}} .woocommerce-product-details__short-description', '{{WRAPPER}}'],
        ]
      );

      $this->end_controls_section();
    }
  }

	protected function content_template() {
    if (Woocommerce::is_template_page() && Woocommerce::get_preview_product()) {
      $sh_desc = __('This is the product Short Description Widget. It is a dynamic widget that displays the short description of WooCommerce products.', 'tenweb-builder');
      $preview_product = Woocommerce::get_preview_product();
      if ($preview_product->post_excerpt) {
        $sh_desc = $preview_product->post_excerpt;
      }
    } else {
      return;
    }
    ?>
    <div class="woocommerce-product-details__short-description"><?php echo do_shortcode( $sh_desc ); ?></div>
    <?php
  }

	protected function render() {
		global $product;
		$product = wc_get_product();

		if ( empty( $product ) ) {
			return;
		}

		wc_get_template( 'single-product/short-description.php' );
	}

	public function render_plain_content() {}
}

\Elementor\Plugin::instance()->widgets_manager->register( new Product_Short_Description() );

