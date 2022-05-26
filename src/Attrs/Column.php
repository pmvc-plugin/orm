<?php

namespace PMVC\PlugIn\orm\Attrs;
use PMVC\HashMap;

#[Attribute]
class Column extends HashMap
{
    public $name;

    public function __construct($name, $type, array $columnOptions = [])
    {
        $this->name = $name;
        $columnOptions['name'] = $name;
        $columnOptions['type'] = $type;
        parent::__construct($columnOptions);
    }

    public function getAllRequired()
    {
        return ['name', 'type'];
    }

    public function getAllOptional()
    {
        return ['primaryKey', 'notNull', 'unique', 'default', 'check'];
    }

    public function getSupported()
    {
        return array_merge($this->getAllRequired(), $this->getAllOptional());
    }

    public function verifySpec()
    {
        return \PMVC\plug('orm')->check_required(
            $this,
            $this->getAllRequired()
        );
    }
}
