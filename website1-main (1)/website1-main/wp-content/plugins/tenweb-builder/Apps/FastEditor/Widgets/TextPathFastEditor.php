<?php

namespace Tenweb_Builder\FastEditor\Widgets;

class TextPathFastEditor extends BaseWidgetFastEditor
{

    public $widget = 'TextPath';

    protected function setToolsList()
    {

        $options_alignment = [
          'align_left' => 'left',
          'align_center' => 'center',
          'align_right' => 'right',
        ];

        $paths = [
          'wave' => esc_html('Wave'),
          'arc' => esc_html('Arc'),
          'circle' => esc_html('Circle'),
          'line' => esc_html('Line'),
          'oval' => esc_html('Oval'),
          'spiral' => esc_html('Spiral'),
        ];

        $this->toolsList = (
            array(
              array(
                'class' => '\Tenweb_Builder\FastEditor\Tools\WriteWithAITool',
                'changed-control-data' => array(
                  array('control_name' => 'text', 'title' => 'Text'),
                ),
              ),
              array(
                'class' => '\Tenweb_Builder\FastEditor\Tools\DropdownSelectTool',
                'changed-control-data' => array(
                  array(
                    'control_name' => 'path',
                    'title' => 'Path Type',
                    'tool_text' => 'Path Type',
                    'options' => array( 'id' => 'path_type', 'value' => $paths ),
                    'widgetType' => 'text-path',
                    'analytics' => 'Text path type',
                  ),
                ),
              ),
              array(
                'class' => '\Tenweb_Builder\FastEditor\Tools\ChoiceTypeTool',
                'changed-control-data' => array(
                  array(
                    'control_name' => 'align',
                    'title' => 'Alignment',
                    'options' => $options_alignment,
                    'tool'=> 'align',
                    'analytics' => 'Text path Alignment',
                    'tooltip' => 'Alignment',
                  ),
                ),
              ),
              array(
                'class' => '\Tenweb_Builder\FastEditor\Tools\ColorPickerTool',
                'changed-control-data' => array(
                  array('control_name' => 'text_color_normal', 'title' => 'Color', 'tooltip' => 'Color'),
                ),
              ),
            )
        );
    }

}