<?php

namespace PMVC\PlugIn\orm\Behaviors;

use PMVC\PlugIn\orm\Interfaces\Behavior;
use PMVC\PlugIn\orm\Engine;

class BuildDropIndex implements Behavior
{
    public $indexName;
    public $sql;

    public function __construct(string $indexName)
    {
        $this->indexName = $indexName;
    }

    public function accept(Engine $engine)
    {
        return $engine->buildDropIndex($this);
    }

    public function process()
    {
        if (empty($this->sql)) {
            $this->sql = "DROP INDEX IF EXISTS {$this->indexName}";
        }
        return $this->sql;
    }
}
