<?php

namespace PMVC\PlugIn\orm\Behaviors;

use PMVC\PlugIn\orm\Interfaces\Behavior;
use PMVC\PlugIn\orm\Engine;

class BuildDropColumn implements Behavior
{
    public $tableName;
    public $fieldName;
    public $sql;

    public function __construct(string $tableName, string $fieldName)
    {
        $this->tableName = $tableName;
        $this->fieldName = $fieldName;
    }

    public function accept(Engine $engine)
    {
        return $engine->buildDropColumn($this);
    }

    public function process()
    {
        if (empty($this->sql)) {
            $this->sql = "ALTER TABLE {$this->tableName} DROP COLUMN {$this->fieldName}";
        }
        return $this->sql;
    }
}
