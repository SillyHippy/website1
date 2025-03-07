<?php

namespace Tenweb_Builder\FastEditor\Widgets;

class ImageCarouselFastEditor extends BaseWidgetFastEditor
{
    public $widget = 'Widget_Image_Carousel';

    protected function setToolsList()
    {
        $counterRange = ['min' => 1, 'max' => 2000 ];

        $this->toolsList = (
            array(
                array(
                    'class' => '\Tenweb_Builder\FastEditor\Tools\ClickTool',
                    'changed-control-data' => array(
                        array(
                            'control_name' => 'elementor-control-gallery-add',
                            'title' => 'Gallery Add',
                            'analytics' => 'Image Carousel media preview',
                            'tooltip' => 'Select images',
                        ),
                    ),
                ),
                array(
                    'class' => '\Tenweb_Builder\FastEditor\Tools\CountControlTool',
                    'changed-control-data' => array(
                        array(
                            'control_name' => 'slides_to_show',
                            'title' => 'Slides to show',
                            'tool_type' => 'number',
                            'options' => array( 'id' => 'number_' . $counterRange['min'] . '_' . $counterRange['max'], 'value' => $counterRange ),
                            'widgetType' => 'image-carousel',
                            'tooltip' => 'Slides count to show'
                        ),
                    ),
                ),
                array(
                    'class' => '\Tenweb_Builder\FastEditor\Tools\DuplicateTool',
                    'changed-control-data' => [],
                ),
                array(
                    'class' => '\Tenweb_Builder\FastEditor\Tools\DeleteTool',
                    'changed-control-data' => [],
                ),
                array(
                    'class' => '\Tenweb_Builder\FastEditor\Tools\MoreTool',
                    'changed-control-data' => [],
                ),
            )
        );
    }

}