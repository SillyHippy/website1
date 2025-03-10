<?php
namespace Tenweb_Builder\Widgets\Facebook\Widgets\Embed;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Tenweb_Builder\Widgets\Facebook\Classes\Facebook_SDK_Manager;

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

class Facebook_Embed extends Widget_Base {

  public function get_name() {
    return 'twbb_facebook-embed';
  }

  public function get_title() {
    return __( 'Facebook Embed', 'tenweb-builder' );
  }

  public function get_icon() {
    return 'twbb-facebook-embed twbb-widget-icon';
  }

  public function get_categories() {
    return [ 'tenweb-widgets' ];
  }

  public function get_keywords() {
    return [ 'facebook', 'social', 'embed', 'video', 'post', 'comment' ];
  }

  protected function register_controls() {
    $this->start_controls_section(
      'section_content',
      [
        'label' => __( 'Embed', 'tenweb-builder' ),
      ]
    );

    Facebook_SDK_Manager::add_app_id_control( $this );

    $this->add_control(
      'type',
      [
        'label' => __( 'Type', 'tenweb-builder' ),
        'type' => Controls_Manager::SELECT,
        'default' => 'post',
        'options' => [
          'post' => __( 'Post', 'tenweb-builder' ),
          'video' => __( 'Video', 'tenweb-builder' ),
          'comment' => __( 'Comment', 'tenweb-builder' ),
        ],
      ]
    );

    $this->add_control(
      'post_url',
      [
        'label' => __( 'URL', 'tenweb-builder' ),
        'default' => 'https://www.facebook.com/10Web.io/posts/851561032139972',
        'dynamic' => [
          'active' => true,
        ],
        'label_block' => true,
        'condition' => [
          'type' => 'post',
        ],
        'description' => __( 'Hover over the date next to the post, and copy its link address.', 'tenweb-builder' ),
      ]
    );

    $this->add_control(
      'video_url',
      [
        'label' => __( 'URL', 'tenweb-builder' ),
        'default' => 'https://www.facebook.com/148743955755020/videos/2238719583111026',
        'dynamic' => [
          'active' => true,
        ],
        'label_block' => true,
        'condition' => [
          'type' => 'video',
        ],
        'description' => __( 'Hover over the date next to the video, and copy its link address.', 'tenweb-builder' ),
      ]
    );

    $this->add_control(
      'comment_url',
      [
        'label' => __( 'URL', 'tenweb-builder' ),
        'default' => 'https://www.facebook.com/10Web.io/photos/a.166427270653355/846552035974205/?type=3&comment_id=857922988170443',
        'dynamic' => [
          'active' => true,
        ],
        'label_block' => true,
        'condition' => [
          'type' => 'comment',
        ],
        'description' => __( 'Hover over the date next to the comment, and copy its link address.', 'tenweb-builder' ),
      ]
    );

    $this->add_control(
      'include_parent',
      [
        'label' => __( 'Parent Comment', 'tenweb-builder' ),
        'type' => Controls_Manager::SWITCHER,
        'default' => '',
        'description' => __( 'Set to include parent comment (if URL is a reply).', 'tenweb-builder' ),
        'condition' => [
          'type' => 'comment',
        ],
      ]
    );

    $this->add_control(
      'show_text',
      [
        'label' => __( 'Full Post', 'tenweb-builder' ),
        'type' => Controls_Manager::SWITCHER,
        'default' => '',
        'description' => __( 'Show the full text of the post', 'tenweb-builder' ),
        'condition' => [
          'type' => [ 'post', 'video' ],
        ],
      ]
    );

    $this->add_control(
      'video_allowfullscreen',
      [
        'label' => __( 'Allow Full Screen', 'tenweb-builder' ),
        'type' => Controls_Manager::SWITCHER,
        'default' => '',
        'condition' => [
          'type' => 'video',
        ],
      ]
    );

    $this->add_control(
      'video_autoplay',
      [
        'label' => __( 'Autoplay', 'tenweb-builder' ),
        'type' => Controls_Manager::SWITCHER,
        'default' => '',
        'condition' => [
          'type' => 'video',
        ],
      ]
    );

    $this->add_control(
      'video_show_captions',
      [
        'label' => __( 'Captions', 'tenweb-builder' ),
        'type' => Controls_Manager::SWITCHER,
        'default' => '',
        'description' => __( 'Show captions if available (only on desktop).', 'tenweb-builder' ),
        'condition' => [
          'type' => 'video',
        ],
      ]
    );
  }

  public function render() {
    $settings = $this->get_settings_for_display();

    if ( empty( $settings['type'] ) ) {
      esc_html_e( 'Please set the embed type', 'tenweb-builder' );

      return;
    }

    if ( 'comment' === $settings['type'] && empty( $settings['comment_url'] ) || 'post' === $settings['type'] && empty( $settings['post_url'] ) || 'video' === $settings['type'] && empty( $settings['video_url'] ) ) {
      esc_html_e( 'Please enter a valid URL', 'tenweb-builder' );

      return;
    }

    $attributes = [
      // The style prevent's the `widget.handleEmptyWidget` to set it as an empty widget
      'style' => 'min-height: 1px',
    ];

    switch ( $settings['type'] ) {
      case 'comment':
        $attributes['class'] = 'elementor-facebook-widget fb-comment-embed';
        $attributes['data-href'] = esc_url( $settings['comment_url'] );
        $attributes['data-include-parent'] = 'yes' === $settings['include_parent'] ? 'true' : 'false';
        break;
      case 'post':
        $attributes['class'] = 'elementor-facebook-widget fb-post';
        $attributes['data-href'] = esc_url( $settings['post_url'] );
        $attributes['data-show-text'] = 'yes' === $settings['show_text'] ? 'true' : 'false';
        break;
      case 'video':
        $attributes['class'] = 'elementor-facebook-widget fb-video';
        $attributes['data-href'] = esc_url( $settings['video_url'] );
        $attributes['data-show-text'] = 'yes' === $settings['show_text'] ? 'true' : 'false';
        $attributes['data-allowfullscreen'] = 'yes' === $settings['video_allowfullscreen'] ? 'true' : 'false';
        $attributes['data-autoplay'] = 'yes' === $settings['video_autoplay'] ? 'true' : 'false';
        $attributes['data-show-captions'] = 'yes' === $settings['video_show_captions'] ? 'true' : 'false';
        break;
    }

    $this->add_render_attribute( 'embed_div', $attributes ); ?>

    <div <?php $this->print_render_attribute_string( 'embed_div' );?>></div><?php
  }

  public function render_plain_content() {}
}
\Elementor\Plugin::instance()->widgets_manager->register( new Facebook_Embed() );
