<?php

namespace Tenweb_Builder\FastEditor\Widgets;

class CounterFastEditor extends BaseWidgetFastEditor
{
    public $widget = 'Widget_Counter';

    protected function setToolsList()
    {
        $counterRange = ['min' => 1, 'max' => 2000 ];

        $this->toolsList = (
            array(
                array(
                    'class' => '\Tenweb_Builder\FastEditor\Tools\ColorPickerTool',
                    'changed-control-data' => array(
                        array('control_name' => 'number_color', 'title' => 'Text Color', 'tooltip' => 'Text color'),
                        array('control_name' => 'title_color', 'title' => 'Title Color', 'tooltip' => 'Title color'),
                    ),
                ),
                array(
                    'class' => '\Tenweb_Builder\FastEditor\Tools\CountControlTool',
                    'changed-control-data' => array(
                        array(
                            'control_name' => 'starting_number',
                            'title' => 'Starting Number',
                            'tool_type' => 'number',
                            'options' => array( 'id' => 'number_' . $counterRange['min'] . '_' . $counterRange['max'], 'value' => $counterRange ),
                            'widgetType' => 'counter',
                            'tooltip' => 'Starting number'
                        ),
                    ),
                ),
                array(
                    'class' => '\Tenweb_Builder\FastEditor\Tools\CountControlTool',
                    'changed-control-data' => array(
                        array(
                            'control_name' => 'ending_number',
                            'title' => 'Ending Number',
                            'tool_type' => 'number',
                            'options' => array( 'id' => 'number_' . $counterRange['min'] . '_' . $counterRange['max'], 'value' => $counterRange ),
                            'widgetType' => 'counter',
                            'tooltip' => 'Ending number'
                        ),
                    ),
                ),
            )
        );
    }

}