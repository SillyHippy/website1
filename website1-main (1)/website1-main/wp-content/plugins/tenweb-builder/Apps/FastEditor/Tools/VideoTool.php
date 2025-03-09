<?php

namespace Tenweb_Builder\FastEditor\Tools;

class VideoTool extends FastEditorTool
{
    public function getToolContent() {

        $video_html = '';
        if( $this->visibilityCheck() ) {
            $video_html = "
                <div class='twbb-video-tool-container twbb-fe-onedit-tool twbb-fe-right-border' data-control='" . esc_attr($this->controlName) . "'  data-tool='video'>
                    <span class='twbb-video-tool'>
                        <div class='twbb-video-tool-cont-icon twbb-video-tool-upload-cont'>
                            <div class='twbb-fet-tooltip'>" . __('Source', 'tenweb-builder') . "</div>
                            <img class='twbb-video-tool-upload' src='". esc_url(TWBB_URL . '/Apps/FastEditor/assets/images/video_icon_black.svg' ). "'>
                        </div>
                        <div class='twbb-video-tool-cont-icon twbb-video-tool-link-cont'>
                            <div class='twbb-fet-tooltip'>" . __('Link', 'tenweb-builder') . "</div>
                            <img class='twbb-video-tool-link' src='".esc_url( TWBB_URL . '/Apps/FastEditor/assets/images/link_icon_black.svg') . "'>
                        </div>
                    </span>
                    <div class='twbb-video-tool-content twbb-video-link-tool-content' style='display: none'>
                        <span class='twbb-video-tool-content-title'>".esc_html__('Select source')."</span>
                        <div data-control='video_type' class='twbb-tool-container twbb-fe-select-tool twbb-fe-onedit-tool twbb-fe-tool'>
                        <span class='twbb-fe-selected-display twbb-fe-tool twbb-video-tool'>" . esc_html("YouTube") . "</span>
                        <ul class='twbb-fe-dropdown twbb-pen-menu twbb-video-tool-content-row'>
                            <li data-action='youtube'>".esc_html('YouTube')."</li>
                            <li data-action='vimeo'>".esc_html('Vimeo')."</li>
                            <li data-action='dailymotion'>".esc_html('Dailymotion')."</li>
                            <li data-action='videopress'>".esc_html('VideoPress')."</li>
                            <li data-action='hosted'>".esc_html('Self Hosted')."</li>
                        </ul>
                        </div>
                    </div>
                    <div class='twbb-video-tool-content twbb-video-source-tool-content' style='display: none'>
                        <span class='twbb-video-tool-content-title twbb-video-tool-link-title'>".esc_html__('Add link')."</span>
                        <span style='display: none' class='twbb-video-tool-content-title twbb-video-tool-file-title'>".esc_html__('Add file')."</span>
                        <div class='twbb-video-tool-url-container'>
                            <input style='display: none' type='url' placeholder='".esc_attr('Enter video link')."' value='' class='twbb-video-tool-url' data-analytics='Video'>
                            <a href='' target='_blank' class='twbb-video-tool-open-url'></a>
                        </div>
                        <span class='twbb-video-self-hosted'>".esc_html('Choose file')."</span>
                    </div>
                </div>";
        }
        return $video_html;
    }

    public function editorScripts() {}

    public function frontendScripts() {

        wp_enqueue_script('twbb-video-frontend-script', TWBB_URL . '/Apps/FastEditor/assets/scripts/video_frontend.js', ['jquery', 'twbb-fe-tool-script'], TWBB_VERSION);
    }
    public function frontendStyles() {
        wp_enqueue_style( 'twbb-video-frontend-style', TWBB_URL . '/Apps/FastEditor/assets/styles/video_frontend.css', array(), TWBB_VERSION );
    }

}