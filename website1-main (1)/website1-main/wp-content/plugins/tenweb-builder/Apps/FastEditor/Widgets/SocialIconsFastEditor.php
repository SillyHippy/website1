<?php

namespace Tenweb_Builder\FastEditor\Widgets;

class SocialIconsFastEditor extends BaseWidgetFastEditor
{
    public $widget = 'Widget_Social_Icons';

    protected function setToolsList()
    {
        // $options should have 'icon name' => 'control value' construction
        $options_alignment = [
            'align_left' => 'left',
            'align_center' => 'center',
            'align_right' => 'right',
        ];

        $shapes_list = [
          esc_attr('rounded') => esc_html('Rounded'),
          esc_attr('square') => esc_html('Square'),
          esc_attr('circle') => esc_html('Circle'),
        ];

        $this->toolsList = (
            array(
                array(
                    'class' => '\Tenweb_Builder\FastEditor\Tools\ClickTool',
                    'changed-control-data' => array(
                        array(
                            'control_name' => 'elementor-repeater-add',
                            'title' => 'Add social icon',
                            'analytics' => 'Social icon add',
                            'tooltip' => 'Add item',
                        ),
                    ),
                ),
                array(
                    'class' => '\Tenweb_Builder\FastEditor\Tools\ShapeTool',
                    'changed-control-data' => array(
                        array(
                          'control_name' => 'shape',
                          'title' => 'Shape',
                          'analytics' => 'Shape',
                          'options' => array( 'id' => 'shapes_list', 'value' => $shapes_list ),
                          'widgetType' => 'social-icons'
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
                            'analytics' => 'Social Icons Alignment',
                            'tooltip' => 'Alignment',
                        ),
                    ),
                ),
            )
        );
    }
}