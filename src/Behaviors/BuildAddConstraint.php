<?php

namespace PMVC\PlugIn\orm\Behaviors;

use PMVC\PlugIn\orm\Interfaces\Behavior;
use PMVC\PlugIn\orm\Engine;

class BuildAddConstraint implements Behavior
{
    public $tableName;
    public $constraintName;
    public $constraintDef;
    public $sql;

    public function __construct(string $tableName, string $constraintName, string $constraintDef)
    {
        $this->tableName = $tableName;
        $this->constraintName = $constraintName;
        $this->constraintDef = $constraintDef;
    }

    public function accept(Engine $engine)
    {
        return $engine->buildAddConstraint($this);
    }

    public function process()
    {
        if (empty($this->sql)) {
            $this->sql = "ALTER TABLE {$this->tableName} ADD CONSTRAINT {$this->constraintName} {$this->constraintDef}";
        }
        return $this->sql;
    }
}
