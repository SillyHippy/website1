<?php
namespace Tenweb_Builder;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;


if(!defined('ABSPATH')) exit; // Exit if accessed directly

class Post_Info extends Widget_Base {

  public function get_name() {
    return Builder::$prefix . 'post-info';
  }

  public function get_title() {
    return __( 'Post Info', 'tenweb-builder' );
  }

  public function get_icon() {
    return 'twbb-post-info twbb-widget-icon';
  }

  public function get_categories() {
    return [ 'tenweb-builder-widgets' ];
  }

  public function get_keywords() {
    return [ 'post', 'info', 'date', 'time', 'author', 'taxonomy', 'comments', 'terms', 'avatar' ];
  }

  protected function register_controls() {
    $this->start_controls_section(
      'section_icon',
      [
        'label' => __( 'Meta Data', 'tenweb-builder' ),
      ]
    );

    $this->add_responsive_control(
      'view',
      [
          'label' => __( 'Layout', 'tenweb-builder' ),
          'type' => Controls_Manager::CHOOSE,
          'default' => 'inline',
          'options' => [
              'traditional' => [
                  'title' => __( 'Default', 'tenweb-builder' ),
                  'icon' => 'eicon-editor-list-ul',
              ],
              'inline' => [
                  'title' => __( 'Inline', 'tenweb-builder' ),
                  'icon' => 'eicon-ellipsis-h',
              ],
          ],
          'render_type' => 'template',
          'classes' => 'elementor-control-start-end',
      ]
    );

    $repeater = new Repeater();

    $repeater->add_control(
      'type',
      [
        'label' => __( 'Type', 'tenweb-builder' ),
        'type' => Controls_Manager::SELECT,
        'default' => 'date',
        'options' => [
          'author' => __( 'Author', 'tenweb-builder' ),
          'date' => __( 'Date', 'tenweb-builder' ),
          'time' => __( 'Time', 'tenweb-builder' ),
          'comments' => __( 'Comments', 'tenweb-builder' ),
          'terms' => __( 'Terms', 'tenweb-builder' ),
          'custom' => __( 'Custom', 'tenweb-builder' ),
        ],
      ]
    );

    $repeater->add_control(
      'date_format',
      [
        'label' => __( 'Date Format', 'tenweb-builder' ),
        'type' => Controls_Manager::SELECT,
        'default' => 'default',
        'options' => [
          'default' => 'Default',
          '0' => _x( 'March 6, 2018 (F j, Y)', 'Date Format', 'tenweb-builder' ),
          '1' => '2018-03-06 (Y-m-d)',
          '2' => '03/06/2018 (m/d/Y)',
          '3' => '06/03/2018 (d/m/Y)',
          'custom' => __( 'Custom', 'tenweb-builder' ),
        ],
        'condition' => [
          'type' => 'date',
        ],
      ]
    );

    $repeater->add_control(
      'custom_date_format',
      [
        'label' => __( 'Custom Date Format', 'tenweb-builder' ),
        'type' => Controls_Manager::TEXT,
        'default' => 'F j, Y',
        'condition' => [
          'type' => 'date',
          'date_format' => 'custom',
        ],
        'description' => sprintf(
        /* translators: %s: Allowed data letters (see: http://php.net/manual/en/function.date.php). */
          __( 'Use the letters: %s', 'tenweb-builder' ),
          'l D d j S F m M n Y y'
        ),
      ]
    );

    $repeater->add_control(
      'time_format',
      [
        'label' => __( 'Time Format', 'tenweb-builder' ),
        'type' => Controls_Manager::SELECT,
        'default' => 'default',
        'options' => [
          'default' => 'Default',
          '0' => '3:31 pm (g:i a)',
          '1' => '3:31 PM (g:i A)',
          '2' => '15:31 (H:i)',
          'custom' => __( 'Custom', 'tenweb-builder' ),
        ],
        'condition' => [
          'type' => 'time',
        ],
      ]
    );
    $repeater->add_control(
      'custom_time_format',
      [
        'label' => __( 'Custom Time Format', 'tenweb-builder' ),
        'type' => Controls_Manager::TEXT,
        'default' => 'g:i a',
        'placeholder' => 'g:i a',
        'condition' => [
          'type' => 'time',
          'time_format' => 'custom',
        ],
        'description' => sprintf(
        /* translators: %s: Allowed time letters (see: http://php.net/manual/en/function.time.php). */
          __( 'Use the letters: %s', 'tenweb-builder' ),
          'g G H i a A'
        ),
      ]
    );

    $repeater->add_control(
      'taxonomy',
      [
        'label' => __( 'Taxonomy', 'tenweb-builder' ),
        'type' => Controls_Manager::SELECT2,
        'label_block' => true,
        'default' => ['category'],
        'options' => $this->get_taxonomies(),
        'condition' => [
          'type' => 'terms',
        ],
      ]
    );

    $repeater->add_control(
      'text_prefix',
      [
        'label' => __( 'Before', 'tenweb-builder' ),
        'type' => Controls_Manager::TEXT,
        'condition' => [
          'type!' => 'custom',
        ],
      ]
    );

    $repeater->add_control(
      'show_avatar',
      [
        'label' => __( 'Avatar', 'tenweb-builder' ),
        'type' => Controls_Manager::SWITCHER,
        'condition' => [
          'type' => 'author',
        ],
      ]
    );

    $repeater->add_responsive_control(
      'avatar_size',
      [
        'label' => __( 'Size', 'tenweb-builder' ),
        'type' => Controls_Manager::SLIDER,
        'selectors' => [
          '{{WRAPPER}} {{CURRENT_ITEM}} .elementor-icon-list-icon' => 'width: {{SIZE}}{{UNIT}}',
        ],
        'condition' => [
          'show_avatar' => 'yes',
        ],
      ]
    );

    $repeater->add_control(
      'comments_custom_strings',
      [
        'label' => __( 'Custom Format', 'tenweb-builder' ),
        'type' => Controls_Manager::SWITCHER,
        'default' => false,
        'condition' => [
          'type' => 'comments',
        ],
      ]
    );

    $repeater->add_control(
      'string_no_comments',
      [
        'label' => __( 'No Comments', 'tenweb-builder' ),
        'type' => Controls_Manager::TEXT,
        'placeholder' => __( 'No Comments', 'tenweb-builder' ),
        'condition' => [
          'comments_custom_strings' => 'yes',
          'type' => 'comments',
        ],
      ]
    );

    $repeater->add_control(
      'string_one_comment',
      [
        'label' => __( 'One Comment', 'tenweb-builder' ),
        'type' => Controls_Manager::TEXT,
        'placeholder' => __( 'One Comment', 'tenweb-builder' ),
        'condition' => [
          'comments_custom_strings' => 'yes',
          'type' => 'comments',
        ],
      ]
    );

    $repeater->add_control(
      'string_comments',
      [
        'label' => __( 'Comments', 'tenweb-builder' ),
        'type' => Controls_Manager::TEXT,
        'placeholder' => __( '%s Comments', 'tenweb-builder' ),
        'condition' => [
          'comments_custom_strings' => 'yes',
          'type' => 'comments',
        ],
      ]
    );

    $repeater->add_control(
      'custom_text',
      [
        'label' => __( 'Custom', 'tenweb-builder' ),
        'type' => Controls_Manager::TEXT,
        'dynamic' => [
          'active' => true,
        ],
        'label_block' => true,
        'condition' => [
          'type' => 'custom',
        ],
      ]
    );

    $repeater->add_control(
      'link',
      [
        'label' => __( 'Link', 'tenweb-builder' ),
        'type' => Controls_Manager::SWITCHER,
        'default' => 'yes',
        'condition' => [
          'type!' => 'time',
        ],
      ]
    );

    $repeater->add_control(
      'custom_url',
      [
        'label' => __( 'Custom URL', 'tenweb-builder' ),
        'type' => Controls_Manager::URL,
        'dynamic' => [
          'active' => true,
        ],
        'condition' => [
          'type' => 'custom',
        ],
      ]
    );

    $repeater->add_control(
      'show_icon',
      [
        'label' => __( 'Icon', 'tenweb-builder' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
          'none' => __( 'None', 'tenweb-builder' ),
          'default' => __( 'Default', 'tenweb-builder' ),
          'custom' => __( 'Custom', 'tenweb-builder' ),
        ],
        'default' => 'default',
        'condition' => [
          'show_avatar!' => 'yes',
        ],
      ]
    );

    $repeater->add_control(
      'selected_icon',
      [
        'label' => __( 'Choose Icon', 'tenweb-builder' ),
        'type' => Controls_Manager::ICONS,
        'fa4compatibility' => 'icon',
        'condition' => [
          'show_icon' => 'custom',
          'show_avatar!' => 'yes',
        ],
      ]
    );

    $this->add_control(
      'icon_list',
      [
        'label' => '',
        'type' => Controls_Manager::REPEATER,
        'fields' => $repeater->get_controls(),
        'default' => [
          [
            'type' => 'author',
            'selected_icon' => [
              'value' => 'far fa-user-circle',
              'library' => 'fa-regular',
            ],
          ],
          [
            'type' => 'date',
            'selected_icon' => [
              'value' => 'fas fa-calendar',
              'library' => 'fa-solid',
            ],
          ],
          [
            'type' => 'time',
            'selected_icon' => [
              'value' => 'far fa-clock',
              'library' => 'fa-regular',
            ],
          ],
          [
            'type' => 'comments',
            'selected_icon' => [
              'value' => 'far fa-comment-dots',
              'library' => 'fa-regular',
            ],
          ],
        ],
        'title_field' => '{{{ elementor.helpers.renderIcon( this, selected_icon, {}, "i", "panel" ) || \'<i class="{{ icon }}" aria-hidden="true"></i>\' }}} <span style="text-transform: capitalize;">{{{ type }}}</span>', //phpcs:ignore WordPressVIPMinimum.Security.Mustache.OutputNotation
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section(
      'section_icon_list',
      [
        'label' => __( 'List', 'tenweb-builder' ),
        'tab' => Controls_Manager::TAB_STYLE,
      ]
    );

    $this->add_responsive_control(
      'space_between',
      [
        'label' => __( 'Space Between', 'tenweb-builder' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'max' => 50,
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .elementor-icon-list-items:not(.elementor-inline-items) .elementor-icon-list-item:not(:last-child)' => 'padding-bottom: calc({{SIZE}}{{UNIT}}/2)',
          '{{WRAPPER}} .elementor-icon-list-items:not(.elementor-inline-items) .elementor-icon-list-item:not(:first-child)' => 'margin-top: calc({{SIZE}}{{UNIT}}/2)',
          '{{WRAPPER}} .elementor-icon-list-items.elementor-inline-items .elementor-icon-list-item' => 'margin-right: calc({{SIZE}}{{UNIT}}/2); margin-left: calc({{SIZE}}{{UNIT}}/2)',
          '{{WRAPPER}} .elementor-icon-list-items.elementor-inline-items' => 'margin-right: calc(-{{SIZE}}{{UNIT}}/2); margin-left: calc(-{{SIZE}}{{UNIT}}/2)',
          'body.rtl {{WRAPPER}} .elementor-icon-list-items.elementor-inline-items .elementor-icon-list-item:after' => 'left: calc(-{{SIZE}}{{UNIT}}/2)',
          'body:not(.rtl) {{WRAPPER}} .elementor-icon-list-items.elementor-inline-items .elementor-icon-list-item:after' => 'right: calc(-{{SIZE}}{{UNIT}}/2)',
        ],
      ]
    );

    $this->add_responsive_control(
      'icon_align',
      [
        'label' => __( 'Alignment', 'tenweb-builder' ),
        'type' => Controls_Manager::CHOOSE,
        'options' => [
          'left' => [
            'title' => __( 'Start', 'tenweb-builder' ),
            'icon' => 'eicon-h-align-left',
          ],
          'center' => [
            'title' => __( 'Center', 'tenweb-builder' ),
            'icon' => 'eicon-h-align-center',
          ],
          'right' => [
            'title' => __( 'End', 'tenweb-builder' ),
            'icon' => 'eicon-h-align-right',
          ],
        ],
        'prefix_class' => 'elementor%s-align-',
      ]
    );

    $this->add_control(
      'divider',
      [
        'label' => __( 'Divider', 'tenweb-builder' ),
        'type' => Controls_Manager::SWITCHER,
        'label_off' => __( 'Off', 'tenweb-builder' ),
        'label_on' => __( 'On', 'tenweb-builder' ),
        'selectors' => [
          '{{WRAPPER}} .elementor-icon-list-item:not(:last-child):after' => 'content: ""',
        ],
        'separator' => 'before',
      ]
    );

    $this->add_control(
      'divider_style',
      [
        'label' => __( 'Style', 'tenweb-builder' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
          'solid' => __( 'Solid', 'tenweb-builder' ),
          'double' => __( 'Double', 'tenweb-builder' ),
          'dotted' => __( 'Dotted', 'tenweb-builder' ),
          'dashed' => __( 'Dashed', 'tenweb-builder' ),
        ],
        'default' => 'solid',
        'condition' => [
          'divider' => 'yes',
        ],
        'selectors' => [
          '{{WRAPPER}} .elementor-icon-list-items:not(.elementor-inline-items) .elementor-icon-list-item:not(:last-child):after' => 'border-top-style: {{VALUE}};',
          '{{WRAPPER}} .elementor-icon-list-items.elementor-inline-items .elementor-icon-list-item:not(:last-child):after' => 'border-left-style: {{VALUE}}',
        ],
      ]
    );

    $this->add_control(
      'divider_weight',
      [
        'label' => __( 'Weight', 'tenweb-builder' ),
        'type' => Controls_Manager::SLIDER,
        'default' => [
          'size' => 1,
        ],
        'range' => [
          'px' => [
            'min' => 1,
            'max' => 20,
          ],
        ],
        'condition' => [
          'divider' => 'yes',
        ],
        'selectors' => [
          '{{WRAPPER}} .elementor-icon-list-items:not(.elementor-inline-items) .elementor-icon-list-item:not(:last-child):after' => 'border-top-width: {{SIZE}}{{UNIT}}',
          '{{WRAPPER}} .elementor-inline-items .elementor-icon-list-item:not(:last-child):after' => 'border-left-width: {{SIZE}}{{UNIT}}',
        ],
      ]
    );

    $this->add_control(
      'divider_width',
      [
        'label' => __( 'Width', 'tenweb-builder' ),
        'type' => Controls_Manager::SLIDER,
        'size_units' => [ '%', 'px' ],
        'default' => [
          'unit' => '%',
        ],
        'range' => [
          'px' => [
            'min' => 1,
            'max' => 100,
          ],
          '%' => [
            'min' => 1,
            'max' => 100,
          ],
        ],
        'condition' => [
          'divider' => 'yes',
          'view!' => 'inline',
        ],
        'selectors' => [
          '{{WRAPPER}} .elementor-icon-list-item:not(:last-child):after' => 'width: {{SIZE}}{{UNIT}}',
        ],
      ]
    );

    $this->add_control(
      'divider_height',
      [
        'label' => __( 'Height', 'tenweb-builder' ),
        'type' => Controls_Manager::SLIDER,
        'size_units' => [ '%', 'px' ],
        'default' => [
          'unit' => '%',
        ],
        'range' => [
          'px' => [
            'min' => 1,
            'max' => 100,
          ],
          '%' => [
            'min' => 1,
            'max' => 100,
          ],
        ],
        'condition' => [
          'divider' => 'yes',
          'view' => 'inline',
        ],
        'selectors' => [
          '{{WRAPPER}} .elementor-icon-list-item:not(:last-child):after' => 'height: {{SIZE}}{{UNIT}}',
        ],
      ]
    );

    $this->add_control(
      'divider_color',
      [
        'label' => __( 'Color', 'tenweb-builder' ),
        'type' => Controls_Manager::COLOR,
        'default' => '#ddd',
        'global' => [
          'default' => Global_Colors::COLOR_TEXT,
        ],
        'condition' => [
          'divider' => 'yes',
        ],
        'selectors' => [
          '{{WRAPPER}} .elementor-icon-list-item:not(:last-child):after' => 'border-color: {{VALUE}};',
        ],
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section(
      'section_icon_style',
      [
        'label' => __( 'Icon', 'tenweb-builder' ),
        'tab' => Controls_Manager::TAB_STYLE,
      ]
    );

    $this->add_control(
      'icon_color',
      [
        'label' => __( 'Color', 'tenweb-builder' ),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .elementor-icon-list-icon i' => 'color: {{VALUE}};',
          '{{WRAPPER}} .elementor-icon-list-icon svg' => 'fill: {{VALUE}};',
        ],
        'global' => [
          'default' => Global_Colors::COLOR_PRIMARY,
        ],
      ]
    );

    $this->add_responsive_control(
      'icon_size',
      [
        'label' => __( 'Size', 'tenweb-builder' ),
        'type' => Controls_Manager::SLIDER,
        'default' => [
          'size' => 14,
        ],
        'range' => [
          'px' => [
            'min' => 6,
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .elementor-icon-list-icon' => 'width: {{SIZE}}{{UNIT}};',
          '{{WRAPPER}} .elementor-icon-list-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
          '{{WRAPPER}} .elementor-icon-list-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
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
      'text_indent',
      [
        'label' => __( 'Text Indent', 'tenweb-builder' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'max' => 50,
          ],
        ],
        'selectors' => [
          'body:not(.rtl) {{WRAPPER}} .elementor-icon-list-text' => 'padding-left: {{SIZE}}{{UNIT}}',
          'body.rtl {{WRAPPER}} .elementor-icon-list-text' => 'padding-right: {{SIZE}}{{UNIT}}',
        ],
      ]
    );

    $this->add_control(
      'text_color',
      [
        'label' => __( 'Text Color', 'tenweb-builder' ),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .elementor-icon-list-text, {{WRAPPER}} .elementor-icon-list-text a' => 'color: {{VALUE}}',
        ],
        'global' => [
          'default' => Global_Colors::COLOR_SECONDARY,
        ],
      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'icon_typography',
        'selector' => '{{WRAPPER}} .elementor-icon-list-item',
        'global' => [
          'default' => Global_Typography::TYPOGRAPHY_TEXT,
        ],
      ]
    );

    $this->end_controls_section();
  }

  protected function get_taxonomies() {
    $taxonomies = get_taxonomies( [
      'show_in_nav_menus' => true,
    ], 'objects' );

    $options = [
      '' => __( 'Choose', 'tenweb-builder' ),
    ];

    foreach ( $taxonomies as $taxonomy ) {
      $options[ $taxonomy->name ] = $taxonomy->label;
    }

    return $options;
  }

  protected function set_global_authordata() {
    global $authordata;
    if ( ! isset( $authordata->ID ) ) {
      $post = get_post();
      $authordata = get_userdata( $post->post_author ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
    }
  }

  protected function get_meta_data( $repeater_item ) {
    $item_data = [];

    switch ( $repeater_item['type'] ) {
      case 'author':
        $item_data['text'] = get_the_author_meta( 'display_name' );
        $item_data['icon'] = 'far fa-user-circle'; // Default icon.
        $item_data['selected_icon'] = [
          'value' => 'far fa-user-circle',
          'library' => 'fa-regular',
        ]; // Default icons.
        $item_data['itemprop'] = 'author';

        if ( 'yes' === $repeater_item['link'] ) {
          $item_data['url'] = get_author_posts_url( get_the_author_meta( 'ID' ) );
        }

        if ( 'yes' === $repeater_item['show_avatar'] ) {
          $item_data['image'] = get_avatar_url( get_the_author_meta( 'ID' ), 96 );
        }

        break;

      case 'date':
        $custom_date_format = empty( $repeater_item['custom_date_format'] ) ? 'F j, Y' : $repeater_item['custom_date_format'];

        $format_options = [
          'default' => 'F j, Y',
          '0' => 'F j, Y',
          '1' => 'Y-m-d',
          '2' => 'm/d/Y',
          '3' => 'd/m/Y',
          'custom' => $custom_date_format,
        ];

        $item_data['text'] = get_the_time( $format_options[$repeater_item['date_format']] );
        $item_data['icon'] = 'fa fa-calendar'; // Default icon
        $item_data['selected_icon'] = [
          'value' => 'fas fa-calendar',
          'library' => 'fa-solid',
        ]; // Default icons.
        $item_data['itemprop'] = 'datePublished';

        if ( 'yes' === $repeater_item['link'] ) {
          $item_data['url'] = get_day_link( get_post_time( 'Y' ), get_post_time( 'm' ), get_post_time( 'j' ) );
        }
        break;

      case 'time':
        $custom_time_format = empty( $repeater_item['custom_time_format'] ) ? 'g:i a' : $repeater_item['custom_time_format'];

        $format_options = [
          'default' => 'g:i a',
          '0' => 'g:i a',
          '1' => 'g:i A',
          '2' => 'H:i',
          'custom' => $custom_time_format,
        ];
        $item_data['text'] = get_the_time( $format_options[$repeater_item['time_format']] );
        $item_data['icon'] = 'far fa-clock'; // Default icon
        $item_data['selected_icon'] = [
          'value' => 'far fa-clock',
          'library' => 'fa-regular',
        ]; // Default icons.
        break;

      case 'comments':

          if(Templates::get_instance()->is_twbb_template()['template_type'] !== false){
            //defaults for template page
            $item_data['text'] = '15 Comments';
            $item_data['url'] = '#';
            $item_data['selected_icon'] = [
              'value' => 'far fa-comment-dots',
              'library' => 'fa-regular',
            ];
            $item_data['itemprop'] = 'commentCount';
          }else if ( comments_open() ) {
          $default_strings = [
            'string_no_comments' => __( 'No Comments', 'tenweb-builder' ),
            'string_one_comment' => __( 'One Comment', 'tenweb-builder' ),
            'string_comments' => __( '%s Comments', 'tenweb-builder' ),
          ];

          if ( 'yes' === $repeater_item['comments_custom_strings'] ) {
            if ( ! empty( $repeater_item['string_no_comments'] ) ) {
              $default_strings['string_no_comments'] = $repeater_item['string_no_comments'];
            }

            if ( ! empty( $repeater_item['string_one_comment'] ) ) {
              $default_strings['string_one_comment'] = $repeater_item['string_one_comment'];
            }

            if ( ! empty( $repeater_item['string_comments'] ) ) {
              $default_strings['string_comments'] = $repeater_item['string_comments'];
            }
          }

          $num_comments = (int) get_comments_number(); // get_comments_number returns only a numeric value

          if ( 0 === $num_comments ) {
            $item_data['text'] = $default_strings['string_no_comments'];
          } else {
            $item_data['text'] = sprintf( _n( $default_strings['string_one_comment'], $default_strings['string_comments'], $num_comments, 'tenweb-builder' ), $num_comments );
          }

          if ( 'yes' === $repeater_item['link'] ) {
            $item_data['url'] = get_comments_link();
          }
          $item_data['icon'] = 'far fa-comment-dots'; // Default icon
          $item_data['selected_icon'] = [
            'value' => 'far fa-comment-dots',
            'library' => 'fa-regular',
          ]; // Default icons.
          $item_data['itemprop'] = 'commentCount';
        }
        break;

      case 'terms':
        $item_data['icon'] = 'fas fa-tags'; // Default icon
        $item_data['selected_icon'] = [
          'value' => 'fas fa-tags',
          'library' => 'fa-solid',
        ]; // Default icons.
        $item_data['itemprop'] = 'about';


        if(Templates::get_instance()->is_twbb_template()['template_type'] !== false){
          $item_data['terms_list'] = [
            1 => ['text' => 'Term 1', 'url' => '#'],
            2 => ['text' => 'Term 2', 'url' => '#'],
          ];
        }else {

          $taxonomy = $repeater_item['taxonomy'];
          $terms = wp_get_post_terms(get_the_ID(), $taxonomy);
          foreach($terms as $term) {
            $item_data['terms_list'][$term->term_id]['text'] = $term->name;
            if('yes' === $repeater_item['link']) {
              $item_data['terms_list'][$term->term_id]['url'] = get_term_link($term);
            }
          }
        }

        break;

      case 'custom':
        $item_data['text'] = $repeater_item['custom_text'];
        $item_data['icon'] = 'fa fa-info-circle'; // Default icon.
        $item_data['selected_icon'] = [
          'value' => 'fas fa-tags',
          'library' => 'fa-regular',
        ]; // Default icons.

        if ( 'yes' === $repeater_item['link'] && ! empty( $repeater_item['custom_url'] ) ) {
          $item_data['url'] = $repeater_item['custom_url'];
        }

        break;
    }

    $item_data['type'] = $repeater_item['type'];

    if ( ! empty( $repeater_item['text_prefix'] ) ) {
      $item_data['text_prefix'] = esc_html( $repeater_item['text_prefix'] );
    }

    return $item_data;
  }

  protected function render_item( $repeater_item ) {
    $item_data = $this->get_meta_data( $repeater_item );
    $repeater_index = $repeater_item['_id'];

    if ( empty( $item_data['text'] ) && empty( $item_data['terms_list'] ) ) {
      return;
    }

    $has_link = false;
    $link_key = 'link_' . $repeater_index;
    $item_key = 'item_' . $repeater_index;

    $this->add_render_attribute( $item_key, 'class', [
      'elementor-icon-list-item',
      'elementor-repeater-item-' . $repeater_item['_id'],
      ]
    );

    $active_settings = $this->get_active_settings();

    if ( 'inline' === $active_settings['view'] ) {
      $this->add_render_attribute( $item_key, 'class', 'elementor-inline-item' );
    }

    if ( ! empty( $item_data['url'] ) ) {
      $has_link = true;
      $this->add_render_attribute( $link_key, 'href', $item_data['url'] );
    }

    if ( ! empty( $item_data['itemprop'] ) ) {
      $this->add_render_attribute( $item_key, 'itemprop', $item_data['itemprop'] );
    }

    ?>
    <li <?php $this->print_render_attribute_string( $item_key ); ?>>
      <?php if ( $has_link ) : ?>
      <a <?php $this->print_render_attribute_string( $link_key ); ?>>
        <?php endif; ?>
        <?php $this->render_item_icon_or_image( $item_data, $repeater_item, $repeater_index ); ?>
        <?php $this->render_item_text( $item_data, $repeater_index ); ?>
        <?php if ( $has_link ) : ?>
      </a>
    <?php endif; ?>
    </li>
    <?php
  }

  protected function render_item_icon_or_image( $item_data, $repeater_item, $repeater_index ) {
    // Set icon according to user settings.
    $migration_allowed = Icons_Manager::is_migration_allowed();
    if ( ! $migration_allowed ) {
      if ( 'custom' === $repeater_item['show_icon'] && !empty($repeater_item['icon']) ) {
        $item_data['icon'] = $repeater_item['icon'];
      }
      elseif ( 'none' === $repeater_item['show_icon'] ) {
        $item_data['icon'] = '';
      }
    } else {
      if ( 'custom' === $repeater_item['show_icon'] && ! empty( $repeater_item['selected_icon'] ) ) {
        $item_data['selected_icon'] = $repeater_item['selected_icon'];
      } elseif ( 'none' === $repeater_item['show_icon'] ) {
        $item_data['selected_icon'] = [];
      }
    }

    if ( empty( $item_data['icon'] ) && empty( $item_data['selected_icon'] ) && empty( $item_data['image'] ) ) {
      return;
    }

    $migrated = isset( $repeater_item['__fa4_migrated']['selected_icon'] );
    $is_new = empty( $repeater_item['icon'] ) && $migration_allowed;
    $show_icon = 'none' !== $repeater_item['show_icon'];

		if ( ! empty( $item_data['image'] ) || $show_icon ) {
			?>
			<span class="elementor-icon-list-icon">
			<?php
      if ( ! empty( $item_data['image'] ) ) :
        $image_data = 'image_' . $repeater_index;
        $this->add_render_attribute( $image_data, 'src', $item_data['image'] );
        $this->add_render_attribute( $image_data, 'alt', $item_data['text'] );
        ?>
        <img class="elementor-avatar" <?php $this->print_render_attribute_string( $image_data ); ?>>
      <?php elseif ( $show_icon ) : ?>
        <?php if ( $is_new || $migrated ) :
          Icons_Manager::render_icon( $item_data['selected_icon'], [ 'aria-hidden' => 'true' ] );
        else : ?>
        <i class="<?php echo esc_attr( $item_data['icon'] ); ?>" aria-hidden="true"></i>
       <?php endif; ?>
      <?php endif; ?>
		</span>
    <?php
		}
  }

  protected function render_item_text( $item_data, $repeater_index ) {
    $repeater_setting_key = $this->get_repeater_setting_key( 'text', 'icon_list', $repeater_index );

    $this->add_render_attribute( $repeater_setting_key, 'class', [ 'elementor-icon-list-text', 'elementor-post-info__item', 'elementor-post-info__item--type-' . $item_data['type'] ] );
    if ( ! empty( $item['terms_list'] ) ) {
      $this->add_render_attribute( $repeater_setting_key, 'class', 'elementor-terms-list' );
    }

    ?>
    <span <?php $this->print_render_attribute_string( $repeater_setting_key ); ?>>
			<?php if ( ! empty( $item_data['text_prefix'] ) ) : ?>
        <span class="elementor-post-info__item-prefix"><?php echo esc_html( $item_data['text_prefix'] ); ?></span>
      <?php endif; ?>
      <?php
      if ( ! empty( $item_data['terms_list'] ) ) :
        $terms_list = [];
        $item_class = 'elementor-post-info__terms-list-item';
        ?>
        <span class="elementor-post-info__terms-list">
				<?php
        foreach ( $item_data['terms_list'] as $term ) :
          if ( ! empty( $term['url'] ) ) :
            $terms_list[] = '<a href="' . esc_url( $term['url'] ) . '" class="' . esc_attr($item_class) . '">' . esc_html( $term['text'] ) . '</a>';
          else :
            $terms_list[] = '<span class="' . esc_attr($item_class) . '">' . esc_html( $term['text'] ) . '</span>';
          endif;
        endforeach;

        echo implode( ', ', $terms_list ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        ?>
				</span>
      <?php else : ?>
        <?php
        echo wp_kses( $item_data['text'], [
          'a' => [
            'href' => [],
            'title' => [],
            'rel' => [],
          ],
        ] );
        ?>
      <?php endif; ?>
		</span>
    <?php
  }

  protected function render() {
    $settings = $this->get_settings_for_display();
    if ( is_singular() ) {
      $this->set_global_authordata();
    }
    ob_start();
    if ( ! empty( $settings['icon_list'] ) ) {
      foreach ( $settings['icon_list'] as $repeater_item ) {
        $this->render_item( $repeater_item );
      }
    }
    $items_html = ob_get_clean();

    if ( empty( $items_html ) ) {
      return;
    }
    if ( 'inline' === $settings['view'] ) {
      $this->add_render_attribute( 'icon_list', 'class', ['elementor-inline-items', 'elementor-inline-terms'] );
    }
    if ( 'inline' === $settings['view_tablet'] ) {
          $this->add_render_attribute( 'icon_list', 'class', ['elementor-inline-tablet-items', 'elementor-inline-terms'] );
    }
    if ( 'inline' === $settings['view_mobile'] ) {
        $this->add_render_attribute( 'icon_list', 'class', ['elementor-inline-mobile-items', 'elementor-inline-terms'] );
    }

    $this->add_render_attribute( 'icon_list', 'class', [ 'elementor-icon-list-items', 'elementor-post-info' ] );
    ?>
    <ul <?php $this->print_render_attribute_string( 'icon_list' ); ?>>
      <?php echo $items_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
      ?>
    </ul>
    <?php
  }
}

\Elementor\Plugin::instance()->widgets_manager->register(new Post_Info());
