<?php
namespace Tenweb_Builder\ElementorPro\Modules\DynamicTags\Tags;

use Tenweb_Builder\ElementorPro\Modules\DynamicTags\Tags\Base\Tag;
use Tenweb_Builder\ElementorPro\Modules\DynamicTags\Module;
use Tenweb_Builder\ElementorPro\Modules\LoopBuilder\Providers\Taxonomy_Loop_Provider;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Archive_Description extends Tag {

	public function get_name() {
		return 'archive-description';
	}

	public function get_title() {
		return esc_html__( 'Archive Description', 'elementor-pro' );
	}

	public function get_group() {
		return Module::ARCHIVE_GROUP;
	}

	public function get_categories() {
		return [ Module::TEXT_CATEGORY ];
	}

	public function render() {
		if ( Taxonomy_Loop_Provider::is_loop_taxonomy() ) {
			$this->render_loop_taxonomy();
			return;
		}

		$this->render_post();
	}

	private function render_post() {
		echo wp_kses_post( get_the_archive_description() );
	}

	private function render_loop_taxonomy() {
		$this->render_taxonomy_content_by_key( 'description' );
	}
}
