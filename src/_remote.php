<?php

namespace PMVC\PlugIn\orm;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__ . '\RemoteActions';

use PMVC\PlugIn\orm\Attrs\Table;

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
        return $this->caller->behavior()->tableExists($tableName);
    }
}
