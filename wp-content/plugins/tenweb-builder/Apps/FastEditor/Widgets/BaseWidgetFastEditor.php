<?php

namespace Tenweb_Builder\FastEditor\Widgets;

abstract class BaseWidgetFastEditor
{
    /**
     * @var string $widget main class or some attribute which describes the widget
     */
    public $widget;

    /**
     * @var array $beforeRemoteRenderWidgetsLists is used for adding tools container before render widgets only for this widgets
     *  for this widgets default tools should be added directly from widgets php (not DefaultWidgets.php)
     */
    private $beforeRemoteRenderWidgetsLists = [
        'Widget_Image_Carousel',
        'Widget_Video',
        'Widget_Image_Gallery',
    ];

    /**
     * @var array $toolsList tools list for every widget, each tool should contain it\'s namespace and
     * control name for that tool in the widget
     */
    public $toolsList;
    public $toolsHTMLContent;

    //collect widget base localized variables from all widget tools
    public $localizedData = array();

    public function process(){
        $this->beforeBuild();
        $this->build();
        $this->afterBuild();
    }

    public function getWidgetLocalizedData() {
        if ( empty($this->toolsList) ) {
            return false;
        }

        foreach ($this->toolsList as $tool) {
            if ( !is_array($tool) ){
                return false;
            }

            $dropdownOptions = $tool['changed-control-data'][0]['options'] ?? array();
            if ( !empty( $dropdownOptions ) && !empty($tool['changed-control-data']) && isset($tool['changed-control-data'][0]['widgetType']) ) {
                $this->localizedData = array_merge( $this->localizedData,
                    [
                        $tool['changed-control-data'][0]['widgetType'] . '_' .
                        $tool['changed-control-data'][0]['control_name'] .
                        '_dropdown_options' => $dropdownOptions
                    ]
                );
            }
        }

        return $this->localizedData;
    }

    public function addToolsToContentAlreadyThere($content, $widget) {
        return $this->addToolsToContent($content, $widget,false);
    }

    public function addToolsToContentNewAdded($content, $widget) {
        return $this->addToolsToContent($content, $widget, true);
    }

    public function addToolsToContent($content, $widget, $isNew) {
        $class = '';
        if( $isNew ) {
            $class = 'new_added';
        }
        if( !empty($content) && $this->isElementorEditMode() && $this->checkWidget($widget) ) {
            if (strpos($content, 'twbb-fast-editor-tools-container') !== false) {
                $str = "</div></div>";
                $len = strlen($str);
                $contentPart = substr($content, 0, -$len);
                $contentPart .= $this->toolsHTMLContent . '</div></div>';
                $content = $contentPart;
            } else {
                $ask_ai_html = '';
                $class .= ' twbb_'.$this->widget;
                if (strpos($this->toolsHTMLContent, 'twbb_ask_to_ai') === false) {
                    $ask_ai_html = $this->getAskAiHtml();
                }
                $smartScale =  get_option('elementor_experiment-smart_scale');
                $class.=' twbb_smart_scale_'.$smartScale;
                $content .= '<div class="twbb-fast-editor-tools-container twbb_ask_to_ai_opened ' . $class . '"><div class="twbb-fe-tools">'.$ask_ai_html . $this->toolsHTMLContent . '</div></div>';
            }
        }

        return $content;
    }

    protected function collectToolsHTML() {
        $html = '';
        if (!empty($this->toolsList)) {
            foreach ($this->toolsList as $tool) {
                if ( is_array($tool) ){
                    $editorTool = new $tool['class']($tool['changed-control-data']);
                } else {
                    $editorTool = new $tool(array());
                }
                $html .= $editorTool->getToolContent();
            }
        }
        $this->toolsHTMLContent = $html;
    }

    protected function beforeBuild() {
        $this->setToolsList();
        $this->collectToolsHTML();
    }
    protected function build() {
        $this->renderContents();
    }

    protected function afterBuild() {}

    protected function renderContents() {
        add_action('elementor/widget/before_render_content', array($this, 'beforeRenderContent'), 10, 1);
        add_filter('elementor/widget/render_content', array( $this, 'addToolsToContentAlreadyThere'), 2, 2);
        add_filter('elementor/widget/print_template', array( $this, 'addToolsToContentNewAdded'), 2, 2);
        add_filter('elementor/container/print_template', array($this, 'addToolsToContainer'), 2, 2 );
    }
    public function addToolsToContainer($template_content, $widget) {
        $ask_ai_html = '';
        if ( !empty($template_content) && $this->widget === 'Container' ) {
            $class = ' twbb_'.$this->widget;
            if (strpos($this->toolsHTMLContent, 'twbb_ask_to_ai') === false) {
                $ask_ai_html = $this->getAskAiHtml();
            }
            $smartScale =  get_option('elementor_experiment-smart_scale');
            $class.=' twbb_smart_scale_'.$smartScale;
            $template_content .= '<div class="twbb-fast-editor-tools-container twbb_ask_to_ai_opened '.$class.'"><div class="twbb-fe-tools">'.$ask_ai_html . $this->toolsHTMLContent . '</div></div>';
        }
        return $template_content;
    }
    public function beforeRenderContent($widget)
    {
        $ask_ai_html = '';
        if ( substr(get_class($widget),10) === $this->widget
                && in_array($this->widget, $this->beforeRemoteRenderWidgetsLists, true)
                && $this->isElementorEditMode() ) {
            $class = ' twbb_'.$this->widget;
            if (strpos($this->toolsHTMLContent, 'twbb_ask_to_ai') === false) {
                $ask_ai_html = $this->getAskAiHtml();
            }
            $smartScale =  get_option('elementor_experiment-smart_scale');
            $class.=' twbb_smart_scale_'.$smartScale;
            echo '<div class="twbb-fast-editor-tools-container twbb_ask_to_ai_opened '.$class.'"><div class="twbb-fe-tools">'.$ask_ai_html . $this->toolsHTMLContent . '</div></div>'; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }
    }
    private function getAskAiHtml()
    {
        $write_with_ai_html = "<div class='twbb-fe-tool twbb_ask_to_ai_empty twbb-ai-front'>
                                    <div class='ask_to_ai_container'>
                                        <span class='twbb_ask_to_ai_button' onclick='twbb_fast_edit_tools_events(this, \"ask_ai\")'><span class='twbb_ask_to_ai_icon'></span>Ask AI</span>
                                        <span class='ask_to_ai_input_container ask_to_ai_disabled'>
                                            <textarea value='' class='twbb_ask_to_ai twbb-fe-dropdown' name='ask_to_ai' type='text' placeholder='Ask AI to edit..'></textarea>
                                            <span class='twbb_ask_to_ai_submit_button'></span>
                                        </span>
                                    </div>
                                </div><div class='twbb-fast-edit-tools' onclick='twbb_fast_edit_tools_events(this, \"tools\")'>";
        return $write_with_ai_html;
    }

    protected function setToolsList() {
        $this->toolsList = array();
    }

    protected function addToolsList($toolsArr) {
        $this->toolsList = array_merge($this->toolsList,$toolsArr);
    }

    protected function isElementorEditMode() {
        if ( !\Elementor\Plugin::instance()->editor->is_edit_mode() ) {
            return false;
        }
        return true;
    }

    //check does the $content contains widget class so to add tool html only for that widget
    protected function checkWidget($widgetClass) {
        $classToArray = explode("\\",get_class($widgetClass));
        $widgetClass = $classToArray[count($classToArray) - 1];
        if ( in_array($widgetClass, $this->beforeRemoteRenderWidgetsLists, true) ) {
            return false;
        }
        if ( $this->widget === 'All_Widgets') {
            return true;
        }
        if ( is_array($this->widget) ) {
            foreach ($this->widget as $widget) {
                if ( $widget === $widgetClass ) {
                    return true;
                }
            }
        } else {
            if ( $this->widget === $widgetClass ) {
                return true;
            }
        }
        return false;
    }
}