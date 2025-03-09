<?php
namespace Tenweb_Builder\Widgets\Facebook\Widgets\Comments;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Tenweb_Builder\Widgets\Facebook\Classes\Facebook_SDK_Manager;
use Tenweb_Builder\Widgets\Facebook\Widgets;
use Tenweb_Builder\Widgets\Facebook\FB_Module;

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

class Facebook_Comments extends Widget_Base {

  public function get_name() {
    return 'twbb_facebook-comments';
  }

  public function get_title() {
    return __( 'Facebook Comments', 'tenweb-builder' );
  }

  public function get_icon() {
    return 'twbb-facebook-comments twbb-widget-icon';
  }

  public function get_keywords() {
    return [ 'facebook', 'comments', 'embed' ];
  }

  public function get_categories() {
    return [ 'tenweb-widgets' ];
  }

  protected function register_controls() {
    $this->start_controls_section(
      'section_content',
      [
        'label' => __( 'Comments Box', 'tenweb-builder' ),
      ]
    );

    Facebook_SDK_Manager::add_app_id_control( $this );

    $this->add_control(
      'comments_number',
      [
        'label' => __( 'Comment Count', 'tenweb-builder' ),
        'type' => Controls_Manager::NUMBER,
        'min' => 5,
        'max' => 100,
        'default' => '10',
        'description' => __( 'Minimum number of comments: 5', 'tenweb-builder' ),
      ]
    );

    $this->add_control(
      'order_by',
      [
        'label' => __( 'Order By', 'tenweb-builder' ),
        'type' => Controls_Manager::SELECT,
        'default' => 'social',
        'options' => [
          'social' => __( 'Social', 'tenweb-builder' ),
          'reverse_time' => __( 'Reverse Time', 'tenweb-builder' ),
          'time' => __( 'Time', 'tenweb-builder' ),
        ],
      ]
    );

    $this->add_control(
      'url_type',
      [
        'label' => __( 'Target URL', 'tenweb-builder' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
          FB_Module::URL_TYPE_CURRENT_PAGE => __( 'Current Page', 'tenweb-builder' ),
          FB_Module::URL_TYPE_CUSTOM => __( 'Custom', 'tenweb-builder' ),
        ],
        'default' => FB_Module::URL_TYPE_CURRENT_PAGE,
        'separator' => 'before',
      ]
    );

    $this->add_control(
      'url_format',
      [
        'label' => __( 'URL Format', 'tenweb-builder' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
          FB_Module::URL_FORMAT_PLAIN => __( 'Plain Permalink', 'tenweb-builder' ),
          FB_Module::URL_FORMAT_PRETTY => __( 'Pretty Permalink', 'tenweb-builder' ),
        ],
        'default' => FB_Module::URL_FORMAT_PLAIN,
        'condition' => [
          'url_type' => FB_Module::URL_TYPE_CURRENT_PAGE,
        ],
      ]
    );

    $this->add_control(
      'url',
      [
        'label' => __( 'Link', 'tenweb-builder' ),
        'placeholder' => __( 'https://your-link.com', 'tenweb-builder' ),
        'label_block' => true,
        'condition' => [
          'url_type' => FB_Module::URL_TYPE_CUSTOM,
        ],
      ]
    );

    $this->end_controls_section();
  }

  public function render() {
    $settings = $this->get_settings();

    if ( FB_Module:: URL_TYPE_CURRENT_PAGE === $settings['url_type']) {
      $permalink = Facebook_SDK_Manager::get_permalink( $settings );
    } else {
      if ( ! filter_var( $settings['url'], FILTER_VALIDATE_URL ) ) {
        echo $this->get_title() . ': ' . esc_html__( 'Please enter a valid URL', 'tenweb-builder' ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        return;
      }
      $permalink = esc_url( $settings['url'] );
    }

    $attributes = [
      'class' => 'elementor-facebook-widget fb-comments',
      'data-href' => $permalink,
      'data-width' => '100%',
      'data-numposts' => $settings['comments_number'],
      'data-order-by' => $settings['order_by'],
      // The style prevent's the `widget.handleEmptyWidget` to set it as an empty widget
      'style' => 'min-height: 1px',
    ];

    $this->add_render_attribute( 'embed_div', $attributes );

    ?><div <?php $this->print_render_attribute_string( 'embed_div' );?> ></div>
      <?php
  }

  public function render_plain_content() {}
}
\Elementor\Plugin::instance()->widgets_manager->register( new Facebook_Comments() );
