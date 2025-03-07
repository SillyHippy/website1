<?php

namespace Tenweb_Builder\FastEditor\Tools;

class ClickTool extends FastEditorTool
{
    public function getToolContent() {
        $click_tool_html = '';
        if( $this->visibilityCheck() ) {
            $click_tool_html = "
                <div class='twbb-click-tool-container twbb-fe-onedit-tool twbb-fe-right-border' data-control='" . esc_attr($this->controlName) . "'  data-tool='click' data-analytics='" . esc_attr($this->controlData[0]['analytics']) . "'>
                    <div class='twbb-fet-tooltip'>" . esc_attr($this->tooltip) . "</div>
                    <span class='twbb-click-tool' onclick='twbb_onToolClick(this,\"click_tool\")'>
                    </span>
                </div>";
        }
        return $click_tool_html;
    }

    public function editorScripts() {}

    public function frontendScripts() {
        wp_enqueue_script('twbb-click-tool-frontend-script', TWBB_URL . '/Apps/FastEditor/assets/scripts/click_tool_frontend.js', ['jquery', 'twbb-fe-tool-script'], TWBB_VERSION);
    }
    public function frontendStyles() {
        wp_enqueue_style( 'twbb-click-tool-frontend-style', TWBB_URL . '/Apps/FastEditor/assets/styles/click_tool_frontend.css', array(), TWBB_VERSION );
    }

}