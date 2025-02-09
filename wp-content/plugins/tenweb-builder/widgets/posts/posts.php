<?php

namespace Tenweb_Builder;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Widget_Base;
use Elementor\Group_Control_Image_Size;

if(!defined('ABSPATH')) exit; // Exit if accessed directly

class Posts extends Widget_Base {

  public function __construct($data = [], $args = null){
    parent::__construct($data, $args);
  }

  public function get_name(){
    return 'twbb-posts';
  }

  public function get_title(){
    return '';
  }

  public function get_icon(){
    return 'twbb-posts twbb-widget-icon';
  }

  public function get_categories(){
    return array('tenweb-depreciated');
  }

  protected function register_controls(){

    //content tab
    $this->register_layout_section_controls();
    $this->register_query_section_controls();
    $this->register_pagination_section_controls();

    //style tab
    $this->register_style_tab_controls();
    $this->register_pagination_style_tab_controls();
  }

  protected function register_layout_section_controls(){
    $this->start_controls_section(
      'section_layout',
      [
        'label' => __('Layout', 'tenweb-builder'),
        'tab' => Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'masonry',
      [
        'label' => __('Masonry', 'tenweb-builder'),
        'type' => Controls_Manager::SWITCHER,
        'label_off' => __('Off', 'tenweb-builder'),
        'label_on' => __('On', 'tenweb-builder')
      ]
    );

    $this->add_responsive_control(
      'columns',
      [
        'label' => __('Columns', 'tenweb-builder'),
        'type' => Controls_Manager::SELECT,
        'default' => '2',
        'tablet_default' => '2',
        'mobile_default' => '1',
        'options' => [
          '1' => '1',
          '2' => '2',
          '3' => '3',
          '4' => '4',
          '5' => '5',
          '6' => '6',
        ],
        'selectors' => [
          '{{WRAPPER}} .twbb-posts-grid-container' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
        ],
        'condition' => [
          'masonry' => ''
        ],
        'prefix_class' => 'twbb-posts-grid%s-'
      ]
    );

    $this->add_responsive_control(
      'masonry_columns',
      [
        'label' => __('Columns', 'tenweb-builder'),
        'type' => Controls_Manager::SELECT,
        'default' => '2',
        'tablet_default' => '1',
        'mobile_default' => '1',
        'options' => [
          '1' => '1',
          '2' => '2',
          '3' => '3',
          '4' => '4',
          '5' => '5',
          '6' => '6',
        ],
        'selectors' => [
        ],
        'render_type' => 'template',
        'condition' => [
          'masonry' => 'yes'
        ]
      ]
    );

    if($this->add_widget_controll('posts_per_page')) {
      $this->add_control(
        'posts_per_page',
        [
          'label' => __('Posts Per Page', 'tenweb-builder'),
          'type' => Controls_Manager::NUMBER,
          'default' => 6,
        ]
      );
    }

    $this->add_control(
      'show_image',
      [
        'label' => __('Show image', 'tenweb-builder'),
        'type' => Controls_Manager::SELECT,
        'default' => 'yes',
        'options' => [
          'yes' => 'Yes',
          'no' => 'No',
        ],
      ]
    );

    $this->add_control(
      'image_position',
      [
        'label' => __('Image Position', 'tenweb-builder'),
        'type' => Controls_Manager::SELECT,
        'default' => 'above_title',
        'options' => [
          'above_title' => 'Above title',
          'below_title' => 'Below title',
        ],
      ]
    );

    $this->add_group_control(
      Group_Control_Image_Size::get_type(),
      [
        'name' => 'thumbnail_size',
        'default' => 'medium',
        'exclude' => ['custom'], //phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude
        'condition' => [
          'show_image' => 'yes',
        ]
      ]
    );

    $this->add_control(
      'show_title',
      [
        'label' => __('Title', 'tenweb-builder'),
        'type' => Controls_Manager::SWITCHER,
        'label_on' => __('Show', 'tenweb-builder'),
        'label_off' => __('Hide', 'tenweb-builder'),
        'default' => 'yes',
        'separator' => 'before',
      ]
    );

    $this->add_control(
      'title_tag',
      [
        'label' => __('Title HTML Tag', 'tenweb-builder'),
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
          'p' => 'p',
        ],
        'default' => 'h3',
        'condition' => [
          'show_title' => 'yes',
        ],
      ]
    );

    $this->add_control(
      'title_length',
      [
        'label' => __('Title Length', 'tenweb-builder'),
        'type' => Controls_Manager::NUMBER,
        'default' => '',
        'condition' => [
          'show_title' => 'yes',
        ],
      ]
    );

    $this->add_control(
      'show_excerpt',
      [
        'label' => __('Excerpt', 'tenweb-builder'),
        'type' => Controls_Manager::SWITCHER,
        'label_on' => __('Show', 'tenweb-builder'),
        'label_off' => __('Hide', 'tenweb-builder'),
        'default' => 'yes',
      ]
    );

    $this->add_control(
      'excerpt_length',
      [
        'label' => __('Excerpt Length', 'tenweb-builder'),
        'type' => Controls_Manager::NUMBER,
        /** This filter is documented in wp-includes/formatting.php */
        'default' => apply_filters('excerpt_length', 25),
        'condition' => [
          'show_excerpt' => 'yes',
        ],
      ]
    );

    $this->add_control(
      'meta_data',
      [
        'label' => __('Meta Data', 'tenweb-builder'),
        'label_block' => true,
        'type' => Controls_Manager::SELECT2,
        'default' => ['date'],
        'multiple' => true,
        'options' => [
          'author' => __('Author', 'tenweb-builder'),
          'date' => __('Date', 'tenweb-builder'),
          'time' => __('Time', 'tenweb-builder'),
          'comments' => __('Comments', 'tenweb-builder'),
          'categories' => __('Categories', 'tenweb-builder'),
          'tags' => __('Tags', 'tenweb-builder'),
        ],
        'separator' => 'before',
      ]
    );

    $this->add_control(
      'author_meta_link',
      [
        'label' => __('Author link', 'tenweb-builder'),
        'type' => Controls_Manager::SWITCHER,
        'label_on' => __('Show', 'tenweb-builder'),
        'label_off' => __('Hide', 'tenweb-builder'),
        'default' => 'no',
        'condition' => [
          'meta_data' => 'author',
        ],
      ]
    );

    $this->add_control(
      'categories_meta_link',
      [
        'label' => __('Categories link', 'tenweb-builder'),
        'type' => Controls_Manager::SWITCHER,
        'label_on' => __('Show', 'tenweb-builder'),
        'label_off' => __('Hide', 'tenweb-builder'),
        'default' => 'yes',
        'condition' => [
          'meta_data' => 'categories',
        ],
      ]
    );

    $this->add_control(
      'tags_meta_link',
      [
        'label' => __('Tags link', 'tenweb-builder'),
        'type' => Controls_Manager::SWITCHER,
        'label_on' => __('Show', 'tenweb-builder'),
        'label_off' => __('Hide', 'tenweb-builder'),
        'default' => 'no',
        'condition' => [
          'meta_data' => 'tags',
        ],
      ]
    );

    $this->add_control(
      'meta_separator',
      [
        'label' => __('Separator Between', 'tenweb-builder'),
        'type' => Controls_Manager::TEXT,
        'default' => '•',
        'selectors' => [
        ],
        'condition' => [
          'meta_data!' => [],
        ],
      ]
    );

    $this->add_control(
      'show_read_more',
      [
        'label' => __('Read More', 'tenweb-builder'),
        'type' => Controls_Manager::SWITCHER,
        'label_on' => __('Show', 'tenweb-builder'),
        'label_off' => __('Hide', 'tenweb-builder'),
        'default' => 'yes',
        'separator' => 'before',
      ]
    );

    $this->add_control(
      'read_more_text',
      [
        'label' => __('Read More Text', 'tenweb-builder'),
        'type' => Controls_Manager::TEXT,
        'default' => __('Read More', 'tenweb-builder'),
        'condition' => [
          'show_read_more' => 'yes',
        ],
      ]
    );

    $this->end_controls_section();
  }

  protected function register_query_section_controls(){

    $this->start_controls_section(
      'section_query',
      [
        'label' => __('Query', 'tenweb-builder'),
        'tab' => Controls_Manager::TAB_CONTENT,
      ]
    );

    $query_controls_data = $this->get_query_controls();
    $query_controls_data['tx_options'] = '';
    $query_controls_data['pt_options']['manual'] = __('Manual Selection', 'tenweb-builder');

    $this->add_control(
      'post_types',
      [
        'label' => __('Source', 'tenweb-builder'),
        'type' => Controls_Manager::SELECT,
        'options' => $query_controls_data['pt_options'],
        'default' => 'post'
      ]
    );

    $this->add_control(
      'post_author',
      [
        'label' => __('Author', 'tenweb-builder'),
        'type' => 'TWBBSelectAjax',
        'multiple' => true,
        'options' => [],
        'label_block' => true,
        'filter_by' => 'author',
        'condition' => [
          'post_types!' => 'manual'
        ]
      ]
    );

    $this->add_control(
      'manual_selected_posts',
      [
        'label' => __('Search & Select', 'tenweb-builder'),
        'type' => 'TWBBSelectAjax',
        'multiple' => true,
        'options' => [],
        'label_block' => true,
        'filter_by' => 'post',
        'condition' => [
          'post_types' => 'manual'
        ]
      ]
    );


    foreach($query_controls_data['tax_options'] as $pt => $taxs_options) {

      foreach($taxs_options as $tax => $tax_opt) {

        $this->add_control(
          'taxonomy_' . $pt . '_' . $tax,
          [
            'label' => $tax_opt['title'],
            'type' => Controls_Manager::SELECT2,
            'options' => $tax_opt['terms'],
            'multiple' => true,
            'label_block' => true,
            'condition' => [
              'post_types' => $pt,
            ],
          ]
        );

      }
    }

    $this->add_control(
      'advanced',
      [
        'label' => __('Advanced', 'tenweb-builder'),
        'type' => Controls_Manager::HEADING,
      ]
    );

    $this->add_control(
      'orderby',
      [
        'label' => __('Order By', 'tenweb-builder'),
        'type' => Controls_Manager::SELECT,
        'default' => 'post_date',
        'options' => [
          'post_date' => __('Date', 'tenweb-builder'),
          'post_title' => __('Title', 'tenweb-builder'),
          'rand' => __('Random', 'tenweb-builder'),
        ],
      ]
    );

    $this->add_control(
      'order',
      [
        'label' => __('Order', 'tenweb-builder'),
        'type' => Controls_Manager::SELECT,
        'default' => 'desc',
        'options' => [
          'asc' => __('ASC', 'tenweb-builder'),
          'desc' => __('DESC', 'tenweb-builder'),
        ],
      ]
    );

    $this->add_control(
      'exclude_posts',
      [
        'label' => __('Exclude posts', 'tenweb-builder'),
        'type' => 'TWBBSelectAjax',
        'multiple' => true,
        'options' => [],
        'label_block' => true,
        'filter_by' => 'post',
        'condition' => [
          'post_types!' => 'manual'
        ]
      ]
    );


    $this->end_controls_section();
  }

  protected function register_pagination_section_controls(){
    $this->start_controls_section(
      'section_pagination',
      [
        'label' => __('Pagination', 'tenweb-builder'),
        'tab' => Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'pagination',
      [
        'label' => __('Pagination', 'tenweb-builder'),
        'type' => Controls_Manager::SWITCHER,
        'label_on' => __('On', 'your-plugin'),
        'label_off' => __('Off', 'your-plugin'),
        'default' => 'yes',
      ]
    );

    $this->add_control(
      'pagination_page_limit',
      [
        'label' => __('Page Limit', 'tenweb-builder'),
        'type' => Controls_Manager::NUMBER,
        'default' => '5',
        'condition' => [
          'pagination' => 'yes'
        ]
      ]
    );

      $this->add_control(
          'pagination_scroll_top',
          [
              'label' => __('Scroll to top', 'tenweb-builder'),
              'description' => esc_html__('Scroll to top after changing page','tenweb-builder'),
              'type' => Controls_Manager::SWITCHER,
              'label_on' => __('Yes', 'your-plugin'),
              'label_off' => __('No', 'your-plugin'),
              'default' => 'yes',
              'condition' => [
                  'pagination' => 'yes'
              ]
          ]
      );

    $this->add_control(
      'pagination_next_prev_buttons',
      [
        'label' => __('Next and Previous buttons', 'tenweb-builder'),
        'type' => Controls_Manager::SWITCHER,
        'label_on' => __('On', 'your-plugin'),
        'label_off' => __('Off', 'your-plugin'),
        'default' => 'yes',
        'condition' => [
          'pagination' => 'yes'
        ]
      ]
    );

    $this->add_control(
      'pagination_first_last_buttons',
      [
        'label' => __('First and Last Buttons', 'tenweb-builder'),
        'type' => Controls_Manager::SWITCHER,
        'label_on' => __('On', 'your-plugin'),
        'label_off' => __('Off', 'your-plugin'),
        'default' => '',
        'condition' => [
          'pagination' => 'yes'
        ]
      ]
    );

    $this->add_control(
      'pagination_number_buttons',
      [
        'label' => __('Numbers', 'tenweb-builder'),
        'type' => Controls_Manager::SWITCHER,
        'label_on' => __('On', 'your-plugin'),
        'label_off' => __('Off', 'your-plugin'),
        'default' => 'yes',
        'condition' => [
          'pagination' => 'yes'
        ]
      ]
    );


    $this->add_control(
      'pagination_prev_label',
      [
        'label' => __('Previous Label', 'tenweb-builder'),
        'default' => __('Prev', 'tenweb-builder'),
        'condition' => [
          'pagination' => 'yes',
          'pagination_next_prev_buttons' => 'yes',
        ]
      ]
    );

    $this->add_control(
      'pagination_next_label',
      [
        'label' => __('Next Label', 'tenweb-builder'),
        'default' => __('Next', 'tenweb-builder'),
        'condition' => [
          'pagination' => 'yes',
          'pagination_next_prev_buttons' => 'yes',
        ]
      ]
    );

    $this->add_control(
      'pagination_first_label',
      [
        'label' => __('First Label', 'tenweb-builder'),
        'default' => __('First', 'tenweb-builder'),
        'condition' => [
          'pagination' => 'yes',
          'pagination_first_last_buttons' => 'yes',
        ]
      ]
    );

    $this->add_control(
      'pagination_last_label',
      [
        'label' => __('Last Label', 'tenweb-builder'),
        'default' => __('Last', 'tenweb-builder'),
        'condition' => [
          'pagination' => 'yes',
          'pagination_first_last_buttons' => 'yes',
        ]
      ]
    );

    $this->end_controls_section();
  }

  protected function register_style_tab_controls(){

    //layout section
    $this->start_controls_section(
      'section_style_layout',
      [
        'label' => __('Layout', 'tenweb-builder'),
        'tab' => Controls_Manager::TAB_STYLE,
      ]
    );

    $this->add_control(
      'column_gap',
      [
        'label' => __('Columns Gap', 'tenweb-builder'),
        'type' => Controls_Manager::SLIDER,
        'default' => [
          'size' => 30,
        ],
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 100,
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .twbb-posts-grid-container .twbb-posts-item' => 'margin-right: calc( {{SIZE}}{{UNIT}}/2 ); margin-left: calc( {{SIZE}}{{UNIT}}/2 );',
          '{{WRAPPER}} .twbb-posts-grid-container' => 'margin-left: calc( -{{SIZE}}{{UNIT}}/2 ); margin-right: calc( -{{SIZE}}{{UNIT}}/2 );',
        ],
        'condition' => [
          'masonry' => ''
        ]
      ]
    );


    $this->add_control(
      'row_gap',
      [
        'label' => __('Rows Gap', 'tenweb-builder'),
        'type' => Controls_Manager::SLIDER,
        'default' => [
          'size' => 35,
        ],
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 100,
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .twbb-posts-grid-container .twbb-posts-item' => 'margin-bottom: {{SIZE}}{{UNIT}};',
        ],
        'condition' => [
          'masonry' => ''
        ]
      ]
    );

    $this->add_control(
      'masonry_column_gap',
      [
        'label' => __('Columns Gap', 'tenweb-builder'),
        'type' => Controls_Manager::SLIDER,
        'default' => [
          'size' => 30,
        ],
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 100,
          ],
        ],
        'selectors' => [],
        'condition' => [
          'masonry' => 'yes'
        ],
        'render_type' => 'template'
      ]
    );


    $this->add_control(
      'masonry_row_gap',
      [
        'label' => __('Rows Gap', 'tenweb-builder'),
        'type' => Controls_Manager::SLIDER,
        'default' => [
          'size' => 35,
        ],
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 100,
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .twbb-posts-masonry-container .twbb-posts-item' => 'margin-bottom: {{SIZE}}{{UNIT}};',
        ],
        'condition' => [
          'masonry' => 'yes'
        ],
        'render_type' => 'template'
      ]
    );

    $this->end_controls_section();


    //block skin section
    $this->start_controls_section(
      'section_style_block',
      [
        'label' => __('Block', 'tenweb-builder'),
        'tab' => Controls_Manager::TAB_STYLE,
      ]
    );

    $this->add_control(
      'block_bg_color',
      [
        'label' => __('Background Color', 'tenweb-builder'),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .twbb-posts-widget-container .twbb-posts-item' => 'background-color:{{VALUE}};'
        ],
      ]
    );

    $this->add_control(
      'block_border_color',
      [
        'label' => __('Border Color', 'tenweb-builder'),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .twbb-posts-widget-container .twbb-posts-item' => 'border-color:{{VALUE}};'
        ],
      ]
    );

    $this->add_control(
      'block_border_width',
      [
        'label' => __('Border Width', 'tenweb-builder'),
        'type' => Controls_Manager::SLIDER,
        'size_units' => ['px'],
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 15,
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .twbb-posts-widget-container .twbb-posts-item' => 'border-width:{{SIZE}}{{UNIT}};'
        ],
      ]
    );

    $this->add_control(
      'block_border_radius',
      [
        'label' => __('Border Radius', 'tenweb-builder'),
        'type' => Controls_Manager::SLIDER,
        'size_units' => ['px', '%'],
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 200,
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .twbb-posts-widget-container .twbb-posts-item' => 'border-radius:{{SIZE}}{{UNIT}};'
        ],
      ]
    );

    $this->add_control(
      'block_padding',
      [
        'label' => __('Horizontal Padding', 'tenweb-builder'),
        'type' => Controls_Manager::SLIDER,
        'size_units' => ['px'],
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 50,
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .twbb-posts-widget-container .twbb-posts-item' => 'padding-left:{{SIZE}}{{UNIT}};padding-right:{{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_control(
      'block_vertical_padding',
      [
        'label' => __('Vertical Padding', 'tenweb-builder'),
        'type' => Controls_Manager::SLIDER,
        'size_units' => ['px'],
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 50,
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .twbb-posts-widget-container .twbb-posts-item' => 'padding-top:{{SIZE}}{{UNIT}};padding-bottom:{{SIZE}}{{UNIT}};',
        ],
      ]
    );
    $this->end_controls_section();

    //content
    $this->start_controls_section(
      'section_design_content',
      [
        'label' => __('Content', 'tenweb-builder'),
        'tab' => Controls_Manager::TAB_STYLE,
      ]
    );

    $this->add_control(
      'alignment',
      [
        'label' => __('Alignment', 'tenweb-builder'),
        'type' => Controls_Manager::CHOOSE,
        'label_block' => false,
        'options' => [
          'left' => [
            'title' => __('Left', 'tenweb-builder'),
            'icon' => 'fa fa-align-left',
          ],
          'center' => [
            'title' => __('Center', 'tenweb-builder'),
            'icon' => 'fa fa-align-center',
          ],
          'right' => [
            'title' => __('Right', 'tenweb-builder'),
            'icon' => 'fa fa-align-right',
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .twbb-posts-widget-container .twbb-posts-item' => 'text-align:{{VALUE}};'
        ]
      ]
    );

    $this->add_control(
      'image_spacing',
      [
        'label' => __('Image Spacing', 'tenweb-builder'),
        'type' => Controls_Manager::SLIDER,
        'size_units' => ['px'],
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 200,
          ],
        ],
        'default' => [
          'size' => 16
        ],
        'condition' => [
          'show_image' => 'yes'
        ],
        'selectors' => [
          '{{WRAPPER}} .twbb-posts-widget-container .twbb-posts-image' => 'margin-bottom:{{SIZE}}{{UNIT}};'
        ],
      ]
    );

    $this->add_control(
      'heading_title_style',
      [
        'label' => __('Title', 'tenweb-builder'),
        'type' => Controls_Manager::HEADING,
        'condition' => [
          'show_title' => 'yes',
        ],
      ]
    );

    $this->add_control(
      'title_color',
      [
        'label' => __('Color', 'tenweb-builder'),
        'type' => Controls_Manager::COLOR,
          'global' => [
              'default' => Global_Colors::COLOR_SECONDARY,
          ],
        'selectors' => [
          '{{WRAPPER}} .twbb-posts-widget-container .twbb-posts-title' => 'color: {{VALUE}};',
          '{{WRAPPER}} .twbb-posts-widget-container .twbb-posts-title .twbb-posts-title-tag' => 'color: {{VALUE}};',
        ],
        'condition' => [
          'show_title' => 'yes',
        ],
      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'title_typography',
          'global' => [
              'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
          ],
        'selector' => '{{WRAPPER}} .twbb-posts-widget-container .twbb-posts-title, {{WRAPPER}} .twbb-posts-widget-container .twbb-posts-title .twbb-posts-title-tag',
        'fields_options' => [
          'font_size' => [
            'default' => ['unit' => 'px', 'size' => 22]
          ],
          'line_height' => [
            'default' => ['unit' => 'px', 'size' => 30]
          ],
          'letter_spacing' => [
            'default' => ['unit' => 'px', 'size' => 0.6]
          ],
        ],
        'condition' => [
          'show_title' => 'yes',
        ],
      ]
    );

    $this->add_control(
      'title_spacing',
      [
        'label' => __('Spacing', 'tenweb-builder'),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'max' => 100,
          ],
        ],
        'default' => [
          'size' => 0
        ],
        'selectors' => [
          '{{WRAPPER}} .twbb-posts-widget-container .twbb-posts-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
        ],
        'condition' => [
          'show_title' => 'yes',
        ],
      ]
    );

    $this->add_control(
        'title_padding',
        [
            'label' => __( 'Padding', 'tenweb-builder' ),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em', '%' ],
            'selectors' => [
                '{{WRAPPER}} .twbb-posts-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
    );

    $this->add_control(
      'heading_excerpt_style',
      [
        'label' => __('Excerpt', 'tenweb-builder'),
        'type' => Controls_Manager::HEADING,
        'separator' => 'before',
        'condition' => [
          'show_excerpt' => 'yes',
        ],
      ]
    );

    $this->add_control(
      'excerpt_color',
      [
        'label' => __('Color', 'tenweb-builder'),
        'type' => Controls_Manager::COLOR,
          'global' => [
              'default' => Global_Colors::COLOR_ACCENT,
          ],
        'selectors' => [
          '{{WRAPPER}} .twbb-posts-widget-container .twbb-posts-content' => 'color: {{VALUE}};',
        ],
        'condition' => [
          'show_excerpt' => 'yes',
        ],
      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'excerpt_typography',
          'global' => [
              'default' => Global_Typography::TYPOGRAPHY_TEXT,
          ],
        'selector' => '{{WRAPPER}} .twbb-posts-widget-container .twbb-posts-content',
        'fields_options' => [
          'font_size' => [
            'default' => ['unit' => 'px', 'size' => 16]
          ],
          'line_height' => [
            'default' => ['unit' => 'px', 'size' => 22]
          ],
          'letter_spacing' => [
            'default' => ['unit' => 'px', 'size' => 0.5]
          ],
        ],
        'condition' => [
          'show_excerpt' => 'yes',
        ],
      ]
    );

    $this->add_control(
      'excerpt_spacing',
      [
        'label' => __('Spacing', 'tenweb-builder'),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'max' => 100,
          ],
        ],
        'default' => [
          'size' => 12
        ],
        'selectors' => [
          '{{WRAPPER}} .twbb-posts-widget-container .twbb-posts-content' => 'margin-bottom: {{SIZE}}{{UNIT}};',
        ],
        'condition' => [
          'show_excerpt' => 'yes',
        ],
      ]
    );

    $this->add_control(
      'excerpt_padding',
      [
          'label' => __( 'Padding', 'tenweb-builder' ),
          'type' => Controls_Manager::DIMENSIONS,
          'size_units' => [ 'px', 'em', '%' ],
          'selectors' => [
              '{{WRAPPER}} .twbb-posts-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
          ],
      ]
    );

    $this->add_control(
      'heading_readmore_style',
      [
        'label' => __('Read More', 'tenweb-builder'),
        'type' => Controls_Manager::HEADING,
        'separator' => 'before',
        'condition' => [
          'show_read_more' => 'yes',
        ],
      ]
    );

    $this->add_control(
      'read_more_color',
      [
        'label' => __('Color', 'tenweb-builder'),
        'type' => Controls_Manager::COLOR,
          'global' => [
              'default' => Global_Colors::COLOR_ACCENT,
          ],
        'selectors' => [
          '{{WRAPPER}} .twbb-posts-widget-container .twbb-posts-read-more a' => 'color: {{VALUE}};',
        ],
        'condition' => [
          'show_read_more' => 'yes',
        ],
      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'read_more_typography',
        'selector' => '{{WRAPPER}} .twbb-posts-widget-container .twbb-posts-read-more a',
          'global' => [
              'default' => Global_Typography::TYPOGRAPHY_ACCENT,
          ],
        'fields_options' => [
          'font_size' => [
            'default' => ['unit' => 'px', 'size' => 14]
          ],
          'line_height' => [
            'default' => ['unit' => 'px', 'size' => 30]
          ],
          'letter_spacing' => [
            'default' => ['unit' => 'px', 'size' => 0.6]
          ],
        ],
        'condition' => [
          'show_read_more' => 'yes',
        ],
      ]
    );

    $this->add_control(
      'read_more_spacing',
      [
        'label' => __('Spacing', 'tenweb-builder'),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'max' => 100,
          ],
        ],
        'default' => [
          'size' => 5
        ],
        'selectors' => [
          '{{WRAPPER}} .twbb-posts-widget-container .twbb-posts-read-more' => 'margin-bottom: {{SIZE}}{{UNIT}};',
        ],
        'condition' => [
          'show_read_more' => 'yes',
        ],
      ]
    );

    $this->add_control(
      'read_more_padding',
      [
          'label' => __( 'Padding', 'tenweb-builder' ),
          'type' => Controls_Manager::DIMENSIONS,
          'size_units' => [ 'px', 'em', '%' ],
          'selectors' => [
              '{{WRAPPER}} .twbb-posts-read-more' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
          ],
      ]
    );

    $this->add_control(
      'heading_meta_style',
      [
        'label' => __('Meta', 'tenweb-builder'),
        'type' => Controls_Manager::HEADING,
        'separator' => 'before',
        'condition' => [
          'meta_data!' => [],
        ],
      ]
    );

    $this->add_control(
      'meta_color',
      [
        'label' => __('Color', 'tenweb-builder'),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .twbb-posts-widget-container .twbb-posts-meta-data *' => 'color: {{VALUE}};',
        ],
        'condition' => [
          'meta_data!' => [],
        ],
      ]
    );

    $this->add_control(
      'meta_separator_color',
      [
        'label' => __('Separator Color', 'tenweb-builder'),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .twbb-posts-widget-container .twbb-posts-meta-data .twbb-posts-meta-separator' => 'color: {{VALUE}};',
        ],
        'condition' => [
          'meta_data!' => [],
        ],
      ]
    );

    $this->add_control(
      'meta_border_color',
      [
        'label' => __('Meta Border Color', 'tenweb-builder'),
        'type' => Controls_Manager::COLOR,
        'separator' => 'before',
        'selectors' => [
          '{{WRAPPER}} .twbb-posts-widget-container .twbb-posts-meta-data' => 'border-top-color:{{VALUE}};',
        ],
        'default' => '#eaeaea',
        'condition' => [
          'meta_data!' => [],
        ],
      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'meta_typography',
          'global' => [
              'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
          ],
        'selector' => '{{WRAPPER}} .twbb-posts-widget-container .twbb-posts-meta-data *',
        'fields_options' => [
          'font_size' => [
            'default' => ['unit' => 'px', 'size' => 14]
          ],
          'line_height' => [
            'default' => ['unit' => 'px', 'size' => 20]
          ],
          'letter_spacing' => [
            'default' => ['unit' => 'px', 'size' => 0.5]
          ],
        ],
        'condition' => [
          'meta_data!' => [],
        ],
      ]
    );

    $this->add_control(
      'meta_spacing',
      [
        'label' => __('Spacing', 'tenweb-builder'),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'max' => 100,
          ],
        ],
        'default' => [
          'size' => 0
        ],
        'selectors' => [
          '{{WRAPPER}} .twbb-posts-widget-container .twbb-posts-meta-data' => 'margin-bottom: {{SIZE}}{{UNIT}};',
        ],
        'condition' => [
          'meta_data!' => [],
        ],
      ]
    );

    $this->add_control(
      'meta_padding',
      [
          'label' => __( 'Padding', 'tenweb-builder' ),
          'type' => Controls_Manager::DIMENSIONS,
          'size_units' => [ 'px', 'em', '%' ],
          'selectors' => [
              '{{WRAPPER}} .twbb-posts-meta-data' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
          ],
      ]
    );

    $this->end_controls_section();
  }

  protected function register_pagination_style_tab_controls(){

    $this->start_controls_section(
      'section_pagination_style',
      [
        'label' => __('Pagination', 'tenweb-builder'),
        'tab' => Controls_Manager::TAB_STYLE,
        'condition' => [
          'pagination' => 'yes'
        ],
      ]
    );

    $this->add_control(
      'pagination_align',
      [
        'label' => __('Alignment', 'tenweb-builder'),
        'type' => Controls_Manager::CHOOSE,
        'options' => [
          'left' => [
            'title' => __('Left', 'tenweb-builder'),
            'icon' => 'fa fa-align-left',
          ],
          'center' => [
            'title' => __('Center', 'tenweb-builder'),
            'icon' => 'fa fa-align-center',
          ],
          'right' => [
            'title' => __('Right', 'tenweb-builder'),
            'icon' => 'fa fa-align-right',
          ],
        ],
        'default' => 'center',
        'selectors' => [
          '{{WRAPPER}} .twbb-posts-pagination' => 'text-align: {{VALUE}};',
        ]
      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'pagination_typography',
        'selector' => '{{WRAPPER}} .twbb-posts-pagination a.twbb-posts-page',
          'global' => [
              'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
          ],
        'fields_options' => [
          'font_size' => [
            'default' => ['unit' => 'px', 'size' => 14]
          ],
          'letter_spacing' => [
            'default' => ['unit' => 'px', 'size' => 0.6]
          ],
        ],
      ]
    );

    $this->add_control(
      'pagination_color_heading',
      [
        'label' => __('Colors', 'tenweb-builder'),
        'type' => Controls_Manager::HEADING,
        'separator' => 'before',
      ]
    );

    $this->start_controls_tabs('pagination_colors');

    $this->start_controls_tab(
      'pagination_color_normal',
      [
        'label' => __('Normal', 'tenweb-builder'),
      ]
    );

    $this->add_control(
      'pagination_color',
      [
        'label' => __('Color', 'tenweb-builder'),
        'type' => Controls_Manager::COLOR,
          'global' => [
              'default' => Global_Colors::COLOR_SECONDARY,
          ],
        'selectors' => [
          '{{WRAPPER}} .twbb-posts-pagination a.twbb-posts-page' => 'color: {{VALUE}};',
        ],
      ]
    );

    $this->end_controls_tab();

    $this->start_controls_tab(
      'pagination_color_hover',
      [
        'label' => __('Hover', 'tenweb-builder'),
      ]
    );

    $this->add_control(
      'pagination_hover_color',
      [
        'label' => __('Color', 'tenweb-builder'),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .twbb-posts-pagination a.twbb-posts-page:hover' => 'color: {{VALUE}};',
        ],
      ]
    );

    $this->end_controls_tab();

    $this->start_controls_tab(
      'pagination_color_active',
      [
        'label' => __('Active', 'tenweb-builder'),
      ]
    );

    $this->add_control(
      'pagination_active_color',
      [
        'label' => __('Color', 'tenweb-builder'),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .twbb-posts-pagination .twbb-posts-page.twbb-posts-current-page' => 'color: {{VALUE}};',
        ],
      ]
    );

    $this->end_controls_tab();

    $this->end_controls_tabs();

    $this->add_responsive_control(
      'pagination_spacing',
      [
        'label' => __('Space Between', 'tenweb-builder'),
        'type' => Controls_Manager::SLIDER,
        'separator' => 'before',
        'default' => [
          'size' => 10,
        ],
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 100,
          ],
        ],
        'selectors' => [
          '.twbb-posts-pagination .twbb-posts-page:not(:first-child)' => 'margin-left: calc( {{SIZE}}{{UNIT}}/2 );',
          '.twbb-posts-pagination .twbb-posts-page:not(:last-child)' => 'margin-right: calc( {{SIZE}}{{UNIT}}/2 );',
        ],
      ]
    );

    $this->end_controls_section();
  }

  public function get_query_controls(){

    $pt_options = array();
    $tax_options = array();

    $post_type_args = array('show_in_nav_menus' => true);
    $post_types = get_post_types($post_type_args, 'objects');

    foreach($post_types as $pt) {
      $pt_options[$pt->name] = $pt->label;


      $taxonomy_filter_args = [
        'show_in_nav_menus' => true,
        'object_type' => [$pt->name],
      ];

      $taxonomies = get_taxonomies($taxonomy_filter_args, 'objects');
      if(empty($taxonomies)) {
        continue;
      }


      $tax_options[$pt->name] = array();
      foreach($taxonomies as $tax) {

        $terms = get_terms(array(
          'taxonomy' => $tax->name,
          'hide_empty' => false,
        ));


        $terms_options = array();
        foreach($terms as $term) {
          $terms_options[$term->term_id] = $term->name;
        }


        $tax_options[$pt->name][$tax->name] = array(
          'title' => $tax->label,
          'terms' => $terms_options
        );
      }
    }

    $result = array(
      'tax_options' => $tax_options,
      'pt_options' => $pt_options,
    );

    return $result;
  }

  protected function get_taxonomies(){
    $taxonomies = get_taxonomies(['show_in_nav_menus' => true], 'objects');

    $options = ['' => ''];

    foreach($taxonomies as $taxonomy) {
      $options[$taxonomy->name] = $taxonomy->label;
    }

    return $options;
  }

  protected function render(){

    $settings = $this->get_settings();


    $js_params = $this->get_js_params();


    $this->add_render_attribute('posts-container', 'class', 'twbb-posts-widget-container');
    if($settings['masonry'] === 'yes') {
      $this->add_render_attribute('posts-container', 'class', 'twbb-posts-masonry-container');
    } else {
      $this->add_render_attribute('posts-container', 'class', 'twbb-posts-grid-container');
    }

    include TWBB_DIR . '/widgets/posts-base/view.php';
  }

  protected function get_query_args() {

    $settings = $this->get_settings();

    if($settings['post_types'] === 'manual') {

      $query_args = array(
        'post_type' => 'any',
        'posts_per_page' => $settings['posts_per_page'],
        'orderby' => $settings['orderby'],
        'order' => $settings['order'],
        'post__in' => (is_array($settings['manual_selected_posts'])) ? $settings['manual_selected_posts'] : array()
      );

      $query_args['additional_info'] = $this->get_query_args_additional_info();


      return $query_args;
    }

    $taxonomy_filter_args = array(
      'show_in_nav_menus' => true,
      'object_type' => array($settings['post_types']),
    );

    $taxonomies = get_taxonomies($taxonomy_filter_args, 'objects');

    $tax_query = array(
      "relation" => 'OR'
    );

    foreach($taxonomies as $tax => $tax_obj) {
      $opt_name = 'taxonomy_' . $settings['post_types'] . '_' . $tax;
      if(!empty($settings[$opt_name])) {

        $tax_query[] = array(
          'taxonomy' => $tax,
          'field' => 'term_id',
          'terms' => $settings[$opt_name],
          'operator' => 'IN',
        );

      }
    }

    $query_args = array(
      'posts_per_page' => $settings['posts_per_page'],
      'post_type' => $settings['post_types'],
      'orderby' => $settings['orderby'],
      'order' => $settings['order'],
      'tax_query' => $tax_query, //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
    );

    if(is_array($settings['post_author']) && !empty($settings['post_author'])) {
      $query_args['author__in'] = $settings['post_author'];
    }

    if(is_array($settings['exclude_posts']) && !empty($settings['exclude_posts'])) {
      $exclude_posts = $settings['exclude_posts'];
    } else {
      $exclude_posts = array();
    }

    $query_args['post__not_in'] = $exclude_posts; //phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn
    $query_args['additional_info'] = $this->get_query_args_additional_info();
    return $query_args;
  }

  protected function get_query_args_additional_info(){
    $settings = $this->get_settings();
    $query_args_additional_info = array(
      'thumbnail_size_size' => (isset($settings['show_image']) && $settings['show_image'] === 'yes') ? $settings['thumbnail_size_size'] : null,
      'excerpt_length' => (isset($settings['show_excerpt']) && $settings['show_excerpt'] === 'yes') ? $settings['excerpt_length'] : null,
      'permalink' => true
    );
    if (isset($settings['title_length'])) {
	  $query_args_additional_info['title_length'] = $settings['title_length'];
	}
    if (isset($settings['meta_data']) && is_array($settings['meta_data'])) {
	  $query_args_additional_info['date'] = in_array('date', $settings['meta_data'], true);
	  $query_args_additional_info['time'] = in_array('time', $settings['meta_data'], true);
      $query_args_additional_info['comments'] = in_array('comments', $settings['meta_data'], true);
      $query_args_additional_info['author'] = in_array('author', $settings['meta_data'], true);
      $query_args_additional_info['categories'] = in_array('categories', $settings['meta_data'], true);
      $query_args_additional_info['tags'] = in_array('tags', $settings['meta_data'], true);
    }

    return $query_args_additional_info;
  }

    protected function get_js_params(){
        $query_args = $this->get_query_args();
        $query_args_hash = self::get_query_args_hash($query_args);
        $js_params = array(
            'query_args' => base64_encode(json_encode($query_args)),//phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode
            'query_args_hash' => $query_args_hash,
            'widget_id' => $this->get_id(),
            'settings' => $this->get_settings()
        );
        return $js_params;
    }

  public static function twbb_ajax() {
	//phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotValidated
    $query_args = json_decode(base64_decode(sanitize_text_field($_POST['query_args'])), true);
	//phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotValidated
    $query_args_hash = sanitize_text_field($_POST['query_args_hash']);
    $page = intval($_POST['page']);// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotValidated

    if(self::get_query_args_hash($query_args) !== $query_args_hash) {
      wp_send_json_error();
    }

    $query_args_other = $query_args['additional_info'];
    unset($query_args['additional_info']);

    $query_args['post_status'] = 'publish';
    $query_args['paged'] = $page;

    $get_posts = new \WP_Query();
    $posts = $get_posts->query($query_args);
    self::add_posts_additional_info($posts, $query_args_other);

    $data = array(
      'posts' => $posts,
      'pages_count' => $get_posts->max_num_pages
    );

    wp_send_json_success($data);
  }

  protected static function strip_tags_content($text, $tags = '', $invert = FALSE) {
    preg_match_all('/<(.+?)[\s]*\/?[\s]*>/si', trim($tags), $tags);
    $tags = array_unique($tags[1]);

    if(is_array($tags) AND count($tags) > 0) {
      if($invert === FALSE) {
        return preg_replace('@<(?!(?:'. implode('|', $tags) .')\b)(\w+)\b.*?>.*?</\1>@si', '', $text);
      }
      else {
        return preg_replace('@<('. implode('|', $tags) .')\b.*?>.*?</\1>@si', '', $text);
      }
    }
    elseif($invert === FALSE) {
      return preg_replace('@<(\w+)\b.*?>.*?</\1>@si', '', $text);
    }
    return $text;
  }

  protected static function add_posts_additional_info( &$posts, $additional_settings ) {
    if ( !empty($posts) ) {
      foreach ( $posts as $i => $post ) {
        if ( isset($additional_settings['thumbnail_size_size']) ) {
          $img = get_the_post_thumbnail_url($post, $additional_settings['thumbnail_size_size']);
          $post->twbb_image = (is_string($img) && !empty($img)) ? $img : "";
        }
        if ( !empty($additional_settings['title_length']) ) {
          $post->post_title = wp_trim_words(strip_shortcodes(wp_strip_all_tags($post->post_title)), intval($additional_settings['title_length']));
        }
        if ( isset($additional_settings['excerpt_length']) ) {
          $excerpt = (!empty($post->post_excerpt)) ? $post->post_excerpt : $post->post_content;
          $excerpt = self::strip_tags_content($excerpt, '<style>', TRUE);
          $post->twbb_excerpt = wp_trim_words(strip_shortcodes(wp_strip_all_tags($excerpt)), intval($additional_settings['excerpt_length']));
        }
        if ( isset($additional_settings['permalink']) ) {
          $post->twbb_permalink = get_permalink($post);
        }
        if ( isset($additional_settings['date']) ) {
          $date_format = get_option('date_format', 'Y-m-d');
		  // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
          $post->twbb_date = date($date_format, strtotime($post->post_date));//todo
        }
        if ( isset($additional_settings['time']) ) {
          $time_format = get_option('time_format', 'g:i a');
          // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
          $post->twbb_time = date($time_format, strtotime($post->post_date));//todo
        }
        if ( isset($additional_settings['comments']) ) {
          $post->twbb_comments = get_comments_number($post);
        }
        if ( isset($additional_settings['author']) ) {
          $post->twbb_author = array(
            'name' => get_the_author_meta('display_name', $post->post_author),
            'link' => get_author_posts_url($post->post_author),
          );
        }
        if ( isset($additional_settings['categories']) ) {
          $post->twbb_categories = self::get_post_terms($post->ID, 'category');
        }
        if ( isset($additional_settings['tags']) ) {
          $post->twbb_tags = self::get_post_terms($post->ID, 'post_tag');
        }
      }
    }
  }

  protected static function get_post_terms($post_id, $tax){
    $terms = wp_get_post_terms($post_id, $tax);

    if(is_wp_error($terms)) {
      return array();
    }

    $options = array();

    foreach($terms as $term) {
      $options[$term->term_id] = array(
        'name' => $term->name,
        'link' => get_term_link($term, $tax)
      );
    }

    return $options;
  }

  protected function add_widget_controll($key){
    return true;
  }

  private static function get_query_args_hash($args){
    return md5(json_encode($args));// phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode
  }

}

\Elementor\Plugin::instance()->widgets_manager->register(new Posts());
