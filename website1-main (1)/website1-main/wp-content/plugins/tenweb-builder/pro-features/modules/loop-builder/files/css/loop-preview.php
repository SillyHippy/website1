<?php
namespace Tenweb_Builder\ElementorPro\Modules\LoopBuilder\Files\Css;

use Elementor\Core\Files\CSS\Post_Preview;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Loop_Preview extends Post_Preview {

	use Loop_Css_Trait;

	/**
	 * Get CSS file name.
	 *
	 * Retrieve the CSS file name.
	 *
	 * @access public
	 *
	 * @return string CSS file name.
	 */
	public function get_name() {
		return 'loop';
	}
}
