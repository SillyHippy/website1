<?php
namespace Tenweb_Builder;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Share_Buttons extends Widget_Base {

  private static $networks_class_dictionary = [
    'pocket' => 'fab fa-get-pocket',
    'email' => 'fa fa-envelope',
    'print' => 'fa fa-print',
  ];

  private static function get_network_class( $network_name ) {
    if ( isset( self::$networks_class_dictionary[ $network_name ] ) ) {
      return self::$networks_class_dictionary[ $network_name ];
    }

    return 'fab fa-' . $network_name;
  }

  private static $networks = [
    'facebook' => 'Facebook',
    'twitter' => 'Twitter',
    'linkedin' => 'LinkedIn',
    'pinterest' => 'Pinterest',
    'reddit' => 'Reddit',
    'vk' => 'VK',
    'odnoklassniki' => 'OK',
    'tumblr' => 'Tumblr',
    'delicious' => 'Delicious',
    'digg' => 'Digg',
    'skype' => 'Skype',
    'telegram' => 'Telegram',
    'pocket' => 'Pocket',
    'xing' => 'XING',
    'whatsapp' => 'WhatsApp',
    'email' => 'Email',
    'print' => 'Print',
  ];

  public static function get_networks( $network_name = null ) {
    if ( $network_name ) {
      return isset( self::$networks[ $network_name ] ) ? self::$networks[ $network_name ] : null;
    }

    return self::$networks;
  }

  public function get_name() {
    return Builder::$prefix . 'share-buttons';
  }

  public function get_title() {
    return __( 'Share Buttons', 'tenweb-builder' );
  }

  public function get_icon() {
    return 'twbb-share-button twbb-widget-icon';
  }

  public function get_categories(){
    return ['tenweb-widgets'];
  }

  protected function register_controls() {
    $this->start_controls_section(
      'section_buttons_content',
      [
        'label' => __( 'Share Buttons', 'tenweb-builder' ),
      ]
    );

    $repeater = new Repeater();

    $networks = $this->get_networks();

    $networks_names = array_keys( $networks );

    $repeater->add_control(
      'button',
      [
        'label' => __( 'Network', 'tenweb-builder' ),
        'type' => Controls_Manager::SELECT,
        'options' => array_reduce( $networks_names, function( $options, $network_name ) use ( $networks ) {
          $options[ $network_name ] = $networks[ $network_name ];

          return $options;
        }, [] ),
        'default' => 'facebook',
      ]
    );

    $repeater->add_control(
      'text',
      [
        'label' => __( 'Custom Label', 'tenweb-builder' ),
        'type' => Controls_Manager::TEXT,
      ]
    );

    $this->add_control(
      'share_buttons',
      [
        'type' => Controls_Manager::REPEATER,
        'fields' => $repeater->get_controls(),
        'default' => [
          [
            'button' => 'facebook',
          ],
          [
            'button' => 'twitter',
          ],
          [
            'button' => 'linkedin',
          ],
        ],
        'title_field' => '<i class="{{ tenwebShareButtons.getNetworkClass( button ) }}"></i> {{{ tenwebShareButtons.getNetworkTitle( obj ) }}}', //phpcs:ignore WordPressVIPMinimum.Security.Mustache.OutputNotation
      ]
    );

    $this->add_control(
      'view',
      [
        'label' => __( 'View', 'tenweb-builder' ),
        'type' => Controls_Manager::SELECT,
        'label_block' => false,
        'options' => [
          'icon-text' => __( 'Icon & Text', 'tenweb-builder' ),
          'icon' => __( 'Icon', 'tenweb-builder' ),
          'text' => __( 'Text', 'tenweb-builder' ),
        ],
        'default' => 'icon-text',
        'separator' => 'before',
        'prefix_class' => 'elementor-share-buttons--view-',
        'render_type' => 'template',
      ]
    );

    $this->add_control(
      'skin',
      [
        'label' => __( 'Skin', 'tenweb-builder' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
          'gradient' => __( 'Gradient', 'tenweb-builder' ),
          'minimal' => __( 'Minimal', 'tenweb-builder' ),
          'framed' => __( 'Framed', 'tenweb-builder' ),
          'boxed' => __( 'Boxed Icon', 'tenweb-builder' ),
          'flat' => __( 'Flat', 'tenweb-builder' ),
        ],
        'default' => 'gradient',
        'prefix_class' => 'elementor-share-buttons--skin-',
      ]
    );

    $this->add_control(
      'shape',
      [
        'label' => __( 'Shape', 'tenweb-builder' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
          'square' => __( 'Square', 'tenweb-builder' ),
          'rounded' => __( 'Rounded', 'tenweb-builder' ),
          'circle' => __( 'Circle', 'tenweb-builder' ),
        ],
        'default' => 'square',
        'prefix_class' => 'elementor-share-buttons--shape-',
      ]
    );

    $this->add_responsive_control(
      'columns',
      [
        'label' => __( 'Columns', 'tenweb-builder' ),
        'type' => Controls_Manager::SELECT,
        'default' => '0',
        'options' => [
          '0' => 'Auto',
          '1' => '1',
          '2' => '2',
          '3' => '3',
          '4' => '4',
          '5' => '5',
          '6' => '6',
        ],
        'prefix_class' => 'elementor-grid%s-',
      ]
    );

    $this->add_control(
      'alignment',
      [
        'label' => __( 'Alignment', 'tenweb-builder' ),
        'type' => Controls_Manager::CHOOSE,
        'options' => [
          'left' => [
            'title' => __( 'Left', 'tenweb-builder' ),
            'icon' => 'fa fa-align-left',
          ],
          'center' => [
            'title' => __( 'Center', 'tenweb-builder' ),
            'icon' => 'fa fa-align-center',
          ],
          'right' => [
            'title' => __( 'Right', 'tenweb-builder' ),
            'icon' => 'fa fa-align-right',
          ],
          'justify' => [
            'title' => __( 'Justify', 'tenweb-builder' ),
            'icon' => 'fa fa-align-justify',
          ],
        ],
        'prefix_class' => 'elementor-share-buttons--align-',
        'condition' => [
          'columns' => '0',
        ],
      ]
    );

    $this->add_control(
      'share_url_type',
      [
        'label' => __( 'Target URL', 'tenweb-builder' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
          'current_page' => __( 'Current Page', 'tenweb-builder' ),
          'custom' => __( 'Custom', 'tenweb-builder' ),
        ],
        'default' => 'current_page',
        'separator' => 'before',
      ]
    );

    $this->add_control(
      'share_url',
      [
        'label' => __( 'URL', 'tenweb-builder' ),
        'type' => Controls_Manager::URL,
        'show_external' => false,
        'placeholder' => __( 'https://your-link.com', 'tenweb-builder' ),
        'condition' => [
          'share_url_type' => 'custom',
        ],
        'show_label' => false,
        'frontend_available' => true,
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section(
      'section_buttons_style',
      [
        'label' => __( 'Share Buttons', 'tenweb-builder' ),
        'tab' => Controls_Manager::TAB_STYLE,
      ]
    );

    $this->add_responsive_control(
      'column_gap',
      [
        'label'     => __( 'Columns Gap', 'tenweb-builder' ),
        'type' => Controls_Manager::SLIDER,
        'default' => [
          'size' => 10,
        ],
        'selectors' => [
          '{{WRAPPER}}:not(.elementor-grid-0) .elementor-grid' => 'grid-column-gap: {{SIZE}}{{UNIT}}',
          '{{WRAPPER}}.elementor-grid-0 .elementor-share-btn' => 'margin-right: calc({{SIZE}}{{UNIT}} / 2); margin-left: calc({{SIZE}}{{UNIT}} / 2);',
          '{{WRAPPER}}.elementor-grid-0 .elementor-grid' => 'margin-right: calc(-{{SIZE}}{{UNIT}} / 2); margin-left: calc(-{{SIZE}}{{UNIT}} / 2);',
        ],
      ]
    );

    $this->add_responsive_control(
      'row_gap',
      [
        'label'     => __( 'Rows Gap', 'tenweb-builder' ),
        'type' => Controls_Manager::SLIDER,
        'default' => [
          'size' => 10,
        ],
        'selectors' => [
          '{{WRAPPER}}:not(.elementor-grid-0) .elementor-grid' => 'grid-row-gap: {{SIZE}}{{UNIT}}',
          '{{WRAPPER}}.elementor-grid-0 .elementor-share-btn' => 'margin-bottom: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_responsive_control(
      'button_size',
      [
        'label' => __( 'Button Size', 'tenweb-builder' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0.5,
            'max' => 2,
            'step' => 0.05,
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .elementor-share-btn' => 'font-size: calc({{SIZE}}{{UNIT}} * 10);',
        ],
      ]
    );

    $this->add_responsive_control(
      'icon_size',
      [
        'label' => __( 'Icon Size', 'tenweb-builder' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'em' => [
            'min' => 0.5,
            'max' => 4,
            'step' => 0.1,
          ],
          'px' => [
            'min' => 0,
            'max' => 100,
          ],
        ],
        'default' => [
          'unit' => 'em',
        ],
        'tablet_default' => [
          'unit' => 'em',
        ],
        'mobile_default' => [
          'unit' => 'em',
        ],
        'size_units' => [ 'em', 'px' ],
        'selectors' => [
          '{{WRAPPER}} .elementor-share-btn__icon i' => 'font-size: {{SIZE}}{{UNIT}};',
          '{{WRAPPER}} .elementor-share-btn__icon' => 'font-size: calc({{SIZE}}{{UNIT}} / 4.5);',
        ],
        'condition' => [
          'view!' => 'text',
        ],
      ]
    );

    $this->add_responsive_control(
      'button_height',
      [
        'label' => __( 'Button Height', 'tenweb-builder' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'em' => [
            'min' => 1,
            'max' => 7,
            'step' => 0.1,
          ],
          'px' => [
            'min' => 0,
            'max' => 100,
          ],
        ],
        'default' => [
          'unit' => 'em',
        ],
        'tablet_default' => [
          'unit' => 'em',
        ],
        'mobile_default' => [
          'unit' => 'em',
        ],
        'size_units' => [ 'em', 'px' ],
        'selectors' => [
          '{{WRAPPER}} .elementor-share-btn' => 'height: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_responsive_control(
      'border_size',
      [
        'label' => __( 'Border Size', 'tenweb-builder' ),
        'type' => Controls_Manager::SLIDER,
        'size_units' => [ 'px', 'em' ],
        'default' => [
          'size' => 2,
        ],
        'range' => [
          'px' => [
            'min' => 1,
            'max' => 20,
          ],
          'em' => [
            'max' => 2,
            'step' => 0.1,
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .elementor-share-btn' => 'border-width: {{SIZE}}{{UNIT}};',
        ],
        'condition' => [
          'skin' => [ 'framed', 'boxed' ],
        ],
      ]
    );

    $this->add_control(
      'color_source',
      [
        'label' => __( 'Color', 'tenweb-builder' ),
        'type' => Controls_Manager::SELECT,
        'label_block' => false,
        'options' => [
          'official' => 'Official Color',
          'custom' => 'Custom Color',
        ],
        'default' => 'official',
        'prefix_class' => 'elementor-share-buttons--color-',
        'separator' => 'before',
      ]
    );

    $this->start_controls_tabs( 'tabs_button_style' );

    $this->start_controls_tab(
      'tab_button_normal',
      [
        'label' => __( 'Normal', 'tenweb-builder' ),
        'condition' => [
          'color_source' => 'custom',
        ],
      ]
    );

    $this->add_control(
      'primary_color',
      [
        'label' => __( 'Primary Color', 'tenweb-builder' ),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}}.elementor-share-buttons--skin-flat .elementor-share-btn,
					 {{WRAPPER}}.elementor-share-buttons--skin-gradient .elementor-share-btn,
					 {{WRAPPER}}.elementor-share-buttons--skin-boxed .elementor-share-btn .elementor-share-btn__icon,
					 {{WRAPPER}}.elementor-share-buttons--skin-minimal .elementor-share-btn .elementor-share-btn__icon' => 'background-color: {{VALUE}}',
          '{{WRAPPER}}.elementor-share-buttons--skin-framed .elementor-share-btn,
					 {{WRAPPER}}.elementor-share-buttons--skin-minimal .elementor-share-btn,
					 {{WRAPPER}}.elementor-share-buttons--skin-boxed .elementor-share-btn' => 'color: {{VALUE}}; border-color: {{VALUE}}',
        ],
        'condition' => [
          'color_source' => 'custom',
        ],
      ]
    );

    $this->add_control(
      'secondary_color',
      [
        'label' => __( 'Secondary Color', 'tenweb-builder' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}}.elementor-share-buttons--skin-flat .elementor-share-btn__icon, 
					 {{WRAPPER}}.elementor-share-buttons--skin-flat .elementor-share-btn__text, 
					 {{WRAPPER}}.elementor-share-buttons--skin-gradient .elementor-share-btn__icon,
					 {{WRAPPER}}.elementor-share-buttons--skin-gradient .elementor-share-btn__text,
					 {{WRAPPER}}.elementor-share-buttons--skin-boxed .elementor-share-btn__icon,
					 {{WRAPPER}}.elementor-share-buttons--skin-minimal .elementor-share-btn__icon' => 'color: {{VALUE}}',
        ],
        'condition' => [
          'color_source' => 'custom',
        ],
        'separator' => 'after',
      ]
    );

    $this->end_controls_tab();

    $this->start_controls_tab(
      'tab_button_hover',
      [
        'label' => __( 'Hover', 'tenweb-builder' ),
        'condition' => [
          'color_source' => 'custom',
        ],
      ]
    );

    $this->add_control(
      'primary_color_hover',
      [
        'label' => __( 'Primary Color', 'tenweb-builder' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}}.elementor-share-buttons--skin-flat .elementor-share-btn:hover,
					 {{WRAPPER}}.elementor-share-buttons--skin-gradient .elementor-share-btn:hover' => 'background-color: {{VALUE}}',
          '{{WRAPPER}}.elementor-share-buttons--skin-framed .elementor-share-btn:hover,
					 {{WRAPPER}}.elementor-share-buttons--skin-minimal .elementor-share-btn:hover,
					 {{WRAPPER}}.elementor-share-buttons--skin-boxed .elementor-share-btn:hover' => 'color: {{VALUE}}; border-color: {{VALUE}}',
          '{{WRAPPER}}.elementor-share-buttons--skin-boxed .elementor-share-btn:hover .elementor-share-btn__icon, 
					 {{WRAPPER}}.elementor-share-buttons--skin-minimal .elementor-share-btn:hover .elementor-share-btn__icon' => 'background-color: {{VALUE}}',
        ],
        'condition' => [
          'color_source' => 'custom',
        ],
      ]
    );

    $this->add_control(
      'secondary_color_hover',
      [
        'label' => __( 'Secondary Color', 'tenweb-builder' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}}.elementor-share-buttons--skin-flat .elementor-share-btn:hover .elementor-share-btn__icon, 
					 {{WRAPPER}}.elementor-share-buttons--skin-flat .elementor-share-btn:hover .elementor-share-btn__text, 
					 {{WRAPPER}}.elementor-share-buttons--skin-gradient .elementor-share-btn:hover .elementor-share-btn__icon,
					 {{WRAPPER}}.elementor-share-buttons--skin-gradient .elementor-share-btn:hover .elementor-share-btn__text,
					 {{WRAPPER}}.elementor-share-buttons--skin-boxed .elementor-share-btn:hover .elementor-share-btn__icon,
					 {{WRAPPER}}.elementor-share-buttons--skin-minimal .elementor-share-btn:hover .elementor-share-btn__icon' => 'color: {{VALUE}}',
        ],
        'condition' => [
          'color_source' => 'custom',
        ],
        'separator' => 'after',
      ]
    );

    $this->end_controls_tab();

    $this->end_controls_tabs();

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'typography',
        'selector' => '{{WRAPPER}} .elementor-share-btn__title',
        'exclude' => [ 'line_height' ], //phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude
      ]
    );

    $this->add_control(
      'text_padding',
      [
        'label' => __( 'Text Padding', 'tenweb-builder' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', 'em', '%' ],
        'selectors' => [
          '{{WRAPPER}} a.elementor-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
        'separator' => 'before',
        'condition' => [
          'view' => 'text',
        ],
      ]
    );

    $this->end_controls_section();

  }

  protected function render() {
    $settings = $this->get_active_settings();

    if ( empty( $settings['share_buttons'] ) ) {
      return;
    }

    $button_classes = 'elementor-share-btn';
    ?>
    <div class="elementor-grid">
      <?php
      foreach ( $settings['share_buttons'] as $button ) {
        $network_name = $button['button'];

        $social_network_class = ' elementor-share-btn_' . $network_name;
        ?>
        <div class="elementor-grid-item">
          <div class="<?php echo esc_attr( $button_classes . $social_network_class ); ?>">
            <?php
            if ( 'text' !== $settings['view'] ) {
            ?>
              <span class="elementor-share-btn__icon">
								<i class="<?php echo esc_attr(self::get_network_class( $network_name )); ?>"></i>
							</span>
            <?php
            }
            ?>
            <?php
            if ( 'icon' !== $settings['view'] ) {
            ?>
              <div class="elementor-share-btn__text">
                <span class="elementor-share-btn__title">
                  <?php echo $button['text'] ? $button['text'] : $this->get_networks( $network_name ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped?>
                </span>
              </div>
            <?php
            }
            ?>
          </div>
        </div>
        <?php
      }
      ?>
    </div>
    <?php
  }
}
\Elementor\Plugin::instance()->widgets_manager->register(new Share_Buttons());
