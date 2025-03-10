<?php
namespace Tenweb_Builder;


class Condition {

  protected static $instance = null;
  protected $page_type = null;
  protected $page_conditions = null;

  public function __construct(){
    include_once TWBB_DIR . '/templates/condition/condition-item.php';
    add_action('elementor/editor/footer', [$this, 'condition_popup'], 11);
    add_action('rest_api_init', [$this, 'register_rest_routes']);
    add_action('delete_post', [$this, 'delete_template_conditions']);
  }


  public function condition_popup(){
    include_once TWBB_DIR . '/templates/condition/views/condition-popup.php';
  }


  public function register_rest_routes(){
    include_once TWBB_DIR . '/templates/condition/rest-api.php';
    ConditionRestApi::get_instance();
  }

  public function save_conditions($condition_data, $template_id, $template_type = "") {
    $conditions = [
      'archive' => [],
      'singular' => [],
      'general' => []
    ];

    $page_id = isset($condition_data[0]['specific_pages'][0]) ? $condition_data[0]['specific_pages'][0] : null;
    $post_type = isset($condition_data[0]['post_type']) ? $condition_data[0]['post_type'] : null; // page or post

    if( empty($condition_data) ) {
      $this->delete_template_conditions($template_id);
      return;
    }

    $order = 0;
    foreach($condition_data as $condition) {
      $c = new ConditionItem();
      $c->set_and_validate_fields($condition);
      $data = $c->get_as_array();

      $data['order'] = $order;
      $conditions[$c->page_type][] = $data;
      $order++;
    }

    $excluded_keys = array(); // keep excluded templates keys
    foreach($conditions as $key => $condition) { // archive, singular, general
      $conditions_opt = get_option('twbb_' . $key . '_conditions', []);
      if( isset($condition_data[0]['popup']) && $condition_data[0]['popup'] === "include" && !empty($conditions_opt) ) { // Check if save come from template edit popup and input

          if( empty($condition) ) {
              continue;
          } else {
              $identical_status = false;
              if( array_key_exists($template_id, $conditions_opt) ) { // check array has key template_id
                foreach ( $conditions_opt[$template_id] as $cond ) {
                  if ( empty(array_diff($cond, $condition[0]))) { // check the array has the same condition
                    foreach ($cond['specific_pages'] as $sp){
                      if($sp === $condition[0]['specific_pages'][0]){
                        $identical_status = true;
                      }
                    }
                  }
                }
              }
               $conditions_opt = $this->delelte_excluded( $conditions_opt, $template_id, $condition[0]['specific_pages'][0] );
              if( !$identical_status && !empty( $conditions_opt[$template_id] ) ) { // if condition absent in array and the array is not empty
                $conditions_opt = $this->delelte_excluded( $conditions_opt, $template_id, $condition[0]['specific_pages'][0] );
                $conditions_opt[$template_id][] = $condition[0];
              } else if ( !$identical_status ) {
                $conditions_opt = $this->delelte_excluded( $conditions_opt, $template_id, $condition[0]['specific_pages'][0] );
                $conditions_opt[$template_id] = $condition;
              }
          }
          update_option('twbb_' . $key . '_conditions', $conditions_opt);
      } else if(isset($condition_data[0]['popup']) && $condition_data[0]['popup'] === "exclude") { // exclude post from templates
          foreach ($conditions_opt as $datakey => $dataval ) {
            $breake_status = false;

            $type = get_post_meta( $datakey, '_elementor_template_type', TRUE); // template type twbb_header, twbb_footer, twbb_single
            if( $type !== $template_type || empty($dataval) ) { // check if different template tipe or value is empty no need to add exclude
              continue;
            }
            if( $key === 'singular' || $key === 'archive' ) {
              if( $this->compare_arrays( $conditions_opt[$datakey], $condition[0] ) ) { //if template has the same exclude
                $breake_status = true;
              }
              foreach ( $dataval as $datakey1 => $dataval1 ) {
                  if( $breake_status ) break;
                  if( ($dataval1['post_type'] === $post_type || $dataval1['post_type'] === "all") && $dataval1['filter_type'] === "all" ) { // if page type the same and condition for all pages
                      $conditions_opt[$datakey][] = $condition[0];
                      $excluded_keys[] = $datakey;
                      $breake_status = true;
                      break;
                  }
                }
                update_option('twbb_' . $key . '_conditions', $conditions_opt);
            } else if( $key === 'general' ) {
                if( $type !== $template_type || empty($dataval) ){ // check if different template tipe or value is empty no need to add exclude
                  continue;
                }

                if( !empty($excluded_keys) ) { // Check if already excluded during singular cycle
                  foreach ( $excluded_keys as $excluded_key ) {
                    if( $excluded_key === $datakey ) {
                      $breake_status = true;
                      break;
                    }
                  }
                }
                if( !$breake_status ) {
                  $conditions_opt = get_option('twbb_singular_conditions', []);
                  if( !empty( $condition ) && $this->compare_arrays( $conditions_opt[$datakey][$dataval], $condition[0] ) ) { //if template has the same exclude
                    continue;
                  }
                  $conditions_opt[$datakey][] = $conditions['singular'][0];
                  update_option('twbb_singular_conditions', $conditions_opt);
                }
            }
          }
      } else {
        $conditions_opt[$template_id] = $condition;
        update_option('twbb_' . $key . '_conditions', $conditions_opt);
      }
    }
  }

  public function delelte_excluded( $conditions_opt, $template_id, $page_id ) {
    if(!isset($conditions_opt[$template_id])) return $conditions_opt;
    foreach ($conditions_opt[$template_id] as $key => $val) {
      if( $val["condition_type"] === "exclude" ) {
        foreach ( $val['specific_pages'] as $sp => $sp_val ) {
          if($sp_val === $page_id) {
            unset($conditions_opt[$template_id][$key]);
          }
        }
      }
    }
    return $conditions_opt;
  }

  public function compare_arrays( $array1, $array2 ) {
    foreach ( $array1 as $array ) {
      if( empty(array_diff($array,$array2)) ) {
        return true;
      }
    }
    return false;
  }

  /**
   * @param $id integer template id
   * @param $type 'all', 'archive','singular','general'
   * @param $with_names boolean
   * @return array
   * */
  public function get_template_condition($id, $type = 'all', $with_names = false) {
    $type_valid_values = ['all', 'archive', 'singular', 'general'];
    //phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
    if(!in_array($type, $type_valid_values)) {
      $type = 'all';
    }

    if($type === 'all') {
      $conditions = $this->get_template_condition_all($id);
    } else {
      $conditions = get_option('twbb_' . $type . '_conditions', []);
    }

    if(isset($conditions[$id])) {
      if($with_names === true) {
        return $this->add_post_term_names($conditions[$id]);
      } else {
        return $conditions[$id];
      }
    } else {
      return [];
    }

  }


    // function is written for Woocommerce Package for getting product template id
  public function get_product_template($product_id, $page_type = 'singular') {
      $page_conditions = [];
      $conditions = get_option('twbb_' . $page_type . '_conditions', []);

      if($page_type !== 'general') {
          $general_conditions = get_option('twbb_general_conditions', []);
          $conditions = $this->array_merge($conditions, $general_conditions);
      }

      foreach($conditions as $template_id => $template_conditions) {
          if(empty($template_conditions)) {
              continue;
          }

          if(get_post_status($template_id) !== 'publish') {
              continue;
          }
          $valid_conditions = $this->get_template_valid_conditions($template_conditions, $template_id, $product_id);
          if(!empty($valid_conditions)) {
              $page_conditions[$template_id] = $valid_conditions;
          }

      }

      $condition = null;
      if(!empty($page_conditions)) {
          $single_condition = $this->get_condition_by_template_type('twbb_single', $page_conditions);
          $product_single_condition = $this->get_condition_by_template_type('twbb_single_product', $page_conditions);
          $condition = $this->array_merge($single_condition, $product_single_condition);
      }

      if($condition !== null) {
          return $condition->template_id;
      } else {
          return 0;
      }
  }

  private function get_template_condition_all($id){
    $archive = get_option('twbb_archive_conditions', []);
    $single = get_option('twbb_singular_conditions', []);
    $general = get_option('twbb_general_conditions', []);


    $archive = (!empty($archive[$id])) ? $archive[$id] : [];
    $single = (!empty($single[$id])) ? $single[$id] : [];
    $general = (!empty($general[$id])) ? $general[$id] : [];

    $condition = array_merge($archive, $single, $general);

    usort($condition, [$this, 'sort_conditions']);

    return [$id => $condition];
  }

  private function add_post_term_names($conditions_data){

    foreach($conditions_data as $index => $data) {

      if($data['page_type'] === 'general' || $data['filter_type'] === 'all' || empty($data['specific_pages'])) {
        continue;
      }

      $pages = [];
      if($data['page_type'] === 'singular' && $data['filter_type'] === 'specific_posts') {

        $args = array(
          'numberposts' => -1,
          'post_type' => 'any',
          'include' => $data['specific_pages'],
          'post_status' => 'any'
        );
		//phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.get_posts_get_posts
        $posts = get_posts($args);

        foreach($posts as $post) {
          $pages[] = ['id' => $post->ID, 'text' => $post->post_title];
        }

      } else {
        $args = array(
          'hide_empty' => false,
          'term_taxonomy_id' => $data['specific_pages']
        );

        $terms = get_terms($args);
        foreach($terms as $term) {
          $pages[] = ['id' => $term->term_id, 'text' => $term->name];
        }
      }

      $conditions_data[$index]['specific_pages_options'] = $pages;

    }

    return $conditions_data;
  }

  public function get_header_template(){
    return $this->get_template(HeaderTemplate::get_slug());
  }

  public function get_single_template(){
    return $this->get_template(SingleTemplate::get_slug());
  }

  public function get_single_product_template(){
    return $this->get_template(SingleProductTemplate::get_slug());
  }

  public function get_archive_template(){
    return $this->get_template(ArchiveTemplate::get_slug());
  }

  public function get_archive_products_template(){
    return $this->get_template(ArchiveProductsTemplate::get_slug());
  }

  public function get_footer_template(){
    return $this->get_template(FooterTemplate::get_slug());
  }

  private function get_template($type, $ignore_editor_mode = false){
    global $post;

    if($ignore_editor_mode === false) {

      $is_twbb_template = Templates::get_instance()->is_twbb_template()['template_type'];

      if( $is_twbb_template !== false ) {
        return $post->ID;
      }

    }
    if($this->page_conditions === null) {
      $this->set_page_conditions();
    }

    $condition = null;
    if(!empty($this->page_conditions)) {
      $condition = $this->get_condition_by_template_type($type);
    }

    if($condition !== null) {
      return $condition->template_id;
    } else {
      return 0;
    }

  }

  private function set_page_conditions() {
    if($this->page_type === null) {
      $this->set_page_type();
    }
    /* in DB kept only singular condition type which included product condition */
    if( $this->page_type === 'singular_product' ) {
        $page_type = 'singular';
    } else if($this->page_type === 'archive_products') {
        $page_type = 'archive';
    } else {
        $page_type = $this->page_type;
    }
    $this->page_conditions = [];
    $conditions = get_option('twbb_' . $page_type . '_conditions', []);

    if($this->page_type !== 'general') {
      $general_conditions = get_option('twbb_general_conditions', []);
      $conditions = $this->array_merge($conditions, $general_conditions);
    }

    foreach($conditions as $template_id => $template_conditions) {
      if(empty($template_conditions)) {
        continue;
      }

      if(get_post_status($template_id) !== 'publish') {
        continue;
      }
      $valid_conditions = $this->get_template_valid_conditions($template_conditions, $template_id);
      if(!empty($valid_conditions)) {
        $this->page_conditions[$template_id] = $valid_conditions;
      }

    }
  }

  private function get_template_valid_conditions($template_conditions, $template_id, $product_id = null){
    $valid_conditions = [];

    foreach($template_conditions as $condition) {
      $c = new ConditionItem();

      $c->set_fields($condition);
      $c->set_template_id($template_id);
      $c->set_template_type();

      if( $product_id !== null ) {
          if ($c->condition_for_specific_product($product_id) === false) {
              continue;
          }
      } else {
          if ($c->condition_for_post($this->page_type) === false) {
              continue;
          }
      }

      if($c->condition_type === 'include') {
        $valid_conditions[] = $c;
      } else {
        $valid_conditions = [];
        break;
      }

    }

    return $valid_conditions;
  }


  private function get_condition_by_template_type($template_type, $page_conditions = null) {
    if( $page_conditions === null ) {
        $page_conditions = $this->page_conditions;
    }
    if(empty($page_conditions)) {
      return null;
    }

    $max_level = 0;
    $condition_obj = null;
    foreach($page_conditions as $template_id => $conditions) {
      foreach($conditions as $condition) {
        if($condition->get_template_type() === $template_type && $condition->get_level() >= $max_level) {
          $condition_obj = $condition;
          $max_level = $condition_obj->get_level();
        }
      }
    }

    return $condition_obj;
  }

  private function set_page_type() {
    if(function_exists('is_product') && is_product()) {
      $this->page_type = 'singular_product';
    } else if(is_page() || is_single() || is_front_page() || is_404()) {
      $this->page_type = 'singular';
    } else if(function_exists('is_woocommerce') && (is_product_category() || is_product_tag() || is_woocommerce() || is_shop()) ) {
       $this->page_type = 'archive_products';
    } else if(is_archive() || is_search() || is_home()) {
       $this->page_type = 'archive';
    } else {
       $this->page_type = 'general';
    }
  }

  public function get_page_type(){

    if($this->page_type === null) {
      $this->set_page_type();
    }

    return $this->page_type;
  }

  public function sort_conditions($a, $b){
    if($a['order'] === $b['order']) {
      return 0;
    }
    return ($a['order'] < $b['order']) ? -1 : 1;
  }

  private function array_merge($arr1, $arr2){
    foreach($arr1 as $key => $value) {
      if(isset($arr2[$key])) {
        $arr1[$key] = array_merge($value, $arr2[$key]);
      }
    }

    foreach($arr2 as $key => $value) {
      if(!isset($arr1[$key])) {
        $arr1[$key] = $value;
      }
    }

    return $arr1;
  }

  public function delete_template_conditions($post_id){
    $this->delete_template_condition($post_id, 'twbb_singular_conditions');
    $this->delete_template_condition($post_id, 'twbb_archive_conditions');
    $this->delete_template_condition($post_id, 'twbb_general_conditions');
  }

  public function delete_template_condition($post_id, $opt_name){
    $conditions = get_option($opt_name, []);
    if(isset($conditions[$post_id])) {
      unset($conditions[$post_id]);
    }
    update_option($opt_name, $conditions);
  }

  public static function get_instance(){
    if(self::$instance === null) {
      self::$instance = new self();
    }
    return self::$instance;
  }
}
