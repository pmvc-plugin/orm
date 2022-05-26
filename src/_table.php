<?php

namespace PMVC\PlugIn\orm;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__ . '\TableActions';

use PMVC\PlugIn\orm\Attrs\Table;
use PMVC\PlugIn\orm\Behaviors\CheckTableExists;

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
        $result = $this->caller->compile([new CheckTableExists($tableName)]);
        return \PMVC\get($result, 0);
    }
}
