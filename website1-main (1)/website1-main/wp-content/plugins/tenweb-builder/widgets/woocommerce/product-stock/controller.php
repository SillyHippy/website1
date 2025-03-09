<?php
namespace Tenweb_Buider\Widgets\Woocommerce;

use Tenweb_Builder\Classes\Woocommerce\Woocommerce;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Product_Stock extends Widget_Base {

	public function get_name() {
		return 'twbb_woocommerce-product-stock';
	}

	public function get_title() {
		return __( 'Product Stock', 'tenweb-builder' );
	}

	public function get_icon() {
		return 'twbb-product_stock twbb-widget-icon';
	}

	public function get_categories() {
    	return [ Woocommerce::WOOCOMMERCE_BUILDER_GROUP ];
  	}

	public function get_keywords() {
		return [ 'woocommerce', 'shop', 'store', 'stock', 'quantity', 'product' ];
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
			$this->start_controls_section(
				'section_product_stock_style',
				[
					'label' => __( 'Style', 'tenweb-builder' ),
					'tab' => Controls_Manager::TAB_STYLE,
				]
			);

			$this->add_control(
				'wc_style_warning',
				[
					'type' => Controls_Manager::RAW_HTML,
					'raw' => __( 'The style of this widget is often affected by your theme and plugins. If you experience any such issue, try to switch to a basic theme and deactivate related plugins.', 'tenweb-builder' ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				]
			);

			$this->add_control(
				'text_color',
				[
					'label' => __( 'Text Color', 'tenweb-builder' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'.woocommerce {{WRAPPER}} .stock' => 'color: {{VALUE}}',
						'{{WRAPPER}}' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' => 'text_typography',
					'label' => __( 'Typography', 'tenweb-builder' ),
					'selector' => '{{WRAPPER}}',
				]
			);

			$this->end_controls_section();
		}
	}

	protected function content_template() {
		if ( Woocommerce::is_template_page() && Woocommerce::get_preview_product() ) {
	    	$stock = __('This is the product Stock Widget. It is a dynamic widget that displays the availability of WooCommerce products in stock.', 'tenweb-builder');
	      	$preview_product = Woocommerce::get_preview_product();
	      	$product = wc_get_product($preview_product->ID);
	      	$availability = $product->get_availability();
	      	if( $availability['availability'] ) {
	      		$stock = $availability['availability'];
	      	}
	    } else {
	    	return;
	    }
	    ?>
	    <#
	    stock = '<?php echo esc_html($stock); ?>';
	    print( stock );
	    #>
	    <?php
  	}

	protected function render() {
		global $product;
		$product = wc_get_product();

		if ( empty( $product ) ) {
			return;
		}

        // PHPCS - the method wc_get_stock_html is safe.
        echo wc_get_stock_html( $product ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

    }

	public function render_plain_content() {} // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

\Elementor\Plugin::instance()->widgets_manager->register( new Product_Stock() );
