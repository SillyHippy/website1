<?php

namespace Tenweb_Builder\FastEditor\Widgets;

class ContainerFastEditor extends BaseWidgetFastEditor
{
    public $widget = 'Container';

    protected function setToolsList()
    {
        $counterRange = ['min' => 1, 'max' => 2000 ];

        $this->toolsList = (
            array(
                array(
                    'class' => '\Tenweb_Builder\FastEditor\Tools\ColorPickerTool',
                    'changed-control-data' => array(
                        array('control_name' => 'background_color', 'title' => 'Container Color', 'tooltip' => 'Background color'),
                    ),
                ),
                array(
                    'class' => '\Tenweb_Builder\FastEditor\Tools\ClickTool',
                    'changed-control-data' => array(
                        array(
                            'control_name' => 'elementor-control-media__preview',
                            'title' => 'Media preview',
                            'analytics' => 'Container media preview',
                            'tooltip' => 'Background images',
                        ),
                    ),
                ),
                array(
                    'class' => '\Tenweb_Builder\FastEditor\Tools\CountControlTool',
                    'changed-control-data' => array(
                        array(
                            'control_name' => 'min_height',
                            'title' => 'Height',
                            'options' => array( 'id' => 'number_' . $counterRange['min'] . '_' . $counterRange['max'], 'value' => $counterRange ),
                            'widgetType' => 'container',
                            'tooltip' => 'Min height'
                        ),
                    ),
                ),
                array(
                    'class' => '\Tenweb_Builder\FastEditor\Tools\CountControlTool',
                    'changed-control-data' => array(
                        array(
                            'control_name' => 'boxed_width',
                            'title' => 'Width',
                            'tool_type' => 'boxed_width',
                            'analytics' => 'Container width',
                            'options' => array( 'id' => 'number_' . $counterRange['min'] . '_' . $counterRange['max'], 'value' => $counterRange ),
                            'widgetType' => 'container',
                            'tooltip' => 'Width'
                        ),
                    ),
                ),
                array(
                    'class' => '\Tenweb_Builder\FastEditor\Tools\CountControlTool',
                    'changed-control-data' => array(
                        array(
                            'control_name' => 'width',
                            'title' => 'Width',
                            'tool_type' => 'width',
                            'analytics' => 'Container width',
                            'options' => array( 'id' => 'number_' . $counterRange['min'] . '_' . $counterRange['max'], 'value' => $counterRange ),
                            'widgetType' => 'container',
                            'tooltip' => 'Width'
                        ),
                    ),
                ),
                array(
                    'class' => '\Tenweb_Builder\FastEditor\Tools\DuplicateTool',
                    'changed-control-data' => [],
                ),
                array(
                    'class' => '\Tenweb_Builder\FastEditor\Tools\DeleteTool',
                    'changed-control-data' => [],
                ),
                array(
                    'class' => '\Tenweb_Builder\FastEditor\Tools\MoreTool',
                    'changed-control-data' => [],
                ),
            )
        );
    }

}