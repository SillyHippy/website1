<?php

namespace Tenweb_Builder\FastEditor\Widgets;

class ProgressBarFastEditor extends BaseWidgetFastEditor
{
    public $widget = 'Widget_Progress';

    protected function setToolsList()
    {
        $counterRange = ['min' => 1, 'max' => 2000 ];

        $this->toolsList = (
            array(
                array(
                  'class' => '\Tenweb_Builder\FastEditor\Tools\WriteWithAITool',
                  'changed-control-data' => array(
                    array('control_name' => 'title', 'title' => 'Title'),
                  ),
                ),
                array(
                    'class' => '\Tenweb_Builder\FastEditor\Tools\CountControlTool',
                    'changed-control-data' => array(
                        array(
                            'control_name' => 'bar_height',
                            'title' => 'Bar Height',
                            'options' => array( 'id' => 'number_' . $counterRange['min'] . '_' . $counterRange['max'], 'value' => $counterRange ),
                            'widgetType' => 'progress',
                            'tooltip' => 'Height'
                        ),
                    ),
                ),
                array(
                    'class' => '\Tenweb_Builder\FastEditor\Tools\CountControlTool',
                    'changed-control-data' => array(
                        array(
                            'control_name' => 'percent',
                            'title' => 'Percentage',
                            'options' => array( 'id' => 'number_' . $counterRange['min'] . '_' . $counterRange['max'], 'value' => $counterRange ),
                            'widgetType' => 'progress',
                            'tooltip' => 'Percentage'
                        ),
                    ),
                ),
                array(
                    'class' => '\Tenweb_Builder\FastEditor\Tools\ColorPickerTool',
                    'changed-control-data' => array(
                        array('control_name' => 'title_color', 'title' => 'Title Color'),
                        array('control_name' => 'bar_inline_color', 'title' => 'Inline Color'),
                        array('control_name' => 'bar_color', 'title' => 'Bar Color'),
                    ),
                ),
            )
        );
    }

}