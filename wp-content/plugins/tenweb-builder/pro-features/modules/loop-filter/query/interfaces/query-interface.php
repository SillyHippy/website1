<?php

namespace Tenweb_Builder\ElementorPro\Modules\LoopFilter\Query\Interfaces;

use Tenweb_Builder\ElementorPro\Modules\LoopFilter\Query\Taxonomy_Manager;

interface Query_Interface {
	public function __construct( $filter_terms, Taxonomy_Manager $taxonomy_manager );
	public function get_query();
}
