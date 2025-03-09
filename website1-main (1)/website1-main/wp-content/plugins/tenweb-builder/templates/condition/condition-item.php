<?php
/**
 * Created by PhpStorm.
 * User: mher
 * Date: 10/8/18
 * Time: 6:18 PM
 */

namespace Tenweb_Builder;

class ConditionItem {

  protected static $instance = null;

  /**
   * @var string
   * Possible values are "include", "exclude"
   */
  public $condition_type = "include";
  /**
   * @var string
   * Possible values are "general", "singular", "archive"
   */
  public $page_type = "general";
  public $post_type = "";
  public $filter_type = "";
  public $specific_pages = [];
  public $template_id = 0;

  private $level = 1;
  private $template_type = "";

  private $archive_static_pages = ['author', 'date', 'search', 'product_archive'];
  private $singular_static_pages = ['front_page', 'not_found'];

  public function __construct(){

  }

  public function set_and_validate_fields($options){
    $valid_values = array(
      'condition_type' => ['include', 'exclude'],
      'page_type' => ['general', 'archive', 'singular']
    );

    //phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
    if(isset($options['condition_type']) && in_array($options['condition_type'], $valid_values['condition_type'])) {
      $this->condition_type = $options['condition_type'];
    }

	//phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
    if(isset($options['page_type']) && in_array($options['page_type'], $valid_values['page_type'])) {
      $this->page_type = $options['page_type'];
    }

    if(isset($options['post_type'])) {
      $this->post_type = $options['post_type'];
    } else if($this->page_type === 'singular' || $this->page_type === 'archive') {
      $this->post_type = 'all';
    } else {
      $this->post_type = '';
    }

    if($this->post_type !== '') {
      $this->filter_type = (isset($options['filter_type'])) ? $options['filter_type'] : "all";
      $this->specific_pages = (isset($options['specific_pages'])) ? (array)$options['specific_pages'] : [];
    }

    $this->set_level();

  }

  public function set_fields($options){
    $this->condition_type = $options['condition_type'];
    $this->page_type = $options['page_type'];
    $this->post_type = $options['post_type'];
    $this->filter_type = $options['filter_type'];
    $this->specific_pages = $options['specific_pages'];

    $this->set_level();
  }

  public function set_template_id($template_id){
    $this->template_id = $template_id;
  }

  public function set_template_type(){
    $this->template_type = get_post_meta($this->template_id, '_elementor_template_type', true);
  }

  public function get_template_type(){
    return $this->template_type;
  }

  public function condition_for_post($wp_page_type){

    if(($wp_page_type === 'singular' || $wp_page_type === 'singular_product') && $this->page_type === 'singular') {
      return $this->condition_for_singular_post();
    } else if(($wp_page_type === 'archive'  || $wp_page_type === 'archive_products' ) && $this->page_type === 'archive') {
      return $this->condition_for_archive_page();
    } else {
      return true;
    }
  }

    public function condition_for_specific_product($product_id){
      if($this->page_type === 'singular') {
          return $this->condition_for_singular_post($product_id);
      }
    }

  private function condition_for_singular_post($id = null){
    if($id === null) {
        $id = get_the_ID();
        $get_post_type = get_post_type();
    } else {
        if( $id === 0 ) {
            $get_post_type = 'product';
        } else {
            $get_post_type = get_post_type($id);
        }
    }
    /*if is front page*/
    if($this->post_type === 'front_page') {
      return is_front_page();
    }

    /*if is 404 page*/
    if($this->post_type === 'not_found') {
      return is_404();
    }

    /*if option is All singular pages*/
    if($this->post_type === 'all') {
      return true;
    }


    if($this->filter_type === 'all' && $this->post_type === $get_post_type) {
      /*if option is All CPT (ALL Posts)*/
      return true;
    } else if($this->filter_type === 'specific_posts' && in_array($id, $this->specific_pages)) {//phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
      /*Posts with ids*/
      return true;
    } else {
      /*Posts with terms*/
      $post_terms_ids = wp_get_post_terms($id, $this->filter_type, array("fields" => "ids"));
      if(is_wp_error($post_terms_ids)) {
        return false;
      }

      $common_ids = array_intersect($post_terms_ids, $this->specific_pages);
      return (!empty($common_ids));
    }
  }

  private function condition_for_archive_page(){
    /*if is Author,Date archive page or search page*/
    if($this->post_type === 'author') {
      return is_author();
    }

    if($this->post_type === 'date') {
      return is_date();
    }

    if($this->post_type === 'search') {
      return is_search();
    }

    /*For all archive pages*/
    if($this->post_type === 'all') {
      return true;
    }

    if($this->post_type === 'product') {
      if($this->filter_type === 'all') {
        return ( class_exists('woocommerce') && ( is_shop() || is_product_taxonomy() || is_product_category() || is_product_tag() ) );
      }
      else {
        return is_tax( $this->filter_type, $this->specific_pages );
      }
    }

    if($this->post_type === 'post' && is_home()) {
      return true;
    } else if($this->filter_type === 'all' && is_post_type_archive($this->post_type)) {
      return true;//Post type archive page
    } else {
      if($this->filter_type === 'category') {//category archive
        return is_category($this->specific_pages);
      } else if($this->filter_type === 'post_tag') {//tag archive
        return is_tag($this->specific_pages);
      } else {
        return is_tax($this->filter_type, $this->specific_pages);//custom taxonomy archive
      }
    }

  }

  private function set_level(){
    if($this->page_type === 'general') {
      $this->level = 1;
      return;
    }

    if($this->post_type === 'all') {
      $this->level = 10;
      return;
    }

    if($this->page_type === 'singular') {
	  //phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
      if(in_array($this->post_type, $this->singular_static_pages)) {
        $this->level = 60;
        return;
      }

      if($this->filter_type === 'all') {
        $this->level = 20;
      } else if($this->filter_type === 'specific_posts') {
        $this->level = 50;
      } else {
        $this->level = 30;
      }

    } else {
	  //phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
      if(in_array($this->post_type, $this->archive_static_pages)) {
        $this->level = 60;
        return;
      }

      if($this->filter_type === 'all') {
        $this->level = 20;
      } else {
        if(empty($this->specific_pages)) {
          $this->level = 30;
        } else {
          $this->level = 50;
        }
      }

    }

  }

  public function get_level(){
    return $this->level;
  }

  public function get_as_array(){

    return array(
      'condition_type' => $this->condition_type,
      'page_type' => $this->page_type,
      'post_type' => $this->post_type,
      'filter_type' => $this->filter_type,
      'specific_pages' => $this->specific_pages
    );

  }

  public static function get_instance(){
    if(self::$instance === null) {
      self::$instance = new self();
    }
    return self::$instance;
  }


}
