<?php
namespace Tenweb_Builder\Widgets\Woocommerce\Breadcrumb;
include_once ABSPATH . 'wp-content/plugins/tenweb-builder/widgets/woocommerce/breadcrumb/Twbb_Breadcrumb.php';
use Tenweb_Builder\Widgets\Woocommerce\Breadcrumb\Twbb_Breadcrumb;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;
use Tenweb_Builder\Classes\Woocommerce\Woocommerce;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Breadcrumb extends Widget_Base {

	public function get_name() {
		return 'twbb_woocommerce-breadcrumb';
	}

	public function get_title() {
		return __( 'Woo Breadcrumbs', 'tenweb-builder' );
	}

	public function get_icon() {
		return 'twbb-breadcrumbs twbb-widget-icon';
	}

	public function get_categories() {
		return [ Woocommerce::WOOCOMMERCE_GROUP, Woocommerce::WOOCOMMERCE_BUILDER_GROUP ];
	}

	public function get_keywords() {
		return [ 'woocommerce-elements', 'shop', 'store', 'product', 'breadcrumb' ];
	}	

	protected function register_controls() {

		$this->start_controls_section(
			'section_product_rating_style',
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
					'{{WRAPPER}} .woocommerce-breadcrumb' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'link_color',
			[
				'label' => __( 'Link Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-breadcrumb > a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'text_typography',
				'selector' => '{{WRAPPER}} .woocommerce-breadcrumb',
			]
		);

		$this->add_responsive_control(
			'alignment',
			[
				'label' => __( 'Alignment', 'tenweb-builder' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'tenweb-builder' ),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'tenweb-builder' ),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'tenweb-builder' ),
						'icon' => 'fa fa-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .woocommerce-breadcrumb' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
        $this->twbb_woocommerce_breadcrumb();
	}

    /*
    * this function is copied from woocommerce_breadcrumb function
    * for changing Product Tag text and home page name and url
     */
    protected function twbb_woocommerce_breadcrumb($args = array() ) {
    $args = wp_parse_args(
        $args,
        apply_filters(
            'woocommerce_breadcrumb_defaults',
            array(
                'delimiter'   => '&nbsp;&#47;&nbsp;',
                'wrap_before' => '<nav class="woocommerce-breadcrumb" aria-label="Breadcrumb">',
                'wrap_after'  => '</nav>',
                'before'      => '',
                'after'       => '',
            )
        )
    );

    $breadcrumbs = new Twbb_Breadcrumb();

    $breadcrumbs->add_crumbs_shop_home();

    $args['breadcrumb'] = $breadcrumbs->generate();

    /**
     * WooCommerce Breadcrumb hook
     *
     * @hooked WC_Structured_Data::generate_breadcrumblist_data() - 10
     */
    do_action( 'woocommerce_breadcrumb', $breadcrumbs, $args );

    wc_get_template( 'global/breadcrumb.php', $args );
}

	public function render_plain_content() {}
}

\Elementor\Plugin::instance()->widgets_manager->register( new Breadcrumb() );
