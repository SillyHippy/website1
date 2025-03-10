<?php
/**
 * Plugin Name: 10Web Builder
 * Description: 10Web Builder is an ultimate premium tool, based on Elementor,  to create websites with stunning design.
 * Plugin URI:  https://10web.io/wordpress-website-builder/
 * Author: 10Web
 * Version: 1.27.37
 * Author URI: https://10web.io/plugins/
 * Text Domain: tenweb-builder
 * License: GNU /GPLv3.0 http://www.gnu.org/licenses/gpl-3.0.html
 */
if(!defined('ABSPATH')) {
  exit;
}
include_once plugin_dir_path(__FILE__) . 'config.php';
require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';
include_once plugin_dir_path(__FILE__) . 'widgets-list.php';
if(twbb_check_plugin_requirements()) {
  add_action('plugins_loaded', 'twbb_plugins_loaded', 1);
  function twbb_plugins_loaded(){
    include_once TWBB_DIR . '/builder.php';
    \Tenweb_Builder\Builder::get_instance();
  }
}
register_activation_hook(__FILE__, 'twbb_activate');
function twbb_activate(){
  if( !twbb_check_plugin_requirements() ) {
    die("PHP or Wordpress version is not compatible with plugin.");
  }
  include_once TWBB_DIR . '/builder.php';
  Tenweb_Builder\Builder::install();
}

function twbb_check_plugin_requirements(){
  global $wp_version;
  $php_version = explode("-", PHP_VERSION);
  $php_version = $php_version[0];
  $result = (
    version_compare($wp_version, '4.7', ">=") &&
    version_compare($php_version, '5.4', ">=")
  );

  return $result;
}

add_filter('post_row_actions', 'template_list_row_actions', 10, 2);
/* Change edit links */
function template_list_row_actions($actions, $post){
  // Check for your post type.
  if($post->post_type === "elementor_library") {
    unset($actions['view']);
  }

  return $actions;
}

function get_template_label_by_type($template_type){
  $document_types = \Elementor\Plugin::instance()->documents->get_document_types();
  if(isset($document_types[$template_type])) {
    $template_label = call_user_func([$document_types[$template_type], 'get_title']);
  } else {
    $template_label = ucwords(str_replace(['_', '-'], ' ', $template_type));
  }
  /**
   * Template label by template type.
   * Filters the template label by template type in the template library .
   *
   * @param string $template_label Template label.
   * @param string $template_type Template type.
   *
   * @since 2.0.0
   */
  $template_label = apply_filters('elementor/template-library/get_template_label_by_type', $template_label, $template_type);

  return $template_label;
}

function is_current_screen(){
  global $pagenow, $typenow;

  return 'edit.php' === $pagenow && 'elementor_library' === $typenow;
}

function get_current_tab_group($default = ''){
  $current_tabs_group = 'twbb_templates';
  if(!empty($_REQUEST['elementor_library_type'])) {//phpcs:ignore WordPress.Security.NonceVerification.Recommended
    $doc_type = \Elementor\Plugin::instance()->documents->get_document_type(sanitize_text_field( $_REQUEST['elementor_library_type'] ), '');//phpcs:ignore WordPress.Security.NonceVerification.Recommended
    if($doc_type) {
      $current_tabs_group = $doc_type::get_property('admin_tab_group');
    }
  } elseif(!empty($_REQUEST['tabs_group'])) {//phpcs:ignore WordPress.Security.NonceVerification.Recommended
    $current_tabs_group = sanitize_text_field( $_REQUEST['tabs_group'] );//phpcs:ignore WordPress.Security.NonceVerification.Recommended
  }

  return $current_tabs_group;
}

function get_library_title(){
  $title = '';
  if(is_current_screen()) {
    $current_tab_group = get_current_tab_group();
    if($current_tab_group) {
      $titles = [
        'library' => __('10Web Templates', 'tenweb-builder'),
        'twbb_templates' => __('10Web Templates', 'tenweb-builder'),
        'twbb_theme' => __('Theme Builder', 'tenweb-builder'),
        'popup' => __('Popups', 'tenweb-builder'),
      ];
      if(!empty($titles[$current_tab_group])) {
        $title = $titles[$current_tab_group];
      }
    }
  }

  return $title;
}

//Print Google Analytics script head
if( domain_not_pointed() ) {
    add_action('admin_print_scripts-widgets.php', 'twbb_head_ga_scripts');
    add_action('wp_enqueue_scripts', 'twbb_head_ga_scripts');
}
function twbb_head_ga_scripts() {
    if ( current_user_can('administrator') ) {
        echo "<!-- Google Tag Manager -->
                <script class='pointerier'>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
                j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
                'https://metrics.10web.site/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
                })(window,document,'script','dataLayer','GTM-P7NJPR5C');</script>
                <!-- End Google Tag Manager -->";
    }
}

//Print Google Analytics script body
if( domain_not_pointed() ) {
    add_action('elementor/editor/after_enqueue_scripts', 'twbb_body_ga_scripts');
    add_action('elementor/frontend/after_enqueue_scripts', 'twbb_body_ga_scripts', 1);
}
function twbb_body_ga_scripts() {
    if ( current_user_can('administrator') ) {
        echo '<!-- Google Tag Manager (noscript) -->
            <noscript><iframe class="pointerier" src="https://metrics.10web.site/ns.html?id=GTM-P7NJPR5C"
            height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
            <!-- End Google Tag Manager (noscript) -->';
    }
}

function domain_not_pointed() {
    $domain = sanitize_text_field( $_SERVER['HTTP_HOST'] ?? '' );
    if ( ( defined("TENWEB_DASHBOARD") && (TENWEB_DASHBOARD === 'https://my.10web.io' ||
                TENWEB_DASHBOARD === 'https://testmy.10web.io' ||
                TENWEB_DASHBOARD === 'https://testmy1.10web.io') ) &&
        (strpos($domain, '.10web.club') !== false ||
            strpos($domain, '.10web.me') !== false ||
            strpos($domain, '.10web.site') !== false ||
            strpos($domain, '.10web.cloud') !== false) ) {
        return true;
    }
    return false;
}
