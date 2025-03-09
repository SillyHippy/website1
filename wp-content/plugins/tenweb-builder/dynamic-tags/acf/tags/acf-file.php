<?php
namespace Tenweb_Builder\DynamicTags\ACF\Tags;

use Tenweb_Builder\DynamicTags\ACF\Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

require_once TWBB_DIR . '/dynamic-tags/acf/tags/acf-image.php';

class ACF_File extends ACF_Image {

	public function get_name() {
		return 'acf-file';
	}

	public function get_title() {
		return __( 'ACF', 'tenweb-builder' ) . ' ' . __( 'File Field', 'tenweb-builder' );
	}

	public function get_categories() {
		return [
			Module::MEDIA_CATEGORY,
		];
	}

	protected function get_supported_fields() {
		return [
			'file',
		];
	}
}
