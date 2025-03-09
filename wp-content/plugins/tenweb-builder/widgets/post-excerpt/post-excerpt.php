<?php
namespace Tenweb_Builder;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Post_Excerpt extends Widget_Base {

	public function get_name() {
		return Builder::$prefix . 'post-excerpt';
	}

	public function get_title() {
		return __( 'Post Excerpt', 'tenweb-builder' );
	}

	public function get_icon() {
		return 'twbb-post-excerpt twbb-widget-icon';
	}

	public function get_categories() {
		return [ 'tenweb-builder-widgets' ];
	}

	public function get_keywords() {
		return [ 'post', 'page', 'content', 'excerpt' ];
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_style',
			[
				'label' => __( 'Style', 'tenweb-builder' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'align',
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
					'justify' => [
						'title' => __( 'Justified', 'tenweb-builder' ),
						'icon' => 'fa fa-align-justify',
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'text_color',
			[
				'label' => __( 'Text Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}}' => 'color: {{VALUE}};',
				],
                'global' => [
                    'default' => Global_Colors::COLOR_TEXT,
                ],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'typography',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_TEXT,
                ],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		if(Templates::get_instance()->is_twbb_template()['template_type'] !== false) {
		  $excerpt = $this->get_template_placeholder();
		} else {
		  $excerpt = get_the_excerpt();
		}

		if ( empty( $excerpt ) ) {
		  return;
		}

		echo $excerpt; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	private function get_template_placeholder(){
		$content = "<b>This is the Post Excerpt Widget.</b> It is a dynamic widget that displays the excerpt of each post/page content.";

		return $content;
	}
}

\Elementor\Plugin::instance()->widgets_manager->register( new Post_Excerpt() );
