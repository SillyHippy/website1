<?php
namespace Tenweb_Builder\Widgets\Posts_Skins\Skins;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Skin_Classic extends Skin_Base {

	protected function _register_controls_actions() {
		parent::_register_controls_actions();
        add_action( 'elementor/element/tenweb-posts/section_layout/after_section_end', [ $this, 'register_ordering_controls' ] );
		add_action( 'elementor/element/tenweb-posts/classic_section_design_layout/after_section_end', [ $this, 'register_additional_design_controls' ] );
		add_action( 'elementor/element/tenweb-posts/classic_section_design_content/after_section_end', [ $this, 'register_design_badge_controls' ] );
		add_action( 'elementor/element/tenweb-posts/classic_section_design_image/before_section_end', [ $this, 'register_design_image_control' ] );
	}

	public function get_id() {
		return 'classic';
	}

	public function get_title() {
		return esc_html__( 'Image Top', 'tenweb-builder' );
	}

    public function register_ordering_controls() {
        $this->start_controls_section(
            'section_ordering_layout',
            [
                'label' => esc_html__( 'Meta Data', 'tenweb-builder' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->register_meta_data_controls();
        $this->update_control(
            'meta_data',
            [
                'default' => [],
            ]
        );
        $this->add_control(
            'twbb_meta_order',
            [
                'label' => __('Meta Order', 'plugin-name'),
                'type' => 'draggable_order_control', // Custom control type
                //phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode
                'default' => json_encode([
                    ['id' => 'author', 'label' => 'Author'],
                    ['id' => 'date', 'label' => 'Date'],
                    ['id' => 'time', 'label' => 'Time'],
                    ['id' => 'comments', 'label' => 'Comments'],
                    ['id' => 'modified', 'label' => 'Date Modified'],
                ]),
            ]
        );

        $this->end_controls_section();
    }

	public function register_additional_design_controls() {
		$this->start_controls_section(
			'section_design_box',
			[
				'label' => esc_html__( 'Box', 'tenweb-builder' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'box_border_width',
			[
				'label' => esc_html__( 'Border Width', 'tenweb-builder' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .elementor-post' => 'border-style: solid; border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'box_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'tenweb-builder' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'max' => 200,
					],
					'em' => [
						'max' => 20,
					],
					'rem' => [
						'max' => 20,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-post' => 'border-radius: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'box_padding',
			[
				'label' => esc_html__( 'Padding', 'tenweb-builder' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'range' => [
					'px' => [
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-post' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'content_padding',
			[
				'label' => esc_html__( 'Content Padding', 'tenweb-builder' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'range' => [
					'px' => [
						'max' => 50,
					],
					'em' => [
						'max' => 5,
					],
					'rem' => [
						'max' => 5,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-post__text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
				'separator' => 'after',
			]
		);

        $this->start_controls_tabs( 'bg_effects_tabs' );

		$this->start_controls_tab( 'classic_style_normal',
			[
				'label' => esc_html__( 'Normal', 'tenweb-builder' ),
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'box_shadow',
				'selector' => '{{WRAPPER}} .elementor-post',
			]
		);

		$this->add_control(
			'box_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-post' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'box_border_color',
			[
				'label' => esc_html__( 'Border Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-post' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'classic_style_hover',
			[
				'label' => esc_html__( 'Hover', 'tenweb-builder' ),
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'box_shadow_hover',
				'selector' => '{{WRAPPER}} .elementor-post:hover',
			]
		);

		$this->add_control(
			'box_bg_color_hover',
			[
				'label' => esc_html__( 'Background Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-post:hover' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'box_border_color_hover',
			[
				'label' => esc_html__( 'Border Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-post:hover' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

    public function register_design_badge_controls() {
        $this->start_controls_section(
            'section_design_badge',
            [
                'label' => esc_html__( 'Badge', 'elementor' ),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    $this->get_control_id( 'show_badge' ) => 'yes',
                ],
            ]
        );
        $this->start_controls_tabs( 'tabs_badge_style' );

        $this->start_controls_tab(
            'tab_badge_normal',
            [
                'label' => __( 'Normal', 'tenweb-builder' ),
            ]
        );

        $this->add_control(
            'heading_badge_style',
            [
                'label' => esc_html__( 'Badge', 'tenweb-builder' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'badge_position',
            [
                'label' => esc_html__( 'Position', 'elementor' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__( 'Left', 'elementor' ),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__( 'Center', 'elementor' ),
                        'icon' => 'eicon-h-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__( 'Right', 'elementor' ),
                        'icon' => 'eicon-h-align-right',
                    ],
                ],
                'default' => 'left',
                'prefix_class' => 'twbb-post__badge-align-',
                'separator' => 'before',
                'selectors' => [
                    '{{WRAPPER}} .elementor-post__badge' => '{{VALUE}}: 0',
                ],
            ]
        );


        $this->add_control(
            'badge_bg_color',
            [
                'label' => esc_html__( 'Background Color', 'tenweb-builder' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-posts--skin-classic .elementor-post__badge' => 'background-color: {{VALUE}};',
                ],
                'default' => '#FFFFFF',
            ]
        );

        $this->add_control(
            'twbb_badge_color',
            [
                'label' => esc_html__( 'Text Color', 'tenweb-builder' ),
                'type' => Controls_Manager::COLOR,
                'global' => [
                    'default' => Global_Colors::COLOR_SECONDARY,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-posts--skin-classic .elementor-post__badge' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'badge_radius',
            [
                'label' => esc_html__( 'Border Radius', 'tenweb-builder' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                'range' => [
                    'px' => [
                        'max' => 50,
                    ],
                    'em' => [
                        'max' => 5,
                    ],
                    'rem' => [
                        'max' => 5,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-posts--skin-classic .elementor-post__badge' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'badge_border_width',
            [
                'label' => esc_html__( 'Border Width', 'tenweb-builder' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-posts--skin-classic .elementor-post__badge' => 'border-style: solid; border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ],
            ]
        );

        $this->add_control(
            'badge_border_color',
            [
                'label' => esc_html__( 'Border Color', 'tenweb-builder' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-posts--skin-classic .elementor-post__badge' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'badge_spacing',
            [
                'label' => esc_html__( 'Spacing', 'tenweb-builder' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
                'range' => [
                    'px' => [
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-posts--skin-classic .elementor-post__badge' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ],
            ]
        );

        $this->add_control(
            'badge_padding',
            [
                'label' => esc_html__( 'Padding', 'tenweb-builder' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
                'range' => [
                    'px' => [
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-posts--skin-classic .elementor-post__badge' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'badge_typography',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_ACCENT,
                ],
                'selector' => '{{WRAPPER}} .elementor-posts--skin-classic .elementor-post__badge',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_badge_hover',
            [
                'label' => __( 'Hover', 'tenweb-builder' ),
            ]
        );

            $this->add_control(
            'badge_bg_color_hover',
            [
                'label' => esc_html__( 'Background Color', 'tenweb-builder' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-posts--skin-classic .elementor-post__badge:hover' => 'background-color: {{VALUE}};',
                ],
                'default' => '#FFFFFF',
            ]
        );

            $this->add_control(
                'twbb_badge_color_hover',
                [
                    'label' => esc_html__( 'Text Color', 'tenweb-builder' ),
                    'type' => Controls_Manager::COLOR,
                    'global' => [
                        'default' => Global_Colors::COLOR_TEXT,
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .elementor-posts--skin-classic .elementor-post__badge:hover' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'badge_border_color_hover',
                [
                    'label' => esc_html__( 'Border Color', 'tenweb-builder' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .elementor-posts--skin-classic .elementor-post__badge:hover' => 'border-color: {{VALUE}}',
                    ],
                ]
            );


        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();
    }

    public function register_design_image_control() {
        $this->add_control(
            'thumbnail_alignment',
            [
                'label' => esc_html__( 'Vertical Alignment', 'tenweb-builder' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'flex-start',
                'options' => [
                    'flex-start' => esc_html__( 'Top', 'tenweb-builder' ),
                    'center' => esc_html__( 'Center', 'tenweb-builder' ),
                    'flex-end' => esc_html__( 'Bottom', 'tenweb-builder' ),
                ],
                'selectors' => [
                    '{{WRAPPER}} .twbb-image-container' => 'align-items: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'image_hover_animation',
            [
                'label' => esc_html__( 'Image Hover Animation', 'tenweb-builder' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'zoom-in',
                'options' => [
                    'zoom-in' => esc_html__( 'Zoom In', 'tenweb-builder' ),
                    'zoom-out' => esc_html__( 'Zoom Out', 'tenweb-builder' ),
                    'none' => esc_html__( 'None', 'tenweb-builder' ),
                ],
                'prefix_class' => 'twbb-posts-image-animation_',
            ]
        );

        $this->add_control(
            'image_hover_animation_duration',
            [
                'label' => esc_html__( 'Animation Duration', 'elementor' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'fast',
                'options' => [
                    'slow' => esc_html__( 'Slow', 'elementor' ),
                    'fast' => esc_html__( 'Fast', 'elementor' ),
                ],
                'prefix_class' => 'twbb-posts-image-animation-duration-',
                'condition' => [
                    $this->get_control_id( 'image_hover_animation!' ) => 'none',
                ],
            ]
        );

        $this->add_responsive_control(
            'twbb_full_height',
            [
                'label' => esc_html__( 'Full height', 'tenweb-builder' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'tenweb-builder' ),
                'label_off' => esc_html__( 'No', 'tenweb-builder' ),
                'default' => 'no',
                'separator' => 'before',
                'prefix_class' => 'twbb-posts-%s-fullHeight_',
                'condition' => [
                    $this->get_control_id( 'thumbnail!' ) => 'top',
                    $this->get_control_id( 'thumbnail!' ) => 'behind-text',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'background_overlay_gradient',
                'types' => [ 'gradient' ],
                'selector' => '{{WRAPPER}} .twbb-image-overlay',
            ]
        );

    }

    protected function render_meta_data() {
        /** @var array $settings e.g. [ 'author', 'date', ... ] */
        $settings = $this->get_instance_value( 'meta_data' );
        $order_settings = json_decode($this->get_instance_value( 'twbb_meta_order' ), 1);
        if ( empty( $settings ) || !is_array($settings)) {
            return;
        }

        ?>
        <div class="elementor-post__avatar-meta-data-container">
            <?php if ( $this->get_instance_value( 'show_avatar' ) ) { ?>
            <div class="elementor-post__avatar">
                <?php echo get_avatar( get_the_author_meta( 'ID' ), 128, '', get_the_author_meta( 'display_name' ) ); ?>
            </div>
            <?php } ?>
            <div class="elementor-post__meta-data">
                <?php
                if ( empty($order_settings) ) {
                    $order_settings = [
                        ['id' => 'author', 'label' => 'Author'],
                        ['id' => 'date', 'label' => 'Date'],
                        ['id' => 'time', 'label' => 'Time'],
                        ['id' => 'comments', 'label' => 'Comments'],
                        ['id' => 'modified', 'label' => 'Date Modified'],
                    ];
                }
                foreach ($order_settings as $order ) {
                    switch ($order['id']) {
                        case "author":
                            if ( in_array( 'author', $settings, true ) ) {
                                $this->render_author();
                            }
                            break;
                        case "date":
                            if ( in_array( 'date', $settings, true ) ) {
                                $this->render_date_by_type();
                            }
                            break;
                        case "time":
                            if ( in_array( 'time', $settings, true ) ) {
                                $this->render_time();
                            }
                            break;
                        case "comments":
                            if ( in_array( 'comments', $settings, true ) ) {
                                $this->render_comments();
                            }
                            break;
                        case "modified":
                            if ( in_array( 'modified', $settings, true ) ) {
                                $this->render_date_by_type( 'modified' );
                            }
                            break;
                    }
                }
                ?>
            </div>
        </div>
        <?php
    }

}
