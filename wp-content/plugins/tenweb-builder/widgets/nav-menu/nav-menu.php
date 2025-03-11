<?php

namespace Tenweb_Builder;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;

if ( !defined('ABSPATH') ) {
  exit;
} // Exit if accessed directly
class Nav_Menu extends Widget_Base {

  protected $nav_menu_index = 1;
  protected static $inline_style_added = false;

  public function get_name() {
    return 'twbb-nav-menu';
  }

  public function get_title() {
    return __('Nav Menu', 'tenweb-builder');
  }

  public function get_icon() {
    return 'twbb-nav-menu twbb-widget-icon';
  }

  public function get_categories() {
    return [ 'tenweb-widgets' ];
  }

  public function get_script_depends() {
    return [ 'smartmenus' ];
  }

  protected function get_nav_menu_index() {
    return $this->nav_menu_index++;
  }

  private function get_available_menus() {
    $menus = wp_get_nav_menus();
    $options = [];
    foreach ( $menus as $menu ) {
      $options[$menu->slug] = $menu->name;
    }

    return $options;
  }

  protected function register_controls() {

    $this->start_controls_section('section_layout', [
      'label' => __('Layout', 'tenweb-builder'),
    ]);
    $menus = $this->get_available_menus();
    $domain_id = get_option( TENWEB_PREFIX . '_domain_id' );
    $db_navigation_link = TENWEB_DASHBOARD . '/websites/' . $domain_id . '/navigation?came_from_wp=1';
    if ( !empty($menus) ) {
      $this->add_control('menu', [
        'label' => __('Menu', 'tenweb-builder'),
        'type' => Controls_Manager::SELECT,
        'options' => $menus,
        'default' => array_keys($menus)[0],
        'save_default' => TRUE,
        'separator' => 'after',
        'description' => sprintf(__('Go to the <a href="%s" target="_blank">Menus screen</a> to manage your menus.', 'tenweb-builder'), esc_url($db_navigation_link)),
      ]);
    }
    else {
      $this->add_control('menu', [
        'type' => Controls_Manager::RAW_HTML,
        'raw' => sprintf(__('<strong>There are no menus in your site.</strong><br>Go to the <a href="%s" target="_blank">Menus screen</a> to create one.', 'tenweb-builder'), admin_url('nav-menus.php?action=edit&menu=0')),
        'separator' => 'after',
        'content_classes' => 'twbb-panel-alert twbb-panel-alert-info',
      ]);
    }
      $this->add_responsive_control('layout', [
          'label' => __('Layout', 'tenweb-builder'),
          'type' => Controls_Manager::SELECT,
          'desktop_default' => 'horizontal',
          'tablet_default' => 'horizontal',
          'mobile_default' => 'dropdown',

          'options' => [
              'horizontal' => __('Horizontal', 'tenweb-builder'),
              'vertical' => __('Vertical', 'tenweb-builder'),
              'dropdown' => __('Dropdown', 'tenweb-builder'),
          ],
          'frontend_available' => TRUE,
          'prefix_class' => 'twbb-responsive-nav-menu-%s-',
          'render_type' => 'template'
      ]);

      $menu_columns = range( 1, 10 );
	  $menu_columns = array_combine( $menu_columns, $menu_columns );

      $this->add_responsive_control(
          'menu-columns',
          [
              'type' => Controls_Manager::SELECT,
              'label' => __( 'Columns in menu', 'tenweb-builder' ),
              'options' => [ '' => __( 'Default', 'tenweb-builder' ) ] + $menu_columns,
              'desktop_default' => '1',
              'tablet_default' => '1',
              'mobile_default' => '1',
              'frontend_available' => true,
              'condition' => [
                          'layout' => 'vertical',

              ],
              'selectors' => [
	              '{{WRAPPER}} .twbb-nav-menu--main .twbb-menu-columns,
	              {{WRAPPER}} .twbb-nav-menu--main .twbb-menu-columns-tablet,
	              {{WRAPPER}} .twbb-nav-menu--main .twbb-menu-columns-mobile,
				  {{WRAPPER}} .twbb-nav-menu--dropdown .twbb-menu-columns' => 'display: grid; grid-template-columns: repeat({{VALUE}}, calc(100% / {{VALUE}}));',
              ],
          ]
      );

    $this->add_responsive_control('align_items', [
      'label' => __('Align', 'tenweb-builder'),
      'type' => Controls_Manager::CHOOSE,
      'label_block' => FALSE,
      'options' => [
        'left' => [
          'title' => __('Left', 'tenweb-builder'),
          'icon' => 'eicon-h-align-left',
        ],
        'center' => [
          'title' => __('Center', 'tenweb-builder'),
          'icon' => 'eicon-h-align-center',
        ],
        'right' => [
          'title' => __('Right', 'tenweb-builder'),
          'icon' => 'eicon-h-align-right',
        ],
        'justify' => [
          'title' => __('Stretch', 'tenweb-builder'),
          'icon' => 'eicon-h-align-stretch',
        ],
      ],
      'condition' => [
        'layout!' => 'dropdown',
      ],
      'prefix_class' => 'twbb-nav-menu__align%s-',
    ]);
    $this->add_control('pointer', [
      'label' => __('Pointer', 'tenweb-builder'),
      'type' => Controls_Manager::SELECT,
      'default' => 'underline',
      'options' => [
        'none' => __('None', 'tenweb-builder'),
        'underline' => __('Underline', 'tenweb-builder'),
        'overline' => __('Overline', 'tenweb-builder'),
        'double-line' => __('Double Line', 'tenweb-builder'),
        'framed' => __('Framed', 'tenweb-builder'),
        'background' => __('Background', 'tenweb-builder'),
        'text' => __('Text', 'tenweb-builder'),
      ],
      'condition' => [
        'layout!' => 'dropdown',
      ],
    ]);
    $this->add_control('animation_line', [
      'label' => __('Animation', 'tenweb-builder'),
      'type' => Controls_Manager::SELECT,
      'default' => 'fade',
      'options' => [
        'fade' => 'Fade',
        'slide' => 'Slide',
        'grow' => 'Grow',
        'drop-in' => 'Drop In',
        'drop-out' => 'Drop Out',
        'none' => 'None',
      ],
      'condition' => [
        'layout!' => 'dropdown',
        'pointer' => [ 'underline', 'overline', 'double-line' ],
      ],
    ]);
    $this->add_control('animation_framed', [
      'label' => __('Animation', 'tenweb-builder'),
      'type' => Controls_Manager::SELECT,
      'default' => 'fade',
      'options' => [
        'fade' => 'Fade',
        'grow' => 'Grow',
        'shrink' => 'Shrink',
        'draw' => 'Draw',
        'corners' => 'Corners',
        'none' => 'None',
      ],
      'condition' => [
        'layout!' => 'dropdown',
        'pointer' => 'framed',
      ],
    ]);
    $this->add_control('animation_background', [
      'label' => __('Animation', 'tenweb-builder'),
      'type' => Controls_Manager::SELECT,
      'default' => 'fade',
      'options' => [
        'fade' => 'Fade',
        'grow' => 'Grow',
        'shrink' => 'Shrink',
        'sweep-left' => 'Sweep Left',
        'sweep-right' => 'Sweep Right',
        'sweep-up' => 'Sweep Up',
        'sweep-down' => 'Sweep Down',
        'shutter-in-vertical' => 'Shutter In Vertical',
        'shutter-out-vertical' => 'Shutter Out Vertical',
        'shutter-in-horizontal' => 'Shutter In Horizontal',
        'shutter-out-horizontal' => 'Shutter Out Horizontal',
        'none' => 'None',
      ],
      'condition' => [
        'layout!' => 'dropdown',
        'pointer' => 'background',
      ],
    ]);
    $this->add_control('animation_text', [
      'label' => __('Animation', 'tenweb-builder'),
      'type' => Controls_Manager::SELECT,
      'default' => 'grow',
      'options' => [
        'grow' => 'Grow',
        'shrink' => 'Shrink',
        'sink' => 'Sink',
        'float' => 'Float',
        'skew' => 'Skew',
        'rotate' => 'Rotate',
        'none' => 'None',
      ],
      'condition' => [
        'layout!' => 'dropdown',
        'pointer' => 'text',
      ],
    ]);
    $this->add_control('indicator', [
      'label' => __('Submenu Indicator', 'tenweb-builder'),
      'type' => Controls_Manager::SELECT,
      'default' => 'classic',
      'options' => [
        'none' => __('None', 'tenweb-builder'),
        'classic' => __('Classic', 'tenweb-builder'),
        'chevron' => __('Chevron', 'tenweb-builder'),
        'angle' => __('Angle', 'tenweb-builder'),
        'plus' => __('Plus', 'tenweb-builder'),
      ],
      'prefix_class' => 'twbb-nav-menu--indicator-',
    ]);
    $this->add_control('heading_mobile_dropdown', [
      'label' => __('Mobile Dropdown', 'tenweb-builder'),
      'type' => Controls_Manager::HEADING,
      'separator' => 'before',
      'condition' => [
        'layout!' => 'dropdown',
      ],
    ]);
    $this->add_control('toggle', [
      'label' => __('Toggle Button', 'tenweb-builder'),
        'type' => Controls_Manager::SELECT,
      'default' => 'burger',
      'options' => [
          '' => esc_html__( 'None', 'elementor-pro' ),
          'burger' => esc_html__( 'Hamburger', 'elementor-pro' ),
      ],
      'prefix_class' => 'twbb-nav-menu--toggle twbb-nav-menu--',
      'render_type' => 'template',
      'frontend_available' => TRUE,
        'condition' => [
            'dropdown!' => 'none',
        ],
    ]);
    $this->add_control('full_width', [
      'label' => __('Full Width', 'tenweb-builder'),
      'type' => Controls_Manager::SWITCHER,
      'description' => __('Stretch the dropdown of the menu to full width.', 'tenweb-builder'),
      'prefix_class' => 'twbb-nav-menu--stretch',
	 		'default' => ' yes',
			'return_value' => ' yes',
      'frontend_available' => TRUE,
      'condition' => [
        'toggle' => 'burger',
      ],
    ]);
    $this->add_control('dropdown', [
      'label' => __('Breakpoint', 'tenweb-builder'),
      'type' => Controls_Manager::SELECT,
      'default' => 'tablet',
      'options' => [
        'mobile' => __('Mobile (767px >)', 'tenweb-builder'),
        'tablet' => __('Tablet (1023px >)', 'tenweb-builder'),
      ],
      'prefix_class' => 'twbb-nav-menu--dropdown-',
      'condition' => [
        'layout!' => 'dropdown',
        'toggle' => 'burger',
      ],
    ]);
    $this->add_control('toggle_align', [
      'label' => __('Toggle Align', 'tenweb-builder'),
      'type' => Controls_Manager::CHOOSE,
      'label_block' => FALSE,
      'default' => 'center',
      'options' => [
        'left' => [
          'title' => __('Left', 'tenweb-builder'),
          'icon' => 'eicon-h-align-left',
        ],
        'center' => [
          'title' => __('Center', 'tenweb-builder'),
          'icon' => 'eicon-h-align-center',
        ],
        'right' => [
          'title' => __('Right', 'tenweb-builder'),
          'icon' => 'eicon-h-align-right',
        ],
      ],
      'selectors_dictionary' => [
        'left' => 'margin-right: auto',
        'center' => 'margin: 0 auto',
        'right' => 'margin-left: auto',
      ],
      'selectors' => [
        '{{WRAPPER}} .twbb-menu-toggle' => '{{VALUE}}',
      ],
      'condition' => [
        'toggle' => 'burger',
      ],
    ]);
    $this->add_control('text_align', [
      'label' => __('Text Align', 'tenweb-builder'),
      'type' => Controls_Manager::CHOOSE,
      'default' => 'aside',
      'default' => 'center',
      'options' => [
        'left' => [
          'title' => __('Left', 'tenweb-builder'),
          'icon' => 'eicon-h-align-left',
        ],
        'center' => [
          'title' => __('Center', 'tenweb-builder'),
          'icon' => 'eicon-h-align-center',
        ],
        'right' => [
          'title' => __('Right', 'tenweb-builder'),
          'icon' => 'eicon-h-align-right',
        ],
      ],
      'selectors_dictionary' => [
        'left' => 'margin-right: auto',
        'center' => 'margin: 0 auto',
        'right' => 'margin-left: auto',
      ],
      'label_block' => FALSE,
      'prefix_class' => 'twbb-nav-menu__text-align-',
      'condition' => [
        'toggle' => 'burger',
      ],
    ]);
    $this->end_controls_section();
    $this->start_controls_section('section_style_main-menu', [
      'label' => __('Main Menu', 'tenweb-builder'),
      'tab' => Controls_Manager::TAB_STYLE,
      'condition' => [
        'layout!' => 'dropdown',
      ],
    ]);
    $this->add_group_control(Group_Control_Typography::get_type(), [
      'name' => 'menu_typography',
        'global' => [
            'default' => Global_Typography::TYPOGRAPHY_TEXT,
        ],
      'selector' => '{{WRAPPER}} .twbb-nav-menu__container, {{WRAPPER}} .twbb-nav-menu__container ul li a',
    ]);
    $this->start_controls_tabs('tabs_menu_item_style');
    $this->start_controls_tab('tab_menu_item_normal', [
      'label' => __('Normal', 'tenweb-builder'),
    ]);
    $this->add_control('color_menu_item', [
      'label' => __('Text Color', 'tenweb-builder'),
      'type' => Controls_Manager::COLOR,
        'global' => [
            'default' => Global_Colors::COLOR_TEXT,
        ],
      'default' => '',
      'selectors' => [
        '{{WRAPPER}} .twbb-nav-menu--main .twbb-item,
					 {{WRAPPER}} .twbb-nav-menu--dropdown .twbb-item' => 'color: {{VALUE}}',
      ],
    ]);

      $repeater = new Repeater();
      $repeater->add_control(
          'item_background_color',
          [
              'label' => __( 'Color', 'tenweb-builder' ),
              'type' => Controls_Manager::COLOR,
              'global' => [
                  'default' => Global_Colors::COLOR_TEXT,
              ],
              'default' => '#FFFFFF00',
          ]
      );
      $repeater->add_control(
          'heading',
          [
              'label' => __( 'Background Color Name', 'tenweb-builder' ),
              'show_label' => 'true',
              'type' => Controls_Manager::TEXT,
              'default' => __( 'Background', 'tenweb-builder' ),
              'label_block' => true,
          ]
      );

      // due to not workingitems_background_color description, this control was added only for label and description
      $this->add_control('items_background_description', [
        'label' => __('Item Background Color', 'tenweb-builder'),
        'description' => __('Background color sequence and color order in list will affect the menu items in loop. eg. if you have 3 colors and 5 items, item #4 will be color #1, etc.', 'tenweb-builder'),
        'type' => Controls_Manager::CHOOSE,
        'content_classes' => 'twbb-descriptor',
      ]);

      $this->add_control('items_background_color', [
          'label' => __('Item Background Color', 'tenweb-builder'),
          'type' => Controls_Manager::REPEATER,
          'show_label' => false,
          'fields' => $repeater->get_controls(),
          'default' => [
              [
                  'heading' => __( 'Background', 'tenweb-builder' ),
                  'item_background_color' => '#FFFFFF00',
              ],
          ],
          'title_field' => '{{{ heading }}}', //phpcs:ignore WordPressVIPMinimum.Security.Mustache.OutputNotation
      ]);

	  $this->end_controls_tab();
    $this->start_controls_tab('tab_menu_item_hover', [
      'label' => __('Hover', 'tenweb-builder'),
    ]);
    $this->add_control('color_menu_item_hover', [
      'label' => __('Text Color', 'tenweb-builder'),
      'type' => Controls_Manager::COLOR,
        'global' => [
            'default' => Global_Colors::COLOR_ACCENT,
        ],
      'selectors' => [
        '{{WRAPPER}} .twbb-nav-menu--main .twbb-item:hover,
					{{WRAPPER}} .twbb-nav-menu--main .twbb-item.twbb-item-active,
					{{WRAPPER}} .twbb-nav-menu--main .twbb-item.highlighted,
					{{WRAPPER}} .twbb-nav-menu--main .twbb-item:focus,
          {{WRAPPER}} .twbb-nav-menu--dropdown .twbb-item.twbb-item-active' => 'color: {{VALUE}}',
      ],
      'condition' => [
        'pointer!' => 'background',
      ],
    ]);
    $this->add_control('color_menu_item_hover_pointer_bg', [
      'label' => __('Text Color', 'tenweb-builder'),
      'type' => Controls_Manager::COLOR,
      'default' => '#fff',
      'selectors' => [
        '{{WRAPPER}} .twbb-nav-menu--main .twbb-item:hover,
					{{WRAPPER}} .twbb-nav-menu--main .twbb-item.twbb-item-active,
					{{WRAPPER}} .twbb-nav-menu--main .twbb-item.highlighted,
					{{WRAPPER}} .twbb-nav-menu--main .twbb-item:focus,
					{{WRAPPER}} .twbb-nav-menu--dropdown .twbb-item.twbb-item-active' => 'color: {{VALUE}}',
      ],
      'condition' => [
        'pointer' => 'background',
      ],
    ]);

    $this->add_control('pointer_color_menu_item_hover', [
      'label' => __('Pointer Color', 'tenweb-builder'),
      'type' => Controls_Manager::COLOR,
        'global' => [
            'default' => Global_Colors::COLOR_ACCENT,
        ],
      'default' => '',
      'selectors' => [
        '{{WRAPPER}} .twbb-nav-menu--main:not(.e--pointer-framed) .twbb-item:before,
					{{WRAPPER}} .twbb-nav-menu--main:not(.e--pointer-framed) .twbb-item:after' => 'background-color: {{VALUE}}',
        '{{WRAPPER}} .e--pointer-framed .twbb-item:before,
					{{WRAPPER}} .e--pointer-framed .twbb-item:after' => 'border-color: {{VALUE}}',
      ],
      'condition' => [
        'pointer!' => [ 'none', 'text' ],
      ],
    ]);

    $this->end_controls_tab();
    $this->start_controls_tab('tab_menu_item_active', [
      'label' => __('Active', 'tenweb-builder'),
    ]);
    $this->add_control('color_menu_item_active', [
      'label' => __('Text Color', 'tenweb-builder'),
      'type' => Controls_Manager::COLOR,
      'default' => '',
      'selectors' => [
        '{{WRAPPER}} .twbb-nav-menu--main .twbb-item.twbb-item-active,
					{{WRAPPER}} .twbb-nav-menu--dropdown .twbb-item.twbb-item-active' => 'color: {{VALUE}}',
      ],
    ]);

    $this->add_control('pointer_color_menu_item_active', [
      'label' => __('Pointer Color', 'tenweb-builder'),
      'type' => Controls_Manager::COLOR,
      'default' => '',
      'selectors' => [
        '{{WRAPPER}} .twbb-nav-menu--main:not(.e--pointer-framed) .twbb-item.twbb-item-active:before,
					{{WRAPPER}} .twbb-nav-menu--main:not(.e--pointer-framed) .twbb-item.twbb-item-active:after' => 'background-color: {{VALUE}}',
        '{{WRAPPER}} .e--pointer-framed .twbb-item.twbb-item-active:before,
					{{WRAPPER}} .e--pointer-framed .twbb-item.twbb-item-active:after' => 'border-color: {{VALUE}}',
      ],
      'condition' => [
        'pointer!' => [ 'none', 'text' ],
      ],
    ]);

    $this->end_controls_tab();
    $this->end_controls_tabs();
    /* This control is required to handle with complicated conditions */
    $this->add_control('hr', [
      'type' => Controls_Manager::DIVIDER,
      'style' => 'thick',
    ]);
    $this->add_control('pointer_width', [
      'label' => __('Pointer Width', 'tenweb-builder'),
      'type' => Controls_Manager::SLIDER,
      'devices' => [ self::RESPONSIVE_DESKTOP, self::RESPONSIVE_TABLET ],
      'range' => [
        'px' => [
          'max' => 30,
        ],
      ],
      'selectors' => [
        '{{WRAPPER}} .e--pointer-framed .twbb-item:before' => 'border-width: {{SIZE}}{{UNIT}}',
        '{{WRAPPER}} .e--pointer-framed.e--animation-draw .twbb-item:before' => 'border-width: 0 0 {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}}',
        '{{WRAPPER}} .e--pointer-framed.e--animation-draw .twbb-item:after' => 'border-width: {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}} 0 0',
        '{{WRAPPER}} .e--pointer-framed.e--animation-corners .twbb-item:before' => 'border-width: {{SIZE}}{{UNIT}} 0 0 {{SIZE}}{{UNIT}}',
        '{{WRAPPER}} .e--pointer-framed.e--animation-corners .twbb-item:after' => 'border-width: 0 {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}} 0',
        '{{WRAPPER}} .e--pointer-underline .twbb-item:after,
					 {{WRAPPER}} .e--pointer-overline .twbb-item:before,
					 {{WRAPPER}} .e--pointer-double-line .twbb-item:before,
					 {{WRAPPER}} .e--pointer-double-line .twbb-item:after' => 'height: {{SIZE}}{{UNIT}}',
      ],
      'condition' => [
        'pointer' => [ 'underline', 'overline', 'double-line', 'framed' ],
      ],
    ]);
    $this->add_responsive_control('padding_horizontal_menu_item', [
      'label' => __('Horizontal Padding', 'tenweb-builder'),
      'type' => Controls_Manager::SLIDER,
      'range' => [
        'px' => [
          'max' => 50,
        ],
      ],
      'devices' => [ 'desktop', 'tablet' ],
      'selectors' => [
        '{{WRAPPER}} .twbb-nav-menu--main .twbb-item' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}}',
      ],
    ]);
    $this->add_responsive_control('padding_vertical_menu_item', [
      'label' => __('Vertical Padding', 'tenweb-builder'),
      'type' => Controls_Manager::SLIDER,
      'range' => [
        'px' => [
          'max' => 50,
        ],
      ],
      'devices' => [ 'desktop', 'tablet' ],
      'selectors' => [
        '{{WRAPPER}} .twbb-nav-menu--main .twbb-item' => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}}',
      ],
    ]);
    $this->add_responsive_control('menu_space_between', [
      'label' => __('Space Between', 'tenweb-builder'),
      'type' => Controls_Manager::SLIDER,
      'range' => [
        'px' => [
          'max' => 100,
        ],
      ],
      'selectors' => [
        'body:not(.rtl) {{WRAPPER}} .twbb-nav-menu--layout-horizontal .twbb-nav-menu > li:not(:last-child)' => 'margin-right: {{SIZE}}{{UNIT}}',
        'body.rtl {{WRAPPER}} .twbb-nav-menu--layout-horizontal .twbb-nav-menu > li:not(:last-child)' => 'margin-left: {{SIZE}}{{UNIT}}',
        '{{WRAPPER}} .twbb-nav-menu--main:not(.twbb-nav-menu--layout-horizontal) .twbb-nav-menu > li:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}}',
        '{{WRAPPER}}.twbb-nav-menu--toggle .twbb-nav-menu--dropdown .twbb-nav-menu > li:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}}',
      ],
    ]);
    $this->add_responsive_control('border_radius_menu_item', [
      'label' => __('Border Radius', 'tenweb-builder'),
      'type' => Controls_Manager::SLIDER,
      'size_units' => [ 'px', 'em', '%' ],
      'devices' => [ 'desktop', 'tablet' ],
      'selectors' => [
        '{{WRAPPER}} .twbb-item:before, {{WRAPPER}} .twbb-item' => 'border-radius: {{SIZE}}{{UNIT}}',
        '{{WRAPPER}} .e--animation-shutter-in-horizontal .twbb-item:before, {{WRAPPER}} .e--animation-shutter-in-horizontal .twbb-item' => 'border-radius: {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}} 0 0',
        '{{WRAPPER}} .e--animation-shutter-in-horizontal .twbb-item:after, {{WRAPPER}} .e--animation-shutter-in-horizontal .twbb-item' => 'border-radius: 0 0 {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}}',
        '{{WRAPPER}} .e--animation-shutter-in-vertical .twbb-item:before, {{WRAPPER}} .e--animation-shutter-in-vertical .twbb-item' => 'border-radius: 0 {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}} 0',
        '{{WRAPPER}} .e--animation-shutter-in-vertical .twbb-item:after, {{WRAPPER}} .e--animation-shutter-in-vertical .twbb-item' => 'border-radius: {{SIZE}}{{UNIT}} 0 0 {{SIZE}}{{UNIT}}',
      ],
      'condition' => [
        'pointer' => 'background',
      ],
    ]);
    $this->end_controls_section();
    $this->start_controls_section('section_style_dropdown', [
      'label' => __('Dropdown', 'tenweb-builder'),
      'tab' => Controls_Manager::TAB_STYLE,
    ]);
    $this->add_control('dropdown_description', [
      'raw' => __('On desktop, this will affect the submenu. On mobile, this will affect the entire menu.', 'tenweb-builder'),
      'type' => Controls_Manager::RAW_HTML,
      'content_classes' => 'twbb-descriptor',
    ]);
    $this->start_controls_tabs('tabs_dropdown_item_style');
    $this->start_controls_tab('tab_dropdown_item_normal', [
      'label' => __('Normal', 'tenweb-builder'),
    ]);
    $this->add_control('color_dropdown_item', [
      'label' => __('Text Color', 'tenweb-builder'),
      'type' => Controls_Manager::COLOR,
      'default' => '',
      'selectors' => [
	      '{{WRAPPER}} .twbb-nav-menu--dropdown a.twbb-item, {{WRAPPER}} .sub-menu .twbb-sub-item' => 'color: {{VALUE}}',
      ],
    ]);
    $this->add_control('background_color_dropdown_item', [
      'label' => __('Background Color', 'tenweb-builder'),
      'type' => Controls_Manager::COLOR,
      'selectors' => [
        '{{WRAPPER}} .twbb-nav-menu--dropdown' => 'background-color: {{VALUE}}',
      ],
      'separator' => 'none',
    ]);
    $this->end_controls_tab();
    $this->start_controls_tab('tab_dropdown_item_hover', [
      'label' => __('Hover', 'tenweb-builder'),
    ]);
    $this->add_control('color_dropdown_item_hover', [
      'label' => __('Text Color', 'tenweb-builder'),
      'type' => Controls_Manager::COLOR,
      'default' => '',
      'selectors' => [
        '{{WRAPPER}} .twbb-nav-menu--dropdown a:hover,{{WRAPPER}} .twbb-nav-menu--dropdown a.highlighted,
				{{WRAPPER}} .twbb-nav-menu--dropdown .twbb-item.twbb-item-active' => 'color: {{VALUE}}',
      ],
    ]);
    $this->add_control('background_color_dropdown_item_hover', [
      'label' => __('Background Color', 'tenweb-builder'),
      'type' => Controls_Manager::COLOR,
      'default' => '',
      'selectors' => [
        '{{WRAPPER}} .twbb-nav-menu--dropdown a:hover,
				{{WRAPPER}} .twbb-nav-menu--dropdown .twbb-item.twbb-item-active,
				{{WRAPPER}} .twbb-nav-menu--dropdown a.highlighted' => 'background-color: {{VALUE}}',
      ],
      'separator' => 'none',
    ]);
    $this->end_controls_tab();
    $this->start_controls_tab('tab_dropdown_item_active', [
      'label' => __('Active', 'tenweb-builder'),
    ]);
    $this->add_control('color_dropdown_item_active', [
      'label' => __('Text Color', 'tenweb-builder'),
      'type' => Controls_Manager::COLOR,
      'default' => '',
      'selectors' => [
        '{{WRAPPER}} .twbb-nav-menu--dropdown .twbb-item.twbb-item-active' => 'color: {{VALUE}}',
      ],
    ]);
    $this->add_control('background_color_dropdown_item_active', [
      'label' => __('Background Color', 'tenweb-builder'),
      'type' => Controls_Manager::COLOR,
      'default' => '',
      'selectors' => [
        '{{WRAPPER}} .twbb-nav-menu--dropdown .twbb-item.twbb-item-active' => 'background-color: {{VALUE}}',
      ],
      'separator' => 'none',
    ]);
    $this->end_controls_tab();
    $this->end_controls_tabs();
    $this->add_group_control(Group_Control_Typography::get_type(), [
      'name' => 'dropdown_typography',
        'global' => [
            'default' => Global_Typography::TYPOGRAPHY_ACCENT,
        ],
      'exclude' => [ 'line_height' ], //phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude
      'selector' => '{{WRAPPER}} ul.twbb-nav-menu--dropdown li a',
      'separator' => 'before',
    ]);
    $this->add_group_control(Group_Control_Border::get_type(), [
      'name' => 'dropdown_border',
      'selector' => '{{WRAPPER}} .twbb-nav-menu--dropdown',
      'separator' => 'before',
    ]);
    $this->add_responsive_control('dropdown_border_radius', [
      'label' => __('Border Radius', 'tenweb-builder'),
      'type' => Controls_Manager::DIMENSIONS,
      'size_units' => [ 'px', '%' ],
      'selectors' => [
        '{{WRAPPER}} .twbb-nav-menu--dropdown' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        '{{WRAPPER}} .twbb-nav-menu--dropdown li:first-child a' => 'border-top-left-radius: {{TOP}}{{UNIT}}; border-top-right-radius: {{RIGHT}}{{UNIT}};',
        '{{WRAPPER}} .twbb-nav-menu--dropdown li:last-child a' => 'border-bottom-right-radius: {{BOTTOM}}{{UNIT}}; border-bottom-left-radius: {{LEFT}}{{UNIT}};',
      ],
    ]);
    $this->add_group_control(Group_Control_Box_Shadow::get_type(), [
      'name' => 'dropdown_box_shadow',
      'exclude' => [ //phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude
        'box_shadow_position',
      ],
      'selector' => '{{WRAPPER}} .twbb-nav-menu--main .twbb-nav-menu--dropdown, {{WRAPPER}} .twbb-nav-menu__container.twbb-nav-menu--dropdown',
    ]);
    $this->add_responsive_control('box_padding', [
      'label' => __('Padding', 'tenweb-builder'),
      'type' => Controls_Manager::DIMENSIONS,
      'size_units' => [ 'px', '%', 'em' ],
      'tablet_default' => [
        'size' => 0,
        'unit' => 'px',
      ],
      'mobile_default' => [
        'size' => 0,
        'unit' => 'px',
      ],
      'selectors' => [
        '{{WRAPPER}} .twbb-nav-menu--dropdown a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
      ],
    ]);
    $this->add_control('heading_dropdown_divider', [
      'label' => __('Divider', 'tenweb-builder'),
      'type' => Controls_Manager::HEADING,
      'separator' => 'before',
    ]);
    $this->add_group_control(Group_Control_Border::get_type(), [
      'name' => 'dropdown_divider',
      'selector' => '{{WRAPPER}} .twbb-nav-menu--dropdown li:not(:last-child)',
      'exclude' => [ 'width' ], //phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude
    ]);
    $this->add_control('dropdown_divider_width', [
      'label' => __('Divider Width', 'tenweb-builder'),
      'type' => Controls_Manager::SLIDER,
      'range' => [
        'px' => [
          'max' => 50,
        ],
      ],
      'selectors' => [
        '{{WRAPPER}} .twbb-nav-menu--dropdown li:not(:last-child)' => 'border-bottom-width: {{SIZE}}{{UNIT}}',
      ],
      'condition' => [
        'dropdown_divider_border!' => '',
      ],
    ]);
    $this->add_responsive_control('dropdown_top_distance', [
      'label' => __('Distance', 'tenweb-builder'),
      'type' => Controls_Manager::SLIDER,
      'range' => [
        'px' => [
          'min' => -100,
          'max' => 100,
        ],
      ],
      'selectors' => [
        '{{WRAPPER}} .twbb-nav-menu--main > .twbb-nav-menu > li > .twbb-nav-menu--dropdown, {{WRAPPER}} .twbb-nav-menu__container.twbb-nav-menu--dropdown' => 'margin-top: {{SIZE}}{{UNIT}} !important',
      ],
      'separator' => 'before',
    ]);
    $this->end_controls_section();
    $this->start_controls_section('style_toggle', [
      'label' => __('Toggle Button', 'tenweb-builder'),
      'tab' => Controls_Manager::TAB_STYLE,
      'condition' => [
        'toggle!' => '',
      ],
    ]);
    $this->start_controls_tabs('tabs_toggle_style');
    $this->start_controls_tab('tab_toggle_style_normal', [
      'label' => __('Normal', 'tenweb-builder'),
    ]);
    $this->add_control('toggle_color', [
      'label' => __('Color', 'tenweb-builder'),
      'type' => Controls_Manager::COLOR,
      'selectors' => [
        '{{WRAPPER}} div.twbb-menu-toggle' => 'color: {{VALUE}}',
        '{{WRAPPER}} div.twbb-menu-toggle .e-font-icon-svg' => 'fill: {{VALUE}}',
        // Harder selector to override text color control
      ],
    ]);
    $this->add_control('toggle_background_color', [
      'label' => __('Background Color', 'tenweb-builder'),
      'type' => Controls_Manager::COLOR,
      'selectors' => [
        '{{WRAPPER}} .twbb-menu-toggle' => 'background-color: {{VALUE}}',
      ],
    ]);
    $this->end_controls_tab();
    $this->start_controls_tab('tab_toggle_style_hover', [
      'label' => __('Hover', 'tenweb-builder'),
    ]);
    $this->add_control('toggle_color_hover', [
      'label' => __('Color', 'tenweb-builder'),
      'type' => Controls_Manager::COLOR,
      'selectors' => [
        '{{WRAPPER}} div.twbb-menu-toggle:hover' => 'color: {{VALUE}}',
        // Harder selector to override text color control
      ],
    ]);
    $this->add_control('toggle_background_color_hover', [
      'label' => __('Background Color', 'tenweb-builder'),
      'type' => Controls_Manager::COLOR,
      'selectors' => [
        '{{WRAPPER}} .twbb-menu-toggle:hover' => 'background-color: {{VALUE}}',
      ],
    ]);
    $this->end_controls_tab();
    $this->end_controls_tabs();
    $this->add_control('toggle_size', [
      'label' => __('Size', 'tenweb-builder'),
      'type' => Controls_Manager::SLIDER,
      'range' => [
        'px' => [
          'min' => 15,
        ],
      ],
      'selectors' => [
        '{{WRAPPER}} .twbb-menu-toggle' => 'font-size: {{SIZE}}{{UNIT}}',
      ],
      'separator' => 'before',
    ]);
    $this->add_control('toggle_border_width', [
      'label' => __('Border Width', 'tenweb-builder'),
      'type' => Controls_Manager::SLIDER,
      'range' => [
        'px' => [
          'max' => 10,
        ],
      ],
      'selectors' => [
        '{{WRAPPER}} .twbb-menu-toggle' => 'border-width: {{SIZE}}{{UNIT}}',
      ],
    ]);
    $this->add_control('toggle_border_radius', [
      'label' => __('Border Radius', 'tenweb-builder'),
      'type' => Controls_Manager::SLIDER,
      'size_units' => [ 'px', '%' ],
      'selectors' => [
        '{{WRAPPER}} .twbb-menu-toggle' => 'border-radius: {{SIZE}}{{UNIT}}',
      ],
    ]);
    $this->end_controls_section();
  }

  protected function render() {
    $settings = $this->get_active_settings();
    $wp_menu = isset($settings['menu']) ? wp_get_nav_menu_object( $settings['menu'] ) : '';
    $menu_term_id = 0;
    if ( is_array( $wp_menu ) || is_object($wp_menu) ) {
      $menu_term_id = $wp_menu->term_id;
    }
    $menu_ids = [
          'menu_id' => 'menu-' . $this->get_nav_menu_index() . '-' . $this->get_id(),
          'dropdown_id' => 'menu-' . $this->get_nav_menu_index() . '-' . $this->get_id()
    ];
    $args = [
      'echo' => FALSE,
      'menu' => isset($settings['menu']) ? $settings['menu'] : '',
      'menu_class' => 'twbb-nav-menu' . ' ' . 'twbb-menu_term_id-' . $menu_term_id,
      'menu_id' => $menu_ids['menu_id'],
      'fallback_cb' => '__return_empty_string',
      'container' => '',
    ];
    if ( 'vertical' === $settings['layout'] ) {
      $args['menu_class'] .= ' sm-vertical twbb-menu-columns';
    }
    if ( 'vertical' === $settings['layout_tablet'] ) {
      $args['menu_class'] .= ' sm-vertical-tablet twbb-menu-columns-tablet';
    }
    if ( 'vertical' === $settings['layout_mobile'] ) {
      $args['menu_class'] .= ' sm-vertical-mobile twbb-menu-columns-mobile';
    }
    // Add custom filter to handle Nav Menu HTML output.
    add_filter('nav_menu_link_attributes', [ $this, 'handle_link_classes' ], 10, 4);
    add_filter('nav_menu_submenu_css_class', [ $this, 'handle_sub_menu_classes' ]);
    add_filter('nav_menu_item_id', '__return_empty_string');
    // General Menu.
    $menu_html = wp_nav_menu($args);
    // Dropdown Menu.
    $args['menu_id'] = $menu_ids['dropdown_id'];
    $dropdown_menu_html = wp_nav_menu($args);
    // Remove all our custom filters.
    remove_filter('nav_menu_link_attributes', [ $this, 'handle_link_classes' ]);
    remove_filter('nav_menu_submenu_css_class', [ $this, 'handle_sub_menu_classes' ]);
    remove_filter('nav_menu_item_id', '__return_empty_string');
    if ( empty($menu_html) ) {
      return;
    }

    if ( 'dropdown' !== $settings['layout'] ) :
      $this->add_render_attribute('main-menu', 'class', [
        'twbb-nav-menu--main',
        'twbb-nav-menu__container',
        'twbb-nav-menu--layout-' . $settings['layout'],
      ]);
      if ( $settings['pointer'] ) :
        $this->add_render_attribute('main-menu', 'class', 'e--pointer-' . $settings['pointer']);
        foreach ( $settings as $key => $value ) :
          if ( 0 === strpos($key, 'animation') && $value ) :
            $this->add_render_attribute('main-menu', 'class', 'e--animation-' . $value);
            break;
          endif;
        endforeach;
      endif; ?>
        <nav <?php $this->print_render_attribute_string( 'main-menu' ); ?>>
            <?php
            // PHPCS - escaped by WordPress with "wp_nav_menu"
            echo $menu_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            ?>
        </nav>
    <?php
    endif;
    $this->render_menu_toggle( $settings );
    ?>
    <nav class="twbb-nav-menu--dropdown twbb-nav-menu__container">
        <?php
        // PHPCS - escaped by WordPress with "wp_nav_menu"
        echo $dropdown_menu_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        ?>
    </nav>
    <?php
      //10web customization
      $style = '';

      if(self::$inline_style_added === false){
          self::$inline_style_added = true;
          // to keep submenu hidden while css files are loading to avoid Cumulative Layout Shift.
          $style .= '.twbb-nav-menu ul {display: none;}';
      }

      $wp_menu_items = [];
      if ( is_array( $wp_menu ) ) {
        $wp_menu_items = wp_get_nav_menu_items( $menu_term_id );
      }
      $count = 0;
      foreach ( $wp_menu_items as $item ) {
        if ( $item->menu_item_parent === 0 ) {
          $count ++;
        }
      }
      for ( $menu_count = 1, $j = 0; $menu_count <= $count; $j++, $menu_count++ ) {
	      if ( $settings['items_background_color'] !== null && $j === count( $settings['items_background_color'] ) ) {
		      $j = 0;
	      }
	      if ( $settings['items_background_color'] !== null ) {
		      $style .= '.twbb-nav-menu__container ' . '#' . $menu_ids['menu_id'] . '.twbb-nav-menu > .menu-item:nth-child(' . $menu_count . ') > a {
                    background-color: ' . $settings['items_background_color'][ $j ]['item_background_color'] . ';z-index: 1;}
                    .twbb-nav-menu__container ' . '#' . $menu_ids['dropdown_id'] . '.twbb-nav-menu > .menu-item:nth-child(' . $menu_count . ') > a {
                    background-color: ' . $settings['items_background_color'][ $j ]['item_background_color'] . ';z-index: 1;}';

	      }
      }
      ?>
      <style>
          <?php echo $style; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped?>
      </style>
    <?php
	  //end 10web customization

    if(defined('ELEMENTOR_PATH')) {
      include_once ELEMENTOR_PATH . 'includes/managers/icons.php';
      if(class_exists('\Elementor\Icons_Manager')) {
        \Elementor\Icons_Manager::enqueue_shim();
      }
    }
  }
    private function render_menu_toggle( $settings ) {
        if ( ! isset( $settings['toggle'] ) || 'burger' !== $settings['toggle'] ) {
$v = 'burger' !== $settings['toggle'];
            return;
        }

        $this->add_render_attribute( 'menu-toggle', [
            'class' => 'twbb-menu-toggle',
            'role' => 'button',
            'tabindex' => '0',
            'aria-label' => esc_html__( 'Menu Toggle', 'tenweb-builder' ),
            'aria-expanded' => 'false',
        ] );

        if ( \Elementor\Plugin::instance()->editor->is_edit_mode() ) {
            $this->add_render_attribute( 'menu-toggle', [
                'class' => 'twbb-clickable',
            ] );
        }

        ?>
        <div <?php $this->print_render_attribute_string( 'menu-toggle' ); ?>>
            <?php

            $open_class = 'twbb-menu-toggle__icon--open';
            $close_class = 'twbb-menu-toggle__icon--close';

            $normal_icon = ! empty( $settings['toggle_icon_normal']['value'] )
                ? $settings['toggle_icon_normal']
                : [
                    'library' => 'eicons',
                    'value' => 'eicon-menu-bar',
                ];

            $is_normal_icon_svg = 'svg' === $normal_icon['library'];

            if ( $is_normal_icon_svg ) {
                echo '<span class="' . esc_attr( $open_class ) . '">';
            }

            Icons_Manager::render_icon(
                $normal_icon,
                [
                    'aria-hidden' => 'true',
                    'role' => 'presentation',
                    'class' => $open_class,
                ]
            );

            if ( $is_normal_icon_svg ) {
                echo '</span>';
            }

            $active_icon = ! empty( $settings['toggle_icon_active']['value'] )
                ? $settings['toggle_icon_active']
                : [
                    'library' => 'eicons',
                    'value' => 'eicon-close',
                ];

            $is_active_icon_svg = 'svg' === $active_icon['library'];

            if ( $is_active_icon_svg ) {
                echo '<span class="' . esc_attr( $close_class ) . '">';
            }

            Icons_Manager::render_icon(
                $active_icon,
                [
                    'aria-hidden' => 'true',
                    'role' => 'presentation',
                    'class' => $close_class,
                ]
            );

            if ( $is_active_icon_svg ) {
                echo '</span>';
            }
            ?>
            <span class="elementor-screen-only"><?php echo esc_html__( 'Menu', 'tenweb-builder' ); ?></span>
        </div>
        <?php
    }

  public function handle_link_classes( $atts, $item, $args, $depth ) {
    $classes = $depth ? 'twbb-sub-item' : 'twbb-item';
    $is_anchor = false !== strpos( $atts['href'], '#' );

    if ( ! $is_anchor && in_array('current-menu-item', $item->classes, true) ) {
      $classes .= '  twbb-item-active';
    }
    if ( empty($atts['class']) ) {
      $atts['class'] = $classes;
    }
    else {
      $atts['class'] .= ' ' . $classes;
    }

    return $atts;
  }

  public function handle_sub_menu_classes( $classes ) {
    $classes[] = 'twbb-nav-menu--dropdown';

    return $classes;
  }

  public function render_plain_content() {
  }
}

\Elementor\Plugin::instance()->widgets_manager->register(new Nav_Menu());
