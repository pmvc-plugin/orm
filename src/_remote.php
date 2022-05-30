<?php

namespace PMVC\PlugIn\orm;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__ . '\RemoteActions';

use PMVC\PlugIn\orm\Attrs\Table;
use PMVC\PlugIn\orm\Behaviors\CheckTableExists;

class RemoteActions
{
    public function __invoke()
    {
        return $this;
    }

    public function create($tableName)
    {
        return $this->caller->dao()->getDefault()->createModel($tableName);
    }

    public function exists($tableName)
    {
        $result = $this->caller->compile([new CheckTableExists($tableName)]);
        return \PMVC\get($result, 0);
    }
}
