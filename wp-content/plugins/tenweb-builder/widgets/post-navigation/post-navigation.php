<?php
namespace Tenweb_Builder;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Post_Navigation extends Widget_Base {

	public function get_name() {
		return Builder::$prefix .'post-navigation';
	}

	public function get_title() {
		return __( 'Post Navigation', 'tenweb-builder' );
	}

	public function get_icon() {
		return 'twbb-post-navigation twbb-widget-icon';
	}

  public function get_categories(){
    return ['tenweb-builder-widgets'];
  }

  public function get_script_depends() {
		return [ 'post-navigation' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_post_navigation_content',
			[
				'label' => __( 'Post Navigation', 'tenweb-builder' ),
			]
		);

		$this->add_control(
			'show_label',
			[
				'label' => __( 'Label', 'tenweb-builder' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'tenweb-builder' ),
				'label_off' => __( 'Hide', 'tenweb-builder' ),
				'default' => 'yes',
			]
		);

		$this->add_control(
			'prev_label',
			[
				'label' => __( 'Previous Label', 'tenweb-builder' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Previous', 'tenweb-builder' ),
				'condition' => [
					'show_label' => 'yes',
				],
			]
		);

		$this->add_control(
			'next_label',
			[
				'label' => __( 'Next Label', 'tenweb-builder' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Next', 'tenweb-builder' ),
				'condition' => [
					'show_label' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_arrow',
			[
				'label' => __( 'Arrows', 'tenweb-builder' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'tenweb-builder' ),
				'label_off' => __( 'Hide', 'tenweb-builder' ),
				'default' => 'yes',
			]
		);

		$this->add_control(
			'arrow',
			[
				'label' => __( 'Arrows Type', 'tenweb-builder' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'fa fa-angle-left' => __( 'Angle', 'tenweb-builder' ),
					'fa fa-angle-double-left' => __( 'Double Angle', 'tenweb-builder' ),
					'fa fa-chevron-left' => __( 'Chevron', 'tenweb-builder' ),
					'fa fa-chevron-circle-left' => __( 'Chevron Circle', 'tenweb-builder' ),
					'fa fa-caret-left' => __( 'Caret', 'tenweb-builder' ),
					'fa fa-arrow-left' => __( 'Arrow', 'tenweb-builder' ),
					'fa fa-long-arrow-alt-left' => __( 'Long Arrow', 'tenweb-builder' ),
					'fa fa-arrow-circle-left' => __( 'Arrow Circle', 'tenweb-builder' ),
					'fa fa-arrow-alt-circle-left' => __( 'Arrow Circle Negative', 'tenweb-builder' ),
				],
				'default' => 'fa fa-angle-left',
				'condition' => [
					'show_arrow' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_title',
			[
				'label' => __( 'Post Title', 'tenweb-builder' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'tenweb-builder' ),
				'label_off' => __( 'Hide', 'tenweb-builder' ),
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_borders',
			[
				'label' => __( 'Borders', 'tenweb-builder' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'tenweb-builder' ),
				'label_off' => __( 'Hide', 'tenweb-builder' ),
				'default' => 'yes',
				'prefix_class' => 'tenweb-post-navigation-borders-',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'label_style',
			[
				'label' => __( 'Label', 'tenweb-builder' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_label' => 'yes',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_label_style' );

		$this->start_controls_tab(
			'label_color_normal',
			[
				'label' => __( 'Normal', 'tenweb-builder' ),
			]
		);

		$this->add_control(
			'label_color',
			[
				'label' => __( 'Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
                'global' => [
                    'default' => Global_Colors::COLOR_TEXT,
                ],
				'selectors' => [
					'{{WRAPPER}} span.post-navigation__prev--label' => 'color: {{VALUE}};',
					'{{WRAPPER}} span.post-navigation__next--label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'label_color_hover',
			[
				'label' => __( 'Hover', 'tenweb-builder' ),
			]
		);

		$this->add_control(
			'label_hover_color',
			[
				'label' => __( 'Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} span.post-navigation__prev--label:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} span.post-navigation__next--label:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'label_typography',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
                ],
				'selector' => '{{WRAPPER}} span.post-navigation__prev--label, {{WRAPPER}} span.post-navigation__next--label',
				'exclude' => [ 'line_height' ], //phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'title_style',
			[
				'label' => __( 'Title', 'tenweb-builder' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_title' => 'yes',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_post_navigation_style' );

		$this->start_controls_tab(
			'tab_color_normal',
			[
				'label' => __( 'Normal', 'tenweb-builder' ),
			]
		);

		$this->add_control(
			'text_color',
			[
				'label' => __( 'Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
                'global' => [
                    'default' => Global_Colors::COLOR_SECONDARY,
                ],
				'selectors' => [
					'{{WRAPPER}} span.post-navigation__prev--title, {{WRAPPER}} span.post-navigation__next--title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_color_hover',
			[
				'label' => __( 'Hover', 'tenweb-builder' ),
			]
		);

		$this->add_control(
			'hover_color',
			[
				'label' => __( 'Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} span.post-navigation__prev--title:hover, {{WRAPPER}} span.post-navigation__next--title:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
                ],
				'selector' => '{{WRAPPER}} span.post-navigation__prev--title, {{WRAPPER}} span.post-navigation__next--title',
				'exclude' => [ 'line_height' ], //phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'arrow_style',
			[
				'label' => __( 'Arrow', 'tenweb-builder' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_arrow' => 'yes',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_post_navigation_arrow_style' );

		$this->start_controls_tab(
			'arrow_color_normal',
			[
				'label' => __( 'Normal', 'tenweb-builder' ),
			]
		);

		$this->add_control(
			'arrow_color',
			[
				'label' => __( 'Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .post-navigation__arrow-wrapper' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'arrow_color_hover',
			[
				'label' => __( 'Hover', 'tenweb-builder' ),
			]
		);

		$this->add_control(
			'arrow_hover_color',
			[
				'label' => __( 'Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .post-navigation__arrow-wrapper:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'arrow_size',
			[
				'label' => __( 'Size', 'tenweb-builder' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 6,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .post-navigation__arrow-wrapper' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'arrow_padding',
			[
				'label' => __( 'Gap', 'tenweb-builder' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'body:not(.rtl) {{WRAPPER}} .post-navigation__arrow-prev' => 'padding-right: {{SIZE}}{{UNIT}};',
					'body:not(.rtl) {{WRAPPER}} .post-navigation__arrow-next' => 'padding-left: {{SIZE}}{{UNIT}};',
					'body.rtl {{WRAPPER}} .post-navigation__arrow-prev' => 'padding-left: {{SIZE}}{{UNIT}};',
					'body.rtl {{WRAPPER}} .post-navigation__arrow-next' => 'padding-right: {{SIZE}}{{UNIT}};',
				],
				'range' => [
					'em' => [
						'min' => 0,
						'max' => 5,
					],
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'borders_section_style',
			[
				'label' => __( 'Borders', 'tenweb-builder' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_borders!' => '',
				],
			]
		);

		$this->add_control(
			'sep_color',
			[
				'label' => __( 'Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tenweb-post-navigation__separator' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .tenweb-post-navigation' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'borders_width',
			[
				'label' => __( 'Size', 'tenweb-builder' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tenweb-post-navigation__separator' => 'width: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .tenweb-post-navigation' => 'border-top-width: {{SIZE}}{{UNIT}}; border-bottom-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .tenweb-post-navigation__next.tenweb-post-navigation__link' => 'width: calc(50% - ({{SIZE}}{{UNIT}} / 2))',
					'{{WRAPPER}} .tenweb-post-navigation__prev.tenweb-post-navigation__link' => 'width: calc(50% - ({{SIZE}}{{UNIT}} / 2))',
				],
			]
		);

		$this->add_control(
			'borders_spacing',
			[
				'label' => __( 'Spacing', 'tenweb-builder' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .tenweb-post-navigation' => 'padding: {{SIZE}}{{UNIT}} 0;',
				],
				'range' => [
					'em' => [
						'min' => 0,
						'max' => 5,
					],
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_active_settings();

		$prev_label = '';
		$next_label = '';
		$prev_arrow = '';
		$next_arrow = '';

		if ( 'yes' === $settings['show_label'] ) {
			$prev_label = '<span class="post-navigation__prev--label">' . $settings['prev_label'] . '</span>';
			$next_label = '<span class="post-navigation__next--label">' . $settings['next_label'] . '</span>';
		}

		if ( 'yes' === $settings['show_arrow'] ) {
			if ( is_rtl() ) {
				$prev_icon_class = str_replace( 'left', 'right', $settings['arrow'] );
				$next_icon_class = $settings['arrow'];
			} else {
				$prev_icon_class = $settings['arrow'];
				$next_icon_class = str_replace( 'left', 'right', $settings['arrow'] );
			}

			$prev_arrow = '<span class="post-navigation__arrow-wrapper post-navigation__arrow-prev"><i class="' . $prev_icon_class . '" aria-hidden="true"></i></span>';
			$next_arrow = '<span class="post-navigation__arrow-wrapper post-navigation__arrow-next"><i class="' . $next_icon_class . '" aria-hidden="true"></i></span>';
		}

		$prev_title = '';
		$next_title = '';

		if ( 'yes' === $settings['show_title'] ) {
			$prev_title = '<span class="post-navigation__prev--title">%title</span>';
			$next_title = '<span class="post-navigation__next--title">%title</span>';
		}
		?>
		<div class="tenweb-post-navigation elementor-grid">
			<div class="tenweb-post-navigation__prev tenweb-post-navigation__link">
				<?php previous_post_link( '%link', $prev_arrow . '<span class="tenweb-post-navigation__link__prev">' . $prev_label . $prev_title . '</span>' ); ?>
			</div>
			<?php if ( 'yes' === $settings['show_borders'] ) : ?>
				<div class="tenweb-post-navigation__separator-wrapper">
					<div class="tenweb-post-navigation__separator"></div>
				</div>
			<?php endif; ?>
			<div class="tenweb-post-navigation__next tenweb-post-navigation__link">
				<?php next_post_link( '%link', '<span class="tenweb-post-navigation__link__next">' . $next_label . $next_title . '</span>' . $next_arrow ); ?>
			</div>
		</div>
		<?php
	}
}

\Elementor\Plugin::instance()->widgets_manager->register(new Post_Navigation());
