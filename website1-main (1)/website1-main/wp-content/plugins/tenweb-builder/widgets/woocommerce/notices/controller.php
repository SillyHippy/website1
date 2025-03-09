<?php
namespace Tenweb_Builder\Widgets\Woocommerce\Widgets;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class Notices extends Base_Widget {

    public function get_name() {
        return 'twbb_woocommerce-notices';
    }

    public function get_title() {
        return esc_html__( 'WooCommerce Notices', 'tenweb-builder' );
    }

    public function get_icon() {
        return 'twbb-woocommerce-notices twbb-widget-icon';
    }

    public function get_keywords() {
        return [ 'woocommerce', 'notices', 'notifications' ];
    }

    protected function register_controls() {

        $this->start_controls_section(
            'section',
            [
                'label' => esc_html__( 'WooCommerce Notices', 'tenweb-builder' ),
            ]
        );

        $this->add_control(
            'where_to_appear_notice',
            [
                'type' => Controls_Manager::RAW_HTML,
                'raw' => esc_html__( 'Drop this widget anywhere on the page or template where you want notices to appear.', 'tenweb-builder' ),
                'content_classes' => 'elementor-descriptor',
            ]
        );

        $this->add_control(
            'site_settings_notice',
            [
                'type' => Controls_Manager::RAW_HTML,
                'raw' => sprintf(
                /* translators: 1: Link opening tag, 2: Link closing tag. */
                    esc_html__( 'To change the design of your notices, go to your %1$sWooCommerce Settings%2$s', 'tenweb-builder' ),
                    '<a href="#" onclick="elementorTenweb.modules.woocommerce.openSiteSettingsTab( \'settings-woocommerce\', \'section_woocommerce_notices\' );">',
                    '</a>'
                ),
                'content_classes' => 'elementor-descriptor elementor-descriptor-subtle',
            ]
        );

        $this->add_control(
            'one_per_page_notice',
            [
                'type' => Controls_Manager::RAW_HTML,
                'raw' => sprintf(
                /* translators: 1: Bold text opening tag, 2: Bold text closing tag. */
                    esc_html__( '%1$sNote:%2$s You can only add the Notices widget once per page.', 'tenweb-builder' ),
                    '<strong>',
                    '</strong>'
                ),
                'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
            ]
        );

        $this->end_controls_section();
    }

    private function hide_woocommerce_notices() {
        ?>
        <style>
          .woocommerce-notices-wrapper,
          .woocommerce-message,
          .woocommerce-error,
          .woocommerce-info {
            display: none;
          }
        </style>
        <?php
    }

    protected function render() {
        if ( \Elementor\Plugin::instance()->editor->is_edit_mode() || \Elementor\Plugin::instance()->preview->is_preview_mode() ) {
            ?>
            <div class="woocommerce-info e-notices-demo-notice">
                <?php echo esc_html__( 'This is an example of a WooCommerce notice. (You won\'t see this while previewing your site.)', 'tenweb-builder' ); ?>
            </div>
            <?php
        } else {
            $this->hide_woocommerce_notices();
            ?>
            <div class="e-woocommerce-notices-wrapper e-woocommerce-notices-wrapper-loading">
                <?php woocommerce_output_all_notices(); ?>
            </div>
            <?php
        }
    }

    public function get_group_name() {
        return 'woocommerce';
    }
}

\Elementor\Plugin::instance()->widgets_manager->register( new Notices() );
