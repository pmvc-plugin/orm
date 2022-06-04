<?php

namespace PMVC\PlugIn\orm\Fields;
use PMVC\PlugIn\orm\Attrs\Column;

class BaseField extends Column
{
    private $_OptionalField = ['blank', 'choices', 'editable'];

    public function getFieldOptional()
    {
        return $this->_OptionalField;
    }

    public function getAllOptional()
    {
        return array_merge(
            parent::getAllOptional(), 
            $this->getFieldOptional()
        );
    }
}
