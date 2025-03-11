<?php

namespace Tenweb_Builder\Controls\QueryControl\Controls;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Base;
use Tenweb_Builder\Controls\QueryControl\QueryController;

if ( !defined('ABSPATH') ) {
  exit; // Exit if accessed directly
}

/**
 * Class Group_Control_Posts
 *
 * @package Tenweb_Builder\Controls\QueryControl\Controls
 */
class Group_Control_Posts extends Group_Control_Base {

  const INLINE_MAX_RESULTS = 15;

  protected static $fields;

  public static function get_type() {
    return 'posts';
  }

  /**
   * Init fields
   *
   * @return array
   */
  protected function init_fields() {

    $fields = [];
    $fields['post_type'] = [
      'label' => __('Source', 'tenweb-builder'),
      'type' => Controls_Manager::SELECT,
    ];
    $fields['posts_ids'] = [
      'label' => __('Search & Select', 'tenweb-builder'),
      'type' => QueryController::QUERY_CONTROL_ID,
      'post_type' => '',
      'options' => [],
      'label_block' => TRUE,
      'multiple' => TRUE,
      'filter_by' => 'product',
      'condition' => [
        'post_type' => 'by_id',
      ],
    ];
    $fields['authors'] = [
      'label' => __('Author', 'tenweb-builder'),
      'label_block' => TRUE,
      'type' => QueryController::QUERY_CONTROL_ID,
      'multiple' => TRUE,
      'default' => [],
      'options' => [],
      'filter_type' => 'author',
      'condition' => [
        'post_type!' => [
          'by_id',
          'current_query',
        ],
      ],
    ];

    return $fields;
  }

  /**
   * Prepare fields
   *
   * @param array $fields
   *
   * @return array
   */
  protected function prepare_fields( $fields ) {

    $args = $this->get_args();
    $post_types = self::get_public_post_types($args);
    $post_types_options = $post_types;
    $post_types_options['by_id'] = __('Manual Selection', 'tenweb-builder');
    $post_types_options['current_query'] = __('Current Query', 'tenweb-builder');
    $fields['post_type']['options'] = $post_types_options;
    $fields['post_type']['default'] = key($post_types);
    $fields['posts_ids']['object_type'] = array_keys($post_types);
    $taxonomy_filter_args = [
      'show_in_nav_menus' => TRUE,
    ];
    if ( !empty($args['post_type']) ) {
      $taxonomy_filter_args['object_type'] = [ $args['post_type'] ];
    }
    $taxonomies = get_taxonomies($taxonomy_filter_args, 'objects');
    foreach ( $taxonomies as $taxonomy => $object ) {
      $taxonomy_args = [
        'label' => $object->label,
        'type' => QueryController::QUERY_CONTROL_ID,
        'label_block' => TRUE,
        'multiple' => TRUE,
        'object_type' => $taxonomy,
        'options' => [],
        'condition' => [
          'post_type' => $object->object_type,
        ],
      ];
      $count = wp_count_terms($taxonomy);
      $options = [];
      // For large websites, use Ajax to search
      if ( $count > self::INLINE_MAX_RESULTS ) {
        $taxonomy_args['type'] = QueryController::QUERY_CONTROL_ID;
        $taxonomy_args['filter_type'] = 'taxonomy';
      }
      else {
        $taxonomy_args['type'] = Controls_Manager::SELECT2;
        $terms = get_terms([
                             'taxonomy' => $taxonomy,
                             'hide_empty' => FALSE,
                           ]);
        foreach ( $terms as $term ) {
          $options[$term->term_id] = $term->name;
        }
        $taxonomy_args['options'] = $options;
      }
      $fields[$taxonomy . '_ids'] = $taxonomy_args;
    }

    return parent::prepare_fields($fields);
  }

  /**
   * Get default options
   *
   * @return array
   */
  protected function get_default_options() {
    return [
      'popover' => FALSE,
    ];
  }

  /**
   *  Get public post types
   *
   * @param array $args
   *
   * @return array
   */
  public static function get_public_post_types( $args = [] ) {
    $post_type_args = [
      // Default is the value $public.
      'show_in_nav_menus' => TRUE,
    ];
    if ( !empty($args['post_type']) ) {
      $post_type_args['name'] = $args['post_type'];
    }
    $_post_types = get_post_types($post_type_args, 'objects');
    $post_types = [];
    foreach ( $_post_types as $post_type => $object ) {
      $post_types[$post_type] = $object->label;
    }

    return $post_types;
  }

  public static function get_current_post_id() {
  	if ( isset( \Elementor\Plugin::instance()->documents ) ) {
  		return \Elementor\Plugin::instance()->documents->get_current()->get_main_id();
  	}

  	return get_the_ID();
  }
}
