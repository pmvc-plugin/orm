<?php

namespace PMVC\PlugIn\orm;

use PMVC\PlugIn\orm\Attrs\Table;
use PMVC\PlugIn\orm\Tables;

class StructureDAO extends DAO
{
    private $_tables = null;

    public function createModel($tableName)
    {
        if (is_null($this->_tables)) {
            $this->_tables = new Tables();
        }
        if (empty($this->_tables[$tableName])) {
            $this->_tables[$tableName] = new Table($tableName, $this);
        }
        return $this->_tables[$tableName];
    }

    public function toArray()
    {
        return $this->_tables->toArray();
    }

    public function commit($sql, array $prepare = [])
    {
        return $this;
    }

    public function process()
    {
    }
}
