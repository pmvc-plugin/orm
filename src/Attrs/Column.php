<?php

namespace PMVC\PlugIn\orm\Attrs;

use PMVC\HashMap;
use PMVC\PlugIn\orm\Interfaces\Behavior;

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
        $shouldRequired = $this->getAllRequired();

        foreach ($shouldRequired as $key) {
            if (!isset($this[$key])) {
                throw new InvalidArgumentException(
                    'Required field [' . $key . '] not set.'
                );
            }
        }

        return true;
    }
}
