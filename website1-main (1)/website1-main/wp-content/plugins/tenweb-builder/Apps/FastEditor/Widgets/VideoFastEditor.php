<?php

namespace Tenweb_Builder\FastEditor\Widgets;

class VideoFastEditor extends BaseWidgetFastEditor
{
    public $widget = 'Widget_Video';

    protected function setToolsList()
    {
        $this->toolsList = (
            array(
                array(
                    'class' => '\Tenweb_Builder\FastEditor\Tools\VideoTool',
                    'changed-control-data' => array(
                        array('control_name' => 'url', 'title' => 'Url'),
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