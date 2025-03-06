<?php
namespace Tenweb_Builder\Widgets\Woocommerce\Products\Skins;

use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Skin_Base as Elementor_Skin_Base;
use Elementor\Widget_Base;
use Tenweb_Builder\Widget_Slider;
use Tenweb_Builder\Widgets\Woocommerce\Products\Classes\Current_Query_Renderer;
use Tenweb_Builder\Widgets\Woocommerce\Products\Classes\Products_Renderer;
use Tenweb_Builder\Classes\Woocommerce\Woocommerce;
use WC_Product_Variable;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

abstract class Skin_Base extends Elementor_Skin_Base {

	protected function _register_controls_actions() {
		add_action( 'elementor/element/twbb_woocommerce-products/section_content/before_section_end', [ $this, 'register_controls' ] );
		add_action( 'elementor/element/twbb_woocommerce-products/section_buttons_quantity/before_section_end', [ $this, 'register_buttons_controls' ] );
		add_action( 'elementor/element/twbb_woocommerce-products/section_query/after_section_end', [ $this, 'register_base_style_controls' ] );
        $this->add_injected_controls();
	}

    public function add_injected_controls() {}

    public function register_controls(Widget_Base $widget) {
        $this->parent = $widget;
        $this->parent->start_injection( [
            'at' => 'after',
            'of' => '_skin',
        ] );
        $this->add_responsive_control('skin_columns', [
            'label' => __('Columns', 'tenweb-builder'),
            'type' => Controls_Manager::NUMBER,
            'prefix_class' => 'elementor-grid%s-',
            'min' => 1,
            'max' => 12,
            'default' => 3,
            'tablet_default' => '2',
            'mobile_default' => '1',
            'render_type' => 'template',
            'required' => TRUE,
            'device_args' => [
                Controls_Stack::RESPONSIVE_TABLET => [
                    'required' => FALSE,
                ],
                Controls_Stack::RESPONSIVE_MOBILE => [
                    'required' => FALSE,
                ],
            ],
            'min_affected_device' => [
                Controls_Stack::RESPONSIVE_DESKTOP => Controls_Stack::RESPONSIVE_TABLET,
                Controls_Stack::RESPONSIVE_TABLET => Controls_Stack::RESPONSIVE_TABLET,
            ],
        ]);

        $this->add_control(
            'skin_rows',
            [
                'label' => esc_html__( 'Rows', 'tenweb-builder' ),
                'type' => Controls_Manager::NUMBER,
                'default' => Products_Renderer::DEFAULT_COLUMNS_AND_ROWS,
                'render_type' => 'template',
                'range' => [
                    'px' => [
                        'max' => 20,
                    ],
                ],
            ]
        );

        $this->parent->end_injection();

        $this->parent->start_injection( [
            'at' => 'after',
            'of' => 'paginate',
        ] );
        $this->add_control('skin_variation_images', [
            'label' => __('Variation Images', 'tenweb-builder'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => __( 'Show', 'tenweb-builder' ),
            'label_off' => __( 'Hide', 'tenweb-builder' ),
            'default' => 'yes',
            'selectors' => [
                '{{WRAPPER}}.elementor-wc-products ul.products li.product:hover .twbb-woocommerce-products-variations' => 'display: flex',
            ],
            'conditions' => [
                'relation' => 'or',
                'terms' => [
                    [
                        'name' => 'classic_skin_product_images',
                        'operator' => '===',
                        'value' => 'yes',
                    ],
                    [
                        'name' => '_skin',
                        'operator' => '===',
                        'value' => 'modern',
                    ],
                ],
            ],

        ]);
        $this->parent->end_injection();


        $this->parent->start_injection( [
            'at' => 'after',
            'of' => 'show_result_count',
        ] );
        $this->add_control('skin_hide_products_titles', [
            'label' => __('Hide Products Titles', 'tenweb-builder'),
            'type' => Controls_Manager::SWITCHER,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}}.elementor-wc-products.twbb-product-title-default ul.products li.product .woocommerce-loop-product__title' => 'display: none',
            ],
        ]);
        $this->add_control('skin_product_title', [
            'label' => __('Product Title', 'tenweb-builder'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => __( 'Show', 'tenweb-builder' ),
            'label_off' => __( 'Hide', 'tenweb-builder' ),
            'render_type' => 'template',
            'default' => 'default',
            'prefix_class' => 'twbb-product-title-',
            'condition' => [
                '_skin' => '',
            ],
        ]);
        $this->add_control('skin_hide_products_description', [
            'label' => __('Hide Products Descriptions', 'tenweb-builder'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'selectors' => [
                '{{WRAPPER}}.elementor-wc-products.twbb-product-description-default ul.products li.product .twbb_woocommerce-loop-product__desc' => 'display: none',
            ],
        ]);
        $this->add_control('skin_product_description', [
            'label' => __('Product Description', 'tenweb-builder'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => __( 'Show', 'tenweb-builder' ),
            'label_off' => __( 'Hide', 'tenweb-builder' ),
            'render_type' => 'template',
            'default' => 'default',
            'prefix_class' => 'twbb-product-description-',
        ]);
        $this->add_control(
            'skin_description_length',
            [
                'label' => __('Description Length', 'tenweb-builder'),
                'type' => Controls_Manager::NUMBER,
                /** This filter is documented in wp-includes/formatting.php */
                'default' => apply_filters('excerpt_length', 25),
                'condition' => [
                    $this->get_control_id( 'skin_product_description') => 'yes',
                ],
            ]
        );

        //10web customization
        $this->add_control('skin_image_gallery', [
            'label' => __('Image Gallery', 'tenweb-builder'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => __('Show', 'tenweb-builder'),
            'label_off' => __('Hide', 'tenweb-builder'),
            'return_value' => 'yes',
            'default' => 'yes',
            'render_type' => 'template',
            'conditions' => [
                'relation' => 'and',
                'terms' => [
                    [
                        'relation' => 'or',
                        'terms' => [
                            [
                                'name' => 'classic_skin_product_images',
                                'operator' => '===',
                                'value' => 'yes',
                            ],
                            [
                                'name' => '_skin',
                                'operator' => '===',
                                'value' => 'modern',
                            ],
                        ],
                    ],
                    [
                        'name' => 'slider_view',
                        'operator' => '===',
                        'value' => '',
                    ],
                ],
            ],
        ]);

        $this->add_control('skin_show_second_image', [
            'label' => __('Show only one image', 'tenweb-builder'),
            'type' => Controls_Manager::SWITCHER,
            'label_off' => __('Hide', 'tenweb-builder'),
            'label_on' => __('Show', 'tenweb-builder'),
            'return_value' => 'yes',
            'default' => '',
            'condition' => [
                $this->get_control_id( 'skin_image_gallery!') => '',
            ],
        ]);


        //10web customization
        $this->parent->end_injection();
    }

    public function register_buttons_controls(Widget_Base $widget)
    {
        $this->parent = $widget;
        $this->parent->start_injection( [
            'at' => 'before',
            'of' => 'quantity_position',
        ] );
        $this->add_control('skin_hide_product_quantity', [
            'label' => __('Hide Product Quantity', 'tenweb-builder'),
            'type' => Controls_Manager::SWITCHER,
            'default' => '',
            'condition' => [
                $this->get_control_id( 'skin_product_buttons!' ) => '',
            ],
        ]);
        $this->add_control('skin_product_quantity', [
            'label' => __('Product quantity', 'tenweb-builder'),
            'description' => __('If the product has variations quantity will be hidden by default.', 'tenweb-builder'),
            'type' => Controls_Manager::SWITCHER,
            'render_type' => 'template',
            'label_on' => __('Show', 'tenweb-builder'),
            'label_off' => __('Hide', 'tenweb-builder'),
            'default' => 'default',
            'condition' => [
                $this->get_control_id('skin_product_buttons!') => '',
            ],
        ]);
        $this->parent->end_injection();
    }


    public function register_base_style_controls() {
        $this->start_controls_section('skin_section_layout_style', [
            'label' => esc_html__('Layout', 'tenweb-builder'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('skin_wc_style_warning', [
            'type' => Controls_Manager::RAW_HTML,
            'raw' => esc_html__('The style of this widget is often affected by your theme and plugins. If you experience any such issue, try to switch to a basic theme and deactivate related plugins.', 'tenweb-builder'),
            'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
        ]);
        $this->add_control('skin_products_class', [
            'type' => Controls_Manager::HIDDEN,
            'default' => 'wc-products',
            'prefix_class' => 'elementor-products-grid elementor-',
        ]);
        $this->add_responsive_control('skin_column_gap', [
            'label' => esc_html__('Columns Gap', 'tenweb-builder'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'em', 'rem', 'custom' ],
            'default' => [
                'size' => 20,
            ],
            'tablet_default' => [
                'size' => 20,
            ],
            'mobile_default' => [
                'size' => 20,
            ],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 100,
                ],
            ],
            'render_type' => 'template',
            'selectors' => [
                '{{WRAPPER}}.elementor-wc-products  ul.products' => 'grid-column-gap: {{SIZE}}{{UNIT}}',
            ],
        ]);

        $this->add_responsive_control(
            'skin_row_gap',
            [
                'label' => esc_html__( 'Rows Gap', 'tenweb-builder' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem', 'custom' ],
                'default' => [
                    'size' => 40,
                ],
                'tablet_default' => [
                    'size' => 40,
                ],
                'mobile_default' => [
                    'size' => 40,
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}.elementor-wc-products  ul.products' => 'grid-row-gap: {{SIZE}}{{UNIT}}',
                ],
            ]);
        $this->add_responsive_control('skin_align', [
            'label' => __('Alignment', 'tenweb-builder'),
            'type' => Controls_Manager::CHOOSE,
            'options' => [
                'left' => [
                    'title' => esc_html__('Left', 'tenweb-builder'),
                    'icon' => 'eicon-text-align-left',
                ],
                'center' => [
                    'title' => esc_html__('Center', 'tenweb-builder'),
                    'icon' => 'eicon-text-align-center',
                ],
                'right' => [
                    'title' => esc_html__('Right', 'tenweb-builder'),
                    'icon' => 'eicon-text-align-right',
                ],
            ],
            'prefix_class' => 'elementor-product-loop-item--align-',
            'selectors' => [
                '{{WRAPPER}}.elementor-wc-products ul.products li.product' => 'text-align: {{VALUE}}',
            ],
        ]);
        $this->end_controls_section();
        $this->start_controls_section('skin_section_products_style', [
            'label' => esc_html__('Content', 'tenweb-builder'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('skin_heading_title_style', [
            'label' => __('Title', 'tenweb-builder'),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
            'condition' => [
                $this->get_control_id( 'skin_product_title') => 'yes',
            ],
        ]);
        $this->add_control('skin_title_color', [
            'label' => __('Color', 'tenweb-builder'),
            'type' => Controls_Manager::COLOR,
            'global' => [
                'default' => Global_Colors::COLOR_PRIMARY,
            ],
            'selectors' => [
                '{{WRAPPER}}.elementor-wc-products ul.products li.product .woocommerce-loop-product__title' => 'color: {{VALUE}}',
            ],
            'condition' => [
                $this->get_control_id( 'skin_product_title') => 'yes',
            ],
        ]);
        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name' => 'title_typography',
            'global' => [
                'default' => Global_Typography::TYPOGRAPHY_TEXT,
            ],
            'selector' => '{{WRAPPER}}.elementor-wc-products ul.products li.product .woocommerce-loop-product__title',
            'condition' => [
                $this->get_control_id( 'skin_product_title') => 'yes',
            ],
        ]);
        $this->add_responsive_control('skin_title_spacing', [
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
                '{{WRAPPER}}.elementor-wc-products ul.products li.product .woocommerce-loop-product__title' => 'margin-bottom: {{SIZE}}{{UNIT}} !important',
                '{{WRAPPER}}.elementor-wc-products ul.products li.product .woocommerce-loop-category__title' => 'margin-bottom: {{SIZE}}{{UNIT}}',
            ],
            'condition' => [
                $this->get_control_id( 'skin_product_title') => 'yes',
            ],
        ]);
        $this->add_control('skin_heading_desc_style', [
            'label' => __('Description', 'tenweb-builder'),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
            'condition' => [
                $this->get_control_id( 'skin_product_description') => 'yes',
            ],
        ]);
        $this->add_control('skin_desc_color', [
            'label' => __('Color', 'tenweb-builder'),
            'type' => Controls_Manager::COLOR,
            'global' => [
                'default' => Global_Colors::COLOR_TEXT,
            ],
            'selectors' => [
                '{{WRAPPER}}.elementor-wc-products ul.products li.product .twbb_woocommerce-loop-product__desc' => 'color: {{VALUE}}',
            ],
            'condition' => [
                $this->get_control_id( 'skin_product_description') => 'yes',
            ],
        ]);
        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name' => 'desc_typography',
            'global' => [
                'default' => Global_Typography::TYPOGRAPHY_TEXT,
            ],
            'selector' => '{{WRAPPER}}.elementor-wc-products ul.products li.product .twbb_woocommerce-loop-product__desc',
            'condition' => [
                $this->get_control_id( 'skin_product_description') => 'yes',
            ],
        ]);
        $this->add_responsive_control('skin_desc_spacing', [
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
                '{{WRAPPER}}.elementor-wc-products ul.products li.product .twbb_woocommerce-loop-product__desc' => 'margin-bottom: {{SIZE}}{{UNIT}} !important',
            ],
            'condition' => [
                $this->get_control_id( 'skin_product_description') => 'yes',
            ],
        ]);
        $this->add_control('skin_heading_rating_style', [
            'label' => __('Rating', 'tenweb-builder'),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);
        $this->add_control('skin_star_color', [
            'label' => __('Star Color', 'tenweb-builder'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}}.elementor-wc-products ul.products li.product .star-rating' => 'color: {{VALUE}}',
            ],
        ]);
        $this->add_control('skin_empty_star_color', [
            'label' => __('Empty Star Color', 'tenweb-builder'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}}.elementor-wc-products ul.products li.product .star-rating::before' => 'color: {{VALUE}}',
            ],
        ]);
        $this->add_control('skin_star_size', [
            'label' => __('Star Size', 'tenweb-builder'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'em', 'rem', 'custom' ],
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
                '{{WRAPPER}}.elementor-wc-products ul.products li.product .star-rating' => 'font-size: {{SIZE}}{{UNIT}}',
            ],
        ]);
        $this->add_responsive_control('skin_rating_spacing', [
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
                '{{WRAPPER}}.elementor-wc-products ul.products li.product .star-rating' => 'margin-bottom: {{SIZE}}{{UNIT}} !important',
            ],
        ]);
        $this->add_control('skin_heading_price_style', [
            'label' => __('Sale Price', 'tenweb-builder'),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);
        $this->add_control('skin_price_color', [
            'label' => __('Color', 'tenweb-builder'),
            'type' => Controls_Manager::COLOR,
            'global' => [
                'default' => Global_Colors::COLOR_PRIMARY,
            ],
            'selectors' => [
                '{{WRAPPER}}.elementor-wc-products ul.products li.product .price del .amount bdi,
                {{WRAPPER}}.elementor-wc-products ul.products li.product .price del' => 'color: {{VALUE}}',
            ],
        ]);
        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name' => 'price_typography',
            'global' => [
                'default' => Global_Typography::TYPOGRAPHY_TEXT,
            ],
            'selector' => '{{WRAPPER}}.elementor-wc-products ul.products li.product .price del .amount bdi',
        ]);
        $this->add_responsive_control('skin_price_padding', [
            'label' => __('Sale Price Padding', 'tenweb-builder'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em' ],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 50,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}}.elementor-wc-products ul.products li.product .price del bdi' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
            ],
        ]);
        $this->add_control('skin_heading_old_price_style', [
            'label' => __('Regular Price', 'tenweb-builder'),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);
        $this->add_control('skin_old_price_color', [
            'label' => __('Color', 'tenweb-builder'),
            'type' => Controls_Manager::COLOR,
            'global' => [
                'default' => Global_Colors::COLOR_PRIMARY,
            ],
            'selectors' => [
                '{{WRAPPER}}.elementor-wc-products ul.products li.product .price .amount bdi,
                {{WRAPPER}}.elementor-wc-products ul.products li.product .price' => 'color: {{VALUE}}',
            ],
        ]);
        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name' => 'old_price_typography',
            'global' => [
                'default' => Global_Typography::TYPOGRAPHY_TEXT,
            ],
            'selector' => '{{WRAPPER}}.elementor-wc-products ul.products li.product .price .amount bdi',
        ]);
        $this->add_responsive_control('skin_regular_price_padding', [
            'label' => __('Regular Price Padding', 'tenweb-builder'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em' ],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 50,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}}.elementor-wc-products ul.products li.product .price bdi' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
            ],
        ]);
        $this->end_controls_section();
        $this->start_controls_section('skin_section_buttons_style', [
            'label' => esc_html__('Buttons', 'tenweb-builder'),
            'tab' => Controls_Manager::TAB_STYLE,
            'condition' => [
                $this->get_control_id( 'skin_product_buttons!' ) => '',
            ],
        ]);
        $this->start_controls_tabs('skin_tabs_button_style');
        $this->start_controls_tab('skin_tab_button_normal', [
            'label' => __('Normal', 'tenweb-builder'),
        ]);
        $this->add_control('skin_button_text_color', [
            'label' => __('Text Color', 'tenweb-builder'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}}.elementor-wc-products ul.products li.product .button' => 'color: {{VALUE}};',
            ],
        ]);
        $this->add_control('skin_button_background_color', [
            'label' => __('Background Color', 'tenweb-builder'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}}.elementor-wc-products ul.products li.product .button' => 'background-color: {{VALUE}};',
            ],
        ]);
        $this->add_control('skin_button_border_color', [
            'label' => __('Border Color', 'tenweb-builder'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}}.elementor-wc-products ul.products li.product .button' => 'border-color: {{VALUE}};',
            ],
        ]);
        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name' => 'button_typography',
            'global' => [
                'default' => Global_Typography::TYPOGRAPHY_ACCENT,
            ],
            'selector' => '{{WRAPPER}}.elementor-wc-products ul.products li.product .button',
        ]);
        $this->end_controls_tab();
        $this->start_controls_tab('skin_tab_button_hover', [
            'label' => __('Hover', 'tenweb-builder'),
        ]);
        $this->add_control('skin_button_hover_color', [
            'label' => __('Text Color', 'tenweb-builder'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}}.elementor-wc-products ul.products li.product .button:hover' => 'color: {{VALUE}};',
            ],
        ]);
        $this->add_control('skin_button_hover_background_color', [
            'label' => __('Background Color', 'tenweb-builder'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}}.elementor-wc-products ul.products li.product .button:hover' => 'background-color: {{VALUE}};',
            ],
        ]);
        $this->add_control('skin_button_hover_border_color', [
            'label' => __('Border Color', 'tenweb-builder'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}}.elementor-wc-products ul.products li.product .button:hover' => 'border-color: {{VALUE}};',
            ],
        ]);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_group_control(Group_Control_Border::get_type(), [
            'name' => 'button_border',
            'exclude' => [ 'color' ], // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude
            'selector' => '{{WRAPPER}}.elementor-wc-products ul.products li.product .button',
            'separator' => 'before',
        ]);
        $this->add_control('skin_button_border_radius', [
            'label' => __('Border Radius', 'tenweb-builder'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%' ],
            'selectors' => [
                '{{WRAPPER}}.elementor-wc-products ul.products li.product .button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);
        $this->add_control('skin_button_text_padding', [
            'label' => __('Text Padding', 'tenweb-builder'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
            'selectors' => [
                '{{WRAPPER}}.elementor-wc-products ul.products li.product .button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);
        $this->add_responsive_control('skin_button_spacing', [
            'label' => __('Spacing', 'tenweb-builder'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'em' ],
            'selectors' => [
                '{{WRAPPER}}.elementor-wc-products ul.products li.product > .button,
                {{WRAPPER}}.elementor-wc-products ul.products li.product .twbb-product-loop-buttons' => 'margin-top: {{SIZE}}{{UNIT}} !important',
            ],
        ]);
        $this->add_control('skin_quantity_border_radius', [
            'label' => __('Quantity Border Radius', 'tenweb-builder'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%' ],
            'selectors' => [
                '{{WRAPPER}}.elementor-wc-products .twbb-product-quantity-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'condition' => [
                $this->get_control_id('skin_product_quantity!') => '',
            ],
        ]);
        $this->add_control('skin_heading_view_cart_style', [
            'label' => __('View Cart', 'tenweb-builder'),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);
        $this->add_control('skin_view_cart_color', [
            'label' => __('Color', 'tenweb-builder'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}}.elementor-wc-products .added_to_cart' => 'color: {{VALUE}}',
            ],
        ]);
        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name' => 'view_cart_typography',
            'global' => [
                'default' => Global_Typography::TYPOGRAPHY_ACCENT,
            ],
            'selector' => '{{WRAPPER}}.elementor-wc-products .added_to_cart',
        ]);

        $this->add_responsive_control(
            'skin_view_cart_spacing',
            [
                'label' => esc_html__( 'Spacing', 'tenweb-builder' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem', 'custom' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                        'step' => 1,
                    ],
                    'em' => [
                        'min' => 0,
                        'max' => 3.5,
                        'step' => 0.1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}.elementor-wc-products .added_to_cart' => 'margin-inline-start: {{SIZE}}{{UNIT}}',
                ],
            ]);

        $this->end_controls_section();

        $this->register_variations_style();

        $this->start_controls_section('skin_section_design_box', [
            'label' => __('Box', 'tenweb-builder'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);
        $this->add_control('skin_box_border_width', [
            'label' => __('Border Width', 'tenweb-builder'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px' ],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 50,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}}.elementor-wc-products ul.products li.product' => 'border-style: solid; border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
            ],
        ]);
        $this->add_control('skin_box_border_radius', [
            'label' => __('Border Radius', 'tenweb-builder'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 200,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}}.elementor-wc-products ul.products li.product' => 'border-radius: {{SIZE}}{{UNIT}}',
            ],
        ]);
        $this->add_responsive_control('skin_box_padding', [
            'label' => __('Box Padding', 'tenweb-builder'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 50,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}}.elementor-wc-products ul.products li.product' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
            ],
        ]);
        $this->start_controls_tabs('skin_box_style_tabs');
        $this->start_controls_tab('skin_classic_style_normal', [
            'label' => __('Normal', 'tenweb-builder'),
        ]);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), [
            'name' => 'box_shadow',
            'selector' => '{{WRAPPER}}.elementor-wc-products ul.products li.product',
        ]);
        $this->add_control('skin_box_bg_color', [
            'label' => __('Background Color', 'tenweb-builder'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}}.elementor-wc-products ul.products li.product' => 'background-color: {{VALUE}}',
            ],
        ]);
        $this->add_control('skin_box_border_color', [
            'label' => __('Border Color', 'tenweb-builder'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}}.elementor-wc-products ul.products li.product' => 'border-color: {{VALUE}}',
            ],
        ]);
        $this->end_controls_tab();
        $this->start_controls_tab('skin_classic_style_hover', [
            'label' => __('Hover', 'tenweb-builder'),
        ]);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), [
            'name' => 'box_shadow_hover',
            'selector' => '{{WRAPPER}}.elementor-wc-products ul.products li.product:hover',
        ]);
        $this->add_control('skin_box_bg_color_hover', [
            'label' => __('Background Color', 'tenweb-builder'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}}.elementor-wc-products ul.products li.product:hover' => 'background-color: {{VALUE}}',
            ],
        ]);
        $this->add_control('skin_box_border_color_hover', [
            'label' => __('Border Color', 'tenweb-builder'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}}.elementor-wc-products ul.products li.product:hover' => 'border-color: {{VALUE}}',
            ],
        ]);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section();

        $this->start_controls_section('skin_image_style', [
            'label' => __('Image', 'tenweb-builder'),
            'tab' => Controls_Manager::TAB_STYLE,
            'conditions' => [
                'relation' => 'or',
                'terms' => [
                    [
                        'name' => 'classic_skin_product_images',
                        'operator' => '===',
                        'value' => 'yes',
                    ],
                    [
                        'name' => '_skin',
                        'operator' => '===',
                        'value' => 'modern',
                    ],
                ],
            ],

        ]);
        $this->add_control(
            'skin_image_aspect_ratio',
            [
                'type' => Controls_Manager::SELECT,
                'label' => esc_html__( 'Aspect Ratio', 'elementor-pro' ),
                'default' => '1:1',
                'options' => [
                    '1:1' => '1:1',
                    '3:2' => '3:2',
                    '3:4' => '3:4',
                    '4:3' => '4:3',
                    '9:16' => '9:16',
                    '16:9' => '16:9',
                    '21:9' => '21:9',
                ],
                'render_type' => 'template',
                'frontend_available' => true,
            ]
        );
        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'skin_image_resolution',
                'default' => 'woocommerce_thumbnail',
            ]
        );
        $this->add_control('skin_heading_image_style', [
            'label' => esc_html__('Hover Effect', 'tenweb-builder'),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);
        $this->add_control(
            'skin_image_hover_animation',
            [
                'label' => esc_html__( 'Hover Animation', 'tenweb-builder' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    '' => 'None',
                    'zoom-in' => 'Zoom In',
                    'zoom-out' => 'Zoom Out',
                    'move-left' => 'Move Left',
                    'move-right' => 'Move Right',
                    'move-up' => 'Move Up',
                    'move-down' => 'Move Down',
                ],
                'default' => '',
                'prefix_class' => 'elementor-animated-item--',
                'render_type' => 'template',
            ]
        );
        $this->add_control(
            'skin_content_animation_duration',
            [
                'label' => __('Animation Duration', 'tenweb-builder'),
                'type' => Controls_Manager::SLIDER,
                'render_type' => 'ui',
                'default' => [
                    'size' => 1000,
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 3000,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .twbb-image-container' => '--animation-duration: {{SIZE}}ms;',
                ],
            ]
        );
        $this->add_control('skin_heading_image_border_style', [
            'label' => esc_html__('Borders', 'tenweb-builder'),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
            'condition' => [
                '_skin!' => 'modern',
            ],
        ]);
        $this->add_group_control(Group_Control_Border::get_type(), [
            'name' => 'image_border',
            'selector' => '{{WRAPPER}}.elementor-wc-products .twbb-image-container',
            'condition' => [
                '_skin!' => 'modern',
            ],
        ]);
        $this->add_responsive_control('skin_image_border_radius', [
            'label' => __('Border Radius', 'tenweb-builder'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%' ],
            'selectors' => [
                '{{WRAPPER}}.elementor-wc-products .twbb-image-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
            ],
            'condition' => [
                '_skin!' => 'modern',
            ],
        ]);
        $this->add_responsive_control('skin_image_spacing', [
            'label' => __('Spacing', 'tenweb-builder'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'em' ],
            'selectors' => [
                '{{WRAPPER}}.elementor-wc-products .twbb-image-container' => 'margin-bottom: {{SIZE}}{{UNIT}} !important',
            ],
            'condition' => [
                '_skin!' => 'modern',
            ],
        ]);
        $this->end_controls_section();

        $this->start_controls_section('skin_section_pagination_style', [
            'label' => __('Pagination', 'tenweb-builder'),
            'tab' => Controls_Manager::TAB_STYLE,
            'condition' => [
                'paginate' => 'yes',
            ],
        ]);
        $this->add_control('skin_pagination_spacing', [
            'label' => __('Spacing', 'tenweb-builder'),
            'type' => Controls_Manager::SLIDER,
            'selectors' => [
                '{{WRAPPER}} nav.woocommerce-pagination' => 'margin-top: {{SIZE}}{{UNIT}}',
            ],
        ]);
        $this->add_control('skin_show_pagination_border', [
            'label' => __('Border', 'tenweb-builder'),
            'type' => Controls_Manager::SWITCHER,
            'label_off' => __('Hide', 'tenweb-builder'),
            'label_on' => __('Show', 'tenweb-builder'),
            'default' => 'yes',
            'return_value' => 'yes',
            'prefix_class' => 'elementor-show-pagination-border-',
        ]);
        $this->add_control('skin_pagination_border_color', [
            'label' => __('Border Color', 'tenweb-builder'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} nav.woocommerce-pagination ul' => 'border-color: {{VALUE}}',
                '{{WRAPPER}} nav.woocommerce-pagination ul li' => 'border-right-color: {{VALUE}}; border-left-color: {{VALUE}}',
            ],
            'condition' => [
                $this->get_control_id( 'skin_show_pagination_border') => 'yes',
            ],
        ]);
        $this->add_control('skin_pagination_padding', [
            'label' => __('Padding', 'tenweb-builder'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
            'range' => [
                'em' => [
                    'min' => 0,
                    'max' => 2,
                    'step' => 0.1,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} nav.woocommerce-pagination ul li a, {{WRAPPER}} nav.woocommerce-pagination ul li span' => 'padding: {{SIZE}}{{UNIT}}',
            ],
        ]);
        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name' => 'pagination_typography',
            'selector' => '{{WRAPPER}} nav.woocommerce-pagination',
        ]);
        $this->start_controls_tabs('skin_pagination_style_tabs');
        $this->start_controls_tab('skin_pagination_style_normal', [
            'label' => __('Normal', 'tenweb-builder'),
        ]);
        $this->add_control('skin_pagination_link_color', [
            'label' => __('Color', 'tenweb-builder'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} nav.woocommerce-pagination ul li a' => 'color: {{VALUE}}',
            ],
        ]);
        $this->add_control('skin_pagination_link_bg_color', [
            'label' => __('Background Color', 'tenweb-builder'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} nav.woocommerce-pagination ul li a' => 'background-color: {{VALUE}}',
            ],
        ]);
        $this->end_controls_tab();
        $this->start_controls_tab('skin_pagination_style_hover', [
            'label' => __('Hover', 'tenweb-builder'),
        ]);
        $this->add_control('skin_pagination_link_color_hover', [
            'label' => __('Color', 'tenweb-builder'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} nav.woocommerce-pagination ul li a:hover' => 'color: {{VALUE}}',
            ],
        ]);
        $this->add_control('skin_pagination_link_bg_color_hover', [
            'label' => __('Background Color', 'tenweb-builder'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} nav.woocommerce-pagination ul li a:hover' => 'background-color: {{VALUE}}',
            ],
        ]);
        $this->end_controls_tab();
        $this->start_controls_tab('skin_pagination_style_active', [
            'label' => __('Active', 'tenweb-builder'),
        ]);
        $this->add_control('skin_pagination_link_color_active', [
            'label' => __('Color', 'tenweb-builder'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} nav.woocommerce-pagination ul li span.current' => 'color: {{VALUE}}',
            ],
        ]);
        $this->add_control('skin_pagination_link_bg_color_active', [
            'label' => __('Background Color', 'tenweb-builder'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} nav.woocommerce-pagination ul li span.current' => 'background-color: {{VALUE}}',
            ],
        ]);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section();
        $this->start_controls_section('skin_sale_flash_style', [
            'label' => __('Badge', 'tenweb-builder'),
            'tab' => Controls_Manager::TAB_STYLE,
            'condition' => [
                'show_onsale_flash' => 'yes',
            ],
        ]);
        $this->add_control('skin_onsale_text_color', [
            'label' => __('Text Color', 'tenweb-builder'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}}.elementor-wc-products ul.products li.product span.twbb_products_badge' => 'color: {{VALUE}}',
                '{{WRAPPER}}.elementor-wc-products ul.products li.product span.onsale' => 'color: {{VALUE}}',
            ],
        ]);
        $this->add_control('skin_onsale_text_background_color', [
            'label' => __('Background Color', 'tenweb-builder'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}}.elementor-wc-products ul.products li.product span.twbb_products_badge' => 'background-color: {{VALUE}}',
                '{{WRAPPER}}.elementor-wc-products ul.products li.product span.onsale' => 'background-color: {{VALUE}}',
            ],
        ]);
        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name' => 'onsale_typography',
            'selector' => '{{WRAPPER}}.elementor-wc-products ul.products li.product span.twbb_products_badge,{{WRAPPER}}.elementor-wc-products ul.products li.product span.onsale',
        ]);
        $this->add_control('skin_onsale_border_radius', [
            'label' => __('Border Radius', 'tenweb-builder'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
            'selectors' => [
                '{{WRAPPER}}.elementor-wc-products ul.products li.product span.twbb_products_badge' => 'border-radius: {{SIZE}}{{UNIT}}',
                '{{WRAPPER}}.elementor-wc-products ul.products li.product span.onsale' => 'border-radius: {{SIZE}}{{UNIT}}',
            ],
        ]);
        $this->add_control('skin_onsale_width', [
            'label' => __('Width', 'tenweb-builder'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
            'selectors' => [
                '{{WRAPPER}}.elementor-wc-products ul.products li.product span.twbb_products_badge' => 'min-width: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}}.elementor-wc-products ul.products li.product span.onsale' => 'min-width: {{SIZE}}{{UNIT}};',
            ],
        ]);
        $this->add_control('skin_onsale_height', [
            'label' => __('Height', 'tenweb-builder'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'em' ],
            'selectors' => [
                '{{WRAPPER}}.elementor-wc-products ul.products li.product span.twbb_products_badge' => 'min-height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}}.elementor-wc-products ul.products li.product span.onsale' => 'min-height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
            ],
        ]);
        $this->add_control('skin_onsale_horizontal_position', [
            'label' => __('Position', 'tenweb-builder'),
            'type' => Controls_Manager::CHOOSE,
            'label_block' => FALSE,
            'options' => [
                'left' => [
                    'title' => __('Left', 'tenweb-builder'),
                    'icon' => 'eicon-h-align-left',
                ],
                'right' => [
                    'title' => __('Right', 'tenweb-builder'),
                    'icon' => 'eicon-h-align-right',
                ],
            ],
            'selectors' => [
                '{{WRAPPER}}.elementor-wc-products ul.products li.product span.twbb_products_badge' => '{{VALUE}}',
                '{{WRAPPER}}.elementor-wc-products ul.products li.product span.onsale' => '{{VALUE}}',
            ],
            'selectors_dictionary' => [
                'left' => 'right: auto; left: 0',
                'right' => 'left: auto; right: 0',
            ],
        ]);
        $this->add_control('skin_onsale_distance', [
            'label' => __('Distance', 'tenweb-builder'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'em', 'rem', 'custom' ],
            'range' => [
                'px' => [
                    'min' => -20,
                    'max' => 20,
                ],
                'em' => [
                    'min' => -2,
                    'max' => 2,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}}.elementor-wc-products ul.products li.product span.twbb_products_badge' => 'margin: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}}.elementor-wc-products ul.products li.product span.onsale' => 'margin: {{SIZE}}{{UNIT}};',
            ],
        ]);
        $this->end_controls_section();

        $this->parent->start_injection( [
            'type' => 'section',
            'at' => 'start',
            'of' => $this->get_control_id('skin_section_design_box'),
        ] );

        $this->start_controls_section(
            'skin_products_title_style',
            [
                'label' => esc_html__( 'Title', 'tenweb-builder' ),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'products_title_show!' => '',
                ],
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => Products_Renderer::QUERY_CONTROL_NAME . '_post_type',
                            'operator' => '===',
                            'value' => 'related_products',
                        ],
                        [
                            'name' => Products_Renderer::QUERY_CONTROL_NAME . '_post_type',
                            'operator' => '===',
                            'value' => 'upsells',
                        ],
                        [
                            'name' => Products_Renderer::QUERY_CONTROL_NAME . '_post_type',
                            'operator' => '===',
                            'value' => 'cross_sells',
                        ],
                    ],
                ],
            ]
        );

        $this->add_control(
            'skin_products_title_color',
            [
                'label' => esc_html__( 'Color', 'tenweb-builder' ),
                'type' => Controls_Manager::COLOR,
                'global' => [
                    'default' => Global_Colors::COLOR_PRIMARY,
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--products-title-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'products_title_typography',
                'selector' => '{{WRAPPER}}.products-heading-show .related-products > h2, {{WRAPPER}}.products-heading-show .upsells > h2, {{WRAPPER}}.products-heading-show .cross-sells > h2',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
                ],
            ]
        );

        $this->add_responsive_control(
            'skin_products_title_spacing',
            [
                'label' => esc_html__( 'Spacing', 'tenweb-builder' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem', 'custom' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [ 'px' => 0 ],
                'selectors' => [
                    '{{WRAPPER}}' => '--products-title-spacing: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->parent->end_injection();
    }

    public function register_variations_style() {
        $this->start_controls_section('skin_section_variations_style', [
            'label' => __('Variations', 'tenweb-builder'),
            'tab' => Controls_Manager::TAB_STYLE,
            'conditions' => [
                'relation' => 'or',
                'terms' => [
                    [
                        'relation' => 'and',
                        'terms' => [
                            [
                                'name' => '_skin',
                                'operator' => '===',
                                'value' => 'modern',
                            ],
                            [
                                'name' => $this->get_control_id('skin_variation_images'),
                                'operator' => '===',
                                'value' => 'yes',
                            ],
                        ],
                    ],
                    [
                        'relation' => 'and',
                        'terms' => [
                            [
                                'name' => '_skin',
                                'operator' => '===',
                                'value' => 'classic',
                            ],
                            [
                                'name' => 'classic_skin_product_images',
                                'operator' => '!==',
                                'value' => '',
                            ],
                        ],
                    ]
                ]
            ],
        ]);

        $this->add_responsive_control('skin_variations_spacing', [
            'label' => __('Spacing', 'tenweb-builder'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'em' ],
            'selectors' => [
                '{{WRAPPER}}.elementor-wc-products ul.products li.product .twbb-woocommerce-products-variations *' => 'margin-bottom: {{SIZE}}{{UNIT}}',
            ],
        ]);

        $this->add_responsive_control('skin_variations_gap', [
            'label' => __('Gap Between', 'tenweb-builder'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'em' ],
            'default' => [
                'size' => 10,
                'unit' => 'px',
            ],
            'selectors' => [
                '{{WRAPPER}}.elementor-wc-products ul.products li.product .twbb-woocommerce-products-variations' => 'gap: {{SIZE}}{{UNIT}}',
            ],
        ]);

        $this->add_responsive_control('skin_variation_image_width', [
            'label' => __('Image Width', 'tenweb-builder'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'em' ],
            'default' => [
                'size' => 40,
                'unit' => 'px',
            ],
            'selectors' => [
                '{{WRAPPER}}.elementor-wc-products ul.products li.product .twbb-woocommerce-products-variations .variation-image' => 'width: {{SIZE}}{{UNIT}}',
            ],
        ]);

        $this->add_responsive_control('skin_variation_image_height', [
            'label' => __('Image Height', 'tenweb-builder'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'em' ],
            'default' => [
                'size' => 40,
                'unit' => 'px',
            ],
            'selectors' => [
                '{{WRAPPER}}.elementor-wc-products ul.products li.product .twbb-woocommerce-products-variations .variation-image' => 'height: {{SIZE}}{{UNIT}}',
            ],
        ]);

        $this->add_control('skin_variations_number_color', [
            'label' => __('Color', 'tenweb-builder'),
            'type' => Controls_Manager::COLOR,
            'global' => [
                'default' => Global_Colors::COLOR_PRIMARY,
            ],
            'selectors' => [
                '{{WRAPPER}}.elementor-wc-products ul.products li.product .twbb-woocommerce-products-variations .twbb-additional-variations' => 'color: {{VALUE}}',
            ],
        ]);

        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name' => 'skin_variations_typography',
            'global' => [
                'default' => Global_Typography::TYPOGRAPHY_TEXT,
            ],
            'selector' => '{{WRAPPER}}.elementor-wc-products ul.products li.product .twbb-woocommerce-products-variations .twbb-additional-variations',
        ]);

        $this->end_controls_section();
    }

	public function get_container_class() {
		return 'elementor-posts--skin-' . $this->get_id();
	}

    public function remove_add_to_cart( $string ) {
        return '';
    }
    public function quantity_add_to_cart( $string  ) {
        if( count( $this->getProductVariations() ) > 0 ) {
            return $string;
        }
        $string = '<div class="twbb-product-loop-buttons"><div class="twbb-product-quantity-container"><span class="twbb-minus-quantity twbb-product-quantity-change">-</span>' .
            '<input class="twbb-product-quantity-input" type="number" min="1" value="1">' .
            '<span class="twbb-plus-quantity twbb-product-quantity-change">+</span></div><div class="twbb_add_to_cart_cont">' . $string.'</div></div>';
        return $string;
    }

    public function render() {
        if ( WC()->session ) {
            wc_print_notices();
        }
        $settings = $this->parent->get_settings_for_display();

        $skin = $this->parent->get_current_skin();
        $current_skin = $skin->get_id();
        $this->parent->twbb_current_skin = $current_skin . '_skin_';
        /* Start 10web customize */
        add_action( 'woocommerce_before_shop_loop_item_title', [$this->parent,'before_image_open_tag'], 9 );

        // Remove the default product thumbnail
        remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);
        // Add your custom product thumbnail function
        add_action('woocommerce_before_shop_loop_item_title', [$this->parent, 'replace_main_image_template'], 10);

        add_action( 'woocommerce_before_shop_loop_item_title', [$this->parent,'after_image_close_tag'], 11 );

        $this->parent->change_buttons_texts();
        /* End 10web customize */

        $post_type_setting = $settings[ Products_Renderer::QUERY_CONTROL_NAME . '_post_type' ];

        $this->add_filters();

        if ( 'related_products' === $post_type_setting ) {
            $content = Woocommerce::get_products_related_content( $settings );
        } elseif ( 'upsells' === $post_type_setting ) {
            $content = Woocommerce::get_upsells_content( $settings );
        } elseif ( 'cross_sells' === $post_type_setting ) {
            $content = Woocommerce::get_cross_sells_content( $settings );
        } else {
            // For Products_Renderer.
            if ( ! isset( $GLOBALS['post'] ) ) {
                $GLOBALS['post'] = null; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
            }

            $this->parent->get_products_min_max_prices();
            $this->parent->set_wc_thumbnail_single_image_width();
            $shortcode = $this->get_shortcode_object( $settings );
            $content = $shortcode->get_content();
        }


        if ( $content ) {
            $content = str_replace( '<ul class="products', '<ul class="products elementor-grid', $content );

            if( $this->parent->get_settings()['ajax_paginate'] === '' ) {
                echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            } else {
                echo '<div class="twbb_woocommerce-products-ajax-paginate">' . $content . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            }
        } elseif ( \Elementor\Plugin::instance()->editor->is_edit_mode() ||
            (!empty($_GET['twbb_template_preview']) && !empty($_GET['twbb_template_preview_from']) && !empty($_GET['twbb_template_preview_nonce'])) //phpcs:ignore WordPress.Security.NonceVerification.Recommended
        ) {
            $this->handle_no_products();
        } elseif( !\Elementor\Plugin::instance()->editor->is_edit_mode() ) {
            $this->handle_no_posts_found_preview();
        }
        $this->remove_added_filters();
    }

    public function get_shortcode_object( $settings ) {
        if ( 'current_query' === $settings[ Products_Renderer::QUERY_CONTROL_NAME . '_post_type' ] ) {
            return new Current_Query_Renderer( $settings, 'current_query',  $this->get_control_id('skin_') );
        }

        return new Products_Renderer( $settings, 'products',  $this->get_control_id('skin_'));
    }

    protected function getProductVariations() {
        global $product;
        $product_variable = new WC_Product_Variable( $product->get_id() );
        $variations_all = $product_variable->get_available_variations();
        return $variations_all;
    }

    protected function add_filters() {
    }

    protected function remove_added_filters() {
        remove_action( 'woocommerce_before_shop_loop_item_title', [$this->parent,'before_image_open_tag'], 9 );
        remove_action('woocommerce_before_shop_loop_item_title', [$this->parent, 'replace_main_image_template'], 10);
        remove_action( 'woocommerce_before_shop_loop_item_title', [$this->parent,'after_image_close_tag'], 11 );
    }

	public function slider_wrapper_start($woocommerce_product_loop_start) {
		$settings = $this->parent->get_settings();
		$settings['space_between'] = $settings[$this->get_control_id('skin_') . 'column_gap'];
		$settings['space_between_tablet'] = $settings[$this->get_control_id('skin_') . 'column_gap'];
		$settings['space_between_mobile'] = $settings[$this->get_control_id('skin_') . 'column_gap'];
		$items_count = $settings[$this->get_control_id('skin_') . 'columns'] * $settings[$this->get_control_id('skin_') . 'rows'];
		$this->parent->add_render_attribute('tenweb-slider-view-type', ['class' => 'products']);
		$this->parent->add_render_attribute( 'tenweb-slider-view-type', Widget_Slider::get_slider_attributes($settings, $items_count, $this->get_control_id('skin_') . 'columns') );
		$woocommerce_product_loop_start = preg_replace_callback('/<(\w+)([^>]*)>/', function ($matches) {
			$tag = $matches[1];
			return '<' . $tag . ' ' . $this->parent->get_render_attribute_string('tenweb-slider-view-type') . '>';
		}, $woocommerce_product_loop_start);

		ob_start();
		Widget_Slider::slider_wrapper_start();

		return $woocommerce_product_loop_start . ob_get_clean();
	}

	public function slider_wrapper_end($woocommerce_product_loop_end) {
		$settings = $this->parent->get_settings();
		$items_count = $settings[$this->get_control_id('skin_') . 'columns'] * $settings[$this->get_control_id('skin_') . 'rows'];
        $arrows_icon = isset($settings['arrows_icon']) ? $settings['arrows_icon'] : 'arrow2';
        ob_start();
		Widget_Slider::slider_wrapper_end(['items_count' => $items_count, 'arrows_icon' => $arrows_icon]);
		return ob_get_clean() . $woocommerce_product_loop_end;
	}

	public function slider_item_class($classes) {
		$classes[] = Widget_Slider::ITEM_CLASS;
		return $classes;
	}

    protected function handle_no_posts_found_preview() {
        $args = [
            'title' => 'No Products Found',
            'desc' => 'There are currently no products to display.',
        ];
        \Tenweb_Builder\Modules\Utils::handleArchiveNoContentPreviewRender($args);
    }

    private function handle_no_products() {
        $args = [
            'mobile_desc' => 'This is a preview of what your future product list will look like. You haven’t created any products yet. This view will not be visible on your live website.',
            'desktop_desc' => 'This is a preview of what your future product list will look like. You haven’t created any products yet.<br>This view will not be visible on your live website.',
            'el_count' => 3,
        ];
        \Tenweb_Builder\Modules\Utils::handleArchiveNoContentRender($args);
    }
}

