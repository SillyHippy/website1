<?php

namespace Tenweb_Builder\FastEditor\Widgets;

class DividerFastEditor extends BaseWidgetFastEditor
{
    public $widget = 'Widget_Divider';

    protected function setToolsList()
    {
        $styleList = array(
            esc_attr('solid')   => esc_html('Solid'),
            esc_attr('double')   => esc_html('Double'),
            esc_attr('dotted')   => esc_html('Dotted'),
            esc_attr('dashed')   => esc_html('Dashed'),
            esc_attr('curly')   => esc_html('Curly'),
            esc_attr('curved')   => esc_html('Curved'),
            esc_attr('slashed')   => esc_html('Slashes'),
            esc_attr('squared')   => esc_html('Squared'),
            esc_attr('wavy')   => esc_html('Wavy'),
            esc_attr('zigzag')   => esc_html('Zigzag'),
            esc_attr('multiple')   => esc_html('Multiple'),
            esc_attr('arrows')   => esc_html('Arrows'),
            esc_attr('pluses')   => esc_html('Pluses'),
            esc_attr('rhombus')   => esc_html('Rhombus'),
            esc_attr('parallelogram')   => esc_html('Parallelogram'),
            esc_attr('rectangles')   => esc_html('Rectangles'),
            esc_attr('dots_tribal')   => esc_html('Dots'),
            esc_attr('trees_2_tribal')   => esc_html('Fir Tree'),
            esc_attr('rounds_tribal')   => esc_html('Half Rounds'),
            esc_attr('leaves_tribal')   => esc_html('Leaves'),
            esc_attr('stripes_tribal')   => esc_html('Stripes'),
            esc_attr('squares_tribal')   => esc_html('Squares'),
            esc_attr('trees_tribal')   => esc_html('Trees'),
            esc_attr('planes_tribal')   => esc_html('Tribal'),
            esc_attr('x_tribal')   => esc_html('X'),
            esc_attr('zigzag_tribal')   => esc_html('Zigzag'),
        );

        $this->toolsList = (
            array(
                array(
                    'class' => '\Tenweb_Builder\FastEditor\Tools\DropdownSelectTool',
                    'changed-control-data' => array(
                        array(
                            'control_name' => 'style',
                            'title' => 'Divider Style',
                            'tool_text'=> 'Style',
                            'options' => array( 'id' => 'border_style', 'value' => $styleList ),
                            'analytics' => 'Divider Style',
                            'widgetType' => 'divider'
                        ),
                    ),
                ),
                array(
                    'class' => '\Tenweb_Builder\FastEditor\Tools\ColorPickerTool',
                    'changed-control-data' => array(
                        array('control_name' => 'color', 'title' => 'Divider color', 'tooltip' => 'Color'),
                    ),
                ),
            )
        );
    }

}