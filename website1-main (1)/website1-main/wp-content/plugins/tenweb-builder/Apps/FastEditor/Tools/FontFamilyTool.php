<?php

namespace Tenweb_Builder\FastEditor\Tools;

use Elementor\Fonts;

class FontFamilyTool extends FastEditorTool
{
    public string $dataAttr = 'data-font-family-tool';

    public function getToolContent() {
        $font_family_html = '';
        if( $this->visibilityCheck() ) {
            $font_family_html = "<div class='twbb-font-family-tool-container  twbb-fe-right-border_after twbb-fe-select-tool twbb-fe-onedit-tool twbb-fe-tool' " .
                "data-control='" . esc_attr($this->controlName) . "'  data-tool='font_family' data-analytics='Font Family'>
                <div class='twbb-fet-tooltip'>" . __('Font family', 'tenweb-builder') . "</div>
                <span class='twbb-fe-selected-display twbb-font-family-tool'>" . esc_html("Aa") . "</span>
                <div class='twbb-fe-dropdown twbb-select-tool-search-input'><input id='twbb-select-input' placeholder='Search'></div>";
            $font_family_html .= "<ul class='twbb-fe-dropdown twbb-font-family-list'></ul></div>";
        }
        return $font_family_html;
    }

    public function editorScripts() {}

    public function frontendScripts() {
        wp_enqueue_script('twbb-font-family-tool-frontend-script', TWBB_URL . '/Apps/FastEditor/assets/scripts/font-family-tool_frontend.js', ['jquery', 'twbb-fe-tool-script', 'twbb-dropdown-select-tool-frontend-script'], TWBB_VERSION, TRUE);
        wp_enqueue_script('twbb-select-tool-frontend-script', TWBB_URL . '/Apps/FastEditor/assets/scripts/select-tool_frontend.js', ['jquery'], TWBB_VERSION, TRUE);
        wp_enqueue_script('twbb-dropdown-select-tool-frontend-script', TWBB_URL . '/Apps/FastEditor/assets/scripts/dropdown-select-tool_frontend.js', ['jquery'], TWBB_VERSION);
    }
    public function frontendStyles() {
        wp_enqueue_style( 'twbb-select-tool-frontend-style', TWBB_URL . '/Apps/FastEditor/assets/styles/select-tool_frontend.css', array(), TWBB_VERSION );
        wp_enqueue_style( 'twbb-font-family-tool-frontend-style', TWBB_URL . '/Apps/FastEditor/assets/styles/font-family_frontend.css', array(), TWBB_VERSION );
    }

}