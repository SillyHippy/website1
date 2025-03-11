<?php
namespace Tenweb_Builder\Widgets\Woocommerce\Products\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Current_Query_Renderer extends \WC_Shortcode_Products {

	private $settings = [];
	const DEFAULT_COLUMNS_AND_ROWS = 4;

	public function __construct( $settings = [], $type = 'products', $skin = '' ) {
		$this->settings = $settings;
		$this->type = $type;
		$this->attributes = $this->parse_attributes( [
			'columns' => ! empty( $settings[$skin . 'columns'] ) ? $settings[$skin . 'columns'] : self::DEFAULT_COLUMNS_AND_ROWS,
			'rows' => ! empty( $settings[$skin . 'rows'] ) ? $settings[$skin . 'rows'] : self::DEFAULT_COLUMNS_AND_ROWS,
			'paginate' => $settings['paginate'],
			'cache' => false,
		] );
		$this->query_args = $this->parse_query_args();
	}

	protected function get_query_results() {
		return parent::get_query_results();
	}

	protected function parse_query_args() {
		$settings = &$this->settings;

		if ( ! is_page( wc_get_page_id( 'shop' ) ) ) {
			$query_args = $GLOBALS['wp_query']->query_vars;
		}

        if( isset($query_args['post_type']) && $query_args['post_type'] === 'product' ) {
            add_filter('woocommerce_pagination_args', function ($query_args) {
                $query_args['base'] = add_query_arg('product-page', '%#%'); // Use product-page for the query string
                $query_args['format'] = '?product-page=%#%'; // Define the pagination format

                return $query_args;
            });
        }

		add_action( "woocommerce_shortcode_before_{$this->type}_loop", function () {
			wc_set_loop_prop( 'is_shortcode', false );
		} );

        $page = get_query_var( 'paged', 1 );
        //phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $page = absint( empty( $_GET['product-page'] ) ? $page : sanitize_text_field( $_GET['product-page']) );

        $rows = $this->attributes['rows'];
        $columns = $this->attributes['columns'];
        $query_args['posts_per_page'] = $settings['posts_per_page'] ?? intval( $columns * $rows );

        if ( 1 < $page ) {
            $query_args['paged'] = $page;
        }
        $query_args['orderby'] = 'date ID';
        $query_args['order'] = 'DESC';

		if ( 'yes' === $settings['paginate'] ) {

			if ( 'yes' !== $settings['allow_order'] ) {
				remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
			}

			if ( 'yes' !== $settings['show_result_count'] ) {
				remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
			}
		}

		// Always query only IDs.
		$query_args['fields'] = 'ids';

		return apply_filters( 'twb_shortcode_products_query', $query_args, $this->attributes, $this->type );
	}

    public function get_content() {
        $result = $this->get_query_results();

        if ( empty( $result->total ) ) {
            return '';
        }

        return parent::get_content();
    }

}
