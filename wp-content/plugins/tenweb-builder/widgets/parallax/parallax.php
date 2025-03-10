<?php
namespace Tenweb_Builder\Widgets\Parallax;

use Elementor\Controls_Manager;
use Elementor\Element_Base;
use Elementor\Element_Section;
use Elementor\Widget_Base;

/**
 * Class Parallax
 *
 * @package Tenweb_Builder\Widgets\Parallax
 */
class Parallax extends Widget_Base {

  public function __construct() {
    $this->add_actions();
  }

  public function get_name() {
    return 'tenweb-parallax';
  }

  public function show_in_panel() {
    return false;
  }

  /**
   * @param Controls_Parallax $element
   */
	public function register_custom_controls( Element_Base $element ) {
        if ( ! $element instanceof Element_Section ) {
            return;
        }
        $element->start_injection( [
            'of' => 'background_bg_width_mobile',
        ] );
        $element->add_control(
            'tenweb_enable_parallax_efects',
            [
                'label' => __( 'Parallax Effects', 'tenweb-builder' ),
                'type' => Controls_Manager::SWITCHER,
                'label_off' => __( 'Off', 'tenweb-builder' ),
                'label_on' => __( 'On', 'tenweb-builder' ),
                'render_type' => 'ui',
                'frontend_available' => true,
                'condition' => [
                  'background_background' => 'classic',
                  'tenweb_enable_parallax_efects' => 'yes'
                ],
            ]
        );
        /*
         * Vertical effect
         */
        $element->add_control(
            'tenweb_vertical_scroll_efects',
            [
                'label' => __( 'Vertical Scroll', 'tenweb-builder' ),
                'type' => Controls_Manager::POPOVER_TOGGLE,
                'condition' => [
                    'tenweb_enable_parallax_efects' => 'yes',
                    'background_background' => 'classic',
                ],
                'render_type' => 'none',
                'frontend_available' => true,
            ]
        );
        $element->add_control(
            'tenweb_vertical_scroll_efects-direction',
            [
                'label' => __( 'Vertical Scroll direction', 'tenweb-builder' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'up',
                'options' => [
                    'up' => __( 'Up', 'tenweb-builder' ),
                    'down' => __( 'Down', 'tenweb-builder' ),
                ],
                'condition' => [
                    'tenweb_enable_parallax_efects' => 'yes',
                    'tenweb_vertical_scroll_efects' => 'yes',
                    'background_background' => 'classic',
                ],
                'popover' => [
                    'start'=>true
                ],
                'render_type' => 'none',
                'frontend_available' => true,
            ]
        );
        $element->add_control(
            'tenweb_vertical_scroll_efects-speed',
            [
                'label' => __( 'Vertical Scroll Speed', 'tenweb-builder' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 4,
                ],
                'range' => [
                    'px' => [
                        'max' => 10,
                        'step' => 0.1,
                    ],
                ],
                'condition' => [
                    'tenweb_enable_parallax_efects' => 'yes',
                    'tenweb_vertical_scroll_efects' => 'yes',
                    'background_background' => 'classic',
                ],
                'popover' => [
                    'end'=>true
                ],
                'render_type' => 'none',
                'frontend_available' => true,
            ]
        );

    /*
     * Horizontal effect
     */
    $element->add_control(
      'tenweb_horizontal_scroll_efects',
      [
        'label' => __( 'Horizontal Scroll', 'tenweb-builder' ),
        'type' => Controls_Manager::POPOVER_TOGGLE,
        'condition' => [
          'tenweb_enable_parallax_efects' => 'yes',
          'background_background' => 'classic',
        ],
        'render_type' => 'none',
        'frontend_available' => true,
      ]
    );
    $element->add_control(
      'tenweb_horizontal_scroll_efects-direction',
      [
        'label' => __( 'Horizontal Scroll direction', 'tenweb-builder' ),
        'type' => Controls_Manager::SELECT,
        'default' => 'left',
        'options' => [
          'left' => __( 'To Left', 'tenweb-builder' ),
          'right' => __( 'To Right', 'tenweb-builder' ),
        ],
        'condition' => [
          'tenweb_enable_parallax_efects' => 'yes',
          'tenweb_horizontal_scroll_efects' => 'yes',
          'background_background' => 'classic',
        ],
        'popover' => [
          'start' => true
        ],
        'render_type' => 'none',
        'frontend_available' => true,
      ]
    );
    $element->add_control(
      'tenweb_horizontal_scroll_efects-speed',
      [
        'label' => __( 'Horizontal Scroll Speed', 'tenweb-builder' ),
        'type' => Controls_Manager::SLIDER,
        'default' => [
          'size' => 4,
        ],
        'range' => [
          'px' => [
            'max' => 10,
            'step' => 0.1,
          ],
        ],
        'condition' => [
          'tenweb_enable_parallax_efects' => 'yes',
          'tenweb_horizontal_scroll_efects' => 'yes',
          'background_background' => 'classic',
        ],
        'popover' => [
          'end' => true
        ],
        'render_type' => 'none',
        'frontend_available' => true,
      ]
    );

    /*
     *  Transparency
     */
    $element->add_control(
      'tenweb_transparency_efects',
      [
        'label' => __( 'Transparency', 'tenweb-builder' ),
        'type' => Controls_Manager::POPOVER_TOGGLE,
        'condition' => [
          'tenweb_enable_parallax_efects' => 'yes',
          'background_background' => 'classic',
        ],
        'render_type' => 'none',
        'frontend_available' => true,
      ]
    );
    $element->add_control(
      'tenweb_transparency_efects-direction',
      [
        'label' => __( 'Transparency direction', 'tenweb-builder' ),
        'type' => Controls_Manager::SELECT,
        'default' => 'in',
        'options' => [
          'in' => __( 'Fade In', 'tenweb-builder' ),
          'out' => __( 'Fade Out', 'tenweb-builder' ),
        ],
        'condition' => [
          'tenweb_enable_parallax_efects' => 'yes',
          'tenweb_transparency_efects' => 'yes',
          'background_background' => 'classic',
        ],
        'popover' => [
          'start' => true
        ],
        'render_type' => 'none',
        'frontend_available' => true,
      ]
    );
    $element->add_control(
      'tenweb_transparency_efects-speed',
      [
        'label' => __( 'Transparency Speed', 'tenweb-builder' ),
        'type' => Controls_Manager::SLIDER,
        'default' => [
          'size' => 4,
        ],
        'range' => [
          'px' => [
            'max' => 10,
            'step' => 0.1,
          ],
        ],
        'condition' => [
          'tenweb_enable_parallax_efects' => 'yes',
          'tenweb_transparency_efects' => 'yes',
          'background_background' => 'classic',
        ],
        'popover' => [
          'end' => true
        ],
        'render_type' => 'none',
        'frontend_available' => true,
      ]
    );

    /*
     * Blur
     */
    $element->add_control(
      'tenweb_blur_efects',
      [
        'label' => __( 'Blur', 'tenweb-builder' ),
        'type' => Controls_Manager::POPOVER_TOGGLE,
        'condition' => [
          'tenweb_enable_parallax_efects' => 'yes',
          'background_background' => 'classic',
        ],
        'render_type' => 'none',
        'frontend_available' => true,
      ]
    );
    $element->add_control(
      'tenweb_blur_efects-direction',
      [
        'label' => __( 'Blur direction', 'tenweb-builder' ),
        'type' => Controls_Manager::SELECT,
        'default' => 'in',
        'options' => [
          'in' => __( 'Fade In', 'tenweb-builder' ),
          'out' => __( 'Fade Out', 'tenweb-builder' ),
        ],
        'condition' => [
          'tenweb_enable_parallax_efects' => 'yes',
          'tenweb_blur_efects' => 'yes',
          'background_background' => 'classic',
        ],
        'popover' => [
          'start' => true
        ],
        'render_type' => 'none',
        'frontend_available' => true,
      ]
    );
    $element->add_control(
      'tenweb_blur_efects-speed',
      [
        'label' => __( 'Blur Speed', 'tenweb-builder' ),
        'type' => Controls_Manager::SLIDER,
        'default' => [
          'size' => 4,
        ],
        'range' => [
          'px' => [
            'max' => 10,
            'step' => 0.1,
          ],
        ],
        'condition' => [
          'tenweb_enable_parallax_efects' => 'yes',
          'tenweb_blur_efects' => 'yes',
          'background_background' => 'classic',
        ],
        'popover' => [
          'end' => true
        ],
        'render_type' => 'none',
        'frontend_available' => true,
      ]
    );

    /*
     * Scale
     */
    $element->add_control(
      'tenweb_scale_efects',
      [
        'label' => __( 'Scale', 'tenweb-builder' ),
        'type' => Controls_Manager::POPOVER_TOGGLE,
        'condition' => [
          'tenweb_enable_parallax_efects' => 'yes',
          'background_background' => 'classic',
        ],
        'render_type' => 'none',
        'frontend_available' => true,
      ]
    );
    $element->add_control(
      'tenweb_scale_efects-direction',
      [
        'label' => __( 'Scale direction', 'tenweb-builder' ),
        'type' => Controls_Manager::SELECT,
        'default' => 'in',
        'options' => [
          'in' => __( 'Scale In', 'tenweb-builder' ),
          'out' => __( 'Scale Out', 'tenweb-builder' ),
        ],
        'condition' => [
          'tenweb_enable_parallax_efects' => 'yes',
          'tenweb_scale_efects' => 'yes',
          'background_background' => 'classic',
        ],
        'popover' => [
          'start' => true
        ],
        'render_type' => 'none',
        'frontend_available' => true,
      ]
    );
    $element->add_control(
      'tenweb_scale_efects-speed',
      [
        'label' => __( 'Scale Speed', 'tenweb-builder' ),
        'type' => Controls_Manager::SLIDER,
        'default' => [
          'size' => 4,
        ],
        'range' => [
          'px' => [
            'max' => 10,
            'step' => 0.1,
          ],
        ],
        'condition' => [
          'tenweb_enable_parallax_efects' => 'yes',
          'tenweb_scale_efects' => 'yes',
          'background_background' => 'classic',
        ],
        'popover' => [
          'end' => true
        ],
        'render_type' => 'none',
        'frontend_available' => true,
      ]
    );

    $element->add_control(
      'tenweb_parallax_on',
      [
        'label' => __( 'Apply Effects On', 'tenweb-builder' ),
        'type' => Controls_Manager::SELECT2,
        'multiple' => true,
        'label_block' => 'true',
        'default' => [ 'desktop', 'tablet', 'mobile' ],
        'options' => [
          'desktop' => __( 'Desktop', 'tenweb-builder' ),
          'tablet' => __( 'Tablet', 'tenweb-builder' ),
          'mobile' => __( 'Mobile', 'tenweb-builder' ),
        ],
        'condition' => [
          'tenweb_enable_parallax_efects' => 'yes',
          'background_background' => 'classic',
        ],
        'render_type' => 'none',
        'frontend_available' => true,
      ]
    );
    $element->end_injection();
  }

  private function add_actions() {
    add_action( 'elementor/element/section/section_advanced/before_section_end', [ $this, 'register_custom_controls' ], 10, 3 );
  }
}
add_action('elementor/widgets/register',function(){
    \Elementor\Plugin::instance()->widgets_manager->register( new Parallax() );
});
