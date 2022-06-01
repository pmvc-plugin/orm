<?php

namespace PMVC\PlugIn\orm\Behaviors;

use PMVC\PlugIn\orm\Interfaces\Behavior;
use PMVC\PlugIn\orm\Engine;

class BuildTableSql implements Behavior
{
    public function __construct($table)
    {
        $this->_table = $table;
    }

    public function accept(Engine $engine)
    {
        return $engine->buildCreateTable($this);
    }

    public function process()
    {
        return \PMVC\plug('orm')->useTpl('createTable', ['TABLE_NAME', 'OPTION_LIST'], $this->_table);
    }
}
