<?php

namespace Tenweb_Builder\FastEditor\Tools;

abstract class FastEditorTool
{
    public string $dataAttr = '';
    public array $controlData = [];
    public string $controlName = '';
    public string $tooltip = '';
    public string $type = 'fast_edit';

    public function __construct( $controlData = [] ) {
        if ( !empty($controlData) ) {
            $this->controlData = $controlData;
            $this->controlName = $controlData[0]['control_name'];
            $this->tooltip = $controlData[0]['tooltip'] ?? '';
        }
    }

    abstract public function getToolContent();

    abstract public function editorScripts();

    abstract public function frontendScripts();
    abstract public function frontendStyles();

    public function getLocalizedData(){}

    protected function visibilityCheck() {
        return true;
    }
}