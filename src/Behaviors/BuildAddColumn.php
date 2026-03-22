<?php

namespace PMVC\PlugIn\orm\Behaviors;

use PMVC\PlugIn\orm\Interfaces\Behavior;
use PMVC\PlugIn\orm\Engine;

class BuildAddColumn implements Behavior
{
    public $tableName;
    public $fieldName;
    public $fieldType;
    public $options;
    public $columnTypeSql;
    public $sql;

    public function __construct(string $tableName, string $fieldName, string $fieldType, array $options = [])
    {
        $this->tableName = $tableName;
        $this->fieldName = $fieldName;
        $this->fieldType = $fieldType;
        $this->options = $options;
    }

    public function accept(Engine $engine)
    {
        return $engine->buildAddColumn($this);
    }

    public function process()
    {
        if (!empty($this->sql)) {
            return $this->sql;
        }
        $parts = ["ALTER TABLE {$this->tableName} ADD COLUMN {$this->fieldName}"];
        if (!empty($this->columnTypeSql)) {
            $parts[] = $this->columnTypeSql;
        } else {
            $parts[] = $this->fieldType;
        }
        if (!empty($this->options['notNull'])) {
            $parts[] = 'NOT NULL';
        }
        if (isset($this->options['default'])) {
            $parts[] = "DEFAULT {$this->options['default']}";
        }
        if (!empty($this->options['unique'])) {
            $parts[] = 'UNIQUE';
        }
        $this->sql = join(' ', $parts);
        return $this->sql;
    }
}
