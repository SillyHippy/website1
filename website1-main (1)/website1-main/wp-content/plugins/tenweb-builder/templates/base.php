<?php
namespace Tenweb_Builder;

use Elementor\Modules\Library\Documents\Library_Document;

abstract class BaseTemplate extends Library_Document {

  //strict standards ... static function should not be abstract

  public function __construct(array $data = []){
    parent::__construct($data);
  }

  public function get_name(){
    return static::get_slug();
  }

  public static function get_property($id){

    $properties = parent::get_properties();
    $properties['support_kit'] = true;
    if(isset($properties[$id])) {
      return $properties[$id];
    } else {
      return null;
    }

  }

  public function get_content($with_css = false){
    $content = parent::get_content($with_css);

    return $content;
  }

  public function get_container_classes(){
    return parent::get_container_classes() . ' ' . static::get_slug() . '-type';
  }

  protected static function print_builder_content($template_id){
    Templates::get_instance()->add_loaded_templates(static::get_slug(), $template_id);
    //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display($template_id);
  }

  protected static function block_templates_loading($templates){
    ob_start();
    locate_template($templates, true);//not require header.php from theme
    ob_get_clean();
  }

  public static function get_slug(){
    return "";
  }

  public static function print_twbb_template($template_id, $template_name = ""){
    echo '';
  }

  public static function current_template() {
    return (Templates::get_instance()->get_current_template_type() === static::get_slug());
  }

}
