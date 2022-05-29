<?php

namespace PMVC\PlugIn\orm\Fields;
use PMVC\PlugIn\orm\Attrs\Field;
use PMVC\PlugIn\orm;

#[Attribute]
class BaseText extends Field
{
    public $fieldType = 'BaseText';

    public function __construct($name, array $columnOptions = [])
    {
        if (is_null($columnOptions[orm\TYPE])) {
            $columnOptions[orm\TYPE] = 'text';
        }
        parent::__construct($name, null, $columnOptions);
    }
}
