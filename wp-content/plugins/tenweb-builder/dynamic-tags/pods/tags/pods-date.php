<?php
namespace Tenweb_Builder\DynamicTags\Pods\Tags;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Pods_Date extends Pods_Base {

	public function get_name() {
		return 'pods-date';
	}

	public function get_title() {
		return __( 'Pods', 'tenweb-builder' ) . ' ' . __( 'Date Field', 'tenweb-builder' );
	}

	public function render() {
		$field_data = $this->get_field();
		$field = $field_data['field'];
		$value = empty( $field_data['value'] ) ? '' : $field_data['value'];
		//phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
		if ( $field && ! empty( $field['type'] ) && in_array( $field['type'], [ 'date', 'datetime' ] ) ) {

			$format = $this->get_settings( 'format' );

			$timestamp = strtotime( $value );

			if ( 'human' === $format ) {
				$value = human_time_diff( $timestamp );
			} else {
				switch ( $format ) {
					case 'default':
						$date_format = get_option( 'date_format' );
						break;
					case 'custom':
						$date_format = $this->get_settings( 'custom_format' );
						break;
					default:
						$date_format = $format;
						break;
				}

				$value = date( $date_format, $timestamp );//phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
			}
		}
		echo wp_kses_post( $value );
	}

	public function get_panel_template_setting_key() {
		return 'key';
	}

	protected function register_controls() {
		parent::register_controls();

		$this->add_control(
			'format',
			[
				'label' => __( 'Format', 'tenweb-builder' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'default' => __( 'Default', 'tenweb-builder' ),
					'F j, Y' => date( 'F j, Y' ),//phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
					'Y-m-d' => date( 'Y-m-d' ),//phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
					'm/d/Y' => date( 'm/d/Y' ),//phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
					'd/m/Y' => date( 'd/m/Y' ),//phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
					'human' => __( 'Human Readable', 'tenweb-builder' ),
					'custom' => __( 'Custom', 'tenweb-builder' ),
				],
				'default' => 'default',
			]
		);

		$this->add_control(
			'custom_format',
			[
				'label' => __( 'Custom Format', 'tenweb-builder' ),
				'default' => '',
				'description' => sprintf( '<a href="https://codex.wordpress.org/Formatting_Date_and_Time" target="_blank">%s</a>', __( 'Documentation on date and time formatting', 'tenweb-builder' ) ),
				'condition' => [
					'format' => 'custom',
				],
			]
		);
	}

	protected function get_supported_fields() {
		return [
			'datetime',
			'date',
		];
	}
}
