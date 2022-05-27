<?php

namespace PMVC\PlugIn\orm\Fields;
use PMVC\PlugIn\orm\Attrs\Field;

#[Attribute]
class AutoField extends Field
{
    public $fieldType = "AutoField";

    public function __construct($name, $type = null, array $columnOptions = [])
    {
        if (is_null($type)) {
            $type = "int";
        }
        $columnOptions['primaryKey'] = true;
        $columnOptions['autoIncrement'] = true;
        parent::__construct($name, $type, $columnOptions);
    }
}
