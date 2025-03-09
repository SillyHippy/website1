<?php
namespace Tenweb_Builder\Widgets;

use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use Elementor\Widget_Base;

/**
 * Class Sticky
 *
 * @package Tenweb_Builder\Widgets\Widgets_Conditions
 */
class Widgets_Conditions extends Widget_Base  {

	public function __construct() {
		$this->add_actions();
	}
	public function get_name() {
		return 'tenweb-widgets-conditions';
	}

  public function show_in_panel() {
    return false;
  }

  /**
   * @param Controls_Stack $element
   */
	public function register_custom_controls( Controls_Stack $element ) {
		$element->start_controls_section(
			'tenweb_section_widgets_conditions',
			[
				'label' => __( 'Conditions', 'tenweb-builder' ),
				'tab' => Controls_Manager::TAB_ADVANCED,
			]
		);

    $element->add_control(
			'tenweb_front_page_only',
			[
				'label' => __( 'Display on front page only', 'tenweb-builder' ),
				'type' => Controls_Manager::SWITCHER,
				'render_type' => 'none',
				'frontend_available' => true,
			]
		);

    $element->end_controls_section();
	}

  public function render_widget($content, $widget) {
    $settings = $widget->get_settings();

    if (isset($settings['tenweb_front_page_only']) && $settings['tenweb_front_page_only']) {
      // show element in backend
      if ( !\Elementor\Plugin::instance()->editor->is_edit_mode() && !is_front_page() ) {
        return '';
      }
    }
    return $content;
  }

	private function add_actions() {
		add_action( 'elementor/element/common/_section_style/after_section_end', [ $this, 'register_custom_controls' ] );
    add_filter('elementor/widget/render_content', [ $this, 'render_widget' ], 10, 2);
	}
}
\Elementor\Plugin::instance()->widgets_manager->register( new Widgets_Conditions() );
