<?php

namespace Tenweb_Builder\FastEditor\Tools;

class DropdownSelectTool extends FastEditorTool
{

    public function getToolContent() {

        $dataTool = $this->controlData[0]['tool'] ?? '';

        $toolText = $this->controlData[0]['tool_text'];
        $analytics = $this->controlData[0]['analytics'] ?? '';
        $dropdownOptions_html = '';

        $lowercaseText = strtolower($dataTool);
        $cssClass = preg_replace('/[^a-z0-9]+/', '-', $lowercaseText);
        $cssClass = trim($cssClass, '-');
        $cssClass = 'twbb_tool_'.$cssClass;

        if( $this->visibilityCheck() ) {
            $dropdownOptions_html = "<div data-control='" . esc_attr($this->controlName)
                . "' class='".$cssClass." twbb-dropdown-select-tool-container twbb-tool-container twbb-fe-right-border_after twbb-fe-select-tool twbb-fe-onedit-tool twbb-fe-tool' 
                data-tool='" . esc_attr($dataTool) . "' data-analytics='" . esc_attr($analytics) . "'>
                <span class='twbb-fe-selected-display twbb-fe-tool'>" . esc_html($toolText) . "</span><ul class='twbb-fe-dropdown twbb-pen-menu'>";
            $dropdownOptions_html .= "</ul></div>";
        }
        return $dropdownOptions_html;
    }

    public function editorScripts() {}

    public function frontendScripts() {
        wp_enqueue_script('twbb-dropdown-select-tool-frontend-script', TWBB_URL . '/Apps/FastEditor/assets/scripts/dropdown-select-tool_frontend.js', ['jquery', 'twbb-fe-tool-script'], TWBB_VERSION);
        wp_enqueue_script('twbb-html-tag-tool-frontend-script', TWBB_URL . '/Apps/FastEditor/assets/scripts/html-tag-tool_frontend.js', ['jquery', 'twbb-dropdown-select-tool-frontend-script'], TWBB_VERSION);
        wp_enqueue_script('twbb-select-tool-frontend-script', TWBB_URL . '/Apps/FastEditor/assets/scripts/select-tool_frontend.js', ['jquery'], TWBB_VERSION, TRUE);
    }
    public function frontendStyles() {
        wp_enqueue_style( 'twbb-dropdown-select-tool-frontend-style', TWBB_URL . '/Apps/FastEditor/assets/styles/dropdown-select-tool_frontend.css', array(), TWBB_VERSION );
        wp_enqueue_style( 'twbb-select-tool-frontend-style', TWBB_URL . '/Apps/FastEditor/assets/styles/select-tool_frontend.css', array(), TWBB_VERSION );
    }

}