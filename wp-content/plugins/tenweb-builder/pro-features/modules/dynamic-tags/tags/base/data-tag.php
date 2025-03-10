<?php

namespace Tenweb_Builder\ElementorPro\Modules\Tags\Base;

use Tenweb_Builder\ElementorPro\Modules\Data_Tag as Base_Data_Tag;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

abstract class Data_Tag extends Base_Data_Tag {

	use Tag_Trait;
}
