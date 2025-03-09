<?php

namespace Tenweb_Builder\FastEditor\Tools;

class MoreTool extends FastEditorTool
{
    public function getToolContent() {
        $more_html = '';
        if( $this->visibilityCheck() ) {
            $more_html = "<div class='twbb-more-tool-container twbb-fe-tool' data-tool='more'>
<div class='twbb-fet-tooltip'>" . __('More', 'tenweb-builder') . "</div>
<span class='twbb-more-tool' data-analytics='More'></span></div>";
        }
        return $more_html;
    }

    public function editorScripts() {
        wp_enqueue_style('twbb-more-tool-editor-style', TWBB_URL . '/Apps/FastEditor/assets/styles/more-tool_editor.css', array(), TWBB_VERSION);
    }

    public function frontendScripts() {
        wp_enqueue_script('twbb-more-tool-frontend-script', TWBB_URL . '/Apps/FastEditor/assets/scripts/more-tool_frontend.js', ['jquery', 'twbb-fe-tool-script'], TWBB_VERSION, TRUE);
    }
    public function frontendStyles() {}

}