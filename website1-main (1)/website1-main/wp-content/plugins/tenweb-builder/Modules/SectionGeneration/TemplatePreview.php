<?php

namespace Tenweb_Builder\Modules\SectionGeneration;

class TemplatePreview {
  protected static $instance = null;

  protected $templateId = null;
  protected $templatePreviewFrom = null;
  protected $isTemplatePreviewActive = false;

  public function updateElementorCss($postObj) {
     $postObj->update();
  }

  public function loadHeader($name){
      \Tenweb_Builder\HeaderTemplate::print_twbb_template(0, $name, true);
  }

  public function loadFooter($name){
      \Tenweb_Builder\FooterTemplate::print_twbb_template(0, $name, true);
  }

  public function templateRedirect($template){
    return TWBB_DIR . '/templates/views/preview_template.php';
  }

  public function addBodyClass($classes){
    $classes[] = 'twbb_template_preview_page';
    return $classes;
  }

  public function validateGetParams(){

    if(empty($_GET['twbb_template_preview']) || empty($_GET['twbb_template_preview_from']) || empty($_GET['twbb_template_preview_nonce'])) {
      return false;
    }

    if(!wp_verify_nonce(
        sanitize_text_field( $_GET['twbb_template_preview_nonce'] ),
        'twbb_template_preview')
    ) {
      return false;
    }

    $this->templateId = sanitize_text_field($_GET['twbb_template_preview']);
    $this->templatePreviewFrom = sanitize_text_field($_GET['twbb_template_preview_from']);

    return true;
  }

  public function isTemplatePreviewActive(){
    return $this->isTemplatePreviewActive;
  }

  /**
   * This function checks can user edit post/page with id from $_GET['template_preview_from']
   */
  public function userCanEdit(){
    $user_id = get_current_user_id();
    if(!$user_id) {
      return false;
    }

    $post = get_post($this->templatePreviewFrom);
    $post_type = get_post_type_object($post->post_type);

    $cap = ($user_id === $post->post_author && !empty($post_type->cap)) ? $post_type->cap->edit_posts : $post_type->cap->edit_others_posts;

    return current_user_can($cap);
  }

  public function getNonce(){
    return wp_create_nonce('twbb_template_preview');
  }

  public static function getInstance(){
    if(is_null(self::$instance)) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  protected function __construct(){
    if($this->validateGetParams() && $this->userCanEdit()) {
        $this->init();
    }
  }

  protected function init(){
    $this->isTemplatePreviewActive = true;

    \Elementor\Utils::do_not_cache();

    // add empty header and footer templates
    add_action('get_header', [$this, 'loadHeader'], 999999);
    add_action('get_footer', [$this, 'loadFooter'], 999999);

    // load wp template for preview. It contains only header, the_content(), and footer
    add_filter('template_include', [$this, 'templateRedirect'], 999999);
    add_filter('body_class', [$this, 'addBodyClass']);
    add_action( 'elementor/css-file/before_enqueue', [$this,'updateElementorCss'] );
    add_filter('show_admin_bar', '__return_false');//phpcs:ignore WordPressVIPMinimum.UserExperience.AdminBarRemoval.RemovalDetected
    // remove query monitor from sections preview
    add_filter('qm/dispatch/html', '__return_false');
  }
}
