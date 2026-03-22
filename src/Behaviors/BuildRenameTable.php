<?php

namespace PMVC\PlugIn\orm\Behaviors;

use PMVC\PlugIn\orm\Interfaces\Behavior;
use PMVC\PlugIn\orm\Engine;

class BuildRenameTable implements Behavior
{
    public $oldTableName;
    public $newTableName;
    public $sql;

    public function __construct(string $oldTableName, string $newTableName)
    {
        $this->oldTableName = $oldTableName;
        $this->newTableName = $newTableName;
    }

    public function accept(Engine $engine)
    {
        return $engine->buildRenameTable($this);
    }

    public function process()
    {
        if (empty($this->sql)) {
            $this->sql = "ALTER TABLE {$this->oldTableName} RENAME TO {$this->newTableName}";
        }
        return $this->sql;
    }
}
