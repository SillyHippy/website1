<?php

namespace Tenweb_Builder\FastEditor\Widgets;

class SpacerFastEditor extends BaseWidgetFastEditor
{
    public $widget = 'Widget_Spacer';

    protected function setToolsList()
    {
        $counterRange = ['min' => 1, 'max' => 2000 ];

        $this->toolsList = (
            array(
                array(
                    'class' => '\Tenweb_Builder\FastEditor\Tools\CountControlTool',
                    'changed-control-data' => array(
                        array(
                            'control_name' => 'space',
                            'title' => 'Space',
                            'options' => array( 'id' => 'number_' . $counterRange['min'] . '_' . $counterRange['max'], 'value' => $counterRange ),
                            'widgetType' => 'spacer',
                            'tooltip' => 'Space'
                        ),
                    ),
                )
            )
        );
    }

}