<?php

namespace Tenweb_Builder\Modules;

use Tenweb_Builder\AdminCondition;
use \Tenweb_Builder\Modules\Helper;
use Tenweb_Builder\Import;
use Tenweb_Builder\PopupTemplates;
use Tenweb_Builder\Templates;

class ActionsHooksWp {
    public function __construct() {
        $this->registerHooks();
    }

    private function registerHooks() {
        if(!TENWEB_WHITE_LABEL) {
            add_action("admin_menu", array($this,'adminTenwebRelatedSubmenus'));
        }
        //phpcs:ignore WordPress.Security.NonceVerification.Recommended
        if(isset($_GET["tabs_group"]) && $_GET["tabs_group"] === "twbb_templates") {
            add_action("admin_menu", array($this,"adminMenuReorder"), 900);
        }
        add_action("views_edit-elementor_library", array($this, "adminPrintTabs"), 25);

        add_action( 'wp_ajax_twbb_widgets', array( $this, 'widgetsAjax' ) );
        add_action( 'wp_ajax_nopriv_twbb_widgets', array( $this, 'widgetsAjax' ) );

        add_action( 'admin_bar_menu', array( $this, 'addToolbarItems' ), 500 );
        add_action( 'wp_ajax_popup_template_ajax', array( $this, 'popupTemplateAjaxAction' ) );
        add_action( 'wp_ajax_remove_template_ajax', array( $this, 'removeTemplateAjaxAction' ) );
        add_action( 'wp_ajax_trigger_conditions', array( $this, 'triggerConditionsAdminAjax' ) );
        add_action( 'wp_ajax_track_publish_ajax', array( $this, 'trackPublishAjaxAction' ) );
        /* if menu item is created by AI or imported, remove class after editing it */
        add_action( 'wp_update_nav_menu_item', array( $this, 'updateNavMenu' ), 10, 2 );
    }

    /**
     * Adding submenu Tenweb Templates to elementor menu.
     */
    public function adminTenwebRelatedSubmenus(){
        add_submenu_page(
            'edit.php?post_type=elementor_library',
            '',
            __('10Web Templates', 'tenweb-builder'),
            'edit_posts',
            'edit.php?post_type=elementor_library&tabs_group=twbb_templates&elementor_library_type=twbb_header'
        );
    }

    /**
     * Remove Add New item from admin menu.
     * Fired by `admin_menu` action.
     *
     * @since  2.4.0
     * @access public
     */
    public function adminMenuReorder() {
        global $submenu;
        $library_submenu = &$submenu['edit.php?post_type=elementor_library'];
        // Remove 'All Templates' menu.
        unset($library_submenu[5]);
        // If current use can 'Add New' - move the menu to end, and add the '#add_new' anchor.
        if(isset($library_submenu[10][2])) {
            $library_submenu[700] = $library_submenu[10];
            unset($library_submenu[10]);
            $library_submenu[700][2] = admin_url('edit.php?post_type=elementor_library' . '#add_new');
        }
        // Move the 'Categories' menu to end.
        if(isset($library_submenu[15])) {
            $library_submenu[800] = $library_submenu[15];
            unset($library_submenu[15]);
        }
        if(is_current_screen()) {
            $library_title = get_library_title();
            foreach($library_submenu as &$item) {
                if($library_title === $item[0]) {
                    if(!isset($item[4])) {
                        $item[4] = '';
                    }
                    $item[4] .= ' current';
                } else {
                    if(isset($item[4])) {
                        $item[4] = '';
                    }
                }
            }
        }
    }

    public function adminPrintTabs( $views ) {
        $current_type = '';
        $active_class = ' nav-tab-active';
        $current_tabs_group = get_current_tab_group();
        //phpcs:ignore WordPress.Security.NonceVerification.Recommended
        if(!empty($_REQUEST['elementor_library_type'])) {
	          //phpcs:ignore WordPress.Security.NonceVerification.Recommended
            $current_type = sanitize_text_field( $_REQUEST['elementor_library_type'] );
            $active_class = '';
        }
        $url_args = [
            'post_type' => 'elementor_library',
	          //phpcs:ignore WordPress.Security.NonceVerification.Recommended
            'tabs_group' => (isset($_GET['tabs_group']) && $_GET['tabs_group'] === 'twbb_templates') ? 'twbb_templates' : $current_tabs_group,
        ];
        $baseurl = add_query_arg($url_args, admin_url('edit.php'));
        $filter = [
            'admin_tab_group' => $current_tabs_group,
        ];
        $operator = 'and';
        if(empty($current_tabs_group)) {
            // Don't include 'not-supported' or other templates that don't set their `admin_tab_group`.
            $operator = 'NOT';
        }
        /* hide elementor tabs */
	      //phpcs:ignore WordPress.Security.NonceVerification.Recommended
        if(isset($_GET["tabs_group"]) && $_GET['tabs_group'] === 'twbb_templates') {
            ?>
            <style>
                #elementor-template-library-tabs-wrapper:not(.twbb-builder), .subsubsub, .search-box, .alignleft.actions:not(.bulkactions) {
                    display: none;
                }
            </style>
        <?php }
        //phpcs:ignore WordPress.Security.NonceVerification.Recommended
        elseif(isset($_GET["post_type"]) && $_GET["post_type"] === 'elementor_library') { ?>
            <style>
                #elementor-template-library-tabs-wrapper {
                    display: none;
                }
            </style>

            <script>
                jQuery(document).ready(function () {
                    jQuery('#elementor-template-library-tabs-wrapper .nav-tab').each(function () {
                        var href = jQuery(this).attr('href')
                        <?php if ( !TENWEB_WHITE_LABEL ) { ?>
                        var twbb = href.search('twbb_')
                        if (twbb != -1) {
                            jQuery(this).css('display', 'none')
                        }
                        <?php } ?>
                    })
                    jQuery('#elementor-template-library-tabs-wrapper').not('.twbb-builder').show()
                })
            </script>
            <?php
        }
        $doc_types = \Elementor\Plugin::instance()->documents->get_document_types($filter, $operator);
        if(1 >= count($doc_types)) {
            return '';
        }
        ?>
        <div id="elementor-template-library-tabs-wrapper" class="nav-tab-wrapper twbb-builder">
            <?php
            foreach($doc_types as $type => $class_name) :
                $active_class = '';
                if($current_type === $type) {
                    $active_class = ' nav-tab-active';
                }
                $type_url = add_query_arg('elementor_library_type', $type, $baseurl);
                $type_label = get_template_label_by_type($type);

                $template_types = array(
                    "twbb_header",
                    "twbb_single",
                    "twbb_archive",
                    "twbb_footer",
                    "twbb_slide",
                );

                if (function_exists('WC') ) {
                    $template_types[] = "twbb_single_product";
                    $template_types[] = "twbb_archive_products";
                }
	              //phpcs:ignore WordPress.Security.NonceVerification.Recommended
                if(isset($_GET["tabs_group"]) && $_GET["tabs_group"] === "twbb_templates" && !in_array($type, $template_types, true) ) {
                    continue;
                }
                echo "<a class='nav-tab" . esc_attr( $active_class ) . "' href='" . esc_url( $type_url ) . "'>" . esc_html( $type_label ) . "</a>";
            endforeach;
            ?>
        </div>
        <?php
        return $views;
    }

    public function widgetsAjax() {
        //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        if ( ! check_ajax_referer( 'twbb', 'nonce' ) || ! isset( $_REQUEST[ 'widget_name' ] ) ) {
            wp_send_json_error();
        }
        $widget_name = sanitize_text_field( $_REQUEST[ 'widget_name' ] );
        $widget = twbb_get_widgets( $widget_name );
        if ( ! isset( $widget[ 'ajax' ] ) || $widget[ 'ajax' ] !== TRUE ) {
            wp_send_json_error();
        }
        $file = TWBB_DIR . '/widgets/' . $widget_name . '/' . $widget_name . '.php';
        if ( is_file( $file ) ) {
            require_once $file;//phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingVariable
            $class_name = "\Tenweb_Builder\\" . ucfirst( $widget_name );
            $method     = 'twbb_ajax';
            if ( method_exists( $class_name, $method ) ) {
                $class_name::$method();
            }
        }
    }

    public function addToolbarItems( \WP_Admin_Bar $admin_bar ) {

        if ( ! is_admin() ) {
            $edit_url = \Tenweb_Builder\Builder::get_edit_url();
            if ( ! TENWEB_WHITE_LABEL ) {
                $admin_bar->remove_menu( 'elementor_edit_page' );

                if ( is_singular() ) {
                    $page_id = get_the_ID();
                    $document = \Elementor\Plugin::instance()->documents->get_doc_for_frontend( $page_id );
                    if ( $document && $document->is_editable_by_current_user() ) {
                        $admin_bar->add_menu( array(
                            'id'    => 'twbb_builder',
                            'class' => 'admin_bar-twbb_builder',
                            'title' => __( 'Edit with 10Web Builder', 'tenweb-builder' ),
                            'href'  => $edit_url,
                        ) );
                        if ( \Elementor\Plugin::$instance->documents->get( $page_id )->is_built_with_elementor() ) {
                            $admin_bar->remove_node( 'edit' );
                        }
                    }
                    if ( is_singular( array( 'product' ) ) ) {
                        $loaded_templates = Templates::get_instance()->get_loaded_templates();
                        if ( array_key_exists( 'twbb_single', $loaded_templates ) && ! empty( $loaded_templates[ 'twbb_single' ] ) ) {
                            $template_id = $loaded_templates[ 'twbb_single' ];
                            $document    = \Elementor\Plugin::instance()->documents->get_doc_for_frontend( $template_id );
                            if ( $document && $document->is_editable_by_current_user() ) {
                                $admin_bar->remove_node( 'edit' );
                                $admin_bar->add_menu( array(
                                    'id'    => 'twbb_builder',
                                    'class' => 'admin_bar-twbb_builder',
                                    'title' => __( 'Edit Product template with 10Web Builder', 'tenweb-builder' ),
                                    'href'  => $edit_url,
                                    'meta'  => array( 'target' => '_blank' ),
                                ) );
                            }
                        }
                    }
                } else {
                    $page_id = get_the_ID();
                    $loaded_templates = Templates::get_instance()->get_loaded_templates();
                    if ( array_key_exists( 'twbb_archive', $loaded_templates ) && ! empty( $loaded_templates[ 'twbb_archive' ] ) ) {
                        $archive_id = $loaded_templates[ 'twbb_archive' ];
                        $document   = \Elementor\Plugin::instance()->documents->get_doc_for_frontend( $archive_id );
                        if ( $document && $document->is_editable_by_current_user() && !empty($page_id) ) {
                            if ( \Elementor\Plugin::$instance->documents->get( $page_id )->is_built_with_elementor() ) {
                                $admin_bar->remove_node( 'edit' );
                            }

                            $admin_bar->add_menu( array(
                                'id'    => 'twbb_builder',
                                'class' => 'admin_bar-twbb_builder',
                                'title' => __( 'Edit Archive template with 10Web Builder', 'tenweb-builder' ),
                                'href'  => $edit_url,
                                'meta'  => array( 'target' => '_blank' ),
                            ) );
                        }
                    }
                }
            }
        }

    }

    public function popupTemplateAjaxAction() {
        include_once TWBB_DIR . '/templates/popupTemplates.php';
        $task = \Tenweb_Builder\Modules\Helper::get( 'task' );
        switch ( $task ) {
            case 'save_lacaly':
                PopupTemplates::get_instance()->twbb_dublicate_teplate_post();
                break;
            case 'save_popup':
                if ( \Tenweb_Builder\Modules\Helper::get( 'header_template' ) === '0' || \Tenweb_Builder\Modules\Helper::get( 'footer_template' ) === '0' || \Tenweb_Builder\Modules\Helper::get( 'single_template' ) === '0' || \Tenweb_Builder\Modules\Helper::get( 'archive_template' ) === '0' ) {
                    $param = "exclude";
                } else {
                    $param = "include";
                }
                PopupTemplates::get_instance()->twbb_save_templates( $param );
        }
    }

    public function removeTemplateAjaxAction() {
        //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        if ( ! isset( $_GET[ 'twbb_nonce' ] ) || ( isset( $_GET[ 'twbb_nonce' ] ) && ! wp_verify_nonce( $_GET[ 'twbb_nonce' ], 'twbb_remove_template_ajax' ) ) ) {
            $wp_error = array();
            $wp_error[ "message" ] = __( "You have no permission for the action", 'tenweb-builder' );
            $wp_error[ "status" ]  = "error";
            echo json_encode( $wp_error );//phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode
            die();
        }
        include_once TWBB_DIR . '/templates/import/import.php';
        $args = array(
            'posts'       => 'delete',
            'attachments' => '1',
            'terms'       => '1',
            'menus'       => '1',
            'options'     => '1',
        );
        Import::delete_last_imported_site_data( $args , 'twbb_imported_site_data_generated');
    }

    public function triggerConditionsAdminAjax() {
        $post_id = \Tenweb_Builder\Modules\Helper::get( 'post_id' );
        include_once TWBB_DIR . '/templates/condition/admin-condition.php';
        $admincondition = new AdminCondition();
        $admincondition->admin_condition_popup( $post_id );
    }

    public function trackPublishAjaxAction() {
        update_option('twbb_track_publish_button', 1);
    }

    public function updateNavMenu( $menu_id, $menu_item_id ) {
        $saved_menu = get_option('imported_nav_menu_' . $menu_id);
        $updated_menu = wp_get_nav_menu_items($menu_id);
        foreach ( $updated_menu as $key => $new_item ) {
            if ( $new_item->ID == $menu_item_id && $saved_menu ) {//phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
                foreach ( $saved_menu as $old_item ) {
                    if ( $old_item->ID == $menu_item_id ) {//phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
                        if ( $old_item->post_title !== $new_item->post_title ||
                            $old_item->title !== $new_item->title ||
                            $old_item->url !== $new_item->url ) {
                            $item_classes = get_post_meta($menu_item_id,'_menu_item_classes')[0];
                            //phpcs:ignore WordPress.CodeAnalysis.AssignmentInCondition.Found
                            if (($key = array_search('ai-recreated-menu-item', $item_classes, true)) !== false) {
                                unset($item_classes[$key]);
                            }
                            update_post_meta($menu_item_id,'_menu_item_classes',$item_classes);
                        }
                    }
                }
            }
        }
    }

}
