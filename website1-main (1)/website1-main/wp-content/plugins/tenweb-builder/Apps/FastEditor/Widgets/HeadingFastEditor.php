<?php

namespace Tenweb_Builder\FastEditor\Widgets;

class HeadingFastEditor extends BaseWidgetFastEditor
{
    public $widget = 'Widget_Heading';

    protected function setToolsList()
    {

        $fonts = array_keys(\Elementor\Fonts::get_fonts());
        $fontsList = [];
        $fontsList[esc_attr('no-result')] = esc_html('No results..');
        foreach ( $fonts as $font ) {
            $fontsList[esc_attr($font)] = esc_html($font);
        }
        $fontSizeRange = ['min' => 1, 'max' => 100 ];

        $stylesList = array(
            esc_attr('normal')   => esc_html('Normal'),
            esc_attr('italic')   => esc_html('Italic'),
        );

        $this->toolsList = (
            array(
                array(
                  'class' => '\Tenweb_Builder\FastEditor\Tools\WriteWithAITool',
                  'changed-control-data' => array(
                    array('control_name' => 'title', 'title' => 'Title'),
                  ),
                ),
                array(
                    'class' => '\Tenweb_Builder\FastEditor\Tools\FontFamilyTool',
                    'changed-control-data' => array(
                        array(
                            'control_name' => 'typography_font_family',
                            'title' => 'Typography font family',
                            'options' => array( 'id' => 'font_family', 'value' => $fontsList ),
                            'widgetType' => 'heading'
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
                            'widgetType' => 'heading'
                        ),
                    ),
                ),
                array(
                    'class' => '\Tenweb_Builder\FastEditor\Tools\ColorPickerTool',
                    'changed-control-data' => array(
                        array('control_name' => 'title_color', 'title' => 'Title color', 'tooltip' => 'Text color'),
                    ),
                ),
                array(
                    'class' => '\Tenweb_Builder\FastEditor\Tools\FontStyleTool',
                    'changed-control-data' => array(
                        array(
                            'control_name' => 'typography_font_style',
                            'title' => 'Typography font style',
                            'options' => array( 'id' => 'font_style', 'value' => $stylesList ),
                            'widgetType' => 'heading'
                        ),
                    ),
                ),
            )
        );
    }

}