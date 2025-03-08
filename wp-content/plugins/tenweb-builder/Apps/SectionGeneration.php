<?php

namespace Tenweb_Builder\Apps;

use Elementor\Utils;

class SectionGeneration extends BaseApp
{
    const SECTIONS_CPT = 'twbb_sg_preview';
    const GENERATED_SECTIONS_TYPE = 'ai-generated-sections';
    const SECTIONS_PATH = '/ai20-sections';
    public $sectionGenerationTypes = [];
    public $ecommerceSections = [];
    protected static $instance = null;

    public static function getInstance(){
        if(is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct()
    {
        $this->process();
    }

    public function enqueueEditorScripts() {
        $twbb_sg_nonce = wp_create_nonce('twbb-sg-nonce');
        if ( TWBB_DEV === TRUE ) {
            wp_enqueue_script(
                'section-generation-editor-script',
                TWBB_URL . '/Apps/SectionGeneration/assets/script/section_generation_editor.js',
                ['jquery', 'elementor-editor', 'custom-select-js','custom-select-jquery-js', 'twbb-ai-main-js', 'twbb-editor-scripts-v2'],
                TWBB_VERSION,
                TRUE
            );
        } else {
            wp_enqueue_script(
                'section-generation-editor-script',
                TWBB_URL . '/Apps/SectionGeneration/assets/script/section_generation_editor.min.js',
                ['jquery', 'elementor-editor', 'custom-select-js','custom-select-jquery-js', 'twbb-editor-scripts', 'twbb-editor-scripts-v2'],
                TWBB_VERSION,
                TRUE
            );
        }

        wp_localize_script(
            'section-generation-editor-script',
            'twbb_sg_editor',
            array(
                'twbb_sg_nonce' => $twbb_sg_nonce,
                'ajaxurl' => admin_url( 'admin-ajax.php' ),
                'business_type' => $this->setParamsForLocalize()['business_type'],
                'business_name' => $this->setParamsForLocalize()['business_name'],
                'business_description' => $this->setParamsForLocalize()['business_description'],
                'sections_exists' => $this->setParamsForLocalize()['sections_exists'],
                'sections_new' => get_option('twbb_sections_new', 'not_passed'),
                'woocommerceActiveStatus' => is_plugin_active('woocommerce/woocommerce.php'),
            )
        );
        // Enqueue custom-select JS
        wp_enqueue_script(
            'custom-select-jquery-js',
            TWBB_URL . '/assets/libs/custom-select/jquery.custom-select.min.js',
            array('jquery'), // Make sure jQuery is loaded as a dependency
            false,
            true
        );
        wp_enqueue_script(
            'custom-select-js',
            TWBB_URL . '/assets/libs/custom-select/custom-select.min.js',
            array('jquery'), // Make sure jQuery is loaded as a dependency
            false,
            true
        );
    }

    public function enqueueEditorStyles() {
        wp_register_style('twbb-open-sans', 'https://fonts.googleapis.com/css?family=Open+Sans:300,400,500,600,700,800&display=swap');
        if ( TWBB_DEV === TRUE ) {
            wp_enqueue_style(
                'section-generation-editor-style',
                TWBB_URL . '/Apps/SectionGeneration/assets/style/section_generation.css',
                ['twbb-open-sans','custom-select-css'],
                TWBB_VERSION
            );
            // Enqueue custom-select CSS
            wp_enqueue_style(
                'custom-select-css',
                TWBB_URL . '/assets/libs/custom-select/custom-select.css'
            );
        } else {
            //custom-select-css is concated to the section_generation_editor.min.css css file
            wp_enqueue_style(
                'section-generation-editor-style',
                TWBB_URL . '/Apps/SectionGeneration/assets/style/section_generation.min.css',
                ['twbb-open-sans'],
                TWBB_VERSION
            );
        }
    }

    public function enqueueEmbedStyles() {
        if ( TWBB_DEV === TRUE ) {
            wp_enqueue_style(
                'twbb-section-generation-embed-style',
                TWBB_URL . '/Apps/SectionGeneration/assets/style/section_generation_embed.css',
                [],
                TWBB_VERSION
            );
        } else {
            wp_enqueue_style(
                'twbb-section-generation-embed-style',
                TWBB_URL . '/Apps/SectionGeneration/assets/style/section_generation_embed.min.css',
                [],
                TWBB_VERSION
            );
        }
    }

    public function enqueueEmbedScripts() {
        $twbb_sg_nonce = wp_create_nonce('twbb-sg-nonce');
        wp_register_script( 'twbb-editor-helper-script', TWBB_URL . '/assets/editor/js/helper-script.js', array('jquery'), TWBB_VERSION);
        wp_localize_script( 'twbb-editor-helper-script', 'twbb_helper', array(
                'domain_id' => get_option('tenweb_domain_id'))
        );
        if ( TWBB_DEV === TRUE ) {
            //TWBB_URL . '/ai/assets/js/request.js' is not enqueueing because it called from main body of the page
            wp_enqueue_script(
                'twbb-section-generation-embed-script',
                TWBB_URL . '/Apps/SectionGeneration/assets/script/section_generation_embed.js',
                ['jquery','twbb-editor-helper-script'],
                TWBB_VERSION,
                TRUE
            );
        } else {
            wp_enqueue_script(
                'twbb-section-generation-embed-script',
                TWBB_URL . '/Apps/SectionGeneration/assets/script/section_generation_embed.min.js',
                ['jquery','twbb-editor-helper-script'],
                TWBB_VERSION,
                TRUE
            );
        }
        wp_localize_script(
            'twbb-section-generation-embed-script',
            'twbb_sg_embed',
            array(
                'twbb_sg_nonce' => $twbb_sg_nonce,
                'ajaxurl' => admin_url( 'admin-ajax.php' ),
                'business_description' => $this->setParamsForLocalize()['business_description'],
                'ecommerce_label' => esc_html__('Best for stores', 'tenweb-builder'),
            )
        );
    }

    public function setTemplates() {
        $this->generatePostsByType();
        require_once(TWBB_DIR . '/Apps/SectionGeneration/templates/templates.php');
    }

    public function setEmbedTemplates() {
        //to avoid showing embed templates to non-admin users
        if( current_user_can('manage_options') ) {
            require_once(TWBB_DIR . '/Apps/SectionGeneration/templates/embed_templates.php');
        }
    }

    public function generatedWithAISectionTemplate() {
        $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field($_POST['nonce']) : '';
        if ( !wp_verify_nonce( $nonce, 'twbb-sg-nonce' ) ) {
            wp_send_json_error("invalid_nonce");
        }

        $data = isset( $_POST['closest_sections_data'] ) ? json_decode(stripslashes(sanitize_text_field($_POST['closest_sections_data'])), true) : [];
        $data = $this->addNeededArgsToRequest($data);
        $data['unique_id'] = isset($_POST['unique_id']) ? sanitize_text_field($_POST['unique_id']) : '';
        $domain_id = get_site_option( TENWEB_PREFIX . '_domain_id' );
        $data['domain_id'] = $domain_id;
        /* Count of generated sections */
        $n_results = isset($_POST['n_results']) ? intval($_POST['n_results']) : '';
        if( $n_results !== '' ) {
            $data['n_results'] = $n_results;
        }

        $api_response = \Tenweb_Builder\Modules\ai\TenWebApi::get_instance()->getGeneratedSectionVariations($data);

        if( is_wp_error($api_response) ) {
            wp_send_json_error("error");
            die();
        }
        $api_response['body'] = json_decode($api_response['body'], true);
        $directoryPath = wp_upload_dir()['basedir'] . '/' . self::SECTIONS_PATH . '/' . self::GENERATED_SECTIONS_TYPE;

        if (!is_dir($directoryPath)) {
          //phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.directory_mkdir
          mkdir($directoryPath, 0777, true);
        }
        //we are doing this because the response is not in the correct format
        $api_response['body']['data']['elementor_data'] = json_decode($api_response['body']['data']['elementor_data'],true);
        $variations = $this->separateGeneratedVariations( $api_response['body']['data'] );

        wp_send_json_success([
          'status' => 'success',
          'variation' => $variations,
          'post_id' => \Tenweb_Builder\Modules\SectionGeneration\GenerateSectionsPostsByType::getInstance()->checkUpdateSectionsData( self::GENERATED_SECTIONS_TYPE, true)
        ]);
        wp_die();
    }

    public function processSectionGenerationResponse(){
        $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field($_POST['nonce']) : '';
        if ( !wp_verify_nonce( $nonce, 'twbb-sg-nonce' ) ) {
          wp_send_json_error("invalid_nonce");
        }

        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $data = $_POST['data'] ?? [];

        if (empty($data["data"]["elementor_data"])) {
          wp_send_json_error("no_data");
        }

        $elementor_data = json_decode(stripslashes($data["data"]["elementor_data"]), true);
        $directoryPath = wp_upload_dir()['basedir'] . '/' . self::SECTIONS_PATH . '/' . self::GENERATED_SECTIONS_TYPE;

        if (!is_dir($directoryPath)) {
          //phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.directory_mkdir
          mkdir($directoryPath, 0777, true);
        }

        $section_content_data = $elementor_data[0]['elements'];

        // phpcs:ignore Squiz.PHP.CommentedOutCode.Found
        /*$section_path = "ai20-sections/ai-generated-sections/ai_generated.json";
        $section_data = json_decode(file_get_contents(wp_upload_dir()['basedir'] . '/' . $section_path), true)['elementor_data']['elements'];
        $section_content_data = $this->replaceElementsIds($section_data);*/

        $uploadWidgetsAttachments = new \Tenweb_Builder\Modules\UploadWidgetsAttachments();
        $uploadWidgetsAttachments->upload($section_content_data);
        $params = [
          'content' => $section_content_data,
          'post' => $_POST,
            // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
          'post_data' => $_POST["data"],
          'elementor_data' => $data["data"]["elementor_data"],
          'elementor_data_decode' => json_decode($data["data"]["elementor_data"], true)
        ];
        wp_send_json_success(
          [
            'params' => $params,
            'status' => 'success'
          ]
        );
        wp_die();
    }

    public function runSectionGeneratedDataForRequest() {
	    $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : '';;
        if ( !wp_verify_nonce( $nonce, 'twbb-sg-nonce' ) ) {
            wp_send_json_error("invalid_nonce");
        }
        $section_path = isset( $_POST['section_path'] ) ? sanitize_text_field($_POST['section_path']) : '';
        $content_type = isset( $_POST['content_type'] ) ? sanitize_text_field($_POST['content_type']) : 'generate_content';
        $data = isset( $_POST['closest_sections_data'] ) ? json_decode(stripslashes(sanitize_text_field($_POST['closest_sections_data'])), true) : [];
        $data = $this->addNeededArgsToRequest($data);
        $data['current_section']['catalogue_name'] = $section_path;
        $domain_id = get_site_option( TENWEB_PREFIX . '_domain_id' );
        $data['domain_id'] = $domain_id;
        if( $content_type === 'generate_content' ) {
            $api_response = \Tenweb_Builder\Modules\ai\TenWebApi::get_instance()->getGeneratedSectionVariations($data);
            $api_response['body'] = json_decode($api_response['body'], true);
            if( is_wp_error($api_response) ) {
                //if fail add section with dummy content
                $section_data = json_decode(file_get_contents(wp_upload_dir()['basedir'] . '/' . $section_path), true)['elementor_data']['elements'];
                $section_content_data = $this->replaceElementsIds($section_data);
            } else {
                //we are doing this because the response is not in the correct format
                $api_response['body']['data']['elementor_data'] = json_decode($api_response['body']['data']['elementor_data'], true);
                $section_content_data = $api_response['body']['data']['elementor_data'][0]['elements'];
            }
        } else {
            $section_data = json_decode(file_get_contents(wp_upload_dir()['basedir'] . '/' . $section_path), true)['elementor_data']['elements'];
            $section_content_data = $this->replaceElementsIds($section_data);
        }

        $uploadWidgetsAttachments = new \Tenweb_Builder\Modules\UploadWidgetsAttachments();
        $uploadWidgetsAttachments->upload($section_content_data);
        $params = [
            'content' => $section_content_data,
        ];
        //update option to know that new sections are created get one time
        if (get_option('twbb_sections_new') !== 'passed') {
            update_option('twbb_sections_new', 'passed');
        }
        wp_send_json_success(
            [
                'params' => $params,
                'status' => 'success'
            ]
        );

    }

    public function clearGeneratedSections() {
        $ai_generated_path = wp_upload_dir()['basedir'] . self::SECTIONS_PATH . '/' . self::GENERATED_SECTIONS_TYPE;
        $this->emptyFolder($ai_generated_path);
    }

    public function createCustomPostType() {
        $public = false;
        if( TWBB_DEV ) {
            $public = true;
        }
        $args = array(
            'labels' => array(
                'name' => __('SG Preview'),
                'singular_name' => __('SG Preview')
            ),
            'public' => $public,
            'publicly_queryable' => true,
            'show_in_rest' => false,
            'has_archive' => false,
        );

        if (!post_type_exists(self::SECTIONS_CPT)) {
            register_post_type(self::SECTIONS_CPT, $args);
        }

        add_post_type_support( self::SECTIONS_CPT, 'elementor');
    }

    public function sectionsReinstall() {
	    $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : '';;
        if ( !wp_verify_nonce( $nonce, 'twbb-sg-nonce' ) ) {
            wp_send_json_error("invalid_nonce");
        }
        \Tenweb_Builder\Builder::sectionsSync();
        if( !$this->isSectionsFilesExists() ) {
            wp_send_json_error('there is no section files');
        }
        wp_send_json_success('section files are exists');
    }

    public function isSectionsFilesExists() {
        $upload_dir = wp_upload_dir()['basedir'];
        $dir_path = $upload_dir . self::SECTIONS_PATH;

        if(!is_dir($dir_path)){
            return false;
        }

        $scanDir = scandir($dir_path);
        if( empty($scanDir) || count($scanDir) < 3 ) {
            return false;
        }
        return true;
    }

    private function separateGeneratedVariations($api_response_data) {
        $elementor_datas = $api_response_data['elementor_data'];
        if( !is_array($elementor_datas)) {
            return 0;
        }
        $this->clearGeneratedSections();
        foreach ( $elementor_datas as $key => $elementor_data ) {
            $json_content = [
                'elementor_data' => [
                    'elements' => $elementor_data['elements']
                ],
                'catalogue_name' => $elementor_data['catalogue_name'],
            ];
            //catalogue_name will be used from js to send to analytics GA events
            file_put_contents(wp_upload_dir()['basedir'] . '/' . self::SECTIONS_PATH . '/' . self::GENERATED_SECTIONS_TYPE . '/ai_generated_'. $key . '.json', json_encode($json_content)); //phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.file_ops_file_put_contents, WordPress.WP.AlternativeFunctions.json_encode_json_encode
        }
        return 1;
    }

    private function addNeededArgsToRequest($data)
    {

        // Get the global Elementor settings
        $elementor_settings = \Elementor\Plugin::$instance->kits_manager->get_current_settings();

        // Extract the global colors and fonts
        $global_system_colors = isset($elementor_settings['system_colors']) ? $elementor_settings['system_colors'] : [];
        $global_custom_colors = isset($elementor_settings['custom_colors']) ? $elementor_settings['custom_colors'] : [];
        $global_colors = array_merge($global_system_colors, $global_custom_colors);
        $global_fonts = isset($elementor_settings['system_typography']) ? $elementor_settings['system_typography'] : [];
        $globals_for_send = ['primary', 'secondary', 'twbb_bg_inv'];
        foreach ($global_colors as $key => $value) {
            if (in_array($value['_id'], $globals_for_send, true)) {
                if (strlen($value['color']) > 7) {
                    $value['color'] = substr($value['color'], 0, 7);
                }
                if ($value['_id'] === 'twbb_bg_inv') {
                    $data['background_dark'] = $value['color'];
                } else {
                    $data[$value['_id'] . '_color'] = $value['color'];
                }
            }
        }
        $data['primary_font'] = $global_fonts[0]['typography_font_family'];
        $data['theme'] = strtolower(get_option('twbb_kit_theme_name', 'classic'));

        return $data;
    }

    private function setParamsForLocalize() {
        $business_description = '';
        $business_type = '';
        $name = '';
        $sections_exists = 'no';

        $twbb_site_description = get_option('twbb_site_description');
        $twbb_user_inputs = get_option( 'twbb_user_inputs' );

        if( $twbb_site_description !== null ) {
            if ( isset($twbb_site_description['description']) ) {
                $business_description = $twbb_site_description['description'];
            } else if ( !empty( $twbb_user_inputs ) && isset( $twbb_user_inputs['company_description'] )) {
                $business_description = $twbb_user_inputs['company_description'];
            }
            if ( isset($twbb_site_description['business_type']) ) {
                $business_type = $twbb_site_description['business_type'];
            }
            if ( isset($twbb_site_description['description']) ) {
                $name = $twbb_site_description['name'];
            }
        }

        if( $this->isSectionsFilesExists() ) {
            $sections_exists = 'yes';
        }

        return [
            'business_type' => $business_type,
            'business_name' => $name,
            'business_description' => $business_description,
            'sections_exists' => $sections_exists,
        ];
    }

    private function emptyFolder($path) {
        $allFiles = glob( $path ."/*.json" );
        if( $allFiles ) {
            foreach( $allFiles as $file ) {
                unlink($file);//phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.file_ops_unlink
            }
        }
    }

    private function process()
    {
        if ( self::visibilityCheck() ) {
            $this->addActions();
        }
    }

    private function addActions()
    {
        add_action( 'init', array( $this,'createCustomPostType' ) );
        add_action( 'elementor/editor/v2/scripts/enqueue/after', array( $this, 'enqueueEditorScripts' ) , 12);
        add_action( 'elementor/editor/v2/styles/enqueue', array( $this, 'enqueueEditorStyles' ), 12 );
        add_action( 'elementor/editor/footer', array($this, 'setTemplates' ) );
        add_action( 'elementor/frontend/after_register_scripts', array( $this, 'enqueueEmbedScripts' ) );
        add_action( 'elementor/frontend/after_register_styles', array( $this, 'enqueueEmbedStyles' ) );
        add_action( 'wp_footer', array( $this, 'setEmbedTemplates' ) );
        add_action( 'wp_ajax_twbb_generate_with_ai_section_template', array($this, 'generatedWithAISectionTemplate') );
        add_action( 'wp_ajax_twbb_process_generation_response', array($this, 'processSectionGenerationResponse') );
        add_action( 'wp_ajax_twbb_get_section_generated_data_for_request', array($this, 'runSectionGeneratedDataForRequest') );
        add_action( 'wp_ajax_twbb_sections_reinstall', array($this, 'sectionsReinstall') );
    }

    private function generatePostsByType() {
        $this->sectionGenerationTypes = \Tenweb_Builder\Modules\SectionGeneration\GenerateSectionsPostsByType::getInstance()->sectionGenerationTypes['basic'];
        $this->ecommerceSections = \Tenweb_Builder\Modules\SectionGeneration\GenerateSectionsPostsByType::getInstance()->sectionGenerationTypes['ecommerce'];
    }

    private static function visibilityCheck(){
        //the same check is in builder.php sectionsSync method
        return ( get_option('elementor_experiment-sections_generation') !== 'inactive' &&
            defined('TW_HOSTED_ON_10WEB') && TW_HOSTED_ON_10WEB === TRUE );
    }

    //function is copied from elementor replace_elements_ids() function
    protected function replaceElementsIds( $content ) {
        return  \Elementor\Plugin::instance()->db->iterate_data( $content, function( $element ) {
            $element['id'] = Utils::generate_random_string();

            return $element;
        } );
    }
}
