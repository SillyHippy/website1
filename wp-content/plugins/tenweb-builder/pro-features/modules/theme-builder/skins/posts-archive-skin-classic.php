<?php
namespace Tenweb_Builder\ElementorPro\Modules\ThemeBuilder\Skins;

use Tenweb_Builder\Widgets\Posts_Skins\Skins\Skin_Classic;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Posts_Archive_Skin_Classic extends Skin_Classic {
	use Posts_Archive_Skin_Base;

	protected function _register_controls_actions() {
		add_action( 'elementor/element/archive-posts/section_layout/before_section_end', [ $this, 'register_controls' ] );
		add_action( 'elementor/element/archive-posts/section_layout/after_section_end', [ $this, 'register_style_sections' ] );
	}

	public function get_id() {
		return 'archive_classic';
	}

	public function get_title() {
		return esc_html__( 'Classic', 'elementor-pro' );
	}

	public function get_container_class() {
		// Use parent class and parent css.
		return 'elementor-posts--skin-classic';
	}

	/* Remove `posts_per_page` control */
	protected function register_post_count_control() {}
}
