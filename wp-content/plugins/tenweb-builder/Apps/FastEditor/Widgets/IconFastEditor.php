<?php

namespace Tenweb_Builder\FastEditor\Widgets;

class IconFastEditor extends BaseWidgetFastEditor
{
    public $widget = 'Widget_Icon';

    protected function setToolsList()
    {
        // $options should have 'icon name' => 'control value' construction
        $options_alignment = [
            'align_left' => 'left',
            'align_center' => 'center',
            'align_right' => 'right',
        ];
        $counterRange = ['min' => 1, 'max' => 2000 ];
        $this->toolsList = (
            array(
                array(
                    'class' => '\Tenweb_Builder\FastEditor\Tools\ClickTool',
                    'changed-control-data' => array(
                        array(
                            'control_name' => 'elementor-control-media__preview',
                            'title' => 'Media preview',
                            'analytics' => 'Icon media preview',
                            'tooltip' => 'Choose icon',
                        ),
                    ),
                ),
                array(
                    'class' => '\Tenweb_Builder\FastEditor\Tools\URLTool',
                    'changed-control-data' => array(
                        array('control_name' => 'link', 'title' => 'Link', 'analytics' => 'Icon Link'),
                    ),
                ),
                array(
                    'class' => '\Tenweb_Builder\FastEditor\Tools\CountControlTool',
                    'changed-control-data' => array(
                        array(
                            'control_name' => 'size',
                            'title' => 'Size',
                            'options' => array( 'id' => 'number_' . $counterRange['min'] . '_' . $counterRange['max'], 'value' => $counterRange ),
                            'widgetType' => 'icon',
                            'tooltip' => 'Size'
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
                array(
                    'class' => '\Tenweb_Builder\FastEditor\Tools\ColorPickerTool',
                    'changed-control-data' => array(
                        array('control_name' => 'primary_color', 'title' => 'Icon Color', 'tooltip' => 'Color'),
                    ),
                ),
            )
        );
    }

}