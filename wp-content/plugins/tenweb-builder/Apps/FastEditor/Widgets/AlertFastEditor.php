<?php

namespace Tenweb_Builder\FastEditor\Widgets;

class AlertFastEditor extends BaseWidgetFastEditor
{
    public $widget = 'Widget_Alert';

    protected function setToolsList()
    {
        $alert_types = [
          esc_attr('info') => esc_html( 'Info' ),
          esc_attr('success') => esc_html( 'Success' ),
          esc_attr('warning') => esc_html( 'Warning' ),
          esc_attr('danger') => esc_html( 'Danger' ),
        ];

        $this->toolsList = (
            array(
                array(
                    'class' => '\Tenweb_Builder\FastEditor\Tools\WriteWithAITool',
                    'changed-control-data' => array(
                        array('control_name' => 'alert_title', 'title' => 'Alert title'),
                    ),
                ),
                array(
                  'class' => '\Tenweb_Builder\FastEditor\Tools\DropdownSelectTool',
                  'changed-control-data' => array(
                    array(
                      'control_name' => 'alert_type',
                      'title' => 'Type',
                      'tool_text' => 'Type',
                      'options' => array( 'id' => 'alert_type', 'value' => $alert_types ),
                      'widgetType' => 'alert',
                      'analytics' => 'Alert type',
                    ),
                  ),
                ),
            )
        );
    }

}