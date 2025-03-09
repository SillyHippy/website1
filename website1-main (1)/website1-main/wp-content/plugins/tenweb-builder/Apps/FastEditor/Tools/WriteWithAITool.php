<?php

namespace Tenweb_Builder\FastEditor\Tools;
class WriteWithAITool extends FastEditorTool
{
    private $aiActions;
    private $aiErrors;

    public string $type = 'ask_ai';

    public string $dataAttr = 'data-write-ai-tool';

    /**
     * Variable is getting on of 3 values
     * text: for generating texts
     * image: for generating image
     * all: for generating and texts and images
    */
    public $generate_type = 'text';

    public function __construct( $controlData = [] ) {
        parent::__construct( $controlData );
        $this->setActions();
        $this->setAIErrors();
        if ( !empty($controlData) && isset($controlData[0]['generate_type']) ) {
            $this->generate_type = $controlData[0]['generate_type'];
        }
    }

    public function getToolContent() {
        $write_with_ai_html = '';
        $tooltip_button_text = ($this->generate_type === 'text') ? __('Write with ','tenweb-builder') : __('Generate with ','tenweb-builder');
        if( $this->visibilityCheck() ) {
            $actions = $this->aiActions;
            $help_active = false;
            if ( class_exists( '\Tenweb_Manager\Manager' ) ) {
                $user_info = \Tenweb_Manager\Helper::get_tenweb_user_info();
                if ( is_array($user_info) && isset($user_info['agreement_info'] ) ) {
                    if ( is_array($user_info['agreement_info']) && isset($user_info['agreement_info']['coupon_code']) && $user_info['agreement_info']['coupon_code'] === '1wf' ) {
                        $help_active = true;
                    }
                }
            }

            $help_class = $help_active ? ' twbb-help-active' : '';
            $generate_type_class = ' twbb-generatin-type-' . $this->generate_type;
            if( $this->generate_type === 'image' ) {
                $ai_actions_html = "<div class='twbb_ask_to_ai_actions'>
                                        <span data-view='new_image_view' class='twbb_ask_to_ai_action twbb-ai-generate-image'>New image</span>
                                        <span data-view='edit_image_view' class='twbb_ask_to_ai_action twbb-ai-generate-image'>Edit image</span>
                                        <span data-view='multiple_view' class='twbb_ask_to_ai_action twbb-ai-generate-image'>Multiple views</span>
                                        <span class='twbb_ask_to_ai_action twbb_ask_to_ai_action_remove_bg' data-action='image_remove_bg'>Remove background</span>          
                                    </div>";
            }else{
                $ai_actions_html = "<div class='twbb_ask_to_ai_actions'>
                                        <span class='twbb_ask_to_ai_action twbb-ai-action-button twbb_ask_to_ai_simplify_language' data-action='" . $actions['simplify_language']['endpoint'] . "'>" . $actions['simplify_language']['title'] . "</span>
                                        <span class='twbb_ask_to_ai_action twbb-ai-action-button twbb_ask_to_ai_make_it_longer' data-action='" . $actions['make_it_longer']['endpoint'] . "'>" . $actions['make_it_longer']['title'] . "</span>
                                        <span class='twbb_ask_to_ai_action twbb-ai-action-button twbb_ask_to_ai_make_it_shorter' data-action='" . $actions['make_it_shorter']['endpoint'] . "'>" . $actions['make_it_shorter']['title'] . "</span>
                                        <span class='twbb_ask_to_ai_action twbb-ai-action-button twbb_ask_to_ai_fix_spelling_and_grammar' data-action='" . $actions['fix_spelling_and_grammar']['endpoint'] . "'>" . $actions['fix_spelling_and_grammar']['title'] . "</span>          
                                    </div>";
            }


            $write_with_ai_html = "<div class='twbb-fe-tool twbb_ask_to_ai_empty twbb-ai-front'>
                                    " . $ai_actions_html . "
                                    <div class='ask_to_ai_container'>
                                        <span class='twbb_ask_to_ai_button' onclick='twbb_fast_edit_tools_events(this, \"ask_ai\")'><span class='twbb_ask_to_ai_icon'></span>Ask AI</span>
                                        <span class='ask_to_ai_input_container ask_to_ai_disabled'>
                                            <textarea value='' data-type='".$this->generate_type."' class='twbb_ask_to_ai twbb-fe-dropdown' name='ask_to_ai' type='text' placeholder='Ask AI to edit..'></textarea>
                                            <span class='twbb_ask_to_ai_submit_button'></span>
                                        </span>
                                    </div>
                                </div><div class='twbb-fast-edit-tools' onclick='twbb_fast_edit_tools_events(this, \"tools\")'>";
            return $write_with_ai_html;
        }


    }

    public function editorScripts() {}

    public function frontendScripts() {
        wp_enqueue_script( 'twbb-write-with-ai-helper-js', TWBB_URL . '/Apps/TextGenerationAI/assets/js/write_with_ai_helper.js', [ 'jquery' ], TWBB_VERSION, TRUE );
        wp_enqueue_script( 'twbb-write-with-ai-js', TWBB_URL . '/Apps/FastEditor/assets/scripts/write_with_ai_frontend.js', [ 'jquery','twbb-editor-helper-script', 'twbb-write-with-ai-helper-js','twbb-fe-helper-script'], TWBB_VERSION, TRUE );
    }
    public function frontendStyles() {
        wp_enqueue_style( 'twbb-write-with-ai-frontend', TWBB_URL . '/Apps/FastEditor/assets/styles/write_with_ai_frontend.css', array(), TWBB_VERSION );
    }

    protected function visibilityCheck()
    {
        if ( TW_HOSTED_ON_10WEB ) {
            return true;
        }
        return false;
    }

    public function getLocalizedData(){
        $limitation_data  = $this->getLimitationData();
        $total_allowed_words = !empty ($limitation_data['planLimit']) ? intval($limitation_data['planLimit']) : 0;
        $domain_id = get_site_option( TENWEB_PREFIX . '_domain_id' );
        $localized_write_with_ai_data = array(
            'twbb_write_with_ai_data' => array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'ajaxnonce' => wp_create_nonce('wp_rest'),
                "rest_route" => get_rest_url(null, 'ai-builder-tenweb/ai'),
                "notification_status" => get_transient('twbb_notification'),
                'limitation_expired' => $limitation_data['limitation_expired'],
                'plan' => \Tenweb_Builder\Modules\ai\Utils::is_free( $total_allowed_words ) ? 'Free' : '',
                'error_data' => $this->aiErrors,
                'domain_id' => !empty($domain_id) ? $domain_id : '',
            )
        );
        return $localized_write_with_ai_data;
    }

    private function getLimitationData() {
    $limitation = get_option('twbb_limitation');
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

    private function setActions() {
        $this->aiActions = array(
            'new_prompt' => array(
                'title' =>  esc_html__('New prompt','tenweb-builder'),
                'endpoint' => 'new_prompt'
            ),
            'simplify_language' => array(
                'title' =>  esc_html__('Simplify','tenweb-builder'),
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
                'title' =>  esc_html__('Fix grammar','tenweb-builder'),
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
    }

    private function setAIErrors() {
        $this->aiErrors = array(
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

}