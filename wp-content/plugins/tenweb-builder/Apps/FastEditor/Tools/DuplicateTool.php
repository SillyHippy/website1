<?php

namespace Tenweb_Builder\FastEditor\Tools;

class DuplicateTool extends FastEditorTool
{
    public function getToolContent() {
        $duplicate_html = '';
        if( $this->visibilityCheck() ) {
            $duplicate_html = "<div class='twbb-duplicate-tool-container twbb-fe-tool' data-tool='duplicate'>
<div class='twbb-fet-tooltip'>" . __('Duplicate', 'tenweb-builder') . "</div>
<span class='twbb-duplicate-tool' data-analytics='Duplicate'><img src='".
                esc_url( TWBB_URL . '/Apps/FastEditor/assets/images/copy_Icon.svg') . "'></span></div>";
        }
        return $duplicate_html;
    }

    public function editorScripts() {}

    public function frontendScripts() {
        wp_enqueue_script('twbb-duplicate-tool-frontend-script', TWBB_URL . '/Apps/FastEditor/assets/scripts/duplicate-tool_frontend.js', ['jquery', 'twbb-fe-tool-script'], TWBB_VERSION, TRUE);
    }
    public function frontendStyles() {}

}