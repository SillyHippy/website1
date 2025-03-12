<?php

namespace Tenweb_Builder\FastEditor\Widgets;

class GoogleMapsFastEditor extends BaseWidgetFastEditor
{
    public $widget = 'Widget_Google_Maps';

    protected function setToolsList()
    {
        $counterRange = ['min' => 1, 'max' => 2000 ];

        $this->toolsList = (
        array(
            array(
                'class' => '\Tenweb_Builder\FastEditor\Tools\CountControlTool',
                'changed-control-data' => array(
                    array(
                        'control_name' => 'height',
                        'title' => 'Height',
                        'options' => array( 'id' => 'number_' . $counterRange['min'] . '_' . $counterRange['max'], 'value' => $counterRange ),
                        'widgetType' => 'google_maps',
                        'tooltip' => 'Height'
                    ),
                ),
            )
        )
        );
    }

}