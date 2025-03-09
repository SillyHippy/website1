<?php

namespace Tenweb_Builder;

use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;

if(!defined('ABSPATH')) {
  exit; // Exit if accessed directly
}

class Post_Comments extends Widget_Base {

  public function get_name(){
    return Builder::$prefix . 'post-comments';
  }

  public function get_title(){
    return __('Post Comments', 'tenweb-builder');
  }

  public function get_icon() {
    return 'twbb-post-comments twbb-widget-icon';
  }

  public function get_categories(){
    return ['tenweb-builder-widgets'];
  }

  protected function register_controls() {

    $this->start_controls_section(
      'button_style',
      [
        'label' => __( 'Button', 'tenweb-builder' ),
        'tab' => Controls_Manager::TAB_STYLE,
      ]
    );
    $this->add_responsive_control(
      'button_align',
      [

        'label' => __( 'Alignment', 'tenweb-builder' ),
        'type' => Controls_Manager::CHOOSE,
        'options' => [
          'left'    => [
            'title' => __( 'Left', 'tenweb-builder' ),
            'icon' => 'fa fa-align-left',
          ],
          'right' => [
            'title' => __( 'Right', 'tenweb-builder' ),
            'icon' => 'fa fa-align-right',
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} #respond .form-submit input#submit' => 'float: {{VALUE}}',
        ]
      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'button_typography',
          'global' => [
              'default' => Global_Typography::TYPOGRAPHY_ACCENT,
          ],
        'selector' => '{{WRAPPER}} #respond .form-submit input#submit',
      ]
    );

    $this->start_controls_tabs( 'tabs_button_style' );

    $this->start_controls_tab(
      'tab_button_normal',
      [
        'label' => __( 'Normal', 'tenweb-builder' ),
      ]
    );

    $this->add_control(
      'button_text_color',
      [
        'label' => __( 'Text Color', 'tenweb-builder' ),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} #respond .form-submit input#submit' => 'color: {{VALUE}};',
        ],
      ]
    );

    $this->add_control(
      'button_background_color',
      [
        'label' => __( 'Background Color', 'tenweb-builder' ),
        'type' => Controls_Manager::COLOR,
          'global' => [
              'default' => Global_Colors::COLOR_ACCENT,
          ],
        'selectors' => [
          '{{WRAPPER}} #respond .form-submit input#submit' => 'background-color: {{VALUE}};',
        ],
      ]
    );

    $this->end_controls_tab();

    $this->start_controls_tab(
      'tab_button_hover',
      [
        'label' => __( 'Hover', 'tenweb-builder' ),
      ]
    );

    $this->add_control(
      'button_hover_color',
      [
        'label' => __( 'Text Color', 'tenweb-builder' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} #respond .form-submit input#submit:hover, {{WRAPPER}} #respond .form-submit input#submit:focus' => 'color: {{VALUE}};',
        ],
      ]
    );

    $this->add_control(
      'button_background_hover_color',
      [
        'label' => __( 'Background Color', 'tenweb-builder' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} #respond .form-submit input#submit:hover, {{WRAPPER}} #respond .form-submit input#submit:focus' => 'background-color: {{VALUE}};',
        ],
      ]
    );

    $this->add_control(
      'button_hover_border_color',
      [
        'label' => __( 'Border Color', 'tenweb-builder' ),
        'type' => Controls_Manager::COLOR,
        'condition' => [
          'border_border!' => '',
        ],
        'selectors' => [
          '{{WRAPPER}} #respond .form-submit input#submit:hover, {{WRAPPER}} #respond .form-submit input#submit:focus' => 'border-color: {{VALUE}};',
        ],
      ]
    );

    $this->add_control(
      'hover_animation',
      [
        'label' => __( 'Hover Animation', 'tenweb-builder' ),
        'type' => Controls_Manager::HOVER_ANIMATION,
      ]
    );

    $this->end_controls_tab();

    $this->end_controls_tabs();

    $this->add_group_control(
      Group_Control_Border::get_type(),
      [
        'name' => 'button_border',
        'selector' => '{{WRAPPER}} #respond .form-submit input#submit',
        'separator' => 'before',
      ]
    );

    $this->add_control(
      'button_border_radius',
      [
        'label' => __( 'Border Radius', 'tenweb-builder' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%' ],
        'selectors' => [
          '{{WRAPPER}} #respond .form-submit input#submit' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
      ]
    );

    $this->add_responsive_control(
      'button_padding',
      [
        'label' => __( 'Padding', 'tenweb-builder' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', 'em', '%' ],
        'selectors' => [
          '{{WRAPPER}} #respond .form-submit input#submit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
        'separator' => 'before',
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section(
      'inputs_style',
      [
        'label' => __( 'Inputs', 'tenweb-builder' ),
        'tab' => Controls_Manager::TAB_STYLE,
      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'input_typography',
          'global' => [
              'default' => Global_Typography::TYPOGRAPHY_ACCENT,
          ],
        'selector' => '{{WRAPPER}}  #respond input[type="text"], {{WRAPPER}}  #respond input[type="email"], {{WRAPPER}} #respond textarea',
      ]
    );
    $this->add_control(
      'input_text_color',
      [
        'label' => __( 'Text Color', 'tenweb-builder' ),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}}  #respond input[type="text"], {{WRAPPER}}  #respond input[type="email"], {{WRAPPER}} #respond textarea' => 'color: {{VALUE}};',
        ],
      ]
    );

    $this->add_control(
      'input_background_color',
      [
        'label' => __( 'Background Color', 'tenweb-builder' ),
        'type' => Controls_Manager::COLOR,
          'global' => [
              'default' => Global_Colors::COLOR_ACCENT,
          ],
        'selectors' => [
          '{{WRAPPER}}  #respond input[type="text"], {{WRAPPER}}  #respond input[type="email"], {{WRAPPER}} #respond textarea' => 'background-color: {{VALUE}};',
        ],
      ]
    );
    $this->add_group_control(
      Group_Control_Border::get_type(),
      [
        'name' => 'input_border',
        'selector' => '{{WRAPPER}}  #respond input[type="text"], {{WRAPPER}}  #respond input[type="email"], {{WRAPPER}} #respond textarea',
        'separator' => 'before',
      ]
    );

    $this->add_control(
      'input_border_radius',
      [
        'label' => __( 'Border Radius', 'tenweb-builder' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%' ],
        'selectors' => [
          '{{WRAPPER}}  #respond input[type="text"], {{WRAPPER}}  #respond input[type="email"], {{WRAPPER}} #respond textarea' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
      ]
    );

    $this->add_responsive_control(
      'input_padding',
      [
        'label' => __( 'Padding', 'tenweb-builder' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', 'em', '%' ],
        'selectors' => [
          '{{WRAPPER}}  #respond input[type="text"], {{WRAPPER}}  #respond input[type="email"], {{WRAPPER}} #respond textarea' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
        'separator' => 'before',
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section(
      'links_style',
      [
        'label' => __( 'Links', 'tenweb-builder' ),
        'tab' => Controls_Manager::TAB_STYLE,
      ]
    );

    $this->add_control(
      'link_text_color',
      [
        'label' => __( 'Text Color', 'tenweb-builder' ),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}}  #comments a' => 'color: {{VALUE}};',
        ],
      ]
    );

    $this->end_controls_section();
  }

  public function render() {
    $settings = $this->get_settings();
    if ( ! empty( $settings['style'] ) ) {
      $this->add_render_attribute( 'wrapper', 'class', $settings['style'] );
    }

    $editor = \Elementor\Plugin::instance()->editor;
    // Set edit mode as false, so don't render settings and etc.
    $is_edit_mode = $editor->is_edit_mode();

    $preview = \Elementor\Plugin::instance()->preview;
    $is_preview_mode = $preview->is_preview_mode();

    if(!comments_open() && ($is_preview_mode || $is_edit_mode)) :
      ?>
        <div role="alert">
            <h4>
              <?php esc_html_e('Comments Section', 'tenweb-builder'); ?>
            </h4>
            <p>
              <?php esc_html_e('Post comments will be displayed here if preview post.', 'tenweb-builder'); ?>
            </p>
        </div>
      <?php
    else :
      ?>
        <div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
          <?php
          comments_template();
          ?>
        </div>
      <?php
    endif;
  }
}

\Elementor\Plugin::instance()->widgets_manager->register(new Post_Comments());

