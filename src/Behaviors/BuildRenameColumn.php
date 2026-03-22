<?php

namespace PMVC\PlugIn\orm\Behaviors;

use PMVC\PlugIn\orm\Interfaces\Behavior;
use PMVC\PlugIn\orm\Engine;

class BuildRenameColumn implements Behavior
{
    public $tableName;
    public $oldName;
    public $newName;
    public $sql;

    public function __construct(string $tableName, string $oldName, string $newName)
    {
        $this->tableName = $tableName;
        $this->oldName = $oldName;
        $this->newName = $newName;
    }

    public function accept(Engine $engine)
    {
        return $engine->buildRenameColumn($this);
    }

    public function process()
    {
        if (empty($this->sql)) {
            $this->sql = "ALTER TABLE {$this->tableName} RENAME COLUMN {$this->oldName} TO {$this->newName}";
        }
        return $this->sql;
    }
}
