<?php

namespace Tenweb_Builder\Widgets\Woocommerce\Breadcrumb;

class Twbb_Breadcrumb extends \WC_Breadcrumb {
    /**
     * Product tag trail.
     */
    protected function add_crumbs_product_tag() {
        $current_term = $GLOBALS['wp_query']->get_queried_object();

        $this->prepend_shop_page();

        /* translators: %s: product tag */
        $this->add_crumb( sprintf( __( '%s', 'woocommerce' ), $current_term->name ), get_term_link( $current_term, 'product_tag' ) );
    }

    public function add_crumbs_shop_home()
    {
        if( !is_shop() ) {
            parent::add_crumbs_shop();
        }
    }
}