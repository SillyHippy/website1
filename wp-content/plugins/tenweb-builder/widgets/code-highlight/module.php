<?php
namespace Tenweb_Builder\Widgets\CodeHighlight;

use Elementor\Core\Base\Module;
use Tenweb_Builder\Builder;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class CH_Module extends Module {

	protected static $instance = NULL;

	public function get_name() {
		return Builder::$prefix . '_code-highlight';
	}

	/**
	 * @return CH_Module
	 */
	public static function get_instance() {
		if ( self::$instance === NULL ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function register_frontend_scripts() {
        $base_url = 'https://cdnjs.cloudflare.com/ajax/libs/prism/1.23.0';
        wp_register_script( 'prismjs_core', $base_url . '/components/prism-core.min.js', [], '1.23.0', true );
        wp_register_script( 'prismjs_loader', $base_url . '/plugins/autoloader/prism-autoloader.min.js', [ 'prismjs_core' ], '1.23.0', true );
        wp_register_script( 'prismjs_normalize', $base_url . '/plugins/normalize-whitespace/prism-normalize-whitespace.min.js', [ 'prismjs_core' ], '1.23.0', true );
        wp_register_script( 'prismjs_line_numbers', $base_url . '/plugins/line-numbers/prism-line-numbers.min.js', [ 'prismjs_normalize' ], '1.23.0', true );
        wp_register_script( 'prismjs_line_highlight', $base_url . '/plugins/line-highlight/prism-line-highlight.min.js', [ 'prismjs_normalize' ], '1.23.0', true );
        wp_register_script( 'prismjs_toolbar', $base_url . '/plugins/toolbar/prism-toolbar.min.js', [ 'prismjs_normalize' ], '1.23.0', true );
        wp_register_script( 'prismjs_copy_to_clipboard', $base_url . '/plugins/copy-to-clipboard/prism-copy-to-clipboard.min.js', [ 'prismjs_toolbar' ], '1.23.0', true );
	}

	public function __construct() {
		$this->get_actions();
	}

	/**
	 * Get actions.
	 */
	public function get_actions() {
		add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );
        add_action( 'elementor/frontend/before_register_scripts', [ $this, 'register_frontend_scripts' ] );
	}

	/**
	 * Register widgets.
	 */
	public function register_widgets() {
		$file = TWBB_DIR . '/widgets/code-highlight/widgets/controller.php';
		if ( is_file($file) ) {
			require_once $file; //phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingVariable
		}
	}
}
\Tenweb_Builder\Widgets\CodeHighlight\CH_Module::get_instance();
