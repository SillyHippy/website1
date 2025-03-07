<?php
namespace Tenweb_Builder\Apps;

use Elementor\Core\Settings\Manager as SettingsManager;
use Tenweb_Builder\Modules\ai;

class TextGenerationAI extends BaseApp {

    public $prompts = array();
    public $ai_actions = array();
    public $ai_tones = array();
    public $ai_translates = array();
    public $ai_error_data = array();

    const LIMITATION_OPTION = 'twbb_limitation';

    protected static $instance = null;

    public static function getInstance() {
        if ( self::$instance === null ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct() {
        if ( self::visibilityCheck() ) {
            $this->set_defaults();
            $this->register_ai_related_hooks();
        }
    }

    private static function visibilityCheck(){
        return TW_HOSTED_ON_10WEB;
    }

    public function set_defaults() {
        $this->prompts = array(
            'text_prompts' => array(
                esc_html__('Write a product description for…','tenweb-builder'),
                esc_html__('Draft a mission statement for…','tenweb-builder'),
                esc_html__('Develop a set of FAQs for…','tenweb-builder'),
                esc_html__('Craft compelling blog content for…','tenweb-builder'),
                esc_html__('Develop a detailed ‘Services’ section for…','tenweb-builder'),
            ),
            'headline_prompts' => array(
                esc_html__('Generate a compelling headline for','tenweb-builder'),
                esc_html__('Create an engaging website heading for','tenweb-builder'),
                esc_html__('Write a catchy website title for','tenweb-builder'),
                esc_html__('Propose a dynamic website headline for','tenweb-builder'),
                esc_html__('Invent a unique website heading for','tenweb-builder'),
            ),
        );

        $this->ai_actions = array(
            'new_prompt' => array(
                'title' =>  esc_html__('Generate Text','tenweb-builder'),
                'endpoint' => 'new_prompt'
            ),
            'simplify_language' => array(
                'title' =>  esc_html__('Simplify Language','tenweb-builder'),
                'endpoint' => 'simplify_language'
            ),
            'make_it_longer' => array(
                'title' =>  esc_html__('Make it longer','tenweb-builder'),
                'endpoint' => 'make_it_longer'
            ),
            'make_it_shorter' => array(
                'title' =>  esc_html__('Make it shorter','tenweb-builder'),
                'endpoint' => 'make_it_shorter'
            ),
            'fix_spelling_and_grammar' => array(
                'title' =>  esc_html__('Fix spelling & grammar','tenweb-builder'),
                'endpoint' => 'fix_spelling_and_grammar'
            ),
            'change_tone' => array(
                'title' =>  esc_html__('Change tone','tenweb-builder'),
                'endpoint' => 'change_tone'
            ),
            'translate_to' => array(
                'title' =>  esc_html__('Translate to','tenweb-builder'),
                'endpoint' => 'translate_to'
            ),
        );

        $this->ai_tones = array("Casual", "Confidence", "Formal", "Friendly", "Inspirational", "Motivational", "Nostalgic", "Playful",
            "Professional", "Scientific", "Straightforward", "Witty");

        $this->ai_translates = array("Arabic", "Chinese", "Czech", "Danish", "Dutch", "English", "Finnish", "French", "German",
            "Greek", "Hebrew", "Hungarian", "Indonesian", "Italian", "Japanese", "Korean", "Persian", "Polish", "Portuguese", "Russian",
            "Spanish", "Swedish", "Thai", "Turkish", "Vietnamese");

        $this->ai_error_data = array(
            'free_limit_reached' => array(
                'text' => __('You have reached your monthly limit of Free Plan. Upgrade to a higher plan to continue using AI Assistant.', 'tenweb-builder'),
            ),
            'plan_limit_reached' => array(
                'text' => __('You have reached your monthly limit for the Personal Plan. Upgrade to a higher plan to continue using AI Assistant.', 'tenweb-builder'),
            ),
            'permission_error' => array(
                'text' => __('You cannot edit this page because you do not have the necessary permissions. Please log in with an administrator account to proceed.', 'tenweb-builder'),
            ),
            'there_is_in_progress_request' => array(
                'text' => __('It seems like another generation request is in progress. Please retry once its finished.', 'tenweb-builder'),
            ),
            'input_is_long' => array(
                'text' => __('Selected text is too long, please select a short text and try again.', 'tenweb-builder'),
            ),
            'expectation_failed' => array(
                'text' => __('Selected text is too long, please select a short text and try again.', 'tenweb-builder'),
            ),
            'something_wrong' => array(
                'text' => __('There was an issue while attempting to access 10Web services. Please try again later.', 'tenweb-builder'),
            ),
        );

    }

    /**
     * Get limitation expired or not and plan title
     *
     * @return array
     */
    public function get_limitation_data() {
        $limitation = \Tenweb_Builder\Modules\ai\Utils::get_limitation();
        if ( !empty($limitation) && ($limitation['planLimit'] <= $limitation['alreadyUsed']) )  {
            return array(
                'limitation_expired'  => 1,
                'plan' => $limitation['planTitle'],
            );
        }
        return array(
            'limitation_expired'  => 0,
            'plan' => isset($limitation['planTitle']) ? $limitation['planTitle'] : __('Free', 'tenweb-builder'),
        );
    }

    public function view() {
        require_once(TWBB_DIR . '/Apps/TextGenerationAI/templates/view.php');
    }

    public function register_ai_related_hooks() {
        add_action( 'elementor/editor/before_enqueue_scripts', array( $this, 'enqueue_editor_scripts'));
    }

    public function enqueue_editor_scripts() {
        wp_enqueue_script( 'twbb-write-with-ai-helper-js', TWBB_URL . '/Apps/TextGenerationAI/assets/js/write_with_ai_helper.js', [ 'jquery' ], TWBB_VERSION, TRUE );
        wp_enqueue_script( 'twbb-fe-helper-script', TWBB_URL . '/Apps/FastEditor/assets/scripts/FastEditorHelper.js', [ 'jquery' ], TWBB_VERSION, TRUE );
        wp_enqueue_script('twbb-ai-js', TWBB_URL . '/Apps/TextGenerationAI/assets/js/script.js', ['jquery', 'twbb-write-with-ai-helper-js', 'twbb-fe-helper-script'], TWBB_VERSION, TRUE);
        $limitation_data  = $this->get_limitation_data();
        $total_allowed_words = !empty ($limitation_data['planLimit']) ? intval($limitation_data['planLimit']) : 0;
        $ui_theme_selected = SettingsManager::get_settings_managers( 'editorPreferences' )->get_model()->get_settings( 'ui_theme' );
        $domain_id = get_site_option( TENWEB_PREFIX . '_domain_id' );
        wp_localize_script('twbb-ai-js', 'twbb_write_with_ai_data', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'ajaxnonce' => wp_create_nonce('wp_rest'),
            "rest_route" => get_rest_url(null, 'ai-builder-tenweb/ai'),
            "notification_status" => get_transient('twbb_notification'),
            'limitation_expired' => $limitation_data['limitation_expired'],
            'plan' => \Tenweb_Builder\Modules\ai\Utils::is_free( $total_allowed_words ) ? 'Free' : '',
            'error_data' => $this->ai_error_data,
            'twbb_ui_theme' => $ui_theme_selected,
            'domain_id' => !empty($domain_id) ? $domain_id : '',
        ));
        if ( TWBB_DEV === TRUE ) {
            wp_enqueue_script('twbb-ai-main-js', TWBB_URL . '/Apps/TextGenerationAI/assets/js/request.js', ['jquery'], TWBB_VERSION, TRUE);
        } else {
           // it is concated in editor-tenweb.min.js
        }
        wp_register_style('twbb-open-sans', 'https://fonts.googleapis.com/css?family=Open+Sans:300,400,500,600,700,800&display=swap');
        wp_enqueue_style( 'twbb-ai-css', TWBB_URL . '/Apps/TextGenerationAI/assets/css/style.css', array('twbb-open-sans'), TWBB_VERSION );
        wp_enqueue_style('twbb-ai-css-dark', TWBB_URL . '/Apps/TextGenerationAI/assets/css/dark_style.css', array(), TWBB_VERSION);

        $this->view();
    }

}