<?php

namespace PMVC\PlugIn\orm\Behaviors;

use PMVC\PlugIn\orm\Interfaces\Behavior;
use PMVC\PlugIn\orm\Engine;

class BuildCreateIndex implements Behavior
{
    public $tableName;
    public $indexName;
    public $columns;
    public $unique;
    public $sql;

    public function __construct(string $tableName, string $indexName, array $columns, bool $unique = false)
    {
        $this->tableName = $tableName;
        $this->indexName = $indexName;
        $this->columns = $columns;
        $this->unique = $unique;
    }

    public function accept(Engine $engine)
    {
        return $engine->buildCreateIndex($this);
    }

    public function process()
    {
        if (empty($this->sql)) {
            $uniqueStr = $this->unique ? 'UNIQUE ' : '';
            $colStr = join(', ', $this->columns);
            $this->sql = "CREATE {$uniqueStr}INDEX {$this->indexName} ON {$this->tableName} ({$colStr})";
        }
        return $this->sql;
    }
}
