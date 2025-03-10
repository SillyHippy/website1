<?php

namespace Tenweb_Builder\FastEditor\Widgets;

class ImageFastEditor extends BaseWidgetFastEditor
{
    public $widget = 'Widget_Image';

    protected function setToolsList()
    {
        // $options should have 'icon name' => 'control value' construction
        $options_alignment = [
            'align_left' => 'left',
            'align_center' => 'center',
            'align_right' => 'right',
        ];
        $this->toolsList = (
            array(
                array(
                    'class' => '\Tenweb_Builder\FastEditor\Tools\WriteWithAITool',
                    'changed-control-data' => array(
                        array('control_name' => 'twb-ai-image-button', 'title' => 'Image generation', 'generate_type'=>'image'),
                    ),
                ),
                array(
                    'class' => '\Tenweb_Builder\FastEditor\Tools\ClickTool',
                    'changed-control-data' => array(
                        array(
                            'control_name' => 'elementor-control-media__preview',
                            'title' => 'Media preview',
                            'analytics' => 'Image media preview',
                            'tooltip' => 'Choose image'
                        ),
                    ),
                ),
                array(
                    'class' => '\Tenweb_Builder\FastEditor\Tools\ChoiceTypeTool',
                    'changed-control-data' => array(
                        array(
                            'control_name' => 'align',
                            'title' => 'Align',
                            'options' => $options_alignment,
                            'tool'=> 'align',
                            'analytics' => 'Image Alignment',
                            'tooltip' => 'Alignment',
                        ),
                    ),
                ),
            )
        );
    }

}