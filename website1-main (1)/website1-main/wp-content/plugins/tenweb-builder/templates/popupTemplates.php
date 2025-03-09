<?php

namespace Tenweb_Builder;

class PopupTemplates {
  protected static $instance = null;

  /*
  * Function creates post duplicate as a draft and redirects then to the edit post screen
  */
  public function twbb_dublicate_teplate_post(){
    global $wpdb;
    if ( \Tenweb_Builder\Modules\Helper::get('post', '') === '' ) {
      wp_die('No post to duplicate has been supplied!');
    }

    $page_title = \Tenweb_Builder\Modules\Helper::get('page_title');
    $template_type = \Tenweb_Builder\Modules\Helper::get('template_type');

    /*
     * get the original post id
     */
    $post_id = absint( \Tenweb_Builder\Modules\Helper::get('post'));
    /*
     * and all the original post data then
     */
    $post = get_post( $post_id );



    /*
     * if you don't want current user to be the new post author,
     * then change next couple of lines to this: $new_post_author = $post->post_author;
     */
    $current_user = wp_get_current_user();
    $new_post_author = $current_user->ID;

    /*
     * if post data exists, create the post duplicate
     */
    if (isset( $post ) && $post !== null) {

      /*duplicate all post meta just in two SQL queries*/
      $post_metas = get_post_meta( $post_id );
      $metas = array();
      foreach($post_metas as $key=>$val){
        $metas[$key] = $val[0];
      }

      /*
       * new post data array
       */
      $args = array(
        'comment_status' => $post->comment_status,
        'meta_input'     => $metas,
        'ping_status'    => $post->ping_status,
        'post_author'    => $new_post_author,
        'post_content'   => $post->post_content,
        'post_excerpt'   => $post->post_excerpt,
        'post_name'      => $template_type.' of '.$page_title,
        'post_parent'    => $post->post_parent,
        'post_password'  => $post->post_password,
        'post_status'    => 'publish',
        'post_title'     => $template_type.' of '.$page_title,
        'post_type'      => 'elementor_library',
        'to_ping'        => $post->to_ping,
        'menu_order'     => $post->menu_order
      );

      /*
       * insert the post by wp_insert_post() function
       */
      $new_post_id = wp_insert_post( $args );

      /*
       * get all current post terms ad set them to the new post draft
       */
      $taxonomies = get_object_taxonomies($post->post_type); // returns array of taxonomy names for post type, ex array("category", "post_tag");
      foreach ($taxonomies as $taxonomy) {
        $post_terms = wp_get_object_terms($post_id, $taxonomy, array('fields' => 'slugs'));
        wp_set_object_terms($new_post_id, $post_terms, $taxonomy, false);
      }
      $params = array();
      $params['template_post_id'] = $new_post_id;
      $params['template_type'] = 'twbb_'.strtolower($template_type);
      $params['page_type'] = \Tenweb_Builder\Modules\Helper::get('page_type');
      $params['specific_page'] = \Tenweb_Builder\Modules\Helper::get('current_post');

      $this->set_condition_data( $params );
      /*
       * finally, redirect to the edit post screen for the new draft
       */
      wp_send_json(['post' => $new_post_id]);
      exit;
    } else {
      //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
      wp_die('Post creation failed, could not find original post: ' . $post_id);
    }
  }

  public function set_condition_data( $params ) {
    $page_type = $params['page_type'];
    $template_post_id = $params['template_post_id'];
    $specific_page = $params['specific_page'];
    $condition_data = [];
    $condition_data[] = array(
      'condition_type'  =>  (isset($params['save_status']) && $params['save_status'] !== "") ? $params['save_status'] : 'include',
      'page_type'       =>  $page_type,
      'post_type'       =>  'page',
      'filter_type'     =>  'specific_posts',
      'specific_pages'  =>  $specific_page,
      'order'           =>  '0',
      'popup'           =>  isset($params['save_status']) ? $params['save_status'] : "",
    );

    $this->remove_spacific_page_data($specific_page, $params['template_type']);
    Condition::get_instance()->save_conditions( $condition_data, $template_post_id, $params['template_type'] );
  }

  /**
   * Save templates from popup
   * */
  public function twbb_save_templates( $param = "" ) {
    $data = array(
      'post' => \Tenweb_Builder\Modules\Helper::get('current_post_id'),
      'page_type' => \Tenweb_Builder\Modules\Helper::get('page_type'),
      'templates' => array(
                      'twbb_header'   => \Tenweb_Builder\Modules\Helper::get('header_template'),
                      'twbb_single'   => \Tenweb_Builder\Modules\Helper::get('single_template'),
                      'twbb_single_product'   => \Tenweb_Builder\Modules\Helper::get('single_product_template'),
                      'twbb_archive'  => \Tenweb_Builder\Modules\Helper::get('archive_template'),
                      'twbb_archive_products'  => \Tenweb_Builder\Modules\Helper::get('archive_products_template'),
                      'twbb_footer'   => \Tenweb_Builder\Modules\Helper::get('footer_template'),
                    )
    );
    foreach ( $data['templates'] as $key => $value ) {
      if ( $value !== "" ) {
        $params = array();
        $params['template_post_id'] = $value;
        $params['page_type'] = $data['page_type'];
        $params['specific_page'] = $data['post'];
        $params['template_type'] = $key;
        $params['save_status'] = $param;
        $this->set_condition_data( $params );
      }
    }
  }

  /**
   * Remove specific page id from wp_options
   *
   * @param $specific_page_id integer
   * @param $template_type 'twbb_header', 'twbb_single','twbb_footer'
   * */
  public function remove_spacific_page_data( $specific_page_id, $template_type ) {
    $datas =  get_option( 'twbb_singular_conditions' );
    foreach ( $datas as $datakey => $dataval ) {
      foreach ( $dataval as $datakey1 => $dataval1 ) {
        foreach ( $dataval1["specific_pages"] as $datakey2 => $dataval2 ) {
          if ( $dataval2 === $specific_page_id ) {
            $type = get_post_meta( $datakey, '_elementor_template_type', TRUE);
            if ( $type === $template_type ) {
              if ( count($dataval1["specific_pages"]) === 1 ) {
                unset($datas[$datakey][$datakey1]);
              }
              else {
                $datas[$datakey][$datakey1]['specific_pages'][$datakey2] = '';
              }
            }
          }
        }
      }
    }
    $opt_name = 'twbb_singular_conditions';
    update_option($opt_name, $datas);
  }

  public static function get_instance(){
    if(self::$instance === null) {
      self::$instance = new self();
    }
    return self::$instance;
  }


}
