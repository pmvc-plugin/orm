<?php

namespace PMVC\PlugIn\orm\Fields;
use PMVC\PlugIn\orm\Fields\BaseText;

#[Attribute]
class CharField extends BaseText
{
    public $fieldType = 'CharField';

    public function __construct($name, array $columnOptions = [])
    {
        parent::__construct($name, $columnOptions);
    }
}
