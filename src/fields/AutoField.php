<?php

namespace PMVC\PlugIn\orm\Fields;
use PMVC\PlugIn\orm\Attrs\BaseInteger;

#[Attribute]
class AutoField extends BaseInteger 
{
    public $fieldType = "AutoField";

    public function __construct($name, array $columnOptions = [])
    {
        $columnOptions['primaryKey'] = true;
        $columnOptions['autoIncrement'] = true;
        parent::__construct($name, $columnOptions);
    }
}
