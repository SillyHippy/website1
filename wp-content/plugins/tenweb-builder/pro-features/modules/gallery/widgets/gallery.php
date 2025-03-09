<?php

namespace Tenweb_Builder\ElementorPro\Modules\Gallery\Widgets;

use Elementor\Controls_Manager;
use Elementor\Core\Breakpoints\Manager as Breakpoints_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Gallery extends Widget_Base {

	/**
	 * Get element name.
	 *
	 * Retrieve the element name.
	 *
	 * @return string The name.
	 * @since 2.7.0
	 * @access public
	 *
	 */
	public function get_name() {
		return 'twbb_gallery';
	}

	public function get_title() {
		return esc_html__( 'Gallery', 'elementor-pro' );
	}

  public function get_categories() {
    return [ 'tenweb-widgets' ];
  }

	public function get_script_depends() {
		return [ 'elementor-gallery' ];
	}

	public function get_style_depends() {
		return [ 'elementor-gallery' ];
	}

	public function get_icon() {
		return 'twbb-gallery twbb-widget-icon';
	}

	public function get_inline_css_depends() {
		if ( 'multiple' === $this->get_settings_for_display( 'gallery_type' ) ) {
			return [ 'nav-menu' ];
		}

		return [];
	}

	protected function register_controls() {
		$this->start_controls_section( 'settings', [ 'label' => esc_html__( 'Settings', 'elementor-pro' ) ] );

		$this->add_control(
			'gallery_type',
			[
				'type' => Controls_Manager::SELECT,
				'label' => esc_html__( 'Type', 'elementor-pro' ),
				'default' => 'single',
				'options' => [
					'single' => esc_html__( 'Single', 'elementor-pro' ),
					'multiple' => esc_html__( 'Multiple', 'elementor-pro' ),
				],
			]
		);

		$this->add_control(
			'gallery',
			[
				'type' => Controls_Manager::GALLERY,
				'condition' => [
					'gallery_type' => 'single',
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

    /* 10Web Builder */
    $this->add_control(
      'display_gallery_title_description',
      [
        'label' => esc_html__( 'Display Title and Description', 'tenweb-builder' ),
        'type' => Controls_Manager::SWITCHER,
        'condition' => [
          'gallery_type' => 'single',
        ],
        'default' => 'no',
      ]
    );

		$repeater = new Repeater();

		$repeater->add_control(
			'gallery_title',
			[
				'type' => Controls_Manager::TEXT,
				'label' => esc_html__( 'Title', 'elementor-pro' ),
				'default' => esc_html__( 'New Gallery', 'elementor-pro' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'multiple_gallery',
			[
				'type' => Controls_Manager::GALLERY,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'galleries',
			[
				'type' => Controls_Manager::REPEATER,
				'label' => esc_html__( 'Galleries', 'elementor-pro' ),
				'fields' => $repeater->get_controls(),
				'title_field' => '{{{ gallery_title }}}',
				'default' => [
					[
						'gallery_title' => esc_html__( 'New Gallery', 'elementor-pro' ),
					],
				],
				'condition' => [
					'gallery_type' => 'multiple',
				],
			]
		);

		$this->add_control(
			'order_by',
			[
				'type' => Controls_Manager::SELECT,
				'label' => esc_html__( 'Order By', 'elementor-pro' ),
				'options' => [
					'' => esc_html__( 'Default', 'elementor-pro' ),
					'random' => esc_html__( 'Random', 'elementor-pro' ),
				],
				'default' => '',
			]
		);

		$this->add_control(
			'lazyload',
			[
				'type' => Controls_Manager::SWITCHER,
				'label' => esc_html__( 'Lazy Load', 'elementor-pro' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'gallery_layout',
			[
				'type' => Controls_Manager::SELECT,
				'label' => esc_html__( 'Layout', 'elementor-pro' ),
				'default' => 'grid',
				'options' => [
					'grid' => esc_html__( 'Grid', 'elementor-pro' ),
					'justified' => esc_html__( 'Justified', 'elementor-pro' ),
					'masonry' => esc_html__( 'Masonry', 'elementor-pro' ),
				],
				'separator' => 'before',
				'frontend_available' => true,
			]
		);

        $this->add_control(
			'last_row',
			[
				'type' => Controls_Manager::SELECT,
				'label' => esc_html__( 'Last Row', 'elementor-pro' ),
				'default' => 'normal',
				'options' => [
					'fit' => esc_html__( 'Fit', 'elementor-pro' ),
					'normal' => esc_html__( 'Normal', 'elementor-pro' ),
					'hide' => esc_html__( 'Hide', 'elementor-pro' ),
				],
                'condition' => [
                    'gallery_layout' => 'justified',
                ],
				'separator' => 'before',
				'frontend_available' => true,
                'render_type' => 'template',
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label' => esc_html__( 'Columns', 'elementor-pro' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 4,
				'tablet_default' => 2,
				'mobile_default' => 1,
				'min' => 1,
				'max' => 24,
				'condition' => [
					'gallery_layout!' => 'justified',
				],
				'render_type' => 'none',
				'frontend_available' => true,
			]
		);

		$active_breakpoints = \Elementor\Plugin::instance()->breakpoints->get_active_breakpoints();
		$ideal_row_height_device_args = [];
		$gap_device_args = [];

		// Add default values for all active breakpoints.
		foreach ( $active_breakpoints as $breakpoint_name => $breakpoint_instance ) {
			if ( 'widescreen' !== $breakpoint_name ) {
				$ideal_row_height_device_args[ $breakpoint_name ] = [
					'default' => [
						'size' => 150,
					],
				];

				$gap_device_args[ $breakpoint_name ] = [
					'default' => [
						'size' => 10,
					],
				];
			}
		}

		$this->add_responsive_control(
			'ideal_row_height',
			[
				'label' => esc_html__( 'Row Height', 'elementor-pro' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 50,
						'max' => 500,
					],
				],
				'default' => [
					'size' => 200,
				],
				'device_args' => $ideal_row_height_device_args,
				'condition' => [
					'gallery_layout' => 'justified',
				],
				'required' => true,
				'render_type' => 'none',
				'frontend_available' => true,
			]
		);

		$this->add_responsive_control(
			'gap',
			[
				'label' => esc_html__( 'Spacing', 'elementor-pro' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 10,
				],
				'device_args' => $gap_device_args,
				'required' => true,
				'render_type' => 'none',
				'frontend_available' => true,
			]
		);

    $this->add_responsive_control(
      'hgap',
      [
        'label' => esc_html__( 'Horizontal Spacing', 'elementor-pro' ),
        'type' => Controls_Manager::SLIDER,
        'default' => [
          'size' => 10,
        ],
        'tablet_default' => [
          'size' => 10,
        ],
        'mobile_default' => [
          'size' => 10,
        ],
        'required' => true,
        'render_type' => 'none',
        'frontend_available' => true,
      ]
    );


    $this->add_control(
			'link_to',
			[
				'label' => esc_html__( 'Link', 'elementor-pro' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'file',
				'options' => [
					'' => esc_html__( 'None', 'elementor-pro' ),
					'file' => esc_html__( 'Media File', 'elementor-pro' ),
					'custom' => esc_html__( 'Custom URL', 'elementor-pro' ),
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'url',
			[
				'label' => esc_html__( 'URL', 'elementor-pro' ),
				'type' => Controls_Manager::URL,
				'condition' => [
					'link_to' => 'custom',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'aspect_ratio',
			[
				'type' => Controls_Manager::SELECT,
				'label' => esc_html__( 'Aspect Ratio', 'elementor-pro' ),
				'default' => '3:2',
				'options' => [
					'1:1' => '1:1',
					'3:2' => '3:2',
					'3:4' => '3:4',
					'4:3' => '4:3',
					'9:16' => '9:16',
					'16:9' => '16:9',
					'21:9' => '21:9',
				],
				'condition' => [
					'gallery_layout' => 'grid',
				],
				'render_type' => 'none',
				'frontend_available' => true,
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'thumbnail_image',
				'default' => 'medium',
			]
		);

		$this->end_controls_section(); // settings

		$this->start_controls_section(
			'section_filter_bar_content',
			[
				'label' => esc_html__( 'Filter Bar', 'elementor-pro' ),
				'condition' => [
					'gallery_type' => 'multiple',
				],
			]
		);

		$this->add_control(
			'show_all_galleries',
			[
				'type' => Controls_Manager::SWITCHER,
				'label' => esc_html__( '"All" Filter', 'elementor-pro' ),
				'default' => 'yes',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'show_all_galleries_label',
			[
				'type' => Controls_Manager::TEXT,
				'label' => esc_html__( '"All" Filter Label', 'elementor-pro' ),
				'default' => esc_html__( 'All', 'elementor-pro' ),
				'condition' => [
					'show_all_galleries' => 'yes',
				],
			]
		);

		$this->add_control(
			'pointer',
			[
				'label' => esc_html__( 'Pointer', 'elementor-pro' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'underline',
				'options' => [
					'none' => esc_html__( 'None', 'elementor-pro' ),
					'underline' => esc_html__( 'Underline', 'elementor-pro' ),
					'overline' => esc_html__( 'Overline', 'elementor-pro' ),
					'double-line' => esc_html__( 'Double Line', 'elementor-pro' ),
					'framed' => esc_html__( 'Framed', 'elementor-pro' ),
					'background' => esc_html__( 'Background', 'elementor-pro' ),
					'text' => esc_html__( 'Text', 'elementor-pro' ),
				],
				'style_transfer' => true,
			]
		);

		$this->add_control(
			'animation_line',
			[
				'label' => esc_html__( 'Animation', 'elementor-pro' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'fade',
				'options' => [
					'fade' => 'Fade',
					'slide' => 'Slide',
					'grow' => 'Grow',
					'drop-in' => 'Drop In',
					'drop-out' => 'Drop Out',
					'none' => 'None',
				],
				'condition' => [
					'pointer' => [ 'underline', 'overline', 'double-line' ],
				],
			]
		);

		$this->add_control(
			'animation_framed',
			[
				'label' => esc_html__( 'Animation', 'elementor-pro' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'fade',
				'options' => [
					'fade' => 'Fade',
					'grow' => 'Grow',
					'shrink' => 'Shrink',
					'draw' => 'Draw',
					'corners' => 'Corners',
					'none' => 'None',
				],
				'condition' => [
					'pointer' => 'framed',
				],
			]
		);

		$this->add_control(
			'animation_background',
			[
				'label' => esc_html__( 'Animation', 'elementor-pro' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'fade',
				'options' => [
					'fade' => 'Fade',
					'grow' => 'Grow',
					'shrink' => 'Shrink',
					'sweep-left' => 'Sweep Left',
					'sweep-right' => 'Sweep Right',
					'sweep-up' => 'Sweep Up',
					'sweep-down' => 'Sweep Down',
					'shutter-in-vertical' => 'Shutter In Vertical',
					'shutter-out-vertical' => 'Shutter Out Vertical',
					'shutter-in-horizontal' => 'Shutter In Horizontal',
					'shutter-out-horizontal' => 'Shutter Out Horizontal',
					'none' => 'None',
				],
				'condition' => [
					'pointer' => 'background',
				],
			]
		);

		$this->add_control(
			'animation_text',
			[
				'label' => esc_html__( 'Animation', 'elementor-pro' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'grow',
				'options' => [
					'grow' => 'Grow',
					'shrink' => 'Shrink',
					'sink' => 'Sink',
					'float' => 'Float',
					'skew' => 'Skew',
					'rotate' => 'Rotate',
					'none' => 'None',
				],
				'condition' => [
					'pointer' => 'text',
				],
			]
		);

		$this->end_controls_section(); // settings

    /**
     * 10Web Builder
     */
    $this->start_controls_section( 'title',
      [
        'label' => esc_html__( 'Title', 'elementor' ),
        'condition' => [
          'display_gallery_title_description' => 'yes',
        ],
      ]
    );

    $this->add_control(
      'gallery_name',
      [
        'label' => esc_html__( 'Gallery Name', 'elementor-pro' ),
        'type' => Controls_Manager::TEXT,
        'default' => esc_html__( 'New Gallery', 'elementor-pro' ),
        'placeholder' => esc_html__( 'Gallery Name', 'elementor-pro' ),
      ]
    );

    $this->add_control(
      'title_size',
      [
        'label' => esc_html__( 'Size', 'elementor' ),
        'type' => Controls_Manager::SELECT,
        'default' => 'large',
        'options' => [
          'default' => esc_html__( 'Default', 'elementor' ),
          'small' => esc_html__( 'Small', 'elementor' ),
          'medium' => esc_html__( 'Medium', 'elementor' ),
          'large' => esc_html__( 'Large', 'elementor' ),
          'xl' => esc_html__( 'XL', 'elementor' ),
          'xxl' => esc_html__( 'XXL', 'elementor' ),
        ],
      ]
    );

    $this->add_control(
      'title_tag',
      [
        'label' => esc_html__( 'HTML Tag', 'elementor' ),
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
        'default' => 'h2',
      ]
    );

    $this->add_responsive_control(
      'title_align',
      [
        'label' => esc_html__( 'Alignment', 'elementor' ),
        'type' => Controls_Manager::CHOOSE,
        'options' => [
          'left' => [
            'title' => esc_html__( 'Left', 'elementor' ),
            'icon' => 'eicon-text-align-left',
          ],
          'center' => [
            'title' => esc_html__( 'Center', 'elementor' ),
            'icon' => 'eicon-text-align-center',
          ],
          'right' => [
            'title' => esc_html__( 'Right', 'elementor' ),
            'icon' => 'eicon-text-align-right',
          ],
          'justify' => [
            'title' => esc_html__( 'Justified', 'elementor' ),
            'icon' => 'eicon-text-align-justify',
          ],
        ],
        'default' => 'center',
        'selectors' => [
          '{{WRAPPER}} .elementor-single-gallery-title-description-wrapper .elementor-single-gallery-title' => 'text-align: {{VALUE}};',
        ],
      ]
    );

    $this->add_control(
      'gallery_title_spacing',
      [
        'label' => esc_html__( 'Title Spacing', 'tenweb-builder' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 100,
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .elementor-single-gallery-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->end_controls_section(); // Title

    $this->start_controls_section( 'description',
      [
        'label' => esc_html__( 'Description', 'elementor' ),
        'condition' => [
          'display_gallery_title_description' => 'yes',
        ],
      ]
    );

    $this->add_control(
      'description_position',
      [
        'label' => esc_html__('Position', 'elementor'),
        'type' => Controls_Manager::CHOOSE,
        'label_block' => false,
        'options' => [
          'top' => [
            'title' => esc_html__('Top', 'elementor'),
            'icon'  => 'eicon-v-align-top',
          ],
          'bottom' => [
            'title' => esc_html__('Bottom', 'elementor'),
            'icon'  => 'eicon-v-align-bottom',
          ],
        ],
        'default' => 'top',
      ]
    );

    $this->add_control(
      'gallery_top_description_spacing',
      [
        'label' => esc_html__( 'Description Spacing', 'tenweb-builder' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 100,
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .elementor-single-gallery-title-description-wrapper .elementor-text-editor' => 'margin-bottom: {{SIZE}}{{UNIT}}; margin-top: 0;',
        ],
        'condition' => [
          'description_position' => 'top',
        ],
      ]
    );

    $this->add_control(
      'gallery_bottom_description_spacing',
      [
        'label' => esc_html__( 'Description Spacing', 'tenweb-builder' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 100,
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .elementor-single-gallery-title-description-wrapper .elementor-text-editor' => 'margin-top: {{SIZE}}{{UNIT}}; margin-bottom: 0;',
        ],
        'condition' => [
          'description_position' => 'bottom',
        ],
      ]
    );

    $this->add_control(
      'gallery_description',
      [
        'label' => esc_html__( 'Description', 'tenweb-builder' ),
        'type' => Controls_Manager::TEXTAREA,
        'default' => '',
        'placeholder' => esc_html__( 'Description', 'tenweb-builder' ),
        'dynamic' => [
          'active' => false,
        ],
      ]
    );

    $this->end_controls_section(); // Description

    $this->start_controls_section( 'item_content', [ 'label' => __( 'Item Content', 'elementor-pro' ) ] );

    $this->add_control(
      'overlay_background',
      [
        'label' => esc_html__( 'Overlay Background', 'elementor-pro' ),
        'type' => Controls_Manager::SWITCHER,
        'default' => 'yes',
        'frontend_available' => true,
      ]
    );

    $this->add_control(
      'image_title_description',
      [
        'label' => esc_html__( 'Title and Description', 'tenweb-builder' ),
        'type' => Controls_Manager::SELECT,
        'default' => 'on_hover',
        'options' => [
          'on_hover' => esc_html__( 'Show On Hover', 'tenweb-builder' ),
          'always_show' => esc_html__( 'Always Show', 'tenweb-builder' ),
        ],
        'condition' => [
          'gallery_layout' => 'grid',
        ],
        'frontend_available' => true,
      ]
    );

    //end 10web customizing

		/*$this->start_controls_section( 'overlay', [ 'label' => esc_html__( 'Overlay', 'elementor-pro' ) ] );

		$this->add_control(
			'overlay_background',
			[
				'label' => esc_html__( 'Background', 'elementor-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'frontend_available' => true,
			]
		);*/

		$this->add_control(
			'overlay_title',
			[
				'label' => esc_html__( 'Title', 'elementor-pro' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => esc_html__( 'None', 'elementor-pro' ),
					'title' => esc_html__( 'Title', 'elementor-pro' ),
					'caption' => esc_html__( 'Caption', 'elementor-pro' ),
					'alt' => esc_html__( 'Alt', 'elementor-pro' ),
					'description' => esc_html__( 'Description', 'elementor-pro' ),
				],
				'frontend_available' => true,
			]
		);

    /* 10web */
    $this->add_control(
      'image_title_position',
      [
        'label' => esc_html__( 'Title Position', 'elementor-pro' ),
        'type' => Controls_Manager::CHOOSE,
        'options' => [
          'top' => [
            'title' => esc_html__( 'Top', 'elementor-pro' ),
            'icon' => 'eicon-v-align-top',
          ],
          'bottom' => [
            'title' => esc_html__( 'Bottom', 'elementor-pro' ),
            'icon' => 'eicon-v-align-bottom',
          ],
        ],
        'default' => 'top',
        'condition' => [
          'image_title_description' => 'always_show',
          'overlay_title!' => '',
          'gallery_layout' => 'grid',
        ],
      ]
    );

		$this->add_control(
			'overlay_description',
			[
				'label' => esc_html__( 'Description', 'elementor-pro' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => esc_html__( 'None', 'elementor-pro' ),
					'title' => esc_html__( 'Title', 'elementor-pro' ),
					'caption' => esc_html__( 'Caption', 'elementor-pro' ),
					'alt' => esc_html__( 'Alt', 'elementor-pro' ),
					'description' => esc_html__( 'Description', 'elementor-pro' ),
				],
				'frontend_available' => true,
			]
		);

    /* 10web */
    $this->add_control(
      'image_description_position',
      [
        'label' => esc_html__( 'Description Position', 'elementor-pro' ),
        'type' => Controls_Manager::CHOOSE,
        'options' => [
          'top' => [
            'title' => esc_html__( 'Top', 'elementor-pro' ),
            'icon' => 'eicon-v-align-top',
          ],
          'bottom' => [
            'title' => esc_html__( 'Bottom', 'elementor-pro' ),
            'icon' => 'eicon-v-align-bottom',
          ],
        ],
        'default' => 'top',
        'condition' => [
          'image_title_description' => 'always_show',
          'overlay_description!' => '',
          'gallery_layout' => 'grid',
        ],
      ]
    );

		$this->end_controls_section(); // overlay

		$this->start_controls_section(
			'image_style',
			[
				'label' => esc_html__( 'Image', 'elementor-pro' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs( 'image_tabs' );

		$this->start_controls_tab(
			'image_normal',
			[
				'label' => esc_html__( 'Normal', 'elementor-pro' ),
			]
		);

		$this->add_control(
			'image_border_color',
			[
				'label' => esc_html__( 'Border Color', 'elementor-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => '--image-border-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'image_border_width',
			[
				'label' => esc_html__( 'Border Width', 'elementor-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'range' => [
					'px' => [
						'max' => 20,
					],
					'em' => [
						'max' => 2,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--image-border-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'image_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'elementor-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'selectors' => [
					'{{WRAPPER}}' => '--image-border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

    /* 10WEB customization */
    $this->add_control(
      'image_background_color',
      [
        'label' => esc_html__( 'Background Color', 'tenweb-builder' ),
        'type' => Controls_Manager::COLOR,
        /*
         * 10web
         */
        'default' => '#00000020',
        /*
         *
         */
        'selectors' => [
          '{{WRAPPER}}' => '--image-background-color: {{VALUE}};',
        ],
      ]
    );//end

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'image_css_filters',
				'selector' => '{{WRAPPER}} .e-gallery-image',
			]
		);

		$this->end_controls_tab(); // overlay_background normal

		$this->start_controls_tab(
			'image_hover',
			[
				'label' => esc_html__( 'Hover', 'elementor-pro' ),
			]
		);

		$this->add_control(
			'image_border_color_hover',
			[
				'label' => esc_html__( 'Border Color', 'elementor-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-gallery-item:hover' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'image_border_radius_hover',
			[
				'label' => esc_html__( 'Border Radius', 'elementor-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .elementor-gallery-item:hover' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'image_css_filters_hover',
				'selector' => '{{WRAPPER}} .e-gallery-item:hover .e-gallery-image',
			]
		);

		$this->end_controls_tab(); // overlay_background normal

		$this->end_controls_tabs();// overlay_background tabs

    /* 10web customiztion */
    $this->add_control(
      'background_size',
      [
        'label' => esc_html__( 'Background Size', 'tenweb-builder' ),
        'type' => Controls_Manager::SELECT,
        'separator' => 'before',
        'default' => 'cover',
        'options' => [
          'cover' => _x( 'Cover', 'Background Control', 'tenweb-builder' ),
          'contain' => _x( 'Contain', 'Background Control', 'tenweb-builder' ),
          'auto' => _x( 'Auto', 'Background Control', 'tenweb-builder' ),
        ],
        'selectors' => [
          '{{WRAPPER}} .elementor-gallery-item .elementor-gallery-item__image' => 'background-size: {{VALUE}}; background-repeat: no-repeat;',
        ],
        'condition' => [
          'gallery_layout' => 'grid',
        ],
      ]
    );
    /* end 10web customiztion */

		$this->add_control(
			'image_hover_animation',
			[
				'label' => esc_html__( 'Hover Animation', 'elementor-pro' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => 'None',
					'grow' => 'Zoom In',
					'shrink-contained' => 'Zoom Out',
					'move-contained-left' => 'Move Left',
					'move-contained-right' => 'Move Right',
					'move-contained-top' => 'Move Up',
					'move-contained-bottom' => 'Move Down',
				],
				'separator' => 'before',
				'default' => '',
				'frontend_available' => true,
				'render_type' => 'ui',
			]
		);

		$this->add_control(
			'image_animation_duration',
			[
				'label' => esc_html__( 'Animation Duration', 'elementor-pro' ) . ' (ms)',
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 800,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 3000,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--image-transition-duration: {{SIZE}}ms',
				],
			]
		);

		$this->end_controls_section(); // overlay_background

    /* 10Web Builder */
    $this->start_controls_section(
      'gallery_title_description',
      [
        'label' => esc_html__( 'Title and Description', 'tenweb-builder' ),
        'tab' => Controls_Manager::TAB_STYLE,
        'condition' => [
          'display_gallery_title_description' => 'yes',
        ],
      ]
    );

    $this->add_control(
      'heading_gallery_name',
      [
        'label' => esc_html__( 'Title', 'tenweb-builder' ),
        'type' => Controls_Manager::HEADING,
        'separator' => 'before',
      ]
    );

    $this->add_control(
      'gallery_title_color',
      [
        'label' => esc_html__( 'Text Color', 'elementor' ),
        'type' => Controls_Manager::COLOR,
        'global' => [
          'default' => Global_Colors::COLOR_PRIMARY,
        ],
        'selectors' => [
          '{{WRAPPER}} .elementor-heading-title.elementor-single-gallery-title' => 'color: {{VALUE}};',
        ],
      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'typography_title',
        'global' => [
          'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
        ],
        'selector' => '{{WRAPPER}} .elementor-heading-title.elementor-single-gallery-title',
      ]
    );

    $this->add_group_control(
      Group_Control_Text_Shadow::get_type(),
      [
        'name' => 'text_shadow',
        'selector' => '{{WRAPPER}} .elementor-heading-title.elementor-single-gallery-title',
      ]
    );

    $this->add_control(
      'heading_gallery_description',
      [
        'label' => esc_html__( 'Description', 'tenweb-builder' ),
        'type' => Controls_Manager::HEADING,
        'separator' => 'before',
      ]
    );

    $this->add_control(
      'gallery_description_color',
      [
        'label' => esc_html__( 'Text Color', 'elementor' ),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .elementor-single-gallery-title-description-wrapper .elementor-text-editor' => 'color: {{VALUE}};',
        ],
        'global' => [
          'default' => Global_Colors::COLOR_TEXT,
        ],
      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'typography_description',
        'global' => [
          'default' => Global_Typography::TYPOGRAPHY_TEXT,
        ],
        'selector' => '{{WRAPPER}} .elementor-single-gallery-title-description-wrapper .elementor-text-editor',
      ]
    );
    $this->end_controls_section(); // title and description
    //end 10web customiztion

		$this->start_controls_section(
			'overlay_style',
			[
				'label' => esc_html__( 'Overlay', 'elementor-pro' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'overlay_background' => 'yes',
				],
			]
		);

		$this->start_controls_tabs( 'overlay_background_tabs' );

		$this->start_controls_tab(
			'overlay_normal',
			[
				'label' => esc_html__( 'Normal', 'elementor-pro' ),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'overlay_background',
				'types' => [ 'classic', 'gradient' ],
				'exclude' => [ 'image' ],
				'selector' => '{{WRAPPER}} .elementor-gallery-item__overlay',
				'fields_options' => [
					'background' => [
						'label' => esc_html__( 'Overlay', 'elementor-pro' ),
					],
				],
			]
		);

		$this->end_controls_tab(); // overlay_background normal

		$this->start_controls_tab(
			'overlay_hover',
			[
				'label' => esc_html__( 'Hover', 'elementor-pro' ),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'overlay_background_hover',
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .e-gallery-item:hover .elementor-gallery-item__overlay',
				'exclude' => [ 'image' ],
				'fields_options' => [
					'background' => [
						'default' => 'classic',
					],
					'color' => [
						'default' => 'rgba(0,0,0,0.5)',
					],
				],
			]
		);

		$this->end_controls_tab(); // overlay_background normal

		$this->end_controls_tabs();// overlay_background tabs

		$this->add_control(
			'image_blend_mode',
			[
				'label' => esc_html__( 'Blend Mode', 'elementor-pro' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => esc_html__( 'Normal', 'elementor-pro' ),
					'multiply' => 'Multiply',
					'screen' => 'Screen',
					'overlay' => 'Overlay',
					'darken' => 'Darken',
					'lighten' => 'Lighten',
					'color-dodge' => 'Color Dodge',
					'color-burn' => 'Color Burn',
					'hue' => 'Hue',
					'saturation' => 'Saturation',
					'color' => 'Color',
					'exclusion' => 'Exclusion',
					'luminosity' => 'Luminosity',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--overlay-mix-blend-mode: {{VALUE}}',
				],
				'separator' => 'before',
				'render_type' => 'ui',
			]
		);

		$this->add_control(
			'background_overlay_hover_animation',
			[
				'label' => esc_html__( 'Hover Animation', 'elementor-pro' ),
				'type' => Controls_Manager::SELECT,
				'groups' => [
					[
						'label' => esc_html__( 'None', 'elementor-pro' ),
						'options' => [
							'' => esc_html__( 'None', 'elementor-pro' ),
						],
					],
					[
						'label' => esc_html__( 'Entrance', 'elementor-pro' ),
						'options' => [
							'enter-from-right' => 'Slide In Right',
							'enter-from-left' => 'Slide In Left',
							'enter-from-top' => 'Slide In Up',
							'enter-from-bottom' => 'Slide In Down',
							'enter-zoom-in' => 'Zoom In',
							'enter-zoom-out' => 'Zoom Out',
							'fade-in' => 'Fade In',
						],
					],
					[
						'label' => esc_html__( 'Exit', 'elementor-pro' ),
						'options' => [
							'exit-to-right' => 'Slide Out Right',
							'exit-to-left' => 'Slide Out Left',
							'exit-to-top' => 'Slide Out Up',
							'exit-to-bottom' => 'Slide Out Down',
							'exit-zoom-in' => 'Zoom In',
							'exit-zoom-out' => 'Zoom Out',
							'fade-out' => 'Fade Out',
						],
					],
				],
				'separator' => 'before',
				'default' => '',
				'frontend_available' => true,
				'render_type' => 'ui',
			]
		);

		$this->add_control(
			'background_overlay_animation_duration',
			[
				'label' => esc_html__( 'Animation Duration', 'elementor-pro' ) . ' (ms)',
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 800,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 3000,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--overlay-transition-duration: {{SIZE}}ms',
				],
			]
		);

		$this->end_controls_section(); // overlay_background

		$this->start_controls_section(
			'overlay_content_style',
			[
				'label' => esc_html__( 'Content', 'elementor-pro' ),
				'tab' => Controls_Manager::TAB_STYLE,
				//TODO: add conditions for this section
			]
		);

		$this->add_control(
			'content_alignment',
			[
				'label' => esc_html__( 'Alignment', 'elementor-pro' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'elementor-pro' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'elementor-pro' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'elementor-pro' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}}' => '--content-text-align: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'content_vertical_position',
			[
				'label' => esc_html__( 'Vertical Position', 'elementor-pro' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'top' => [
						'title' => esc_html__( 'Top', 'elementor-pro' ),
						'icon' => 'eicon-v-align-top',
					],
					'middle' => [
						'title' => esc_html__( 'Middle', 'elementor-pro' ),
						'icon' => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' => esc_html__( 'Bottom', 'elementor-pro' ),
						'icon' => 'eicon-v-align-bottom',
					],
				],
				'selectors_dictionary' => [
					'top' => 'flex-start',
					'middle' => 'center',
					'bottom' => 'flex-end',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--content-justify-content: {{VALUE}}',
				],
        /* 10web */
        'conditions' => [
          'relation' => 'or',
          'terms' => [
            [
              'name' => 'image_title_description',
              'operator' => '==',
              'value' => 'on_hover'
            ],
            [
              'name' => 'gallery_layout',
              'operator' => 'in',
              'value' => ['masonry','justified']
            ],
          ]
        ],
			]
		);

		$this->add_responsive_control(
			'content_padding',
			[
				'label' => esc_html__( 'Padding', 'elementor-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'default' => [
					'size' => 20,
				],
				'selectors' => [
					'{{WRAPPER}}' => '--content-padding: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'heading_title',
			[
				'label' => esc_html__( 'Title', 'elementor-pro' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'overlay_title!' => '',
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => esc_html__( 'Color', 'elementor-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => '--title-text-color: {{VALUE}}',
				],
				'condition' => [
					'overlay_title!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '{{WRAPPER}} .elementor-gallery-item__title',
				'condition' => [
					'overlay_title!' => '',
				],
			]
		);

		$this->add_control(
			'title_spacing',
			[
				'label' => esc_html__( 'Spacing', 'elementor-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'selectors' => [
					'{{WRAPPER}}' => '--description-margin-top: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'overlay_title!' => '',
				],
			]
		);

		$this->add_control(
			'heading_description',
			[
				'label' => esc_html__( 'Description', 'elementor-pro' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'overlay_description!' => '',
				],
			]
		);

		$this->add_control(
			'description_color',
			[
				'label' => esc_html__( 'Color', 'elementor-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => '--description-text-color: {{VALUE}}',
				],
				'condition' => [
					'overlay_description!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'description_typography',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
				'selector' => '{{WRAPPER}} .elementor-gallery-item__description',
				'condition' => [
					'overlay_description!' => '',
				],
			]
		);

		$this->add_control(
			'content_hover_animation',
			[
				'label' => esc_html__( 'Hover Animation', 'elementor-pro' ),
				'type' => Controls_Manager::SELECT,
				'groups' => [
					[
						'label' => esc_html__( 'None', 'elementor-pro' ),
						'options' => [
							'' => esc_html__( 'None', 'elementor-pro' ),
						],
					],
					[
						'label' => esc_html__( 'Entrance', 'elementor-pro' ),
						'options' => [
							'enter-from-right' => 'Slide In Right',
							'enter-from-left' => 'Slide In Left',
							'enter-from-top' => 'Slide In Up',
							'enter-from-bottom' => 'Slide In Down',
							'enter-zoom-in' => 'Zoom In',
							'enter-zoom-out' => 'Zoom Out',
							'fade-in' => 'Fade In',
						],
					],
					[
						'label' => esc_html__( 'Reaction', 'elementor-pro' ),
						'options' => [
							'grow' => 'Grow',
							'shrink' => 'Shrink',
							'move-right' => 'Move Right',
							'move-left' => 'Move Left',
							'move-up' => 'Move Up',
							'move-down' => 'Move Down',
						],
					],
					[
						'label' => esc_html__( 'Exit', 'elementor-pro' ),
						'options' => [
							'exit-to-right' => 'Slide Out Right',
							'exit-to-left' => 'Slide Out Left',
							'exit-to-top' => 'Slide Out Up',
							'exit-to-bottom' => 'Slide Out Down',
							'exit-zoom-in' => 'Zoom In',
							'exit-zoom-out' => 'Zoom Out',
							'fade-out' => 'Fade Out',
						],
					],
				],
				'default' => 'fade-in',
				'separator' => 'before',
				'render_type' => 'ui',
        /* 10web */
        'conditions' => [
          'relation' => 'or',
          'terms' => [
            [
              'name' => 'image_title_description',
              'operator' => '==',
              'value' => 'on_hover'
            ],
            [
              'name' => 'gallery_layout',
              'operator' => 'in',
              'value' => ['masonry','justified']
            ],
          ]
        ],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'content_animation_duration',
			[
				'label' => esc_html__( 'Animation Duration', 'elementor-pro' ) . ' (ms)',
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 800,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 3000,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--content-transition-duration: {{SIZE}}ms; --content-transition-delay: {{SIZE}}ms;',
				],
        /* 10web */
        'conditions' => [
          'relation' => 'or',
          'terms' => [
            [
              'name' => 'image_title_description',
              'operator' => '==',
              'value' => 'on_hover',
	      'content_hover_animation!' => '',
            ],
            [
              'name' => 'gallery_layout',
              'operator' => 'in',
              'value' => ['masonry','justified'],
	      'content_hover_animation!' => '',
            ],
          ]
        ],
			]
		);

		$this->add_control(
			'content_sequenced_animation',
			[
				'label' => esc_html__( 'Sequenced Animation', 'elementor-pro' ),
				'type' => Controls_Manager::SWITCHER,
        /* 10web */
        'conditions' => [
          'relation' => 'or',
          'terms' => [
            [
              'name' => 'image_title_description',
              'operator' => '==',
              'value' => 'on_hover',
	      'content_hover_animation!' => '',
            ],
            [
              'name' => 'gallery_layout',
              'operator' => 'in',
              'value' => ['masonry','justified'],
	      'content_hover_animation!' => '',
            ],
          ]
        ],
				'frontend_available' => true,
				'render_type' => 'ui',
			]
		);

		$this->end_controls_section(); // overlay_content

		$this->start_controls_section(
			'filter_bar_style',
			[
				'label' => esc_html__( 'Filter Bar', 'elementor-pro' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'gallery_type' => 'multiple',
				],
			]
		);

		$this->add_control(
			'align_filter_bar_items',
			[
				'label' => esc_html__( 'Alignment', 'elementor-pro' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'elementor-pro' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'elementor-pro' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'elementor-pro' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'prefix_class' => 'elementor-gallery--filter-align-',
				'selectors_dictionary' => [
					'left' => 'flex-start',
					'right' => 'flex-end',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--titles-container-justify-content: {{VALUE}}',
				],
			]
		);

		$this->start_controls_tabs( 'filter_bar_colors' );

		$this->start_controls_tab( 'filter_bar_colors_normal',
			[
				'label' => esc_html__( 'Normal', 'elementor-pro' ),
			]
		);

		$this->add_control(
			'galleries_title_color_normal',
			[
				'label' => esc_html__( 'Text Color', 'elementor-pro' ),
				'type' => Controls_Manager::COLOR,
				'global' => [
					'default' => Global_Colors::COLOR_PRIMARY,
				],
				'selectors' => [
					'{{WRAPPER}}' => '--galleries-title-color-normal: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'galleries_titles_typography',
				'selector' => '{{WRAPPER}} .elementor-gallery-title',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
			]
		);

		$this->end_controls_tab();// filter_bar_colors_normal

		$this->start_controls_tab( 'filter_bar_colors_hover',
			[
				'label' => esc_html__( 'Hover', 'elementor-pro' ),
			]
		);

		$this->add_control(
			'galleries_title_color_hover',
			[
				'label' => esc_html__( 'Text Color', 'elementor-pro' ),
				'type' => Controls_Manager::COLOR,
				'global' => [
					'default' => Global_Colors::COLOR_SECONDARY,
				],
				'selectors' => [
					'{{WRAPPER}}' => '--galleries-title-color-hover: {{VALUE}}',
				],
				'condition' => [
					'pointer!' => 'background',
				],
			]
		);

		/*
		When the pointer style = background, users could need a different text color.
		The control handles the title color in hover state, only when the pointer style is background.
		*/
		$this->add_control(
			'galleries_title_color_hover_pointer_bg',
			[
				'label' => esc_html__( 'Text Color', 'elementor-pro' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}}' => '--galleries-title-color-hover: {{VALUE}}',
				],
				'condition' => [
					'pointer' => 'background',
				],
			]
		);

		$this->add_control(
			'galleries_pointer_color_hover',
			[
				'label' => esc_html__( 'Pointer Color', 'elementor-pro' ),
				'type' => Controls_Manager::COLOR,
				'global' => [
					'default' => Global_Colors::COLOR_ACCENT,
				],
				'selectors' => [
					'{{WRAPPER}}' => '--galleries-pointer-bg-color-hover: {{VALUE}}',
				],
				'condition' => [
					'pointer!' => [ 'none', 'text' ],
				],
			]
		);

		$this->end_controls_tab();// filter_bar_colors_hover

		$this->start_controls_tab( 'filter_bar_colors_active',
			[
				'label' => esc_html__( 'Active', 'elementor-pro' ),
			]
		);

		$this->add_control(
			'galleries_title_color_active',
			[
				'label' => esc_html__( 'Text Color', 'elementor-pro' ),
				'type' => Controls_Manager::COLOR,
				'global' => [
					'default' => Global_Colors::COLOR_SECONDARY,
				],
				'selectors' => [
					'{{WRAPPER}}' => '--gallery-title-color-active: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'galleries_pointer_color_active',
			[
				'label' => esc_html__( 'Pointer Color', 'elementor-pro' ),
				'type' => Controls_Manager::COLOR,
				'global' => [
					'default' => Global_Colors::COLOR_ACCENT,
				],
				'selectors' => [
					'{{WRAPPER}}' => '--galleries-pointer-bg-color-active: {{VALUE}}',
				],
				'condition' => [
					'pointer!' => [ 'none', 'text' ],
				],

			]
		);

		$this->end_controls_tab();// filter_bar_colors_active

		$this->end_controls_tabs(); // filter_bar_colors

		$this->add_control(
			'pointer_width',
			[
				'label' => esc_html__( 'Pointer Width', 'elementor-pro' ),
				'type' => Controls_Manager::SLIDER,
				'devices' => [ Breakpoints_Manager::BREAKPOINT_KEY_DESKTOP, Breakpoints_Manager::BREAKPOINT_KEY_TABLET ],
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'range' => [
					'px' => [
						'max' => 30,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--galleries-pointer-border-width: {{SIZE}}{{UNIT}}',
				],
				'separator' => 'before',
				'condition' => [
					'pointer' => [ 'underline', 'overline', 'double-line', 'framed' ],
				],
			]
		);

		$this->add_control(
			'galleries_titles_space_between',
			[
				'label' => esc_html__( 'Space Between', 'elementor-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .elementor-gallery-title' => '--space-between: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'galleries_titles_gap',
			[
				'label' => esc_html__( 'Gap', 'elementor-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .elementor-gallery__titles-container' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section(); // filter_bar_style
	}

	protected function render_static() {
		$settings = $this->get_settings_for_display();

		$is_multiple = 'multiple' === $settings['gallery_type'] && ! empty( $settings['galleries'] );

		$is_single = 'single' === $settings['gallery_type'] && ! empty( $settings['gallery'] );

		$gap = $settings['gap']['size'] . $settings['gap']['unit'];
    $hgap = $settings['hgap']['size'] . $settings['hgap']['unit'];
		$ratio_percentage = '75';
		$columns = 4;

		if ( $settings['columns'] ) {
			$columns = $settings['columns'];
		}

		if ( $settings['aspect_ratio'] ) {
			$ratio_array = explode( ':', $settings['aspect_ratio'] );

			$ratio_percentage = ( $ratio_array[1] / $ratio_array[0] ) * 100;
		}

		$this->add_render_attribute(
			'gallery_container',
			[
				'style' => "--columns: {$columns}; --aspect-ratio: {$ratio_percentage}%; --hgap: {$hgap}; --vgap: {$gap};",
				'class' => 'e-gallery-grid',
			]
		);

		$galleries = [];

		if ( $is_multiple ) {
			foreach ( array_values( $settings['galleries'] ) as $multi_gallery ) {
				if ( ! $multi_gallery['multiple_gallery'] ) {
					continue;
				}

				$galleries[] = $multi_gallery['multiple_gallery'];
			}
		} elseif ( $is_single ) {
			$galleries[0] = $settings['gallery'];
		}

		foreach ( $galleries as $gallery ) {
			foreach ( $gallery as $item ) {
				$image_src = wp_get_attachment_image_src( $item['id'] );

				$this->add_render_attribute( 'gallery_item_image_' . $item['id'], [
					'style' => "background-image: url('{$image_src[0]}');",
				] );
			}
		}

		$this->render();
	}

	/**
	 *
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$is_multiple = 'multiple' === $settings['gallery_type'] && ! empty( $settings['galleries'] );

		$is_single = 'single' === $settings['gallery_type'] && ! empty( $settings['gallery'] );

		$has_description = ! empty( $settings['overlay_description'] );

		$has_title = ! empty( $settings['overlay_title'] );

    /* 10Web */

    $on_hover = ( $has_title || $has_description )
      && ( $settings['image_title_description'] == 'on_hover' || $settings['image_title_description'] == NULL );
    $always_show = ( $has_title || $has_description )
      && $settings['image_title_description'] == 'always_show';
    $always_show_top = $always_show && ( $settings['image_title_position'] == 'top' || $settings['image_description_position'] == 'top' );
    $always_show_bottom = $always_show && ( $settings['image_title_position'] == 'bottom' || $settings['image_description_position'] == 'bottom' );

		$has_animation = ! empty( $settings['image_hover_animation'] ) || ! empty( $settings['content_hover_animation'] ) || ! empty( $settings['background_overlay_hover_animation'] );

		$gallery_item_tag = ! empty( $settings['link_to'] ) ? 'a' : 'div';

		$galleries = [];

		if ( $is_multiple ) {
			$this->add_render_attribute( 'titles-container', 'class', 'elementor-gallery__titles-container' );

			if ( $settings['pointer'] ) {
				$this->add_render_attribute( 'titles-container', 'class', 'e--pointer-' . $settings['pointer'] );

				foreach ( $settings as $key => $value ) {
					if ( 0 === strpos( $key, 'animation' ) && $value ) {
						$this->add_render_attribute( 'titles-container', 'class', 'e--animation-' . $value );
						break;
					}
				}
			} ?>
			<div <?php $this->print_render_attribute_string( 'titles-container' ); ?>>
				<?php if ( $settings['show_all_galleries'] ) { ?>
					<a data-gallery-index="all" class="elementor-item elementor-gallery-title">
						<?php $this->print_unescaped_setting( 'show_all_galleries_label' ); ?>
					</a>
				<?php } ?>

				<?php foreach ( $settings['galleries'] as $index => $gallery ) :
					if ( ! $gallery['multiple_gallery'] ) {
						continue;
					}

					$galleries[ $index ] = $gallery['multiple_gallery'];
					?>
					<a data-gallery-index="<?php echo esc_attr( $index ); ?>" class="elementor-item elementor-gallery-title">
						<?php $this->print_unescaped_setting( 'gallery_title', 'galleries', $index ); ?>
					</a>
					<?php
				endforeach; ?>
			</div>
			<?php
		} elseif ( $is_single ) {
			$galleries[0] = $settings['gallery'];
      /* 10Web Builder */
      if ( $settings['display_gallery_title_description'] != '' ) {
        ?>
        <div class="elementor-single-gallery-title-description-wrapper elementor-field-group elementor-column elementor-col-100 elementor-widget-wrap elementor-widget-heading">
          <?php
          $this->render_single_gallery_title();
          if ( $settings['description_position'] == 'top' ) {
            $this->render_single_gallery_description();
          }
          ?>
        </div>
        <?php
      }
		} elseif ( \Elementor\Plugin::instance()->editor->is_edit_mode() ) { ?>
			<i class="elementor-widget-empty-icon eicon-gallery-justified"></i>
		<?php }

		$this->add_render_attribute( 'gallery_container', 'class', 'elementor-gallery__container' );

		if ( $has_title || $has_description ) {
      /* 10Web */
      if ( $always_show_top ) {
        $this->add_render_attribute( 'gallery_item_content_top', 'class', 'elementor-gallery-item__content elementor-gallery-item__content__always_show_top' );
      }
      if ( $always_show_bottom ) {
        $this->add_render_attribute( 'gallery_item_content_bottom', 'class', 'elementor-gallery-item__content elementor-gallery-item__content__always_show_bottom' );
      }
      if ( $on_hover ) {
        $this->add_render_attribute( 'gallery_item_content_hover', 'class', 'elementor-gallery-item__content elementor-gallery-item__content__hover' );
      }

			if ( $has_title ) {
				$this->add_render_attribute( 'gallery_item_title', 'class', 'elementor-gallery-item__title' );
			}

			if ( $has_description ) {
				$this->add_render_attribute( 'gallery_item_description', 'class', 'elementor-gallery-item__description' );
			}
		}

		$this->add_render_attribute( 'gallery_item_background_overlay', [ 'class' => 'elementor-gallery-item__overlay' ] );

		$gallery_items = [];
		$thumbnail_size = $settings['thumbnail_image_size'];
		foreach ( $galleries as $gallery_index => $gallery ) {
			foreach ( $gallery as $index => $item ) {
				if ( in_array( $item['id'], array_keys( $gallery_items ), true ) ) {
					$gallery_items[ $item['id'] ][] = $gallery_index;
				} else {
					$gallery_items[ $item['id'] ] = [ $gallery_index ];
				}
			}
		}

		if ( 'random' === $settings['order_by'] ) {
			$shuffled_items = [];
			$keys = array_keys( $gallery_items );
			shuffle( $keys );
			foreach ( $keys as $key ) {
				$shuffled_items[ $key ] = $gallery_items[ $key ];
			}
			$gallery_items = $shuffled_items;
		}

		if ( ! empty( $galleries ) ) { ?>
		<div <?php $this->print_render_attribute_string( 'gallery_container' ); ?>>
			<?php
			foreach ( $gallery_items as $id => $tags ) :
				$unique_index = $id; //$gallery_index . '_' . $index;
				$image_src = wp_get_attachment_image_src( $id, $thumbnail_size );
				if ( ! $image_src ) {
					continue;
				}
				$attachment = get_post( $id );
				$image_data = [
					'alt' => get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true ),
					'media' => wp_get_attachment_image_src( $id, 'full' )['0'],
					'src' => $image_src['0'],
					'width' => $image_src['1'],
					'height' => $image_src['2'],
					'caption' => $attachment->post_excerpt,
					'description' => $attachment->post_content,
					'title' => $attachment->post_title,
				];

				$this->add_render_attribute( 'gallery_item_' . $unique_index, [
					'class' => [
						'e-gallery-item',
						'elementor-gallery-item',
					],
				] );

        /* 10web */
        if ( !$on_hover && !$always_show_bottom ) {
          $this->add_render_attribute( 'gallery_item_' . $unique_index, [
            'class' => [
              'twbb-flex-end-item',
            ],
          ] );
        }

				if ( $has_animation ) {
					$this->add_render_attribute( 'gallery_item_' . $unique_index, [ 'class' => 'elementor-animated-content' ] );
				}

				if ( $is_multiple ) {
					$this->add_render_attribute( 'gallery_item_' . $unique_index, [ 'data-e-gallery-tags' => implode( ',', $tags ) ] );
				}

				if ( 'a' === $gallery_item_tag ) {
					if ( 'file' === $settings['link_to'] ) {
						$href = $image_data['media'];

						$this->add_render_attribute( 'gallery_item_' . $unique_index, [
							'href' => $href,
						] );

						$this->add_lightbox_data_attributes( 'gallery_item_' . $unique_index, $id, 'yes', 'all-' . $this->get_id() );
					} elseif ( 'custom' === $settings['link_to'] ) {
						$this->add_link_attributes( 'gallery_item_' . $unique_index, $settings['url'] );
					}
				}

				$this->add_render_attribute( 'gallery_item_image_' . $unique_index,
					[
						'class' => [
							'e-gallery-image',
							'elementor-gallery-item__image',
						],
						'data-thumbnail' => $image_data['src'],
						'data-width' => $image_data['width'],
						'data-height' => $image_data['height'],
						'alt' => $image_data['alt'],
					]
				);?>
				<<?php Utils::print_validated_html_tag( $gallery_item_tag ); ?>

                <?php $this->print_render_attribute_string( 'gallery_item_' . $unique_index ); ?>>
        <?php
        /* 10Web Builder */
        if ( $always_show_top ) {
          $this->render_item_title_description( 'top', $image_data );
        }
        ?>

        <div class="twbb_item_image_section">
          <div <?php $this->print_render_attribute_string( 'gallery_item_image_' . $unique_index ); ?> ></div>
          <?php if ( ! empty( $settings['overlay_background'] ) ) : ?>
            <div <?php $this->print_render_attribute_string( 'gallery_item_background_overlay' ); ?>></div>
          <?php endif; ?>
        </div>
        <?php if ( $always_show_bottom ) {
        $this->render_item_title_description( 'bottom', $image_data );
      } elseif ( $on_hover ) {
        $this->render_item_title_description( 'hover', $image_data );
      }
        /* end customization */
        ?>
       </<?php Utils::print_validated_html_tag( $gallery_item_tag ); ?>>
      <?php endforeach;
      //endforeach; ?>
      </div>

      <?php
      /**
       * 10Web Builder
       */
      if ( $settings['display_gallery_title_description'] != '' && $settings['description_position'] == 'bottom' ) {
        ?>
        <div class="elementor-single-gallery-title-description-wrapper elementor-field-group elementor-column elementor-col-100 elementor-widget-wrap elementor-widget-heading">
          <?php
          $this->render_single_gallery_description();
          ?>
        </div>
        <?php
      }
    }
  }

  /**
   * 10Web
   */
  protected function render_single_gallery_title() {
    $settings = $this->get_settings_for_display();

    if ( '' === $settings['gallery_name'] ) {
      return;
    }

    $this->add_render_attribute( 'gallery_name', 'class', 'elementor-single-gallery-title elementor-element elementor-heading-title' );

    if ( ! empty( $settings['title_size'] ) ) {
      $this->add_render_attribute( 'gallery_name', 'class', 'elementor-size-' . $settings['title_size'] );
    }

    $title = $settings['gallery_name'];

    $title_html = sprintf( '<%1$s %2$s>%3$s</%1$s>', \Elementor\Utils::validate_html_tag( $settings['title_tag'] ), $this->get_render_attribute_string( 'gallery_name' ), $title );

    echo $title_html;
  }

  /**
   * 10Web
   */
  protected function render_single_gallery_description() {
    $settings = $this->get_settings_for_display();

    if ( '' === $settings[ 'gallery_description' ] ) {
      return;
    }

    $editor_content = $settings[ 'gallery_description' ];

    $editor_content = $this->parse_text_editor( $editor_content );

    $this->add_render_attribute( 'gallery_description', 'class', [ 'elementor-text-editor', 'elementor-clearfix', 'elementor-element' ] );

    $this->add_inline_editing_attributes( 'gallery_description', 'advanced' );
    ?>
    <div <?php echo $this->get_render_attribute_string( 'gallery_description' ); ?>><?php echo $editor_content; ?></div>
    <?php
  }

  /**
   * 10Web
   */
  protected function render_item_title_description( $position, $image_data ) {
    $settings = $this->get_settings_for_display();

    $has_description = ! empty( $settings['overlay_description'] );

    $has_title = ! empty( $settings['overlay_title'] );

    $on_hover = ( $has_title || $has_description )
      && ( $settings['image_title_description'] == 'on_' . $position || $settings['image_title_description'] == NULL );
    $always_show = ( $has_title || $has_description ) && $settings['image_title_description'] == 'always_show';

    if ( $position == 'top' ) {
      ?><div <?php echo $this->get_render_attribute_string( 'gallery_item_content_top' ); ?>><?php
    } elseif ( $position == 'bottom' ) {
      ?><div <?php $this->print_render_attribute_string( 'gallery_item_content_bottom' ); ?>><?php
    } elseif (  $position == 'hover' ) {
      ?><div <?php $this->print_render_attribute_string( 'gallery_item_content_hover' ); ?>><?php
    }

    if ( $has_title && ( ( $always_show && $settings['image_title_position'] == $position )
        || $on_hover ) ) :
      $title = $image_data[ $settings['overlay_title'] ];
      if ( ! empty( $title ) ) : ?>
        <div <?php $this->print_render_attribute_string( 'gallery_item_title' ); ?>><?php echo $title; ?></div>
      <?php endif;
    endif;

    if ( $has_description && ( ( $always_show && $settings['image_description_position'] == $position)
        || $on_hover ) ) :
      $description = $image_data[ $settings['overlay_description'] ];
      if ( ! empty( $description ) ) :?>
        <div <?php $this->print_render_attribute_string( 'gallery_item_description' ); ?>><?php echo $description; ?></div>
      <?php endif;
    endif; ?>
    </div> <?php
  }
}
