<?php

namespace PMVC\PlugIn\orm\Fields;

#[Attribute]
class BaseText extends BaseField
{
    private static $baseType='BaseText'; 

    public function __construct($name, array $columnOptions = [])
    {
        if (empty($this->fieldType)) {
            $this->fieldType = self::$baseType;
        }
        $this->baseFieldType = self::$baseType;
        parent::__construct($name, null, $columnOptions);
    }
}
