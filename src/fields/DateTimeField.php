<?php

namespace PMVC\PlugIn\orm\Fields;

#[Attribute]
class DateTimeField extends BaseField
{
    public $fieldType = 'DateTimeField';

    public function __construct($name, array $columnOptions = [])
    {
        parent::__construct($name, null, $columnOptions);
    }
}
