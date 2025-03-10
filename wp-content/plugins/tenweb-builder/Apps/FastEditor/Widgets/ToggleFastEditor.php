<?php

namespace Tenweb_Builder\FastEditor\Widgets;

class ToggleFastEditor extends BaseWidgetFastEditor
{
    public $widget = 'Widget_Toggle';

    protected function setToolsList()
    {

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
            )
        );
    }

}