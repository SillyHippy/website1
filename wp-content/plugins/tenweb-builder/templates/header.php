<?php

namespace Tenweb_Builder;

include_once TWBB_DIR . '/templates/base.php';

class HeaderTemplate extends BaseTemplate {

  public static function get_slug(){
    return "twbb_header";
  }

  public static function get_title(){
    return 'Header';
  }

  public static function print_twbb_template($template_id, $name = "", $add_empty_template=false){
    include_once TWBB_DIR . '/templates/views/header.php';

    if($add_empty_template === false) {

      $is_elementor_canvas = false;
      if(is_singular()) {
        $document = \Elementor\Plugin::instance()->documents->get_doc_for_frontend(get_the_ID());
        if($document && 'elementor_canvas' === $document->get_meta('_wp_page_template')) {
          $is_elementor_canvas = true;
        }
      }

      if(!$is_elementor_canvas) {
        if(\Elementor\Plugin::instance()->preview->is_preview_mode() && static::current_template()) {
          //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
          echo \Elementor\Plugin::instance()->preview->builder_wrapper('');
        } else {
          self::print_builder_content($template_id);
        }
      }

    }

    $templates = array();
    $name = (string)$name;
    if('' !== $name) {
      $templates[] = "header-{$name}.php";
    }

    $templates[] = 'header.php';

    remove_all_actions('wp_head');

    self::block_templates_loading($templates);
  }

}
