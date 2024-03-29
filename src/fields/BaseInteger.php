<?php

namespace PMVC\PlugIn\orm\Fields;

#[Attribute]
class BaseInteger extends BaseField
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
