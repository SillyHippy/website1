<?php

namespace Tenweb_Builder\ElementorPro\Modules\Forms\Registrars;

use Tenweb_Builder\ElementorPro\Core\Utils\Registrar;
use Tenweb_Builder\ElementorPro\Modules\Forms\Fields;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Basic form fields registration manager.
 */
class Form_Fields_Registrar extends Registrar {

	/**
	 * Form_Fields_Registrar constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct();

		$this->init();
	}

	/**
	 * Initialize the default fields.
	 *
	 * @return void
	 */
	public function init() {
		$this->register( new Fields\Time() );
		$this->register( new Fields\Date() );
		$this->register( new Fields\Tel() );
		$this->register( new Fields\Number() );
		$this->register( new Fields\Acceptance() );
		$this->register( new Fields\Upload() );
		$this->register( new Fields\Step() );
		$this->register( new Fields\Custom_Button() );

		/**
		 * Elementor Pro form fields registration.
		 * Passes the Fields_Registrar as a parameter in order to register new fields using `$registrar->register()`.
		 *
		 * @since 3.5.0
		 */
		do_action( 'elementor_tenweb/forms/fields/register', $this );
	}
}
