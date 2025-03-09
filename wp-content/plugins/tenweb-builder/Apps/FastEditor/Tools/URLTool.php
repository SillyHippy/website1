<?php

namespace Tenweb_Builder\FastEditor\Tools;

class URLTool extends FastEditorTool
{
    public function getToolContent() {

        $url_html = '';
        if( $this->visibilityCheck() ) {
            $url_html = "
                <div class='twbb-url-tool-container twbb-fe-onedit-tool twbb-fe-right-border' data-control='" . esc_attr($this->controlName) . "'  data-tool='url'>
                    <div class='twbb-fet-tooltip'>" . __('Link', 'tenweb-builder') . "</div>
                    <span class='twbb-url-tool'>
                        <img src='". esc_url(TWBB_URL . '/Apps/FastEditor/assets/images/link_icon_black.svg' ) . "'>
                    </span>
                    <div class='twbb-url-tool-content' style='display: none'>
                        <span class='twbb-url-tool-content-title'>".esc_html__('Add link')."</span>                        
                        <input type='url' placeholder='".esc_attr('Enter Link')."' value='' class='twbb-url-tool-url'>
                        <span class='twbb-url-tool-content-button' data-analytics='" . esc_attr($this->controlData[0]['analytics']) . "'>".esc_html__('Add')."</span>
                    </div>
                </div>";
        }
        return $url_html;
    }

    public function editorScripts() {}

    public function frontendScripts() {
        wp_enqueue_script('twbb-url-tool-frontend-script', TWBB_URL . '/Apps/FastEditor/assets/scripts/url_tool_frontend.js', ['jquery'], TWBB_VERSION);
    }
    public function frontendStyles() {
        wp_enqueue_style( 'twbb-url-tool-frontend-style', TWBB_URL . '/Apps/FastEditor/assets/styles/url_tool_frontend.css', array(), TWBB_VERSION );
    }

}