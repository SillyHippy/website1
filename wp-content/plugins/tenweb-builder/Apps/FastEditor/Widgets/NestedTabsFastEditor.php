<?php

namespace Tenweb_Builder\FastEditor\Widgets;

class NestedTabsFastEditor extends BaseWidgetFastEditor
{
    public $widget = 'NestedTabs';

    protected function setToolsList()
    {
      $options_position = [
        'position_top' => 'block-start',
        'position_bottom' => 'block-end',
        'position_right' => 'inline-end',
        'position_left' => 'inline-start',
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
                  'control_name' => 'tabs_direction',
                  'title' => 'Position',
                  'options' => $options_position,
                  'tool'=> 'position',
                  'analytics' => 'Tab Position',
                  'tooltip' => 'Position',
                ),
              ),
            )
          )
      );
    }

}