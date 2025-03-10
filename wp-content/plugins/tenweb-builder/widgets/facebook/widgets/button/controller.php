<?php
namespace Tenweb_Builder\Widgets\Facebook\Widgets\Button;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Tenweb_Builder\Widgets\Facebook\Classes\Facebook_SDK_Manager;
use Tenweb_Builder\Widgets\Facebook\FB_Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Facebook_Button extends Widget_Base {

	public function get_name() {
		return 'twbb_facebook-button';
	}

	public function get_title() {
		return __( 'Facebook Button', 'tenweb-builder' );
	}

	public function get_icon() {
		return 'twbb-facebook-button twbb-widget-icon';
	}

	public function get_keywords() {
		return [ 'facebook', 'social', 'embed', 'button', 'like', 'share', 'recommend', 'follow' ];
	}

	public function get_categories() {
		return [ 'tenweb-widgets' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Button', 'tenweb-builder' ),
			]
		);

		Facebook_SDK_Manager::add_app_id_control( $this );

		$this->add_control(
			'type',
			[
				'label' => __( 'Type', 'tenweb-builder' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'like',
				'options' => [
					'like' => __( 'Like', 'tenweb-builder' ),
					'recommend' => __( 'Recommend', 'tenweb-builder' ),
				],
			]
		);

		$this->add_control(
			'layout',
			[
				'label' => __( 'Layout', 'tenweb-builder' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'standard',
				'options' => [
					'standard' => __( 'Standard', 'tenweb-builder' ),
					'button' => __( 'Button', 'tenweb-builder' ),
					'button_count' => __( 'Button Count', 'tenweb-builder' ),
					'box_count' => __( 'Box Count', 'tenweb-builder' ),
				],
			]
		);

		$this->add_control(
			'size',
			[
				'label' => __( 'Size', 'tenweb-builder' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'small',
				'options' => [
					'small' => __( 'Small', 'tenweb-builder' ),
					'large' => __( 'Large', 'tenweb-builder' ),
				],
			]
		);

		$this->add_control(
			'color_scheme',
			[
				'label' => __( 'Color Scheme', 'tenweb-builder' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'light',
				'options' => [
					'light' => __( 'Light', 'tenweb-builder' ),
					'dark' => __( 'Dark', 'tenweb-builder' ),
				],
			]
		);

		$this->add_control(
			'show_share',
			[
				'label' => __( 'Share Button', 'tenweb-builder' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'condition' => [
					'type!' => 'follow',
				],
			]
		);

		$this->add_control(
			'show_faces',
			[
				'label' => __( 'Faces', 'tenweb-builder' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
			]
		);

		$this->add_control(
			'url_type',
			[
				'label' => __( 'Target URL', 'tenweb-builder' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					FB_Module::URL_TYPE_CURRENT_PAGE => __( 'Current Page', 'tenweb-builder' ),
					FB_Module::URL_TYPE_CUSTOM => __( 'Custom', 'tenweb-builder' ),
				],
				'default' => FB_Module::URL_TYPE_CURRENT_PAGE,
				'separator' => 'before',
				'condition' => [
					'type' => [ 'like', 'recommend' ],
				],
			]
		);

		$this->add_control(
			'url_format',
			[
				'label' => __( 'URL Format', 'tenweb-builder' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					FB_Module::URL_FORMAT_PLAIN => __( 'Plain Permalink', 'tenweb-builder' ),
					FB_Module::URL_FORMAT_PRETTY => __( 'Pretty Permalink', 'tenweb-builder' ),
				],
				'default' => FB_Module::URL_FORMAT_PLAIN,
				'condition' => [
					'url_type' => FB_Module::URL_TYPE_CURRENT_PAGE,
				],
			]
		);

		$this->add_control(
			'url',
			[
				'label' => __( 'Link', 'tenweb-builder' ),
				'placeholder' => __( 'https://your-link.com', 'tenweb-builder' ),
				'label_block' => true,
				'condition' => [
					'type' => [ 'like', 'recommend' ],
					'url_type' => FB_Module::URL_TYPE_CUSTOM,
				],
			]
		);

		$this->end_controls_section();
	}

	public function render() {
		$settings = $this->get_settings();

		// Validate URL
		switch ( $settings['type'] ) {
			case 'like':
			case 'recommend':
				if ( FB_Module::URL_TYPE_CUSTOM === $settings['url_type'] && ! filter_var( $settings['url'], FILTER_VALIDATE_URL ) ) {
					if ( \Elementor\Plugin::instance()->editor->is_edit_mode() ) {
						echo $this->get_title() . ': ' . esc_html__( 'Please enter a valid URL', 'tenweb-builder' ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}

					return;
				}
				break;
		}

		$attributes = [
			'data-layout' => $settings['layout'],
			'data-colorscheme' => $settings['color_scheme'],
			'data-size' => $settings['size'],
			'data-show-faces' => $settings['show_faces'] ? 'true' : 'false',
		];

		switch ( $settings['type'] ) {
			case 'like':
			case 'recommend':
				if ( FB_Module::URL_TYPE_CURRENT_PAGE === $settings['url_type'] ) {
					$permalink = Facebook_SDK_Manager::get_permalink( $settings );
				} else {
					$permalink = esc_url( $settings['url'] );
				}

				$attributes['class'] = 'elementor-facebook-widget fb-like';
				$attributes['data-href'] = $permalink;
				$attributes['data-share'] = $settings['show_share'] ? 'true' : 'false';
				$attributes['data-action'] = $settings['type'];
				break;
		}

		$this->add_render_attribute( 'embed_div', $attributes ); ?>

		<div <?php $this->print_render_attribute_string( 'embed_div' );?> ></div><?php
	}

	public function render_plain_content() {}

}

\Elementor\Plugin::instance()->widgets_manager->register( new Facebook_Button() );
