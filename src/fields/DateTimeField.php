<?php

namespace PMVC\PlugIn\orm\Fields;
use PMVC\PlugIn\orm\Attrs\Field;

#[Attribute]
class DateTimeField extends Field
{
    public $fieldType = 'DateTimeField';

    public function __construct($name, array $columnOptions = [])
    {
        parent::__construct($name, null, $columnOptions);
    }
}
