<?php
namespace Tenweb_Builder\ElementorPro\Modules\Forms\Fields;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Acceptance extends Field_Base {

	public function get_type() {
		return 'acceptance';
	}

	public function get_name() {
		return esc_html__( 'Acceptance', 'elementor-pro' );
	}

	public function update_controls( $widget ) {
		$elementor = \Elementor\Plugin::instance();

		$control_data = $elementor->controls_manager->get_control_from_stack( $widget->get_unique_name(), 'form_fields' );

		if ( is_wp_error( $control_data ) ) {
			return;
		}

		$field_controls = [
			'acceptance_text' => [
				'name' => 'acceptance_text',
				'label' => esc_html__( 'Acceptance Text', 'elementor-pro' ),
				'type' => Controls_Manager::TEXTAREA,
				'condition' => [
					'field_type' => $this->get_type(),
				],
				'tab' => 'content',
				'inner_tab' => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			'checked_by_default' => [
				'name' => 'checked_by_default',
				'label' => esc_html__( 'Checked by Default', 'elementor-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'condition' => [
					'field_type' => $this->get_type(),
				],
				'tab' => 'content',
				'inner_tab' => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
            'acceptance_url' => [
                'name' => 'acceptance_url',
                'label' => esc_html__( 'Acceptance Text Link', 'textdomain' ),
                'type' => \Elementor\Controls_Manager::URL,
                'options' => [ 'url', 'is_external', 'nofollow' ],
                'default' => [
                    'url' => '',
                    'is_external' => true,
                    'nofollow' => true,
                    // 'custom_attributes' => '',
                ],
                'label_block' => true,
            ],
		];

		$control_data['fields'] = $this->inject_field_controls( $control_data['fields'], $field_controls );
		$widget->update_control( 'form_fields', $control_data );
	}

	public function render( $item, $item_index, $form ) {
		$label = '';
		$form->add_render_attribute( 'input' . $item_index, 'class', 'elementor-acceptance-field' );
		$form->add_render_attribute( 'input' . $item_index, 'type', 'checkbox', true );
		if ( ! empty( $item['acceptance_text'] ) ) {
            $acceptance_url = $item['acceptance_url'];
            $link = $item['acceptance_text'];
            if( !empty($acceptance_url['url'])) {
                $link = $this->render_acceptance_link($item['acceptance_text'], $acceptance_url['url'], $acceptance_url['is_external'], $acceptance_url['nofollow'], $acceptance_url['custom_attributes']);
            }
  			$label = '<label for="' . $form->get_attribute_id( $item ) . '">' . $link . '</label>';
		}

		if ( ! empty( $item['checked_by_default'] ) ) {
			$form->add_render_attribute( 'input' . $item_index, 'checked', 'checked' );
		}

		?>
		<div class="elementor-field-subgroup">
			<span class="elementor-field-option">
				<input <?php $form->print_render_attribute_string( 'input' . $item_index ); ?>>
				<?php // PHPCS - the variables $label is safe.
				echo $label; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</span>
		</div>
		<?php
	}

    /* 10WEB custom function to add link on Acceptance */
    public function render_acceptance_link($text = '', $url = '', $blank = '', $nofollow = '', $custom_attr = '') {
        $html = '<a href="' . esc_url($url) . '"';
        if ( $blank ) {
            $html .= ' target="_blank"';
        }
        if ( $nofollow ) {
            $html .= ' rel="nofollow"';
        }
        if( $custom_attr ) {
            $attributes = explode(',', $custom_attr);
            foreach ($attributes as $attr ) {
                $attrItem = explode('|', $attr);
                if( isset($attrItem[0]) && isset($attrItem[1]) ) {
                    $html .= ' ' . esc_attr($attrItem[0]) . '="' . esc_attr($attrItem[1]) . '"';
                }
            }
        }
        $html .= '>'. esc_html($text);
        $html .= '</a>';
        return $html;
    }
}
