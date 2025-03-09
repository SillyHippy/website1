<?php
/**
 * Created by PhpStorm.
 * User: mher
 * Date: 8/23/18
 * Time: 11:05 AM
 */

namespace Tenweb_Builder;

class SelectAjax extends \Elementor\Control_Select2 {

  public function __construct(){
    parent::__construct();
  }

  public function get_type(){
    return "TWBBSelectAjax";
  }

  public function enqueue(){
    parent::enqueue();
    wp_enqueue_script('twbb-control-select-ajax', TWBB_URL . '/assets/editor/js/select-ajax.js', ['jquery'], TWBB_VERSION);
  }
}