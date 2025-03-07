<?php

namespace Tenweb_Builder\FastEditor\Widgets;

class TabsFastEditor extends BaseWidgetFastEditor
{
    public $widget = 'Widget_Tabs';

    protected function setToolsList()
    {

        $options_position = [
            'position_left' => 'vertical',
            'position_top' => 'horizontal',
        ];

        $this->toolsList = (
            array(
                array(
                    'class' => '\Tenweb_Builder\FastEditor\Tools\ClickTool',
                    'changed-control-data' => array(
                        array(
                            'control_name' => 'elementor-repeater-add',
                            'title' => 'Repeater Add',
                            'analytics' => 'Tabs add',
                            'tooltip' => 'Add item',
                        ),
                    ),
                ),
                array(
                    'class' => '\Tenweb_Builder\FastEditor\Tools\ChoiceTypeTool',
                    'changed-control-data' => array(
                        array(
                            'control_name' => 'type',
                            'title' => 'Position',
                            'options' => $options_position,
                            'tool'=> 'position',
                            'analytics' => 'Tab Position',
                            'tooltip' => 'Position',
                        ),
                    ),
                ),
            )
        );
    }

}