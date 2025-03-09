<?php
namespace Tenweb_Builder;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

/**
 * Class Posts
 */
class PostsArchive extends Posts {

  private $additional_info = [];

  public function __construct(array $data = [], $args = null){
    parent::__construct($data, $args);
  }

  public function get_name() {
    return 'twbb-posts-archive';
  }

  public function get_title() {
    return __('Posts Archive', 'tenweb-builder');
  }

  public function get_icon() {
    return ['twbb-posts-archive twbb-widget-icon'];
  }

  public function get_categories() {
    return ['tenweb-builder-widgets'];
  }

  public function get_keywords() {
    return [ 'posts', 'cpt', 'archive', 'loop', 'query', 'cards', 'custom post type' ];
  }

  protected function register_controls() {
    parent::register_controls();

    $this->register_advanced_section_controls();

    $this->update_control(
      'pagination_type',
      [
        'default' => 'numbers',
      ]
    );
  }

  public function register_advanced_section_controls() {
    $this->start_controls_section(
      'section_advanced',
      [
        'label' => __( 'Advanced', 'tenweb-builder' ),
      ]
    );

    $this->add_control(
      'nothing_found_message',
      [
        'label' => __( 'Nothing Found Message', 'tenweb-builder' ),
        'type' => Controls_Manager::TEXTAREA,
        'default' => __( 'It seems we can\'t find what you\'re looking for.', 'tenweb-builder' ),
        'dynamic' => [
          'active' => true,
        ],
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section(
      'section_nothing_found_style',
      [
        'tab' => Controls_Manager::TAB_STYLE,
        'label' => __( 'Nothing Found Message', 'tenweb-builder' ),
        'condition' => [
          'nothing_found_message!' => '',
        ],
      ]
    );

    $this->add_control(
      'nothing_found_color',
      [
        'label' => __( 'Color', 'tenweb-builder' ),
        'type' => Controls_Manager::COLOR,
          'global' => [
              'default' => Global_Colors::COLOR_TEXT,
          ],
        'selectors' => [
          '{{WRAPPER}} .elementor-posts-nothing-found' => 'color: {{VALUE}};',
        ],
      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'nothing_found_typography',
          'global' => [
              'default' => Global_Typography::TYPOGRAPHY_TEXT,
          ],
        'selector' => '{{WRAPPER}} .elementor-posts-nothing-found',
      ]
    );

    $this->end_controls_section();
  }


  protected function get_query_args(){
    global $wp_query;

    if(Templates::get_instance()->is_twbb_template()['template_type']) {
      $query_args = array(
        'posts_per_page' => 10,
        'post_type' => 'post',
      );
    } else {
      $query_args = $wp_query->query_vars;
      $query_args['tax_query'] = (!empty($wp_query->tax_query->queries)) ? $wp_query->tax_query->queries : []; //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
      $query_args['meta_query'] = (!empty($wp_query->meta_query->queries)) ? $wp_query->meta_query->queries : []; //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
    }
    $query_args['additional_info'] = $this->get_query_args_additional_info();
    $this->additional_info = $query_args['additional_info'];

    return $query_args;
  }

  protected function get_js_params(){
    global $wp_query;
    $js_params = parent::get_js_params();

    if(Templates::get_instance()->is_twbb_template()['template_type'] === false) {
      $posts = $wp_query->posts;
      self::add_posts_additional_info($posts, $this->additional_info);
      $js_params['first_page_data'] = [
        'posts' => $posts,
        'pages_count' => $wp_query->max_num_pages
      ];
    }

    return $js_params;
  }

  protected function register_query_section_controls(){

  }

  protected function add_widget_controll($key){
    if($key === 'posts_per_page') {
      return false;
    } else {
      return true;
    }
  }
}

\Elementor\Plugin::instance()->widgets_manager->register(new PostsArchive());

