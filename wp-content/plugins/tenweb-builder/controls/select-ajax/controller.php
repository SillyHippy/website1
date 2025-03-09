<?php

namespace Tenweb_Builder;

class SelectAjaxController {

  protected static $instance = null;

  private function __construct() {
    add_action('elementor/controls/controls_registered', array($this, 'register_controls'));
    add_action('elementor/ajax/register_actions', array($this, 'register_ajax_actions'));
    add_action('wp_ajax_elementor_twbb_editor_select_ajax_get_options', array($this, 'get_options'));
  }

  /**
   * @param $ElementorControls_Manager \Elementor\Controls_Manager
   * */
  public function register_controls($ElementorControls_Manager){
    include_once TWBB_DIR . '/controls/select-ajax/select-ajax.php';

    $ElementorControls_Manager->register( new SelectAjax() );
  }

  /**
   * @param \Elementor\Core\Ajax_Manager $ajax_manager
   */
  public function register_ajax_actions($ajax_manager){
    $ajax_manager->register_ajax_action('twbb_editor_select_ajax_get_saved_options', [$this, 'get_saved_options']);
  }

  public function get_options() {
    $results = $this->query($_POST, '1');//phpcs:ignore WordPress.Security.NonceVerification.Missing
    wp_send_json_success( ['results' => $results] );
  }

  public function get_saved_options($request){
    $results = $this->query($request, '2');
    return $results;
  }

  /**
   * @param $options_format string [1,2]
   * @param $request array
   * @return array
   * */
  private function query( $request, $options_format ) {
    $ids = (isset($request['id'])) ? (array)($request['id']) : null;
    $filter_by = (isset($request['filter_by'])) ? $request['filter_by'] : null;
    $search = (isset($request['q'])) ? $request['q'] : null;
    $options = array();
    switch( $filter_by ) {
      case 'author': {
        $query_params = array(
          'who' => 'authors',
          'fields' => array('ID', 'display_name')
        );

        if ( $search !== null ) {
          $query_params['search'] = '*' . $search . '*';
          $query_params['search_columns'] = array('user_login', 'user_nicename');
        }

        if ( !empty($ids) ) {
          $query_params['include'] = $ids;
        }

        $user_query = new \WP_User_Query($query_params);
        $options = $this->user_format($user_query, $options_format);
        break;
      }
      case 'post': {
        $query_params = array(
          'post_type' => 'any',
          'posts_per_page' => -1
        );

        if ( $search !== null ) {
          $query_params['s'] = $search;
        }

        if ( !empty($ids) ) {
          $query_params['post__in'] = $ids;
        }

        $post_query = new \WP_Query($query_params);
        $options = $this->post_format($post_query, $options_format);
        break;
      }
      case 'product': {
        $options = $this->get_posts_by_type( ['search' => $search, 'format' => $options_format, 'search_post_type' => 'product'] );
        break;
      }
      case 'any_type': {
        $options = $this->get_posts_by_type( ['search' => $search, 'format' => $options_format, 'search_post_type' => 'any'] );
        break;
      }
      default:
      break;
    }
    return $options;
  }

  /**
   * Get products
   *
   * @param array $args
   *
   * @return array
   */
  private function get_posts_by_type( $args = array() ) {
    $search = $args['search'];
    $format = $args['format'];
    $search_post_type = $args['search_post_type'];
    $query_args = [
      'post_type' => $search_post_type,
      'post_status' => 'publish',
      'ignore_sticky_posts' => true
    ];
    if ( !empty($search) ) {
      $query_args['s'] = $search;
    }
    $posts = [];
    $query = new \WP_Query($query_args);
    if ( !empty($query->posts) ) {
        $posts = $this->post_format( $query, $format );
    }

    return $posts;
  }

  /**
   * @param $query \WP_Query
   * @param $options_format string
   * @return array
   * */
  private function post_format($query, $options_format) {
    $options = array();
    foreach($query->posts as $post) {
      if($options_format == "1") {//phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
        $options[] = array(
          "id" => $post->ID,
          "text" => $post->post_title,
        );
      } else {
        $options[$post->ID] = $post->post_title;
      }
    }

    return $options;
  }

  /**
   * @param $query \WP_User_Query
   * @param $options_format string
   * @return array
   * */
  private function user_format($query, $options_format) {
    $options = array();
    foreach($query->get_results() as $user) {
      if($options_format == "1") {//phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
        $options[] = array(
          "id" => $user->ID,
          "text" => $user->display_name,
        );
      } else {
        $options[$user->ID] = $user->display_name;
      }
    }

    return $options;
  }

  public static function get_instance(){

    if(self::$instance === null) {
      self::$instance = new self();
    }

    return self::$instance;
  }
}
