<?php
namespace Tenweb_Builder;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Utils;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

class Author_Box extends Widget_Base {

  public function get_name() {
    return Builder::$prefix . 'author-box';
  }

  public function get_title() {
    return __( 'Author Box', 'tenweb-builder' );
  }

  public function get_icon() {
    return 'twbb-author-box twbb-widget-icon';
  }

  public function get_categories() {
    return [ 'tenweb-builder-widgets' ];
  }

  protected function register_controls() {
    $this->start_controls_section(
      'section_author_info',
      [
        'label' => __( 'Author Info', 'tenweb-builder' ),
      ]
    );

    $this->add_control(
      'source',
      [
        'label' => __( 'Source', 'tenweb-builder' ),
        'type' => Controls_Manager::SELECT,
        'default' => 'current',
        'options' => [
          'current' => __( 'Current Author', 'tenweb-builder' ),
          'custom' => __( 'Custom', 'tenweb-builder' ),
        ],
      ]
    );

    $this->add_control(
      'show_avatar',
      [
        'label' => __( 'Profile Picture', 'tenweb-builder' ),
        'type' => Controls_Manager::SWITCHER,
        'prefix_class' => 'elementor-author-box--avatar-',
        'label_on' => __( 'Show', 'tenweb-builder' ),
        'label_off' => __( 'Hide', 'tenweb-builder' ),
        'default' => 'yes',
        'separator' => 'before',
        'condition' => [
          'source!' => 'custom',
        ],
        'render_type' => 'template',
      ]
    );

    //This controls for custom source
    $this->add_control(
      'author_avatar',
      [
        'label' => __( 'Profile Picture', 'tenweb-builder' ),
        'type' => Controls_Manager::MEDIA,
        'default' => [
          'url' => Utils::get_placeholder_image_src(),
        ],
        'condition' => [
          'source' => 'custom',
        ],
        'separator' => 'before',
      ]
    );
    //END

    $this->add_control(
      'show_name',
      [
        'label' => __( 'Display Name', 'tenweb-builder' ),
        'type' => Controls_Manager::SWITCHER,
        'prefix_class' => 'elementor-author-box--name-',
        'label_on' => __( 'Show', 'tenweb-builder' ),
        'label_off' => __( 'Hide', 'tenweb-builder' ),
        'default' => 'yes',
        'condition' => [
          'source!' => 'custom',
        ],
        'render_type' => 'template',
        'separator' => 'before',
      ]
    );

    //This control for custom source
    $this->add_control(
      'author_name',
      [
        'label' => __( 'Name', 'tenweb-builder' ),
        'type' => Controls_Manager::TEXT,
        'default' => __( 'John Doe', 'tenweb-builder' ),
        'condition' => [
          'source' => 'custom',
        ],
        'separator' => 'before',
      ]
    );
    //END

    $this->add_control(
      'author_name_tag',
      [
        'label' => __( 'HTML Tag', 'tenweb-builder' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
          'h1' => 'H1',
          'h2' => 'H2',
          'h3' => 'H3',
          'h4' => 'H4',
          'h5' => 'H5',
          'h6' => 'H6',
          'div' => 'div',
          'span' => 'span',
        ],
        'default' => 'h4',
      ]
    );

    $this->add_control(
      'link_to',
      [
        'label' => __( 'Link To', 'tenweb-builder' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
          '' => __( 'None', 'tenweb-builder' ),
          'website' => __( 'Website', 'tenweb-builder' ),
          'posts_archive' => __( 'Posts Archive', 'tenweb-builder' ),
        ],
        'condition' => [
          'source!' => 'custom',
        ],
        'description' => __( 'Link for the Author Name and Image', 'tenweb-builder' ),
      ]
    );

    $this->add_control(
      'show_biography',
      [
        'label' => __( 'Biography', 'tenweb-builder' ),
        'type' => Controls_Manager::SWITCHER,
        'prefix_class' => 'elementor-author-box--biography-',
        'label_on' => __( 'Show', 'tenweb-builder' ),
        'label_off' => __( 'Hide', 'tenweb-builder' ),
        'default' => 'yes',
        'condition' => [
          'source!' => 'custom',
        ],
        'render_type' => 'template',
        'separator' => 'before',
      ]
    );

    $this->add_control(
      'show_link',
      [
        'label' => __( 'Archive Button', 'tenweb-builder' ),
        'type' => Controls_Manager::SWITCHER,
        'prefix_class' => 'elementor-author-box--link-',
        'label_on' => __( 'Show', 'tenweb-builder' ),
        'label_off' => __( 'Hide', 'tenweb-builder' ),
        'default' => 'no',
        'condition' => [
          'source!' => 'custom',
        ],
        'render_type' => 'template',
      ]
    );

    $this->add_control(
      'author_website',
      [
        'label' => __( 'Link To', 'tenweb-builder' ),
        'type' => Controls_Manager::URL,
        'placeholder' => __( 'https://your-link.com', 'tenweb-builder' ),
        'condition' => [
          'source' => 'custom',
        ],
        'description' => __( 'Link for the Author Name and Image', 'tenweb-builder' ),
      ]
    );

    $this->add_control(
      'author_bio',
      [
        'label' => __( 'Biography', 'tenweb-builder' ),
        'type' => Controls_Manager::TEXTAREA,
        'default' => __( 'Click edit button to change this text. Lorem ipsum dolor sit amet consectetur adipiscing elit dolor', 'tenweb-builder' ),
        'rows' => 4,
        'condition' => [
          'source' => 'custom',
        ],
        'separator' => 'before',
      ]
    );

    $this->add_control(
      'posts_url',
      [
        'label' => __( 'Archive Button', 'tenweb-builder' ),
        'type' => Controls_Manager::URL,
        'placeholder' => __( 'https://your-link.com', 'tenweb-builder' ),
        'condition' => [
          'source' => 'custom',
        ],
      ]
    );

    $this->add_control(
      'link_text',
      [
        'label' => __( 'Archive Text', 'tenweb-builder' ),
        'type' => Controls_Manager::TEXT,
        'default' => __( 'All Posts', 'tenweb-builder' ),
      ]
    );

    $this->add_control(
      'layout',
      [
        'label' => __( 'Layout', 'tenweb-builder' ),
        'type' => Controls_Manager::CHOOSE,
        'label_block' => false,
        'options' => [
          'left' => [
            'title' => __( 'Left', 'tenweb-builder' ),
            'icon'  => 'eicon-h-align-left',
          ],
          'above' => [
            'title' => __( 'Above', 'tenweb-builder' ),
            'icon'  => 'eicon-v-align-top',
          ],
          'right' => [
            'title' => __( 'Right', 'tenweb-builder' ),
            'icon'  => 'eicon-h-align-right',
          ],
        ],
        'separator' => 'before',
        'prefix_class' => 'elementor-author-box--layout-image-',
      ]
    );

    $this->add_control(
      'alignment',
      [
        'label' => __( 'Alignment', 'tenweb-builder' ),
        'type' => Controls_Manager::CHOOSE,
        'label_block' => false,
        'options' => [
          'left' => [
            'title' => __( 'Left', 'tenweb-builder' ),
            'icon'  => 'fa fa-align-left',
          ],
          'center' => [
            'title' => __( 'Center', 'tenweb-builder' ),
            'icon'  => 'fa fa-align-center',
          ],
          'right' => [
            'title' => __( 'Right', 'tenweb-builder' ),
            'icon'  => 'fa fa-align-right',
          ],
        ],
        'prefix_class' => 'elementor-author-box--align-',
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section(
      'section_image_style',
      [
        'label' => __( 'Image', 'tenweb-builder' ),
        'tab' => Controls_Manager::TAB_STYLE,
      ]
    );

    $this->add_control(
      'image_vertical_align',
      [
        'label' => __( 'Vertical Align', 'tenweb-builder' ),
        'type' => Controls_Manager::CHOOSE,
        'label_block' => false,
        'options' => [
          'top' => [
            'title' => __( 'Top', 'tenweb-builder' ),
            'icon'  => 'eicon-v-align-top',
          ],
          'middle' => [
            'title' => __( 'Middle', 'tenweb-builder' ),
            'icon'  => 'eicon-v-align-middle',
          ],
        ],
        'prefix_class' => 'elementor-author-box--image-valign-',
        'condition' => [
          'layout!' => 'above',
        ],
      ]
    );

    $this->add_responsive_control(
      'image_size',
      [
        'label' => __( 'Image Size', 'tenweb-builder' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 200,
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .elementor-author-box__avatar img' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
        ],
      ]
    );

    $this->add_responsive_control(
      'image_gap',
      [
        'label' => __( 'Gap', 'tenweb-builder' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 100,
          ],
        ],
        'selectors' => [
          'body.rtl {{WRAPPER}}.elementor-author-box--layout-image-left .elementor-author-box__avatar, 
					 body:not(.rtl) {{WRAPPER}}:not(.elementor-author-box--layout-image-above) .elementor-author-box__avatar' => 'margin-right: {{SIZE}}{{UNIT}}; margin-left: 0;',

          'body:not(.rtl) {{WRAPPER}}.elementor-author-box--layout-image-right .elementor-author-box__avatar, 
					 body.rtl {{WRAPPER}}:not(.elementor-author-box--layout-image-above) .elementor-author-box__avatar' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right:0;',

          '{{WRAPPER}}.elementor-author-box--layout-image-above .elementor-author-box__avatar' => 'margin-bottom: {{SIZE}}{{UNIT}}',
        ],
      ]
    );

    $this->add_control(
      'image_border',
      [
        'label' => __( 'Border', 'tenweb-builder' ),
        'type' => Controls_Manager::SWITCHER,
        'selectors' => [
          '{{WRAPPER}} .elementor-author-box__avatar img' => 'border-style: solid',
        ],
      ]
    );

    $this->add_control(
      'image_border_color',
      [
        'label' => __( 'Border Color', 'tenweb-builder' ),
        'type' => Controls_Manager::COLOR,
        'default' => '#000',
        'selectors' => [
          '{{WRAPPER}} .elementor-author-box__avatar img' => 'border-color: {{VALUE}}',
        ],
        'condition' => [
          'image_border' => 'yes',
        ],
      ]
    );

    $this->add_responsive_control(
      'image_border_width',
      [
        'label' => __( 'Border Width', 'tenweb-builder' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 20,
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .elementor-author-box__avatar img' => 'border-width: {{SIZE}}{{UNIT}}',
        ],
        'condition' => [
          'image_border' => 'yes',
        ],
      ]
    );

    $this->add_control(
      'image_border_radius',
      [
        'label' => __( 'Border Radius', 'tenweb-builder' ),
        'type' => Controls_Manager::SLIDER,
        'selectors' => [
          '{{WRAPPER}} .elementor-author-box__avatar img' => 'border-radius: {{SIZE}}{{UNIT}}',
        ],
      ]
    );

    $this->add_group_control(
      Group_Control_Box_Shadow::get_type(),
      [
        'name' => 'input_box_shadow',
        'selector' => '{{WRAPPER}} .elementor-author-box__avatar img',
        'fields_options' => [
          'box_shadow_type' => [
            'separator' => 'default',
          ],
        ],
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section(
      'section_text_style',
      [
        'label' => __( 'Text', 'tenweb-builder' ),
        'tab' => Controls_Manager::TAB_STYLE,
      ]
    );

    $this->add_control(
      'heading_name_style',
      [
        'label' => __( 'Name', 'tenweb-builder' ),
        'type' => Controls_Manager::HEADING,
        'separator' => 'before',
      ]
    );

    $this->add_control(
      'name_color',
      [
        'label' => __( 'Color', 'tenweb-builder' ),
        'type' => Controls_Manager::COLOR,
          'global' => [
              'default' => Global_Colors::COLOR_SECONDARY,
          ],
        'selectors' => [
          '{{WRAPPER}} .elementor-author-box__name' => 'color: {{VALUE}}',
        ],
      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'name_typography',
        'selector' => '{{WRAPPER}} .elementor-author-box__name',
          'global' => [
              'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
          ],
      ]
    );

    $this->add_responsive_control(
      'name_gap',
      [
        'label' => __( 'Gap', 'tenweb-builder' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 100,
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .elementor-author-box__name' => 'margin-bottom: {{SIZE}}{{UNIT}}',
        ],
      ]
    );

    $this->add_control(
      'heading_bio_style',
      [
        'label' => __( 'Biography', 'tenweb-builder' ),
        'type' => Controls_Manager::HEADING,
        'separator' => 'before',
      ]
    );

    $this->add_control(
      'bio_color',
      [
        'label' => __( 'Color', 'tenweb-builder' ),
        'type' => Controls_Manager::COLOR,
          'global' => [
              'default' => Global_Colors::COLOR_TEXT,
          ],
        'selectors' => [
          '{{WRAPPER}} .elementor-author-box__bio' => 'color: {{VALUE}}',
        ],
      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'bio_typography',
        'selector' => '{{WRAPPER}} .elementor-author-box__bio',
          'global' => [
              'default' => Global_Typography::TYPOGRAPHY_TEXT,
          ],
      ]
    );

    $this->add_responsive_control(
      'bio_gap',
      [
        'label' => __( 'Gap', 'tenweb-builder' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 100,
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .elementor-author-box__bio' => 'margin-bottom: {{SIZE}}{{UNIT}}',
        ],
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section(
      'section_style_button',
      [
        'label' => 'Button',
        'tab' => Controls_Manager::TAB_STYLE,
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
          'global' => [
              'default' => Global_Colors::COLOR_SECONDARY,
          ],
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .elementor-author-box__button' => 'color: {{VALUE}}; border-color: {{VALUE}}',
        ],
      ]
    );

    $this->add_control(
      'button_background_color',
      [
        'label' => __( 'Background Color', 'tenweb-builder' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .elementor-author-box__button' => 'background-color: {{VALUE}}',
        ],
      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'button_typography',
          'global' => [
              'default' => Global_Typography::TYPOGRAPHY_ACCENT,
          ],
        'selector' => '{{WRAPPER}} .elementor-author-box__button',
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
        'label' => __( 'Color', 'tenweb-builder' ),
        'type' => Controls_Manager::COLOR,
          'global' => [
              'default' => Global_Colors::COLOR_SECONDARY,
          ],
        'selectors' => [
          '{{WRAPPER}} .elementor-author-box__button:hover' => 'border-color: {{VALUE}}; color: {{VALUE}};',
        ],
      ]
    );

    $this->add_control(
      'button_background_hover_color',
      [
        'label' => __( 'Background Color', 'tenweb-builder' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .elementor-author-box__button:hover' => 'background-color: {{VALUE}};',
        ],
      ]
    );

    $this->add_control(
      'button_hover_animation',
      [
        'label' => __( 'Animation', 'tenweb-builder' ),
        'type' => Controls_Manager::HOVER_ANIMATION,
      ]
    );

    $this->end_controls_tab();

    $this->end_controls_tabs();

    $this->add_control(
      'button_border_width',
      [
        'label' => __( 'Border Width', 'tenweb-builder' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 20,
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .elementor-author-box__button' => 'border-width: {{SIZE}}{{UNIT}};',
        ],
        'separator' => 'before',
        'condition' => [
          'link_text!' => '',
        ],
      ]
    );

    $this->add_control(
      'button_border_radius',
      [
        'label' => __( 'Border Radius', 'tenweb-builder' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 100,
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .elementor-author-box__button' => 'border-radius: {{SIZE}}{{UNIT}};',
        ],
        'separator' => 'after',
        'condition' => [
          'link_text!' => '',
        ],
      ]
    );

    $this->add_control(
      'button_text_padding',
      [
        'label' => __( 'Padding', 'tenweb-builder' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', 'em' ],
        'selectors' => [
          '{{WRAPPER}} .elementor-author-box__button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
        'separator' => 'before',
      ]
    );

    $this->end_controls_section();
  }

  protected function render() {
    $settings = $this->get_active_settings();
    $author = [];
    $link_tag = 'div';
    $link_url = '#';
    $author_name_tag = $settings['author_name_tag'];

    $custom_src = ( 'custom' === $settings['source'] );

    if ( 'current' === $settings['source'] ) {
	 		/* Post,Page Author:User ID */
	 		$current_post_author_id = get_the_author_meta( 'ID' );
            $avatar_args = [];
      $avatar_args['size'] = 300;

			$author['avatar'] = get_avatar_url( $current_post_author_id, $avatar_args );
			$author['display_name'] = get_the_author_meta( 'display_name', $current_post_author_id );
			$author['website'] = get_the_author_meta( 'user_url', $current_post_author_id );
			$author['bio'] = get_the_author_meta( 'description', $current_post_author_id );
			$author['posts_url'] = get_author_posts_url( $current_post_author_id );

    } elseif ( $custom_src ) {

      if ( ! empty( $settings['author_avatar']['url'] ) ) {
        $avatar_src = $settings['author_avatar']['url'];

        if ( $settings['author_avatar']['id'] ) {
          $attachment_image_src = wp_get_attachment_image_src( $settings['author_avatar']['id'], 'medium' );

          if ( ! empty( $attachment_image_src[0] ) ) {
            $avatar_src = $attachment_image_src[0];
          }
        }

        $author['avatar'] = $avatar_src;
      }

      $author['display_name'] = $settings['author_name'];
      $author['website'] = $settings['author_website']['url'];
      $author['bio'] = wpautop( $settings['author_bio'] );
      $author['posts_url'] = $settings['posts_url']['url'];
    }

    $print_avatar = ( ( ! $custom_src && 'yes' === $settings['show_avatar'] ) || ( $custom_src && ! empty( $author['avatar'] ) ) );
    $print_name = ( ( ! $custom_src && 'yes' === $settings['show_name'] ) || ( $custom_src && ! empty( $author['display_name'] ) ) );
    $print_bio = ( ( ! $custom_src && 'yes' === $settings['show_biography'] ) || ( $custom_src && ! empty( $author['bio'] ) ) );
    $print_link = ( ( ! $custom_src && 'yes' === $settings['show_link'] ) && ! empty( $settings['link_text'] ) || ( $custom_src && ! empty( $author['posts_url'] ) && ! empty( $settings['link_text'] ) ) );

    if ( ! empty( $settings['link_to'] ) || $custom_src ) {
      if ( ( $custom_src || 'website' === $settings['link_to'] ) && ! empty( $author['website'] ) ) {
        $link_tag = 'a';
        $link_url = $author['website'];
      } elseif ( 'posts_archive' === $settings['link_to'] && ! empty( $author['posts_url'] ) ) {
        $link_tag = 'a';
        $link_url = $author['posts_url'];
      }

      $this->add_render_attribute(
        'author_link',
        'href', $link_url
      );

      // Link to avatar
      if( $settings['author_website']['is_external'] === "on" ) {
        $this->add_render_attribute('avatar_target', 'target', '_blank');
      }
      if( $settings['author_website']['nofollow'] === "on" ) {
        $this->add_render_attribute('avatar_nofollow', 'rel', 'nofollow');
      }

      // Archive button
      if( $settings["posts_url"]['is_external'] === "on" ) {
        $this->add_render_attribute('button_target', 'target', '_blank');
      }
      if( $settings["posts_url"]['nofollow'] === "on" ) {
        $this->add_render_attribute('button_nofollow', 'rel', 'nofollow');
      }

    }

    $this->add_render_attribute(
      'button',
      'class', [
        'elementor-author-box__button',
        'elementor-button',
        'elementor-size-xs',
      ]
    );

    if ( $print_link ) {
      $this->add_render_attribute( 'button', 'href', $author['posts_url'] );
    }

    if ( $print_link && ! empty( $settings['button_hover_animation'] ) ) {
      $this->add_render_attribute(
        'button',
        'class',
        'elementor-animation-' . $settings['button_hover_animation']
      );
    }

    if ( $print_avatar ) {
      $this->add_render_attribute( 'avatar', 'src', $author['avatar'] );

      if ( ! empty( $author['display_name'] ) ) {
        $this->add_render_attribute( 'avatar', 'alt', $author['display_name'] );
      }
    }

    ?>
    <div class="elementor-author-box">
    <?php if ( $print_avatar ) { ?>
      <<?php Utils::print_validated_html_tag( $link_tag ); ?>
          <?php $this->print_render_attribute_string( 'author_link' ); ?> class="elementor-author-box__avatar"
          <?php $this->print_render_attribute_string( 'avatar_target' ); ?>
          <?php $this->print_render_attribute_string( 'avatar_nofollow' ); ?>>
      <img <?php $this->print_render_attribute_string( 'avatar' ); ?>>
      </<?php Utils::print_validated_html_tag( $link_tag ); ?>>
    <?php } ?>

    <div class="elementor-author-box__text">
    <?php if ( $print_name ) : ?>
      <<?php Utils::print_validated_html_tag($link_tag); ?>
          <?php $this->print_render_attribute_string('author_link' ); ?>
          <?php $this->print_render_attribute_string( 'avatar_target' ); ?>
          <?php $this->print_render_attribute_string( 'avatar_nofollow' ); ?>>
          <<?php Utils::print_validated_html_tag( $author_name_tag ); ?> class="elementor-author-box__name">
          <?php Utils::print_unescaped_internal_string( $author['display_name'] ); ?>
          </<?php Utils::print_validated_html_tag( $author_name_tag ); ?>>
          </<?php Utils::print_validated_html_tag( $link_tag ); ?>>
    <?php endif; ?>

    <?php if ( $print_bio ) : ?>
      <div class="elementor-author-box__bio">
          <?php Utils::print_unescaped_internal_string( $author['bio'] ); ?>
      </div>
    <?php endif; ?>

    <?php if ( $print_link ) : ?>
      <a <?php $this->print_render_attribute_string( 'button' ); ?> <?php $this->print_render_attribute_string( 'button_target' ); ?> <?php $this->print_render_attribute_string( 'button_nofollow' ); ?>>
        <?php $this->print_unescaped_setting('link_text'); ?>
      </a>
    <?php endif; ?>
    </div>
    </div>
    <?php
  }
}

\Elementor\Plugin::instance()->widgets_manager->register(new Author_Box());
