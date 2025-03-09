<?php

namespace Tenweb_Builder\Apps;

class BuilderTour extends BaseApp
{
    public $builderTourVisibility;

    private $tourContentVariables;

    protected static $instance = null;

    public function tourView() {
        require_once ( TWBB_DIR . '/Apps/BuilderTour/templates/tour_view.php');
    }

    public function enqueueFrontendScripts() {
        wp_register_style('twbb-open-sans', 'https://fonts.googleapis.com/css?family=Open+Sans:300,400,500,600,700,800&display=swap');
        if ( TWBB_DEV === TRUE ) {
            wp_enqueue_style(
                    'twbb-editor-tour-style',
                    TWBB_URL . '/Apps/BuilderTour/assets/style/editor/tour.css',
                    array('twbb-open-sans'),
                    TWBB_VERSION
            );
        } else {
            wp_enqueue_style(
                    'twbb-editor-tour-style',
                    TWBB_URL . '/Apps/BuilderTour/assets/style/editor/tour.min.css',
                    array('twbb-open-sans'),
                    TWBB_VERSION
            );
        }
    }

    public function enqueueEditorScripts() {
        if ( TWBB_DEV === TRUE ) {
            wp_enqueue_style(
                'twbb-editor-tour-style',
                TWBB_URL . '/Apps/BuilderTour/assets/style/editor/tour.css',
                array('twbb-open-sans'),
                TWBB_VERSION
            );
            wp_enqueue_script(
                'twbb-editor-tour-script',
                TWBB_URL . '/Apps/BuilderTour/assets/script/editor/tour.js',
                [ 'jquery' ],
                TWBB_VERSION,
                TRUE
            );
        } else {
            wp_enqueue_style(
                    'twbb-editor-tour-style',
                    TWBB_URL . '/Apps/BuilderTour/assets/style/editor/tour.min.css',
                    array('twbb-open-sans'),
                    TWBB_VERSION
            );
            wp_enqueue_script(
                    'twbb-editor-tour-script',
                    TWBB_URL . '/Apps/BuilderTour/assets/script/editor/tour.min.js',
                    [ 'jquery' ],
                    TWBB_VERSION,
                    TRUE
            );
        }

        wp_localize_script( 'twbb-editor-tour-script','tour_data', array(
            'tour_status' => get_option('twbb_tour_status'),
            'show_tour' => $this->builderTourVisibility,
            'ajaxurl'     => admin_url( 'admin-ajax.php' ),
            'nonce'       => wp_create_nonce( 'twbb' ),
            'tour_content_variables' => $this->tourContentVariables,
        ) );
    }

    public function updateTourStatus() {
        $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : '';
        if ( isset($_POST['tour_status']) && wp_verify_nonce( $nonce, 'twbb') ) {
            update_option('twbb_tour_status', sanitize_text_field( $_POST['tour_status'] ) );
            wp_send_json_success();
        }
        wp_send_json_error();
    }

    private function __construct()
    {
        $this->setTourStatusOption();
        $this->setVariables();
        $this->process();
    }

    private function setVariables() {
        $this->builderTourVisibility = self::visibilityCheck();
    }

    private function setTourStatusOption()
    {
        //show tour to all users( open automatically one time )
        if (get_option('twbb_tour_status') === false) {
            update_option('twbb_tour_status', 'not_started');
        }
    }

    private function registerHooks() {
        add_action( 'wp_ajax_twbb_update_tour_status', array( $this, 'updateTourStatus' ) );
        add_action('elementor/editor/footer', array($this, 'tourView'));
        /* wp_footer action's third parameter need to be elementor's 'wp_footer' actions third parameter +1 */
        add_action('elementor/editor/init', function() {
            add_action('wp_footer', [$this, 'enqueueFrontendScripts'], 12);
        }, 1 );
        add_action('wp_footer', function() {
            ?>
            <style>
                .twbb-tour-overlay-preview-part {
                    position: fixed;
                    width: 100vw;
                    height: 100vh;
                    top: 0;
                    left: 0;
                    z-index: 99999;
                    background-color: #5966D9;
                    opacity: 20%;
                }
            </style>
            <?php
        }, 12);
        add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'enqueueEditorScripts' ] );
    }

    private function run() {
        $this->registerHooks();
    }

    private static function visibilityCheck(){
        return ( is_array(get_option("twbb_imported_site_data_generated")) &&
                ( get_option("twbb_imported_site_data_generated")['import_type'] === 'ai_regenerate' ||
                    get_option("twbb_imported_site_data_generated")['import_type'] === 'ai_recreate' ) )
            && get_option('elementor_experiment-editor_v2') === 'active';
    }

    private function setTourContentVariables() {
        $step_0 = array(
            'video_link' => esc_url('https://10-web-for-upload.s3.eu-central-1.amazonaws.com/builder-plugin-files/step_intro.mp4'),
            'poster_link' => '',
            'icon' => esc_attr('twbb-no-icon'),
            'title' => esc_html__('10Web Builder Editor tour', 'tenweb-builder'),
            'description' => wp_kses_post(__('Explore our drag-and-drop editor’s powerful <br>features that simplify website creation.', 'tenweb-builder') ),
            'buttons' => array(
                'class' => esc_attr('twbb-tour-guide__buttons twbb-tour-guide-column'),
                'buttonLeft' => array(
                    'text' => esc_html__('Remind me later', 'tenweb-builder'),
                    'classes' => esc_attr('twbb-tour-guide__button_no_bg twbb-tour-guide__button-stop'),
                ),
                'buttonRight' => array(
                    'text' => esc_html__('Let’s get started!', 'tenweb-builder'),
                    'classes' => esc_attr('twbb-tour-guide__button_medium twbb-tour-guide__button_black twbb-tour-guide__button-next twbb-start_tour-send-ga'),
                ),
            ),
            'actionFunction' => 'getStarted',
        );
        $step_container_section = array(
            'video_link' => esc_url('https://10-web-for-upload.s3.eu-central-1.amazonaws.com/builder-plugin-files/step_container.mp4'),
            'poster_link' => esc_url('https://10-web-for-upload.s3.eu-central-1.amazonaws.com/builder-plugin-files/poster_container.jpg'),
            'icon' => esc_attr('container_section'),
            'title' => esc_html__('Use containers', 'tenweb-builder'),
            'description' => wp_kses_post(__('Use containers to create new sections<br> and make editing more flexible', 'tenweb-builder') ),
            'buttons' => array(
                'class' => esc_attr('twbb-tour-guide__buttons twbb-tour-guide-row'),
                'buttonLeft' => array(
                    'text' => esc_html__('Back', 'tenweb-builder'),
                    'classes' => esc_attr('twbb-tour-guide__button_small twbb-tour-guide__button_grey twbb-tour-guide__button-back'),
                ),
                'buttonRight' => array(
                    'text' => esc_html__('Next', 'tenweb-builder'),
                    'classes' => esc_attr('twbb-tour-guide__button_small twbb-tour-guide__button_black twbb-tour-guide__button-next'),
                ),
            ),
            'actionFunction' => 'containerSection',
        );
        $step_visual_element = array(
            'video_link' => esc_url('https://10-web-for-upload.s3.eu-central-1.amazonaws.com/builder-plugin-files/step_container_openned.mp4'),
            'poster_link' => esc_url('https://10-web-for-upload.s3.eu-central-1.amazonaws.com/builder-plugin-files/poster_container_openned.jpg'),
            'icon' => esc_attr('visual_element'),
            'title' => esc_html__('Add a new visual element', 'tenweb-builder'),
            'description' => wp_kses_post(__('Add a new visual element using a variety of <br>widgets. Simply drag and drop the widgets <br>to the desired location.', 'tenweb-builder') ),
            'buttons' => array(
                'class' => esc_attr('twbb-tour-guide__buttons twbb-tour-guide-row'),
                'buttonLeft' => array(
                    'text' => esc_html__('Back', 'tenweb-builder'),
                    'classes' => esc_attr('twbb-tour-guide__button_small twbb-tour-guide__button_grey twbb-tour-guide__button-back'),
                ),
                'buttonRight' => array(
                    'text' => esc_html__('Next', 'tenweb-builder'),
                    'classes' => esc_attr('twbb-tour-guide__button_small twbb-tour-guide__button_black twbb-tour-guide__button-next'),
                ),
            ),
            'actionFunction' => 'visualElement',
        );
        $step_responsiveness = array(
            'video_link' => esc_url('https://10-web-for-upload.s3.eu-central-1.amazonaws.com/builder-plugin-files/step_responsiveness.mp4'),
            'poster_link' => esc_url('https://10-web-for-upload.s3.eu-central-1.amazonaws.com/builder-plugin-files/poster_responsiveness.jpg'),
            'icon' => esc_attr('responsiveness'),
            'title' => esc_html__('Edit any page and check responsiveness', 'tenweb-builder'),
            'description' => wp_kses_post(__('Switch to edit or add pages on your <br>website and quickly check their <br>responsiveness.', 'tenweb-builder') ),
            'buttons' => array(
                'class' => esc_attr('twbb-tour-guide__buttons twbb-tour-guide-row'),
                'buttonLeft' => array(
                    'text' => esc_html__('Back', 'tenweb-builder'),
                    'classes' => esc_attr('twbb-tour-guide__button_small twbb-tour-guide__button_grey twbb-tour-guide__button-back'),
                ),
                'buttonRight' => array(
                    'text' => esc_html__('Next', 'tenweb-builder'),
                    'classes' => esc_attr('twbb-tour-guide__button_small twbb-tour-guide__button_black twbb-tour-guide__button-next'),
                ),
            ),
            'actionFunction' => 'responsiveness',
        );
        $step_global_styles = array(
            'video_link' => esc_url('https://10-web-for-upload.s3.eu-central-1.amazonaws.com/builder-plugin-files/step_themes.mp4'),
            'poster_link' => esc_url('https://10-web-for-upload.s3.eu-central-1.amazonaws.com/builder-plugin-files/poster_themes.jpg'),
            'icon' => esc_attr('global_styles'),
            'title' => esc_html__('Change Global Styles and Settings', 'tenweb-builder'),
            'description' => wp_kses_post(__('Easily change the entire website’s colors <br>and fonts all at once.', 'tenweb-builder') ),
            'buttons' => array(
                'class' => esc_attr('twbb-tour-guide__buttons twbb-tour-guide-row'),
                'buttonLeft' => array(
                    'text' => esc_html__('Back', 'tenweb-builder'),
                    'classes' => esc_attr('twbb-tour-guide__button_small twbb-tour-guide__button_grey twbb-tour-guide__button-back'),
                ),
                'buttonRight' => array(
                    'text' => esc_html__('Next', 'tenweb-builder'),
                    'classes' => esc_attr('twbb-tour-guide__button_small twbb-tour-guide__button_black twbb-tour-guide__button-next'),
                ),
            ),
            'actionFunction' => 'globalStyles',
        );
        $step_publish_website = array(
            'video_link' => esc_url('https://10-web-for-upload.s3.eu-central-1.amazonaws.com/builder-plugin-files/step_publish.mp4'),
            'poster_link' => esc_url('https://10-web-for-upload.s3.eu-central-1.amazonaws.com/builder-plugin-files/poster_publish.jpg'),
            'icon' => esc_attr('publish_website'),
            'title' => esc_html__('Publish your website', 'tenweb-builder'),
            'description' => wp_kses_post(__('Publish your changes and see it live.<br>You can always save your changes as<br>a draft to edit them later.', 'tenweb-builder') ),
            'buttons' => array(
                'class' => esc_attr('twbb-tour-guide__buttons twbb-tour-guide-row'),
                'buttonLeft' => array(
                    'text' => esc_html__('Back', 'tenweb-builder'),
                    'classes' => esc_attr('twbb-tour-guide__button_small twbb-tour-guide__button_grey twbb-tour-guide__button-back'),
                ),
                'buttonRight' => array(
                    'text' => esc_html__('Done', 'tenweb-builder'),
                    'classes' => esc_attr('twbb-tour-guide__button_small twbb-tour-guide__button_black twbb-tour-guide__button-done'),
                ),
            ),
            'actionFunction' => 'publishWebsite',
        );
        $step_section_generation = array(
            'video_link' => esc_url('https://10-web-for-upload.s3.eu-central-1.amazonaws.com/builder-plugin-files/step_section_generation.mp4'),
            'poster_link' => esc_url('https://10-web-for-upload.s3.eu-central-1.amazonaws.com/builder-plugin-files/poster_section_generation.png'),
            'icon' => esc_attr('section_generation'),
            'title' => esc_html__('Add a new section', 'tenweb-builder'),
            'description' => wp_kses_post(__('Generate a new section with AI by simply<br>describing what you need, or add one
<br>from the list of predesigned sections.', 'tenweb-builder') ),
            'buttons' => array(
                'class' => esc_attr('twbb-tour-guide__buttons twbb-tour-guide-row'),
                'buttonLeft' => array(
                    'text' => '',
                    'classes' => esc_attr('twbb-button-invisible'),
                ),
                'buttonRight' => array(
                    'text' => esc_html__('Next', 'tenweb-builder'),
                    'classes' => esc_attr('twbb-tour-guide__button_small twbb-tour-guide__button_black twbb-tour-guide__button-next'),
                ),
            ),
            'actionFunction' => 'sectionGeneration',
        );
        if( \Tenweb_Builder\Modules\ElementorKit\ElementorKit::isUltimateKitActive() ) {

            $step_quick_edit = array(
                'video_link' => esc_url('https://10-web-for-upload.s3.eu-central-1.amazonaws.com/builder-plugin-files/step_text_editor.mp4'),
                'poster_link' => esc_url('https://10-web-for-upload.s3.eu-central-1.amazonaws.com/builder-plugin-files/poster_text_editor.jpg'),
                'icon' => esc_attr('quick_edit'),
                'title' => esc_html__('Quick edit', 'tenweb-builder'),
                'description' => wp_kses_post(__('Easily customize the text and styles <br>of the selected section.', 'tenweb-builder') ),
                'buttons' => array(
                    'class' => esc_attr('twbb-tour-guide__buttons twbb-tour-guide-row'),
                    'buttonLeft' => array(
                        'text' => esc_html__('Back', 'tenweb-builder'),
                        'classes' => esc_attr('twbb-tour-guide__button_small twbb-tour-guide__button_grey twbb-tour-guide__button-back'),
                    ),
                    'buttonRight' => array(
                        'text' => esc_html__('Next', 'tenweb-builder'),
                        'classes' => esc_attr('twbb-tour-guide__button_small twbb-tour-guide__button_black twbb-tour-guide__button-next'),
                    ),
                ),
                'actionFunction' => 'quickEdit',
            );

            $this->tourContentVariables = array(
                'all_steps_count' => 7,
                '0' => $step_0,
                '1' => $step_section_generation,
                '2' => $step_quick_edit,
                '3' => $step_container_section,
                '4' => $step_visual_element,
                '5' => $step_responsiveness,
                '6' => $step_global_styles,
                '7' => $step_publish_website,
            );
        } else {
            $step_quick_edit = array(
                'video_link' => esc_url('https://10-web-for-upload.s3.eu-central-1.amazonaws.com/builder-plugin-files/step_text_editor.mp4'),
                'poster_link' => esc_url('https://10-web-for-upload.s3.eu-central-1.amazonaws.com/builder-plugin-files/poster_text_editor.jpg'),
                'icon' => esc_attr('quick_edit'),
                'title' => esc_html__('Quick edit', 'tenweb-builder'),
                'description' => wp_kses_post(__('Easily customize the text and styles <br>of the selected section.', 'tenweb-builder') ),
                'buttons' => array(
                    'class' => esc_attr('twbb-tour-guide__buttons twbb-tour-guide-row'),
                    'buttonLeft' => array(
                        'text' => '',
                        'classes' => esc_attr('twbb-button-invisible'),
                    ),
                    'buttonRight' => array(
                        'text' => esc_html__('Next Step', 'tenweb-builder'),
                        'classes' => esc_attr('twbb-tour-guide__button_small twbb-tour-guide__button_black twbb-tour-guide__button-next'),
                    ),
                ),
                'actionFunction' => 'quickEdit',
            );

            $this->tourContentVariables = array(
                'all_steps_count' => 5,
                '0' => $step_0,
                '1' => $step_quick_edit,
                '2' => $step_container_section,
                '3' => $step_visual_element,
                '4' => $step_responsiveness,
                '5' => $step_publish_website,
            );
        }
    }

    private function process() {
        if( $this->builderTourVisibility ) {
            $this->setTourContentVariables();
            $this->run();
        }
    }

    public static function getInstance() {
        if ( self::$instance === null ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

}