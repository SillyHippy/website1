<?php
namespace Tenweb_Builder;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Post_Content extends Widget_Base {

	public function get_name() {
		return Builder::$prefix . 'post-content';
	}

	public function get_title() {
		return __( 'Post Content', 'tenweb-builder' );
	}

	public function get_icon() {
		return 'twbb-post-content twbb-widget-icon';
	}

	public function get_categories() {
		return [ 'tenweb-builder-widgets' ];
	}

	public function get_keywords() {
		return [ 'post', 'page', 'content' ];
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
		static $did_post = [];

		$post = get_post();
		if ( !$post ) {
		  return;
		}

		if ( post_password_required( $post->ID ) ) {
			echo get_the_password_form( $post->ID ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			return;
		}

		if ( isset( $did_post[ $post->ID ] ) ) {
			return;
		}
		$did_post[ $post->ID ] = true;
		if(\Elementor\Plugin::instance()->preview->is_preview_mode($post->ID) && Templates::get_instance()->is_twbb_template()['template_type'] === false) {
		  $content = \Elementor\Plugin::instance()->preview->builder_wrapper('');
		} else if(\Elementor\Plugin::instance()->preview->is_preview_mode($post->ID) || Templates::get_instance()->is_twbb_template()['template_type'] !== false) {
		  $content = $this->get_template_placeholder();
		}
		else {
		  $document = \Elementor\Plugin::instance()->documents->get( $post->ID );
		  if ( $document ) {
			$preview_type = $document->get_settings( 'preview_type' );
			$preview_id = $document->get_settings( 'preview_id' );

			if ( !empty( $preview_type ) && 0 === strpos( $preview_type, 'single' ) && ! empty( $preview_id ) ) {
			  $post = get_post( $preview_id );

			  if ( ! $post ) {
				return;
			  }
			}
		  }
		  $editor = \Elementor\Plugin::instance()->editor;

		  // Set edit mode as false, so don't render settings and etc.
		  $is_edit_mode = $editor->is_edit_mode();
		  $editor->set_edit_mode( false );

		  // Print manually and don't use `the_content()`.
		  $content = \Elementor\Plugin::instance()->frontend->get_builder_content( $post->ID, true );

		  // Restore edit mode state.
		  \Elementor\Plugin::instance()->editor->set_edit_mode( $is_edit_mode );

		  if ( empty( $content ) ) {
			\Elementor\Plugin::instance()->frontend->remove_content_filter();
			$content = apply_filters('the_content', $post->post_content);
			\Elementor\Plugin::instance()->frontend->add_content_filter();
		  }
		}

		echo $content; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	private function get_template_placeholder(){
		$content = "<b>This is the Post Content Widget.</b> It is a dynamic widget that displays the content of each post/page. IMPORTANT! Please do not delete this widget.";

		return $content;
	}
}

\Elementor\Plugin::instance()->widgets_manager->register(new Post_Content());
