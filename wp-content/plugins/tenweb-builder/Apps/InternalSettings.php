<?php

namespace Tenweb_Builder\Apps;

class InternalSettings extends BaseApp
{
    protected static $instance = null;

    public function __construct() {
        $this->register_hooks();
    }

    public static function getInstance() {
        if ( self::$instance === null ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function addMenuPages() {
        add_submenu_page(
            "",
            __('10Web Builder Internal', 'tenweb-builder'),
            __('10Web Builder Internal', 'tenweb-builder'),
            'manage_options',
            'twbb_internal_settings',
            [$this, 'runInternalSettingsPage']
        );
    }

    public function runInternalSettingsPage() {
        $this->internalSettingsView();
    }

    public function updateSectionsInUploads() {
        $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : '';
        if ( !wp_verify_nonce( $nonce, 'twbb_internal_nonce' ) ) {
            wp_send_json_error("invalid_nonce");
            wp_die();
        }
        //TODO remove when autoloading is implemented
        $api_response = \Tenweb_Builder\Modules\ai\TenWebApi::get_instance()->sectionSyncRequest();
        if ( json_decode($api_response['body'])->msg === 'Success' ) {
            wp_send_json_success($api_response);
            wp_die();
        }
    }

    public function updateSGPosts() {
        $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : '';
        if ( !wp_verify_nonce( $nonce, 'twbb_internal_nonce' ) ) {
            wp_send_json_error("invalid_nonce");
            wp_die();
        }
        \Tenweb_Builder\Modules\SectionGeneration\GenerateSectionsPostsByType::getInstance()->process(true);
    }

    public function enqueueAssets() {
        if( self::visibilityCheck() ) {
            $this->enqueueScripts();
        }
    }

    private static function visibilityCheck() {
        if( !isset($_GET['page']) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
            return false;
        }
        return sanitize_text_field($_GET['page']) === 'twbb_internal_settings'; //phpcs:ignore WordPress.Security.NonceVerification.Recommended
    }

    private function register_hooks() {
        add_action('wp_ajax_twbb_update_sections_in_uploads', [$this, 'updateSectionsInUploads']);
        add_action('wp_ajax_twbb_update_sg_posts', [$this, 'updateSGPosts']);
        add_action("admin_menu", [$this, 'addMenuPages']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAssets']);
    }

    private function internalSettingsView()
    {
        require_once TWBB_DIR . '/Apps/InternalSettings/templates/internal_settings_view.php';
    }

    private function enqueueScripts() {
        wp_enqueue_script(
            'twbb-internal-settings-script',
            TWBB_URL . '/Apps/InternalSettings/assets/script/internal-settings.js',
            array('jquery'),
            TWBB_VERSION,
            true
        );
        wp_localize_script(
            'twbb-internal-settings-script',
            'twbb_internal_admin',
            array(
                'nonce' => wp_create_nonce('twbb_internal_nonce')
            )
        );
    }

}