<?php

namespace PMVC\PlugIn\orm\Behaviors;

use PMVC\PlugIn\orm\Interfaces\Behavior;
use PMVC\PlugIn\orm\Engine;

class BuildTableArray implements Behavior
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
        $keys = ['TABLE_NAME', 'OPTION_LIST', 'OPTION_COLS'];
        $res = [];
        foreach ($keys as $key) {
          $res[$key] = $this->_table[$key];
        }
        return $res;
    }
}
