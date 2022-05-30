<?php

namespace PMVC\PlugIn\orm\Fields;
use PMVC\PlugIn\orm\Attrs\Field;
use PMVC\PlugIn\orm;

#[Attribute]
class BaseInteger extends Field
{
    private static $baseType='BaseInteger'; 

    public function __construct($name, array $columnOptions = [])
    {
        if (empty($this->fieldType)) {
            $this->fieldType = self::$baseType;
        }
        $this->baseFieldType = self::$baseType;
        parent::__construct($name, null, $columnOptions);
    }
}
