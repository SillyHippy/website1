<?php

namespace Tenweb_Builder\FastEditor\Widgets;

class ImageBoxFastEditor extends BaseWidgetFastEditor
{
    public $widget = 'Widget_Image_Box';

    protected function setToolsList()
    {
        // $options should have 'icon name' => 'control value' construction
        $options_alignment = [
            'align_left' => 'left',
            'align_center' => 'center',
            'align_right' => 'right',
        ];
        $options_position = [
            'position_left' => 'left',
            'position_top' => 'top',
            'position_right' => 'right',
        ];
        $this->toolsList = (
            array(
                array(
                    'class' => '\Tenweb_Builder\FastEditor\Tools\WriteWithAITool',
                    'changed-control-data' => array(
                    array('control_name' => 'description_text', 'title' => 'Description text', 'generate_type'=>'all'),
                    ),
                ),
                array(
                    'class' => '\Tenweb_Builder\FastEditor\Tools\ClickTool',
                    'changed-control-data' => array(
                        array(
                            'control_name' => 'elementor-control-media__preview',
                            'title' => 'Media preview',
                            'analytics' => 'Image Box media preview',
                            'tooltip' => 'Choose image'
                        ),
                    ),
                ),
                array(
                    'class' => '\Tenweb_Builder\FastEditor\Tools\ChoiceTypeTool',
                    'changed-control-data' => array(
                        array(
                            'control_name' => 'text_align',
                            'title' => 'Align',
                            'options' => $options_alignment,
                            'tool'=> 'align',
                            'analytics' => 'Image Alignment',
                            'tooltip' => 'Alignment',
                        ),
                    ),
                ),
                array(
                    'class' => '\Tenweb_Builder\FastEditor\Tools\ChoiceTypeTool',
                    'changed-control-data' => array(
                        array(
                            'control_name' => 'position',
                            'title' => 'Position',
                            'options' => $options_position,
                            'tool'=> 'position',
                            'analytics' => 'Image Position',
                            'tooltip' => 'Image Position',
                        ),
                    ),
                ),
            )
        );
    }

}