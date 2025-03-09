<?php
namespace Tenweb_Builder\Widgets\Posts_Skins\Skins;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Stroke;
use Elementor\Icons_Manager;
use Elementor\Skin_Base as Elementor_Skin_Base;
use Elementor\Utils;
use Elementor\Widget_Base;
use Tenweb_Builder\Widgets\Posts_Skins\Traits\Button_Widget_Trait;
use Tenweb_Builder\Widgets\Posts_Skins\Widgets\Posts_Base;
use Tenweb_Builder\ElementorPro\Core\Utils as ProUtils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

abstract class Skin_Base extends Elementor_Skin_Base {
	use Button_Widget_Trait;

	/**
	 * @var string Save current permalink to avoid conflict with plugins the filters the permalink during the post render.
	 */
	protected $current_permalink;

	protected function _register_controls_actions() {

		add_action( 'elementor/element/tenweb-posts/section_layout/before_section_end', [ $this, 'register_controls' ] );
        add_action( 'elementor/element/tenweb-posts/section_query/after_section_end', [ $this, 'register_slider_controls' ] );
		add_action( 'elementor/element/tenweb-posts/section_query/after_section_end', [ $this, 'register_style_sections' ] );
	}

	public function register_style_sections( Widget_Base $widget ) {
		$this->parent = $widget;

		$this->register_design_controls();
	}

	public function register_controls( Widget_Base $widget ) {
		$this->parent = $widget;
		$this->register_columns_controls();
		$this->register_post_count_control();
		$this->register_thumbnail_controls();
		$this->register_title_controls();
		$this->register_excerpt_controls();
		$this->register_read_more_controls();
		$this->register_link_controls();
        $this->register_badge_controls();
        $this->register_avatar_controls();
	}

    public function register_slider_controls() {
        $this->start_controls_section(
            'slider_settings',
            [
                'label' => esc_html__( 'Slider Settings', 'tenweb-builder' ),
                'tab' => Controls_Manager::TAB_CONTENT,
                'condition' => [
                    $this->get_control_id( 'slides_view' ) => 'yes',
                ],
            ]
        );

        $this->add_responsive_control(
            'slides_per_view',
            [
                'label' => esc_html__( 'Slides Per View', 'tenweb-builder' ),
                'type' => Controls_Manager::SELECT,
                'default' => '',
                'options' => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                    '6' => '6',
                ],
                'frontend_available' => true,
            ]
        );
        $slides_per_view = range( 1, 10 );
        $slides_per_view = array_combine( $slides_per_view, $slides_per_view );
        $this->add_control(
            'slides_to_scroll',
            [
                'type' => Controls_Manager::SELECT,
                'label' => __( 'Slides to Scroll', 'tenweb-builder' ),
                'description' => __( 'Set how many slides are scrolled per swipe.', 'tenweb-builder' ),
                'options' => [ '' => __( 'Default', 'tenweb-builder' ) ] + $slides_per_view,
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'autoplay',
            [
                'label' => __( 'Autoplay', 'tenweb-builder' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'separator' => 'before',
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'autoplay_speed',
            [
                'label' => __( 'Autoplay Speed', 'tenweb-builder' ),
                'type' => Controls_Manager::NUMBER,
                'default' => 5000,
                'condition' => [
                    $this->get_control_id( 'autoplay' ) => 'yes',
                ],
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'loop',
            [
                'label' => __( 'Infinite Loop', 'tenweb-builder' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'disable_on_interaction',
            [
                'label' => __( 'Disable on Interaction', 'tenweb-builder' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'condition' => [
                    $this->get_control_id( 'autoplay' ) => 'yes',
                ],
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'pause_on_mouseover',
            [
                'label' => __( 'Pause on Mouseover', 'tenweb-builder' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'condition' => [
                    $this->get_control_id( 'autoplay' ) => 'yes',
                ],
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'slider_navigation',
            [
                'label' => __( 'Navigation', 'tenweb-builder' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'both',
                'options' => [
                    'both' => __( 'Arrows and Dots', 'tenweb-builder' ),
                    'arrows' => __( 'Arrows', 'tenweb-builder' ),
                    'dots' => __( 'Dots', 'tenweb-builder' ),
                    'none' => __( 'None', 'tenweb-builder' ),
                ],
                'render_type' => 'template',
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'slider_navigation_position',
            [
                'label' => __( 'Navigation Position', 'tenweb-builder' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'outside',
                'options' => [
                    'inside' => __( 'Inside', 'tenweb-builder' ),
                    'outside' => __( 'Outside', 'tenweb-builder' ),
                ],
                'prefix_class' => 'tenweb-posts-slider--navigation-position-',
                'condition' => [
                    $this->get_control_id( 'slider_navigation!' ) => 'none',
                ],
            ]
        );

        $this->add_control(
            'slider_view_type',
            [
                'label' => __( 'View Type', 'tenweb-builder' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'cut_next',
                'options' => [
                    'cut_next' => __( 'Cut Next', 'tenweb-builder' ),
                    'full' => __( 'Full', 'tenweb-builder' ),
                ],
                'prefix_class' => 'tenweb-posts-slider--slider-view-type-',
            ]
        );

        $this->end_controls_section();
    }

    public function register_badge_controls() {
        $this->add_control(
            'show_badge',
            [
                'label' => esc_html__( 'Badge', 'tenweb-builder' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'tenweb-builder' ),
                'label_off' => esc_html__( 'Hide', 'tenweb-builder' ),
                'default' => 'yes',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'badge_on_image',
            [
                'label' => esc_html__( 'Badge on image', 'tenweb-builder' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'tenweb-builder' ),
                'label_off' => esc_html__( 'Hide', 'tenweb-builder' ),
                'default' => 'no',
                'condition' => [
                    $this->get_control_id( 'show_badge' ) => 'yes',
                    '_skin!' => 'on_image',
                ],
                'prefix_class' => 'twbb-post__badge-onimage_',
                'render_type' => 'template',
            ]
        );

        $this->add_control(
            'badge_taxonomy',
            [
                'label' => esc_html__( 'Badge Taxonomy', 'tenweb-builder' ),
                'type' => Controls_Manager::SELECT2,
                'label_block' => true,
                'default' => 'category',
                'options' => $this->get_taxonomies(),
                'condition' => [
                    $this->get_control_id( 'show_badge' ) => 'yes',
                ],
            ]
        );
    }

    public function register_avatar_controls() {
        $this->add_control(
            'show_avatar',
            [
                'label' => esc_html__( 'Avatar', 'tenweb-builder' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'tenweb-builder' ),
                'label_off' => esc_html__( 'Hide', 'tenweb-builder' ),
                'return_value' => 'show-avatar',
                'default' => 'show-avatar',
                'separator' => 'before',
                'prefix_class' => 'elementor-posts--',
                'render_type' => 'template',
                'condition' => [
                    $this->get_control_id( 'thumbnail!' ) => 'none',
                ],
            ]
        );
        $this->add_control(
            'twbb_avatar_position',
            [
                'label' => esc_html__( 'Avatar Position', 'elementor' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__( 'Left', 'elementor' ),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'right' => [
                        'title' => esc_html__( 'Right', 'elementor' ),
                        'icon' => 'eicon-h-align-right',
                    ],
                ],
                'default' => 'left',
                'prefix_class' => 'twbb-post__avatar-align-',
                'condition' => [
                    $this->get_control_id( 'show_avatar!' ) => '',
                ],
            ]
        );
    }


    protected function get_taxonomies() {
        $taxonomies = get_taxonomies( [ 'show_in_nav_menus' => true ], 'objects' );

        $options = [ '' => '' ];

        foreach ( $taxonomies as $taxonomy ) {
            $options[ $taxonomy->name ] = $taxonomy->label;
        }

        return $options;
    }


    public function register_design_controls() {
		$this->register_design_layout_controls();
		$this->register_design_image_controls();
		$this->register_design_content_controls();
		$this->register_design_slider_controls();
	}

	protected function register_thumbnail_controls() {
		$this->add_responsive_control(
			'thumbnail',
			[
				'label' => esc_html__( 'Image Position', 'tenweb-builder' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'top',
                'tablet_default' => 'top',
                'mobile_default' => 'top',
                'options' => [
					'top' => esc_html__( 'Top', 'tenweb-builder' ),
					'left' => esc_html__( 'Left', 'tenweb-builder' ),
					'right' => esc_html__( 'Right', 'tenweb-builder' ),
                    'behind-text' => esc_html__( 'Behind Text', 'tenweb-builder' ),
					'none' => esc_html__( 'None', 'tenweb-builder' ),
				],
				'prefix_class' => 'elementor-posts--thumbnail%s-',
                'render_type' => 'template',
                'frontend_available' => true,
                'condition' => [
                    '_skin!' => ['on_image','image_left'],
                ],
            ]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'thumbnail_size',
				'default' => 'medium',
				'exclude' => [ 'custom' ], //phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude
				'condition' => [
					$this->get_control_id( 'thumbnail!' ) => 'none',
				],
				'prefix_class' => 'elementor-posts--thumbnail-size-',
			]
		);

		$this->add_responsive_control(
			'item_ratio',
			[
				'label' => esc_html__( 'Image Ratio', 'tenweb-builder' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0.66,
				],
				'tablet_default' => [
					'size' => '',
				],
				'mobile_default' => [
					'size' => 0.5,
				],
				'range' => [
					'px' => [
						'min' => 0.1,
						'max' => 2,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-posts-container .elementor-post__thumbnail' => 'padding-bottom: calc( {{SIZE}} * 100% );',
					'{{WRAPPER}}:after' => 'content: "{{SIZE}}";',
				],
            ]
		);

		$this->add_responsive_control(
			'image_width',
			[
				'label' => esc_html__( 'Image Width', 'tenweb-builder' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'range' => [
					'%' => [
						'min' => 10,
						'max' => 100,
					],
					'px' => [
						'min' => 10,
						'max' => 600,
					],
					'em' => [
						'min' => 1,
						'max' => 60,
					],
					'rem' => [
						'min' => 1,
						'max' => 60,
					],
				],
				'default' => [
					'size' => 100,
					'unit' => '%',
				],
				'tablet_default' => [
					'size' => '',
					'unit' => '%',
				],
				'mobile_default' => [
					'size' => 100,
					'unit' => '%',
				],
				'selectors' => [
					'{{WRAPPER}} .twbb-image-container' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					$this->get_control_id( 'thumbnail' ) => 'top',
                    '_skin!' => ['on_image','image_left']
				],
			]
		);

		$this->add_responsive_control(
			'image_width_left',
			[
				'label' => esc_html__( 'Image Width', 'tenweb-builder' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'range' => [
					'%' => [
						'min' => 10,
						'max' => 100,
					],
					'px' => [
						'min' => 10,
						'max' => 600,
					],
					'em' => [
						'min' => 1,
						'max' => 60,
					],
					'rem' => [
						'min' => 1,
						'max' => 60,
					],
				],
				'default' => [
					'size' => 50,
					'unit' => '%',
				],
				'tablet_default' => [
					'size' => '',
					'unit' => '%',
				],
				'mobile_default' => [
					'size' => '',
					'unit' => '%',
				],
				'selectors' => [
					'{{WRAPPER}} .twbb-image-container' => 'width: {{SIZE}}{{UNIT}};',
				],
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'relation' => 'or',
                            'terms' => [
                                [
                                    'name' => $this->get_control_id( 'thumbnail' ),
                                    'operator' => '===',
                                    'value' => 'right',
                                ],
                                [
                                    'name' => $this->get_control_id( 'thumbnail' ),
                                    'operator' => '===',
                                    'value' => 'left',
                                ],

                            ],
                        ],
                        [
                            'name' => '_skin',
                            'operator' => '===',
                            'value' => 'image_left',
                        ],
                    ],
                ],
			]
		);
	}

	protected function register_columns_controls() {
        $this->add_control(
            'slides_view',
            [
                'label' => esc_html__( 'Slides View', 'tenweb-builder' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'On', 'tenweb-builder' ),
                'label_off' => esc_html__( 'Off', 'tenweb-builder' ),
                'default' => 'no',
                'separator' => 'after',
            ]
        );

		$this->add_responsive_control(
			'columns',
			[
				'label' => esc_html__( 'Columns', 'tenweb-builder' ),
                'type' => Controls_Manager::SELECT,
				'default' => '3',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'options' => [
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				],
                'condition' => [
                    $this->get_control_id( 'slides_view!' ) => 'yes',
                ],
				'prefix_class' => 'elementor-grid%s-',
				'frontend_available' => true,
                'render_type' => 'template',
			]
		);
	}

	protected function register_post_count_control() {
		$this->add_control(
			'posts_per_page',
			[
				'label' => esc_html__( 'Posts Per Page', 'tenweb-builder' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 6,
			]
		);
	}

	protected function register_title_controls() {
		$this->add_control(
			'show_title',
			[
				'label' => esc_html__( 'Title', 'tenweb-builder' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'tenweb-builder' ),
				'label_off' => esc_html__( 'Hide', 'tenweb-builder' ),
				'default' => 'yes',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'title_tag',
			[
				'label' => esc_html__( 'Title HTML Tag', 'tenweb-builder' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
					'span' => 'span',
					'p' => 'p',
				],
				'default' => 'h3',
				'condition' => [
					$this->get_control_id( 'show_title' ) => 'yes',
				],
			]
		);

	}

	protected function register_excerpt_controls() {
		$this->add_control(
			'show_excerpt',
			[
				'label' => esc_html__( 'Excerpt', 'tenweb-builder' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'tenweb-builder' ),
				'label_off' => esc_html__( 'Hide', 'tenweb-builder' ),
				'default' => 'yes',
			]
		);

        $this->add_control(
            'apply_to_custom_excerpt',
            [
                'label' => esc_html__( 'Apply to custom Excerpt', 'tenweb-builder' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'tenweb-builder' ),
                'label_off' => esc_html__( 'No', 'tenweb-builder' ),
                'default' => 'no',
                'condition' => [
                    $this->get_control_id( 'show_excerpt' ) => 'yes',
                ],
            ]
        );

		$this->add_control(
			'excerpt_length',
			[
				'label' => esc_html__( 'Excerpt Length', 'tenweb-builder' ),
				'type' => Controls_Manager::NUMBER,
				/** This filter is documented in wp-includes/formatting.php */
				'default' => apply_filters( 'excerpt_length', 25 ),
				'condition' => [
					$this->get_control_id( 'show_excerpt' ) => 'yes',
					$this->get_control_id( 'apply_to_custom_excerpt' ) => 'yes',
				],
			]
		);
	}

	protected function register_read_more_controls() {
		$this->add_control(
			'show_read_more',
			[
				'label' => esc_html__( 'Read More', 'tenweb-builder' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'tenweb-builder' ),
				'label_off' => esc_html__( 'Hide', 'tenweb-builder' ),
				'default' => 'yes',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'read_more_text',
			[
				'label' => esc_html__( 'Read More Text', 'tenweb-builder' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'ai' => [
					'active' => false,
				],
				'default' => esc_html__( 'Read More', 'tenweb-builder' ),
				'condition' => [
					$this->get_control_id( 'show_read_more' ) => 'yes',
				],
			]
		);

        $this->add_control(
            'read_more_icon',
            [
                'label' => __('Read More Icon', 'plugin-name'),
                'type' => \Elementor\Controls_Manager::ICONS,
                'default' => [
                    'value' => 'fas fa-angle-double-right',
                    'library' => 'fa-solid',
                ],
                'condition' => [
                    $this->get_control_id( 'show_read_more' ) => 'yes',
                ],
            ]
        );

        $this->add_control(
        'read_more_icon_size',
            [
                'label' => esc_html__( 'Read More Icon Size', 'tenweb-builder' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem', 'custom' ],
                'range' => [
                    'px' => [
                        'min' => 5,
                        'max' => 50,
                    ],
                    'em' => [
                        'min' => 0.5,
                        'max' => 5,
                    ],
                    'rem' => [
                        'min' => 0.5,
                        'max' => 5,
                    ],
                ],
                'default' => [
                    'size' => 12,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-post__read-more i' => 'font-size: {{SIZE}}{{UNIT}}',
                    '{{WRAPPER}} .elementor-post__read-more svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
                ],
                'condition' => [
                    $this->get_control_id( 'show_read_more' ) => 'yes',
                ],
            ]
        );
	}

	protected function register_link_controls() {
		$this->add_control(
			'open_new_tab',
			[
				'label' => esc_html__( 'Open in new window', 'tenweb-builder' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'tenweb-builder' ),
				'label_off' => esc_html__( 'No', 'tenweb-builder' ),
				'default' => 'no',
				'render_type' => 'none',
                'condition' => [
                    $this->get_control_id( 'show_read_more' ) => 'yes',
                ],
			]
		);
	}

	protected function get_optional_link_attributes_html() {
		$settings = $this->parent->get_settings();
		$new_tab_setting_key = $this->get_control_id( 'open_new_tab' );
		$optional_attributes_html = (isset($settings[ $new_tab_setting_key ]) && 'yes' === $settings[ $new_tab_setting_key ]) ? 'target="_blank"' : '';

		return $optional_attributes_html;
	}

	protected function register_meta_data_controls() {
		$this->add_control(
			'meta_data',
			[
                    'label' => esc_html__( 'Meta Data', 'tenweb-builder' ),
				'label_block' => true,
				'type' => Controls_Manager::SELECT2,
				'default' => [ 'date', 'comments' ],
				'multiple' => true,
				'options' => [
					'author' => esc_html__( 'Author', 'tenweb-builder' ),
					'date' => esc_html__( 'Date', 'tenweb-builder' ),
					'time' => esc_html__( 'Time', 'tenweb-builder' ),
					'comments' => esc_html__( 'Comments', 'tenweb-builder' ),
					'modified' => esc_html__( 'Date Modified', 'tenweb-builder' ),
				],
				'separator' => 'before',
			]
		);

        $this->add_control(
            'twbb_meta_separator',
            [
                'label' => esc_html__( 'Separator Between', 'elementor-pro' ),
                'type' => Controls_Manager::TEXT,
                'default' => '///',
                'ai' => [
                    'active' => false,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-post__meta-data span + span:before' => 'content: "{{VALUE}}"',
                ],
                'condition' => [
                    $this->get_control_id( 'meta_data!' ) => [],
                    $this->get_control_id( 'show_avatar' ) => '',
                ],
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

    }

	/**
	 * Style Tab
	 */
	protected function register_design_layout_controls() {
		$this->start_controls_section(
			'section_design_layout',
			[
				'label' => esc_html__( 'Layout', 'tenweb-builder' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'column_gap',
			[
				'label' => esc_html__( 'Columns Gap', 'tenweb-builder' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'custom' ],
				'default' => [
					'size' => 30,
				],
				'range' => [
					'px' => [
						'max' => 100,
					],
					'em' => [
						'max' => 10,
					],
					'rem' => [
						'max' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--grid-column-gap: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'row_gap',
			[
				'label' => esc_html__( 'Rows Gap', 'tenweb-builder' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'custom' ],
				'default' => [
					'size' => 35,
				],
				'range' => [
					'px' => [
						'max' => 100,
					],
					'em' => [
						'max' => 10,
					],
					'rem' => [
						'max' => 10,
					],
				],
				'frontend_available' => true,
				'selectors' => [
					'{{WRAPPER}}' => '--grid-row-gap: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function register_design_image_controls() {
		$this->start_controls_section(
			'section_design_image',
			[
				'label' => esc_html__( 'Image', 'tenweb-builder' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					$this->get_control_id( 'thumbnail!' ) => 'none',
				],
			]
		);

		$this->add_responsive_control(
			'img_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'tenweb-builder' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .elementor-post__thumbnail' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					$this->get_control_id( 'thumbnail!' ) => 'none',
				],
			]
		);

		$this->add_responsive_control(
			'image_spacing',
			[
				'label' => esc_html__( 'Spacing', 'tenweb-builder' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'max' => 100,
					],
					'em' => [
						'max' => 10,
					],
					'rem' => [
						'max' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-posts--skin-image_left .twbb-image-container' => 'margin-right: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}}.elementor-posts--thumbnail-right .twbb-image-container' => 'margin-left: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}}.elementor-posts--thumbnail-top .elementor-post__thumbnail__link' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
				'default' => [
					'size' => 20,
				],
				'condition' => [
					$this->get_control_id( 'thumbnail!' ) => 'none',
                    '_skin!' => 'on_image',
				],
			]
		);

		$this->start_controls_tabs( 'thumbnail_effects_tabs' );

		$this->start_controls_tab( 'normal',
			[
				'label' => esc_html__( 'Normal', 'tenweb-builder' ),
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'thumbnail_filters',
				'selector' => '{{WRAPPER}} .elementor-post__thumbnail img',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'hover',
			[
				'label' => esc_html__( 'Hover', 'tenweb-builder' ),
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'thumbnail_hover_filters',
				'selector' => '{{WRAPPER}} .elementor-post:hover .elementor-post__thumbnail img',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function register_design_content_controls() {
		$this->start_controls_section(
			'section_design_content',
			[
				'label' => esc_html__( 'Content', 'tenweb-builder' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'heading_title_style',
			[
				'label' => esc_html__( 'Title', 'tenweb-builder' ),
				'type' => Controls_Manager::HEADING,
				'condition' => [
					$this->get_control_id( 'show_title' ) => 'yes',
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => esc_html__( 'Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'global' => [
                    'default' => Global_Colors::COLOR_TEXT
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-post__title, {{WRAPPER}} .elementor-post__title a' => 'color: {{VALUE}};',
				],
				'condition' => [
					$this->get_control_id( 'show_title' ) => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'global' => [
					'default' => 'globals/typography?id=twbb_h5',
				],
				'selector' => '{{WRAPPER}} .elementor-post__title, {{WRAPPER}} .elementor-post__title a',
				'condition' => [
					$this->get_control_id( 'show_title' ) => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Stroke::get_type(),
			[
				'name' => 'text_stroke',
				'selector' => '{{WRAPPER}} .elementor-post__title',
			]
		);

		$this->add_responsive_control(
			'title_spacing',
			[
				'label' => esc_html__( 'Spacing', 'tenweb-builder' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'max' => 100,
					],
					'em' => [
						'max' => 10,
					],
					'rem' => [
						'max' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-post__title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					$this->get_control_id( 'show_title' ) => 'yes',
				],
			]
		);

        $this->add_responsive_control( 'title_padding', [
                'label' => __('Padding', 'tenweb-builder'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-post__title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ],
            ]
        );

        $this->add_control(
            'title_alignment',
            [
                'label' => esc_html__( 'Alignment', 'tenweb-builder' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__( 'Left', 'tenweb-builder' ),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__( 'Center', 'tenweb-builder' ),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__( 'Right', 'tenweb-builder' ),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-post__title' => 'text-align: {{VALUE}}',
                ],
            ]
        );

	  $this->add_control(
		  'heading_excerpt_style',
		  [
			  'label' => esc_html__( 'Excerpt', 'tenweb-builder' ),
			  'type' => Controls_Manager::HEADING,
			  'separator' => 'before',
			  'condition' => [
				  $this->get_control_id( 'show_excerpt' ) => 'yes',
			  ],
		  ]
	  );

	  $this->add_control(
		  'excerpt_color',
		  [
			  'label' => esc_html__( 'Color', 'tenweb-builder' ),
			  'type' => Controls_Manager::COLOR,
              'global' => [
                  'default' => Global_Colors::COLOR_TEXT,
              ],
			  'selectors' => [
				  '{{WRAPPER}} .elementor-post__excerpt p' => 'color: {{VALUE}};',
			  ],
			  'condition' => [
				  $this->get_control_id( 'show_excerpt' ) => 'yes',
			  ],
		  ]
	  );

	  $this->add_group_control(
		  Group_Control_Typography::get_type(),
		  [
			  'name' => 'excerpt_typography',
			  'global' => [
                  'default' => 'globals/typography?id=twbb_p5',
			  ],
			  'selector' => '{{WRAPPER}} .elementor-post__excerpt p',
			  'condition' => [
				  $this->get_control_id( 'show_excerpt' ) => 'yes',
			  ],
		  ]
	  );

	  $this->add_responsive_control(
		  'excerpt_spacing',
		  [
			  'label' => esc_html__( 'Spacing', 'tenweb-builder' ),
			  'type' => Controls_Manager::SLIDER,
			  'size_units' => [ 'px', 'em', 'rem', 'custom' ],
			  'range' => [
				  'px' => [
					  'max' => 100,
				  ],
				  'em' => [
					  'max' => 10,
				  ],
				  'rem' => [
					  'max' => 10,
				  ],
			  ],
			  'selectors' => [
				  '{{WRAPPER}} .elementor-post__excerpt' => 'margin-bottom: {{SIZE}}{{UNIT}};',
			  ],
			  'condition' => [
				  $this->get_control_id( 'show_excerpt' ) => 'yes',
			  ],
		  ]
	  );

	  $this->add_responsive_control( 'excerpt_padding', [
			  'label' => __('Padding', 'tenweb-builder'),
			  'type' => Controls_Manager::DIMENSIONS,
			  'size_units' => [ 'px', 'em', '%' ],
			  'selectors' => [
				  '{{WRAPPER}} .elementor-post__excerpt' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
			  ],
		  ]
	  );

	  $this->add_control(
		  'excerpt_alignment',
		  [
			  'label' => esc_html__( 'Alignment', 'tenweb-builder' ),
			  'type' => Controls_Manager::CHOOSE,
			  'options' => [
				  'left' => [
					  'title' => esc_html__( 'Left', 'tenweb-builder' ),
					  'icon' => 'eicon-text-align-left',
				  ],
				  'center' => [
					  'title' => esc_html__( 'Center', 'tenweb-builder' ),
					  'icon' => 'eicon-text-align-center',
				  ],
				  'right' => [
					  'title' => esc_html__( 'Right', 'tenweb-builder' ),
					  'icon' => 'eicon-text-align-right',
				  ],
			  ],
			  'selectors' => [
				  '{{WRAPPER}} .elementor-post__excerpt' => 'text-align: {{VALUE}}',
			  ],
		  ]
	  );

	  $this->add_control(
		  'twbb_heading_avatar_style',
		  [
			  'label' => esc_html__( 'Avatar', 'tenweb-builder' ),
			  'type' => Controls_Manager::HEADING,
			  'separator' => 'before',
              'condition' => [
                  $this->get_control_id( 'show_avatar!' ) => '',
              ],
		  ]
	  );

	  $this->add_responsive_control( 'avatar_padding', [
			  'label' => __('Padding', 'tenweb-builder'),
			  'type' => Controls_Manager::DIMENSIONS,
			  'size_units' => [ 'px', 'em', '%' ],
			  'selectors' => [
				  '{{WRAPPER}} .elementor-post__avatar-meta-data-container .elementor-post__avatar' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
			  ],
              'condition' => [
                  $this->get_control_id( 'show_avatar!' ) => '',
              ],
		  ]
	  );

        $this->add_control(
			'heading_meta_style',
			[
				'label' => esc_html__( 'Meta', 'tenweb-builder' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					$this->get_control_id( 'meta_data!' ) => [],
				],
			]
		);

		$this->add_control(
			'meta_color',
			[
				'label' => esc_html__( 'Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
                'global' => [
                    'default' => Global_Colors::COLOR_TEXT,
                ],
				'selectors' => [
					'{{WRAPPER}} .elementor-post__meta-data' => 'color: {{VALUE}};',
				],
				'condition' => [
					$this->get_control_id( 'meta_data!' ) => [],
				],
			]
		);

		$this->add_control(
			'meta_separator_color',
			[
				'label' => esc_html__( 'Separator Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-post__meta-data span:before' => 'color: {{VALUE}};',
				],
				'condition' => [
					$this->get_control_id( 'meta_data!' ) => [],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'meta_typography',
				'global' => [
                    'default' => 'globals/typography?id=twbb_p5',
				],
				'selector' => '{{WRAPPER}} .elementor-post__meta-data',
				'condition' => [
					$this->get_control_id( 'meta_data!' ) => [],
				],
			]
		);

		$this->add_responsive_control(
			'meta_spacing',
			[
				'label' => esc_html__( 'Spacing', 'tenweb-builder' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'max' => 100,
					],
					'em' => [
						'max' => 10,
					],
					'rem' => [
						'max' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-post__avatar-meta-data-container' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					$this->get_control_id( 'meta_data!' ) => [],
				],
			]
		);

        $this->add_responsive_control( 'meta_padding', [
                'label' => __('Padding', 'tenweb-builder'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-post__meta-data' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ],
            ]
        );

        $this->add_control(
            'meta_alignment',
            [
                'label' => esc_html__( 'Alignment', 'tenweb-builder' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__( 'Left', 'tenweb-builder' ),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__( 'Center', 'tenweb-builder' ),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__( 'Right', 'tenweb-builder' ),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-post__avatar-meta-data-container' => 'justify-content: {{VALUE}}',
                ],
            ]
        );


        $this->add_control(
			'heading_readmore_style',
			[
				'label' => esc_html__( 'Read More', 'tenweb-builder' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					$this->get_control_id( 'show_read_more' ) => 'yes',
				],
			]
		);

		$this->add_control(
			'read_more_color',
			[
				'label' => esc_html__( 'Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'global' => [
					'default' => Global_Colors::COLOR_ACCENT,
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-post__read-more' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementor-post__read-more svg' => 'fill: {{VALUE}};',
				],
				'condition' => [
					$this->get_control_id( 'show_read_more' ) => 'yes',
				],
			]
		);

		$this->add_control(
			'read_more_hover_color',
			[
				'label' => esc_html__( 'Hover Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'global' => [
					'default' => Global_Colors::COLOR_ACCENT,
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-post__read-more:hover' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .elementor-post__read-more:hover svg' => 'fill: {{VALUE}};',
				],
				'condition' => [
					$this->get_control_id( 'show_read_more' ) => 'yes',
				],
			]
		);

        $this->add_control(
            'read_more_hover_animation',
            [
                'label' => esc_html__( 'Hover Animation', 'tenweb-builder' ),
                'type' => Controls_Manager::HOVER_ANIMATION,
                'frontend_available' => true,
                'render_type' => 'template',
                'condition' => [
                    $this->get_control_id( 'show_read_more' ) => 'yes',
                ],
            ]
        );

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'read_more_typography',
				// The 'a' selector is added for specificity, for when this control's selector is used in globals CSS.
				'selector' => '{{WRAPPER}} a.elementor-post__read-more',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				],
				'condition' => [
					$this->get_control_id( 'show_read_more' ) => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'read_more_spacing',
			[
				'label' => esc_html__( 'Spacing', 'tenweb-builder' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'max' => 100,
					],
					'em' => [
						'max' => 10,
					],
					'rem' => [
						'max' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-post__read-more-container' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
                'default' => [
                    'size' => 25,
                    'unit' => 'px',
                ],
				'condition' => [
					$this->get_control_id( 'show_read_more' ) => 'yes',
				],
			]
		);

        $this->add_responsive_control( 'read_more_padding', [
                'label' => __('Padding', 'tenweb-builder'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} a.elementor-post__read-more' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ],
                'condition' => [
                    $this->get_control_id( 'show_read_more' ) => 'yes',
                ],
            ]
        );

        $this->add_control(
            'read_more_style_alignment',
            [
                'label' => esc_html__( 'Alignment', 'tenweb-builder' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__( 'Left', 'tenweb-builder' ),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__( 'Center', 'tenweb-builder' ),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__( 'Right', 'tenweb-builder' ),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-post__read-more-container   ' => 'text-align: {{VALUE}}',
                ],
                'condition' => [
                    $this->get_control_id( 'show_read_more' ) => 'yes',
                ],
            ]
        );

        $this->end_controls_section();
	}

    protected function register_design_slider_controls() {
        $this->start_controls_section(
            'section_design_slider',
            [
                'label' => esc_html__( 'Slider', 'tenweb-builder' ),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    $this->get_control_id( 'slides_view' ) => 'yes',
                ],
            ]
        );

        $this->add_responsive_control(
            'space_between',
            [
                'label' => __( 'Space Between', 'tenweb-builder' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 50,
                    ],
                ],
                'desktop_default' => [
                    'size' => 10,
                ],
                'tablet_default' => [
                    'size' => 10,
                ],
                'mobile_default' => [
                    'size' => 10,
                ],
                'render_type' => 'ui',
                'frontend_available' => true,
                'selectors' => [
                    '{{WRAPPER}} .tenweb-posts-slider .swiper-slide' => 'margin-right: {{SIZE}}{{UNIT}} !important;',
                ],
            ]
        );

        $this->add_responsive_control(
            'slider_navigation_distance',
            [
                'label' => __( 'Navigation Distance', 'tenweb-builder' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => ['size' => 48],
                'selectors' => [
                    '{{WRAPPER}} .elementor-posts-container.swiper-wrapper' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    $this->get_control_id( 'slider_navigation_position' ) => 'outside',
                ],
            ]
        );

        $this->add_control(
            'heading_style_dots',
            [
                'label' => __( 'Dots', 'tenweb-builder' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                    $this->get_control_id( 'slider_navigation' ) => [ 'dots', 'both' ],
                ],
            ]
        );

        $this->add_control(
            'slider_navigation_dots_color',
            [
                'label' => __( 'Navigation Dots Color', 'tenweb-builder' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .swiper-pagination-bullet' => 'background-color: {{VALUE}};',
                ],
                'condition' => [
                    $this->get_control_id( 'slider_navigation' ) => ['dots','both'],
                ],
            ]
        );

        $this->add_responsive_control(
            'slider_navigation_dots_size',
            [
                'label' => __( 'Navigation Dots Size', 'tenweb-builder' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 5,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .swiper-pagination-bullet' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    $this->get_control_id( 'slider_navigation' ) => ['dots','both'],
                ],
            ]
        );

        $this->add_control(
            'heading_style_arrows',
            [
                'label' => __( 'Arrows', 'tenweb-builder' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                    $this->get_control_id( 'slider_navigation' ) => [ 'arrows', 'both' ],
                ],
            ]
        );

        $this->add_control(
            'slider_navigation_arrows_color',
            [
                'label' => __( 'Navigation Arrows Color', 'tenweb-builder' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .swiper-button-next, {{WRAPPER}} .swiper-button-prev' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    $this->get_control_id( 'slider_navigation' ) => ['arrows','both'],
                ],
            ]
        );

        $this->add_responsive_control(
            'slider_navigation_arrows_size',
            [
                'label' => __( 'Navigation Arrows Size', 'tenweb-builder' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 5,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .swiper-button-next:after, {{WRAPPER}} .swiper-button-prev:after' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}}.tenweb-posts-slider--navigation-position-outside .swiper-pagination-container' => 'min-height: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}}.tenweb-posts-slider--navigation-position-outside .swiper-pagination-container .swiper-pagination-arrows-container' => 'height: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}}.tenweb-posts-slider--navigation-position-outside.elementor-widget-tenweb-posts .swiper-button-prev:not(.twbb-swiper-last)' => 'right: calc( {{SIZE}}px + 15px );',
                ],
                'condition' => [
                    $this->get_control_id( 'slider_navigation' ) => ['arrows','both'],
                ],
            ]
        );

        $this->end_controls_section();
    }

	public function render() {
		$this->parent->query_posts();

		/** @var \WP_Query $query */
		$query = $this->parent->get_query();
		if ( ! $query->found_posts ) {
            if( \Elementor\Plugin::instance()->editor->is_edit_mode() ||
                (!empty($_GET['twbb_template_preview']) && !empty($_GET['twbb_template_preview_from']) && !empty($_GET['twbb_template_preview_nonce'])) //phpcs:ignore WordPress.Security.NonceVerification.Recommended
            ) {
                $this->handle_no_posts_found();
                return;
            } elseif( !\Elementor\Plugin::instance()->editor->is_edit_mode() ) {
                $this->handle_no_posts_found_preview();
                return;
            }
		}
        $settings = $this->parent->get_settings();
        $setting_key = $this->get_control_id( 'slides_view' );
        $slides_count = $settings[$this->get_control_id( 'posts_per_page')];
        if($settings[ $setting_key ] === 'yes' ){
            $speed = isset($settings['speed']) ? $settings['speed'] : 5000;
            $show_dots = ( in_array( $settings[$this->get_control_id( 'slider_navigation')], [ 'dots', 'both' ], true ) );
            $show_arrows = ( in_array( $settings[$this->get_control_id( 'slider_navigation')], [ 'arrows', 'both' ], true ) );
            $swiperObj = [
                'slides_per_view' => $settings[$this->get_control_id( 'slides_per_view')] === '' ? $settings[$this->get_control_id( 'columns')] : $settings[$this->get_control_id( 'slides_per_view')],
                'slides_per_view_tablet' => $settings[$this->get_control_id( 'slides_per_view_tablet')],
                'slides_per_view_mobile' => $settings[$this->get_control_id( 'slides_per_view_mobile')],
                'slides_to_scroll' => $settings[$this->get_control_id( 'slides_to_scroll')],
                'slider_navigation' => $settings[$this->get_control_id( 'slider_navigation')],
                'slides_count' => $slides_count,
                'speed' => $speed,
                'autoplay' => $settings[$this->get_control_id( 'autoplay')],
                'autoplay_speed' => $settings[$this->get_control_id( 'autoplay_speed')],
                'loop' => $settings[$this->get_control_id( 'loop')],
                'disable_on_interaction' => $settings[$this->get_control_id( 'disable_on_interaction')],
                'pause_on_mouseover' => $settings[$this->get_control_id( 'pause_on_mouseover')],
                'breakpoints' => [
                    'space_between' => $settings[$this->get_control_id( 'space_between')],
                    'space_between_tablet' => $settings[$this->get_control_id( 'space_between_tablet')],
                    'space_between_mobile' => $settings[$this->get_control_id( 'space_between_mobile')],
                ],
            ];

            $this->parent->add_render_attribute( 'tenweb-posts-slider', [
                    'class' => 'swiper-container tenweb-posts-slider swiper-container-horizontal',
                    'data-settings' => json_encode($swiperObj),// phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode
                ]
            );
            ?><div <?php $this->parent->print_render_attribute_string( 'tenweb-posts-slider' ); ?>><?php
        }
		$this->render_loop_header();

		// It's the global `wp_query` it self. and the loop was started from the theme.
		if ( $query->in_the_loop ) {
			$this->current_permalink = get_permalink();
			$this->render_post();
		} else {
			while ( $query->have_posts() ) {
				$query->the_post();

				$this->current_permalink = get_permalink();
				$this->render_post();
			}
		}

		wp_reset_postdata();

		$this->render_loop_footer();
        if ( $settings[ $setting_key ] === 'yes' && 1 < $slides_count ) {
            ?><div class="swiper-pagination-container"><?php
            if ( $show_dots ) {
                ?><div class="swiper-pagination" style="width: 99%;"></div><?php
            } else {
                ?><div style="width: 99%;"></div><?php
            }
            if( $show_arrows ) {
                ?><div class="swiper-pagination-arrows-container"><div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div></div><?php
            }
            ?></div><?php
        }
	}

	public function filter_excerpt_length() {
		return $this->get_instance_value( 'excerpt_length' );
	}

	public function filter_excerpt_more( $more ) {
		return '';
	}

	public function get_container_class() {
		return 'elementor-posts--skin-' . $this->get_id();
	}

	protected function render_thumbnail() {
		$thumbnail = $this->get_instance_value( 'thumbnail' );

		if ( 'none' === $thumbnail && ! \Elementor\Plugin::instance()->editor->is_edit_mode() ) {
			return;
		}

		$settings = $this->parent->get_settings();
		$setting_key = $this->get_control_id( 'thumbnail_size' );
		$settings[ $setting_key ] = [
			'id' => get_post_thumbnail_id(),
		];
		$thumbnail_html = Group_Control_Image_Size::get_attachment_image_html( $settings, $setting_key );

		if ( empty( $thumbnail_html ) && $this->get_id() !== 'on_image' ) {
			return;
		}

		$optional_attributes_html = $this->get_optional_link_attributes_html();
        $badge_on_image = isset($settings[$this->get_control_id( 'badge_on_image')]) ? esc_html($settings[$this->get_control_id( 'badge_on_image')]) : '';
		?>
        <div class="twbb-image-container">
            <?php if($this->get_id() === 'on_image') { ?>
                <a class="twbb-image-overlay" href="<?php echo esc_url( $this->current_permalink ); ?>"></a>
            <?php } ?>
            <a class="elementor-post__thumbnail__link" href="<?php echo esc_url( $this->current_permalink ); ?>" tabindex="-1" <?php echo esc_attr( $optional_attributes_html ); ?>>
                <div class="elementor-post__thumbnail"><?php echo wp_kses_post( $thumbnail_html ); ?></div>
            </a>
            <?php if( $badge_on_image === 'yes' ){
                $this->render_badge();
            } ?>
        </div>
		<?php
	}

    protected function render_badge() {
        $taxonomy = $this->get_instance_value( 'badge_taxonomy' );
        $settings = $this->parent->get_settings();

        if ( empty( $taxonomy ) || ! taxonomy_exists( $taxonomy ) ) {
            return;
        }

        $terms = get_the_terms( get_the_ID(), $taxonomy );
        if ( empty( $terms[0] ) || !$this->get_instance_value( 'show_badge' )) {
            return;
        }
        $term_url = get_term_link($terms[0]->term_id);
        if (!is_wp_error($term_url)) {
        ?>
        <div class="twbb-post__badge-container">
          <a class="twbb-post__badge-link" href="<?php echo esc_url($term_url); ?>" alt="<?php echo esc_attr( $terms[0]->name ); ?>">
            <div class="elementor-post__badge"><?php echo esc_html( $terms[0]->name ); ?></div>
          </a>
        </div>
        <?php
        }
    }

    protected function render_avatar() {
        ?>
        <div class="elementor-post__avatar">
            <?php echo get_avatar( get_the_author_meta( 'ID' ), 128, '', get_the_author_meta( 'display_name' ) ); ?>
        </div>
        <?php
    }

    protected function render_title() {
		if ( ! $this->get_instance_value( 'show_title' ) ) {
			return;
		}

		$optional_attributes_html = $this->get_optional_link_attributes_html();

		$tag = $this->get_instance_value( 'title_tag' );
		?>
		<<?php Utils::print_validated_html_tag( $tag ); ?> class="elementor-post__title">
			<a href="<?php echo esc_url( $this->current_permalink ); ?>" <?php echo esc_html( $optional_attributes_html ); ?>>
				<?php the_title(); ?>
			</a>
		</<?php Utils::print_validated_html_tag( $tag ); ?>>
		<?php
	}

	protected function render_excerpt() {

		add_filter( 'excerpt_more', [ $this, 'filter_excerpt_more' ], 20 );
		add_filter( 'excerpt_length', [ $this, 'filter_excerpt_length' ], 20 );

		if ( ! $this->get_instance_value( 'show_excerpt' ) ) {
			return;
		}

		add_filter( 'excerpt_more', [ $this, 'filter_excerpt_more' ], 20 );
		add_filter( 'excerpt_length', [ $this, 'filter_excerpt_length' ], 20 );

		?>
		<div class="elementor-post__excerpt">
			<?php
			global $post;
			$apply_to_custom_excerpt = $this->get_instance_value( 'apply_to_custom_excerpt' );

			// Force the manually-generated Excerpt length as well if the user chose to enable 'apply_to_custom_excerpt'.
			if ( 'yes' === $apply_to_custom_excerpt ) {
				$max_length = (int) $this->get_instance_value( 'excerpt_length' );
				$excerpt = apply_filters( 'the_excerpt', get_the_excerpt() );
				$excerpt = ProUtils::trim_words( $excerpt, $max_length );
				echo $excerpt; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			} else {
				the_excerpt();
			}
			?>
		</div>
		<?php

		remove_filter( 'excerpt_length', [ $this, 'filter_excerpt_length' ], 20 );
		remove_filter( 'excerpt_more', [ $this, 'filter_excerpt_more' ], 20 );
	}

	protected function render_read_more() {
		$settings = $this->parent->get_settings_for_display();
		$read_more_key = $this->get_control_id( 'read_more_text' );
		$read_more = isset($settings[ $read_more_key ]) ? $settings[ $read_more_key ] : '';
		if ( ! $this->get_instance_value( 'show_read_more' ) ) {
			return;
		}

		$aria_label_text = sprintf(
			/* translators: %s: Post title. */
			esc_attr__( 'Read more about %s', 'tenweb-builder' ),
			get_the_title()
		);

		$optional_attributes_html = $this->get_optional_link_attributes_html();

        $elementClass = '';
        if ( $settings['classic_read_more_hover_animation'] ) {
            $elementClass .= ' elementor-animation-' . $settings['classic_read_more_hover_animation'];
        }

		if ( $this->display_read_more_bottom() ) : ?>
			<div class="elementor-post__read-more-wrapper">
		<?php endif; ?>
        <div class="elementor-post__read-more-container">
		<a class="elementor-post__read-more<?php echo esc_attr($elementClass); ?>" href="<?php echo esc_url( $this->current_permalink ); ?>" aria-label="<?php echo esc_attr( $aria_label_text ); ?>" tabindex="-1" <?php Utils::print_unescaped_internal_string( $optional_attributes_html ); ?>>
			<?php echo wp_kses_post( $read_more ); ?>
            <?php
            \Elementor\Icons_Manager::render_icon($settings[$settings['_skin'].'_read_more_icon'], ['aria-hidden' => 'true']);
            ?>
		</a>
        </div>
		<?php if ( $this->display_read_more_bottom() ) : ?>
			</div>
		<?php endif;
	}

	protected function render_post_header() {
        $classes = [ 'elementor-post elementor-grid-item' ];
        $settings = $this->parent->get_settings();
        $setting_key = $this->get_control_id( 'slides_view' );
        if($settings[ $setting_key ] === 'yes' ){
            $classes = [ 'elementor-post elementor-grid-item swiper-slide'];
        }
		?>
		<article <?php post_class( $classes ); ?>>
		<?php
	}

	protected function render_post_footer() {
		?>
		</article>
		<?php
	}

	protected function render_text_header() {
		?>
		<div class="elementor-post__text">
		<?php
	}

	protected function render_text_footer() {
		?>
		</div>
		<?php
	}

	protected function get_loop_header_widget_classes() {
		return [
			'elementor-posts-container',
			'elementor-posts',
			$this->get_container_class(),
		];
	}

	protected function handle_no_posts_found() {
        $args = [
            'mobile_desc' => 'This is a preview of what your future posts will look like. You haven’t shared any posts yet. This view will not be visible on your live website.',
            'desktop_desc' => 'This is a preview of what your future posts list will look like. You haven’t created any posts yet.<br> This view will not be visible on your live website.',
            'el_count' => 3,
        ];
        \Tenweb_Builder\Modules\Utils::handleArchiveNoContentRender($args);
    }

    protected function handle_no_posts_found_preview() {
        $args = [
            'title' => 'No Blog Posts Found',
            'desc' => 'There are currently no blog posts to display.',
        ];
        \Tenweb_Builder\Modules\Utils::handleArchiveNoContentPreviewRender($args);
    }

	protected function render_loop_header() {
		$classes = $this->get_loop_header_widget_classes();

		/** @var \WP_Query $e_wp_query */
		$e_wp_query = $this->parent->get_query();

		// Use grid only if found posts.
		if ( isset( $e_wp_query->found_posts ) || Taxonomy_Loop_Provider::is_loop_taxonomy() ) {
			$classes[] = 'elementor-grid';
            $settings = $this->parent->get_settings();
            $setting_key = $this->get_control_id( 'slides_view' );
            if($settings[ $setting_key ] === 'yes' ) {
                $classes[] = 'swiper-wrapper';
            }
		}

		$render_attributes = apply_filters( 'elementor/skin/loop_header_attributes', [
			'class' => $classes,
		] );

		$this->parent->add_render_attribute( 'container', $render_attributes );

		?>
		<div data-skin="<?php echo esc_attr($this->get_id()) . ' ';?>" <?php $this->parent->print_render_attribute_string( 'container' ); ?>>
		<?php
	}

	protected function render_message() {
		$settings = $this->parent->get_settings_for_display();
		?>
		<div class="e-load-more-message"><?php echo esc_html( $settings['load_more_no_posts_custom_message'] ); ?></div>
		<?php
	}

	protected function render_loop_footer() {
		?>
		</div>
		<?php
		$parent_settings = $this->parent->get_settings_for_display();

		// If the skin has no pagination, there's nothing to render in the loop footer.
		if ( ! isset( $parent_settings['pagination_type'] ) ) {
			return;
		}

		$using_ajax_pagination = in_array( $parent_settings['pagination_type'], [
			Posts_Base::LOAD_MORE_ON_CLICK,
			Posts_Base::LOAD_MORE_INFINITE_SCROLL,
		], true);

		if ( $using_ajax_pagination && ! empty( $parent_settings['load_more_spinner']['value'] ) ) : ?>
			<span class="e-load-more-spinner">
				<?php Icons_Manager::render_icon( $parent_settings['load_more_spinner'], [ 'aria-hidden' => 'true' ] ); ?>
			</span>
		<?php endif; ?>

		<?php

		if ( '' === $parent_settings['pagination_type'] ) {
			return;
		}

		$page_limit = $this->parent->get_query()->max_num_pages;

		// Page limit control should not effect in load more mode.
		if ( '' !== $parent_settings['pagination_page_limit'] && ! $using_ajax_pagination ) {
			$page_limit = min( $parent_settings['pagination_page_limit'], $page_limit );
		}

		if ( 2 > $page_limit ) {
			return;
		}

		$this->parent->add_render_attribute( 'pagination', 'class', 'elementor-pagination' );

		$has_numbers = in_array( $parent_settings['pagination_type'], [ 'numbers', 'numbers_and_prev_next' ], true );
		$has_prev_next = in_array( $parent_settings['pagination_type'], [ 'prev_next', 'numbers_and_prev_next' ], true );

		$load_more_type = $parent_settings['pagination_type'];

		$current_page = $this->parent->get_current_page();
		$next_page = intval( $current_page ) + 1;

		$this->parent->add_render_attribute( 'load_more_anchor', [
			'data-page' => $current_page,
			'data-max-page' => $this->parent->get_query()->max_num_pages,
			'data-next-page' => $this->parent->get_wp_link_page( $next_page ),
		] );

		?>
		<div class="e-load-more-anchor" <?php $this->parent->print_render_attribute_string( 'load_more_anchor' ); ?>></div>
		<?php

		if ( $using_ajax_pagination ) {
			if ( 'load_more_on_click' === $load_more_type ) {
				// The link-url control is hidden, so default value is added to keep the same style like button widget.
				$this->parent->set_settings( 'link', [ 'url' => '#' ] );

				$this->render_button( $this->parent );
			}

			$this->render_message();
			return;
		}

		$links = [];

		if ( $has_numbers ) {
			$paginate_args = [
				'type' => 'array',
				'current' => $this->parent->get_current_page(),
				'total' => $page_limit,
				'prev_next' => false,
				'show_all' => 'yes' !== $parent_settings['pagination_numbers_shorten'],
				'before_page_number' => '<span class="elementor-screen-only">' . esc_html__( 'Page', 'tenweb-builder' ) . '</span>',
			];

			if ( is_singular() && ! is_front_page() && ! $this->parent->is_rest_request() ) {
				$paginate_args = $this->get_paginate_args_for_singular_post( $paginate_args );
			}

			if ( is_archive() && $this->parent->current_url_contains_taxonomy_filter() ) {
				$paginate_args = $this->get_paginate_args_for_archive_with_filters( $paginate_args );
			}

			if ( $this->parent->is_rest_request() ) {
				$paginate_args = $this->get_paginate_args_for_rest_request( $paginate_args );
			}

			if ( $this->parent->is_allow_to_use_custom_page_option() ) {
				$paginate_args['format'] = $this->get_pagination_format( $paginate_args );
			}

			$links = paginate_links( $paginate_args );
		}

		if ( $has_prev_next ) {
			$prev_next = $this->parent->get_posts_nav_link( $page_limit );
			array_unshift( $links, $prev_next['prev'] );
			$links[] = $prev_next['next'];
		}

		// PHPCS - Seems that `$links` is safe.
		?>
		<nav class="elementor-pagination" aria-label="<?php esc_attr_e( 'Pagination', 'tenweb-builder' ); ?>">
			<?php echo implode( PHP_EOL, $links ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</nav>
		<?php
	}

	protected function get_pagination_format( $paginate_args ) {
		$query_string_connector = ! empty( $paginate_args['base'] ) && strpos( $paginate_args['base'], '?' ) ? '&' : '?';
		return $query_string_connector . 'e-page-' . $this->parent->get_id() . '=%#%';
	}

	protected function get_paginate_args_for_singular_post( $paginate_args ) {
		global $wp_rewrite;

		if ( $wp_rewrite->using_permalinks() ) {
			$paginate_args['base'] = trailingslashit( get_permalink() ) . '%_%';
			$paginate_args['format'] = user_trailingslashit( '%#%', 'single_paged' );
		} else {
			$paginate_args['format'] = '?page=%#%';
		}

		return $paginate_args;
	}

	protected function get_paginate_args_for_archive_with_filters( $paginate_args ) {
		global $wp_rewrite;

		if ( ! $wp_rewrite->using_permalinks() ) {
			$paginate_args['format'] = '?page=%#%';
		}

		return $paginate_args;
	}

	protected function get_paginate_args_for_rest_request( $paginate_args ) {
		global $wp_rewrite;

		$link_unescaped = wp_get_referer();
		$url_components = wp_parse_url( $link_unescaped );
		$add_args = [];

		if ( isset( $url_components['query'] ) ) {
			wp_parse_str( $url_components['query'], $add_args );
		}

		$url_to_post_id = url_to_postid( $link_unescaped );//phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.url_to_postid_url_to_postid
		$pagination_base_url = 0 !== $url_to_post_id
			? get_permalink( $url_to_post_id )
			: get_query_var( 'pagination_base_url' );

		if ( $wp_rewrite->using_permalinks() ) {
			$paginate_args['base'] = trailingslashit( $pagination_base_url ) . '%_%';
			$paginate_args['format'] = user_trailingslashit( '%#%', 'single_paged' );
			$paginate_args['add_args'] = $add_args;

			if ( 0 === $url_to_post_id ) {
				unset( $paginate_args['format'] );
			}
		} else {
			$base = $this->parent->is_allow_to_use_custom_page_option() ? $pagination_base_url . '&%_%' : trailingslashit( $pagination_base_url ) . '%_%';
			$paginate_args['base'] = $base;
			$paginate_args['format'] = '&page=%#%';
			$paginate_args['add_args'] = $add_args;
		}

		return $paginate_args;
	}

	protected function render_meta_data() {
		/** @var array $settings e.g. [ 'author', 'date', ... ] */
		$settings = $this->get_instance_value( 'meta_data' );
		if ( empty( $settings ) ) {
			return;
		}

		?>
        <div class="elementor-post__meta-data">
        <?php
        if ( in_array( 'author', $settings, true ) ) {
            $this->render_author();
        }

        if ( in_array( 'date', $settings, true ) ) {
            $this->render_date_by_type();
        }

        if ( in_array( 'time', $settings, true ) ) {
            $this->render_time();
        }

        if ( in_array( 'comments', $settings, true ) ) {
            $this->render_comments();
        }
        if ( in_array( 'modified', $settings, true ) ) {
            $this->render_date_by_type( 'modified' );
        }
        ?>
        </div>
		<?php
	}

	protected function render_author() {
		?>
		<span class="elementor-post-author">
			<?php the_author(); ?>
		</span>
		<?php
	}

	protected function render_date_by_type( $type = 'publish' ) {
		?>
		<span class="elementor-post-date">
			<?php
			switch ( $type ) :
				case 'modified':
					$date = get_the_modified_date();
					break;
				default:
					$date = get_the_date();
			endswitch;
			/** This filter is documented in wp-includes/general-template.php */
			// PHPCS - The date is safe.
			echo apply_filters( 'the_date', $date, get_option( 'date_format' ), '', '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            ?>
		</span>
		<?php
	}

	protected function render_time() {
		?>
		<span class="elementor-post-time">
			<?php the_time(); ?>
		</span>
		<?php
	}

	/**
	 * Check if the Read More links needs to be displayed at the bottom of the Post item.
	 *
	 * Conditions:
	 * 1) Read More aligned to the bottom
	 * 2) Masonry layout not used.
	 * 3) Display Read More link.
	 *
	 * @since 3.7.0
	 *
	 * @return boolean
	 */
	protected function display_read_more_bottom() {
		$settings = $this->parent->get_settings();

		if ( 'full_content' === $settings['_skin'] ) {
			return false;
		}

		return 'yes' === $settings[ $this->get_control_id( 'show_read_more' ) ];
	}

	protected function render_comments() {
		?>
		<span class="elementor-post-avatar">
			<?php comments_number(); ?>
		</span>
		<?php
	}

	protected function render_post() {
        $settings = $this->parent->get_settings();
        $badge_on_image = isset($settings['classic_badge_on_image']) ? esc_html($settings['classic_badge_on_image']) : '';
        $badge_on_image_left = isset($settings['image_left_badge_on_image']) ? esc_html($settings['image_left_badge_on_image']) : '';
		$this->render_post_header();
		$this->render_thumbnail();
		$this->render_text_header();
        if ( ( $settings['_skin'] === 'classic' && $badge_on_image !== 'yes' ) ||
            ( $settings['_skin'] === 'image_left' && $badge_on_image_left !== 'yes') || ($settings['_skin'] === 'on_image') ) {
            $this->render_badge();
        }
		$this->render_title();
		$this->render_excerpt();
        $this->render_meta_data();
        $this->render_read_more();
        $this->render_text_footer();
		$this->render_post_footer();
	}
}
