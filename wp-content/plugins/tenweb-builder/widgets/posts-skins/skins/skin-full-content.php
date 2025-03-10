<?php
namespace Tenweb_Builder\Widgets\Posts_Skins\Skins;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Skin_Full_Content extends Skin_Classic {
	use Skin_Content_Base;

	public function get_id() {
		return 'full_content';
	}
}
