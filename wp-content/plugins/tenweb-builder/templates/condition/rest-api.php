<?php
/**
 * Created by PhpStorm.
 * User: mher
 * Date: 10/8/18
 * Time: 10:34 AM
 */

namespace Tenweb_Builder;

class ConditionRestApi {
  protected static $instance = null;

  public function __construct(){
    $this->register_routes();
  }

  private function register_routes(){
    register_rest_route('10webBuilder/conditions', '/post_types/(?P<page_type>[a-zA-Z_\-0-9]+?)',
      [
        'methods' => 'GET',
        'callback' => [$this, 'get_post_types'],
        'permission_callback' => '__return_true',
        'args' => [
          'page_type' => [
            'validate_callback' => function($param, $request, $key){
              return ($param === 'archive' || $param === 'singular');
            }
          ]
        ]
      ]
    );

    register_rest_route('10webBuilder/conditions', '/post_filter_types/(?P<post_type>[a-zA-Z_\-0-9]+?)', [
        'methods' => 'GET',
        'callback' => [$this, 'post_filter_types'],
        'permission_callback' => '__return_true',
        'args' => [
          'post_type' => [
            'validate_callback' => function($param, $request, $key){
              return (!empty(get_post_types(array('name' => $param), 'objects')));
            }
          ]
        ]
      ]
    );

    register_rest_route('10webBuilder/conditions', '/posts/', [
        'methods' => 'GET',
        'callback' => [$this, 'posts'],
        'permission_callback' => '__return_true',
      ]
    );

    register_rest_route('10webBuilder/conditions', '/taxonomy/', [
        'methods' => 'GET',
        'callback' => [$this, 'post_taxonomies'],
        'permission_callback' => '__return_true',
      ]
    );

    register_rest_route('10webBuilder/conditions', '/archive_filter_types/(?P<post_type>[a-zA-Z_\-0-9]+?)', [
        'methods' => 'GET',
        'callback' => [$this, 'archive_filter_types'],
        'permission_callback' => '__return_true',
        'args' => [
          'post_type' => [
            'validate_callback' => function($param, $request, $key){
              return (!empty(get_post_types(array('name' => $param), 'objects')));
            }
          ]
        ]
      ]
    );

    register_rest_route('10webBuilder/conditions', '/save_conditions', [
        'methods' => 'POST',
        'callback' => [$this, 'save_conditions'],
        'permission_callback' => '__return_true',
      ]
    );

  }

  /**
   * @param $request \WP_REST_Request
   * */
  public function get_post_types($request){

    if($request->get_param('page_type') === 'singular') {

      $data = array(
        array('id' => 'all', 'text' => __('All Singular', 'tenweb-builder')),
        array('id' => 'front_page', 'text' => __('Front Page', 'tenweb-builder')),
      );

      $post_types = \Tenweb_Builder\Modules\Helper::get_post_types(['publicly_queryable' => true]);
      foreach($post_types as $id => $pt_obj) {
        $data[] = array(
          'id' => $id,
          'text' => $pt_obj->labels->singular_name
        );

        if($id === 'post') {
          $data[] = array('id' => 'page', 'text' => 'Page');
        }
      }

      $data[] = array('id' => 'not_found', 'text' => __('404 Page', 'tenweb-builder'));
    } else {

      $data = array(
        array('id' => 'all', 'text' => __('All Archives', 'tenweb-builder')),
        array('id' => 'author', 'text' => __('Author Archive', 'tenweb-builder')),
        array('id' => 'date', 'text' => __('Date Archive', 'tenweb-builder')),
        array('id' => 'search', 'text' => __('Search Results', 'tenweb-builder')),
        array('id' => 'post', 'text' => __('Posts', 'tenweb-builder')),
      );

      if ( class_exists( 'woocommerce' ) ) {
        $data[] = array( 'id' => 'product', 'text' => __('Product Archive', 'tenweb-builder') );
      }

      $post_types = \Tenweb_Builder\Modules\Helper::get_post_types();
      foreach($post_types as $id => $pt_obj) {
        if($pt_obj->has_archive === true) {
          $data[] = array(
            'id' => $id,
            'text' => $pt_obj->labels->singular_name
          );
        }
      }

    }

    wp_send_json_success(['options' => $data]);
  }


  /**
   * @param $request \WP_REST_Request
   * */
  public function post_filter_types($request){
    $post_type = $request->get_param('post_type');

    $post_type_obj = get_post_types(array('name' => $post_type), 'objects');


    $data = array(
      array('id' => 'all', 'text' => $post_type_obj[$post_type]->labels->all_items),
      array('id' => 'specific_posts', 'text' => __('Specific ', 'tenweb-builder') . $post_type_obj[$post_type]->labels->name),
    );

    $taxonomies = get_object_taxonomies($post_type, 'objects');
    foreach($taxonomies as $id => $taxonomy) {
      $data[] = array(
        'id' => $id,
        'text' => 'In ' . $taxonomy->labels->singular_name,
      );
    }

    wp_send_json_success(['options' => $data]);
  }

  /**
   * @param $request \WP_REST_Request
   * */
  public function posts($request){
    //phpcs:ignore WordPress.Security.NonceVerification.Recommended
    if(empty($_REQUEST['post_type'])) {
      wp_send_json_error();
    }

    $page_for_posts = intval(get_option('page_for_posts'));

    //phpcs:ignore WordPress.Security.NonceVerification.Recommended
    $post_type = sanitize_text_field( $_REQUEST['post_type'] );
    //phpcs:ignore WordPress.Security.NonceVerification.Recommended
    $search = (isset($_REQUEST['search'])) ? sanitize_text_field( $_REQUEST['search'] ) : '';

    $args = array(
      'posts_per_page' => -1,
      's' => $search,
      'post_type' => $post_type,
      'post__not_in' => [$page_for_posts],//phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn
      'post_status' => 'any'
    );

    $query = new \WP_Query($args);
    $posts = $query->posts;

    $data = array();
    foreach($posts as $post) {
      $data[] = array(
        'id' => $post->ID,
        'text' => $post->post_title
      );
    }

    wp_send_json_success(['options' => $data]);
  }

  /**
   * @param $request \WP_REST_Request
   * */
  public function post_taxonomies($request){
	//phpcs:ignore WordPress.Security.NonceVerification.Recommended
    if(empty($_REQUEST['search_in'])) {
      wp_send_json_error();
    }

    //phpcs:ignore WordPress.Security.NonceVerification.Recommended
    $search_in = sanitize_text_field( $_REQUEST['search_in'] );
    //phpcs:ignore WordPress.Security.NonceVerification.Recommended
    $search = (isset($_REQUEST['search'])) ? sanitize_text_field( $_REQUEST['search'] ) : '';

    $args = array(
      'taxonomy' => $search_in,
      'hide_empty' => false,
      'search' => $search
    );

    $terms = get_terms($args);
    $data = [];
    foreach($terms as $term) {
      $data[] = array(
        'id' => $term->term_id,
        'text' => $term->name,
      );
    }

    wp_send_json_success(['options' => $data]);
  }

  /**
   * @param $request \WP_REST_Request
   * */
  public function archive_filter_types($request){
    $post_type = $request->get_param('post_type');

    $post_type_obj = get_post_types(array('name' => $post_type), 'objects');

    $data = array(
      array('id' => 'all', 'text' => $post_type_obj[$post_type]->labels->archives),
    );

    $taxonomies = get_object_taxonomies($post_type, 'objects');
    foreach($taxonomies as $id => $taxonomy) {
      $data[] = array(
        'id' => $id,
        'text' => 'In ' . $taxonomy->labels->singular_name,
      );
    }

    wp_send_json_success(['options' => $data]);
  }

  /**
   * @param $request \WP_REST_Request
   * */
  public function save_conditions($request){
	//phpcs:ignore WordPress.Security.NonceVerification.Recommended
    if(empty($_REQUEST['post_id']) || empty($_REQUEST['conditions'])) {
      wp_send_json_error();
    }

    $post_id = intval(sanitize_text_field( $_REQUEST['post_id'] ));//phpcs:ignore WordPress.Security.NonceVerification.Recommended
    $conditions = stripslashes(sanitize_text_field($_REQUEST['conditions']));//phpcs:ignore WordPress.Security.NonceVerification.Recommended
    $conditions = json_decode($conditions, true);

    if(!is_array($conditions)) {
      wp_send_json_error();
    }

    Condition::get_instance()->save_conditions($conditions, $post_id);
    wp_send_json_success();
  }

  public static function get_instance(){
    if(self::$instance === null) {
      self::$instance = new self();
    }
    return self::$instance;
  }
}
