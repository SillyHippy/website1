<?php

namespace Tenweb_Builder\Apps;

use Tenweb_Builder\Apps\CoPilot\AjaxActionControllers\WpPostController;

class CoPilot extends BaseApp
{
    protected static ?self $instance = null;

    public static function getInstance(): ?self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {
        parent::__construct();
        $this->addActions();
        $this->registerApiRoutes();
    }

    public static function checkVisibility(): bool
    {
        return get_option('elementor_experiment-co_pilot') === 'active';
    }

    private function addActions(): void
    {
        add_action('elementor/editor/after_enqueue_styles', [ $this, 'enqueueEditorScripts' ]);
        add_action('elementor/editor/footer', [$this, 'set_templates']);
    }

    /**
     * Include HTML templates
     */
    public function set_templates(): void
    {
        require_once TWBB_DIR . '/Apps/CoPilot/templates.php';
    }

    public function enqueueEditorScripts(): void
    {

        wp_enqueue_script('twbb-copilot-firebase-app', 'https://www.gstatic.com/firebasejs/10.12.4/firebase-app-compat.js');
        wp_enqueue_script('twbb-copilot-firebase-firestore', 'https://www.gstatic.com/firebasejs/10.12.4/firebase-firestore-compat.js');
        wp_enqueue_script('twbb-copilot-firebase-auth', 'https://www.gstatic.com/firebasejs/10.12.4/firebase-auth-compat.js');
        wp_enqueue_script('twbb-copilot-showdown', 'https://unpkg.com/showdown/dist/showdown.min.js');
        wp_enqueue_script('twbb-copilot-script-utils', TWBB_URL . '/Apps/CoPilot/assets/js/utils.js', ['jquery'], TWBB_VERSION);
        wp_enqueue_script('twbb-copilot-widget_settings', TWBB_URL . '/Apps/CoPilot/assets/js/widget_settings.js', ['jquery'], TWBB_VERSION);
        wp_enqueue_script('twbb-copilot-elementor_tree', TWBB_URL . '/Apps/CoPilot/assets/js/elementor_tree.js', ['jquery'], TWBB_VERSION);
        wp_enqueue_script('twbb-copilot-update_elementor_tree', TWBB_URL . '/Apps/CoPilot/assets/js/update_elementor_tree.js', ['jquery'], TWBB_VERSION);
        wp_enqueue_script('twbb-copilot-script', TWBB_URL . '/Apps/CoPilot/assets/js/script.js', ['jquery'], TWBB_VERSION);

        $accessToken = get_site_option('tenweb_access_token');
        $wpUserId = get_current_user_id();
        $pageId = get_the_ID();
        $domainId = get_site_option('tenweb_domain_id');
        $workspaceId = get_site_option('tenweb_workspace_id');
        $ai_assistant_url = rtrim(TENWEB_AI_ASSISTANT, '/');
        $customTokenApi = $ai_assistant_url . '/copilot/workspaces/' . get_site_option('tenweb_workspace_id') . '/domains/' . get_site_option('tenweb_domain_id') . '/firebase-token';

        $clearChatUrl = $ai_assistant_url . '/copilot/workspaces/' . $workspaceId . '/domains/' . $domainId . '/pages/' . $pageId;
        $feedBackApi = $ai_assistant_url . '/copilot/workspaces/' . $workspaceId . '/domains/' . $domainId . '/feedback';
        $copilotAjaxUrl = admin_url('admin-ajax.php');

        // phpcs:ignore WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $testMode = isset($_GET['tenweb_copilot_test_mode']) ? rest_sanitize_boolean($_GET['tenweb_copilot_test_mode']) : null;
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $testModeApi = isset($_GET['tenweb_copilot_test_mode_api']) ? rest_sanitize_boolean($_GET['tenweb_copilot_test_mode_api']) : null;
        global $tenweb_firebaseConfig;

        $menus = wp_get_nav_menus();

        $menu_name_id_mapping = [];

        foreach ($menus as $menu) {
          $menu_name_id_mapping[$menu->slug] = $menu->term_id;
        }

        wp_localize_script(
            'twbb-copilot-script',
            'twbb_chat',
            [
                'accessToken' => $accessToken,
                'wpUserId' => $wpUserId,
                'pageId' => $pageId,
                'domainId' => $domainId,
                'workspaceId' => $workspaceId,
                'customTokenApi' => $customTokenApi,
                'clearChatUrl' => $clearChatUrl,
                'feedBackApi' => $feedBackApi,
                'dashboardURL' => TENWEB_DASHBOARD,
                'restURL' => esc_url(rest_url()),
                'twbb_sg_nonce' => wp_create_nonce('twbb-sg-nonce'),
                'twbb_cop_nonce' => wp_create_nonce('twbb-cop-nonce'),
                'firebaseConfig' => $tenweb_firebaseConfig,
                'testMode' => $testMode,
                'testModeApi' => $testModeApi,
                'copilotAjaxUrl' => $copilotAjaxUrl,
                'theme' => strtolower(get_option('twbb_kit_theme_name', 'classic')),
                'menu_name_id_mapping' => $menu_name_id_mapping
            ]
        );

        wp_enqueue_style('twbb-copilot-style', TWBB_URL . '/Apps/CoPilot/assets/css/style.css', [], TWBB_VERSION);
    }

    private function registerApiRoutes(): void
    {
        $wpPostController = new WpPostController();

        add_action('wp_ajax_twbb_copilot_change_wp_post_visibility', [$wpPostController, 'changeWpPostVisibility']);
        add_action('wp_ajax_twbb_copilot_clone_wp_post', [$wpPostController, 'cloneWpPost']);
        add_action('wp_ajax_twbb_copilot_create_wp_post', [$wpPostController, 'createWpPost']);
    }
}