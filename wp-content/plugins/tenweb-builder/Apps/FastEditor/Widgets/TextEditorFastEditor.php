<?php

namespace Tenweb_Builder\FastEditor\Widgets;

class TextEditorFastEditor extends BaseWidgetFastEditor
{
    public $widget = 'Widget_Text_Editor';

    protected function setToolsList()
    {
        $tagsList = array(
            esc_attr('h1')   => esc_html('Heading 1'),
            esc_attr('h2')   => esc_html('Heading 2'),
            esc_attr('h3')   => esc_html('Heading 3'),
            esc_attr('h4')   => esc_html('Heading 4'),
            esc_attr('h5')   => esc_html('Heading 5'),
            esc_attr('h6')   => esc_html('Heading 6'),
            esc_attr('p')    => esc_html('Paragraph'),
            esc_attr('div')  => esc_html('div'),
            esc_attr('span') => esc_html('span'),
        );

        $fonts = array_keys(\Elementor\Fonts::get_fonts());
        $fontsList = [];
        $fontsList[esc_attr('no-result')] = esc_html('No results..');
        foreach ( $fonts as $font ) {
            $fontsList[esc_attr($font)] = esc_html($font);
        }

        $fontSizeRange = ['min' => 1, 'max' => 100 ];

        $this->toolsList = (
            array(
                array(
                  'class' => '\Tenweb_Builder\FastEditor\Tools\WriteWithAITool',
                  'changed-control-data' => array(
                    array('control_name' => 'editor', 'title' => 'Editor'),
                  ),
                ),
                array(
                    'class' => '\Tenweb_Builder\FastEditor\Tools\FontFamilyTool',
                    'changed-control-data' => array(
                        array(
                            'control_name' => 'typography_font_family',
                            'title' => 'Typography font family',
                            'options' => array( 'id' => 'font_family', 'value' => $fontsList ),
                            'widgetType' => 'text-editor'
                        ),
                    ),
                ),
                array(
                    'class' => '\Tenweb_Builder\FastEditor\Tools\FontSizeTool',
                    'changed-control-data' => array(
                        array(
                            'control_name' => 'typography_font_size',
                            'title' => 'Typography font size',
                            'options' => array( 'id' => 'number_' . $fontSizeRange['min'] . '_' . $fontSizeRange['max'], 'value' => $fontSizeRange ),
                            'widgetType' => 'text-editor'
                        ),
                    ),
                ),
                array(
                    'class' => '\Tenweb_Builder\FastEditor\Tools\ColorPickerTool',
                    'changed-control-data' => array(
                        array('control_name' => 'text_color', 'title' => 'Text color', 'tooltip' => 'Text color'),
                    ),
                ),
                array(
                    'class' => '\Tenweb_Builder\FastEditor\Tools\DropdownSelectTool',
                    'changed-control-data' => array(
                        array(
                            'control_name' => 'editor',
                            'title' => 'Editor',
                            'tool' => 'html_tag',
                            'tool_text' => 'H1',
                            'options' => array( 'id' => 'html_tags', 'value' => $tagsList ),
                            'analytics' => 'HTML Tag',
                            'widgetType' => 'text-editor',
                        ),
                    ),
                )
            )
        );
    }

}