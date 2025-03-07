<?php

namespace Tenweb_Builder\FastEditor\Tools;

class CountControlTool extends FastEditorTool
{
    public string $dataAttr = '';

    public function getToolContent() {
        $counter_tool_html = '';
        $tool_type = $this->controlData[0]['tool_type'] ?? '';
        $analytics = $this->controlData[0]['analytics'] ?? 'Count Control';
        if( $this->visibilityCheck() ) {
            $counter_tool_html = "
                <div class='twbb-fe-onedit-tool twbb-fe-right-border_after twbb-fe-tool twbb-fe-select-tool twbb-fe-counter-select-tool twbb-fe-right-border' " .
                "data-control='" . esc_attr($this->controlName) . "' data-tool='" . esc_attr($this->controlName) . "' data-analytics='" . esc_attr($analytics) . "'
                data-tool_type='" . esc_attr($tool_type) . "'>
                    <div class='twbb-fet-tooltip'>" . esc_attr($this->tooltip) . "</div>
                    <span class='twbb-count-control-tool'>
                    <div class='twbb-quantity'>
                        <input type='number' class='twbb-count_control' name='count_control' value='' min='0'>
                    </div>
                    </span><ul class='twbb-fe-dropdown twbb-counter-list'></ul></div>";
        }
        return $counter_tool_html;
    }

    public function editorScripts() {}

    public function frontendScripts() {
        wp_enqueue_script('twbb-count-control-frontend-script', TWBB_URL . '/Apps/FastEditor/assets/scripts/count-control-tool_frontend.js', ['jquery', 'twbb-fe-tool-script', 'twbb-dropdown-select-tool-frontend-script'], TWBB_VERSION);
        wp_enqueue_script('twbb-dropdown-select-tool-frontend-script', TWBB_URL . '/Apps/FastEditor/assets/scripts/dropdown-select-tool_frontend.js', ['jquery'], TWBB_VERSION);
    }
    public function frontendStyles() {
        wp_enqueue_style( 'twbb-count-control-frontend-style', TWBB_URL . '/Apps/FastEditor/assets/styles/count-control_frontend.css', array(), TWBB_VERSION );
    }

}