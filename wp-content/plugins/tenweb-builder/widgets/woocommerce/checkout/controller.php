<?php
namespace Tenweb_Builder\Widgets\Woocommerce\Widgets;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Repeater;
use Elementor\Group_Control_Background;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Checkout extends Base_Widget {

    private $reformatted_form_fields;

	public function get_name() {
		return TWBB_PREFIX . '_woocommerce-checkout-page';
	}

	public function get_title() {
		return esc_html__( 'Checkout', 'tenweb-builder' );
	}

	public function get_icon() {
		return 'twbb-checkout twbb-widget-icon';
	}

	public function get_keywords() {
		return [ 'woocommerce', 'checkout' ];
	}

	public function get_script_depends() {
		return [
			'wc-checkout',
			'wc-password-strength-meter',
			'selectWoo',
		];
	}

	public function get_style_depends() {
		return [ 'select2' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'General', 'tenweb-builder' ),
			]
		);

		$this->add_control(
			'checkout_layout',
			[
				'label' => esc_html__( 'Layout', 'tenweb-builder' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'two-column' => esc_html__( 'Two columns', 'tenweb-builder' ),
					'one-column' => esc_html__( 'One column', 'tenweb-builder' ),
				],
				'default' => 'two-column',
				'prefix_class' => 'e-checkout-layout-',
			]
		);

		$this->add_control(
			'sticky_right_column',
			[
				'label' => esc_html__( 'Sticky Right Column', 'tenweb-builder' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'tenweb-builder' ),
				'label_off' => esc_html__( 'No', 'tenweb-builder' ),
				'return_value' => 'yes',
				'description' => esc_html__( 'The Order Summary and Payment sections will remain in place while scrolling.', 'tenweb-builder' ),
				'frontend_available' => true,
				'render_type' => 'none',
				'condition' => [
					'checkout_layout' => 'two-column',
				],
			]
		);

		$this->add_control(
			'sticky_right_column_offset',
			[
				'label' => esc_html__( 'Offset', 'tenweb-builder' ),
				'type' => Controls_Manager::NUMBER,
				'frontend_available' => true,
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'sticky_right_column',
							'operator' => '!==',
							'value' => '',
						],
						[
							'name' => 'checkout_layout',
							'operator' => '=',
							'value' => 'two-column',
						],
					],
				],
			]
		);

        $this->add_control(
            'forms_label_title',
            [
                'type' => Controls_Manager::HEADING,
                'label' => esc_html__( 'Billing & Shipping labels', 'tenweb-builder' ),
            ]
        );

        $this->add_control(
            'hide_field_labels',
            [
                'label' => esc_html__( 'Hide Field Labels', 'tenweb-builder' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __( 'Yes', 'tenweb-builder' ),
                'label_off' => __( 'No', 'tenweb-builder' ),
                'return_value' => 'yes',
                'default' => 'yes',
                'prefix_class' => 'twbb-hide-label-',
            ]
        );


        $this->end_controls_section();

		if ( $this->is_wc_feature_active( 'checkout_login_reminder' ) ) {
			$this->add_checkout_login_reminder_controls();
		}

		$this->start_controls_section(
			'billing_details_section',
			[
				'label' => $this->is_wc_feature_active( 'ship_to_billing_address_only' ) ? esc_html__( 'Billing and Shipping Details', 'tenweb-builder' ) : esc_html__( 'Billing Details', 'tenweb-builder' ),
			]
		);

		$this->add_control(
			'billing_details_section_title',
			[
				'label' => esc_html__( 'Section Title', 'tenweb-builder' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => $this->is_wc_feature_active( 'ship_to_billing_address_only' ) ? esc_html__( 'Billing and Shipping Details', 'tenweb-builder' ) : esc_html__( 'Billing Details', 'tenweb-builder' ),
				'default' => $this->is_wc_feature_active( 'ship_to_billing_address_only' ) ? esc_html__( 'Billing and Shipping Details', 'tenweb-builder' ) : esc_html__( 'Billing Details', 'tenweb-builder' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_responsive_control(
			'billing_details_alignment',
			[
				'label' => esc_html__( 'Alignment', 'tenweb-builder' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'start' => [
						'title' => esc_html__( 'Start', 'tenweb-builder' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'tenweb-builder' ),
						'icon' => 'eicon-text-align-center',
					],
					'end' => [
						'title' => esc_html__( 'End', 'tenweb-builder' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--billing-details-title-alignment: {{VALUE}};',
				],
			]
		);

		$repeater = new Repeater();

		$repeater->start_controls_tabs( 'tabs', [
			'condition' => [
				'repeater_state' => '',
			],
		] );

		$repeater->start_controls_tab( 'content_tab', [
			'label' => esc_html__( 'Content', 'tenweb-builder' ),
		] );

		$repeater->add_control(
			'label',
			[
				'label' => esc_html__( 'Label', 'tenweb-builder' ),
				'type' => Controls_Manager::TEXT,
			]
		);

		$repeater->add_control(
			'placeholder',
			[
				'label' => esc_html__( 'Placeholder', 'tenweb-builder' ),
				'type' => Controls_Manager::TEXT,
			]
		);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab( 'advanced_tab', [
			'label' => esc_html__( 'Advanced', 'tenweb-builder' ),
		] );

		$repeater->add_control(
			'default',
			[
				'label' => esc_html__( 'Default Value', 'tenweb-builder' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$repeater->end_controls_tab();

		$repeater->end_controls_tabs();

		$repeater->add_control(
			'repeater_state',
			[
				'label' => esc_html__( 'Repeater State - hidden', 'tenweb-builder' ),
				'type' => Controls_Manager::HIDDEN,
			]
		);

		$repeater->add_control(
			'locale_notice',
			[
				'raw' => __( 'Note: This content cannot be changed due to local regulations.', 'tenweb-builder' ),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-descriptor',
				'condition' => [
					'repeater_state' => 'locale',
				],
			]
		);

		$repeater->add_control(
			'from_billing_notice',
			[
				'raw' => __( 'Note: This label and placeholder are taken from the Billing section. You can change it there.', 'tenweb-builder' ),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-descriptor',
				'condition' => [
					'repeater_state' => 'from_billing',
				],
			]
		);

		$this->add_control(
			'billing_details_form_fields',
			[
				'label' => esc_html__( 'Form Items', 'tenweb-builder' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'item_actions' => [
					'add' => false,
					'duplicate' => false,
					'remove' => false,
					'sort' => false,
				],
				'default' => $this->get_billing_field_defaults(),
				'title_field' => '{{{ label }}}', // phpcs:ignore WordPressVIPMinimum.Security.Mustache.OutputNotation
			]
		);

		$this->end_controls_section();

		if ( $this->is_wc_feature_active( 'shipping' ) && ! $this->is_wc_feature_active( 'ship_to_billing_address_only' ) ) {
			$this->add_shipping_controls();
		}

		$this->start_controls_section(
			'additional_information_section',
			[
				'label' => esc_html__( 'Additional Information', 'tenweb-builder' ),
			]
		);

		$this->add_control(
			'additional_information_active',
			[
				'label' => esc_html__( 'Additional Information', 'tenweb-builder' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'tenweb-builder' ),
				'label_off' => esc_html__( 'Hide', 'tenweb-builder' ),
				'default' => 'yes',
                'render_type' => 'template',
				'selectors' => [
					'{{WRAPPER}}' => '--additional-information-display: block;',
				],
			]
		);

		if ( $this->is_wc_feature_active( 'ship_to_billing_address_only' ) ) {
			$this->add_control(
				'additional_information_section_title',
				[
					'label' => esc_html__( 'Section Title', 'tenweb-builder' ),
					'type' => Controls_Manager::TEXT,
					'placeholder' => esc_html__( 'Additional Information', 'tenweb-builder' ),
					'default' => esc_html__( 'Additional Information', 'tenweb-builder' ),
					'dynamic' => [
						'active' => true,
					],
					'condition' => [
						'additional_information_active!' => '',
					],
				]
			);

			$this->add_responsive_control(
				'additional_information_alignment',
				[
					'label' => esc_html__( 'Alignment', 'tenweb-builder' ),
					'type' => Controls_Manager::CHOOSE,
					'options' => [
						'start' => [
							'title' => esc_html__( 'Start', 'tenweb-builder' ),
							'icon' => 'eicon-text-align-left',
						],
						'center' => [
							'title' => esc_html__( 'Center', 'tenweb-builder' ),
							'icon' => 'eicon-text-align-center',
						],
						'end' => [
							'title' => esc_html__( 'End', 'tenweb-builder' ),
							'icon' => 'eicon-text-align-right',
						],
					],
					'selectors' => [
						'{{WRAPPER}}' => '--additional-fields-title-alignment: {{VALUE}};',
					],
					'condition' => [
						'additional_information_active!' => '',
					],
				]
			);
		}

		$repeater = new Repeater();

		$repeater->start_controls_tabs( 'additional_information_form_fields_tabs' );

		$repeater->start_controls_tab( 'additional_information_form_fields_content_tab', [
			'label' => esc_html__( 'Content', 'tenweb-builder' ),
		] );

		$repeater->add_control(
			'label',
			[
				'label' => esc_html__( 'Label', 'tenweb-builder' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'placeholder',
			[
				'label' => esc_html__( 'Placeholder', 'tenweb-builder' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab( 'additional_information_form_fields_advanced_tab', [
			'label' => esc_html__( 'Advanced', 'tenweb-builder' ),
		] );

		$repeater->add_control(
			'default',
			[
				'label' => esc_html__( 'Default Value', 'tenweb-builder' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$repeater->end_controls_tab();

		$repeater->end_controls_tabs();

		$this->add_control(
			'additional_information_form_fields',
			[
				'label' => esc_html__( 'Items', 'tenweb-builder' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'item_actions' => [
					'add' => false,
					'duplicate' => false,
					'remove' => false,
					'sort' => false,
				],
				'default' => [
					[
						'field_key' => 'order_comments',
						'field_label' => esc_html__( 'Order Notes', 'tenweb-builder' ),
						'label' => esc_html__( 'Order Notes', 'tenweb-builder' ),
						'placeholder' => esc_html__( 'Notes about your order, e.g. special notes for delivery.', 'tenweb-builder' ),
					],
				],
				'title_field' => '{{{ label }}}', // phpcs:ignore WordPressVIPMinimum.Security.Mustache.OutputNotation
				'condition' => [
					'additional_information_active!' => '',
				],
			]
		);

		$this->end_controls_section();

		if ( $this->is_wc_feature_active( 'signup_and_login_from_checkout' ) ) {
			$this->add_signup_and_login_from_checkout_controls();
		}

		$this->start_controls_section(
			'order_summary_section',
			[
				'label' => esc_html__( 'Your Order', 'tenweb-builder' ),
			]
		);

		$this->add_control(
			'order_summary_section_title',
			[
				'label' => esc_html__( 'Section Title', 'tenweb-builder' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Your Order', 'tenweb-builder' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_responsive_control(
			'order_summary_alignment',
			[
				'label' => esc_html__( 'Alignment', 'tenweb-builder' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'start' => [
						'title' => esc_html__( 'Start', 'tenweb-builder' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'tenweb-builder' ),
						'icon' => 'eicon-text-align-center',
					],
					'end' => [
						'title' => esc_html__( 'End', 'tenweb-builder' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--order-review-title-alignment: {{VALUE}};',
				],
			]
		);

        $this->add_control(
            'order_summary_hide_image',
            [
                'label' => esc_html__( 'Hide Images', 'tenweb-builder' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __( 'Yes', 'tenweb-builder' ),
                'label_off' => __( 'No', 'tenweb-builder' ),
                'return_value' => 'yes',
                'default' => '',
                'prefix_class' => 'twbb-order-summery-hide-image-',
            ]
        );


        $this->end_controls_section();

		if ( $this->is_wc_feature_active( 'coupons' ) ) {
			$this->add_coupon_controls();
		}

		$this->start_controls_section(
			'payment_section',
			[
				'label' => esc_html__( 'Payment', 'tenweb-builder' ),
			]
		);

        $this->add_control(
            'payment_section_title',
            [
                'label' => esc_html__( 'Section Title', 'tenweb-builder' ),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__( 'Payments', 'tenweb-builder' ),
            ]
        );


        $this->add_control(
			'terms_conditions_heading',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Terms &amp; Conditions', 'tenweb-builder' ),
			]
		);

		$this->add_control(
			'terms_conditions_message_text',
			[
				'label' => esc_html__( 'Message', 'tenweb-builder' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'default' => esc_html__( 'I have read and agree to the website', 'tenweb-builder' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'terms_conditions_link_text',
			[
				'label' => esc_html__( 'Link Text', 'tenweb-builder' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'default' => esc_html__( 'terms and conditions', 'tenweb-builder' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'purchase_buttom_heading',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Purchase Button', 'tenweb-builder' ),
			]
		);

        $this->add_control(
            'purchase_button_text',
            [
                'label' => __( 'Purchase Buttom Text', 'plugin-domain' ),
                'label_block' => true,
                'type' => Controls_Manager::TEXT,
                'default' => __( 'Pay Now', 'tenweb-builder' ),
            ]
        );

		$this->add_responsive_control(
			'purchase_button_alignment',
			[
				'label' => esc_html__( 'Alignment', 'tenweb-builder' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'start' => [
						'title' => esc_html__( 'Start', 'tenweb-builder' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'tenweb-builder' ),
						'icon' => 'eicon-text-align-center',
					],
					'end' => [
						'title' => esc_html__( 'End', 'tenweb-builder' ),
						'icon' => 'eicon-text-align-right',
					],
					'justify' => [
						'title' => esc_html__( 'Justify', 'tenweb-builder' ),
						'icon' => 'eicon-text-align-justify',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .woocommerce-checkout' => '{{VALUE}}',
				],
				'selectors_dictionary' => [
					'start' => '--place-order-title-alignment: flex-start; --purchase-button-width: fit-content;',
					'center' => '--place-order-title-alignment: center; --purchase-button-width: fit-content;',
					'end' => '--place-order-title-alignment: flex-end; --purchase-button-width: fit-content;',
					'justify' => '--place-order-title-alignment: stretch; --purchase-button-width: 100%;',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_checkout_tabs_style',
			[
				'label' => esc_html__( 'Sections', 'tenweb-builder' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'sections_background_color',
			[
				'label' => esc_html__( 'Background Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => '--sections-background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'section_normal_box_shadow',
				'selector' => $this->get_main_woocommerce_sections_selectors(),
			]
		);

		$this->add_control(
			'sections_border_type',
			[
				'label' => esc_html__( 'Border Type', 'tenweb-builder' ),
				'type' => Controls_Manager::SELECT,
				'options' => $this->get_custom_border_type_options(),
				'selectors' => [
					'{{WRAPPER}}' => '--sections-border-type: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'sections_border_width',
			[
				'label' => esc_html__( 'Width', 'tenweb-builder' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'selectors' => [
					$this->get_main_woocommerce_sections_selectors() => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'sections_border_type!' => 'none',
				],
			]
		);

		$this->add_control(
			'sections_border_color',
			[
				'label' => esc_html__( 'Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => '--sections-border-color: {{VALUE}};',
				],
				'condition' => [
					'sections_border_type!' => 'none',
				],
			]
		);

		$this->add_responsive_control(
			'sections_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'tenweb-builder' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'selectors' => [
					'{{WRAPPER}}' => '--sections-border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'sections_padding',
			[
				'label' => esc_html__( 'Padding', 'tenweb-builder' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'selectors' => [
					'{{WRAPPER}}' => '--sections-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					// move the 'Ship to a different address?' checkbox
					'{{WRAPPER}} .woocommerce-shipping-fields' => '--shipping-heading-padding-start: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'sections_margin',
			[
				'label' => esc_html__( 'Margin', 'tenweb-builder' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'selectors' => [
					'{{WRAPPER}}' => '--sections-margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_checkout_tabs_typography',
			[
				'label' => esc_html__( 'Typography', 'tenweb-builder' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'sections_typography',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Titles', 'tenweb-builder' ),
			]
		);

		$this->add_control(
			'sections_title_color',
			[
				'label' => esc_html__( 'Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => '--sections-title-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'sections_titles_typography',
				'selector' => $this->get_main_woocommerce_sections_title_selectors(),
                'fields_options' => [
                    'font_size' => [
                        'default' => [
                            'size' => 'twbb_h5',
                        ],
                    ],
                ],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'sections_titles_text_shadow',
				'selector' => $this->get_main_woocommerce_sections_title_selectors(),
			]
		);

		$this->add_responsive_control(
			'sections_title_spacing',
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
					'{{WRAPPER}}' => '--sections-title-spacing: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'sections_secondary_typography',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Secondary Titles', 'tenweb-builder' ),
				'separator' => 'before',
			]
		);

		$this->add_control(
			'sections_secondary_title_color',
			[
				'label' => esc_html__( 'Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => '--sections-secondary-title-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'sections_secondary_titles_typography',
				'selector' => '{{WRAPPER}} .e-checkout-secondary-title',
			]
		);

		$this->add_responsive_control(
			'sections_secondary_title_spacing',
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
					'{{WRAPPER}}' => '--sections-secondary-title-spacing: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'sections_descriptions_title',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Descriptions', 'tenweb-builder' ),
				'separator' => 'before',
			]
		);

		$this->add_control(
			'sections_descriptions_color',
			[
				'label' => esc_html__( 'Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => '--sections-descriptions-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'sections_descriptions_typography',
				'selector' => '{{WRAPPER}} .e-description',
			]
		);

		$this->add_responsive_control(
			'sections_descriptions_spacing',
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
					'{{WRAPPER}}' => '--sections-descriptions-spacing: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'sections_messages_title',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Messages', 'tenweb-builder' ),
				'separator' => 'before',
			]
		);

		$this->add_control(
			'sections_messages_color',
			[
				'label' => esc_html__( 'Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => '--sections-messages-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'sections_messages_typography',
				'selector' => '{{WRAPPER}} .woocommerce-checkout #payment .payment_box, {{WRAPPER}} .woocommerce-privacy-policy-text p, {{WRAPPER}} .e-checkout-message',
			]
		);

		$this->add_control(
			'sections_checkboxes_title',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Checkboxes', 'tenweb-builder' ),
				'separator' => 'before',
			]
		);

		$this->add_control(
			'sections_checkboxes_color',
			[
				'label' => esc_html__( 'Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => '--sections-checkboxes-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'sections_checkboxes_typography',
				'selector' => '{{WRAPPER}} .woocommerce-form__label-for-checkbox span',
			]
		);

		$this->add_control(
			'sections_radio_buttons_title',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Radio Buttons', 'tenweb-builder' ),
				'separator' => 'before',
			]
		);

		$this->add_control(
			'sections_radio_buttons_color',
			[
				'label' => esc_html__( 'Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => '--sections-radio-buttons-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'sections_radio_buttons_typography',
				'selector' => '{{WRAPPER}} .wc_payment_method label, {{WRAPPER}} #shipping_method li label',
			]
		);

		// Links
		$this->add_control(
			'sections_links_title',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Links', 'tenweb-builder' ),
				'separator' => 'before',
			]
		);

		$this->start_controls_tabs( 'links_colors' );

		$this->start_controls_tab( 'links_normal_colors', [
			'label' => esc_html__( 'Normal', 'tenweb-builder' ),
		] );

		$this->add_control(
			'links_normal_color',
			[
				'label' => esc_html__( 'Link Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => '--links-normal-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'links_hover_colors', [
			'label' => esc_html__( 'Hover', 'tenweb-builder' ),
		] );

		$this->add_control(
			'links_hover_color',
			[
				'label' => esc_html__( 'Link Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => '--links-hover-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_checkout_tabs_forms',
			[
				'label' => esc_html__( 'Forms', 'tenweb-builder' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'forms_columns_gap',
			[
				'label' => esc_html__( 'Columns Gap', 'tenweb-builder' ),
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
					'{{WRAPPER}}' => '--forms-columns-gap-padding: calc( {{SIZE}}{{UNIT}}/2 ); --forms-columns-gap-margin: calc( -{{SIZE}}{{UNIT}}/2 );',
				],
			]
		);

		$this->add_responsive_control(
			'forms_rows_gap',
			[
				'label' => esc_html__( 'Rows Gap', 'tenweb-builder' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 60,
					],
				],
				'default' => [ 'px' => 0 ],
				'selectors' => [
					'{{WRAPPER}}' => '--forms-rows-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'forms_label_title',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Labels', 'tenweb-builder' ),
			]
		);


        $this->add_control(
			'forms_label_color',
			[
				'label' => esc_html__( 'Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => '--forms-labels-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'forms_label_typography',
				'selector' => '{{WRAPPER}} .woocommerce-billing-fields .form-row label, {{WRAPPER}} .woocommerce-shipping-fields .form-row label, {{WRAPPER}} .woocommerce-additional-fields .form-row label, {{WRAPPER}} .e-woocommerce-login-anchor .form-row label, {{WRAPPER}} .e-coupon-anchor-description',
			]
		);

		$this->add_responsive_control(
			'forms_label_spacing',
			[
				'label' => esc_html__( 'Spacing', 'tenweb-builder' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 60,
					],
				],
				'default' => [ 'px' => 0 ],
				'selectors' => [
					'{{WRAPPER}}' => '--forms-label-spacing: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'forms_field_title',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Fields', 'tenweb-builder' ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'forms_field_typography',
				'selector' => '{{WRAPPER}} #customer_details .input-text, {{WRAPPER}} #customer_details .form-row textarea, {{WRAPPER}} #customer_details .form-row select, {{WRAPPER}} .e-woocommerce-login-anchor .input-text, {{WRAPPER}} #coupon_code, {{WRAPPER}} ::placeholder, {{WRAPPER}} .select2-container--default .select2-selection--single, {{WRAPPER}} .select2-container--default .select2-selection--single .select2-selection__rendered, .select2-results__option',
			]
		);

		$this->start_controls_tabs( 'forms_fields_styles' );

		$this->start_controls_tab( 'forms_fields_normal_styles', [
			'label' => esc_html__( 'Normal', 'tenweb-builder' ),
		] );

		$this->add_control(
			'forms_fields_normal_color',
			[
				'label' => esc_html__( 'Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => '--forms-fields-normal-color: {{VALUE}};',
					'.e-woo-select2-wrapper .select2-results__option' => 'color: {{VALUE}};',
					// style select2 arrow
					'{{WRAPPER}} .select2-container--default .select2-selection--single .select2-selection__arrow b' => 'border-color: {{VALUE}} transparent transparent transparent;',
				],
			]
		);

		$this->add_control(
			'forms_fields_normal_color_placeholder',
			[
				'label' => esc_html__( 'Placeholder Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => '--forms-fields-normal-color-placeholder: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'forms_fields_normal_background',
				'selector' => '{{WRAPPER}} .woocommerce #customer_details .form-row .input-text, {{WRAPPER}}  .woocommerce #customer_details .form-row textarea, {{WRAPPER}} .woocommerce form #customer_details select, {{WRAPPER}} .woocommerce .e-woocommerce-login-anchor .form-row .input-text, {{WRAPPER}} #coupon_code, {{WRAPPER}} .select2-container--default .select2-selection--single, {{WRAPPER}} .woocommerce-checkout #payment .payment_methods .payment_box',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'forms_fields_normal_box_shadow',
				'selector' => '{{WRAPPER}} #customer_details .input-text, {{WRAPPER}}  #customer_details .form-row textarea, {{WRAPPER}} .woocommerce form #customer_details select, {{WRAPPER}} .e-woocommerce-login-anchor .input-text, {{WRAPPER}} #coupon_code, {{WRAPPER}} .select2-container--default .select2-selection--single',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'forms_fields_focus_styles', [
			'label' => esc_html__( 'Focus', 'tenweb-builder' ),
		] );

		$this->add_control(
			'forms_fields_focus_color',
			[
				'label' => esc_html__( 'Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => '--forms-fields-focus-color: {{VALUE}}',
					'.e-woo-select2-wrapper .select2-results__option:focus' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'forms_fields_focus_background',
				'selector' => '{{WRAPPER}} .woocommerce #customer_details .form-row .input-text:focus, {{WRAPPER}}  .woocommerce #customer_details .form-row textarea:focus, {{WRAPPER}} #customer_details select:focus, {{WRAPPER}} .woocommerce .e-woocommerce-login-anchor .form-row .input-text:focus, {{WRAPPER}} #coupon_code:focus, {{WRAPPER}} .select2-container--default .select2-selection--single:focus',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'forms_fields_focus_box_shadow',
				'selector' => '{{WRAPPER}} #customer_details .input-text:focus, {{WRAPPER}} #customer_details textarea:focus, {{WRAPPER}} #customer_details select:focus, {{WRAPPER}} .e-woocommerce-login-anchor .input-text:focus, {{WRAPPER}} #coupon_code:focus, {{WRAPPER}} .select2-container--default .select2-selection--single:focus',
			]
		);

		$this->add_control(
			'forms_fields_focus_border_color',
			[
				'label' => esc_html__( 'Border Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce #customer_details .form-row .input-text:focus, {{WRAPPER}}  .woocommerce #customer_details .form-row textarea:focus, {{WRAPPER}} #customer_details select:focus, {{WRAPPER}} .woocommerce .e-woocommerce-login-anchor .form-row .input-text:focus, {{WRAPPER}} #coupon_code:focus, {{WRAPPER}} .select2-container--default .select2-selection--single:focus' => 'border-color: {{VALUE}}',
				],
				'condition' => [
					'forms_fields_border_border!' => '',
				],
			]
		);

		$this->add_control(
			'forms_fields_focus_transition_duration',
			[
				'label' => esc_html__( 'Transition Duration', 'tenweb-builder' ) . ' (ms)',
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}}' => '--forms-fields-focus-transition-duration: {{SIZE}}ms',
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 3000,
					],
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'forms_fields_border',
				'selector' => '{{WRAPPER}} .woocommerce #customer_details .form-row .input-text, {{WRAPPER}}  .woocommerce #customer_details .form-row textarea, {{WRAPPER}} .woocommerce form #customer_details select, {{WRAPPER}} .woocommerce .e-woocommerce-login-anchor .form-row .input-text, {{WRAPPER}} #coupon_code, {{WRAPPER}} .select2-container--default .select2-selection--single',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'forms_fields_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'tenweb-builder' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'selectors' => [
					'{{WRAPPER}}' => '--forms-fields-border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'forms_fields_padding',
			[
				'label' => esc_html__( 'Padding', 'tenweb-builder' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'selectors' => [
					'{{WRAPPER}}' => '--forms-fields-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					// style select2
					'{{WRAPPER}} .select2-container--default .select2-selection--single' => 'height: auto;',
				],
				'separator' => 'after',
			]
		);

		$this->add_control(
			'forms_button_title',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Button', 'tenweb-builder' ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'forms_button_typography',
				'selector' => '{{WRAPPER}} .woocommerce-button',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'forms_button_text_shadow',
				'selector' => '{{WRAPPER}} .woocommerce-button',
			]
		);

		$this->start_controls_tabs( 'forms_buttons_styles' );

		$this->start_controls_tab( 'forms_buttons_normal_styles', [
			'label' => esc_html__( 'Normal', 'tenweb-builder' ),
		] );

		$this->add_control(
			'forms_buttons_normal_text_color',
			[
				'label' => esc_html__( 'Text Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => '--forms-buttons-normal-text-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'forms_buttons_normal_background',
				'selector' => '{{WRAPPER}} .woocommerce-button',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'forms_buttons_normal_box_shadow',
				'selector' => '{{WRAPPER}} .woocommerce-button',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'forms_buttons_hover_styles', [
			'label' => esc_html__( 'Hover', 'tenweb-builder' ),
		] );

		$this->add_control(
			'forms_buttons_hover_text_color',
			[
				'label' => esc_html__( 'Text Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => '--forms-buttons-hover-text-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'forms_buttons_hover_background',
				'selector' => '{{WRAPPER}} .woocommerce-button:hover',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'forms_buttons_focus_box_shadow',
				'selector' => '{{WRAPPER}} .woocommerce-button:hover',
			]
		);

		$this->add_control(
			'forms_buttons_hover_border_color',
			[
				'label' => esc_html__( 'Border Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .e-apply-coupon:hover, {{WRAPPER}} .woocommerce-form-login__submit:hover' => 'border-color: {{VALUE}}',
				],
				'condition' => [
					'forms_buttons_border_type!' => 'none',
				],
			]
		);

		$this->add_control(
			'forms_buttons_hover_transition_duration',
			[
				'label' => esc_html__( 'Transition Duration', 'tenweb-builder' ) . ' (ms)',
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}}' => '--forms-buttons-hover-transition-duration: {{SIZE}}ms',
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 3000,
					],
				],
			]
		);

		$this->add_control(
			'forms_buttons_hover_animation',
			[
				'label' => esc_html__( 'Hover Animation', 'tenweb-builder' ),
				'type' => Controls_Manager::HOVER_ANIMATION,
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'forms_buttons_border_type',
			[
				'label' => esc_html__( 'Border Type', 'tenweb-builder' ),
				'type' => Controls_Manager::SELECT,
				'options' => $this->get_custom_border_type_options(),
				'selectors' => [
					'{{WRAPPER}}' => '--forms-buttons-border-type: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'forms_buttons_border_width',
			[
				'label' => esc_html__( 'Width', 'tenweb-builder' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .e-apply-coupon, {{WRAPPER}} .woocommerce-form-login__submit' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'forms_buttons_border_type!' => 'none',
				],
			]
		);

		$this->add_control(
			'forms_buttons_border_color',
			[
				'label' => esc_html__( 'Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' => '--forms-buttons-border-color: {{VALUE}};',
				],
				'condition' => [
					'forms_buttons_border_type!' => 'none',
				],
			]
		);

		$this->add_responsive_control(
			'forms_buttons_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'tenweb-builder' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'selectors' => [
					'{{WRAPPER}}' => '--forms-buttons-border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'forms_buttons_padding',
			[
				'label' => esc_html__( 'Padding', 'tenweb-builder' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .woocommerce-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; width: auto;',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_checkout_tabs_order_summary',
			[
				'label' => esc_html__( 'Order Summary', 'tenweb-builder' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'order_summary_rows_gap',
			[
				'label' => esc_html__( 'Rows Gap', 'tenweb-builder' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 60,
					],
				],
				'default' => [ 'px' => 0 ],
				'selectors' => [
					'{{WRAPPER}}' => '--order-summary-rows-gap-top: calc( {{SIZE}}{{UNIT}}/2 ); --order-summary-rows-gap-bottom: calc( {{SIZE}}{{UNIT}}/2 );',
				],
			]
		);

        /*-----------Product title ----------*/
		$this->add_control(
			'order_summary_product_title',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Product Title', 'tenweb-builder' ),
			]
		);

		$this->add_control(
			'order_summary_product_color',
			[
				'label' => esc_html__( 'Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => '--order-summary-product-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'order_summary_product_typography',
                'selector' => '{{WRAPPER}} .woocommerce-checkout-review-order-table .product-name .twbb-product-content span',
			]
		);

        /*-----------Product price ----------*/
		$this->add_control(
			'order_summary_product_price',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Product Price', 'tenweb-builder' ),
			]
		);

		$this->add_control(
			'order_summary_product_price_color',
			[
				'label' => esc_html__( 'Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => '--order-summary-product-price-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'order_summary_product_price_typography',
                'selector' => '{{WRAPPER}} .woocommerce-checkout-review-order-table .product-total .woocommerce-Price-amount.amount bdi',
			]
		);

        /*-----------Shipping rate price ----------*/
        $this->add_control(
            'order_summary_shipping_price',
            [
                'type' => Controls_Manager::HEADING,
                'label' => esc_html__( 'Shipping Rate', 'tenweb-builder' ),
            ]
        );

        $this->add_control(
            'order_summary_shipping_price_color',
            [
                'label' => esc_html__( 'Color', 'tenweb-builder' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}}' => '--order-summary-shipping-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'order_summary_shipping_price_typography',
                'selector' => '{{WRAPPER}} .woocommerce-checkout-review-order-table .woocommerce-shipping-totals .woocommerce-Price-amount.amount bdi,
                {{WRAPPER}} .woocommerce-checkout-review-order-table .woocommerce-shipping-totals .woocommerce-Price-amount.amount,
                {{WRAPPER}} .woocommerce-checkout-review-order-table .woocommerce-shipping-totals .shipping-method-label',
            ]
        );

        /*----------Tax rate price ----------*/
        $this->add_control(
            'order_summary_tax_rate_price',
            [
                'type' => Controls_Manager::HEADING,
                'label' => esc_html__( 'Tax Rate', 'tenweb-builder' ),
            ]
        );

        $this->add_control(
            'order_summary_tax_rate_price_color',
            [
                'label' => esc_html__( 'Color', 'tenweb-builder' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}}' => '--order-summary-tax-rate-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'order_summary_tax_rate_price_typography',
                'selector' => '{{WRAPPER}} .woocommerce-checkout-review-order-table .tax-rate .woocommerce-Price-amount.amount',
            ]
        );

        /*----------Titles (Product, Subtotal, Shipping, Tax) ----------*/
        $this->add_control(
            'order_summary_items_titles',
            [
                'type' => Controls_Manager::HEADING,
                'label' => esc_html__( 'Titles', 'tenweb-builder' ),
            ]
        );

        $this->add_control(
            'order_summary_items_titles_color',
            [
                'label' => esc_html__( 'Color', 'tenweb-builder' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}}' => '--order-summary-items-titles-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'order_summary_items_titles_typography',
                'selector' => '{{WRAPPER}}.elementor-widget-twbb_woocommerce-checkout-page .woocommerce-checkout-review-order-table thead tr th, 
                {{WRAPPER}}.elementor-widget-twbb_woocommerce-checkout-page .woocommerce-checkout-review-order-table .woocommerce-shipping-totals.twbb-shipping-totals-title th, 
                {{WRAPPER}}.elementor-widget-twbb_woocommerce-checkout-page .woocommerce-checkout-review-order-table .tax-rate th,
                {{WRAPPER}}.elementor-widget-twbb_woocommerce-checkout-page .woocommerce-checkout-review-order-table tfoot .cart-subtotal th',
            ]
        );

        /*---------- Subtotal (price) ----------*/
        $this->add_control(
            'order_summary_subtotal_price',
            [
                'type' => Controls_Manager::HEADING,
                'label' => esc_html__( 'Subtotal Price', 'tenweb-builder' ),
            ]
        );

        $this->add_control(
            'order_summary_subtotal_price_color',
            [
                'label' => esc_html__( 'Color', 'tenweb-builder' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}}' => '--order-summary-subtotal-price-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'order_summary_subtotal_price_typography',
                'selector' => '{{WRAPPER}} .woocommerce-checkout-review-order-table .cart-subtotal .woocommerce-Price-amount.amount bdi',
            ]
        );

        /*---------- Total (Name) ----------*/
        $this->add_control(
            'order_summary_total_title',
            [
                'type' => Controls_Manager::HEADING,
                'label' => esc_html__( 'Total Title', 'tenweb-builder' ),
            ]
        );

        $this->add_control(
            'order_summary_total_title_color',
            [
                'label' => esc_html__( 'Color', 'tenweb-builder' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}}' => '--order-summary-total-title-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'order_summary_total_title_typography',
                'selector' => '{{WRAPPER}} .woocommerce-checkout-review-order-table .order-total > th',
            ]
        );

        /*---------- Total (price) ----------*/
        $this->add_control(
            'order_summary_total_price',
            [
                'type' => Controls_Manager::HEADING,
                'label' => esc_html__( 'Total Price', 'tenweb-builder' ),
            ]
        );

        $this->add_control(
            'order_summary_total_price_color',
            [
                'label' => esc_html__( 'Color', 'tenweb-builder' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}}' => '--order-summary-total-price-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'order_summary_total_price_typography',
                'selector' => '{{WRAPPER}} .woocommerce-checkout-review-order-table .order-total .woocommerce-Price-amount.amount bdi',
            ]
        );

        /* Keeping controls for old users but hide it */

        $this->add_control(
            'order_summary_items_color',
            [
                'label' => esc_html__( 'Color', 'tenweb-builder' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}}' => '--order-summary-items-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'order_summary_items_typography',
                'selector' => '{{WRAPPER}} .woocommerce-checkout-review-order-table .cart_item td',
            ]
        );


        $this->add_control(
            'order_summary_totals_color',
            [
                'label' => esc_html__( 'Color', 'tenweb-builder' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}}' => '--order-summary-totals-color: {{VALUE}};',
                ],
                'render_type' => 'none', // Hides the control in the editor
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'order_summary_totals_typography',
                'selector' => '{{WRAPPER}} .woocommerce-checkout-review-order-table thead tr th, {{WRAPPER}} .woocommerce-checkout-review-order-table tfoot tr th, {{WRAPPER}} .woocommerce-checkout-review-order-table tfoot tr td',
                'render_type' => 'none', // Hides the control in the editor
            ]
        );

        /* End Keeping controls for ol users but hide it */



        $this->add_control(
			'order_summary_variations_title',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Variations', 'tenweb-builder' ),
			]
		);

		$this->add_control(
			'order_summary_variations_color',
			[
				'label' => esc_html__( 'Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => '--order-summary-variations-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'order_summary_variations_typography',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
				'selector' => '{{WRAPPER}} .product-name .variation',
			]
		);

		$this->add_control(
			'order_summary_items_divider_title',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Dividers', 'tenweb-builder' ),
			]
		);

		$this->add_control(
			'order_summary_items_divider_color',
			[
				'label' => esc_html__( 'Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => '--order-summary-items-divider-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'order_summary_items_divider_weight',
			[
				'label' => esc_html__( 'Weight', 'tenweb-builder' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'custom' ],
                'default' => [
                    'unit' => 'px',
                    'size' => 1,
                ],
                'tablet_default' => [
                    'unit' => 'px',
                    'size' => 1,
                ],
                'mobile_default' => [
                    'unit' => 'px',
                    'size' => 1,
                ],
				'selectors' => [
					'{{WRAPPER}}' => '--order-summary-items-divider-weight: {{SIZE}}{{UNIT}};',
				],
			]
		);

        $this->add_control(
			'order_summary_dividers_total_title',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Divider Total', 'tenweb-builder' ),
			]
		);

		$this->add_control(
			'order_summary_totals_divider_color',
			[
				'label' => esc_html__( 'Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => '--order-summary-totals-divider-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'order_summary_totals_divider_weight',
			[
				'label' => esc_html__( 'Weight', 'tenweb-builder' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'custom' ],
                'default' => [
                    'unit' => 'px',
                    'size' => 1,
                ],
                'tablet_default' => [
                    'unit' => 'px',
                    'size' => 1,
                ],
                'mobile_default' => [
                    'unit' => 'px',
                    'size' => 1,
                ],
				'selectors' => [
					'{{WRAPPER}}' => '--order-summary-totals-divider-weight: {{SIZE}}{{UNIT}};',
				],
			]
		);

        $this->add_control(
			'order_summary_dividers_subtotal_title',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Divider Subtotal', 'tenweb-builder' ),
			]
		);

		$this->add_control(
			'order_summary_subtotals_divider_color',
			[
				'label' => esc_html__( 'Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => '--order-summary-subtotals-divider-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'order_summary_subtotals_divider_weight',
			[
				'label' => esc_html__( 'Weight', 'tenweb-builder' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'custom' ],
                'default' => [
                    'unit' => 'px',
                    'size' => 1,
                ],
                'tablet_default' => [
                    'unit' => 'px',
                    'size' => 1,
                ],
                'mobile_default' => [
                    'unit' => 'px',
                    'size' => 1,
                ],
				'selectors' => [
					'{{WRAPPER}}' => '--order-summary-subtotals-divider-weight: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_checkout_tabs_purchase_button',
			[
				'label' => esc_html__( 'Purchase Button', 'tenweb-builder' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'purchase_button_typography',
				'selector' => '{{WRAPPER}} .woocommerce #payment #place_order',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'purchase_button_text_shadow',
				'selector' => '{{WRAPPER}} .woocommerce #payment #place_order',
			]
		);

		$this->start_controls_tabs( 'purchase_button_styles' );

		$this->start_controls_tab( 'purchase_button_normal_styles', [
			'label' => esc_html__( 'Normal', 'tenweb-builder' ),
		] );

		$this->add_control(
			'purchase_button_normal_text_color',
			[
				'label' => esc_html__( 'Text Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => '--purchase-button-normal-text-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'purchase_button_normal_background',
				'selector' => '{{WRAPPER}} #payment #place_order',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'purchase_button_normal_box_shadow',
				'selector' => '{{WRAPPER}} #place_order',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'purchase_button_hover_styles', [
			'label' => esc_html__( 'Hover', 'tenweb-builder' ),
		] );

		$this->add_control(
			'purchase_button_hover_text_color',
			[
				'label' => esc_html__( 'Text Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => '--purchase-button-hover-text-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'purchase_button_hover_background',
				'selector' => '{{WRAPPER}} #payment #place_order:hover',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'purchase_button_hover_box_shadow',
				'selector' => '{{WRAPPER}} #place_order:hover',
			]
		);

		$this->add_control(
			'purchase_button_hover_border_color',
			[
				'label' => esc_html__( 'Border Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => '--purchase-button-hover-border-color: {{VALUE}}',
				],
				'condition' => [
					'purchase_button_border_border!' => '',
				],
			]
		);

		$this->add_control(
			'purchase_button_hover_transition_duration',
			[
				'label' => esc_html__( 'Transition Duration', 'tenweb-builder' ) . ' (ms)',
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}}' => '--purchase-button-hover-transition-duration: {{SIZE}}ms',
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 3000,
					],
				],
			]
		);

		$this->add_control(
			'purchase_button_hover_animation',
			[
				'label' => esc_html__( 'Hover Animation', 'tenweb-builder' ),
				'type' => Controls_Manager::HOVER_ANIMATION,
				'frontend_available' => true,
				'render_type' => 'template',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'purchase_button_border',
				'selector' => '{{WRAPPER}} #place_order',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'purchase_button_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'tenweb-builder' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'selectors' => [
					'{{WRAPPER}}' => '--purchase-button-border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'purchase_button_padding',
			[
				'label' => esc_html__( 'Padding', 'tenweb-builder' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'selectors' => [
					'{{WRAPPER}}' => '--purchase-button-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; --purchase-button-width: fit-content;',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_checkout_tabs_customize',
			[
				'label' => esc_html__( 'Customize', 'tenweb-builder' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$customize_options = [];

		if ( $this->is_wc_feature_active( 'checkout_login_reminder' ) ) {
			$customize_options += [
				'customize_returning_customer' => esc_html__( 'Returning Customer', 'tenweb-builder' ),
			];
		}

		$customize_options += [
			'customize_billing_details' => esc_html__( 'Billing Details', 'tenweb-builder' ),
			'customize_additional_info' => esc_html__( 'Additional Information', 'tenweb-builder' ),
		];

		if ( $this->is_wc_feature_active( 'shipping' ) ) {
			$customize_options += [
				'customize_shipping_address' => esc_html__( 'Shipping Address', 'tenweb-builder' ),
			];
		}

		$customize_options += [
			'customize_order_summary' => esc_html__( 'Order Summary', 'tenweb-builder' ),
		];

		if ( $this->is_wc_feature_active( 'coupons' ) ) {
			$customize_options += [
				'customize_coupon' => esc_html__( 'Coupon', 'tenweb-builder' ),
			];
		}

		$customize_options += [
			'customize_payment' => esc_html__( 'Payment', 'tenweb-builder' ),
		];

		$this->add_control(
			'section_checkout_show_customize_elements',
			[
				'label' => esc_html__( 'Select sections of the checkout to customize:', 'tenweb-builder' ),
				'type' => Controls_Manager::SELECT2,
                'default' => 'customize_payment',
				'multiple' => true,
				'options' => $customize_options,
				'render_type' => 'ui',
				'label_block' => true,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_checkout_tabs_customize_returning_customer',
			[
				'label' => esc_html__( 'Customize: Returning Customer', 'tenweb-builder' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'section_checkout_show_customize_elements' => 'customize_returning_customer',
				],
			]
		);

		$this->add_control(
			'returning_customers_section_title',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Section', 'tenweb-builder' ),
			]
		);

		$this->add_control(
			'customize_returning_customer_background_color',
			[
				'label' => esc_html__( 'Background Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .e-woocommerce-login-section' => '--sections-background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'returning_customers_section_normal_box_shadow',
				'selector' => '{{WRAPPER}} .e-woocommerce-login-section',
			]
		);

		$this->add_control(
			'returning_customers_border_type',
			[
				'label' => esc_html__( 'Border Type', 'tenweb-builder' ),
				'type' => Controls_Manager::SELECT,
				'options' => $this->get_custom_border_type_options(),
				'selectors' => [
					'{{WRAPPER}} .e-woocommerce-login-section' => '--sections-border-type: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'returning_customers_border_width',
			[
				'label' => esc_html__( 'Border Width', 'tenweb-builder' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .e-woocommerce-login-section' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'returning_customers_border_type!' => 'none',
				],
			]
		);

		$this->add_control(
			'returning_customers_border_color',
			[
				'label' => esc_html__( 'Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .e-woocommerce-login-section' => '--sections-border-color: {{VALUE}};',
				],
				'condition' => [
					'returning_customers_border_type!' => 'none',
				],
			]
		);

		$this->add_responsive_control(
			'returning_customers_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'tenweb-builder' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .e-woocommerce-login-section' => '--sections-border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'returning_customers_padding',
			[
				'label' => esc_html__( 'Padding', 'tenweb-builder' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .e-woocommerce-login-section' => '--sections-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'returning_customers_margin',
			[
				'label' => esc_html__( 'Margin', 'tenweb-builder' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .e-woocommerce-login-section' => '--sections-margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);

		$this->add_control(
			'returning_customers_secondary_title',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Secondary Title', 'tenweb-builder' ),
			]
		);

		$this->add_control(
			'returning_customers_secondary_title_color',
			[
				'label' => esc_html__( 'Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-form-login-toggle' => '--sections-secondary-title-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'returning_customers_content_typography',
				'selector' => '{{WRAPPER}} .woocommerce-form-login-toggle',
			]
		);

		$this->add_control(
			'returning_customers_description_title',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Description', 'tenweb-builder' ),
			]
		);

		$this->add_control(
			'returning_customers_description_color',
			[
				'label' => esc_html__( 'Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .e-woocommerce-login-nudge' => '--sections-descriptions-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'returning_customers_description_typography',
				'selector' => '{{WRAPPER}} .e-woocommerce-login-nudge.e-description',
			]
		);

		$this->add_control(
			'returning_customers_checkboxes_title',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Checkbox', 'tenweb-builder' ),
			]
		);

		$this->add_control(
			'returning_customers_checkboxes_color',
			[
				'label' => esc_html__( 'Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .e-woocommerce-login-section' => '--sections-checkboxes-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'returning_customers_checkboxes_typography',
				'selector' => '{{WRAPPER}} .e-woocommerce-login-section .woocommerce-form__label-for-checkbox span',
			]
		);

		$this->add_control(
			'returning_customers_link_title',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Link', 'tenweb-builder' ),
			]
		);

		$this->start_controls_tabs( 'returning_customers_links' );

		$this->start_controls_tab( 'returning_customers_normal_links', [
			'label' => esc_html__( 'Normal', 'tenweb-builder' ),
		] );

		$this->add_control(
			'returning_customers_normal_links_color',
			[
				'label' => esc_html__( 'Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .e-woocommerce-login-section' => '--links-normal-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'returning_customers_hover_links', [
			'label' => esc_html__( 'Hover', 'tenweb-builder' ),
		] );

		$this->add_control(
			'returning_customers_hover_links_color',
			[
				'label' => esc_html__( 'Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .e-woocommerce-login-section' => '--links-hover-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_checkout_tabs_customize_billing_details',
			[
				'label' => esc_html__( 'Customize: Billing Details', 'tenweb-builder' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'section_checkout_show_customize_elements' => 'customize_billing_details',
				],
			]
		);

		$this->add_control(
			'customize_billing_details_section_title',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Section', 'tenweb-builder' ),
			]
		);

		$this->add_control(
			'customize_billing_details_background_color',
			[
				'label' => esc_html__( 'Background Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .col2-set .col-1' => '--sections-background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'billing_details_section_normal_box_shadow',
				'selector' => '{{WRAPPER}} .e-checkout__column-start .col2-set .col-1',
			]
		);

		$this->add_control(
			'billing_details_border_type',
			[
				'label' => esc_html__( 'Border Type', 'tenweb-builder' ),
				'type' => Controls_Manager::SELECT,
				'options' => $this->get_custom_border_type_options(),
				'selectors' => [
					'{{WRAPPER}} .col2-set .col-1' => '--sections-border-type: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'billing_details_border_width',
			[
				'label' => esc_html__( 'Border Width', 'tenweb-builder' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .e-checkout__column-start #customer_details .col-1' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'billing_details_border_type!' => 'none',
				],
			]
		);

		$this->add_control(
			'billing_details_border_color',
			[
				'label' => esc_html__( 'Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .col2-set .col-1' => '--sections-border-color: {{VALUE}}',
				],
				'condition' => [
					'billing_details_border_type!' => 'none',
				],
			]
		);

		$this->add_responsive_control(
			'billing_details_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'tenweb-builder' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .col2-set .col-1' => '--sections-border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'billing_details_padding',
			[
				'label' => esc_html__( 'Padding', 'tenweb-builder' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .col2-set .col-1' => '--sections-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'billing_details_margin',
			[
				'label' => esc_html__( 'Margin', 'tenweb-builder' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .col2-set .col-1' => '--sections-margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);

		$this->add_control(
			'billing_details_titles_title',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Title', 'tenweb-builder' ),
			]
		);

		$this->add_control(
			'billing_details_titles_color',
			[
				'label' => esc_html__( 'Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .col2-set .col-1' => '--sections-title-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'billing_details_titles_typography',
				'selector' => '{{WRAPPER}} .woocommerce-billing-fields h3',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'billing_details_titles_text_shadow',
				'selector' => '{{WRAPPER}} .woocommerce-billing-fields h3',
			]
		);

		$this->add_control(
			'billing_details_checkboxes_title',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Checkbox', 'tenweb-builder' ),
			]
		);

		$this->add_control(
			'billing_details_checkboxes_color',
			[
				'label' => esc_html__( 'Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .col2-set .col-1' => '--sections-checkboxes-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'billing_details_checkboxes_typography',
				'selector' => '{{WRAPPER}} .col2-set .col-1 .woocommerce-form__label-for-checkbox span',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_checkout_tabs_customize_additional_info',
			[
				'label' => esc_html__( 'Customize: Additional Information', 'tenweb-builder' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'section_checkout_show_customize_elements' => 'customize_additional_info',
				],
			]
		);

		$this->add_control(
			'customize_additional_information_section_title',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Section', 'tenweb-builder' ),
			]
		);

		$this->add_control(
			'customize_additional_information_background_color',
			[
				'label' => esc_html__( 'Background Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-additional-fields' => '--sections-background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'additional_information_section_normal_box_shadow',
				'selector' => '{{WRAPPER}} .woocommerce-additional-fields',
			]
		);

		$this->add_control(
			'additional_information_border_type',
			[
				'label' => esc_html__( 'Border Type', 'tenweb-builder' ),
				'type' => Controls_Manager::SELECT,
				'options' => $this->get_custom_border_type_options(),
				'selectors' => [
					'{{WRAPPER}} .woocommerce-additional-fields' => '--sections-border-type: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'additional_information_border_width',
			[
				'label' => esc_html__( 'Border Width', 'tenweb-builder' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .woocommerce-additional-fields' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'additional_information_border_type!' => 'none',
				],
			]
		);

		$this->add_control(
			'additional_information_border_color',
			[
				'label' => esc_html__( 'Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-additional-fields' => '--sections-border-color: {{VALUE}};',
				],
				'condition' => [
					'additional_information_border_type!' => 'none',
				],
			]
		);

		$this->add_responsive_control(
			'additional_information_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'tenweb-builder' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .woocommerce-additional-fields' => '--sections-border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'additional_information_padding',
			[
				'label' => esc_html__( 'Padding', 'tenweb-builder' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .woocommerce-additional-fields' => '--sections-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'additional_information_margin',
			[
				'label' => esc_html__( 'Margin', 'tenweb-builder' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .woocommerce-additional-fields' => '--sections-margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}}.e-checkout-layout-one-column .e-checkout__container' => 'grid-row-gap: {{BOTTOM}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);

		$this->add_control(
			'additional_information_titles_title',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Title', 'tenweb-builder' ),
			]
		);

		$this->add_control(
			'additional_information_titles_color',
			[
				'label' => esc_html__( 'Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-additional-fields' => '--sections-title-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'additional_information_titles_typography',
				'selector' => '{{WRAPPER}} .woocommerce-additional-fields h3',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'additional_information_titles_text_shadow',
				'selector' => '{{WRAPPER}} .woocommerce-additional-fields h3',
			]
		);

		$this->end_controls_section();

		if ( $this->is_wc_feature_active( 'shipping' ) ) {
			$this->add_shipping_style_controls();
		}

		if ( $this->is_wc_feature_active( 'coupons' ) ) {
			$this->add_coupons_style_controls();
		}

		$this->start_controls_section(
			'section_checkout_tabs_customize_order_summary',
			[
				'label' => esc_html__( 'Customize: Order Summary', 'tenweb-builder' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'section_checkout_show_customize_elements' => 'customize_order_summary',
				],
			]
		);

		$this->add_control(
			'customize_order_summary_section_title',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Section', 'tenweb-builder' ),
			]
		);

		$this->add_control(
			'customize_order_summary_background_color',
			[
				'label' => esc_html__( 'Background Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .e-checkout__order_review' => '--sections-background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'order_summary_section_normal_box_shadow',
				'selector' => '{{WRAPPER}} .e-checkout__order_review',
			]
		);

		$this->add_control(
			'order_summary_border_type',
			[
				'label' => esc_html__( 'Border Type', 'tenweb-builder' ),
				'type' => Controls_Manager::SELECT,
				'options' => $this->get_custom_border_type_options(),
				'selectors' => [
					'{{WRAPPER}} .e-checkout__order_review' => '--sections-border-type: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'order_summary_border_width',
			[
				'label' => esc_html__( 'Border Width', 'tenweb-builder' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .e-checkout__order_review' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'order_summary_border_type!' => 'none',
				],
			]
		);

		$this->add_control(
			'order_summary_border_color',
			[
				'label' => esc_html__( 'Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .e-checkout__order_review' => '--sections-border-color: {{VALUE}}',
				],
				'condition' => [
					'order_summary_border_type!' => 'none',
				],
			]
		);

		$this->add_responsive_control(
			'order_summary_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'tenweb-builder' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .e-checkout__order_review' => '--sections-border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'order_summary_padding',
			[
				'label' => esc_html__( 'Padding', 'tenweb-builder' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .e-checkout__order_review' => '--sections-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'order_summary_margin',
			[
				'label' => esc_html__( 'Margin', 'tenweb-builder' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .e-checkout__order_review' => '--sections-margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);

		$this->add_control(
			'order_summary_titles_title',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Title', 'tenweb-builder' ),
			]
		);

		$this->add_control(
			'order_summary_titles_color',
			[
				'label' => esc_html__( 'Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .e-checkout__order_review' => '--sections-title-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'order_summary_titles_typography',
				'selector' => '{{WRAPPER}} h3#order_review_heading',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'order_summary_titles_text_shadow',
				'selector' => '{{WRAPPER}} h3#order_review_heading',
			]
		);

		$this->add_control(
			'order_summary_descriptions_title',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Message', 'tenweb-builder' ),
			]
		);

		$this->add_control(
			'order_summary_descriptions_color',
			[
				'label' => esc_html__( 'Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .e-checkout__order_review' => '--sections-descriptions-color: {{VALUE}}; --sections-messages-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'order_summary_descriptions_typography',
				'selector' => '{{WRAPPER}} .woocommerce-no-shipping-available-html.e-description, {{WRAPPER}} .woocommerce-no-shipping-available-html.e-checkout-message',
			]
		);

		$this->add_control(
			'order_summary_radios_title',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Radio Button', 'tenweb-builder' ),
			]
		);

		$this->add_control(
			'order_summary_radios_color',
			[
				'label' => esc_html__( 'Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .e-checkout__order_review' => '--sections-radio-buttons-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'order_summary_radio_typography',
				'selector' => '{{WRAPPER}} .woocommerce .e-checkout__order_review ul#shipping_method li label',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_checkout_tabs_customize_payment',
			[
				'label' => esc_html__( 'Customize: Payment', 'tenweb-builder' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'section_checkout_show_customize_elements' => 'customize_payment',
				],
			]
		);

		$this->add_control(
			'customize_payment_section_title',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Section', 'tenweb-builder' ),
			]
		);

		$this->add_control(
			'customize_payment_background_color',
			[
				'label' => esc_html__( 'Background Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-checkout .twbb-payment-section' => '--sections-background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'payment_section_normal_box_shadow',
				'selector' => '{{WRAPPER}} .woocommerce-checkout .twbb-payment-section',
			]
		);

		$this->add_control(
			'payment_border_type',
			[
				'label' => esc_html__( 'Border Type', 'tenweb-builder' ),
				'type' => Controls_Manager::SELECT,
				'options' => $this->get_custom_border_type_options(),
				'selectors' => [
					'{{WRAPPER}} .woocommerce-checkout .twbb-payment-section' => '--sections-border-type: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'payment_border_width',
			[
				'label' => esc_html__( 'Border Width', 'tenweb-builder' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .woocommerce-checkout .twbb-payment-section' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'payment_border_type!' => 'none',
				],
			]
		);

		$this->add_control(
			'payment_border_color',
			[
				'label' => esc_html__( 'Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-checkout .twbb-payment-section' => '--sections-border-color: {{VALUE}};',
				],
				'condition' => [
					'payment_border_type!' => 'none',
				],
			]
		);

		$this->add_responsive_control(
			'payment_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'tenweb-builder' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .woocommerce-checkout .twbb-payment-section' => '--sections-border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'payment_padding',
			[
				'label' => esc_html__( 'Padding', 'tenweb-builder' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .woocommerce-checkout .twbb-payment-section' => '--sections-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'payment_margin',
			[
				'label' => esc_html__( 'Margin', 'tenweb-builder' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .woocommerce-checkout .twbb-payment-section' => '--sections-margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);

		$this->add_control(
			'payment_info_box_title',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Info Box', 'tenweb-builder' ),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'payment_info_box_title_background',
				'selector' => '{{WRAPPER}} .woocommerce-checkout #payment .payment_methods .payment_box',
			]
		);

		$this->add_control(
			'payment_description_title',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Description', 'tenweb-builder' ),
			]
		);

		$this->add_control(
			'payment_description_color',
			[
				'label' => esc_html__( 'Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
                'global' => [
                    'default' => Global_Colors::COLOR_TEXT,
                ],
				'selectors' => [
					'{{WRAPPER}} .woocommerce-checkout-payment' => '--sections-descriptions-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'payment_description_typography',
				'selector' => '{{WRAPPER}} .woocommerce-checkout-payment .e-description',
			]
		);

		$this->add_control(
			'payment_messages_title',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Message', 'tenweb-builder' ),
			]
		);

		$this->add_control(
			'payment_messages_color',
			[
				'label' => esc_html__( 'Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
                'global' => [
                    'default' => Global_Colors::COLOR_TEXT,
                ],
				'selectors' => [
					'{{WRAPPER}} .woocommerce-checkout-payment' => '--sections-messages-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'payment_messages_typography',
				'selector' => '{{WRAPPER}} .woocommerce-checkout #payment .payment_box, {{WRAPPER}} .woocommerce-privacy-policy-text p',
            ]
		);

		$this->add_control(
			'payment_checkboxes_title',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Checkbox', 'tenweb-builder' ),
			]
		);

		$this->add_control(
			'payment_checkboxes_color',
			[
				'label' => esc_html__( 'Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
                'global' => [
                    'default' => Global_Colors::COLOR_TEXT,
                ],
				'selectors' => [
					'{{WRAPPER}} .woocommerce-terms-and-conditions-wrapper' => '--sections-checkboxes-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'payment_checkboxes_typography',
				'selector' => '{{WRAPPER}} .woocommerce-terms-and-conditions-wrapper .woocommerce-form__label-for-checkbox span',
			]
		);

		$this->add_control(
			'payment_radio_title',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Radio Button', 'tenweb-builder' ),
			]
		);

		$this->add_control(
			'payment_radio_color',
			[
				'label' => esc_html__( 'Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
                'global' => [
                    'default' => Global_Colors::COLOR_TEXT,
                ],
				'selectors' => [
					'{{WRAPPER}} .woocommerce-checkout-payment' => '--sections-radio-buttons-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'payment_radio_typography',
				'selector' => '{{WRAPPER}} .woocommerce-checkout-payment .wc_payment_method label',
			]
		);

		$this->add_control(
			'payment_links_title',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Links', 'tenweb-builder' ),
			]
		);

		$this->start_controls_tabs( 'payment_colors' );

		$this->start_controls_tab( 'payment_normal_colors', [
			'label' => esc_html__( 'Normal', 'tenweb-builder' ),
		] );

		$this->add_control(
			'payment_normal_color',
			[
				'label' => esc_html__( 'Link Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-checkout-payment' => '--links-normal-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'payment_hover_colors', [
			'label' => esc_html__( 'Hover', 'tenweb-builder' ),
		] );

		$this->add_control(
			'payment_hover_color',
			[
				'label' => esc_html__( 'Link Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-checkout-payment' => '--links-hover-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	private function add_checkout_login_reminder_controls() {
		$this->start_controls_section(
			'returning_customer_heading',
			[
				'label' => esc_html__( 'Returning Customer', 'tenweb-builder' ),
			]
		);

		$this->add_control(
			'returning_customer_section_title',
			[
				'label' => esc_html__( 'Section Title', 'tenweb-builder' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Returning customer?', 'tenweb-builder' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'returning_customer_link_text',
			[
				'label' => esc_html__( 'Link Text', 'tenweb-builder' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Click here to login', 'tenweb-builder' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_responsive_control(
			'returning_customer_title_alignment',
			[
				'label' => esc_html__( 'Alignment', 'tenweb-builder' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'start' => [
						'title' => esc_html__( 'Start', 'tenweb-builder' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'tenweb-builder' ),
						'icon' => 'eicon-text-align-center',
					],
					'end' => [
						'title' => esc_html__( 'End', 'tenweb-builder' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--login-title-alignment: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'login_button_title',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Login Button', 'tenweb-builder' ),
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'login_button_alignment',
			[
				'label' => esc_html__( 'Alignment', 'tenweb-builder' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'start' => [
						'title' => esc_html__( 'Start', 'tenweb-builder' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'tenweb-builder' ),
						'icon' => 'eicon-text-align-center',
					],
					'end' => [
						'title' => esc_html__( 'End', 'tenweb-builder' ),
						'icon' => 'eicon-text-align-right',
					],
					'justify' => [
						'title' => esc_html__( 'Justify', 'tenweb-builder' ),
						'icon' => 'eicon-text-align-justify',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .e-login-wrap' => '{{VALUE}}',
				],
				'selectors_dictionary' => [
					'start' => '--login-button-alignment: start; --login-button-width: 35%;',
					'center' => '--login-button-alignment: center;  --login-button-width: 35%;',
					'end' => '--login-button-alignment: end;  --login-button-width: 35%;',
					'justify' => '--login-button-alignment: center; --login-button-width: 100%;',
				],
			]
		);

		$this->add_control(
			'login_button_alignment_note',
			[
				'raw' => esc_html__( 'Note: This control will only affect screen sizes Tablet and below', 'tenweb-builder' ),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-descriptor',
			]
		);

		$this->end_controls_section();
	}

	private function add_shipping_controls() {
		$this->start_controls_section(
			'shipping_details_section',
			[
				'label' => esc_html__( 'Shipping Details', 'tenweb-builder' ),
			]
		);

		$this->add_control(
			'shipping_details_section_title_main',
			[
				'label' => esc_html__( 'Section Title', 'tenweb-builder' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Shipping Details', 'tenweb-builder' ),
			]
		);

		$this->add_control(
			'shipping_details_section_title',
			[
				'label' => esc_html__( 'Checkbox Label', 'tenweb-builder' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Ship to a different address?', 'tenweb-builder' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'repeater_state',
			[
				'label' => esc_html__( 'Repeater State - hidden', 'tenweb-builder' ),
				'type' => Controls_Manager::HIDDEN,
			]
		);

		$repeater->add_control(
			'label_placeholder_notification',
			[
				'raw' => __( 'Note: This label and placeholder are taken from the Billing section. You can change it there.', 'tenweb-builder' ),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-descriptor',
				'condition' => [
					'repeater_state' => 'from_billing',
				],
			]
		);

		$repeater->start_controls_tabs( 'tabs', [
			'condition' => [
				'repeater_state' => '',
			],
		] );

		$repeater->start_controls_tab( 'content_tab', [
			'label' => esc_html__( 'Content', 'tenweb-builder' ),
		] );

		$repeater->add_control(
			'label',
			[
				'label' => esc_html__( 'Label', 'tenweb-builder' ),
				'type' => Controls_Manager::TEXT,
			]
		);

		$repeater->add_control(
			'placeholder',
			[
				'label' => esc_html__( 'Placeholder', 'tenweb-builder' ),
				'type' => Controls_Manager::TEXT,
			]
		);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab( 'advanced_tab', [
			'label' => esc_html__( 'Advanced', 'tenweb-builder' ),
		] );

		$repeater->add_control(
			'default',
			[
				'label' => esc_html__( 'Default Value', 'tenweb-builder' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$repeater->end_controls_tab();

		$repeater->end_controls_tabs();

		$repeater->add_control(
			'locale_notice',
			[
				'raw' => __( 'Note: This content cannot be changed due to local regulations.', 'tenweb-builder' ),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-descriptor',
				'condition' => [
					'repeater_state' => 'locale',
				],
			]
		);

		$repeater->add_control(
			'from_billing_notice',
			[
				'raw' => __( 'Note: This label and placeholder are taken from the Billing section. You can change it there.', 'tenweb-builder' ),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-descriptor',
				'condition' => [
					'repeater_state' => 'from_billing',
				],
			]
		);

		$this->add_control(
			'shipping_details_form_fields',
			[
				'label' => esc_html__( 'Form Items', 'tenweb-builder' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'item_actions' => [
					'add' => false,
					'duplicate' => false,
					'remove' => false,
					'sort' => false,
				],
				'default' => $this->get_shipping_field_defaults(),
				'title_field' => '{{{ label }}}', // phpcs:ignore WordPressVIPMinimum.Security.Mustache.OutputNotation
			]
		);

		$this->end_controls_section();
	}

	private function add_signup_and_login_from_checkout_controls() {
		$this->start_controls_section(
			'create_account_section',
			[
				'label' => esc_html__( 'Create an Account', 'tenweb-builder' ),
			]
		);

		$this->add_control(
			'create_account_text',
			[
				'label' => esc_html__( 'Section Title', 'tenweb-builder' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Create an account?', 'tenweb-builder' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->end_controls_section();
	}

	private function add_coupon_controls() {
		$this->start_controls_section(
			'coupon_section',
			[
				'label' => esc_html__( 'Coupon', 'tenweb-builder' ),
			]
		);

		$this->add_control(
			'coupon_section_display',
			[
				'label' => esc_html__( 'Coupon', 'tenweb-builder' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'tenweb-builder' ),
				'label_off' => esc_html__( 'Hide', 'tenweb-builder' ),
				'default' => 'yes',
			]
		);

		$this->add_control(
			'coupon_section_title_text',
			[
				'label' => esc_html__( 'Section Title', 'tenweb-builder' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Have a coupon?', 'tenweb-builder' ),
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'coupon_section_display' => 'yes',
				],
			]
		);

		$this->add_control(
			'coupon_section_title_link_text',
			[
				'label' => esc_html__( 'Link Text', 'tenweb-builder' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Click here to enter your coupon code', 'tenweb-builder' ),
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'coupon_section_display' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'coupon_alignment',
			[
				'label' => esc_html__( 'Alignment', 'tenweb-builder' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'start' => [
						'title' => esc_html__( 'Start', 'tenweb-builder' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'tenweb-builder' ),
						'icon' => 'eicon-text-align-center',
					],
					'end' => [
						'title' => esc_html__( 'End', 'tenweb-builder' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--coupon-title-alignment: {{VALUE}};',
				],
				'condition' => [
					'coupon_section_display' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'coupon_button_title',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Apply Button', 'tenweb-builder' ),
				'separator' => 'before',
				'condition' => [
					'coupon_section_display' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'coupon_button_alignment',
			[
				'label' => esc_html__( 'Alignment', 'tenweb-builder' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'start' => [
						'title' => esc_html__( 'Start', 'tenweb-builder' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'tenweb-builder' ),
						'icon' => 'eicon-text-align-center',
					],
					'end' => [
						'title' => esc_html__( 'End', 'tenweb-builder' ),
						'icon' => 'eicon-text-align-right',
					],
					'justify' => [
						'title' => esc_html__( 'Justify', 'tenweb-builder' ),
						'icon' => 'eicon-text-align-justify',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .coupon-container-grid' => '{{VALUE}}',
				],
				'selectors_dictionary' => [
					'start' => '--coupon-button-alignment: start;',
					'center' => '--coupon-button-alignment: center;',
					'end' => '--coupon-button-alignment: end;',
					'justify' => '--coupon-button-alignment: justify; --coupon-button-width: 100%;',
				],
				'condition' => [
					'coupon_section_display' => 'yes',
				],
			]
		);

		$this->add_control(
			'coupon_button_alignment_note',
			[
				'raw' => esc_html__( 'Note: This control will only affect screen sizes Tablet and below', 'tenweb-builder' ),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-descriptor',
				'condition' => [
					'coupon_section_display' => 'yes',
				],
			]
		);

		$this->end_controls_section();
	}

	private function add_shipping_style_controls() {
		$this->start_controls_section(
			'section_checkout_tabs_customize_shipping_address',
			[
				'label' => esc_html__( 'Customize: Shipping Address', 'tenweb-builder' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'section_checkout_show_customize_elements' => 'customize_shipping_address',
				],
			]
		);

		$this->add_control(
			'shipping_address_section_title',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Section', 'tenweb-builder' ),
			]
		);

		$this->add_control(
			'customize_shipping_address_background_color',
			[
				'label' => esc_html__( 'Background Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-shipping-fields .shipping_address' => '--sections-background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'shipping_address_section_normal_box_shadow',
				'selector' => '{{WRAPPER}} .woocommerce-shipping-fields .shipping_address',
			]
		);

		$this->add_control(
			'shipping_address_border_type',
			[
				'label' => esc_html__( 'Border Type', 'tenweb-builder' ),
				'type' => Controls_Manager::SELECT,
				'options' => $this->get_custom_border_type_options(),
				'selectors' => [
					'{{WRAPPER}} .woocommerce-shipping-fields .shipping_address' => '--sections-border-type: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'shipping_address_border_width',
			[
				'label' => esc_html__( 'Border Width', 'tenweb-builder' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .woocommerce-shipping-fields .shipping_address' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'shipping_address_border_type!' => 'none',
				],
			]
		);

		$this->add_control(
			'shipping_address_border_color',
			[
				'label' => esc_html__( 'Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-shipping-fields .shipping_address' => '--sections-border-color: {{VALUE}}',
				],
				'condition' => [
					'shipping_address_border_type!' => 'none',
				],
			]
		);

		$this->add_responsive_control(
			'shipping_address_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'tenweb-builder' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .woocommerce-shipping-fields .shipping_address' => '--sections-border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'shipping_address_padding',
			[
				'label' => esc_html__( 'Padding', 'tenweb-builder' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .woocommerce-shipping-fields .shipping_address' => '--sections-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'shipping_address_margin',
			[
				'label' => esc_html__( 'Margin', 'tenweb-builder' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .woocommerce-shipping-fields .shipping_address' => '--sections-margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);

		$this->add_control(
			'shipping_address_checkboxes_title',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Checkboxes', 'tenweb-builder' ),
				'separator' => 'before',
			]
		);

		$this->add_control(
			'shipping_address_checkboxes_color',
			[
				'label' => esc_html__( 'Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-shipping-fields' => '--sections-checkboxes-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'shipping_address_checkboxes_typography',
				'selector' => '{{WRAPPER}} .woocommerce-shipping-fields .woocommerce-form__label-for-checkbox span',
			]
		);

		$this->end_controls_section();
	}

	private function add_coupons_style_controls() {
		$this->start_controls_section(
			'section_checkout_tabs_customize_coupon',
			[
				'label' => esc_html__( 'Customize: Coupon', 'tenweb-builder' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'section_checkout_show_customize_elements' => 'customize_coupon',
				],
			]
		);

		$this->add_control(
			'customize_coupon_section_title',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Section', 'tenweb-builder' ),
			]
		);

		$this->add_control(
			'customize_coupon_background_color',
			[
				'label' => esc_html__( 'Background Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .e-coupon-box' => '--sections-background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'coupon_section_normal_box_shadow',
				'selector' => '{{WRAPPER}} .e-coupon-box',
			]
		);

		$this->add_control(
			'coupon_border_type',
			[
				'label' => esc_html__( 'Border Type', 'tenweb-builder' ),
				'type' => Controls_Manager::SELECT,
				'options' => $this->get_custom_border_type_options(),
				'selectors' => [
					'{{WRAPPER}} .e-coupon-box' => '--sections-border-type: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'coupon_border_width',
			[
				'label' => esc_html__( 'Border Width', 'tenweb-builder' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .e-coupon-box' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'coupon_border_type!' => 'none',
				],
			]
		);

		$this->add_control(
			'coupon_border_color',
			[
				'label' => esc_html__( 'Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .e-coupon-box' => '--sections-border-color: {{VALUE}};',
				],
				'condition' => [
					'coupon_border_type!' => 'none',
				],
			]
		);

		$this->add_responsive_control(
			'coupon_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'tenweb-builder' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .e-coupon-box' => '--sections-border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'coupon_padding',
			[
				'label' => esc_html__( 'Padding', 'tenweb-builder' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .e-coupon-box' => '--sections-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'coupon_margin',
			[
				'label' => esc_html__( 'Margin', 'tenweb-builder' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .e-coupon-box' => '--sections-margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);

		$this->add_control(
			'coupon_secondary_title',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Secondary Title', 'tenweb-builder' ),
			]
		);

		$this->add_control(
			'coupon_secondary_title_color',
			[
				'label' => esc_html__( 'Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .e-woocommerce-coupon-nudge' => '--sections-secondary-title-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'coupon_content_typography',
				'selector' => '{{WRAPPER}} .e-woocommerce-coupon-nudge.e-checkout-secondary-title',
			]
		);

		$this->add_control(
			'coupon_link_title',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Link', 'tenweb-builder' ),
			]
		);

		$this->start_controls_tabs( 'coupon_links' );

		$this->start_controls_tab( 'coupon_normal_links', [
			'label' => esc_html__( 'Normal', 'tenweb-builder' ),
		] );

		$this->add_control(
			'coupon_normal_links_color',
			[
				'label' => esc_html__( 'Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .e-coupon-box' => '--links-normal-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'coupon_hover_links', [
			'label' => esc_html__( 'Hover', 'tenweb-builder' ),
		] );

		$this->add_control(
			'coupon_hover_links_color',
			[
				'label' => esc_html__( 'Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .e-coupon-box' => '--links-hover-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Get Billing Field Defaults
	 *
	 * Get defaults used for the billing details repeater control.
	 *
	 * @since 3.5.0
	 *
	 * @return array
	 */
	private function get_billing_field_defaults() {
		$fields = [
			'billing_first_name' => [
				'label' => esc_html__( 'First Name', 'tenweb-builder' ),
				'repeater_state' => '',
			],
			'billing_last_name' => [
				'label' => esc_html__( 'Last Name', 'tenweb-builder' ),
				'repeater_state' => '',
			],
			'billing_company' => [
				'label' => esc_html__( 'Company Name', 'tenweb-builder' ),
				'repeater_state' => '',
			],
			'billing_country' => [
				'label' => esc_html__( 'Country / Region', 'tenweb-builder' ),
				'repeater_state' => '',
			],
			'billing_address_1' => [
				'label' => esc_html__( 'Street Address', 'tenweb-builder' ),
				'repeater_state' => '',
			],
			'billing_postcode' => [
				'label' => esc_html__( 'Post Code', 'tenweb-builder' ),
				'repeater_state' => '',
			],
			'billing_city' => [
				'label' => esc_html__( 'Town / City', 'tenweb-builder' ),
				'repeater_state' => '',
			],
			'billing_state' => [
				'label' => esc_html__( 'State', 'tenweb-builder' ),
				'repeater_state' => '',
			],
			'billing_phone' => [
				'label' => esc_html__( 'Phone', 'tenweb-builder' ),
				'repeater_state' => '',
			],
			'billing_email' => [
				'label' => esc_html__( 'Email Address', 'tenweb-builder' ),
				'repeater_state' => '',
			],
		];

		return $this->reformat_address_field_defaults( $fields );
	}

	/**
	 * Get Shipping Field Defaults
	 *
	 * Get defaults used for the shipping details repeater control.
	 *
	 * @since 3.5.0
	 *
	 * @return array
	 */
	private function get_shipping_field_defaults() {
		$fields = [
			'shipping_first_name' => [
				'label' => esc_html__( 'First Name', 'tenweb-builder' ),
				'repeater_state' => '',
			],
			'shipping_last_name' => [
				'label' => esc_html__( 'Last Name', 'tenweb-builder' ),
				'repeater_state' => '',
			],
			'shipping_company' => [
				'label' => esc_html__( 'Company Name', 'tenweb-builder' ),
				'repeater_state' => '',
			],
			'shipping_country' => [
				'label' => esc_html__( 'Country / Region', 'tenweb-builder' ),
				'repeater_state' => '',
			],
			'shipping_address_1' => [
				'label' => esc_html__( 'Street Address', 'tenweb-builder' ),
				'repeater_state' => '',
			],
			'shipping_postcode' => [
				'label' => esc_html__( 'Post Code', 'tenweb-builder' ),
				'repeater_state' => '',
			],
			'shipping_city' => [
				'label' => esc_html__( 'Town / City', 'tenweb-builder' ),
				'repeater_state' => '',
			],
			'shipping_state' => [
				'label' => esc_html__( 'State', 'tenweb-builder' ),
				'repeater_state' => '',
			],
		];

		return $this->reformat_address_field_defaults( $fields );
	}

	/**
	 * Reformat Address Field Defaults
	 *
	 * Used with the `get_..._field_defaults()` methods.
	 * Takes the address array and converts it into the format expected by the repeater controls.
	 *
	 * @since 3.5.0
	 *
	 * @param $address
	 * @return array
	 */
	private function reformat_address_field_defaults( $address ) {
		$defaults = [];
		foreach ( $address as $key => $value ) {
			$defaults[] = [
				'field_key' => $key,
				'field_label' => $value['label'],
				'label' => $value['label'],
				'placeholder' => $value['label'],
				'repeater_state' => $value['repeater_state'],
			];
		}

		return $defaults;
	}

	/**
	 * Get Main Woocommerce Sections Selectors
	 *
	 * Get all the 'Sections' selectors. There are numerous controls that need these selectors so it was easier
	 * to consolidate them into one function. Especially when updates need to be made.
	 *
	 * @since 3.5.0
	 *
	 * @return string
	 */
	private function get_main_woocommerce_sections_selectors() {
		$selector = '{{WRAPPER}} .e-woocommerce-login-section, {{WRAPPER}} .woocommerce-checkout #customer_details .col-1, {{WRAPPER}} .woocommerce-additional-fields, {{WRAPPER}} .e-checkout__order_review, {{WRAPPER}} .e-coupon-box, {{WRAPPER}} .woocommerce-checkout .twbb-payment-section';
		if ( $this->is_wc_feature_active( 'shipping' ) ) {
			$selector .= ', {{WRAPPER}} .woocommerce-shipping-fields .shipping_address';
		}
		return $selector;
	}

	/**
	 * Get Main Woocommerce Sections Title Selectors
	 *
	 * Get all the 'Title' selectors. There are numerous controls that need these selectors so it was easier to
	 * consolidate them into one function. Especially when updates need to be made.
	 *
	 * @since 3.5.0
	 *
	 * @return string
	 */
	private function get_main_woocommerce_sections_title_selectors() {
		return '{{WRAPPER}} h3#order_review_heading, {{WRAPPER}} h3#order_payment_heading, {{WRAPPER}} .woocommerce-billing-fields h3, {{WRAPPER}} .woocommerce-additional-fields h3';
	}

	/**
	 * Init Gettext Modifications
	 *
	 * Sets the `$gettext_modifications` property used with the `filter_gettext()` in the extended Base_Widget.
	 *
	 * @since 3.5.0
	 */
	protected function init_gettext_modifications() {
		$instance = $this->get_settings_for_display();
		$this->gettext_modifications = [
			'Billing details' => isset( $instance['billing_details_section_title'] ) ? $instance['billing_details_section_title'] : '',
			'Billing &amp; Shipping' => isset( $instance['billing_details_section_title'] ) ? $instance['billing_details_section_title'] : '',
			'Ship to a different address?' => isset( $instance['shipping_details_section_title'] ) ? $instance['shipping_details_section_title'] : '',
			'Additional information' => isset( $instance['additional_information_section_title'] ) ? $instance['additional_information_section_title'] : '',
			'Your order' => isset( $instance['order_summary_section_title'] ) ? $instance['order_summary_section_title'] : '',
			'Have a coupon?' => isset( $instance['coupon_section_title_text'] ) ? $instance['coupon_section_title_text'] : '',
			'Click here to enter your coupon code' => isset( $instance['coupon_section_title_link_text'] ) ? $instance['coupon_section_title_link_text'] : '',
			'Returning customer?' => isset( $instance['returning_customer_section_title'] ) ? $instance['returning_customer_section_title'] : '',
			'Click here to login' => isset( $instance['returning_customer_link_text'] ) ? $instance['returning_customer_link_text'] : '',
			'Create an account?' => isset( $instance['create_account_text'] ) ? $instance['create_account_text'] : '',
		];
	}

	/**
	 * WooCommerce Terms and Conditions Checkbox Text.
	 *
	 * WooCommerce filter is used to apply widget settings to Checkout Terms & Conditions text and link text.
	 *
	 * @since 3.5.0
	 *
	 * @param string $text
	 * @return string
	 */
	public function woocommerce_terms_and_conditions_checkbox_text( $text ) {
		$instance = $this->get_settings_for_display();

		if ( ! isset( $instance['terms_conditions_message_text'] ) || ! isset( $instance['terms_conditions_link_text'] ) ) {
			return $text;
		}

		$message = $instance['terms_conditions_message_text'];
		$link = $instance['terms_conditions_link_text'];

		$terms_page_id = wc_terms_and_conditions_page_id();
		if ( $terms_page_id ) {
			$message .= ' <a href="' . esc_url( get_permalink( $terms_page_id ) ) . '" class="woocommerce-terms-and-conditions-link" target="_blank">' . $link . '</a>';
		}

		return $message;
	}

	/**
	 * Modify Form Field.
	 *
	 * WooCommerce filter is used to apply widget settings to the Checkout forms address fields
	 * from the Billing and Shipping Details widget sections, e.g. label, placeholder, default.
	 *
	 * @since 3.5.0
	 *
	 * @param array $args
	 * @param string $key
	 * @param string $value
	 * @return array
	 */
	public function modify_form_field( $args, $key, $value ) {
		$reformatted_form_fields = $this->get_reformatted_form_fields();

		// Check if we need to modify the args of this form field.
		if ( isset( $reformatted_form_fields[ $key ] ) ) {
			$apply_fields = [
				'label',
				'placeholder',
				'default',
			];

			foreach ( $apply_fields as $field ) {
				if ( ! empty( $reformatted_form_fields[ $key ][ $field ] ) ) {
					$args[ $field ] = $reformatted_form_fields[ $key ][ $field ];
				}
			}
		}

		return $args;
	}

	/**
	 * Get Reformatted Form Fields.
	 *
	 * Combines the 3 relevant repeater settings arrays into a one level deep associative array
	 * with the keys that match those that WooCommerce uses for its form fields.
	 *
	 * The result is cached so the conversion only ever happens once.
	 *
	 * @since 3.5.0
	 *
	 * @return array
	 */
	private function get_reformatted_form_fields() {
		if ( ! isset( $this->reformatted_form_fields ) ) {
			$instance = $this->get_settings_for_display();

			// Reformat form repeater field into one usable array.
			$repeater_fields = [
				'billing_details_form_fields',
				'shipping_details_form_fields',
				'additional_information_form_fields',
			];

			$this->reformatted_form_fields = [];

			// Apply other modifications to inputs.
			foreach ( $repeater_fields as $repeater_field ) {
				if ( isset( $instance[ $repeater_field ] ) ) {
					foreach ( $instance[ $repeater_field ] as $item ) {
						if ( ! isset( $item['field_key'] ) ) {
							continue;
						}
						$this->reformatted_form_fields[ $item['field_key'] ] = $item;
					}
				}
			}
		}

		return $this->reformatted_form_fields;
	}

	/**
	 * Render Woocommerce Checkout Login Form
	 *
	 * A custom function to render a login form on the Checkout widget. The default WC Login form
	 * was removed in this file's render() method with:
	 * remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_login_form' );
	 *
	 * And then we are adding this form into the widget at the
	 * 'woocommerce_checkout_before_customer_details' hook.
	 *
	 * We are doing this in order to match the placement of the Login form to the provided design.
	 * WC places these forms ABOVE the checkout form section where as we needed to place them inside the
	 * checkout form section. So we removed the default login form and added our own form.
	 *
	 * @since 3.5.0
	 */
	private function render_woocommerce_checkout_login_form() {
		$settings = $this->get_settings_for_display();
		$button_classes = [ 'woocommerce-button', 'button', 'woocommerce-form-login__submit', 'e-woocommerce-form-login-submit' ];
		if ( $settings['forms_buttons_hover_animation'] ) {
			$button_classes[] = 'elementor-animation-' . $settings['forms_buttons_hover_animation'];
		}
		$this->add_render_attribute(
			'button_login', [
				'class' => $button_classes,
				'name' => 'login',
				'type' => 'submit',
			]
		);
		?>
		<div class="e-woocommerce-login-section">
			<div class="elementor-woocommerce-login-messages"></div>
			<div class="woocommerce-form-login-toggle e-checkout-secondary-title">
				<?php echo esc_html__( 'Returning customer?', 'tenweb-builder' ) . ' <a href="#" class="e-show-login">' . esc_html__( 'Click here to login', 'tenweb-builder' ) . '</a>'; ?>
			</div>
			<div class="e-woocommerce-login-anchor" style="display:none;">
				<p class="e-woocommerce-login-nudge e-description"><?php echo esc_html__( 'If you have shopped with us before, please enter your details below. If you are a new customer, please proceed to the Billing section.', 'tenweb-builder' ); ?></p>

				<div class="e-login-wrap">
					<div class="e-login-wrap-start">
						<p class="form-row form-row-first">
							<label for="username"><?php esc_html_e( 'Email', 'tenweb-builder' ); ?> <span class="required">*</span></label>
							<input type="text" class="input-text" name="username" id="username" autocomplete="username" />
						</p>
						<p class="form-row form-row-last">
							<label for="password"><?php esc_html_e( 'Password', 'tenweb-builder' ); ?> <span class="required">*</span></label>
							<input class="input-text" type="password" name="password" id="password" autocomplete="current-password" />
						</p>
						<div class="clear"></div>
					</div>

					<div class="e-login-wrap-end">
						<p class="form-row">
							<label for="login" class="e-login-label">&nbsp;</label>
							<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
							<input type="hidden" name="redirect" value="<?php echo esc_url( get_permalink() ); ?>" />
							<button <?php $this->print_render_attribute_string( 'button_login' ); ?> value="<?php esc_attr_e( 'Login', 'tenweb-builder' ); ?>"><?php esc_html_e( 'Login', 'tenweb-builder' ); ?></button>
						</p>
						<div class="clear"></div>
					</div>
				</div>

				<div class="e-login-actions-wrap">
					<div class="e-login-actions-wrap-start">
						<label class="woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__rememberme">
							<input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" /> <span class="elementor-woocomemrce-login-rememberme"><?php esc_html_e( 'Remember me', 'tenweb-builder' ); ?></span>
						</label>
					</div>

					<div class="e-login-actions-wrap-end">
						<p class="lost_password">
							<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Lost your password?', 'tenweb-builder' ); ?></a>
						</p>
					</div>
				</div>

			</div>
		</div>
		<?php
	}

	/**
	 * Render Woocommerce Checkout Coupon Form
	 *
	 * A custom function to render a coupon form on the Checkout widget. The default WC coupon form
	 * was removed in this file's render() method with:
	 * remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_login_form' );
	 *
	 * And then we are adding this form into the widget at the
	 * 'woocommerce_checkout_order_review' hook.
	 *
	 * We are doing this in order to match the placement of the coupon form to the provided design.
	 * WC places these forms ABOVE the checkout form section where as we needed to place them inside the
	 * checkout form section. So we removed the default coupon form and added our own form.
	 *
	 * @since 3.5.0
	 */
	private function render_woocommerce_checkout_coupon_form() {
		$settings = $this->get_settings_for_display();
        $coupon_section_title_text = !empty($settings['coupon_section_title_text']) ? __($settings['coupon_section_title_text'], 'tenweb-builder' ) : __('Have a coupon?', 'tenweb-builder');
        $coupon_section_title_link_text = !empty($settings['coupon_section_title_link_text']) ? __($settings['coupon_section_title_link_text'], 'tenweb-builder') : __('Have a coupon?', 'tenweb-builder');
		$button_classes = [ 'woocommerce-button', 'button', 'e-apply-coupon' ];
		if ( $settings['forms_buttons_hover_animation'] ) {
			$button_classes[] = 'elementor-animation-' . $settings['forms_buttons_hover_animation'];
		}
		$this->add_render_attribute(
			'button_coupon', [
				'class' => $button_classes,
				'name' => 'apply_coupon',
				'type' => 'submit',
			]
		);
		?>
		<div class="e-coupon-box">
			<p class="e-woocommerce-coupon-nudge e-checkout-secondary-title"><?php echo esc_html( $coupon_section_title_text ); ?> <a href="#" class="e-show-coupon-form"><?php echo esc_html( $coupon_section_title_link_text ); ?></a></p>
			<div class="e-coupon-anchor" style="display:none">
				<label class="e-coupon-anchor-description"><?php esc_html_e( 'If you have a coupon code, please apply it below.', 'tenweb-builder' ); ?></label>
				<div class="form-row">
					<div class="coupon-container-grid">
						<div class="col coupon-col-1 ">
							<input type="text" name="coupon_code" class="input-text" placeholder="<?php esc_attr_e( 'Coupon code', 'tenweb-builder' ); ?>" id="coupon_code" value="" />
						</div>
						<div class="col coupon-col-2">
							<button <?php $this->print_render_attribute_string( 'button_coupon' ); ?>><?php esc_html_e( 'Apply', 'tenweb-builder' ); ?></button>
						</div>
					</div>
				</div>
				<div class="clear"></div>
			</div>
		</div>
		<?php
	}

	/**
	 * Should Render Login
	 *
	 * Decide if the login form should be rendered.
	 * The login form should be rendered if:
	 * 1) The WooCommerce setting is enabled
	 * 2) AND: a logged out user is viewing the page, OR the Editor is open
	 *
	 * @since 3.5.0
	 *
	 * @return boolean
	 */
	private function should_render_login() {
		return 'no' !== get_option( 'woocommerce_enable_checkout_login_reminder' ) && ( ! is_user_logged_in() || \Elementor\Plugin::instance()->editor->is_edit_mode() );
	}

	/**
	 * Should Render Coupon
	 *
	 * Decide if the coupon form should be rendered.
	 * The coupon form should be rendered if:
	 * 1) The WooCommerce setting is enabled
	 * 2) And the Coupon Display toggle hasn't been set to 'no'
	 * 3) AND: a payment is needed, OR the Editor is open
	 *
	 * @since 3.5.0
	 *
	 * @return boolean
	 */
	private function should_render_coupon() {
		$settings = $this->get_settings_for_display();
		$coupon_display_control = true;

		if ( !isset($settings['coupon_section_display']) || '' === $settings['coupon_section_display'] ) {
			$coupon_display_control = false;
		}

		return ( WC()->cart->needs_payment() || \Elementor\Plugin::instance()->editor->is_edit_mode() ) && wc_coupons_enabled() && $coupon_display_control;
	}

	/**
	 * WooCommerce Checkout Before Customer Details
	 *
	 * Callback function for the woocommerce_checkout_before_customer_details hook that outputs elements
	 *
	 * This eliminates the need for template overrides.
	 *
	 * @since 3.5.0
	 */
	public function woocommerce_checkout_before_customer_details() {
		?>
		<div class="e-checkout__container">
			<!--open container-->
			<div class="e-checkout__column e-checkout__column-start">
				<!--open column-1-->
		<?php
		if ( $this->should_render_login() ) {
			$this->render_woocommerce_checkout_login_form();
		}
	}

	/**
	 * Woocommerce Checkout After Customer Details
	 *
	 * Output containing elements. Callback function for the woocommerce_checkout_after_customer_details hook.
	 *
	 * This eliminates the need for template overrides.
	 *
	 * @since 3.5.0
	 */
	public function woocommerce_checkout_after_customer_details() {
		?>
					<!--close column-1-->
				</div>
		<?php
	}

	/**
	 * Woocommerce Checkout Before Order Review Heading 1
	 *
	 * Output containing elements. Callback function for the woocommerce_checkout_before_order_review_heading hook.
	 *
	 * This eliminates the need for template overrides.
	 *
	 * @since 3.5.0
	 */
	public function woocommerce_checkout_before_order_review_heading_1() {
		?>
				<div class="e-checkout__column e-checkout__column-end">
					<!--open column-2-->
						<div class="e-checkout__column-inner e-sticky-right-column">
							<!--open column-inner-->
		<?php
	}

	/**
	 * Woocommerce Checkout Before Order Review Heading 2
	 *
	 * Output containing elements. Callback function for the woocommerce_checkout_before_order_review_heading hook.
	 *
	 * This eliminates the need for template overrides.
	 *
	 * @since 3.5.0
	 */
	public function woocommerce_checkout_before_order_review_heading_2() {
		?>
							<div class="e-checkout__order_review">
								<!--open order_review-->
		<?php
	}

	/**
	 * Woocommerce Checkout Order Review
	 *
	 * Output containing elements. Callback function for the woocommerce_checkout_order_review hook.
	 *
	 * This eliminates the need for template overrides.
	 *
	 * @since 3.5.0
	 */
	public function woocommerce_checkout_order_review() {
		?>
									<!--close wc_order_review-->
								</div>
								<!--close order_review-->
							</div>
		<?php
		if ( $this->should_render_coupon() ) {
			$this->render_woocommerce_checkout_coupon_form();
		}
        if ( function_exists( 'WC' ) ) {
            $available_gateways = WC()->payment_gateways->get_available_payment_gateways();
        }

        $class = '';
        if ( is_array( $available_gateways ) && count( $available_gateways ) > 1 ) {
            $class = ' twbb-payment-multiple';
        }
		?>
        <div class="e-checkout__order_review-2 twbb-payment-section<?php echo esc_attr($class); ?>">
            <!--reopen wc_order_review-2-->
            <?php
            $title = $this->get_settings_for_display('payment_section_title');
            if ($title) {
                echo '<h3 id="order_payment_heading">' . esc_html($title) . '</h3>';
            }
	}

	/**
	 * Woocommerce Checkout After Order Review
	 *
	 * Output containing elements. Callback function for the woocommerce_checkout_after_order_review hook.
	 *
	 * This eliminates the need for template overrides.
	 *
	 * @since 3.5.0
	 */
	public function woocommerce_checkout_after_order_review() {
		?>
										<!--close wc_order_review-2-->
						<!--close column-inner-->
					</div>
					<!--close column-2-->
				</div>
				<!--close container-->
			</div>
		<?php
	}

	/**
	 * Add Render Hooks
	 *
	 * Add actions & filters before displaying our widget.
	 *
	 * @since 3.5.0
	 */
	public function add_render_hooks() {
		add_filter( 'woocommerce_form_field_args', [ $this, 'modify_form_field' ], 70, 3 );
		add_filter( 'woocommerce_get_terms_and_conditions_checkbox_text', [ $this, 'woocommerce_terms_and_conditions_checkbox_text' ], 10, 1 );

		add_filter( 'gettext', [ $this, 'filter_gettext' ], 20, 3 );

		add_action( 'woocommerce_checkout_before_customer_details', [ $this, 'woocommerce_checkout_before_customer_details' ], 5 );
		add_action( 'woocommerce_checkout_after_customer_details', [ $this, 'woocommerce_checkout_after_customer_details' ], 95 );
		add_action( 'woocommerce_checkout_before_order_review_heading', [ $this, 'woocommerce_checkout_before_order_review_heading_1' ], 5 );
		add_action( 'woocommerce_checkout_before_order_review_heading', [ $this, 'woocommerce_checkout_before_order_review_heading_2' ], 95 );
		add_action( 'woocommerce_checkout_order_review', [ $this, 'woocommerce_checkout_order_review' ], 15 );
		add_action( 'woocommerce_checkout_after_order_review', [ $this, 'woocommerce_checkout_after_order_review' ], 95 );

		// remove the default login & coupon form because we'll be adding our own forms
		remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form' );
		remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_login_form' );

        add_filter('woocommerce_checkout_fields', [ $this, 'field_empty_label' ]);

        add_filter('woocommerce_order_button_text', [ $this, 'change_purchase_button_text' ]);

        $elementor_use_checkout_template = get_option("elementor_use_checkout_template");

        if( $elementor_use_checkout_template !== 'yes' ) {
            remove_action('woocommerce_review_order_after_shipping', 'woocommerce_shipping_methods');
            remove_action('woocommerce_review_order_before_shipping', 'woocommerce_shipping_methods');
            add_action('woocommerce_review_order_before_shipping', [$this, 'customize_shipping_methods_in_order_review']);
            add_filter('woocommerce_checkout_cart_item_quantity', [$this, 'remove_checkout_cart_item_quantity'], 10, 3);
            add_filter('woocommerce_cart_item_name', [$this, 'change_product_name_structure'], 10, 3);

            add_action( 'woocommerce_after_checkout_billing_form', [$this, 'add_ship_to_different_address_checkbox'] );
            add_action( 'woocommerce_before_checkout_shipping_form', [$this, 'add_shipping_section_title']  );
        }
	}

    public function add_shipping_section_title() {
        $title = $this->get_settings_for_display('shipping_details_section_title_main');
        if ($title) {
            echo '<h3 id="shipping_section_heading">' . esc_html($title) . '</h3>';
        }
    }

    public function add_ship_to_different_address_checkbox() {
        $title = $this->get_settings_for_display('shipping_details_section_title');
        $title = !empty( $title ) ? $title : esc_html__( 'Ship to a different address?', 'tenweb-builder' );
        ?>
        <h3 id="ship-to-different-address">
            <label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
                <input id="ship-to-different-address-checkbox" class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" type="checkbox" name="ship_to_different_address" value="1"> <span><?php echo esc_html($title); ?></span>
            </label>
        </h3>
        <?php
    }


    public function change_product_name_structure($product_name, $cart_item, $cart_item_key) {
        if ( strpos($product_name, '<img') === false ) {
            // Get the product object
            $product = $cart_item['data'];
            // Get the quantity of the current cart item
            $quantity = $cart_item['quantity'];

            // Get the product thumbnail
            $thumbnail = $product->get_image('thumbnail');

            // Prepend the thumbnail to the product name
            $product_name = "<div class='twbb-product-image'>".$thumbnail. "</div>" . " <div class='twbb-product-content'><span>" . $product_name. "</span><span>x". $quantity ."</span></div></div>";
        }
        return $product_name;
    }

    public function remove_checkout_cart_item_quantity( $quantity_html, $cart_item, $cart_item_key ) {
        // Return an empty string to hide the quantity
        return '';
    }

    /**
     * The function append new shipping method structure
     *
     */
    public function customize_shipping_methods_in_order_review() {
        static $rendered = false; // Prevent multiple renders

        if ( $rendered ) {
            return;
        }

        $rendered = true;
        $packages = WC()->shipping->get_packages();
        $chosen_methods = WC()->session->get( 'chosen_shipping_methods' );

        foreach ( $packages as $package_index => $package ) {
            $available_methods = $package['rates'];
            $active_method = isset( $chosen_methods[ $package_index ] ) ? $chosen_methods[ $package_index ] : '';
            ?>
            <tr class="woocommerce-shipping-totals shipping twbb-shipping-totals twbb-shipping-totals-title">
                <th><?php esc_html_e( 'Shipping', 'woocommerce' ); ?></th>
                <td></td>
            </tr>
            <?php
            $available_methods_length = count($available_methods);
            $i = 1;
            foreach ( $available_methods as $method_id => $method ) {
                $is_active = ($method_id === $active_method) ? ' twbb-active-method' : '';
                $last_child = $i === count($available_methods) ? ' twbb-shipping-last' : '';
                    $i++;
                ?>
            <tr class="woocommerce-shipping-totals shipping twbb-shipping-totals twbb-shipping-totals-items<?php echo esc_attr($is_active); ?><?php echo esc_attr($last_child); ?>">
                <th>
                    <?php if( $available_methods_length > 1 ) { ?>
                    <input type="radio" name="shipping_method[<?php echo esc_attr( $package_index ); ?>]"
                           data-index="<?php echo esc_attr( $package_index ); ?>"
                           id="shipping_method_<?php echo esc_attr( $package_index . '_' . sanitize_title( $method_id ) ); ?>"
                           value="<?php echo esc_attr( $method_id ); ?>"
                           class="shipping_method"
                        <?php checked( $method_id, $chosen_methods[ $package_index ] ); ?> />
                    <?php } ?>
                    <label for="shipping_method_<?php echo esc_attr( $package_index . '_' . sanitize_title( $method_id ) ); ?>">
                        <span class="shipping-method-label"><?php echo esc_html( $method->label ); ?></span>
                    </label>
                </th>
                <td>
                    <bdi>
                    <span class="shipping-method-cost">
                        <?php echo wp_kses_post( wc_price( $method->cost ) ); ?>
                    </span>
                    </bdi>
                </td>
            </tr>
        <?php
            }
        }
    }

    /**
     * The function change purchase button text
     *
     * return string
     */
    public function change_purchase_button_text() {
        $setting = $this->get_settings_for_display('purchase_button_text');
        // Get the custom "Place Order" button text
        $button_text = !empty($setting) ? $setting : __( 'Place Order', 'plugin-domain' );
        return $button_text;
    }

    /**
     * The function replace field labels default values to empty if current label setting is empty
     *
     * @params $fields array
     *
     * return array
    */
    public function field_empty_label($fields) {
        $settings = $this->get_settings_for_display();
        $merged_settings = [];

        if (isset($settings['billing_details_form_fields']) && is_array($settings['billing_details_form_fields'])) {
            $merged_settings = array_merge($merged_settings, $settings['billing_details_form_fields']);
        }

        if (isset($settings['shipping_details_form_fields']) && is_array($settings['shipping_details_form_fields'])) {
            $merged_settings = array_merge($merged_settings, $settings['shipping_details_form_fields']);
        }

        if (isset($settings['additional_information_form_fields']) && is_array($settings['additional_information_form_fields'])) {
            $merged_settings = array_merge($merged_settings, $settings['additional_information_form_fields']);
        }

        if ( !empty($merged_settings) ) {
            foreach ($merged_settings as $field) {
                if (isset($field['field_key']) && isset($field['label'])) {
                    // Check if the label is empty
                    if (empty(trim($field['label']))) {
                        $field_key = $field['field_key'];

                        // Remove the WooCommerce field label
                        if (isset($fields['billing'][$field_key])) {
                            $fields['billing'][$field_key]['label'] = ' '; // Set to empty to hide the label
                            $fields['billing'][$field_key]['class'][] = 'no-label'; // Add class for styling
                        } elseif (isset($fields['shipping'][$field_key])) {
                            $fields['shipping'][$field_key]['label'] = ' '; // Hide label for shipping fields
                            $fields['shipping'][$field_key]['class'][] = 'no-label'; // Add class for styling
                        } elseif (isset($fields['order'][$field_key])) {
                            $fields['order'][$field_key]['label'] = ' '; // Hide label for additional fields
                            $fields['order'][$field_key]['class'][] = 'no-label'; // Add class for styling
                        }
                    }
                }
            }
        }
        return $fields;
    }

	/**
	 * Remove Render Hooks
	 *
	 * Remove actions & filters after displaying our widget.
	 *
	 * @since 3.5.0
	 */
	public function remove_render_hooks() {
		remove_filter( 'woocommerce_form_field_args', [ $this, 'modify_form_field' ], 70 );
		remove_filter( 'woocommerce_get_terms_and_conditions_checkbox_text', [ $this, 'woocommerce_terms_and_conditions_checkbox_text' ], 10 );

		remove_filter( 'gettext', [ $this, 'filter_gettext' ], 20 );

		remove_action( 'woocommerce_checkout_before_customer_details', [ $this, 'woocommerce_checkout_before_customer_details' ], 5 );
		remove_action( 'woocommerce_checkout_after_customer_details', [ $this, 'woocommerce_checkout_after_customer_details' ], 95 );
		remove_action( 'woocommerce_checkout_before_order_review_heading', [ $this, 'woocommerce_checkout_before_order_review_heading_1' ], 5 );
		remove_action( 'woocommerce_checkout_before_order_review_heading', [ $this, 'woocommerce_checkout_before_order_review_heading_2' ], 95 );
		remove_action( 'woocommerce_checkout_order_review', [ $this, 'woocommerce_checkout_order_review' ], 15 );
		remove_action( 'woocommerce_checkout_after_order_review', [ $this, 'woocommerce_checkout_after_order_review' ], 95 );
	}

	protected function render() {
		$is_editor = \Elementor\Plugin::instance()->editor->is_edit_mode();

		// Simulate a logged out user so that all WooCommerce sections will render in the Editor
		if ( $is_editor ) {
			$store_current_user = wp_get_current_user()->ID;
			wp_set_current_user( 0 );
		}

		// Add actions & filters before displaying our Widget.
		$this->add_render_hooks();

		// Display our Widget.
		echo do_shortcode( '[woocommerce_checkout]' );
		// Remove actions & filters after displaying our Widget.
		$this->remove_render_hooks();

		// Return to existing logged-in user after widget is rendered.
		if ( $is_editor ) {
			wp_set_current_user( $store_current_user );
		}
	}

}

\Elementor\Plugin::instance()->widgets_manager->register( new Checkout() );
