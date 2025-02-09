<?php
namespace Tenweb_Builder\ElementorPro\Modules\Forms\Submissions\Data\Responses;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Query_Failed_Response  extends \WP_Error {
	public function __construct( $query_error_message, $message = null ) {
		if ( ! $message ) {
			$message = __( 'Something went wrong.', 'elementor-pro' );
		}

		$this->log_error( $query_error_message );

		parent::__construct(
			'rest_internal_error',
			$message,
			[ 'status' => 500 ]
		);
	}

	private function log_error( $query_error_message ) {
		\Elementor\Plugin::instance()->logger->error( $query_error_message );
	}
}
