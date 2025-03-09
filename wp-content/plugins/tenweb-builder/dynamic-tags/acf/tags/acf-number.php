<?php
namespace Tenweb_Builder\DynamicTags\ACF\Tags;

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use Tenweb_Builder\DynamicTags\ACF\Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class ACF_Number extends Tag {

	public function get_name() {
		return 'acf-number';
	}

	public function get_title() {
		return __( 'ACF', 'tenweb-builder' ) . ' ' . __( 'Number', 'tenweb-builder' ) . ' ' . __( 'Field', 'tenweb-builder' );
	}

	public function get_group() {
		return Module::ACF_GROUP;
	}

	public function get_categories() {
	  if ( property_exists('Module', 'NUMBER_CATEGORY') ) {
      return [
        Module::NUMBER_CATEGORY,
        Module::POST_META_CATEGORY,
      ];
    }
	  else {
      return [
        Module::POST_META_CATEGORY,
      ];
    }
	}

	public function render() {
		$key = $this->get_settings( 'key' );
		if ( empty( $key ) ) {
			return;
		}

		list( $field_key, $meta_key ) = explode( ':', $key );

		if ( 'options' === $field_key ) {
			$field = get_field_object( $meta_key, $field_key );
		} else {
			$field = get_field_object( $field_key, get_queried_object() );
		}

		if ( $field && ! empty( $field['type'] ) ) {
			$value = $field['value'];
		} else {
			// Field settings has been deleted or not available.
			$value = get_field( $meta_key );
		}

		echo wp_kses_post( $value );
	}

	public function get_panel_template_setting_key() {
		return 'key';
	}

	protected function register_controls() {
		$this->add_control(
			'key',
			[
				'label' => __( 'Key', 'tenweb-builder' ),
				'type' => Controls_Manager::SELECT,
				'groups' => Module::get_control_options( $this->get_supported_fields() ),
			]
		);
	}

	protected function get_supported_fields() {
		return [
			'number',
		];
	}
}
