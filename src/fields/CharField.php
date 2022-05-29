<?php

namespace PMVC\PlugIn\orm\Fields;
use PMVC\PlugIn\orm;

#[Attribute]
class CharField extends BaseText
{
    public $fieldType = 'CharField';

    public function __construct($name, array $columnOptions = [])
    {
        if (is_null($columnOptions[orm\TYPE])) {
            $columnOptions[orm\TYPE] = 'text';
        }
        parent::__construct($name, null, $columnOptions);
    }
}
