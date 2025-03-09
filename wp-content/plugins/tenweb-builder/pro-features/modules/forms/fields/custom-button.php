<?php
namespace Tenweb_Builder\ElementorPro\Modules\Forms\Fields;

use Elementor\Controls_Manager;
use Elementor\Icons_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Custom_Button extends Field_Base {

	public function get_type() {
		return 'custom_button';
	}

	public function get_name() {
		return esc_html__( 'Button', 'elementor-pro' );
	}

	public function update_controls( $widget ) {
		$elementor = \Elementor\Plugin::instance();

		$control_data = $elementor->controls_manager->get_control_from_stack( $widget->get_unique_name(), 'form_fields' );

		if ( is_wp_error( $control_data ) ) {
			return;
		}

		$field_controls = [
			'custom_button_text' => [
				'name' => 'custom_button_text',
				'label' => esc_html__( 'Text', 'elementor-pro' ),
				'type' => Controls_Manager::TEXT,
        'default' => 'Custom Button',
				'condition' => [
					'field_type' => $this->get_type(),
				],
				'tab' => 'content',
				'inner_tab' => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			'custom_button_type' => [
				'name' => 'custom_button_type',
				'label' => esc_html( 'Type', 'elementor-pro' ),
				'type' => Controls_Manager::SELECT,
        'default' => '',
        'options' => [
          '' => esc_html__( 'Default', 'elementor' ),
          'info' => esc_html__( 'Info', 'elementor' ),
          'success' => esc_html__( 'Success', 'elementor' ),
          'warning' => esc_html__( 'Warning', 'elementor' ),
          'danger' => esc_html__( 'Danger', 'elementor' ),
        ],
				'condition' => [
					'field_type' => $this->get_type(),
				],
				'tab' => 'content',
				'inner_tab' => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			'custom_button_align' => [
				'name' => 'custom_button_align',
				'label' => esc_html__( 'Alignment', 'elementor-pro' ),
				'type' => Controls_Manager::CHOOSE,
        'default' => '',
        'options' => [
          'left'    => [
            'title' => esc_html__( 'Left', 'elementor' ),
            'icon' => 'eicon-text-align-left',
          ],
          'center' => [
            'title' => esc_html__( 'Center', 'elementor' ),
            'icon' => 'eicon-text-align-center',
          ],
          'right' => [
            'title' => esc_html__( 'Right', 'elementor' ),
            'icon' => 'eicon-text-align-right',
          ],
          'justify' => [
            'title' => __( 'Justified', 'elementor' ),
            'icon' => 'eicon-text-align-justify',
          ],
        ],
				'condition' => [
					'field_type' => $this->get_type(),
				],
				'tab' => 'content',
				'inner_tab' => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			'custom_button_size' => [
				'name' => 'custom_button_size',
				'label' => esc_html__( 'Size', 'elementor-pro' ),
				'type' => Controls_Manager::SELECT,
        'default' => 'sm',
        'options' => $widget::get_button_sizes(),
				'condition' => [
					'field_type' => $this->get_type(),
				],
				'tab' => 'content',
				'inner_tab' => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			'custom_button_url' => [
				'name' => 'custom_button_url',
				'label' => esc_html__( 'URL', 'elementor-pro' ),
				'type' => Controls_Manager::URL,
        'dynamic' => [
          'active' => true,
        ],
        'placeholder' => esc_html__( 'https://your-link.com', 'elementor' ),
        'default' => [
          'url' => '#',
        ],
				'condition' => [
					'field_type' => $this->get_type(),
				],
				'tab' => 'content',
				'inner_tab' => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
		];

    foreach ( $control_data['fields'] as $index => $field ) {
      if ( 'required' !== $field['name'] ) {
        continue;
      }
      foreach ( $field['conditions']['terms'] as $condition_index => $terms ) {
        if ( ! isset( $terms['name'] ) || 'field_type' !== $terms['name'] || ! isset( $terms['operator'] ) || '!in' !== $terms['operator'] ) {
          continue;
        }
        $control_data['fields'][ $index ]['conditions']['terms'][ $condition_index ]['value'][] = $this->get_type();
        break;
      }
      break;
    }

    $control_data['fields'] = $this->inject_field_controls( $control_data['fields'], $field_controls );

    $widget->update_control( 'form_fields', $control_data );
	}

	public function render( $item, $item_index, $form ) {
    $form->add_render_attribute( [
      'wrapper' . $item_index => [
        'class' => [
          'elementor-button-wrapper',
          'elementor-element',
          'elementor-field',
        ],
      ],
    ] );

    if ( ! empty( $item['custom_button_type'] ) ) {
      $form->add_render_attribute( 'wrapper' . $item_index, 'class', 'elementor-button-' . $item['custom_button_type'] );
    }

    if ( ! empty( $item['custom_button_align'] ) ) {
      $form->add_render_attribute( 'wrapper' . $item_index, 'class', 'elementor-align-' . $item['custom_button_align'] );
    }

    if ( ! empty( $item['custom_button_url']['url'] ) ) {
      $form->add_link_attributes( 'button' . $item_index, $item['custom_button_url'] );
      $form->add_render_attribute( 'button' . $item_index, 'class', 'elementor-button-link' );
    }

    $form->add_render_attribute( 'button' . $item_index, 'class', 'elementor-button' );
    $form->add_render_attribute( 'button' . $item_index, 'role', 'button' );

    if ( ! empty( $item['custom_id'] ) ) {
      $form->add_render_attribute( 'button' . $item_index, 'id', $item['custom_id'] );
    }

    if ( ! empty( $item['custom_button_size'] ) ) {
      $form->add_render_attribute( 'button' . $item_index, 'class', 'elementor-size-' . $item['custom_button_size'] );
    }
    ?>
    <div <?php echo $form->print_render_attribute_string( 'wrapper' . $item_index ); ?>>
      <a <?php echo $form->print_render_attribute_string( 'button' . $item_index ); ?>>
        <?php $this->render_text( $item, $item_index, $form ); ?>
      </a>
    </div>
    <?php
	}

  protected function render_text( $item, $item_index, $form ) {
    $form->add_render_attribute( [
      'content-wrapper' . $item_index => [
        'class' => 'elementor-button-content-wrapper',
      ],
      'icon-align' . $item_index => [
        'class' => [
          'elementor-button-icon',
//          'elementor-align-icon-' . $item['custom_button_icon_align'],
        ],
      ],
      'text' . $item_index => [
        'class' => 'elementor-button-text',
      ],
    ] );

    ?>
    <span <?php echo $form->print_render_attribute_string( 'content-wrapper' . $item_index ); ?>>
			<?php if ( ! empty( $item['custom_button_selected_icon']['value'] ) ) : ?>
        <span <?php echo $form->print_render_attribute_string( 'icon-align' ); ?>>
				<?php Icons_Manager::render_icon( $item['custom_button_selected_icon'], [ 'aria-hidden' => 'true' ] ); ?>
			</span>
      <?php endif; ?>
			<span <?php echo $form->print_render_attribute_string( 'text' . $item_index ); ?>><?php echo $item['custom_button_text']; ?></span>
		</span>
    <?php
  }
}
