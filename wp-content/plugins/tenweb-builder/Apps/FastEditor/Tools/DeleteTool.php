<?php

namespace Tenweb_Builder\FastEditor\Tools;

class DeleteTool extends FastEditorTool
{
    public function getToolContent() {
        $delete_html = '';
        if( $this->visibilityCheck() ) {
            $delete_html = "<div class='twbb-delete-tool-container twbb-fe-tool' data-tool='delete'>
                <div class='twbb-fet-tooltip'>" . __('Delete', 'tenweb-builder') . "</div>
                <span class='twbb-delete-tool' data-analytics='Delete'>
                <img src='". esc_url(TWBB_URL . '/Apps/FastEditor/assets/images/recycle_bin_Icon.svg' ). "'></span></div></div>";
        }
        return $delete_html;
    }

    public function editorScripts() {}

    public function frontendScripts() {
        wp_enqueue_script('twbb-delete-tool-frontend-script', TWBB_URL . '/Apps/FastEditor/assets/scripts/delete-tool_frontend.js', ['jquery', 'twbb-fe-tool-script'], TWBB_VERSION, TRUE);
    }
    public function frontendStyles() {}

}