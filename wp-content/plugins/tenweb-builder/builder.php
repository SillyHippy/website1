<?php
namespace Tenweb_Builder;

use \Tenweb_Builder\Modules\ElementorKit\ElementorKit;

class Builder {
  public static $prefix = '';
  public static $version = '';
  protected static $instance = NULL;

  private function __construct() {
    $this->setVariables();
    $this->process();
  }

  private function process() {
    $this->checkBuilderUpdate();
    add_action('elementor/init', array( 'Tenweb_Builder\Builder', 'applyUltimateKit' ), 12 );
    $this->checkSectionsFolderExistense();
    add_post_type_support( 'page', 'excerpt' );
	add_action('init', function() {
		// Redirect to homepage editor if user comes from 10Web dashboard
		if ( ! empty( $_GET['from'] ) && 'tenweb_dashboard' === $_GET['from'] && ! empty( $_GET['open'] ) && 'homepage' === $_GET['open'] ) {//phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$fronpage_id = get_option( 'page_on_front' );
			if ( $fronpage_id ) {
				wp_safe_redirect( admin_url( 'post.php?post=' . get_option( 'page_on_front' ) . '&action=elementor' ) );
				exit;
			}
			else {
				wp_safe_redirect( home_url() );
				exit;
			}
			die;
		}
		load_plugin_textdomain( 'tenweb-builder', FALSE, basename( dirname( __FILE__ ) ) . '/languages' );
	});
    add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

    //TODO move to separate check processing method.
    if ( ! $this->check_elementor_compatibility() ) {
      add_action( 'admin_notices', array( $this, 'elementor_compatibility_notice' ) );
    } else {
      if ( defined( 'ELEMENTOR_PATH' ) ) {
        //TODO move to Apps
        include_once TWBB_DIR . '/templates/templates.php';
        Templates::get_instance();
        $this->includeApps();
        $this->includeModules();
        $this->registerHooks();

        //TODO move to widgets woocommerce part maybe )
        include_once TWBB_DIR . '/classes/woocommerce.php';
        require_once TWBB_DIR . '/widgets/woocommerce/settings-woocommerce.php';
        \Tenweb_Builder\Classes\Woocommerce\Woocommerce::get_instance();
      }
    }
  }

  private function setVariables()
  {
    self::$prefix = TWBB_PREFIX;
    self::$version = TWBB_VERSION;
  }

  private function checkBuilderUpdate() {
    if ( get_option( 'twbb_version' ) !== TWBB_VERSION ) {
      self::install();
    }
  }

  public static function install() {
    $version = get_option( 'twbb_version' );
    if ( $version === FALSE ) {
      $version = '0.0.0';
      self::setElementorEditorVersion();
    }
    if ( version_compare( $version, TWBB_VERSION, '<=' ) ) {
      //TODO
    }
    self::afterInstall();
  }

  private static function afterInstall() {
    update_option( 'twbb_version', TWBB_VERSION );
    update_option('elementor_experiment-e_global_styleguide', 'inactive');
    update_option('elementor_experiment-e_element_cache', 'inactive');
    update_option('elementor_experiment-e_optimized_css_loading', 'inactive');
    update_option('elementor_experiment-container', 'active');
    self::maybe_update_elementor_onboarding_option();
    self::setElementorEditorVersion();
    self::sectionsSync();
	  if ( class_exists( '\Elementor\Plugin' ) ) {
		  // This can look strange, but this function is called in two different scenarious,
		  // so we need to make sure in will work in both cases.
		  if ( \Elementor\Plugin::instance()->files_manager ) {
			  \Elementor\Plugin::instance()->files_manager->clear_cache();
		  } else {
			  add_action( 'init', function () {
				  //clean Elementor cache update css, Files and data
				  \Elementor\Plugin::instance()->files_manager->clear_cache();
			  } );
		  }
	  }
  }

  private function checkSectionsFolderExistense() {
    if ( ! file_exists( wp_upload_dir()['basedir'] . '/ai20-sections' ) && !get_option('twbb_sections_force_sync')) {
      self::sectionsSync();
      update_option( 'twbb_sections_force_sync', 'true');
    }
  }

  public static function sectionsSync() {
      if ( get_option('elementor_experiment-sections_generation') !== 'inactive' &&
          defined('TW_HOSTED_ON_10WEB') && TW_HOSTED_ON_10WEB === TRUE ) {
          include_once TWBB_DIR . "/Modules/ai/Utils.php";
          include_once TWBB_DIR . "/Modules/ai/TenWebApi.php";
          include_once TWBB_DIR . "/Modules/SectionGeneration/GenerateSectionsPostsByType.php";
          if (class_exists('\Tenweb_Builder\Modules\ai\TenWebApi')
              && class_exists('\Tenweb_Builder\Modules\ai\Utils')
              && class_exists('\Tenweb_Builder\Modules\SectionGeneration\GenerateSectionsPostsByType')) {
              self::deleteSectionsPosts();
              \Tenweb_Builder\Modules\ai\TenWebApi::get_instance()->sectionSyncRequest();
              \Tenweb_Builder\Modules\ai\TenWebApi::get_instance()->getSectionTypeDescriptions();
              \Tenweb_Builder\Modules\SectionGeneration\GenerateSectionsPostsByType::getInstance()->process(true);
          }
      }
  }

  private static function deleteSectionsPosts()
  {
      $args = array(
          'post_type' => 'twbb_sg_preview',
          'posts_per_page' => -1,
      );
      $posts = get_posts($args);//phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.get_posts_get_posts
      foreach ($posts as $post) {
          wp_delete_post($post->ID, true);
      }
  }

  public static function maybe_update_elementor_onboarding_option() {
    $db_option = get_option( 'elementor_onboarded');
    if ( ! $db_option ) {
      update_option( 'elementor_onboarded', true );
    }
  }

  public static function applyUltimateKit(){
    if(!defined('ELEMENTOR_PATH') || !class_exists('\Elementor\Plugin')) {
      return false;
    }

    if(empty(\Elementor\Plugin::instance()->kits_manager)) {
      return false;
    }

    $caps = ['edit_posts', 'edit_published_posts', 'edit_others_posts'];
    foreach($caps as $cap) {
      if(!current_user_can($cap)) {
        return false;
      }
    }

    if(ElementorKit::isUltimateKitActive()) {
      return false;
    }

    if(get_option('twbb_ultimate_kit_installed_by_default') !== false) {
      return false;
    }

    if(!ElementorKit::isElementorDefaultKitActive()) {
      return false;
    }
	//phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.get_posts_get_posts
    $posts = get_posts([
      'numberposts' => 1,
      "posts_per_page" => 1,
      'post_type' => ['page', 'post'],
      'post_status' => 'publish',
      'meta_key' => '_elementor_edit_mode',
      'meta_value' => 'builder'//phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
    ]);

    if(count($posts) > 0) {
      return false;
    }

    $kit_id = (new ElementorKit)->applyDefaultUltimateKit();
    if($kit_id) {
      update_option('twbb_ultimate_kit_installed_by_default', "yes", false);
    } else {
      update_option('twbb_ultimate_kit_installed_by_default', "no", false);
    }
    return $kit_id;
  }

  private function check_elementor_compatibility() {
    if ( ! defined( 'ELEMENTOR_VERSION' ) || version_compare( ELEMENTOR_VERSION, TWBB_ELEMENTOR_MIN_VERSION, '<' ) || ! did_action( 'elementor/loaded' ) ) {
      return FALSE;
    }

    return TRUE;
  }

  private function includeApps() {
    \Tenweb_Builder\Apps\BuilderTour::getInstance();
    \Tenweb_Builder\Apps\SVGUpload::getInstance();
    \Tenweb_Builder\Apps\InternalSettings::getInstance();
    \Tenweb_Builder\Apps\RemoveUpsell::getInstance();
    \Tenweb_Builder\Apps\PreviewUpgrade::getInstance();
    \Tenweb_Builder\Apps\SectionGeneration::getInstance();
    \Tenweb_Builder\Apps\TextGenerationAI::getInstance();
    \Tenweb_Builder\Apps\ImageGenerationAI::getInstance();
    \Tenweb_Builder\Apps\FastEditorDirector::getInstance();
    if (\Tenweb_Builder\Apps\CoPilot::checkVisibility()) {
        \Tenweb_Builder\Apps\CoPilot::getInstance();
    }
  }

  private function includeModules()
  {
      new \Tenweb_Builder\Modules\TenwebRestApi();
      new \Tenweb_Builder\Modules\CLI();
      Modules\SectionGeneration\TemplatePreview::getInstance();
      new \Tenweb_Builder\Modules\ActionsHooksWp();
      new \Tenweb_Builder\Modules\ActionsHooksElementor();
      new \Tenweb_Builder\Modules\ai\Utils();
      new \Tenweb_Builder\Modules\ai\TenWebApi();
  }


  // @TODO Temporary solution.
  private function registerHooks() {
    $this->register_controls();
  }

  public function register_controls() {
    include_once TWBB_DIR . '/controls/select-ajax/controller.php';
    SelectAjaxController::get_instance();
    if ( defined( 'ELEMENTOR_PATH' ) && class_exists( 'Elementor\Group_Control_Base' ) ) {
      include_once TWBB_DIR . '/controls/query-control/controller.php';
      include_once TWBB_DIR . '/controls/query-control/controls/group-control-posts.php';
      \Tenweb_Builder\Controls\QueryControl\QueryController::get_instance();
    }
  }

  /* Remove last imported template*/
  public static function get_instance() {
    if ( self::$instance === NULL ) {
      self::$instance = new self();
    }

    return self::$instance;
  }

  //TODO find this functionality from elementor
  public static function get_edit_url() {
    if ( ! is_admin() ) {
      $edit_url = FALSE;
      if ( is_singular() ) {
        $document = \Elementor\Plugin::instance()->documents->get_doc_for_frontend( get_the_ID() );
        if ( $document && $document->is_editable_by_current_user() ) {
          $edit_url = $document->get_edit_url();
        }
        if ( is_singular( array( 'product' ) ) ) {
          $loaded_templates = Templates::get_instance()->get_loaded_templates();
          if ( array_key_exists( 'twbb_single', $loaded_templates ) && ! empty( $loaded_templates[ 'twbb_single' ] ) ) {
            $template_id = $loaded_templates[ 'twbb_single' ];
            $document    = \Elementor\Plugin::instance()->documents->get_doc_for_frontend( $template_id );
            if ( $document && $document->is_editable_by_current_user() ) {
              $edit_url = admin_url( 'post.php?post=' . $template_id . '&action=elementor' );
            }
          }
          elseif ( array_key_exists( 'twbb_single_product', $loaded_templates ) && ! empty( $loaded_templates[ 'twbb_single_product' ] ) ) {
            $template_id = $loaded_templates[ 'twbb_single_product' ];
            $document    = \Elementor\Plugin::instance()->documents->get_doc_for_frontend( $template_id );
            if ( $document && $document->is_editable_by_current_user() ) {
              $edit_url = admin_url( 'post.php?post=' . $template_id . '&action=elementor' );
            }
          }
        }
      } else {
        $loaded_templates = Templates::get_instance()->get_loaded_templates();
        if ( array_key_exists( 'twbb_archive', $loaded_templates ) && ! empty( $loaded_templates[ 'twbb_archive' ] ) ) {
          $archive_id = $loaded_templates[ 'twbb_archive' ];
          $document   = \Elementor\Plugin::instance()->documents->get_doc_for_frontend( $archive_id );
          if ( $document && $document->is_editable_by_current_user() ) {
            $edit_url = admin_url( 'post.php?post=' . $archive_id . '&action=elementor' );
          }
        } elseif ( array_key_exists( 'twbb_archive_products', $loaded_templates ) && ! empty( $loaded_templates[ 'twbb_archive_products' ] ) ) {
          $archive_id = $loaded_templates[ 'twbb_archive_products' ];
          $document   = \Elementor\Plugin::instance()->documents->get_doc_for_frontend( $archive_id );
          if ( $document && $document->is_editable_by_current_user() ) {
            $edit_url = admin_url( 'post.php?post=' . $archive_id . '&action=elementor' );
          }
        }
      }

      return $edit_url;
    }
  }



  public static function setElementorEditorVersion() {
    update_option('elementor_experiment-editor_v2', 'active' );
  }

  public function admin_enqueue_scripts() {
    wp_enqueue_script( TWBB_PREFIX . '-admin-script', TWBB_URL . '/assets/admin/js/admin.js', [ 'jquery' ], TWBB_VERSION, TRUE );
    wp_localize_script( TWBB_PREFIX . '-admin-script', 'twbb_admin',
      array(
        'ajax_url' => wp_nonce_url( admin_url( 'admin-ajax.php' ), 'twbb_remove_template_ajax', 'twbb_nonce' ),
      ) );
    wp_enqueue_style( TWBB_PREFIX . '-admin-style', TWBB_URL . '/assets/admin/css/admin.css', [], TWBB_VERSION );
  }

  public function elementor_compatibility_notice() {
    $elementor_notice = NULL;
    add_thickbox();
    $thickbox          = add_query_arg(
      array( 'tab' => 'plugin-information', 'plugin' => 'elementor', 'TB_iframe' => 'true' ),
      admin_url( 'plugin-install.php' )
    );
    $link              = "";
    $installed_plugins = get_plugins();
    if ( ! isset( $installed_plugins[ 'elementor/elementor.php' ] ) ) {
      $elementor_notice = __( '10Web Builder requires Elementor plugin. Please install and activate the latest version of %s plugin.', 'tenweb-builder' );
	  //phpcs:ignore WordPress.Security.NonceVerification.Recommended
      if ( isset( $_GET[ 'from' ] ) && '10web' === $_GET[ 'from' ] ) {
        $link   = 'thickbox';
        $script = '<script>jQuery(window).load(function() {jQuery("#twbb_install_elementor").trigger("click")});</script>';
      } else {
        $link = add_query_arg(
          array( 's' => 'elementor', 'tab' => 'search', 'type' => 'term', 'from' => '10web' ),
          admin_url( 'plugin-install.php' )
        );
      }
    } else if ( ! defined( 'ELEMENTOR_VERSION' ) ) {
      $elementor_notice = __( '10Web Builder requires Elementor plugin. Please activate %s plugin.', 'tenweb-builder' );
      $link             = wp_nonce_url( 'plugins.php?action=activate&amp;plugin=elementor/elementor.php&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_elementor/elementor.php' );
    } else if ( version_compare( ELEMENTOR_VERSION, TWBB_ELEMENTOR_MIN_VERSION, '<' ) ) {
      $elementor_notice = __( '10Web Builder requires latest version of Elementor plugin. Please update %s plugin.', 'tenweb-builder' );
      $link             = 'thickbox';
    }
    if ( $elementor_notice !== NULL ) {

      if ( current_user_can( 'activate_plugins' ) ) {

        if ( $link === 'thickbox' ) {
          $link = '<a id="twbb_install_elementor" class="thickbox" href="' . $thickbox . '">Elementor</a>';
        } else {
          $link = '<a href="' . $link . '">Elementor</a>';
        }
      } else {
        $link = 'Elementor';
      }
      echo '<div class="error twbb_notice">' . wp_kses_post( sprintf( $elementor_notice, $link ) ) . "</div>";
      if ( isset( $script ) ) {
        echo esc_js( $script );
      }
    }
  }
}
