<?php

namespace Tenweb_Builder\FastEditor\Widgets;

class IconListFastEditor extends BaseWidgetFastEditor
{
    public $widget = 'Widget_Icon_List';

    protected function setToolsList()
    {
        // $options should have 'icon name' => 'control value' construction
        $options = [
            'position_left' => 'traditional',
            'position_top' => 'inline',
        ];
        $this->toolsList = (
            array(
                array(
                    'class' => '\Tenweb_Builder\FastEditor\Tools\ClickTool',
                    'changed-control-data' => array(
                        array(
                            'control_name' => 'elementor-repeater-add',
                            'title' => 'Repeater Add',
                            'analytics' => 'Icons List add',
                            'tooltip' => 'Add item',
                        ),
                    ),
                ),
                array(
                    'class' => '\Tenweb_Builder\FastEditor\Tools\ChoiceTypeTool',
                    'changed-control-data' => array(
                        array(
                            'control_name' => 'view',
                            'title' => 'View',
                            'options' => $options,
                            'tool'=> 'view',
                            'analytics' => 'Icon List Layout',
                            'tooltip' => 'Layout',
                        ),
                    ),
                ),
            )
        );
    }

}