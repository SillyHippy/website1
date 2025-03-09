<?php

namespace Tenweb_Builder;

include_once TWBB_DIR . '/templates/base.php';

class FooterTemplate extends BaseTemplate {

  public static function get_slug(){
    return "twbb_footer";
  }

  public static function get_title(){
    return 'Footer';
  }

  public static function print_twbb_template($template_id, $name = "", $add_empty_template=false) {
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

    require __DIR__ . '/views/footer.php';

    $templates = array();
    $name = (string)$name;
    if('' !== $name) {
      $templates[] = "footer-{$name}.php";
    }

    $templates[] = 'footer.php';

    self::block_templates_loading($templates);
  }

}
