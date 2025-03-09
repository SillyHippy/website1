<?php

namespace Tenweb_Builder\FastEditor\Tools;

class FontStyleTool extends FastEditorTool
{
    public function getToolContent() {
        $fontStyle_html = '';
        if( $this->visibilityCheck() ) {
            $fontStyle_html = "<div data-control='" . esc_attr($this->controlName) . "' class='twbb-font_style-tool-container twbb-fe-right-border_after twbb-fe-select-tool twbb-fe-onedit-tool twbb-fe-tool' data-tool='style' data-analytics='Font Style'>
                <span class='twbb-fe-selected-display twbb-fe-tool twbb-font_style-tool'>" . esc_html("Style") . "</span><ul class='twbb-fe-dropdown'>";
            $fontStyle_html .= "</ul></div>";
        }
        return $fontStyle_html;
    }

    public function editorScripts() {}

    public function frontendScripts() {
        wp_enqueue_script('twbb-font-style-frontend-script', TWBB_URL . '/Apps/FastEditor/assets/scripts/font-style_frontend.js', ['jquery', 'twbb-fe-tool-script','twbb-dropdown-select-tool-frontend-script'], TWBB_VERSION);
        wp_enqueue_script('twbb-select-tool-frontend-script', TWBB_URL . '/Apps/FastEditor/assets/scripts/select-tool_frontend.js', ['jquery'], TWBB_VERSION, TRUE);
        wp_enqueue_script('twbb-dropdown-select-tool-frontend-script', TWBB_URL . '/Apps/FastEditor/assets/scripts/dropdown-select-tool_frontend.js', ['jquery'], TWBB_VERSION);
    }
    public function frontendStyles() {
        wp_enqueue_style( 'twbb-font-style-frontend-style', TWBB_URL . '/Apps/FastEditor/assets/styles/font-style_frontend.css', array(), TWBB_VERSION );
        wp_enqueue_style( 'twbb-select-tool-frontend-style', TWBB_URL . '/Apps/FastEditor/assets/styles/select-tool_frontend.css', array(), TWBB_VERSION );
    }

}