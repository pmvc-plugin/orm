<?php

namespace PMVC\PlugIn\orm\Attrs;

use PMVC\HashMap;
use PMVC\PlugIn\orm;

#[Attribute]
class Column extends HashMap
{
    public $name;
    protected $baseFieldType;

    public function __construct($name, $type, array $columnOptions = [])
    {
        $this->name = $name;
        $columnOptions['name'] = $name;

        if (!isset($columnOptions[orm\TYPE])) {
            if (!is_null($type)) {
                $columnOptions[orm\TYPE] = $type;
            } elseif (!empty($this->fieldType)) {
                if (empty($columnOptions['baseType'])) {
                    $columnOptions['baseType'] = $this->baseFieldType;
                }
                $columnOptions[orm\TYPE] = \PMVC\plug('orm')->get_default_type(
                    $this->fieldType,
                    $columnOptions
                );
            }
        }

        parent::__construct($columnOptions);
    }

    public function getAllRequired()
    {
        return ['name', orm\TYPE];
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
