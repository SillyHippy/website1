<?php

namespace Tenweb_Builder\Modules;

class Utils
{
    public static function getWoocommerceData() {
        // Check if WooCommerce is active
        if (!is_plugin_active('woocommerce/woocommerce.php')) {
            return [
                'woocommerce_active' => false,
                'shop_page_id' => null,
                'products_count' => 0
            ];
        }

        // Get the shop page ID
        $shop_page_id = wc_get_page_id('shop');

        // Get the count of published products
        $products_count = wp_count_posts('product')->publish;

        return [
            'woocommerce_active' => true,
            'shop_page_id' => $shop_page_id,
            'products_count' => $products_count
        ];
    }

    public static function getBlogData() {
        // Get the count of published posts
        $posts_count = wp_count_posts()->publish;

        return [
            'posts_count' => $posts_count,
        ];
    }

    public static function handleArchiveNoContentRender($args) {
        ?>
        <div class="elementor-content-not-found">
        <p class="empty-content-description mobile-desc twbb_no_content_text"> <?php echo wp_kses_post( __($args['mobile_desc'], 'tenweb-builder' )); ?></p>
        <div class="elementor-content-not-found-container">
            <?php
            for ($i=1; $i <= $args['el_count']; $i++) {
                echo '<div class="empty-content-default-view ' . esc_attr('empty-default-' . $i)  . '"></div>';
            }
            ?>
        </div>
        <p class="empty-content-description desktop-desc twbb_no_content_text"> <?php echo wp_kses_post( __($args['desktop_desc'], 'tenweb-builder' )); ?></p>
        </div><?php
    }

    public static function handleArchiveNoContentPreviewRender($args) {
        ?><div class="elementor-posts-not-found">
        <div class="twbb_no_content_text twbb_no_content-title"><?php esc_html_e( $args['title'], 'tenweb-builder' ); ?></div>
        <div class="twbb_no_content_text twbb_no_content-desc"><?php esc_html_e( $args['desc'], 'tenweb-builder' ); ?></div>
        </div><?php
    }
}