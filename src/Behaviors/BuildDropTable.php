<?php

namespace PMVC\PlugIn\orm\Behaviors;

use PMVC\PlugIn\orm\Interfaces\Behavior;
use PMVC\PlugIn\orm\Engine;

class BuildDropTable implements Behavior
{
    public $tableName;
    public $sql;

    public function __construct(string $tableName)
    {
        $this->tableName = $tableName;
    }

    public function accept(Engine $engine)
    {
        return $engine->buildDropTable($this);
    }

    public function process()
    {
        if (empty($this->sql)) {
            $this->sql = "DROP TABLE IF EXISTS {$this->tableName}";
        }
        return $this->sql;
    }
}
