<?php

namespace Tenweb_Builder;

use \Tenweb_Builder\Modules\SectionGeneration\TemplatePreview;

class Templates {

  protected static $instance = null;
  protected $templates_slugs = ["twbb_header", "twbb_single", "twbb_archive", "twbb_footer", "twbb_slide", "twbb_single_product", "twbb_archive_products"];
  protected $templates_with_conditions = ["twbb_header", "twbb_single", "twbb_archive", "twbb_footer", "twbb_single_product", "twbb_archive_products"];
  private $page_template_id;
  private $is_twbb_template = null;
  private $loaded_templates = [];
  private $current_template_type = null;

  private function __construct(){
    include_once 'header.php';//phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.NotAbsolutePath
    include_once 'footer.php';//phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.NotAbsolutePath
    include_once 'single.php';//phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.NotAbsolutePath
    include_once 'singleProduct.php';//phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.NotAbsolutePath
    include_once 'archive.php';//phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.NotAbsolutePath
    include_once 'archiveProducts.php';//phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.NotAbsolutePath
    include_once 'slide.php';//phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.NotAbsolutePath

    add_action('wp_enqueue_scripts', [$this, 'load_css_files']);
    add_action('elementor/documents/register', [$this, 'register_templates']);

    //Add condition column only for tenweb-builder templates
	  //phpcs:ignore WordPress.Security.NonceVerification.Recommended
    if((isset($_GET['tabs_group']) && $_GET['tabs_group'] === 'twbb_templates') || TENWEB_WHITE_LABEL) {
      add_action('manage_elementor_library_posts_columns', [$this, 'posts_table_headers'], 100);
    }
    add_action('manage_elementor_library_posts_custom_column', [$this, 'posts_table_columns'], 10, 2);

    include_once TWBB_DIR . '/templates/condition/condition.php';
    Condition::get_instance();
    // 10web-manager is activated
    if(defined('TENWEB_DIR')) {
      include_once TWBB_DIR . '/templates/remote.php';
      \Tenweb_Builder\RemoteTemplates::get_instance();
    }
    /*
     * 'elementor-block' variable is set in block-editor widget, this check was added for elementor-library widget which is added blocks to gutenberg editor,
     the second way to fix bug was change 999999 to 11, but to avoid other unknown bugs the solution is this one
     */
    //phpcs:ignore WordPress.Security.NonceVerification.Recommended
    if(!isset($_GET['elementor-block']) && TemplatePreview::getInstance()->isTemplatePreviewActive() === false) {
      add_action('get_header', [$this, 'load_header'], 999999);
      add_action('get_footer', [$this, 'load_footer'], 999999);
      add_filter('template_include', [$this, 'template_redirect'], 999999);
    }
  }

  public function load_header($name){
    $template_id = Condition::get_instance()->get_header_template();

    if($template_id !== 0) {
      HeaderTemplate::print_twbb_template($template_id, $name);
    }
  }

  public function load_footer($name){
    $template_id = Condition::get_instance()->get_footer_template();

    if($template_id !== 0) {
      FooterTemplate::print_twbb_template($template_id, $name);
    }
  }

  /**
  * In editor type always get singular, the logic of type works in frontend
  */
  public function template_redirect($template){
    if(Condition::get_instance()->get_page_type() === 'singular') {
      $template_id = Condition::get_instance()->get_single_template();
      if($template_id === 0) {
        $template_id = Condition::get_instance()->get_single_product_template();
      }
      if($template_id !== 0) {
        $this->page_template_id = $template_id;
        return TWBB_DIR . '/templates/views/single.php';
      } else {
        return $template;
      }
    } else if(Condition::get_instance()->get_page_type() === 'singular_product') {
        $template_id = Condition::get_instance()->get_single_product_template();
        if($template_id === 0) {
            $template_id = Condition::get_instance()->get_single_template();
        }
        if($template_id !== 0) {
            $this->page_template_id = $template_id;
            return TWBB_DIR . '/templates/views/singleProduct.php';
        } else {
            return $template;
        }
    } else if(Condition::get_instance()->get_page_type() === 'archive') {
      $template_id = Condition::get_instance()->get_archive_template();
      if($template_id === 0) {
        $template_id = Condition::get_instance()->get_archive_products_template();
      }
      if($template_id !== 0) {
        $this->page_template_id = $template_id;
        return TWBB_DIR . '/templates/views/archive.php';
      } else {
        return $template;
      }
    } else if(Condition::get_instance()->get_page_type() === 'archive_products') {

      $template_id = Condition::get_instance()->get_archive_products_template();
      if($template_id === 0) {
        $template_id = Condition::get_instance()->get_archive_template();
      }
      if($template_id !== 0) {
        $this->page_template_id = $template_id;
        return TWBB_DIR . '/templates/views/archiveProducts.php';
      } else {
        return $template;
      }
    }

    return $template;
  }

  /**
   * @param \Elementor\Core\Documents_Manager $documents_manager
   */
  public function register_templates($documents_manager){
    $documents_manager->register_document_type(HeaderTemplate::get_slug(), HeaderTemplate::get_class_full_name());
    $documents_manager->register_document_type(FooterTemplate::get_slug(), FooterTemplate::get_class_full_name());
    $documents_manager->register_document_type(SingleTemplate::get_slug(), SingleTemplate::get_class_full_name());
    $documents_manager->register_document_type(SingleProductTemplate::get_slug(), SingleProductTemplate::get_class_full_name());
    $documents_manager->register_document_type(ArchiveTemplate::get_slug(), ArchiveTemplate::get_class_full_name());
    $documents_manager->register_document_type(ArchiveProductsTemplate::get_slug(), ArchiveProductsTemplate::get_class_full_name());
    $documents_manager->register_document_type(SlideTemplate::get_slug(), SlideTemplate::get_class_full_name());

    $this->templates_slugs = [
      HeaderTemplate::get_slug(),
      SingleTemplate::get_slug(),
      SingleProductTemplate::get_slug(),
      ArchiveTemplate::get_slug(),
      ArchiveProductsTemplate::get_slug(),
      FooterTemplate::get_slug(),
      SlideTemplate::get_slug(),
    ];
  }

  public function print_post_types_select(){
    $args = array(
      'exclude_from_search' => false,
    );

    $post_types = \Tenweb_Builder\Modules\Helper::get_post_types();

    if(empty($post_types)) {
      return;
    }

    $options = array(
      '' => __('Select', 'tenweb-builder') . '...',
    );

    foreach($post_types as $post_type => $pt_info) {
      $options[$post_type] = $pt_info->labels->singular_name;
    }

    $options['not_found'] = __('404 Page', 'tenweb-builder');

    //phpcs:ignore WordPress.Security.NonceVerification.Recommended
    if(isset($_GET['elementor_library_type']) && ($_GET['elementor_library_type'] === "twbb_single" || $_GET['elementor_library_type'] === "twbb_single_product" || $_GET['elementor_library_type'] === "twbb_slide")) {
      $display = "display:block;";
    } else {
      $display = "display:none;";
    }

    ?>
      <div id="twbb-post-type-form-field" class="elementor-form-field" style="<?php echo esc_attr( $display ); ?>">
          <label for="twbb-post-type-form-select" class="elementor-form-field__label">
            <?php esc_attr_e('Select Post Type', 'tenweb-builder'); ?>
          </label>
          <div class="elementor-form-field__select__wrapper">
              <select id="twbb-post-type-form-select" class="elementor-form-field__select"
                      name="twbb-template-post-type">

                <?php
                foreach($options as $value => $title) {
                  echo '<option value="' . esc_attr( $value ) . '">' . esc_html( $title ) . '</option>';
                }
                ?>
              </select>
          </div>
      </div>
    <?php
  }

  public function posts_table_headers($cols){
    unset($cols['instances']);
    $new_cols = $cols;
	  //phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotValidated
    if(in_array($_GET['elementor_library_type'], $this->templates_with_conditions, true)) {
      $new_cols = array_slice($cols, 0, 3, TRUE);
      $new_cols += array('condition' => __('Condition', 'tenweb-builder'));
      $new_cols += array_slice($cols, 3, NULL, TRUE);
    }
    return $new_cols;
  }

  public function posts_table_columns($column_name, $post_id){

    if($column_name !== "condition") {
      return;
    }

    $thickbox = add_query_arg(
      array('action' => 'trigger_conditions', 'post_id' => $post_id, 'TB_iframe' => 'true', 'width' => '1140', 'height' => '486'),
      admin_url('admin-ajax.php')
    );

    $conditions = Condition::get_instance()->get_template_condition($post_id);

    ?>
  <div onclick="tb_show('Display Conditions', '<?php echo esc_url( $thickbox ); ?>' )"
       class="display_admin_condition_popup thickbox"> <?php
    if(!empty($conditions)) {
      if(count($conditions) === 1) {
        echo count($conditions) . ' condition';
      } else {
        echo count($conditions) . ' conditions';
      }
    } else {
      echo esc_html('Add Condition', 'tenweb-builder');
    }
    echo '</div>';
  }

  public function is_twbb_template(){
    if($this->is_twbb_template !== null) {
      $show_button = $this->show_button($this->is_twbb_template);
      return ['template_type' => $this->is_twbb_template, 'header_button_show' => $show_button];
    }

    $type = get_post_meta(get_the_ID(), '_elementor_template_type', true);
    $show_button = $this->show_button($type);
    if(in_array($type, $this->get_templates_slugs(), true)) {
      $this->is_twbb_template = $type;
    } else {
      $this->is_twbb_template = false;
    }

    return ['template_type' => $this->is_twbb_template, 'header_button_show' => $show_button];
  }

  public function show_button($type){
    if(in_array($type, $this->templates_with_conditions, true)) {
      return 'condition';
    } elseif($this->is_elementor_template_type()) {
      return 'none';
    } else {
      return 'header_footer';
    }
  }

  public static function is_elementor_template_type(){
    if(get_post_type(get_the_ID()) === 'elementor_library') {
      return TRUE;
    }
    return FALSE;
  }

  public function install_site($response, $request){
    return $response[''];
  }

  public function add_loaded_templates($slug, $template_id){
    $this->loaded_templates[$slug] = $template_id;
  }

  public function get_loaded_templates(){
    return $this->loaded_templates;
  }

  public function get_templates_slugs(){
    return $this->templates_slugs;
  }

  public function get_page_template_id(){
    return $this->page_template_id;
  }


  public function get_current_template_type(){
    if($this->current_template_type === null) {
      $this->current_template_type = get_post_meta(get_the_ID(), '_elementor_template_type', true);
    }
    return $this->current_template_type;

  }

  public function load_css_files(){
    if(!class_exists('\Elementor\Core\Files\CSS\Post')) {
      return;
    }

    $header_template_id = Condition::get_instance()->get_header_template();
    if($header_template_id) {
      // if there is header template, load it's css file in the head tag to avoid CLS
      $file = \Elementor\Core\Files\CSS\Post::create($header_template_id);
      $file->enqueue();
    }


    if($this->page_template_id) {
      // if there is page template, load it's css file in the head tag to avoid CLS
      $file = \Elementor\Core\Files\CSS\Post::create($this->page_template_id);
      $file->enqueue();
    }
  }

  public static function get_instance(){
    if(self::$instance === null) {
      self::$instance = new self();
    }
    return self::$instance;
  }

}
