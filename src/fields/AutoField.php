<?php

namespace PMVC\PlugIn\orm\Fields;
use PMVC\PlugIn\orm\Attrs\Field;
use PMVC\PlugIn\orm;

#[Attribute]
class AutoField extends Field
{
    public $fieldType = "AutoField";

    public function __construct($name, array $columnOptions = [])
    {
        if (is_null($columnOptions[orm\TYPE])) {
            $columnOptions[orm\TYPE] = "int";
        }
        $columnOptions['primaryKey'] = true;
        $columnOptions['autoIncrement'] = true;
        parent::__construct($name, null, $columnOptions);
    }
}
