<?php

namespace Tenweb_Builder\FastEditor\Tools;

class ColorPickerTool extends FastEditorTool
{
    public string $dataAttr = 'data-color-picker-tool';
    public function getToolContent() {
        $color_html = '';
        if( $this->visibilityCheck() ) {
            if( count($this->controlData) > 1 ) {
                $color_html = "
                <div class='twbb-color_picker-tool-container twbb-color_picker-tool-container-multiple twbb-fe-onedit-tool twbb-fe-right-border_after'>  
                    <div class='twbb-fet-tooltip'>" . __('Colors', 'tenweb-builder') . "</div>                  
                    <div class='color-picker'></div>
                    <span class='twbb-color_picker-tool twbb-color_picker-open-menu twbb-fe-tool'>
                    </span>
                    <div class='twbb-color_picker-tool-content' style='display: none'>";
                        foreach ( $this->controlData as $data ) {
                            $color_html .= "
                            <div class='twbb-color_picker-tool-row' data-tool='color_picker'>
                                <div class='twbb-color_picker-title'>".esc_html($data['title'])."</div>
                                <div class='twbb-color_picker-control twbb-color_picker-open' data-control='" . esc_attr($data['control_name']) . "' data-tool='color_picker' data-analytics='" . esc_attr($data['title']) . "'>
                                    <span class='wbls-pickr-run'></span>
                                </div>
                            </div>";
                        }
                $color_html .= "
                    </div>
                </div>";
            }
            else {
                $wbb_color_picker_tooltip_single = "";
                if(!empty($this->tooltip)){
                    $wbb_color_picker_tooltip_single = "<div class='twbb-fet-tooltip'>" . esc_html($this->tooltip) . "</div>";
                }
                $color_html = "
                <div class='twbb-color_picker-tool-container twbb-color_picker-tool-container-single twbb-fe-onedit-tool twbb-fe-right-border_after'>
                    ".$wbb_color_picker_tooltip_single."
                    <div class='color-picker'></div>
                    <div class='twbb-color_picker-open twbb-fe-tool' data-control='" . esc_attr($this->controlName) . "' data-tool='color_picker' data-analytics='Color'>
                    <span class='twbb-color_picker-tool'>                   
                    </span>
                    </div>
                </div>";
            }
        }
        return $color_html;
    }

    public function editorScripts() {}

    public function frontendScripts() {
        wp_enqueue_script('twbb-color-picker-frontend-script', TWBB_URL . '/Apps/FastEditor/assets/scripts/color-picker_frontend.js', ['jquery','twbb-fe-tool-script'], TWBB_VERSION);
        if ( defined('ELEMENTOR_ASSETS_URL') ) {
            $assets_url = ELEMENTOR_ASSETS_URL;
            wp_enqueue_script(
                'pickr_el',
                "{$assets_url}lib/pickr/pickr.min.js",
                [],
                '1.5.0'
            );
            wp_enqueue_style(
                'pickr_el',
                "{$assets_url}lib/pickr/themes/monolith.min.css",
                [],
                '1.5.0'
            );
        }
    }
    public function frontendStyles() {
        wp_enqueue_style( 'twbb-color-picker-frontend-style', TWBB_URL . '/Apps/FastEditor/assets/styles/color-picker_frontend.css', array(), TWBB_VERSION );
    }

}