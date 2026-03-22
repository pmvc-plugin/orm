<?php

namespace PMVC\PlugIn\orm\Behaviors;

use PMVC\PlugIn\orm\Interfaces\Behavior;
use PMVC\PlugIn\orm\Engine;

class BuildDropConstraint implements Behavior
{
    public $tableName;
    public $constraintName;
    public $sql;

    public function __construct(string $tableName, string $constraintName)
    {
        $this->tableName = $tableName;
        $this->constraintName = $constraintName;
    }

    public function accept(Engine $engine)
    {
        return $engine->buildDropConstraint($this);
    }

    public function process()
    {
        if (empty($this->sql)) {
            $this->sql = "ALTER TABLE {$this->tableName} DROP CONSTRAINT {$this->constraintName}";
        }
        return $this->sql;
    }
}
