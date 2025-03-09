<?php

namespace Tenweb_Builder\FastEditor\Widgets;

class BlockquoteFastEditor extends BaseWidgetFastEditor
{
    public $widget = 'Blockquote';

    protected function setToolsList(){
        $skin = [
          'border' => esc_html('Border'),
          'quotation' => esc_html('Quotation'),
          'boxed' => esc_html('Boxed'),
          'clean' => esc_html('Clean'),
        ];

        $this->toolsList = (
            array(
              array(
                'class' => '\Tenweb_Builder\FastEditor\Tools\DropdownSelectTool',
                'changed-control-data' => array(
                  array(
                    'control_name' => 'twbb_blockquote_skin',
                    'title' => 'Skin',
                    'tool_text' => 'Skin',
                    'options' => array( 'id' => 'skin', 'value' => $skin ),
                    'widgetType' => 'twbb_blockquote',
                    'analytics' => 'Blockquote skin',
                  ),
                ),
              ),
            )
        );
    }

}