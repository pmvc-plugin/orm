<?php

namespace PMVC\PlugIn\orm;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__ . '\TableActions';

use PMVC\PlugIn\orm\Attrs\Table;

class TableActions
{
    public function __invoke()
    {
        return $this;
    }

    public function create($tableName)
    {
        return new Table($tableName);
    }

    public function exists($tableName)
    {
        $result =  $this->caller->compile([
            new CheckTableExists($this)
        ]);
        return \PMVC\get($result, 0);
    }
}


