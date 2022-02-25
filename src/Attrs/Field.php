<?php

namespace PMVC\PlugIn\orm\Attrs;

#[Attribute]
class Field extends Column
{
    public function gettFieldOptional()
    {
        return ['blank', 'choices', 'editable'];
    }

    public function getAllOptional()
    {
        return array_merge(
            parent::getAllOptional(), 
            $this->gettFieldOptional()
        );
    }
}
