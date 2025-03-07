<?php

namespace Tenweb_Builder\FastEditor\Widgets;

class TestimonialFastEditor extends BaseWidgetFastEditor
{
    public $widget = 'Widget_Testimonial';

    protected function setToolsList()
    {
        // $options should have 'icon name' => 'control value' construction
        $options_alignment = [
            'align_left' => 'left',
            'align_center' => 'center',
            'align_right' => 'right',
        ];
        $this->toolsList = (
            array(
                array(
                  'class' => '\Tenweb_Builder\FastEditor\Tools\WriteWithAITool',
                  'changed-control-data' => array(
                    array('control_name' => 'testimonial_content', 'title' => 'Testimonial content', 'generate_type'=>'all'),
                  ),
                ),
                array(
                    'class' => '\Tenweb_Builder\FastEditor\Tools\ClickTool',
                    'changed-control-data' => array(
                        array(
                            'control_name' => 'elementor-control-media__preview',
                            'title' => 'Media preview',
                            'analytics' => 'Testimonial media preview',
                            'tooltip' => 'Choose image',
                            ),
                    ),
                ),
                array(
                    'class' => '\Tenweb_Builder\FastEditor\Tools\ChoiceTypeTool',
                    'changed-control-data' => array(
                        array(
                            'control_name' => 'testimonial_alignment',
                            'title' => 'Align',
                            'options' => $options_alignment,
                            'tool'=> 'align',
                            'analytics' => 'Testimonial Alignment',
                            'tooltip' => 'Alignment',
                        ),
                    ),
                ),
                array(
                    'class' => '\Tenweb_Builder\FastEditor\Tools\ColorPickerTool',
                    'changed-control-data' => array(
                        array('control_name' => 'name_text_color', 'title' => 'Name Color'),
                        array('control_name' => 'content_content_color', 'title' => 'Content Color'),
                        array('control_name' => 'job_text_color', 'title' => 'Title Color'),
                    ),
                ),
            )
        );
    }

}