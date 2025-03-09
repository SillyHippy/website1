<?php

namespace Tenweb_Builder\FastEditor\Tools;

class ChoiceTypeTool extends FastEditorTool
{

    public function getToolContent() {
        $choice_type_tool_html = '';
        if( $this->visibilityCheck() ) {
            $tooltipClass = !empty($this->controlData[0]['tool']) ? " twbb-fet-tooltip-".$this->controlData[0]['tool'] : '';
            $cssClass = '';
            if(isset($this->controlData[0]['tool'])){
                $lowercaseText = strtolower($this->controlData[0]['tool']);
                $cssClass = preg_replace('/[^a-z0-9]+/', '-', $lowercaseText);
                $cssClass = trim($cssClass, '-');
                $cssClass = 'twbb_tool_'.$cssClass;
            }


            $choice_type_tool_html = "
                <div class='".$cssClass." twbb-choice-tool-container twbb-fe-onedit-tool twbb-fe-right-border'
                 data-control='" . esc_attr($this->controlName) . "' 
                 data-tool='" . esc_attr($this->controlData[0]['tool']) . "' 
                 data-analytics='" . esc_attr($this->controlData[0]['analytics']) . "'>";
            $choice_type_tool_html .= "<div class='twbb-fet-tooltip".$tooltipClass."'>" . esc_html($this->tooltip) . "</div>";
            foreach ( $this->controlData[0]['options'] as $key => $value ) {
                $choice_type_tool_html .= " <span class='twbb-choice-tool' onclick='twbb_onToolClick(this,\"choice_tool\")' data-tool-value='" . esc_attr($value) . "'>
                        <img src='". esc_url(TWBB_URL . '/Apps/FastEditor/assets/images/' . $key . '_black.svg' ) . "'>
                    </span>";
            }
            $choice_type_tool_html .= "</div>";
        }
        return $choice_type_tool_html;
    }

    public function editorScripts() {}

    public function frontendScripts() {
        wp_enqueue_script('twbb-choice-type-tool-script', TWBB_URL . '/Apps/FastEditor/assets/scripts/choice-tool_frontend.js', ['jquery', 'twbb-fe-tool-script'], TWBB_VERSION);
    }
    public function frontendStyles() {
        wp_enqueue_style( 'twbb-choice-type-tool-frontend-style', TWBB_URL . '/Apps/FastEditor/assets/styles/choice-tool_frontend.css', array(), TWBB_VERSION );
    }

}