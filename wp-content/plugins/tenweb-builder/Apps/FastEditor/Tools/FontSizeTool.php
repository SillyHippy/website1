<?php

namespace Tenweb_Builder\FastEditor\Tools;

class FontSizeTool extends FastEditorTool
{
    public string $dataAttr = 'data-font-size-tool';

    public function getToolContent() {
        $font_size_tool_html = '';
        if( $this->visibilityCheck() ) {
            $font_size_tool_html = "
                <div class='twbb-font_size-tool-container twbb-fe-onedit-tool twbb-fe-right-border_after twbb-fe-tool twbb-fe-select-tool' " .
                "data-control='" . $this->controlName . "' data-tool='font_size' data-analytics='Font size'>
                    <div class='twbb-fet-tooltip'>" . __('Font size', 'tenweb-builder') . "</div>
                    <span class='twbb-font_size-tool twbb-count-control-tool'>
                    <div class='twbb-quantity'>
                        <input type='number' class='twbb-font_size' name='font_size' value='' min='0'>
                    </div>
                    </span><ul class='twbb-fe-dropdown twbb-counter-list'></ul></div>";
        }
        return $font_size_tool_html;
    }

    public function editorScripts() {}

    public function frontendScripts() {
        wp_enqueue_script('twbb-font-size-frontend-script', TWBB_URL . '/Apps/FastEditor/assets/scripts/font-size_frontend.js', ['jquery', 'twbb-count-control-frontend-script', 'twbb-dropdown-select-tool-frontend-script'], TWBB_VERSION);
        wp_enqueue_script('twbb-dropdown-select-tool-frontend-script', TWBB_URL . '/Apps/FastEditor/assets/scripts/dropdown-select-tool_frontend.js', ['jquery'], TWBB_VERSION);
    }
    public function frontendStyles() {
        wp_enqueue_style( 'twbb-font-size-frontend-style', TWBB_URL . '/Apps/FastEditor/assets/styles/font-size_frontend.css', array(), TWBB_VERSION );
    }

}