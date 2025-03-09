<?php

namespace Tenweb_Builder;

use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use Elementor\Core\Settings\Manager;
use Elementor\Plugin;
use Elementor\Widget_Base;

class Custom_html extends Widget_Base {

  public function __construct() {
    $this->add_actions();
  }

  public function get_name() {
    return 'custom-html';
  }

  /**
   * @param $element    Controls_Stack
   * @param $section_id string
   */
  public function register_custom_controls(Controls_Stack $element, $section_id) {
    $this->custom_html_controls($element);
  }

  /**
   * @param Controls_Stack $controls_stack
   * @param $section_id
   */
  public function register_global_controls(Controls_Stack $controls_stack, $section_id) {
    // Add Section after Custom CSS
    // The first ugly part of this condition is supposed to find Global Settings only.
    if ( !array_key_exists('settings-custom-css', $controls_stack->get_tabs_controls()) || 'section_custom_css_pro' !== $section_id ) {
      return;
    }

    /* Reading old values form options to set as default. New values will be stored in post_meta table for Elementor Default Kit post.  */
    $custom_html_header_global = "";
    $custom_html_footer_global = "";

    $tenweb_global_custom_html = get_option("tenweb_global_custom_html");
    $tenweb_global_custom_html = unserialize($tenweb_global_custom_html); //phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_unserialize

    if (is_array($tenweb_global_custom_html)) {
      if (isset($tenweb_global_custom_html["custom_html_header_global"])) {
        $custom_html_header_global = $tenweb_global_custom_html["custom_html_header_global"];
      }
      if (isset($tenweb_global_custom_html["custom_html_footer_global"])) {
        $custom_html_footer_global = $tenweb_global_custom_html["custom_html_footer_global"];
      }
    }

    $controls_stack->start_controls_section(
      'section_custom_html_header_global',
      [
        'label' => __('Custom HTML', 'tenweb-builder'),
        'tab' => 'settings-custom-css',
      ]
    );
    $controls_stack->add_control(
      'custom_html_title_header_global',
      [
        'raw' => __('<p style="margin-bottom: 20px ">Add your own custom HTML or Javascript here.</p> The code will be inserted in the header section of your website.', 'tenweb-builder'),
        'type' => Controls_Manager::RAW_HTML,
      ]
    );
    $controls_stack->add_control(
      'custom_html_header_global',
      [
        'type' => Controls_Manager::CODE,
        'label' => __('Custom HTML', 'tenweb-builder'),
        'language' => 'html',
        'render_type' => 'ui',
        'show_label' => false,
        'separator' => 'none',
        'default' => $custom_html_header_global
      ]
    );

    $controls_stack->add_control(
      'custom_html_title_footer_global',
      [
        'raw' => __('The code will be inserted at the bottom of the HTML body of your web pages.', 'tenweb-builder'),
        'type' => Controls_Manager::RAW_HTML,
      ]
    );
    $controls_stack->add_control(
      'custom_html_footer_global',
      [
        'type' => Controls_Manager::CODE,
        'label' => __('Custom HTML', 'tenweb-builder'),
        'language' => 'html',
        'render_type' => 'ui',
        'show_label' => false,
        'separator' => 'none',
        'default' => $custom_html_footer_global
      ]
    );
    $controls_stack->end_controls_section();
  }

  public function custom_html_controls($controls_stack) {
    /*header*/
    $controls_stack->start_controls_section(
      'section_custom_html_header',
      [
        'label' => __('Custom HTML', 'tenweb-builder'),
        'tab' => 'advanced',
      ]
    );
    $controls_stack->add_control(
      'custom_html_title_header',
      [
        'raw' => __('<p style="margin-bottom: 20px ">Add your own custom HTML or Javascript here.</p> The code will be inserted in the header section of the current post only.', 'tenweb-builder'),
        'type' => Controls_Manager::RAW_HTML,
      ]
    );
    $controls_stack->add_control(
      'custom_html_header',
      [
        'type' => Controls_Manager::CODE,
        'label' => __('Custom HTML', 'tenweb-builder'),
        'language' => 'html',
        'render_type' => 'ui',
        'show_label' => false,
        'separator' => 'none',
      ]
    );

    /*end of body*/


    $controls_stack->add_control(
      'custom_html_title_body',
      [
        'raw' => __('The code will be inserted at the bottom of the HTML body of the current post only.', 'tenweb-builder'),
        'type' => Controls_Manager::RAW_HTML,
      ]
    );
    $controls_stack->add_control(
      'custom_html_body',
      [
        'type' => Controls_Manager::CODE,
        'label' => __('Custom HTML end of body', 'tenweb-builder'),
        'language' => 'html',
        'render_type' => 'ui',
        'show_label' => false,
        'separator' => 'none',
      ]
    );
    $controls_stack->end_controls_section();
  }

  public function print_head_html() {
    if ( ! Plugin::$instance->preview->is_preview_mode() ) {
      $get_global_html = $this->get_global_html();
      $post_id = get_the_ID();
      $page_settings_manager = Manager::get_settings_managers('page');
      $page_settings_model = $page_settings_manager->get_model($post_id);
      $custom_html = '';
      if (isset($get_global_html["custom_html_header_global"])) {
        $custom_html .= $get_global_html["custom_html_header_global"];
      }
      $custom_html .= $page_settings_model->get_settings('custom_html_header');
      echo $custom_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
  }

  public function print_body_html() {
    if ( ! Plugin::$instance->preview->is_preview_mode() ) {
      $get_global_html = $this->get_global_html();
      $post_id = get_the_ID();
      $page_settings_manager = Manager::get_settings_managers('page');
      $page_settings_model = $page_settings_manager->get_model($post_id);
      $custom_html = $page_settings_model->get_settings('custom_html_body');
      if (isset($get_global_html["custom_html_footer_global"])) {
        $custom_html .= $get_global_html["custom_html_footer_global"];
      }
        echo $custom_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
  }

  protected function add_actions() {
    add_action('elementor/element/wp-post/section_custom_css/after_section_end', [$this, 'register_custom_controls'], 10, 2);
    add_action('elementor/element/wp-page/section_custom_css/after_section_end', [$this, 'register_custom_controls'], 10, 2);
    add_action('elementor/element/after_section_end', [$this, 'register_global_controls'], 11, 2);
    add_action('wp_head', [$this, 'print_head_html']);
    add_action('wp_footer', [$this, 'print_body_html'], 9999);
  }

  private function get_global_html() {
    $kit = Plugin::$instance->kits_manager->get_active_kit_for_frontend();
    $custom_html_header_global = $kit->get_settings_for_display( 'custom_html_header_global' );
    $custom_html_footer_global = $kit->get_settings_for_display( 'custom_html_footer_global' );

    /* Get old values from options. */
    if ( !$custom_html_header_global || !$custom_html_footer_global ) {
      $tenweb_global_custom_html = get_option("tenweb_global_custom_html");
      $tenweb_global_custom_html = unserialize($tenweb_global_custom_html); //phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_unserialize
      if ($tenweb_global_custom_html && is_array($tenweb_global_custom_html)) {
        $custom_html_header_global = $custom_html_header_global ? $custom_html_header_global : ( isset( $tenweb_global_custom_html['custom_html_header_global'] ) ? $tenweb_global_custom_html['custom_html_header_global'] : '' );
        $custom_html_footer_global = $custom_html_footer_global ? $custom_html_footer_global : ( isset( $tenweb_global_custom_html['custom_html_footer_global'] ) ? $tenweb_global_custom_html['custom_html_footer_global'] : '' );
      }
    }
    return array( 'custom_html_header_global' => $custom_html_header_global, 'custom_html_footer_global' => $custom_html_footer_global );
  }
}
new Custom_html();

