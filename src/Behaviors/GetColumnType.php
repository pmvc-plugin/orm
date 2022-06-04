<?php

namespace PMVC\PlugIn\orm\Behaviors;

use PMVC\PlugIn\orm\Interfaces\Behavior;
use PMVC\PlugIn\orm\Engine;

class GetColumnType implements Behavior
{
    public $params;
    private $_types;

    public function __construct($fieldType, $options)
    {
        $this->params = [
            'fieldType' => $fieldType,
            'options' => $options,
        ];
    }

    public function accept(Engine $engine)
    {
        return $engine->getColumnType($this);
    }

    public function setColumnTypes($types)
    {
        $this->_types = $types;
    }

    public function process()
    {
        if (!empty($this->_types)) {
            if (!empty($this->params['options']['setter'])) {
                $this->params['options']['setter']->engineTypes = $this->_types;
            }
            if (isset($this->_types[$this->params['fieldType']])) {
                $columnType = $this->_types[$this->params['fieldType']];
                $result = \PMVC\tplArrayReplace(
                    $columnType,
                    $this->params['options']
                );
                return $result;
            }
        }
    }
}
