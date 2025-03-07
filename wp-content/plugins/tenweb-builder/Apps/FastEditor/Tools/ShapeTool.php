<?php

namespace Tenweb_Builder\FastEditor\Tools;

class ShapeTool extends FastEditorTool
{
    public string $dataAttr = 'data-shape-tool';
    public function getToolContent() {
        $shape_tool_html = '';
        if( $this->visibilityCheck() ) {
            $shape_tool_html = "
                <div class='twbb-shape-tool-container twbb-fe-onedit-tool twbb-fe-right-border twbb-fe-tool twbb-fe-select-tool' " .
                "data-control='" . $this->controlName . "' data-tool='shape' data-analytics='Shape'>
                    <div class='twbb-fet-tooltip'>" . __('Shape', 'tenweb-builder') . "</div>
                    <span class='twbb-shape-tool twbb-fe-selected-display twbb-fe-tool'></span>
                    <ul class='twbb-fe-dropdown'></ul>
                </div>";
        }
        return $shape_tool_html;
    }

    public function editorScripts() {}

    public function frontendScripts() {
        wp_enqueue_script('twbb-shape-frontend-script', TWBB_URL . '/Apps/FastEditor/assets/scripts/shape_frontend.js', ['jquery', 'twbb-fe-tool-script'], TWBB_VERSION);
        wp_enqueue_script('twbb-select-tool-frontend-script', TWBB_URL . '/Apps/FastEditor/assets/scripts/select-tool_frontend.js', ['jquery'], TWBB_VERSION, TRUE);
        wp_enqueue_script('twbb-dropdown-select-tool-frontend-script', TWBB_URL . '/Apps/FastEditor/assets/scripts/dropdown-select-tool_frontend.js', ['jquery'], TWBB_VERSION);
    }
    public function frontendStyles() {
        wp_enqueue_style( 'twbb-select-tool-frontend-style', TWBB_URL . '/Apps/FastEditor/assets/styles/select-tool_frontend.css', array(), TWBB_VERSION );
        wp_enqueue_style( 'twbb-shape-frontend-style', TWBB_URL . '/Apps/FastEditor/assets/styles/shape_frontend.css', array(), TWBB_VERSION );
    }

}