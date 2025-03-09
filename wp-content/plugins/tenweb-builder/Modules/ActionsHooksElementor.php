<?php
namespace Tenweb_Builder\Modules;

use Tenweb_Builder\Condition;
use Tenweb_Builder\DynamicTags;
use Tenweb_Builder\ElementorPro;
use Tenweb_Builder\External;
use Tenweb_Builder\Modules\QuickNavigation;
use Tenweb_Builder\Templates;
use \Tenweb_Builder\Apps\ThemeCustomize;


class ActionsHooksElementor {
    private $widgetsList = array();
    private $groupWidgetsList = array();
    private $customOptions = array();
    private $editorVersion2 = false;

    public function __construct()
    {
        $this->setVariables();
        $this->registerHooks();
    }

    private function setVariables()
    {
        $this->widgetsList = twbb_get_widgets();
        $this->groupWidgetsList = twbb_get_group_widgets();
        if ( ! defined( 'ELEMENTOR_PRO_VERSION' ) ) {
            $this->customOptions = get_custom_options();
        }

        if ( get_option('elementor_experiment-editor_v2') === 'active' ) {
            $this->editorVersion2 = true;
        }
    }

    private function registerHooks()
    {
        add_action( 'elementor/elements/categories_registered', array( $this, 'registerWidgetCategory' ), 9 );
        add_action( 'elementor/widgets/register', array( $this, 'registerWidgets' ), 10 );
        add_action( 'elementor/controls/controls_registered', array( $this, 'registerCustomOptions' ), 10 );

        add_action( 'elementor/frontend/after_register_styles', array( $this, 'enqueueFrontendStyles' ) );
        /* wp_footer action's third parameter need to be elementor's 'wp_footer' actions third parameter +1 */
        add_action( 'wp_footer', array( $this, 'enqueueFrontendScripts' ), 12 );
        add_action('elementor/editor/v2/scripts/enqueue/after', function () {
            wp_enqueue_script('twbb-editor-scripts-v2', TWBB_URL . '/assets/editor/js/editor_v2.js', ['jquery','elementor-editor-loader-v2','elementor-editor-environment-v2'], TWBB_VERSION, TRUE);
        }, 12);
        /* @TODO FIRES AFTER ELEMENTOR EDITOR STYLES AND SCRIPTS ARE ENQUEUED. */
        //fires after elementor editor styles and scripts are enqueued.
        add_filter( 'tw_get_elementor_assets', array( $this, 'registerElementorAssets' ) );
        add_action( 'elementor/editor/after_enqueue_styles', array( $this, 'enqueueEditorStyles' ) );
        add_action( 'elementor/editor/before_enqueue_scripts', array( $this, 'enqueueEditorScripts' ) );

        add_action( 'elementor/init', array( $this, 'elementorInit' ) );
        add_action( 'admin_init', array( $this, 'hideElementorUpgradeNotice' ) );

        add_filter( 'elementor/widget/render_content', array( $this, 'removePoweredBy' ), 10, 3 );
        add_filter( 'elementor/widget/render_content', array( $this, 'removeMadeBy10web' ), 10, 3 );
        /* do_action is called in demo plugin */
        add_filter( 'twbb_domain_init', array( $this, 'initDomain' ), 10, 2 );
        add_action( 'elementor/editor/footer', array($this, 'initDomain') );

        add_action( 'wp_ajax_twbb_mergeUltimateKit_kit', array($this, 'mergeUltimateKit') );

        add_filter('twbb_theme_customize_init', array($this, 'initCustomize'), 10, 2);
        add_action('elementor/editor/footer', array($this, 'initCustomize'));

        add_action('elementor/controls/controls_registered', function ($controls_manager) {
            require_once(TWBB_DIR . '/controls/draggable-order/controller.php');
            $controls_manager->register(new \Tenweb_Builder\Controls\DraggableOrderControl\DraggableOrderControl());
        });
    }


    /**
     * @param $elements_manager \Elementor\Elements_Manager
     * */
    public function registerWidgetCategory( $elements_manager ) {
        $company_name = '10Web ';
        if ( TENWEB_WHITE_LABEL ) {
            $company_name = '';
        }
        $elements_manager->add_category(
            'tenweb-widgets',
            [
                'title' => __( $company_name . 'Premium widgets', 'tenweb-builder' ),
                'icon'  => 'fa fa-plug',
            ]
        );
        $elements_manager->add_category(
            'tenweb-plugins-widgets',
            [
                'title' => __( '10WEB Plugins', 'tenweb-builder' ),
                'icon'  => 'fa fa-plug',
            ]
        );
        /* show sections only on template page! */
        if (Templates::get_instance()->is_elementor_template_type()) {
            $elements_manager->add_category(
                'tenweb-builder-widgets',
                [
                    'title' => __('Site Builder Widgets' . $company_name, 'tenweb-builder'),
                    'icon' => 'fa fa-plug',
                ]
            );
            $elements_manager->add_category(
                'tenweb-woocommerce-builder-widgets',
                [
                    'title' => __('Woocommerce Builder Widgets' . $company_name, 'tenweb-builder'),
                    'icon' => 'fa fa-plug',
                ]
            );
        }
        $elements_manager->add_category(
            'tenweb-woocommerce-widgets',
            [
                'title' => __( 'Woocommerce Widgets' . $company_name, 'tenweb-builder' ),
                'icon'  => 'fa fa-plug',
            ]
        );

        $this->reorderWidgetCategories($elements_manager);

    }

    /**
    * Function sort widgets categories Woocommerce first if 10web template type is product or product archive
     *
     * @param $elements_manager \Elementor\Elements_Manager
    */
    public function reorderWidgetCategories($elements_manager) {
        // Get all registered categories
        $categories = $elements_manager->get_categories();
        global $post;
        if ( \Elementor\Plugin::$instance->editor->is_edit_mode() && $post->post_type === 'elementor_library' ) {
            // Get the Elementor template type meta
            $template_type_meta = get_post_meta( $post->ID, '_elementor_template_type', true );

            if ( $template_type_meta === 'twbb_single_product' || $template_type_meta === 'twbb_archive_products' ) {
                $priority_category1 = $categories['tenweb-woocommerce-widgets'];
                unset($categories['tenweb-woocommerce-widgets']); // Remove it from the list
                $priority_category = $categories['tenweb-woocommerce-builder-widgets'];
                unset($categories['tenweb-woocommerce-builder-widgets']); // Remove it from the list


                // Reorder categories: Place the target category at the beginning
                $categories = ['tenweb-woocommerce-widgets' => $priority_category1] + $categories;
                $categories = ['tenweb-woocommerce-builder-widgets' => $priority_category] + $categories;
            } else {
                $priority_category1 = $categories['tenweb-builder-widgets'];
                unset($categories['tenweb-builder-widgets']); // Remove it from the list
                $priority_category = $categories['tenweb-widgets'];
                unset($categories['tenweb-widgets']); // Remove it from the list
                $priority_category2 = $categories['tenweb-plugins-widgets'];
                unset($categories['tenweb-plugins-widgets']); // Remove it from the list
                // Reorder categories: Place the target category at the beginning
                $categories = ['tenweb-builder-widgets' => $priority_category1] + $categories;
                $categories = ['tenweb-widgets' => $priority_category] + $categories;
                $categories = ['tenweb-plugins-widgets' => $priority_category2] + $categories;
            }
            
            if ( class_exists( 'ReflectionClass' ) ) {
                // Overwrite the categories with the reordered array
                $reflection = new \ReflectionClass($elements_manager);
                $property = $reflection->getProperty('categories');
                $property->setAccessible(true);
                $property->setValue($elements_manager, $categories);
            }

        }
    }

    public function registerWidgets() {
        if ( defined( 'ELEMENTOR_PATH' ) && class_exists( 'Elementor\Widget_Base' ) ) {
            if ( ! empty( $this->widgetsList ) ) {
                $isExternal = FALSE;
                foreach ( $this->widgetsList as $widget_name => $widget_data ) {
                    if ( ! isset( $widget_data[ 'oninit' ] ) || ! $widget_data[ 'oninit' ] ) {
                        if ( isset( $widget_data[ 'external' ] ) && $widget_data[ 'external' ] ) {
                            if ( isset( $widget_data[ 'class_name' ] ) && ! class_exists( $widget_data[ 'class_name' ] ) ) {
                                $isExternal = TRUE;
                                require_once TWBB_DIR . '/widgets/external/external.php';
                                $external_widget = new External();
                                $external_widget->set( $widget_data );
                                \Elementor\Plugin::instance()->widgets_manager->register( $external_widget );
                            }
                        } else {
                            $file = TWBB_DIR . '/widgets/' . $widget_name . '/' . $widget_name . '.php';
                            if ( is_file( $file ) ) {
                                require_once $file;//phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingVariable
                            }
                        }
                    }
                }
                if ( $isExternal && class_exists( '\Tenweb_Manager\Manager' ) && is_admin() ) {
                    wp_enqueue_script( 'twbb-control-external-ajax', TWBB_URL . '/assets/editor/js/external-ajax.js', [ 'jquery' ], TWBB_VERSION );
                    $rest_route = add_query_arg( array(
                        'rest_route' => '/' . TENWEB_REST_NAMESPACE . '/action',
                    ), get_home_url() . "/" );
                    wp_localize_script( 'twbb-control-external-ajax', 'twbb', array(
                        'ajaxurl'          => admin_url( 'admin-ajax.php' ),
                        'ajaxnonce'        => wp_create_nonce( 'wp_rest' ),
                        'plugin_url'       => TENWEB_URL,
                        'action_endpoint'  => $rest_route,
                        'install_success'  => __( 'The plugin was successfully installed and activated. Please save your changes and reload the page for using the widget', 'tenweb-builder' ),
                        'activate_success' => __( 'The plugin was successfully activated. Please save your changes and reload the page for using the widget', 'tenweb-builder' ),
                        'update_success'   => __( 'The plugin was successfully updated. Please save your changes and reload the page for using the widget', 'tenweb-builder' ),
                        'reload_msg'       => __( 'Please save your changes and reload the page for using the widget', 'tenweb-builder' ),
                        'inprogress_msg'   => __( 'Some plugin is in the process of being activated or installed.', 'tenweb-builder' ),
                    ) );
                }
            }
        }

    }

    public function registerCustomOptions() {
        if ( defined( 'ELEMENTOR_PATH' ) && class_exists( 'Elementor\Widget_Base' ) ) {
            if ( ! empty( $this->customOptions ) ) {
                foreach ( $this->customOptions as $widget_name => $widget_data ) {
                    if ( ! isset( $widget_data[ 'oninit' ] ) || ! $widget_data[ 'oninit' ] ) {
                        $file = TWBB_DIR . '/widgets/' . $widget_name . '/' . $widget_name . '.php';
                        if ( is_file( $file ) ) {
                            require_once $file;//phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingVariable
                        }
                    }
                }
            }
        }
    }

    public function enqueueFrontendStyles() {
        if ( TWBB_DEV === FALSE ) {
            wp_enqueue_style( 'twbb-frontend-styles', TWBB_URL . '/assets/frontend/css/frontend.min.css', array(
                'elementor-frontend'
            ),                TWBB_VERSION );
            // Ensure the images remain visible while meta information is being generated after import.
	        //phpcs:ignore Squiz.PHP.CommentedOutCode.Found
            /*if ( get_option( 'tenweb_import_in_progress' ) ) {
                wp_add_inline_style( 'twbb-frontend-styles', 'img {width: initial !important; height: initial !important;}' );
            }*/
        }
        else {
            wp_enqueue_style( 'twbb-fonts', TWBB_URL . '/assets/frontend/css/fonts.css', array(), TWBB_VERSION );
            wp_enqueue_style( 'twbb-frontend-global-styles', TWBB_URL . '/assets/frontend/css/global_frontend.css', array(),TWBB_VERSION );
            $widgets = array_merge( $this->widgetsList, $this->customOptions );
            foreach ( $widgets as $widget_data ) {
                if ( empty( $widget_data[ 'styles' ] ) ) {
                    continue;
                }
                foreach ( $widget_data[ 'styles' ] as $handle => $style_data ) {
                    if ( is_array( $style_data ) ) {
                        $deps = array_merge($style_data['deps'], ['elementor-frontend']);
                        wp_enqueue_style( 'twbb-' . $handle . '-style', $style_data[ 'src' ], $deps, TWBB_VERSION );
                    } else {
                        wp_enqueue_style( $handle );
                    }
                }
            }
        }
        do_action( 'twbb_after_enqueue_styles' );
    }

    public function enqueueFrontendScripts() {
        // For post archive widget.
        wp_enqueue_script( 'underscore' );
        // Do not include admin scripts to front.
        if ( \Elementor\Plugin::instance()->preview->is_preview_mode() ) {
            $handle_editor = 'twbb-common-js';
            wp_enqueue_script( 'jquery-elementor-select2' );
            wp_enqueue_script( 'twbb-common-js', TWBB_URL . '/assets/common/js/common.js', [ 'jquery' ], TWBB_VERSION, TRUE );
            wp_enqueue_script( 'twbb-editor-helper-script', TWBB_URL . '/assets/editor/js/helper-script.js', array('jquery'), TWBB_VERSION, TRUE );
            wp_enqueue_script( 'twbb-condition-js', TWBB_URL . '/assets/editor/js/condition.js', [ 'jquery','twbb-editor-helper-script' ], TWBB_VERSION, TRUE );
            wp_enqueue_style( 'twbb-common', TWBB_URL . '/assets/common/css/common.css', array(), TWBB_VERSION );
            if ( $this->editorVersion2 ) {
                wp_enqueue_style( 'twbb-editor_v2', TWBB_URL . '/assets/editor/css/editor_v2.css', array(), TWBB_VERSION );
				//phpcs:ignore Squiz.PHP.CommentedOutCode.Found
                //wp_enqueue_script( 'twbb-editor-scripts-v2', TWBB_URL . '/assets/editor/js/editor_v2.js', [ 'jquery' ], TWBB_VERSION, TRUE );
            }
            else {
                wp_enqueue_style( 'twbb-editor_v1', TWBB_URL . '/assets/editor/css/editor_v1.css', array(), TWBB_VERSION );
            }
            wp_enqueue_style( 'twbb-condition', TWBB_URL . '/assets/editor/css/condition.css', array(), TWBB_VERSION );
            $rest_route         = add_query_arg( array( 'rest_route' => '/' ), get_home_url() . "/" );
            $twbb_template_type = Templates::get_instance()->is_twbb_template()[ 'template_type' ];
            $header_button      = Templates::get_instance()->is_twbb_template()[ 'header_button_show' ];
            $localizedArrayOptions = array(
                'loaded_templates'    => Templates::get_instance()->get_loaded_templates(),
                'post_id'             => get_the_ID(),
                'current_page'        => __( 'Current Page', 'tenweb-builder' ),
                'entire_site'         => __( 'Entire Site', 'tenweb-builder' ),
                'singular'            => __( 'Singular', 'tenweb-builder' ),
                'archive'             => __( 'Archive', 'tenweb-builder' ),
                'choose'              => __( 'Choose', 'tenweb-builder' ),
                'template'            => __( 'template', 'tenweb-builder' ),
                'twbb_page_type'      => Condition::get_instance()->get_page_type(),
                'edit'                => __( 'Edit', 'tenweb-builder' ),
                'edit_localy'         => __( 'Edit Localy', 'tenweb-builder' ),
                'edit_url'            => admin_url( 'post.php?post={post_id}&action=elementor' ),
                'popup_template_ajax' => add_query_arg( array( 'action' => 'popup_template_ajax' ), admin_url( 'admin-ajax.php' ) ),
                'is_post_template'    => ( get_post_type( get_the_ID() ) === 'elementor_library' ? 1 : 0 ),
                'header_button'       => $header_button,
                'twbb_header'         => __( 'Edit Header Template', 'tenweb-builder' ),
                'twbb_footer'         => __( 'Edit Footer Template', 'tenweb-builder' ),
                'twbb_single'         => __( 'Edit Single Template', 'tenweb-builder' ),
                'twbb_single_product' => __( 'Edit Single Product Template', 'tenweb-builder' ),
                'twbb_archive'        => __( 'Edit Archive Template', 'tenweb-builder' ),
                'twbb_archive_products'        => __( 'Edit Archive Products Template', 'tenweb-builder' ),
                'twbb_template_type'  => $twbb_template_type,
                'plugin_url'          => plugin_dir_url( __FILE__ ),
                'editor_v2'           => $this->editorVersion2,
                'dashboard_url'       => esc_url(self::checkManagerExistence()['dashboard_url']),
                'smart_scale_option'  => get_option('elementor_experiment-smart_scale'),
            );
            wp_localize_script( $handle_editor, 'twbb_options', $localizedArrayOptions );
            wp_localize_script( $handle_editor, 'twbb_editor', array(
                'texts'              => array(
                    'include'           => __( 'Include', 'tenweb-builder' ),
                    'exclude'           => __( 'Exclude', 'tenweb-builder' ),//phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude
                    'general'           => __( 'Entire Site', 'tenweb-builder' ),
                    'archive'           => __( 'Archive', 'tenweb-builder' ),
                    'singular'          => __( 'Singular', 'tenweb-builder' ),
                    'are_your_sure'     => __( 'Are you sure?', 'tenweb-builder' ),
                    'condition_removed' => __( 'A condition has been removed.', 'tenweb-builder' ),
                    'content_missing'   => __( 'Warning: There are no content widgets in this Single template. Please make sure to add some.', 'tenweb-builder' ),
                    'publish'           => __( 'Publish', 'tenweb-builder' ),
                    'continue'          => __( 'Continue', 'tenweb-builder' ),
                ),
                'ajax_url'           => admin_url( 'admin-ajax.php' ),
                'rest_route'         => $rest_route,
                'rest_nonce'         => wp_create_nonce( 'wp_rest' ),
                'post_id'            => get_the_ID(),
                'conditions'         => Condition::get_instance()->get_template_condition( get_the_ID(), 'all', TRUE ),
                'twbb_template_type' => $twbb_template_type,
                'page_permalink'     => home_url(),
                'template_preview_nonce'    => SectionGeneration\TemplatePreview::getInstance()->getNonce()
            ) );

            wp_enqueue_script( 'twbb-editor-helper-script', TWBB_URL . '/assets/editor/js/helper-script.js', array('jquery'), TWBB_VERSION, TRUE );
            wp_localize_script( 'twbb-editor-helper-script', 'twbb_helper', array(
                    'domain_id' => get_option('tenweb_domain_id'))
            );
        }
        $handle_frontend = 'twbb-frontend-scripts';
        $frontend_dependency = [
            'elementor-frontend-modules',
            'imagesloaded',
            'masonry',
        ];
        if ( class_exists('woocommerce') ) {
            array_push($frontend_dependency, 'wc-cart-fragments' );
        }
        if ( TWBB_DEV === FALSE ) {
            wp_enqueue_script( 'twbb-frontend-scripts', TWBB_URL . '/assets/frontend/js/frontend.min.js',
                $frontend_dependency,                 TWBB_VERSION );
            if ( ! defined( 'ELEMENTOR_PRO_VERSION' ) ) {
                wp_enqueue_script( 'twbb-custom-options-frontend-scripts',
                    TWBB_URL . '/assets/frontend/js/custom_options_frontend.min.js',
                    array(),
                    TWBB_VERSION
                );
            }
        } else {
            $handle_frontend = 'twbb-posts-scripts';
            $widgets         = array_merge( $this->widgetsList, $this->customOptions );
            foreach ( $widgets as $widget_data ) {
                if ( empty( $widget_data[ 'scripts' ] ) ) {
                    continue;
                }
                foreach ( $widget_data[ 'scripts' ] as $handle => $script_data ) {
                    wp_enqueue_script( 'twbb-' . $handle . '-scripts', $script_data[ 'src' ], $script_data[ 'deps' ], TWBB_VERSION, TRUE );
                }
            }
        }
        $twbb_script_localize = [
            'ajaxurl'  => admin_url( 'admin-ajax.php' ),
            'home_url' => home_url(),
            'nonce'    => wp_create_nonce( 'twbb' ),
            'tenweb_dashboard' => TENWEB_DASHBOARD,
            'swiper_latest' => get_option("elementor_experiment-e_swiper_latest"),
            'woocommerce' => array(
                //option is set from widget options
                'add_to_cart' => __(get_option('twbb_custom_woocommerce_add_to_cart_text', 'Add to cart'), 'tenweb-builder'),
                'added' => __('Added', 'tenweb-builder'),
            ),
        ];
        if ( is_user_logged_in() ) {
            $twbb_script_localize['dashboard_website_id'] = get_option('tenweb_domain_id');
        }
        wp_localize_script( $handle_frontend, 'twbb', $twbb_script_localize );

        do_action( 'twbb_after_enqueue_scripts', $handle_frontend );
        if ( \Elementor\Plugin::instance()->preview->is_preview_mode() ) {
            $structure = new QuickNavigation();
            $structure->twbb_template_popup();
            if ( $this->editorVersion2 ) {
                $structure->websiteStructure();
            } else {
                $structure->twbb_custom_header(self::checkManagerExistence());
            }
        }
        if ( ! TENWEB_WHITE_LABEL ) {
            /* remove 'Edit with Elementor' from admin bar */
            wp_dequeue_script( 'elementor-admin-bar' );
        }
    }

    public function registerElementorAssets( $assets ) {
        $version = '2.0.2';
        if ( ! isset( $assets[ 'version' ] ) || version_compare( $assets[ 'version' ], $version ) === - 1 ) {
            $assets[ 'version' ]  = $version;
            $assets[ 'css_path' ] = TWBB_URL . '/assets/frontend/css/fonts.css';
        }

        return $assets;
    }

    public function enqueueEditorStyles() {
        $handle_for_old_version = "";
        if ( TWBB_DEV === FALSE ) {
            wp_enqueue_style( 'twbb-admin-styles', TWBB_URL . '/assets/editor/css/combined-editor.min.css', array(), TWBB_VERSION );
            $handle_for_old_version = "twbb-admin-styles";
        } else {
            $handle_for_old_version = "twbb-common";
            $key = 'twbb-editor-styles';
            wp_deregister_style( $key );
            $assets = apply_filters( 'tw_get_elementor_assets', array() );
            wp_enqueue_style( $key, $assets[ 'css_path' ], array(), $assets[ 'version' ] );
            wp_enqueue_style( 'twbb-el-editor-styles', TWBB_URL . '/assets/editor/css/editor.css', array(), TWBB_VERSION );
            wp_enqueue_style( 'twbb-condition', TWBB_URL . '/assets/editor/css/condition.css', array(), TWBB_VERSION );
            wp_enqueue_style( 'twbb-common', TWBB_URL . '/assets/common/css/common.css', array(), TWBB_VERSION );
            wp_enqueue_style( 'twbb-editor-global-styles', TWBB_URL . '/assets/frontend/css/global_frontend.css', array(),TWBB_VERSION );
        }

        // Compatibility with Font awesome 5, remove once Elementor deprecates fa4.
        wp_enqueue_style( 'font-awesome-5-all', self::getFaAssetUrl( 'all' ), array(), ELEMENTOR_VERSION );

        if(defined('ELEMENTOR_VERSION') && version_compare(ELEMENTOR_VERSION, '3.12.0') === -1) {
            wp_add_inline_style($handle_for_old_version, "#elementor-panel #elementor-panel-content-wrapper {top: 60px !important;}");
        }

        if ( TENWEB_WHITE_LABEL ) {
            wp_enqueue_style( 'twbb-white-label', TWBB_URL . '/assets/common/css/white_label.css', array(), TWBB_VERSION );
        }

    }

    public function enqueueEditorScripts() {
        if ( TWBB_DEV === FALSE ) {
            $requirements = array(
                'jquery',
                'backbone-marionette',
                'elementor-common-modules',
                'elementor-common',
                'elementor-editor-modules',
                'elementor-editor-document'
            );
            if ( TWBB_DEBUG === TRUE ) {
                wp_enqueue_script( 'twbb-editor-scripts', TWBB_URL . '/assets/editor/js/editor-tenweb.js', $requirements, TWBB_VERSION, TRUE );
            } else {
                wp_enqueue_script( 'twbb-editor-scripts', TWBB_URL . '/assets/editor/js/editor-tenweb.min.js', $requirements, TWBB_VERSION, TRUE );
            }
        } else {
            foreach ( $this->widgetsList as $widget_data ) {
                if ( empty( $widget_data[ 'admin-scripts' ] ) ) {
                    continue;
                }
                foreach ( $widget_data[ 'admin-scripts' ] as $handle => $script_data ) {
                    wp_enqueue_script( 'twbb-' . $handle . '-admin-scripts', $script_data[ 'src' ], $script_data[ 'deps' ], TWBB_VERSION, TRUE );
                }
            }
            wp_enqueue_script( 'twbb-editor-scripts', TWBB_URL . '/assets/editor/js/editor.js', [ 'jquery' ], TWBB_VERSION, TRUE );
            wp_enqueue_script( 'twbb-editor-helper-script', TWBB_URL . '/assets/editor/js/helper-script.js', array('jquery'), TWBB_VERSION, TRUE );
            wp_enqueue_script( 'twbb-condition-js', TWBB_URL . '/assets/editor/js/condition.js', [ 'jquery','twbb-editor-helper-script' ], TWBB_VERSION, TRUE );
            wp_enqueue_script( 'twbb-common-js', TWBB_URL . '/assets/common/js/common.js', [ 'jquery' ], TWBB_VERSION, TRUE );
            wp_enqueue_script( 'twbb-editor-helper-script', TWBB_URL . '/assets/editor/js/helper-script.js', array('jquery'), TWBB_VERSION, TRUE );
            wp_localize_script( 'twbb-editor-helper-script', 'twbb_helper', array(
                    'domain_id' => get_option('tenweb_domain_id'))
            );
            wp_enqueue_script( 'twbb-editor-ga-events-script', TWBB_URL . '/assets/editor/js/ga_events.js', array('jquery'), TWBB_VERSION, TRUE );
        }

        $rest_route         = add_query_arg( array( 'rest_route' => '/' ), get_home_url() . "/" );
        $twbb_template_type = Templates::get_instance()->is_twbb_template()[ 'template_type' ];
        $localizedArrayEditor = array(
            'texts'              => array(
                'include'           => __( 'Include', 'tenweb-builder' ),
                'exclude'           => __( 'Exclude', 'tenweb-builder' ),//phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude
                'general'           => __( 'Entire Site', 'tenweb-builder' ),
                'archive'           => __( 'Archive', 'tenweb-builder' ),
                'singular'          => __( 'Singular', 'tenweb-builder' ),
                'are_your_sure'     => __( 'Are you sure?', 'tenweb-builder' ),
                'condition_removed' => __( 'A condition has been removed.', 'tenweb-builder' ),
                'content_missing'   => __( 'Warning: There are no content widgets in this Single template. Please make sure to add some.', 'tenweb-builder' ),
                'publish'           => __( 'Publish', 'tenweb-builder' ),
                'continue'          => __( 'Continue', 'tenweb-builder' ),
            ),
            'ajax_url'           => admin_url( 'admin-ajax.php' ),
            'rest_route'         => $rest_route,
            'rest_nonce'         => wp_create_nonce( 'wp_rest' ),
            'post_id'            => get_the_ID(),
            'conditions'         => Condition::get_instance()->get_template_condition( get_the_ID(), 'all', TRUE ),
            'twbb_template_type' => $twbb_template_type,
            'page_permalink'     => home_url(),
            'template_preview_nonce'    => SectionGeneration\TemplatePreview::getInstance()->getNonce()
        );
        wp_localize_script( 'twbb-editor-scripts', 'twbb_editor', $localizedArrayEditor );
        wp_localize_script( 'twbb-editor-scripts', 'twbb_helper', array(
                'domain_id' => get_option('tenweb_domain_id'))
        );
        $edit_url         = admin_url( 'post.php?post={post_id}&action=elementor' );
        $is_post_template = ( get_post_type( get_the_ID() ) === 'elementor_library' ? 1 : 0 );
        $header_button    = Templates::get_instance()->is_twbb_template()[ 'header_button_show' ];
        if ( $this->editorVersion2 ) {
            wp_enqueue_style( 'twbb-editor_v2', TWBB_URL . '/assets/editor/css/editor_v2.css', array(), TWBB_VERSION );
            wp_enqueue_script( 'twbb-editor-scripts-v2', TWBB_URL . '/assets/editor/js/editor_v2.js', [ 'jquery' ], TWBB_VERSION, TRUE );
            wp_register_style('twbb-open-sans', 'https://fonts.googleapis.com/css?family=Open+Sans:300,400,500,600,700,800&display=swap');
        }
        else {
            wp_enqueue_style( 'twbb-editor_v1', TWBB_URL . '/assets/editor/css/editor_v1.css', array(), TWBB_VERSION );
        }
		//phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
        $is_ai_plan = in_array(self::checkManagerExistence()['subscription_id'], TW_AI_PLAN_IDS, true ) ? '1' : '0';
	    //phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
        $is_profesional_plan = in_array(self::checkManagerExistence()['subscription_id'], TW_PROFFESIONAL_IDS, true ) ? '1' : '0';
        $localizedArrayOptions = array(
            'ajaxurl'              => admin_url( 'admin-ajax.php' ),
            'nonce'                => wp_create_nonce( 'twbb' ),
            'loaded_templates'     => Templates::get_instance()->get_loaded_templates(),
            'rest_route'           => $rest_route,
            'rest_nonce'           => wp_create_nonce( 'wp_rest' ),
            'post_id'              => get_the_ID(),
            'edit_button_title'    => __( 'Edit Template', 'tenweb-builder' ),
            'teplate_popup_title'  => __( 'Choose templates for your web site', 'tenweb-builder' ),
            'current_page'         => __( 'Current Page', 'tenweb-builder' ),
            'entire_site'          => __( 'Entire Site', 'tenweb-builder' ),
            'singular'             => __( 'Singular', 'tenweb-builder' ),
            'archive'              => __( 'Archive', 'tenweb-builder' ),
            'choose'               => __( 'Choose', 'tenweb-builder' ),
            'template'             => __( 'template', 'tenweb-builder' ),
            'twbb_page_type'       => Condition::get_instance()->get_page_type(),
            'edit'                 => __( 'Edit', 'tenweb-builder' ),
            'edit_localy'          => __( 'Edit Locally', 'tenweb-builder' ),
            'edit_url'             => $edit_url,
            'edit_local_url'       => add_query_arg( array( 'action' => 'popup_template_ajax' ), admin_url( 'admin-ajax.php' ) ),
            'popup_template_draw'  => add_query_arg( array( 'action' => 'draw_popup' ), admin_url( 'admin-ajax.php' ) ),
            'page_title'           => get_the_title(),
            'is_post_template'     => $is_post_template,
            'header_button'        => $header_button,
            'plugin_url'           => plugin_dir_url( __FILE__ ),
            'remove_template_ajax' => add_query_arg( array( 'action' => 'remove_template_ajax' ), admin_url( 'admin-ajax.php' ) ),
            'twbb_header'          => __( 'Edit Header Template', 'tenweb-builder' ),
            'twbb_footer'          => __( 'Edit Footer Template', 'tenweb-builder' ),
            'twbb_single'          => __( 'Edit Single Template', 'tenweb-builder' ),
            'twbb_single_product'          => __( 'Edit Single Product Template', 'tenweb-builder' ),
            'twbb_archive'         => __( 'Edit Archive Template', 'tenweb-builder' ),
            'twbb_archive_products'        => __( 'Edit Archive Products Template', 'tenweb-builder' ),
            'twbb_template_type'   => $twbb_template_type,
            'editor_v2'           => $this->editorVersion2,
            'dashboard_url'       => esc_url( self::checkManagerExistence()['dashboard_url'] ),
            'wp_dashboard_url'    => esc_url(get_dashboard_url()),
            'dashboard_text'         => __( '10Web Dashboard', 'tenweb-builder' ),
            'request_developer_text'         => __( 'Request a developer', 'tenweb-builder' ),
            'request_developer_url'         => esc_url( TENWEB_DASHBOARD . '/websites/' . get_option('tenweb_domain_id') . '/professional' ),
            'builder_plugin_name'         => __( '10Web Builder', 'tenweb-builder' ),
            'display_conditions_text'         => __( 'Display Conditions', 'tenweb-builder' ),
            'is_tenweb_hosted' => TW_HOSTED_ON_10WEB,
            'is_ai_plan' => $is_ai_plan,
            'is_profesional_plan' => $is_profesional_plan,
            'twbb_imported_kit_ids' => get_option( 'twbb_imported_kit_ids' ),
            'twbb_imported_kit_typo_ids' => get_option( 'twbb_imported_kit_typo_ids' ),
            'add_page_link' => esc_url(add_query_arg(array('add_page' => 1), self::checkManagerExistence()['dashboard_url'])),
            'track_publish_ajax'  => add_query_arg( array( 'action' => 'track_publish_ajax' ), admin_url( 'admin-ajax.php' ) ),
            'track_publish_button'=> get_option("twbb_track_publish_button"),
            'show_ultimate_kit' => \Tenweb_Builder\Modules\ElementorKit\ElementorKit::isUltimateKitActive(),
            'restUrl' => esc_url_raw(rest_url('/wp/v2/pages')),
            'restNonce' => wp_create_nonce('wp_rest'),
            'adminUrl' => add_query_arg( array( 'action' => 'elementor' ), admin_url( 'post.php' ) ),
            'smart_scale_option'  => get_option('elementor_experiment-smart_scale'),
        );
        wp_localize_script( 'twbb-editor-scripts', 'twbb_options', $localizedArrayOptions );
        wp_localize_script( 'twbb-editor-scripts-v2', 'twbb_options', $localizedArrayOptions );
        do_action( 'twbb_after_enqueue_scripts' );
        do_action( 'twbb_before_enqueue_editor_scripts' );
    }

    public static function getFaAssetUrl( $filename, $ext_type = 'css', $add_suffix = TRUE ) {
        static $is_test_mode = NULL;
        if ( NULL === $is_test_mode ) {
            $is_test_mode = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG || defined( 'ELEMENTOR_TESTS' ) && ELEMENTOR_TESTS;
        }
        $url = ELEMENTOR_ASSETS_URL . 'lib/font-awesome/' . $ext_type . '/' . $filename;
        if ( ! $is_test_mode && $add_suffix ) {
            $url .= '.min';
        }

        return $url . '.' . $ext_type;
    }

    public static function checkManagerExistence() {
        /* Check is manager exists and is domain id in options */
        $manager_exist = false;
        $if_trial_user = false;
        $ai_created = false;
        $dashboard_url = false;
        $subscription_id = false;
        if ( class_exists( '\Tenweb_Manager\Manager' ) ) {
            $domain_id = get_site_option( TENWEB_PREFIX . '_domain_id' );
            $user_agreements_info = \Tenweb_Manager\Helper::get_tenweb_user_info()[ 'agreement_info'];
            if ( is_array($user_agreements_info) && !empty($user_agreements_info) ) {
                $subscription_id = $user_agreements_info['subscription_id'];
				//phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
                $if_trial_user = ($user_agreements_info['subscription_category'] == 'starter' && $user_agreements_info['hosting_trial_expire_date'] != '') ? true : false;
	            //phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
                $ai_created = (261 == $user_agreements_info['subscription_id'] || 211 == $user_agreements_info['subscription_id']);
            }
            if ( $domain_id ) {
                $manager_exist = true;
            }
            $dashboard_url = $manager_exist ? TENWEB_DASHBOARD . '/websites/'. $domain_id . '/ai-builder/' : 'https://my.10web.io/websites/';

        }

        return(compact('if_trial_user', 'ai_created', 'dashboard_url', 'subscription_id'));
    }

    public function elementorInit() {
        require_once TWBB_DIR . '/dynamic-tags/module.php';
        new DynamicTags\Module();
        require_once TWBB_DIR . '/pro-features/ElementorPro.php';
        ElementorPro\ElementorPro::get_instance();
        if ( defined( 'ELEMENTOR_PATH' ) && class_exists( 'Elementor\Widget_Base' ) ) {
            if ( ! empty( $this->widgetsList ) ) {
                $widgets = array_merge( $this->widgetsList, $this->customOptions );
                foreach ( $widgets as $widget_name => $widget_data ) {
                    if ( isset( $widget_data[ 'oninit' ] ) && $widget_data[ 'oninit' ] ) {
                        $file = TWBB_DIR . '/widgets/' . $widget_name . '/' . $widget_name . '.php';
                        if ( is_file( $file ) ) {
                            require_once $file;//phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingVariable
                        }
                    }
                }
            }
            if ( ! empty( $this->groupWidgetsList ) ) {
                foreach ( $this->groupWidgetsList as $module_name => $widget_data ) {
                    $file = TWBB_DIR . '/widgets/' . $module_name . '/module.php';
                    if ( is_file( $file ) ) {
                        require_once $file;//phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingVariable
                    }
                }
            }
        }
        $this->registerFastEditorToolsExperiment();
        $this->registerSectionsGenerationExperiment();
        $this->registerCoPilotExperiment();
        $this->registerSmartScalePreviewExperiment();
    }

    //TODO move all experiments to separate class, create parent class for all of them with common check availibility method
    private function registerCoPilotExperiment() {
        \Elementor\Plugin::instance()->experiments->add_feature( [
            'name' => 'co_pilot',
            'title' => esc_html__( '10Web Ai Co-Pilot', 'tenweb-builder' ),
            'description' => '',
            'release_status' => \Elementor\Core\Experiments\Manager::RELEASE_STATUS_ALPHA,
            'new_site' => [
                'default_active' => false,
                'minimum_installation_version' => '3.1.0-beta',
            ],
        ] );
    }

    private function registerFastEditorToolsExperiment() {
        \Elementor\Plugin::instance()->experiments->add_feature( [
            'name' => 'fast_editing_tools',
            'title' => esc_html__( 'Fast Editing Tools', 'tenweb-builder' ),
            'description' => esc_html__( 'Fast Editing Tools in Editor Preview part.', 'tenweb-builder' ),
            'release_status' => \Elementor\Core\Experiments\Manager::RELEASE_STATUS_BETA,
            'new_site' => [
                'default_active' => true,
                'minimum_installation_version' => '3.1.0-beta',
            ],
        ] );
    }

    private function registerSectionsGenerationExperiment() {
        \Elementor\Plugin::instance()->experiments->add_feature( [
            'name' => 'sections_generation',
            'title' => esc_html__( 'Sections Generation', 'tenweb-builder' ),
            'release_status' => \Elementor\Core\Experiments\Manager::RELEASE_STATUS_BETA,
            'new_site' => [
                'default_active' => true,
                'minimum_installation_version' => '3.1.0-beta',
            ],
        ] );
    }

    private function registerSmartScalePreviewExperiment() {
        \Elementor\Plugin::instance()->experiments->add_feature( [
            'name' => 'smart_scale',
            'title' => esc_html__( 'Smart scale', 'tenweb-builder' ),
            'description' => esc_html__( 'Automatically adjusts the website’s size proportionally to fit within the editor’s available space, preventing visual distortions.', 'tenweb-builder' ),
            'release_status' => \Elementor\Core\Experiments\Manager::RELEASE_STATUS_BETA,
            'default' => 'inactive',
            'new_site' => [
                'default_active' => false,
                'minimum_installation_version' => '3.1.0-beta',
            ],
        ] );
    }

    public function hideElementorUpgradeNotice() {
        update_option( '_elementor_editor_upgrade_notice_dismissed', 1 );
    }

    public function removePoweredBy( $content ) {
        $re = '/(Powered by 10Web)|(<a ?.*>Powered by 10Web<\/a>)/mi';
        preg_match( $re, $content, $matches, PREG_OFFSET_CAPTURE, 0 );
        if ( ! empty( $matches ) ) {
            $content = '';
        }

        return $content;
    }

    public function removeMadeBy10web($content) {
        $re = '/(Made by 10Web)|(<a ?.*><span ?.*>Made by 10Web<\/span><\/a>)/mi';
        preg_match( $re, $content, $matches, PREG_OFFSET_CAPTURE, 0 );
        if ( ! empty( $matches ) ) {
            $content = '';
        }

        return $content;
    }

    public function initDomain($from_demo = FALSE, $twbb_domain_name_suggestion = FALSE ){
        new \Tenweb_Builder\Modules\DomainConnect($from_demo, $twbb_domain_name_suggestion);
		return true;
    }

    public function initCustomize($from_demo = FALSE ) {
		//phpcs:ignore Squiz.PHP.CommentedOutCode.Found
        //if( \Tenweb_Builder\Modules\ElementorKit\ElementorKit::isUltimateKitActive() ) {
            $ob = new ThemeCustomize($from_demo);
            if ($from_demo) {
                return $ob->html_template();
            } else {
                echo $ob->html_template();//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            }
        //}
	    return true;
    }

    public function mergeUltimateKit() {
        $nonce = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '';
        if (wp_verify_nonce($nonce, 'wp_rest') === false) {
            wp_send_json_error("invalid_nonce");
        }
        if((new \Tenweb_Builder\Modules\ElementorKit\ElementorKit)->mergeCurrentKitWithUltimateKit()) {
            wp_send_json_success();
        }
        wp_send_json_error("invalid_nonce");
    }
}
