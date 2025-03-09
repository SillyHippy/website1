<?php
namespace Tenweb_Builder\ElementorPro\Modules\DynamicTags\Tags;

use Tenweb_Builder\ElementorPro\Modules\DynamicTags\Tags\Base\Data_Tag;
use Tenweb_Builder\ElementorPro\Core\Utils;
use Tenweb_Builder\ElementorPro\Modules\DynamicTags\Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Archive_URL extends Data_Tag {

	public function get_name() {
		return 'archive-url';
	}

	public function get_group() {
		return Module::ARCHIVE_GROUP;
	}

	public function get_categories() {
		return [ Module::URL_CATEGORY ];
	}

	public function get_title() {
		return esc_html__( 'Archive URL', 'elementor-pro' );
	}

	public function get_panel_template() {
		return ' ({{ url }})';
	}

	public function get_value( array $options = [] ) {
		return Utils::get_the_archive_url();
	}
}

