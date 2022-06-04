<?php

namespace PMVC\PlugIn\orm\Attrs;

use PMVC\PlugIn\orm\Fields\BaseField;

#[Attribute]
class Field
{
    private $_field;

    public function __construct(
        string $name,
        string $type,
        array $columnOptions = []
    ) {
        $namespace = 'PMVC\PlugIn\orm\Fields';
        $class = $namespace . '\\' . $type;
        if (class_exists($class)) {
            $this->_field = new $class($name, $columnOptions);
        } else {
            $this->_field = new BaseField($name, $type, $columnOptions);
        }
    }

    public function getField()
    {
        return $this->_field;
    }
}
