<?php
namespace Tenweb_Builder;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Utils;
use Elementor\Widget_Base;

if (!defined('ABSPATH'))
  exit; // Exit if accessed directly

class Countdown extends Widget_Base
{

  public function get_name()
  {
    return Builder::$prefix . 'countdown';
  }

  public function get_title()
  {
    return __('Countdown', 'tenweb-builder');
  }

  public function get_icon()
  {
    return 'twbb-countdown twbb-widget-icon';
  }

  public function get_categories()
  {
    return ['tenweb-widgets'];
  }

  protected function register_controls()
  {
    $this->start_controls_section(
      'section_countdown',
      [
        'label' => __('Countdown', 'tenweb-builder'),
      ]
    );

    $this->add_control(
      'countdown_type',
      [
        'label'   => __('Type', 'tenweb-builder'),
        'type'    => Controls_Manager::SELECT,
        'options' => [
          'due_date'  => esc_html__('Due Date', 'tenweb-builder'),
          'evergreen' => esc_html__('Evergreen Timer', 'tenweb-builder'),
        ],
        'default' => 'due_date',
      ]
    );

    $this->add_control(
      'due_date',
      [
        'label'       => __('Due Date', 'tenweb-builder'),
        'type'        => Controls_Manager::DATE_TIME,
        'default'     => gmdate('Y-m-d H:i', $this->timestamp('+1 month')),
        'description' => sprintf(__('Date set according to your timezone: %s.', 'tenweb-builder'), Utils::get_timezone_string()),
        'condition'   => [
          'countdown_type' => 'due_date',
        ],
        'dynamic'     => [
          'active' => true,
        ],
      ]
    );

    $this->add_control(
      'evergreen_counter_hours',
      [
        'label'       => __('Hours', 'tenweb-builder'),
        'type'        => Controls_Manager::NUMBER,
        'default'     => 47,
        'placeholder' => __('Hours', 'tenweb-builder'),
        'condition'   => [
          'countdown_type' => 'evergreen',
        ],
        'dynamic'     => [
          'active' => true,
        ],
      ]
    );

    $this->add_control(
      'evergreen_counter_minutes',
      [
        'label'       => __('Minutes', 'tenweb-builder'),
        'type'        => Controls_Manager::NUMBER,
        'default'     => 59,
        'placeholder' => __('Minutes', 'tenweb-builder'),
        'condition'   => [
          'countdown_type' => 'evergreen',
        ],
        'dynamic'     => [
          'active' => true,
        ],
      ]
    );

    $this->add_control(
      'layout',
      [
        'label'        => __('Layout', 'tenweb-builder'),
        'type'         => Controls_Manager::SELECT,
        'options'      => [
          'squares' => __('Squares', 'tenweb-builder'),
          'circles' => __('Circles', 'tenweb-builder'),
          'inline'  => __('Inline', 'tenweb-builder'),
        ],
        'default'      => 'squares',
        'prefix_class' => 'tenweb-countdown--layout-',
      ]
    );

    $this->add_control(
      'show_delimiter',
      [
        'label'        => __('Delimiter', 'tenweb-builder') . ' (:) ',
        'type'         => Controls_Manager::SWITCHER,
        'label_on'     => __('Show', 'tenweb-builder'),
        'label_off'    => __('Hide', 'tenweb-builder'),
        'default'      => 'no',
        'condition'    => [
          'layout' => 'inline',
        ],
        'prefix_class' => 'tenweb-countdown--delimiters-',
      ]
    );

    $this->add_control(
      'show_months',
      [
        'label'     => __('Months', 'tenweb-builder'),
        'type'      => Controls_Manager::SWITCHER,
        'label_on'  => __('Show', 'tenweb-builder'),
        'label_off' => __('Hide', 'tenweb-builder'),
      ]
    );

    $this->add_control(
      'show_days',
      [
        'label'     => __('Days', 'tenweb-builder'),
        'type'      => Controls_Manager::SWITCHER,
        'label_on'  => __('Show', 'tenweb-builder'),
        'label_off' => __('Hide', 'tenweb-builder'),
        'default'   => 'yes',
      ]
    );

    $this->add_control(
      'show_hours',
      [
        'label'     => __('Hours', 'tenweb-builder'),
        'type'      => Controls_Manager::SWITCHER,
        'label_on'  => __('Show', 'tenweb-builder'),
        'label_off' => __('Hide', 'tenweb-builder'),
        'default'   => 'yes',
      ]
    );

    $this->add_control(
      'show_minutes',
      [
        'label'     => __('Minutes', 'tenweb-builder'),
        'type'      => Controls_Manager::SWITCHER,
        'label_on'  => __('Show', 'tenweb-builder'),
        'label_off' => __('Hide', 'tenweb-builder'),
        'default'   => 'yes',
      ]
    );

    $this->add_control(
      'show_seconds',
      [
        'label'     => __('Seconds', 'tenweb-builder'),
        'type'      => Controls_Manager::SWITCHER,
        'label_on'  => __('Show', 'tenweb-builder'),
        'label_off' => __('Hide', 'tenweb-builder'),
        'default'   => 'yes',
      ]
    );

    $this->add_control(
      'description',
      [
        'label'       => __('Description', 'tenweb-builder'),
        'type'        => Controls_Manager::TEXTAREA,
        'placeholder' => "",
        'default'     => "",
        'rows'        => 3,
        'separator'   => 'before',
      ]
    );

    $this->add_control(
      'hide_after_expiry',
      [
        'label'     => __('Hide counter after expiry', 'tenweb-builder'),
        'type'      => Controls_Manager::SWITCHER,
        'label_on'  => __('Yes', 'tenweb-builder'),
        'label_off' => __('No', 'tenweb-builder'),
        'default'   => 'no',
      ]
    );

    $this->add_control(
      'after_expiry_text',
      [
        'label'       => __('Expired event text', 'tenweb-builder'),
        'type'        => Controls_Manager::TEXTAREA,
        'placeholder' => __('Expired event text', 'tenweb-builder'),
        'default'     => "Expired event text",
        'rows'        => 3,
        'condition'   => [
          'hide_after_expiry' => 'yes',
        ],
      ]
    );

    $this->add_control(
      'show_labels',
      [
        'label'     => __('Show Label', 'tenweb-builder'),
        'type'      => Controls_Manager::SWITCHER,
        'label_on'  => __('Show', 'tenweb-builder'),
        'label_off' => __('Hide', 'tenweb-builder'),
        'default'   => 'yes',
      ]
    );

    $this->add_control(
      'custom_labels',
      [
        'label'     => __('Custom Label', 'tenweb-builder'),
        'type'      => Controls_Manager::SWITCHER,
        'condition' => [
          'show_labels!' => '',
        ],
      ]
    );

    $this->add_control(
      'label_months',
      [
        'label'       => __('Months', 'tenweb-builder'),
        'type'        => Controls_Manager::TEXT,
        'default'     => __('Months', 'tenweb-builder'),
        'placeholder' => __('Months', 'tenweb-builder'),
        'condition'   => [
          'show_labels!'   => '',
          'custom_labels!' => '',
          'show_months'    => 'yes',
        ],
      ]
    );

    $this->add_control(
      'label_days',
      [
        'label'       => __('Days', 'tenweb-builder'),
        'type'        => Controls_Manager::TEXT,
        'default'     => __('Days', 'tenweb-builder'),
        'placeholder' => __('Days', 'tenweb-builder'),
        'condition'   => [
          'show_labels!'   => '',
          'custom_labels!' => '',
          'show_days'      => 'yes',
        ],
      ]
    );

    $this->add_control(
      'label_hours',
      [
        'label'       => __('Hours', 'tenweb-builder'),
        'type'        => Controls_Manager::TEXT,
        'default'     => __('Hours', 'tenweb-builder'),
        'placeholder' => __('Hours', 'tenweb-builder'),
        'condition'   => [
          'show_labels!'   => '',
          'custom_labels!' => '',
          'show_hours'     => 'yes',
        ],
      ]
    );

    $this->add_control(
      'label_minutes',
      [
        'label'       => __('Minutes', 'tenweb-builder'),
        'type'        => Controls_Manager::TEXT,
        'default'     => __('Minutes', 'tenweb-builder'),
        'placeholder' => __('Minutes', 'tenweb-builder'),
        'condition'   => [
          'show_labels!'   => '',
          'custom_labels!' => '',
          'show_minutes'   => 'yes',
        ],
      ]
    );

    $this->add_control(
      'label_seconds',
      [
        'label'       => __('Seconds', 'tenweb-builder'),
        'type'        => Controls_Manager::TEXT,
        'default'     => __('Seconds', 'tenweb-builder'),
        'placeholder' => __('Seconds', 'tenweb-builder'),
        'condition'   => [
          'show_labels!'   => '',
          'custom_labels!' => '',
          'show_seconds'   => 'yes',
        ],
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section(
      'section_box_style',
      [
        'label' => __('Boxes', 'tenweb-builder'),
        'tab'   => Controls_Manager::TAB_STYLE,
      ]
    );

    $this->add_responsive_control(
      'container_width',
      [
        'label'          => __('Container Width', 'tenweb-builder'),
        'type'           => Controls_Manager::SLIDER,
        'default'        => [
          'unit' => '%',
          'size' => 100,
        ],
        'tablet_default' => [
          'unit' => '%',
        ],
        'mobile_default' => [
          'unit' => '%',
        ],
        'range'          => [
          'px' => [
            'min' => 0,
            'max' => 2000,
          ],
          '%'  => [
            'min' => 0,
            'max' => 100,
          ],
        ],
        'size_units'     => ['%', 'px'],
        'selectors'      => [
          '{{WRAPPER}} .tenweb-countdown-wrapper' => 'max-width: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_control(
      'box_background_color',
      [
        'label'     => __('Background Color', 'tenweb-builder'),
        'type'      => Controls_Manager::COLOR,
        'default'   => '',
        'selectors' => [
          '{{WRAPPER}}.tenweb-countdown--layout-squares .tenweb-countdown-item' => 'background-color: {{VALUE}};',
          '{{WRAPPER}}.tenweb-countdown--layout-circles .tenweb-countdown-item' => 'background-color: {{VALUE}};',
        ],
        'condition' => [
          'layout!' => 'inline',
        ],
      ]
    );

    $this->add_group_control(
      Group_Control_Border::get_type(),
      [
        'name'      => 'box_border',
        'selector'  => '{{WRAPPER}} .tenweb-countdown-item',
        'separator' => 'before',
        'condition' => [
          'layout!' => 'inline',
        ]
      ]
    );

    $this->add_control(
      'box_border_radius',
      [
        'label'      => __('Border Radius', 'tenweb-builder'),
        'type'       => Controls_Manager::DIMENSIONS,
        'size_units' => ['px', '%'],
        'selectors'  => [
          '{{WRAPPER}} .tenweb-countdown-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
        'condition'  => [
          'layout' => 'squares',
        ]
      ]
    );

    $this->add_responsive_control(
      'box_spacing',
      [
        'label'     => __('Space Between', 'tenweb-builder'),
        'type'      => Controls_Manager::SLIDER,
        'default'   => [
          'size' => 1,
        ],
        'range'     => [
          'px' => [
            'min' => 0,
            'max' => 100,
          ],
        ],
        'selectors' => [
          'body:not(.rtl) {{WRAPPER}} .tenweb-countdown-item:not(:first-of-type)' => 'margin-left: calc( {{SIZE}}{{UNIT}}/2 );',
          'body:not(.rtl) {{WRAPPER}} .tenweb-countdown-item:not(:last-of-type)'  => 'margin-right: calc( {{SIZE}}{{UNIT}}/2 );',
          'body.rtl {{WRAPPER}} .tenweb-countdown-item:not(:first-of-type)'       => 'margin-right: calc( {{SIZE}}{{UNIT}}/2 );',
          'body.rtl {{WRAPPER}} .tenweb-countdown-item:not(:last-of-type)'        => 'margin-left: calc( {{SIZE}}{{UNIT}}/2 );',
        ],
      ]
    );

    $this->add_responsive_control(
      'box_padding',
      [
        'label'          => __('Padding', 'tenweb-builder'),
        'type'           => Controls_Manager::DIMENSIONS,
        'size_units'     => ['px', '%', 'em'],
        'tablet_default' => [
          'size' => 0,
          'unit' => 'px',
        ],
        'mobile_default' => [
          'size' => 0,
          'unit' => 'px',
        ],
        'selectors'      => [
          '{{WRAPPER}} .tenweb-countdown-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section(
      'section_content_style',
      [
        'label' => __('Content', 'tenweb-builder'),
        'tab'   => Controls_Manager::TAB_STYLE,
      ]
    );

    $this->add_control(
      'heading_description',
      [
        'label' => __('Description', 'tenweb-builder'),
        'type'  => Controls_Manager::HEADING,
      ]
    );

    $this->add_control(
      'description_color',
      [
        'label'     => __('Color', 'tenweb-builder'),
        'type'      => Controls_Manager::COLOR,
        'global'    => [
          'default' => Global_Colors::COLOR_TEXT,
        ],
        'selectors' => [
          '{{WRAPPER}} .tenweb-countdown-description' => 'color: {{VALUE}};',
        ],
      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name'     => 'description_typography',
        'selector' => '{{WRAPPER}} .tenweb-countdown-description',
          'global' => [
              'default' => Global_Typography::TYPOGRAPHY_TEXT,
          ],
      ]
    );

    $this->add_control(
      'heading_digits',
      [
        'label'     => __('Digits', 'tenweb-builder'),
        'type'      => Controls_Manager::HEADING,
        'separator' => 'before',
      ]
    );

    $this->add_control(
      'digits_color',
      [
        'label'     => __('Color', 'tenweb-builder'),
        'type'      => Controls_Manager::COLOR,
        'global'    => [
          'default' => Global_Colors::COLOR_TEXT,
        ],
        'selectors' => [
          '{{WRAPPER}} .tenweb-countdown-digits' => 'color: {{VALUE}};',
        ],
        'condition' => [
          'layout!' => 'inline',
        ],
      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name'      => 'digits_typography',
        'selector'  => '{{WRAPPER}} .tenweb-countdown-digits',
          'global' => [
              'default' => Global_Typography::TYPOGRAPHY_TEXT,
          ],
        'condition' => [
          'layout!' => 'inline',
        ],
      ]
    );


    $this->add_control(
      'digits_color_inline',
      [
        'label'     => __('Color', 'tenweb-builder'),
        'type'      => Controls_Manager::COLOR,
        'global'    => [
          'default' => Global_Colors::COLOR_TEXT,
        ],
        'selectors' => [
          '{{WRAPPER}}.tenweb-countdown--layout-inline .tenweb-countdown-digits' => 'color: {{VALUE}};',
        ],
        'condition' => [
          'layout' => 'inline',
        ],
      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name'            => 'digits_typography_inline',
        'selector'        => '{{WRAPPER}}.tenweb-countdown--layout-inline .tenweb-countdown-digits',
          'global' => [
              'default' => Global_Typography::TYPOGRAPHY_TEXT,
          ],
        'desktop_default' => [
          'size' => 53,
          'unit' => 'px',
        ],
        'tablet_default'  => [
          'size' => 38,
          'unit' => 'px',
        ],
        'mobile_default'  => [
          'size' => 25,
          'unit' => 'px',
        ],
        'condition'       => [
          'layout' => 'inline',
        ],
      ]
    );

    $this->add_control(
      'heading_label',
      [
        'label'     => __('Label', 'tenweb-builder'),
        'type'      => Controls_Manager::HEADING,
        'separator' => 'before',
      ]
    );

    $this->add_control(
      'label_color',
      [
        'label'     => __('Color', 'tenweb-builder'),
        'type'      => Controls_Manager::COLOR,
        'global'    => [
          'default' => Global_Colors::COLOR_TEXT,
        ],
        'selectors' => [
          '{{WRAPPER}} .tenweb-countdown-label' => 'color: {{VALUE}};',
        ],
        'condition' => [
          'layout!' => 'inline',
        ],
      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name'      => 'label_typography',
        'selector'  => '{{WRAPPER}} .tenweb-countdown-label',
          'global' => [
              'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
          ],
        'condition' => [
          'layout!' => 'inline',
        ],
      ]
    );

    $this->add_control(
      'label_color_inline',
      [
        'label'     => __('Color', 'tenweb-builder'),
        'type'      => Controls_Manager::COLOR,
        'global'    => [
          'default' => Global_Colors::COLOR_TEXT,
        ],
        'selectors' => [
          '{{WRAPPER}}.tenweb-countdown--layout-inline .tenweb-countdown-label' => 'color: {{VALUE}};',
        ],
        'condition' => [
          'layout' => 'inline',
        ],
      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name'            => 'label_typography_inline',
        'selector'        => '{{WRAPPER}}.tenweb-countdown--layout-inline .tenweb-countdown-label',
          'global' => [
              'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
          ],
        'desktop_default' => [
          'size' => 15,
          'unit' => 'px',
        ],
        'tablet_default'  => [
          'size' => 13,
          'unit' => 'px',
        ],
        'mobile_default'  => [
          'size' => 10,
          'unit' => 'px',
        ],
        'condition'       => [
          'layout' => 'inline',
        ],
      ]
    );

    $this->add_control(
      'heading_expiry_text',
      [
        'label'     => __('Expiry Text', 'tenweb-builder'),
        'type'      => Controls_Manager::HEADING,
        'separator' => 'before',
      ]
    );

    $this->add_control(
      'expiry_text_color',
      [
        'label'     => __('Color', 'tenweb-builder'),
        'global'    => [
          'default' => Global_Colors::COLOR_TEXT,
        ],
        'type'      => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .tenweb-countdown-expired' => 'color: {{VALUE}};',
        ],
      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name'     => 'expiry_text_typography',
        'selector' => '{{WRAPPER}} .tenweb-countdown-wrapper.tenweb-countdown-header .tenweb-countdown-expired .tenweb-countdown-label',
          'global' => [
              'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
          ],
      ]
    );

    $this->end_controls_section();
  }

  private function timestamp($time)
  {
    return strtotime($time) + (get_option('gmt_offset') * HOUR_IN_SECONDS);
  }

  private function get_strftime($instance)
  {
    $string = '';
    $block_count = 0;
    if ($instance['show_months']) {
      $string .= $this->render_countdown_item($instance, 'label_months', 'tenweb-countdown-months', $block_count);
      $block_count++;
    }
    if ($instance['show_days']) {
      $string .= $this->render_countdown_item($instance, 'label_days', 'tenweb-countdown-days', $block_count);
      $block_count++;
    }
    if ($instance['show_hours']) {
      $string .= $this->render_countdown_item($instance, 'label_hours', 'tenweb-countdown-hours', $block_count);
      $block_count++;
    }
    if ($instance['show_minutes']) {
      $string .= $this->render_countdown_item($instance, 'label_minutes', 'tenweb-countdown-minutes', $block_count);
      $block_count++;
    }
    if ($instance['show_seconds']) {
      $string .= $this->render_countdown_item($instance, 'label_seconds', 'tenweb-countdown-seconds', $block_count);
    }

    return $string;
  }

  private function get_evergreen_interval($instance)
  {
    $hours = empty($instance['evergreen_counter_hours']) ? 0 : ($instance['evergreen_counter_hours'] * HOUR_IN_SECONDS);
    $minutes = empty($instance['evergreen_counter_minutes']) ? 0 : ($instance['evergreen_counter_minutes'] * MINUTE_IN_SECONDS);

    return $hours + $minutes;
  }

  private $_default_countdown_labels;

  private function _init_default_countdown_labels()
  {
    $this->_default_countdown_labels = [
      'label_months'  => __('Months', 'tenweb-builder'),
      'label_weeks'   => __('Weeks', 'tenweb-builder'),
      'label_days'    => __('Days', 'tenweb-builder'),
      'label_hours'   => __('Hours', 'tenweb-builder'),
      'label_minutes' => __('Minutes', 'tenweb-builder'),
      'label_seconds' => __('Seconds', 'tenweb-builder'),
    ];
  }

  public function get_default_countdown_labels()
  {
    if (!$this->_default_countdown_labels) {
      $this->_init_default_countdown_labels();
    }

    return $this->_default_countdown_labels;
  }

  private function render_countdown_item($instance, $label, $part_class, $block_count)
  {
    $string = $block_count > 0 ? '<div class="tenweb-countdown-item tenweb-countdown-delimiter-container"><div><div><span class="tenweb-countdown-digits">:</span></div></div></div>' : '';
    $string .= '<div class="tenweb-countdown-item"><div><div><span class="tenweb-countdown-digits ' . $part_class . '"></span>';

    if ($instance['show_labels']) {
      $default_labels = $this->get_default_countdown_labels();
      $label = ($instance['custom_labels']) ? $instance[$label] : $default_labels[$label];
      $string .= ' <span class="tenweb-countdown-label">' . $label . '</span>';
    }

    $string .= '</div></div></div>';

    return $string;
  }

  protected function render()
  {
    $instance = $this->get_settings_for_display();
    $counter_string = $this->get_strftime($instance);

    if ('evergreen' === $instance['countdown_type']) {
      $evergreen = $this->get_evergreen_interval($instance);
      $due_date = gmdate("Y-m-d H:i", time() + $evergreen);
    } else {
      $due_date = $instance['due_date'];
    }
    // Handle timezone ( we need to set GMT time )
    $gmt = get_gmt_from_date($due_date . ':00');
    $due_date = strtotime($gmt);
    ?>
      <div class="tenweb-countdown-wrapper tenweb-countdown-header">
        <?php if ( $instance['description'] ) { ?>
            <div class="tenweb-countdown-description"><?php $this->print_unescaped_setting( 'description' ); ?></div>
        <?php }
        if ( $instance['hide_after_expiry'] ) {?>
          <div class="tenweb-countdown-expired tenweb-hidden">
            <span class="tenweb-countdown-label"><?php $this->print_unescaped_setting( 'after_expiry_text');?></span>
          </div>
        <?php } ?>
      </div>
      <div class="tenweb-countdown tenweb-countdown-wrapper" data-date="<?php echo esc_attr($due_date); ?>"
           data-hide-after-expiry="<?php echo esc_attr($instance['hide_after_expiry']); ?>">
        <?php echo wp_kses_post($counter_string); ?>
      </div>
    <?php
  }
}

\Elementor\Plugin::instance()->widgets_manager->register(new Countdown());
