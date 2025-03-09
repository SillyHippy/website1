<?php
namespace Tenweb_Builder\Controls\DraggableOrderControl;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class DraggableOrderControl extends \Elementor\Base_Data_Control {
    public function get_type() {
        return 'draggable_order_control';
    }

    public function enqueue() {
        wp_enqueue_script('jquery-ui-sortable');
        add_action('elementor/editor/after_enqueue_scripts', function() {
            wp_enqueue_script('draggable_order_control', TWBB_URL . '/controls/draggable-order/draggable_order_control.js', ['jquery'], false, true);
            });
    }

    public function content_template() {
        $control_uid = $this->get_control_uid();
        //phpcs:disable
        ?>
        <div class="draggable_order_control">
            <# if ( data.label ) {#>
            <label for="<?php echo esc_attr( $control_uid ); ?>" class="elementor-control-title">{{{ data.label }}}</label>
            <# } #>
            <ul class="draggable-order-list">
                <#
                var dataArray = [];
                if (typeof data.controlValue === 'string' && data.controlValue.trim() !== '') {
                try {
                dataArray = JSON.parse(data.controlValue);
                } catch (e) {
                console.error('Failed to parse data:', e);
                }
                }

                if (dataArray.length) {
                _.each(dataArray, function(item, index) {
                #>
                <li class="draggable-order-item" data-id="{{ item.id }}">
                    <span class="draggable-order-label">{{ item.label }}</span>
                    <span class="dashicons dashicons-move"></span>
                </li>
                <#
                });
                } else {
                var sampleData = [
                { id: 1, label: 'Data 1' },
                { id: 2, label: 'Data 2' },
                { id: 3, label: 'Data 3' }
                ];
                _.each(sampleMetasampleData, function(item, index) {
                #>
                <li class="draggable-order-item" data-id="{{ meta.id }}">
                    <span class="draggable-order-label">{{ item.label }}</span>
                    <span class="dashicons dashicons-move"></span>
                </li>
                <# });
                }
                #>
            </ul>
            <input type="hidden" data-setting="{{ data.name }}" value="{{ JSON.stringify(data.controlValue) }}">
        </div>
        <?php
        //phpcs:enable
    }

    public function get_default_settings() {
        return [
            'label_block' => true,
        ];
    }
}
