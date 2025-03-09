<?php
namespace Tenweb_Builder\Widgets\Posts_Skins;

require_once TWBB_DIR . '/widgets/posts-skins/traits/button-widget-trait.php';
require_once TWBB_DIR . '/widgets/posts-skins/traits/pagination-trait.php';
require_once TWBB_DIR . '/widgets/posts-skins/data/controller.php';

$skins = ['base', 'cards', 'classic', 'content-base','on_image', 'image_left'];
foreach ( $skins as $skin ) {
    $file = TWBB_DIR . '/widgets/posts-skins/skins/skin-' . $skin . '.php';
    if ( is_file($file) ) {
        require_once $file; //phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingVariable
    }
}

use Tenweb_Builder\ElementorPro\Base\Module_Base;
use Tenweb_Builder\Widgets\Posts_Skins\Traits\Pagination_Trait;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Module extends Module_Base {
    use Pagination_Trait;

    protected static $instance = NULL;

	public function get_name() {
		return 'tenweb-posts';
	}

	public function tenweb_get_widgets() {
		return [
			'posts-base',
			'posts',
/*			'Portfolio',*/
		];
	}

	/**
	 * Fix WP 5.5 pagination issue.
	 *
	 * Return true to mark that it's handled and avoid WP to set it as 404.
	 *
	 * @see https://github.com/elementor/elementor/issues/12126
	 * @see https://core.trac.wordpress.org/ticket/50976
	 *
	 * Based on the logic at \WP::handle_404.
	 *
	 * @param $handled - Default false.
	 * @param $wp_query
	 *
	 * @return bool
	 */
	public function allow_posts_widget_pagination( $handled, $wp_query ) {
		// Check it's not already handled and it's a single paged query.
		if ( $handled || empty( $wp_query->query_vars['page'] ) || ! is_singular() || empty( $wp_query->post ) ) {
			return $handled;
		}

		$document = \Elementor\Plugin::instance()->documents->get( $wp_query->post->ID );

		return $this->is_valid_pagination( $document->get_elements_data(), $wp_query->query_vars['page'] );
	}

	public function __construct() {
		parent::__construct();
        $this->get_actions();
		add_filter( 'pre_handle_404', [ $this, 'allow_posts_widget_pagination' ], 10, 2 );
	}

    /**
     * Get actions.
     */
    public function get_actions() {
        add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );
    }

    /**
     * Register widgets.
     */
    public function register_widgets($widgets_manager) {

        foreach ( $this->tenweb_get_widgets() as $widget ) {
            $file = TWBB_DIR . '/widgets/posts-skins/widgets/' . $widget . '.php';
            if ( is_file($file) ) {
                require_once $file; //phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingVariable
            }
        }

    }



    /**
     * @return Module
     */
    public static function get_instance() {
        if ( self::$instance === NULL ) {
            self::$instance = new self();
        }

        return self::$instance;
    }


}

\Tenweb_Builder\Widgets\Posts_Skins\Module::get_instance();
