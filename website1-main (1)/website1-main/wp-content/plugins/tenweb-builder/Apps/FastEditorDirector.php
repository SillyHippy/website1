<?php
namespace Tenweb_Builder\Apps;

class FastEditorDirector extends BaseApp
{
    public $widgetsList;
    public $toolsList;

    private $toolsInstances;

    private $localized_data = array();

    protected static $instance = null;

    public static function getInstance() {
        if ( self::$instance === null ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct(){
        if( $this->visibilityCheck() ) {
            $this->includeFastEditorApp();
            add_action( 'elementor/init', array($this, 'process') , 11 );
        }
    }

    private function includeFastEditorApp() {
        require_once(TWBB_DIR . '/Apps/FastEditor/Tools/FastEditorTool.php');
        require_once(TWBB_DIR . '/Apps/FastEditor/Widgets/BaseWidgetFastEditor.php');
        $toolFiles = glob(TWBB_DIR . '/Apps/FastEditor/Tools' . '/*.php');
        foreach ($toolFiles as $file) {
            require_once($file); //phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingVariable
        }
        $widgetFiles = glob(TWBB_DIR . '/Apps/FastEditor/Widgets' . '/*.php');
        foreach ($widgetFiles as $file) {
            require_once($file); //phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingVariable
        }
    }


    public function process() {
        $this->setWidgetsList();
        $this->setToolsList();
        $this->setToolsInstances();
        $this->runFastEditor();
        $this->enqueueToolsScripts();
    }

    public function enqueueToolsEditorScripts() {
        $this->collectLocalizedData();
        if ( TWBB_DEV === TRUE ) {
            if (TWBB_DEBUG === TRUE) {
                $this->enqueueCommonScripts();
                $this->enqueueEditorMainScripts();
                foreach ($this->toolsInstances as $tool) {
                    $tool->editorScripts();
                }
            }
        } else {
            wp_enqueue_script('twbb-tools-editor-min-script', TWBB_URL . '/Apps/FastEditor/assets/scripts/editor-fast_editing.min.js', ['jquery','twbb-fe-helper-script'], TWBB_VERSION, TRUE);
            wp_enqueue_style( 'twbb-tools-editor-min-style', TWBB_URL . '/Apps/FastEditor/assets/styles/editor-fast_editing.min.css', array(), TWBB_VERSION );
            wp_localize_script( 'twbb-tools-editor-min-script', 'twbb_fe_localized_data', $this->localized_data );
        }
    }

    public function enqueueEditorMainScripts() {
        wp_enqueue_script( 'twbb-fe-main-script', TWBB_URL . '/Apps/FastEditor/assets/scripts/main_editor.js', [ 'jquery' ], TWBB_VERSION, TRUE );
    }

    public function enqueueToolsFrontendScripts() {
        $this->collectLocalizedData();
        if ( TWBB_DEV === TRUE ) {
            if (TWBB_DEBUG === TRUE) {
                $this->enqueueCommonScripts();
                $this->enqueueFrontendMainScripts();
                foreach ($this->toolsInstances as $tool) {
                    $tool->frontendScripts();
                }
            }
        } else {
            //TODO create separate function for enqueing libs
            if ( defined('ELEMENTOR_ASSETS_URL') ) {
                $assets_url = ELEMENTOR_ASSETS_URL;
                wp_enqueue_script(
                    'pickr_el',
                    "{$assets_url}lib/pickr/pickr.min.js",
                    [],
                    '1.5.0'
                );
                wp_enqueue_style(
                    'pickr_el',
                    "{$assets_url}lib/pickr/themes/monolith.min.css",
                    [],
                    '1.5.0'
                );
            }
            wp_enqueue_script('twbb-tools-frontend-min-script', TWBB_URL . '/Apps/FastEditor/assets/scripts/frontend-fast_editing.min.js', ['jquery', 'pickr_el'], TWBB_VERSION, TRUE);
            wp_localize_script( 'twbb-tools-frontend-min-script', 'twbb_fe_localized_data', $this->localized_data );
            wp_localize_script('twbb-tools-frontend-min-script', 'twbb_write_with_ai_data', $this->localized_data['twbb_write_with_ai_data']);
        }
    }

    public function enqueueToolsFrontendStyles() {
        if ( TWBB_DEV === TRUE ) {
            if (TWBB_DEBUG === TRUE) {
                $this->enqueueFrontendMainStyles();
                foreach ($this->toolsInstances as $tool) {
                    $tool->frontendStyles();
                }

                $this->enqueueMainStyles();
            }
        } else {
            wp_enqueue_style( 'twbb-tools-frontend-min-style', TWBB_URL . '/Apps/FastEditor/assets/styles/frontend-fast_editing.min.css', array(), TWBB_VERSION );
        }
    }

    private function collectLocalizedData() {
        foreach ($this->toolsInstances as $tool) {
            $data = $tool->getLocalizedData();
            if ( is_array($data) ) {
                $this->localized_data = array_merge($this->localized_data, $data);
            }
        }
    }

    private function collectPerWidgetLocalizedData($data) {
        $this->localized_data = array_merge($this->localized_data, $data);
    }

    private function enqueueCommonScripts() {
        wp_enqueue_script( 'twbb-fe-setting-value-lib', TWBB_URL . '/Apps/FastEditor/assets/scripts/lib/setting_value.js', [ 'twbb-fe-tool-script' ], TWBB_VERSION, TRUE );
        wp_enqueue_script( 'twbb-fe-helper-script', TWBB_URL . '/Apps/FastEditor/assets/scripts/FastEditorHelper.js', [ 'jquery' ], TWBB_VERSION, TRUE );
        wp_localize_script( 'twbb-fe-helper-script', 'twbb_fe_localized_data', $this->localized_data );
    }

    private function enqueueMainStyles() {
        wp_enqueue_style( 'twbb-fe-main-style', TWBB_URL . '/Apps/FastEditor/assets/styles/main_frontend.css', array(), TWBB_VERSION );
    }

    private function enqueueFrontendMainStyles() {
        wp_enqueue_style( 'twbb-fe-main-style', TWBB_URL . '/Apps/FastEditor/assets/styles/main_frontend.css', array(), TWBB_VERSION );
    }

    private function enqueueFrontendMainScripts() {
        wp_enqueue_script( 'twbb-fe-main-script', TWBB_URL . '/Apps/FastEditor/assets/scripts/main_frontend.js', [ 'jquery' ], TWBB_VERSION, TRUE );
        wp_enqueue_script( 'twbb-fe-tool-script', TWBB_URL . '/Apps/FastEditor/assets/scripts/fe_tool_frontend.js', [ 'jquery' ], TWBB_VERSION, TRUE );
    }

    private function enqueueToolsScripts() {
        add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'enqueueToolsEditorScripts' ] );
        add_action( 'elementor/preview/enqueue_scripts', [ $this, 'enqueueToolsFrontendScripts' ] );
        add_action( 'elementor/preview/enqueue_styles', [ $this, 'enqueueToolsFrontendStyles' ] );
    }

    private function runFastEditor() {
        foreach ($this->widgetsList as $widget) {
            $widgetWithFE = new $widget();
            $widgetWithFE->process();
            $this->collectPerWidgetLocalizedData($widgetWithFE->getWidgetLocalizedData());
        }
    }

    private function setWidgetsList() {
        $this->widgetsList = array(
            '\Tenweb_Builder\FastEditor\Widgets\HeadingFastEditor',
            '\Tenweb_Builder\FastEditor\Widgets\TextEditorFastEditor',
            '\Tenweb_Builder\FastEditor\Widgets\ImageFastEditor',
            '\Tenweb_Builder\FastEditor\Widgets\ImageBoxFastEditor',
            '\Tenweb_Builder\FastEditor\Widgets\IconBoxFastEditor',
            '\Tenweb_Builder\FastEditor\Widgets\TestimonialFastEditor',
            '\Tenweb_Builder\FastEditor\Widgets\ProgressBarFastEditor',
            '\Tenweb_Builder\FastEditor\Widgets\TextPathFastEditor',
            '\Tenweb_Builder\FastEditor\Widgets\AlertFastEditor',
            '\Tenweb_Builder\FastEditor\Widgets\VideoFastEditor',
            '\Tenweb_Builder\FastEditor\Widgets\ButtonFastEditor',
            '\Tenweb_Builder\FastEditor\Widgets\SpacerFastEditor',
            '\Tenweb_Builder\FastEditor\Widgets\GoogleMapsFastEditor',
            '\Tenweb_Builder\FastEditor\Widgets\CounterFastEditor',
            '\Tenweb_Builder\FastEditor\Widgets\ImageCarouselFastEditor',
            '\Tenweb_Builder\FastEditor\Widgets\BasicGalleryFastEditor',
            '\Tenweb_Builder\FastEditor\Widgets\BlockquoteFastEditor',
            '\Tenweb_Builder\FastEditor\Widgets\DividerFastEditor',
            '\Tenweb_Builder\FastEditor\Widgets\IconListFastEditor',
            '\Tenweb_Builder\FastEditor\Widgets\IconFastEditor',
            '\Tenweb_Builder\FastEditor\Widgets\NestedAccordionFastEditor',
            '\Tenweb_Builder\FastEditor\Widgets\AccordionFastEditor',
            '\Tenweb_Builder\FastEditor\Widgets\TabsFastEditor',
            '\Tenweb_Builder\FastEditor\Widgets\ToggleFastEditor',
            '\Tenweb_Builder\FastEditor\Widgets\NestedTabsFastEditor',
            //'\Tenweb_Builder\FastEditor\Widgets\PriceListFastEditor',
            '\Tenweb_Builder\FastEditor\Widgets\ContainerFastEditor',
            '\Tenweb_Builder\FastEditor\Widgets\SocialIconsFastEditor',
            '\Tenweb_Builder\FastEditor\Widgets\DefaultWidgetFastEditor',
        );
    }

    private function setToolsList() {
        $this->toolsList = array(
            '\Tenweb_Builder\FastEditor\Tools\FontSizeTool',
            '\Tenweb_Builder\FastEditor\Tools\WriteWithAITool',
            '\Tenweb_Builder\FastEditor\Tools\DeleteTool',
            '\Tenweb_Builder\FastEditor\Tools\DuplicateTool',
            '\Tenweb_Builder\FastEditor\Tools\MoreTool',
            '\Tenweb_Builder\FastEditor\Tools\FontFamilyTool',
            '\Tenweb_Builder\FastEditor\Tools\ColorPickerTool',
            '\Tenweb_Builder\FastEditor\Tools\ClickTool',
            '\Tenweb_Builder\FastEditor\Tools\FontStyleTool',
            '\Tenweb_Builder\FastEditor\Tools\VideoTool',
            '\Tenweb_Builder\FastEditor\Tools\URLTool',
            '\Tenweb_Builder\FastEditor\Tools\CountControlTool',
            '\Tenweb_Builder\FastEditor\Tools\DropdownSelectTool',
            '\Tenweb_Builder\FastEditor\Tools\ChoiceTypeTool',
            '\Tenweb_Builder\FastEditor\Tools\ShapeTool',
        );
    }

    private function setToolsInstances() {
        foreach ( $this->toolsList as $tool ) {
            $this->toolsInstances[] = new $tool([]);
        }
    }

    private static function visibilityCheck(){
        if ( get_option('elementor_experiment-fast_editing_tools') !== 'inactive' ) {
            return TRUE;
        }
        return FALSE;
    }


}